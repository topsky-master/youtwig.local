<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/** @global CMain $APPLICATION */

$APPLICATION->RestartBuffer();

$GLOBALS["APPLICATION"]->IncludeComponent(
	"ipol:ipol.apiship2vPickup",
	"",
	array(
	),
	false
);