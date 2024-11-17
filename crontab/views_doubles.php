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

class impelViewDoubles{

    private static $maxCount = 20;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt'));
        $vFound = 0;

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles.csv', 'w+');

        }

        $aVSelect = Array(
            "ID",
            "NAME"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "!ID" => 241718
        );

        $aVNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => $skip,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );

        $rViews = CIBlockElement::GetList(
            Array(
                'NAME' => 'DESC',
                'ID' => 'DESC'
            ),
            $aVFilter,
            false,
            $aVNavParams,
            $aVSelect);

        if($rViews){

            while($aViews = $rViews->GetNext()){

                $vFound = $aViews['ID'];
                $sMans = static::getModelManufacturer($vFound);
                //$vCount = static::makeViewsByNameFile($aViews['NAME']);
                fputcsv($fp,array($aViews['ID'],$aViews['NAME'],join(',',$sMans)),';');

            }

        }

        fclose($fp);

        return $vFound ? ++$skip : 0;

    }

    private static function makeViewsByNameFile($vName){

        static $vNames;

        if(!isset($vNames[$vName])){

            $aVSelect = Array(
                "ID",
                "NAME"
            );

            $aVFilter = Array(
                "IBLOCK_ID" => 34,
                "=NAME" => $vName
            );

            $vFound = 0;
            $rViews = CIBlockElement::GetList(Array('CNT' => 'DESC'), $aVFilter, Array('NAME'), false, $aVSelect);

            if($rViews){
                while($aViews = $rViews->GetNext()){
                    $vFound = $aViews['CNT'];
                }
            }

            $vNames[$vName] = $vFound;

        } else {
            $vFound = $vNames[$vName];
        }


        return $vFound;
    }

    private static function getModelManufacturer($viewId)
    {
        $sMan = array();

        $aMSelect = Array(
            "ID",
            "PROPERTY_MANUFACTURER"
        );

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_VIEW" => $viewId
        );


        $rMan = CIBlockElement::GetList(
            Array(),
            $aMFilter,
            false,
            false,
            $aMSelect);

        if($rMan){
            while($aMan = $rMan->GetNext()){

                if(isset($aMan['PROPERTY_MANUFACTURER_VALUE'])
                    && !empty($aMan['PROPERTY_MANUFACTURER_VALUE'])){
                    $sMan[] = $aMan['PROPERTY_MANUFACTURER_VALUE'];
                }
            }
        }

        return $sMan;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/views_doubles.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_viewdoubles_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelViewDoubles::getList();