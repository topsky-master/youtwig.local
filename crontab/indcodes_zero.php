#!/usr/bin/php -q
<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
}

//https://youtwig.ru/local/crontab/indcodes_zero.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

$aFilter = array(
    'IBLOCK_ID' => 17,
    'PROPERTY_manufacturer_VALUE' => array('Ariston','Indesit','Hotpoint-Ariston'),
    'ACTIVE' => 'Y'
);

$aSelect = array(
    'ID',
    'IBLOCK_ID',
    'PROPERTY_MANUFACTURER'
);

$rProduct = impelCIBlockElement::GetList(
    ($order = Array('ID' => 'DESC')),
    $aFilter,
    false,
    false,
    $aSelect
);

$iElement = new impelCIBlockElement;

$moreImage = [];
$aCodes = [];

$rFp = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/zero_indcodes.csv','w+');

if($rProduct) {
    while ($aProduct = $rProduct->GetNext()) {

        $rProp  = impelCIBlockElement::GetProperty(
            $aProduct["IBLOCK_ID"],
            $aProduct["ID"],
            Array(),
            Array(
                "CODE" => "SIMPLEREPLACE_INDCODE"
            )
        );

        if ($rProp) {

            while ($aProp = $rProp->getNext()) {

                if ($aProp['VALUE'] > 0 && !isset($aCodes[$aProp['VALUE']])) {

                    $aCodes[$aProp['VALUE']] = 0;

                    $rIndCode = impelCIBlockElement::GetList(
                        ($order = Array('ID' => 'DESC')),
                        ['ID' => $aProp['VALUE']],
                        false,
                        false,
                        ['NAME','ID']
                    );

                    if ($rIndCode && ($aIndCode = $rIndCode->GetNext())) {

                        if (stripos($aIndCode['NAME'],'0') === 0) {

                            fputcsv($rFp,[$aProduct["ID"],$aProduct["PROPERTY_MANUFACTURER_VALUE"],$aIndCode['NAME'],$aIndCode['ID']],';');

                        }

                    }

                }

            }

        }

    }

}

fclose($rFp);