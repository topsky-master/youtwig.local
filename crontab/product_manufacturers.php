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

class impelCatalogMan{

    private static $countStrings = 200;
    private static $namesFile = 'set_mprops_last.txt';

    public static function checkProps(){
        $skip = static::getNextProps();
        static::getRedirect($skip);
    }

    private static function getNextProps($sProp = 'NAME'){

        $cElt = new CIBlockElement;
        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile));

        $arProductSelect = Array(
            "ID"
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE" => "Y"
        );

        if(!empty($skip)){

            $arProductFilter['>ID'] = $skip;

        } else {

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

                $acompatibility = static::getProperty('MANUFACTURER',$arProduct['ID']);
                $amanufacturers = static::getProperty('MANUFACTURER_DETAIL',$arProduct['ID']);
                $acompatibility = array_diff($acompatibility,$amanufacturers);

                if(!empty($acompatibility)){

                    $aMan = array();
                    $aMan['MANUFACTURER'] = array_keys($acompatibility);

                    CIBlockElement::SetPropertyValuesEx($arProduct['ID'], 11, $aMan);
                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arProduct['ID']);
                }

            }

        }

        fclose($fp);

        return $pLastPropId;

    }

    private static function getProperty($propCode, $prodId){

        $resmDB = CIBlockElement::GetProperty(
                11,
                $prodId,
                array(),
                ($armFilter = array('CODE' => $propCode))
        );

        $manufacturers = array();

        if ($resmDB) {

            while ($aMan = $resmDB->GetNext()) {

                if (isset($aMan['VALUE'])
                    && !empty($aMan['VALUE'])
                ) {

                    $manufacturers[$aMan['VALUE']] = $aMan['VALUE_ENUM'];

                }

            }

        }

        return $manufacturers;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile, $skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/product_manufacturers.php?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']).'&time='.time().'&PageSpeed=off";},'.mt_rand(5,30).');</script></header></html>');
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/'.static::$namesFile, 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelCatalogMan::checkProps();