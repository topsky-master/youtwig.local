#!/usr/bin/php -q
<?php

function errx(){
    print_r(error_get_last());
}

function mUniqueAttrs(&$productsArray,&$indcodeArray,&$viewsArray,&$posArray,$skipProdId,$skipViewId,$skipIndCodeId){

    $aHasFound = $atProducts = $atIndcodeArray = $atViewsArray = $atPosArray= array();

    foreach ($productsArray as $productNum => $productsId) {

        if(isset($indcodeArray[$productNum])
            && isset($viewsArray[$productNum])
            && isset($posArray[$productNum]) && !empty($indcodeArray[$productNum])
            && !empty($viewsArray[$productNum])
            && !empty($posArray[$productNum])
            && !empty($productsId)
            && $skipProdId != $productsId
        ){

            $sHasFound = $productsId . ';' . $indcodeArray[$productNum] . ';' . $viewsArray[$productNum] . ';' . $posArray[$productNum];

            if (!isset($aHasFound[$sHasFound])) {
                $aHasFound[$sHasFound] = 0;
                $atProducts[] = $productsId;
                $atIndcodeArray[] = $indcodeArray[$productNum];
                $atViewsArray[] = $viewsArray[$productNum];
                $atPosArray[] = $posArray[$productNum];

            }

        }

    }


    $productsArray = $atProducts;
    $indcodeArray = $atIndcodeArray;
    $viewsArray = $atViewsArray;
    $posArray = $atPosArray;

}

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

$bSkipMan = false;

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

//define('LANGUAGE_ID','ru');
//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if (isset($argc)
    && isset($argv)
    && $argc > 0
    && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;
    $_REQUEST['file'] = $argv[1];

}

$file = isset($_REQUEST['file'])
&&!empty($_REQUEST['file'])
&&file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/'.urldecode(trim($_REQUEST['file'])))
    ? urldecode(trim($_REQUEST['file']))
    : '';

if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file)
    && ((filemtime(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file) + 1200) < time())){
    unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file);
}


require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $DB;
// $refp = fopen($_SERVER['DOCUMENT_ROOT'].'/indexes.csv','a+');
$sStr = '';
$r = $DB->query('SELECT DISTINCT ID FROM b_iblock_element WHERE iblock_id=11 AND active=\'Y\' AND ID NOT IN(SELECT ELEMENT_ID FROM `b_iblock_11_index` GROUP BY ELEMENT_ID) ORDER BY ID');
$arr = [];

while($a = $r->fetch()) {
	$arr[$a['ID']] = $a['ID']; 
}

if($refp != null)
{
    fputcsv($refp,[date('Y-m-d H:i:s'),join(',',$arr),count($arr)],';');
    fclose($refp);
}

if(!isset($_REQUEST['intestwetrust'])
    || empty($file)
    || file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file)) die();

file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file,date('Y.m.d H:i:s'));

//usleep(2500);
ini_set('max_execution_time',999999);
ignore_user_abort();

global $USER;

function checkIsSkipped(
    $manValue = '',
    $typeValue = '',
    $modelValue = '') {

    static $skipTypeManufacturers,$skipType;

    $return = false;

    if(!is_array($skipTypeManufacturers)){

        $skipTypeManFile = dirname(dirname(__DIR__)).'/bitrix/tmp/skiptypeman.csv';
        $skipTypeManufacturers = array();
        $skipType = array();

        if(file_exists($skipTypeManFile)){
            $skipLines = file($skipTypeManFile);

            foreach($skipLines as $line){

                $lines = str_getcsv($line,";");
                $lines = array_map('trim',$lines);

                $types = array_slice($lines,1);


                if(!isset($skipTypeManufacturers[$lines[0]])){
                    $skipTypeManufacturers[$lines[0]] = array();
                }

                $types = array_filter($types);

                if(!empty($types)) {

                    if (!empty($lines[0])) {

                        $skipTypeManufacturers[$lines[0]] = array_unique(array_merge($skipTypeManufacturers[$lines[0]], $types));

                    } else {

                        foreach ($types as $type) {
                            $skipType[$type] = $type;
                        }

                    }
                }

            }

        }

    }

    if(isset($skipTypeManufacturers[$manValue])){

        if(empty($skipTypeManufacturers[$manValue])){

            $return = true;

        } else {

            foreach($skipTypeManufacturers[$manValue] as $type){

                if($type == $typeValue){
                    $return = true;
                    break;
                }

            };

        }

    }

    if(!$return && isset($skipType[$typeValue])){
        $return = true;
    }

    return $return;

}

$skipProdId = 0;

$arProdFilter = Array(
    "CODE" => "bez_tovara",
    "IBLOCK_ID" => 11
);

$arProdSelect = Array("ID");

$resProdDB = impelCIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

$resProdArr = Array();

if($resProdDB) {
    $resProdArr = $resProdDB->GetNext();

    if(isset($resProdArr['ID'])
        && !empty($resProdArr['ID'])){

        $skipProdId = $resProdArr['ID'];
    }
}


$skipViewId = 0;

$arViewFilter = Array(
    "CODE" => "bez_vida",
    "IBLOCK_ID" => 34
);

$arViewSelect = Array("ID");

$resViewDB = impelCIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

$resViewArr = Array();

if($resViewDB) {
    $resViewArr = $resViewDB->GetNext();

    if(isset($resViewArr['ID'])
        && !empty($resViewArr['ID'])){

        $skipViewId = $resViewArr['ID'];
    }
}

$skipIndCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_ind_koda",
    "IBLOCK_ID" => 35
);

$arCodeSelect = Array("ID");

$resCodeDB = impelCIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipIndCodeId = $resCodeArr['ID'];

    }
}
/*
$skipComCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_com_koda",
    "IBLOCK_ID" => 36
);

$arCodeSelect = Array("ID");

$resCodeDB = impelCIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipComCodeId = $resCodeArr['ID'];

    }
} */


$pfOpen = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$file,'r');

$countStrings = 100;
$currentCount = 0;
$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;

if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file)
    && filesize(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file)){

    $skip = (int)trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file));

}

if(!empty($skip)){
    fseek($pfOpen,$skip);
}

file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/tst'.$file,$skip);

function _remove_spaces($value){
    $value = preg_replace('~^[\s]+~is','',$value);
    $value = preg_replace('~[\s]+?$~is','',$value);
    return $value;
}

$indCodeEl = $viewEl = $modelEl = new impelCIBlockElement;

if(CModule::IncludeModule("iblock")){

    //IBLOCK_ID=17 model_new_link

    while($current = fgetcsv($pfOpen, 0 , ";")){

        $current = array_map('trim',$current);
        $current = array_map('_remove_spaces',$current);
        $newModel = false;

        if(is_array($current)
            && !empty($current)
            && isset($current[0])
            && !empty($current[0])
            && isset($current[1])
            && !empty($current[1])
            && isset($current[2])
            && !empty($current[2])
            && !checkIsSkipped($current[1],$current[0])
        ){
            //тип продукта;производитель;

            $modelName = '';

            if(isset($current[0]) && !empty($current[0])){

                $modelName = trim($current[0]);

                $PROPERTY_CODE = 'type_of_product';

                $typeProperties = CIBlockProperty::GetList(
                    Array(
                        "sort"=>"asc",
                        "name"=>"asc"
                    ),
                    Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => 17,
                        "CODE" => $PROPERTY_CODE)
                );



                if($typeProperties){

                    while ($typeFields = $typeProperties->GetNext()){

                        $typePropertyID = $typeFields["ID"];
                        $enumTypeNew = new CIBlockPropertyEnum;

                        $typePropertyDB = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => 17, "VALUE" => $current[0], "CODE" => $PROPERTY_CODE));

                        if($typePropertyDB){
                            while($typePropertyFields = $typePropertyDB->GetNext()){

                                if(isset($typePropertyFields["ID"])){
                                    $typePropID = $typePropertyFields["ID"];
                                }

                            }
                        }


                        if(!$typePropID){
                            if($typePropID = $enumTypeNew->Add(
                                Array(
                                    'PROPERTY_ID' => $typePropertyID,
                                    'VALUE' => trim($current[0]))
                            )
                            ){



                            } else {

                                if(isset($enumTypeNew->LAST_ERROR)){
                                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'typePropID: '.$current[0].', '.$enumTypeNew->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                                }

                            }

                        }

                    }

                }

            }

            if(!empty($typePropID)){
                $modelProperties['type_of_product'] = Array("VALUE" => $typePropID);
            }

            $manufacturerPropID = false;

            if(isset($current[1]) && !empty($current[1])){

                $modelName .= (!empty($modelName) ? ' ': '') . trim($current[1]);

                $PROPERTY_CODE = 'manufacturer';

                $manufacturerProperties = CIBlockProperty::GetList(
                    Array(
                        "sort"=>"asc",
                        "name"=>"asc"
                    ),
                    Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => 17,
                        "CODE" => $PROPERTY_CODE)
                );



                if($manufacturerProperties){

                    while ($manufacturerFields = $manufacturerProperties->GetNext()){

                        $manufacturerPropertyID = $manufacturerFields["ID"];
                        $enummanufacturerNew = new CIBlockPropertyEnum;

                        $manufacturerPropertyDB = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=> 17, "VALUE" => $current[1], "CODE" => $PROPERTY_CODE));

                        if($manufacturerPropertyDB){
                            while($manufacturerPropertyFields = $manufacturerPropertyDB->GetNext()){

                                if(isset($manufacturerPropertyFields["ID"])){
                                    $manufacturerPropID = $manufacturerPropertyFields["ID"];
                                }

                            }
                        }


                        if(!$manufacturerPropID){
                            if($manufacturerPropID = $enummanufacturerNew->Add(
                                Array(
                                    'PROPERTY_ID' => $manufacturerPropertyID,
                                    'VALUE' => trim($current[1]))
                            )
                            ){



                            } else {

                                if(isset($enummanufacturerNew->LAST_ERROR)){
                                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt','enummanufacturerNew: '.$current[1].', '.$enummanufacturerNew->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                                }

                            }

                        }

                    }

                }

            }

            if(!empty($manufacturerPropID)){
                $modelProperties['manufacturer'] = Array("VALUE" => $manufacturerPropID);
            }

            $arModelSelect = Array("ID");
            $arModelFilter = Array(
                "IBLOCK_ID" => 27,
                "ACTIVE" => "Y",
                "=NAME" => trim($current[2])
            );

            $resModel = impelCIBlockElement::GetList(Array(), $arModelFilter, false, false, $arModelSelect);

            $modelPropIds = array();

            if($resModel){

                while($arModelFields = $resModel->GetNext()) {

                    if (isset($arModelFields["ID"])
                        && !empty($arModelFields["ID"])
                        && !in_array($arModelFields["ID"],$modelPropIds)) {

                        $modelPropIds[] = $arModelFields["ID"];

                    }

                }

                if(empty($modelPropIds)) {

                    $params = Array(
                        "max_len" => "100",
                        "change_case" => "L",
                        "replace_space" => "_",
                        "replace_other" => "_",
                        "delete_repeat_replace" => "true",
                    );

                    $arModelArray = Array(
                        "NAME" => trim($current[2]),
                        "ACTIVE" => "Y",
                        "CODE" => trim(CUtil::translit(trim($current[2]), LANGUAGE_ID, $params)),
                        "IBLOCK_ID" => 27,
                        "PREVIEW_TEXT" => " ",
                        "DETAIL_TEXT" => " ",
                    );

                    if ($modelNewPropId = $modelEl->Add($arModelArray)) {

                        $modelPropIds[] = $modelNewPropId;

                    } else {

                        if(isset($modelEl->LAST_ERROR)){
                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt','Model new name IB 27: '.trim($current[2]).', Code: '.trim(CUtil::translit(trim($current[2]), LANGUAGE_ID, $params)).', '.$modelEl->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                        }
                    }
                };

                if (isset($current[2]) && !empty($current[2])) {

                    $modelName .= (!empty($modelName) ? ' ' : '') . trim($current[2]);

                }

                if(!empty($modelPropIds))
                    foreach($modelPropIds as $modelPropId) {

                        if ($modelPropId) {
                            $modelProperties['model_new_link'] = Array("VALUE" => $modelPropId);
                        }

                        if (!empty($modelName)
                            && !empty($typePropID)
                            && !empty($manufacturerPropID)
                            && !empty($modelPropId)) {

                            $arModelSelect = Array("ID","PROPERTY_VERSION");
                            $arModelFilter = Array(
                                "IBLOCK_ID" => 17,
                                //"=NAME" => trim($modelName),
                                "PROPERTY_type_of_product" => $typePropID,
                                //"PROPERTY_manufacturer" => $manufacturerPropID,
                                "PROPERTY_model_new_link" => $modelPropId,
                                "ACTIVE" => "Y"
                            );

                            if(!$bSkipMan){
                                $arModelFilter["PROPERTY_manufacturer"] = $manufacturerPropID;
                            }

                            $resModel = impelCIBlockElement::GetList(Array(), $arModelFilter, false, false, $arModelSelect);

                            $foundModels = array();
                            $foundModel = false;
                            $hasVersion = false;

                            if ($resModel) {

                                while ($arFields = $resModel->GetNext()) {

                                    if(isset($arFields['PROPERTY_VERSION_VALUE'])
                                        && $arFields['PROPERTY_VERSION_VALUE'] == 'Да'){

                                        $hasVersion = true;
                                        continue;

                                    }

                                    if (isset($arFields["ID"])
                                        && !empty($arFields["ID"])
                                    ) {

                                        $foundModels[$arFields["ID"]] = $arFields["ID"];

                                    }


                                }

                            }

                            $modelPropID = false;

                            if (empty($foundModels)
                                && !$hasVersion
                            ) {

                                $params = Array(
                                    "max_len" => "100",
                                    "change_case" => "L",
                                    "replace_space" => "_",
                                    "replace_other" => "_",
                                    "delete_repeat_replace" => "true",
                                );

                                $arModelArray = Array(
                                    "NAME" => trim($modelName),
                                    "ACTIVE" => "Y",
                                    "CODE" => trim(CUtil::translit($modelName, LANGUAGE_ID, $params)),
                                    "PROPERTY_VALUES" => $modelProperties,
                                    "IBLOCK_ID" => 17,
                                    "PREVIEW_TEXT" => " ",
                                    "DETAIL_TEXT" => " ",
                                );


                                if ($foundModel = $modelEl->Add($arModelArray)) {

                                    $newModel = true;
                                    $foundModels[] = $foundModel;

                                } else {

                                    if (isset($modelEl->LAST_ERROR)) {
                                        //file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 17: ' . trim($modelName) . ',' . ', Code: ' . trim(CUtil::translit($modelName, LANGUAGE_ID, $params)) . ', ' . $modelEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                    }

                                    $arCodeModelFilter = Array(
                                        "IBLOCK_ID" => 17,
                                        "CODE" => trim(CUtil::translit($modelName, LANGUAGE_ID, $params)),
                                        "PROPERTY_type_of_product" => $typePropID,
                                        //"PROPERTY_manufacturer" => $manufacturerPropID,
                                        "ACTIVE" => "Y"
                                    );

                                    if(!$bSkipMan){
                                        $arCodeModelFilter["PROPERTY_manufacturer"] = $manufacturerPropID;
                                    }

                                    $arCodeModelSelect = array('PROPERTY_model_new_link','ID');
                                    $resCodeModel = impelCIBlockElement::GetList(Array(), $arCodeModelFilter, false, false, $arCodeModelSelect);

                                    if($resCodeModel){

                                        while($arCodeModel = $resCodeModel->GetNext()){

                                            if(isset($arCodeModel['ID'])
                                                && !empty($arCodeModel['ID'])){

                                                if ($modelEl->Update($arCodeModel['PROPERTY_MODEL_NEW_LINK_VALUE'],
                                                    array('NAME' => $current[2], 'TIMESTAMP_X' => true))) {

                                                    $foundModels[] = $arCodeModel['ID'];

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                        if (!empty($foundModels)
                            && isset($current[3])
                            && !empty($current[3])) {

                            $current[3] = trim($current[3]);
                            $current[3] = (int)$current[3];

                            $foundModels = array_unique($foundModels);
                            $foundModels = array_filter($foundModels);

                            foreach($foundModels as $foundModel){

                                if ($current[3]) {

                                    $prodId = $current[3];

                                    $productsArray = array();

                                    $arModelProductsFilter = Array("CODE" => "SIMPLEREPLACE_PRODUCTS");

                                    $resProductsModelDB = impelCIBlockElement::GetProperty(17, $foundModel, array(), $arModelProductsFilter);

                                    if ($resProductsModelDB) {

                                        while ($productsModelFields = $resProductsModelDB->GetNext()) {

                                            if (isset($productsModelFields['VALUE'])
                                                && !empty($productsModelFields['VALUE'])
                                            ) {

                                                $productsArray[] = $productsModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $viewsArray = array();

                                    $arModelViewsFilter = Array("CODE" => "SIMPLEREPLACE_VIEW");
                                    $resViewsModelDB = impelCIBlockElement::GetProperty(17, $foundModel, array(), $arModelViewsFilter);

                                    if ($resViewsModelDB) {

                                        while ($viewsModelFields = $resViewsModelDB->GetNext()) {

                                            if (isset($viewsModelFields['VALUE'])
                                                && !empty($viewsModelFields['VALUE'])
                                            ) {

                                                $viewsArray[] = $viewsModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $indcodeArray = array();
                                    $arModelIndcodeFilter = Array("CODE" => "SIMPLEREPLACE_INDCODE");

                                    $resIndcodeModelDB = impelCIBlockElement::GetProperty(17, $foundModel, array(), $arModelIndcodeFilter);

                                    if ($resIndcodeModelDB) {

                                        while ($indcodeModelFields = $resIndcodeModelDB->GetNext()) {

                                            if (isset($indcodeModelFields['VALUE'])
                                                && !empty($indcodeModelFields['VALUE'])
                                            ) {

                                                $indcodeArray[] = $indcodeModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    /* $comcodeArray = array();

                                    $arModelComcodeFilter = Array("CODE" => "COMCODE");

                                    $resComcodeModelDB = impelCIBlockElement::GetProperty(17, $foundModel, array(), $arModelComcodeFilter);

                                    if ($resComcodeModelDB) {

                                        while ($comcodeModelFields = $resComcodeModelDB->GetNext()) {

                                            if (isset($comcodeModelFields['VALUE'])
                                                && !empty($comcodeModelFields['VALUE'])
                                            ) {

                                                $comcodeArray[] = $comcodeModelFields['VALUE'];

                                            }

                                        }

                                    } */

                                    $posArray = array();

                                    $arModelPosFilter = Array("CODE" => "SIMPLEREPLACE_POSITION");

                                    $resPosModelDB = impelCIBlockElement::GetProperty(17, $foundModel, array(), $arModelPosFilter);

                                    if ($resPosModelDB) {

                                        while ($posModelFields = $resPosModelDB->GetNext()) {

                                            if (isset($posModelFields['VALUE'])
                                                && !empty($posModelFields['VALUE'])) {

                                                $posArray[] = $posModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $max = array();
                                    $max[] = sizeof($indcodeArray);
                                    $max[] = sizeof($viewsArray);
                                    //$max[] = sizeof($comcodeArray);
                                    $max[] = sizeof($productsArray);
                                    $max[] = sizeof($posArray);

                                    $sizeof = max($max);

                                    if (sizeof($indcodeArray) < $sizeof) {
                                        $indcodeArray = array_merge($indcodeArray, array_fill(sizeof($indcodeArray), ($sizeof - sizeof($indcodeArray)), $skipIndCodeId));
                                    }

                                    if (sizeof($viewsArray) < $sizeof) {
                                        $viewsArray = array_merge($viewsArray, array_fill(sizeof($viewsArray), ($sizeof - sizeof($viewsArray)), $skipViewId));
                                    }

                                    /* if (sizeof($comcodeArray) < $sizeof) {
                                        $comcodeArray = array_merge($comcodeArray, array_fill(sizeof($comcodeArray), ($sizeof - sizeof($comcodeArray)), $skipComCodeId));
                                    } */

                                    if (sizeof($productsArray) < $sizeof) {
                                        $productsArray = array_merge($productsArray, array_fill(sizeof($productsArray), ($sizeof - sizeof($productsArray)), $skipProdId));
                                    }

                                    if (sizeof($posArray) < $sizeof) {
                                        $posArray = array_merge($posArray, array_fill(sizeof($posArray), ($sizeof - sizeof($posArray)), '-'));
                                    }


                                    $viewPosSt = '-';

                                    if (isset($current[7]) && !empty($current[7])) {
                                        $viewPosSt = trim($current[7]);
                                    }

                                    $viewId = false;

                                    if (!(isset($current[6]) && !empty($current[6]))
                                        && (isset($current[8]) && !empty($current[8]))) {

                                        $current[6] = pathinfo(dirname(dirname(__DIR__)).'/bitrix/tmp/images/' . $current[8], PATHINFO_FILENAME);

                                    }

                                    if (isset($current[6]) && !empty($current[6])) {

                                        $arViewSelect = Array("ID");
                                        $arViewFilter = Array(
                                            "IBLOCK_ID" => 34,
                                            "=NAME" => trim($current[6]),
                                            "ACTIVE" => "Y"
                                            //"PROPERTY_MODEL_LINK" => $foundModel
                                        );

                                        $resView = impelCIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

                                        if ($resView) {

                                            while ($arFields = $resView->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $viewId = $arFields["ID"];

                                                }
                                            }
                                        }

                                        /* if (!empty($viewId)) {

                                            $arMViewSelect = Array("PROPERTY_MODEL_LINK");
                                            $arMViewFilter = Array(
                                                "IBLOCK_ID" => 34,
                                                "ID" => $viewId,
                                            );

                                            $resMView = impelCIBlockElement::GetList(Array(), $arMViewSelect, false, false, $arMViewFilter);

                                            $viewModelLink = array();

                                            if ($resMView) {

                                                while ($arMView = $resMView->GetNext()) {

                                                    if (isset($arMView['PROPERTY_MODEL_LINK_VALUE'])
                                                        && !empty($arMView['PROPERTY_MODEL_LINK_VALUE'])) {
                                                        $viewModelLink[] = array('VALUE' => $arMView['PROPERTY_MODEL_LINK_VALUE'], 'DESCRIPTION' => '');
                                                    }

                                                }

                                            }

                                            $viewModelLink[] = array('VALUE' => $foundModel, 'DESCRIPTION' => '');
                                            impelCIBlockElement::SetPropertyValuesEx($viewId, 34, ($viewModelPropArr = array('MODEL_LINK' => $viewModelLink)));


                                        } */

                                    }

                                    if (empty($viewId)
                                        && isset($current[6]) && !empty($current[6])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arViewArray = Array(
                                            "NAME" => trim($current[6]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[6]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 34,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        if ($viewId = $viewEl->Add($arViewArray)) {

                                            //impelCIBlockElement::SetPropertyValuesEx($viewId, 34, ($viewModelPropArr = array('MODEL_LINK' => array(array('VALUE' => $foundModel, 'DESCRIPTION' => '')))));

                                        } else {

                                            if (isset($viewEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'View new name IB 34: ' . trim($current[6]) . ', Code: ' . trim(CUtil::translit(trim($current[6]), LANGUAGE_ID, $params)) . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }


                                    if ($viewId) {

                                        if (isset($current[8])
                                            && !empty($current[8])) {

                                            $image = dirname(dirname(__DIR__)).'/bitrix/tmp/images/' . $current[8];

                                            try{

                                                if (file_exists($image)
                                                    && class_exists('Imagick')
                                                ) {

                                                    $imagick = new Imagick($image);
                                                    $valid = $imagick->valid();

                                                    if($valid){

                                                        $viewProperties['PREVIEW_PICTURE'] = CFile::MakeFileArray($image);

                                                        if ($viewEl->Update($viewId, $viewProperties)) {

                                                        } else {
                                                            if (isset($viewEl->LAST_ERROR)) {
                                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 34: ' . trim($image) . ',' . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                                            }
                                                        }
                                                    }

                                                }

                                            } catch(Exception $exception){

                                            }

                                        }

                                    }


                                    if (!$viewId) {
                                        $viewId = $skipViewId;
                                    }

                                    /* $comCodeId = false;

                                    if (isset($current[4]) && !empty($current[4])) {

                                        $arComCodeSelect = Array("ID");
                                        $arComCodeFilter = Array(
                                            "IBLOCK_ID" => 36,
                                            "=NAME" => trim($current[4])
                                        );

                                        $resComCode = impelCIBlockElement::GetList(Array(), $arComCodeFilter, false, false, $arComCodeSelect);

                                        if ($resComCode) {

                                            while ($arFields = $resComCode->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $comCodeId = $arFields["ID"];

                                                }
                                            }
                                        }

                                    }

                                    if (empty($comCodeId)
                                        && isset($current[4]) && !empty($current[4])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arComCodeArray = Array(
                                            "NAME" => trim($current[4]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[4]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 36,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        if ($comCodeId = $comCodeEl->Add($arComCodeArray)) {

                                        } else {

                                            if (isset($comCodeEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'ComCode new name IB 36: ' . trim($current[4]) . ', Code: ' . trim(CUtil::translit(trim($current[4]), LANGUAGE_ID, $params)) . ', ' . $comCodeEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }

                                    if (!$comCodeId) {
                                        $comCodeId = $skipComCodeId;
                                    } */

                                    $indCodeId = false;

                                    if (isset($current[5]) && !empty($current[5])) {

                                        $arIndCodeSelect = Array("ID");
                                        $arIndCodeFilter = Array(
                                            "IBLOCK_ID" => 35,
                                            "=NAME" => trim($current[5])
                                        );

                                        $resIndCode = impelCIBlockElement::GetList(Array(), $arIndCodeFilter, false, false, $arIndCodeSelect);

                                        if ($resIndCode) {

                                            while ($arFields = $resIndCode->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $indCodeId = $arFields["ID"];

                                                }
                                            }
                                        }

                                    }

                                    if (empty($indCodeId)
                                        && isset($current[5]) && !empty($current[5])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arIndCodeArray = Array(
                                            "NAME" => trim($current[5]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[5]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 35,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        if ($indCodeId = $indCodeEl->Add($arIndCodeArray)) {

                                        } else {

                                            if (isset($indCodeEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'IndCode new name IB 35: ' . trim($current[5]) . ', Code: ' . trim(CUtil::translit(trim($current[5]), LANGUAGE_ID, $params)) . ', ' . $indCodeEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }

                                    if (!$indCodeId) {
                                        $indCodeId = $skipIndCodeId;
                                    }

                                    $toBaseProducts = array();
                                    $hasString = false;

                                    foreach ($productsArray as $productNum => $productsId) {

                                        if ($productsId == $prodId
                                            && (isset($indcodeArray[$productNum])
                                                && $indcodeArray[$productNum] == $indCodeId
                                                && isset($viewsArray[$productNum])
                                                && $viewsArray[$productNum] == $viewId
                                                //&& isset($comcodeArray[$productNum])
                                                //&& $comcodeArray[$productNum] == $comCodeId
                                                && isset($posArray[$productNum])
                                                && $posArray[$productNum] == $viewPosSt)
                                        ) {

                                            $hasString = true;
                                            break;

                                        }

                                    }

                                    if (!$hasString) {

                                        $productsArray[] = $prodId;
                                        $indcodeArray[] = $indCodeId;
                                        $posArray[] = $viewPosSt;
                                        $viewsArray[] = $viewId;
                                        //$toBaseProducts['COMCODE'][] = array('VALUE' => $comCodeId, 'DESCRIPTION' => '');

                                    }

                                    mUniqueAttrs($productsArray,$indcodeArray,$viewsArray,$posArray,$skipProdId,$skipViewId,$skipIndCodeId);

                                    foreach ($productsArray as $value) {
                                        $toBaseProducts['SIMPLEREPLACE_PRODUCTS'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($posArray as $value) {
                                        $toBaseProducts['SIMPLEREPLACE_POSITION'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($indcodeArray as $value) {
                                        $toBaseProducts['SIMPLEREPLACE_INDCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($viewsArray as $value) {
                                        $toBaseProducts['SIMPLEREPLACE_VIEW'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    /*foreach ($comcodeArray as $value) {
                                        $toBaseProducts['COMCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }*/

                                    //if(sizeof($current) > 4)
                                    $toBaseProducts['PRODUCTS_REMOVED'] = 56422;

                                    //$toBaseProducts['BOSCH_UPDATE'] = 56793;

                                    if(!empty($typePropID))
                                        $toBaseProducts['type_of_product'] = Array("VALUE" => $typePropID);

                                    if(!empty($manufacturerPropID)
                                        && (($newModel && $bSkipMan) || !$bSkipMan))
                                        $toBaseProducts['manufacturer'] = Array("VALUE" => $manufacturerPropID);

                                    impelCIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
									//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);

                                    $updModel = Array('TIMESTAMP_X' => true);

                                    if ($modelEl->Update($foundModel, $updModel)) {

                                    } else {

                                        if (isset($modelEl->LAST_ERROR)) {
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Update model id IB 17 timestamp: ' . $foundModel . ', ' . $modelEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                        }

                                    }

                                }

                            }

                        }

                    }

            }

        }

        ++$currentCount;

        if($countStrings <= $currentCount){

            $skip = ftell($pfOpen);
            fclose($pfOpen);

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file,$skip);
            echo "\n".$skip.' b';

            if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file))
                unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file);

            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/index_cmanufacturer.php?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']).'&time='.time().'";},'.mt_rand(5,30).');</script></header></html>');

        }

    }


}

unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$file);


file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/result'.$file,'0');
fclose($pfOpen);

echo $skip.' b'."\n";

CEvent::SendImmediate('PARSE_MODELS', SITE_ID, array('TIME' => date('Y.m.d H:i:s')));

if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file))
    unlink(dirname(dirname(__DIR__)).'/bitrix/tmp/lock'.$file);

echo 'done';
die();