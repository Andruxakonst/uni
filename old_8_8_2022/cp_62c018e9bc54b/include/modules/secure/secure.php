<?php 
if( !defined('unisitecms') ) exit;

$LINK = '?route=secure';   
$_GET["page"] = empty($_GET["page"]) ? 1 : intval($_GET["page"]);

$Ads = new Ads();
$Profile = new Profile();

if(!empty($_GET["search"])){

  $_GET["search"] = clearSearch($_GET["search"]);

   $query = "where secure_id_order LIKE '%".$_GET["search"]."%' OR ads_title LIKE '%".$_GET["search"]."%'"; 
   $url[] = 'search='.$_GET["search"];
   
}

if(!$settings["functionality"]["secure"]){
   ?>
     <div class="alert alert-warning" role="alert">
       Модуль недоступен. Подключить его можно в разделе <a href="?route=modules" ><strong>модули</strong></a> 
     </div>             
   <?php
}

if( getCount("uni_secure") ){
?>

<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title">Безопасные сделки</h2>
      </div>
   </div>
</div>  

<div class="row" >
  <div class="col-lg-12" >
    
      <form method="get" action="/<?php echo $config["folder_admin"]; ?>" >
        <input type="text" class="form-control" placeholder="Укажите номер заказа или название объявления" style="height: 44px;" value="<?php echo $_GET["search"]; ?>" name="search">
        <input type="hidden" name="route" value="secure" >
      </form>

  </div>
</div>

<div class="row flex-row"  style="margin-top: 25px;" >

   <?php

      $count = getOne("SELECT count(*) as total FROM uni_secure $query")["total"];

      $get = getAll("SELECT * FROM uni_secure INNER JOIN `uni_ads` ON `uni_ads`.ads_id = `uni_secure`.secure_id_ad $query order by secure_id desc ".navigation_offset( array( "count"=>$count, "output"=>$_SESSION["ByShow"], "page"=>$_GET["page"] ) ));     

       if(count($get)){   

          foreach($get AS $value){

          $image = $Ads->getImages($value["ads_images"]);

          $user_seller = findOne("uni_clients", "clients_id=?", [$value["secure_id_user_seller"]]);
          $user_buyer = findOne("uni_clients", "clients_id=?", [$value["secure_id_user_buyer"]]);
           

          ?>

           <div class="col-xl-3 col-md-4 col-sm-6 col-remove">
              <div class="widget-image has-shadow">
                 <div class="contact-card secure-card">  

                      <?php
                      
                        if( $value["secure_status"] == 0 ){
                           echo '<span class="secure-card-label secure-card-label-0" >Ожидается оплата</span>';
                        }elseif( $value["secure_status"] == 1 ){
                           echo '<span class="secure-card-label secure-card-label-1" >В работе</span>';
                        }elseif( $value["secure_status"] == 2 ){
                           echo '<span class="secure-card-label secure-card-label-2" >В работе</span>';
                        }elseif( $value["secure_status"] == 3 ){

                           $getPayment = findOne("uni_secure_payments", "secure_payments_id_order=? and secure_payments_status_pay=?", [$value["secure_id_order"],2]); 
                           if(!$getPayment){
                              echo '<span class="secure-card-label secure-card-label-3" >Сделка завершена</span>';
                           }else{
                              echo '<span class="secure-card-label secure-card-label-4" >Ошибка выплаты</span>';
                           }
                           
                        }elseif( $value["secure_status"] == 4 ){
                           echo '<span class="secure-card-label secure-card-label-0" >Открыт спор</span>';
                        }elseif( $value["secure_status"] == 5 ){

                           echo '<span class="secure-card-label secure-card-label-4" >Сделка отменена</span>';

                        }

                      ?>
                                    
                    <div class="cover-image mx-auto" >
                       
                       <div class="mx-auto img-circle" >
                         <img src="<?php echo Exists($config["media"]["big_image_ads"],$image[0],$config["media"]["no_image"]); ?>" width="100%" />
                       </div>

                    </div>
                    <div class="widget-body">
                       <h4 class="text-center" > <?php echo $value["ads_title"]; ?> </h4>
                       <div class="text-center">
                          <div class="btn-group" style="padding-top: 10px;" role="group">
                             <a href="?route=secure_view&id=<?php echo $value["secure_id"]; ?>" class="btn btn-gradient-01">Подробнее</a>
                          </div>
                       </div>
                       <div class="secure-card-users" >
                        <span>
                          <img title="<?php echo $user_seller["clients_name"]; ?>" src="<?php echo $Profile->userAvatar($user_seller["clients_avatar"]); ?>">
                        </span>
                        <span>
                          <img title="<?php echo $user_buyer["clients_name"]; ?>" src="<?php echo $Profile->userAvatar($user_buyer["clients_avatar"]); ?>">
                        </span>
                       </div>                                      
                    </div>
                 </div>
              </div>
           </div> 
   
         
           <?php                                         
          } 

       }                  
    ?>

 </div>


<ul class="pagination">  
 <?php echo out_navigation( array("count"=>$count, "output"=>$_SESSION["ByShow"], "url"=>$LINK, "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
</ul>

<?php }else{ ?>

  <div class="circle-img-icon" >
     <img src="<?php echo $settings["path_admin_image"]; ?>/admin-secure.png">
     <h3 class="mt10" > <strong>Безопасных сделок пока нет</strong> </h3>
     <p>Как только появятся сделки - они тут отобразятся</p>
  </div>

<?php } ?>

<script type="text/javascript" src="include/modules/secure/script.js"></script>     
