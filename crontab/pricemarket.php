#!/usr/bin/php -q
<?

//https://youtwig.ru/local/crontab/pricemarket.php?intestwetrust=1

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
$PRICE_TYPE_ID = 9;

if(CModule::IncludeModule("iblock")
    && CModule::IncludeModule("catalog")){

    global $USER;

    $chained_products = array();

    $arSelect = Array("ID", "NAME", "PROPERTY_MAIN_PRODUCTS","PROPERTY_ARTNUMBER");
    $arFilter = Array("IBLOCK_ID" => 11, "!PROPERTY_MAIN_PRODUCTS" => false);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $product = array();


    $quantity = 0;

    while($res
        && ($product = $res->Fetch())){

        if(isset($product['PROPERTY_MAIN_PRODUCTS_VALUE'])
            && !empty($product['PROPERTY_MAIN_PRODUCTS_VALUE'])){

            $rsPrices = CPrice::GetListEx(array(),array(
                    'PRODUCT_ID' => $product['PROPERTY_MAIN_PRODUCTS_VALUE'],
                    'CATALOG_GROUP_ID' => $PRICE_TYPE_ID,
                    'GROUP_GROUP_ID' => array()
                )
            );

            if ($arPrice = $rsPrices->Fetch())
            {

                $arPrice['PRODUCT_ID'] = $product['ID'];
                $arPrice['CATALOG_GROUP_ID'] = $PRICE_TYPE_ID;

                unset($arPrice['ID'],$arPrice['TIMESTAMP_X']);



                $sRes = CPrice::GetListEx(
                    array(),
                    array(
                        "PRODUCT_ID" => $product['ID'],
                        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                    )
                );

                if ($sRes
                    && $sParr = $sRes->Fetch())
                {

                    CPrice::Update($sParr["ID"], $arPrice);

                } else {

                    CPrice::Add($arPrice);
                }


            }

        }

    }

}



