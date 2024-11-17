<?php

//https://dev.youtwig.ru/local/crontab/remove_comcodes.php?intestwetrust=1
//https://youtwig.ru/local/crontab/remove_comcodes.php?intestwetrust=1

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
    'IBLOCK_ID' => 36
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

        $oModelEl->Delete($aFields['ID']);
        $iModelId = $aFields['ID'];

    }

if(!empty($iModelId)){
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/remove_comcodes.php?intestwetrust=1&skip='.$iModelId.'&time='.time().'&PageSpeed=off";},'.mt_rand(50,80).');</script></header></html>');
} else {
    echo 'done';
}