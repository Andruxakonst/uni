<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_city']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){

$cache = new Cache();
$Main = new Main();

$id = intval($_POST["id"]);

$getCountry = findOne("uni_country", "country_id =?", [$id]);

$status = intval($_POST["status"]);
$country = clear($_POST["country"]);
$desc = clear($_POST["desc"]);
$code_phone = clear($_POST["code_phone"]);
$format_phone = clear($_POST["format_phone"]);

$lat = clear($_POST["lat"]);
$lng = clear($_POST["lng"]);

$alias = translite($_POST["alias"]);

if(!$code_phone){
   $format_phone = '';
}

if(!$alias){
	$alias = translite($country);
}

$error = array();
 
if(empty($country)){$error[] = 'Укажите название страны';}

if( !$_POST["image_delete"] ){

  if(count($error) == 0){
      $image = $Main->uploadedImage( ["files"=>$_FILES["image"], "path"=>$config["media"]["other"], "prefix_name"=>"country"] );
      if($image["error"]){
          $error = array_merge($error,$image["error"]);
      }else{
          if($image["name"]) $getCountry["country_image"] = $image["name"];
      }    
  }

}else{

  $getCountry["country_image"] = "";

}

if (count($error) == 0) {
       
      update("UPDATE uni_country SET country_name=?, country_status = ?, country_alias=?, country_desc=?, country_lat=?, country_lng=?, country_format_phone=?, country_code_phone=?, country_image=?, country_declination=? WHERE country_id=?", array($country,$status,$alias,$desc,$lat,$lng,$format_phone,$code_phone,$getCountry["country_image"],clear($_POST["declination"]),$id));  
      $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";
      echo true;

      $cache->update("cityDefault");
              
} else { $_SESSION["CheckMessage"]["warning"] = implode("<br/>", $error); }

}  
?>