<?php

//https://youtwig.ru/local/crontab/copy_images_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

    private static $maxCount = 100;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getimagesviews_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getimagesviews_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getimagesviews_last.txt'));
        $vFound = 0;

        $aVSelect = Array(
            "ID",
            "NAME",
            "PREVIEW_PICTURE",
            "IBLOCK_ID"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "ACTIVE" => "Y"
        );

        $aVNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => $skip,
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

                $vFound = $aViews['NAME'];

                if ($vFound != 'Без вида'
                    && !$aViews['PREVIEW_PICTURE']) {

                    $auViews = array();

                    static::copyViewImg($aViews['ID'],$vFound);

                }
            }
        }

        return $vFound ? ++$skip : 0;

    }

    private static function copyViewImg($ivId,$svName){

        $viewEl = new CIBlockElement;

        $aVSelect = Array(
            "ID",
            "PREVIEW_PICTURE",
            "NAME",
            "IBLOCK_ID"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "!PREVIEW_PICTURE" => false,
            "ACTIVE" => "N",
            "=NAME" => $svName
        );


        $rViews = CIBlockElement::GetList(
            Array(
            ),
            $aVFilter,
            false,
            false,
            $aVSelect);

        if($rViews) {

            while ($aViews = $rViews->GetNext()) {

                if($aViews
                    && $aViews['PREVIEW_PICTURE']){

                    if(is_numeric($aViews['PREVIEW_PICTURE'])){
                        $aViews['PREVIEW_PICTURE'] = CFile::GetPath($aViews['PREVIEW_PICTURE']);
                    }

                    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$aViews['PREVIEW_PICTURE'])){


                        $viewProperties = array();
                        $viewProperties['PREVIEW_PICTURE'] = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.$aViews['PREVIEW_PICTURE']);
                        $viewProperties['TIMESTAMP_X'] = true;

                        if ($viewEl->Update($ivId, $viewProperties)) {

                        } else {
                            if (isset($viewEl->LAST_ERROR)) {
                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/views_log.txt', 'Model IB 34: ' . trim($viewProperties['PREVIEW_PICTURE']) . ',' . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                            }
                        }

                    }

                }

            }
        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getimagesviews_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="0;url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/copy_images_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getimagesviews_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDoubleViews::getList();