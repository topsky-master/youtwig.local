<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/bosch_models.csv','w+');

if(CModule::IncludeModule("iblock")) {

    $models_exists = array();

    $mlArFilter = array(
        'IBLOCK_ID' => 17,
        'PROPERTY_manufacturer' => 2537,


    );

    $mlArSelect = array('ID', 'PROPERTY_model_new_link');

    $mlDBRes = CIBlockElement::GetList(Array(), $mlArFilter, false, false, $mlArSelect);

    if ($mlDBRes) {

        while ($mlArr = $mlDBRes->getNext()) {

            $mRes = CIBlockElement::GetByID($mlArr['PROPERTY_MODEL_NEW_LINK_VALUE']);
            if($arRes = $mRes->GetNext()){

                if(isset($arRes['NAME'])
                    && !isset($models_exists[trim($arRes['NAME'])])){

                    fwrite($fp,trim($arRes['NAME'])."\n");
                    $models_exists[trim($arRes['NAME'])] = trim($arRes['NAME']);

                }

            }



        }


	}



}

fclose($fp);

?>