<?php

//https://youtwig.ru/local/crontab/remove_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

class impelDelEmpty{

    private static $maxCount = 20;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delempty_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delempty_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delempty_last.txt'));
		$skip = $skip == 0 ? 1 : $skip;
        $vFound = 0;

        if(empty($skip)){
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/delempty.csv','w+');
        } else {
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/delempty.csv','a+');
        }

        $views = file(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getempty.csv');
        $views = array_map('trim',$views);
        $views = array_unique($views);
        $views = array_values($views);

        $aVSelect = Array(
            "ID",
            "NAME",
            "PREVIEW_PICTURE"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
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

                if(!in_array($vFound,$views)){

                    $path = '';

                    if(isset($aViews['PREVIEW_PICTURE'])
                        && !empty($aViews['PREVIEW_PICTURE'])
                    ){

                        if(is_numeric($aViews['PREVIEW_PICTURE'])){
                            $path = CFile::getPath($aViews['PREVIEW_PICTURE']);
                        } else {
                            $path = isset($aViews['PREVIEW_PICTURE']['SRC'])
                                ? $aViews['PREVIEW_PICTURE']['SRC']
                                : $aViews['PREVIEW_PICTURE'];
                        }

                        $sfName = pathinfo($path,PATHINFO_BASENAME);

                        if($sfName && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path)){
                            copy($_SERVER['DOCUMENT_ROOT'].'/'.$path,$_SERVER['DOCUMENT_ROOT'].'/upload/views/'.$sfName);
                            $path = '/upload/views/'.$sfName;
                        } else {
                            $path = '';
                        }
                    }

                    fputcsv($fp,array($aViews['ID'],$aViews['NAME'],$path),';');
                    CIBlockElement::Delete($vFound);

                }
            }

        }

        fclose($fp);

        return $vFound ? ++$skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delempty_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/remove_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_delempty_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelDelEmpty::getList();