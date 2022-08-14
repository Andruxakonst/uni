<?php

$config = require "./config.php";

$route_name = "order";
$visible_footer = true;

$Main = new Main();
$settings = $Main->settings();

if( !$_SESSION["profile"]["id"] ){
	header( "Location: " . _link("auth") );
}

$data["order"] = findOne('uni_clients_orders', 'clients_orders_uniq_id=? and (clients_orders_from_user_id=? or clients_orders_to_user_id=?)', [intval($id_order),$_SESSION["profile"]["id"],$_SESSION["profile"]["id"]]);

if(!$data["order"]){
	$Main->response(404);
}

if($data["order"]["clients_orders_secure"]){

	if(!findOne("uni_secure", "secure_id_order=?", [intval($id_order)])){
		$Main->response(404);
	}

}

$Ads = new Ads();
$Seo = new Seo();
$Geo = new Geo();
$Profile = new Profile();
$CategoryBoard = new CategoryBoard();
$Banners = new Banners();
$ULang = new ULang();

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

update('update uni_clients_orders_messages set clients_orders_messages_status=? where clients_orders_messages_id_order=? and clients_orders_messages_from_id_user!=?', [1, intval($id_order), $_SESSION["profile"]["id"]]);

if( $data["order"]["clients_orders_from_user_id"] == $_SESSION["profile"]["id"] ){

	$data["user"] = findOne("uni_clients", "clients_id=?", [$data["order"]["clients_orders_to_user_id"]]);

}elseif( $data["order"]["clients_orders_to_user_id"] == $_SESSION["profile"]["id"] ){
	
	$data["user"] = findOne("uni_clients", "clients_id=?", [$data["order"]["clients_orders_from_user_id"]]);

}


if($data["order"]["clients_orders_secure"]){

$data["order"] = findOne("uni_secure", "secure_id_order=?", [intval($id_order)]);

$data["ad"] = $Ads->get("ads_id=?", [$data["order"]["secure_id_ad"]]);

$data["order"]["commission"] = $Ads->getSecureCommission( $data["order"]["secure_price"] );

$data["order"]["commission_and_price"] = $Ads->secureTotalAmountPercent( $data["order"]["secure_price"] );

$data["disputes"] = getOne("SELECT * FROM uni_secure_disputes INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_secure_disputes`.secure_disputes_id_user where secure_disputes_id_secure=?", [$data["order"]["secure_id"]]);

echo $Main->tpl("order.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','data','Profile','CategoryBoard','Banners','getCategoryBoard','Ads','ULang' ) );

}else{

	echo $Main->tpl("order_marketplace.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','data','Profile','CategoryBoard','Banners','getCategoryBoard','Ads','ULang' ) );

}

?>