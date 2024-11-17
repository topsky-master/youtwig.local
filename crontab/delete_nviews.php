<?php

//https://youtwig.ru/local/crontab/delete_nviews.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

class impelDelDoubleViews{

    private static $maxCount = 200;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $viewEl = new CIBlockElement;


        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delviews_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delviews_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delviews_last.txt'));
		$skip = $skip == 0 ? 1 : $skip;

        if($skip == 0){
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/delviews.csv','w+');
        } else {
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/delviews.csv','a+');
        }

        $vFound = 0;

        $aVSelect = Array(
            "ID",
            "NAME"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "ACTIVE" => "N"
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

        if($rViews) {

            while ($aViews = $rViews->GetNext()) {

                $vFound = $aViews['ID'];


                fputcsv($fp,array($aViews['ID'],$aViews['NAME']),';');
                $viewEl->Delete($aViews['ID']);

            }
        }

        fclose($fp);

        return $vFound ? ++$skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delviews_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="0;url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/delete_nviews.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delviews_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelDelDoubleViews::getList();