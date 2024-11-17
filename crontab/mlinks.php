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

$rfp = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/tmp_links.csv','r');
$rfp1 = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/wredirects.csv','w+');
$rfp2 = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/nfound.csv','w+');

$iFirst = true;

if($rfp)
while($aLinks = fgetcsv($rfp,0,';')){

	if($iFirst){
		$iFirst = false;
		continue;
	}
	
	$aOrig = $aLinks = array_map('trim',$aLinks);
	
	$arSelect = array('ID','DETAIL_PAGE_URL');

	$aLinks[2] = iconv('windows-1251','utf-8//ignore',$aLinks[2]);
	$aLinks[2] = trim(preg_replace('~^(.*?)(купить в Москве.*$)~isu',"$1",$aLinks[2]));
	
	$arFilter = array('ACTIVE' => 'Y','IBLOCK_ID' => 11, 'NAME' => $aLinks[2]);
	
	$dbRes = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
	$lFound = false;
	if($dbRes){

		while($arFields = $dbRes->GetNext()) {
			if(isset($arFields['DETAIL_PAGE_URL']) && !empty($arFields['DETAIL_PAGE_URL'])){
				$lFound = true;
				$sFrom = trim(preg_replace('~http(.*?)://[^/]+?/~isu','/',$aLinks[0]),'/');
				$sTo = '/'.trim($arFields['DETAIL_PAGE_URL'],'/').'/';
				fputcsv($rfp1,array($sFrom,$sTo),';');
			}
		}
		
	} 
	
	if(!$lFound) {
		
		$arFilter = array('ACTIVE' => 'Y','IBLOCK_ID' => 11, 'NAME' => $aLinks[2].'%');
	
		$dbRes = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
		$lFound = false;
		if($dbRes){

			while($arFields = $dbRes->GetNext()) {
				if(isset($arFields['DETAIL_PAGE_URL']) && !empty($arFields['DETAIL_PAGE_URL'])){
					$lFound = true;
					$sFrom = trim(preg_replace('~http(.*?)://[^/]+?/~isu','/',$aLinks[0]),'/');
					$sTo = '/'.trim($arFields['DETAIL_PAGE_URL'],'/').'/';
					fputcsv($rfp1,array($sFrom,$sTo),';');
				}
			}
			
		}
			
	}
	
	if(!$lFound){
		fputcsv($rfp2,$aOrig,';');
	}

}

fclose($rfp);
fclose($rfp1);
fclose($rfp2);