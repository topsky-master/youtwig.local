#!/usr/bin/php -q
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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$lCount = 0;

if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/linkedc.txt')){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/linkedc.txt','0');
}

$lCount = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/linkedc.txt');

$arPSelect = Array(
    "ID",
);

$maxCount = 50;

$arPFilter = Array(
    "IBLOCK_ID" => 11
);

if($lCount > 0){
    $arPFilter['>ID'] = $lCount;
}

$dbPres = CIBlockElement::GetList(
    Array('ID' => 'ASC'),
    $arPFilter,
    false,
    Array('nTopCount' => $maxCount, 'checkOutOfRange' => true),
    $arPSelect);

$product_id = 0;

if ($dbPres) {
    while ($arPRes = $dbPres->GetNext()) {

        $product_id = $arPRes['ID'];

        $linkedElements = false;

        $mpResDB = impelCIBlockElement::GetList(
            Array("ID" => "DESC"),
            Array(
                'PROPERTY_SIMPLEREPLACE_PRODUCTS' => ($product_id),
                'IBLOCK_ID' => 17),
            false,
            false,
            array('ID','PROPERTY_SIMPLEREPLACE_PRODUCTS'));

        if($mpResDB){

            $count = 0;
            $productIds = array();

            while($mpADb = $mpResDB->GetNext()) {

                ++$count;

                if(isset($mpADb['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'])
                    && !empty($mpADb['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'])) {

                    $pProductsValues = (!empty($pProductsValues)
                        && !is_array($pProductsValues))
                        ? array($pProductsValues)
                        : $pProductsValues;

                    $pProductsValues = array_filter($mpADb['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE']);
                    $pProductsValues = array_unique($pProductsValues);
                    $dKey = array_search($product_id, $pProductsValues);

                    if($dKey !== false) {
                        unset($pProductsValues[$dKey]);
                    }



                }

            }

            if($count == 1) {

                $linkedElements = $pProductsValues;

            }

        }


        $fieldsUpdate = array('LINKED_ELEMETS' => $linkedElements, 'TIMESTAMP_X' => true);

        CIBlockElement::SetPropertyValuesEx(
            $product_id,
            11,
            $fieldsUpdate
        );

        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $product_id);

        $lCount = $product_id;

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/linkedc.txt',$lCount);

    }

}

$lCount = $product_id;
file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/linkedc.txt',$lCount);

