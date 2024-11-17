#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/newproducts.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

$iNum = 10;

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(CModule::IncludeModule("iblock")){

    $rDb = \CIBlockElement::GetList(
        array('show_counter' => 'desc'),
        array(
            'IBLOCK_ID' => 11,
            'ACTIVE' => 'Y',
            '!PROPERTY_VIEW_NEW_1' => false),
        false,
        false,
        array('ID')
    );

    $oEl = new CIBlockElement;

    if($rDb){

        while($aDb = $rDb->GetNext()){

            CIBlockElement::SetPropertyValuesEx($aDb['ID'], 11, $aNewProducts = array('VIEW_NEW_1' => false));
            \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aDb['ID']);

            if ($oEl->Update($aDb['ID'], ($aUpdEl = Array('TIMESTAMP_X' => true)))) {

            }

        }
    }

    $iskip = 0;
    $iSubCount = 0;
	$aProducts = [];

    $hasProducts = true;

    while($hasProducts){

        $aPNavParams = array(
            'nTopCount' => false,
            'nPageSize' => $iNum,
            'iNumPage' => $iskip,
            'checkOutOfRange' => true
        );

        $rDb = \CIBlockElement::GetList(
            array('show_counter' => 'desc'),
            array(
                'IBLOCK_ID' => 11,
                '>=PRICE' => 0,
                '@PRICE_TYPE' => 1,
                '!PREVIEW_PICTURE' => false),
            array('ID'),
            $aPNavParams,
            array('ID')
        );

        $foundId = 0;

        if($rDb){

            while($aDb = $rDb->GetNext()){

                $foundId = $aDb['ID'];
				
                if(canYouBuy($aDb['ID'],true)
                    && get_quantity_product($aDb['ID'])){
                    ++$iSubCount;
					
					$product_buy_id = getBondsProduct($aDb['ID']);
		
					if (!isset($aProducts[$product_buy_id])) {
						
						$aProducts[$product_buy_id] = $aDb['ID'];
						
						echo $aDb['ID'].'<br />';

						CIBlockElement::SetPropertyValuesEx($aDb['ID'], 11, $aNewProducts = array('VIEW_NEW_1' => array('VALUE' => 96, 'DESCRIPTION' => '')));
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aDb['ID']);

						if ($oEl->Update($aDb['ID'], ($aUpdEl = Array('TIMESTAMP_X' => true)))) {

						}
						
					}

                };

            }

        }

        $hasProducts = $foundId  && $iNum  > count($aProducts) ? true : false;

        ++$iskip;

    }


    $rDb = \CIBlockElement::GetList(
        array('ID' => 'DESC'),
        array(
            'IBLOCK_ID' => 11,
            'ACTIVE' => 'Y',
            '!PROPERTY_NEWPRODUCT' => false),
        false,
        false,
        array('ID')
    );

    $oEl = new CIBlockElement;

    if($rDb){

        while($aDb = $rDb->GetNext()){

            CIBlockElement::SetPropertyValuesEx($aDb['ID'], 11, $aNewProducts = array('NEWPRODUCT' => false));
            \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aDb['ID']);

            if ($oEl->Update($aDb['ID'], ($aUpdEl = Array('TIMESTAMP_X' => true)))) {

            }

        }
    }

    $iskip = 0;
    $iSubCount = 0;

    $hasProducts = true;

    while($hasProducts){

        $aPNavParams = array(
            'nTopCount' => false,
            'nPageSize' => $iNum,
            'iNumPage' => $iskip,
            'checkOutOfRange' => true
        );

        $rDb = \CIBlockElement::GetList(
            array('ID' => 'DESC'),
            array(
                'IBLOCK_ID' => 11,
                '>=PRICE' => 0,
                '@PRICE_TYPE' => 1,
                '!PREVIEW_PICTURE' => false),
            array('ID'),
            $aPNavParams,
            array('ID')
        );

        $foundId = 0;

        if($rDb){

            while($aDb = $rDb->GetNext()){

                $foundId = $aDb['ID'];

                if(canYouBuy($aDb['ID'],true)
                    && get_quantity_product($aDb['ID'])){
                    ++$iSubCount;

					$product_buy_id = getBondsProduct($aDb['ID']);
		
					if (!isset($aProducts[$product_buy_id])) {
	
						$aProducts[$product_buy_id] = $aDb['ID'];
						
						echo $aDb['ID'].'<br />';

						CIBlockElement::SetPropertyValuesEx($aDb['ID'], 11, $aNewProducts = array('NEWPRODUCT' => array('VALUE' => 32, 'DESCRIPTION' => '')));
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aDb['ID']);

						if ($oEl->Update($aDb['ID'], ($aUpdEl = Array('TIMESTAMP_X' => true)))) {

						}
						
					}

                };

            }

        }

        $hasProducts = $foundId && ($iNum * 2)  > count($aProducts) ? true : false;

        ++$iskip;

    }

}