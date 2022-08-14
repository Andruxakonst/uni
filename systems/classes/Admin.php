<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class Admin{

	function getPages(){
	    global $settings;
	    $arr = array();
	      if($settings["array_pages"]){
	         $settings["array_pages"] = json_decode($settings["array_pages"],true);
	           foreach($settings["array_pages"] AS $pages=>$path){
	              $arr[$pages] = $path;
	           }
	         return $arr;  
	      }
	}

    function checkPages($page){
	   $array = $this->getPages();
	      if(isset($array[$page])){ 
	          return $array[$page]; 
	      }else{
	          return "/index/index"; 
	      }      
    } 

	function setPrivileges($privileges){ 
	  if($privileges){
	    $exp = explode(",",$privileges);
	      if(count($exp)>0){  
	         foreach($exp AS $value){
	            $_SESSION["cp_".$value] = 1;
	         }
	      }
	  }
	}

	function salesOrders(){

	$Main = new Main();

        $all = getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1")["total"];
        $now = getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1 and date(orders_date) = date(now())")["total"];
        $month = getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1 and YEAR(orders_date) = YEAR(now()) AND MONTH(orders_date) = MONTH(now())")["total"];
        
        return [ "all" => $Main->price($all), "now" => $Main->price($now), "month" => $Main->price($month) ];

	}

	function areaOrders(){

	    $x=0;
	    while ($x++<30){
	       $month[ date('Y-m-d', strtotime("-".$x." day")) ] = date('Y-m-d', strtotime("-".$x." day"));
	    }

	    $month[ date('Y-m-d') ] = date('Y-m-d');

	    ksort($month);

	    foreach ($month as $key => $value) {
	    	$data[] = round( getOne("select sum(orders_price) as total from uni_orders where orders_status_pay='1' and date(orders_date) = '".$value."'")["total"], 2 );
	    	$date[] = date( "d", strtotime($value) );
	    }

	    return [ "data" => json_encode($data),"date" => json_encode($date) ]; 

	}

    function addNotification($code = ""){
         global $config,$settings;

         if($code == "ads"){

	         $sql = findOne("uni_notifications", "code=?", array($code));
	         if(count($sql) == 0){

	           insert("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES(?,?,?,?,?)", array("Публикация объявления",date("Y-m-d H:i:s"),$code,"la la-thumb-tack","?route=board") );

	         }else{
	           update("UPDATE uni_notifications SET count=count+1 WHERE id=?", array($sql->id));
	         }

         }elseif($code == "user"){

	         $sql = findOne("uni_notifications", "code=?", array($code));
	         if(count($sql) == 0){
	           insert("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES(?,?,?,?,?)", array("Регистрация пользователя",date("Y-m-d H:i:s"),$code,"la la-user","?route=clients") );
	         }else{
	           update("UPDATE uni_notifications SET count=count+1 WHERE id=?", array($sql->id));
	         }

         }elseif($code == "order"){

	         $sql = findOne("uni_notifications", "code=?", array($code));
	         if(count($sql) == 0){
	           insert("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES(?,?,?,?,?)", array("Продажа",date("Y-m-d H:i:s"),$code,"la la-money","?route=orders") );
	         }else{
	           update("UPDATE uni_notifications SET count=count+1 WHERE id=?", array($sql->id));
	         }     

         }elseif($code == "complaint"){

	         $sql = findOne("uni_notifications", "code=?", array($code));
	         if(count($sql) == 0){
	           insert("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES(?,?,?,?,?)", array("Жалоба",date("Y-m-d H:i:s"),$code,"la la-exclamation-triangle","?route=complaints") );
	         }else{
	           update("UPDATE uni_notifications SET count=count+1 WHERE id=?", array($sql->id));
	         } 

         }

    }


	function browser($user_agent){
	  if (strpos($user_agent, "Firefox/") !== false) $browser = "Firefox";
	  elseif (strpos($user_agent, "Opera/") !== false || strpos($user_agent, 'OPR/') !== false ) $browser = "Opera";
	  elseif (strpos($user_agent, "YaBrowser/") !== false) $browser = "Yandex";      
	  elseif (strpos($user_agent, "Chrome/") !== false) $browser = "Chrome";
	  elseif (strpos($user_agent, "MSIE/") !== false || strpos($user_agent, 'Trident/') !== false ) $browser = "Explorer";
	  elseif (strpos($user_agent, "Safari/") !== false) $browser = "Safari";
	  else $browser = "Undefined";
	  return $browser;    
	}

	function setMode(){
		global $config;
		update("UPDATE uni_admin SET datetime_view = ? WHERE id=?", array(date("Y-m-d H:i:s"),$_SESSION['cp_auth'][ $config["private_hash"] ]["id"]));
	}

	function manager_filesize($filesize)
	{

	   if($filesize > 1024)
	   {
	       $filesize = ($filesize/1024);
	       if($filesize > 1024)
	       {
	            $filesize = ($filesize/1024);
	           if($filesize > 1024)
	           {
	               $filesize = ($filesize/1024);
	               $filesize = round($filesize, 1);
	               return $filesize." Gb";       
	           }
	           else
	           {
	               $filesize = round($filesize, 1);
	               return $filesize." Mb";   
	           }       
	       }
	       else
	       {
	           $filesize = round($filesize, 1);
	           return $filesize." Kb";   
	       }  
	   }
	   else
	   {
	       $filesize = round($filesize, 1);
	       return $filesize." byte";   
	   }
	}

	function manager_total_size( $dir = "" ){
		global $config;
	    if(is_dir($dir)){
	    	$name = scandir($dir);
	        for($i=2; $i<=(sizeof($name)-1); $i++) {
	           if(is_file($dir.$name[$i]) && $name[$i] != '.'){ 
	            $total += filesize($dir.$name[$i]);
	           }
	        }
	      return get_filesize($total);  
	    }     
	}

	function getFile($dir){
        if(file_exists($dir)){ 

         $fp = @fopen($dir, 'r' );
          if ($fp) {
              $size = @filesize($dir);
              $content = @fread($fp, $size);
              @fclose ($fp); 
          }

          return trim($content);
        }		
	}

	function warningSystems(){
	global $settings,$config;

	   if(!$settings["cron_systems_status"]){
	   	  $data["cron"] = 1;
	   	  $count_warning += 1;
	   }

	   if($data["cron"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }

	   if( $settings["cron_datetime_update"] ){
	   	   $cron_datetime_update = '<br><strong>Последнее выполнение:</strong> ' . date( "d.m.Y H:i:s", strtotime($settings["cron_datetime_update"]) );
	   }

	   $warning .= '
				  <div class="alert-custom '.$alert.'">
				    '.$check.'
				    Необходимо включить cron для выполнения системных функций сайта. Создайте запись в cron журнале вашего хостинга или сервера.
				        <hr> 
				        <strong>Скрипт:</strong> '.$config["urlPath"].'/systems/cron/cron_systems.php?key='.$config["cron_key"].'<br> 
				        <strong>Интервал выполнения:</strong> 1 минута
				        '.$cron_datetime_update.'
                        <hr>
				        <a href="https://unisite.org/doc/kak-dobavit-cron-zapis" ><i class="la la-question-circle" style="font-size: 18px;" ></i> Документация по настройке cron</a>
				  </div>
	   ';
    
        if(!$settings["robots_index_site"]){
            $count_warning += 1;
	    }

	   if(!$settings["robots_index_site"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }

        $warning .= '
			  <div class="alert-custom '.$alert.'" >
			  '.$check.'
			  Сайт отключен от индексации поисковыми системами, после настройки сайта вам необходимо включить индексацию в настройках <a href="?route=settings&tab=robots">robots.txt</a>
			  </div>
		';

        if(!$settings["site_name"] || !$settings["title"]){
	        $data["site_name"] = 1;
	        $count_warning += 1;
        }

	   if($data["site_name"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }
        
        $warning .= '
			  <div class="alert-custom '.$alert.'">
			    '.$check.'
			    Необходимо указать в настройках сайта "Название сайта/проекта" и "Заголовок сайта" эти данные будут отображаться на сайте и в email сообщениях.
			  </div>
        ';

        if(!$settings["sms_service_login"] && !$settings["sms_service_id"]){
	       $data["sms"] = 1;
	       $count_warning += 1;
        }

	   if($data["sms"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }

        $warning .= '
			  <div class="alert-custom '.$alert.'">
			    '.$check.'
			    Необходимо произвести интеграцию с сервисом СМС рассылок. Для этого перейдите в <a href="?route=settings&tab=integrations" >интеграции</a>
			  </div>
        ';

        if(!$settings["map_yandex_key"] && !$settings["map_google_key"]){
	       $data["map"] = 1;
	       $count_warning += 1;
        }

	   if($data["map"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }

        $warning .= '
			  <div class="alert-custom '.$alert.'">
			    '.$check.'
			    Необходимо настроить интеграцию с интерактивной картой. Для этого перейдите в <a href="?route=settings&tab=integrations" >интеграции</a>
			  </div>
        ';

        if(!$settings["payment_variant"]){
	       $data["payment_variant"] = 1;
	       $count_warning += 1;
        }

	   if($data["payment_variant"]){
	   	  $alert = "alert-warning";
	   	  $check = "";
	   }else{
	   	  $alert = "alert-success";
	   	  $check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
	   }

        $warning .= '
			  <div class="alert-custom '.$alert.'">
			   '.$check.'
			   Необходимо настроить платежную систему для приема оплаты с сайта. Для этого перейдите в <a href="?route=settings&tab=payments" >настройку платежных систем</a>
			  </div>
        ';
	    
		if($settings["functionality"]["booking"]){
			if(!$settings["booking_payment_service_name"]){
				$alert = "alert-warning";
				$check = "";
			}else{
				$alert = "alert-success";
				$check = '<span class="alert-status-check" ><i class="la la-check"></i></span>';
			}

			$warning .= '
				<div class="alert-custom '.$alert.'">
				'.$check.'
				Необходимо выбрать платежную систему для приема оплаты онлайн бронирования и аренды. Для этого перейдите в <a href="?route=settings&tab=booking" >настройку бронирования</a>
				</div>
			';
	    }

       return ["html"=>$warning, "count"=>$count_warning];

	}

	function dir_size($dir) {
	   $totalsize=0;

	   if( !is_dir($dir) ) return 0;
	   
	   if ($dirstream = @opendir($dir)) {
	      while (false !== ($filename = readdir($dirstream))) {
	         if ($filename!="." && $filename!=".."){
	            if (is_file($dir."/".$filename)) $totalsize+=filesize($dir."/".$filename);
	            if (is_dir($dir."/".$filename)) $totalsize+=$this->dir_size($dir."/".$filename);
	         }
	      }
	   }
	   closedir($dirstream);
	   return $totalsize;
	}

	function adminRole($id = 0){
		$set = array(1 => "Администратор", 2 => "Менеджер", 3 => "Копирайтер", 4 => "Модератор", 5 => "Дизайнер", 6 => "Программист", 7 => "Сеошник", 8 => "Арбитражник");
        if($id){
        	return $set[$id];
        }else{
        	return $set;
        }
	}

	function accessAdmin( $session = 0 ){
		global $config;

		if(!$_SESSION['cp_auth'][ $config["private_hash"] ]){
		   return false;
		}else{
		   if($session){
		   	  return true;
		   }else{
		   	  return false;
		   }
		}

	}

	function randQuotes(){
		$data = ["Чтобы достичь успеха, перестаньте гнаться за деньгами, гонитесь за мечтой","Просыпаясь утром, спроси себя: «Что я должен сделать?» Вечером, прежде чем заснуть: «Что я сделал?","Заработайте себе репутацию, и она будет работать на вас!","Есть только один способ проделать большую работу — полюбить её!","Для того, чтобы преуспеть, мы первым делом должны верить, что мы можем","Успех не приходит к вам. Это вы идете к нему","Если ты чувствуешь, что сдаешься, вспомни, ради чего ты держался до этого","Если ты не можешь быть первым — будь лучшим. Если ты не можешь быть лучшим — будь первым","Самая великая слава приходит ни к тому, кто никогда не падал, а к тому, кто поднимается как можно выше после каждого своего падения","Не ошибается лишь тот, кто ничего не делает! Не бойтесь ошибаться — бойтесь повторять ошибки!","Если ты собираешься в один прекрасный день создать что-то великое, помни – один прекрасный день – это сегодня"];
		return $data[ mt_rand(0, count($data)-1 ) ];
	}

	function getAllMessagesSupport($notification=false){

        $total = 0;
        $groupBy = [];

        $getAll = getAll("select * from uni_chat_users where chat_users_id_user=?", array(0));

        if(count($getAll)){

           foreach ($getAll as $key => $value) {

              $groupBy[ $value["chat_users_id_hash"] ] = $value["chat_users_id_hash"];

           }

           if( count($groupBy) ){
               foreach ($groupBy as $id_hash) {

               	  if($notification){
               	  	 $total += (int)getOne("select count(*) as total from uni_chat_messages where chat_messages_id_hash=? and chat_messages_status=? and chat_messages_id_user!=? and chat_messages_notification=?",array($id_hash,0,0,0))["total"];
               	  	 update("update uni_chat_messages set chat_messages_notification=? where chat_messages_id_hash=?", [1,$id_hash]);
               	  }else{
               	  	 $total += (int)getOne("select count(*) as total from uni_chat_messages where chat_messages_id_hash=? and chat_messages_status=? and chat_messages_id_user!=?",array($id_hash,0,0))["total"];
               	  }

               }
           }

        }

        return $total;
    }


}

$Admin = new Admin();

?>
