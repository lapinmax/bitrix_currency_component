<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
for($i = 0; $i < count($arResult["CURRENCY"]); $i++):?>
<p>Курс валюты, на сегодня:</p><br>
<p><?=$arResult["CURRENCY"][$i]["FROM"]?> = <?=$arResult["CURRENCY"][$i]["BASE"]?></p>
<?endfor;?>
