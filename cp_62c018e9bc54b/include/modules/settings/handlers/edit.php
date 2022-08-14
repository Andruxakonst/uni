<?php
define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");

if( !(new Admin())->accessAdmin($_SESSION['cp_control_settings']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

$Main = new Main();

if(isAjax() == true){

   $error = array();
   

   if(!$_POST["email_noreply"]){$error[] = "Пожалуйста, укажите ответный E-Mail!";}

   if(isset($_POST["notification_method_new_ads"])){
      $notification_method_new_ads = implode(",",$_POST["notification_method_new_ads"]);
   }else{
      $notification_method_new_ads = "";
   }


   if(isset($_POST["notification_method_new_user"])){
      $notification_method_new_user = implode(",",$_POST["notification_method_new_user"]);
   }else{
      $notification_method_new_user = "";
   }

   if(isset($_POST["notification_method_new_buy"])){
      $notification_method_new_buy = implode(",",$_POST["notification_method_new_buy"]);
   }else{
      $notification_method_new_buy = "";
   }

   if(isset($_POST["notification_method_new_chat_message"])){
      $notification_method_new_chat_message = implode(",",$_POST["notification_method_new_chat_message"]);
   }else{
      $notification_method_new_chat_message = "";
   }

   if( intval($_POST["variant_send_mail"]) == 1 ){
    
       $_POST["smtp_host"] = "";
       $_POST["smtp_port"] = "";
       $_POST["smtp_username"] = "";
       $_POST["smtp_password"] = "";

   }else{

       if( $_POST["smtp_password"] ){
          $_POST["smtp_password"] = encrypt($_POST["smtp_password"]);
       }else{
          $_POST["smtp_password"] = $settings["smtp_password"];
       }

   }

   if(!$_POST["ad_create_period_list"]){
      $_POST["ad_create_period"] = 0;
   }

   if($_POST["count_images_add_ad"] > 100){
      $_POST["count_images_add_ad"] = 100;
   }

   if(!$_POST["region_id"]){
      $_POST["city_id"] = 0;
   }

   if(!intval($_POST["catalog_out_content"])){
      $_POST["catalog_out_content"] = 60;
   }

   if(!intval($_POST["blog_out_content"])){
      $_POST["blog_out_content"] = 20;
   }

   if(!intval($_POST["index_out_content"])){
      $_POST["index_out_content"] = 32;
   }

   if(!intval($_POST["index_out_count_shops"])){
      $_POST["index_out_count_shops"] = 3;
   }

   if( $_POST["api_id_telegram"] ){
      $_POST["api_id_telegram"] = encrypt($_POST["api_id_telegram"]);
   }

   if( $_POST["sms_service_pass"] ){
      $_POST["sms_service_pass"] = encrypt($_POST["sms_service_pass"]);
   }

   if( $_POST["sms_service_id"] ){
      $_POST["sms_service_id"] = encrypt($_POST["sms_service_id"]);
   }

   if( $_POST["social_auth_params"] ){
      $_POST["social_auth_params"] = encrypt( json_encode($_POST["social_auth_params"]) );
   }
   
   if( intval($_POST["watermark_caption_opacity"]) < 0 || intval($_POST["watermark_caption_opacity"]) > 100 ){
       $_POST["watermark_caption_opacity"] = 100;
   }

   if( !$_POST["user_shop_count_sliders"] ){
        $_POST["user_shop_count_sliders"] = 1;
   }

   if($_POST["marketplace_status"]){
      $_POST["ad_create_currency"] = 0;
   }

   $_POST["user_shop_alias_url_all"] = translite($_POST["user_shop_alias_url_all"]);
   $_POST["user_shop_alias_url_page"] = translite($_POST["user_shop_alias_url_page"]);

   if(!$_POST["user_shop_alias_url_all"]){
       $_POST["user_shop_alias_url_all"] = 'shops';
   }

   if(!$_POST["user_shop_alias_url_page"]){
       $_POST["user_shop_alias_url_page"] = 'shop';
   }

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_publication_moderat"]),'ads_publication_moderat'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_publication_auto_moderat"]),'ads_publication_auto_moderat'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["lang_site_default"]),'lang_site_default'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["visible_lang_site"]),'visible_lang_site'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["main_timezone"]),'main_timezone'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["country_default"]),'country_default'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["region_id"]),'region_id'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["city_id"]),'city_id'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service"]),'sms_service'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service_id"]),'sms_service_id'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service_login"]),'sms_service_login'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service_pass"]),'sms_service_pass'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service_label"]),'sms_service_label'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_service_method_send"]),'sms_service_method_send'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["sms_prefix_confirmation_code"]),'sms_prefix_confirmation_code'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["ad_format_photo"]),'ad_format_photo'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["marketplace_status"]),'marketplace_status'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["marketplace_view_cart"]),'marketplace_view_cart'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["marketplace_available_cart"]),'marketplace_available_cart'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["authorization_method"]),'authorization_method'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["registration_method"]),'registration_method'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(implode(",",$_POST["authorization_social"]),'authorization_social'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["city_auto_detect"]),'city_auto_detect'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["count_images_add_ad"]),'count_images_add_ad'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_time_publication_default"]),'ads_time_publication_default'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["catalog_out_content"]),'catalog_out_content'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["blog_out_content"]),'blog_out_content'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["index_out_content"]),'index_out_content'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["index_out_count_shops"]),'index_out_count_shops'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_currency"]),'ad_create_currency'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_period"]),'ad_create_period'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["ad_create_period_list"]),'ad_create_period_list'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_currency_price"]),'ads_currency_price'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["secure_status"]),'secure_status'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["auto_lang_detection"]),'auto_lang_detection'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["logo_color_inversion"]),'logo_color_inversion'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["confirmation_phone"]),'confirmation_phone'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["seo_empty_page"]),'seo_empty_page'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["shops_out_content"]),'shops_out_content'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_length_title"]),'ad_create_length_title'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_length_text"]),'ad_create_length_text'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["abbreviation_million"]),'abbreviation_million'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["catalog_city_distance"]),'catalog_city_distance'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_comments"]),'ads_comments'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["assets_vendors"]),'assets_vendors'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["banner_markup"]),'banner_markup'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(json_encode($_POST["bonus"]),'bonus_program'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_view_phone"]),'ad_view_phone'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["type_content_loading"]),'type_content_loading'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["index_out_content_method"]),'index_out_content_method'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_similar_count"]),'ad_similar_count'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["robots_index_site"]),'robots_index_site'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["robots_manual_setting"]),'robots_manual_setting'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["robots_exclude_link"]),'robots_exclude_link'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(round($_POST["min_deposit_balance"],2),'min_deposit_balance'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(round($_POST["max_deposit_balance"],2),'max_deposit_balance'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["user_shop_count_sliders"]),'user_shop_count_sliders'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["user_shop_count_pages"]),'user_shop_count_pages'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($_POST["user_shop_alias_url_all"],'user_shop_alias_url_all'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($_POST["user_shop_alias_url_page"],'user_shop_alias_url_page'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["user_shop_status"]),'user_shop_status'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["contact_phone"]),'contact_phone'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["contact_email"]), 'contact_email'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["contact_address"]),'contact_address'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($notification_method_new_ads,'notification_method_new_ads'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($notification_method_new_user,'notification_method_new_user'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($notification_method_new_buy,'notification_method_new_buy'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($notification_method_new_chat_message,'notification_method_new_chat_message'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["email_alert"]),'email_alert'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["phone_alert"]),'phone_alert'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["access_site"]),'access_site'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["access_action"]),'access_action'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["access_text"]),'access_text'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["access_redirect_link"]),'access_redirect_link'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["access_allowed_ip"]),'access_allowed_ip'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["name_responder"]),'name_responder'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["email_noreply"]),'email_noreply'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["variant_send_mail"]),'variant_send_mail'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["smtp_host"]),'smtp_host'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["smtp_port"]),'smtp_port'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["smtp_username"]),'smtp_username'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["smtp_password"]),'smtp_password'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["smtp_secure"]),'smtp_secure'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($Main->clearPHP($_POST["code_script"]),'code_script'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array($Main->clearPHP($_POST["header_meta"]),'header_meta'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["api_id_telegram"]),'api_id_telegram'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["chat_id_telegram"]),'chat_id_telegram'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(implode(",",$_POST["payment_variant"]),'payment_variant'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["site_name"]),'site_name'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["title"]),'title'));
   
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["ad_black_list_words"]),'ad_black_list_words'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_phone"]),'ad_create_phone'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ad_create_always_image"]),'ad_create_always_image'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["map_vendor"]),'map_vendor'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["map_google_key"]),'map_google_key'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["map_yandex_key"]),'map_yandex_key'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["map_openstreetmap_key"]),'map_openstreetmap_key'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["social_link_vk"]),'social_link_vk'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["social_link_ok"]),'social_link_ok'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["social_link_you"]),'social_link_you'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["social_link_telegram"]),'social_link_telegram'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["watermark_status"]),'watermark_status'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["watermark_type"]),'watermark_type'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["watermark_pos"]),'watermark_pos'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["watermark_caption"]),'watermark_caption'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["watermark_caption_font"]),'watermark_caption_font'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["watermark_caption_size"]),'watermark_caption_size'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["watermark_caption_opacity"]),'watermark_caption_opacity'));
   
   update("UPDATE uni_settings SET value=? WHERE name=?", array($_POST["social_auth_params"],'social_auth_params'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_alias_filters"]),'sitemap_alias_filters'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_seo_filters"]),'sitemap_seo_filters'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_blog"]),'sitemap_blog'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_blog_category"]),'sitemap_blog_category'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_services"]),'sitemap_services'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_cities"]),'sitemap_cities'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_category"]),'sitemap_category'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_shops"]),'sitemap_shops'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["sitemap_ads"]),'sitemap_ads'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["ads_sorting_variant"]),'ads_sorting_variant'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["secure_payment_service_name"]),'secure_payment_service_name'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["pwa_name"]),'pwa_name'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["pwa_short_name"]),'pwa_short_name'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array( mb_substr(clear($_POST["pwa_desc"]), 0, 255, "UTF-8") ,'pwa_desc'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["pwa_status"]),'pwa_status'));

   update("UPDATE uni_settings SET value=? WHERE name=?", array(clear($_POST["booking_payment_service_name"]),'booking_payment_service_name'));
   update("UPDATE uni_settings SET value=? WHERE name=?", array(intval($_POST["booking_prepayment_percent_service"]),'booking_prepayment_percent_service'));
    
    $manifest = [];
    $manifest["dir"] = "ltr";
    $manifest["lang"] = "Russian";
    $manifest["name"] = $_POST["pwa_name"];
    $manifest["short_name"] = $_POST["pwa_short_name"];
    $manifest["scope"] = $config["urlPrefix"];
    $manifest["display"] = "standalone";
    $manifest["start_url"] = $config["urlPath"] . "/";
    $manifest["url"] = $config["urlPath"] . "/";
    $manifest["background_color"] = "#FFFFFF";
    $manifest["theme_color"] = "#FFFFFF";
    $manifest["description"] = mb_substr($_POST["pwa_desc"], 0, 255, "UTF-8");
    $manifest["orientation"] = "any";
    $manifest["related_applications"] = [];
    $manifest["prefer_related_applications"] = false;

    $manifest["icons"][] = [ "src" => $config["urlPath"] . '/' . $config["template_folder"] . "/icons_pwa/icon-192x192.png", "sizes" => "192x192", "type" => "image/png", "purpose" => "any maskable" ];
    $manifest["icons"][] = [ "src" => $config["urlPath"] . '/' . $config["template_folder"] . "/icons_pwa/icon-256x256.png", "sizes" => "256x256", "type" => "image/png" ];
    $manifest["icons"][] = [ "src" => $config["urlPath"] . '/' . $config["template_folder"] . "/icons_pwa/icon-384x384.png", "sizes" => "384x384", "type" => "image/png" ];
    $manifest["icons"][] = [ "src" => $config["urlPath"] . '/' . $config["template_folder"] . "/icons_pwa/icon-512x512.png", "sizes" => "512x512", "type" => "image/png" ];
    $manifest["screenshots"][] = [ "src" => $config["urlPath"] . '/' . $config["template_folder"] . "/icons_pwa/icon-512x512.png", "sizes" => "512x512", "type" => "image/png" ];

    if(!empty($_FILES['pwa_icon']['name'])){

        $path = $config["template_path"] . "/icons_pwa/";
        $max_file_size = 1;
        $extensions = array('png');
        $ext = strtolower(pathinfo($_FILES['pwa_icon']['name'], PATHINFO_EXTENSION));
        
        if($_FILES["pwa_icon"]["size"] > $max_file_size*1024*1024){
            $error[] = "Иконка для pwa не загружена. Максимальный размер файла ".$max_file_size.' mb!';
        }{
                
        if (in_array($ext, $extensions))
        {
                
                $image_name = 'icon-512x512.png';
                $path = $path . $image_name;

                if (!move_uploaded_file($_FILES['pwa_icon']['tmp_name'], $path))
                {                    
                    $error[] = "Иконка для pwa не загружена. Недостаточно прав на запись.";                                   
                }else{
                    resize($path, $config["template_path"] . "/icons_pwa/icon-384x384.png" , 384, 384, 100);
                    resize($path, $config["template_path"] . "/icons_pwa/icon-256x256.png" , 256, 256, 100);
                    resize($path, $config["template_path"] . "/icons_pwa/icon-192x192.png" , 192, 192, 100);
                }
                
        update("UPDATE uni_settings SET value=? WHERE name = ?", array($image_name,'pwa_image')); 

        }else{
                $error[] = "Иконка для pwa не загружена. Допустимые форматы ".implode(",",$extensions); 
        }

        }       
        
    }
    
    if( intval($_POST["pwa_status"]) ){
        file_put_contents( $config["basePath"] . "/manifest.json" , json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) );
    }
   
   if( $_POST["secure_payment_service_name"] ){

       update("UPDATE uni_payments SET secure_percent_service=? WHERE code=?", array( round($_POST["secure_percent_service"],2), clear($_POST["secure_payment_service_name"]) ));

       update("UPDATE uni_payments SET secure_percent_payment=? WHERE code=?", array( round($_POST["secure_percent_payment"],2), clear($_POST["secure_payment_service_name"]) ));

       update("UPDATE uni_payments SET secure_other_payment=? WHERE code=?", array( round($_POST["secure_other_payment"],2), clear($_POST["secure_payment_service_name"]) ));

       update("UPDATE uni_payments SET secure_min_amount_payment=? WHERE code=?", array( round($_POST["secure_min_amount_payment"],2), clear($_POST["secure_payment_service_name"]) ));

       update("UPDATE uni_payments SET secure_max_amount_payment=? WHERE code=?", array( round($_POST["secure_max_amount_payment"],2), clear($_POST["secure_payment_service_name"]) ));

   }

   if($_POST["country_default"]){

     $country = findOne("uni_country","country_alias=?", array(clear($_POST["country_default"])));

     update("UPDATE uni_settings SET value=? WHERE name=?", array($country->country_lat,'country_lat'));
     update("UPDATE uni_settings SET value=? WHERE name=?", array($country->country_lng,'country_lng'));  
     update("UPDATE uni_settings SET value=? WHERE name=?", array($country->country_id,'country_id')); 

   }


   $main = clear($_POST["main_currency"]);
   
   if(isset($_POST["currency"])){

    $currency_array = array();

     foreach($_POST["currency"] AS $array){
         foreach($array AS $id => $data){
          
          if(!empty($data["name"])){
            $currency_array[$id]["name"] = clear($data["name"]);  
          }
          if(!empty($data["sign"])){
            $currency_array[$id]["sign"] = clear($data["sign"]);  
          }
          if(!empty($data["code"])){
            $currency_array[$id]["code"] = clear($data["code"]);  
          } 

        }    
     }

     foreach($currency_array AS $id => $data){
       if(!empty($id)){
          
         if(!empty($main)){           
             if($data["code"] == $main){
                $main_update = ",main='1'";
             }else{
                $main_update = ",main='0'";
             }  
         }

         update("UPDATE uni_currency SET name='{$data["name"]}',sign='{$data["sign"]}',code='{$data["code"]}',visible='1' $main_update WHERE id = '$id'"); 
                                              
       }
    }

  }


  if(!empty($_FILES['watermark_img']['name'])){

      $path = $config["basePath"] . "/" . $config["media"]["other"] . "/";
      $max_file_size = 1;
      $extensions = array('png');
      $ext = strtolower(pathinfo($_FILES['watermark_img']['name'], PATHINFO_EXTENSION));
      
      if($_FILES["watermark_img"]["size"] > $max_file_size*1024*1024){
          $error[] = "Watermark не загружен. Максимальный размер файла ".$max_file_size.' mb!';
      }else{

        if (in_array($ext, $extensions))
        {
              
              $image_name = md5("watermark") . "." . $ext;
              $path = $path . $image_name;

              if (!move_uploaded_file($_FILES['watermark_img']['tmp_name'], $path))
              {                    

                  $error[] = "Watermark не загружен. Недостаточно прав на запись в директорию " . $config["media"]["other"];                                   
              }
              
        update("UPDATE uni_settings SET value=? WHERE name = ?", array($image_name,'watermark_img'));      
        }else{
              $error[] = "Watermark не загружен. Допустимые форматы ".implode(",",$extensions); 
        }

      }
                       
  }

  if(!empty($_FILES['logo']['name'])){

      $path = $config["basePath"] . "/" . $config["media"]["other"] . "/";
      $max_file_size = 1;
      $extensions = array('png','jpg','jpeg', 'gif', 'svg');
      $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
      
      if($_FILES["logo"]["size"] > $max_file_size*1024*1024){
          $error[] = "Основной логотип не загружен. Максимальный размер файла ".$max_file_size.' mb!';
      }{
              
        if (in_array($ext, $extensions))
        {
              
              $image_name = md5("logo") . "." . $ext;
              $path = $path . $image_name;

              if (!move_uploaded_file($_FILES['logo']['tmp_name'], $path))
              {      

                  $error[] = "Основной логотип не загружен. Недостаточно прав на запись в директорию " . $config["media"]["other"];                                   
              }
              
        update("UPDATE uni_settings SET value=? WHERE name = ?", array($image_name,'logo-image')); 

        }else{
              $error[] = "Основной логотип не загружен. Допустимые форматы ".implode(",",$extensions); 
        }

      }       
      
  }

  if(!empty($_FILES['logo-mobile']['name'])){

      $path = $config["basePath"] . "/" . $config["media"]["other"] . "/";
      $max_file_size = 1;
      $extensions = array('png','jpg','jpeg', 'gif', 'svg');
      $ext = strtolower(pathinfo($_FILES['logo-mobile']['name'], PATHINFO_EXTENSION));
      
      if($_FILES["logo-mobile"]["size"] > $max_file_size*1024*1024){
          $error[] = "Логотип для мобильной версии не загружен. Максимальный размер файла ".$max_file_size.' mb!';
      }{
              
        if (in_array($ext, $extensions))
        {
              
              $image_name = md5("logo-mobile") . "." . $ext;
              $path = $path . $image_name;

              if (!move_uploaded_file($_FILES['logo-mobile']['tmp_name'], $path))
              {      

                  $error[] = "Логотип для мобильной версии не загружен. Недостаточно прав на запись в директорию " . $config["media"]["other"];                                   
              }
              
        update("UPDATE uni_settings SET value=? WHERE name = ?", array($image_name,'logo-image-mobile')); 

        }else{
              $error[] = "Логотип для мобильной версии не загружен. Допустимые форматы ".implode(",",$extensions); 
        }

      }       
      
  }

  if(!empty($_FILES['favicon']['name'])){

      $path = $config["basePath"] . "/";
      $max_file_size = 1;
      $extensions = array('png');
      $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
      
      if($_FILES["favicon"]["size"] > $max_file_size*1024*1024){
          $error[] = "Favicon не загружен. Максимальный размер файла ".$max_file_size.' mb!';
      }{
              
        if (in_array($ext, $extensions))
        {
              
              $image_name = 'favicon-120x120.'.$ext;
              $path = $path . $image_name;

              if (!move_uploaded_file($_FILES['favicon']['tmp_name'], $path))
              {                    
                  $error[] = "Favicon не загружен. Недостаточно прав на запись.";                                   
              }else{
                  resize($path, $config["basePath"] . "/favicon.ico" , 32, 32, 100);
                  resize($path, $config["basePath"] . "/favicon-120x120.".$ext , 120, 120, 100);
                  resize($path, $config["basePath"] . "/favicon-96x96.".$ext , 96, 96, 100);
                  resize($path, $config["basePath"] . "/favicon-32x32.".$ext , 32, 32, 100);
                  resize($path, $config["basePath"] . "/favicon-16x16.".$ext , 16, 16, 100);
              }
              
        update("UPDATE uni_settings SET value=? WHERE name = ?", array($image_name,'favicon-image')); 

        }else{
              $error[] = "Favicon не загружен. Допустимые форматы ".implode(",",$extensions); 
        }

      }       
      
  }

  if(isset($_POST["payment_param"])){

       $param = json_encode($_POST["payment_param"]);
       $param = encrypt($param);
       update("UPDATE uni_payments SET param=? WHERE code = ?", array($param,$_POST["payment"]));

  }

  if( $_POST["robots_manual_setting"] ){

       if( !file_put_contents($config["basePath"] . "/robots.txt", $_POST["robots"]) ){
           $error[] = "Недостаточно прав на запись для файла robots.txt";
       }

  }else{

       $robots_index_site = (int)$_POST["robots_index_site"];

       $content_robots = "User-agent: *\n";

       if(!$robots_index_site){
         $content_robots .= "Disallow: /\n";
       }
       
       $content_robots .= "Host: " . $config["urlPath"] . "\n";
       $content_robots .= "Sitemap: " . $config["urlPath"] . "/sitemap.xml\n";

       $content_robots .= "Disallow: /media/\n";
       $content_robots .= "Disallow: /temp/\n";
       $content_robots .= "Disallow: /templates/\n";

       if( $_POST["robots_exclude_link"] ){
           $links = explode(PHP_EOL, $_POST["robots_exclude_link"]);
           foreach ($links as $key => $value) {
              if($value) $content_robots .= "Disallow: $value";
           }
       }

       if( !file_put_contents($config["basePath"] . "/robots.txt", $content_robots) ){
           $error[] = "Недостаточно прав на запись для файла robots.txt";
       }

  }


  if(count($error) > 0){ $_SESSION["CheckMessage"]["error"] = implode("<br/>",$error);  }
  else { $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!"; }
    
   
}     
?>