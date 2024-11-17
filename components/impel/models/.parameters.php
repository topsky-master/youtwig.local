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
            "NAME" => GetMessage("IMPEL_MODELS_CACHE_TIME"),
		),
        "BLOCK_TITLE" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_MODELS_BLOCK_TITLE"),
        ),
		"BLOCK_COLUMNS" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_MODELS_BLOCK_COLUMNS"),
        ),
        "DETAIL_URL" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_MODELS_DETAIL_URL"),
        ),
        "RESULTS_COUNT" => array(
			"DEFAULT" => "30",
            "NAME" => GetMessage("IMPEL_MODELS_RESULTS_COUNT"),
        ),
		"AREA_ID" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_MODELS_AREA_ID"),
        ),
    )
);
?>