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
            "NAME" => GetMessage("IMPEL_LOC_CACHE_TIME"),
        ),
        "PROPERTY_CODE" => array(
            "NAME" => GetMessage("IMPEL_LOC_PROPERTY_CODE"),
        ),
        "PROPERTY_VALUE" => array(
            "NAME" => GetMessage("IMPEL_LOC_PROPERTY_VALUE"),
        ),
		"PROPERTY_ZIP_CODE" => array(
            "NAME" => GetMessage("IMPEL_LOC_PROPERTY_ZIP_CODE"),
        ), 
		"LIMIT" => array(
            "NAME" => GetMessage("IMPEL_LOC_LIMIT"),
        ),
		"ACTION" => array(
            "NAME" => GetMessage("IMPEL_LOC_ACTION"),
        ),
		"CLASSES" => array(
            "NAME" => GetMessage("IMPEL_LOC_CLASSES"),
        ),
		"AREA_ID" => array(
            "NAME" => GetMessage("IMPEL_LOC_AREA_ID"),
			"DEFAULT" => 1,
        ),
    )
);
?>