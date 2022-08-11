<?php
defined('unisitecms') or exit();

if( !$settings["secure_payment_service_name"] ) exit;

$param = paymentParams( $settings["secure_payment_service_name"] );
$payment = findOne("uni_payments","code=?", array( $settings["secure_payment_service_name"] ));

function actionVarPay( $value = [] ){
	global $config,$settings,$param,$payment,$static_msg;
    
    $Main = new Main();

    if(!$value["clients_score"]){
        update("update uni_secure_payments set secure_payments_errors=?, secure_payments_status_pay=? where secure_payments_id=?", [ $static_msg["51"], 2, $value["payments_id"] ]);
        return false;
    }

	if( $settings["secure_payment_service_name"] == "liqpay" ){

		include("{$config["basePath"]}/systems/payment/liqpay/LiqPay.php");

	   $score = decrypt($value["clients_score"]);

		$liqpay = new LiqPay(trim($param["public_key"]), trim($param["private_key"]));
		$result = $liqpay->api("request", array(
		'action'         => 'p2pcredit',
		'version'        => '3',
		'amount'         => round($value["secure_price"],2),
		'currency'       => $settings["currency_main"]["code"],
		'description'    => $value["secure_desc"],
		'order_id'       => $value["secure_id_order"],
		'receiver_card'  => $score,
		'receiver_last_name'  => $value["user_name"]
		));
        
        $result = json_decode( json_encode($result) , true);

		if ( $result["result"] == "ok" && $result["status"] == "success" )
		{
			update("update uni_secure_payments set secure_payments_status_pay=?,secure_payments_errors=? where secure_payments_id=?", [ 1, "", $value["payments_id"] ]);

			$profit = calcPercent( $value["amount"], $payment["secure_percent_service"] );

            if($profit && $value["amount"]){
               $Main->addOrder( ["id_ad"=>$value["id_ad"],"price"=>$profit,"title"=>$static_msg["52"],"id_user"=>$value["id_user"],"status_pay"=>1, "user_name" => $value["user_name"], "id_hash_user" => $value["id_hash_user"], "action_name" => "secure"] );
            }
		}
		else
		{
			update("update uni_secure_payments set secure_payments_errors=?,secure_payments_status_pay=? where secure_payments_id=?", [ $result["err_description"], 2 , $value["payments_id"] ]);
		}      

	}elseif( $settings["secure_payment_service_name"] == "yoomoney" ){

		include("{$config["basePath"]}/systems/payment/yoomoney/sendRequest.php");

	   $score = decrypt($value["clients_score"]);

	   $options =  array(
             'client_id'=>$param["client_id"],
             'pattern_id'=>'p2p',
             'to'=>trim($score),
             'amount_due'=>round($value["secure_price"],2),
             'message'=>$value["secure_desc"],
             'comment'=>$value["secure_desc"],
             'label'=>$value["secure_id_order"],
      );

      $data = sendRequest($options, '/api/request-payment', $param["access_token"]);
      $result = json_decode( $data->body , true);

		if ( $result['status'] == "success" )
		{
			update("update uni_secure_payments set secure_payments_status_pay=?,secure_payments_errors=? where secure_payments_id=?", [ 1, "", $value["payments_id"] ]);

			$profit = calcPercent( $value["amount"], $payment["secure_percent_service"] );

            if($profit && $value["amount"]){
               $Main->addOrder( ["id_ad"=>$value["id_ad"],"price"=>$profit,"title"=>$static_msg["52"],"id_user"=>$value["id_user"],"status_pay"=>1, "user_name" => $value["user_name"], "id_hash_user" => $value["id_hash_user"], "action_name" => "secure"] );
            }
		}
		else
		{
			update("update uni_secure_payments set secure_payments_errors=?,secure_payments_status_pay=? where secure_payments_id=?", [ $result['error_description'], 2 , $value["payments_id"] ]);
		}      

	}



}


    
$getSecurePayments = getAll("select * from uni_secure_payments where secure_payments_status_pay=?", [0]);

if( count($getSecurePayments) ){

   foreach ($getSecurePayments as $key => $value) {

   	   $secure = findOne("uni_secure", "secure_id_order=?", [$value["secure_payments_id_order"]]);

   	   if($secure){
		
			$user_buyer = findOne("uni_clients", "clients_id=?", [$secure["secure_id_user_buyer"]]);
			$user_seller = findOne("uni_clients", "clients_id=?", [$secure["secure_id_user_seller"]]);

			$disputes = findOne("uni_secure_disputes", "secure_disputes_id_secure=?", [$secure["secure_id"]]);

			if($disputes){
				if($disputes["secure_disputes_status"] == 0){

					actionVarPay( [ "clients_score"=>$user_seller["clients_score"],"secure_price"=>$value["secure_payments_amount"] - calcPercent( $value["secure_payments_amount"], $payment["secure_percent_service"] ),"secure_id"=>$secure["secure_id"],"secure_id_order"=>$secure["secure_id_order"],"payments_id"=>$value["secure_payments_id"], "amount" => $value["secure_payments_amount"], "id_ad" => $secure["secure_id_ad"], "id_user" => $user_seller["clients_id"], "user_name" => $user_seller["clients_name"], "id_hash_user" => $user_seller["clients_id_hash"], "secure_desc" => $static_msg["53"] ] );

				}elseif($disputes["secure_disputes_status"] == 1){

					actionVarPay( [ "clients_score"=>$user_buyer["clients_score"],"secure_price"=>$value["secure_payments_amount"],"secure_id"=>$secure["secure_id"],"secure_id_order"=>$secure["secure_id_order"],"payments_id"=>$value["secure_payments_id"], "secure_desc" => $static_msg["54"] ] );

				}elseif($disputes["secure_disputes_status"] == 2){

					$value["secure_payments_amount"] = $value["secure_payments_amount"] / 2;
					
					actionVarPay( [ "clients_score"=>$user_seller["clients_score"],"secure_price"=>$value["secure_payments_amount"] - calcPercent( $value["secure_payments_amount"], $payment["secure_percent_service"] ),"secure_id"=>$secure["secure_id"],"secure_id_order"=>$secure["secure_id_order"],"payments_id"=>$value["secure_payments_id"], "amount" => $value["secure_payments_amount"], "id_ad" => $secure["secure_id_ad"], "id_user" => $user_seller["clients_id"], "user_name" => $user_seller["clients_name"], "id_hash_user" => $user_seller["clients_id_hash"], "secure_desc" => $static_msg["53"] ] );

					actionVarPay( [ "clients_score"=>$user_buyer["clients_score"],"secure_price"=>$value["secure_payments_amount"],"secure_id"=>$secure["secure_id"],"secure_id_order"=>$secure["secure_id_order"],"payments_id"=>$value["secure_payments_id"], "secure_desc" => $static_msg["55"] ] );

				}
			}else{

				actionVarPay( [ "clients_score"=>$user_seller["clients_score"],"secure_price"=>$value["secure_payments_amount"] - calcPercent( $value["secure_payments_amount"], $payment["secure_percent_service"] ),"secure_id"=>$secure["secure_id"],"secure_id_order"=>$secure["secure_id_order"],"payments_id"=>$value["secure_payments_id"], "amount" => $value["secure_payments_amount"], "id_ad" => $secure["secure_id_ad"], "id_user" => $user_seller["clients_id"], "user_name" => $user_seller["clients_name"], "id_hash_user" => $user_seller["clients_id_hash"], "secure_desc" => $static_msg["53"] ] );

			}

	    }

   }

}

?>