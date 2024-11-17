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



$declension_products_models = unserialize(COption::GetOptionString("my.stat", "declension_products", array()) || "");
$declension_productsSizeof = (!empty($declension_products_models)
    && isset($declension_products_models['type_of_product']))
    ?  (sizeof($declension_products_models['type_of_product'])) : 0;



if($declension_productsSizeof > 0) {

    $product_to_decl = Array();

    for($it = 0; $it < $declension_productsSizeof; $it++){

        if(
            isset($declension_products_models['declension'][$it])
            && !empty($declension_products_models['declension'][$it])
            && isset($declension_products_models['type_of_product'][$it])
            && !empty($declension_products_models['type_of_product'][$it])
            ) {

            $product_to_decl[$declension_products_models['type_of_product'][$it]] =
                $declension_products_models['declension'][$it];

        }
    }
 

    if(!empty($product_to_decl)) {

        $aDSelect = Array(
            "ID",
            "PROPERTY_TYPEPRODUCT"
        );

        $aDFilter = Array(
            "IBLOCK_ID" => 11,
            "PROPERTY_TYPEPRODUCT" => array_keys($product_to_decl)
        );


        $dDecl = CIBlockElement::GetList(Array(), $aDFilter, false, false, $aDSelect);

        if($dDecl){

            while($aDecl = $dDecl->GetNext()){

                $declension = isset($aDecl["PROPERTY_TYPEPRODUCT_ENUM_ID"])
                    && !empty($aDecl["PROPERTY_TYPEPRODUCT_ENUM_ID"])
                    && isset($product_to_decl[$aDecl["PROPERTY_TYPEPRODUCT_ENUM_ID"]])
                    && !empty($product_to_decl[$aDecl["PROPERTY_TYPEPRODUCT_ENUM_ID"]])
                    ? trim($product_to_decl[$aDecl["PROPERTY_TYPEPRODUCT_ENUM_ID"]])
                    : '';

                $product_id = $aDecl['ID'];

                CIBlockElement::SetPropertyValuesEx(
                    $product_id,
                    11,
                    array('DECLENSION' => array('VALUE' => $declension))
                );

                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $product_id);


            }

        }

    }

}

?>