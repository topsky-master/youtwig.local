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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$updatingFile = $_SERVER['DOCUMENT_ROOT'].'/exchange_log/updating_base.txt';
$mtime = 0;

if(file_exists($updatingFile)){
	$mtime = filemtime($updatingFile);
}
 
if(!file_exists($updatingFile)) {

    file_put_contents($updatingFile,date('Y-m-d H:i:s'));

    $arPSelect = Array(
        "ID",
    );

    $arPFilter = Array(
        "IBLOCK_ID" => 16
	);

    $acOnStock = array();

    $productEl = new CIBlockElement;

    $dbPres = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

    if ($dbPres) {
        while ($arPRes = $dbPres->GetNext()) {

            $product_id = $arPRes['ID'];
            $can_buy = false;
            $quantity = 0;
            $can_buy = canYouBuy($product_id);
            $quantity = get_quantity_product($product_id);
            $can_buy = $quantity > 0 ? $can_buy : false;

            $acOnStock[$product_id] = $can_buy;

            $on_stock = $can_buy == true ? 60693 : 60694;
            $fieldsUpdate = array('ONSTOCK' => array('VALUE' => $on_stock));

            CIBlockElement::SetPropertyValuesEx(
                $product_id,
                16,
                $fieldsUpdate
            );

			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $product_id);

        }

    }


    /* file_put_contents($cacheFile,'<?php $acOnStock = '.var_export($acOnStock,true).'; ?>'); */
    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/exchange_log/rests_updated.txt',date('Y-m-d H:i:s')."\n",FILE_APPEND);
    @unlink($updatingFile);

} else {

    if(($mtime + 36000) < time()) {
        //@unlink($cacheFile);

        if(file_exists($updatingFile)){
            @unlink($updatingFile);
        }

    }

}