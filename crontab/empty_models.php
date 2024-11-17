<?
if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$lArSelect = array('ID','NAME');
$lArFilter = array(
    'IBLOCK_ID' => 17,
    'PROPERTY_SIMPLEREPLACE_PRODUCTS' => false,
	"ACTIVE" => "Y",
    "PROPERTY_VERSION_VALUE" => false
);

$lDBRes = CIBlockElement::GetList(Array(), $lArFilter, false, false, $lArSelect);

if($lDBRes){

    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models.csv','w+');

    while($arFields = $lDBRes->GetNext()){

        fputcsv($fp,array($arFields['ID'],$arFields['NAME'],'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$arFields['ID'].'&lang=ru&find_section_section=0&WF=Y'),';');

    }

    fclose($fp);

}

