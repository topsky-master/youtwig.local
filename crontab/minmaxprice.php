#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/minmaxprice.php?intestwetrust=1

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

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

function getFiltersMinPrice($aSort){

    $priceMin = ['CATALOG_PRICE_1' => 0];

    $aFilters['!CATALOG_PRICE_1'] = false;
    $aFilters['ACTIVE'] = 'Y';
    $aFilters['GLOBAL_ACTIVE'] = 'Y';

    $aSelect = ["CATALOG_GROUP_1"];
    $dRes = CIBlockElement::GetList($aSort, $aFilters, false, Array("nTopCount"=>1), $aSelect);

    if($dRes) {
        $priceMin = $dRes->GetNext();
    }

    return $priceMin['CATALOG_PRICE_1'];

}

$json = [];
$json['min_price'] = getFiltersMinPrice(['catalog_PRICE_1' => 'ASC']);
$json['max_price'] = getFiltersMinPrice(['catalog_PRICE_1' => 'DESC']);

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/price_range.txt',serialize($json));
