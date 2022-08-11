<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_board']) && !(new Admin())->accessAdmin($_SESSION['cp_processing_board']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){

 $Ads = new Ads();

 if( is_array($_POST["id"]) ){
 	 $ids = iteratingArray($_POST["id"], "int");
 	 $Ads->delete(["id"=>implode(",", $ids)]);
 }else{
     $Ads->delete(["id"=>intval($_POST["id"])]);
 }


 $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";          
 echo true;

}

?>
