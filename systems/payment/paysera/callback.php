<?php 

define('unisitecms', true);
session_start();

$config = require "../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");

require "WebToPay.php";

$Profile = new Profile();

$param = paymentParams('paysera');

try {
        $response = WebToPay::checkResponse($_GET, array(
            'projectid'     => $param["id_shop"],
            'sign_password' => $param["private_key"],
        ));
 
        $Profile->payCallBack( $response['orderid'] );
 
        echo 'OK';
} catch (Exception $e) {
        echo get_class($e) . ': ' . $e->getMessage();
}

?>