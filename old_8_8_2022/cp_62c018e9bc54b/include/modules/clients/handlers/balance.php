<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_clients']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){

$Main = new Main();
$Profile = new Profile();

$id = (int)$_POST["id"];

$error = array();

if(empty($_POST['summa'])){$error[] = 'Укажите сумму пополнения!';}


if (count($error) == 0) {

    $getUser = findOne("uni_clients","clients_id=?", array($id));

    if($getUser){

        $Profile->actionBalance(array("id_user"=>$id,"summa"=>$_POST['summa'],"title"=>"Пополнение баланса","id_order"=>$config["key_rand"],"email" => $getUser->clients_email,"name" => $getUser->clients_name, "note" => $_POST['title']),"+");

        $_SESSION["CheckMessage"]["success"] = "Баланс на сумму ".$Main->price($_POST['summa'])." успешно пополнен"; 

        echo true;

    }

} else {
    $_SESSION["CheckMessage"]["warning"] = implode("<br/>",$error);
}

}
?>