<?php

//https://youtwig.ru/local/crontab/what_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

        $skip = $foundModel = 0;

        $array = array(10172,4423,111024);

        foreach($array as $product_id){

            $aVSelect = Array(
                "ID",
                "PROPERTY_VIEW"
            );

            $aVFilter = Array(
                "IBLOCK_ID" => 17,
                "PROPERTY_PRODUCTS" => $product_id,
                "!PROPERTY_VIEW" => false
            );

            $rViews = CIBlockElement::GetList(
                Array(
                ),
                $aVFilter,
                false,
                false,
                $aVSelect);



            while($aViews = $rViews->GetNext()){

                $rDb = CIBlockElement::GetProperty(17,$aViews['ID'],Array(),Array("VIEW"));
                $foundModel = $aViews['ID'];

                $arrs = array();

                $notDef = 0;
                $defCount = 0;

                if($rDb)
                while($aDb = $rDb->GetNext()){

                    $arrs[] = $aDb['VALUE'];

                    if($aDb['VALUE'] != 241718){
                        ++$notDef;
                    } else {
                        if($aDb['VALUE'] == 0 || $aDb['VALUE'] == 241718)
                            ++$defCount;
                    }

                }

                if(!empty($arrs)
                    && (in_array(241718,$arrs) || in_array(0,$arrs))
                    && $notDef
                ){
                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/fcsv.csv',$aViews['ID'].';'.$product_id."\n",FILE_APPEND);
                }

                if(!empty($arrs)
                    && (in_array(241718,$arrs) || in_array(0,$arrs))
                    && $notDef
                    && $defCount > 1
                ){
                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/fcsv1.csv',$aViews['ID'].';'.$product_id."\n",FILE_APPEND);
                }

            }

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/fcsv.txt',$product_id."\n",FILE_APPEND);

        }

        return $foundModel ? ++$skip : 0;

    }

    private static function getRedirect($skip = ''){

        echo 'done';
        die();

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDoubleViews::getList();