<?php

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

class impelCopyProps{

    private static $countStrings = 100;

    public static function getList($toFind, $toCopy){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList($toFind, $toCopy);

        static::getRedirect($modelLastPropId);

    }

    private static function checkList($toFind, $toCopy){

        $modelEl = new CIBlockElement;

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_copy_props_last.txt'));

        $arProductSelect = Array(
            "ID",
            "NAME",
        );

        $arProductFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            "PROPERTY_products" => array($toFind)
        );

        if($skip > 0){


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_copy_props_last.txt', 0);

        }


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

            while($arProduct = $resProduct->GetNext()){

                $productsArray = $posArray = $indcodeArray = $comcodeArray = $viewsArray = array();

                ++$countStrings;
                $modelLastPropId = $arProduct['ID'];

                $posProducts = array();
                $fProductsArray = array();

                $arModelPosFilter = Array(
                    "CODE" => "products"
                );

                $resPosModelDB = CIBlockElement::GetProperty(
                    17,
                    $modelLastPropId,
                    array(),
                    $arModelPosFilter
                );

                if ($resPosModelDB) {

                    $count = 0;

                    while ($posModelFields = $resPosModelDB->GetNext()) {



                        if (isset($posModelFields['VALUE'])
                            && !empty($posModelFields['VALUE'])
                        ) {

                            if($posModelFields['VALUE'] == $toFind){
                                $posProducts[] = $count;
                                $fProductsArray[] = $toCopy;
                            }

                            $productsArray[] = $posModelFields['VALUE'];

                            ++$count;

                        }

                    }

                }

                $fIndcodeArray = array();
                $arModelIndcodeFilter = Array("CODE" => "INDCODE");

                $count = 0;

                $resIndcodeModelDB = CIBlockElement::GetProperty(
                    17,
                    $modelLastPropId,
                    array(),
                    $arModelIndcodeFilter
                );

                if ($resIndcodeModelDB) {

                    while ($indcodeModelFields = $resIndcodeModelDB->GetNext()) {

                        if (isset($indcodeModelFields['VALUE'])
                            && !empty($indcodeModelFields['VALUE'])
                        ) {

                            if(in_array($count,$posProducts)){
                                $fIndcodeArray[] = $indcodeModelFields['VALUE'];
                            }

                            $indcodeArray[] = $indcodeModelFields['VALUE'];

                        }

                        ++$count;

                    }

                }

                $fComcodeArray = array();

                $arModelComcodeFilter = Array("CODE" => "COMCODE");

                $resComcodeModelDB = CIBlockElement::GetProperty(
                    17,
                    $modelLastPropId,
                    array(),
                    $arModelComcodeFilter
                );

                $count = 0;

                if ($resComcodeModelDB) {

                    while ($comcodeModelFields = $resComcodeModelDB->GetNext()) {

                        if (isset($comcodeModelFields['VALUE'])
                            && !empty($comcodeModelFields['VALUE'])
                        ) {

                            if(in_array($count,$posProducts)){
                                $fComcodeArray[] = $comcodeModelFields['VALUE'];
                            }

                            $comcodeArray[] = $comcodeModelFields['VALUE'];

                        }

                        ++$count;

                    }

                }

                $fViewsArray = array();

                $arModelViewsFilter = Array("CODE" => "VIEW");
                $resViewsModelDB = CIBlockElement::GetProperty(
                    17,
                    $modelLastPropId,
                    array(),
                    $arModelViewsFilter
                );

                $count = 0;

                if ($resViewsModelDB) {

                    while ($viewsModelFields = $resViewsModelDB->GetNext()) {

                        if (isset($viewsModelFields['VALUE'])
                            && !empty($viewsModelFields['VALUE'])

                        ) {

                            if(in_array($count,$posProducts)){
                                $fViewsArray[] = $viewsModelFields['VALUE'];
                            }

                            $viewsArray[] = $viewsModelFields['VALUE'];

                        }

                        ++$count;

                    }

                }

                $fPosArray = array();

                $arModelPosFilter = Array("CODE" => "POSITION");

                $resPosModelDB = CIBlockElement::GetProperty(
                    17,
                    $modelLastPropId,
                    array(),
                    $arModelPosFilter
                );

                $count = 0;

                if ($resPosModelDB) {

                    while ($posModelFields = $resPosModelDB->GetNext()) {

                        if (isset($posModelFields['VALUE'])
                            && !empty($posModelFields['VALUE'])
                        ) {

                            if(in_array($count,$posProducts)){
                                $fPosArray[] = $posModelFields['VALUE'];
                            }

                            $posArray[] = $posModelFields['VALUE'];

                        }

                        ++$count;

                    }

                }

                $toBaseProducts = array();

                if(!in_array($toCopy,$productsArray)) {

                    if (!(!empty($viewsArray)
                        || !empty($comcodeArray)
                        || !empty($indcodeArray)
                        || !empty($posArray))) {


                        $productsArray[] = $toCopy;
                        $toBaseProducts = array('products' => $productsArray);

                        CIBlockElement::SetPropertyValuesEx($modelLastPropId, 17, $toBaseProducts);
						//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $modelLastPropId);

                        if ($modelEl->Update($modelLastPropId, Array('TIMESTAMP_X' => true))) {

                        };


                    } else {


                        $productsArray = array_merge($productsArray, $fProductsArray);
                        $viewsArray = array_merge($viewsArray, $fViewsArray);
                        $comcodeArray = array_merge($comcodeArray, $fComcodeArray);
                        $indcodeArray = array_merge($indcodeArray, $fIndcodeArray);
                        $posArray = array_merge($posArray, $fPosArray);

                        foreach ($productsArray as $value) {
                            $toBaseProducts['products'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                        }

                        foreach ($posArray as $value) {
                            $toBaseProducts['POSITION'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                        }

                        foreach ($indcodeArray as $value) {
                            $toBaseProducts['INDCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                        }

                        foreach ($viewsArray as $value) {
                            $toBaseProducts['VIEW'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                        }

                        foreach ($comcodeArray as $value) {
                            $toBaseProducts['COMCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                        }

                        CIBlockElement::SetPropertyValuesEx($modelLastPropId, 17, $toBaseProducts);
						//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $modelLastPropId);


                        if ($modelEl->Update($modelLastPropId, Array('TIMESTAMP_X' => true))) {

                        };


                    }

                }

            }

        }

        if($countStrings < static::$countStrings){
            $modelLastPropId = 0;
        }

        ++$skip;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_copy_props_last.txt', $skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/copy_props_images.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/avail_copy_props_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelCopyProps::getList(160101,155569);