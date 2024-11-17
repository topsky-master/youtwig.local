<?php

//https://youtwig.ru/local/crontab/get_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

class impelGetEmpty{

    private static $maxCount = 20;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt'));
        $vFound = 0;

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty.csv', 'w+');

        }

        $aVSelect = Array(
            "ID",
            "NAME"
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
                $mViews = static::getModelView($vFound);
                $mViews = array_unique($mViews);
                $mViews = array_filter($mViews);
                $mViews = array_values($mViews);

                foreach($mViews as $mView){
                    fputcsv($fp,array($mView),';');
                }
            }

        }

        fclose($fp);

        return $vFound ? ++$skip : 0;

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

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/get_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetEmpty::getList();