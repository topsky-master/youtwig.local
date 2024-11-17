<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<? require_once($_SERVER['DOCUMENT_ROOT'].'/include/personal/header.php'); ?>
<?$APPLICATION->IncludeComponent("bitrix:support.ticket", "main-sup", array(
	"SEF_MODE" => "N",
	"SEF_FOLDER" => "/personal/support/",
	"TICKETS_PER_PAGE" => "50",
	"MESSAGES_PER_PAGE" => "20",
	"MESSAGE_MAX_LENGTH" => "70",
	"MESSAGE_SORT_ORDER" => "asc",
	"SET_PAGE_TITLE" => "Y",
	"SHOW_COUPON_FIELD" => "N",
	"SET_SHOW_USER_FIELD" => array(
	),
	"SEF_URL_TEMPLATES" => array(
		"ticket_list" => "index.php",
		"ticket_edit" => "#ID#.php",
	)
	),
	false
);?> 
<? require_once($_SERVER['DOCUMENT_ROOT'].'/include/personal/footer.php'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>