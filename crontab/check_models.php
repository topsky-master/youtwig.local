<?php

//https://youtwig.ru/local/crontab/check_models.php?intestwetrust=1

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
    define('MAX_COUNT',50000); //сколько строк обоработать за 1 раз
} else {
    define('MAX_COUNT',50);
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelIndcodes{

    private static $maxCount = MAX_COUNT;
    private static $flAvail = '/bitrix/tmp/lockcheck';
    private static $fcsvFifle = '/bitrix/tmp/models_check.csv';

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

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_models_last.txt'));

        $aIndcodeSelect = Array(
            "ID",
            "NAME"
        );

        $aIndcodeFilter = Array(
            "IBLOCK_ID" => 27,
            "ACTIVE" => "Y"
        );

        $lines = file(static::$fcsvFifle);

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/models_indcodes.csv', 'a+');
            $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/models_indcodes_notfound.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_indcodes_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/models_indcodes.csv', 'w+');
            $fp1 = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/models_indcodes_notfound.csv', 'w+');

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

                $aStr[3] = isset($aStr[3]) ? $aStr[3] : '';
                $aStr[4] = isset($aStr[4]) ? $aStr[4] : '';
                $aStr[5] = isset($aStr[5]) ? $aStr[5] : '';

                $aIndcodeFilter['=NAME'] = $aStr[1];
                $model = trim($aStr[1]);
                $resIndcode = CIBlockElement::GetList(
                    ($aOrder = Array('ID' => 'ASC')),
                    $aIndcodeFilter,
                    array(),
                    false,
                    $aIndcodeSelect
                );

                $bFound = false;

                if($resIndcode){

                    $resIndcode = CIBlockElement::GetList(
                        ($aOrder = Array('ID' => 'ASC')),
                        $aIndcodeFilter,
                        false,
                        false,
                        $aIndcodeSelect
                    );

                    while($aIndcode = $resIndcode->GetNext()){


                        $amSelect = Array(
                            "ID",
                            "PROPERTY_model_new_link",
                            "PROPERTY_type_of_product",
                            "PROPERTY_manufacturer"
                        );

                        $amFilter = Array(
                            "IBLOCK_ID" => 17,
                            "ACTIVE" => "Y",
                            "PROPERTY_model_new_link" => $aIndcode['ID']
                        );

                        {

                            $rModel = CIBlockElement::GetList(
                                ($order = Array()),
                                $amFilter,
                                false,
                                false,
                                $amSelect
                            );

                            if($rModel){

                                while($aModel = $rModel->GetNext()){

                                    if(!empty($model)
                                        && isset($aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                        && isset($aModel['PROPERTY_MANUFACTURER_VALUE'])
                                        && !empty($aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                        && !empty($aModel['PROPERTY_MANUFACTURER_VALUE'])
                                    ) {

                                        //тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

                                        fputcsv($fp,array(
                                            $aModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                            $aModel['PROPERTY_MANUFACTURER_VALUE'],
                                            $aStr[1], //модель
                                            $aStr[2], //товар
                                            '',
                                            $aStr[0], //инд код
                                            $aStr[3], //вид код
                                            $aStr[4], //вид поз
                                            $aStr[5], //вид изображение
                                        ),';');

                                        $bFound = true;

                                    }

                                }

                            }



                        }

                    }

                }

                if(!$bFound){

                    fputcsv($fp1,array(
                        $aStr[0],
                        $aStr[1],
                        $aStr[2],
                        $aStr[3],
                        $aStr[4],
                        $aStr[5]
                    ),';');

                }

            }

        }

        fclose($fp);
        fclose($fp1);

        return $cFound ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(file_exists(static::$flAvail))
            unlink(static::$flAvail);

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_models_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/check_models.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');


        } else {

            CEvent::SendImmediate('PARSE_MODELS', SITE_ID, array('TIME' => (date('Y.m.d H:i:s').' get checkmodels')));
            unlink(static::$fcsvFifle);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_models_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelIndcodes::getList();