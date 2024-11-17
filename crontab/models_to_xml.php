#!/usr/bin/php -q
<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

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
define('__SPLIT_SIZE__', 150 * 1024 * 1024);

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

$RUR = 'RUB';

define("NO_KEEP_STATISTIC", true);

ini_set('default_charset','utf-8');

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

define('__FILE_MODELS_NAME',dirname(dirname(__DIR__)).'/bitrix/tmp/xml_last.txt');

$product_image_width = 370;
$product_image_height = '';

global $USER, $APPLICATION, $DB;

$_SERVER['HTTP_HOST'] = 'youtwig.ru';

if(!function_exists('checkIsVaidXML')){
    function checkIsVaidXML($text){

        /* $text = htmlspecialchars($text,ENT_HTML5,LANG_CHARSET);
        libxml_use_internal_errors(true);
        $checkDoc = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><root><document>'.$text.'</document></root>');
        return (bool)$checkDoc === true ? true : false; */
        return true;

    }
}

if(!function_exists('valid_text2xml')){

    function valid_text2xml($text, $bHSC = false, $bDblQuote = false, $denyCovert = false){
        global $APPLICATION;

        $bHSC = (true == $bHSC ? true : false);
        $bDblQuote = (true == $bDblQuote ? true: false);

        if ($bHSC)
        {

            //if(function_exists('tidy_repair_string')){
            //$text = (htmlspecialchars(tidy_repair_string(html_entity_decode($text,ENT_QUOTES,LANG_CHARSET), array('show-body-only' => true), LANG_CHARSET),ENT_QUOTES, LANG_CHARSET));
            //} else {
            $text = (htmlspecialchars(html_entity_decode($text,ENT_QUOTES,LANG_CHARSET),ENT_QUOTES, LANG_CHARSET));
            //}

            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);

        }
        $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
        $text = str_replace("'", "'", $text);

        if(!$denyCovert){
            //$text = $APPLICATION->ConvertCharset($text, LANG_CHARSET, 'windows-1251');
        };

        $text = trim($text);

        return $text;
    }

}

if(!function_exists('get_file_path')){
    function get_file_path($pictNo, $upload_dir = false){

        global $DB;

        $ar_file = false;
        $pictNo = (int)$pictNo;

        if($pictNo > 0){
            $strSql = "SELECT f.*,".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X FROM b_file f WHERE f.ID=".$pictNo;
            $dbFRes = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);

            if ($dbFRes
                && is_object($dbFRes)
                && $ar_file = $dbFRes->Fetch())
            {

                $ar_file['SRC'] = CFile::GetFileSRC($ar_file, $upload_dir);

            }

        }

        return (isset($ar_file['SRC']) && !empty($ar_file['SRC'])) ? ($ar_file['SRC']) : '';
    }

}

if(!function_exists('getLastLine')) {

    function getLastLine($filePath, $lastPos = -1)
    {

        $fp = fopen($filePath, 'r');
        $pos = $lastPos;
        $t = " ";
        while ($t != "\n") {
            fseek($fp, $pos, SEEK_END);
            $t = fgetc($fp);
            $pos = $pos - 1;
        }
        $t = fgets($fp);

        if (trim($t) == "") {
            $t = getLastLine($filePath, $pos);
        }

        fclose($fp);
        return trim($t);

    }

}

if(!function_exists('get_next_part_num')){
    function get_next_part_num($file,$num = 0){

        if(!file_exists($file)){
            $fp = fopen($file,'wb+');
            fwrite($fp,'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL);
            fwrite($fp,'<root>'.PHP_EOL);
            return $file;
        }

        if(file_exists($file)
            && filesize($file) > __SPLIT_SIZE__){

            if(getLastLine($file) != '</root>'){

                $fp = fopen($file,'ab+');
                fwrite($fp,'</root>');
                fclose($fp);

            }

            $num++;

            $fileInfo = pathinfo($file);

            $fileInfo['filename'] = preg_replace('~_[0-9]+?$~is','',$fileInfo['filename']);
            $fileInfo['filename'] .= '_'.$num;
            $file = $fileInfo['dirname'].'/'.$fileInfo['filename'].'.'.$fileInfo['extension'];
            $file = get_next_part_num($file,$num);


        }

        return $file;

    }
}

$countStrings = 1000;
$currentCount = 0;

if(!file_exists(__FILE_MODELS_NAME)){
    file_put_contents(__FILE_MODELS_NAME,'0');
}

$skip = file_get_contents(__FILE_MODELS_NAME);
$skip = trim($skip);
$skip = (int)$skip;
$skip = !is_numeric($skip) ? 0 : $skip;


$file_to_write = dirname(dirname(__DIR__)).'/bitrix/tmp/models_to_xml_test_0.xml';
$file_to_write = get_next_part_num($file_to_write);

$fp = fopen($file_to_write,'ab+');

$strProperty = '';

//CIBlockElement::GetProperty(
//VOPR_BLACK E TYPEPRODUCT E VNUNTRENNIY_DIAMETR E VNESHNIY_DIAMETR E SHIRINA E
//ARTNUMBER S NEWPRODUCT E MANUFACTURER E SALEPRODUCT E MAIN_PRODUCTS ELT
//COUNTRY E QUALITY E COLOR E DIAMETR E VISOTA E DLINNA E KOLICHESTVO_ZUBEV E
//TYPE_OF_PROFILE E POWER E TYPE_OF_MOUNT E PLACE_OF_CONTACTS E TYPE_OF_FABRIC E
//NUMBER_OF_CONTACTS E VOLUME E COVERING E FEATURES E MANUFACTURER_DETAIL E
//RESISTANCE E HOLE E WHEEL_DIAMETR E ANGLE E MORE_PHOTO F

$cpCodes = array(
    'E' => array(
        'TYPEPRODUCT',
        'VNUNTRENNIY_DIAMETR',
        'VNESHNIY_DIAMETR',
        'SHIRINA',
        'MANUFACTURER',
        'COUNTRY',
        'QUALITY',
        'COLOR',
        'DIAMETR',
        'VISOTA',
        'DLINNA',
        'KOLICHESTVO_ZUBEV',
        'NUMBER_OF_CONTACTS',
        'VOLUME',
        'COVERING',
        'FEATURES',
        'MANUFACTURER_DETAIL',
        'RESISTANCE',
        'HOLE',
        'WHEEL_DIAMETR',
        'KOMPLEKT',
        'MATERIAL',
        'TYPE_OF_BELT',
        'TYPE_OF_BORE',
        'ANGLE',
        'TYPE_OF_PROFILE',
        'POWER',
        'TYPE_OF_MOUNT',
        'PLACE_OF_CONTACTS',
        'TYPE_OF_FABRIC',
    ),
    /*'F' => array(
        'MORE_PHOTO',
    ),*/
    'S' => array(
        'ARTNUMBER'
    )
);

//MAIN_PRODUCTS

//ID=11
//NAME DETAIL_TEXT DETAIL_PICTURE LINK

//MAIN_PRODUCTS
//ID=16 CML2_TRAITS
//#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/

define('MAX_TO_EXECUTE',90);

$timer = time();

$arModelFilter = array('IBLOCK_ID' => 17, 'ACTIVE' => 'Y');

if($skip > 0){
    $arModelFilter['>ID'] = $skip;
}

$subRange = false;

$arModelSelect = array(
    'ID',
    'PROPERTY_type_of_product',
    'PROPERTY_manufacturer',
    'PROPERTY_model_new_link',
    'DETAIL_PICTURE',
    'DETAIL_TEXT',
    'DETAIL_PAGE_URL',
    'PROPERTY_instruction',
    'CODE');

$currentIteration = 0;

$resModel = CIBlockElement::GetList(Array('ID' => 'ASC'), $arModelFilter, false, false, $arModelSelect);

if ($resModel) {

    while($arModels = $resModel->Fetch()){

        ++$currentIteration;

        usleep(500);

        $linkedProducsRes = impelCIBlockElement::GetProperty(
            17,
            $arModels['ID'],
            array(),
            Array("CODE" => "SIMPLEREPLACE_PRODUCTS")
        );

        $linkedProducsIds = array();

        if($linkedProducsRes){

            while($linkedProducsArr = $linkedProducsRes->GetNext()){

                $linkedProducsIds[$linkedProducsArr['VALUE']] = $linkedProducsArr['VALUE'];

            }

        }

        $linkedProducsIds = array_unique($linkedProducsIds);

        if(!empty($linkedProducsIds)){

            $strProperty = '<model id="'.$arModels['ID'].'">'.PHP_EOL;
            fwrite($fp, $strProperty);

            if(isset($arModels['PROPERTY_MODEL_NEW_LINK_VALUE'])
                && !empty($arModels['PROPERTY_MODEL_NEW_LINK_VALUE'])){

                $modelNameRes = CIBlockElement::GetByID($arModels['PROPERTY_MODEL_NEW_LINK_VALUE']);
                if($modelNameRes
                    && $modelNameArr = $modelNameRes->GetNext()){

                    $modelName = $modelNameArr['NAME'];

                    if(isset($modelNameArr['NAME'])
                        && !empty($modelNameArr['NAME'])
                        && checkIsVaidXML($modelName)){

                        $strProperty = '<name>'.valid_text2xml($modelName, true).'</name>'.PHP_EOL;
                        fwrite($fp, $strProperty);
                    }

                }

            }

            if(isset($arModels['DETAIL_TEXT'])
                && !empty($arModels['DETAIL_TEXT'])
                && checkIsVaidXML($arModels['DETAIL_TEXT'])){

                $strProperty = '<description>'.valid_text2xml($arModels['DETAIL_TEXT'], true).'</description>'.PHP_EOL;
                fwrite($fp, $strProperty);
            }

            if(isset($arModels['DETAIL_PICTURE'])
                && !empty($arModels['DETAIL_PICTURE'])){

                if(is_numeric($arModels['DETAIL_PICTURE'])){
                    $arModels['DETAIL_PICTURE'] = get_file_path($arModels['DETAIL_PICTURE']);
                }
                if(!empty($arModels['DETAIL_PICTURE'])
                    && preg_match('~\.(jpg|jpeg|gif|png)$~is',$arModels['DETAIL_PICTURE'])
                    && checkIsVaidXML($arModels['DETAIL_PICTURE'])
                ){

                    $arModels['DETAIL_PICTURE'] = rectangleImage($_SERVER['DOCUMENT_ROOT'].$arModels['DETAIL_PICTURE'],$product_image_width,$product_image_height,$arModels['DETAIL_PICTURE'],"",true,false);
                    $arModels['DETAIL_PICTURE'] = preg_match('~http(s*?)://~is',$arModels['DETAIL_PICTURE']) ? $arModels['DETAIL_PICTURE'] : ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  $arModels['DETAIL_PICTURE']);
                    $strProperty = '<picture>'.valid_text2xml($arModels['DETAIL_PICTURE'], true).'</picture>'.PHP_EOL;
                    fwrite($fp, $strProperty);
                }

            }

            if(isset($arModels['DETAIL_PAGE_URL'])
                && !empty($arModels['DETAIL_PAGE_URL'])
                && checkIsVaidXML($arModels['DETAIL_PAGE_URL'])){

                $arModels['DETAIL_PAGE_URL'] = ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  "/model/".$arModels['CODE']."/");
                $strProperty = '<url>'.valid_text2xml($arModels['DETAIL_PAGE_URL'], true).'</url>'.PHP_EOL;
                fwrite($fp, $strProperty);
            }


            if(isset($arModels['PROPERTY_MANUFACTURER_VALUE'])
                && !empty($arModels['PROPERTY_MANUFACTURER_VALUE'])
                && checkIsVaidXML($arModels['PROPERTY_MANUFACTURER_VALUE'])){

                $manufacturer = $arModels['PROPERTY_MANUFACTURER_VALUE'];
                $strProperty = '<manufacturer>'.valid_text2xml($manufacturer, true).'</manufacturer>'.PHP_EOL;
                fwrite($fp, $strProperty);
            }

            if(isset($arModels['PROPERTY_INSTRUCTION_VALUE'])
                && !empty($arModels['PROPERTY_INSTRUCTION_VALUE'])){

                $instruction = $arModels['PROPERTY_INSTRUCTION_VALUE'];

                if(is_numeric($instruction)){
                    $instruction = get_file_path($instruction);

                }

                if(!empty($instruction)
                    && checkIsVaidXML($instruction)){
                    $instruction = preg_match('~http(s*?)://~is',$instruction) ? $instruction : ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  $instruction);
                    $strProperty = '<instruction>'.valid_text2xml($instruction, true).'</instruction>'.PHP_EOL;
                    fwrite($fp, $strProperty);
                }
            }


            if(isset($arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                && !empty($arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                && checkIsVaidXML($arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){
                $type_of_product = $arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'];
                $strProperty = '<typeofproduct>'.valid_text2xml($type_of_product, true).'</typeofproduct>'.PHP_EOL;
                fwrite($fp, $strProperty);
            }

            $strProperty = '<offers>'.PHP_EOL;
            fwrite($fp, $strProperty);

            foreach($linkedProducsIds as $linkedProductId){

                $linkedProductDB = CIBlockElement::GetList(array(),array('ID' => $linkedProductId, 'ACTIVE' => 'Y'), false, false, array('ID','NAME','DETAIL_PICTURE','DETAIL_TEXT','DETAIL_PAGE_URL'));

                if($linkedProductDB
                    && $linkedProductArr = $linkedProductDB->GetNext()){

                    $strProperty = '<offer id="'.$linkedProductId.'">'.PHP_EOL;
                    fwrite($fp, $strProperty);

                    $linkedName = $linkedProductArr['NAME'];

                    if(checkIsVaidXML($linkedName)){
                        $strProperty = '<name>'.valid_text2xml($linkedName, true).'</name>'.PHP_EOL;
                        fwrite($fp, $strProperty);
                    }

                    if ($arPrice = CCatalogProduct::GetOptimalPrice(
                        $linkedProductId,
                        1,
                        array(2), // anonymous
                        'N',
                        array(),
                        SITE_ID
                    ))
                    {

                        if(isset($arPrice['PRICE']['PRICE'])
                            && isset($arPrice['PRICE']['CURRENCY'])){

                            $minPriceRUR        = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']["CURRENCY"], $RUR);

                            if(!empty($minPriceRUR)
                                && checkIsVaidXML($minPriceRUR)){
                                $strProperty = '<price>'.valid_text2xml(CurrencyFormat($minPriceRUR,$RUR), true).'</price>'.PHP_EOL;
                                fwrite($fp, $strProperty);
                            }

                        }

                    }

                    $linkedText = $linkedProductArr['DETAIL_TEXT'];

                    if(isset($linkedProductArr['DETAIL_TEXT'])
                        && !empty($linkedProductArr['DETAIL_TEXT'])
                        && checkIsVaidXML($linkedText)){
                        $strProperty = '<description>'.valid_text2xml($linkedText, true).'</description>'.PHP_EOL;
                        fwrite($fp, $strProperty);
                    }


                    $linkedURL = $linkedProductArr['DETAIL_PAGE_URL'];
                    $linkedURL = preg_match('~http(s*?)://~is',$linkedURL) ? $linkedURL : ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  $linkedURL);

                    if(checkIsVaidXML($linkedURL)){
                        $strProperty = '<url>'.valid_text2xml($linkedURL, true).'</url>'.PHP_EOL;
                        fwrite($fp, $strProperty);
                    }

                    $linkedPicture = $linkedProductArr['DETAIL_PICTURE'];

                    if(is_numeric($linkedPicture)){
                        $linkedPicture = get_file_path($linkedPicture);
                    }
                    if(!empty($linkedPicture)
                        && preg_match('~\.(jpg|jpeg|gif|png)$~is',$linkedPicture)
                        && checkIsVaidXML($linkedPicture)
                    ){

                        $linkedPicture = rectangleImage($_SERVER['DOCUMENT_ROOT'].$linkedPicture,$product_image_width,$product_image_height,$linkedPicture,"",true,false);
                        $linkedPicture = preg_match('~http(s*?)://~is',$linkedPicture) ? $linkedPicture : ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  $linkedPicture);
                        $strProperty = '<picture>'.valid_text2xml($linkedPicture, true).'</picture>'.PHP_EOL;
                        fwrite($fp, $strProperty);
                    }

                    $linkedProductSectionsDB = CIBlockElement::GetElementGroups($linkedProductId, false);

                    if($linkedProductSectionsDB){
                        while($linkedProductSectionsArr = $linkedProductSectionsDB->GetNext()) {
                            $linkedSection = $linkedProductSectionsArr["NAME"];

                            if(checkIsVaidXML($linkedSection)){
                                $strProperty = '<category id="'.$linkedProductSectionsArr["ID"].'">'.valid_text2xml($linkedSection, true).'</category>'.PHP_EOL;
                                fwrite($fp, $strProperty);
                            }
                        }
                    }

                    $product_property = array();

                    $strProperty = '<properties>'.PHP_EOL;
                    fwrite($fp, $strProperty);

                    foreach($cpCodes as $productType => $propCodes){

                        foreach($propCodes as $propCode){

                            $linkedProductsPropsRes = CIBlockElement::GetProperty(
                                11,
                                $linkedProductId,
                                array(),
                                Array("CODE" => $propCode)
                            );

                            if($linkedProductsPropsRes){

                                while($linkedProductsProps = $linkedProductsPropsRes->GetNext()){

                                    $propertyValue = '';
                                    $productCode = '';

                                    switch($productType){
                                        case 'F':

                                            if(isset($linkedProductsProps['VALUE'])
                                                && !empty($linkedProductsProps['VALUE'])){

                                                $propertyValue = $linkedProductsProps['VALUE'];

                                                if(is_numeric($propertyValue)){
                                                    $propertyValue = get_file_path($propertyValue);
                                                }


                                                if(!empty($propertyValue)
                                                    && preg_match('~\.(jpg|jpeg|gif|png)$~is',$propertyValue)){

                                                    $propertyValue = rectangleImage($_SERVER['DOCUMENT_ROOT'].$propertyValue,$product_image_width,$product_image_height,$propertyValue,"",true,false);
                                                    $propertyValue = preg_match('~http(s*?)://~is',$propertyValue) ? $propertyValue : ((CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] .  $propertyValue);

                                                }

                                                $productCode = $propCode;

                                            }

                                            break;
                                        case 'E':


                                            if(isset($linkedProductsProps['VALUE_ENUM'])){

                                                $propertyValue = $linkedProductsProps['VALUE_ENUM'];
                                                $productCode = $propCode;

                                            }

                                            break;
                                        case 'S':

                                            if(isset($linkedProductsProps['VALUE'])
                                                && !empty($linkedProductsProps['VALUE'])){

                                                $propertyValue = $linkedProductsProps['VALUE'];
                                                $productCode = $propCode;


                                            }

                                            break;
                                    }

                                    if(!empty($productCode)
                                        && !empty($propertyValue)
                                        && checkIsVaidXML($productCode)
                                        && checkIsVaidXML($propertyValue)){

                                        $strProperty = '<param value="'.preg_replace('~[^\w+\-\_]~is','',trim(mb_strtolower(valid_text2xml($productCode, true)))).'">'.valid_text2xml($propertyValue, true).'</param>'.PHP_EOL;
                                        fwrite($fp, $strProperty);
                                    }

                                }


                            }

                        }

                    }

                    $strProperty = '</properties>'.PHP_EOL;
                    fwrite($fp, $strProperty);

                    $traitsValue = '';

                    $mainProductsDB = CIBlockElement::GetProperty(
                        11,
                        $linkedProductId,
                        array(),
                        Array("CODE" => "MAIN_PRODUCTS")
                    );

                    if($mainProductsDB
                        && $mainProductsArr = $mainProductsDB->GetNext()){

                        if(isset($mainProductsArr['VALUE'])
                            && !empty($mainProductsArr['VALUE'])){

                            $mainProductsTraitsDB = CIBlockElement::GetProperty(
                                16,
                                $mainProductsArr['VALUE'],
                                array(),
                                Array("CODE" => "CML2_TRAITS")
                            );

                            if($mainProductsTraitsDB){

                                while($mainProductsTraitsArr = $mainProductsTraitsDB->GetNext()){


                                    if(isset($mainProductsTraitsArr['DESCRIPTION'])
                                        && $mainProductsTraitsArr['DESCRIPTION'] == 'Код'
                                        && isset($mainProductsTraitsArr['VALUE'])
                                        && checkIsVaidXML($mainProductsTraitsArr['VALUE'])){

                                        $traitsValue = $mainProductsTraitsArr['VALUE'];
                                        $strProperty = '<mainproductcode>'.valid_text2xml($traitsValue, true).'</mainproductcode>'.PHP_EOL;
                                        fwrite($fp, $strProperty);

                                    }

                                }


                            }


                        }

                    }

                    $strProperty = '</offer>'.PHP_EOL;
                    fwrite($fp, $strProperty);

                }

            }

            $strProperty = '</offers>'.PHP_EOL;
            fwrite($fp, $strProperty);


        }

        $strProperty = '</model>'.PHP_EOL;
        fwrite($fp, $strProperty);

        $newFileName = get_next_part_num($file_to_write);

        if($file_to_write != $newFileName){
            fclose($fp);
            $fp = fopen($newFileName,'ab+');
            $file_to_write = $newFileName;
        }


        file_put_contents(__FILE_MODELS_NAME,$arModels['ID']);

        if((time() - ($timer + MAX_TO_EXECUTE)) > 0){
            break;
        }

    }

    if($currentIteration > 0){
        fclose($fp);
        die();
    }

}


if(getLastLine($file_to_write) != '</root>'){

    fwrite($fp,'</root>');
    fclose($fp);

}

foreach (glob(dirname(__DIR__).'/parser_models/models_to_xml_test_*.xml') as $filename) {
    @unlink($filename);
}

foreach (glob(dirname(dirname(__DIR__)).'/bitrix/tmp/models_to_xml_test_*.xml') as $filename) {
    $fileInfo = pathinfo($filename);
    @copy($filename, dirname(__DIR__).'/parser_models/'.$fileInfo['filename'].'.'.$fileInfo['extension']);
    @unlink($filename);
}

file_put_contents(__FILE_MODELS_NAME,'0');

echo 'done date:'.date('Y-m-d H:i:s')."\n";
