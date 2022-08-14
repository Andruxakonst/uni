<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");

if( !(new Admin())->accessAdmin($_SESSION['cp_control_tpl']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){
    
    if( is_dir( $config["template_path"] ) ){
        update("UPDATE uni_settings SET value=? WHERE name=?", array(0,'status_install_template'));
        deleteFolder( $config["template_path"] );
        echo true;
    }

}
?>