<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->IncludeComponent(
	"impel:support.ticket.list", 
	"chargeback", 
	Array(
		"TICKET_EDIT_TEMPLATE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["ticket_edit"],
		"TICKETS_PER_PAGE" => $arParams["TICKETS_PER_PAGE"],
		"SET_PAGE_TITLE" => $arParams["SET_PAGE_TITLE"],
		"TICKET_ID_VARIABLE" => $arResult["ALIASES"]["ID"],
		"AJAX_ID" => $arParams["AJAX_ID"],
		"CATEGORY" => $arParams["CATEGORY"]
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);


?>