#!/usr/bin/php -q
<?php
//https://youtwig.ru/local/crontab/filterscreate_new.php?intestwetrust=1
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

$arSelect = array('ID','PROPERTY_SEO_CONSTRUCTOR');
$arFilter = array('ACTIVE' => 'Y','IBLOCK_ID' => 45, '!PROPERTY_SEO_CONSTRUCTOR' => false);

$dbRes = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);

$aLinks = array();

if($dbRes){

    while($arFields = $dbRes->GetNext()) {

        if(isset($arFields['PROPERTY_SEO_CONSTRUCTOR_VALUE']) && !empty($arFields['PROPERTY_SEO_CONSTRUCTOR_VALUE'])){

            $aMatches = array();

            {

                $aValue = $arFields['PROPERTY_SEO_CONSTRUCTOR_VALUE'];

                if(isset($aValue['s']) && isset($aValue['s'][0]) && !empty($aValue['s'][0])){
                    if(is_string($aValue['s'][0])) {
                        $aMatches[0][-1][] = '/'.trim($aValue['s'][0],'/').'/';
                    } else if(is_array($aValue['s'][0])) {
                        $aMatches[0][-1][] = '/'.trim($aValue['s'][0][0],'/').'/';
                    }
                }

                $sMathFilter = array();

                foreach($aValue['p'] as $iNum => $sValue) {

                    if(isset($aValue['p']) && isset($aValue['p'][$iNum]) && !empty($aValue['p'][$iNum])) {

                        $sMathFilter = '/'.trim($aValue['p'][$iNum].'-is-','/');

                        if(isset($aValue['v'][$iNum]) && !empty($aValue['v'][$iNum])) {

                            foreach($aValue['v'][$iNum] as $snValue){

                                if(isset($aValue['o']) && isset($aValue['o'][$iNum]) && !empty($aValue['o'][$iNum])) {
                                    if(!isset($aMatches[1][$iNum+1])){
                                        $aMatches[1][$iNum+1] = array();
                                    }
                                    $aMatches[1][$iNum+1][] = $sMathFilter.$snValue.'/';
                                } else {
                                    if(!isset($aMatches[0][$iNum+1])){
                                        $aMatches[0][$iNum+1] = array();
                                    }
                                    $aMatches[0][$iNum+1][] = $sMathFilter.$snValue.'/';
                                }
                            }

                        } else {

                            if(isset($aValue['o']) && isset($aValue['o'][$iNum]) && !empty($aValue['o'][$iNum])) {
                                if(!isset($aMatches[1][$iNum])){
                                    $aMatches[1][$iNum] = array();
                                }
                                $aMatches[1][$iNum][] = $sMathFilter.'[^/]+?/';
                            } else {
                                if(!isset($aMatches[0][$iNum])){
                                    $aMatches[0][$iNum] = array();
                                }
                                $aMatches[0][$iNum][] = $sMathFilter.'[^/]+?/';
                            }

                        }

                    }


                }



            }

            $aLinks[$arFields['ID']] = $aMatches;
        }
    }
}
// var_dump("aLinks =>", $aLinks);
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/sfilterlinks_new.php','<?php $atNewLinks = '.var_export($aLinks,true).'; ?>');
