<?
il::define("STOP_STATISTICS", true);
il::define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/nmain/lang/'.LANGUAGE_ID.'/header.php');

global $APPLICATION;

 $APPLICATION->IncludeComponent(
    "impel:location",
    "",
    Array(
		"LIMIT" => 8
	)
); 