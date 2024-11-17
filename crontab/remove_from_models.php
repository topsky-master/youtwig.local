<?

//https://youtwig.ru/local/crontab/remove_from_models.php?intestwetrust=1

/*
CModule::IncludeModule('iblock');

$aIndCode = "000000000000150057
000000000000102848
000000000000175669
000000000000150058
000000000000175827
000000000000175765
000000000000150059
000000000000150061
000000000000175828
000000000000175654
000000000000150062
000000000000175766
000000000000301170
000000000000301218
000000000000301103";

$aIndCodes = explode("\n",$aIndCode);
$aFound = array();

foreach($aIndCodes as $aIndCode){

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID"=> 35, "NAME" => $aIndCode, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

if($res)
    while($arFields = $res->GetNext())
    {
        $aFound[$arFields['NAME']] = $arFields['ID'];
    }

}

var_export($aFound);
var_export(array_values($aFound));
*/
//тип товара;производитель;модель;товар;;инд код

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
//define('LANGUAGE_ID','ru');

//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if ($argc > 0 && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;

}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

CModule::IncludeModule('iblock');

$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/remove_from_models.csv','r+');

while($adLines = fgetcsv($fp,0,';')){

    $adLines = array_map('trim',$adLines);


    if(
    !(isset($adLines[0])
        && !empty($adLines[0])
        && isset($adLines[1])
        && !empty($adLines[1])
        && isset($adLines[2])
        && !empty($adLines[2])
        && isset($adLines[3])
        && !empty($adLines[3]))
    )
        continue;

    $sIndCode = '';
    $sModelNewLink = '';

    if(isset($adLines[5])
        && !empty($adLines[5])){

        $stIndCode = $adLines[5];

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=> 35, "NAME" => $stIndCode, "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        if($res)
            while($arFields = $res->GetNext())
            {
                $sIndCode = $arFields['ID'];
                break;
            }

    }



    if(isset($adLines[2])
        && !empty($adLines[2])){

        $stModelNewLink = $adLines[2];

        $arSelect = Array("ID", "NAME");
        $arFilter = Array("IBLOCK_ID"=> 27, "NAME" => $stModelNewLink, "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        if($res)
            while($arFields = $res->GetNext())
            {
                $sModelNewLink = $arFields['ID'];
                break;
            }

    }

    $currentCount = 0;

    $toFind = array($adLines[3]);
    $toDelete = array($adLines[3]);
    $toAdd = array();

    $arOrder = Array("ID" => "asc");

    $lArSelect = array('ID');

    $hasCopy = true;

    $lArFilter = array(
        'IBLOCK_ID' => 17,
        'ACTIVE' => 'Y',
        'PROPERTY_manufacturer_VALUE' => array($adLines[1]),
        'PROPERTY_type_of_product_VALUE' => array($adLines[0])
    );

    if(!empty($toFind)){
        $lArFilter['PROPERTY_products'] = $toFind;
    }

    if(!empty($sIndCode)){
        $lArFilter['PROPERTY_INDCODE'] = $sIndCode;
    }

    if(!empty($sModelNewLink)){
        $lArFilter['PROPERTY_model_new_link'] = $sModelNewLink;
    }

    $skipProdId = 0;

    $arProdFilter = Array(
        "CODE" => "bez_tovara",
        "IBLOCK_ID" => 11
    );

    $arProdSelect = Array("ID");

    $resProdDB = CIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

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

    $resViewDB = CIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

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

    $resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

    $resCodeArr = Array();

    if($resCodeDB) {
        $resCodeArr = $resCodeDB->GetNext();

        if(isset($resCodeArr['ID'])
            && !empty($resCodeArr['ID'])){

            $skipIndCodeId = $resCodeArr['ID'];

        }
    }

    $skipComCodeId = 0;

    $arCodeFilter = Array(
        "CODE" => "bez_com_koda",
        "IBLOCK_ID" => 36
    );

    $arCodeSelect = Array("ID");

    $resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

    $resCodeArr = Array();

    if($resCodeDB) {
        $resCodeArr = $resCodeDB->GetNext();

        if(isset($resCodeArr['ID'])
            && !empty($resCodeArr['ID'])){

            $skipComCodeId = $resCodeArr['ID'];

        }
    }


    $lastId = 0;

    $modelEl = new CIBlockElement;

    $lDBRes = CIBlockElement::GetList($arOrder, $lArFilter, false, false, $lArSelect);

    if($lDBRes)
        while($arFields = $lDBRes->GetNext()){

            ++$currentCount;

            $indexes = array();

            //usleep(50);

            $posArray = array_filter($posArray);

            $productPropsDB = CIBlockElement::GetProperty(
                17,
                $arFields["ID"],
                array("sort" => "asc"),
                Array("CODE" => "products")
            );

            $productsArray = array();
            $skipNumber = array();
            $skipCounter = 0;

            if($productPropsDB){
                while($productPropsAr = $productPropsDB->GetNext()){

                    if(isset($productPropsAr['VALUE'])
                        && !empty($productPropsAr['VALUE'])
                        && in_array($productPropsAr['VALUE'],$toFind)){

                        $indexes[$skipCounter] = array(
                            'POSITION' => array(),
                            'INDCODE' => array(),
                            'VIEW' => array(),
                            'COMCODE' => array()
                        );

                    }

                    if((!empty($toDelete)
                            && isset($productPropsAr['VALUE'])
                            && !empty($productPropsAr['VALUE'])
                            && !in_array($productPropsAr['VALUE'],$toDelete))
                        || empty($toDelete)
                    ){
                        $productsArray[] = $productPropsAr['VALUE'];
                    } else if((!empty($toDelete)
                        && isset($productPropsAr['VALUE'])
                        && !empty($productPropsAr['VALUE'])
                        && in_array($productPropsAr['VALUE'],$toDelete))){

                        $skipNumber[] = $skipCounter;


                    }

                    ++$skipCounter;
                }
            }




            $posArray = array();

            $arModelPosFilter = Array("CODE" => "POSITION");

            $resPosModelDB = CIBlockElement::GetProperty(
                17,
                $arFields["ID"],
                array(),
                $arModelPosFilter
            );

            if ($resPosModelDB) {

                $pCounter = 0;

                while ($posModelFields = $resPosModelDB->GetNext()) {

                    if(isset($indexes[$pCounter])){
                        $indexes[$pCounter]['POSITION'] = $posModelFields['VALUE'];
                    }

                    ++$pCounter;

                    if (isset($posModelFields['VALUE'])
                        && !empty($posModelFields['VALUE'])) {

                        $posArray[] = $posModelFields['VALUE'];

                    }

                }

            }

            $toAddIds = $toAdd;
            $toBaseProducts = array();

            if(empty($posArray)){

                if(!empty($toAddIds)){

                    $productsDiff = array_diff((array)$toAddIds, (array)$productsArray);

                    if(is_array($productsDiff)
                        && sizeof($productsDiff) > 0){
                        $productsArray = array_merge((array)$productsArray,(array)$productsDiff);
                    }

                    //$productsArray = array_unique($productsArray);

                    if(empty($productsArray)){
                        $productsArray = $toAddIds;
                    }


                } else if(empty($productsArray)){
                    $productsArray = false;
                }

                $toBaseProducts = array('products' => $productsArray);

            } else {

                $indcodeArray = array();
                $arModelIndcodeFilter = Array("CODE" => "INDCODE");

                $skipCounter = 0;

                $resIndcodeModelDB = CIBlockElement::GetProperty(
                    17,
                    $arFields["ID"],
                    array(),
                    $arModelIndcodeFilter
                );

                if ($resIndcodeModelDB) {

                    while ($indcodeModelFields = $resIndcodeModelDB->GetNext()) {

                        if(isset($indexes[$skipCounter])){
                            $indexes[$skipCounter]['INDCODE'] = $indcodeModelFields['VALUE'];
                        }

                        if (isset($indcodeModelFields['VALUE'])
                            && !empty($indcodeModelFields['VALUE'])
                            && (empty($skipNumber) || !in_array($skipCounter,$skipNumber))
                        ) {

                            $indcodeArray[] = $indcodeModelFields['VALUE'];

                        }

                        ++$skipCounter;

                    }

                }

                $comcodeArray = array();

                $arModelComcodeFilter = Array("CODE" => "COMCODE");

                $resComcodeModelDB = CIBlockElement::GetProperty(
                    17,
                    $arFields["ID"],
                    array(),
                    $arModelComcodeFilter
                );

                $skipCounter = 0;

                if ($resComcodeModelDB) {

                    while ($comcodeModelFields = $resComcodeModelDB->GetNext()) {

                        if(isset($indexes[$skipCounter])){
                            $indexes[$skipCounter]['COMCODE'] = $comcodeModelFields['VALUE'];
                        }

                        if (isset($comcodeModelFields['VALUE'])
                            && !empty($comcodeModelFields['VALUE'])
                            && (empty($skipNumber) || !in_array($skipCounter,$skipNumber))
                        ) {

                            $comcodeArray[] = $comcodeModelFields['VALUE'];

                        }

                        ++$skipCounter;

                    }

                }

                $viewsArray = array();

                $arModelViewsFilter = Array("CODE" => "VIEW");
                $resViewsModelDB = CIBlockElement::GetProperty(
                    17,
                    $arFields["ID"],
                    array(),
                    $arModelViewsFilter
                );

                $skipCounter = 0;

                if ($resViewsModelDB) {

                    while ($viewsModelFields = $resViewsModelDB->GetNext()) {

                        if(isset($indexes[$skipCounter])){
                            $indexes[$skipCounter]['VIEW'] = $viewsModelFields['VALUE'];
                        }

                        if (isset($viewsModelFields['VALUE'])
                            && !empty($viewsModelFields['VALUE'])
                            && (empty($skipNumber) || !in_array($skipCounter,$skipNumber))
                        ) {

                            $viewsArray[] = $viewsModelFields['VALUE'];

                        }

                        ++$skipCounter;

                    }

                }

                $indcodeArrayUniq = array_unique($indcodeArray);

                if(!empty($toAddIds)){

                    if(!empty($productsArray)) {

                        $productsDiff = array();
                        $productsSkip = array();

                        $count = 0;

                        foreach ($productsArray as $productNum => $productId) {

                            if (
                            in_array($productId, $toAddIds)
                            ) {

                                if (
                                    !empty($indcodeArray)
                                    && isset($indcodeArray[$productNum])
                                ) {

                                    if($indcodeArray[$productNum] == $skipIndCodeId){

                                        unset(
                                            $productsArray[$productNum],
                                            $viewsArray[$productNum],
                                            $comcodeArray[$productNum],
                                            $indcodeArray[$productNum],
                                            $posArray[$productNum]
                                        );

                                    } else {

                                        if(!isset($productsSkip[$indcodeArray[$productNum].'_'.$productId])){
                                            $productsSkip[$indcodeArray[$productNum].'_'.$productId] = 0;
                                        }

                                        $productsSkip[$indcodeArray[$productNum].'_'.$productId]++;

                                    }

                                }

                            }

                        }

                        if(!empty($productsDiff)){
                            $toAddIds = array_diff((array)$toAddIds,(array)$productsDiff);
                        }


                    } if(empty($productsArray)){
                        $productsArray = $toAddIds;
                    }

                }

                $skipCounter = 0;

                foreach($posArray as $posKey => $posVal){

                    if(!(empty($skipNumber) || !in_array($skipCounter,$skipNumber))){
                        unset($posArray[$posKey]);
                    }

                    ++$skipCounter;
                }

                $max = array();
                $max[] = sizeof($indcodeArray);
                $max[] = sizeof($viewsArray);
                $max[] = sizeof($comcodeArray);
                $max[] = sizeof($productsArray);
                $max[] = sizeof($posArray);

                $sizeof = max($max);

                if (sizeof($indcodeArray) < $sizeof) {
                    $indcodeArray = array_merge($indcodeArray, array_fill(sizeof($indcodeArray), ($sizeof - sizeof($indcodeArray)), $skipIndCodeId));
                }

                if (sizeof($viewsArray) < $sizeof) {
                    $viewsArray = array_merge($viewsArray, array_fill(sizeof($viewsArray), ($sizeof - sizeof($viewsArray)), $skipViewId));
                }

                if (sizeof($comcodeArray) < $sizeof) {
                    $comcodeArray = array_merge($comcodeArray, array_fill(sizeof($comcodeArray), ($sizeof - sizeof($comcodeArray)), $skipComCodeId));
                }

                if (sizeof($productsArray) < $sizeof) {
                    $productsArray = array_merge($productsArray, array_fill(sizeof($productsArray), ($sizeof - sizeof($productsArray)), $skipProdId));
                }

                if (sizeof($posArray) < $sizeof) {
                    $posArray = array_merge($posArray, array_fill(sizeof($posArray), ($sizeof - sizeof($posArray)), '-'));
                }

                if(!empty($toAddIds)){

                    foreach ($indcodeArrayUniq as $indcode) {

                        if(sizeof($indcodeArrayUniq) > 1
                            && $indcode == $skipIndCodeId){

                            continue;
                        }

                        foreach($toAddIds as $productId){

                            if(isset($productsSkip[$indcode.'_'.$productId])
                                && $productsSkip[$indcode.'_'.$productId] > 0){

                                --$productsSkip[$indcode.'_'.$productId];
                                continue;
                            }

                            if(!empty($indexes) && $hasCopy){

                                foreach($indexes as $index){


                                    $posArray[] = $index['POSITION'];
                                    $productsArray[] = $productId;
                                    $indcodeArray[] = $index['INDCODE'];
                                    $comcodeArray[] = $index['COMCODE'];
                                    $viewsArray[] = $index['VIEW'];

                                }

                            } else {

                                $posArray[] = '-';
                                $productsArray[] = $productId;
                                $indcodeArray[] = $indcode;
                                $comcodeArray[] = $skipComCodeId;
                                $viewsArray[] = $skipViewId;

                            }

                        }

                    }

                }

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

            }

            if(empty($toBaseProducts)
                && !empty($toDelete)){

                $restoreArray = array();

                $arModelRestoreFilter = Array("CODE" => "RESTORE_PRODUCTS");
                $resRestoreModelDB = CIBlockElement::GetProperty(
                    17,
                    $arFields["ID"],
                    array(),
                    $arModelRestoreFilter
                );

                if ($resRestoreModelDB) {

                    while ($restoreModelFields = $resRestoreModelDB->GetNext()) {

                        if (isset($restoreModelFields['VALUE'])
                            && !empty($restoreModelFields['VALUE'])
                        ) {

                            $restoreArray[] = $restoreModelFields['VALUE'];

                        }



                    }

                }

                $toBaseProducts = array(
                    'products' => false,
                    'COMCODE' => false,
                    'VIEW' => false,
                    'INDCODE' => false,
                    'POSITION' => false
                );

                if(!empty($restoreArray)){
                    $toBaseProducts['products'] = $restoreArray;
                    $toBaseProducts['PRODUCTS_REMOVED'] = 56423;
                    $toBaseProducts['RESTORE_PRODUCTS'] = false;
                }

            }

            CIBlockElement::SetPropertyValuesEx($arFields['ID'], 17, $toBaseProducts);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arFields['ID']);

            if ($modelEl->Update($arFields['ID'], Array('TIMESTAMP_X' => true))) {

            };

            $lastId = $arFields['ID'];



        }


}


echo $currentCount.'-';
echo 'done';