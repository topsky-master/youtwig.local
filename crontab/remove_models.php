<?php

//https://youtwig.ru/local/crontab/remove_models.php?intestwetrust=1

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

        $indCodes = array();

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt'));

        $lines = file(dirname(dirname(__DIR__)).'/bitrix/tmp/remove1.csv');
        $lines = array_slice($lines,$skip, static::$maxCount);
        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/aremove1.php';
        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/indcodes.php';

        if($skip > 0){

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', 0);
            $skip = 0;
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

                $smCheck = $aStr[0].';'.$aStr[1].';'.$aStr[2];
                ++$skip;

                if(!isset($aRemove[$smCheck])){
                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', $skip);
                    continue;
                }

                if(!empty($aStr[0])
                    && !empty($aStr[1])
                    && !empty($aStr[2])
                    && !empty($aStr[3])
                    && !empty($aStr[5])){

                    $imProductId = $aStr[3];
                    $smIndcodeId = $aStr[5];

                    if(isset($indCodes[$smIndcodeId])){

                        $aModelIds = $aRemove[$smCheck];

                        foreach ($aModelIds as $aModelId) {

                            $toBaseProducts = array();

                            if(!isset(static::$asFoundModels[$aModelId])){
                                $toBaseProducts['products'] = static::getPropValues('products', $aModelId);
                                $toBaseProducts['INDCODE'] = static::getPropValues('INDCODE', $aModelId);
                                static::$asFoundModels[$aModelId] = $toBaseProducts;
                            } else {
                                $toBaseProducts = static::$asFoundModels[$aModelId];
                            }

                            $adNums = array();

                            foreach ($toBaseProducts['products'] as $iProductNum => $iProductId) {

                                if(isset($indCodes[$smIndcodeId])
                                    && $indCodes[$smIndcodeId] == $toBaseProducts['INDCODE'][$iProductNum]
                                    && $imProductId == $iProductId
                                ){
                                    $adNums[] = $iProductNum;
                                }

                            }

                            if (!empty($adNums)) {

                                if(!isset(static::$asFoundModels[$aModelId]['POSITION'])){

                                    static::$asFoundModels[$aModelId]['POSITION'] = $toBaseProducts['POSITION'] = static::getPropValues('POSITION', $aModelId);
                                    static::$asFoundModels[$aModelId]['VIEW'] = $toBaseProducts['VIEW'] = static::getPropValues('VIEW', $aModelId);
                                    static::$asFoundModels[$aModelId]['COMCODE'] = $toBaseProducts['COMCODE'] = static::getPropValues('COMCODE', $aModelId);

                                } else {
                                    $toBaseProducts = static::$asFoundModels[$aModelId];
                                }

                                foreach ($adNums as $productNum) {

                                    unset($toBaseProducts['products'][$productNum]);
                                    unset($toBaseProducts['POSITION'][$productNum]);
                                    unset($toBaseProducts['INDCODE'][$productNum]);
                                    unset($toBaseProducts['VIEW'][$productNum]);
                                    unset($toBaseProducts['COMCODE'][$productNum]);

                                }

                                foreach ($toBaseProducts as $dName => $dValue) {

                                    $dValue = array_values($dValue);
                                    $toBaseProducts[$dName] = $dValue;

                                    if(empty($toBaseProducts[$dName]))
                                        $toBaseProducts[$dName] = false;

                                }

                                static::$asFoundModels[$aModelId] = $toBaseProducts;

                                CIBlockElement::SetPropertyValuesEx($aModelId, 17, $toBaseProducts);
								//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aModelId);

                                $updModel = Array('TIMESTAMP_X' => true);

                                $modelEl->Update($aModelId, $updModel);

                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_id.txt', $aModelId);



                            }

                        }

                    }


                }

                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', $skip);

            }


        }

        return $cFound ? $skip : 0;

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
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/remove_models.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_remove_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelModels::getList();