#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/add_redirects.php?intestwetrust=1

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

$rfp = fopen($_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/credirects.csv','r');
$rfp1 = fopen($_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/wredirects.csv','w+');

//132398

$rsData = CBXShortUri::GetList(
	Array("ID" => "DESC"),
	false,
	Array("nTopCount" => 1)
);

$asData = $rsData->Fetch();

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/credirects_last.txt',$asData['ID']);

while($aFp = fgetcsv($rfp,0,';')) {

	$aFp = array_map('trim',$aFp);

	$what = $aFp[0];
	$where = $aFp[1];

	$what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
    $what = rtrim($what,'/');

    $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
	$where = empty($where) ? "/" : $where;

	$show = false;
	$rsData = CBXShortUri::GetList(
		Array(),
		Array(
			"URI" => '/'.trim($where,'/').'/',
			"SHORT_URI" => trim($what,'/')
		)
	);

	while($arRes = $rsData->Fetch()) {
		$show = true;
	}

	$rsData = CBXShortUri::GetList(
		Array(),
		Array(
			"URI" => '/'.trim($what,'/').'/',
			"SHORT_URI" => trim($where,'/')
		)
	);

	while($arRes = $rsData->Fetch()) {
		$show = true;
	}

	
	if (!$show
		&&
		(
			(trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
			|| (trim(($where),'/') != trim(($what),'/'))
		)
	){

		fputcsv($rfp1,array($what,$where),';');		

		$arShortFields = Array(
			"URI" => '/'.trim($where,'/').'/',
			"SHORT_URI" => trim($what,'/'),
			"STATUS" => "301",
		);

		CBXShortUri::Add($arShortFields);
	}
	
	if (isset($_GET['test'])) {
		die();
	}
		
}

fclose($rfp);
fclose($rfp1);

unlink($_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/credirects.csv');
