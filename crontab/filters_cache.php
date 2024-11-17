<?php

//https://youtwig.ru/local/crontab/filters_cache.php?intestwetrust=1&PageSpeed=off

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

class impelFCache{


    public static function getList($FiltersId = array()){
var_dump("impelFCache");
        global $USER;

        static::setUrls();
        static::setEnums();
        static::setOptions();
        static::setProductType();

        static::getRedirect();


    }

    private static function setUrls(){

        $aFilterSelect = Array(
            "ID",
            'NAME',
            'PREVIEW_TEXT',
            'PROPERTY_FILTER_URL',
            "PROPERTY_FOR_UNION_FILTERS",
            "PROPERTY_FOR_UNION_FILTERS_NC",
            'PROPERTY_SEO_TITLE',
            'PROPERTY_SEO_DECRIPTION',
            'PROPERTY_SEO_KEYWORDS',
            'PROPERTY_FOR_UNION_SECTIONS',
            'PROPERTY_FOR_UNION_SECTIONS_NC',
            'PROPERTY_DOMAIN',
            'PROPERTY_IS_REGEXP',
            'PROPERTY_H1_BOTTOM',
            'PROPERTY_SEO_DECRIPTION_PAGEN',
            'PROPERTY_SEO_TITLE_PAGEN',
        );

        $aFilter = Array(
            "IBLOCK_ID" => 45,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
        );

        $afLines = array();
        $afLinesToId = array();

        $rFilter = CIBlockElement::GetList(
            ($aFilterOrder = ['SORT' => 'DESC']),
            $aFilter,
            false,
            false,
            $aFilterSelect
        );

        if($rFilter){

            while($aFilter = $rFilter->GetNext()){

                if(isset($aFilter['PROPERTY_FILTER_URL_VALUE'])
                    && !empty($aFilter['PROPERTY_FILTER_URL_VALUE'])){

                    $afLines[$aFilter['PROPERTY_DOMAIN_VALUE']][$aFilter['PROPERTY_FILTER_URL_VALUE']] = array(
                        'ID' => $aFilter['ID'],
                        'NAME' => $aFilter['NAME'],
                        'PREVIEW_TEXT' => ($aFilter['PREVIEW_TEXT'] ? $aFilter['PREVIEW_TEXT'] : ''),
                        "PROPERTY_FOR_UNION_FILTERS_VALUE" => ($aFilter['PROPERTY_FOR_UNION_FILTERS_VALUE'] ? $aFilter['PROPERTY_FOR_UNION_FILTERS_VALUE'] : ''),
                        "PROPERTY_FOR_UNION_FILTERS_NC_VALUE" => ($aFilter['PROPERTY_FOR_UNION_FILTERS_NC_VALUE'] ? $aFilter['PROPERTY_FOR_UNION_FILTERS_NC_VALUE'] : ''),
                        'PROPERTY_SEO_TITLE_VALUE' => ($aFilter['PROPERTY_SEO_TITLE_VALUE'] ? $aFilter['PROPERTY_SEO_TITLE_VALUE'] : ''),
                        'PROPERTY_SEO_DECRIPTION_VALUE' => ($aFilter['PROPERTY_SEO_DECRIPTION_VALUE'] ? $aFilter['PROPERTY_SEO_DECRIPTION_VALUE'] : ''),
                        'PROPERTY_SEO_KEYWORDS_VALUE' => ($aFilter['PROPERTY_SEO_KEYWORDS_VALUE'] ? $aFilter['PROPERTY_SEO_KEYWORDS_VALUE'] : ''),
                        'PROPERTY_FOR_UNION_SECTIONS_VALUE' => ($aFilter['PROPERTY_FOR_UNION_SECTIONS_VALUE'] ? $aFilter['PROPERTY_FOR_UNION_SECTIONS_VALUE'] : ''),
                        'PROPERTY_FOR_UNION_SECTIONS_NC_VALUE' => ($aFilter['PROPERTY_FOR_UNION_SECTIONS_NC_VALUE'] ? $aFilter['PROPERTY_FOR_UNION_SECTIONS_NC_VALUE'] : ''),
                        'PROPERTY_DOMAIN_VALUE' => ($aFilter['PROPERTY_DOMAIN_VALUE'] ? $aFilter['PROPERTY_DOMAIN_VALUE'] : ''),
                        'PROPERTY_IS_REGEXP_VALUE' => ((isset($aFilter['PROPERTY_IS_REGEXP_VALUE']) && $aFilter['PROPERTY_IS_REGEXP_VALUE'] == 'Да') ? true : false),
                        'PROPERTY_H1_BOTTOM_VALUE' => ($aFilter['PROPERTY_H1_BOTTOM_VALUE'] ? $aFilter['PROPERTY_H1_BOTTOM_VALUE'] : ''),
                        'PROPERTY_SEO_DECRIPTION_PAGEN_VALUE' => ($aFilter['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'] ? $aFilter['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'] : ''),
                        'PROPERTY_SEO_TITLE_PAGEN_VALUE' => ($aFilter['PROPERTY_SEO_TITLE_PAGEN_VALUE'] ? $aFilter['PROPERTY_SEO_TITLE_PAGEN_VALUE'] : ''),
                    );

                    $afLinesToId[$aFilter['ID']] = $aFilter['PROPERTY_FILTER_URL_VALUE'];

                }

            }

        }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_titles_cache.php','<?php $aFilterUrlsCache = '.var_export($afLines,true).'; $afLinesToId = '.var_export($afLinesToId,true).'; ?>');

        return 0;

    }

    public static function setEnums(){

        $peDB = CIBlockPropertyEnum::GetList(
            Array(
                "DEF" => "DESC",
                "SORT" => "ASC"),
            Array(
                "IBLOCK_ID" => 11
            )
        );

        $aEnums = array();
        $aKeys = array();
        $aEnumsIdToCode = array();

        if($peDB){

            while($peArr = $peDB->GetNext()){

                $aEnums[$peArr['PROPERTY_ID']][$peArr['XML_ID']] = $peArr;

                if(static::filterHasProducts($peArr['PROPERTY_CODE'],$peArr["VALUE"])){

                    if(!isset($aKeys[$peArr['PROPERTY_CODE']]))
                        $aKeys[$peArr['PROPERTY_CODE']] = array();

                    $peArr['XML_ID'] = mb_strtolower($peArr['XML_ID']);
                    $peArr['PROPERTY_CODE'] = mb_strtolower($peArr['PROPERTY_CODE']);

                    if(!isset($aKeys[$peArr['PROPERTY_CODE']][$peArr['XML_ID']]))
                        $aKeys[$peArr['PROPERTY_CODE']][$peArr['XML_ID']] = array();

                    if(!isset($aKeys[$peArr['PROPERTY_CODE']][$peArr['XML_ID']][$peArr['PROPERTY_ID']]))
                        $aKeys[$peArr['PROPERTY_CODE']][$peArr['XML_ID']][$peArr['PROPERTY_ID']] = array();


                    $aKeys[$peArr['PROPERTY_CODE']][$peArr['XML_ID']][$peArr['PROPERTY_ID']] = $peArr["VALUE"];



                    foreach($peArr as $sKey => $sValue){

                        if(mb_stripos($sKey,'~') === 0
                            || in_array($sKey,array('TMP_ID','PROPERTY_SORT','SORT','DEF'))
                        ){
                            unset($peArr[$sKey]);
                        }

                    }

                    $aEnumsIdToCode[mb_strtoupper($peArr['PROPERTY_CODE'])] = $peArr['PROPERTY_ID'];

                }


            }


        }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_enum_cache.php','<?php $aFilterEnumCache = '.var_export($aKeys,true).'; ?>');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/enum_cache.php','<?php $aEnums = '.var_export($aEnums,true).'; $aEnumsIdToCode = '.var_export($aEnumsIdToCode,true).'; ?>');

        return 0;

    }

    public static function filterHasProducts($sPropCode = '',$sPropVal = ''){

        $aSelect = Array("ID");
        $aFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_".$sPropCode."_VALUE" => $sPropVal
        );

        $rEres = CIBlockElement::GetList(['SORT' => 'DESC'], $aFilter, false, ($aNavParams = array('nTopCount' => 1)), $aSelect);

        $aFields = array();

        if($rEres)
            $aFields = $rEres->GetNext();


        return (!empty($aFields) && isset($aFields['ID']) && !empty($aFields['ID'])) ? true : false;
    }

    public static function setProductType(){

        $aFilterSelect = Array(
            "ID",
            'PROPERTY_TYPEPRODUCT',
            'PROPERTY_MANUFACTURER',
            'PROPERTY_MAIN_PRODUCTS'
        );

        $aFilter = Array(
            "IBLOCK_ID" => 11,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
        );

        $atLines = array();
        $amLines = array();
        $bondProducts = array();

        $rFilter = CIBlockElement::GetList(
            ($aFilterOrder = Array()),
            $aFilter,
            false,
            false,
            $aFilterSelect
        );

        if($rFilter){

            while($aFilter = $rFilter->GetNext()){

                if(isset($aFilter['PROPERTY_TYPEPRODUCT_VALUE'])
                    && !empty($aFilter['PROPERTY_TYPEPRODUCT_VALUE'])){

                    $atLines[$aFilter['ID']] = $aFilter['PROPERTY_TYPEPRODUCT_VALUE'];

                }

                if(isset($aFilter['PROPERTY_MANUFACTURER_VALUE'])
                    && !empty($aFilter['PROPERTY_MANUFACTURER_VALUE'])){

                    $amLines[$aFilter['ID']] = $aFilter['PROPERTY_MANUFACTURER_VALUE'];

                }

                $bondProducts[$aFilter['ID']] = isset($aFilter['PROPERTY_MAIN_PRODUCTS_VALUE']) ? (int)$aFilter['PROPERTY_MAIN_PRODUCTS_VALUE'] : 0;

            }

        }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_mt_cache.php','<?php $atLines = '.var_export($atLines,true).'; $amLines = '.var_export($amLines,true).'; ?>');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/bonds_cache.php','<?php $bondProducts = '.var_export($bondProducts,true).'; ?>');

        return 0;

    }

    public static function setOptions(){

        $main_parameter = array();
        $main_parameter_sizeof = \COption::GetOptionString('my.stat', "main_parameter_sizeof", 0, SITE_ID);
        $valueid = array();

        if($main_parameter_sizeof > 0)
            for($i = 0; $i < $main_parameter_sizeof; $i ++){

                $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id".$i, "", SITE_ID);
                $main_parameter['chain'][$i] = \COption::GetOptionString('my.stat', "main_parameter_chain".$i, "", SITE_ID);
                $main_parameter['value'][$i] = \COption::GetOptionString('my.stat', "main_parameter_value".$i, "", SITE_ID);
                $main_parameter['section'][$i] = \COption::GetOptionString('my.stat', "main_parameter_section".$i, "", SITE_ID);

                $values = $main_parameter['value'][$i];

                $values = mb_stripos($values, ',') !== false ? explode(',', $values) : array($values);
                $values = array_map('trim', $values);
                $values = array_unique($values);
                $values = array_filter($values);

                foreach ($values as $enumValue) {

                    $peDB = CIBlockPropertyEnum::GetList(
                        array(),
                        array(
                            'VALUE' => $enumValue,
                            'PROPERTY_ID' => $main_parameter['id'][$i]
                        )
                    );

                    if ($peDB && ($peArr = $peDB->GetNext())) {

                        if (!is_array($valueid[$main_parameter['id'][$i]])) {
                            $valueid[$main_parameter['id'][$i]] = array();
                        }

                        if (!in_array($peArr['ID'], $valueid[$main_parameter['id'][$i]])) {
                            $valueid[$main_parameter['id'][$i]][] = $peArr['ID'];
                        }

                    }

                }

            }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/filters_options_cache.php','<?php $main_parameter = '.var_export($main_parameter,true).'; $valueid = '.var_export($valueid,true).' ?>');

        return 0;

    }

    private static function getRedirect(){

        echo 'done';
        die();

    }
}

if(CModule::IncludeModule("iblock"))
    impelFCache::getList();