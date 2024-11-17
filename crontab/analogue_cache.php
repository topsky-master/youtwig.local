#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/analogue_cache.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

function getAnalogue() {

    $aFilters = ['ACTIVE' => 'Y', '!PROPERTY_ANALOGUE' => false];

    $aSelect = ['ID','IBLOCK_ID'];

    $aSkip = [];

    $aSort = ['ID' => 'DESC'];
    $dRes = CIBlockElement::GetList($aSort, $aFilters, false, false, $aSelect);

    $aProds = [];

    $count = 0;
    if($dRes) {

        while ($aRes = $dRes->GetNext()) {
            $dProps = CIBlockElement::GetProperty($aRes['IBLOCK_ID'], $aRes['ID'], [], ["CODE"=>"ANALOGUE"]);

            if ($dProps) {
                while ($aProps = $dProps->GetNext()) {
                    if (is_numeric($aProps['VALUE'])) {
                        $aProds[$aRes['ID']][$aProps['VALUE']] = $aProps['VALUE'];
                    }
                }
            }
        }

    }

    return $aProds;
}

function getAnalogueTypes(&$skip) {

    $aProds = getAnalogue();
    $aFound = [];

    foreach ($aProds as $iProd => $aList) {

        $aType = [];

        $dProps = CIBlockElement::GetProperty(11, $iProd, [], ["CODE"=>"ANALOGUE_TYPE"]);

        if ($dProps) {
            while ($aProps = $dProps->GetNext()) {
                if (isset($aProps['VALUE_XML_ID'])
                    && !empty($aProps['VALUE_XML_ID'])
                    && isset($aProps['VALUE_ENUM'])
                    && !empty($aProps['VALUE_ENUM'])) {

                    $aType[$aProps['VALUE_XML_ID']] = $aProps['VALUE_ENUM'];

                }
            }
        }

        if (!empty($aType)) {
            $aList[$iProd] = $iProd;
            $aFound[$iProd] = getFiltersMinPrice(['catalog_PRICE_1' => 'ASC'],['ID' => $aList],$aType,$skip);
        }

    }

    return $aFound;
}

function getFiltersMinPrice($aSort, $aFilters, $aType, &$skip){

    $aProds = [];
    $aFilters['!CATALOG_PRICE_1'] = false;
    $aFilters['ACTIVE'] = 'Y';

    $aSelect = ["ID","IBLOCK_ID","CATALOG_GROUP_1", "DETAIL_PAGE_URL"];
    $dRes = CIBlockElement::GetList($aSort, $aFilters, false, false, $aSelect);

    if($dRes) {
        while ($aRes = $dRes->GetNext()) {


            foreach ($aType as $typeCode => $typeName) {

                $dProps = CIBlockElement::GetProperty($aRes['IBLOCK_ID'], $aRes['ID'], [], ["CODE" => $typeCode]);

                if ($dProps) {
                    while ($aProps = $dProps->GetNext()) {

                        if ($aRes['CATALOG_CURRENCY_1'] != 'RUB') {
                            $aRes['CATALOG_PRICE_1'] = CCurrencyRates::ConvertCurrency($aRes['CATALOG_PRICE_1'], $aRes['CATALOG_CURRENCY_1'], 'RUB');
                        }

                        $aProds[$aProps['CODE']][] = [
                            'id' => $aRes['ID'],
                            'price' => $aRes['CATALOG_PRICE_1'],
                            'price_format' => CCurrencyLang::CurrencyFormat($aRes['CATALOG_PRICE_1'],'RUB'),
                            'can_buy' => price_correct($aRes['ID']),
                            'prop' => $typeName,
                            'value' => $aProps['VALUE_ENUM'] ?? $aProps['VALUE'],
                            'url' => $aRes['DETAIL_PAGE_URL']];
                    }
                }
            }
        }
    }

    if (!empty($aProds)) {

        foreach ($aProds as $code => $values) {

            usort($aProds[$code],function ($a, $b)
            {
                if (!$a['can_buy']) {
                    return 1;
                }

                if (!$b['can_buy']) {
                    return -1;
                }
                
                if ($a['price'] == $b['price']) {
                    return 0;
                }

                return ($a['price'] < $b['price']) ? -1 : 1;
            });

            $bFirst = false;

            foreach ($aProds[$code] as $product) {

                if (!$bFirst) {
                    $bFirst = $product['id'];
                    continue;
                }

                if ($bFirst != $product['id']) {
                    $skip[$product['id']] = $product['id'];
                }

            }

        }

    }

    return $aProds;
}

function price_correct($product_id) {
    $can_buy = canYouBuy($product_id);
    $quantity = get_quantity_product($product_id);
    $can_buy = $quantity > 0 ? $can_buy : false;
    return $can_buy ? 1 : 0;
}

function processProductsWithSnyatSProdajiFlag(&$skip) { // Передаем $skip по ссылке
  $arSelect = array("ID", "PROPERTY_SNYAT_S_PRODAJI");
  $arFilter = array("IBLOCK_ID" => 11, "ACTIVE" => "Y");
  $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

  while ($ob = $res->GetNextElement()) {
      $arFields = $ob->GetFields();
      
        // // Отладочный вывод
        // echo "<pre>"; 
        // echo "ID товара: " . $arFields["ID"] . "\n";
        // echo "XML_ID свойства: " . $arFields["PROPERTY_SNYAT_S_PRODAJI_ENUM_ID"]["VALUE_XML_ID"] . "\n";
        // echo "</pre>";

      if (is_array($arFields["PROPERTY_SNYAT_S_PRODAJI_ENUM_ID"]) && $arFields["PROPERTY_SNYAT_S_PRODAJI_ENUM_ID"]["VALUE_XML_ID"] == "1") {
          $skip[$arFields["ID"]] = $arFields["ID"]; // Добавляем ID в массив $skip
        //   echo "Товар с ID ".$arFields["ID"]." снят с продажи (добавлен в skip).<br>"; 
      }
  }
}


$skip = [];
$aProds = getAnalogueTypes($skip);

// echo "<pre>"; var_dump($skip); echo "</pre>"; // Вывод $skip до функции

// Вызываем функцию для обработки флага
processProductsWithSnyatSProdajiFlag($skip); 

// echo "<pre>"; var_dump($skip); echo "</pre>"; // Вывод $skip после функции

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/analogue.txt',serialize($aProds));
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/skip_analogue_list.txt',serialize($skip));
