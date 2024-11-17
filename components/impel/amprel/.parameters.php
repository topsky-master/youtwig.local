<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main;
use Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Iblock;

if(!Loader::includeModule('iblock')){
    return ;
}

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
        "AMP_DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
            "DETAIL",
            "DETAIL_URL",
            GetMessage("IMPEL_AMPREL_IBLOCK_DETAIL_URL"),
            "#SECTION_ID#/#ELEMENT_ID#/",
            "URL_TEMPLATES"
        ),
        "AMP_SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
            "SECTION",
            "SECTION_URL",
            GetMessage("CP_BCSL_SECTION_URL"),
            "#SECTION_ID#/",
            "URL_TEMPLATES"
        ),
        "AMP_SECTIONS_URL" => array(
            "NAME" => GetMessage("IMPEL_SECTIONS_TITLE"),
			"DEFAULT" => SITE_DIR."amp/sections/"
        )

    )
);


?>