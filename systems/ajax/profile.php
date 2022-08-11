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
$ULang = new ULang();

$Profile->checkAuth();

verify_auth(['user-avatar','user_edit_pass','profile_user_locked','balance_payment','user_edit','user_edit_email','user_edit_phone_send','user_edit_phone_save','user_edit_notifications','user_edit_score','add_review_user','delete_ads_subscriptions','period_ads_subscriptions','delete_shop_subscriptions','order_send_message','order_update_message','activate_services_tariff','delete_services_tariff','autorenewal_services_tariff','scheduler_ad_delete','statistics_load_info_user']);

if(isAjax() == true){

   if($_POST["action"] == "check_auth"){
      
      $error = array();

      $user_login = clear($_POST["user_login"]);
      $user_pass = $_POST["user_pass"];

      if($settings["authorization_method"] == 1){

          $user_phone = formatPhone($_POST["user_login"]);
          $validatePhone = validatePhone($user_phone);

          if(!$validatePhone['status']){
              $error["user_login"] = $validatePhone['error'];
          }

      }elseif($settings["authorization_method"] == 2){
        
          if(!$user_login){
              $error["user_login"] = $ULang->t("Пожалуйста, укажите телефон или электронную почту.");
          }else{
              if( strpos($user_login, "@") !== false ){

                if(validateEmail( $user_login ) == false){
                    $error["user_login"] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
                }else{
                    $user_email = $user_login; 
                }

              }else{

                $user_phone = formatPhone($_POST["user_login"]);
                $validatePhone = validatePhone($user_phone);

                if(!$validatePhone['status']){
                    $error["user_login"] = $validatePhone['error'];
                }

              }         
          }

      }elseif($settings["authorization_method"] == 3){
        
          if(validateEmail( $user_login ) == false){
              $error["user_login"] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
          }else{
              $user_email = $user_login; 
          }

      }

      if( mb_strlen($user_pass, "UTF-8") < 6 || mb_strlen($user_pass, "UTF-8") > 25 ){
          $error["user_pass"] = $ULang->t("Пожалуйста, укажите пароль от 6-ти до 25 символов.");
      }

      if($_SESSION["auth_captcha"]["status"]){
          if(!$_POST["captcha"]){
            $error["captcha"] = $ULang->t("Пожалуйста, укажите код с картинки");
          }elseif($_POST["captcha"] != $_SESSION["captcha"]["auth"]){
            $error["captcha"] = $ULang->t("Неверный код с картинки");
          }
      }

      if( count( $error ) == 0 ){
         
         if($user_email){
            $getUser = findOne("uni_clients","clients_email = ?", array( $user_email ));
         }elseif($user_phone){
            $getUser = findOne("uni_clients","clients_phone = ?", array( $user_phone ));
         }
         
         if($getUser){

               $_SESSION["auth_captcha"]["count"]++;
               if($_SESSION["auth_captcha"]["count"] >= 3){ $_SESSION["auth_captcha"]["status"] = true; }

               if($getUser->clients_status == 2 || $getUser->clients_status == 3){
                     
                 echo json_encode( array( "status" => false, "status_user" => $getUser->clients_status, "captcha"=>$_SESSION["auth_captcha"]["status"] ) );

               }else{
               
                 if (password_verify($user_pass.$config["private_hash"], $getUser->clients_pass)) {  
                    
                      $_SESSION['profile']['id'] = $getUser->clients_id;

                      echo json_encode( array( "status"=>true, "location" => _link( "user/".$getUser["clients_id_hash"] ) ) );
                    
                 }else{

                      echo json_encode( array( "status" => false, "answer" => array("user_pass"=>$ULang->t("Неверный логин и(или) пароль!")), "captcha"=>$_SESSION["auth_captcha"]["status"] ) );

                 }

               }

         }else{
             echo json_encode( array( "status" => false, "answer" => array("user_pass"=>$ULang->t("Неверный логин и(или) пароль!")), "captcha"=>$_SESSION["auth_captcha"]["status"] ) );    
         }

      }else{

         echo json_encode(array("status" => false, "answer" => $error));

      }

   }

   if($_POST["action"] == "registration"){
      
      $error = array();

      $user_login = clear($_POST["user_login"]);

      if($settings["registration_method"] == 1){

          $user_phone = formatPhone($_POST["user_login"]);
          $validatePhone = validatePhone($user_phone);

          if(!$validatePhone['status']){
              $error["user_login"] = $validatePhone['error'];
          }

      }elseif($settings["registration_method"] == 2){
        
          if(!$user_login){
              $error["user_login"] = $ULang->t("Пожалуйста, укажите телефон или электронную почту.");
          }else{
              if( strpos($user_login, "@") !== false ){

                if(validateEmail( $user_login ) == false){
                    $error["user_login"] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
                }else{
                    $user_email = $user_login; 
                }

              }else{

                $user_phone = formatPhone($_POST["user_login"]);
                $validatePhone = validatePhone($user_phone);

                if(!$validatePhone['status']){
                    $error["user_login"] = $validatePhone['error'];
                }

              }         
          }

      }elseif($settings["registration_method"] == 3){
        
          if(validateEmail( $user_login ) == false){
              $error["user_login"] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
          }else{
              $user_email = $user_login; 
          }

      }

      if($_SESSION["auth_captcha"]["status"]){
          if(!$_POST["captcha"]){
            $error["captcha"] = $ULang->t("Пожалуйста, укажите код с картинки");
          }elseif($_POST["captcha"] != $_SESSION["captcha"]["auth"]){
            $error["captcha"] = $ULang->t("Неверный код с картинки");
          }
      }

      if( count( $error ) == 0 ){
         
         if($user_email){
            $getUser = findOne("uni_clients","clients_email = ?", array( $user_email ));
         }elseif($user_phone){
            $getUser = findOne("uni_clients","clients_phone = ?", array( $user_phone ));
         }
         
         $_SESSION["auth_captcha"]["count"]++;
         if($_SESSION["auth_captcha"]["count"] >= 3){ $_SESSION["auth_captcha"]["status"] = true; }

         if($getUser){
               echo json_encode( array( "status" => false, "answer" => array("user_login"=>$ULang->t("Указанный логин уже используется на сайте!")), "captcha"=>$_SESSION["auth_captcha"]["status"] ) );
         }else{
                
               if( $_SESSION["verify_login"]["time"] ){
                   if( $_SESSION["verify_login"]["time"] < time() ){
                       unset($_SESSION["verify_login"]["count"]);
                       unset($_SESSION["verify_login"]["time"]);
                   }
               }

               if( intval($_SESSION["verify_login"]["count"]) < 5 ){
                   
                   if($user_email){

                     $_SESSION["verify_login"][$user_login]["code"] = mt_rand(1000,9999);

                     $data = array("{USER_EMAIL}"=>$user_email,
                                   "{CODE}"=>$_SESSION["verify_login"][$user_login]["code"],
                                   "{EMAIL_TO}"=>$user_email
                                   );

                     email_notification( array( "variable" => $data, "code" => "SEND_EMAIL_CODE" ) );
                     echo json_encode(array("status"=>true, 'confirmation'=>true, 'confirmation_title' => $ULang->t("Укажите код из email сообщения")));
                     
                     $_SESSION["verify_login"]["count"]++;

                   }elseif($user_phone){

                     if($settings["confirmation_phone"]){
                        $_SESSION["verify_login"][$user_login]["code"] = smsVerificationCode($user_phone);

                        if($settings["sms_service_method_send"] == 'call'){ 
                          $confirmation_title = $ULang->t("Укажите 4 последние цифры номера"); 
                        }else{ $confirmation_title = $ULang->t("Укажите код из смс"); }

                        echo json_encode(array("status"=>true, 'confirmation'=>true, 'confirmation_title' => $confirmation_title));
                        $_SESSION["verify_login"]["count"]++;                        
                     }else{
                        echo json_encode(array("status"=>true, 'confirmation'=>false));
                     }

                   }
                   
               }else{
                   
                   if(!$_SESSION["verify_login"]["time"]) $_SESSION["verify_login"]["time"] = time() + 300;

                   echo json_encode( array( "status"=>false, "answer" => array( "user_login" => $ULang->t("Достигнут лимит отправки сообщений. Попробуйте чуть позже") ) ) );

               }

         }

      }else{
         echo json_encode(array("status" => false, "answer" => $error));
      }

   }

   if($_POST["action"] == "verify_login"){

      $error = [];

      $user_login = clear( $_POST["user_login"] );
      $code_login = (int)$_POST["user_code_login"];

      if($_SESSION["auth_captcha"]["status"]){
          if(!$_POST["captcha"]){
            $error['captcha'] = $ULang->t("Пожалуйста, укажите код с картинки");
          }elseif($_POST["captcha"] != $_SESSION["captcha"]["auth"]){
            $error['captcha'] = $ULang->t("Неверный код с картинки");
          }
      }

      if(!$_SESSION["verify_login"][$user_login]["code"] || $_SESSION["verify_login"][$user_login]["code"] != $code_login){
          $error['user_code_login'] = $ULang->t("Неверный код");
      }

      $_SESSION["auth_captcha"]["count"]++;
      if($_SESSION["auth_captcha"]["count"] >= 3){ $_SESSION["auth_captcha"]["status"] = true; }

      if(count($error) == 0){

         echo json_encode( array( "status"=>true ) );

      }else{

         echo json_encode( array( "status"=>false, "answer" => $error, "captcha" => $_SESSION["auth_captcha"]["status"] ) );

      }

      
   }

   if($_POST["action"] == "reg_finish"){

      $user_login = clear( $_POST["user_login"] );
      $user_name = clear( $_POST["user_name"] );
      $user_code_login = (int)$_POST["user_code_login"];
      $user_pass = clear( $_POST["user_pass"] );

      if($settings["registration_method"] == 1){

      $user_phone = formatPhone($_POST["user_login"]);
      $validatePhone = validatePhone($user_phone);
    
      if(!$validatePhone['status']){
          exit(json_encode(array("status"=>false, "reload" => true)));
      }

      }elseif($settings["registration_method"] == 2){
        
          if(!$user_login){
              exit(json_encode(array("status"=>false, "reload" => true)));
          }else{
              if( strpos($user_login, "@") !== false ){

                if(validateEmail( $user_login ) == false){
                    exit(json_encode(array("status"=>false, "reload" => true)));
                }else{
                    $user_email = $user_login; 
                }

              }else{

                $user_phone = formatPhone($_POST["user_login"]);
                $validatePhone = validatePhone($user_phone);
    
                if(!$validatePhone['status']){
                    exit(json_encode(array("status"=>false, "reload" => true)));
                }

              }         
          }

      }elseif($settings["registration_method"] == 3){
        
          if(validateEmail( $user_login ) == false){
              exit(json_encode(array("status"=>false, "reload" => true)));
          }else{
              $user_email = $user_login; 
          }

      }

      if(!$_SESSION["verify_login"][$user_login]["code"] || $_SESSION["verify_login"][$user_login]["code"] != $user_code_login){
          if($user_email){
             exit(json_encode(array("status"=>false, "reload" => true)));
          }else{
             if($settings["confirmation_phone"]){
                exit(json_encode(array("status"=>false, "reload" => true))); 
             }             
          }
      }

      if( mb_strlen($user_pass, "UTF-8") < 6 || mb_strlen($user_pass, "UTF-8") > 25 ){
         $error['user_pass'] = $ULang->t("Пожалуйста, укажите пароль от 6-ти до 25 символов.");
      }

      if(!$user_name){
         $error['user_name'] = $ULang->t("Пожалуйста, укажите Ваше имя");
      }

      if( count($error) == 0 ){

         $result = $Profile->auth_reg(array("method"=>$settings["registration_method"],"email"=>$user_email,"phone"=>$user_phone,"name"=>$user_name, "activation" => 1, "pass" => $user_pass));

         echo json_encode( array( "status"=>$result["status"],"answer" => $result["answer"], "reg" => 1, "location" => _link( "user/".$result["data"]["clients_id_hash"] ) ) );

         unset($_SESSION["verify_login"]);

      }else{

         echo json_encode(array("status"=>false, "answer" => $error));

      }

      
   }

   if($_POST["action"] == "forgot"){

      $error = array();

      $user_login = clear($_POST["login"]);
           
      if(!$user_login){
          $error['user_recovery_login'] = $ULang->t("Пожалуйста, укажите телефон или электронную почту.");
      }else{
          if( strpos($user_login, "@") !== false ){

            if(validateEmail($user_login) == false){
                $error['user_recovery_login'] = $ULang->t("Пожалуйста, укажите корректный e-mail адрес.");
            }else{
                $user_email = $user_login; 
            }

          }else{

            $user_phone = formatPhone($user_login);
            $validatePhone = validatePhone($user_phone);

            if(!$validatePhone['status']){
                $error['user_recovery_login'] = $validatePhone['error'];
            }

          }         
      }

      if($_SESSION["auth_captcha"]["status"]){
          if(!$_POST["captcha"]){
            $error['captcha'] = $ULang->t("Пожалуйста, укажите код с картинки");
          }elseif($_POST["captcha"] != $_SESSION["captcha"]["auth"]){
            $error['captcha'] = $ULang->t("Неверный код с картинки");
          }
      }

      $_SESSION["auth_captcha"]["count"]++;
      if($_SESSION["auth_captcha"]["count"] >= 3){ $_SESSION["auth_captcha"]["status"] = true; }

      if ( count($error) == 0 ) {
         
         if($user_email){
           $getUser = findOne("uni_clients","clients_email = ?", array($user_email));
         }elseif($user_phone){
           $getUser = findOne("uni_clients","clients_phone = ?", array($user_phone));
         }
           
           if ($getUser) { 

               $pass =  generatePass(10);
               $password_hash =  password_hash($pass.$config["private_hash"], PASSWORD_DEFAULT);

               update("UPDATE uni_clients SET clients_pass=? WHERE clients_id=?", [$password_hash,$getUser->clients_id]);

               if($user_email){

                 $data = array("{USER_NAME}"=>$getUser->clients_name,
                               "{USER_EMAIL}"=>$getUser->clients_email,
                               "{USER_PASS}"=>$pass,
                               "{UNSUBSCRIBE}"=>"",
                               "{EMAIL_TO}"=>$getUser->clients_email
                               );

                 email_notification( array( "variable" => $data, "code" => "AUTH_FORGOT" ) );

                 echo json_encode(array("status"=>true, "answer"=>$ULang->t("Пароль успешно выслан на Ваш e-mail.")));

                 unset($_SESSION["captcha"]["auth"]);

               }elseif($user_phone){

                 sms($user_phone,$pass, 'sms');

                 echo json_encode(array("status"=>true, "answer"=>$ULang->t("Пароль успешно выслан на Ваш номер телефона.") ));

               }


           }else{
               echo json_encode(array("status"=>false, "answer"=> ['user_recovery_login'=>$ULang->t("Пользователь не найден!")], "captcha" => $_SESSION["auth_captcha"]["status"]));
           }

      } else {
          echo json_encode(array("status"=>false, "answer"=>$error, "captcha" => $_SESSION["auth_captcha"]["status"]));
      }


   }


   if($_POST["action"] == "user-avatar"){

        $error = array();
      
        $result = $Main->uploadedImage( ["files"=>$_FILES["image"], "path"=>$config["media"]["avatar"], "name"=>$_SESSION['profile']['id']] );

        if($result["error"]){
            echo json_encode( ["error"=>implode("\n", $result["error"])] );
        }else{
            update("UPDATE uni_clients SET clients_avatar=? WHERE clients_id=?", array($result["name"],$_SESSION['profile']['id'])); 
            echo json_encode(array("img"=>Exists($config["media"]["avatar"],$result["name"],$config["media"]["no_avatar"]).'?'.mt_rand(100, 1000)));            
        }

   }


   if($_POST["action"] == "favorite"){

       if(!$_SESSION['profile']['id']) exit(json_encode(array( "auth"=>0 )));

       $id_ad = intval($_POST["id_ad"]);

       $findAd = findOne("uni_ads", "ads_id=?", array($id_ad));
       
       if($findAd){

             $find = findOne("uni_favorites", "favorites_id_ad=? and favorites_from_id_user=?", array($id_ad,$_SESSION['profile']['id']));
             if($find){

                update("DELETE FROM uni_favorites WHERE favorites_id=?", array($find->favorites_id));
                unset($_SESSION['profile']["favorite"][$id_ad]);
                echo json_encode( array( "auth"=>1, "html" => $ULang->t("Добавить в избранное"), "status" => 0 ) );

             }else{
                
                insert("INSERT INTO uni_favorites(favorites_id_ad,favorites_from_id_user,favorites_to_id_user,favorites_date)VALUES(?,?,?,?)", [$id_ad,$_SESSION['profile']['id'],$findAd['ads_id_user'],date('Y-m-d H:i:s')]);
                $_SESSION['profile']["favorite"][$id_ad] = $id_ad;
                $Profile->sendChat( array("id_ad" => $id_ad, "action" => 1, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $findAd["ads_id_user"] ) );

                $Main->addActionStatistics(['ad_id'=>$id_ad,'from_user_id'=>$_SESSION['profile']['id'],'to_user_id'=>$findAd["ads_id_user"]],"favorite");

                echo json_encode( array( "auth"=>1, "html" => $ULang->t("Удалить из избранного"), "status" => 1 ) );

             }

       }

  }

  if($_POST["action"] == "profile_user_locked"){

     $id_user = (int)$_POST["id"];

     $getLocked = findOne("uni_chat_locked", "chat_locked_user_id = ? and chat_locked_user_id_locked = ?", array(intval($_SESSION['profile']['id']),$id_user));
     if($getLocked){
        update("DELETE FROM uni_chat_locked WHERE chat_locked_id=?", array($getLocked->chat_locked_id));
     }else{
        insert("INSERT INTO uni_chat_locked(chat_locked_user_id,chat_locked_user_id_locked)VALUES('".intval($_SESSION['profile']['id'])."','".$id_user."')");
     }

     echo json_encode( array( "status"=> true ) );

  }

  if($_POST["action"] == "balance_payment"){
    
    $error = [];

    $getUser = findOne("uni_clients", "clients_id=?", [$_SESSION['profile']['id']]);

    $amount = round($_POST["amount"],2) ? round($_POST["amount"],2) : round($_POST["change_amount"],2);

    if(!$_POST["payment"]){
       $error[] = $ULang->t("Пожалуйста, выберите способ оплаты");
    }

    if(!$amount){
       $error[] = $ULang->t("Пожалуйста, укажите сумму оплаты");
    }else{

        if( $amount < round($settings["min_deposit_balance"], 2) ){
           $error[] = $ULang->t("Минимальная сумма пополнения") . " " . $Main->price($settings["min_deposit_balance"]);
        }elseif( $amount > round($settings["max_deposit_balance"], 2) ){
           $error[] = $ULang->t("Максимальная сумма пополнения") . " " . $Main->price($settings["max_deposit_balance"]);
        }

    }

    if( count($error) == 0 ){

     $answer = $Profile->payMethod( $_POST["payment"], array( "amount" => $amount, "name" => $getUser["clients_name"], "email" => $getUser["clients_email"], "phone" => $getUser["clients_phone"], "id_order" => $config["key_rand"], "id_user" => $_SESSION['profile']['id'], "action" => "balance", "title" => $static_msg["19"] . " - " . $settings["site_name"] ) );

      echo json_encode( array( "status" => true, "redirect" => $answer ) );

    }else{

      echo json_encode( array( "status" => false, "answer" => implode("\n", $error) ) );

    }

  
  }

  if($_POST["action"] == "user_edit"){

         $error = [];
         $status = ["user", "company"];

         if( $_POST["status"] == "user" ) $_POST["name_company"] = "";

         if( !in_array($_POST["status"], $status) ){
            $error["status"] = $ULang->t("Пожалуйста, укажите статус");
         }else{
            if(!$_POST["name_company"] && $_POST["status"] == "company"){
               $error["name_company"] = $ULang->t("Пожалуйста, укажите название компании");
            }
         }

         if( !$_POST["user_name"] ){
            $error["user_name"] = $ULang->t("Пожалуйста, укажите имя");
         }

         if( !translite($_POST["id_hash"]) ){
            $error["id_hash"] = $ULang->t("Пожалуйста, укажите короткое имя");
         }else{
            if( findOne("uni_clients", "clients_id_hash=? and clients_id!=?", [translite($_POST["id_hash"]),$_SESSION["profile"]["id"]]) ){
               $error["id_hash"] = $ULang->t("Указанное имя уже используется");
            }
         }

         if(count($error) == 0){

            insert("update uni_clients set clients_name=?,clients_surname=?,clients_type_person=?,clients_name_company=?,clients_city_id=?,clients_id_hash=?,clients_comments=?,clients_secure=?,clients_view_phone=? where clients_id=?", [clear($_POST["user_name"]), clear($_POST["user_surname"]), $_POST["status"], clear($_POST["name_company"]), intval($_POST["city_id"]), translite($_POST["id_hash"]),intval($_POST["comments"]),intval($_POST["secure"]),intval($_POST["view_phone"]), $_SESSION["profile"]["id"] ]);

            echo json_encode( ["status"=>true, "answer"=>$ULang->t("Данные успешно сохранены"), "location"=>_link("user/".translite($_POST["id_hash"])."/settings") ] );

         }else{
            echo json_encode( ["status"=>false, "answer"=>$error] );
         }

  }

  if($_POST["action"] == "user_edit_pass"){
     
       $error = [];

       $getUser = findOne("uni_clients", "clients_id=?", [$_SESSION["profile"]["id"]]);

       if( !$_POST["user_current_pass"] ){ $error[] = $ULang->t("Пожалуйста, укажите текущий пароль"); }else{

          if (!password_verify($_POST["user_current_pass"].$config["private_hash"], $getUser["clients_pass"])) {
             $error[] = $ULang->t("Неверный текущий пароль");
          }

       }

       if( mb_strlen($_POST["user_new_pass"], "UTF-8") < 6 || mb_strlen($_POST["user_new_pass"], "UTF-8") > 25 ){
          $error[] = $ULang->t("Пожалуйста, укажите новый пароль от 6-ти до 25 символов.");
       }

       $password_hash =  password_hash($_POST["user_new_pass"].$config["private_hash"], PASSWORD_DEFAULT);

       if(count($error) == 0){

          update("update uni_clients set clients_pass=? where clients_id=?", [ $password_hash, $_SESSION["profile"]["id"] ]);

          echo json_encode( ["status"=>true] );

       }else{
          echo json_encode( ["status"=>false, "answer"=>implode("\n", $error)] );
       }

  }

  if($_POST["action"] == "user_edit_email"){

        $error = [];

        if(validateEmail($_POST["user_email"]) == false){
           $error[] = $ULang->t("Пожалуйста, укажите корректный e-mail");
        }else{
           if( findOne("uni_clients", "clients_email=?", [ clear($_POST["user_email"]) ]) ){
              $error[] = $ULang->t("Указанный e-mail уже используется в системе");
           }
        }

        if( count($error) == 0 ){

           $getUser = findOne("uni_clients", "clients_id=?", [$_SESSION["profile"]["id"]]);

           $hash = hash('sha256', $getUser["clients_id"].$config["private_hash"]);

           $getHash = findOne("uni_clients_hash_email","clients_hash_email_email=?",[clear($_POST["user_email"])]);
           if($getHash){
               update("delete from uni_clients_hash_email where clients_hash_email_id=?", [$getHash["clients_hash_email_id"]]);
           }

           insert("INSERT INTO uni_clients_hash_email(clients_hash_email_id_user,clients_hash_email_email,clients_hash_email_hash)VALUES('".$getUser["clients_id"]."','".clear($_POST["user_email"])."','".$hash."')");
           
           $data = array("{USER_EMAIL}"=>$_POST["user_email"],
                         "{ACTIVATION_LINK}"=>_link("user/".$getUser["clients_id_hash"]."/settings")."?activation_hash=$hash",
                         "{EMAIL_TO}"=>$_POST["user_email"]
                         );

           email_notification( array( "variable" => $data, "code" => "ACTIVATION_EMAIL" ) );

           echo json_encode( ["status"=>true, "answer"=>$ULang->t("Мы вам отправили письмо для подтверждения почты") ] );

        }else{
           echo json_encode( ["status"=>false, "answer"=>implode("\n", $error)] );
        }

  }

  if($_POST["action"] == "user_edit_phone_send"){
    
      $phone = formatPhone($_POST["phone"]);
      $validatePhone = validatePhone($phone);

      if($validatePhone['status']){

         $_SESSION["verify_sms"][$phone]["code"] = smsVerificationCode($phone);

         echo json_encode(["status"=>true]);

      }else{
         echo json_encode(["status"=>false, "answer"=>$validatePhone['error']]);
      }

  }

  if($_POST["action"] == "user_edit_phone_save"){
    
    $phone = formatPhone($_POST["phone"]);
    $code = $_POST["code"];

    $validatePhone = validatePhone($phone);
    
    if($validatePhone['status']){

        if( $settings["confirmation_phone"] ){

            if($_SESSION["verify_sms"][$phone]["code"] == $code && $code){
               update("update uni_clients set clients_phone=? where clients_id=?", [$phone,$_SESSION["profile"]["id"]]);
               echo json_encode( ["status"=>true] );
            }else{
               echo json_encode( ["status"=>false, "answer"=>$ULang->t("Неверный код") ] );
            }

        }else{

           update("update uni_clients set clients_phone=? where clients_id=?", [$phone,$_SESSION["profile"]["id"]]);
           echo json_encode( ["status"=>true] );

        }
        
    }else{
      echo json_encode(["status"=>false, "answer"=>$validatePhone['error']]);
    }

  }

  if($_POST["action"] == "user_edit_notifications"){
    
    update("update uni_clients set clients_notifications=? where clients_id=?", [json_encode($_POST["notifications"]),$_SESSION["profile"]["id"]]);

  }

  if($_POST["action"] == "user_edit_score"){
    
    $user_score_type = clear($_POST['user_score_type']);

    if($user_score_type != 'wallet' && $user_score_type != 'card') exit(json_encode( ["status"=>false, "answer"=>$ULang->t("Пожалуйста, выберите тип счета") ] ));

    if($_POST["user_score"]) $user_score = encrypt($_POST["user_score"]);
    update("update uni_clients set clients_score=?,clients_score_type=? where clients_id=?", [$user_score,$user_score_type,$_SESSION["profile"]["id"]]);
    echo json_encode( ["status"=>true] );

  }

  if($_POST["action"] == "add_review_user"){
    
    $error = [];

    $id_ad = (int)$_POST["id_ad"];
    $status_result = (int)$_POST["status_result"];
    $id_user = (int)$_POST["id_user"];
    $attach = $_POST["attach"] ? array_slice($_POST["attach"],0, 10) : [];

    if( !intval($_POST["rating"]) ){ $error[] = $ULang->t("Пожалуйста, поставьте оценку"); }
    if( !$_POST["text"] ){ $error[] = $ULang->t("Пожалуйста, напишите отзыв"); }
    if( !$id_user ){ $error[] = $ULang->t("Пожалуйста, выберите ваш статус"); }

    $getAd = findOne("uni_ads", "ads_id=?", [ $id_ad ]);

    if( !$getAd ){ $error[] = $ULang->t("Товар не найден!"); }

    if( count($error) == 0 ){

           $status_publication_review = findOne("uni_clients_reviews", "clients_reviews_from_id_user=? and clients_reviews_id_user=?", [ intval($_SESSION['profile']['id']), $id_user ]);

           if(!$status_publication_review){

              insert("INSERT INTO uni_clients_reviews(clients_reviews_id_user,clients_reviews_text,clients_reviews_from_id_user,clients_reviews_rating,clients_reviews_id_ad,clients_reviews_status_result,clients_reviews_files,clients_reviews_date)VALUES(?,?,?,?,?,?,?,?)", [ $id_user,clear($_POST["text"]),$_SESSION["profile"]["id"],intval($_POST["rating"]),$id_ad,$status_result,implode(",",$attach), date("Y-m-d H:i:s") ]);

              $Profile->sendChat( array("id_ad" => $id_ad, "action" => 4, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $id_user ) );
              
              if( count($attach) ){

                  foreach ($attach as $name) {
                      @copy( $config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $name , $config["basePath"] . "/" . $config["media"]["users"] . "/" . $name );
                  }
                  
              }

              echo json_encode( ["status"=>true] );

              unset($_SESSION['csrf_token'][$_POST['csrf_token']]);

           }else{
              echo json_encode( ["status"=>false, "answer"=> $ULang->t("Вы уже оставляли отзыв для данного товара!") ] );
           }

    }else{
       echo json_encode( ["status"=>false, "answer"=> implode("\n", $error) ] );
    }

  }

  if($_POST["action"] == "load_reviews_attach_files"){


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
                  
                  $uid = uniqid();
                  $name = "attach_" . $uid . ".jpg";
                  
                  if (move_uploaded_file($value["tmp_name"], $path."/".$name))
                  {
                    
                     rotateImage( $path . "/" . $name );
                     resize($path . "/" . $name, $path . "/" . $name, 1024, 0);
                    
                     ?>

                       <div class="id<?php echo $uid; ?> attach-files-preview" ><img class="image-autofocus" src="<?php echo $config["urlPath"] . "/" . $config["media"]["temp_images"] . "/" . $name; ?>" /><input type="hidden" name="attach[<?php echo $uid; ?>]" value="<?php echo $name; ?>" /> <span class="attach-files-delete" ><i class="las la-trash-alt"></i></span> </div>

                     <?php

                  }
                  
            }else{

               echo false;

            }

          }

      }

    }

  
  }

  if($_POST["action"] == "delete_review"){

     if( $_SESSION['cp_auth'][ $config["private_hash"] ] && $_SESSION['cp_control_board'] ){

         $getReview = findOne("uni_clients_reviews", "clients_reviews_id=?", [intval($_POST["id"])]);

         update("delete from uni_clients_reviews where clients_reviews_id=?", [intval($_POST["id"])]);

     }else{

         $getReview = findOne("uni_clients_reviews", "clients_reviews_id=? and clients_reviews_from_id_user=?", [intval($_POST["id"]),intval($_SESSION["profile"]["id"])] );

         update("delete from uni_clients_reviews where clients_reviews_id=? and clients_reviews_from_id_user=?", [intval($_POST["id"]),intval($_SESSION["profile"]["id"])]);

     }

     if($getReview["clients_reviews_files"]){
        $files = explode(",", $getReview["clients_reviews_files"]);
        if($files){
           foreach ($files as $name) {
               @unlink( $config["basePath"] . "/" . $config["media"]["users"] . "/" . $name );
           }
        }
     }

  }

  if($_POST["action"] == "delete_ads_subscriptions"){

     $getUser = findOne("uni_clients", "clients_id=?", [intval($_SESSION['profile']['id'])]);

     if(!$getUser) exit;

     update("delete from uni_ads_subscriptions where (ads_subscriptions_id_user=? or ads_subscriptions_email=?) and ads_subscriptions_id=?", [intval($_SESSION["profile"]["id"]),$getUser["clients_email"],intval($_POST["id"])]);

  }

  if($_POST["action"] == "period_ads_subscriptions"){

     $getUser = findOne("uni_clients", "clients_id=?", [ intval($_SESSION['profile']['id']) ] );

     if(!$getUser) exit;

     update("update uni_ads_subscriptions set ads_subscriptions_period=?,ads_subscriptions_date_update=? where (ads_subscriptions_id_user=? or ads_subscriptions_email=?) and ads_subscriptions_id=?", [intval($_POST["period"]),date("Y-m-d H:i:s"),intval($_SESSION["profile"]["id"]),$getUser["clients_email"],intval($_POST["id"])]);

  }

  if($_POST["action"] == "delete_shop_subscriptions"){

     update("delete from uni_clients_subscriptions where clients_subscriptions_id_user_from=? and clients_subscriptions_id=?", [intval($_SESSION["profile"]["id"]),intval($_POST["id"])]);

  }

  if($_POST["action"] == "order_send_message"){

        $error = [];

        $order_id = (int)$_POST["order_id"];
        $message = clear($_POST["message"]);
        $attach = $_POST["attach"] ? array_slice($_POST["attach"],0, 10) : [];

        if( !$message && !$attach ){ exit; }

        $getOrder = findOne("uni_clients_orders", "clients_orders_uniq_id=?", [ $order_id ]);

        if( !$getOrder ){ $error[] = $ULang->t("Заказ не определен!"); }

        if($_SESSION['profile']['id'] == $getOrder['clients_orders_from_user_id']){
            $clients_orders_messages_to_id_user = $getOrder['clients_orders_to_user_id'];
        }elseif($_SESSION['profile']['id'] == $getOrder['clients_orders_to_user_id']){
            $clients_orders_messages_to_id_user = $getOrder['clients_orders_from_user_id'];
        }

        if( count($error) == 0 ){

              insert("INSERT INTO uni_clients_orders_messages(clients_orders_messages_from_id_user,clients_orders_messages_to_id_user,clients_orders_messages_message,clients_orders_messages_id_order,clients_orders_messages_date,clients_orders_messages_files)VALUES(?,?,?,?,?,?)", [ $_SESSION['profile']['id'], $clients_orders_messages_to_id_user, encrypt($message), $order_id , date("Y-m-d H:i:s"), implode(",",$attach) ]);

              if( count($attach) ){

                  foreach ($attach as $name) {
                      @copy( $config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $name , $config["basePath"] . "/" . $config["media"]["users"] . "/" . $name );
                  }
                  
              }

              echo json_encode( ["status"=>true, 'messages' => $Profile->getMessagesOrder($order_id) ] );

        }else{
           echo json_encode( ["status"=>false, "answer"=> implode("\n", $error) ] );
        }


  }

  if($_POST["action"] == "order_update_message"){

        $order_id = (int)$_POST["order_id"];
        $getOrderMessages = getAll("select * from uni_clients_orders_messages where clients_orders_messages_status=? and clients_orders_messages_id_order=? and clients_orders_messages_to_id_user=?", [0,$order_id,$_SESSION['profile']['id']]);

        if(count($getOrderMessages)){
            foreach ($getOrderMessages as $key => $value) {
                update('update uni_clients_orders_messages set clients_orders_messages_status=? where clients_orders_messages_id=?', [1, $value['clients_orders_messages_id']]);
            }
            echo json_encode( ["status"=>true, 'messages' => $Profile->getMessagesOrder($order_id) ] );
        }else{
            echo json_encode( ["status"=>false] );
        }

  }

  if($_POST["action"] == "change_services_tariff"){

      $tariff_id = (int)$_POST['tariff_id'];
      $time_now = time();
      $sidebar = true;

      $getTariff = $Profile->getTariff($tariff_id);

      if(!$getTariff){ exit; }

      $price_tariff = $getTariff['tariff']['services_tariffs_new_price'] ?: $getTariff['tariff']['services_tariffs_price'];

      if($_SESSION["profile"]["id"]){

        $getTariffOrder = findOne('uni_services_tariffs_orders', 'services_tariffs_orders_id_user=?', [$_SESSION["profile"]["id"]]);

        if($getTariffOrder){

            if(strtotime($getTariffOrder['services_tariffs_orders_date_completion']) > $time_now){
                
                if($getTariff['tariff']['services_tariffs_id'] != $getTariffOrder['services_tariffs_orders_id_tariff']){

                    if($price_tariff > $getTariffOrder['services_tariffs_orders_price']){

                        $price_new = $Profile->calcPriceTariff($getTariff,$getTariffOrder);
                        $total = $price_new;
                        $button = $ULang->t('Доплатить').' '.$Main->price($total);

                        exit(json_encode(["status"=>true, 'total' => $Main->price($total), "button" => $button, 'price_tariff' => $Main->price($price_tariff),'sidebar' => $sidebar]));
                    }

                }else{

                    exit(json_encode(["status" => true, 'total' => 0, "button" => "", 'price_tariff' => $ULang->t('Подключен'),'sidebar' => false]));

                }

            }

        }

      }

      $total = $price_tariff;
      $button = $total ? $ULang->t('Оплатить').' '.$Main->price($total) : $ULang->t('Подключить');

      echo json_encode(["status"=>true, 'total' => $Main->price($total), "button" => $button, 'price_tariff' => $price_tariff ? $Main->price($price_tariff) : $ULang->t('Бесплатно'),'sidebar' => $sidebar]);

  }

  if($_POST["action"] == "activate_services_tariff"){

      $tariff_id = (int)$_POST['tariff_id'];
      $time_now = time();
      $price_tariff_current = 0;
      $add_tariff = true;

      $getTariff = $Profile->getTariff($tariff_id);

      if(!$getTariff){ exit; }

      if($getTariff['tariff']['services_tariffs_days']){
         $date_completion = date("Y-m-d H:i:s", strtotime("+{$getTariff['tariff']['services_tariffs_days']} days", $time_now));
      }else{
         $date_completion = null;
      }

      $getTariffOrder = findOne('uni_services_tariffs_orders', 'services_tariffs_orders_id_user=?', [$_SESSION["profile"]["id"]]);

      $price_tariff = $getTariff['tariff']['services_tariffs_new_price'] ?: $getTariff['tariff']['services_tariffs_price'];

      if(strtotime($getTariffOrder['services_tariffs_orders_date_completion']) > $time_now && $getTariffOrder){

          if($getTariff['tariff']['services_tariffs_id'] != $getTariffOrder['services_tariffs_orders_id_tariff']){
            
              if($price_tariff > $getTariffOrder['services_tariffs_orders_price']){
                 $price_tariff_current = $Profile->calcPriceTariff($getTariff,$getTariffOrder);
              }else{
                 exit(json_encode(["status"=>false, "answer" => $ULang->t("Перейти на тариф ниже можно только по истечению существующего тарифа!")]));
              }

          }else{
              $add_tariff = false;
          }

      }else{

          if($getTariff['tariff']['services_tariffs_onetime']){
              $getOnetime = findOne('uni_services_tariffs_onetime', 'services_tariffs_onetime_user_id=? and services_tariffs_onetime_tariff_id=?', [$_SESSION["profile"]["id"],$tariff_id]);
              if($getOnetime){
                    exit(json_encode(["status"=>false, "answer" => $ULang->t("Данный тариф можно подключить только 1 раз!")]));
              }        
          }

          $price_tariff_current = $price_tariff;

      }

      $total = $price_tariff_current;
      
      if($total){
          if($_SESSION["profile"]["data"]["clients_balance"] >= $total){

            if($price_tariff_current){
                $title = "Подключение тарифа - {$getTariff['tariff']['services_tariffs_name']}";
                $Main->addOrder( ["price"=>$price_tariff_current,"title"=>$title,"id_user"=>$_SESSION["profile"]["id"],"status_pay"=>1, "user_name" => $_SESSION["profile"]["data"]["clients_name"], "id_hash_user" => $_SESSION["profile"]["data"]["clients_id_hash"], "action_name" => "services_tariff"] );
                $Profile->actionBalance(array("id_user"=>$_SESSION['profile']['id'],"summa"=>$price_tariff_current,"title"=>$title),"-");
            }

            if($getTariff['tariff']['services_tariffs_bonus']){

                $getBonus = findOne('uni_services_tariffs_bonus', 'services_tariffs_bonus_user_id=? and services_tariffs_bonus_tariff_id=?', [$_SESSION["profile"]["id"],$tariff_id]);
                if(!$getBonus){
                    $title = "Бонус за подключение тарифа - {$getTariff['tariff']['services_tariffs_name']}";
                    $Main->addOrder( ["price"=>$getTariff['tariff']['services_tariffs_bonus'],"title"=>$title,"id_user"=>$_SESSION["profile"]["id"],"status_pay"=>1, "user_name" => $_SESSION["profile"]["data"]["clients_name"], "id_hash_user" => $_SESSION["profile"]["data"]["clients_id_hash"], "action_name" => "services_tariff_bonus"] );
                    $Profile->actionBalance(array("id_user"=>$_SESSION['profile']['id'],"summa"=>$getTariff['tariff']['services_tariffs_bonus'],"title"=>$title),"+");
                    insert("INSERT INTO uni_services_tariffs_bonus(services_tariffs_bonus_user_id,services_tariffs_bonus_tariff_id)VALUES(?,?)",[$_SESSION["profile"]["id"],$tariff_id]);
                }

            }

          }else{
            
            exit(json_encode(["status"=>false, "balance" => $Main->price($_SESSION["profile"]["data"]["clients_balance"])]));

          }
      }

      if($add_tariff){
          if($getTariffOrder['services_tariffs_orders_id']) update('delete from uni_services_tariffs_orders where services_tariffs_orders_id=?', [$getTariffOrder['services_tariffs_orders_id']]);

          insert("INSERT INTO uni_services_tariffs_orders(services_tariffs_orders_id_tariff,services_tariffs_orders_days,services_tariffs_orders_date_activation,services_tariffs_orders_id_user,services_tariffs_orders_date_completion,services_tariffs_orders_price)VALUES(?,?,?,?,?,?)",[$tariff_id,$getTariff['tariff']['services_tariffs_days'],date('Y-m-d H:i:s',$time_now),$_SESSION["profile"]["id"],$date_completion,$price_tariff]);

          if($getTariff['services']['shop']){
              if(!$_SESSION["profile"]['shop']){
                 insert("INSERT INTO uni_clients_shops(clients_shops_id_user,clients_shops_id_hash,clients_shops_time_validity,clients_shops_title)VALUES(?,?,?,?)", [$_SESSION["profile"]["id"],md5($_SESSION["profile"]["id"]),$date_completion, $Profile->name($_SESSION["profile"]["data"])]);
              }else{
                 update("update uni_clients_shops set clients_shops_time_validity=?,clients_shops_status=? where clients_shops_id=?", [$date_completion,1, $_SESSION["profile"]['shop']["clients_shops_id"]]);
              }
          }else{
              if($_SESSION["profile"]['shop']) update("update uni_clients_shops set clients_shops_status=? where clients_shops_id=?", [0, $_SESSION["profile"]['shop']["clients_shops_id"]]);
          }

          if($getTariff['tariff']['services_tariffs_onetime']){
             insert("INSERT INTO uni_services_tariffs_onetime(services_tariffs_onetime_user_id,services_tariffs_onetime_tariff_id)VALUES(?,?)",[$_SESSION["profile"]["id"],$tariff_id]);
          }
          update('update uni_clients set clients_tariff_id=? where clients_id=?', [$tariff_id,$_SESSION["profile"]["id"]]);
      }

      echo json_encode(["status"=>true, 'redirect' =>_link("user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/tariff")]);

  }

  if($_POST["action"] == "delete_services_tariff"){

      update('delete from uni_services_tariffs_orders where services_tariffs_orders_id_user=?', [$_SESSION["profile"]["id"]]);
      update('update uni_clients set clients_tariff_id=? where clients_id=?', [0,$_SESSION["profile"]["id"]]);
      if($_SESSION["profile"]['shop']) update("update uni_clients_shops set clients_shops_status=? where clients_shops_id=?", [0, $_SESSION["profile"]['shop']["clients_shops_id"]]);

  }

  if($_POST["action"] == "autorenewal_services_tariff"){

      update('update uni_clients set clients_tariff_autorenewal=? where clients_id=?', [intval($_POST['status']),$_SESSION["profile"]["id"]]);

  }

  if($_POST["action"] == "scheduler_ad_delete"){

      $id = (int)$_POST['id'];
      update('update uni_ads set ads_auto_renewal=? where ads_id=? and ads_id_user=?', [0,$id,$_SESSION["profile"]["id"]]);

  }

  if($_POST["action"] == "statistics_load_info_user"){

      $id = (int)$_POST['id'];
      
      ?>
      <div class="table-responsive">

           <?php   
               
               $get = getAll('select * from uni_action_statistics where action_statistics_from_user_id=? and action_statistics_to_user_id=?', [$id,$_SESSION["profile"]["id"]]);

               if($get){   

               ?>
                  <table class="table table-borderless mt15">
                  <thead>
                     <tr>
                      <th><?php echo $ULang->t("Объявление"); ?></th>
                      <th><?php echo $ULang->t("Действие"); ?></th>
                     </tr>
                  </thead>
                  <tbody class="sort-container" >                     
                  <?php 
                  foreach($get AS $value){
                      $getAd = findOne("uni_ads", "ads_id=?", [$value['action_statistics_ad_id']]);
                      if($getAd){
                      ?>
                       <tr>
                           <td><?php echo $getAd['ads_title']; ?></td>
                           <td>
                               <?php
                                    if($value['action_statistics_action'] == 'favorite'){
                                        echo $ULang->t('Добавил в избранное');
                                    }elseif($value['action_statistics_action'] == 'show_phone'){
                                        echo $ULang->t('Просмотрел телефон');
                                    }elseif($value['action_statistics_action'] == 'ad_sell'){
                                        echo $ULang->t('Купил');
                                    }elseif($value['action_statistics_action'] == 'add_to_cart'){
                                        echo $ULang->t('Добавил в корзину');
                                    }
                               ?>
                           </td>                      
                       </tr> 
                      <?php 
                      }                                        
                  } 
                  ?>
                  </tbody>
                  </table>
                  <?php               
               }                  
            ?>

      </div>
      <?php

  }



}

?>