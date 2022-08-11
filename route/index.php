<?php

$config = require "./config.php";

$route_name = "index";
$visible_footer = true;
$visible_cities = true;

$Main = new Main();
$settings = $Main->settings();

$Ads = new Ads();
$Main = new Main();
$Seo = new Seo();
$Geo = new Geo();
$Profile = new Profile();
$CategoryBoard = new CategoryBoard();
$Banners = new Banners();
$Filters = new Filters();
$Blog = new Blog();
$ULang = new ULang();
$Elastic = new Elastic();
$Shop = new Shop();

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

$data["seo_text"] = $Seo->out(array("page" => "index", "field" => "text"));
$data["h1"] = $Seo->out(array("page" => "index", "field" => "h1"));

$data["article_rand"] = $Blog->getAll( ["query"=>"blog_articles_visible=1", "sort"=>"order by rand() limit 3"] );

$geo = $Ads->queryGeo() ? " and " . $Ads->queryGeo() : "";

$param_search = $Elastic->paramAdquery();
$param_search["query"]["bool"]["filter"][]["term"] = $Ads->arrayGeo();
$param_search["query"]["bool"]["filter"][]["term"]["ads_vip"] = 1;

if($settings["index_out_content_method"] == 0){
  $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' order by rand() limit 16", "param_search" => $param_search, "output" => 16 ] );
}else{
  $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' $geo order by rand() limit 16", "param_search" => $param_search, "output" => 16 ] );
}

if($settings["index_out_count_shops"] && $settings["user_shop_status"]){
$data["shops"] = getAll( "select * from uni_clients_shops INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_shops`.clients_shops_id_user where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and clients_status IN(0,1) order by rand() limit " . $settings["index_out_count_shops"] );
}

$data["slider_ad_category"] = $Main->outSlideAdCategory(16);

if($_SESSION["geo"]["alias"]){
  $data["vip_link"] = _link($_SESSION["geo"]["alias"]."/vip");
}else{
  $data["vip_link"] = _link($settings["country_default"]."/vip"); 
}

$data["sliders"] = getAll("select * from uni_sliders where sliders_visible=? order by sliders_sort asc", [1]);

echo $Main->tpl("index.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','data','Profile','CategoryBoard','Banners','getCategoryBoard', 'Ads', 'Blog', 'ULang', 'Shop', 'visible_cities') );

?>