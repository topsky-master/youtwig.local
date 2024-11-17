<?php

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/bosch_mt.csv','w+');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


$mlNDBRes = CIBlockElement::GetList(
    Array(),
    Array(
        'IBLOCK_ID' => 17,
        'PROPERTY_MANUFACTURER' => 173
    ),
    false,
    false,
    array('ID','PROPERTY_TYPE_OF_PRODUCT','PROPERTY_MODEL_NEW_LINK','PROPERTY_MANUFACTURER')
);

if($mlNDBRes) {

    while ($mlNArr = $mlNDBRes->getNext()) {

        $mlDBRes = CIBlockElement::GetList(
            Array(),
            Array(
                'IBLOCK_ID' => 27,
                'ID' => $mlNArr['PROPERTY_MODEL_NEW_LINK_VALUE']
            ),
            false,
            false,
            array('NAME')
        );

        if($mlDBRes
            && $mlArr = $mlDBRes->getNext()){

            fputcsv($fp,array($mlNArr['PROPERTY_TYPE_OF_PRODUCT_VALUE'],$mlArr['NAME']),';');

        }

    }

}

fclose($fp);