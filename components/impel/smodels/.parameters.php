<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
	"GROUPS" => array(
    ),
    "PARAMETERS" => array(
		"CACHE_TIME" => array(
			"DEFAULT" => 36000000,
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("IMPEL_SMODELS_CACHE_TIME"),
		),
		"DETAIL_URL" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_SMODELS_DETAIL_URL"),
        ),
        "RESULTS_COUNT" => array(
			"DEFAULT" => "30",
            "NAME" => GetMessage("IMPEL_SMODELS_RESULTS_COUNT"),
        ),
		"AREA_ID" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_SMODELS_AREA_ID"),
        ),
    )
);
?>