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
            "NAME" => GetMessage("IMPEL_CSVMAP_CACHE_TIME"),
		),
        "BLOCK_TITLE" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_CSVMAP_BLOCK_TITLE"),
        ),
		"AREA_ID" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_CSVMAP_AREA_ID"),
        ),
    )
);
?>