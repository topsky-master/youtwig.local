#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/zip_price.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("ZIP_PERCENT", 30);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);


set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if (isset($argc)
    && isset($argv)
    && $argc > 0
    && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;

}

if (!isset($_REQUEST['intestwetrust']))
    die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$aMainFilter = array(
    'IBLOCK_ID' => 16
);

$aMainSelect = array('ID');

$rmDb = impelCIBlockElement::GetList(array(), $aMainFilter, false, false, $aMainSelect);

$aProducts = array();
$aSections = array();

$sProducts = '';

if ($rmDb) {

    while ($amDb = $rmDb->GetNext()) {

        if (isset($amDb['ID'])
            && !empty($amDb['ID'])) {

            $product_buy_id = $amDb['ID'];

            if ($product_buy_id
                && $amDb['ID']) {

                $imId = $amDb['ID'];

                if ($arPrice = CCatalogProduct::GetOptimalPrice(
                    $imId,
                    1,
                    array(2), // anonymous
                    'N',
                    array(),
                    SITE_ID
                )) {

                    if (isset($arPrice['PRICE']['PRICE'])
                        && isset($arPrice['PRICE']['CURRENCY'])) {

                        $minPriceRUR = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']["CURRENCY"], 'RUB');

                        if (!empty($minPriceRUR)) {

                            $pquantity = get_quantity_product_provider($product_buy_id);

                            if ($pquantity) {

                                //ONSTOCK_ZIP ONSTOCK_RTK

                                $ioZip = '';

                                $dProp = CIBlockElement::GetProperty(
                                    16,
                                    $product_buy_id,
                                    array("sort" => "asc"),
                                    array("CODE" => "ONSTOCK_ZIP"));

                                if ($dProp) {
                                    while ($aProp = $dProp->GetNext()) {
                                        $ioZip = $aProp['VALUE'];
                                    }
                                }

                                $ioRtk = '';

                                $dProp = CIBlockElement::GetProperty(
                                    16,
                                    $product_buy_id,
                                    array("sort" => "asc"),
                                    array("CODE" => "ONSTOCK_RTK"));

                                if ($dProp) {
                                    while ($aProp = $dProp->GetNext()) {
                                        $ioRtk = $aProp['VALUE'];
                                    }
                                }

                                if ($ioZip) {

                                    $pprice = 0;

                                    $dProp = CIBlockElement::GetProperty(
                                        16,
                                        $product_buy_id,
                                        array("sort" => "asc"),
                                        array("CODE" => "PROVIDERPRICE"));

                                    if ($dProp) {
                                        while ($aProp = $dProp->GetNext()) {
                                            $pprice = $aProp['VALUE'];
                                        }
                                    }

                                    $pcurr = '';

                                    $dProp = CIBlockElement::GetProperty(
                                        16,
                                        $product_buy_id,
                                        array("sort" => "asc"),
                                        array("CODE" => "PROVIDERPRICECUR"));

                                    if ($dProp) {
                                        while ($aProp = $dProp->GetNext()) {
                                            $pcurr = $aProp['VALUE'];
                                        }
                                    }

                                    if ($pprice > 0) {

                                        switch ($pcurr) {
                                            case 'руб';
                                                break;
                                            case '€':
                                                $pprice = CCurrencyRates::ConvertCurrency($pprice, 'EUR', 'RUB');
                                                break;
                                        }

                                        $minPriceRURMax = $minPriceRUR - $minPriceRUR / 100 * ZIP_PERCENT;

                                        if ($minPriceRURMax < $pprice) {

                                            $dProd = CIBlockElement::GetById($product_buy_id);
                                            $sName = '';

                                            if ($dProd && $aProd = $dProd->GetNext()) {
                                                $sName = $aProd['NAME'];
                                            }

                                            $pCode = '';

                                            $dProp = CIBlockElement::GetProperty(
                                                16,
                                                $product_buy_id,
                                                array("sort" => "asc"),
                                                array("CODE" => "PROVIDERCODE"));

                                            if ($dProp) {
                                                while ($aProp = $dProp->GetNext()) {
                                                    $pCode = trim($aProp['VALUE']);
                                                }
                                            }

                                            $sProducts .= 'Поставщик: ZIP, ZIP код: ' . $pCode . ', Имя товара: ' . $sName . ', Цена поставщика: ' . $pprice . ', Цена на сайте: ' . $minPriceRUR . ', Ссылка для редактирования: https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=16&type=catalog&lang=ru&ID=' . $product_buy_id . '&find_section_section=-1&WF=Y' . "\n";
                                        }

                                    }

                                }

                                if ($ioRtk) {

                                    $pprice = 0;

                                    $dProp = CIBlockElement::GetProperty(
                                        16,
                                        $product_buy_id,
                                        array("sort" => "asc"),
                                        array("CODE" => "PROVIDERPRICE_RTK"));

                                    if ($dProp) {
                                        while ($aProp = $dProp->GetNext()) {
                                            $pprice = $aProp['VALUE'];
                                        }
                                    }

                                    if ($pprice > 0) {

                                        $minPriceRURMax = $minPriceRUR - $minPriceRUR / 100 * ZIP_PERCENT;

                                        if ($minPriceRURMax < $pprice) {

                                            $dProd = CIBlockElement::GetById($product_buy_id);
                                            $sName = '';

                                            if ($dProd && $aProd = $dProd->GetNext()) {
                                                $sName = $aProd['NAME'];
                                            }

                                            $pCode = '';

                                            $dProp = CIBlockElement::GetProperty(
                                                16,
                                                $product_buy_id,
                                                array("sort" => "asc"),
                                                array("CODE" => "PROVIDERCODE_RTK"));

                                            if ($dProp) {
                                                while ($aProp = $dProp->GetNext()) {
                                                    $pCode = trim($aProp['VALUE']);
                                                }
                                            }

                                            $sProducts .= 'Поставщик: RTK, ZIP код: ' . $pCode . ', Имя товара: ' . $sName . ', Цена поставщика: ' . $pprice . ', Цена на сайте: ' . $minPriceRUR . ', Ссылка для редактирования: https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=16&type=catalog&lang=ru&ID=' . $product_buy_id . '&find_section_section=-1&WF=Y' . "\n";
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


}

if (!empty($sProducts)) {

    CModule::IncludeModule("main");

    $event_name = 'ZIPPRICE';

    $arrSites = array();
    $objSites = CSite::GetList(($by = "sort"), ($order = "asc"));

    while ($arrSite = $objSites->Fetch()) {
        $arrSites[] = $arrSite["ID"];
    };

    $arFields = array('ORDER_LIST' => $sProducts);

    CEvent::SendImmediate($event_name, $arrSites, $arFields);
}