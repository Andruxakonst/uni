<?php 

define('unisitecms', true);
session_start();

require 'lib/autoload.php'; 

$config = require "../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");

$Profile = new Profile();

$param = paymentParams('yookassa');

$source = file_get_contents('php://input');
$requestBody = json_decode($source, true);

use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;
use YooKassa\Model\PaymentStatus;

try {
  $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
    ? new NotificationSucceeded($requestBody)
    : new NotificationWaitingForCapture($requestBody);
} catch (Exception $e) {
    
}

$payment = $notification->getObject();

if($payment->getStatus() === PaymentStatus::SUCCEEDED) {
    
    $Profile->payCallBack( $payment->metadata->order_id );

    header("HTTP/1.0 200 OK");

}

?>