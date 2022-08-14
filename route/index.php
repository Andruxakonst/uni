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
$Cache = new Cache();

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

$data["seo_text"] = $Seo->out(array("page" => "index", "field" => "text"));

$data["article_rand"] = $Blog->getAll( ["query"=>"blog_articles_visible=1", "sort"=>"order by rand() limit 3"] );

echo $Main->tpl("index.tpl",compact('Seo','Geo','Main','visible_footer','route_name','settings','config','data','Profile','CategoryBoard','Banners','getCategoryBoard', 'Ads', 'Blog', 'ULang', 'Shop', 'visible_cities'));

?>