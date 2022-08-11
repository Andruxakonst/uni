<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t('Корзина товаров'); ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
  
  <header class="header-cart" >
   <div class="container" >
         
         <div class="row" >
             <div class="col-lg-2" >
               
                <a class="h-logo" href="<?php echo _link(); ?>" title="<?php echo $ULang->t($settings["title"]); ?>" >
                    <img src="<?php echo $settings["logotip"]; ?>" data-inv="<?php echo $settings["logo_color_inversion"]; ?>" alt="<?php echo $ULang->t($settings["title"]); ?>">
                </a>

             </div>
             <div class="col-lg-10" >
                

             </div>
         </div>

   </div>   
   </header> 

    <div class="container" >
       
        <?php if(count($_SESSION['cart'])){ ?>

          <div class="cart-box-1" >

          <div class="row" >
              <div class="col-lg-12" >
                <h2 class="mb30 title-bold" ><?php echo $ULang->t('Корзина товаров'); ?></h2>
              </div>
              <div class="col-lg-9 order-lg-1  order-2" >

                <?php if(!$_SESSION['profile']['id']){ ?>
                <div class="cart-info-auth" >
                  <h4><?php echo $ULang->t('Войдите или зарегистрируйтесь'); ?></h4>
                  <p><?php echo $ULang->t('Для оформления заказа - Вам нужно войти в личный кабинет.'); ?></p>

                  <button class="open-modal" data-id-modal="modal-auth" ><i class="las la-sign-in-alt"></i> <?php echo $ULang->t('Войти'); ?></button>
                </div>
                <?php } ?>
                
                <div class="cart-page-container" >

                    <div class="row" >
                        <div class="col-lg-9 col-8" >
                            <h4 class="mb30 title-bold" ><?php echo $ULang->t('Товары в заказе'); ?></h4>
                        </div>
                        <div class="col-lg-3 col-4 text-right" >
                            <span class="cart-clear cart-page-link-clear" ><?php echo $ULang->t('Очистить'); ?></span>
                        </div>
                    </div>

                    <?php

                          $cart = $Cart->getCart();

                          foreach ($cart as $id => $value) {

                              $count = $value['count'];

                              $image = $Ads->getImages($value['ad']["ads_images"]);

                              $getShop = $Shop->getShop(['user_id'=>$value['ad']["ads_id_user"],'conditions'=>true]);

                              if( $getShop ){
                                  $link = '<a href="'.$Shop->linkShop($getShop["clients_shops_id_hash"]).'" class="cart-goods-item-content-seller"  >'.$getShop["clients_shops_title"].'</a>';
                              }else{
                                  $link = '<a href="'._link( "user/" . $data["ad"]["clients_id_hash"] ).'" class="cart-goods-item-content-seller"  >'.$Profile->name($value['ad']).'</a>';
                              }

                              $price_info = '
                                  <span class="cart-goods-item-content-price" >'.$count.' x '.$Main->price( $value['ad']["ads_price"] ).'</span>
                                  <span class="cart-goods-item-content-price-total" >'.$Main->price( $value['ad']["ads_price"] * $count ).'</span>
                              ';

                              if($value['ad']['ads_available'] == 1){
                                $notification_available = $ULang->t('Остался 1 товар');
                              }else{
                                $notification_available = '
                                  <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                      <button class="btn btn-outline-secondary cart-goods-item-count-change" data-action="minus" ><i class="las la-minus"></i></button>
                                    </div>
                                    <input type="text" class="form-control cart-goods-item-count" value="'.$count.'" >
                                    <div class="input-group-append">
                                      <button class="btn btn-outline-secondary cart-goods-item-count-change" data-action="plus" ><i class="las la-plus"></i></button>
                                    </div>
                                  </div>
                                ';
                              }

                              if( $value['ad']["ads_status"] != 1 || strtotime($value['ad']["ads_period_publication"]) < time() ){
                                  
                                  $status = '<div class="cart-goods-item-label-status" >'.$Ads->publicationAndStatus($value['ad']).'</div>';
                                  $group = '';

                              }else{ 

                                  if( !$value['ad']['ads_available_unlimitedly'] ){

                                       if( $value['ad']['ads_available'] ){
                                              $group = '
                                                  <div class="row" >
                                                      <div class="col-lg-8 col-6" >

                                                          '.$notification_available.'

                                                      </div>
                                                      <div class="col-lg-4 col-6" >     
                                                          <div class="cart-goods-item-content-price-info" >
                                                              '.$price_info.'
                                                          </div>
                                                      </div>                                                                    
                                                  </div>
                                              ';
                                       }else{
                                              $group = '
                                                  <div class="row" >
                                                      <div class="col-lg-6 col-6 col-md-3 col-sm-3" >
                                                        <span class="cart-not-available" >'.$ULang->t('Нет в наличии').'</span>
                                                      </div>
                                                      <div class="col-lg-6 col-6 col-md-9 col-sm-9" >            
                                                          <div class="cart-goods-item-content-price-info" >
                                                              '.$price_info.'
                                                          </div>
                                                      </div>                                                                    
                                                  </div>
                                              ';
                                       }

                                  }else{

                                      $group = '
                                          <div class="row" >
                                              <div class="col-lg-8 col-6" >

                                                  '.$notification_available.'

                                              </div>
                                              <div class="col-lg-4 col-6" >   
                                                <div class="cart-goods-item-content-price-info" >
                                                    '.$price_info.'
                                                </div>
                                              </div>                                                                    
                                          </div>
                                      ';

                                  }

                                  $status = '';

                              }

                              $items .= '
                                  <div class="cart-goods-item" data-id="'.$value['ad']["ads_id"].'" >

                                      <div class="row" >
                                          <div class="col-lg-2 col-4" >
                                              <div class="cart-goods-item-image" >
                                                <img class="image-autofocus" alt="'.$value['ad']["ads_title"].'" src="'.Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]).'"  >
                                              </div>
                                          </div>
                                          <div class="col-lg-10 col-8" >

                                              <div class="row" >
                                                  <div class="col-lg-10 col-10" >

                                                      <div class="cart-goods-item-content" >
                                                        '.$status.'
                                                        <a href="'.$Ads->alias($value['ad']).'" >'.$value['ad']["ads_title"].'</a>
                                                        '.$link.'
                                                        '.$group.'
                                                      </div>

                                                  </div>
                                                  <div class="col-lg-2 col-2 text-right" >
                                                      <span class="cart-page-goods-item-delete" ><i class="las la-trash"></i></span>
                                                  </div>
                                              </div>

                                          </div>               
                                      </div>
                                    
                                  </div>
                              ';

                          }

                          $container = '

                              <div class="cart-goods" >
                                  '.$items.'
                              </div>

                          ';


                      echo $container;
                    ?>

                </div>

              </div>
              <div class="col-lg-3 order-lg-2  order-1" >

                <div class="cart-page-container mb15" >

                    <h4 class="title-bold" ><?php echo $ULang->t('Ваш заказ'); ?></h4>

                    <div class="row" >
                        <div class="col-lg-6 col-6" >
                            <span class="cart-page-sidebar-name" ><?php echo $ULang->t('Товаров'); ?></span>
                        </div>
                        <div class="col-lg-6 col-6" >
                            <span class="cart-page-sidebar-value cart-item-counter" ><?php echo $Cart->totalCount(); ?></span>
                        </div>
                    </div>
                  
                    <h6 class="title-bold mt20" ><?php echo $ULang->t('Итого:'); ?></h6>

                    <div class="row" >
                        <div class="col-lg-6 col-6" >
                            <span class="cart-page-sidebar-name" ><?php echo $ULang->t('Сумма заказа'); ?></span>
                        </div>
                        <div class="col-lg-6 col-6" >
                            <span class="cart-page-sidebar-value cart-itog" ><?php echo $Main->price( $Cart->calcTotalPrice() ); ?></span>
                        </div>
                    </div>

                    <div class="btn-custom btn-color-blue width100 cart-order mt30" data-page-type="page" >
                      <span><?php echo $ULang->t("Оформить заказ"); ?></span>
                    </div>

                </div>


              </div>
          </div>

          </div>

          <div class="cart-box-2" >
            
            <div class="cart-page-container" >
            <div class="cart-empty" >
              
                <div class="cart-empty-icon" >
                  <i class="las la-check"></i>
                  <h5><strong><?php echo $ULang->t('Заказ успешно оформлен!'); ?></strong></h5>
                  <p><?php echo $ULang->t('Скоро мы с Вами свяжемся=)'); ?></p>
                </div>         

            </div>
            </div>

          </div>

        <?php }else{ ?>

            
            <div class="cart-page-container" >
            <div class="cart-empty" >
              
                <div class="cart-empty-icon" >
                  <i class="las la-shopping-bag"></i>
                  <h5><strong><?php echo $ULang->t('Корзина пуста'); ?></strong></h5>
                  <p><?php echo $ULang->t('Скорее за покупками=)'); ?></p>
                </div>         

            </div>
            </div>

        <?php } ?>
         
          
       <div class="mt50" ></div>


    </div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>