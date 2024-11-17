#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/check_mmdoubles.php?intestwetrust=1

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

define("__FILE_MODELS_CSV",dirname(dirname(__DIR__)).'/bitrix/tmp/mdoubles.csv');

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
				"PROPERTY_type_of_product",
				"PROPERTY_manufacturer",
			);

			$aMFilter = Array(
				"IBLOCK_ID" => 17,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"PROPERTY_MODEL_NEW_LINK" => $aResult["ID"],
				"PROPERTY_VERSION" => false,
			);
			
			$aMOrder = Array(
				"ID" => "ASC"
			);
			
			$rmDb = CIBlockElement::GetList($aMOrder, $aMFilter, false, false, $aMSelect);
					
			$aDoubles = array();		
			
			$iCount = 0;
					
			if($rmDb){		
				
				$aDoubles = array();
				
				while($amResult = $rmDb->GetNext()){	

					if(!isset($aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']])){
					
						$aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']] = array();
					
					}
					
					++$iCount;
						
					$smLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=27&type=catalog&ID='.$aResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
					$snLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$amResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
						
					if(!isset($aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$amResult['PROPERTY_MANUFACTURER_VALUE']])){
						$aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$amResult['PROPERTY_MANUFACTURER_VALUE']] = array();
					}
						
					$aDoubles[$amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE']][$amResult['PROPERTY_MANUFACTURER_VALUE']][] = array($amResult['PROPERTY_TYPE_OF_PRODUCT_VALUE'],$amResult['PROPERTY_MANUFACTURER_VALUE'],$aResult['ID'],$aResult['NAME'],$snLink,$amResult['ID'],$smLink,$amResult["NAME"]);
			
				}

				if(sizeof($aDoubles) > 0){
						
					foreach($aDoubles as $apType){
						
						if(sizeof($apType) > 1){
								
							foreach($apType as $amType){
								foreach($amType as $aModel){
									fputcsv($fp,$aModel,';');
								}
							}
						
						}
					}
					
				}
			
			}		
		
		}
		
	}
	
	
    if(!$ifProductId)
        echo 'done';
	
	fclose($fp);
}
