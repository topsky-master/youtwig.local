#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/sites/data/www/dev.youtwig.ru/";

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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$filterOrderSelection = array();

$arSelectFields = array(
	"ORDER_ID","DATE_INSERT","ID"
);

$by = "DATE_INSERT";
$order = "ASC";

if (!empty($by) && in_array($by, $arSelectFields))
{
	if (!isset($order))
		$order = "DESC";
	$filterOrderSelection[mb_strtoupper($by)] = $order;
}

$arFilterTmp = array(
	array(
		"LOGIC" => "AND",
		">=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime(0, 0, 0, 2, 1, 2021)),
		"<=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime(0, 0, 0, 8, 31, 2021)),
	)
);


$orderIterator = \Bitrix\Sale\Internals\OrderArchiveTable::getList(array(
	'filter' => $arFilterTmp,
	'select' => $arSelectFields,
	'order' => $filterOrderSelection,
));

if($orderIterator)
while($order = $orderIterator->fetch())
{
	print_r($order);
	$aReturn = \Bitrix\Sale\Archive\Manager::returnArchivedOrder($order['ORDER_ID']);
	print_r($aReturn);
	die();
	
}
