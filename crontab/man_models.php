<?php

//https://youtwig.ru/local/crontab/man_models.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelManufacturersProps{

    private static $countStrings = 100;

    public static function getList($toFind){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList($toFind);

        static::getRedirect($modelLastPropId);

    }

    private static function checkList($toFind){

        $modelEl = new CIBlockElement;

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_manufacturers_props_last.txt'));

        $arProductSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_model_new_link",
            "PROPERTY_manufacturer",
            "PROPERTY_type_of_product",
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            "PROPERTY_manufacturer_VALUE" => $toFind
        );

        $flag = 'w+';

        if($skip > 0){

            $flag = 'a+';

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_manufacturers_props_last.txt', 0);

        }

        $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/man_models.csv',$flag);

        $skip = empty($skip) ? 1 : $skip;
        $countStrings = 0;

        $arProductNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resProduct = CIBlockElement::GetList(
            ($order = Array()),
            $arProductFilter,
            false,
            $arProductNavParams,
            $arProductSelect
        );

        if($resProduct){

            while($arModel = $resProduct->GetNext()) {


                ++$countStrings;
                $modelLastPropId = $arModel['ID'];

                //тип продукта;производитель;модель;
                

                $manufacturer = trim($arModel["PROPERTY_MANUFACTURER_VALUE"]);
                $type_of_product = trim($arModel["PROPERTY_TYPE_OF_PRODUCT_VALUE"]);
                $model = '';

                $pRes = CIBlockElement::GetByID($arModel["PROPERTY_MODEL_NEW_LINK_VALUE"]);

                if($pRes){

                    $aModel = $pRes->GetNext();

                    if(isset($aModel['NAME'])){
                        $model = trim($aModel['NAME']);
                    }

                }

                if(!empty($manufacturer)
                    && !empty($type_of_product)
                    && !empty($model)) {

                    fputcsv($fp,array($type_of_product,$manufacturer,$model),';');

                }


            }

        }

        fclose($fp);

        if($countStrings < static::$countStrings){
            $modelLastPropId = 0;
        }

        ++$skip;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_manufacturers_props_last.txt', $skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/man_models.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_manufacturers_props_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelManufacturersProps::getList("Bosch");