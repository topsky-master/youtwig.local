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

if ($argc > 0 && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;
    define("__COUNT_STRINGS",1000);

} else {
    define("__COUNT_STRINGS",1);

}

if(!isset($_REQUEST['intestwetrust'])) die();

define("__FILE_MODELS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/models_to_man_last.txt');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$chained_products = array();
$currentCount = 0;

if(CModule::IncludeModule("iblock")) {
    $params = Array(
        "max_len" => "100",
        "change_case" => "L",
        "replace_space" => "_",
        "replace_other" => "_",
        "delete_repeat_replace" => "true",
    );

    $dbPeMan = CIBlockPropertyEnum::GetList(
        Array(),
        Array(
            "IBLOCK_ID" => 11,
            "CODE" => "MANUFACTURER"
        )
    );

    $pmanufacturers = array();
    $cmanufacturers = array();

    if($dbPeMan){
        while($arrPeMan = $dbPeMan->GetNext()) {
            $pmanufacturers[mb_strtolower(trim($arrPeMan["VALUE"]))] = $arrPeMan["ID"];
            $cmanufacturers[mb_strtolower(trim($arrPeMan["XML_ID"]))] = $arrPeMan["ID"];
        }
    }

    $linkEl = new CIBlockElement;
    $enumManNew = new CIBlockPropertyEnum;

    $countStrings = __COUNT_STRINGS;

    if(!file_exists(__FILE_MODELS_NAME)){
        file_put_contents(__FILE_MODELS_NAME,'0');
    }

    // var_dump("models_to_manufacturers =>", "3");
    $skip = file_get_contents(__FILE_MODELS_NAME);
    $skip = trim($skip);
    $skip = (int)$skip;
    $skip = !is_numeric($skip) ? 0 : $skip;

    if($skip == 0){
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_manufacturer_log.txt','');
    }

    //$skip = 0;

    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_MODEL",
        "PROPERTY_MANUFACTURER_DETAIL",
        "NAME"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    if($skip > 0){
        $arPFilter[">ID"] = $skip;
	}

	//$arPFilter["ID"] = 241285;
	
    $order = Array(
        "ID" => "ASC"
    );
    
    // var_dump("models_to_manufacturers =>", $order, $arPFilter, $arPSelect);

    $dbPres = CIBlockElement::GetList($order, $arPFilter, false, false, $arPSelect);
    
    // var_dump("models_to_manufacturers =>", $dbPres);
    // exit(0); 

    if ($dbPres) {

        while ($arResult = $dbPres->GetNext()) {

            $manufacturers = array();

            $models_html = "";

            $arMSelect = Array(
                "ID",
                "NAME",
                "PROPERTY_manufacturer"
            );

            $arMFilter = Array(
                "IBLOCK_ID" => 17,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "PROPERTY_SIMPLEREPLACE_PRODUCTS" => $arResult['ID']
            );

            $dbMres = impelCIBlockElement::GetList(Array(), $arMFilter, false, false, $arMSelect);
			
			if($dbMres){
				
				while ($arMRes = $dbMres->GetNext()) {

					$manufacturers[$arMRes["PROPERTY_MANUFACTURER_VALUE"]] = $arMRes["PROPERTY_MANUFACTURER_VALUE"];

				}

				$sManufacturer = array();

				if(!empty($manufacturers)){

					foreach($manufacturers as $manKey => $manName) {

						$foundAny = false;

						if(isset($pmanufacturers[mb_strtolower(trim($manName))])){

							$sManufacturer[$manKey] = $pmanufacturers[mb_strtolower(trim($manName))];
							$foundAny = true;

						} else {

							$xml_id = mb_strtolower(trim(CUtil::translit(trim($manKey), LANGUAGE_ID, $params)));
							$xml_count = 1;
							$xml_string = $xml_id;

							if(isset($cmanufacturers[$xml_string])){

								$xml_string = ($xml_id.'_'.$xml_count);

								while (isset($cmanufacturers[$xml_string])){
									++$xml_count;
									$xml_string = ($xml_id.'_'.$xml_count);
								}
							}

							if(!isset($cmanufacturers[trim(CUtil::translit(trim($manKey), LANGUAGE_ID, $params))])
								&& $manPropID = $enumManNew->Add(
									Array(
										'PROPERTY_ID' => 44,
										'VALUE' => trim($manKey),
										'XML_ID' => ($xml_string)
									)
								)
							){
								$sManufacturer[$manKey] = $pmanufacturers[mb_strtolower(trim($manName))] = $manPropID;
								$cmanufacturers[trim(CUtil::translit(trim($manKey), LANGUAGE_ID, $params))] = $manPropID;

								$foundAny = true;

							}

						}


					}

					if(!empty($sManufacturer)){
						
						CIBlockElement::SetPropertyValuesEx($arResult['ID'], 11, array('MANUFACTURER' => array_values($sManufacturer)));
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arResult['ID']);

					}

				} else {

					CIBlockElement::SetPropertyValuesEx($arResult['ID'], 11, array('MANUFACTURER' => false));
					\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arResult['ID']);
					file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_manufacturer_log.txt','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$arResult['ID']."\n",FILE_APPEND);

				}

            //MANUFACTURER 11
			
			}

            echo '.';
            echo $arResult["ID"];


            ++$currentCount;

            if($currentCount >= $countStrings){
                file_put_contents(__FILE_MODELS_NAME,$arResult["ID"]);
                die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/crontab/models_to_manufacturers.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');
            }
			
			

        }

    }

    file_put_contents(__FILE_MODELS_NAME,'0');
    echo 'done';

}
