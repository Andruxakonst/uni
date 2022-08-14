<?php
$config = require "./config.php";

$route_name = "cart";
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
$CategoryBoard = new CategoryBoard(); 
$Cart = new Cart(); 
$Shop = new Shop(); 

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

echo $Main->tpl("cart.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','Banners','Profile',"Ads","data","ULang","CategoryBoard","getCategoryBoard","Shop","Cart" ) );

?>