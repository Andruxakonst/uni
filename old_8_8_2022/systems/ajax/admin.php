<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );

verify_csrf_token();

$Main = new Main();
$Admin = new Admin();
$Ads = new Ads();

if(isAjax() == true){

	if($_POST["action"] == "auth"){

	if($settings["block-time-auth-admin"] <= time()){

		$error = array();

		$email = clear($_POST["email"]);
		     
		if(validateEmail($email) == false){
		    $error[] = "Пожалуйста, укажите корректный E-mail!";
		}

		if(empty($_POST["pass"])){$error[] = "Пожалуйста, укажите пароль!";}         
		     
		if (count($error) == 0) {
		   
		   $get = findOne("uni_admin","email = ?", array($email));
		     
		     if (password_verify($_POST["pass"].$config["private_hash"], $get->pass)) {	
		        
                $_SESSION['cp_auth'][ $config["private_hash"] ] = getOne("select fio,image,role,id from uni_admin where id=?", array( $get->id ));

                $Admin->setPrivileges($get->privileges);
                
                update("update uni_settings set value=? where name=?", array(0,"count-password-attempts"));
                update("update uni_settings set value=? where name=?", array(0,"block-time-auth-admin"));

				echo json_encode( ["status"=>true, "location" => $_SESSION["entry_point"] ? $_SESSION["entry_point"] : $config["urlPath"] . "/" . $config["folder_admin"] . '?route=index' ] );
		        
		     }else{
		     	
		     	if($settings["count-password-attempts"] >= $settings["password-attempts"]){

                   update("update uni_settings set value=? where name=?", array( time() + 900 ,"block-time-auth-admin"));
                   update("update uni_settings set value=? where name=?", array(0,"count-password-attempts"));

		     	}else{
		     	   update("update uni_settings set value=value+1 where name=?", array("count-password-attempts"));
		     	}

				echo json_encode( ["status"=>false, "answer"=>"Не верный логин и(или) пароль!"] );
		     }

		} else {
			echo json_encode( ["status"=>false, "answer"=>implode("<br/>",$error)] );
		} 
      
      }else{
		 echo json_encode( ["status"=>false, "answer"=>'Внимание! Авторизация заблокирована до '.date("H:i:s", $settings["block-time-auth-admin"]) ] );
      }

    }

    if($_POST["action"] == "remind"){
     
     if($settings["block-time-auth-admin"] <= time()){

		$error = array();

		$email = clear($_POST["email"]);
		     
		if(validateEmail($email) == false){
		    $error[] = "Пожалуйста, укажите корректный E-mail!";
		}
           
		if (count($error) == 0) {
		   
		   $get = findOne("uni_admin","email = ? Limit ?", array($email,1));
		     
		     if ($get) {	

		        $new_pass =  generatePass(10);
                $password =  password_hash($new_pass.$config["private_hash"], PASSWORD_DEFAULT);

                update("UPDATE uni_admin SET pass=? WHERE id=?", array($password,$get->id));

	             $param = array("{USER_NAME}"=>$get->fio,
	             	           "{USER_EMAIL}"=>$get->email,
	                           "{USER_PASS}"=>$new_pass,
	                           "{EMAIL_TO}"=>$get->email
	                           );

	             email_notification( array( "variable" => $param, "code" => "ADMIN_REMIND_PASS" ) );

		         echo true;

		     }else{

		     	if($settings["count-password-attempts"] >= $settings["password-attempts"]){
                   update("update uni_settings set value=? where name=?", array( time() + 900 ,"block-time-auth-admin"));
                   update("update uni_settings set value=? where name=?", array(0,"count-password-attempts"));
		     	}else{
		     	   update("update uni_settings set value=value+1 where name=?", array("count-password-attempts"));
		     	}

		         echo "Указанный E-mail не найден!";
		     }

		} else {
		    echo implode("<br/>",$error);
		}

      }else{
      	 echo 'Внимание! Восстановление пароля заблокировано до '.date("H:i:s", $settings["block-time-auth-admin"] );
      }

    }

    if($_POST["action"] == "translate"){
          
          echo translite($_POST["name"]);

    }

    if($_POST["action"] == "notification"){
	    if (isset($_SESSION["CheckMessage"])){
	       echo json_encode($_SESSION["CheckMessage"]);
	       unset($_SESSION["CheckMessage"]);  
	    }else{ echo false; }    	
    }

    if($_POST["action"] == "delete_fast_tab"){
	    unset( $_SESSION["fast_tabs"][$_POST["index"]] );  	
    }

    if($_POST["action"] == "save_menu"){

    	$_POST["menu"] = $_POST["menu"] ? $_POST["menu"] : [];
    	$menu = [];

    	if( count($_POST["menu"]) ){

            foreach ( array_slice($_POST["menu"], 0, 10)  as $key => $value) {
            	if( $value["name"] ){
            	    $menu[$key]["name"] = $value["name"];
            	    $menu[$key]["link"] = $value["link"];
                }
            }

    	}

        update("UPDATE uni_settings SET value=? WHERE name=?", array( json_encode($menu) ,'site_frontend_menu'));
 	
    }



}

?>