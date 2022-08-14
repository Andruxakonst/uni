<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Оформление и оплата"); ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
    
    <?php include $config["template_path"] . "/header.tpl"; ?>

    <div class="container mt35" >
        
        <?php if( !$data["order"] ){ ?>
        <div class="row" >
            <div class="col-lg-8" >
              
              <div class="bg-container mb15" >

                <h3> <strong><?php echo $ULang->t("Оформление и оплата"); ?></strong> </h3>

                <div class="buy-item-product mt30" >
                  <div class="buy-item-product-img" >
                    <img alt="<?php echo $data["ad"]["ads_title"]; ?>" src="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>" >
                  </div>
                  <div class="buy-item-product-title" >
                     <strong><?php echo $Main->price( $data["ad"]["ads_price"] ); ?></strong><br>
                     <a href="<?php echo $Ads->alias($data["ad"]); ?>" target="_blank" ><?php echo $data["ad"]["ads_title"]; ?></a>
                  </div>
                </div>

                <div class="clr" ></div>

                <p class="info-text mt30" ><?php echo $ULang->t("Договаривайтесь с продавцом о месте и времени передачи товара самостоятельно. Деньги за оплату товара продавец получит только после того, как вы подтвердите успешное получение товара. А в случае возникновения спорной ситуации, уладить возникшие разногласия поможет сервис «Безопасная сделка»."); ?></p>

              </div>

            </div>
            <div class="col-lg-4" >

               <div class="buy-sidebar" >
                   
                   <h3> <strong><?php echo $Main->price( $data["ad"]["ads_price"] ); ?></strong> </h3>

                   <button class="btn-custom btn-color-green mt15 mb15 buy-payment-goods width100" data-id="<?php echo $data["ad"]["ads_id"]; ?>" > <span><?php echo $ULang->t("Перейти к оплате"); ?></span> </button>

                   <p class="buy-secure-label" > <i class="las la-shield-alt"></i> <a href="<?php echo _link("promo/secure"); ?>" target="_blank" ><?php echo $ULang->t("Как работает безопасная сделка?"); ?></a> </p>

                   <p><?php echo $ULang->t("Нажимая кнопку «Перейти к оплате», вы соглашаетесь с заключением Договора купли-продажи товаров с использованием Онлайн сервиса «Безопасная сделка»"); ?></p>

               </div>
              
            </div>
        </div>
        <?php }else{ ?>
        <div class="row" >
           <div class="col-lg-12" >
             <h4 class="text-center mt30" ><?php echo $ULang->t("Заказ уже оплачивается другим пользователем!"); ?></h4>
             <p class="text-center" ><?php echo $ULang->t("Если заказ не будет оплачен в течении"); ?> <?php echo $Ads->timeSecureReservation( $data["order"]["secure_date"] ); ?>, <?php echo $ULang->t("то вы сможете оформить заказ."); ?></p>
           </div>
        </div>
        <?php } ?>
         
          
       <div class="mt50" ></div>


    </div>

    <div class="redirect-form-pay" ></div>


    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>