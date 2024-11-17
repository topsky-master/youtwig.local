<?php

//https://dev.youtwig.ru/local/crontab/clear_restore_products.php?intestwetrust=1
//https://youtwig.ru/local/crontab/clear_restore_products.php?intestwetrust=1

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');

$iSkip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;
$iSkip = trim($iSkip);
$iSkip = (int)$iSkip;
$iSkip = !is_numeric($iSkip) ? 0 : $iSkip;
$oModelEl = new CIBlockElement;

$aFilter = array(
    'IBLOCK_ID' => 17,
    '!RESTORE_PRODUCTS' => false
);

if($iSkip > 0){
    $aFilter['>ID'] = $iSkip;
};

$aOrder = Array("ID" => "asc");
$aSelect = array('ID');
$iMaxString = 50;
$iModelId = 0;

$rDB = CIBlockElement::GetList(
    $aOrder,
    $aFilter,
    false,
    ($aNavs = array(
        'nTopCount' => $iMaxString
    )),
    $aSelect);

$indexes = array();

if($rDB)
    while($aFields = $rDB->GetNext()){

        $aModelResFilter = Array("CODE" => "RESTORE_PRODUCTS");

        $rResModel = CIBlockElement::GetProperty(
            17,
            $aFields["ID"],
            array(),
            $aModelResFilter
        );

        $aResProducts = array();

        if ($rResModel) {

            while ($aResModel = $rResModel->GetNext()) {

                if (isset($aResModel['VALUE'])
                    && !empty($aResModel['VALUE'])) {

                    $aResProducts[$aResModel['VALUE']] = $aResModel['VALUE'];

                }

            }

        }

        if(!empty($aResProducts)) {
            $sResProducts = join(',',$aResProducts);
            $sResProducts = trim($sResProducts,',');
            $sResProducts = trim($sResProducts);
            $aFieldProps['SRESTORE_PRODUCTS'] = $sResProducts;
            $aFieldProps['RESTORE_PRODUCTS'] = false;

            CIBlockElement::SetPropertyValuesEx($aFields['ID'], 17, $aFieldProps);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aFields['ID']);
            if ($oModelEl->Update($aFields['ID'], Array('TIMESTAMP_X' => true))) {

            };

        }

        $iModelId = $aFields['ID'];

    }

if(!empty($iModelId)){
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/clear_restore_products.php?intestwetrust=1&skip='.$iModelId.'&time='.time().'&PageSpeed=off";},'.mt_rand(50,80).');</script></header></html>');
} else {
    echo 'done';
}