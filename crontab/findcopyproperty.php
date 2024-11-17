<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if(CModule::IncludeModule("iblock")){

    $dbRes = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => 17,
            "PROPERTY_type_of_product" => 54459
        ),
        false,
        false,
        array(
            'ID',
            'NAME'
        )
    );

    $tryFindCopies = array();

    if($dbRes){
        while($arRes = $dbRes->GetNext()){

            $tryFindCopies[] = $arRes['ID'];
			echo $arRes['ID'].'-';


        }

    }

    echo sizeof($tryFindCopies);


}

