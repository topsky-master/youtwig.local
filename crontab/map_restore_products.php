#!/usr/bin/php -q
<?php

//https://dev.youtwig.ru/local/crontab/map_restore_products.php?intestwetrust=1
//https://youtwig.ru/local/crontab/map_restore_products.php?intestwetrust=1

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
$oModelEl = new CIBlockElement;

$IBLOCK_ID = 17;
$rProp = CIBlockProperty::GetList(
    Array(
        "sort" => "asc",
        "name" => "asc"),
    Array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $IBLOCK_ID,
        "CODE" => "SIMPLEREPLACE%"
    )
);

if($rProp) {
    while ($aProp = $rProp->GetNext()) {

        $aMaps = array();

        $aFilter = array(
            'IBLOCK_ID' => 17,
            '!'.$aProp['CODE'] => false
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
            false,
            $aSelect);

        $aResult = array();

        if($rDB)
            while($aFields = $rDB->GetNext()){

                $aModelResFilter = Array("CODE" => $aProp['CODE']);

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

                            $aResProducts = $aResModel['VALUE'];

                        }

                    }

                    if(!empty($aResProducts)){

                        foreach($aResProducts as $aResProduct){

                            if(!isset($aResult[$aResProduct]))
                                $aResult[$aResProduct] = array();

                            $aResult[$aResProduct][$aFields['ID']] = $aFields['ID'];

                        }

                    }

                }

            }

        $aMaps[$aProp['CODE']] = $aResult;
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aProp['CODE']).'.php','<?php $aMaps = '.var_export($aMaps,true).'; ?>');
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/map_restore_products_'.mb_strtolower($aProp['CODE']).'.txt',serialize($aMaps));
        
    }

}
