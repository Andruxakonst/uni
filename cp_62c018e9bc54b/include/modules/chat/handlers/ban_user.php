<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_chat']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

$Profile = new Profile();

$idUser = (int)$_POST['id'];

if(!$idUser) exit;

$getLocked = findOne("uni_chat_locked", "chat_locked_user_id = ? and chat_locked_user_id_locked = ?", array(0,$idUser));

if($getLocked){
  update("DELETE FROM uni_chat_locked WHERE chat_locked_id=?", array($getLocked->chat_locked_id));
}else{
  insert("INSERT INTO uni_chat_locked(chat_locked_user_id,chat_locked_user_id_locked)VALUES(?,?)",[0,$idUser]);
}

$_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";          
echo true;

?>
 

