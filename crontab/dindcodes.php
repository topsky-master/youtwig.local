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

class impelIndcodes{

    private static $maxCount = 10;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){



        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode_last.txt'));

        $aIndcodeSelect = Array(
            "ID",
            "NAME"
        );

        $aIndcodeFilter = Array(
            "IBLOCK_ID" => 35,
            "ACTIVE" => "Y"
        );

        if($skip > 0){

            $aIndcodeFilter['>ID'] = $skip;
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode.csv', 'a+');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode.csv', 'w+');

        }

        $resIndcode = CIBlockElement::GetList(
            ($aOrder = Array('ID' => 'ASC')),
            $aIndcodeFilter,
            false,
            array('nTopCount' => static::$maxCount),
            $aIndcodeSelect
        );

        $cFound = 0;

        if($resIndcode){

            while($aIndcode = $resIndcode->GetNext()){

                $cFound = $aIndcode['ID'];

                if($aIndcode['NAME'] == 'Без кода')
                    continue;

                $amSelect = Array(
                    "ID",
                    "PROPERTY_model_new_link",
                    "PROPERTY_type_of_product",
                    "PROPERTY_manufacturer"
                );

                $amFilter = Array(
                    "IBLOCK_ID" => 17,
                    "ACTIVE" => "Y",
                    "PROPERTY_INDCODE" => $cFound
                );

                $nModel = CIBlockElement::GetList(
                    ($order = Array()),
                    $amFilter,
                    array(),
                    false,
                    $amSelect
                );

                if($nModel > 1){

                    $rModel = CIBlockElement::GetList(
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

                                $rnModel = CIBlockElement::GetByID($aModel['PROPERTY_MODEL_NEW_LINK_VALUE']);

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
                                    $aIndcode['NAME'],
                                    $aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                    $aModel['PROPERTY_MANUFACTURER_VALUE'],
                                    $model,
                                    (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$aModel['ID']
                                ),';');
                            }



                        }

                    }

                }

            }

        }

        fclose($fp);

        return $cFound ? $cFound : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/dindcodes.php?intestwetrust=1&time='.time().'" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_dindcode_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelIndcodes::getList();