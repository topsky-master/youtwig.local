<?php

//https://youtwig.ru/local/crontab/models_to_csv.php

if(!isset($argv)) die();

define("NO_KEEP_STATISTIC", true);

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

$bSkipMan = false;

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

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$arModelSelect = Array(
    "ID",
    "CODE",
	"NAME",
    "DETAIL_PAGE_URL",
	"PROPERTY_PRODUCTS_REMOVED",
	"ACTIVE"
);

$arModelFilter = Array(
    "IBLOCK_ID" => 17,
);


$resModel = CIBlockElement::GetList(
    ($order = Array(
        'PROPERTY_manufacturer' => 'asc',
        'created' => 'desc'
    )),
    $arModelFilter,
    false,
    false,
    $arModelSelect
);

$sPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/cumodels.csv';

file_put_contents($sPath,'');

if($resModel){
    while($arModel = $resModel->GetNext()){

		file_put_contents($sPath,$arModel['ID'].';'.$arModel['CODE'].';'.$arModel['ACTIVE'].';'.$arModel['PROPERTY_PRODUCTS_REMOVED_VALUE']."\n",FILE_APPEND);

    }
}
