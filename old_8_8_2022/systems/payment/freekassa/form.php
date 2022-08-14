<?php

$sign = md5($param["id_shop"].':'.$paramForm["amount"].':'.$param["secret_word1"].':'.$param["currency"].':'.$paramForm["id_order"]);

$html = "
<form method='get' class='form-pay' action='https://pay.freekassa.ru/'>
    <input type='hidden' name='m' value='".$param["id_shop"]."'>
    <input type='hidden' name='oa' value='".$paramForm["amount"]."'>
    <input type='hidden' name='o' value='".$paramForm["id_order"]."'>
    <input type='hidden' name='s' value='".$sign."'>
    <input type='hidden' name='currency' value='".$param["currency"]."'>
    <input type='submit' name='pay' class='pay-trans' value='Оплатить'>
</form>
";

return ["form"=>$html];

?>