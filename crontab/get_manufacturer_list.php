<?
//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

//usleep(2500);

global $USER;

$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturer_list.csv','w+');

$property_enums = CIBlockPropertyEnum::GetList(
    Array(
            "DEF" => "DESC",
            "SORT" => "ASC"),
    Array(
            "IBLOCK_ID" => 17,
            "CODE" => "manufacturer")
);

while($enum_fields = $property_enums->GetNext()) {
    fputcsv($fp,array($enum_fields["VALUE"]),';');
}

fclose($fp);