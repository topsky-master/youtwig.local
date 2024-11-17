#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("__MAX_MODELS",800);
define("__COUNT_STRINGS",200);
define("__FILE_MODELS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/models_last.txt');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$currentCount = 0;
CModule::includeModule("iblock");

$arModSelect = Array(
    "ID"
);

$arModFilter = Array(
    "IBLOCK_ID" => 17,
    "ACTIVE" => "N"
);

$dbModRes = CIBlockElement::GetList(
    Array(),
    $arModFilter,
	false,
	array("nTopCount" => 500),
    $arModSelect
);

$count = 0;

while($arModRest = $dbModRes->GetNext()){
    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/users.txt',$arModRest['PROPERTY_USER_VALUE'],FILE_APPEND);
	CIBlockElement::Delete($arModRest['ID']);
	++$count;
}

if($count > 0) {
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/inactive_models.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');
}
