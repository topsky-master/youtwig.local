#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/check_ddoubles.php?intestwetrust=1

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

$aDoubles = array();

if ($argc > 0 && $argv[0]) {
	
    $_REQUEST['intestwetrust'] = 1;
    define("__COUNT_STRINGS",200000);
			
} else {
	
    define("__COUNT_STRINGS",20);
}

if(!isset($_REQUEST['intestwetrust'])) die();

define("__FILE_MODELS_CSV",dirname(dirname(__DIR__)).'/bitrix/tmp/doubles.csv');

if(!file_exists(__FILE_MODELS_CSV)){
	file_put_contents(__FILE_MODELS_CSV,'');
}

$fp = fopen(__FILE_MODELS_CSV,'w+');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$chained_products = array();
$currentCount = 0;

if(CModule::IncludeModule("iblock")) {
	
	$oiElt = new CIBlockElement;

    $aPSelect = Array(
        "ID",
     	"NAME"
	);

    $aPFilter = Array(
        "IBLOCK_ID" => 27,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
	);
	
	$aPOrder = Array(
        "ID" => "ASC"
    );

    $rpDb = CIBlockElement::GetList($aPOrder, $aPFilter, false, false, $aPSelect);

    if ($rpDb) {
		
		while ($aResult = $rpDb->GetNext()) {
						
			$ifProductId = $aResult["ID"];
				
			$aMSelect = Array(
				"ID",
				"NAME",
				"PROPERTY_type_of_product"
			);

			$aMFilter = Array(
				"IBLOCK_ID" => 17,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"PROPERTY_MODEL_NEW_LINK" => $aResult["ID"]
			);
			
			$aMOrder = Array(
				"ID" => "ASC"
			);
			
			$rmDb = CIBlockElement::GetList($aMOrder, $aMFilter, false, false, $aMSelect);
					
			$aDoubles = array();		
			
			$iCount = 0;
					
			if($rmDb)		
				while($amResult = $rmDb->GetNext()){	

					if(!isset($aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']])){
					
						$aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']] = array();
					
					}
					
					if(!isset($aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$amResult['ID']])){
						
						++$iCount;
						
						$snLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=27&type=catalog&ID='.$aResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
						$smLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$amResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
												
						$aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$amResult['ID']] = array($amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE'], $amResult['ID'],$amResult['NAME'],$smLink,$aResult["NAME"],$snLink);
					}
				}

			if($iCount > 1){
				foreach($aDoubles as $spType){
					foreach($spType as $aType){
						fputcsv($fp,$aType,';');
					}
					
				}
			}
					 
		
		}
		
	}
	
	
    if(!$ifProductId)
        echo 'done';
	
	fclose($fp);
}
