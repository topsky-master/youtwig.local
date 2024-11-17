<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

//https://youtwig.ru/local/crontab/remap_products_to_models.php?intestwetrust=1

CModule::IncludeModule('iblock');

$sModPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/modelids.csv';
$sModPathIds = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/modelids.php';


$aFound = array();

if(file_exists($sModPath)){

    $aModelNames = file($sModPath);
    $aModelNames = array_map('trim',$aModelNames);

    $arSelect = Array("ID", "NAME");
    $arFilter = Array("IBLOCK_ID"=> 27, "NAME" => $aModelNames, "ACTIVE"=>"Y");
    $res = impelCIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    if($res)
        while($arFields = $res->GetNext())
        {
            $aFound[$arFields['NAME']] = $arFields['ID'];
        }

    $aFound = array_values($aFound);

    file_put_contents($sModPathIds,'<?php $aFound = '.var_export($aFound,true).'; ?>');

    if(!empty($aFound))
        unlink($sModPath);

} else if(file_exists($sModPathIds)){

    require_once $sModPathIds;

}

$countStrings = 200;
$currentCount = 0;
$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;
$skip = trim($skip);
$skip = (int)$skip;
$skip = !is_numeric($skip) ? 0 : $skip;

    $toFind = array(790977);
            $toDelete = array();
            $toAdd = array(1079710);

$arOrder = Array("ID" => "asc");
$lArSelect = array('ID');
$hasCopy = true;

$lArFilter = array(
    'IBLOCK_ID' => 17,
    'ACTIVE' => 'Y',
    //'=ID' => 748917
	//	'PROPERTY_manufacturer_VALUE' => array('Siemens'),
	// 'PROPERTY_type_of_product_VALUE' => array('Ð¡Ñ‚Ð¸Ñ€Ð°Ð»ÑŒÐ½Ð°Ñ Ð¼Ð°ÑˆÐ¸Ð½Ð°')
    //'PROPERTY_model_new_link' => $aFound
);

if(!empty($aFound)){
    $lArFilter['PROPERTY_model_new_link'] = $aFound;
}

if(!empty($toFind)){
    $lArFilter['PROPERTY_SIMPLEREPLACE_PRODUCTS'] = $toFind;
}

if($skip > 0){
    $lArFilter['>ID'] = $skip;
};

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

$lastId = 0;
$modelEl = new impelCIBlockElement;

$lDBRes = impelCIBlockElement::GetList($arOrder, $lArFilter, false, false, $lArSelect);

if($lDBRes)
    while($arFields = $lDBRes->GetNext()){

        ++$currentCount;

        $indexes = array();

        //usleep(50);

        //$posArray = array_filter($posArray);

        $productPropsDB = impelCIBlockElement::GetProperty(
            17,
            $arFields["ID"],
            array("sort" => "asc"),
            Array("CODE" => "SIMPLEREPLACE_PRODUCTS")
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
                        'SIMPLEREPLACE_POSITION' => array(),
                        'SIMPLEREPLACE_INDCODE' => array(),
                        'SIMPLEREPLACE_VIEW' => array()
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

        $arModelPosFilter = Array("CODE" => "SIMPLEREPLACE_POSITION");

        $resPosModelDB = impelCIBlockElement::GetProperty(
            17,
            $arFields["ID"],
            array(),
            $arModelPosFilter
        );

        if ($resPosModelDB) {

            $pCounter = 0;

            while ($posModelFields = $resPosModelDB->GetNext()) {

                if(isset($indexes[$pCounter])){
                    $indexes[$pCounter]['SIMPLEREPLACE_POSITION'] = $posModelFields['VALUE'];
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
        $auStrings = array();

        $oPosArray = $posArray;

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

            $toBaseProducts = array('SIMPLEREPLACE_PRODUCTS' => $productsArray);

        } else {

            $indcodeArray = array();
            $arModelIndcodeFilter = Array("CODE" => "SIMPLEREPLACE_INDCODE");

            $skipCounter = 0;

            $resIndcodeModelDB = impelCIBlockElement::GetProperty(
                17,
                $arFields["ID"],
                array(),
                $arModelIndcodeFilter
            );

            if ($resIndcodeModelDB) {

                while ($indcodeModelFields = $resIndcodeModelDB->GetNext()) {

                    if(isset($indexes[$skipCounter])){
                        $indexes[$skipCounter]['SIMPLEREPLACE_INDCODE'] = $indcodeModelFields['VALUE'];
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

            $viewsArray = array();

            $arModelViewsFilter = Array("CODE" => "SIMPLEREPLACE_VIEW");
            $resViewsModelDB = impelCIBlockElement::GetProperty(
                17,
                $arFields["ID"],
                array(),
                $arModelViewsFilter
            );

            $skipCounter = 0;

            if ($resViewsModelDB) {

                while ($viewsModelFields = $resViewsModelDB->GetNext()) {

                    if(isset($indexes[$skipCounter])){
                        $indexes[$skipCounter]['SIMPLEREPLACE_VIEW'] = $viewsModelFields['VALUE'];
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
            $max[] = sizeof($productsArray);
            $max[] = sizeof($posArray);

            $sizeof = max($max);

            if (sizeof($indcodeArray) < $sizeof) {
                $indcodeArray = array_merge($indcodeArray, array_fill(sizeof($indcodeArray), ($sizeof - sizeof($indcodeArray)), $skipIndCodeId));
            }

            if (sizeof($viewsArray) < $sizeof) {
                $viewsArray = array_merge($viewsArray, array_fill(sizeof($viewsArray), ($sizeof - sizeof($viewsArray)), $skipViewId));
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

                                $posArray[] = $index['SIMPLEREPLACE_POSITION'];
                                $productsArray[] = $productId;
                                $indcodeArray[] = $index['SIMPLEREPLACE_INDCODE'];
                                $viewsArray[] = $index['SIMPLEREPLACE_VIEW'];

                            }

                        } else {

                            $posArray[] = '-';
                            $productsArray[] = $productId;
                            $indcodeArray[] = $indcode;
                            $viewsArray[] = $skipViewId;

                        }

                    }

                }

            }

            $bError = false;

            $posArray = array_values($posArray);
            $productsArray = array_values($productsArray);
            $indcodeArray = array_values($indcodeArray);
            $viewsArray = array_values($viewsArray);

            foreach($productsArray as $key => $value){

                $stArray = $productsArray[$key].':'.$posArray[$key].':'.$indcodeArray[$key].':'.$viewsArray[$key];

                if(!isset($auStrings[$stArray])
                    && $skipProdId != $productsArray[$key]){

                    $auStrings[$stArray] = $stArray;

                    $posArray[$key] = $posArray[$key] == 'Array' ? '-' : $posArray[$key];
                    $indcodeArray[$key] = $indcodeArray[$key] == 'Array' ? $skipIndCodeId : $indcodeArray[$key];
                    $viewsArray[$key] = $viewsArray[$key] == 'Array' ? $skipViewId : $viewsArray[$key];

                    $toBaseProducts['SIMPLEREPLACE_PRODUCTS'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                    $toBaseProducts['SIMPLEREPLACE_POSITION'][] = array('VALUE' => $posArray[$key], 'DESCRIPTION' => '');
                    $toBaseProducts['SIMPLEREPLACE_INDCODE'][] = array('VALUE' => $indcodeArray[$key], 'DESCRIPTION' => '');
                    $toBaseProducts['SIMPLEREPLACE_VIEW'][] = array('VALUE' => $viewsArray[$key], 'DESCRIPTION' => '');

                }

            }

        }

        if(empty($toBaseProducts)
            && !empty($toDelete)){

            $toBaseProducts = array(
                'SIMPLEREPLACE_PRODUCTS' => false,
                'SIMPLEREPLACE_VIEW' => false,
                'SIMPLEREPLACE_INDCODE' => false,
                'SIMPLEREPLACE_POSITION' => false
            );

        }


		
        //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/tst_prop.log',$arFields['ID'].'-'."\n".var_export($toBaseProducts,true)."\n",FILE_APPEND);

        impelCIBlockElement::SetPropertyValuesEx($arFields['ID'], 17, $toBaseProducts);
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arFields['ID']);

        if ($modelEl->Update($arFields['ID'], Array('TIMESTAMP_X' => true))) {

        };

        $lastId = $arFields['ID'];

        if($currentCount >= $countStrings){
            //header("HTTP/1.1 301 Moved Permanently");
            //header('Location: '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remap_products_to_models.php?intestwetrust=1&skip='.$lastId.'&time='.time().'&PageSpeed=off');
            echo '<html><header><meta http-equiv="refresh" content="'.mt_rand(0,1).';URL=\''.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remap_products_to_models.php?intestwetrust=1&skip='.$lastId.'&time='.time().'&PageSpeed=off\'" /><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remap_products_to_models.php?intestwetrust=1&skip='.$lastId.'&time='.time().'&PageSpeed=off";},'.mt_rand(500,700).');</script></header><body><h1>'.time().'</h1></body></html>';
            die();

        }

    }

echo 'done';