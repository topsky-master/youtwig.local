<?
if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/without_models.csv','');

$arMSelect = Array(
    "ID","PROPERTY_products"
);

$arMFilter = Array(
    "IBLOCK_ID" => 37,
    "ACTIVE_DATE" => "Y",
    "ACTIVE" => "Y",
    "DETAIL_TEXT" => false,

);

$modelEl = new CIBlockElement;

$dbMres = CIBlockElement::GetList(Array(), $arMFilter, false, false, $arMSelect);



if($dbMres){

    while($arMres = $dbMres->GetNext()){

        $link = 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$arMres['PROPERTY_PRODUCTS_VALUE']."\n";
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/without_models.csv',$link,FILE_APPEND);

    }

}