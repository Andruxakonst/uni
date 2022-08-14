<?php
 Class Seo{

   function replace($text=""){
   global $settings;

   $Geo = new Geo();
   $ULang = new ULang();

    if($_SESSION["geo"]){
       
       $geolocation = ["city"=>$_SESSION["geo"]["data"]["city_name"],"region"=>$_SESSION["geo"]["data"]["region_name"],"country"=>$_SESSION["geo"]["data"]["country_name"]];

    }else{

       $geolocation = $Geo->geoIp($_SERVER['REMOTE_ADDR']);

    }

    if($geolocation["city"]){
       $geo_name = $ULang->t($geolocation["city"] , [ "table" => "geo", "field" => "geo_name" ] );
    }elseif($geolocation["region"]){
       $geo_name = $ULang->t($geolocation["region"] , [ "table" => "geo", "field" => "geo_name" ] );
    }elseif($geolocation["country"]){
       $geo_name = $ULang->t($geolocation["country"] , [ "table" => "geo", "field" => "geo_name" ] );
    }

      $param_name = array(
        "{domen}",
        "{url}",
        "{country}",
        "{city}",
        "{region}",
        "{site_name}",
        "{geo}",
      );

      $param_val = array(
        $_SERVER["SERVER_NAME"],
        $config["urlPath"],
        $ULang->t($geolocation["country"] , [ "table" => "geo", "field" => "geo_name" ] ),
        $ULang->t($geolocation["city"] , [ "table" => "geo", "field" => "geo_name" ] ),
        $ULang->t($geolocation["region"] , [ "table" => "geo", "field" => "geo_name" ] ),
        $settings["site_name"],
        $geo_name  
      ); 

      return str_replace($param_name, $param_val, $text);

   }
    
   function out( $param = [], $data = [] ){
   global $settings;

   $lang_iso = getLang();

   $CategoryBoard = new CategoryBoard(); 
   $Geo = new Geo();
   $Main = new Main();
   $Blog = new Blog();
   $ULang = new ULang();
     
   if(count($param)){

    if($_SESSION["geo"]){
       
       $geolocation = ["city"=>$_SESSION["geo"]["data"]["city_name"],"region"=>$_SESSION["geo"]["data"]["region_name"],"country"=>$_SESSION["geo"]["data"]["country_name"]];

    }else{

       $geolocation = $Geo->geoIp($_SERVER['REMOTE_ADDR']);

    }

    if($geolocation["city"]){
       $geo_name = $ULang->t($geolocation["city"] , [ "table" => "geo", "field" => "geo_name" ] );
    }elseif($geolocation["region"]){
       $geo_name = $ULang->t($geolocation["region"] , [ "table" => "geo", "field" => "geo_name" ] );
    }elseif($geolocation["country"]){
       $geo_name = $ULang->t($geolocation["country"] , [ "table" => "geo", "field" => "geo_name" ] );
    }

    if($data["category"]["category_board_description"]){
       $data["category"]["category_board_description"] = $this->replace( $ULang->t(urldecode($data["category"]["category_board_description"]) , [ "table" => "uni_category_board", "field" => "category_board_description" ] ) );
    }

    if($data["category"]["category_board_text"]){
       $data["category"]["category_board_text"] = $this->replace( $ULang->t(urldecode($data["category"]["category_board_text"]) , [ "table" => "uni_category_board", "field" => "category_board_text" ] ) );
    }

    if($data["category"]["category_board_title"]){
       $data["category"]["category_board_title"] = $this->replace( $ULang->t($data["category"]["category_board_title"] , [ "table" => "uni_category_board", "field" => "category_board_title" ] ) );
    }

    if($data["category"]["category_board_h1"]){
       $data["category"]["category_board_h1"] = $this->replace( $ULang->t($data["category"]["category_board_h1"] , [ "table" => "uni_category_board", "field" => "category_board_h1" ] ) );
    }

    if($data["category"]["blog_category_desc"]){
       $data["category"]["blog_category_desc"] = $this->replace( $ULang->t(urldecode($data["category"]["blog_category_desc"]) , [ "table" => "uni_blog_category", "field" => "blog_category_desc" ] ) );
    }

    if($data["category"]["blog_category_text"]){
       $data["category"]["blog_category_text"] = $this->replace( $ULang->t(urldecode($data["category"]["blog_category_text"]) , [ "table" => "uni_blog_category", "field" => "blog_category_text" ] ) );
    }

    if($data["ad"]["ads_price"]){

       $data["ad"]["ads_price"] = $Main->price($data["ad"]["ads_price"], $data["ad"]["ads_currency"], true);

    }else{

       if( $data["ad"]["ads_price_free"] ){
           $data["ad"]["ads_price"] = $ULang->t("Даром");
       }else{ 
           $data["ad"]["ads_price"] = $ULang->t("Цена не указана");
       }

    }

    $param_name = array(
      "{domen}",
      "{url}",
      "{country}",
      "{city}",
      "{region}",
      "{site_name}",
      "{board_main_categories}",
      "{board_category_name}",
      "{board_category_title}",
      "{board_category_h1}",
      "{geo}",
      "{geo_meta_desc}",
      "{board_category_meta_desc}",
      "{board_category_text}",
      "{ad_title}",
      "{ad_text}",
      "{ad_city}",
      "{ad_region}",
      "{ad_country}",
      "{ad_price}",
      "{ad_publication}",
      "{blog_main_categories}",
      "{blog_category_name}",
      "{blog_category_title}",
      "{blog_category_h1}",
      "{blog_category_meta_desc}",
      "{blog_category_text}",      
      "{article_title}",
      "{article_meta_desc}", 
      "{shop_category_name}",
    );

    $param_val = array(
      $_SERVER["SERVER_NAME"],
      $config["urlPath"],
      $ULang->t($geolocation["country"] , [ "table" => "geo", "field" => "geo_name" ] ),
      $ULang->t($geolocation["city"] , [ "table" => "geo", "field" => "geo_name" ] ),
      $ULang->t($geolocation["region"] , [ "table" => "geo", "field" => "geo_name" ] ),
      $ULang->t($settings["site_name"]),
      $CategoryBoard->allMain(),
      $ULang->t($data["category"]["category_board_name"] , [ "table" => "uni_category_board", "field" => "category_board_name" ] ),
      $data["category"]["category_board_title"],
      $data["category"]["category_board_h1"],
      $geo_name,
      $ULang->t($_SESSION["geo"]["desc"] , [ "table" => "geo", "field" => "geo_desc" ] ),
      $data["category"]["category_board_description"],
      $data["category"]["category_board_text"],
      $data["ad"]["ads_title"],
      $data["ad"]["ads_text"],
      $data["ad"]["city_name"],
      $data["ad"]["region_name"],
      $data["ad"]["country_name"],
      $data["ad"]["ads_price"],
      date("d.m.Y", strtotime($data["ad"]["ads_datetime_add"]) ),
      $Blog->allMainCategory(),
      $ULang->t($data["category"]["blog_category_name"] , [ "table" => "uni_blog_category", "field" => "blog_category_name" ] ),
      $ULang->t($data["category"]["blog_category_title"] , [ "table" => "uni_blog_category", "field" => "blog_category_title" ] ),
      $ULang->t($data["category"]["blog_category_h1"] , [ "table" => "uni_blog_category", "field" => "blog_category_h1" ] ),
      $data["category"]["blog_category_desc"],
      $data["category"]["blog_category_text"],
      $ULang->t($data["article"]["blog_articles_title"] , [ "table" => "uni_blog_articles", "field" => "blog_articles_title" ] ),
      $ULang->t($data["article"]["blog_articles_desc"] , [ "table" => "uni_blog_articles", "field" => "blog_articles_desc" ] ),
      $data["current_category"]["category_board_name"],  
    );    
    
      if($param["page"]){    
         
          $get = getOne("SELECT * FROM uni_seo WHERE page=? and lang_iso=?", array($param["page"], $lang_iso));
          
          if(count($get)){

            if($get[$param["field"]]){
                if(!$param["text"]){
                    return str_replace($param_name, $param_val, $get[$param["field"]]);
                }else{
                    $replace = str_replace($param_name, $param_val, $get[$param["field"]]);
                    return str_replace($param_name, $param_val, $replace);
                }
            }

          }
      }else{
         if($param["text"]) return str_replace($param_name, $param_val, $param["text"]);
      }


    }
   
   } 

   function allowedPages( $data = [] ){
       
       global $settings;
       
       if(!$settings["seo_empty_page"]) return true;
       
       $CategoryBoard = new CategoryBoard();

       if( $data["id_cat"] ){

           $ids_cat = idsBuildJoin( $CategoryBoard->idsBuild($data["id_cat"], $CategoryBoard->getCategories()), $data["id_cat"] );

           if($data["city_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_id_cat IN(".$ids_cat.") and ads_city_id='".$data["city_id"]."'");
           }elseif($data["region_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_id_cat IN(".$ids_cat.") and ads_region_id='".$data["region_id"]."'");
           }elseif($data["country_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_id_cat IN(".$ids_cat.") and ads_country_id='".$data["country_id"]."'");
           }

           return $getAdCount["total"];

       }else{
           
           if($data["city_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_city_id='".$data["city_id"]."'");
           }elseif($data["region_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_region_id='".$data["region_id"]."'");
           }elseif($data["country_id"]){
              $getAdCount = getOne("select count(*) as total from uni_ads where ads_status='1' and ads_period_publication > now() and ads_country_id='".$data["country_id"]."'");
           }

           return $getAdCount["total"];

       }

   }

    
 }
 
?>