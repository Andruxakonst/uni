<?php

class ULang{

    public function __construct( $execute = true ) {
        global $config, $settings;

          $dir = $config["basePath"]."/lang";
          if(!is_dir($dir)){ @mkdir($dir, $config["create_mode"] ); }

          $_SESSION["ULang"]["lang_data"] = $this->loadFile();

    }

    public function loadFile( $table = "", $lang = "" ){
       global $config;

       if(!$lang) $lang = getLang();

       $file = $table ? $table : "main";

       if( file_exists( $config["basePath"] . "/lang/{$lang}/{$file}.php" ) ){
          return require $config["basePath"] . "/lang/{$lang}/{$file}.php";
       }else{
          return [];
       }

    }

    public function t( $string = "", $param = [] ){
       global $settings;
       
       if( $param ){
           $data = $this->loadFile( $param["table"] );
           $key = md5( $param["field"] . "_" . $string );
       }else{
           $data = $_SESSION["ULang"]["lang_data"];
           $key = md5( $string );
       }

       if( $data[$key] ){
          return stripcslashes($data[$key]);
       }else{
          if(!$param) $_SESSION["ULang"]["in_data"][md5($string)] = $string;
       }

       return stripcslashes($string);
    }

    public function edit( $in_data = [], $lang = "", $table = "" ){
       global $config;

       $line = [];

       if($lang){

          $data = $this->loadFile($table,$lang);

          if($in_data){
            foreach ($in_data as $key => $value) {
              $data[$key] = $value;
            }
          }
          
           foreach ($data as $key => $value) {
             if($value) $line[] = '"'.$key.'" => "'.addslashes($value).'"';
           }
       
           $forming_s = '<?php return ['.implode(",", $line).']; ?>';

           if( file_put_contents( $config["basePath"] . "/lang/{$lang}/main.php" , $forming_s) ){
              return true;
           }else{
              return false;
           }

       }

    }

    public function __destruct() {
       global $config, $settings;
       
       $line = [];
       $lang = getLang();

       if( $_SESSION["ULang"]["in_data"] ){

          $dir = $config["basePath"]."/lang/{$lang}";
          if(!is_dir($dir)){
             @mkdir($dir, $config["create_mode"] );
          }

          $data = $this->loadFile();
          
          if($data){
            $result = array_merge($data,$_SESSION["ULang"]["in_data"]);
          }else{
            $result = $_SESSION["ULang"]["in_data"];
          }
          
          if($result){
            foreach ($result as $key => $value) {
               if($value) $line[] = '"'.$key.'" => "'.addslashes($value).'"';
            }
          }
       
          $forming_s = '<?php return ['.implode(",", $line).']; ?>';

          file_put_contents( $config["basePath"] . "/lang/{$lang}/main.php" , $forming_s);

       }

       unset($_SESSION["ULang"]["in_data"]);

    }

}

?>