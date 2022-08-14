<?php

$config = require "./config.php";

$route_name = "map";
$visible_footer = false;

$Main = new Main();
$settings = $Main->settings();

$Ads = new Ads();
$Filters = new Filters();
$Seo = new Seo();
$Geo = new Geo();
$Profile = new Profile();
$CategoryBoard = new CategoryBoard();
$Banners = new Banners();
$ULang = new ULang();
$Shop = new Shop();

if($_SESSION["geo"]["action"] == "modal"){
    
	if($alias_city != $_SESSION["geo"]["alias"]){
		$_SESSION["geo"]["action"] = "uri";
		$vars = trim( explode("?", $_SERVER['REQUEST_URI'])[1] , "/");
		$vars = $vars ? "?" . $vars : "";
		header( "location: ". _link( "map/" . $_SESSION["geo"]["alias"] . $vars ) );
	}

}else{

    if(!$alias_city && $_SESSION["geo"]["alias"]){
		$_SESSION["geo"]["action"] = "uri";
		$vars = trim( explode("?", $_SERVER['REQUEST_URI'])[1] , "/");
		$vars = $vars ? "?" . $vars : "";
		header( "location: ". _link( "map/" . $_SESSION["geo"]["alias"] . $vars ) );    	
    }

}

$getCategoryBoard = $CategoryBoard->getCategories("where category_board_visible=1");

$data["category"] = $getCategoryBoard["category_board_id"][ $_GET["id_c"] ];
$data["param_filter"] = $_GET;

$result = $Filters->queryFilter($_GET, ["navigation"=>false, "map"=>true]);

if($result["count"]){
	foreach ($result["all"] as $key => $value) {

		$service = $Ads->adServices($value["ads_id"]);
	    
	    ob_start();
	    include $config["template_path"] . "/include/map_ad_grid.php";
	    $offers = ob_get_clean();

	    if($settings["map_vendor"] == "yandex"){

			$data["balloonContentBody"][] = '

			  {
			    id: "'.$value["ads_id"].'",
			    link: "'.$Ads->alias($value).'",
			    balloonContentHeader: `'.str_replace("\\", "-", $value["ads_title"]).'`,
			    hintContent: `'.str_replace("\\", "-", $value["ads_title"]).'`,
                balloonContentBody: `
                  <div class="ballon-point">
                     '.addslashes($offers).'
                  </div>
                `			    
			  }

			';

			$data["points"][] = '['.$value["ads_latitude"].', '.$value["ads_longitude"].']';

	    }elseif($settings["map_vendor"] == "google"){

			$data["balloonContentBody"][] = '

	            new google.maps.Marker({
	                position: new google.maps.LatLng('.$value["ads_latitude"].','.$value["ads_longitude"].'),
	                map: map,
	                title: `'.$value["ads_title"].'`,
	                link: "'.$Ads->alias($value).'",
	                id: "'.$value["ads_id"].'",
	                content: `'.addslashes($offers).'`
	            })

	        ';

	    }elseif($settings["map_vendor"] == "openstreetmap"){

			$data["balloonContentBody"][] = '
			    ['.$value["ads_longitude"].','.$value["ads_latitude"].',`'.addslashes($offers).'`, '.$value["ads_id"].']
	        ';

	    }


	}
}

$data["filters"] = $Filters->load_filters_catalog( $_GET["id_c"] , $data["param_filter"], "filters_modal" );

echo $Main->tpl("map.tpl", compact( 'Seo','Geo','Main','visible_footer','route_name','settings','config','data','Profile','CategoryBoard','Banners','getCategoryBoard','ULang','Ads','Filters','Shop' ) );

?>