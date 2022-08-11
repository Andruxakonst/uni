<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_settings']) ){
   $_SESSION["CheckMessage"]["warning"] = "Ограничение прав доступа!";
   exit;
}

if(isAjax() == true){

$getPayment = findOne( "uni_payments", "code=?" , [ clear($_POST["code"]) ] );

	?>

	   <div class="form-group row d-flex align-items-center" style="margin-bottom: 0px;" >
	      <label class="col-lg-3 form-control-label">Доход от безопасной сделки</label>
	      <div class="col-lg-2">

	          <div class="input-group mb-2">
	             <input type="text" class="form-control" name="secure_percent_service" value="<?php echo $getPayment["secure_percent_service"]; ?>" >
	             <div class="input-group-prepend">
	                <div class="input-group-text">%</div>
	             </div>                       
	          </div>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label"></label>
	      <div class="col-lg-9">

	          <small>Укажите процент который вы будете получать от безопасной сделки</small>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center"  style="margin-bottom: 0px;" >
	      <label class="col-lg-3 form-control-label">Процент платежной системы</label>
	      <div class="col-lg-2">

	          <div class="input-group mb-2">
	             <input type="text" class="form-control" name="secure_percent_payment" value="<?php echo $getPayment["secure_percent_payment"]; ?>" >
	             <div class="input-group-prepend">
	                <div class="input-group-text">%</div>
	             </div>                       
	          </div>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label"></label>
	      <div class="col-lg-9">

	          <small>Укажите процент который списывает платежная система при переводе денег по безопасной сделке</small>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label">Дополнительные вычеты платежной системы</label>
	      <div class="col-lg-2">

	          <div class="input-group mb-2">
	             <input type="text" class="form-control" name="secure_other_payment" value="<?php echo $getPayment["secure_other_payment"]; ?>" >
	             <div class="input-group-prepend">
	                <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
	             </div>                       
	          </div>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label"></label>
	      <div class="col-lg-9">
	         <h3 style="margin-top: 15px" > <strong>Лимиты</strong> </h3>
	         <small>Лимиты на массовую выплату средств устанавливает платежная система. Безопасная сделка не будет действовать если сумма товара будет меньше <?php echo $getPayment["secure_min_amount_payment"]; ?> или больше <?php echo $getPayment["secure_max_amount_payment"]; ?></small>
	      </div>
	   </div>           

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label">Минимальная сумма безопасной сделки</label>
	      <div class="col-lg-2">

	          <div class="input-group mb-2">
	             <input type="text" class="form-control" name="secure_min_amount_payment" value="<?php echo $getPayment["secure_min_amount_payment"]; ?>" >
	             <div class="input-group-prepend">
	                <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
	             </div>                       
	          </div>

	      </div>
	   </div>

	   <div class="form-group row d-flex align-items-center mb-5">
	      <label class="col-lg-3 form-control-label">Максимальная сумма безопасной сделки</label>
	      <div class="col-lg-2">

	          <div class="input-group mb-2">
	             <input type="text" class="form-control" name="secure_max_amount_payment" value="<?php echo $getPayment["secure_max_amount_payment"]; ?>" >
	             <div class="input-group-prepend">
	                <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
	             </div>                       
	          </div>

	      </div>
	   </div>

	<?php

}  
?>