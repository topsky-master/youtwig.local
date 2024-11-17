#!/usr/bin/php -q
<?php


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

/*$sModels = 'MQ3000WH
"MQ 3000 WH"
MQ3005WH
"MQ 3005 WH"
MQ3025WH
"MQ 3025 WH"
MQ3035WH
"MQ 3035 WH"
MQ500SOUP
"MQ 500 SOUP"
MQ520PASTA
"MQ 520 PASTA"
MQ525OMELETTE
"MQ 525 OMELETTE"
MQ535SAUCE
"MQ 535 SAUCE"
MQ545APERITIVE
"MQ 545 APERITIVE"
MQ700SOUP
"MQ 700 SOUP"
MQ735SAUCE
"MQ 735 SAUCE"
MQ745APERITIVE
"MQ 745 APERITIVE"
MQ775PATIS
"MQ 775 PATIS"
MQ725OMELETTE
"MQ 725 OMELETTE"
MQ787Gourmet
"MQ 787 Gourmet"
MQ9037X
MR400
"MR 400"
MR4000
"MR 4000"
MR405
"MR 405"
MR4050
"MR 4050"
MR530
"MR 530"
MR540
"MR 540"
MR550
"MR 550"
MR5500
"MR 5500"
MR555
"MR 555"
MR5550
"MR 5550"
MR5555
"MR 5555"
MR570
"MR 570"
MR6500
"MR 6500"
MR6550
"MR 6550"
MQ3020WH
"MQ 3020 WH"
MQ325Spaghetti
"MQ 325 Spaghetti"
MR730
"MR 730"
MR740
"MR 740"
PHB0711L
"PHB 0711L"
PHB0712L
"PHB 0712L"
PHB0713AL
"PHB 0713AL"
PHB0815AL
"PHB 0815AL"
PHB0816AL
"PHB 0816AL"
PHB0817L
"PHB 0817L"
RHB-2908
RHB-2912
RHB-2913
RHB-2915
RHB-2935
RHB-2938
RHB-2939
RHB-2940
RHB-2941
RHB-2942
RHB-2943
RHB-2944
RHB-2945
RHB-CB2931
SL-1547
SC-HB42F50
SC-HB42F41
';

$aModels = explode("\n",$sModels);
$aModels = array_map(function($sVal){ 
	$sVal = trim($sVal);
	$sVal = trim($sVal,'"');
	return $sVal; 
},$aModels);

$aModels = array_filter($aModels);*/

$aDoubles = array();

if ($argc > 0 && $argv[0]) {
	
    $_REQUEST['intestwetrust'] = 1;
    define("__COUNT_STRINGS",200000);
			
} else {
	
    define("__COUNT_STRINGS",20);
}

if(!isset($_REQUEST['intestwetrust'])) die();

//define('PRINT_IT',true);
define("__FILE_MODELS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/models_doubles.txt');


define("__FILE_MODELS_FOUND",dirname(dirname(__DIR__)).'/bitrix/tmp/doubles.php');

define("__FILE_MODELS_CSV",dirname(dirname(__DIR__)).'/bitrix/tmp/doubles.csv');
define("__FILE_MODELS_BCSV",dirname(dirname(__DIR__)).'/bitrix/tmp/bdoubles.csv');


if(!file_exists(__FILE_MODELS_FOUND)){
	file_put_contents(__FILE_MODELS_FOUND,'<?php $auModels = array(); ?>');
}

if(!file_exists(__FILE_MODELS_CSV)){
	file_put_contents(__FILE_MODELS_CSV,'');
}

if(filesize(__FILE_MODELS_CSV) > 0){
	$fp = fopen(__FILE_MODELS_CSV,'a+');
} else {
	$fp = fopen(__FILE_MODELS_CSV,'w+');
}

if(!file_exists(__FILE_MODELS_BCSV)){
	file_put_contents(__FILE_MODELS_BCSV,'');
}

if(filesize(__FILE_MODELS_BCSV) > 0){
	$fp1 = fopen(__FILE_MODELS_BCSV,'a+');
} else {
	$fp1 = fopen(__FILE_MODELS_BCSV,'w+');
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

require_once(__FILE_MODELS_FOUND);

global $USER;

$chained_products = array();
$currentCount = 0;

if(CModule::IncludeModule("iblock")) {
	
	$oiElt = new CIBlockElement;

    $countStrings = __COUNT_STRINGS;

    if(!file_exists(__FILE_MODELS_NAME)){
        file_put_contents(__FILE_MODELS_NAME,'0');
    }

    $iSkip = file_get_contents(__FILE_MODELS_NAME);
    $iSkip = trim($iSkip);
    $iSkip = (int)$iSkip;
    $iSkip = !is_numeric($iSkip) ? 0 : $iSkip;
    //$iSkip = 0;

    $aPSelect = Array(
        "ID",
        "IBLOCK_ID",
		"CODE",
        "PROPERTY_model_new_link",
        "PROPERTY_PRODUCTS_REMOVED",
		"NAME",
		"PROPERTY_type_of_product"
    );

    $aPFilter = Array(
        "IBLOCK_ID" => 17,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
		"PROPERTY_VERSION" => false,
		//"PROPERTY_PRODUCTS_REMOVED" => false
	);
	
	$aOrder = Array(
        "ID" => "ASC"
    );

    $rmDb = CIBlockElement::GetList($aOrder, $aPFilter, false, false, $aPSelect);

    if ($rmDb) {
		
		while ($aResult = $rmDb->GetNext()) {
						
			$ifProductId = $aResult["ID"];
				
			if(!isset($auModels[$aResult['ID']]) 
				//&& empty($aResult['PROPERTY_PRODUCTS_REMOVED_VALUE']) 
				&& empty($aResult['PROPERTY_VERSION_VALUE'])){
				
				$auModels[$aResult['ID']] = $aResult['ID'];
				
				$rnDb = CIBlockElement::GetById($aResult['PROPERTY_MODEL_NEW_LINK_VALUE']);
					
				if($rnDb && ($anResult = $rnDb->GetNext())){	

					$aModelName = $aoModelName = trim($anResult['NAME']);
						
					$aModelName = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$aModelName));
					$aModelName = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$aModelName));
					$aModelName = trim(str_ireplace('(','',$aModelName));
					$aModelName = trim(str_ireplace(')','',$aModelName));
					$aModelName = trim(preg_replace('~\s+~isu','',$aModelName));
					$aModelName = trim($aModelName, '/\\-()');
					
					if(in_array($aModelName,$aModels) || in_array($aoModelName,$aModels) || empty($aModels)){	
						
						if($aModelName != $aoModelName){
								
							$adSelect = Array(
								"ID",
								"NAME",
								"CODE",
							);

							$adFilter = Array(
								"IBLOCK_ID" => 27,
								"=NAME" => $aModelName,
								"!ID" => $anResult['ID'],
							);
										
							$rdDb = CIBlockElement::GetList($aOrder, $adFilter, false, false, $adSelect);
							
							if($rdDb) 
								while($adResult = $rdDb->GetNext()){
								
									if($aResult['ID'] != $adResult['ID']){
												
										$admSelect = Array(
											"ID",
											"NAME",
											"CODE",
										);

										$admFilter = Array(
											"IBLOCK_ID" => 17,
											"PROPERTY_model_new_link" => $adResult['ID'],
											"PROPERTY_type_of_product_VALUE" => $aResult["PROPERTY_TYPE_OF_PRODUCT_VALUE"] 
										);
										
										$rdmDb = CIBlockElement::GetList($aOrder, $admFilter, false, false, $admSelect);
								
										if($rdmDb) 
											while($admResult = $rdmDb->GetNext()){
					
												$auModels[$admResult['ID']] = $admResult['ID'];
									
												$soLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$aResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
												$sdLink = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$admResult['ID'].'&lang=ru&find_section_section=0&WF=Y';
												
												fputcsv($fp,array($aResult['NAME'],$admResult['NAME'],$soLink,$sdLink),";");
											
												if((mb_stripos($aResult['NAME'],')') !== false || mb_stripos($aResult['NAME'],'(') !== false) && false) {
												
												
													$show = false;
											
													$what = '/model/'.$admResult['CODE'].'/';
											
													$what = trim($what);
													$what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
													$what = rtrim($what,'/');

													$where = '/model/'.$aResult['CODE'].'/';
											
													$where = trim($where);
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
														break;
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
														break;
													}

													if (!$show
														&&
														(
															(trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
															|| (trim(($where),'/') != trim(($what),'/'))
														)
													){

														$arShortFields = Array(
															"URI" => '/'.trim($where,'/').'/',
															"SHORT_URI" => trim($what,'/'),
															"STATUS" => "301",
														);

														CBXShortUri::Add($arShortFields);

													}
												
													$toBaseProducts = array();
													
													$toBaseProducts['PRODUCTS_REMOVED'] = 56422;
													impelCIBlockElement::SetPropertyValuesEx($aResult['ID'], 17, $toBaseProducts);
													//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aResult['ID']);

													$oiElt->Update($aResult['ID'],Array('ACTIVE' => 'Y', 'TIMESTAMP_X' => true));
													$oiElt->Update($admResult['ID'],Array('ACTIVE' => 'N', 'TIMESTAMP_X' => true));
													
													fputcsv($fp1,array($aResult['NAME'],$admResult['NAME'],$soLink,$sdLink,'https://youtwig.ru/'.'/'.trim($what,'/').'/','https://youtwig.ru'.'/'.trim($where,'/').'/'),";");
											
												}

					
											}		
											
											
									}
								
								}
								
						}
						
					}
				
				}

			}
		
		}
		
	}
	
	file_put_contents(__FILE_MODELS_FOUND,'<?php $auModels = '.var_export($auModels,true).'; ?>');
	file_put_contents(__FILE_MODELS_NAME,$ifProductId);

    if(!$ifProductId)
        echo 'done';
	
	fclose($fp);
	fclose($fp1);
}
