<?php

$form = '
<form action="https://yoomoney.ru/quickpay/confirm.xml" method="post" class="form-pay" >		
	<input type="hidden" name="sum" value="'.number_format($paramForm["amount"], 2, ".", "").'" />
	<input type="hidden" name="receiver" value="'.$param["wallet_number"].'">
	<input type="hidden" name="label" value="'.$paramForm["id_order"].'">
	<input type="hidden" name="quickpay-form" value="donate">
	<input type="hidden" name="targets" value="'.$paramForm["title"].'">
	<input type="hidden" name="need-fio" value="false">
	<input type="hidden" name="need-email" value="false">
	<input type="hidden" name="need-phone" value="false">
	<input type="hidden" name="need-address" value="false">
	<input type="hidden" name="successURL" value="'.$paramForm["link_success"].'">
	<input type="hidden" name="paymentType" value="PC" />
	<input type="submit" name="pay" class="pay-trans" value="">
</form>
';

return ["form"=>$form];

?>

