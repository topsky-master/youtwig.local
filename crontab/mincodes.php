<?php

//https://youtwig.ru/local/crontab/mincodes.php?intestwetrust=1

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
    private static $flAvail = '/bitrix/tmp/lockavail';
    private static $fcsvFifle = '/bitrix/tmp/beko_upd.csv';

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        static::$flAvail = dirname(dirname(__DIR__)).static::$flAvail;
        static::$fcsvFifle = dirname(dirname(__DIR__)).static::$fcsvFifle;

        if(file_exists(static::$flAvail)
            && ((filemtime(static::$flAvail) + 1200) < time())){
            unlink(static::$flAvail);
        }

        if(file_exists(static::$flAvail) || !file_exists(static::$fcsvFifle)) die();

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode_last.txt'));

        $aIndcodeSelect = Array(
            "ID",
            "NAME"
        );

        $aIndcodeFilter = Array(
            "IBLOCK_ID" => 35,
            "ACTIVE" => "Y"
        );

        $lines = file(static::$fcsvFifle);

        if($skip > 0){


            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode.csv', 'a+');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode.csv', 'w+');

        }

        $lines = array_slice($lines,$skip, static::$maxCount);

        $cFound = 0;

        if(sizeof($lines)){

            ++$cFound;

            $lines = array_map('trim',$lines);

            foreach($lines as $line){

                $aStr = str_getcsv($line,';');
                $aStr = array_map(function($val){
                    $val = trim($val,';');
                    $val = trim($val);
                    return $val;},$aStr);


                ++$skip;


                if(empty($aStr[1]))
                    continue;

                $aIndcodeFilter['=NAME'] = $aStr[2];

                $products_id = $aStr[1];
                $products_id = explode(';',$products_id);
                $products_id = !empty($products_id) && !is_array($products_id)
                    ? array($products_id)
                    : $products_id;

                $products_id = array_filter($products_id);
                $products_id = array_unique($products_id);

                $resIndcode = CIBlockElement::GetList(
                    ($aOrder = Array('ID' => 'ASC')),
                    $aIndcodeFilter,
                    false,
                    false,
                    $aIndcodeSelect
                );

                if($resIndcode){

                    while($aIndcode = $resIndcode->GetNext()){


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
                            "PROPERTY_SIMPLEREPLACE_INDCODE" => $aIndcode['ID']
                        );

                        $nModel = impelCIBlockElement::GetList(
                            ($order = Array()),
                            $amFilter,
                            array(),
                            false,
                            $amSelect
                        );

                        if($nModel == 1 || true){

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

                                        foreach($products_id as $product_id) {

                                            fputcsv($fp,array(
                                                $aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                                $aModel['PROPERTY_MANUFACTURER_VALUE'],
                                                $model,
                                                $product_id,
                                                '',
                                                $aIndcode['NAME'],
                                                '',
                                                '',
                                                '',
                                                ''
                                            ),';');

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

        fclose($fp);

        return $cFound ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(file_exists(static::$flAvail))
            unlink(static::$flAvail);

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/mincodes.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            CEvent::SendImmediate('PARSE_MODELS', SITE_ID, array('TIME' => (date('Y.m.d H:i:s').' get mincodes')));
            unlink(static::$fcsvFifle);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_mincode_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelIndcodes::getList();