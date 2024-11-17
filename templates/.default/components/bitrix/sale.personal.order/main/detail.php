<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arDetParams = array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
		"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ID" => $arResult["VARIABLES"]["ID"]
	);
foreach($arParams as $key => $val)
{
	if(mb_strpos($key, "PROP_") !== false)
		$arDetParams[$key] = $val;
}
$APPLICATION->IncludeComponent(
	"impel:sale.personal.order.detail",
	"",
	$arDetParams,
	$component
);
?>
