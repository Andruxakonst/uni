<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );

verify_csrf_token();

$Ads = new Ads();
$Main = new Main();
$Geo = new Geo();
$ULang = new ULang();

if(isAjax() == true){


    if($_POST["action"] == "change-city"){

		$Geo->set( array( "city_id" => intval($_POST["city_id"]) , "region_id" => intval($_POST["region_id"]) , "country_id" => intval($_POST["country_id"]), "action" => "modal" ) );

    }

    if($_POST["action"] == "search-city-region"){
        
        $query = clearSearch( $_POST["q"] );

		if($query && mb_strlen($query, "UTF-8") >= 2 ){

			$results = $Geo->search($query);

		    if(count($results)){

		       foreach($results AS $data){
                     
                     if($data["region_name"]){
			       	 $list["region"][$data["region_name"]] = '
			            <div class="item-city" data-name="'.$data["region_name"].'" id-country="0"  id-city="0"  id-region="'.$data["region_id"].'" >
			            	<strong>'.$ULang->t( $data["region_name"], [ "table" => "geo", "field" => "geo_name" ] ).'</strong>
			            </div>
			       	 ';
			       	 }
                     
                     if($data["city_name"]){
			       	 $list["city"][] = '
			            <div class="item-city"  data-name="'.$data["city_name"].'" id-country="0" id-region="0" id-city="'.$data["city_id"].'" >
			            	<strong>'.$ULang->t( $data["city_name"], [ "table" => "geo", "field" => "geo_name" ] ).'</strong> <span class="span-subtitle" >'.$ULang->t( $data["region_name"], [ "table" => "geo", "field" => "geo_name" ] ).', '.$ULang->t( $data["country_name"], [ "table" => "geo", "field" => "geo_name" ] ).'</span>
			            </div>
			       	 ';
			       	 }
			       	 
			       	 if($data["country_name"]){
			       	 $list["country"][$data["country_name"]] = '
			            <div class="item-city" data-name="'.$data["country_name"].'"  id-city="0"  id-region="0" id-country="'.$data["country_id"].'" >
			            	<strong>'.$ULang->t( $data["country_name"], [ "table" => "geo", "field" => "geo_name" ] ).'</strong>
			            </div>
			       	 ';
			       	 }

		       }


		       if($list["region"]) echo implode("", $list["region"]);
		       echo implode("", $list["city"]);
		       if($list["country"]) echo implode("", $list["country"]);

   

		    }else{
		    	echo false;
		    }

		}else{
			echo false;
		}       

    }

    if($_POST["action"] == "search-city"){
        
        $query = clearSearch( $_POST["q"] );

		if($query && mb_strlen($query, "UTF-8") >= 2 ){

			if($settings["region_id"]) $where_region = "and `uni_region`.region_id = '{$settings["region_id"]}'"; else $where_region = "";
            
		    $get = getAll("SELECT * FROM uni_city INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_city`.country_id INNER JOIN `uni_region` ON `uni_region`.region_id = `uni_city`.region_id WHERE `uni_country`.country_status = '1' $where_region and `uni_city`.city_name LIKE '%".$query."%' order by city_name asc");

		    if(count($get)>0){

		       foreach($get AS $data){

		        if($data["region_name"] == $data["country_name"]){

			          ?>
			            <div class="item-city" data-city="<?php echo $data["city_name"]; ?>"  id-city="<?php echo $data["city_id"]; ?>" >
			            	<strong><?php echo $ULang->t( $data["city_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?></strong> <span class="span-subtitle" ><?php echo $ULang->t( $data["country_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?></span>
			            </div>
			          <?php

		        }else{

			          ?>
			            <div class="item-city"  data-city="<?php echo $data["city_name"]; ?>"  id-city="<?php echo $data["city_id"]; ?>" >
			            	<strong><?php echo $ULang->t( $data["city_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?></strong> <span class="span-subtitle" ><?php echo $ULang->t( $data["region_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?>, <?php echo $ULang->t( $data["country_name"], [ "table" => "geo", "field" => "geo_name" ] ); ?></span>
			            </div>
			          <?php

		        }


		       }   

		    }else{
		    	echo false;
		    }

		}else{
			echo false;
		}       

    }

    if($_POST["action"] == "city-options"){

       $id = (int)$_POST["id"];

       $get_metro = getOne("SELECT count(*) as total FROM uni_metro WHERE city_id = '$id'")["total"];
       $get_area = getAll("SELECT * FROM uni_city_area WHERE city_area_id_city = '$id' order by city_area_name asc");

       if($get_area){

       	  foreach ($get_area as $key => $value) {
       	  	
             $items .= '<label> <input type="radio" name="area[]" value="'.$value["city_area_id"].'" > <span>'.$ULang->t( $value["city_area_name"], [ "table" => "uni_city_area", "field" => "city_area_name" ] ).'</span> <i class="la la-check"></i> </label>';

       	  }

       	  $data .= '
            <div class="ads-create-main-data-box-item" >      
            <p class="ads-create-subtitle" >'.$ULang->t("Район").'</p> 

	       	     <div class="ads-create-main-data-city-options-area" >
	                <div class="uni-select" data-status="0" >

		                 <div class="uni-select-name" data-name="'.$ULang->t("Не выбрано").'" > <span>'.$ULang->t("Не выбрано").'</span> <i class="la la-angle-down"></i> </div>
		                 <div class="uni-select-list" >
		                     '.$items.'
		                 </div>
	                
	                </div>
	             </div>

            </div>
       	  ';
       }

       if($get_metro){

       	  $data .= '
            <div class="ads-create-main-data-box-item" >      
            <p class="ads-create-subtitle" >'.$ULang->t("Ближайшее метро").'</p>

	       	     <div class="ads-create-main-data-city-options-metro" >
		            <div class="container-custom-search" >
		              <input type="text" class="ads-create-input action-input-search-metro" placeholder="'.$ULang->t("Начните вводить станции, а потом выберите ее из списка").'" >
		              <div class="custom-results SearchMetroResults" ></div>
		            </div>

		            <div class="ads-container-metro-station" ></div>
		         </div>

	        </div>
       	  ';

       }
       
       echo $data; 

    }

    if($_POST["action"] == "search_metro"){

    	$search = clear( $_POST["search"] );
    	$city_id = (int)$_POST["city_id"];
        
        if($search){

	    	$getAll = getAll("select * from uni_metro where name like '%$search%' and parent_id!=0 and city_id='".$city_id."'");

	    	if(count($getAll)){
	    		foreach ($getAll as $key => $value) {
	    			$main = findOne("uni_metro", "id=?", [$value["parent_id"]]);
	    			?>
		            <div  data-name="<?php echo $value["name"]; ?>" data-id="<?php echo $value["id"]; ?>" data-color="<?php echo $main["color"]; ?>" >
		            	<strong><i style="background-color:<?php echo $main["color"]; ?>;"></i> <?php echo $value["name"]; ?></strong> <span class="span-subtitle" ><?php echo $main["name"]; ?></span>
		            </div>    			
	    			<?php
	    		}
	    	}else{
	    		echo false;
	    	}

        }else{
        	echo false;
        }

    }

    if($_POST["action"] == "load_country_city"){

    	$country_alias = clear($_POST["alias"]);
        
        echo $Geo->cityDefault($country_alias,30,false);

    }

    if($_POST["action"] == "mobile_menu_load_geo"){

		  $Geo->set( array( "city_id" => intval($_POST["city_id"]) , "region_id" => intval($_POST["region_id"]) , "country_id" => intval($_POST["country_id"]), "action" => "modal" ) );

		  $city_areas = getAll("select * from uni_city_area where city_area_id_city=? order by city_area_name asc", [ intval($_SESSION["geo"]["data"]["city_id"]) ]);
		  $city_metro = getAll("select * from uni_metro where city_id=? and parent_id!=0 Order by name ASC", [ intval($_SESSION["geo"]["data"]["city_id"]) ]); 

		  if($city_areas){
		  ?>

		      <div class="uni-select" data-status="0" >

		           <div class="uni-select-name" data-name="<?php echo $ULang->t("Район"); ?>" > <span><?php echo $ULang->t("Район"); ?></span> <i class="la la-angle-down"></i> </div>
		           <div class="uni-select-list" >
		               <?php
		               foreach ($city_areas as $value) {
		                  ?>
		                  <label> <input type="checkbox" name="filter[area][]" value="<?php echo $value["city_area_id"]; ?>" > <span><?php echo $ULang->t( $value["city_area_name"], [ "table" => "uni_city_area", "field" => "city_area_name" ] ); ?></span> <i class="la la-check"></i> </label>
		                  <?php
		               }
		               ?>
		           </div>
		      
		      </div>

		  <?php
		  }

		  if($city_metro){
		  ?>

		      <div class="container-custom-search">
		        <input type="text" class="ads-create-input action-input-search-metro" placeholder="<?php echo $ULang->t("Поиск станций метро"); ?>">
		        <div class="custom-results SearchMetroResults" style="display: none;"></div>
		      </div>

		      <div class="ads-container-metro-station"></div>

		  <?php
		  }

    }





}

?>