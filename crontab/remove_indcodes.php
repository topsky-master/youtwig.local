<?php

//https://youtwig.ru/local/crontab/remove_indcodes.php?intestwetrust=1

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

class impelModels{

    private static $maxCount = 200000;
    private static $aSkip = array();
    private static $aFoundNames = array();
    private static $aFoundModels = array();
    private static $asFoundModels = array();


    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $modelEl = new CIBlockElement;

        $dIndcode = CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => 35,
                ">DATE_CREATE" => ConvertTimeStamp(time()-86400 * 3, "FULL"),
                'ACTIVE' => 'Y'
            ),
            false,
            false,
            array(
                'ID',
                'NAME')
        );

        if ($dIndcode) {

            while ($aIdcode = $dIndcode->GetNext()) {

                if (isset($aIdcode['NAME'])
                    && isset($aIdcode['ID'])) {

                    $aModelSelect = array(
                        'ID');
                    $aModelFilter = array();
                    $aModelFilter["IBLOCK_ID"] = 17;
                    $aModelFilter["PROPERTY_INDCODE"] = $aIdcode['ID'];

                    $resModel = CIBlockElement::GetList(
                        ($aOrder = Array('ID' => 'ASC')),
                        $aModelFilter,
                        false,
                        false,
                        $aModelSelect
                    );

                    $modelId = array();

                    if ($resModel) {

                        $aModel = $resModel->GetNext();

                        if(isset($aModel['ID'])){

                            $aModelId = $aModel['ID'];

                            if(isset($aModelId)){
                                $modelId[] = $aModelId;
                            }

                        }

                    }

                    if(empty($modelId)){
                        $modelEl->Update($aIdcode['ID'],Array('ACTIVE' => 'N'));
                    }

                }

            }

        }

        return 0;

    }

    private static function getPropValues($sCode, $foundModel){


        $arModelProductsFilter = Array("CODE" => $sCode);
        $aProps = array();

        $resProductsModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelProductsFilter);

        if ($resProductsModelDB) {

            while ($productsModelFields = $resProductsModelDB->GetNext()) {

                if (isset($productsModelFields['VALUE'])
                    && !empty($productsModelFields['VALUE'])
                ) {

                    $aProps[] = $productsModelFields['VALUE'];

                }

            }

        }

        return $aProps;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/remove_indcodes.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelModels::getList();