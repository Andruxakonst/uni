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

if(isAjax() == true){

    $ULang = new ULang();

    if(isset($_POST["keys"])){ 
        foreach ($_POST["keys"] as $iso => $array) {
            if( $ULang->edit($array,$iso) ){
                $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";
            }else{
                $_SESSION["CheckMessage"]["warning"] = "Недостаточно прав на запись для файла /lang/{$iso}/main.php";
            }
        }
    } 

    echo true;   

}
?>