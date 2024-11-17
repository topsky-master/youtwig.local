#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/newsort.php?intestwetrust=1

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

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$arPSelect = Array(
    "ID",
);

$arPFilter = Array(
    "IBLOCK_ID" => 11,
    "ACTIVE" => "Y"
);

$iCount = CIBlockElement::GetList(Array(), $arPFilter, Array(), false, $arPSelect);

if ($iCount) {

    $arPSelect = Array(
        "ID","NAME","SHOW_COUNTER"
    );

    $aProds = array();

    $arPFilter = Array(
        "IBLOCK_ID" => 11,
        "ACTIVE" => "Y"
    );

    $dRes = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

    $aProdsToBuy = array();
    $aBuy = array();

    $aBuyCart = array();
    $connection = Bitrix\Main\Application::getConnection();

    if ($dRes) {

        while ($aRes = $dRes->GetNext()) {

            $can_buy = canYouBuy($aRes['ID']);
            $quantity = get_quantity_product($aRes['ID']);
            $can_buy = $quantity > 0 && $can_buy ? $quantity : 0;
            $aProds[$aRes['ID']] = $can_buy;

            if ($can_buy > 0) {

                $sql = "SELECT SUM(`QUANTITY`) as `count` FROM `b_sale_basket` WHERE `NAME` = '".$DB->forSql($aRes['NAME'])."'";
                $dbCount = $connection->query($sql);

                $ipCount = 0;

                if($dbCount) {
                    $ipcCount = $dbCount->fetch();
                    if(isset($ipcCount['count'])) {
                        $ipcCount = $ipcCount['count'];
                        $ipCount += (int)$ipcCount;
                    }
                }

                $sql = "SELECT SUM(`QUANTITY`) as `count` FROM `b_sale_basket_archive` WHERE `NAME` = '".$DB->forSql($aRes['NAME'])."'";

                $dbCount = $connection->query($sql);

                if($dbCount) {
                    $ipcCount = $dbCount->fetch();
                    if(isset($ipcCount['count'])) {
                        $ipcCount = $ipcCount['count'];
                        $ipCount += (int)$ipcCount;
                    }
                }

                if($ipCount > 0) {
                    $aBuyCart[$aRes['ID']] = $ipCount;
                }

                $buy_id = getBondsProduct($aRes['ID']);

                {

                    if(!isset($aProdsToBuy[$buy_id])) {
                        $aProdsToBuy[$buy_id] = array();
                    }

                    $aProdsToBuy[$buy_id][$aRes['ID']] = [$can_buy,$aRes["SHOW_COUNTER"]];
                    $aBuy[$buy_id] = $buy_id;

                }

            }
        }

        $iMax = max($aProds);

        $arPFilter = Array(
            "IBLOCK_ID" => 16,
            "ACTIVE" => "Y",
            //"!PROPERTY_STATISTIC24" => false,
            "@ID" => $aBuy
        );

        $arPSelect = Array(
            "ID",
            "PROPERTY_STATISTIC24"
        );

        $dRes = CIBlockElement::GetList(Array("PROPERTY_STATISTIC24" => "DESC"), $arPFilter, false, false, $arPSelect);

        if ($dRes) {

            while ($aRes = $dRes->GetNext()) {

                //if (isset($aRes['PROPERTY_STATISTIC24_VALUE']) && $aRes['PROPERTY_STATISTIC24_VALUE'] > 0) {
                foreach($aProdsToBuy[$aRes['ID']] as $iProdId => $aNext) {
					$quantity = $aNext[0];
                    $quantity = (int)$iMax + (int)trim($aRes['PROPERTY_STATISTIC24_VALUE']) + (int)(isset($aBuyCart[$iProdId]) ? $aBuyCart[$iProdId] : 0);
                    $quantity = (int)$iMax + (int)(isset($aBuyCart[$iProdId]) ? $aBuyCart[$iProdId] : 0);
                    if ($aNext[1] != $quantity) {
						$aProds[$iProdId] = $quantity;
					} else { 
						unset($aProds[$iProdId]);
					}
                }
                //}

            }

        }

        uasort($aProds,function ($iFirst,$iSecond) {
            if ($iFirst == $iSecond) {
                return 0;
            }

            return ($iFirst < $iSecond) ? 1 : -1;
        });

        $productEl = new CIBlockElement;

        foreach ($aProds as $product_id => $iCOunt) {

            $productEl->Update(
                $product_id,
                array(
                    'SHOW_COUNTER' => $iCOunt,
                    'TIMESTAMP_X' => true
                )
            );

            \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $product_id);

        }

    }

}