#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/order_archive.php?intestwetrust=1

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

}

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("__FILE_MODELS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/models_to_man_last.txt');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$chained_products = array();
$currentCount = 0;

$parameters = [
    'filter' => [
		"<=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), (time() - 3600)),
   		"DELIVERY_ID" => Array(5,41)
    ],
    'order' => ["DATE_INSERT" => "DESC"]
];

$dbRes = \Bitrix\Sale\Order::getList($parameters);

if($dbRes){

    while ($order = $dbRes->fetch())
    {
		if(isset($order["ID"])
		   && !empty($order["ID"])){
			echo $order["ID"]."<br />";
			\Bitrix\Sale\Archive\Manager::archiveOrders(
                array(
                    "ID" => array($order["ID"])
                )
			);
		}
    }

}