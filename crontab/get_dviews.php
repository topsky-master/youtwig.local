<?php

//https://youtwig.ru/local/crontab/get_dviews.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

class impelGetDViews{

    private static $maxCount = 20;
    private static $aVNames = array();
    private static $aNames = array();

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt'));
        $vFound = 0;

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews.csv', 'w+');

        }

        $aVSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT",
            "PROPERTY_model_new_link"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 17
        );

        $aVNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => $skip,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );

        $rViews = CIBlockElement::GetList(
            Array(
                'ID' => 'DESC'
            ),
            $aVFilter,
            false,
            $aVNavParams,
            $aVSelect);

        if($rViews){

            while($aViews = $rViews->GetNext()){

                $vFound = $aViews['ID'];
                $amViews = static::getModelView($vFound);
                $amViews = array_unique($amViews);
                $amViews = array_filter($amViews);
                $amViews = array_values($amViews);

                foreach($amViews as $imView){

                    $sVName = static::getViewName($imView);

                    if($sVName == 'Без вида')
                        continue;

                    $sMName = static::getModelName($aViews['PROPERTY_MODEL_NEW_LINK_VALUE']);
                    fputcsv($fp,array($sMName,$sVName,$aViews['PROPERTY_MANUFACTURER_VALUE'],$aViews['PROPERTY_TYPE_OF_PRODUCT_VALUE']),';');
                }
            }

        }

        fclose($fp);

        return $vFound ? ++$skip : 0;

    }

    private static function getViewName($ivId){

        $vName = '';

        if(!isset(static::$aVNames[$ivId])){

            $rView = CIBlockElement::GetByID($ivId);

            if($rView
                && $aView = $rView->GetNext()){

                if(isset($aView['NAME'])
                    && !empty($aView['NAME'])){

                    $vName = $aView['NAME'];

                    static::$aVNames[$ivId] = $vName;


                }

            }

        } else {
            $vName = static::$aVNames[$ivId];
        }

        return $vName;

    }

    private static function getModelName($imId){

        $sName = '';

        if(!isset(static::$aNames[$imId])){
            $rMod = CIBlockElement::GetByID($imId);

            if($rMod
                && $aMod = $rMod->GetNext()){

                if(isset($aMod['NAME'])
                    && !empty($aMod['NAME'])){

                    $sModel = $aMod['NAME'];
                    $sName = static::$aNames[$imId] = $sModel;

                }

            }

        } else {
            $sName = static::$aNames[$imId];
        }

        return $sName;

    }

    private static function getModelView($modelId)
    {
        $aViews = array();

        $dMView = CIBlockElement::GetProperty(
            17,
            $modelId,
            array(),
            array("CODE" => "VIEW"));

        if($dMView){

            while($aMView = $dMView->GetNext()){


                if(!empty($aMView['VALUE'])){

                    $aViews[$aMView['VALUE']] = $aMView['VALUE'];

                }

            }

        }

        return $aViews;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/get_dviews.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdviews_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDViews::getList();