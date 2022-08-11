<!doctype html>
<html lang="<?php echo $settings["lang_site_default"]; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Заказ"); ?> №<?php echo $data["order"]["clients_orders_uniq_id"]; ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
    
    <?php include $config["template_path"] . "/header.tpl"; ?>

    <div class="container" >
       
        <h2 class="mt30 mb30" > <strong><?php echo $ULang->t("Заказ"); ?> №<?php echo $data["order"]["clients_orders_uniq_id"]; ?></strong> </h2>
          
        <div class="row" >
            <div class="col-lg-12" >

              <div class="bg-container" >
                
                <div class="row" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Статус"); ?></label> </div>
                   <div class="col-lg-9" >

                    <?php if( $data["order"]["clients_orders_from_user_id"] == $_SESSION["profile"]["id"] ){
                    

                        if($data["order"]["clients_orders_status"] == 0){ 

                          ?>
                          <span class="order-status" > <?php echo $ULang->t("Новый заказ"); ?> </span>

                        <?php 
                        }elseif($data["order"]["clients_orders_status"] == 1){

                          ?>
                          <span class="order-status" > <?php echo $ULang->t("Заказ выполнен"); ?> </span>
                          <?php

                        }elseif($data["order"]["clients_orders_status"] == 2){

                          ?>
                          <span class="order-status" > <?php echo $ULang->t("Заказ отменен"); ?> </span>
                          <?php

                        }

                        ?>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["clients_orders_date"]); ?> </span>
                        <?php

                        if($data["order"]["clients_orders_status"] == 1){
                            ?>
                            <a class="btn-custom-mini btn-color-green mt15" href="<?php echo _link( "user/" . $data["user"]["clients_id_hash"] . "/reviews" ); ?>" > <span><?php echo $ULang->t("Оставить отзыв о продавце"); ?></span> </a>                
                            <?php
                        }

                   }elseif( $data["order"]["clients_orders_to_user_id"] == $_SESSION["profile"]["id"] ){ ?>

                        
                        <?php if($data["order"]["clients_orders_status"] != 2){ ?>
                        <select class="custom-selectpicker order-change-status" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" >
                            <option value="0" <?php if($data["order"]["clients_orders_status"] == 0){ echo 'selected=""'; } ?> ><?php echo $ULang->t("Новый"); ?></option>
                            <option value="1" <?php if($data["order"]["clients_orders_status"] == 1){ echo 'selected=""'; } ?> ><?php echo $ULang->t("Выполнен"); ?></option>
                            <option value="2" <?php if($data["order"]["clients_orders_status"] == 2){ echo 'selected=""'; } ?> ><?php echo $ULang->t("Отменен"); ?></option>
                        </select>
                        <?php }else{ ?>
                        <span class="order-status" > <?php echo $ULang->t("Заказ отменен"); ?> </span>    
                        <?php } ?>

                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["clients_orders_date"]); ?> </span>

                        <?php
                        if($data["order"]["clients_orders_status"] == 1){
                            ?>
                            <a class="btn-custom-mini btn-color-green mt15" href="<?php echo _link( "user/" . $data["user"]["clients_id_hash"] . "/reviews" ); ?>" > <span><?php echo $ULang->t("Оставить отзыв о покупателе"); ?></span> </a>                
                            <?php
                        }
                        ?>

                   <?php } ?>
                    

                   </div>
                </div>

                <hr>
                
                <div class="row" >
                <?php if( $data["order"]["clients_orders_to_user_id"] == $_SESSION["profile"]["id"] ){ ?>
                
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Покупатель"); ?></label> </div>
                   <div class="col-lg-9" >
                      <?php echo $Profile->cardUserOrder( $data ); ?>
                   </div>

               <?php }elseif( $data["order"]["clients_orders_from_user_id"] == $_SESSION["profile"]["id"] ){ ?>

                  <div class="col-lg-2" > <label><?php echo $ULang->t("Продавец"); ?></label> </div>
                  <div class="col-lg-9" >
                    <?php echo $Profile->cardUserOrder( $data ); ?>
                  </div>  

               <?php } ?>
               </div>

               <hr>

                <div class="row" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Товары"); ?></label> </div>
                   <div class="col-lg-9" >
                      <?php
                        $getAds = getAll('select * from uni_clients_orders_ads where clients_orders_ads_order_id=?', [$data["order"]['clients_orders_uniq_id']]);
                        if(count($getAds)){
                            foreach ($getAds as $value) {

                                $getAd = $Ads->get("ads_id=?", [$value['clients_orders_ads_ad_id']]);
                                $image = $Ads->getImages($getAd["ads_images"]);

                                if($getAd){
                                    $itog += $value['clients_orders_ads_total'];
                                    ?>
                                    <div style="margin-bottom: 10px;" >
                                      <div class="board-view-ads-left" >
                                        <div class="board-view-ads-img" >
                                          <img src="<?php echo Exists($config["media"]["big_image_ads"],$image[0],$config["media"]["no_image"]); ?>">
                                        </div>
                                      </div>

                                      <div class="board-view-ads-right" >

                                        <a href="<?php echo $Ads->alias($getAd); ?>"  ><?php echo $getAd["ads_title"]; ?></a>
                                        <br>
                                        <div class="board-view-ads-right-span" >
                                            <span><?php echo $ULang->t("Количество"); ?>: <?php echo $value["clients_orders_ads_count"]; ?></span>
                                            <span><?php echo $ULang->t("Сумма"); ?>: <?php echo $Main->price($value["clients_orders_ads_total"]); ?></span>
                                        </div>

                                      </div>

                                      <div class="clr" ></div>
                                    </div>
                                    <?php
                                }

                            }
                        }
                      ?>
                   </div>
                </div>

                <hr>

                <div class="row mb10" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Итого"); ?></label> </div>
                   <div class="col-lg-9" >
                      <h6><?php echo $Main->price( $itog ); ?></h6>
                   </div>
                </div>   

                
                <?php if( $data["order"]["clients_orders_status"] == 0 && $data["order"]["clients_orders_from_user_id"] == $_SESSION["profile"]["id"] ){ ?>
                <div class="row" >
                  <div class="col-lg-6" ></div>
                  <div class="col-lg-6 text-right" > 
                    <span class="order-cancel-deal open-modal" style="color: red; margin-right: 15px;" data-id-modal="modal-delete-order" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" ><?php echo $ULang->t("Удалить заказ"); ?></span> 
                    <span class="order-cancel-deal open-modal" data-id-modal="modal-confirm-cancel-order" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" ><?php echo $ULang->t("Отменить заказ"); ?></span>
                  </div>
                </div>
                <?php } ?>

                <?php if( $data["order"]["clients_orders_to_user_id"] == $_SESSION["profile"]["id"] ){ ?>
                <div class="row" >
                  <div class="col-lg-6" ></div>
                  <div class="col-lg-6 text-right" > 
                    <span class="order-cancel-deal open-modal" style="color: red; margin-right: 15px;" data-id-modal="modal-delete-order" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" ><?php echo $ULang->t("Удалить заказ"); ?></span> 
                  </div>
                </div>
                <?php } ?>

              </div>

            </div>
        </div>
          
        <h5 class="mt30 mb30" > <strong><?php echo $ULang->t("Сообщения"); ?></strong> </h5>
          
        <div class="row" >
            <div class="col-lg-12" >

              <div class="order-messages-box" >

                <?php
                $getMessagesOrder = $Profile->getMessagesOrder($data["order"]["clients_orders_uniq_id"]);

                if(isset($getMessagesOrder)){
                    echo $getMessagesOrder;
                }
                ?>
                
              </div>

              <textarea class="order-send-message" placeholder="<?php echo $ULang->t("Напишите сообщение и нажмите Enter"); ?>" ></textarea>

              <span class="order-message-attach-change" ><?php echo $ULang->t("Прикрепить фото"); ?></span>

              <form class="form-order-message" >
                 <div class="order-message-attach-files" ></div>
                 <input type="hidden" name="order_id" value="<?php echo $data["order"]["clients_orders_uniq_id"]; ?>" >
              </form>

              <input type="file" accept=".jpg,.jpeg,.png" multiple="true" style="display: none;" class="input_attach_files" />

            </div>
        </div>

       <div class="mt50" ></div>


    </div>


    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-cancel-order" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите отменить заказ?"); ?></h4>            
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue confirm-cancel-order-marketplace schema-color-button" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" ><?php echo $ULang->t("Отменить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-delete-order" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить заказ?"); ?></h4>            
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue confirm-delete-order-marketplace schema-color-button" data-id="<?php echo $data["order"]["clients_orders_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>