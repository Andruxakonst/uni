<?php 

define('unisitecms', true);
session_start();

$config = require "../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");

$Profile = new Profile();

$param = paymentParams('robokassa');

if($param["test"] == 1){
   $param["pass2"] = $param["test_pass2"];
}

$amount = $_REQUEST["OutSum"];
$id_order = $_REQUEST["InvId"];
$crc = strtoupper($_REQUEST["SignatureValue"]);

$my_crc = strtoupper(md5("$amount:$id_order:".$param["pass2"]));

if ($my_crc != $crc)
{
  echo "bad sign\n";
  exit();
}

$Profile->payCallBack( $id_order );


echo "OK$id_order\n";

 ?>