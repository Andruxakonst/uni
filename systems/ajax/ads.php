<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );
$static_msg = require $config["basePath"] . "/static/msg.php";

verify_csrf_token(['ad-create','ad-update']);

$Ads = new Ads();
$Profile = new Profile();
$Main = new Main();
$Admin = new Admin();
$CategoryBoard = new CategoryBoard();
$Geo = new Geo();
$Seo = new Seo();
$Filters = new Filters();
$Banners = new Banners();
$Elastic = new Elastic();
$ULang = new ULang();
$Watermark = new Watermark();
$Subscription = new Subscription();
$Shop = new Shop();
$Cache = new Cache();
$Cart = new Cart();

$Profile->checkAuth();

verify_auth(['confirm_transfer_goods','remove_publication','ads_status_sell','ads_publication','ads_delete','auction_cancel_rate','confirm_receive_goods','order_cancel_deal','order_cancel_deal_marketplace','order_delete_marketplace','order_change_status','add_disputes','auction_accept_order_reservation','create_accept_phone','load_booking','add_order_booking','order_delete_booking','order_confirm_booking','order_prepayment_booking','order_cancel_booking']);

if(isAjax() == true){

  if($_POST["action"] == "create_load_category"){

       $id = (int)$_POST["id"];

       $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");

       $filters = $Filters->load_filters_ad( $id );

       if ( $getCategories["category_board_id_parent"][$id] ) {
            
            if( $_POST["var"] == "create" ){

                $lenght = floor(count($getCategories["category_board_id_parent"][$id]) / 2);

                $chunk = array_chunk($getCategories["category_board_id_parent"][$id], $lenght ? $lenght : 1, true);

                foreach ($chunk as $key => $nested) {
                   
                    $parent_list .= '<div class="col-lg-6 col-md-6 col-sm-6 col-12" >';

                    foreach ($nested as $key => $parent_value) {

                        $parent_list .=  '<span data-id="'.$parent_value["category_board_id"].'" >'.$ULang->t($parent_value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'</span>';

                    }

                    $parent_list .= '</div>';

                }

                if( $getCategories["category_board_id"][$id]["category_board_id_parent"] ){
                  $prev = '<span class="ads-create-subcategory-prev" data-id="'.$getCategories["category_board_id"][$id]["category_board_id_parent"].'" ><i class="las la-arrow-left"></i></span>';
                }

                $data = '
                  <p class="ads-create-subtitle mt30" > '.$prev.' '.$ULang->t("Выберите подкатегорию").'</p>

                  <div class="ads-create-subcategory-list" >
                     <div class="row" >' . $parent_list . '</div>
                  </div>
                ';

            }elseif( $_POST["var"] == "update" ){

                foreach ($getCategories["category_board_id_parent"][$id] as $key => $parent_value) {
                   
                    $parent_list .=  '<span data-id="'.$parent_value["category_board_id"].'" data-name="'.$CategoryBoard->breadcrumb($getCategories,$parent_value["category_board_id"],'{NAME}',' &rsaquo; ').'" >'.$ULang->t($parent_value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'</span>';

                }

                if( $id ){
                    $prev = '<span data-id="'.$getCategories["category_board_id"][$id]["category_board_id_parent"].'" ><i class="las la-arrow-left"></i> '.$ULang->t("Назад").'</span>';
                }

                $data = $prev . $parent_list;

            }
            
            echo json_encode( array("subcategory" => true, "data" => $data ) );

       }else{

          $data = [];

          if( !$getCategories["category_board_id"][$id]["category_board_auto_title"] ){

             $data["title"] = '
                <div class="ads-create-main-data-box-item" style="margin-top: 0px; margin-bottom: 25px;" >
                    <p class="ads-create-subtitle" >'.$ULang->t("Название").'</p>
                    <input type="text" name="title" class="ads-create-input" >
                    <p class="create-input-length" >'.$ULang->t("Символов").' <span>0</span> '.$ULang->t("из").' '.$settings["ad_create_length_title"].'</p>
                    <div class="msg-error" data-name="title" ></div>
                </div>
             ';

          }

          if( $getCategories["category_board_id"][$id]["category_board_online_view"] ){

             $data["online_view"] = '
                 <div class="ads-create-main-data-box-item" >
                    <p class="ads-create-subtitle" >'.$ULang->t("Онлайн-показ").'</p>
                    <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Выберите, если готовы показать товар/объект с помощью видео-звонка — например, через WhatsApp, Viber, Skype или другой сервис").'</div>
                    <div class="custom-control custom-checkbox mt15">
                        <input type="checkbox" class="custom-control-input" name="online_view" id="online_view" value="1">
                        <label class="custom-control-label" for="online_view">'.$ULang->t("Готовы показать онлайн").'</label>
                    </div>
                 </div>
             ';

          }

          if( $getCategories["category_board_id"][$id]["category_board_booking"] ){

             if($getCategories["category_board_id"][$id]["category_board_booking_variant"] == 0){

                 $data["booking"] = '
                     <div class="ads-create-main-data-box-item" >
                        <p class="ads-create-subtitle" >'.$ULang->t("Онлайн-бронирование").'</p>
                        <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Выберите, если хотите сдавать объект в аренду. Пользователи смогут бронировать онлайн.").'</div>
                        <div class="custom-control custom-checkbox mt15">
                            <input type="checkbox" class="custom-control-input" name="booking" id="booking" value="1">
                            <label class="custom-control-label" for="booking">'.$ULang->t("Онлайн-бронирование").'</label>
                        </div>
                     </div>
                 ';

             }elseif($getCategories["category_board_id"][$id]["category_board_booking_variant"] == 1){
                
                 $data["booking"] = '
                     <div class="ads-create-main-data-box-item" >
                        <p class="ads-create-subtitle" >'.$ULang->t("Онлайн-аренда").'</p>
                        <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Выберите, если хотите сдавать товар/объект в аренду. Пользователи смогут брать в аренду онлайн.").'</div>
                        <div class="custom-control custom-checkbox mt15">
                            <input type="checkbox" class="custom-control-input" name="booking" id="booking" value="1">
                            <label class="custom-control-label" for="booking">'.$ULang->t("Онлайн-аренда").'</label>
                        </div>
                     </div>
                 ';

             }

          }

          if( $Cart->modeAvailableCart($getCategories,$id,$_SESSION["profile"]["id"]) ){

             $data["available"] = '

                  <div class="ads-create-main-data-box-item" >

                      <p class="ads-create-subtitle" >'.$ULang->t("В наличии").'</p>

                      <div class="row" >
                        
                        <div class="col-lg-6" >
                            <input type="text" name="available" placeholder="" class="ads-create-input" maxlength="5" disabled="" >
                            <div class="msg-error" data-name="available" ></div>
                        </div>
                        
                        <div class="col-lg-6" >

                            <div class="custom-control custom-checkbox mt10">
                                <input type="checkbox" class="custom-control-input" name="available_unlimitedly" id="available_unlimitedly" value="1" checked="" >
                                <label class="custom-control-label" for="available_unlimitedly">'.$ULang->t("Неограниченно").'</label>
                            </div>

                        </div> 

                      </div>

                  </div>

             ';

          }

          if( $filters ){

             $getCategory = $Filters->getCategory( ["id_cat" => $id] );
             
             if( $getCategory ){

                 $getFilters = getAll( "select * from uni_ads_filters where ads_filters_id IN(".implode(",", $getCategory).")" );

                 if(count($getFilters)){

                    foreach ( $getFilters as $key => $value) {
                        $list_filters[] = $ULang->t( $value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] );
                    }

                    $data["filters"] = '
                       <div class="ads-create-main-data-box-item" >
                          <p class="ads-create-subtitle" >'.$ULang->t("Характеристики").'</p>
                          <div class="ads-create-main-data-filters-spoiler" >
                            <div class="ads-create-list-filters-show create-info" ><i class="las la-plus-circle"></i> '.implode(", ", $list_filters).'</div>
                          </div>
                          <div class="ads-create-main-data-filters-list" >
                          <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Укажите как можно больше параметров - это повысит интерес к объявлению.").'</div>
                          <div class="mb25" ></div>
                          '.$filters.'
                          </div>
                       </div> 
                    ';

                 }

             }

          }

          if( $getCategories["category_board_id"][$id]["category_board_display_price"] ){

              $field_price_name = $Main->nameInputPrice($getCategories["category_board_id"][$id]["category_board_variant_price_id"]);

              $data["price"] .= '
                  <div class="ads-create-main-data-box-item" >
                  <p class="ads-create-subtitle" >'.$field_price_name.'</p>
              ';

              if( $getCategories["category_board_id"][$id]["category_board_auction"] ){
                  $data["price"] .= '
                       <div class="row" >
                           <div class="col-lg-4" >
                              <div data-var="fix" class="ads-create-main-data-price-variant" >
                                 <div>
                                   <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Фиксированная").'</span>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-4" >
                              <div data-var="from" class="ads-create-main-data-price-variant" >
                                 <div>
                                   <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Не фиксированная").'</span>
                                 </div>
                              </div>
                           </div>                           
                           <div class="col-lg-4" >
                              <div data-var="auction" class="ads-create-main-data-price-variant" >
                                 <div>
                                   <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Аукцион").'</span>
                                 </div>                          
                              </div>
                           </div>                     
                       </div>
                       <div class="mb25" ></div>
                       <div class="ads-create-main-data-stock-container" ></div>
                       <div class="ads-create-main-data-price-container" ></div>
                  ';
              }else{
                  $data["price"] .= '
                       <div class="row" >
                           <div class="col-lg-6" >
                              <div data-var="fix" class="ads-create-main-data-price-variant" >
                                 <div>
                                   <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Фиксированная").'</span>
                                 </div>
                              </div>
                           </div>
                           <div class="col-lg-6" >
                              <div data-var="from" class="ads-create-main-data-price-variant" >
                                 <div>
                                   <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Не фиксированная").'</span>
                                 </div>
                              </div>
                           </div>                                                
                       </div>
                       <div class="mb25" ></div>
                       <div class="ads-create-main-data-stock-container" ></div>
                       <div class="ads-create-main-data-price-container" ></div>
                  ';                
              }

              
               
             $data["price"] .= '
                </div>             
             ';               

          }

          echo json_encode( array( "subcategory" => false, "data" => $data ) );

       }

  }

  if($_POST["action"] == "create_load_variant_price"){

      $id = (int)$_POST["id"];
      $variant = clear($_POST["variant"]);
      $booking = $_POST["booking"] == 'true' ? 1 : 0;

      $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");

      $field_price_name = $Main->nameInputPrice($getCategories["category_board_id"][$id]["category_board_variant_price_id"]);

      if(!$settings["ad_create_currency"]){
        
        $dropdown_currency = '
              <div class="input-dropdown-box">
                <div class="uni-dropdown-align" >
                   <span class="input-dropdown-name-display"> '.$settings["currency_main"]["sign"].' </span>
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
                    <span class="uni-dropdown-name" > <span>'.$settings["currency_main"]["sign"].'</span> <i class="las la-angle-down"></i> </span>
                    <div class="uni-dropdown-content" >
                       '.$list_currency.'
                    </div>
                 </div>
                </span>

            </div>
        ';

      }

      $getShop = $Shop->getUserShop( $_SESSION["profile"]["id"] );

      if($getShop && $getCategories["category_board_id"][$id]["category_board_rules"]["accept_promo"]){

         $data["stock"] = '
            <div class="ads-create-main-data-box-item" style="margin-bottom: 25px;" >
                <p class="ads-create-subtitle" >Акция</p>
                <div class="create-info" ><i class="las la-question-circle"></i> '.$ULang->t("Вы можете включить акцию для своего объявления. В каталоге объявлений будет показываться старая и новая цена. Акция работает только при активном магизине.").'</div>
                <div class="custom-control custom-checkbox mt15">
                    <input type="checkbox" class="custom-control-input" name="stock" id="stock" value="1">
                    <label class="custom-control-label" for="stock">'.$ULang->t("Включить акцию").'</label>
                </div>
            </div>
         ';

      }

      if($getCategories["category_board_id"][$id]["category_board_measures_price"]){

            $measuresPrice = json_decode($getCategories["category_board_id"][$id]["category_board_measures_price"], true);

            if($measuresPrice){

                foreach ($measuresPrice as $value) {
                   $listMeasures .= '<label> <input type="radio" name="measure" value="'.$value.'" > <span>'.getNameMeasuresPrice($value).'</span> <i class="la la-check"></i> </label>';
                }

                $measures = '
                    <div class="col-lg-6" >
                        <div class="uni-select" data-status="0" >

                             <div class="uni-select-name" data-name="'.$ULang->t("Не выбрано").'" > <span>'.$ULang->t("Не выбрано").'</span> <i class="la la-angle-down"></i> </div>
                             <div class="uni-select-list" >
                                 '.$listMeasures.'
                             </div>
                        
                        </div>
                        <div class="msg-error" data-name="measure" ></div> 
                    </div>
                ';

                $measures_lg4 = '
                    <div class="col-lg-4" >
                        <div class="uni-select" data-status="0" >

                             <div class="uni-select-name" data-name="'.$ULang->t("Не выбрано").'" > <span>'.$ULang->t("Не выбрано").'</span> <i class="la la-angle-down"></i> </div>
                             <div class="uni-select-list" >
                                 '.$listMeasures.'
                             </div>
                        
                        </div> 
                        <div class="msg-error" data-name="measure" ></div>
                    </div>
                ';

            }

      }

      if($getCategories["category_board_id"][$id]["category_board_rules"]["measure_booking"]){
         if(!$booking) $measures = '';
      }

      if( $variant == "fix" ){

           $data["price"] .= '
              <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
              <div class="row" >

                <div class="col-lg-6" >

                    <div class="input-dropdown" >
                       <input type="text" name="price" placeholder="'.$field_price_name.'" class="ads-create-input inputNumber" maxlength="11" > 
                       '.$dropdown_currency.'
                    </div>
                    <div class="msg-error" data-name="price" ></div>

                </div>
           ';

            $data["price"] .= $measures;
                
            if( $getCategories["category_board_id"][$id]["category_board_rules"]["free_price"] ){

                $data["price"] .= '
                <div class="col-lg-6" >

                    <div class="custom-control custom-checkbox mt10">
                        <input type="checkbox" class="custom-control-input" name="price_free" id="price_free" value="1">
                        <label class="custom-control-label" for="price_free">'.$ULang->t("Отдам даром").'</label>
                    </div>

                </div> 
                ';

            }

           $data["price"] .= '
              </div> 
              </div>          
           ';

      }elseif( $variant == "auction" ){

           $data["stock"] = '';

           $data["price"] .= '
                
                <div class="ads-create-main-data-box-item" >

                    <p class="ads-create-subtitle" >'.$ULang->t("С какой цены начать торг?").'</p>

                    <div class="row" >
                      <div class="col-lg-6" >
                          <div class="input-dropdown" >
                             <input type="text" name="price" class="ads-create-input inputNumber" maxlength="11" > 
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
                             <input type="text" name="auction_price_sell" class="ads-create-input inputNumber" maxlength="11" > 
                             <div class="input-dropdown-box">
                                <div class="uni-dropdown-align" >
                                   <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_main"]["sign"].' </span>
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
                          <input type="text" name="auction_duration_day" class="ads-create-input" maxlength="2" value="1" > 
                          <div class="msg-error" data-name="auction_duration_day" ></div>
                      </div>
                    </div>

                </div>

            ';
                           

      }elseif( $variant == "stock" ){

           if($measures){

               $data["price"] .= '
               <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
                  <div class="row" >
                    <div class="col-lg-4" >

                        <div class="input-dropdown" >
                           <input type="text" name="price" placeholder="'.$ULang->t("Старая цена").'" class="ads-create-input inputNumber" maxlength="11" > 
                           '.$dropdown_currency.'
                        </div>
                        <div class="msg-error" data-name="price" ></div>

                    </div>
                    <div class="col-lg-4" >

                        <div class="input-dropdown" >
                           <input type="text" name="stock_price" placeholder="'.$ULang->t("Новая цена").'" class="ads-create-input inputNumber" maxlength="11" > 
                           <div class="input-dropdown-box">
                              <div class="uni-dropdown-align" >
                                 <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_main"]["sign"].' </span>
                              </div>
                           </div>
                        </div>

                    </div> 
                    '.$measures_lg4.'               
                  </div>
               </div>
               ';

           }else{

               $data["price"] .= '
               <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
                  <div class="row" >
                    <div class="col-lg-6" >

                        <div class="input-dropdown" >
                           <input type="text" name="price" placeholder="'.$ULang->t("Старая цена").'" class="ads-create-input inputNumber" maxlength="11" > 
                           '.$dropdown_currency.'
                        </div>
                        <div class="msg-error" data-name="price" ></div>

                    </div>
                    <div class="col-lg-6" >

                        <div class="input-dropdown" >
                           <input type="text" name="stock_price" placeholder="'.$ULang->t("Новая цена").'" class="ads-create-input inputNumber" maxlength="11" > 
                           <div class="input-dropdown-box">
                              <div class="uni-dropdown-align" >
                                 <span class="input-dropdown-name-display static-currency-sign"> '.$settings["currency_main"]["sign"].' </span>
                              </div>
                           </div>
                        </div>

                    </div>                
                  </div>
               </div>
               ';

           }

    
      }elseif( $variant == "from" ){

           $data["price"] .= '
              <div class="ads-create-main-data-box-item" style="margin-top: 0px;" >
              <div class="row" >

                <div class="col-lg-6" >

                    <div class="ads-create-main-data-box-item-flex" >
                        <div class="ads-create-main-data-box-item-flex1" >
                            <span>'.$ULang->t("От").'</span>
                        </div>
                        <div class="ads-create-main-data-box-item-flex2" >
                            <div class="input-dropdown" >
                               <input type="text" name="price" placeholder="'.$field_price_name.'" class="ads-create-input inputNumber" maxlength="11" > 
                               '.$dropdown_currency.'
                            </div>
                            <div class="msg-error" data-name="price" ></div>
                        </div>                        
                    </div>

                </div>
           ';

           $data["price"] .= $measures;
                
           $data["price"] .= '
              </div> 
              </div>          
           ';

      }elseif( $variant == "booking_measure" ){

           if($getCategories["category_board_id"][$id]["category_board_booking_variant"] == 0){

               $data["booking_options"] = '
                   <div class="ads-create-main-data-box-item" >

                       <p class="ads-create-subtitle" >'.$ULang->t("Предоплата").'</p>

                       <div class="create-info"><i class="las la-question-circle"></i> '.$ULang->t("Оставьте это поле пустым если предоплата не требуется.").'</div>

                       <div class="mb15" ></div>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <div class="input-dropdown" >
                               <input type="number" name="booking_prepayment_percent" placeholder="'.$ULang->t("Процент предоплаты").'" class="ads-create-input" maxlength="3" > 
                               <div class="input-dropdown-box">
                                  <div class="uni-dropdown-align" >
                                     <span class="input-dropdown-name-display">%</span>
                                  </div>
                               </div>
                            </div>
                        </div>
                         
                       </div>

                       <div class="mb25" ></div>

                       <p class="ads-create-subtitle" >'.$ULang->t("Максимальное количество гостей").'</p>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <input type="number" name="booking_max_guests" class="ads-create-input" maxlength="11" value="3" >
                        </div>
                         
                       </div>

                       <div class="mb25" ></div>

                       <div class="create-info"><i class="las la-question-circle"></i> '.$ULang->t("Оставьте эти поля пустыми если ограничений нет.").'</div>

                       <div class="mb15" ></div>

                       <p class="ads-create-subtitle" >'.$ULang->t("Минимум дней аренды").'</p>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <input type="number" name="booking_min_days" class="ads-create-input" maxlength="11" >
                        </div>
                         
                       </div>

                       <div class="mb25" ></div>

                       <p class="ads-create-subtitle" >'.$ULang->t("Максимум дней аренды").'</p>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <input type="number" name="booking_max_days" class="ads-create-input" maxlength="11" >
                        </div>
                         
                       </div>

                       <div class="mb25" ></div>

                       <p class="ads-create-subtitle data-count-services" data-count-services="'.$settings['count_add_booking_additional_services'].'" >'.$ULang->t("Дополнительные услуги").' <span class="booking-additional-services-item-add btn-custom-mini btn-custom-mini-icon btn-color-blue-light" ><i class="las la-plus"></i></span></p>

                       <div class="booking-additional-services-container" ></div>

                       <div class="mb25" ></div>
                   </div>           
               ';

           }elseif($getCategories["category_board_id"][$id]["category_board_booking_variant"] == 1){

               $data["booking_options"] = '
                   <div class="ads-create-main-data-box-item" >

                       <p class="ads-create-subtitle" >'.$ULang->t("Предоплата").'</p>

                       <div class="create-info"><i class="las la-question-circle"></i> '.$ULang->t("Оставьте это поле пустым если предоплата не требуется.").'</div>

                       <div class="mb15" ></div>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <div class="input-dropdown" >
                               <input type="number" name="booking_prepayment_percent" placeholder="'.$ULang->t("Процент предоплаты").'" class="ads-create-input" maxlength="3" > 
                               <div class="input-dropdown-box">
                                  <div class="uni-dropdown-align" >
                                     <span class="input-dropdown-name-display">%</span>
                                  </div>
                               </div>
                            </div>
                        </div>
                         
                       </div>

                       <div class="mb25" ></div>

                       <p class="ads-create-subtitle" >'.$ULang->t("Доступно").'</p>

                       <div class="create-info"><i class="las la-question-circle"></i> '.$ULang->t("Укажите сколько единиц доступно для аренды. По истечению лимита аренда будет недоступна. Система автоматически вернет возможность аренды после того, как у пользователя закончится выбранный срок.").'</div>

                       <div class="mb15" ></div>

                       <div class="row" >
                        
                        <div class="col-lg-6" >
                            <input type="number" name="booking_available" placeholder="'.$ULang->t("Доступно").'" class="ads-create-input" maxlength="3" >
                        </div>
                         
                        <div class="col-lg-6" >
                            <div class="custom-control custom-checkbox mt10">
                                <input type="checkbox" class="custom-control-input" name="booking_available_unlimitedly" id="booking_available_unlimitedly" value="1" >
                                <label class="custom-control-label" for="booking_available_unlimitedly">'.$ULang->t("Неограниченно").'</label>
                            </div>                                                
                        </div>

                       </div>

                       <div class="mb25" ></div>

                       <p class="ads-create-subtitle data-count-services" data-count-services="'.$settings['count_add_booking_additional_services'].'" >'.$ULang->t("Дополнительные услуги").' <span class="booking-additional-services-item-add btn-custom-mini btn-custom-mini-icon btn-color-blue-light" ><i class="las la-plus"></i></span></p>

                       <div class="booking-additional-services-container" ></div>

                       <div class="mb25" ></div>
                   </div>           
               ';

           }

           $data["price"] .= '
               <div class="ads-create-main-data-box-item" >
                   <p class="ads-create-subtitle" >'.$field_price_name.'</p>           
                   <div class="row" >
                       <div class="col-lg-6" >
                          <div data-var="fix" class="ads-create-main-data-price-variant" >
                             <div>
                               <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Фиксированная").'</span>
                             </div>
                          </div>
                       </div>
                       <div class="col-lg-6" >
                          <div data-var="from" class="ads-create-main-data-price-variant" >
                             <div>
                               <span class="ads-create-main-data-price-variant-name" >'.$ULang->t("Не фиксированная").'</span>
                             </div>
                          </div>
                       </div>                                    
                   </div>
                   <div class="mb25" ></div>
                   <div class="ads-create-main-data-stock-container" ></div>
                   <div class="ads-create-main-data-price-container" ></div>
               </div>
           ';
 
      }

      echo json_encode( $data );


  }

  if($_POST["action"] == "create_load_booking_options"){

      $rand_id = mt_rand(10000, 90000);

      echo '
           <div class="booking-additional-services-item" >
                <div class="booking-additional-services-item-row" >
                    <div class="booking-additional-services-item-row1" >
                        <input type="text" name="booking_additional_services['.$rand_id.'][name]" placeholder="'.$ULang->t("Название услуги").'" class="ads-create-input" >
                    </div>
                    <div class="booking-additional-services-item-row2" >
                        <div class="input-dropdown" >
                           <input type="text" name="booking_additional_services['.$rand_id.'][price]" placeholder="'.$ULang->t("Цена").'" class="ads-create-input" maxlength="11" > 
                           <div class="input-dropdown-box">
                              <div class="uni-dropdown-align" >
                                 <span class="input-dropdown-name-display"> '.$settings["currency_main"]["sign"].' </span>
                              </div>
                           </div>
                        </div>
                    </div>
                    <div class="booking-additional-services-item-row3" >
                        <span class="booking-additional-services-item-delete" ><i class="las la-trash-alt"></i></span>
                    </div>                                                                
                </div>
           </div>
      ';

  }

  if($_POST["action"] == "ad-update"){

        $id_ad = (int)$_POST["id_ad"];

        if(!$_SESSION['cp_auth'][ $config["private_hash"] ] && !$_SESSION['cp_control_board']){

          if($_SESSION["profile"]["id"]){ 
             
             $getAd = $Ads->get("ads_id=? and ads_id_user=?", [$id_ad,intval($_SESSION["profile"]["id"])]);

          }else{

             exit;

          }

        }else{

          $getAd = $Ads->get("ads_id=?", [$id_ad]);

        }
        
        if($_POST["metro"]){
          if(!is_array($_POST["metro"])){
             $_POST["metro"] = [];
          }
        }else{
          $_POST["metro"] = [];
        }

        if($_POST["area"]){
          if(!is_array($_POST["area"])){
             $_POST["area"] = [];
          }else{
             $_POST["area"] = array_slice($_POST["area"], 0,1);
          }
        }else{
          $_POST["area"] = [];
        }

        if(abs($_POST["booking_prepayment_percent"])){
            if(abs($_POST["booking_prepayment_percent"]) > 100){
                $_POST["booking_prepayment_percent"] = 100;
            }else{
                $_POST["booking_prepayment_percent"] = abs($_POST["booking_prepayment_percent"]);
            }
        }else{
            $_POST["booking_prepayment_percent"] = 0;
        }
        
        $getCategories = (new CategoryBoard())->getCategories("where category_board_visible=1");

        $error = $Ads->validationAdForm($_POST, ["categories"=>$getCategories] );

        $period = $Ads->adPeriodPub($_POST["period"]);

        if($settings["ad_create_currency"] && $_POST["currency"]){
            
            if($settings["currency_data"][ $_POST["currency"] ]){
               $currency = $_POST["currency"];
            }else{
               $currency = $settings["currency_main"]["code"];
            }

        }else{

            $currency = $settings["currency_main"]["code"];
            
        }

        if( $getCategories["category_board_id"][$_POST["c_id"]]["category_board_auto_title"] ){
            $title = $Ads->autoTitle($_POST["filter"],$getCategories["category_board_id"][$_POST["c_id"]]);
        }else{
            $title = custom_substr(clear($_POST["title"]), $settings["ad_create_length_title"]);
        }

        $text = custom_substr(clear($_POST["text"]), $settings["ad_create_length_text"]);

        if( $getCategories["category_board_id"][$_POST["c_id"]]["category_board_status_paid"] ){

            $getCountCatFree = (int)getOne("select count(*) as total from uni_ads where ads_id_cat=? and ads_id_user=?", [ $_POST["c_id"], $getAd['ads_id_user'] ] )["total"];

            if($getCountCatFree > $getCategories["category_board_id"][$_POST["c_id"]]["category_board_count_free"]){

                $findOrder = findOne('uni_orders', 'orders_id_ad=? and orders_action_name=? and orders_status_pay=?', [$id_ad, 'category', 1]);
                if($findOrder){
                    $ads_status = $Ads->autoModeration($id_ad, [ "title" => $title, "text" => $text, "video" => videoLink($_POST["video"]) ] );
                }else{
                    $ads_status = 6;
                }

            }else{
               $ads_status = $Ads->autoModeration($id_ad, [ "title" => $title, "text" => $text, "video" => videoLink($_POST["video"]) ] ); 
            }

        }else{
            $ads_status = $Ads->autoModeration($id_ad, [ "title" => $title, "text" => $text, "video" => videoLink($_POST["video"]) ] );
        }

        $price_sell = 0; $duration_day = 0; $auction = 0; $stock_price = 0; $price = round(preg_replace('/\s/', '', $_POST["price"]),2);

        if($_POST["var_price"] == "auction"){

           $_POST["measure"] = '';

           $price = round(preg_replace('/\s/', '', $_POST["price"]),2);
           $price_sell = round(preg_replace('/\s/', '', $_POST["auction_price_sell"]),2);
           $duration_day = intval($_POST["auction_duration_day"]);

           if(!$price){ $error["price"] = $ULang->t("Начальная ставка не может начинаться с нуля"); }else{
              if($price_sell){
                if($price_sell < $price){
                    $error["auction_price_sell"] = $ULang->t("Цена продажи не может быть меньше начальной ставки");
                }
              }
           }
           
           if( $duration_day < 1 || $duration_day > 30 ){ $error["auction_duration_day"] = $ULang->t("Укажите длительность торгов от 1-го до 30-ти дней"); }
           
           $auction = 1;
           $auction_duration = date("Y-m-d H:i:s", time() + ($duration_day * 86400) );

        }elseif($_POST["var_price"] == "from"){

           $ads_price_from = 1;
           
        }else{

            if( $_POST["stock"] ){

               $getShop = $Shop->getUserShop( $_SESSION["profile"]["id"] );

               if( $getShop ){

                   $price = round(preg_replace('/\s/', '', $_POST["stock_price"]),2); 
                   $stock_price = round(preg_replace('/\s/', '', $_POST["price"]),2);

                   if( $price >= $stock_price ){
                       $error["price"] = $ULang->t("Новая цена должна быть меньше старой цены");
                   }

               }

            }
           
        }

        if(count($_POST["gallery"])>0 && count($error) == 0){

            $path = $config["basePath"] . "/" . $config["media"]["temp_images"];

            foreach(array_slice($_POST["gallery"], 0, $settings["count_images_add_ad"], true) AS $key => $data){
                                 
              if(file_exists($path . "/big_" . $data)){

               $gallery[] = $data;
               
               @copy($path . "/big_" . $data, $config["basePath"] . "/" . $config["media"]["big_image_ads"] . "/" . $data);
               @copy($path . "/small_" . $data, $config["basePath"] . "/" . $config["media"]["small_image_ads"] . "/" . $data);

              }else{

                $gallery[] = $data;

              } 
                   
            }
         
        }
        
        if( !$_POST["map_lat"] && !$_POST["map_lon"] ){
             $_POST["address"] = "";
        }

        if( intval($_POST["available_unlimitedly"]) ){
            $_POST["available"] = 0;
        }

        if( !$Cart->modeAvailableCart($getCategories,$_POST["c_id"],$_SESSION["profile"]["id"]) ){
            $_POST["available"] = 0;
            $_POST["available_unlimitedly"] = 1;
        }

        if($_POST["measure"]){
            $measuresPrice = json_decode($getCategories["category_board_id"][$_POST["c_id"]]["category_board_measures_price"], true);
            if(!in_array($_POST["measure"], $measuresPrice)){
                unset($_POST["measure"]);
            }
        }

        $booking_additional_services = [];

        if($_POST["booking_additional_services"] && $_POST["booking"]){
            foreach (array_slice($_POST["booking_additional_services"],0,$settings['count_add_booking_additional_services']) as $value) {
                if($value['name']) $booking_additional_services[] = ['name'=>$value['name'], 'price'=>round($value['price'],2)];
            }
        }

        if($getAd){

          if(count($error) == 0){

            if($settings["city_id"]){
              $getCity = $Geo->getCity($settings["city_id"]);
            }else{
              $getCity = $Geo->getCity($_POST["city_id"]);
            }

            if(strtotime($getAd["ads_period_publication"]) <= time()){
                $ads_period_day = $period["days"];
                $ads_period_publication = $period["date"];
            }else{
                $ads_period_day = $getAd["ads_period_day"];
                $ads_period_publication = $getAd["ads_period_publication"];                
            }

            update("UPDATE uni_ads SET ads_title=?,ads_alias=?,ads_text=?,ads_id_cat=?,ads_price=?,ads_city_id=?,ads_region_id=?,ads_country_id=?,ads_address=?,ads_latitude=?,ads_longitude=?,ads_status=?,ads_images=?,ads_metro_ids=?,ads_currency=?,ads_auction=?,ads_auction_duration=?,ads_auction_price_sell=?,ads_auction_day=?,ads_area_ids=?,ads_video=?,ads_online_view=?,ads_price_old=?,ads_filter_tags=?,ads_update=?,ads_period_day=?,ads_period_publication=?,ads_price_free=?,ads_available=?,ads_available_unlimitedly=?,ads_auto_renewal=?,ads_booking=?,ads_price_measure=?,ads_price_from=?,ads_booking_additional_services=?,ads_booking_prepayment_percent=?,ads_booking_max_guests=?,ads_booking_min_days=?,ads_booking_max_days=?,ads_booking_available=?,ads_booking_available_unlimitedly=? WHERE ads_id=?", [$title,translite($title),$text,intval($_POST["c_id"]),$price,$getCity["city_id"],$getCity["region_id"],$getCity["country_id"],clear($_POST["address"]),clear($_POST["map_lat"]),clear($_POST["map_lon"]),$ads_status,json_encode($gallery),implode(",", $_POST["metro"]),$currency,$auction,$auction_duration,$price_sell,$duration_day,implode(",", $_POST["area"]),videoLink($_POST["video"]),intval($_POST["online_view"]),$stock_price,$Filters->buildTags($_POST["filter"]),date("Y-m-d H:i:s"),$ads_period_day,$ads_period_publication,intval($_POST["price_free"]),abs($_POST["available"]),intval($_POST["available_unlimitedly"]),intval($_POST['renewal']),intval($_POST['booking']),clear($_POST["measure"]),intval($ads_price_from),json_encode($booking_additional_services,JSON_UNESCAPED_UNICODE),$_POST["booking_prepayment_percent"],intval($_POST["booking_max_guests"]),intval($_POST["booking_min_days"]),intval($_POST["booking_max_days"]),intval($_POST["booking_available"]),intval($_POST["booking_available_unlimitedly"]),$id_ad], true);

            $Ads->addMetroVariants($_POST["metro"],$id_ad);
            $Ads->addAreaVariants($_POST["area"],$id_ad);

            $Filters->addVariants($_POST["filter"],$id_ad);

            $Ads->changeStatus( $id_ad, $ads_status, "update" );

            $getAd = $Ads->get("ads_id=".$id_ad);

            if($ads_status == 0){
                notifications("ads", [ "title" => $getAd["ads_title"], "link" => $Ads->alias($getAd), "image" => $gallery[0], "user_name" => $getAd["clients_name"], "id_hash_user" => $getAd["clients_id_hash"] ] );
                $Admin->addNotification("ads");
            }

            echo json_encode( array( "status" => true, "action" => "update" , "id" => $id_ad, "location" => $Ads->alias($getAd) . "?modal=update_ad" ) );

            unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

          }else{
            echo json_encode(array("status" => false, "answer" => $error));
          }

        }


  }

  if($_POST["action"] == "ad-create"){

        if(!$_SESSION["profile"]["id"]){ exit(json_encode([ "status" => false, "auth" => true ])); }

        if($_POST["metro"]){
          if(!is_array($_POST["metro"])){
             $_POST["metro"] = [];
          }
        }else{
          $_POST["metro"] = [];
        }

        if($_POST["area"]){
          if(!is_array($_POST["area"])){
             $_POST["area"] = [];
          }else{
             $_POST["area"] = array_slice($_POST["area"], 0,1);
          }
        }else{
          $_POST["area"] = [];
        }

        if(abs($_POST["booking_prepayment_percent"])){
            if(abs($_POST["booking_prepayment_percent"]) > 100){
                $_POST["booking_prepayment_percent"] = 100;
            }else{
                $_POST["booking_prepayment_percent"] = abs($_POST["booking_prepayment_percent"]);
            }
        }else{
            $_POST["booking_prepayment_percent"] = 0;
        }

        $getCategories = (new CategoryBoard())->getCategories("where category_board_visible=1");

        $error = $Ads->validationAdForm($_POST, ["categories"=>$getCategories] );

        $period = $Ads->adPeriodPub($_POST["period"]);

        if($settings["ad_create_currency"] && $_POST["currency"]){
            
            if($settings["currency_data"][ $_POST["currency"] ]){
               $currency = $_POST["currency"];
            }else{
               $currency = $settings["currency_main"]["code"];
            }

        }else{

            $currency = $settings["currency_main"]["code"];
            
        }

        if( $getCategories["category_board_id"][$_POST["c_id"]]["category_board_auto_title"] ){
            $title = $Ads->autoTitle($_POST["filter"],$getCategories["category_board_id"][$_POST["c_id"]]);
        }else{
            $title = custom_substr(clear($_POST["title"]), $settings["ad_create_length_title"]);
        }

        $text = custom_substr(clear($_POST["text"]), $settings["ad_create_length_text"]);

        $price_sell = 0; $duration_day = 0; $auction = 0; $stock_price = 0; $price = round(preg_replace('/\s/', '', $_POST["price"]),2);

        if($_POST["var_price"] == "auction"){

           $_POST["measure"] = '';

           $price = round(preg_replace('/\s/', '', $_POST["price"]),2);
           $price_sell = round(preg_replace('/\s/', '', $_POST["auction_price_sell"]),2);
           $duration_day = intval($_POST["auction_duration_day"]);

           if(!$price){ $error["price"] = $ULang->t("Начальная ставка не может начинаться с нуля"); }else{
              if($price_sell){
                if($price_sell < $price){
                    $error["auction_price_sell"] = $ULang->t("Цена продажи не может быть меньше начальной ставки");
                }
              }
           }
           
           if( $duration_day < 1 || $duration_day > 30 ){ $error["auction_duration_day"] = $ULang->t("Укажите длительность торгов от 1-го до 30-ти дней"); }
           
           $auction = 1;
           $auction_duration = date("Y-m-d H:i:s", time() + ($duration_day * 86400) );

        }elseif($_POST["var_price"] == "from"){

           $ads_price_from = 1;
           
        }else{

            if( $_POST["stock"] ){

               $getShop = $Shop->getUserShop( $_SESSION["profile"]["id"] );

               if( $getShop ){

                   $price = round(preg_replace('/\s/', '', $_POST["stock_price"]),2); 
                   $stock_price = round(preg_replace('/\s/', '', $_POST["price"]),2);

                   if( $price >= $stock_price ){
                       $error["price"] = $ULang->t("Новая цена должна быть меньше старой цены");
                   }

               }

            }
           
        }
        
        if(count($_POST["gallery"])>0 && count($error) == 0){

            $path = $config["basePath"] . "/" . $config["media"]["temp_images"];

            foreach(array_slice($_POST["gallery"], 0, $settings["count_images_add_ad"], true) AS $key => $data){
                                 
              if(file_exists($path . "/big_" . $data)){

               $gallery[] = $data;

               @copy($path . "/big_" . $data, $config["basePath"] . "/" . $config["media"]["big_image_ads"] . "/" . $data);
               @copy($path . "/small_" . $data, $config["basePath"] . "/" . $config["media"]["small_image_ads"] . "/" . $data);

              } 
                   
            }
         
        }

        if( !$_POST["map_lat"] && !$_POST["map_lon"] ){
             $_POST["address"] = "";
        }
      
        if( intval($_POST["available_unlimitedly"]) ){
            $_POST["available"] = 0;
        }

        if( !$Cart->modeAvailableCart($getCategories,$_POST["c_id"],$_SESSION["profile"]["id"]) ){
            $_POST["available"] = 0;
            $_POST["available_unlimitedly"] = 1;
        }
      
        if($_POST['renewal']){
           if($_SESSION['profile']['tariff']['services']['scheduler']){
              $renewal = 1;
           }
        }

        if($_POST["measure"]){
            $measuresPrice = json_decode($getCategories["category_board_id"][$_POST["c_id"]]["category_board_measures_price"], true);
            if(!in_array($_POST["measure"], $measuresPrice)){
                unset($_POST["measure"]);
            }
        }

        $booking_additional_services = [];

        if($_POST["booking_additional_services"] && $_POST["booking"]){
            foreach (array_slice($_POST["booking_additional_services"],0,$settings['count_add_booking_additional_services']) as $value) {
                if($value['name']) $booking_additional_services[] = ['name'=>$value['name'], 'price'=>round($value['price'],2)];
            }
        }

        if(count($error) == 0){

          verify_mass_requests();

          if( $_SESSION["create-verify-phone"]["phone"] ){
              update( "update uni_clients set clients_phone=? where clients_id=?", [ $_SESSION["create-verify-phone"]["phone"], $_SESSION["profile"]["id"] ] );
          }

          $status = $Ads->statusAd( [ "id_cat"=>$_POST["c_id"], "categories"=>$getCategories, "text" => $text ,"title" => $title ] );

          if($settings["city_id"]){
            $getCity = $Geo->getCity($settings["city_id"]);
          }else{
            $getCity = $Geo->getCity($_POST["city_id"]);
          }

          $insert_id = insert("INSERT INTO uni_ads(ads_title,ads_alias,ads_text,ads_id_cat,ads_id_user,ads_price,ads_city_id,ads_region_id,ads_country_id,ads_address,ads_latitude,ads_longitude,ads_period_publication,ads_status,ads_note,ads_images,ads_metro_ids,ads_currency,ads_period_day,ads_datetime_add,ads_auction,ads_auction_duration,ads_auction_price_sell,ads_auction_day,ads_area_ids,ads_video,ads_online_view,ads_price_old,ads_filter_tags,ads_price_free,ads_available,ads_available_unlimitedly,ads_auto_renewal,ads_booking,ads_price_measure,ads_price_from,ads_booking_additional_services,ads_booking_prepayment_percent,ads_booking_max_guests,ads_booking_min_days,ads_booking_max_days,ads_booking_available,ads_booking_available_unlimitedly)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array( $title,translite($title),$text,intval($_POST["c_id"]),intval($_SESSION["profile"]["id"]),$price,$getCity["city_id"],$getCity["region_id"],$getCity["country_id"],clear($_POST["address"]),clear($_POST["map_lat"]),clear($_POST["map_lon"]),$period["date"],$status["status"],$status["message"],json_encode($gallery),implode(",", $_POST["metro"]),$currency,$period["days"], date("Y-m-d H:i:s"),$auction,$auction_duration,$price_sell,$duration_day,implode(",",$_POST["area"]),videoLink($_POST["video"]),intval($_POST["online_view"]), $stock_price, $Filters->buildTags($_POST["filter"]),intval($_POST["price_free"]),abs($_POST["available"]),intval($_POST["available_unlimitedly"]),intval($renewal),intval($_POST["booking"]),clear($_POST["measure"]),intval($ads_price_from), json_encode($booking_additional_services,JSON_UNESCAPED_UNICODE),$_POST["booking_prepayment_percent"],intval($_POST["booking_max_guests"]),intval($_POST["booking_min_days"]),intval($_POST["booking_max_days"]),intval($_POST["booking_available"]),intval($_POST["booking_available_unlimitedly"]) ));

          if( $insert_id ){

              $Ads->addMetroVariants($_POST["metro"],$insert_id);
              $Ads->addAreaVariants($_POST["area"],$insert_id);

              $Filters->addVariants($_POST["filter"],$insert_id);

              $getAd = $Ads->get("ads_id=".$insert_id);
              
              $Elastic->index( [ "index" => "uni_ads", "type" => "ad", "id" => $insert_id, "body" => $Elastic->prepareFields( $getAd ) ] );
              
              if( $status["status"] != 7 ){
                notifications("ads", [ "title" => $_POST["title"], "link" => $Ads->alias($getAd), "image" => $gallery[0], "user_name" => $getAd["clients_name"], "id_hash_user" => $getAd["clients_id_hash"] ] );
                $Admin->addNotification("ads");
              }

              if($status["status"] == 1 && !$getAd['clients_first_ad_publication'] && $settings["bonus_program"]["ad_publication"]["status"] && $settings["bonus_program"]["ad_publication"]["price"]){
                
                   $Profile->actionBalance(array("id_user"=>intval($_SESSION["profile"]["id"]),"summa"=>$settings["bonus_program"]["ad_publication"]["price"],"title"=>$settings["bonus_program"]["ad_publication"]["name"],"id_order"=>$config["key_rand"],"email" => $getAd["clients_email"],"name" => $getAd["clients_name"], "note" => $settings["bonus_program"]["ad_publication"]["name"]),"+");   
                   update('update uni_clients set clients_first_ad_publication=? where clients_id=?', [1, intval($_SESSION["profile"]["id"])]);            
              }

              if($status["status"] == 6){
                 $location = _link("ad/publish/".$insert_id);
              }else{
                 $location = $Ads->alias($getAd) . "?modal=new_ad";
              }

          }

          echo json_encode( [ "status" => true, "action" => "add" , "id" => $insert_id, "location" => $location ] );

          unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

        }else{
          echo json_encode( [ "status" => false, "answer" => $error ] );
        }


  }
  
  if($_POST["action"] == "complaint"){

      if(!$_SESSION["profile"]["id"]){ exit(json_encode(array("status"=>false,"answer"=>"","auth" => false))); }
    
      $id = (int)$_POST["id"];
      $text = custom_substr(clear($_POST["text"]), 2000);

      $error = array();

      if($text){

      if($_POST["action_complain"] == 'ad'){
        
        $getAd = findOne("uni_ads", "ads_id=?", array($id));

        if($getAd){

              $getComplain = findOne("uni_ads_complain", "ads_complain_id_ad=? and ads_complain_from_user_id=? and ads_complain_action=? and ads_complain_status=?", array($id,intval($_SESSION["profile"]["id"]),'ad',0));

              if(!$getComplain){

                insert("INSERT INTO uni_ads_complain(ads_complain_id_ad,ads_complain_from_user_id,ads_complain_text,ads_complain_date,ads_complain_to_user_id,ads_complain_action)VALUES(?,?,?,?,?,?)", array($id,intval($_SESSION["profile"]["id"]),$text,date("Y-m-d H:i:s"),$getAd['ads_id_user'],$_POST["action_complain"])); 

                echo json_encode(array("status"=>true,"answer"=>$ULang->t("Спасибо! Обращение успешно принято!"),"auth" => true));

                $Admin->addNotification("complaint");

                unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

              }else{

                 echo json_encode(array("status"=>true,"answer"=>$ULang->t("Ваше обращение уже принято и находится на рассмотрении."),"auth" => true));

              }

        }

      }elseif($_POST["action_complain"] == 'user'){

        $getUser = findOne("uni_clients", "clients_id=?", array($id));

        if($getUser){

              $getComplain = findOne("uni_ads_complain", "ads_complain_from_user_id=? and ads_complain_to_user_id=? and ads_complain_action=? and ads_complain_status=?", array(intval($_SESSION["profile"]["id"]),$id,'user',0));

              if(!$getComplain){

                insert("INSERT INTO uni_ads_complain(ads_complain_from_user_id,ads_complain_text,ads_complain_date,ads_complain_to_user_id,ads_complain_action)VALUES(?,?,?,?,?)", array(intval($_SESSION["profile"]["id"]),$text,date("Y-m-d H:i:s"),$getUser['clients_id'],$_POST["action_complain"])); 

                echo json_encode(array("status"=>true,"answer"=>$ULang->t("Спасибо! Обращение успешно принято!"),"auth" => true));

                $Admin->addNotification("complaint");

                unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

              }else{

                 echo json_encode(array("status"=>true,"answer"=>$ULang->t("Ваше обращение уже принято и находится на рассмотрении."),"auth" => true));

              }

        }

      }
      
      }else{
          echo json_encode(array("status"=>false,"answer"=>$ULang->t("Пожалуйста, опишите подробности нарушения"),"auth" => true));
      }

  }


  if($_POST["action"] == "service_activation"){
      
      $id_ad = (int)$_POST["id_ad"];
      $id_s = (int)$_POST["id_s"];
      $pay_v = (int)$_POST["pay_v"];

      $error = [];

      $getService = findOne("uni_services_ads", "services_ads_uid=?", array($id_s)); 
      $getAd = $Ads->get("ads_id=? and ads_id_user=?", [$id_ad,intval($_SESSION['profile']['id'])] );

      if(!$getService){ $error[] = $ULang->t("Пожалуйста, выберите услугу");}
      if(!$getAd){ $error[] = $ULang->t("Товар не найден");}

      if($getService["services_ads_variant"] == 1){
        $services_order_count_day = $getService["services_ads_count_day"];
        $price = $getService["services_ads_new_price"] ? $getService["services_ads_new_price"] : $getService["services_ads_price"];
      }else{
        $services_order_count_day = abs($_POST["service"][$id_s]) ? abs($_POST["service"][$id_s]) : 1;
        $price = $getService["services_ads_new_price"] ? $getService["services_ads_new_price"] * $services_order_count_day : $getService["services_ads_price"] * $services_order_count_day;
      }

      $services_order_time_validity = date( "Y-m-d H:i:s", strtotime("+".$services_order_count_day." days", time()) );
      
      $title = $getService["services_ads_name"] . " " . $ULang->t("на срок") . " " . $services_order_count_day . " " . ending($services_order_count_day, $ULang->t("день"), $ULang->t("дня"), $ULang->t("дней"));

      if(count($error) == 0){

        $getServiceOrder = findOne("uni_services_order", "services_order_id_service=? and services_order_id_ads=?", array($id_s,$id_ad));

        if(!$getServiceOrder){

          $getOrderServiceIds = $Ads->getOrderServiceIds( $id_ad );

           if( in_array(1, $getOrderServiceIds) || in_array(2, $getOrderServiceIds) ){

              if($id_s == 3){
                 echo json_encode( ["status"=>false, "answer"=>$ULang->t("Данная услуга уже подключена к вашему объявлению!")] );
                 exit;
              }

           }elseif( in_array(3, $getOrderServiceIds) ){
               echo json_encode( ["status"=>false, "answer"=>$ULang->t("Данная услуга уже подключена к вашему объявлению!")] );
               exit;
           }


          if( $getAd["clients_balance"] >= $price ){

            insert("INSERT INTO uni_services_order(services_order_id_ads,services_order_time_validity,services_order_id_service,services_order_count_day,services_order_status,services_order_time_create)VALUES('$id_ad','$services_order_time_validity','$id_s','$services_order_count_day','{$getAd["ads_status"]}','".date("Y-m-d H:i:s")."')");

            $Main->addOrder( ["id_ad"=>$id_ad,"price"=>$price,"title"=>$title,"id_user"=>$_SESSION["profile"]["id"],"status_pay"=>1, "link" => $Ads->alias($getAd), "user_name" => $getAd["clients_name"], "id_hash_user" => $getAd["clients_id_hash"], "action_name" => "services"] );


            $Profile->actionBalance(array("id_user"=>$_SESSION['profile']['id'],"summa"=>$price,"title"=>$title,"id_order"=>$config["key_rand"]),"-");

            echo json_encode( ["status"=>true] );

          }else{
            
            echo json_encode( ["status"=>false, "balance"=> $Main->price($getAd["clients_balance"]) ] );

          }



        }elseif( strtotime($getServiceOrder["services_order_time_validity"]) < time() ){
          
          if( $getAd["clients_balance"] >= $price ){

            update("UPDATE uni_services_order SET services_order_time_validity=?,services_order_count_day=?,services_order_status=? WHERE services_order_id=?", array($services_order_time_validity,$services_order_count_day,$getAd["ads_status"],$getServiceOrder["services_order_id"]));

            $Main->addOrder( ["id_ad"=>$id_ad,"price"=>$price,"title"=>$title,"id_user"=>$_SESSION["profile"]["id"],"status_pay"=>1, "link" => $Ads->alias($getAd), "user_name" => $getAd["clients_name"], "id_hash_user" => $getAd["clients_id_hash"], "action_name" => "services"] );

            $Profile->actionBalance(array("id_user"=>$_SESSION['profile']['id'],"summa"=>$price,"title"=>$title,"id_order"=>$config["key_rand"]),"-");

            echo json_encode( ["status"=>true] );

          }else{
            
            echo json_encode( ["status"=>false, "balance" => $Main->price($getAd["clients_balance"]) ] );

          }

        }else{

            echo json_encode( ["status"=>false, "answer"=>$ULang->t("Данная услуга уже подключена к вашему объявлению!")] );

        }


      }else{

        echo json_encode( ["status"=>false, "answer"=>implode("\n", $error)] );

      }

           
  }


  if($_POST["action"] == "show_phone"){

     $id_ad = intval($_POST["id_ad"]);

     if($settings["ad_view_phone"] == 1){
     
         if($_SESSION["profile"]["id"]){

           $findAd = findOne("uni_ads", "ads_id=?", array($id_ad));

           if($_SESSION["ad-phone"][$id_ad] && $findAd){

             $Profile->sendChat( array("id_ad" => $id_ad, "action" => 2, "user_from" => $_SESSION["profile"]["id"], "user_to" => $findAd["ads_id_user"] ) );

             $Main->addActionStatistics(['ad_id'=>$id_ad,'from_user_id'=>$_SESSION['profile']['id'],'to_user_id'=>$findAd["ads_id_user"]],"show_phone");

             echo json_encode( array( "auth" => 1, "html" => '<a href="tel:+'.$_SESSION["ad-phone"][$id_ad].'" >+'.$_SESSION["ad-phone"][$id_ad].'</a>' ) );

           }   

         }else{
            echo json_encode( array("auth" => 0) );
         }

     }else{

        echo json_encode( array( "auth" => 1, "html" => '<a href="tel:+'.$_SESSION["ad-phone"][$id_ad].'" >+'.$_SESSION["ad-phone"][$id_ad].'</a>' ) );

     }
    
  }

  if($_POST["action"] == "remove_publication"){

     $id_ad = intval($_POST["id_ad"]);

     update( "update uni_ads set ads_status=? where ads_id=? and ads_id_user=?", array(2,$id_ad, intval($_SESSION["profile"]["id"]) ), true );

  }

  if($_POST["action"] == "ads_status_sell"){

     $id_ad = intval($_POST["id_ad"]);

     update( "update uni_ads set ads_status=? where ads_id=? and ads_id_user=?", array(5,$id_ad, intval($_SESSION["profile"]["id"]) ), true );

     $Main->addActionStatistics(['ad_id'=>$id_ad,'from_user_id'=>0,'to_user_id'=>$_SESSION['profile']['id']],"ad_sell");

  }

  if($_POST["action"] == "ads_publication"){

     $id_ad = intval($_POST["id_ad"]);

     $getAd = $Ads->get("ads_id=?",[$id_ad]);

     if(strtotime($getAd["ads_period_publication"]) <= time()){

        $period = date("Y-m-d H:i:s", time() + ($settings["ads_time_publication_default"] * 86400) );

        update( "update uni_ads set ads_status=?, ads_period_publication=? where ads_id=? and ads_id_user=?", array(1,$period,$id_ad,intval($_SESSION["profile"]["id"]) ), true );

     }else{

        update( "update uni_ads set ads_status=? where ads_id=? and ads_id_user=?", array(1,$id_ad, intval($_SESSION["profile"]["id"]) ), true );

     }


     echo $Ads->alias($getAd) . "?modal=new_ad";
     
  }

  if($_POST["action"] == "ads_delete"){
     
     $id_ad = intval($_POST["id_ad"]);

     update( "update uni_ads set ads_status=? where ads_id=? and ads_id_user=?", array(8,$id_ad, intval($_SESSION["profile"]["id"]) ), true );

  }

  if($_POST["action"] == "ads_extend"){

     $id_ad = intval($_POST["id_ad"]);

     $period = date("Y-m-d H:i:s", time() + ($settings["ads_time_publication_default"] * 86400) );

     update( "update uni_ads set ads_period_publication=? where ads_id=? and ads_id_user=?", array($period,$id_ad,intval($_SESSION["profile"]["id"]) ), true );

  }

  if($_POST["action"] == "pay_category_publication"){
      
      $id_ad = (int)$_POST["id_ad"];

      $getAd = $Ads->get("ads_id=? and ads_id_user=?", [$id_ad,intval($_SESSION['profile']['id'])] );

      if($getAd){

        $getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

        $price = $getCategoryBoard["category_board_id"][$getAd["ads_id_cat"]]["category_board_price"];
        
        if( $getAd["clients_balance"] >= $price ){
          
          if($settings["ads_publication_moderat"]){
             update("update uni_ads set ads_status=? where ads_id=?", [0,$id_ad], true );
          }else{
             update("update uni_ads set ads_status=? where ads_id=?", [1,$id_ad], true );
          }

          $Main->addOrder( ["id_ad"=>$id_ad,"price"=>$price,"title"=>$static_msg["10"]." - ".$getAd["category_board_name"],"id_user"=>$_SESSION["profile"]["id"],"status_pay"=>1, "link" => $Ads->alias($getAd), "user_name" => $getAd["clients_name"], "id_hash_user" => $getAd["clients_id_hash"], "action_name" => "category"] );

          $Profile->actionBalance(array("id_user"=>$_SESSION['profile']['id'],"summa"=>$price,"title"=>$static_msg["10"]." - ".$getAd["category_board_name"],"id_order"=>$config["key_rand"]),"-");

          echo json_encode( ["status"=>true, "location" => $Ads->alias($getAd)] );

        }else{
          
          echo json_encode( ["status"=>false, "balance"=> $Main->price($getAd["clients_balance"]) ] );

        }

      }
       
  }

  if($_POST["action"] == "load_filters_ads"){

     $filters = $_POST["filter"];
     $id_c = (int)$_POST["id_c"];

     unset($_POST["_"]);
     unset($_POST["page"]);
     unset($_POST["action"]);
     unset($_POST["id_c"]);
     unset($_POST["search"]);

     $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");

     if(count($filters)){
        foreach ($filters as $id_filter => $nested) {

          if( is_array($nested) ){

              foreach ($nested as $key => $value) {
                if($value){
                    $count_change_fl += 1;
                    $id_filter_item = $value;
                } 
              }

          }else{

             $count_change_fl += 1;

          }

        }
     }

     $filters = http_build_query($_POST, 'flags_');

     $filters = $filters ? "?" . $filters : "";
     
     if($id_c){
           
           $params = $CategoryBoard->alias( $getCategories['category_board_id'][$id_c]['category_board_chain'] ) . $filters;
           
     }else{

           if($_SESSION["geo"]["alias"]){
              $params = _link($_SESSION["geo"]["alias"]) . $filters;
           }else{
              $params = _link($settings["country_default"]) . $filters; 
           }

     }

     if($count_change_fl == 1 && $id_c){

        $getAlias = findOne("uni_ads_filters_alias", "ads_filters_alias_id_filter_item=? and ads_filters_alias_id_cat=?", [ intval($id_filter_item),$id_c ]);

        if($getAlias){

            echo json_encode( [ "params" => $Filters->alias( ["category_alias"=>$getCategories['category_board_id'][$id_c]['category_board_chain'], "filter_alias"=>$getAlias["ads_filters_alias_alias"]] ) . $var, "count" => $count_change_fl ] );

        }else{
            echo json_encode( [ "params" => $params, "count" => $count_change_fl ] );
        }

     }else{

        echo json_encode( [ "params" => $params, "count" => $count_change_fl ] );

     }

     
  }

  if($_POST["action"] == "load_catalog_ads"){

    $page = (int)$_POST["page"] ? (int)$_POST["page"] : 1;
    $query = clearSearchBack($_POST["search"]);
    $output = $settings["catalog_out_content"];

    $param_search = $Elastic->paramAdSearch($query);
    $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];

    if( $query ){
      
      $geoQuery = $Ads->queryGeo();

      $geoQuery = $geoQuery ? ' and ' . $geoQuery : '';

      $results = $Ads->getAll( array( "query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() $geoQuery and " . $Filters->explodeSearch($query), "navigation"=>true, "output"=>$output, "page"=>$page, "param_search" => $param_search ) );
      
    }else{

      $results = $Filters->queryFilter($_POST, ["navigation"=>true, "output"=>$output, "page"=>$page]);

    }

    unset($_SESSION['current_load']['total']);

    if($results["count"]){

      $_SESSION['current_load']['total'] = $results["count"];

      if($page <= getCountPage($results["count"],$output)){

        if($_SESSION["catalog_ad_view"] == "grid" || !$_SESSION["catalog_ad_view"]){
          foreach ($results["all"] as $key => $value) {
             $ad_not_city_distance[] = $value["ads_city_id"];
             $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
             ob_start();
             include $config["template_path"] . "/include/catalog_ad_grid.php";
             $content .= ob_get_clean();
          }
        }elseif($_SESSION["catalog_ad_view"] == "list"){
          foreach ($results["all"] as $key => $value) {
             $ad_not_city_distance[] = $value["ads_city_id"];
             $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
             ob_start();
             include $config["template_path"] . "/include/catalog_ad_list.php";
             $content .= ob_get_clean();
          }
        }
        
      }

      $getCityDistance = $Ads->getCityDistance( $_POST, $ad_not_city_distance );

      if($page + 1 <= getCountPage($results["count"],$output)){

        $found = true;
        
        if( $settings["type_content_loading"] == 1 ){
            $content .= '
              
              <div class="col-lg-12" >
              <div class="action-catalog-load-ads text-center mt20" >
                  <button class="btn-custom btn-color-blue width250 button-inline" > <span class="action-load-span-start" > <span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> '.$ULang->t("Загрузка").'</span> <span class="action-load-span-end" >'.$ULang->t("Показать еще").' <i class="la la-angle-down"></i></span> </button>
              </div>
              </div>

            ';
        }else{
            $content .= '
              
              <div class="col-lg-12" >
              <div class="text-center mt20 preload-scroll" >

                  <div class="spinner-grow preload-spinner" role="status">
                    <span class="sr-only"></span>
                  </div>
                  
              </div>
              </div>

            ';           
        }

      }else{

         $content .= '
           <div class="col-lg-12" >
           <p class="text-center mt15" >'.$ULang->t("Измените условия поиска, чтобы увидеть больше объявлений").'</p>
           </div>
         ';

         if( $getCityDistance["count"] ){

             $content .= '
                 <div class="col-lg-12 text-center" >
                 <h4 class="mt40 mb40" ><strong>'.$ULang->t("Объявления в ближайших городах").'</strong></h4>
                 </div>
             ';

             foreach ($getCityDistance["all"] as $key => $value) {
                 $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
                 ob_start();
                 include $config["template_path"] . "/include/catalog_ad_grid.php";
                 $content .= ob_get_clean();

             }

         }

      }


    }else{

       $getCityDistance = $Ads->getCityDistance( $_POST );

       $found = false;

       $content = '
           <div class="col-lg-12" >
           <div class="catalog-no-results" >
              <div class="catalog-no-results-box" >
                  <img src="'.$settings["path_tpl_image"].'/person-shrugging_1f937.png" />
                  <h5>'.$ULang->t("Ничего не найдено").'</h5>
                  <p>'.$ULang->t("Увы, мы не нашли то, что вы искали. Смягчите условия поиска и попробуйте еще раз.").'</p>
              </div>
           </div>           
           </div>
       ';

       if( $getCityDistance["count"] ){

           $content .= '
               <div class="col-lg-12 text-center" >
               <h4 class="mt40 mb40" ><strong>'.$ULang->t("Объявления в ближайших городах").'</strong></h4>
               </div>
           ';

           foreach ($getCityDistance["all"] as $key => $value) {
               $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
               ob_start();
               include $config["template_path"] . "/include/catalog_ad_grid.php";
               $content .= ob_get_clean();

           }

       }


    }


    echo json_encode(array("content"=>$content, "found"=>$found, "count" => $results["count"]));
  
  }

  if($_POST["action"] == "load_index_ads"){
    
    $page = (int)$_POST["page"] ? (int)$_POST["page"] : 1;

    $param_search = $Elastic->paramAdquery();

    if( $settings["ads_sorting_variant"] == 0 ){
      $sorting = "order by ads_sorting desc, ads_id desc";
      $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
      $param_search["sort"]["ads_id"] = [ "order" => "desc" ];
    }elseif( $settings["ads_sorting_variant"] == 1 ){ 
      $sorting = "order by ads_sorting desc, ads_id asc";
      $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
      $param_search["sort"]["ads_id"] = [ "order" => "asc" ];      
    }else{
      $sorting = "order by ads_sorting desc";
      $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
    }

    if($settings["index_out_content_method"] == 0){

        $results = $Ads->getAll( ["sort"=>$sorting, "query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now()", "navigation" => true, "page" => $page, "output" => $settings["index_out_content"], "param_search" => $param_search ] );

    }else{

        $geo = $Ads->queryGeo() ? " and " . $Ads->queryGeo() : "";

        $results = $Ads->getAll( ["sort"=>$sorting, "query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() $geo", "navigation" => true, "page" => $page, "output" => $settings["index_out_content"], "param_search" => $param_search ] );

    }

    if($results["count"]){

      if($page <= getCountPage($results["count"],$settings["index_out_content"])){

          foreach ($results["all"] as $key => $value) {
             $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
             ob_start();
             include $config["template_path"] . "/include/catalog_ad_grid.php";
             $content .= ob_get_clean();
          }

      }

      if($page + 1 <= getCountPage($results["count"],$settings["index_out_content"])){
        
        $found = true;

        if( $settings["type_content_loading"] == 1 ){
            $content .= '
              
              <div class="col-lg-12" >
              <div class="ajax-load-button action-index-load-ads text-center mt20" >
                  <button class="btn-custom btn-color-blue width250 button-inline" > <span class="action-load-span-start" > <span class="spinner-border spinner-border-sm button-ajax-loader" role="status" aria-hidden="true"></span> '.$ULang->t("Загрузка").'</span> <span class="action-load-span-end" >'.$ULang->t("Показать еще").' <i class="la la-angle-down"></i></span> </button>
              </div>
              </div>

            ';
        }else{
            $content .= '
              
              <div class="col-lg-12" >
              <div class="text-center mt20 preload-scroll" >

                  <div class="spinner-grow preload-spinner" role="status">
                    <span class="sr-only"></span>
                  </div>
                  
              </div>
              </div>

            ';         
        }


      }else{

         $found = false;

      }


    }else{

       $found = false;

       $content = '
           <div class="col-lg-12" >
           <div class="catalog-no-results" >
              <div class="catalog-no-results-box" >
                  <img src="'.$settings["path_tpl_image"].'/person-shrugging_1f937.png" />
                  <h5>'.$ULang->t("Объявлений нет").'</h5>
              </div>
           </div>           
           </div>
       ';

    }


    echo json_encode(array("content"=>$content, "found"=>$found));
  
  }

  if($_POST["action"] == "catalog_view"){
     
     if( $_POST["view"] == "grid" ){
        $_POST["view"] = "grid";
     }elseif( $_POST["view"] == "list" ){
        $_POST["view"] = "list";
     }else{
        $_POST["view"] = "grid";
     }

     $_SESSION["catalog_ad_view"] = $_POST["view"];

  }

  if($_POST["action"] == "ad_similar"){
      
    $id_cat = (int)$_POST["id_cat"];
    $id_ad = (int)$_POST["id_ad"];
    
    if($id_ad && $id_cat && $settings["ad_similar_count"]){

      $getAd = findOne("uni_ads", "ads_id=?", [$id_ad] );

      if(!$getAd) exit;

      $getTariff = $Profile->getOrderTariff($getAd["ads_id_user"]);

      $ids_cat = idsBuildJoin( $CategoryBoard->idsBuild($id_cat, $CategoryBoard->getCategories()), $id_cat );

      $param_search = $Elastic->paramAdquery();

      if($getTariff['services']['hiding_competitors_ads']){
          $param_search["query"]["bool"]["filter"][]["terms"]["ads_id_user"] = $getAd["ads_id_user"];
          $ads_id_user = "and ads_id_user='{$getAd["ads_id_user"]}'";
      }

      $param_search["query"]["bool"]["filter"][]["terms"]["ads_id_cat"] = explode(",", $ids_cat);
      $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];

      $data["similar"] = $Ads->getAll( [ "query" => "ads_id_cat IN(".$ids_cat.") and clients_status IN(0,1) and ads_status='1' and ads_period_publication > now() and ads_id!=".$id_ad." {$ads_id_user} order by ads_sorting desc limit " . $settings["ad_similar_count"], "param_search" => $param_search, "output" => $settings["ad_similar_count"] ] );

       if($data["similar"]["all"]){

          foreach ($data["similar"]["all"] as $key => $value) {
             $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
             ob_start();
             include $config["template_path"] . "/include/ad_grid.php";
             $content .= ob_get_clean();
          }

          echo json_encode(array("content"=>$content));

       }

     }


  }

  if($_POST["action"] == "auction_rate"){

      if(!$_SESSION["profile"]["id"]){ exit(json_encode(array("status"=>false,"auth" => false))); }

      $error = [];

      $id = (int)$_POST["id"];
      $rate = round($_POST["rate"]);

      if(!$id){  $error[] = $ULang->t("Объявление не определено"); }else{
          $getAd = $Ads->get("ads_id=? and ads_auction=?", [$id,1]);
          if($getAd){
            if( strtotime($getAd["ads_auction_duration"]) <= time() ){
               $error[] = $ULang->t("Ставка не принята. Аукцион завершен!");
            }else{
               if( $rate <= $getAd["ads_price"]){
                  $error[] = $ULang->t("Минимальная ставка на данный момент: ") . $Main->price($getAd["ads_price"]) . $ULang->t(". Пожалуйста, повысьте свою ставку!");
               }else{
                  if( $getAd["ads_auction_price_sell"] && $rate > $getAd["ads_auction_price_sell"] ){
                     $error[] = $ULang->t("Вы не можете сделать ставку, превышающую цену \"Купить сейчас\". По цене \"Купить сейчас\" Вы можете купить лот без торга.");
                  }
               }
            }
          }else{
             $error[] = $ULang->t("Для данного товара аукцион не действует!");
          }
      }

      if( count($error) == 0 ){

            insert("INSERT INTO uni_ads_auction(ads_auction_id_ad,ads_auction_price,ads_auction_id_user,ads_auction_date)VALUES(?,?,?,?)", [$id, $rate, $_SESSION["profile"]["id"],date("Y-m-d H:i:s")]);

            update("update uni_ads set ads_price=? where ads_id=?", [$rate , $id ], true);

            $getRate = getOne("select * from uni_ads_auction INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads_auction`.ads_auction_id_user where ads_auction_id_ad=? and ads_auction_id_user!=? order by ads_auction_price desc", [$id, intval($_SESSION["profile"]["id"])]);
             
            if($getRate){

               $data = array("{ADS_TITLE}"=>$getAd["ads_title"],
                             "{ADS_LINK}"=>$Ads->alias($getAd),
                             "{USER_NAME}"=>$getRate["clients_name"],                          
                             "{UNSUBSCRIBE}"=>"",                          
                             "{EMAIL_TO}"=>$getRate["clients_email"]
                             );

               email_notification( array( "variable" => $data, "code" => "AUCTION_INTERRUPT" ) );

               $Profile->sendChat( array("id_ad" => $id, "action" => 6, "user_from" => $getAd["ads_id_user"] , "user_to" => $getRate["clients_id"] ) );

            }

            echo json_encode( [ "status"=>true,"auth" => true ] );

      }else{
         echo json_encode( [ "status"=>false, "answer"=> implode("<br>", $error),"auth" => true ] );
      }

  }

  if($_POST["action"] == "auction_cancel_rate"){

        $id = (int)$_POST["id"];

        $getAd = $Ads->get("ads_id=? and ads_auction=?", [$id,1]);
        
        if($getAd){

            $getRate = getOne("select * from uni_ads_auction where ads_auction_id_ad=? order by ads_auction_price desc", [$id]);

            update("delete from uni_ads_auction where ads_auction_id_user=? and ads_auction_id_ad=?", [ $getRate["ads_auction_id_user"], $id ]);
            
            $user_winner = getOne("select * from uni_ads_auction INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads_auction`.ads_auction_id_user where ads_auction_id_ad=? order by ads_auction_price desc", [$id]);

            if($user_winner){

                update("update uni_ads set ads_price=? where ads_id=?", [ $user_winner["ads_auction_price"] , $id ], true);

                $data = array("{ADS_LINK}"=>$Ads->alias($getAd),
                              "{ADS_TITLE}"=>$getAd["ads_title"],
                              "{USER_NAME}"=>$user_winner["clients_name"],
                              "{UNSUBSCRIBE}"=>"",
                              "{EMAIL_TO}"=>$user_winner["clients_email"]
                              );

                email_notification( array( "variable" => $data, "code" => "AUCTION_USER_WINNER" ) );   

                $Profile->sendChat( array("id_ad" => $id, "action" => 5, "user_from" => $getAd["ads_id_user"] , "user_to" => $user_winner["clients_id"] ) );      
            
            }else{

               update("update uni_ads set ads_status=? where ads_id=?", [2, $id ], true);

            }

        }
 
  }

  if($_POST["action"] == "buy_payment_goods"){

     if(!$_SESSION["profile"]["id"]){ exit(json_encode(["status" => false])); }

     $id = (int)$_POST["id"];

     $findAd = $Ads->get("ads_id=? and ads_status IN(1,4)", [$id]);

     $getOrder = findOne("uni_secure", "secure_id_ad=? and secure_status!=?", [$findAd["ads_id"],5]);
     
     if($getOrder){

       if( $getOrder["secure_status"] == 0 ){
          if( $getOrder["secure_id_user_buyer"] != $_SESSION["profile"]["id"] ){
              echo json_encode( ["status" => false] ); exit;
          }
       }else{
          echo json_encode( ["status" => false] ); exit; 
       }

       $orderId = $getOrder["secure_id_order"];

     }else{

       $orderId = $config["key_rand"];

     }

     
     if( $findAd && $Ads->getStatusSecure($findAd, true) ){

        if( $findAd["ads_auction"] ){

          if( $findAd["ads_status"] == 1 ){

                if( $findAd["ads_auction_price_sell"] ){
                  $price = $findAd["ads_auction_price_sell"];
                }else{
                  echo json_encode( ["status" => false] ); exit;
                }

          }elseif( $findAd["ads_status"] == 4 ){

                $auction_user_winner = $Ads->getAuctionWinner( $findAd["ads_id"] );

                if( $_SESSION["profile"]["id"] == $auction_user_winner["ads_auction_id_user"] ){
                  $price = $findAd["ads_price"];
                }else{
                  echo json_encode( ["status" => false] ); exit;
                }

          }

        }else{

           $price = $findAd["ads_price"];

        }
        

        if(!$getOrder){
          insert("INSERT INTO uni_secure(secure_id_ad,secure_date,secure_id_user_buyer,secure_id_user_seller,secure_id_order,secure_price)VALUES(?,?,?,?,?,?)", [ $findAd["ads_id"], date("Y-m-d H:i:s"), $_SESSION['profile']['id'], $findAd["ads_id_user"], $orderId, $price ]);

          update("update uni_ads set ads_status=? where ads_id=?", [ 4 , $findAd["ads_id"] ], true);

          $Profile->sendChat( array("id_ad" => $findAd["ads_id"], "action" => 3, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $findAd["ads_id_user"] ) );

          insert("INSERT INTO uni_clients_orders(clients_orders_from_user_id,clients_orders_uniq_id,clients_orders_date,clients_orders_to_user_id,clients_orders_secure)VALUES(?,?,?,?,?)", [intval($_SESSION["profile"]["id"]), $orderId, date('Y-m-d H:i:s'),$findAd["ads_id_user"],1]);
        }

        $html = $Profile->payMethod( $settings["secure_payment_service_name"] , array( "amount" => $price, "id_order" => $orderId, "id_user" => $_SESSION['profile']['id'], "id_user_ad" => $findAd["ads_id_user"], "action" => "secure", "title" => $static_msg["11"]." №".$orderId, "auction" => $findAd["ads_auction"], "id_ad" => $id, "ad_price" => $price, "link_success" => _link("order/".$orderId) ) );

        echo json_encode( array( "status" => true, "redirect" => $html ) );

     }else{

        echo json_encode( ["status" => false] );

     }

  }

  if($_POST["action"] == "confirm_transfer_goods"){
      
      $id = (int)$_POST["id"];

      $getOrder = findOne("uni_secure", "secure_id=?", [ $id ]);

      if($getOrder["secure_status"] == 1){

        update("update uni_secure set secure_status=? where secure_id=? and secure_id_user_seller=?", [ 2 , $id, $_SESSION['profile']['id'] ]);

        echo true;

      }else{

        echo false;

      }

  }

  if($_POST["action"] == "confirm_receive_goods"){
      
      $id = (int)$_POST["id"];

      $getOrder = findOne("uni_secure", "secure_id=? and secure_id_user_buyer=?", [ $id, $_SESSION['profile']['id'] ]);
      
      if($getOrder){

        update("update uni_secure set secure_status=? where secure_id=?", [ 3 , $id ]);
        update("update uni_ads set ads_status=? where ads_id=?", [ 5 , $getOrder["secure_id_ad"] ], true);

        $payments = findOne("uni_secure_payments", "secure_payments_id_order=? and secure_payments_id_user=?", [$getOrder["secure_id_order"],$getOrder["secure_id_user_seller"]]);

        $user = findOne("uni_clients", "clients_id=?", [$getOrder["secure_id_user_seller"]]);

        if( !$payments && $user ){

          $Ads->addSecurePayments( ["id_order"=>$getOrder["secure_id_order"], "amount"=>$getOrder["secure_price"], "score"=>$user["clients_score"], "id_user"=>$getOrder["secure_id_user_seller"], "status_pay"=>0, "status"=>1, "amount_percent" => $Ads->secureTotalAmountPercent($getOrder["secure_price"])] );

        }

      }

      echo true;

  }

  if($_POST["action"] == "order_cancel_deal"){
      
      $id = (int)$_POST["id"];

      $getOrder = findOne("uni_secure", "secure_id=? and secure_id_user_buyer=? and secure_status=?", [ $id, $_SESSION['profile']['id'],1 ]);
      
      if($getOrder){

        update("update uni_secure set secure_status=? where secure_id=?", [ 5 , $id ]);
        update("update uni_ads set ads_status=? where ads_id=?", [ 1 , $getOrder["secure_id_ad"] ], true);

        $payments = findOne("uni_secure_payments", "secure_payments_id_order=? and secure_payments_id_user=?", [$getOrder["secure_id_order"],$getOrder["secure_id_user_buyer"]]);

        $user = findOne("uni_clients", "clients_id=?", [$getOrder["secure_id_user_buyer"]]);

        if( !$payments && $user ){

          $Ads->addSecurePayments( ["id_order"=>$getOrder["secure_id_order"], "amount"=>$getOrder["secure_price"], "score"=>$user["clients_score"], "id_user"=>$getOrder["secure_id_user_buyer"], "status_pay"=>0, "status"=>2, "amount_percent" => $Ads->secureTotalAmountPercent($getOrder["secure_price"], false)] );

        }

      }

      echo true;

  }

  if($_POST["action"] == "order_cancel_deal_marketplace"){
      
      $id = (int)$_POST["id"];

      $getOrder = findOne("uni_clients_orders", "clients_orders_id=? and (clients_orders_from_user_id=? or clients_orders_to_user_id=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);
      
      if($getOrder){
        $Cart->returnAvailable($id);
        update('update uni_clients_orders set clients_orders_status=? where clients_orders_id=?', [2,$id]);
      }
      

      echo true;

  }

  if($_POST["action"] == "order_delete_marketplace"){
      
      $id = (int)$_POST["id"];

      $getOrder = findOne("uni_clients_orders", "clients_orders_id=? and (clients_orders_from_user_id=? or clients_orders_to_user_id=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);

      if($getOrder){
          if($getOrder['clients_orders_status'] != 2){
            $Cart->returnAvailable($id);
          }
          update('delete from uni_clients_orders_ads where clients_orders_ads_order_id=?', [$getOrder['clients_orders_uniq_id']]);
          update('delete from uni_clients_orders where clients_orders_id=?', [$id]);
      }

      echo json_encode(['link'=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/orders" )]);

  }

  if($_POST["action"] == "order_change_status"){
      
      $id = (int)$_POST["id"];
      $status = (int)$_POST["status"];

      $getOrder = findOne("uni_clients_orders", "clients_orders_id=? and (clients_orders_from_user_id=? or clients_orders_to_user_id=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);
      
      if($getOrder){
        if($status == 2){
            $Cart->returnAvailable($id);
        }
        update('update uni_clients_orders set clients_orders_status=? where clients_orders_id=?', [$status,$id]);
      }
      

      echo true;

  }

  if($_POST["action"] == "add_disputes"){

      if(!intval($_POST["id"])){ exit; }
      
      $text = clear( $_POST["text"] );

      $attach = [];

      if( $text ){

         $getSecure = findOne("uni_secure", "secure_id=? and secure_status=?", [intval($_POST["id"]),2]);
         
         if($getSecure){

         $files = normalize_files_array( $_FILES );
         if($files["files"]){
            foreach ( array_slice($files["files"], 0, 5) as $key => $value) {

                $path = $config["basePath"] . "/" . $config["media"]["attach"];
                $max_file_size = 2;
                $extensions = array('jpeg', 'jpg', 'png');
                $ext = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));
                
                if($value["size"] <= $max_file_size*1024*1024){

                  if (in_array($ext, $extensions))
                  {
                    
                        $uid = md5($_SESSION['profile']['id'] . uniqid());

                        $name = $uid . "." . $ext;
                        
                        if( move_uploaded_file($value["tmp_name"], $path . "/" . $name) ){
                            $attach[] = $name;
                        }
                        
                  }

                }
                 
            }
         }

         insert("INSERT INTO uni_secure_disputes(secure_disputes_id_secure,secure_disputes_text,secure_disputes_date,secure_disputes_id_user,secure_disputes_attach)VALUES(?,?,?,?,?)", [intval($_POST["id"]), $text, date("Y-m-d H:i:s"), $_SESSION['profile']['id'], json_encode($attach)]);

         update("update uni_secure set secure_status=? where secure_id=? and secure_id_user_buyer=?", [ 4 , intval($_POST["id"]), $_SESSION['profile']['id'] ]);

         echo json_encode( ["status"=>true] );

         }

      }else{
         echo json_encode( ["status"=>false, "answer"=>$ULang->t("Пожалуйста, опишите причину спора")] );
      }

  }

  if($_POST["action"] == "feedback"){

     $error = [];

     if(!$_POST["subject"]) $error[] = $ULang->t("Пожалуйста, укажите тему обращения");
     if(!$_POST["text"]) $error[] = $ULang->t("Пожалуйста, укажите текст обращения");
     if(!$_POST["email"]) $error[] = $ULang->t("Пожалуйста, укажите ваш e-mail");

     if(!$_POST["code"] || $_POST["code"] != $_SESSION['captcha']['feedback']) $error[] = $ULang->t("Пожалуйста, укажите корректный код проверки");

     if( count($error) == 0 ){

        $text = '
        <p style="margin-bottom: 0px;" >'.$static_msg["12"].': '.$_POST["subject"].'</p>
        <p style="margin-bottom: 0px;" >'.$static_msg["13"].': '.$_POST["name"].'</p>
        <p>'.$static_msg["14"].': '.$_POST["email"].'</p>
        <hr>
        <p><strong>'.$static_msg["15"].'</strong></p>
        <p>'.$_POST["text"].'</p>
        ';

        mailer($settings["email_alert"],$static_msg["16"]." - " . $settings["site_name"],$text);
        
        echo json_encode( ["status"=>true] );

        unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

     }else{
        echo json_encode( ["status"=>false, "answer"=> implode("\n", $error) ] );
     }

  }

  if($_POST["action"] == "user_subscribe"){

     $error = [];

     if(validateEmail( $_POST["email"] ) == false){
          $error[] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
     }

     if( count($error) == 0 ){

        $hash = hash('sha256', $_POST["email"].$config["private_hash"]);
        $subscribe = $config["urlPath"].'/subscribe?hash='.$hash.'&email='.$_POST["email"];

        $data = array("{ACTIVATION_LINK}"=>$subscribe,
                      "{UNSUBSCRIBE}"=>"",
                      "{EMAIL_TO}"=>$_POST["email"]
                      );

        email_notification( array( "variable" => $data, "code" => "SUBSCRIBE_ACTIVATION_EMAIL" ) );
        
        echo json_encode( ["status"=>true] );
     }else{
        echo json_encode( ["status"=>false, "answer"=> implode("\n", $error) ] );
     }

  }

  if($_POST["action"] == "add_comment"){

      if(!$_SESSION['profile']['id']){ exit(json_encode(["status"=>false])); }

      $id_ad = (int)$_POST["id_ad"];
      $id_msg = (int)$_POST["id_msg"];
      $text = clear($_POST["text"]);

      if($id_msg){
         if( $_POST["token"] != md5($config["private_hash"].$id_msg.$id_ad) ){
             exit(json_encode(["status"=>false]));
         }
      }

      $getAd = findOne( "uni_ads", "ads_id=?", [$id_ad]);
      
      if(!$getAd){
          exit(json_encode(["status"=>false]));
      }

      $getUser = findOne( "uni_clients", "clients_id=?", [$getAd["ads_id_user"]]);

      if(!$settings["ads_comments"] || !$getUser["clients_comments"]){
          exit(json_encode(["status"=>false]));
      }

      $locked = $Profile->getUserLocked( $getAd["ads_id_user"], $_SESSION["profile"]["id"] );

      if( $locked ){
         exit(json_encode(["status"=>false]));
      }

      if($text){

         insert("INSERT INTO uni_ads_comments(ads_comments_id_user,ads_comments_text,ads_comments_date,ads_comments_id_parent,ads_comments_id_ad)VALUES(?,?,?,?,?)", [$_SESSION['profile']['id'],$text,date("Y-m-d H:i:s"),$id_msg,$id_ad]);

         echo json_encode( ["status"=>true] );

      }else{
         echo json_encode( ["status"=>false] );
      }

  }

  if($_POST["action"] == "delete_comment"){

     $id = intval($_POST["id"]);

     if( $_SESSION['cp_auth'][ $config["private_hash"] ] ){

         $getMsg = findOne("uni_ads_comments", "ads_comments_id=?", [$id]);

         $nested_ids = idsBuildJoin($Ads->idsComments($id,$Ads->getComments($getMsg["ads_comments_id_ad"])),$id);
        
         if($nested_ids){
            foreach (explode(",", $nested_ids) as $key => $value) {
              
               update( "delete from uni_ads_comments where ads_comments_id=?", array( $value ) );

            }
         }

     }else{
    
         $getMsg = findOne("uni_ads_comments", "ads_comments_id=? and ads_comments_id_user=?", [$id,intval($_SESSION["profile"]["id"])]);
         
         $nested_ids = idsBuildJoin($Ads->idsComments($id,$Ads->getComments($getMsg["ads_comments_id_ad"])),$id);

         if($nested_ids && $getMsg){
            foreach (explode(",", $nested_ids) as $key => $value) {
              
               update( "delete from uni_ads_comments where ads_comments_id=?", array( $value ) );

            }
         }

     }

     echo json_encode( ["status"=>true] );

  }

  if($_POST["action"] == "ads_search"){

       $query = clearSearchBack($_POST["search"]);
       $id_s = (int)$_POST["id_s"];
       $page = clear($_POST['page']);
       $results = [];
       $temp = [];
       $main_id_categories = [];
       $getShop = [];
       $delete_words = ['с','в','на','или'];

       if(!$query || mb_strlen($query, 'UTF-8') <= 1) exit;

       if($id_s){
           $getShop = $Shop->getShop(['shop_id'=>$id_s,'conditions'=>true]);
           $getTariff = $Profile->getOrderTariff($getShop["clients_shops_id_user"]);
           if(!$getTariff['services']['search_shop']){
               $getShop = [];
           }
       }

       $query = str_replace('-', ' ', $query);
       $queryNotDeleteWord = str_replace('-', ' ', $query);

       foreach ($delete_words as $value) {
           $query = preg_replace('/\b'.$value.'\b/u','',$query);
       }

       $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");

       $split = preg_split("/( )+/", $query);
       $splitNotDeleteWord = preg_split("/( )+/", $queryNotDeleteWord);

       if($page != 'shops'){

            if(count($splitNotDeleteWord) > 1 && $page != 'shop'){
                $endWord = $splitNotDeleteWord[ count($splitNotDeleteWord) - 1 ];
                $penultimateWord = $splitNotDeleteWord[ count($splitNotDeleteWord) - 2 ];
                if(mb_strlen($endWord, 'UTF-8') >= 3) $searchCity = getOne("select * from uni_city where city_name LIKE '".$endWord."' or city_declination LIKE '".$penultimateWord.' '.$endWord."'", []);
            }

           if($getShop["clients_shops_id_theme_category"]){
                $shop_get_category_ids = idsBuildJoin($CategoryBoard->idsBuild($getShop["clients_shops_id_theme_category"], $getCategories), $getShop["clients_shops_id_theme_category"]);
                if($shop_get_category_ids){
                    $search = getAll("select * from uni_ads_keywords where ads_keywords_id_cat IN(".$shop_get_category_ids.") and (ads_keywords_tag LIKE '%".$split[0]."%' or ads_keywords_tag LIKE '%".searchSubstr($split[0],1)."%') order by ads_keywords_count_click desc limit 100");
                }
           }else{
                $search = getAll("select * from uni_ads_keywords where ads_keywords_tag LIKE '%".$split[0]."%' or ads_keywords_tag LIKE '%".searchSubstr($split[0],1)."%' order by ads_keywords_count_click desc limit 100");
           }

           if(count($search)){
              if(count($split) > 1){
                  foreach ($search as $value) {

                      if(count($split) == 2){
                          if(searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[1],1))){
                               $results[] = $value;
                          }
                      }elseif(count($split) == 3){
                          if(searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[1],1)) && searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[2],1))){
                               $results[] = $value;
                          }else{
                             if(searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[1],1))){
                                $results[] = $value;
                             }
                          }
                      }elseif(count($split) == 4){
                          if(searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[1],1)) && searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[2],1)) && searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[3],1))){
                               $results[] = $value;
                          }else{
                             if(searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[1],1)) && searchCheckWord($value['ads_keywords_tag'],searchSubstr($split[2],1))){
                                 $results[] = $value;
                             }                        
                          }
                      }

                  }

              }else{
                  $results = $search;
              }
           }

           if(count($results)){

              foreach ($results as $value) {
                $get_main_id = $CategoryBoard->reverseMainId($getCategories,$value['ads_keywords_id_cat']);
                if($get_main_id) $main_id_categories[$get_main_id] = $get_main_id;
              }

           }

           if(count($results)){

                 foreach (array_slice($results,0,10,true) as $value) {

                    $params = [];

                    if($value['ads_keywords_params']){
                        $params[] = $value['ads_keywords_params'];
                    }

                    $params[] = 's_id='.$value['ads_keywords_id'];

                    if($getShop){
                        $link = $Shop->linkShop($getShop['clients_shops_id_hash']).'/'.$getCategories["category_board_id"][$value["ads_keywords_id_cat"]]["category_board_chain"].'?'.implode('&',$params);
                    }else{
                        $link = $CategoryBoard->alias($getCategories["category_board_id"][$value["ads_keywords_id_cat"]]["category_board_chain"], $searchCity['city_alias']).'?'.implode('&',$params);
                    }
                    
                    ?> 
                      <a href="<?php echo $link; ?>" > 
                          <span class="main-search-results-name" ><?php echo $value["ads_keywords_tag"]; ?> <span class="main-search-results-city" ><?php if($page != 'shop'){ echo $Geo->outGeoDeclination($searchCity['city_declination']); } ?></span> </span>
                          <?php if(!$value['ads_keywords_params']){ ?>
                          <span class="main-search-results-category" ><?php echo $getCategories["category_board_id"][$value["ads_keywords_id_cat"]]["category_board_name"]; ?></span>
                          <?php } ?>

                      </a>              
                    <?php
                 }

           }

       }

       if($settings["user_shop_status"]){

       if($page == 'shop'){

           if($getShop){
               $results = $Ads->getAll( array("navigation"=>false,"output"=>10,"query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_id_user='".$getShop["clients_shops_id_user"]."' and ".$Filters->explodeSearch($query), "sort"=>"ORDER By ads_datetime_add DESC limit 10", "param_search" => $Elastic->paramAdSearch($query,$getShop["clients_shops_id_user"])));

               if($results["count"]){

                     foreach ($results["all"] as $key => $value) {
                        $image = $Ads->getImages($value["ads_images"]);
                        $service = $Ads->adServices($value["ads_id"]);
                        ?>
                          <a href="<?php echo $Ads->alias($value); ?>" > 
                            <div class="main-search-results-img" ><img src="<?php echo Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]); ?>"></div>
                            <div class="main-search-results-cont" >

                              <span class="main-search-results-name" ><?php echo $value["ads_title"]; echo $service[2] || $service[3] ? '<span class="main-search-results-item-vip" >Vip</span>' : ""; ?></span>
                              <span class="main-search-results-category" ><?php echo $value["category_board_name"]; ?></span>

                            </div>
                            <div class="clr" ></div>
                          </a>              
                        <?php
                     }

               }
           }

       }else{

           foreach ($split as $value) {
               $shop_like_query[] = "clients_shops_title LIKE '%".$value."%'";
           }

           if(count($main_id_categories)){
                $getShops = getAll("select * from uni_clients_shops where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and (clients_shops_id_theme_category IN(".implode(',', $main_id_categories).") or (".implode(' and ', $shop_like_query).")) order by rand() limit 5", []);
           }else{
                $getShops = getAll("select * from uni_clients_shops where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and ".implode(' and ', $shop_like_query)." order by rand() limit 10", []);
           }

           if(count($getShops)){
                ?>
                <div class="search-store-offers" >
                <?php
                    foreach ($getShops as $key => $value) {
                       $count_ads = $Ads->getCount("ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_id_user='{$value["clients_shops_id_user"]}'");
                       ?>
                          <a href="<?php echo $Shop->linkShop($value["clients_shops_id_hash"]); ?>" > 
                            <div class="main-search-results-img" ><img src="<?php echo Exists($config["media"]["other"], $value["clients_shops_logo"], $config["media"]["no_image"]); ?>"></div>
                            <div class="main-search-results-cont" >

                              <span class="main-search-results-name" ><?php echo custom_substr($value["clients_shops_title"], 35, "..."); ?></span>
                              <span class="main-search-results-category" ><?php if($value["clients_shops_id_theme_category"]){ echo $getCategories["category_board_id"][$value["clients_shops_id_theme_category"]]["category_board_name"].' &bull; '; } ?> <?php echo $count_ads; ?> <?php echo ending($count_ads, $ULang->t("объявление"), $ULang->t("объявления"), $ULang->t("объявлений") ) ?></span>

                            </div>
                            <div class="clr" ></div>
                          </a>
                       <?php
                    }
                ?>
                </div>
                <?php
           }

       }

       }

  }

  if($_POST["action"] == "load_items_filter"){

      $id_filter = (int)$_POST["id_filter"];
      $id_item = (int)$_POST["id_item"];


      if($_POST["view"] == "catalog"){
         echo $Filters->load_podfilters_catalog($id_filter,$id_item);
      }elseif($_POST["view"] == "modal"){
         echo $Filters->load_podfilters_catalog($id_filter,$id_item,[],"podfilters_modal");
      }elseif($_POST["view"] == "ad"){
         echo $Filters->load_podfilters_ad($id_filter,$id_item);
      }
      
  }

  if($_POST["action"] == "load_offers_map"){
      
      if($_POST["ids"]){

        $ids = iteratingArray( explode(",", $_POST["ids"]) , "int");

        $param_search = $Elastic->paramAdquery();

        $param_search["query"]["bool"]["filter"][]["terms"]["ads_id"] = $ids;

        if( $settings["ads_sorting_variant"] == 0 ){
          $sorting = "order by ads_sorting desc, ads_id desc";
          $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
          $param_search["sort"]["ads_id"] = [ "order" => "desc" ];
        }elseif( $settings["ads_sorting_variant"] == 1 ){ 
          $sorting = "order by ads_sorting desc, ads_id asc";
          $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
          $param_search["sort"]["ads_id"] = [ "order" => "asc" ];      
        }else{
          $sorting = "order by ads_sorting desc";
          $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];
        }

        $result = $Ads->getAll( array("query"=>"ads_id IN(".implode(",",$ids).")", "sort"=>$sorting, "navigation"=>true, "page"=>intval($_POST["page"]), "param_search" => $param_search) );
        
        foreach ($result["all"] as $key => $value) {

           $_SESSION['count_display_ads'][$value['ads_id']] = $value['ads_id_user'];
           
           ob_start();
           include $config["template_path"] . "/include/map_ad_grid.php";
           $offers .= ob_get_clean();

        }

        $info = '<i data-tippy-placement="bottom" title="'.$ULang->t("Объявления без адреса не отображаются на карте. Перейдите в список, чтобы увидеть все объявления.").'" class="las la-question-circle map-search-offers-header-count-info"></i>';

        $navigation = '
            <div>
              <ul class="pagination pagination-map-offers justify-content-center mt15">  
                 '.out_navigation( array("count"=>$result["count"], "output" => $settings["catalog_out_content"], "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => intval($_POST["page"]), "page_variable" => "page") ).'
              </ul>
            </div>
        ';

        echo json_encode( [ "offers" => '<div class="row no-gutters">' . $offers . '</div>' . $navigation, "count" => $result["count"], "status" => true, "countHtml" => $result["count"] . " " . ending($result["count"],$ULang->t("объявление"),$ULang->t("объявления"),$ULang->t("объявлений") ) . $info ] );

      }else{

        $offers = '
          <div class="map-no-result" >
          <i class="las la-search-location"></i>
          <h6><strong>'.$ULang->t("К сожалению, нет объявлений в этой области карты").'</strong></h6>
          <p>'.$ULang->t("Попробуйте сменить масштаб или область карты.").'</p>
          </div>
        ';
        
        echo json_encode( [ "offers" => $offers, "count" => 0, "status" => false ] );

      }
 
  }

  if($_POST["action"] == "load_offer_map"){

      $id = (int)$_POST["id"];

      if(!$id) exit;

      $getAd = $Ads->get('ads_id=?', [$id]);

      if($getAd){

        $images = $Ads->getImages($getAd["ads_images"]);
        $service = $Ads->adServices($getAd["ads_id"]);
        $getShop = $Shop->getUserShop($getAd["ads_id_user"]);

        ?>
          <div class="item-grid <?php echo $service[2] || $service[3] ? "ads-highlight" : ""; ?>" title="<?php echo $getAd["ads_title"]; ?>" >
             <div class="item-grid-img" >
             <a href="<?php echo $Ads->alias($getAd); ?>" target="_blank" title="<?php echo $getAd["ads_title"]; ?>" >

               <div class="item-labels" >
                  <?php echo $Ads->outStatus($service, $getAd); ?>
               </div>

               <?php echo $Ads->CatalogOutAdGallery($images, $getAd); ?>

             </a>
             <?php echo $Ads->adActionFavorite($getAd, "catalog", "item-grid-favorite"); ?>
             </div>
             <div class="item-grid-info" >

                <div class="item-grid-price" >
                 <?php
                       echo $Ads->outPrice( [ "data"=>$getAd,"class_price"=>"item-grid-price-now","class_price_old"=>"item-grid-price-old", "shop"=>$getShop, "abbreviation_million" => true ] );
                 ?>        
                </div>
                <a href="<?php echo $Ads->alias($value); ?>" target="_blank" ><?php echo custom_substr($getAd["ads_title"], 35, "..."); ?></a>

                <span class="item-grid-city" >
                 <?php 
                     echo $Ads->outAdAddressArea($getAd);
                 ?>
                </span>
                <span class="item-grid-date" ><?php echo datetime_format($getAd["ads_datetime_add"], false); ?></span>

             </div>
          </div>
        <?php

      }

  }

  if($_POST["action"] == "modal_ads_subscriptions_add"){
      
     $error = [];

     $url = trim($_POST["url"], "/");

     if(validateEmail( $_POST["email"] ) == false){

          $error[] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");

     }else{

          $findUrl = findOne("uni_ads_subscriptions", "ads_subscriptions_params=? and ads_subscriptions_email=?", [$url,$_POST["email"]]);
          if($findUrl) $error[] = $ULang->t("Сохраненный поиск с такими параметрами уже существует!");

     }

     if( !count($error) ){
         
         insert("INSERT INTO uni_ads_subscriptions(ads_subscriptions_email,ads_subscriptions_id_user,ads_subscriptions_params,ads_subscriptions_date,ads_subscriptions_period,ads_subscriptions_date_update)VALUES(?,?,?,?,?,?)", [ $_POST["email"],intval($_SESSION['profile']['id']),$url,date("Y-m-d H:i:s"), intval($_POST["period"]),date("Y-m-d H:i:s") ]);
         
         $Subscription->add(array("email"=>$_POST["email"],"user_id"=>intval($_SESSION['profile']['id']),"name"=>$_POST["email"],"status" => 1));

         echo json_encode( [ "status" => true ] );

     }else{

         echo json_encode( [ "status" => false, "answer" => implode("\n", $error) ] );

     }

      

  }

  if($_POST["action"] == "catalog_ads_subscriptions_add"){

      if(!$_SESSION["profile"]["id"]){ exit(json_encode([ "auth" => false, "status" => false ])); }

      $url = trim($_POST["url"], "/");

      if($_SESSION["profile"]["data"]["clients_email"]){

         $findUrl = findOne("uni_ads_subscriptions", "ads_subscriptions_params=? and ads_subscriptions_email=?", [$url,$_SESSION["profile"]["data"]["clients_email"]]);
         
         if(!$findUrl){
            insert("INSERT INTO uni_ads_subscriptions(ads_subscriptions_email,ads_subscriptions_id_user,ads_subscriptions_params,ads_subscriptions_date,ads_subscriptions_period,ads_subscriptions_date_update)VALUES(?,?,?,?,?,?)", [ $_SESSION["profile"]["data"]["clients_email"],intval($_SESSION['profile']['id']),$url,date("Y-m-d H:i:s"), 1, date("Y-m-d H:i:s") ]);
         }

         echo json_encode( [ "status" => true, "auth" => true ] );
      }else{
         echo json_encode( [ "auth" => true, "status" => false ] );
      }

  }

  if($_POST["action"] == "media_slider_click"){

     update("update uni_sliders set sliders_click=sliders_click+? where sliders_id=?", [1, intval($_POST["id"]) ]);

  }

  if($_POST["action"] == "auction_accept_order_reservation"){

     $id_ad = (int)$_POST["id"];

     $getAd = $Ads->get("ads_id=? and ads_auction=?", [$id_ad,1]);
     
     if( $getAd["ads_auction_price_sell"] ){

         update( "update uni_ads set ads_status=? where ads_id=?", array(4,$id_ad), true );

         insert("INSERT INTO uni_ads_auction(ads_auction_id_ad,ads_auction_price,ads_auction_id_user,ads_auction_date)VALUES(?,?,?,?)", [$id_ad, $getAd["ads_auction_price_sell"], $_SESSION["profile"]["id"], date("Y-m-d H:i:s")]);

         update("update uni_ads set ads_price=? where ads_id=?", [$getAd["ads_auction_price_sell"] , $id_ad ], true);

         $Profile->sendChat( array("id_ad" => $id_ad, "action" => 3, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $getAd["ads_id_user"] ) );

         echo true;

     }

  }

  if($_POST["action"] == "create_accept_phone"){

     $phone = formatPhone($_POST["phone"]);
     $validatePhone = validatePhone($phone);

     if($validatePhone['status']){

         if( $settings["confirmation_phone"] ){
 
             if( $_SESSION["create-verify-phone-attempts"]["date"] ){

                 if( $_SESSION["create-verify-phone-attempts"]["date"] <= time() ){
                     unset($_SESSION["create-verify-phone-attempts"]);
                 }else{
                     $time = date("i ".$ULang->t('мин')." s " . $ULang->t('сек'), mktime(0, 0, $_SESSION["create-verify-phone-attempts"]["date"] - time() ) );
                     exit(json_encode([ "status" => false, "answer" => $ULang->t("Повторно отправить сообщение можно через") . ' ' . $time]));
                 }

             }else{

                 if( intval($_SESSION["create-verify-phone-attempts"]["count"]) >= 3 ){
                     $_SESSION["create-verify-phone-attempts"]["date"] = time() + 180;
                     $time = date("i ".$ULang->t('мин')." s " . $ULang->t('сек'), mktime(0, 0, 180 ) );
                     exit(json_encode(["status" => false, "answer" => $ULang->t("Повторно отправить сообщение можно через") . ' ' . $time]));
                 }

             }
            
            $_SESSION["create-verify-phone-attempts"]["count"]++;
            
            $_SESSION["create-verify-phone"][$phone]["code"] = smsVerificationCode($phone);

            echo json_encode(["status" => true]);

         }else{

            update("update uni_clients set clients_phone=? where clients_id=?", [$phone,$_SESSION["profile"]["id"]]);
            echo json_encode(["status"=>true]);

         }

    }else{
        echo json_encode(["status"=>false, "answer"=>$validatePhone['error']]);
    }

  }

  if($_POST["action"] == "create_verify_phone"){

     $phone = formatPhone( $_POST["phone"] );
     $code = intval( $_POST["code"] );

     if( $_SESSION["create-verify-phone"][$phone]["code"] && $_SESSION["create-verify-phone"][$phone]["code"] == $code ){
        $_SESSION["create-verify-phone"]["phone"] = $phone;
        echo true;
        unset($_SESSION["create-verify-phone-attempts"]);
     }else{
        echo $ULang->t("Неверный код");
     }

  }

  if($_POST["action"] == "update_count_display"){
      $Ads = new Ads();
      if($_SESSION['count_display_ads']){
         foreach ($_SESSION['count_display_ads'] as $id => $id_user) {
             $Ads->updateCountDisplay($id,$id_user);
         }
         unset($_SESSION['count_display_ads']);
      }
  }

  if($_POST["action"] == "mobile_menu_load_category"){

      $id = (int)$_POST['id'];

      $getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

      if(isset($getCategoryBoard["category_board_id"][$id]['category_board_id_parent'])){
          ?>
          <span class="mobile-fixed-menu_prev-category" data-id="<?php echo $getCategoryBoard["category_board_id"][$id]['category_board_id_parent']; ?>" ><i class="las la-arrow-left"></i> <?php echo $ULang->t('Назад'); ?></span>
          <?php
      }

      ?>
       <a class="mobile-fixed-menu_link-category" href="<?php echo $CategoryBoard->alias($getCategoryBoard["category_board_id"][$id]["category_board_chain"]); ?>" data-parent="false"  >
        
        <span class="mobile-fixed-menu_name-category" ><?php echo $ULang->t('Все категории'); ?></span>
        <span class="mobile-fixed-menu_count-category" ><?php echo $CategoryBoard->getCountAd( $id ); ?></span>

       </a>
      <?php

      if(count($getCategoryBoard["category_board_id_parent"][$id])){
        foreach ($getCategoryBoard["category_board_id_parent"][$id] as $value) {

           ?>
           <a class="mobile-fixed-menu_link-category" href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>" data-id="<?php echo $value["category_board_id"]; ?>" data-parent="<?php if(isset($getCategoryBoard["category_board_id_parent"][$value["category_board_id"]])){ echo 'true'; }else{ echo 'false'; } ?>"  >
            
            <span class="mobile-fixed-menu_name-category" ><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span>
            <span class="mobile-fixed-menu_count-category" ><?php echo $CategoryBoard->getCountAd( $value["category_board_id"] ); ?></span>

           </a>
           <?php

        }
      }

  }

  if($_POST["action"] == "mobile_menu_load_subcategory"){

      $id = (int)$_POST['id'];

      $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");

      $ids_cat = $CategoryBoard->reverseId($getCategories,$id);

      if($ids_cat){
          $ids_cat = explode(',', $ids_cat);
          foreach ($ids_cat as $key => $value) {
              $array_cats[$value] = $ids_cat[ $key + 1 ];
          }
      }

      if($array_cats){

         foreach ($array_cats as $id_main_cat => $id_sub_cat) {

              $parent_list = '';

              if($getCategories["category_board_id_parent"][$id_main_cat]){

                  foreach ($getCategories["category_board_id_parent"][$id_main_cat] as $key => $parent_value) {

                    if($parent_value["category_board_id"] == $id_sub_cat){ $active = 'class="uni-select-item-active"'; }else{ $active = ''; }
                       
                    $parent_list .=  '<label '.$active.' > <input type="radio" class="modal-filter-select-category" value="'.$parent_value["category_board_id"].'" > <span>'.$ULang->t($parent_value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'</span> <i class="la la-check"></i> </label>';

                  }

                  $select_subcategory .= '
                        <div class="uni-select" data-status="0" >

                             <div class="uni-select-name" data-name="'.$ULang->t("Не выбрано").'" > <span>'.$ULang->t("Не выбрано").'</span> <i class="la la-angle-down"></i> </div>
                             <div class="uni-select-list" >
                                 <label> <input type="radio" class="modal-filter-select-category" value="'.$id_main_cat.'" > <span>'.$ULang->t("Все категории").'</span> <i class="la la-check"></i> </label>
                                 '.$parent_list.'
                             </div>
                        
                        </div>
                  ';

              }

         }

      }

      if( $getCategories["category_board_id"][ $id ]["category_board_display_price"] ){

      $filters_list = '
          <div class="row" >
             <div class="col-lg-4" >
               <label>
                  '.$Main->nameInputPrice($getCategories["category_board_id"][ $id ]["category_board_variant_price_id"]).'                             
               </label>
             </div>
             <div class="col-lg-5" >
               
                <div class="filter-input" >
                  <div><span>'.$ULang->t("от").'</span><input type="text" class="inputNumber" name="filter[price][from]" value="" /></div>
                  <div><span>'.$ULang->t("до").'</span><input type="text" class="inputNumber" name="filter[price][to]" value="" /></div>
                </div>

             </div>
          </div>
      '; 

      }

      $filters_list .= '
          <div class="row mt15" >
             <div class="col-lg-4" >
               <label>
                  '.$ULang->t("Статус").'                             
               </label>
             </div>
             <div class="col-lg-8" >
             <div class="filter-items-spacing" >
      ';

      if( $getCategories["category_board_id"][ $id ]["category_board_secure"] && $settings["secure_status"] ){
          $filters_list .= '
          <div class="custom-control custom-checkbox" >
              <input type="checkbox" class="custom-control-input" name="filter[secure]" id="flsecure" value="1" >
              <label class="custom-control-label" for="flsecure">'.$ULang->t("Безопасная сделка").'</label>
          </div>
          ';
      }

      if( $getCategories["category_board_id"][ $id ]["category_board_auction"] ){
          $filters_list .= '
          <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="filter[auction]" id="flauction" value="1" >
              <label class="custom-control-label" for="flauction">'.$ULang->t("Аукционный товар").'</label>
          </div>
          ';
      }

      if( $getCategories["category_board_id"][ $id ]["category_board_online_view"] ){
          $filters_list .= '
          <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="filter[online_view]" id="online_view" value="1" >
              <label class="custom-control-label" for="online_view">'.$ULang->t("Онлайн-показ").'</label>
          </div>
          ';
      }

      $filters_list .= '
          <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="filter[vip]" id="flvip" value="1" >
              <label class="custom-control-label" for="flvip">'.$ULang->t("VIP объявление").'</label>
          </div>
      ';

      if( $getCategories["category_board_id"][ $id ]["category_board_booking"] ){

        if( $getCategories["category_board_id"][ $id ]["category_board_booking_variant"] == 0 ){

            $filters_list .= '
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="filter[booking]" id="booking_variant" value="1" >
                <label class="custom-control-label" for="booking_variant">'.$ULang->t("Онлайн-бронирование").'</label>
            </div>
            ';           

        }elseif( $getCategories["category_board_id"][ $id ]["category_board_booking_variant"] == 1 ){

            $filters_list .= '
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="filter[booking]" id="booking_variant" value="1" >
                <label class="custom-control-label" for="booking_variant">'.$ULang->t("Онлайн-аренда").'</label>
            </div>
            ';

        }

      }

      $filters_list .= '</div></div></div>'; 

      $filters_list .= $Filters->load_filters_catalog( $id , "", "filters_modal" );
       
      echo json_encode( array("subcategory" => $select_subcategory, "filters" => $filters_list ) );

  }

  if($_POST["action"] == "mobile_user_step_route"){

     echo back_step_user();

     unset($_SESSION['user_step_route'][ count($_SESSION['user_step_route']) - 1 ]);

  }

  if($_POST["action"] == "load_booking"){

     $id_ad = (int)$_POST['id_ad'];
     $booking_guests = (int)$_POST['booking_guests'] ?: 1;
     $additional_services_total_price = 0;
     $booking_hour_count = (int)$_POST['booking_hour_count'] ?: 1;
     $booking_hour_start = clear($_POST['booking_hour_start']) ?: '12:00';

     $getAd = $Ads->get("ads_id=?",[$id_ad]);

     if(!$getAd) exit();

     $booking_date_start = $_POST['booking_date_start'] ? date('d.m.Y', strtotime($_POST['booking_date_start'])) : date('d.m.Y');

     if($_POST['booking_date_end']){
        $booking_date_end = date('d.m.Y', strtotime($_POST['booking_date_end']));
     }else{
        if($getAd["ads_booking_min_days"]){ 
            $booking_date_end = date('d.m.Y', strtotime('+'.$getAd["ads_booking_min_days"].' days')); 
        }else{ 
            $booking_date_end = date('d.m.Y', strtotime('+1 days')); 
        }
     }

     $difference_days = difference_days($booking_date_end,$booking_date_start) ?: 1;

     $booking_additional_services = json_decode($getAd["ads_booking_additional_services"], true);

     if($_POST['booking_additional_services'] && $getAd["ads_booking_additional_services"]){
        foreach ($_POST['booking_additional_services'] as $key => $value) {
            if($booking_additional_services[$key]){
                $additional_services_total_price += $booking_additional_services[$key]['price'];
            }
        }
     }

     if($getAd['ads_price_measure'] == 'hour'){
        $total = ($booking_hour_count * $getAd["ads_price"]) + $additional_services_total_price;
        $prepayment = calcPercent($booking_hour_count * $getAd["ads_price"], $getAd["ads_booking_prepayment_percent"]);
     }else{
        $total = ($difference_days * $getAd["ads_price"]) + $additional_services_total_price;
        $prepayment = calcPercent($difference_days * $getAd["ads_price"], $getAd["ads_booking_prepayment_percent"]);
     }

     if($getAd["category_board_booking_variant"] == 0){

     ?>

        <h4> <strong><?php echo $ULang->t("Бронирование"); ?></strong> </h4>

        <div class="modal-booking-errors" ></div>

        <div class="booking-change-date-box mt15 mb10" >
            <div class="row" >
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Заселение'); ?></strong></p>
                    <input type="text" class="form-control" name="booking_date_start" value="<?php echo $booking_date_start; ?>" >                    
                </div>
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Выезд'); ?></strong></p>
                    <input type="text" class="form-control" name="booking_date_end" value="<?php echo $booking_date_end; ?>" >
                </div>                        
            </div>
        </div>

        <?php if($getAd["ads_booking_max_guests"]){ ?>
        <p class="mt25 mb10" ><strong><?php echo $ULang->t('Количество гостей'); ?></strong></p>

        <div class="booking-max-guests-box" >
            <div class="row" >
                <div class="col-lg-6" >
                    <input type="number" class="form-control" name="booking_guests" placeholder="Максимум <?php echo $getAd["ads_booking_max_guests"]; ?>" value="<?php echo $booking_guests; ?>" >
                </div>                        
            </div>
        </div>
        <?php } ?>

    <?php }else{ ?>

        <h4> <strong><?php echo $ULang->t("Аренда"); ?></strong> </h4>

        <div class="modal-booking-errors" ></div>

        <?php if($getAd['ads_price_measure'] == 'hour'){ ?>
        <div class="booking-change-date-box mt15 mb10" >
            <div class="row" >
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Дата начала'); ?></strong></p>
                    <input type="text" class="form-control" name="booking_date_start" value="<?php echo $booking_date_start; ?>" >                    
                </div>
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Время начала'); ?></strong></p>
                    <input type="time" class="form-control" name="booking_hour_start" value="<?php echo $booking_hour_start; ?>" >
                </div>                        
            </div>
        </div>

        <div class="booking-change-time-box mt15 mb10" >
            <div class="row" >
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Количество часов'); ?></strong></p>
                    <select class="form-control" name="booking_hour_count" >
                        <option value="1" <?php if($booking_hour_count == 1){ echo 'selected=""'; } ?> >1</option>
                        <option value="2" <?php if($booking_hour_count == 2){ echo 'selected=""'; } ?> >2</option>
                        <option value="3" <?php if($booking_hour_count == 3){ echo 'selected=""'; } ?> >3</option>
                        <option value="4" <?php if($booking_hour_count == 4){ echo 'selected=""'; } ?> >4</option>
                        <option value="5" <?php if($booking_hour_count == 5){ echo 'selected=""'; } ?> >5</option>
                        <option value="6" <?php if($booking_hour_count == 6){ echo 'selected=""'; } ?> >6</option>
                        <option value="7" <?php if($booking_hour_count == 7){ echo 'selected=""'; } ?> >7</option>
                        <option value="8" <?php if($booking_hour_count == 8){ echo 'selected=""'; } ?> >8</option>
                        <option value="9" <?php if($booking_hour_count == 9){ echo 'selected=""'; } ?> >9</option>
                        <option value="10" <?php if($booking_hour_count == 10){ echo 'selected=""'; } ?> >10</option>
                        <option value="11" <?php if($booking_hour_count == 11){ echo 'selected=""'; } ?> >11</option>
                        <option value="12" <?php if($booking_hour_count == 12){ echo 'selected=""'; } ?> >12</option>
                        <option value="13" <?php if($booking_hour_count == 13){ echo 'selected=""'; } ?> >13</option>
                        <option value="14" <?php if($booking_hour_count == 14){ echo 'selected=""'; } ?> >14</option>
                        <option value="15" <?php if($booking_hour_count == 15){ echo 'selected=""'; } ?> >15</option>
                        <option value="16" <?php if($booking_hour_count == 16){ echo 'selected=""'; } ?> >16</option>
                        <option value="17" <?php if($booking_hour_count == 17){ echo 'selected=""'; } ?> >17</option>
                        <option value="18" <?php if($booking_hour_count == 18){ echo 'selected=""'; } ?> >18</option>
                        <option value="19" <?php if($booking_hour_count == 19){ echo 'selected=""'; } ?> >19</option>
                        <option value="20" <?php if($booking_hour_count == 20){ echo 'selected=""'; } ?> >20</option>
                        <option value="21" <?php if($booking_hour_count == 21){ echo 'selected=""'; } ?> >21</option>
                        <option value="22" <?php if($booking_hour_count == 22){ echo 'selected=""'; } ?> >22</option>
                        <option value="23" <?php if($booking_hour_count == 23){ echo 'selected=""'; } ?> >23</option>
                        <option value="24" <?php if($booking_hour_count == 24){ echo 'selected=""'; } ?> >24</option>
                    </select>                    
                </div>                        
            </div>
        </div>        
        <?php }else{ ?>
        <div class="booking-change-date-box mt15 mb10" >
            <div class="row" >
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Дата начала'); ?></strong></p>
                    <input type="text" class="form-control" name="booking_date_start" value="<?php echo $booking_date_start; ?>" >                    
                </div>
                <div class="col-lg-6" >
                    <p><strong><?php echo $ULang->t('Дата окончания'); ?></strong></p>
                    <input type="text" class="form-control" name="booking_date_end" value="<?php echo $booking_date_end; ?>" >
                </div>                        
            </div>
        </div>            
        <?php } ?>

    <?php } ?>

    <?php if($booking_additional_services){ ?>
        <p class="mt25 mb10" ><strong><?php echo $ULang->t('Дополнительные услуги'); ?></strong></p>

        <div class="booking-additional-services-box" >
            <?php
              foreach ($booking_additional_services as $key => $value) {

                  $checked = '';

                  if($_POST['booking_additional_services']){ 
                     if($_POST['booking_additional_services'][$key]){
                        $checked = 'checked=""';
                     }
                  }

                  ?>
                  <div class="booking-additional-services-box-item" >
                      <div class="row" >
                          <div class="col-lg-8" >
                              <label class="checkbox">
                                <input type="checkbox" <?php echo $checked; ?> name="booking_additional_services[<?php echo $key; ?>]" value="1" >
                                <span></span>
                                <?php echo $value['name']; ?>
                              </label>                                          
                          </div>
                          <div class="col-lg-4 text-right" >
                              <strong><?php echo $Main->price($value['price']); ?></strong>
                          </div>
                      </div>                                  
                  </div>
                  <?php

              }
            
            ?>
        </div>
    <?php } ?>

    <hr>

    <div class="modal-booking-box-total" >

        <?php if($getAd["category_board_booking_variant"] == 0){ ?>
            <span class="modal-booking-box-total-title1" ><?php echo $Main->price($getAd["ads_price"]); ?> × <?php echo $difference_days; ?> <?php echo ending($difference_days, $ULang->t('день'), $ULang->t('дня'), $ULang->t('дней')); ?></span>
        <?php }else{ ?>

            <?php if($getAd['ads_price_measure'] == 'hour'){ ?>
            <span class="modal-booking-box-total-title1" ><?php echo $Main->price($getAd["ads_price"]); ?> × <?php echo $booking_hour_count; ?> <?php echo ending($booking_hour_count, $ULang->t('час'), $ULang->t('часа'), $ULang->t('часов')); ?></span>
            <?php }else{ ?>
            <span class="modal-booking-box-total-title1" ><?php echo $Main->price($getAd["ads_price"]); ?> × <?php echo $difference_days; ?> <?php echo ending($difference_days, $ULang->t('день'), $ULang->t('дня'), $ULang->t('дней')); ?></span>
            <?php } ?>

        <?php } ?>

       <?php if($getAd["ads_booking_prepayment_percent"]){ ?>
            <span class="modal-booking-box-total-title1" >Предоплата <?php echo $Main->price($prepayment); ?></span>  
       <?php } ?>  

        <span class="modal-booking-box-total-title2" >Итого: <span class="modal-booking-box-total-price" ><?php echo $Main->price($total); ?></span></span>

    </div>

    <div class="mt25" >

        <button class="button-style-custom color-green mb5 modal-booking-add-order" ><?php echo $ULang->t('Оформить заказ'); ?></button>

    </div>

    <?php
   
  }

  if($_POST["action"] == "add_order_booking"){

         $errors = [];

         $id_ad = (int)$_POST['id_ad'];
         $booking_guests = (int)$_POST['booking_guests'];
         $busy_dates = [];
         $additional_services = [];
         $additional_services_total_price = 0;
         $booking_hour_count = (int)$_POST['booking_hour_count'] ?: 1;
         $booking_hour_start = clear($_POST['booking_hour_start']) ?: '12:00';

         $getAd = $Ads->get("ads_id=?",[$id_ad]);

         if(!$getAd) exit();

         $booking_date_start = $_POST['booking_date_start'] ? date('Y-m-d', strtotime($_POST['booking_date_start'])) : date('Y-m-d');

         if($_POST['booking_date_end']){
            $booking_date_end = date('Y-m-d', strtotime($_POST['booking_date_end']));
         }else{
            if($getAd["ads_booking_min_days"]){ 
                $booking_date_end = date('Y-m-d', strtotime('+'.$getAd["ads_booking_min_days"].' days')); 
            }else{ 
                $booking_date_end = date('Y-m-d', strtotime('+1 days')); 
            }
         }

         $difference_days = difference_days($booking_date_end,$booking_date_start) ?: 1;

         $booking_additional_services = json_decode($getAd["ads_booking_additional_services"], true);

         if($_POST['booking_additional_services'] && $getAd["ads_booking_additional_services"]){
            foreach ($_POST['booking_additional_services'] as $key => $value) {
                if($booking_additional_services[$key]){
                    $additional_services[$booking_additional_services[$key]['name']] = $booking_additional_services[$key]['price'];
                    $additional_services_total_price += $booking_additional_services[$key]['price'];
                }
            }
         }

         if($booking_date_start < date('Y-m-d')){
            $errors[] = $ULang->t('Выбранная дата недоступна!');
         }

         if($getAd["category_board_booking_variant"] == 0){

             $total = ($difference_days * $getAd["ads_price"]) + $additional_services_total_price;

             $x=0;
             $dates[] = date('Y-m-d', strtotime($booking_date_start));
             while ($x++<$difference_days){
               $dates[] = date('Y-m-d', strtotime("+".$x." day", strtotime($booking_date_start)));
             }

            if($getAd['ads_booking_min_days'] && $getAd['ads_booking_max_days']){
                if($difference_days < $getAd['ads_booking_min_days'] || $difference_days > $getAd['ads_booking_max_days']){
                    $errors[] = $ULang->t('Бронирование доступно от').' '.$getAd['ads_booking_min_days'].' '.$ULang->t('до').' '.$getAd['ads_booking_max_days'].' '.ending($getAd['ads_booking_max_days'], $ULang->t('день'), $ULang->t('дня'), $ULang->t('дней'));
                }                
            }elseif($getAd['ads_booking_min_days']){
                if($difference_days < $getAd['ads_booking_min_days']){
                    $errors[] = $ULang->t('Срок бронирования минимум').' '.$getAd['ads_booking_min_days'].' '.ending($getAd['ads_booking_min_days'], $ULang->t('день'), $ULang->t('дня'), $ULang->t('дней'));
                }
            }elseif($getAd['ads_booking_max_days']){
                if($difference_days > $getAd['ads_booking_max_days']){
                    $errors[] = $ULang->t('Срок бронирования максимум').' '.$getAd['ads_booking_max_days'].' '.ending($getAd['ads_booking_max_days'], $ULang->t('день'), $ULang->t('дня'), $ULang->t('дней'));
                }
            }

            if($getAd["ads_booking_max_guests"]){
                if($booking_guests > $getAd["ads_booking_max_guests"]){
                    $errors[] = $ULang->t('Максимум гостей').' '.$getAd["ads_booking_max_guests"];
                }
            }

            foreach ($dates as $date) {
               if(findOne('uni_ads_booking_dates', 'ads_booking_dates_date=? and ads_booking_dates_id_ad=?', [$date,$id_ad])){
                   $busy_dates[] = datetime_format(strtotime($date), false);
               }
            }

            if(count($busy_dates)){
                $errors[] = $ULang->t('По выбранным датам бронирование недоступно!');
            }

            if($booking_date_start > $booking_date_end){
                $errors[] = $ULang->t('Начальная дата не может быть больше конечной!');
            }

        }else{

            if($Ads->adCountActiveRent($id_ad) >= $getAd["ads_booking_available"]){
                $errors[] = $ULang->t('Аренда для данного объявления не доступна!');
            }

            if($getAd['ads_price_measure'] == 'hour'){

                $total = ($booking_hour_count * $getAd["ads_price"]) + $additional_services_total_price;

                $booking_date_end = date('Y-m-d', strtotime('+'.$booking_hour_count.' days'));

            }else{

                $total = ($difference_days * $getAd["ads_price"]) + $additional_services_total_price;

                $x=0;
                $dates[] = date('Y-m-d', strtotime($booking_date_start));
                while ($x++<$difference_days){
                   $dates[] = date('Y-m-d', strtotime("+".$x." day", strtotime($booking_date_start)));
                }

                foreach ($dates as $date) {
                   if(findOne('uni_ads_booking_dates', 'ads_booking_dates_date=? and ads_booking_dates_id_ad=?', [$date,$id_ad])){
                       $busy_dates[] = datetime_format(strtotime($date), false);
                   }
                }

                if(count($busy_dates)){
                    $errors[] = $ULang->t('По выбранным датам аренда недоступна!');
                }

                if($booking_date_start > $booking_date_end){
                    $errors[] = $ULang->t('Начальная дата не может быть больше конечной!');
                }

            }

        }

        if(!count($errors)){

            $orderId = generateOrderId();

            $insert_id = insert("INSERT INTO uni_ads_booking(ads_booking_id_ad,ads_booking_id_user_from,ads_booking_id_user_to,ads_booking_date_start,ads_booking_date_end,ads_booking_guests,ads_booking_number_days,ads_booking_date_add,ads_booking_additional_services,ads_booking_id_order,ads_booking_total_price,ads_booking_variant,ads_booking_hour_start,ads_booking_hour_count,ads_booking_measure)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$id_ad,$_SESSION['profile']['id'],$getAd['ads_id_user'],$booking_date_start,$booking_date_end,$booking_guests,$difference_days,date("Y-m-d H:i:s"),json_encode($additional_services,JSON_UNESCAPED_UNICODE),$orderId,$total,$getAd["category_board_booking_variant"],$booking_hour_start,$booking_hour_count,$getAd['ads_price_measure']]); 

            if($insert_id){

                if($getAd["category_board_booking_variant"] == 0){
                    foreach ($dates as $date) {
                       insert("INSERT INTO uni_ads_booking_dates(ads_booking_dates_date,ads_booking_dates_id_ad,ads_booking_dates_id_order)VALUES(?,?,?)", [$date,$id_ad,$insert_id]);
                    }
                }

                echo json_encode(['status'=>true, 'link'=>_link('booking/'.$orderId)]);

            }

        }else{
            echo json_encode(['status'=>false, 'answer'=>implode('<br>', $errors)]);
        }


  }

  if($_POST["action"] == "load_dates_booking"){
       
       $id_ad = (int)$_POST['id_ad'];

       $dates = [];

       $getDates = getAll('select * from uni_ads_booking_dates where ads_booking_dates_id_ad=?', [$id_ad]);

       if(count($getDates)){
            foreach ($getDates as $value) {
                $dates[] = date('Y-m-d', strtotime($value['ads_booking_dates_date']));
            }
       }

       echo json_encode($dates);

  }

  if($_POST["action"] == "order_delete_booking"){
       
      $id = (int)$_POST['id'];

      $getOrder = findOne("uni_ads_booking", "ads_booking_id=? and (ads_booking_id_user_from=? or ads_booking_id_user_to=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);

      if($getOrder){
          update('delete from uni_ads_booking where ads_booking_id=?', [$id]);
          update('delete from uni_ads_booking_dates where ads_booking_dates_id_order=?', [$id]);
      }

      echo json_encode(['link'=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/booking" )]);

  }

  if($_POST["action"] == "order_cancel_booking"){
       
      $id = (int)$_POST['id'];
      $reason = clear($_POST['reason']);

      if($reason){

          $getOrder = findOne("uni_ads_booking", "ads_booking_id=? and (ads_booking_id_user_from=? or ads_booking_id_user_to=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);

          if($getOrder){

              update('update uni_ads_booking set ads_booking_status=?,ads_booking_reason_cancel=? where ads_booking_id=?', [2,$reason,$id]);
              update('delete from uni_ads_booking_dates where ads_booking_dates_id_order=?', [$id]);

              if($getOrder["ads_booking_id_user_from"] == $_SESSION['profile']['id']){
                  $getUser = findOne("uni_clients", "clients_id=?", [$getOrder["ads_booking_id_user_to"]]);
              }else{
                  $getUser = findOne("uni_clients", "clients_id=?", [$getOrder["ads_booking_id_user_from"]]);
              }
              
              $getAd = $Ads->get("ads_id=?", [$getOrder["ads_booking_id_ad"]]);

              $data      = array("{USER_NAME}"=>$getUser["clients_name"],
                                 "{USER_EMAIL}"=>$getUser["clients_email"],
                                 "{ADS_TITLE}"=>$getAd["ads_title"],
                                 "{ADS_LINK}"=>$Ads->alias($getAd),
                                 "{REASON_TEXT}"=>$reason,
                                 "{PROFILE_LINK_ORDER}"=>_link('booking/'.$getOrder['ads_booking_id_order']),
                                 "{UNSUBCRIBE}"=>"",
                                 "{EMAIL_TO}"=>$getUser["clients_email"]);

              email_notification( array( "variable" => $data, "code" => "USER_CANCEL_ORDER_BOOKING" ) );

          }

          echo json_encode(['status'=>true]);

      }else{
         echo json_encode(['status'=>false, 'answer'=>$ULang->t('Пожалуйста, укажите причину отмены заказа.')]);
      }

  }

  if($_POST["action"] == "order_prepayment_booking"){
       
      $id = (int)$_POST['id'];

      $getOrder = findOne("uni_ads_booking", "ads_booking_id=? and (ads_booking_id_user_from=? or ads_booking_id_user_to=?)", [ $id, $_SESSION['profile']['id'], $_SESSION['profile']['id'] ]);

      if($getOrder){

          $getAd = $Ads->get("ads_id=?", [$getOrder['ads_booking_id_ad']]);

          if($getOrder["ads_booking_measure"] == 'hour'){
             $prepayment = calcPercent($getOrder['ads_booking_hour_count'] * $getAd["ads_price"], $getAd["ads_booking_prepayment_percent"]);
          }else{
             $prepayment = calcPercent($getOrder['ads_booking_number_days'] * $getAd["ads_price"], $getAd["ads_booking_prepayment_percent"]);
          }

          $result = $Profile->payMethod( $settings["booking_payment_service_name"] , array("amount" => $prepayment, "id_order" => $getOrder['ads_booking_id_order'], "id_ad" => $getOrder['ads_booking_id_ad'], "from_user_id" => $getOrder['ads_booking_id_user_from'], "to_user_id" => $getOrder['ads_booking_id_user_to'], "action" => "booking", "title" => $static_msg["57"]." №".$getOrder['ads_booking_id_order'], 'link_order' => _link('booking/'.$getOrder['ads_booking_id_order'])) );

          if($result['form']){
              echo json_encode(['status'=>true, 'form'=>$result['form']]);
          }else{
              echo json_encode(['status'=>true, 'link'=>$result['link']]);
          }

      }

  }

  if($_POST["action"] == "order_confirm_booking"){

      $id = (int)$_POST['id'];

      $getOrder = findOne("uni_ads_booking", "ads_booking_id=? and ads_booking_id_user_to=?", [ $id, $_SESSION['profile']['id'] ]);

      if($getOrder){

         update('update uni_ads_booking set ads_booking_status=? where ads_booking_id=?', [1, $id]);

         $getUser = findOne("uni_clients", "clients_id=?", [$getOrder["ads_booking_id_user_from"]]);

         $getAd = $Ads->get("ads_id=?", [$getOrder["ads_booking_id_ad"]]);

         $data      = array("{USER_NAME}"=>$getUser["clients_name"],
                             "{USER_EMAIL}"=>$getUser["clients_email"],
                             "{ADS_TITLE}"=>$getAd["ads_title"],
                             "{ADS_LINK}"=>$Ads->alias($getAd),
                             "{PROFILE_LINK_ORDER}"=>_link('booking/'.$getOrder['ads_booking_id_order']),
                             "{UNSUBCRIBE}"=>"",
                             "{EMAIL_TO}"=>$getUser["clients_email"]);

         if($getOrder['ads_booking_variant'] == 0){
            email_notification( array( "variable" => $data, "code" => "USER_CONFIRM_ORDER_BOOKING" ) );
         }else{
            email_notification( array( "variable" => $data, "code" => "USER_CONFIRM_ORDER_RENT" ) );
         }

         echo true;

      }

  }

  if($_POST["action"] == "catalog_load_categories_header"){

      $getCategoryBoard = (new CategoryBoard())->getCategories("where category_board_visible=1");

      ?>

          <div class="container" >
          <div class="row no-gutters" >
             <div class="col-lg-4" >
                 <div class="header-big-category-menu-left" >
                 <?php

                    if(count($getCategoryBoard["category_board_id_parent"][0])){
                          foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {

                            ?>
                             <div data-id="<?php echo $value["category_board_id"]; ?>" >

                                <a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>" <?php echo $active; ?> >
                                <?php if( $value["category_board_image"] ){ ?>
                                <div class="category-menu-left-image" >
                                  <img src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>" >
                                </div>
                                <?php } ?>
                                <div class="category-menu-left-name" ><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?><span class="header-big-category-count" ><?php echo $CategoryBoard->getCountAd( $value["category_board_id"] ); ?></span></div>
                                <div class="clr" ></div>
                                </a>

                             </div>
                            <?php

                          }
                    }

                 ?>
                 </div>
             </div>
             <div class="col-lg-8" >
                 <div class="header-big-category-menu-right" >

                 <?php

                    $count_key = 0;

                    if(count($getCategoryBoard["category_board_id_parent"][0])){
                          foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {

                               if( $getCategoryBoard["category_board_id_parent"][ $value["category_board_id"] ] ){
                                    
                                    $show = '';

                                    if( $count_key == 0 ){
                                        $show = ' style="display: block;" ';
                                    }

                                    $count_key++;

                                    echo '
                                      <div class="header-big-subcategory-menu-list" '.$show.' data-id-parent="'.$value["category_board_id"].'" >
                                      <h4>'.$Seo->replace($ULang->t( $value["category_board_title"], [ "table" => "uni_category_board", "field" => "category_board_title" ] )).'</h4>
                                      <div class="row no-gutters" >
                                    ';

                                    foreach ($getCategoryBoard["category_board_id_parent"][ $value["category_board_id"] ] as $subvalue1) {

                                        echo '
                                           <div class="col-lg-6" >
                                           <div data-id="'.$subvalue1["category_board_id"].'" >
                                             <a href="'.$CategoryBoard->alias($subvalue1["category_board_chain"]).'">'.$ULang->t( $subvalue1["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ).'<span class="header-big-category-count" >'.$CategoryBoard->getCountAd( $subvalue1["category_board_id"] ).'</span></a>
                                           </div>
                                           </div>
                                        ';

                                    }

                                    echo '
                                      </div>
                                      </div>
                                    ';

                               }

                          }
                    }

                 ?>

                 </div>
             </div>
          </div>
          </div>

      <?php

  }

  if($_POST["action"] == "load_content_index"){

        $getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

        $geo = $Ads->queryGeo() ? " and " . $Ads->queryGeo() : "";

        $param_search = $Elastic->paramAdquery();
        $param_search["query"]["bool"]["filter"][]["term"] = $Ads->arrayGeo();
        $param_search["query"]["bool"]["filter"][]["term"]["ads_vip"] = 1;

        if($settings["index_out_content_method"] == 0){
          $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' order by rand() limit 16", "param_search" => $param_search, "output" => 16 ] );
        }else{
          $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' $geo order by rand() limit 16", "param_search" => $param_search, "output" => 16 ] );
        }

        if($settings["index_out_count_shops"] && $settings["user_shop_status"]){
          $data["shops"] = getAll( "select * from uni_clients_shops INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_shops`.clients_shops_id_user where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and clients_status IN(0,1) order by rand() limit " . $settings["index_out_count_shops"] );
        }

        $data["slider_ad_category"] = $Main->outSlideAdCategory(16);

        if($_SESSION["geo"]["alias"]){
          $data["vip_link"] = _link($_SESSION["geo"]["alias"]."/vip");
        }else{
          $data["vip_link"] = _link($settings["country_default"]."/vip"); 
        }

        $data["h1"] = $Seo->out(array("page" => "index", "field" => "h1"));

        $data["sliders"] = getAll("select * from uni_sliders where sliders_visible=? order by sliders_sort asc", [1]);

        if( count($data["sliders"]) ){
             ?>

                <div class="load-sliders-wide" >
                <div class="sliders-wide" data-show-slider="<?php echo $settings["media_slider_count_show"]; ?>" data-autoplay="<?php echo $settings["media_slider_autoplay"]; ?>" data-arrows="<?php echo $settings["media_slider_arrows"]; ?>" >
                   
                   <?php
                   foreach ($data["sliders"] as $key => $value) {
                       ?>
                         <div class="sliders-wide-item" data-id="<?php echo $value["sliders_id"]; ?>" >

                              <a title="<?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?>. <?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?>" style="
                                <?php if($value["sliders_image"]){ ?>
                                background: url(<?php echo Exists($config["media"]["other"],$value["sliders_image"],$config["media"]["no_image"]); ?>);
                                background-position: right;
                                background-size: contain;
                                background-repeat: no-repeat;
                                <?php } ?>
                                background-color: <?php echo $value["sliders_color_bg"]; ?>;
                                display: block;
                                border-radius: 10px;
                                height: <?php echo $settings["media_slider_height"]; ?>px;
                                " target="_blank"  href="<?php echo $Main->sliderLink( $value["sliders_link"] ); ?>">
                                
                                <span class="sliders-wide-title">
                                  <span class="sliders-wide-title1"><?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?></span>
                                  <span class="sliders-wide-title2"><?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?></span>
                                </span>

                              </a>

                        </div>               
                       <?php
                   }
                   ?>

                </div>
                </div>

             <?php
        }

        echo $Banners->out( ["position_name"=>"index_top"] );
        
        ?>

        <div class="row mt30" >
          
          <div class="col-lg-12" >           
            <?php if( $data["vip"]["count"] ){ ?>
            <div class="vip-block" >
              <h4 class="mb25 title-and-link" > <strong><?php echo $ULang->t( "VIP объявления" ); ?></strong> <a href="<?php echo $data["vip_link"]; ?>" ><?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i></a> </h4>
              <div class="slider-item-grid init-slider-grid" >
                  <?php 
                  
                     foreach ( $data["vip"]["all"] as $key => $value) {
                         include $config["template_path"] . "/include/slider_ad_grid.php";
                     }
                  
                  ?>
              </div>
            </div>
            <?php
            } 
            ?>

          <?php if( count($data["shops"]) ){ ?>
            <h3 class="mt30 mb25 title-and-link" > <strong><?php echo $ULang->t( "Магазины" ); ?></strong> <a href="<?php echo $Shop->linkShops(); ?>"><?php echo $ULang->t( "Все магазины" ); ?> <i class="las la-arrow-right"></i> </a> </h3>
              <div class="row no-gutters gutters10 mb25" >
                  <?php 
                  
                     foreach ($data["shops"] as $key => $value) {
                         include $config["template_path"] . "/include/shop_grid.php";
                     }
                  
                  ?>
              </div>
            <?php
            } 
            ?>

            <?php
            if( count($data["slider_ad_category"]) ){
                foreach ($data["slider_ad_category"] as $id_category => $nested) {
                    ?>

                      <h3 class="mt20 mb25 title-and-link" > <strong><?php echo $ULang->t( $getCategoryBoard["category_board_id"][$id_category]["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></strong> 
                      <a href="<?php echo $CategoryBoard->alias($getCategoryBoard["category_board_id"][$id_category]["category_board_chain"]); ?>" >
                            <?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i>
                      </a>                            
                      </h3>
                      <div class="slider-item-grid init-slider-grid mb25" >
                          <?php 
                          
                            foreach ($nested as $key => $value) {
                                include $config["template_path"] . "/include/slider_ad_grid.php";
                            }
                          
                          ?>
                      </div>

                    <?php

                }
            }
            ?>

            <h1 style="font-size: 1.75rem;" class="mb25" > <strong><?php echo $data["h1"]; ?></strong> </h1>

            <div class="catalog-results" >
            
                <div class="preload" >

                    <div class="spinner-grow mt80 preload-spinner" role="status">
                      <span class="sr-only"></span>
                    </div>

                </div>

            </div>


          </div>
        </div>

        <?php
  }



}

?>