<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_board']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){
 
 $error = array();

 $Main = new Main();
 $Cache = new Cache();
 
 $_POST["name"] = addslashes($_POST["name"]);
 $_POST["title"] = addslashes($_POST["title"]);
 $_POST["h1"] = addslashes($_POST["h1"]);
 $_POST["desc"] = addslashes($_POST["desc"]);

 if($_POST["paid"]){
    if(!$_POST["price"]){
       $error[] = "Укажите стоимость размещения";
    }
 }
 
 if(!$_POST["name"]){$error[] = "Укажите название категории";}

 if(empty($_POST["alias"])){
     $alias = translite($_POST["name"]);         
 }else{
     $alias = translite($_POST["alias"]);
 }

if(!$_POST["title"]){
    $_POST["title"] = $_POST["name"];
}

if(!$_POST["h1"]){
    $_POST["h1"] = $_POST["title"];
}

if(!$_POST["display_price"]){
    $_POST["auction"] = 0;
    $_POST["variant_price"] = 0;
    $_POST["secure"] = 0;
}

if( $_POST["variant_price"] != 0 && $_POST["variant_price"] !=2 ){
    $_POST["auction"] = 0;
    $_POST["secure"] = 0;
}

if( $_POST["variant_price"] == 1 || $_POST["variant_price"] == 2 ){
    $_POST["online_view"] = 0;
}

if( !$_POST["image_delete"] ){

  if(count($error) == 0){
      $image = $Main->uploadedImage( ["files"=>$_FILES["image"], "path"=>$config["media"]["other"], "prefix_name"=>"category"] );
      if($image["error"]){
          $error = array_merge($error,$image["error"]);
      }    
  }

}

if (count($error) == 0) {

      $insert = insert("INSERT INTO uni_category_board(category_board_name,category_board_title,category_board_alias,category_board_description,category_board_id_parent,category_board_image,category_board_text,category_board_visible,category_board_price,category_board_count_free,category_board_status_paid,category_board_display_price,category_board_variant_price,category_board_measure_price,category_board_auction,category_board_secure,category_board_online_view,category_board_h1,category_board_show_index,category_board_marketplace)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(clear($_POST["name"]),clear($_POST["title"]),$alias,$_POST["desc"],$_POST["id_cat"],$image["name"],urlencode($_POST["text"]),intval($_POST["visible"]),round($_POST["price"],2),intval($_POST["count_free"]),intval($_POST["paid"]),intval($_POST["display_price"]),intval($_POST["variant_price"]),intval($_POST["measure_price"]),intval($_POST["auction"]),intval($_POST["secure"]),intval($_POST["online_view"]),clear($_POST["h1"]),intval($_POST["show_index"]),intval($_POST["marketplace"])));        
        
      $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";
      echo true;  

      $Cache->update( "uni_category_board" );             
    
    } else {
              $_SESSION["CheckMessage"]["warning"] = implode("<br/>", $error);        
           }

}
?>