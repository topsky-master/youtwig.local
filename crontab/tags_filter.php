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
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$arSelect = array('ID','PROPERTY_SECTION_FILTER','PROPERTY_LINK_HREF','PROPERTY_LINK_NAME','PROPERTY_LINK_DISABLE');
$arFilter = array('ACTIVE' => 'Y','IBLOCK_ID' => 41, '!PROPERTY_SECTION_FILTER_VALUE' => false);

$dbRes = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);

$aLinks = array();

if($dbRes){

	while($arFields = $dbRes->GetNext()) {
		if(isset($arFields['PROPERTY_SECTION_FILTER_VALUE']) && !empty($arFields['PROPERTY_SECTION_FILTER_VALUE'])){
			$aLinks[$arFields['PROPERTY_SECTION_FILTER_VALUE']][] = array($arFields['PROPERTY_LINK_HREF_VALUE'],$arFields['PROPERTY_LINK_NAME_VALUE'],$arFields['PROPERTY_LINK_DISABLE_VALUE']);
		}
	}
}

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/taglinks.php','<?php $atLinks = '.var_export($aLinks,true).'; ?>');
