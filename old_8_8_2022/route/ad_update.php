<?php
$config = require "./config.php";

$route_name = "ad_update";
$visible_footer = false;

if( !$_SESSION['cp_auth'][ $config["private_hash"] ] && !$_SESSION['cp_control_board'] ){

	if(!$_SESSION["profile"]["id"]){
	   header("location:" . _link("auth", true) );
	}

}

$Main = new Main();
$settings = $Main->settings();

$CategoryBoard = new CategoryBoard(); 
$Ads = new Ads();
$Seo = new Seo();
$Geo = new Geo();
$Profile = new Profile();
$Filters = new Filters();
$Banners = new Banners();
$ULang = new ULang();
$Shop = new Shop();
$Cart = new Cart();

if( $_SESSION['cp_control_board'] ){

   $data = $Ads->get("ads_id=?",[$id_ad]);

}else{

   $data = $Ads->get("ads_id='$id_ad' and ads_id_user='".intval($_SESSION["profile"]["id"])."' and clients_status IN(0,1) and ads_status IN(0,7,2,1)");

}

if(!$data){
	$Main->response("404");
}


$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

if(count($getCategoryBoard["category_board_id_parent"][0])){
	foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {
	  $data["list_categories"] .= '<span class="box-change-category-list-item" data-name="'.$ULang->t($value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'" data-id="'.$key.'" >'.$ULang->t($value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'</span>';
	}
}

if($settings["ad_create_period"]){
	$ad_create_period_list = explode(",", $settings["ad_create_period_list"]);
	if ($ad_create_period_list) {
	  foreach ($ad_create_period_list as $key => $value) {

	  	if( $value == $data["ads_period_day"] ){
	  		$active = 'class="uni-select-item-active"';
	  	}else{
	  		$active = '';
	  	}

	  	$list_period .= '<label '.$active.' > <input type="radio" name="period" value="'.$value.'"> <span>'.$value.' '.ending($value, $ULang->t("день") , $ULang->t("дня"),$ULang->t("дней") ).'</span> <i class="la la-check"></i> </label>';
	  }
	}
}


$load_filters_ad = $Filters->load_filters_ad($data["ads_id_cat"],$Filters->getVariants($data["ads_id"]));

if( $load_filters_ad ){

 $getCategory = $Filters->getCategory( ["id_cat" => $data["ads_id_cat"]] );

 if( $getCategory ){

     $getFilters = getAll( "select * from uni_ads_filters where ads_filters_id IN(".implode(",", $getCategory).")" );

     if(count($getFilters)){

        foreach ( $getFilters as $key => $value) {
            $list_filters[] = $value["ads_filters_name"];
        }

        $data["filters"] = '
           <div class="ads-create-main-data-box-item" >
              <p class="ads-create-subtitle" >'.$ULang->t("Характеристики").'</p>

              <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Укажите как можно больше параметров - это повысит интерес к объявлению.").'</div>
              <div class="mb25" ></div>
              '.$load_filters_ad.'

           </div> 
        ';

     }

 }

}

$getArea = getAll("select * from uni_city_area where city_area_id_city=? order by city_area_name asc", [$data["ads_city_id"]]);

if(count($getArea)){

	foreach ($getArea as $key => $value) {
        
        if( $data["ads_area_ids"] ){

			if( in_array($value["city_area_id"], explode(",", $data["ads_area_ids"]) ) ){
	            $active = 'class="uni-select-item-active"'; $checked = 'checked=""';			
			}else{
	            $active = ''; $checked = '';
			}

	    }

		$list_area .= '
           <label '.$active.' > <input '.$checked.' type="radio" name="area[]" value="'.$value["city_area_id"].'" > <span>'.$value["city_area_name"].'</span> <i class="la la-check"></i> </label>
		';
	}

    $data["city_options"] .= '
	    <div class="ads-create-main-data-box-item" >      
	    <p class="ads-create-subtitle" >'.$ULang->t("Район").'</p> 

	   	     <div class="ads-create-main-data-city-options-area" >
	            <div class="uni-select" data-status="0" >

	                 <div class="uni-select-name" data-name="'.$ULang->t("Не выбрано").'" > <span>'.$ULang->t("Не выбрано").'</span> <i class="la la-angle-down"></i> </div>
	                 <div class="uni-select-list" >
	                     '.$list_area.'
	                 </div>
	            
	            </div>
	         </div>

	    </div>
    ';

}


if($data["ads_metro_ids"]){

	$getMetro = getAll("select * from uni_metro where id IN(".$data["ads_metro_ids"].")");

	if(count($getMetro)){
		foreach ($getMetro as $key => $value) {
			$main = findOne("uni_metro", "id=?", [$value["parent_id"]]);
			if($main){
				$list_metro .= '
		           <span><i style="background-color:'.$main["color"].';"></i>'.$value["name"].' <i class="las la-times ads-metro-delete"></i><input type="hidden" value="'.$value["id"].'" name="metro[]"></span>
				';
		    }
		}
	}

}

$checkMetro = findOne( "uni_metro", "city_id=?", [ $data["ads_city_id"] ]);

if( $checkMetro ){

	$data["city_options"] .= '
	<div class="ads-create-main-data-box-item" >      
	<p class="ads-create-subtitle" >'.$ULang->t("Ближайшее метро").'</p>

		     <div class="ads-create-main-data-city-options-metro" >
	        <div class="container-custom-search" >
	          <input type="text" class="form-control action-input-search-metro" placeholder="'.$ULang->t("Начните вводить станции, а потом выберите ее из списка").'" >
	          <div class="custom-results SearchMetroResults" ></div>
	        </div>

	        <div class="ads-container-metro-station" >'.$list_metro.'</div>
	     </div>

	</div>
	';

}


if(!$settings["ad_create_currency"]){

	$dropdown_currency = '
	      <div class="input-dropdown-box">
	        <div class="uni-dropdown-align" >
	           <span class="input-dropdown-name-display"> '.$settings["currency_data"][ $data["ads_currency"] ]["sign"].' </span>
	        </div>
	      </div>
	';

}else{

	$getCurrency = getAll("select * from uni_currency order by id_position asc");
	if ($getCurrency) {
	  foreach ($getCurrency as $key => $value) {
	     $list_currency .= '<span data-value="'.$value["code"].'" data-name="'.$value["sign"].'" data-input="currency" >'.$value["name"].' ('.$value["sign"].')</span>';
	  }
	}

	$dropdown_currency = '
	    <div class="input-dropdown-box">
	      
	        <span class="uni-dropdown-bg">
	         <div class="uni-dropdown uni-dropdown-align" >
	            <span class="uni-dropdown-name" > <span>'.$settings["currency_data"][ $data["ads_currency"] ]["sign"].'</span> <i class="las la-angle-down"></i> </span>
	            <div class="uni-dropdown-content" >
	               '.$list_currency.'
	            </div>
	         </div>
	        </span>

	    </div>
	';

}


$field_price_name = $Ads->variantPrice( $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_variant_price"] );
$getShop = $Shop->getUserShop( $_SESSION["profile"]["id"] );


if( $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_display_price"] ){

  if($getShop && $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_variant_price"] != 1 && !$getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_auction"]){

     $data["price"] .= '
        <div class="ads-create-main-data-box-item" >
            <p class="ads-create-subtitle" >'.$ULang->t("Акция").'</p>
            <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Вы можете включить акцию для своего объявления. В каталоге объявлений будет показываться старая и новая цена. Акция работает только при активном магизине.").'</div>
            <div class="custom-control custom-checkbox mt15">
                <input type="checkbox" class="custom-control-input" '.($data["ads_price_old"] ? 'checked=""' : '').' name="stock" id="stock" value="1">
                <label class="custom-control-label" for="stock">'.$ULang->t("Включить акцию").'</label>
            </div>
        </div>
     ';

  }

  
  $data["price"] .= '
      <div class="ads-create-main-data-box-item" >
      <p class="ads-create-subtitle" >'.$field_price_name.'</p>
  ';

  if( $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_auction"] ){

      if( $data["ads_auction"] ){

           $price = '

                <div class="ads-create-main-data-box-item" >

                    <p class="ads-create-subtitle" >'.$ULang->t("С какой цены начать торг?").'</p>

                    <div class="row" >
                      <div class="col-lg-6" >
                          <div class="input-dropdown" >
                             <input type="text" name="price" class="ads-create-input inputNumber" value="'.number_format($data["ads_price"],0,"."," ").'" maxlength="11" > 
                             '.$dropdown_currency.'
                          </div>
                          <div class="msg-error" data-name="price" ></div>
                      </div>
                    </div>

                </div>

                <div class="ads-create-main-data-box-item" >

                    <p class="ads-create-subtitle" >'.$ULang->t("Цена продажи").'</p>
                    <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Укажите цену, за которую вы готовы сразу продать товар или оставьте это поле пустым если у аукциона нет ограничений по цене.").'</div>

                    <div class="mt15" ></div>

                    <div class="row" >
                      <div class="col-lg-6" >
                          <div class="input-dropdown" >
                             <input type="text" name="auction_price_sell" class="ads-create-input inputNumber" value="'.number_format($data["ads_auction_price_sell"],0,"."," ").'" maxlength="11" > 
                             <div class="input-dropdown-box">
                                <div class="uni-dropdown-align" >
                                   <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_data"][ $data["ads_currency"] ]["sign"].' </span>
                                </div>
                             </div>
                          </div>
                          <div class="msg-error" data-name="auction_price_sell" ></div>
                      </div>
                    </div>

                </div>

                <div class="ads-create-main-data-box-item" >

                    <p class="ads-create-subtitle" >'.$ULang->t("Длительность торгов").'</p>
                    <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Укажите срок действия аукциона от 1-го до 30-ти дней.").'</div>

                    <div class="mt15" ></div>

                    <div class="row" >
                      <div class="col-lg-3" >
                          <input type="text" name="auction_duration_day" value="'.$data["ads_auction_day"].'" class="ads-create-input" maxlength="2" value="1" > 
                          <div class="msg-error" data-name="auction_duration_day" ></div>
                      </div>
                    </div>

                </div>

           ';

      }else{

           if($getShop){

             $stock = '
                <div class="ads-create-main-data-box-item" style="margin-bottom: 25px;" >
                    <p class="ads-create-subtitle" >Акция</p>
                    <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Вы можете включить акцию для своего объявления. В каталоге объявлений будет показываться старая и новая цена. Акция работает только при активном магизине.").'</div>
                    <div class="custom-control custom-checkbox mt15">
                        <input type="checkbox" class="custom-control-input" name="stock" '.($data["ads_price_old"] ? 'checked=""' : '').' id="stock" value="1">
                        <label class="custom-control-label" for="stock">'.$ULang->t("Включить акцию").'</label>
                    </div>
                </div>
             ';

           }  


           if( $data["ads_price_old"] ){    

	           $price = '

		           <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
		              <div class="row" >
		                <div class="col-lg-6" >

		                    <div class="input-dropdown" >
		                       <input type="text" name="price" placeholder="'.$ULang->t("Старая цена").'" value="'.number_format($data["ads_price_old"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
		                       '.$dropdown_currency.'
		                    </div>
		                    <div class="msg-error" data-name="price" ></div>

		                </div>
		                <div class="col-lg-6" >

		                    <div class="input-dropdown" >
		                       <input type="text" name="stock_price" placeholder="'.$ULang->t("Новая цена").'" value="'.number_format($data["ads_price"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
		                       <div class="input-dropdown-box">
		                          <div class="uni-dropdown-align" >
		                             <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_data"][ $data["ads_currency"] ]["sign"].' </span>
		                          </div>
		                       </div>
		                    </div>

		                </div>                
		              </div>
		           </div>

	           ';     

           }else{

	           $price .= '
	              <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
	              <div class="row" >

	                <div class="col-lg-6" >

	                    <div class="input-dropdown" >
	                       <input type="text" name="price" '.($data["ads_price_free"] ? 'disabled=""' : '').' placeholder="'.$field_price_name.'" value="'.number_format($data["ads_price"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
	                       '.$dropdown_currency.'
	                    </div>
	                    <div class="msg-error" data-name="price" ></div>

	                </div>
	           ';
	                
	            if( $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_variant_price"] == 0 ){

	                $price .= '
	                <div class="col-lg-6" >

	                    <div class="custom-control custom-checkbox mt10">
	                        <input type="checkbox" class="custom-control-input" '.($data["ads_price_free"] ? 'checked=""' : '').' name="price_free" id="price_free" value="1">
	                        <label class="custom-control-label" for="price_free">'.$ULang->t("Отдам даром").'</label>
	                    </div>

	                </div> 
	                ';

	            }

	           $price .= '
	              </div> 
	              </div>          
	           ';         

           }

      }

      $data["price"] .= '
           <div class="row" >
               <div class="col-lg-6" >
                  <div data-var="fix" class="ads-create-main-data-price-variant '.(!$data["ads_auction"] ? 'ads-create-main-data-price-variant-active' : '').'" >
                     <div >
                       <i class="las la-money-bill-wave"></i>
                       <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Фиксированная").'</span>
                     </div>
                  </div>
               </div>
               <div class="col-lg-6" >
                  <div data-var="auction" class="ads-create-main-data-price-variant '.($data["ads_auction"] ? 'ads-create-main-data-price-variant-active' : '').'" >
                     <div >
                       <i class="las la-gavel"></i>
                       <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Аукцион").'</span>
                     </div>                          
                  </div>
               </div>                     
           </div>
           <div class="mb25" ></div>
           <div class="ads-create-main-data-stock-container" >'.$stock.'</div>
           <div class="ads-create-main-data-price-container" >'.$price.'</div>
      ';

  }else{

      if( $data["ads_price_old"] ){    

         $data["price"] .= '

            <div class="ads-create-main-data-price-container" >
               <div class="row" >
               <div class="col-lg-6" >

                     <div class="input-dropdown" >
                        <input type="text" name="price" placeholder="'.$ULang->t("Старая цена").'" value="'.number_format($data["ads_price_old"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
                        '.$dropdown_currency.'
                     </div>
                     <div class="msg-error" data-name="price" ></div>

               </div>
               <div class="col-lg-6" >

                     <div class="input-dropdown" >
                        <input type="text" name="stock_price" placeholder="'.$ULang->t("Новая цена").'" value="'.number_format($data["ads_price"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
                        <div class="input-dropdown-box">
                           <div class="uni-dropdown-align" >
                              <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_data"][ $data["ads_currency"] ]["sign"].' </span>
                           </div>
                        </div>
                     </div>

               </div>                
               </div>
            </div>

         ';     

      }else{

         $data["price"] .= '
            <div class="ads-create-main-data-price-container" >
            <div class="row" >

            <div class="col-lg-6" >

                  <div class="input-dropdown" >
                     <input type="text" name="price" '.($data["ads_price_free"] ? 'disabled=""' : '').' placeholder="'.$field_price_name.'" value="'.number_format($data["ads_price"],0,"."," ").'" class="ads-create-input inputNumber" maxlength="11" > 
                     '.$dropdown_currency.'
                  </div>
                  <div class="msg-error" data-name="price" ></div>

            </div>
         ';
            
         if( $getCategoryBoard["category_board_id"][$data["ads_id_cat"]]["category_board_variant_price"] == 0 ){

            $data["price"] .= '
            <div class="col-lg-6" >

                  <div class="custom-control custom-checkbox mt10">
                     <input type="checkbox" class="custom-control-input" '.($data["ads_price_free"] ? 'checked=""' : '').' name="price_free" id="price_free" value="1">
                     <label class="custom-control-label" for="price_free">'.$ULang->t("Отдам даром").'</label>
                  </div>

            </div> 
            ';

         }

         $data["price"] .= '
            </div> 
            </div>          
         ';         

      }

            
  }
   
 $data["price"] .= '
    </div>             
 ';               

}


echo $Main->tpl("ad_update.tpl", compact( 'Seo','Geo','Main','visible_footer','Ads','route_name','Profile','CategoryBoard','getCategoryBoard','data','settings','list_period','Filters','Banners', 'ULang', 'dropdown_currency','Cart' ) );

?>