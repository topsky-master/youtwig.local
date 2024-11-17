#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

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
    define("__COUNT_STRINGS",1000);

} else {
    define("__COUNT_STRINGS",1);

}

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;


CModule::IncludeModule('iblock');

$arPSelect = Array(
    "ID",
	"CODE"
);

$arPFilter = Array(
    "IBLOCK_ID" => 17,
	"ACTIVE" => "Y"
);

$dbPres = impelCIBlockElement::GetList(
    Array('ID' => 'ASC'),
    $arPFilter,
    false,
    false,
    $arPSelect);

if ($dbPres) {
    while ($arPRes = $dbPres->GetNext()) {
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/mcodes.txt',$arPRes['ID'].';'.$arPRes['CODE']."\n",FILE_APPEND);
	}
}
