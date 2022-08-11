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

if(isAjax() == true){

	if($_POST["action"] == "add_to_cart"){

		$id = (int)$_POST['id'];

		if(!$id) exit;

		$getAd = findOne('uni_ads', 'ads_id=?', [$id]);

        if($getAd["ads_available_unlimitedly"]){
            $next = true;
        }elseif($getAd["ads_available"]){
            $next = true;
        }else{
            $next = false;
        }

        if($next){

			if( !isset($_SESSION['cart'][$id]) ){
					$_SESSION['cart'][$id] = 1; 
					if($_SESSION['profile']['id']){
						$Main->addActionStatistics(['ad_id'=>$id,'from_user_id'=>$_SESSION['profile']['id'],'to_user_id'=>$getAd['ads_id_user']],"add_to_cart");
				    }
					echo json_encode(['status'=>true, 'action'=>'add', 'view_cart'=>$settings["marketplace_view_cart"], 'link_cart'=>_link('cart')]);
			}else{
					unset($_SESSION['cart'][$id]);

					if($_SESSION['profile']['id']){
						 update("DELETE FROM uni_cart WHERE cart_ad_id=? and cart_user_id=?", [$id,$_SESSION["profile"]["id"]]);
					}

					echo json_encode(['status'=>true, 'action'=>'delete', 'view_cart'=>$settings["marketplace_view_cart"], 'link_cart'=>_link('cart')]);
			}

			$Cart->refresh();

		}else{
			echo json_encode(['status'=>false, 'answer'=>'Данного товара уже нет в наличии']);
		}

	}

	if($_POST["action"] == "load_cart"){

		$id = (int)$_POST['id'];

		$cart = $Cart->getCart();

		if(count($cart)){

				foreach ($cart as $id => $value) {

						$count = $value['count'];

						$image = $Ads->getImages($value['ad']["ads_images"]);

						$price_info = '
    						<span class="cart-goods-item-content-price" >'.$count.' x '.$Main->price( $value['ad']["ads_price"] ).'</span>
    						<span class="cart-goods-item-content-price-total" >'.$Main->price( $value['ad']["ads_price"] * $count ).'</span>
						';

                      	$getShop = $Shop->getShop(['user_id'=>$value['ad']["ads_id_user"],'conditions'=>true]);

                      	if( $getShop ){
                          $link = '<a href="'.$Shop->linkShop($getShop["clients_shops_id_hash"]).'" class="cart-goods-item-content-seller"  >'.$getShop["clients_shops_title"].'</a>';
                      	}else{
                          $link = '<a href="'._link( "user/" . $data["ad"]["clients_id_hash"] ).'" class="cart-goods-item-content-seller"  >'.$Profile->name($value['ad']).'</a>';
                      	}

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
						                				<div class="col-lg-7 col-7 col-md-4 col-sm-4" >

															'.$notification_available.'

						                				</div>
						                				<div class="col-lg-5 col-5 col-md-8 col-sm-8" >			
							                				  <div class="cart-goods-item-content-price-info" >
								                						'.$price_info.'
							                					</div>
						                				</div>				                  								                  				
						                		</div>
														';
										 }else{
														$group = '
						                		<div class="row" >
						                				<div class="col-lg-7 col-7 col-md-4 col-sm-4" >
																			<span class="cart-not-available" >'.$ULang->t('Нет в наличии').'</span>
						                				</div>
						                				<div class="col-lg-5 col-5 col-md-8 col-sm-8" >				     
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
		                				<div class="col-lg-7 col-7 col-md-4 col-sm-4" >

											'.$notification_available.'

		                				</div>
		                				<div class="col-lg-5 col-5 col-md-8 col-sm-8" >		
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
				                <div class="col-lg-3 col-3 col-md-2 col-sm-3" >
				                  	<div class="cart-goods-item-image" >
				                  		<img class="image-autofocus" alt="'.$value['ad']["ads_title"].'" src="'.Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]).'"  >
				                  	</div>
				                </div>
				                <div class="col-lg-9 col-9 col-md-10 col-sm-9" >

				                		<div class="row" >
				                				<div class="col-lg-10 col-10" >

							                		<div class="cart-goods-item-content" >
							                		'.$status.'
							                  		<a href="'.$Ads->alias($value['ad']).'" >'.$value['ad']["ads_title"].'</a>
							                  		'.$link.'
							                  		'.$group.'
							                  		</div>

				                				</div>
				                				<div class="col-lg-2 col-2" >
				                						<span class="cart-goods-item-delete" ><i class="las la-trash"></i></span>
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

				      <div class="cart-buttons" >

				          <div class="btn-custom btn-color-blue cart-order mb5 width100" data-page-type="'.$settings["marketplace_view_cart"].'" >
				            <span>'.$ULang->t("Оформить заказ").'</span>
				          </div>
				        
				      </div>

				';

		}else{

				$container = '

			      <div class="cart-empty" >
			        
			          <div class="cart-empty-icon" >
			            <i class="las la-shopping-bag"></i>
			            <p>'.$ULang->t('Корзина пуста').'</p>
			          </div>         

			      </div>

				';

		}

		$info = $Cart->totalCount() . ' ' . ending($Cart->totalCount(), $ULang->t('товар'), $ULang->t('товара'), $ULang->t('товаров')) . ' '.$ULang->t('на сумму').' ' . $Main->price( $Cart->calcTotalPrice() );

		$itog = $Main->price( $Cart->calcTotalPrice() );

		echo json_encode(['items'=>$container, 'counter'=>$Cart->totalCount(), 'info'=>$info, 'itog'=>$itog]);

	}

	if($_POST["action"] == "change_count"){

		$id = (int)$_POST['id'];
		$variant = $_POST['variant'];

		$getAd = findOne('uni_ads', 'ads_id=?', [$id]);

		if($getAd){

			if(!$getAd['ads_available'] && !$getAd['ads_available_unlimitedly']){
				echo json_encode(['status'=>false, 'available'=>0]);
				exit;
			}
			
			if($variant == 'minus'){

					$_SESSION['cart'][$id]--;

					if( abs($_SESSION['cart'][$id]) == 0 ){
							$_SESSION['cart'][$id] = 1;
					}

			}elseif($variant == 'plus'){
					
					if($getAd['ads_available_unlimitedly']){
							$_SESSION['cart'][$id]++;
					}else{

							$_SESSION['cart'][$id]++;

							if(abs($_SESSION['cart'][$id]) > $getAd['ads_available']){
								  $_SESSION['cart'][$id] = $getAd['ads_available'];
							}

					}

			}

		}else{
			unset($_SESSION['cart'][$id]);
		}

		$info = $Cart->totalCount() . ' ' . ending($Cart->totalCount(), $ULang->t('товар'), $ULang->t('товара'), $ULang->t('товаров')) . ' '.$ULang->t('на сумму').' ' . $Main->price( $Cart->calcTotalPrice() );

		$total = '
		<span class="cart-goods-item-content-price" >'.intval($_SESSION['cart'][$id]).' x '.$Main->price( $getAd["ads_price"] ).'</span>
		<span class="cart-goods-item-content-price-total" >'.$Main->price( $getAd["ads_price"] * intval($_SESSION['cart'][$id]) ).'</span>
		';

		$itog = $Main->price( $Cart->calcTotalPrice() );

		echo json_encode(['status'=>true, 'count'=>$_SESSION['cart'][$id],'total'=>$total, 'counter'=>$Cart->totalCount(), 'info'=>$info, "itog"=>$itog]);


	}

	if($_POST["action"] == "delete"){

		$id = (int)$_POST['id'];

		unset($_SESSION['cart'][$id]);

		if($_SESSION['profile']['id']){
			 update("DELETE FROM uni_cart WHERE cart_ad_id=? and cart_user_id=?", [$id,$_SESSION["profile"]["id"]]);
		}

	}

	if($_POST["action"] == "clear"){
        
        if(!$_SESSION["profile"]["id"]){ echo $Main->response(401); }

		unset($_SESSION['cart']);

		update("DELETE FROM uni_cart WHERE cart_user_id=?", [$_SESSION["profile"]["id"]]);

	}

	if($_POST["action"] == "order"){

		$cart = $Cart->getCart();

		if(!$_SESSION['profile']['id']){
			echo json_encode(['status'=>false, 'auth'=>false]);
			exit;
		}

		if(count($cart)){

			foreach ($cart as $id => $value) {
				$data_order[$value['ad']['ads_id_user']][] = $value;
			}

			foreach ($data_order as $id_user => $array) {

				$order_id = $config['key_rand'];

				insert("INSERT INTO uni_clients_orders(clients_orders_from_user_id,clients_orders_uniq_id,clients_orders_date,clients_orders_to_user_id)VALUES(?,?,?,?)", [intval($_SESSION["profile"]["id"]), $order_id, date('Y-m-d H:i:s'),$id_user]);

				foreach ($array as $value) {

					if($value['ad']['ads_available_unlimitedly']){
						$next = true;
					}else{
						if($value['ad']['ads_available'] >= $value['count']){
							$next = true;
						}else{
							if($value['ad']['ads_available']){
								$value['count'] = $value['ad']['ads_available'];
								$change_stock[$value['ad']['ads_id']] = $value['ad']['ads_available'];
								$next = true;
							}
						}
					}
					
					if($next){

						insert("INSERT INTO uni_clients_orders_ads(clients_orders_ads_ad_id,clients_orders_ads_count,clients_orders_ads_total,clients_orders_ads_order_id,clients_orders_ads_user_id)VALUES(?,?,?,?,?)", [$value['ad']['ads_id'], $value['count'], $Cart->calcTotalPrice(), $order_id,$value['ad']['ads_id_user']]);

						if(!$value['ad']['ads_available_unlimitedly']){
							update("update uni_ads set ads_available=ads_available-".$value['count']." where ads_id=?", [$value['ad']['ads_id']]);
						}

						$Profile->sendChat( array("id_ad" => $value['ad']['ads_id'], "action" => 3, "user_from" => intval($_SESSION["profile"]["id"]), "user_to" => $value['ad']["ads_id_user"] ) );

					}else{
						$out_of_stock[] = $value['ad']['ads_id'];
					}

				}

			}

			echo json_encode(['status'=>true]);

			unset($_SESSION['cart']);

			if($_SESSION['profile']['id']){
				 update("DELETE FROM uni_cart WHERE cart_user_id=?", [$_SESSION["profile"]["id"]]);
			}

		}else{
			echo json_encode(['status'=>false]);
		}

	}

    
}

?>