#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$arSelect = Array(
    "ID",
    "PREVIEW_TEXT",
    "DETAIL_TEXT"
);

$arFilter = Array(
    "IBLOCK_ID" => 11,
    "ACTIVE" => "Y"
);


$productEl = new CIBlockElement;

$dbRes = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

if($dbRes){
    while($arItem = $dbRes->GetNext()){


        if($arItem['PREVIEW_TEXT_TYPE'] == 'html'){
            $arItem["PREVIEW_TEXT"] = function_exists('tidy_repair_string')
                ? tidy_repair_string($arItem["PREVIEW_TEXT"], array('show-body-only' => true), "utf8")
                : $arItem["PREVIEW_TEXT"];

        } else {

            $arItem["PREVIEW_TEXT"] = trim(strip_tags(html_entity_decode($arItem["PREVIEW_TEXT"],ENT_QUOTES,LANG_CHARSET)));

        }


        if($arItem['DETAIL_TEXT_TYPE'] == 'html'){

            $arItem["DETAIL_TEXT"] = function_exists('tidy_repair_string')
                ? tidy_repair_string($arItem["DETAIL_TEXT"], array('show-body-only' => true), "utf8")
                : $arItem["DETAIL_TEXT"];

        } else {

            $arItem["DETAIL_TEXT"] = trim(strip_tags($arItem["DETAIL_TEXT"]));

        }

        $update = array();

        if(!empty($arItem["PREVIEW_TEXT"])){
            $update["PREVIEW_TEXT"] = $arItem["PREVIEW_TEXT"];
        }

        if(!empty($arItem["DETAIL_TEXT"])){
            $update["DETAIL_TEXT"] = $arItem["DETAIL_TEXT"];
        }



        if(!empty($update)){
            $productEl->Update(
                $arItem['ID'],
                $update
            );
        }

    }

}


?>