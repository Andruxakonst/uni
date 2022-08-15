<?php

define('unisitecms', true);
session_start();

$config = require "../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if(isAjax() == true){
  
  $id = (int)$_POST["id"];
  $params = $_POST["params"];

  $always = ["title", "phone", "name_user", "city", "region", "text", "category"];

  foreach ($always as $key => $value) {
     if(!$params[$value]){
      $_SESSION["CheckMessage"]["error"] = "Пожалуйста, заполните обязательные поля";
      echo json_encode( ["status"=>false] );
      exit;
     }
  }

  update("update uni_ads_import set ads_import_status=?,ads_import_params=? where ads_import_id=?", [1,json_encode($params),$id]);
  
  $_SESSION["CheckMessage"]["warning"] = "Импорт успешно создан и поставлен в очередь";
  echo json_encode( ["status"=>true] ); 

}  
?>