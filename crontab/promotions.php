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

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}	

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);
define("__COUNT_STRINGS",10000);

define("__FILE_PROMOTIONS_NAME",dirname(dirname(__DIR__)).'/bitrix/tmp/promotions_last.txt');
define("__FILE_PROMOTIONS_IDS",dirname(dirname(__DIR__)).'/bitrix/tmp/promotions_ids.php');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$currentCount = 0;

if(CModule::IncludeModule("iblock")) {

    $countStrings = __COUNT_STRINGS;

    if(!file_exists(__FILE_PROMOTIONS_NAME)){
        file_put_contents(__FILE_PROMOTIONS_NAME,'0');
    }

    $skip = file_get_contents(__FILE_PROMOTIONS_NAME);
    $skip = trim($skip);
    $skip = (int)$skip;
    $skip = !is_numeric($skip) ? 0 : $skip;
    $updElement = new CIBlockElement;

    $skip = 0;
    $promotionsIDS = array();

    if(empty($skip)){

        $arPSelect = Array(
            "ID",
            "PROPERTY_OLD_PRICE"
        );

        $arPFilter = Array(
            "IBLOCK_ID" => 11,
            "!PROPERTY_OLD_PRICE" => false,
        );

        $dbPres = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

        if($dbPres){

            while($arPres = $dbPres->GetNext()){

                if($arPres["PROPERTY_OLD_PRICE_VALUE"] > 0) {

                    if ($arPrice = CCatalogProduct::GetOptimalPrice(
                        $arPres['ID'],
                        1,
                        array(2), // anonymous
                        'N',
                        array(),
                        SITE_ID
                    ))
                    {

                        if(isset($arPrice['PRICE']['PRICE'])
                            && isset($arPrice['PRICE']['CURRENCY'])){

                            $minPriceRUR = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']["CURRENCY"], 'RUB');

                            if(!empty($minPriceRUR) && $minPriceRUR < $arPres["PROPERTY_OLD_PRICE_VALUE"]){
                                $promotionsIDS[] = $arPres["ID"];
                            }
                        }
                    }

                }

            }

        }

        file_put_contents(__FILE_PROMOTIONS_IDS, '<?php $promotionsIDS = '.var_export($promotionsIDS,true).';?>');

		{

            $arPSelect = Array(
                "ID",
                "IBLOCK_SECTION",
                "IBLOCK_SECTION_ID"
            );

            $arPFilter = Array(
                "IBLOCK_ID" => 11,
                "SECTION_ID" => 878
            );

            $dbPres = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

            if($dbPres){

                while($arPres = $dbPres->GetNext()){

                    $arSections = array();
                    $dbSections = CIBlockElement::GetElementGroups($arPres['ID']);

                    if($dbSections){

                        while($arSection = $dbSections->GetNext()){

                            if($arSection['ID'] != 878)
                                $arSections[] = $arSection['ID'];

                        }

                        $updElement->Update(
                            $arPres['ID'],
                            array(
                                "IBLOCK_SECTION" => (empty($arSections) ? false : $arSections),
                                "IBLOCK_ID" => 11,
                                'TIMESTAMP_X' => true
                            )
                        );

                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arPres['ID']);

                    }

                }

            }


        }


    }

    require __FILE_PROMOTIONS_IDS;

    //$promotionsIDS = array_slice($promotionsIDS,$skip * $countStrings, $countStrings);

	if(!empty($promotionsIDS)){

		$arPSelect = Array(
            "ID",
            "IBLOCK_ID",
            "PROPERTY_MODEL",
            "PROPERTY_HIDE_MODELS",
            "NAME"
        );

        $arPFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE" => "Y",
            "ID" => $promotionsIDS
        );

        $order = Array(
            "ID" => "ASC"
        );

        $dbPres = CIBlockElement::GetList($order, $arPFilter, false, false, $arPSelect);

        if ($dbPres) {

            while ($arPres = $dbPres->GetNext()) {

                ++$currentCount;

                $arSections = array();
                $dbSections = CIBlockElement::GetElementGroups($arPres['ID']);

                if($dbSections){

                    $iMainId = 0;

                    while($arSection = $dbSections->GetNext()){

                        $arSections[] = $arSection['ID'];

                        if (!$iMainId && $arSection['ID'] != 878) {
                            $iMainId = $arSection['ID'];
                        }

                    }

                    $arSections[] = 878;
                    $arSections = array_unique($arSections);

                    $aProps = array(
                        "IBLOCK_SECTION" => (empty($arSections) ? false : $arSections),
                        "IBLOCK_ID" => 11,
                        'TIMESTAMP_X' => true,
                    );

                    if ($iMainId) {
                        $aProps['IBLOCK_SECTION_ID'] = $iMainId;
                    }

                    $updElement->Update(
                        $arPres['ID'],
                        $aProps
                    );

                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $arPres['ID']);

                }


            }

        }

        if($currentCount > 0){

            ++$skip;
            file_put_contents(__FILE_PROMOTIONS_NAME,$skip);
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/promotions.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');

        }

    }

    file_put_contents(__FILE_PROMOTIONS_NAME,'0');
    echo 'done';

}
