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

$tags_replaces = unserialize(COption::GetOptionString("my.stat", "tags_replaces", array()) || "");
$tagsSizeof = (isset($tags_replaces['name']) && !empty($tags_replaces['name']))
                ?  (sizeof($tags_replaces['name'])) : 1;

$aReplaces = array();

for($counter = 0; $counter < $tagsSizeof; $counter++){

	if(isset($tags_replaces['name'][$counter]) 
		&& !empty($tags_replaces['name'][$counter])
		&& isset($tags_replaces['replace'][$counter])) {

			$tags_replaces['name'][$counter] = trim($tags_replaces['name'][$counter]);
			$aReplaces[$tags_replaces['name'][$counter]] = $tags_replaces['replace'][$counter];	

		}

}            

$aTSelect = Array(
	"ID",
	"TAGS",
	"PROPERTY_TYPEPRODUCT"
);

$aTFilter = Array(
	"IBLOCK_ID" => 11, "ACTIVE" => "Y");

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt')) {
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt',0);
}

$ifId = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt');

if($ifId > 0){
	$aTFilter[">ID"] = $ifId;
}	

$ltId = 0;

$aOrder = array("ID" => "ASC");

$aNavParams = array("nTopCount" => 100);
$dTags = CIBlockElement::GetList($aOrder, $aTFilter, false, $aNavParams, $aTSelect);

$oiElt = new CIBlockElement;
$sTagsIds = "";


if($dTags){

	while($aTags = $dTags->GetNext()){

		$ltId = $aTags['ID'];

		$sTagsIds = $aTags['ID'].";id;\n";
		//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagids.txt',$sTagsIds,FILE_APPEND);

		$anTags = array();

		$amSelect = Array(
			"ID",
			"IBLOCK_ID",
			"PROPERTY_type_of_product",
			"PROPERTY_model_new_link",
			"PROPERTY_manufacturer",
			"PROPERTY_SIMPLEREPLACE_INDCODE",
			"PROPERTY_SIMPLEREPLACE_PRODUCTS"
		);

		$amFilter = Array(
			"IBLOCK_ID" => 17,
			"PROPERTY_SIMPLEREPLACE_PRODUCTS" => $aTags["ID"],
			"ACTIVE" => "Y"
		);

		$dModels = impelCIBlockElement::GetList(Array(), $amFilter, false, false, $amSelect);

		if($dModels){
			while($aModels = $dModels->GetNext()){

				$sTagsIds = $aTags['ID'].';model;'.$aModels['ID']."\n";
				//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagids.txt',$sTagsIds,FILE_APPEND);

				if(isset($aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
					&& !empty($aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){
					$aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'] = trim($aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE']);
					$anTags[$aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE']] = trim($aModels['PROPERTY_TYPE_OF_PRODUCT_VALUE']);
				}

				if(isset($aModels['PROPERTY_MODEL_NEW_LINK_VALUE'])
					&& !empty($aModels['PROPERTY_MODEL_NEW_LINK_VALUE'])){

					$rmnDb = CIBlockElement::GetById($aModels['PROPERTY_MODEL_NEW_LINK_VALUE']);
					
					if($rmnDb && $anModels = $rmnDb->GetNext()){
						
						$anModels['NAME'] = trim($anModels['NAME']);
						$anTags[$anModels['NAME']] = $anModels['NAME'];
					}
					
				
				}
				
				if(isset($aModels['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'])
					&& !empty($aModels['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'])){
						
					foreach($aModels['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'] as $pKey => $pValue){
						
						if($pValue == $aTags['ID']){
							
							if(isset($aModels['PROPERTY_SIMPLEREPLACE_INDCODE_VALUE'])
								&& isset($aModels['PROPERTY_SIMPLEREPLACE_INDCODE_VALUE'][$pKey])
								&& !empty($aModels['PROPERTY_SIMPLEREPLACE_INDCODE_VALUE'][$pKey])
								&& $aModels['PROPERTY_SIMPLEREPLACE_INDCODE_VALUE'][$pKey] != '-') {
									
									$iIndcodeId = trim($aModels['PROPERTY_SIMPLEREPLACE_INDCODE_VALUE'][$pKey]);
									
									$rinDb = CIBlockElement::GetById($iIndcodeId);
									
									if($rinDb && $aiModels = $rinDb->GetNext()){
										
										$aiModels['NAME'] = trim($aiModels['NAME']);
											
										if($aiModels['NAME'] != '-' && $aiModels['NAME'] != 'Без кода'){
											$anTags[$aiModels['NAME']] = $aiModels['NAME'];
										}
									}
				
									
								}
						
						}
						
						
					}		
					
					
								
								
						
				}
				
				
				if(isset($aModels['PROPERTY_MANUFACTURER_VALUE'])
					&& !empty($aModels['PROPERTY_MANUFACTURER_VALUE'])){
					$aModels['PROPERTY_MANUFACTURER_VALUE'] = trim($aModels['PROPERTY_MANUFACTURER_VALUE']);
					$anTags[$aModels['PROPERTY_MANUFACTURER_VALUE']] = trim($aModels['PROPERTY_MANUFACTURER_VALUE']);
				}
				
				
			}
			
			{
				
			if(isset($aTags['PROPERTY_TYPEPRODUCT_VALUE']) && !empty($aTags['PROPERTY_TYPEPRODUCT_VALUE']))
				
				$sOldTags = trim($aTags['TAGS']);
			
				$aTags['TAGS'] = ','.trim($aTags['PROPERTY_TYPEPRODUCT_VALUE']);
				
				$aTags['TAGS'] = explode(',',$aTags['TAGS']);
				$aTags['TAGS'] = array_map('trim',$aTags['TAGS']);
				
				$anTags = array_values($anTags);
				
				$anTags = array_merge($aTags['TAGS'],$anTags);
				$anTags = array_map('trim',$anTags);
				
				foreach($aReplaces as $sWhat => $sWhere){
					
					foreach($anTags as $itNum => $itValue) {
						if(stripos($itValue,$sWhere) === false)
						$anTags[$itNum] = str_ireplace($sWhat,$sWhere,$itValue); 
					}

				}

				$anTags = array_unique($anTags);
				$anTags = array_filter($anTags);
				$snTags = trim(join(',',$anTags));

				if(!empty($snTags)){

					//$sTagsIds = $aTags['ID'].';tags;'.$snTags."\n";
					//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagids.txt',$sTagsIds,FILE_APPEND);
					file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt',$aTags['ID']);
					file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/btagid.txt',$aTags['ID']."\n");

					if ($sOldTags != $snTags) {

						$atProduct = Array('TIMESTAMP_X' => true, 'TAGS' => $snTags);
						$oiElt->Update($aTags['ID'], $atProduct);
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aTags['ID']);
					
					}
					
				}


			}


		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt',$ltId);
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/atagid.txt',$aTags['ID']."\n");


    }

}

unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltlock.txt');


file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/ltagid.txt',$ltId);