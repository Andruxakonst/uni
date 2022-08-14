<?php

$amount = number_format($paramForm["amount"], 2, ".", "");

$m_shop = $param["id_merchant"];
$m_orderid = $paramForm["id_order"];
$m_amount = $amount;
$m_curr = $param["curr"];
$m_desc = base64_encode($paramForm["title"]);
$m_key = $param["secret_key"];

$arHash = array(
   $m_shop,
   $m_orderid,
   $m_amount,
   $m_curr,
   $m_desc
);


$arParams = array(
   'success_url' => $paramForm["link_success"] ? $paramForm["link_success"] : $param["link_success"],
   'fail_url' => $param["link_cancel"],
   'status_url' => $config["urlPath"] . "/systems/payment/payeer/callback.php",   
);

$key = md5($param["secret_key_parameters"].$m_orderid);

$m_params = @urlencode(base64_encode(openssl_encrypt(json_encode($arParams), 'AES-256-CBC', $key, OPENSSL_RAW_DATA)));

$arHash[] = $m_params;

$arHash[] = $m_key;

$sign = strtoupper(hash('sha256', implode(':', $arHash)));

$html = '
<form method="post" action="https://payeer.com/merchant/" class="form-pay" >
<input type="hidden" name="m_shop" value="'.$m_shop.'">
<input type="hidden" name="m_orderid" value="'.$m_orderid.'">
<input type="hidden" name="m_amount" value="'.$m_amount.'">
<input type="hidden" name="m_curr" value="'.$m_curr.'">
<input type="hidden" name="m_desc" value="'.$m_desc.'">
<input type="hidden" name="m_sign" value="'.$sign.'">
<input type="hidden" name="m_params" value="'.$m_params.'">
<input type="hidden" name="m_cipher_method" value="AES-256-CBC">
<input type="submit" name="m_process" value="send" class="pay-trans" />
</form>
';

return ["form"=>$html];
?>