#!/usr/bin/php -q
<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
}

//https://youtwig.ru/local/crontab/indcodes_zero_name.php?intestwetrust=1

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

$iElement = new impelCIBlockElement;

$moreImage = [];
$aCodes = [];

$rFp = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/zero_indcodes.csv','r');

if ($rFp) {
    while ($aString = fgetcsv($rFp,0,';')) {

        $aString = array_map('trim',$aString);

        $name = preg_replace('~^0+~isu','',$aString[2]);
        $id = $aString[3];

        if (!empty($name) && $id > 0) {
            $iElement->Update($id,['NAME' => $name,'TIMESTAMP_X' => true]);
        }

    }

    fclose($rFp);
}
