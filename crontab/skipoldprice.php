#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/skipoldprice.php?intestwetrust=1
//https://dev.qtwig.com/local/crontab/skipoldprice.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/twig.d6r.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('DIFF_PERCENTS',10);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
    define('SIZE_LIMIT',99990);
} else {
    define('SIZE_LIMIT',50);
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$uElt = new CIBlockElement;

$sFile = $_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/oldprice_last.txt';

if (!file_exists($sFile)) {
    file_put_contents($sFile,0);
}

$skip = trim(file_get_contents($sFile));

$chained_products = array();
$PRICE_TYPE_ID = 1;
$iFound = false;

$aNavParams = Array(
    'nTopCount' => false,
    'iNumPage' => $skip,
    'nPageSize' => SIZE_LIMIT,
    'checkOutOfRange' => true
);

if(CModule::IncludeModule("iblock")
    && CModule::IncludeModule("catalog")){

    global $USER;

    $chained_products = array();

    $arSelect = Array("ID", "NAME", "PROPERTY_OLD_PRICE", "PROPERTY_PREVIOUS_PRICE");
    $arFilter = Array("IBLOCK_ID" => 11, "!PROPERTY_OLD_PRICE" => false,);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, $aNavParams, $arSelect);
    $product = array();

    $quantity = 0;

    while($res
        && ($product = $res->Fetch())){

        $toPrevPrice = [];
        $toPrevPrice['OLD_PRICE'] = Array("VALUE" => "");
        CIBlockElement::SetPropertyValuesEx($product['ID'], 11, $toPrevPrice);
        $uElt->Update($product['ID'],['TIMESTAMP_X' => 'Y']);

    }

}

if(!empty($iFound)){
    ++$skip;
    file_put_contents($sFile, $skip);
    die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/oldprice.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');
} else {
    file_put_contents($sFile, 0);
    echo 'done';
    die();
}
