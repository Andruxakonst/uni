<?php
include_once("{$config["basePath"]}/systems/classes/Logger.php");
include_once("{$config["basePath"]}/systems/classes/ErrorHandler.php");

(new ErrorHandler)->register();

include_once("{$config["basePath"]}/systems/classes/ULang.php");
include_once("{$config["basePath"]}/systems/libs/query.php");
include_once("{$config["basePath"]}/systems/libs/fn.php");
include_once("{$config["basePath"]}/systems/libs/mail.php");
include_once("{$config["basePath"]}/systems/libs/resize.php");
include_once("{$config["basePath"]}/systems/libs/Watermark.php");
include_once("{$config["basePath"]}/systems/libs/Mobile_Detect.php");
include_once("{$config["basePath"]}/systems/libs/rest.inc.php");
include_once("{$config["basePath"]}/systems/classes/Cache.php");
include_once("{$config["basePath"]}/systems/classes/Elastic.php");
include_once("{$config["basePath"]}/systems/classes/Main.php");
include_once("{$config["basePath"]}/systems/classes/Router.php");
include_once("{$config["basePath"]}/systems/classes/Admin.php");
include_once("{$config["basePath"]}/systems/classes/Geo.php");
include_once("{$config["basePath"]}/systems/classes/Ads.php");
include_once("{$config["basePath"]}/systems/classes/Blog.php");
include_once("{$config["basePath"]}/systems/classes/Banners.php");
include_once("{$config["basePath"]}/systems/classes/CategoryBoard.php");
include_once("{$config["basePath"]}/systems/classes/Filters.php");
include_once("{$config["basePath"]}/systems/classes/Shop.php");
include_once("{$config["basePath"]}/systems/classes/Profile.php");
include_once("{$config["basePath"]}/systems/classes/Seo.php");
include_once("{$config["basePath"]}/systems/classes/Subscription.php");
include_once("{$config["basePath"]}/systems/classes/Cart.php");
include_once("{$config["basePath"]}/systems/libs/SxGeo/SxGeo.php");
include_once("{$config["basePath"]}/systems/libs/Slugify.php");

$settings = (new Main())->settings();

(new Main())->setTimeZone();

$_SERVER["REMOTE_ADDR"] = getRealIp();

?>