<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );
$static_msg = require $config["basePath"] . "/static/msg.php";

verify_csrf_token();

$Ads = new Ads();
$Profile = new Profile();
$Main = new Main();
$Admin = new Admin();
$CategoryBoard = new CategoryBoard();
$Geo = new Geo();
$Filters = new Filters();
$Banners = new Banners();
$Elastic = new Elastic();
$ULang = new ULang();
$Watermark = new Watermark();
$Subscription = new Subscription();
$Shop = new Shop();
$Cache = new Cache();
$Cart = new Cart();

$Profile->checkAuth();

verify_auth(['play_voice','send_chat','load_chat','delete_chat','chat_user_locked']);

if(isAjax() == true){

	if($_POST["action"] == "init"){

		if(!$_SESSION['profile']['id']){ exit; }

		$id_ad = (int)$_POST['id_ad'];

		$getAd = findOne("uni_ads", "ads_id=?", [$id_ad]);

		if($getAd){ 

		   $interlocutor = $Profile->oneUser(" where clients_id=?" , array($getAd['ads_id_user']));
		   if($interlocutor){ 

		       if(findOne("uni_ads", "ads_id=? and (ads_id_user=? or ads_id_user=?)", [$id_ad,intval($_SESSION['profile']['id']),intval($interlocutor["clients_id"])])){

		           $getUserChat = findOne("uni_chat_users", "chat_users_id_ad=? and chat_users_id_user=? and chat_users_id_interlocutor=?", [$id_ad, intval($_SESSION['profile']['id']), $interlocutor["clients_id"] ]);
		           if(!$getUserChat){
		               $data["id_hash"] = md5($id_ad.intval($_SESSION['profile']['id']));
		               insert("INSERT INTO uni_chat_users(chat_users_id_ad,chat_users_id_user,chat_users_id_hash,chat_users_id_interlocutor)VALUES(?,?,?,?)", array($id_ad, intval($_SESSION['profile']['id']), $data["id_hash"], $interlocutor["clients_id"]));
		           }else{
		               $data["id_hash"] =  $getUserChat["chat_users_id_hash"];
		           }

		       }
		       
		   }
		}

		ob_start();
		$Profile->chatUsers($data["id_hash"]);
		$list_chat_users = ob_get_clean();

		ob_start();
		include $config["template_path"] . "/include/chat_body.php";
		echo ob_get_clean();

   }
  
   if($_POST["action"] == "load_chat"){
      
      $id_hash = clear($_POST["id"]);
      echo json_encode( array( "dialog"=> $Profile->chatDialog($id_hash), "count_msg" => $Profile->getMessage()["total"] ) );

   }

   if($_POST["action"] == "delete_chat"){
      
      $id_hash = clear($_POST["id"]);

      update("DELETE FROM uni_chat_users WHERE chat_users_id_hash=? and chat_users_id_user=?", array( $id_hash, intval($_SESSION["profile"]["id"]) ) );

      $get = getOne("select count(*) as total from uni_chat_users where chat_users_id_user=? group by chat_users_id_hash", array(intval($_SESSION['profile']['id'])) );
        
      echo json_encode( array( "dialog"=> $Profile->chatDialog(0), "count_chat_users" => $get["total"] ) );

   }

   if($_POST["action"] == "send_chat"){

        $id_hash = clear($_POST["id"]);
        $text = clear( urldecode($_POST["text"]) );
        $attach = $_POST["attach"] ? array_slice($_POST["attach"],0, 10) : [];
        $voice = clear($_POST["voice"]);
        $duration = (int)$_POST["duration"];

        $getUser = getOne("select * from uni_chat_users where chat_users_id_hash=? and chat_users_id_user=?", array($id_hash,intval($_SESSION["profile"]["id"])) ); 

        $Profile->sendChat( array( "id_ad" => $getUser["chat_users_id_ad"], "id_hash" => $id_hash, "text" => $text, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $getUser["chat_users_id_interlocutor"], "attach" => $attach, 'voice' => $voice, 'duration' => $duration ) );

        echo json_encode( array( "dialog"=> $Profile->chatDialog($id_hash) ) );
        
   }

   if($_POST["action"] == "chat_user_locked"){

     $id_hash = clear($_POST["id"]);

     $getUser = getOne("select * from uni_chat_users where chat_users_id_hash=? and chat_users_id_user=?", array( $id_hash,intval($_SESSION['profile']['id']) ) );
        
     if($getUser){

       $getLocked = findOne("uni_chat_locked", "chat_locked_user_id = ? and chat_locked_user_id_locked = ?", array(intval($_SESSION['profile']['id']),$getUser["chat_users_id_interlocutor"]));
       if($getLocked){
          update("DELETE FROM uni_chat_locked WHERE chat_locked_id=?", array($getLocked->chat_locked_id));
       }else{
          insert("INSERT INTO uni_chat_locked(chat_locked_user_id,chat_locked_user_id_locked)VALUES('".intval($_SESSION['profile']['id'])."','".$getUser["chat_users_id_interlocutor"]."')");
       }

     }

     echo json_encode( array( "dialog"=> $Profile->chatDialog($id_hash) ) );

   }

  if($_POST["action"] == "update_count_message"){

  	  if(!$_SESSION['profile']['id']){ exit(json_encode(["auth" => false])); }

     $id_hash = clear($_POST["id"]);

     if($id_hash){
     	echo json_encode( [ "auth" => true, "all" => $Profile->getMessage()['total'], "active" => $Profile->getMessage($id_hash)['total'], "hash_counts" => $Profile->getMessage()['hash_counts'] ] );
     }else{
     	echo json_encode( [ "auth" => true, "all" => $Profile->getMessage()['total'], "active" => "", "hash_counts" => $Profile->getMessage()['hash_counts'] ] );
     }

  }

  if($_POST["action"] == "save_voice"){

	  	if($_FILES['voice']){
			$name = md5($_FILES['voice']['tmp_name'].time()).'.mp3';
			if (move_uploaded_file($_FILES['voice']['tmp_name'], $config["basePath"] . "/" . $config["media"]["attach"] . "/voice/" . $name)) {
				 $getVoice = file_get_contents($config["basePath"] . "/" . $config["media"]["attach"] . "/voice/" . $name);
				 file_put_contents($config["basePath"] . "/" . $config["media"]["attach"] . "/voice/" . $name, encrypt($getVoice));
			    echo json_encode(['status'=>true, 'name'=>$name]);
			}
		}

  }

  if($_POST["action"] == "play_voice"){

  	   $id_hash = clear($_POST['id_hash']);
  	   $id = (int)$_POST['id'];

  	   if($id_hash && $id){

  	   	$getMessage = findOne('uni_chat_messages', 'chat_messages_id_hash=? and chat_messages_id=?', [$id_hash,$id]);

   	   if($getMessage["chat_messages_attach"]){
   	   	 $attach = json_decode($getMessage["chat_messages_attach"], true);
   	   	 if(file_exists($config["basePath"] . "/" . $config["media"]["attach"] . "/voice/" . $attach['voice'])){
	   	   	 $getVoice = file_get_contents($config["basePath"] . "/" . $config["media"]["attach"] . "/voice/" . $attach['voice']);
	   	   	 echo 'data:audio/mp3;base64,' . base64_encode(decrypt($getVoice));
   	   	 }
   	   }

  	   }

  }

  if($_POST["action"] == "attach_files"){

	    if(count($_FILES) > 0){

	      $count_images_add = 10;
	      $max_file_size = 10;

	      foreach (array_slice($_FILES, 0, $count_images_add) as $key => $value) {

	          $path = $config["basePath"] . "/" . $config["media"]["temp_images"];

	          $extensions = array('jpeg', 'jpg', 'png');
	          $ext = strtolower(pathinfo($value["name"], PATHINFO_EXTENSION));
	          
	          if($value['size'] > $max_file_size*1024*1024){

	            echo false;

	          }else{

	            if (in_array($ext, $extensions))
	            {
	                  
	                  $uid = md5(time().uniqid());
	                  $name = "attach_" . $uid . ".jpg";
	                  
	                  if (move_uploaded_file($value["tmp_name"], $path."/".$name))
	                  {
	                    
	                     rotateImage( $path . "/" . $name );
	                     resize($path . "/" . $name, $path . "/" . $name, 1024, 0);
	                    
	                     ?>

	                       <div class="id<?php echo $uid; ?> attach-files-preview" ><img class="image-autofocus" src="<?php echo $config["urlPath"] . "/" . $config["media"]["temp_images"] . "/" . $name; ?>" /><input type="hidden" name="attach[<?php echo $uid; ?>]" value="<?php echo $name; ?>" /> <span class="chat-dialog-attach-delete" ><i class="las la-trash-alt"></i></span> </div>

	                     <?php

	                  }
	                  
	            }else{

	               echo false;

	            }

	          }

	      }

	    }

  }


}

?>