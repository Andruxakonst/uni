<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class Cache{

     function __construct() {
        global $config;

        $dir = $config["basePath"]."/temp/cache";
        if(!is_dir($dir)){ @mkdir($dir, $config["create_mode"] ); }

     }

    function set( $params = [] ){
        global $settings,$config;

        $dir = $config["basePath"]."/temp/cache";
        $name_folder = md5($params["table"]);

        if( !is_dir( $dir . "/" . $name_folder ) ){
             @mkdir( $dir . "/" . $name_folder , $config["create_mode"] );
        }
          
        $key = md5( md5( $params["table"] . $params["key"] ) . $config["private_hash"] ) . ".temp";

        $content["time"] = time() + 3600;
        $content["data"] = $params["data"];

        if( file_put_contents( $dir . "/" . $name_folder . "/" . $key, "<?php return ".var_export($content, true)." ?>" ) ){
            return true;
        }else{
            return false;
        }
  
    }  

    function get( $params = [] ){
        global $settings,$config;

        $dir = $config["basePath"]."/temp/cache";
        $name_folder = md5($params["table"]);

        $key = md5( md5( $params["table"] . $params["key"] ) . $config["private_hash"] ) . ".temp";
        
          if(file_exists( $dir . "/" . $name_folder . "/" . $key )){

              $content = require $dir . "/" . $name_folder . "/" . $key;

              if(time() < $content["time"]){
                  return $content["data"];
              }

              unlink( $dir . "/" . $name_folder . "/" . $key );
              return false;

          }else{
              return false;
          }

    }
 
    function reset(){
      global $config;

      deleteFolder( $config["basePath"]."/temp/cache" );
      deleteFolder( $config["template_path"]."/temp" );

    }

    function update( $table = "" ){
      global $config;

      if($table) deleteFolder( $config["basePath"]."/temp/cache/" . md5($table) );

    }

}



?>