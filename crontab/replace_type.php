<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$toFind = 56774;
$toReplace = 2579;

if(CModule::IncludeModule("iblock")) {

    $dbRes = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => 17,
            "PROPERTY_type_of_product" => $toFind
        ),
        false,
        false,
        array(
            'ID',
        )
    );

    $tryFindCopies = array();

    if($dbRes){

        while($arRes = $dbRes->GetNext()){

            CIBlockElement::SetPropertyValuesEx(
                $arRes['ID'],
                false,
                array("type_of_product" => $toReplace)
            );
			
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arRes['ID']);
            

        }

    }

}
