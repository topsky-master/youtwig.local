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
        "FILTER_H1" => array(
			"DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_SECTIONTITLE_FILTER_H1"),
        ),


    )
);
?>