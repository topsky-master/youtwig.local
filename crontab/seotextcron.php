#!/usr/bin/php -q
<?php
//http://twig.d6r.ru/local/crontab/seotextcron.php?intestwetrust=1
$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";
//$_SERVER["DOCUMENT_ROOT"] = "/var/www/sites/data/www/dev.youtwig.ru/";

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

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$arSelect = array('ID','PROPERTY_DETAIL_SECTIONS','PROPERTY_DETAIL_PRODUCTS','PREVIEW_TEXT');
$arFilter = array('ACTIVE' => 'Y','IBLOCK_ID' => 42);

//DETAIL_SECTIONS
//DETAIL_PRODUCTS
//CONSTRUCTOR_DETAIL

$dbRes = CIBlockElement::GetList(Array('SORT' => 'DESC'), $arFilter, false, false, $arSelect);

$aLinks = array();

if($dbRes){

    while($arFields = $dbRes->GetNext()) {

        $apIds = array();

        if(isset($arFields['PROPERTY_DETAIL_PRODUCTS_VALUE'])
            && !empty($arFields['PROPERTY_DETAIL_PRODUCTS_VALUE'])) {

            foreach($arFields['PROPERTY_DETAIL_PRODUCTS_VALUE'] as $iProdId) {
                $apIds[$iProdId] = $iProdId;
            }

        } else if(isset($arFields['PROPERTY_DETAIL_SECTIONS_VALUE'])
            && !empty($arFields['PROPERTY_DETAIL_SECTIONS_VALUE'])) {

            $rDb = CIBlockElement::GetProperty(
                42,
                $arFields['ID'],
                array('sort' => 'asc'),
                array('CODE' => 'DETAIL_SECTIONS')
            );

            $iSectionId = 0;

            if($rDb && $aDb = $rDb->Fetch()) {
                $iSectionId = $aDb['VALUE_XML_ID'];
            }

            if($iSectionId > 0){
                $rDb = CIBlockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => 11,
                        'IBLOCK_SECTION_ID' => $iSectionId,
                        'ACTIVE' => 'Y'),
                    false,
                    false,
                    array('ID')
                );

                if($rDb) {

                    while ($aDb = $rDb->GetNext()) {
                        $apIds[$aDb['ID']] = $aDb['ID'];
                    }

                }

            }
        }

        if(!empty($apIds)) {

            foreach($apIds as $iEltId){

                preg_match_all('~\[([^:]+?):([^\]]+?)\]~isu',$arFields['PREVIEW_TEXT'],$aMatches);

                foreach($aMatches[1] as $sKey => $sCode) {

                    $sCode = trim($sCode);
                    $rDb = CIBlockElement::GetProperty(
                        11,
                        $iEltId,
                        array('sort' =>'asc'),
                        array('CODE' =>$sCode)
                    );

                    $sValue = '';

                    if($rDb) {
                        while($aDb = $rDb->GetNext()) {
                            $stValue = isset($aDb['VALUE_ENUM']) && !empty($aDb['VALUE_ENUM']) ? $aDb['VALUE_ENUM'] : $aDb['VALUE'];
                            $stValue = trim($stValue);

                            if(!empty($stValue)) {
                                $sValue .= ($sValue != '' ? ', ' : '').$stValue;
                            }
                        }
                    }

                    if(isset($aMatches[2][$sKey]) && !empty($aMatches[2][$sKey])) {

                        $sReplace = trim($aMatches[2][$sKey]);
                        $sReplace = str_ireplace('{value}',$sValue,$sReplace);
                        $arFields['PREVIEW_TEXT'] = str_ireplace($aMatches[0][$sKey],$sReplace,$arFields['PREVIEW_TEXT']);

                    }

                }

                $arFields['PREVIEW_TEXT'] = preg_replace('~\[([^:]+?):([^\]]+?)\]~isu','',$arFields['PREVIEW_TEXT']);

                $toSeoProperty['SEO_TEXT1'] = array('VALUE' => $arFields['PREVIEW_TEXT']);

                \impelCIBlockElement::SetPropertyValuesEx($iEltId, 11, $toSeoProperty);
                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $iEltId);

            }

        }

    }
}
