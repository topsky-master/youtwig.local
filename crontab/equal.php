<?php

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$countStrings = 20;
$currentCount = 0;

$skip = isset($_REQUEST['skip'])
&& !empty($_REQUEST['skip'])
    ? (int)$_REQUEST['skip']
    : 0;

$strings = file(dirname(dirname(__DIR__)).'/bitrix/tmp/equal.csv');

$subRange = array_slice($strings,($skip * $countStrings), $countStrings);

$strProperty = '';

if(!empty($subRange)){

    foreach($subRange as $modelString){

        $productsIDs = array();
        $modelsIds = array();

        $similiarModels = str_getcsv($modelString,';');
        $similiarModels = array_map('trim',$similiarModels);
        $similiarModels = array_filter($similiarModels);
        $similiarModels = array_unique($similiarModels);

        foreach($similiarModels as $modelName){

            $mlNDBRes = CIBlockElement::GetList(
                Array(),
                Array(
                    'IBLOCK_ID' => 27,
                    '=NAME' => $modelName
                ),
                false,
                false,
                array('ID')
            );

            if($mlNDBRes) {

                while ($mlNArr = $mlNDBRes->getNext()) {

                    $mlDBRes = CIBlockElement::GetList(
                        Array(),
                        Array(
                            'IBLOCK_ID' => 17,
                            'PROPERTY_MODEL_NEW_LINK' => $mlNArr['ID']
                        ),
                        false,
                        false,
                        array('ID')
                    );

                    if($mlDBRes
                        && $mlArr = $mlDBRes->getNext()){

                        $modelsIds[$mlArr["ID"]] = $mlArr["ID"];

                        $productPropsDB = CIBlockElement::GetProperty(
                            17,
                            $mlArr["ID"],
                            array("sort" => "asc"),
                            Array("CODE" => "products")
                        );

                        if($productPropsDB){

                            while($productPropsAr = $productPropsDB->GetNext()){

                                if(isset($productPropsAr['VALUE'])
                                    && !empty($productPropsAr['VALUE'])){
                                    $productsIDs[$productPropsAr['VALUE']] = $productPropsAr['VALUE'];
                                }
                            }
                        }

                    }

                }

            }

        }

        if(!empty($modelsIds)
            && !empty($productsIDs)){

            foreach($modelsIds as $modelId){

                CIBlockElement::SetPropertyValuesEx($modelId, 17, array('products' => $productsIDs));
				//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $modelId);
            }

        }

    }

    ++$skip;

    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/equal.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';

} else {

    echo 'done';

}