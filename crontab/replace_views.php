<?php

//https://youtwig.ru/local/crontab/replace_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

class impelGetDoubleViews{

    private static $maxCount = 1;
    private static $rFp = false;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $skip = 0;

        $aVSelect = Array(
            "ID",
            "PROPERTY_VIEW"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_VIEW" => "0"
        );

        $aVNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => 1,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );


        $rViews = CIBlockElement::GetList(
            Array(
            ),
            $aVFilter,
            false,
            $aVNavParams,
            $aVSelect);

        $foundModel = 0;
        $modelEl = new CIBlockElement;

        while($aViews = $rViews->GetNext()){

            $rProp = CIBlockElement::GetProperty(17,$aViews['ID'],Array(),Array('CODE' => 'VIEW'));
            $aView = array();

            $foundModel = $aViews['ID'];
            $toBaseProducts = array();

            if($rProp){
                while($aProp = $rProp->GetNext()){

                    $viewId = $aProp['VALUE'] == 0 ? 241718 : $aProp['VALUE'];
                    $toBaseProducts['VIEW'][] = array('VALUE' => $viewId, 'DESCRIPTION' => '');

                }

            }

            CIBlockElement::SetPropertyValuesEx($foundModel,17,$toBaseProducts);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);
			
            $updModel = Array('TIMESTAMP_X' => true);

            if ($modelEl->Update($foundModel, $updModel)) {

            }

        }

        return $foundModel ? ++$skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){


            die('<html><head><meta HTTP-EQUIV="refresh" content="3;url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/replace_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {


            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDoubleViews::getList();