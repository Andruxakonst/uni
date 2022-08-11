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

  $id = intval($_POST["id"]);
  $status = intval($_POST["status"]);

  update("update uni_city set city_default=? where city_id=?", array($status,$id));

  $cache->update("cityDefault");

  $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";
  echo true;

}
?>