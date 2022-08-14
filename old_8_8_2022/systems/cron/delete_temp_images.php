<?php
defined('unisitecms') or exit();

$dir = $config["basePath"] . "/temp/images/";

$files = scandir($dir);

unset($files[0]);
unset($files[1]);

if( !count($files) ) exit;

foreach ($files as $fileName) {
    
    if( $fileName != ".htaccess" ){
	    $unix_time = filemtime( $dir . "/" . $fileName ) + 3600;
	    if( $unix_time < time() ){
	        unlink($dir . "/" . $fileName);
	    }
    }

}

?>