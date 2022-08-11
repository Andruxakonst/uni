<?php
$config = require "./config.php";

$route_name = "buy";
$visible_footer = false;

$Main = new Main();
$settings = $Main->settings();

$Ads = new Ads();
$Main = new Main();
$Seo = new Seo();
$Geo = new Geo();
$Banners = new Banners();
$Profile = new Profile();
$ULang = new ULang();

if( !$_SESSION["profile"]["id"] ){
    header( "Location: " . _link("auth") );
}

$data["ad"] = $Ads->get("ads_id=? and ads_status IN(1,4)", [intval($id_ad)]);

if( !$Ads->getStatusSecure($data["ad"]) ){
    $Main->response(404);
}

$data["order"] = findOne("uni_secure", "secure_id_ad=?", [$data["ad"]["ads_id"]]);

if( $data["order"] ){
    if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] || $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){
      header("Location: " . _link("order/".$data["order"]["secure_id_order"]) );
    }
}

if($data["ad"]["ads_auction"]){

    if( $data["ad"]["ads_status"] == 1 ){

        if( $data["ad"]["ads_auction_price_sell"] ){
            $data["ad"]["ads_price"] = $data["ad"]["ads_auction_price_sell"];
        }else{
            $Main->response(404);
        }

    }elseif( $data["ad"]["ads_status"] == 4 ){

        $auction_user_winner = $Ads->getAuctionWinner( $data["ad"]["ads_id"] );

        if( !$auction_user_winner || $_SESSION["profile"]["id"] != $auction_user_winner["ads_auction_id_user"] ){
            $Main->response(404);
        }

    }

}

$data["ad"]["ads_images"] = $Ads->getImages($data["ad"]["ads_images"]);

echo $Main->tpl("buy.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','Banners','Profile',"Ads","data","ULang" ) );

?>