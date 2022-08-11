<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );
$static_msg = require $config["basePath"] . "/static/msg.php";

verify_csrf_token();

$Ads = new Ads();
$Profile = new Profile();
$Main = new Main();
$ULang = new ULang();

if(isAjax() == true){

  if($_POST["action"] == "load_element"){

     if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_page"] ){
         echo file_get_contents( $config["template_path"]."/include/promo/" . $_POST["name"] . ".html" );
     }

  }

  if($_POST["action"] == "save"){
     
     if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_page"] ){

         update("update uni_promo_pages set promo_pages_html_edit=?,promo_pages_logotip=?,promo_pages_color=? where promo_pages_id=?", [ trim($_POST["html"]),intval($_POST["logo"]),clear($_POST["color"]), intval($_POST["id"]) ]);

        $_POST["html"] = preg_replace('#<div class="promo-add-element">.*?</div>#s', "", trim($_POST["html"]));
        $_POST["html"] = preg_replace('#<div class="promo-controls">.*?</div>#s', "", trim($_POST["html"]));

        update("update uni_promo_pages set promo_pages_html_public=? where promo_pages_id=?", [ $_POST["html"], intval($_POST["id"]) ]);


     }

  }
  
if($_POST["action"] == "image"){
     
   if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION["cp_control_page"] ){

      $name_file = "promo_" . uniqid() . ".jpg";

      base64_to_image( $_POST["image"] , $config["basePath"] . "/" . $config["media"]["other"] . "/" . $name_file );

      echo $config["media"]["other"] . "/" . $name_file ;

   }

}


}

?>