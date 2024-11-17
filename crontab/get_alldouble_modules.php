<?php

//https://youtwig.ru/local/crontab/get_alldouble_modules.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetdoublesall{

    private static $maxCount = 20;
    private static $rdFp = false;
    private static $rFp = false;

    private static $aNames = array();
    private static $aCodes = array();

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt'));
        $mFound = 0;

        if($skip > 0){

            static::$rFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt', 0);
            static::$rFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall.csv', 'w+');

        }

        $aMSelect = Array(
            "ID"
        );

        $aMFilter = Array(
            "IBLOCK_ID" => 17
        );

        $aMNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => $skip,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );

        $rModels = CIBlockElement::GetList(
            Array(
                'CNT' => 'DESC'
            ),
            $aMFilter,
            Array('PROPERTY_model_new_link'),
            $aMNavParams,
            $aMSelect);

        if($rModels) {

            while ($aModels = $rModels->GetNext()) {

                $mFound = $aModels['PROPERTY_MODEL_NEW_LINK_VALUE'];

                if ($aModels['CNT'] > 1) {

                    static::getModelName($mFound);

                    $auModels = array(
                        'NAME' => static::$aNames[$mFound],
                        'VALUES' => array()
                    );

                    static::getModel($mFound, $auModels);
                }
            }
        }

        fclose(static::$rFp);

        return $mFound ? ++$skip : 0;

    }

    private static function getModelName($imId){

        if(!isset(static::$aNames[$imId])){
            $rMod = CIBlockElement::GetByID($imId);

            if($rMod
                && $aMod = $rMod->GetNext()){

                if(isset($aMod['NAME'])
                    && !empty($aMod['NAME'])){

                    $sModel = $aMod['NAME'];

                    static::$aNames[$imId] = $sModel;


                }

            }

        }



    }

    private static function getModel($modelId,&$auModels)
    {
        $aMSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT"
        );

        //MANUFACTURER
        //TYPE_OF_PRODUCT

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_model_new_link" => $modelId,
            "ACTIVE" => "Y"
        );

        $rModels = CIBlockElement::GetList(
            Array(
                'PROPERTY_PRODUCTS_REMOVED' => 'DESC',
                'PROPERTY_MANUFACTURER' => 'DESC'
            ),
            $aMFilter,
            false,
            false,
            $aMSelect);

        if($rModels){

            while($aModels = $rModels->GetNext()){
                $auModels['VALUES'][$aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$aModels['PROPERTY_MANUFACTURER_VALUE']][$aModels['ID']] = $aModels['NAME'];
            }

        }

        static::toCsv($auModels);

    }

    private static function toCsv($auModels){

        if (!empty($auModels['VALUES'])) {
            foreach ($auModels['VALUES'] as $product_type => $manufacutres) {

                foreach ($manufacutres as $manufacutrer => $models) {

                    foreach ($models as $model_id => $model_name) {
                        fputcsv(static::$rFp, array($auModels['NAME'], $product_type, $manufacutrer, $model_name, $model_id, 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$model_id.'&find_section_section=0&WF=Y'), ';');
                    }
                }
            }
        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/get_alldouble_modules.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesall_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetdoublesall::getList();