<?php

//https://youtwig.ru/local/crontab/indcodes.php?intestwetrust=1

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

class impelIndcodes{

    private static $countStrings = 3;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        $modelEl = new impelCIBlockElement;

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_last.txt'));

        $aIndcodeSelect = Array(
            "ID"
        );

        $aIndcodeFilter = Array(
            "IBLOCK_ID" => 35,
            "ACTIVE" => "Y"
        );

        if($skip > 0){


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode.csv', '');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_wc.csv', '');

        }

        $codes = file(dirname(dirname(__DIR__)).'/bitrix/tmp/codes.csv');
        $codes = array_map('trim',$codes);

        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode.csv','a+');
        $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_wc.csv','a+');

        $skip = empty($skip) ? 0 : $skip;
        $countStrings = 0;
        $codes = array_slice($codes,$skip * static::$countStrings,static::$countStrings);


        foreach($codes as $code) {

            $aIndcodeFilter['=NAME'] = $code;

            $resIndcode = impelCIBlockElement::GetList(
                ($order = Array()),
                $aIndcodeFilter,
                false,
                false,
                $aIndcodeSelect
            );

            $cFound = false;

            if($resIndcode){

                while($aIndcode = $resIndcode->GetNext()){

                    $cFound = true;
                    $modelLastPropId = $aIndcode['ID'];

                    $amSelect = Array(
                        "ID",
                        "PROPERTY_model_new_link",
                        "PROPERTY_type_of_product",
                        "PROPERTY_manufacturer"
                    );

                    $amFilter = Array(
                        "IBLOCK_ID" => 17,
                        "ACTIVE" => "Y",
                        "PROPERTY_SIMPLEREPLACE_INDCODE" => $modelLastPropId
                    );

                    $rModel = impelCIBlockElement::GetList(
                        ($order = Array()),
                        $amFilter,
                        false,
                        false,
                        $amSelect
                    );


                    if($rModel){

                        while($aModel = $rModel->GetNext()){

                            $model = '';

                            if(isset($aModel['PROPERTY_MODEL_NEW_LINK_VALUE'])
                                && !empty($aModel['PROPERTY_MODEL_NEW_LINK_VALUE'])) {

                                $rnModel = impelCIBlockElement::GetByID($aModel['PROPERTY_MODEL_NEW_LINK_VALUE']);

                                if($rnModel){
                                    $anModel = $rnModel->GetNext();

                                    if(isset($anModel['NAME'])
                                        && !empty($anModel['NAME'])) {

                                        $model = trim($anModel['NAME']);

                                    }
                                }

                            }

                            if(!empty($model)
                                && isset($aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                && isset($aModel['PROPERTY_MANUFACTURER_VALUE'])
                                && !empty($aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                && !empty($aModel['PROPERTY_MANUFACTURER_VALUE'])
                            ) {


                                fputcsv($fp,array(
                                    $aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                    $aModel['PROPERTY_MANUFACTURER_VALUE'],
                                    $model,
                                    $code
                                ),';');
                            }

                        }

                    }

                }

            }

            if(!$cFound) {
                fputcsv($fp1,array($code));
            }

        }

        fclose($fp);
        fclose($fp1);
        ++$skip;

        echo $skip.'-'.sizeof($codes);

        return sizeof($codes) ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/indcodes.php?intestwetrust=1&time='.time().'" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_indcode_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelIndcodes::getList();