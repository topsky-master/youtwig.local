<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelSEOEmptyModels{

    private static $countStrings = 20000;
    private static $sections_list_ids = array();

    public static function checkModels(){

        global $USER;

        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function setSections(){

        $sections_list = array();
        $sections_list_ids = array();

        $sectDb = CIBlockSection::GetList(
            $arOrder = Array("SORT"=>"ASC"),
            $arFilter = Array("IBLOCK_ID" => 11),
            false,
            $arSelect = Array("SECTION_PAGE_URL","NAME","ID")
        );

        if($sectDb)
            while($sectAr = $sectDb->GetNext()) {
                $sections_list[$sectAr['NAME']] = $sectAr['SECTION_PAGE_URL'];
                $sections_list_ids[$sectAr['NAME']] = $sectAr['ID'];
            }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sections_list.php','<?php $sections_list = '.var_export($sections_list,true).'; ?>');
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/sections_list_ids.php','<?php $sections_list_ids = '.var_export($sections_list_ids,true).'; ?>');

    }

    private static function setManufacturers(){

        $manufacutrers_list = array();

        $property_enums = CIBlockPropertyEnum::GetList(
            Array(
                "DEF" => "DESC",
                "SORT" => "ASC"),
            Array(
                "IBLOCK_ID" => 11,
                "CODE" => "MANUFACTURER")
        );

        if($property_enums)
            while($enum_fields = $property_enums->GetNext()) {
                $manufacutrers_list[$enum_fields['VALUE']] = $enum_fields['XML_ID'];
            }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacutrers_list.php','<?php $manufacutrers_list = '.var_export($manufacutrers_list,true).'; ?>');

    }

    private static function setTypeofproducts(){

        $typeproducts_list = array();

        $property_enums = CIBlockPropertyEnum::GetList(
            Array(
                "DEF" => "DESC",
                "SORT" => "ASC"),
            Array(
                "IBLOCK_ID" => 11,
                "CODE" => "TYPEPRODUCT")
        );

        if($property_enums)
            while($enum_fields = $property_enums->GetNext()) {
                $typeproducts_list[$enum_fields['VALUE']] = $enum_fields['XML_ID'];
            }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/typeproducts_list.php','<?php $typeproducts_list = '.var_export($typeproducts_list,true).'; ?>');

    }

    private static function checkFamiliarModels(){

        $modelLastPropId = 0;
        $currentCount = 0;

        $sections_list = array();
        $typeproducts_list = array();
        $manufacutrers_list = array();

        $arNameSelect = Array(
            "ID",
            "NAME"
        );

        $skip = (int)file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_log_last.txt');

        if($skip > 0){

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_seo_filters.csv','a+');

        } else {

            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_seo_filters.csv','w+');

            fputcsv($fp,array('URL','Категория','Тип товара','Совместимость'),';');

            static::setSections();
            static::setManufacturers();
            static::setTypeofproducts();

            /* file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_log_number.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_log_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_get_category.txt','');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_get_manufacturer.txt','');
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_get_typeproduct.txt',''); */

        }

        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/typeproducts_list.php';
        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/manufacutrers_list.php';
        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/sections_list.php';
        require_once dirname(dirname(__DIR__)).'/bitrix/tmp/sections_list_ids.php';

        static::$sections_list_ids = $sections_list_ids;

        $currentCount = 0;
        $anyFound = 0;

        foreach($sections_list as $sectionName => $sectionURI){

            foreach($typeproducts_list as $typeproductsName => $typeproductsURI) {

                foreach($manufacutrers_list as $manufacutrersName => $manufacutrersURI) {

                    if($skip > $currentCount){
                        ++$currentCount;
                        continue;
                    }

                    $currPage = $sectionURI . 'filter/typeproduct-is-'.$typeproductsURI.'/manufacturer-is-' . $manufacutrersURI . '/';
                    $count = static::getEmptyName($currPage,$typeproductsName,$manufacutrersName,$sectionName);

                    if (!$count) {

                        fputcsv($fp, array($currPage, $sectionName, $typeproductsName, $manufacutrersName), ';');
                    }

                    ++$currentCount;
                    ++$anyFound;

                    if(($skip + static::$countStrings) < $currentCount){

                        break;

                    }

                }

                if(($skip + static::$countStrings) < $currentCount){

                    break;

                }

            }

            if(($skip + static::$countStrings) < $currentCount){

                break;

            }

        }


        if($anyFound == 0){

            foreach($sections_list as $sectionName => $sectionURI){

                foreach($typeproducts_list as $typeproductsName => $typeproductsURI) {

                    $currPage = $sectionURI.'filter/typeproduct-is-'.$typeproductsURI.'/';
                    $count = static::getEmptyName($currPage,$typeproductsName,'',$sectionName);

                    if(!$count){
                        fputcsv($fp,array($currPage,$sectionName,$typeproductsName,''),';');
                    }


                }

                foreach($manufacutrers_list as $manufacutrersName => $manufacutrersURI) {

                    $currPage = $sectionURI.'filter/manufacturer-is-'.$manufacutrersURI.'/';
                    $count = static::getEmptyName($currPage,'',$manufacutrersName,$sectionName);

                    if(!$count){
                        fputcsv($fp,array($currPage,$sectionName,'',$manufacutrersName),';');
                    }


                }

                $currPage = $sectionURI;

                $count = static::getEmptyName($currPage,'','',$sectionName);

                if(!$count){

                    fputcsv($fp,array($currPage,$sectionName,'',''),';');
                }

            }

            foreach($typeproducts_list as $typeproductsName => $typeproductsURI) {

                $currPage = '/filter/typeproduct-is-'.$typeproductsURI.'/';
                $count = static::getEmptyName($currPage,$typeproductsName,'','');

                if(!$count){
                    fputcsv($fp,array($currPage,'',$typeproductsName,''),';');
                }

            }

            foreach($manufacutrers_list as $manufacutrersName => $manufacutrersURI) {

                $currPage = '/filter/manufacturer-is-'.$manufacutrersURI.'/';
                $count = static::getEmptyName($currPage,'',$manufacutrersName,'');

                if(!$count){
                    fputcsv($fp,array($currPage,'','',$manufacutrersName),';');
                }


            }

        }

        fclose($fp);

        echo (sizeof($manufacutrers_list) * sizeof($sections_list) * sizeof($typeproducts_list)).'-';
        echo $currentCount + $anyFound;
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_log_last.txt', ($currentCount + $anyFound));

        return $anyFound;

    }

    private static function getEmptyName($currPage,$typeProductXMLValue = '', $manufacturerXMLValue = '', $categoryName = ''){

        $arFilter = array('IBLOCK_ID' => 11);
        //TYPEPRODUCT MANUFACTURER 11 SECTION_ID

        if(!empty($typeProductXMLValue)){
            $arFilter['PROPERTY_TYPEPRODUCT_VALUE'] = $typeProductXMLValue;
        }

        if(!empty($manufacturerXMLValue)){
            $arFilter['PROPERTY_MANUFACTURER_VALUE'] = $manufacturerXMLValue;
        }

        if(!empty($categoryName)
            && isset(static::$sections_list_ids[$categoryName])){
            $arFilter['SECTION_ID'] = static::$sections_list_ids[$categoryName];
        }

        $arOrder = Array(
            "NAME" => "ASC",
            "CREATED" => "ASC"
        );

        $countProducts = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            Array(),
            false
        );

        if(!$countProducts){
            return true;
        }

        $arDescFilter = Array(
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_FILTER_URL" => $currPage,
            "IBLOCK_ID" => 15
        );

        $arDescOrder = Array(
            "NAME" => "ASC",
            "CREATED" => "ASC"
        );

        $countDesc = CIBlockElement::GetList(
            $arDescOrder,
            $arDescFilter,
            Array(),
            false
        );

        return $countDesc ? true : false;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            die ('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/seoemptymodels.php?intestwetrust=1&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/seo_empty_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")){

    impelSEOEmptyModels::checkModels();

}