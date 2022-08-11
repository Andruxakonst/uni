<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_settings']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

$Profile = new Profile();

if(isAjax() == true){

   $answer = $Profile->payMethod( $_POST["payment"], array( "amount" => 100, "name" => $settings["site_name"], "email" => $settings["email_alert"], "phone" => $settings["phone_alert"], "id_order" => $config["key_rand"] , "id_user" => 0, "action" => "test", "title" => "Тестовая оплата №".$config["key_rand"] ) ); 

   echo json_encode($answer);

}     
?>
