<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<? require_once($_SERVER['DOCUMENT_ROOT'].'/include/personal/header.php'); ?>
<?$APPLICATION->IncludeComponent(
	"bitrix:support.ticket", 
	"chargeback", 
	array(
		"CATEGORY" => "41",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"MESSAGES_PER_PAGE" => "20",
		"MESSAGE_MAX_LENGTH" => "70",
		"MESSAGE_SORT_ORDER" => "asc",
		"SEF_FOLDER" => "/personal/chargeback/",
		"SEF_MODE" => "N",
		"SET_PAGE_TITLE" => "Y",
		"SET_SHOW_USER_FIELD" => array(
			"TITLE",
			"UF_SPHONE",
			"UF_SPCHT",
			"UF_SONUM",
			"UF_SPNAME",
			"UF_SMOD",
			"UF_SREAS",
			"MESSAGE",
			"UF_STSP",
			"UF_SPREF"

		),
		"SHOW_COUPON_FIELD" => "N",
		"TICKETS_PER_PAGE" => "50",
		"TITLE" => "Возврат / Обмен",
		"DESCRIPTION" => "Если вы столкнулись с проблемой, пожалуйста, отправьте нам заявку заполнив поля ниже.nЗаявка будет рассмотрена в течении 1-2 дней.",
		"RULES_ID" => "104720",
		"REQUIRED_FIELDS" => "TITLE,MESSAGE,UF_SPHONE,UF_SONUM,UF_SPNAME,UF_SMOD,UF_SREAS",
		"COMPONENT_TEMPLATE" => "chargeback",
		"VARIABLE_ALIASES" => array(
			"ID" => "ID",
		)
	),
	false
);?>
<? require_once($_SERVER['DOCUMENT_ROOT'].'/include/personal/footer.php'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>