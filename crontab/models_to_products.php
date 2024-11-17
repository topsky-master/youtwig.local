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
    define("__COUNT_STRINGS",200000);
    $arpParams = false;
			
} else {
	
    define("__COUNT_STRINGS",20);
    $arpParams = false;
}

if(!isset($_REQUEST['intestwetrust'])) die();

//define('PRINT_IT',true);
define("__FILE_MODELS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/models_last.txt');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
$chained_products = array();
$currentCount = 0;

if(CModule::IncludeModule("iblock")) {

    $countStrings = __COUNT_STRINGS;

    if(!file_exists(__FILE_MODELS_NAME)){
        file_put_contents(__FILE_MODELS_NAME,'0');
    }

    $skip = file_get_contents(__FILE_MODELS_NAME);
    $skip = trim($skip);
    $skip = (int)$skip;
    $skip = !is_numeric($skip) ? 0 : $skip;
    //$skip = 0;
    
    $arPSelect = Array(
        "ID",
        "IBLOCK_ID",
        "PROPERTY_MODEL",
        "PROPERTY_HIDE_MODELS",
        "NAME"
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );

    if($skip > 0 && !($argc > 0 && $argv[0])){
        $arPFilter[">ID"] = $skip;
    }

    $order = Array(
        "ID" => "ASC"
    );

    $dbPres = CIBlockElement::GetList($order, $arPFilter, false, $arpParams, $arPSelect);

    $howMuch = 0;

    $ifProductId = 0;

    if ($dbPres) {
        while ($arResult = $dbPres->GetNext()) {

			$ifProductId = $arResult["ID"];
			$iSubCount = 0;

			if(empty($ifProductId)) continue;

            ++$howMuch;

            $chained_products = array(); 
			$models_html = "";

            $arMSelect = Array(
                "ID",
                "PROPERTY_model_new_link",
                "DETAIL_PAGE_URL",
                "NAME",
                "PROPERTY_manufacturer"
            );

            $arMFilter = Array(
                "IBLOCK_ID" => 17,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "PROPERTY_SIMPLEREPLACE_PRODUCTS" => $arResult['ID'],
                '!PROPERTY_VERSION_VALUE' => 'Да'
            );

			$dbMres = impelCIBlockElement::GetList(Array(), $arMFilter, false, false, $arMSelect);
            //usleep(100);
			
			if ($dbMres
                && ($arResult["PROPERTY_HIDE_MODELS_VALUE"] != "Скрыть")) {

                while ($arMRes = $dbMres->GetNext()) {

					//usleep(100);
					
					$dbMNNres = CIBlockElement::GetById($arMRes["PROPERTY_MODEL_NEW_LINK_VALUE"]);

					++$iSubCount;

					if ($dbMNNres
						&& $arMNNRes = $dbMNNres->GetNext()
					){

						if(!isset($chained_products[$arMRes['DETAIL_PAGE_URL']]) 
							&& isset($arMNNRes['NAME']) 
						&& !empty($arMNNRes['NAME'])){
							
							$chained_products[$arMRes['DETAIL_PAGE_URL']] = $arMRes['DETAIL_PAGE_URL'];
							$models_html .= '<a href="'.$arMRes['DETAIL_PAGE_URL'].'">' . trim($arMRes["PROPERTY_MANUFACTURER_VALUE"] . ' ' . $arMNNRes['NAME']) . "</a>\n";
						
						}
					}
                }
            }
		
			$arMLFilter = array(
                'IBLOCK_ID' => 37,
                'PROPERTY_products' => $arResult['ID']
            );

			$dbMLres = CIBlockElement::GetList(Array(), $arMLFilter, false, false);

            $foundLink = false;

            if($dbMLres
                && $arMLres = $dbMLres->GetNext()){

                if(isset($arMLres['ID'])
                    && !empty($arMLres['ID'])){

                    $foundLink = $arMLres['ID'];

                }

            }
			
			$linkEl = new CIBlockElement;

            if(!$foundLink){

                $arLEl = Array(
                    "NAME" => $arResult['NAME'],
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => 37,
                    "PREVIEW_TEXT" => " ",
                    "DETAIL_TEXT" => $models_html,
                    "DETAIL_TEXT_TYPE" => "html"
                );

                if($foundLink = $linkEl->Add($arLEl)){

                }

            } else {

                $arLEl = Array(
                    "DETAIL_TEXT" => $models_html,
					'TIMESTAMP_X' => true
				);

                if($linkEl->Update($foundLink, $arLEl)){

                };

            }

			if($foundLink){
                CIBlockElement::SetPropertyValuesEx(
                    $foundLink,
                    37,
                    array('products' => array('VALUE' => $arResult['ID'], 'DESCRIPTION' => ''))
                );
            }

            if($foundLink){
                CIBlockElement::SetPropertyValuesEx(
                    $arResult['ID'],
                    11,
                    array('MODEL_HTML' => array('VALUE' => $foundLink, 'DESCRIPTION' => ''))
                );
				
				\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arResult['ID']);

            }
			
			++$currentCount;

        }
    }

    file_put_contents(__FILE_MODELS_NAME, $ifProductId);

    if(!$ifProductId )
        echo 'done';

}
