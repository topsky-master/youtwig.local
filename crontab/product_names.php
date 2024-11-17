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

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelCatalogNames{

    private static $countStrings = 200;
    private static $namesFile = 'set_names_last.txt';
    private static $namesCsvFile = 'set_names_last.csv';

    public static function checkNames($sProp){
        $skip = static::getNextNames($sProp);
        static::getRedirect($skip);
    }

    private static function getNextNames($sProp = 'NAME'){

        $cElt = new CIBlockElement;
        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile));

        $arProductSelect = Array(
            "ID",
            $sProp,
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE" => "Y",
            //$sProp => "%x%"
            //"PREVIEW_PICTURE" => false
        );

        if(!empty($skip)){

            $arProductFilter['>ID'] = $skip;
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesCsvFile,'a+');


        } else {

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesCsvFile,'w+');

        }

        $arNavStartParams = array("nTopCount" => static::$countStrings);

        $resProduct = CIBlockElement::GetList(
            ($order = Array('ID' => 'ASC')),
            $arProductFilter,
            false,
            $arNavStartParams,
            $arProductSelect
        );

        $pLastPropId = 0;

        if($resProduct){

            while($arProduct = $resProduct->GetNext()){




                    $pLastPropId = $arProduct['ID'];



                    $preview_text = htmlspecialchars_decode($arProduct['PREVIEW_TEXT']);
                    $preview_text = preg_replace('~<style[^>]*?>.*?</style>~isu','',$preview_text);
                    $preview_text = preg_replace('~<script[^>]*?>.*?</script>~isu','',$preview_text);
                    $preview_text = preg_replace('~<head[^>]*?>.*?</head>~isu','',$preview_text);
                    $preview_text = preg_replace('~<noscript[^>]*?>.*?</noscript>~isu','',$preview_text);
                    $preview_text = preg_replace('~<table[^>]*?>.*?</table>~isu','',$preview_text);
                    $preview_text = preg_replace('~(\s+)?(\s+)~isu',' ',$preview_text);
                    $preview_text = strip_tags($preview_text);
                    $preview_text = trim($preview_text);

                    fputcsv($fp,array($arProduct['ID'], $arProduct['PREVIEW_TEXT'],$preview_text),';');
                    $cElt->Update($arProduct['ID'],($aEltUpd = array('PREVIEW_TEXT' => $preview_text)));


            }

        }

        fclose($fp);

        return $pLastPropId;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile, $skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/product_names.php?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']).'&time='.time().'&PageSpeed=off";},'.mt_rand(5,30).');</script></header></html>');
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile, 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelCatalogNames::checkNames("PREVIEW_TEXT");