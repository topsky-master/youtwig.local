#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

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

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$chained_products = array();

if(CModule::IncludeModule("iblock")){



    $arSelect = Array("ID","PROPERTY_ARTNUMBER");
    $arFilter = Array("IBLOCK_ID" => 11);
    $dbRes = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    $counter = 0;

    if($dbRes){
        while($arFields = $dbRes->GetNext()){
			
            $PROPERTY_VALUE = 'TWG000'.$arFields["ID"];
			
			if (checkEltPropertyChange($PROPERTY_VALUE,'ARTNUMBER',$arFields["ID"],11)) {
					$fieldsUpdate = array('ARTNUMBER' => $PROPERTY_VALUE);

					setEltPropertyValuesEx(
						$arFields["ID"],
						11,
						$fieldsUpdate);
					
			}
			
			++$counter;

        }
    }


    global $USER;

    $chained_products                               = array();

    if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")){

        $arSelect 									= Array("ID", "NAME", "PROPERTY_MAIN_PRODUCTS","PROPERTY_ARTNUMBER");
        $arFilter 									= Array("IBLOCK_ID" => 11, "!PROPERTY_MAIN_PRODUCTS" => false);
        $res 										= CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        $product									= array();


        $quantity									= 0;

        while($res
            && ($product						= $res->Fetch())){

            if(isset($product['PROPERTY_MAIN_PRODUCTS_VALUE'])
                && !empty($product['PROPERTY_MAIN_PRODUCTS_VALUE'])){

                if(!isset($chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']])){
                    $product[$chained_products['PROPERTY_MAIN_PRODUCTS_VALUE']] = array();
                }

                if(is_array($chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']]) && !in_array($product['PROPERTY_ARTNUMBER_VALUE'],$chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']])){
                    $chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']][] = $product['PROPERTY_ARTNUMBER_VALUE'];
                }
            }

        }

        if(!empty($chained_products)){
            foreach($chained_products as $product_id=>$product_names){

                $product_names                      = join("\n",$product_names);
                $PROPERTY_CODE 				        = "ARTNUMBER_CATALOG";
                $PROPERTY_VALUE 			        = $product_names;

                CIBlockElement::SetPropertyValues($product_id, 16, $PROPERTY_VALUE, $PROPERTY_CODE);
                //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $product_id);

            }
        }


    }


}



