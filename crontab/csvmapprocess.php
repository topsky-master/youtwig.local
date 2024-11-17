#!/usr/bin/php -q
<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) {
    die();
}

//https://youtwig.ru/local/crontab/more_photo.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = '/var/www/twig/data/www/twig.d6r.ru/';

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

$_REQUEST['action'] = 'do_process';

$APPLICATION->IncludeComponent(
    "impel:csvmap",
    "",
    Array(
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "BLOCK_TITLE" => "Подобрать запасную часть или аксессуар"
    )
);
