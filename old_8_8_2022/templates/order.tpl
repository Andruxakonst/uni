<!doctype html>
<html lang="<?php echo $settings["lang_site_default"]; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Заказ"); ?> №<?php echo $data["order"]["secure_id_order"]; ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
    
    <?php include $config["template_path"] . "/header.tpl"; ?>

    <div class="container" >
       
        <h2 class="mt30 mb30" > <strong><?php echo $ULang->t("Заказ"); ?> №<?php echo $data["order"]["secure_id_order"]; ?></strong> </h2>
          
        <div class="row" >
            <div class="col-lg-12" >

              <div class="bg-container" >
                
                <div class="row" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Статус"); ?></label> </div>
                   <div class="col-lg-9" >
                    
                    <?php if($data["order"]["secure_status"] == 0){ ?>

                      <span class="order-status" > <?php echo $ULang->t("Ожидается оплата"); ?> </span>
                      <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>
                      
                      <?php if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){ ?>
                      <p class="mt10" ><?php echo $ULang->t("Деньги будут зарезервированы до получения товара"); ?></p>
                      <button class="btn-custom-mini btn-color-blue mt5 buy-payment-goods" data-id="<?php echo $data["ad"]["ads_id"]; ?>" > <span><?php echo $ULang->t("Перейти к оплате"); ?></span> </button>
                      <p class="mt10" ><?php echo $ULang->t("Произведите оплату, или заказ будет автоматически удален через"); ?> <?php echo $Ads->timeSecureReservation( $data["order"]["secure_date"] ); ?></p>
                      <?php }else{ ?>
                      <p class="mt10" ><?php echo $ULang->t("Если заказ не будет оплачен, то он автоматически будет удален через"); ?> <?php echo $Ads->timeSecureReservation( $data["order"]["secure_date"] ); ?></p>
                      <?php } ?>

                    <?php 
                    }elseif($data["order"]["secure_status"] == 1){

                      if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){

                        ?>
                        <span class="order-status" > <?php echo $ULang->t("Заказ оплачен. Получите товар у продавца."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>
                        <p class="mt10"><?php echo $Main->price( $data["order"]["secure_price"] ); ?> <?php echo $ULang->t("зарезервированы до получения товара. Договоритесь с продавцом о месте и времени встречи."); ?></p>
                        <?php

                      }elseif( $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){
                        
                        ?>
                        <span class="order-status" > <?php echo $ULang->t("Заказ оплачен. Передайте товар покупателю."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>
                        <p class="mt10"><?php echo $Main->price( $data["order"]["commission_and_price"] ); ?> <?php echo $ULang->t("зарезервированы до передачи товара. Договоритесь с покупателем о месте и времени встречи."); ?></p>
                        <button class="btn-custom-mini btn-color-green mt5 open-modal" data-id-modal="modal-confirm-transfer-goods" > <span><?php echo $ULang->t("Подтвердить передачу товара"); ?></span> </button>
                        <?php

                      }

                    }elseif($data["order"]["secure_status"] == 2){

                      if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){

                        ?>
                        <span class="order-status" > <?php echo $ULang->t("Товар передан. Подтвердите получение."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>
                        <p class="mt10"><?php echo $ULang->t("Продавец передал вам товар? Подтвердите получение или откройте спор."); ?></p>    

                        <div class="row" >
                           <div class="col-lg-4 col-md-6 col-sm-6 col-12" > <button class="btn-custom-mini btn-color-green mt5 open-modal width100 mb5" data-id-modal="modal-confirm-receive-goods" > <span><?php echo $ULang->t("Подтвердить получение товара"); ?></span> </button> </div>
                           <div class="col-lg-3 col-md-6 col-sm-6 col-12" > <button class="btn-custom-mini btn-color-light mt5 open-modal width100 mb5" data-id-modal="modal-dispute-secure" > <span><?php echo $ULang->t("Открыть спор"); ?></span> </button> </div>
                        </div>                    
                        <?php

                      }elseif( $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){
                        
                        ?>
                        <span class="order-status" > <?php echo $ULang->t("Товар передан. Ожидаем подтверждение покупателя."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>
                        <p class="mt10"><?php echo $Main->price( $data["order"]["commission_and_price"] ); ?> <?php echo $ULang->t("будут зачислены на ваш счет в течении 24 часа, после того как покупатель подтвердит получение товара."); ?></p>                        
                        <?php

                      }

                    }elseif($data["order"]["secure_status"] == 3){

                      if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){

                        ?>
                        <span class="order-status" style="color: rgb(119, 192, 38);" > <?php echo $ULang->t("Заказ завершён."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>  

                        <a class="btn-custom-mini btn-color-green mt15" href="<?php echo _link( "user/" . $data["user"]["clients_id_hash"] . "/reviews" ); ?>" > <span><?php echo $ULang->t("Оставить отзыв о продавце"); ?></span> </a>                
                        <?php
                        
                        if( $data["disputes"]["secure_disputes_status"] == 1 || $data["disputes"]["secure_disputes_status"] == 2 ){
                            echo $Ads->secureResultPay( [ "id_user"=>$data["order"]["secure_id_user_buyer"],"id_order"=>$data["order"]["secure_id_order"] ] );
                        }

                      }elseif( $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){
                        
                        ?>
                        <span class="order-status" style="color: rgb(119, 192, 38);" > <?php echo $ULang->t("Заказ завершён."); ?> </span>
                        <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>

                        <a class="btn-custom-mini btn-color-green mt15" href="<?php echo _link( "user/" . $data["user"]["clients_id_hash"] . "/reviews" ); ?>" > <span><?php echo $ULang->t("Оставить отзыв о покупателе"); ?></span> </a>
                        <?php

                        echo $Ads->secureResultPay( [ "id_user"=>$data["order"]["secure_id_user_seller"],"id_order"=>$data["order"]["secure_id_order"] ] );

                      }

                    }elseif($data["order"]["secure_status"] == 4){
                        ?>

                         <span class="order-status" > <?php echo $ULang->t("Открыт спор"); ?> </span>
                         <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>

                         <p class="mt10"><?php echo $ULang->t("Открыт спор с участием арбитра. Арбитр приступил к изучению деталей спора."); ?></p>
                         
                         <?php if($data["disputes"]["secure_disputes_text"]){ ?>
                         <div><strong><?php echo $ULang->t("Комментарий"); ?></strong></div>
                         <p class="mt10"><?php echo $data["disputes"]["secure_disputes_text"]; ?></p>
                         <?php } ?>
                         
                         <?php if($data["disputes"]["secure_disputes_text_arbitr"]){ ?>
                         <div><strong><?php echo $ULang->t("Решение арбитра"); ?></strong></div>
                         <p class="mt10"><?php echo $data["disputes"]["secure_disputes_text_arbitr"]; ?></p>
                         <?php } 

                    }elseif($data["order"]["secure_status"] == 5){
                        ?>

                         <span class="order-status" > <?php echo $ULang->t("Заказ отменен"); ?> </span>
                         <span class="order-date" > <?php echo $ULang->t("Заказ создан:"); ?> <?php echo datetime_format($data["order"]["secure_date"]); ?> </span>

                        <?php

                        if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){
                           
                           echo $Ads->secureResultPay( [ "id_user"=>$data["order"]["secure_id_user_buyer"],"id_order"=>$data["order"]["secure_id_order"] ] );

                        }

                    }

                    if( $data["disputes"] && $data["order"]["secure_status"] != 4 ){
                        ?>
                        <div style="margin-top: 15px;" >
                        <strong><?php echo $ULang->t("Решение арбитра:"); ?></strong>
                        <p><?php echo $data["disputes"]["secure_disputes_text_arbitr"]; ?></p>
                        </div>
                        <?php
                    }
                    ?>

                   </div>
                </div>

                <hr>
                
                <div class="row" >
                <?php if( $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){ ?>
                
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Покупатель"); ?></label> </div>
                   <div class="col-lg-9" >
                      <?php echo $Profile->cardUserOrder( $data ); ?>
                      <span class="btn-custom-mini btn-color-blue mt15 open-modal ad-init-message" data-id-ad="<?php echo $data["ad"]["ads_id"]; ?>" data-id-modal="modal-chat-user" > <span><?php echo $ULang->t("Написать покупателю"); ?></span> </span>
                   </div>

               <?php }elseif( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] ){ ?>

                  <div class="col-lg-2" > <label><?php echo $ULang->t("Продавец"); ?></label> </div>
                  <div class="col-lg-9" >
                    <?php echo $Profile->cardUserOrder( $data ); ?>
                    <span class="btn-custom-mini btn-color-blue mt15 open-modal ad-init-message" data-id-ad="<?php echo $data["ad"]["ads_id"]; ?>" data-id-modal="modal-chat-user" > <span><?php echo $ULang->t("Написать продавцу"); ?></span> </span>
                  </div>  

               <?php } ?>
               </div>

               <hr>

                <div class="row" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Товар"); ?></label> </div>
                   <div class="col-lg-9" >
                      <?php echo $Ads->cardAdOrder($data); ?>
                   </div>
                </div>

                <hr>

                <div class="row mb10" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Стоимость"); ?></label> </div>
                   <div class="col-lg-9" >
                      <h6><?php echo $Main->price( $data["order"]["secure_price"] ); ?></h6>
                   </div>
                </div>   

                <?php if( $data["order"]["secure_id_user_seller"] == $_SESSION["profile"]["id"] ){ ?>             
                
                <?php if($data["order"]["commission"]){ ?>
                <div class="row mb10" >
                   <div class="col-lg-2" > <label><?php echo $ULang->t("Комиссия сервиса"); ?></label> </div>
                   <div class="col-lg-9" >
                      <?php echo "-" . $Main->price( $data["order"]["commission"] ); ?>
                   </div>
                </div>
                <?php } ?>

                <div class="row" >
                   <div class="col-lg-2" > <label><strong><?php echo $ULang->t("Итого"); ?></strong></label> </div>
                   <div class="col-lg-9" >
                      <strong><?php echo $Main->price( $data["order"]["commission_and_price"] ); ?></strong>
                   </div>
                </div>

                <?php } ?>
                
                <?php if( $data["order"]["secure_id_user_buyer"] == $_SESSION["profile"]["id"] && $data["order"]["secure_status"] == 1 ){ ?>
                <div class="row" >
                  <div class="col-lg-6" ></div>
                  <div class="col-lg-6 text-right" > <span class="order-cancel-deal open-modal" data-id-modal="modal-confirm-cancel-order" data-id="<?php echo $data["order"]["secure_id"]; ?>" ><?php echo $ULang->t("Отменить сделку"); ?></span> </div>
                </div>
                <?php } ?>


              </div>

            </div>
        </div>
         
          
       <div class="mt50" ></div>


    </div>

    <div class="redirect-form-pay" ></div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-transfer-goods" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Подтвердить передачу?"); ?></h4>    
              <p class="mt15" ><?php echo $ULang->t("Вы действительно передали ваш товар покупателю? Если товар не был передан, покупатель сможет оспорить сделку."); ?></p>        
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue confirm-transfer-goods schema-color-button" data-id="<?php echo $data["order"]["secure_id"]; ?>" ><?php echo $ULang->t("Подтвердить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-receive-goods" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Подтвердить получение?"); ?></h4>    
              <p class="mt15" ><?php echo $ULang->t("Подтверждая получение товара, вы соглашаетесь с тем, что товар получен и полностью вас устраивает."); ?></p>        
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue confirm-receive-goods schema-color-button" data-id="<?php echo $data["order"]["secure_id"]; ?>" ><?php echo $ULang->t("Подтвердить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-confirm-cancel-order" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите отменить сделку?"); ?></h4>            
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom color-blue confirm-cancel-order schema-color-button" data-id="<?php echo $data["order"]["secure_id"]; ?>" ><?php echo $ULang->t("Отменить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-dispute-secure" >
        <div class="modal-custom animation-modal" style="max-width: 600px;" >

         <span class="modal-custom-close" ><i class="las la-times"></i></span>

         <h4> <strong><?php echo $ULang->t("Открытие спора"); ?></strong> </h4>
         
         <form class="form-dispute-secure mt25" >

           <div class="row mt20" >
             <div class="col-lg-3" > <strong><?php echo $ULang->t("Комментарий"); ?></strong> </div>
             <div class="col-lg-9" >
               
               <textarea style="min-height: 150px;" placeholder="<?php echo $ULang->t("Опишите проблему максимально подробно"); ?>" name="text" class="form-control" ></textarea>

               <p style="margin-bottom: 0px; margin-top: 5px;" ><?php echo $ULang->t("Продавец будет видеть ваш комментарий."); ?></p>

             </div>
           </div>

           <div class="row mt20" >
             <div class="col-lg-3" > <strong><?php echo $ULang->t("Вложения"); ?></strong> </div>
             <div class="col-lg-9" > 
                <span class="dispute-secure-attach" ><?php echo $ULang->t("Добавить"); ?></span> 
                <p><?php echo $ULang->t("Прикрепите дополнительные материалы которые помогут в споре (скрины переписок, фото товара и.т д). Не больше 5-ти файлов."); ?></p>
             </div>
           </div>

            <div class="mt30" ></div>

            <div class="modal-custom-button" >
               <div>
                 <button class="button-style-custom color-blue schema-color-button" ><?php echo $ULang->t("Открыть спор"); ?></button>
               </div> 
               <div>
                 <span class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></span>
               </div>                                       
            </div>

            <input type="hidden" name="id" value="<?php echo $data["order"]["secure_id"]; ?>" >
            <input type="file" name="files[]" accept=".jpg,.jpeg,.png" multiple="true" class="file-dispute-attach" />

         </form>

        </div>
    </div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>