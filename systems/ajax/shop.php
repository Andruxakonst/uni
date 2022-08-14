<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );
$static_msg = require $config["basePath"] . "/static/msg.php";

verify_csrf_token();

$Profile = new Profile();
$Main = new Main();
$ULang = new ULang();
$Shop = new Shop();
$Ads = new Ads();
$Elastic = new Elastic();
$Filters = new Filters();

$Profile->checkAuth();

verify_auth(['edit_shop','add_slide','add_page','delete_shop']);

if(isAjax() == true){

  if($_POST["action"] == "edit_shop"){
      
      $error = [];

      $shop_title = clear($_POST["shop_title"]);
      $shop_desc = clear($_POST["shop_desc"]);
      $shop_id = translite($_POST["shop_id"]);
      $shop_theme_category = (int)$_POST["shop_theme_category"];
      $sliders = count($_POST['slider']) ? array_slice($_POST['slider'], 0, $settings["user_shop_count_sliders"]) : [];

      $getShop = $Shop->getShop(['user_id'=>$_SESSION["profile"]["id"],'conditions'=>false]);

      if(!$shop_title) $error["shop_title"] = $ULang->t("Пожалуйста, укажите название магазина.");
      
      if($shop_id && $_SESSION['profile']['tariff']['services']['unique_shop_address']){

         $getShopId = findOne("uni_clients_shops", "clients_shops_id_hash=? and clients_shops_id_user!=?", [$shop_id,$_SESSION["profile"]["id"]]);

         if($getShopId) $error["shop_id"] = $ULang->t("Идентификатор") . " {$shop_id} " . $ULang->t("уже используется на сайте."); 

      }else{
         $shop_id = md5($_SESSION["profile"]["id"]);
      }

      if(!$_POST["image_status"]){

           if(!$_FILES['logo']['tmp_name']){

                $getShop["clients_shops_logo"] = "";
                @unlink( $config["basePath"] . "/" . $config["media"]["other"] . "/" .  $getShop["clients_shops_logo"] );

           }else{

                if(count($error) == 0){
                    $image = $Main->uploadedImage( ["files"=>$_FILES["logo"], "path"=>$config["media"]["other"], "prefix_name"=>"shop_logo"] );
                    if($image["error"]){
                        $error["image"] = $image["error"][0];
                    }else{
                        if($image["name"]) $getShop["clients_shops_logo"] = $image["name"];
                    }    
                }

           }
      }
      
      if( count($error) == 0 ){

          if(count($sliders)){

             $getSliders = getAll('select * from uni_clients_shops_slider where clients_shops_slider_id_shop=?',[$getShop["clients_shops_id"]]);
             if(count($getSliders)){
                foreach ($getSliders as $value) {
                    if(!in_array($value["clients_shops_slider_image"], $sliders)){
                        @unlink($config["basePath"] . "/" . $config["media"]["users"] . "/" . $value["clients_shops_slider_image"]);
                        update("delete from uni_clients_shops_slider where clients_shops_slider_id=?", [$value['clients_shops_slider_id']]);
                    }else{
                        unset($sliders[$value["clients_shops_slider_image"]]);
                    }
                }
             }

             if(count($sliders)){
                 foreach ($sliders as $value) {
                     if(file_exists($config["basePath"]."/".$config["media"]["temp_images"]."/".$value)){

                        if(copy($config["basePath"]."/".$config["media"]["temp_images"]."/".$value, $config["basePath"]."/".$config["media"]["users"]."/".$value)){
                            insert("INSERT INTO uni_clients_shops_slider(clients_shops_slider_id_shop,clients_shops_slider_image,clients_shops_slider_id_user)VALUES(?,?,?)", [$getShop["clients_shops_id"],$value,$_SESSION["profile"]["id"]]);
                        }

                     }
                 }
             }  

          }else{
             $getSliders = getAll('select * from uni_clients_shops_slider where clients_shops_slider_id_shop=?',[$getShop["clients_shops_id"]]);
             if(count($getSliders)){
                foreach ($getSliders as $value) {
                    @unlink($config["basePath"] . "/" . $config["media"]["users"] . "/" . $value["clients_shops_slider_image"]);
                    update("delete from uni_clients_shops_slider where clients_shops_slider_id=?", [$value['clients_shops_slider_id']]);
                }
             }
          }
          
          update("update uni_clients_shops set clients_shops_id_hash=?,clients_shops_title=?,clients_shops_desc=?,clients_shops_logo=?,clients_shops_id_theme_category=? where clients_shops_id_user=?", [$shop_id,$shop_title,$shop_desc,$getShop["clients_shops_logo"],$shop_theme_category,$_SESSION["profile"]["id"]]);

          echo json_encode( [ "status" => true, "redirect" => $Shop->linkShop($shop_id) ] );
      }else{
          echo json_encode( [ "status" => false, "answer" => $error ] );
      }


  }

  if($_POST["action"] == "add_slide"){

      $getShop = $Shop->getShop(['user_id'=>$_SESSION["profile"]["id"],'conditions'=>false]);

      if(!$getShop) exit;

      $countSliders = (int)getOne("select count(*) as total from uni_clients_shops_slider where clients_shops_slider_id_user=?", [ $_SESSION["profile"]["id"] ])["total"];

      if( $countSliders >= $settings["user_shop_count_sliders"] ){
          echo json_encode( ["status" => false, "answer" => $ULang->t("Максимальное количество слайдов") . ' ' . $settings["user_shop_count_sliders"] . " " . $ULang->t("шт.") ] );
          exit;
      }

      $image = $Main->uploadedImage(["files"=>$_FILES["image"], "path"=>$config["media"]["temp_images"], "prefix_name"=>"slide"], 10);
      if($image["error"]){
          echo json_encode(["status" => false, "answer" => implode("\n", $image["error"])]);
      }else{

          resize( $config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $image["name"] , $config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $image["name"], 1920, 0);

          echo json_encode( ["status" => true, "img" => '<div class="shop-container-sliders-img" style="background-image: url('.$config["urlPath"] . "/" . $config["media"]["temp_images"] . "/" . $image["name"].'); background-position: center center; background-size: cover;" > <span class="shop-container-sliders-delete" ><i class="las la-times"></i></span> <input type="hidden" name="slider['.$image["name"].']" value="'.$image["name"].'" /> </div>' ] );
      }

  }

  if($_POST["action"] == "subscribe"){

      if(!$_SESSION["profile"]["id"]){ exit(json_encode(["status" => false, "auth" => false])); }

      $id_user = (int)$_POST["id_user"];
      $id_shop = (int)$_POST["id_shop"];

      $get = findOne("uni_clients_subscriptions", "clients_subscriptions_id_user_from=? and clients_subscriptions_id_user_to=?", [$_SESSION["profile"]["id"],$id_user]);

      if( $get ){
          update("delete from uni_clients_subscriptions where clients_subscriptions_id=?", [ $get["clients_subscriptions_id"] ]);
          echo json_encode( ["status" => false, "auth" => true] );
      }else{
          insert("INSERT INTO uni_clients_subscriptions(clients_subscriptions_id_user_from,clients_subscriptions_id_user_to,clients_subscriptions_id_shop,clients_subscriptions_date_add)VALUES(?,?,?,?)", [$_SESSION["profile"]["id"],$id_user,$id_shop, date("Y-m-d H:i:s") ]);
          echo json_encode( ["status" => true, "auth" => true] );
      }

  }

  if($_POST["action"] == "add_page"){

    $id_shop = (int)$_POST["id_shop"];
    $name = custom_substr(clear($_POST["name"]), 50);
    $error = [];

    $getShop = findOne("uni_clients_shops", "clients_shops_id=? and clients_shops_id_user=?", [ $id_shop, $_SESSION["profile"]["id"] ]);
    
    if(!$getShop || !$_SESSION['profile']['tariff']['services']['shop_page']){
       exit;
    }

    $getPages = getAll( "select * from uni_clients_shops_page where clients_shops_page_id_shop=?", [ $id_shop ] );

    if( count($getPages) < $settings["user_shop_count_pages"] ){

        if( !$name ){ $error[] = $ULang->t("Пожалуйста, укажите название страницы"); }else{

            if( findOne( "uni_clients_shops_page", "clients_shops_page_id_shop=? and clients_shops_page_alias=?", [ $id_shop, translite($name) ] ) ){
                $error[] = $ULang->t("Страница с таким названием уже существует!");
            }

        }
    
    }else{
        
        $error[] = $ULang->t("Исчерпан лимит добавления страниц!");

    }

    if( count( $error ) == 0 ){
        insert("INSERT INTO uni_clients_shops_page(clients_shops_page_id_shop,clients_shops_page_name,clients_shops_page_alias)VALUES(?,?,?)", [ $id_shop, $name, translite($name) ]);
        echo json_encode( ["status" => true, "link" => $Shop->aliasPage( $getShop["clients_shops_id_hash"], translite($name) )] );
    }else{
        echo json_encode( ["status" => false, "answer" => implode( "\n", $error ) ] );
    }

}

if($_POST["action"] == "delete_page"){

    $id_page = (int)$_POST["id_page"];
    $id_shop = (int)$_POST["id_shop"];

    $getShop = findOne("uni_clients_shops", "clients_shops_id=? and clients_shops_id_user=?", [ $id_shop, $_SESSION["profile"]["id"] ]);
    
    if(!$getShop || !$_SESSION['profile']['tariff']['services']['shop_page']){
       exit;
    }

    update("delete from uni_clients_shops_page where clients_shops_page_id=? and clients_shops_page_id_shop=?", [ $id_page, $id_shop ]);

    echo $Shop->linkShop( $getShop->clients_shops_id_hash );
}

if($_POST["action"] == "save_text_page"){

    $id_page = (int)$_POST["id_page"];
    $id_shop = (int)$_POST["id_shop"];
    $text = $_POST["text"];

    $getShop = findOne("uni_clients_shops", "clients_shops_id=? and clients_shops_id_user=?", [ $id_shop, $_SESSION["profile"]["id"] ]);
    
    if(!$getShop || !$_SESSION['profile']['tariff']['services']['shop_page']){
       exit;
    }

    update("update uni_clients_shops_page set clients_shops_page_text=? where clients_shops_page_id=? and clients_shops_page_id_shop=?", [ $text, $id_page, $id_shop ]);

    echo true;
}

if($_POST["action"] == "shops_load"){

    $page = (int)$_POST["page"] ? (int)$_POST["page"] : 1;
    $id_c = (int)$_POST["id_c"];
    
    if($id_c){
       $query = " and ( clients_shops_id_theme_category='{$id_c}' or clients_shops_id_theme_category='0' )";
    }
    
    $count = (int)getOne("select count(*) as total from uni_clients_shops INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_shops`.clients_shops_id_user where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and clients_status IN(0,1) {$query}")["total"];
    $results = getAll( "select * from uni_clients_shops INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_shops`.clients_shops_id_user where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and clients_status IN(0,1) {$query} order by clients_shops_id desc" . navigation_offset( array( "count"=>$count, "output"=>$settings["shops_out_content"], "page"=>$page ) ) );
    
    if($results){

      if($page <= getCountPage($count,$settings["shops_out_content"])){

          foreach ($results as $key => $value) {
             ob_start();
             include $config["template_path"] . "/include/shop_list.php";
             $content .= ob_get_clean();
          }

      }

      if($page + 1 <= getCountPage($count,$settings["shops_out_content"])){
        
        $found = true;

        if( $settings["type_content_loading"] == 1 ){
            $content .= '
              
              <div class="col-lg-12" >
              <div class="ajax-load-button action-shops-load text-center mt20" >
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
                  <h5>'.$ULang->t("Ничего не найдено").'</h5>
              </div>
           </div>
           </div>
       ';

    }


    echo json_encode( array("content"=>$content, "found"=>$found) );
  
  }

  if($_POST["action"] == "load_shop_ads"){
    
    $page = (int)$_POST["page"] ? (int)$_POST["page"] : 1;
    $query = clear( $_POST["search"] );
    $id_user = (int)$_POST["id_u"];

    $param_search = $Elastic->paramAdSearch( $query, $id_user );
    $param_search["sort"]["ads_sorting"] = [ "order" => "desc" ];

    if( $query ){

      $results = $Ads->getAll( array( "query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_id_user='{$id_user}' and " . $Filters->explodeSearch( $query ), "navigation"=>true, "page"=>$page, "param_search" => $param_search ) );
      
    }else{

      $results = $Filters->queryFilter($_POST, ["navigation"=>true, "page"=>$page, "disable_query_geo" => true]);

    }

    if($results["count"]){

      if($page <= getCountPage($results["count"],$settings["catalog_out_content"])){

          foreach ($results["all"] as $key => $value) {
             ob_start();
             include $config["template_path"] . "/include/shop_ad_grid.php";
             $content .= ob_get_clean();
          }
        
      }

      if($page + 1 <= getCountPage($results["count"],$settings["catalog_out_content"])){

        $found = true;
        
        if( $settings["type_content_loading"] == 1 ){
            $content .= '
              
              <div class="col-lg-12" >
              <div class="action-shop-load-ads text-center mt20" >
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
      }


    }else{

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

    }


    echo json_encode( array("content"=>$content, "found"=>$found) );
  
  }

  if($_POST["action"] == "delete_shop"){

      $id = (int)$_POST["id"];

      $getShop = findOne("uni_clients_shops", "clients_shops_id_user=? and clients_shops_id=?", [$_SESSION["profile"]["id"], $id]);

      if($getShop){
         $Shop->deleteShop($id);
      }

      echo _link("user/".$_SESSION["profile"]["data"]["clients_id_hash"]);
     
  }


}

?>