<?php
$config = require "./config.php";
$static_msg = require "./static/msg.php";

$route_name = "profile";
$visible_footer = true;

$Main = new Main();
$settings = $Main->settings();

$CategoryBoard = new CategoryBoard(); 
$Main = new Main();
$Ads = new Ads();
$Seo = new Seo();
$Geo = new Geo();
$Profile = new Profile();
$Banners = new Banners();
$ULang = new ULang();
$Shop = new Shop();

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

$array_tabs = array("ad", "sold", "archive" , "orders", "favorites", "settings", "balance", "history", "reviews", "subscriptions", $settings['user_shop_alias_url_page'], "tariff", "statistics", "scheduler");

$user = $Profile->oneUser(" where clients_id_hash=?" , array( clear($id_user) ) );

if($user["clients_id"] == intval($_SESSION['profile']['id'])){

	$data["advanced"] = true;

    if($action) if( !in_array($action, $array_tabs) ) $Main->response(404);
    
}else{
    
    $data["advanced"] = false;

	  if($action && $action != "reviews") header("Location: " . _link( "user/".$id_user ) );

}

if(!$user){ $Main->response(404); }

if($data["advanced"]){
  if($action == $settings['user_shop_alias_url_page']){

    if($_SESSION["profile"]['shop']){
        header('Location: '.$Shop->linkShop($_SESSION["profile"]['shop']["clients_shops_id_hash"]));
    }

  }elseif($action == 'tariff'){

    if(!$_SESSION["profile"]["tariff"]){
        header('Location: '._link('tariffs'));
    }

  }
}

$data["page_name"] = $Profile->menuPageName($action);
 
$data["ratings"] = $Profile->outRating( $user["clients_id"] );
$data["reviews"] = getAll("select * from uni_clients_reviews INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_reviews`.clients_reviews_from_id_user where clients_reviews_id_user=? order by clients_reviews_id desc", [ $user["clients_id"] ]);
$data["share"] = $Main->share( array( "title" => $static_msg["1"] . " ".$settings["site_name"].". ".$static_msg["2"], "image" => $Profile->userAvatar($user["clients_avatar"]), "url" => _link( "user/".$id_user ) ) );


if( $action == "ad" || !$action ){

$data["ad"] = $Ads->getAll( [ "navigation" => true, "page" => $_GET["page"], "query" => "ads_id_user='".$user["clients_id"]."' and ads_status='1' and ads_period_publication > now()", "sort" => "order by ads_id desc" ] );
$data["sold"] = $Ads->getAll( [ "navigation" => true, "page" => $_GET["page"], "query" => "ads_id_user='".$user["clients_id"]."' and ads_status IN(5,4)", "sort" => "order by ads_id desc" ] );
$data["archive"] = $Ads->getAll( ["navigation" => true, "page" => $_GET["page"], "query" => "ads_id_user='".$user["clients_id"]."' and (ads_status NOT IN(1,5,4) or ads_period_publication < now()) and ads_status!=8", "sort" => "order by ads_id desc" ] );

}

$data["locked"] = findOne( "uni_chat_locked", "chat_locked_user_id=? and chat_locked_user_id_locked=?", array(intval($_SESSION['profile']['id']),$user["clients_id"]) );

if($data["advanced"]){
  
  if( $action == "orders" ){

    $data["orders"]["buy"] = getAll("select * from uni_clients_orders where clients_orders_from_user_id=? order by clients_orders_id desc", [ intval($_SESSION['profile']['id']) ]);
    $data["orders"]["sell"] = getAll("select * from uni_clients_orders where clients_orders_to_user_id=? order by clients_orders_id desc", [ intval($_SESSION['profile']['id']) ]);

  }

  $data["subscriptions_search"] = getAll("select * from uni_ads_subscriptions where ads_subscriptions_email=? or ads_subscriptions_id_user=? order by ads_subscriptions_id desc", [ $user["clients_email"], intval($_SESSION['profile']['id']) ]);

  $data["subscriptions_shops"] = getAll("select * from uni_clients_subscriptions INNER JOIN `uni_clients_shops` ON `uni_clients_shops`.clients_shops_id = `uni_clients_subscriptions`.clients_subscriptions_id_shop where clients_subscriptions_id_user_from=? order by clients_subscriptions_id desc", [ intval($_SESSION['profile']['id']) ]);

  $data["favorites"] = getAll( "select * from uni_favorites INNER JOIN `uni_ads` ON `uni_ads`.ads_id = `uni_favorites`.favorites_id_ad where favorites_from_id_user=? and ads_status!=?", [intval($_SESSION['profile']['id']),8] );

  if($settings["payment_variant"]){
    $data["payments"] = getAll("select * from uni_payments where id IN(".$settings["payment_variant"].")");
  }else{
    $data["payments"] = [];
  }

  $data["notifications_param"] = $Profile->paramNotifications($user["clients_notifications"]);

  $data["shop_count_subscriptions"] = (int)getOne("select count(*) as total from uni_clients_subscriptions where clients_subscriptions_id_user_to=?", [ intval($_SESSION['profile']['id']) ])["total"];

}

$user["clients_score"] = decrypt($user["clients_score"]);
$data["new_messages"] = $Profile->getMessage();

$data["menu_links"] = $Profile->arrayMenu();

$data["ad_list_reviews_seller"] = $Ads->getAll( [ "navigation" => false, "page" => $_GET["page"], "query" => "ads_id_user='".$user["clients_id"]."' and ads_status!='0' and ads_status!='8' ", "sort" => "order by ads_id desc" ] );

$data["ad_list_reviews_buyer"] = $Ads->getAll( [ "navigation" => false, "page" => $_GET["page"], "query" => "ads_id_user='".$_SESSION['profile']['id']."' and ads_status!='0' and ads_status!='8' ", "sort" => "order by ads_id desc" ] );

echo $Main->tpl("profile.tpl", compact( 'Seo','Geo','Main','visible_footer','Ads','route_name','list_services','data','Profile','languages_content','user','list_chat_users','action','list_complaints','settings','getCategoryBoard','CategoryBoard','Banners','ULang','Shop' ) );

?>