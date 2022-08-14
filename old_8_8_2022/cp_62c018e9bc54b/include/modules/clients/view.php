<?php 
if( !defined('unisitecms') ) exit;

$_GET["page"] = empty($_GET["page"]) ? 1 : intval($_GET["page"]);

$url[] = "route=client_view&id=".$id;

$LINK = "?".implode("&",$url);

$Profile = new Profile();
$Main = new Main();
$Geo = new Geo();
$Ads = new Ads();
$Shop = new Shop();

$getUser = findOne("uni_clients", "clients_id=?", array($id));

if(!$getUser){ exit; }

$data["ads"] = $Ads->getAll( array("navigation"=>true,"page"=>$_GET["page"],"output"=>$_SESSION["ByShow"],"query"=>"ads_id_user='$id' and ads_status!='8'", "sort"=>"ORDER By ads_datetime_add DESC") );
$data["shop"] = findOne( "uni_clients_shops", "clients_shops_id_user=?", [ $id ] );
$data["tariff"] = $Profile->getOrderTariff($id);

?>

<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title"><?php echo $Profile->name($getUser); ?></h2>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-xl-3">
      <div class="widget has-shadow">
         <div class="widget-body" style="text-align: center;" >

            <div class="view-user-avatar" >
            <img src="<?php echo $Profile->userAvatar($getUser["clients_avatar"]); ?>" />
            </div>
            <h3 class="text-center mt-3 mb-1"><?php echo $getUser["clients_name"]; ?> <?php echo $getUser["clients_surname"]; ?></h3>
           
            <p class="text-center" ><?php echo $Geo->userGeo( ["city_id"=>$getUser["clients_city_id"], "ip"=>$getUser["clients_ip"]] ); ?></p>

            <h4 style="margin-bottom: 15px;" ><?php echo $Main->price($getUser["clients_balance"]); ?> <a href="#" class="clients-add-balance" data-toggle="modal" data-target="#modal-balance" >пополнить</a></h4>
            
            <?php if($getUser["clients_social_identity"]){ ?>
            <p class="text-center clients-info-list" >Соц сеть: <a target="_blank" title="<?php echo $getUser["clients_social_identity"]; ?>" href="<?php echo $getUser["clients_social_identity"]; ?>"><?php echo custom_substr($getUser["clients_social_identity"], 20); ?></a></p>
            <?php } ?>

            <?php if($getUser["clients_ip"]){ ?>
            <p class="text-center clients-info-list" >IP: <?php echo $getUser["clients_ip"]; ?></p>
            <?php } ?>

            <p class="text-center clients-info-list" >Дата регистрации: <?php echo datetime_format_cp($getUser["clients_datetime_add"]); ?></p>
            <p class="text-center clients-info-list" >Был в сети: <?php echo datetime_format_cp($getUser["clients_datetime_view"]); ?></p>

         </div>
      </div>
   </div>
   <div class="col-xl-9">
      <div class="widget has-shadow">
         <div class="widget-body" style="min-height: 400px;" >

          <ul class="nav nav-tabs nav-fill" role="tablist">
             <li class="nav-item">
                <a class="nav-link active" id="just-tab-1" data-toggle="tab" href="#j-tab-1" role="tab" aria-controls="j-tab-1" aria-selected="true">Основные данные</a>
             </li>
             <li class="nav-item">
                <a class="nav-link" id="just-tab-2" data-toggle="tab" href="#j-tab-2" role="tab" aria-controls="j-tab-2" aria-selected="false">Объявления (<?php echo $data["ads"]["count"]; ?>)</a>
             </li>
             <li class="nav-item">
                <a class="nav-link" id="just-tab-5" data-toggle="tab" href="#j-tab-5" role="tab" aria-controls="j-tab-5" aria-selected="false">История платежей</a>
             </li>
          </ul>

          <br>

          <div class="tab-content pt-3">
             <div class="tab-pane fade show active" id="j-tab-1" role="tabpanel" aria-labelledby="just-tab-1">

                <form class="form-horizontal form-edit-user" >
                   
                   <?php
                   if($getUser["clients_status"] == 2){
                      ?>
                      <div class="form-group row d-flex align-items-center mb-1">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end"></label>
                      <div class="col-lg-6">
                        <div class="alert alert-danger" role="alert" style="display: block;" >
                          Заблокирован по причине: <?php echo $getUser["clients_note"]; ?>
                        </div> 
                      </div> 
                      </div>                                                        
                      <?php
                   }
                   ?>

                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Статус</label>
                      <div class="col-lg-6">

                         <div class="dropdown">

                              <?php 

                               if($getUser["clients_status"] == 0){

                                  ?>

                                  <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Не подтвержден
                                  </button>

                                  <div class="dropdown-menu" >
                                    <a class="dropdown-item change-status-user" data-id="<?php echo $getUser["clients_id"]; ?>" data-status="1" href="#">Активировать</a>
                                  </div>

                                  <?php

                               }elseif($getUser["clients_status"] == 1){

                                  ?>

                                  <button class="btn btn-success dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Активен
                                  </button>

                                  <div class="dropdown-menu" >
                                    <a class="dropdown-item change-status-user" data-id="<?php echo $getUser["clients_id"]; ?>" data-status="2" href="#">Заблокировать</a>
                                  </div>

                                  <?php

                               }elseif($getUser["clients_status"] == 2){

                                  ?>

                                  <button class="btn btn-danger dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Заблокирован
                                  </button>

                                  <div class="dropdown-menu" >
                                    <a class="dropdown-item change-status-user" data-id="<?php echo $getUser["clients_id"]; ?>" data-status="1" href="#">Активировать</a>
                                  </div>

                                  <?php

                               }elseif($getUser["clients_status"] == 3){

                                  ?>

                                  <button class="btn btn-dark dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Удален
                                  </button>

                                  <div class="dropdown-menu" >
                                    <a class="dropdown-item change-status-user" data-id="<?php echo $getUser["clients_id"]; ?>" data-status="1" href="#">Восстановить</a>
                                  </div>

                                  <?php

                               }

                               ?>

                          </div>

                      </div>
                   </div>

                  <?php
                  
                   if( $data["shop"] ){
                   ?>

                      <div class="form-group row mb-5">
                         <label class="col-lg-2 form-control-label text-right">Магазин</label>
                         <div class="col-lg-6">
                             <a target="_blank" href="<?php echo $Shop->linkShop($data["shop"]["clients_shops_id_hash"]); ?>"  ><?php echo $data["shop"]["clients_shops_title"]; ?></a>
                         </div>
                      </div>

                   <?php } ?>

                   <?php
                   if($data["tariff"]){
                      ?>
                      <div class="form-group row mb-5">
                         <label class="col-lg-2 form-control-label text-right">Тариф</label>
                         <div class="col-lg-6">
                             <strong><?php echo $data["tariff"]["services_tariffs_name"]; ?></strong>
                             <br>
                            <?php
                            if($data["tariff"]["services_tariffs_orders_days"]){
                               if(strtotime($data["tariff"]["services_tariffs_orders_date_completion"]) > time()){
                                   ?>
                                   <span>Действует до: <?php echo date('d.m.Y',strtotime($data["tariff"]["services_tariffs_orders_date_completion"])); ?></span>
                                   <?php
                               }else{
                                   ?>
                                   <span style="color: red;" >Срок действия истек</span>
                                   <?php
                               }
                            }else{
                               echo 'Срок неограничен';
                            }
                            ?> 
                         </div>
                      </div>                      
                      <?php
                   }
                   ?>
                  
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Я</label>
                      <div class="col-lg-6">
                          <div class="btn-group btn-group-toggle" data-toggle="buttons">
                             <label class="btn btn-light btn-sm <?php if($getUser["clients_type_person"] == "user"){ echo 'active'; } ?>">
                             <input type="radio" name="user_type_person" value="user" id="option-are-you1" <?php if($getUser["clients_type_person"] == "user"){ echo 'checked=""'; } ?> > Частное лицо
                             </label>
                             <label class="btn btn-light btn-sm <?php if($getUser["clients_type_person"] == "company"){ echo 'active'; } ?>">
                             <input type="radio" name="user_type_person" value="company" id="option-are-you2" <?php if($getUser["clients_type_person"] == "company"){ echo 'checked=""'; } ?> > Компания
                             </label>
                          </div>
                      </div>
                   </div>
                   <div class="input-name-company" <?php if($getUser["clients_type_person"] == "company"){ echo 'style="display: block;"'; }else{  echo 'style="display: none;"'; } ?> >
                   <div class="form-group row d-flex align-items-center mb-5" >
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Компания</label>
                      <div class="col-lg-6">
                         <input type="text" class="form-control" name="user_name_company" value="<?php echo $getUser["clients_name_company"]; ?>">
                      </div>
                   </div>  
                   </div>                                                                    
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Имя</label>
                      <div class="col-lg-6">
                         <input type="text" class="form-control" name="user_name" value="<?php echo $getUser["clients_name"]; ?>">
                      </div>
                   </div>
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Фамилия</label>
                      <div class="col-lg-6">
                         <input type="text" class="form-control" name="user_surname" value="<?php echo $getUser["clients_surname"]; ?>">
                      </div>
                   </div>
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Email</label>
                      <div class="col-lg-6">
                         <input type="text" class="form-control" name="user_email" value="<?php echo $getUser["clients_email"]; ?>">
                      </div>
                   </div>
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Пароль</label>
                      <div class="col-lg-6">
                         <input type="text" class="form-control" name="user_pass" >
                      </div>
                   </div>                   
                   <div class="form-group row d-flex align-items-center mb-5">
                      <label class="col-lg-2 form-control-label d-flex justify-content-lg-end">Телефон</label>
                      <div class="col-lg-6">

                        <div class="profile-phone" >
                          <input type="text" class="form-control"  name="user_phone" value="<?php echo $getUser["clients_phone"]; ?>">
                          <span class="additional-phone-add" style="display: none;" ><i class="la la-plus"></i></span>
                        </div>                        
                        <div class="container-additional-phone" >
                            <?php
                               if($getUser["clients_additional_phones"]){
                                $clients_additional_phones = explode(",",$getUser["clients_additional_phones"]);
                                 foreach ($clients_additional_phones as $key => $value) {
                                    ?>
                                    <div class="additional-phone-item" ><input type="text" class="form-control" value="<?php echo $value; ?>" name="additional_phone[]" ><span class="additional-phone-delete" ><i class="la la-close"></i></span></div>
                                    <?php
                                 }
                               }
                            ?>
                        </div>

                      </div>
                   </div>
                                      
                   <input type="hidden" name="id" value="<?php echo $getUser["clients_id"]; ?>" >                
                </form>

              <div class="text-right">
                 <button class="btn btn-gradient-01 delete-user mr5" data-id="<?php echo $getUser["clients_id"]; ?>">Удалить аккаунт</button>
                 <button class="btn btn-gradient-04 clients-edit-user" >Сохранить</button>
              </div>

             </div>
             <div class="tab-pane fade" id="j-tab-2" role="tabpanel" aria-labelledby="just-tab-2">


              <div class="table-responsive">

                   <?php

                       if($data["ads"]["count"]){   

                       ?>
                       <table class="table mb-0">
                          <thead>
                             <tr>
                              <th>Объявление</th>
                              <th>Статус</th>
                              <th class="text-right" ></th>
                             </tr>
                          </thead>
                          <tbody class="sort-container" >                     
                       <?php

                          foreach($data["ads"]["all"] AS $value){

                            $image = $Ads->getImages($value["ads_images"]);
                          ?>

                           <tr>
                               <td>
                                  <div class="item-table-img" >
                                    <img src="<?php echo Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]); ?>" />
                                  </div>
                                  <div class="item-table-title">
                                  
                                    <a target="_blank" href="<?php echo $Ads->alias($value); ?>" target="_blank" ><?php echo $value["ads_title"]; ?></a>
                                    <br>

                                    <i class="la la-bars"></i> <?php echo $value["category_board_name"];?>
                                    <i class="la la-map-marker"></i> <?php echo $value["city_name"];?>

                                  </div>
                              </td>
                               <td>

                               <div class="dropdown">

                                 <?php 

                                 if($value["clients_delete"] == 0){

                                    echo $Ads->adminAdStatus($value);

                                 }else{

                                    ?>
                                    <div class="btn btn-warning btn-sm">
                                      Снято с публикации
                                    </div>                                    
                                    <?php

                                 }

                                 ?>

                              </div>

                               </td> 
                               <td class="td-actions text-right" >
                                <a href="?route=ads_change&id=<?php echo $value["ads_id"]; ?>" ><i class="la la-question-circle edit"></i></a>
                                <a href="<?php echo _link("ad/update/".$value["ads_id"]); ?>" target="_blank" ><i class="la la-edit edit"></i></a>
                                <a href="#" class="delete-ads" data-id="<?php echo $value["ads_id"]; ?>" ><i class="la la-close delete"></i></a>
                               </td>                          
                           </tr> 
                   
                         
                           <?php                                         
                          } 

                          ?>

                             </tbody>
                          </table>

                          <ul class="pagination justify-content-center">  
                           <?php echo out_navigation( array("count"=>$data["ads"]["count"], "output"=>$_SESSION["ByShow"], "url"=>$LINK, "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page") );?>
                          </ul>

                        <?php

                       }else{
                           
                           ?>
                              <div class="plug" >
                                 <i class="la la-exclamation-triangle"></i>
                                 <p>Объявлений нет</p>
                              </div>
                           <?php

                       }                  
                    ?>

              </div>


             </div>


             <div class="tab-pane fade" id="j-tab-5" role="tabpanel" aria-labelledby="just-tab-5">


                <div class="table-responsive">

                     <?php
                        $get = getAll("SELECT * FROM uni_history_balance where id_user='$id' order by id desc");     

                         if(count($get) > 0){   

                         ?>
                         <table class="table mb-0">
                            <thead>
                               <tr>
                                <th>Назначение</th>
                                <th>Сумма</th>
                                <th>Дата</th>
                               </tr>
                            </thead>
                            <tbody class="sort-container" >                     
                         <?php

                            foreach($get AS $value){
     
                            ?>

                             <tr>
                                 <td><?php echo $value["name"]; ?></td>
                                 <td>

                                   <?php
                                    if($value["action"] == "+"){
                                        echo '<span style="color: green;" >+ '.$Main->price($value["summa"]).'</span>';
                                    }else{
                                        echo '<span style="color: red;" >- '.$Main->price($value["summa"]).'</span>';
                                    }
                                   ?>                                   

                                 </td>
                                 <td><?php echo datetime_format_cp($value["datetime"]); ?></td>                          
                             </tr> 
                     
                           
                             <?php                                         
                            } 

                            ?>

                               </tbody>
                            </table>

                            <?php               
                         }else{
                             
                             ?>
                                <div class="plug" >
                                   <i class="la la-exclamation-triangle"></i>
                                   <p>Данных нет</p>
                                </div>
                             <?php

                         }                  
                      ?>

                </div>


             </div>                         
          </div>

         </div>
      </div>

   </div>
</div>


<div id="modal-balance" class="modal fade">
   <div class="modal-dialog" style="max-width: 600px;" >
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Пополнение баланса</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
            
               <form method="post" class="form-balance-replenish" >

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">Комментарий</label>
                    <div class="col-lg-8">
                          <input type="text" class="form-control" name="title" value="Бонус от <?php echo $settings["site_name"]; ?>" >
                    </div>
                  </div>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">Сумма пополнения <span style="color: red;" >*</span> </label>
                    <div class="col-lg-3">
                         <input type="text" class="form-control" name="summa" >
                    </div>
                  </div>
                  
                  <input type="hidden" name="id" value="<?php echo $id; ?>">
               </form>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary button-balance-replenish">Пополнить</button>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript" src="include/modules/clients/script.js"></script>
<script type="text/javascript" src="include/modules/board/script.js"></script>

    