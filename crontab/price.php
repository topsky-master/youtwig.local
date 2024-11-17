#!/usr/bin/php -q
<?

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
$PRICE_TYPE_ID = 5;

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
                    'CAN_BUY' => 'Y',
                    'GROUP_GROUP_ID' => array(2)
                )
            );

            if ($arPrice = $rsPrices->Fetch())
            {
                if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                    $product['PROPERTY_MAIN_PRODUCTS_VALUE'],
                    1,
                    array(2),
                    'N',
                    array($arPrice),
                    SITE_ID
                ))
                {

                    $sRes = CPrice::GetListEx(
                        array(),
                        array(
                            "PRODUCT_ID" => $product['ID'],
                            "CATALOG_GROUP_ID" => 5
                        )
                    );

                    $arOptimalPrice['PRICE']['PRODUCT_ID'] = $product['ID'];

                    if(
                        isset($arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'])
                        &&  isset($arOptimalPrice['RESULT_PRICE']['CURRENCY'])
                        &&  isset($arOptimalPrice['PRICE'])
                        &&  !empty($arOptimalPrice['PRICE'])
                    ){

                        $arOptimalPrice['PRICE']['DISCOUNT_PRICE'] = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                        $arOptimalPrice['PRICE']['CURRENCY'] = $arOptimalPrice['RESULT_PRICE']['CURRENCY'];

						unset($arOptimalPrice['PRICE']['TIMESTAMP_X'],$arOptimalPrice['PRICE']['ID']);

                        $oldPrice = array();

                        if ($sRes && $sParr = $sRes->Fetch())
                        {
                            CPrice::Update($sParr["ID"], $arOptimalPrice['PRICE']);
                            $oldPrice = $sParr;
                        }
                        else
                        {
                            CPrice::Add($arOptimalPrice['PRICE']);
                        }


                        if(!empty($oldPrice)){

                            $sRes = CPrice::GetListEx(
                                array(),
                                array(
                                    "PRODUCT_ID" => $product['ID'],
                                    "CATALOG_GROUP_ID" => 7
                                )
                            );

                            unset($oldPrice['ID']);
                            $oldPrice["CATALOG_GROUP_ID"] = 7;

                            if ($sRes&& $sParr = $sRes->Fetch())
                            {

                                //CPrice::Update($sParr["ID"], $oldPrice);

                            } else {

                                CPrice::Add($oldPrice);

                            }

                        }

                        $sRes = CPrice::GetListEx(
                            array(),
                            array(
                                "PRODUCT_ID" => $product['ID'],
                                "CATALOG_GROUP_ID" => 1
                            )
                        );

                        $arOptimalPrice['PRICE']['PRODUCT_ID'] = $product['ID'];
                        $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'] = 1;

                        unset($arOptimalPrice['PRICE']['ID']);

                        if ($sRes&& $sParr = $sRes->Fetch())
                        {
                            CPrice::Update($sParr["ID"], $arOptimalPrice['PRICE']);

                        } else {

                            CPrice::Add($arOptimalPrice['PRICE']);
                        }
						
						
						//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $product['ID']);

                    }

                }

            }
			
			

        }

    }

}