#!/usr/bin/php -q
<?php

//https://dev.youtwig.ru/local/crontab/remap_new_products.php?intestwetrust=1
//https://youtwig.ru/local/crontab/remap_new_products.php?intestwetrust=1

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

CModule::IncludeModule('iblock');

$iSkip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;
$iSkip = trim($iSkip);
$iSkip = (int)$iSkip;
$iSkip = !is_numeric($iSkip) ? 0 : $iSkip;
$oModelEl = new impelCIBlockElement;

$aProps = array('products','VIEW','INDCODE','POSITION');

$aFilter = array(
    'IBLOCK_ID' => 17,
	//'ID' => 155356
    //'!'.$sProp => false
);

//if($iSkip > 0){
//$aFilter['>ID'] = $iSkip;
//};

$aOrder = Array("ID" => "asc");
$aSelect = array('ID');
$iMaxString = 150000;
$iModelId = 0;

$rDB = impelCIBlockElement::GetList(
    $aOrder,
    $aFilter,
    false,
    false,
    $aSelect);

$indexes = array();

if($rDB)
    while($aFields = $rDB->GetNext()){

        foreach($aProps as $sProp) {

            $aModelResFilter = Array("CODE" => $sProp);

            $rResModel = impelCIBlockElement::GetProperty(
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

                        $aResProducts[] = $aResModel['VALUE'];

                    }

                }

            }

            /*$sResProducts = join(',',$aResProducts);
            $sResProducts = trim($sResProducts,',');
            $sResProducts = trim($sResProducts);*/
            $aFieldProps['SIMPLEREPLACE_'.mb_strtoupper($sProp)] = $aResProducts;
            //$aFieldProps[$sProp] = false;

            impelCIBlockElement::SetPropertyValuesEx($aFields['ID'], 17, $aFieldProps);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aFields['ID']);
            if ($oModelEl->Update($aFields['ID'], Array('TIMESTAMP_X' => true))) {

            };

            //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/sreplace.txt',$aFields['ID']."\n",FILE_APPEND);

        }

        $iModelId = $aFields['ID'];

    }

if(!empty($iModelId)){
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/clear_restore_products.php?intestwetrust=1&skip='.$iModelId.'&time='.time().'&PageSpeed=off";},'.mt_rand(50,80).');</script></header></html>');
} else {
    echo 'done';
}