<?php
define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_settings']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

$cache = new Cache();

if(isAjax() == true){

    if(isset($_POST["form"])){ 

        foreach ($_POST["form"] as $name => $nested) {
          
            foreach ($nested as $iso => $nested_value) {

                  $line = []; $data = [];
              
                  if( file_exists( $config["basePath"] . "/lang/$iso/{$name}.php" ) ){
                     $data = require $config["basePath"] . "/lang/$iso/{$name}.php";                    
                  }
                  
                  $dir = $config["basePath"]."/lang/".$iso;
                  if(!is_dir($dir)){
                     @mkdir($dir, $config["create_mode"] );
                  }                         
              
                  foreach ($nested_value as $key => $value) {
                     $data[$key] = $value;
                  }
                  
                  foreach ($data as $key => $value) {
                     if($value) $line[] = '"'.$key.'" => "'.addslashes($value).'"';
                  }
                  
                  if($line){
                  
                    $forming_s = '<?php return ['.implode(",", $line).']; ?>';
                    file_put_contents( $config["basePath"] . "/lang/{$iso}/{$name}.php" , $forming_s);
                  
                  }
              
            }

        }

    }

    $_SESSION["CheckMessage"]["success"] = "Действие успешно выполнено!";     

    $cache->update("cityDefault");   
    $cache->update("uni_category_board");   
    $cache->update("uni_ads_filters");   

}
?>