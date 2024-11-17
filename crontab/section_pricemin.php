#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/sites/data/www/dev.youtwig.ru/";
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

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

$cPriceId = 5;

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

$aSelect = Array(
    'ID',
    'IBLOCK_ID'
);

$aFilter = Array(
    'IBLOCK_ID' => 11,
    'GLOBAL_ACTIVE' => 'Y'
);

CModule::IncludeModule("iblock");

$dSect = CIBlockSection::GetList(
            ($aOrder = Array("SORT" => "ASC")),
            $aFilter,
            false,
            $aSelect
        );

if($dSect) {

    $bs = new CIBlockSection;

    while($aSect = $dSect->GetNext()) {

        $minPrices = array();
        $default_currency = getCurrentCurrencyCode();

        foreach(($aCur = array('UER','RUB','USD')) as $sCurr) {

            $dPriceMin = CIBlockElement::GetList(
                array("CATALOG_PRICE_".$cPriceId => "ASC"),
                array(
                    "IBLOCK_ID" => $aSect['IBLOCK_ID'],
                    "SECTION_ID" => $aSect['ID'],
                    "ACTIVE" => "Y",
                    ">CATALOG_PRICE_".$cPriceId => 0,
                    "CATALOG_CURRENCY_".$cPriceId => $sCurr
                ),
                false,
                array(
                    "nPageSize" => 1
                ),
                array()
            );

            if($dPriceMin) {

                while($aPriceMin = $dPriceMin->GetNext())
                {

                    if(isset($aPriceMin["CATALOG_PRICE_".$cPriceId])
                        && !empty($aPriceMin["CATALOG_PRICE_".$cPriceId])) {

                        $minPrice = $aPriceMin["CATALOG_PRICE_".$cPriceId];

                        if($sCurr != $default_currency){

                            $minPrice = CCurrencyRates::ConvertCurrency($minPrice, $sCurr, $default_currency);

                        }

                        $minPrices[] = $minPrice;

                    }

                }

            }

        }

        if(!empty($minPrices)){
            $minPrice = min($minPrices);
            $minPricePrint = CurrencyFormat($minPrice,$default_currency);

            if(!empty($minPricePrint)){

                $minPricePrint = trim($minPricePrint,'.');
                $bs->Update($aSect['ID'], array("UF_PRICE" => $minPricePrint));

            }

        }

    }

}

?>