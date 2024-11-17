<?php
//<title>Yandex turbo pages model TWG000</title>

ini_set('default_charset', 'utf-8');
global $protocol, $product_image_width, $product_image_height;

function getDimensionsAndWeight($product_id)
{
    static $aDimensions;

    if (!is_array($aDimensions)) {
        $aDimensions = [];
    }

    if (!isset($aDimensions[$product_id])) {

        $sDimension = '';
        $rcProducts = CCatalogProduct::GetList(
            ["ID" => "DESC"],
            ["ID" => $product_id],
            false,
            false,
            [
                'LENGTH',
                'WIDTH',
                'HEIGHT',
                'WEIGHT'
            ]
        );

        if ($rcProducts) {
            while ($acProducts = $rcProducts->Fetch()) {

                $acProducts = array_map('trim', $acProducts);
                $acProducts = array_map('floatval', $acProducts);

                $acProducts = array_map(function ($fVal) {
                    $fVal = $fVal / 10;
                    return $fVal;
                }, $acProducts);

                if (!empty($acProducts['LENGTH'])
                    && !empty($acProducts['WIDTH'])
                    && !empty($acProducts['HEIGHT'])) {

                    $sDimension .= '<dimensions>' . $acProducts['LENGTH'] . '/' . $acProducts['WIDTH'] . '/' . $acProducts['HEIGHT'] . '</dimensions>';

                }

                if (!empty($acProducts['WEIGHT'])) {

                    $acProducts['WEIGHT'] /= 100;
                    $sDimension .= '<weight>' . $acProducts['WEIGHT'] . '</weight>';

                }

            }
        }

        $aDimensions[$product_id] = $sDimension;

    } else {

        $sDimension = $aDimensions[$product_id];

    }

    return $sDimension;

}

$protocol = 'https://';

$product_image_width = 370;
$product_image_height = "";

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/catalog/export_yandex.php');
set_time_limit(0);

$IBLOCK_ID = (int)$IBLOCK_ID;

if (isset($_REQUEST['PROFILE_ID']) && !empty($_REQUEST['PROFILE_ID'])) {
    $PROFILE_ID = (int)trim($_REQUEST['PROFILE_ID']);
} else if (isset($argv) && isset($argv[1])) {
    $PROFILE_ID = (int)trim($argv[1]);
}

global $USER, $APPLICATION, $DB;

if (!function_exists('convert_file_path')) {
    function convert_file_path($pictNo, $upload_dir = false)
    {

        global $DB;

        $ar_file = false;
        $pictNo = (int)$pictNo;

        if ($pictNo > 0) {
            $strSql = "SELECT f.*," . $DB->DateToCharFunction("f.TIMESTAMP_X") . " as TIMESTAMP_X FROM b_file f WHERE f.ID=" . $pictNo;
            $dbFRes = $DB->Query($strSql, false, "FILE: " . __FILE__ . "<br>LINE: " . __LINE__);

            if ($dbFRes
                && is_object($dbFRes)
                && $ar_file = $dbFRes->Fetch()) {

                $ar_file['SRC'] = CFile::GetFileSRC($ar_file, $upload_dir);

            }

        }

        return $ar_file;
    }
}

$bTmpUserCreated = false;
if (!CCatalog::IsUserExists()) {
    $bTmpUserCreated = true;
    if (isset($USER)) {
        $USER_TMP = $USER;
        unset($USER);
    }

    $USER = new CUser();
}

CCatalogDiscountSave::Disable();

$arYandexFields = array('vendor', 'vendorCode', 'model', 'author', 'name', 'publisher', 'series', 'year', 'ISBN', 'volume', 'part', 'language', 'binding', 'page_extent', 'table_of_contents', 'performed_by', 'performance_type', 'storage', 'format', 'recording_length', 'artist', 'title', 'year', 'media', 'starring', 'director', 'originalName', 'country', 'aliases', 'description', 'sales_notes', 'promo', 'provider', 'tarifplan', 'xCategory', 'additional', 'worldRegion', 'region', 'days', 'dataTour', 'hotel_stars', 'room', 'meal', 'included', 'transport', 'price_min', 'price_max', 'options', 'manufacturer_warranty', 'country_of_origin', 'downloadable', 'param', 'place', 'hall', 'hall_part', 'is_premiere', 'is_kids', 'date',);

if (!function_exists('substr_by_word')) {
    function substr_by_word($description, $maxlength = 0, $clean_text = true)
    {

        if (!empty($maxlength)) {

            $description = trim($description);
            $description = newsListMainTemplateTools::cleanText($description, $clean_text);

            $maxlength = (int)$maxlength;
            $description = (string)$description;

            if ($maxlength > 0 && mb_strlen($description) > $maxlength) {
                $description = mb_substr($description, 0, $maxlength);

                $max = array();
                $max[] = (int)mb_strrpos($description, " ");
                $max[] = (int)mb_strrpos($description, "!");
                $max[] = (int)mb_strrpos($description, "?");
                $max[] = (int)mb_strrpos($description, ",");
                $max[] = (int)mb_strrpos($description, ".");
                $max[] = (int)mb_strrpos($description, "-");
                $max[] = (int)mb_strrpos($description, "+");

                $max = max($max);

                if ($max > 0) {
                    $description = mb_substr($description, 0, $max);
                }

            }

        }

        return $description;
    }
}

if (!function_exists("yandex_replace_special")) {
    function yandex_replace_special($arg)
    {
        if (in_array($arg[0], array("&quot;", "&amp;", "&lt;", "&gt;")))
            return $arg[0];
        else
            return " ";
    }
}

if (!function_exists("yandex_text2xml")) {
    function yandex_text2xml($text, $bHSC = false, $bDblQuote = false, $denyCovert = false)
    {
        global $APPLICATION;

        $bHSC = (true == $bHSC ? true : false);
        $bDblQuote = (true == $bDblQuote ? true : false);

        if ($bHSC) {
            $text = htmlspecialcharsbx($text);
            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);
        }
        $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
        $text = str_replace("'", "'", $text);

        if (!$denyCovert) {
            //$text = $APPLICATION->ConvertCharset($text, LANG_CHARSET, 'windows-1251');
        };

        return $text;
    }
}

if (!function_exists('yandex_get_value')) {
    function yandex_get_value($arOffer, $param, $PROPERTY, &$arProperties, &$arUserTypeFormat, $isParam = false, $checkEmpty = false)
    {
        global $iblockServerName, $protocol, $product_image_width, $product_image_height;

        $strProperty = '';
        $bParam = (strncmp($param, 'PARAM_', 6) == 0);
        if (isset($arProperties[$PROPERTY]) && !empty($arProperties[$PROPERTY])) {
            $PROPERTY_CODE = $arProperties[$PROPERTY]['CODE'];
            $arProperty = (
            isset($arOffer['PROPERTIES'][$PROPERTY_CODE])
                ? $arOffer['PROPERTIES'][$PROPERTY_CODE]
                : $arOffer['PROPERTIES'][$PROPERTY]
            );

            $value = '';
            $description = '';
            switch ($arProperties[$PROPERTY]['PROPERTY_TYPE']) {
                case 'USER_TYPE':
                    if (!empty($arProperty['VALUE'])) {
                        if (is_array($arProperty['VALUE'])) {
                            $arValues = array();
                            foreach ($arProperty["VALUE"] as $oneValue) {
                                $arValues[] = call_user_func_array($arUserTypeFormat[$PROPERTY],
                                    array(
                                        $arProperty,
                                        array("VALUE" => $oneValue),
                                        array('MODE' => 'SIMPLE_TEXT'),
                                    ));
                            }
                            $value = implode(', ', $arValues);
                        } else {
                            $value = call_user_func_array($arUserTypeFormat[$PROPERTY],
                                array(
                                    $arProperty,
                                    array("VALUE" => $arProperty["VALUE"]),
                                    array('MODE' => 'SIMPLE_TEXT'),
                                ));
                        }
                    }
                    break;
                case 'E':
                    if (!empty($arProperty['VALUE'])) {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE'])) {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        } else {
                            foreach ($arProperty['VALUE'] as &$intValue) {
                                $intValue = (int)$intValue;
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue)) {
                            $dbRes = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arProperties[$PROPERTY]['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, false, array('NAME'));
                            while ($arRes = $dbRes->Fetch()) {
                                $value .= ($value ? ', ' : '') . $arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'G':
                    if (!empty($arProperty['VALUE'])) {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE'])) {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        } else {
                            foreach ($arProperty['VALUE'] as &$intValue) {
                                $intValue = (int)$intValue;
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue)) {
                            $dbRes = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, array('NAME'));
                            while ($arRes = $dbRes->Fetch()) {
                                $value .= ($value ? ', ' : '') . $arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'L':
                    if (!empty($arProperty['VALUE'])) {
                        if (is_array($arProperty['VALUE']))
                            $value .= implode(', ', $arProperty['VALUE']);
                        else
                            $value .= $arProperty['VALUE'];
                    }
                    break;
                case 'F':
                    if (!empty($arProperty['VALUE'])) {
                        if (is_array($arProperty['VALUE'])) {
                            foreach ($arProperty['VALUE'] as &$intValue) {
                                $intValue = (int)$intValue;
                                if ($intValue > 0) {

                                    if ($ar_file = convert_file_path($intValue)) {
                                        if (isset($ar_file["SRC"])
                                            && CFile::IsImage($_SERVER["DOCUMENT_ROOT"] . '/' . $ar_file["SRC"])
                                            && !empty($ar_file["SRC"]) && function_exists('rectangleImage')) {

                                            $ar_file["SRC"] = rectangleImage($_SERVER['DOCUMENT_ROOT'] . '/' . $ar_file["SRC"], $product_image_width, $product_image_height, $ar_file["SRC"], "", true, true);

                                        }

                                        if (mb_substr($ar_file["SRC"], 0, 1) == "/")
                                            $strFile = $protocol . $iblockServerName . implode("/", array_map("rawurlencode", explode("/", $ar_file["SRC"])));
                                        elseif (preg_match("/^(http|https):\\/\\/(.*?)\\/(.*)\$/", $ar_file["SRC"], $match))
                                            $strFile = $protocol . $match[2] . '/' . implode("/", array_map("rawurlencode", explode("/", $match[3])));
                                        else
                                            $strFile = $ar_file["SRC"];
                                        $value .= ($value ? ', ' : '') . $strFile;
                                    }
                                }
                            }
                            if (isset($intValue))
                                unset($intValue);
                        } else {
                            $arProperty['VALUE'] = (int)$arProperty['VALUE'];
                            if ($arProperty['VALUE'] > 0) {
                                if ($ar_file = convert_file_path($arProperty['VALUE'])) {

                                    if (isset($ar_file["SRC"])
                                        && CFile::IsImage($_SERVER["DOCUMENT_ROOT"] . '/' . $ar_file["SRC"])
                                        && !empty($ar_file["SRC"]) && function_exists('rectangleImage')) {

                                        $ar_file["SRC"] = rectangleImage($_SERVER['DOCUMENT_ROOT'] . '/' . $ar_file["SRC"], $product_image_width, $product_image_height, $ar_file["SRC"], "", true, true);

                                    }

                                    if (mb_substr($ar_file["SRC"], 0, 1) == "/")
                                        $strFile = $protocol . $iblockServerName . implode("/", array_map("rawurlencode", explode("/", $ar_file["SRC"])));
                                    elseif (preg_match("/^(http|https):\\/\\/(.*?)\\/(.*)\$/", $ar_file["SRC"], $match))
                                        $strFile = $protocol . $match[2] . '/' . implode("/", array_map("rawurlencode", explode("/", $match[3])));
                                    else
                                        $strFile = $ar_file["SRC"];
                                    $value = $strFile;
                                }
                            }
                        }
                    }
                    break;
                default:
                    if ($bParam && $arProperty['WITH_DESCRIPTION'] == 'Y') {
                        $description = $arProperty['DESCRIPTION'];
                        $value = $arProperty['VALUE'];
                    } else {
                        $value = is_array($arProperty['VALUE']) ? implode(', ', $arProperty['VALUE']) : $arProperty['VALUE'];
                    }
            }

            // !!!! check multiple properties and properties like CML2_ATTRIBUTES

            if ($bParam) {
                if (is_array($description)) {
                    foreach ($value as $key => $val) {
                        $strProperty .= $strProperty ? "\n" : "";

                        if (($checkEmpty && !empty($val)) || !$checkEmpty)
                            $strProperty .= '' . yandex_text2xml($description[$key], true) . ': ' . yandex_text2xml($val, true) . '';
                    }
                } else {

                    if (isset($arProperties[$PROPERTY]['CODE'])
                        && !empty($arProperties[$PROPERTY]['CODE'])
                        && !$isParam) {

                        $strProperty .= '<' . yandex_text2xml(mb_strtolower(trim($arProperties[$PROPERTY]['CODE'])), true) . '>' . yandex_text2xml($value, true) . '</' . yandex_text2xml(mb_strtolower(trim($arProperties[$PROPERTY]['CODE'])), true) . '>';

                    } else {

                        if (($checkEmpty && !empty($value)) || !$checkEmpty)
                            $strProperty .= '' . yandex_text2xml($arProperties[$PROPERTY]['NAME'], true) . ': ' . yandex_text2xml($value, true) . '';

                    }
                }
            } else {
                $param_h = yandex_text2xml($param, true);
                $strProperty .= '<' . $param_h . '>' . yandex_text2xml($value, true) . '</' . $param_h . '>';
            }
        }

        return $strProperty;
    }
}

$arRunErrors = array();

if ($XML_DATA && CheckSerializedData($XML_DATA)) {
    $XML_DATA = unserialize(stripslashes($XML_DATA));
    if (!is_array($XML_DATA)) $XML_DATA = array();
}

if (!is_array($XML_DATA)) {
    $XML_DATA = array();
}

if (!is_array($XML_DATA['XML_DATA'])) {
    $XML_DATA['XML_DATA'] = array();
}

$XML_DATA['XML_DATA']['vendor'] = 230;
$XML_DATA['XML_DATA']['country_of_origin'] = 194;

$XML_DATA['PRICE'] = isset($XML_DATA['PRICE']) && $XML_DATA['PRICE'] > 0 ? $XML_DATA['PRICE'] : 0;
$IBLOCK_ID = (int)$IBLOCK_ID;

if (isset($_REQUEST['PROFILE_ID']) && !empty($_REQUEST['PROFILE_ID'])) {
    $PROFILE_ID = (int)trim($_REQUEST['PROFILE_ID']);
} else if (isset($argv) && isset($argv[1])) {
    $PROFILE_ID = (int)trim($argv[1]);
}

if ($IBLOCK_ID == 11) {

    $PROPS = isset($PROPS) && !empty($PROPS) ? $PROPS : $_REQUEST["PROPS"];
    if(is_array($PROPS)) {
        $PROPS = array_map('trim', $PROPS);
    }
    if(is_array($PROPS)) {
        $PROPS = array_map('intval', $PROPS);
    }
    if(is_array($PROPS)) {
        $PROPS = array_unique($PROPS);
    }
    if(is_array($PROPS)) {
        $PROPS = array_filter($PROPS);
    }
    $XML_DATA['XML_DATA']['PARAMS'] = $PROPS;

    $QUALITY = isset($QUALITY) && !empty($QUALITY) ? $QUALITY : $_REQUEST["QUALITY"];

    if(is_array($QUALITY)) {
        $QUALITY = array_map('trim', $QUALITY);
    }
    if(is_array($QUALITY)) {
        $QUALITY = array_map('intval', $QUALITY);
    }
    if(is_array($QUALITY)) {
        $QUALITY = array_unique($QUALITY);
    }
    if(is_array($QUALITY)) {
        $QUALITY = array_filter($QUALITY);
    }

}


$db_iblock = CIBlock::GetByID($IBLOCK_ID);
if (!($ar_iblock = $db_iblock->Fetch())) {
    $arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_FOUND_EXT'));
} else {
    $SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

    $RUR = "RUB";
    $site_id = "";
    $rsSites = CSite::GetList($by = "sort", $order = "desc", array("DOMAIN" => $SETUP_SERVER_NAME));
    if ($rsSites && is_object($rsSites) && method_exists($rsSites, "Fetch")) {

        while ($arSite = $rsSites->Fetch()) {
            $site_id = $arSite["ID"];
        }

    }

    if (!empty($site_id)) {
        $RUR = COption::GetOptionString("sale", "default_currency", "RUB", $site_id);
    }

    if (mb_strlen($SETUP_SERVER_NAME) <= 0) {
        if (mb_strlen($ar_iblock['SERVER_NAME']) <= 0) {
            $b = "sort";
            $o = "asc";
            $rsSite = CSite::GetList($b, $o, array("LID" => $ar_iblock["LID"]));
            if ($arSite = $rsSite->Fetch())
                $ar_iblock["SERVER_NAME"] = $arSite["SERVER_NAME"];
            if (mb_strlen($ar_iblock["SERVER_NAME"]) <= 0 && defined("SITE_SERVER_NAME"))
                $ar_iblock["SERVER_NAME"] = SITE_SERVER_NAME;
            if (mb_strlen($ar_iblock["SERVER_NAME"]) <= 0)
                $ar_iblock["SERVER_NAME"] = COption::GetOptionString("main", "server_name", "");
        }
    } else {
        $ar_iblock['SERVER_NAME'] = $SETUP_SERVER_NAME;
    }
    $ar_iblock['PROPERTY'] = array();
    $rsProps = CIBlockProperty::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
    );
    while ($arProp = $rsProps->Fetch()) {
        $arProp['ID'] = (int)$arProp['ID'];
        $arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
        $arProp['CODE'] = (string)$arProp['CODE'];
        $ar_iblock['PROPERTY'][$arProp['ID']] = $arProp;
    }
}

global $iblockServerName;
$iblockServerName = $ar_iblock["SERVER_NAME"];

$arProperties = array();
if (isset($ar_iblock['PROPERTY']))
    $arProperties = $ar_iblock['PROPERTY'];

$boolOffers = false;
$arOffers = false;
$arOfferIBlock = false;
$intOfferIBlockID = 0;
$arSelectOfferProps = array();
$arSelectedPropTypes = array('S', 'N', 'L', 'E', 'G');
$arOffersSelectKeys = array(
    YANDEX_SKU_EXPORT_ALL,
    YANDEX_SKU_EXPORT_MIN_PRICE,
    YANDEX_SKU_EXPORT_PROP,
);
$arCondSelectProp = array(
    'ZERO',
    'NONZERO',
    'EQUAL',
    'NONEQUAL',
);
$arPropertyMap = array();
$arSKUExport = array();

$arCatalog = CCatalog::GetByIDExt($IBLOCK_ID);
if (empty($arCatalog)) {
    $arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_IS_CATALOG'));
} else {
    $arOffers = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
    if (!empty($arOffers['IBLOCK_ID'])) {
        $intOfferIBlockID = $arOffers['IBLOCK_ID'];
        $rsOfferIBlocks = CIBlock::GetByID($intOfferIBlockID);
        if (($arOfferIBlock = $rsOfferIBlocks->Fetch())) {
            $boolOffers = true;
            $rsProps = CIBlockProperty::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC'),
                array('IBLOCK_ID' => $intOfferIBlockID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
            );
            while ($arProp = $rsProps->Fetch()) {
                $arProp['ID'] = (int)$arProp['ID'];
                if ($arOffers['SKU_PROPERTY_ID'] != $arProp['ID']) {
                    $arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
                    $arProp['CODE'] = (string)$arProp['CODE'];
                    $ar_iblock['OFFERS_PROPERTY'][$arProp['ID']] = $arProp;
                    $arProperties[$arProp['ID']] = $arProp;
                    if (in_array($arProp['PROPERTY_TYPE'], $arSelectedPropTypes))
                        $arSelectOfferProps[] = $arProp['ID'];
                    if ($arProp['CODE'] !== '') {
                        foreach ($ar_iblock['PROPERTY'] as &$arMainProp) {
                            if ($arMainProp['CODE'] == $arProp['CODE']) {
                                $arPropertyMap[$arProp['ID']] = $arMainProp['CODE'];
                                break;
                            }
                        }
                        if (isset($arMainProp))
                            unset($arMainProp);
                    }
                }
            }
            $arOfferIBlock['LID'] = $ar_iblock['LID'];
        } else {
            $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_OFFERS_IBLOCK_ID');
        }
    }
    if (true == $boolOffers) {
        if (empty($XML_DATA['SKU_EXPORT'])) {
            $arRunErrors[] = GetMessage('YANDEX_ERR_SKU_SETTINGS_ABSENT');
        } else {
            $arSKUExport = $XML_DATA['SKU_EXPORT'];;
            if (empty($arSKUExport['SKU_EXPORT_COND']) || !in_array($arSKUExport['SKU_EXPORT_COND'], $arOffersSelectKeys)) {
                $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_CONDITION_ABSENT');
            }
            if (YANDEX_SKU_EXPORT_PROP == $arSKUExport['SKU_EXPORT_COND']) {
                if (empty($arSKUExport['SKU_PROP_COND']) || !is_array($arSKUExport['SKU_PROP_COND'])) {
                    $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
                } else {
                    if (empty($arSKUExport['SKU_PROP_COND']['PROP_ID']) || !in_array($arSKUExport['SKU_PROP_COND']['PROP_ID'], $arSelectOfferProps)) {
                        $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_ABSENT');
                    }
                    if (empty($arSKUExport['SKU_PROP_COND']['COND']) || !in_array($arSKUExport['SKU_PROP_COND']['COND'], $arCondSelectProp)) {
                        $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_COND_ABSENT');
                    } else {
                        if ($arSKUExport['SKU_PROP_COND']['COND'] == 'EQUAL' || $arSKUExport['SKU_PROP_COND']['COND'] == 'NONEQUAL') {
                            if (empty($arSKUExport['SKU_PROP_COND']['VALUES'])) {
                                $arRunErrors[] = GetMessage('YANDEX_SKU_EXPORT_ERR_PROPERTY_VALUES_ABSENT');
                            }
                        }
                    }
                }
            }
        }
    }
}

$arUserTypeFormat = array();
foreach ($arProperties as $key => $arProperty) {
    $arProperty["USER_TYPE"] = (string)$arProperty["USER_TYPE"];
    $arUserTypeFormat[$arProperty["ID"]] = false;
    if ($arProperty["USER_TYPE"] !== '') {
        $arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
        if (isset($arUserType["GetPublicViewHTML"])) {
            $arUserTypeFormat[$arProperty["ID"]] = $arUserType["GetPublicViewHTML"];
            $arProperties[$key]['PROPERTY_TYPE'] = 'USER_TYPE';
        }
    }
}

$aDisableProfiles = [];

$typePropertyDB = \CIBlockPropertyEnum::GetList(
    Array(
        "DEF"=>"DESC",
        "SORT"=>"ASC"),
    Array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "CODE" => "DISABLE_EXPORT"
    )
);

if($typePropertyDB){
    while($typePropertyFields = $typePropertyDB->GetNext()){

        if(isset($typePropertyFields["VALUE"])){

            $aDisableProfiles[$typePropertyFields["XML_ID"]] = $typePropertyFields["VALUE"];

        }

    }

}

$skip_products = [];

if (isset($aDisableProfiles[$PROFILE_ID])) {

    $sfilter = [
        "IBLOCK_ID" => 11,
        "PROPERTY_DISABLE_EXPORT_VALUE" => $aDisableProfiles[$PROFILE_ID]
    ];
    
    $res = CIBlockElement::GetList(array(), $sfilter, false, false, ['ID']);
    
    if ($res) {
        while ($arr = $res->GetNext()) {
            $skip_products[$arr['ID']] = $arr['ID'];
        }
    }

}

twigBuildSectionFilter::skipAnalogueFilter($asFilter);

if (isset($asFilter['!ID'])) {
    $sAnalogue = array_values($asFilter['!ID']);
    foreach ($sAnalogue as $sValue) {
        $skip_products[$sValue] = $sValue;
    }
}

if (empty($arRunErrors)) {

    $arSelect = array("ID");

    $filter = array("IBLOCK_ID" => $IBLOCK_ID);
    if (!$bAllSections && !empty($arSectionIDs)) {
        $filter["INCLUDE_SUBSECTIONS"] = "Y";
        $filter["SECTION_ID"] = $arSectionIDs;
    }
    $filter["ACTIVE"] = "Y";
    $filter["ACTIVE_DATE"] = "Y";

    if (!empty($QUALITY)) {
        $filter["PROPERTY_QUALITY"] = $QUALITY;
    }

    $subFilter = $filter;

    $subFilter["=PROPERTY_PRODUCT_MODEL_SHOW_VALUE"] = "Да";

    if (isset($subFilter["!ID"]) && !empty($skip_products)) {
        $subFilter["!ID"] = $skip_products;
    }

    $res = CIBlockElement::GetList(array(), $subFilter, false, false, $arSelect);

    $products = [];

    while ($arr = $res->GetNext()) {
        $products[$arr['ID']] = $arr['ID'];
    }

    $products = array_values($products);
    $models = [];

    if (!empty($products)) {

        foreach ($products as $product_id) {

            $res = impelCIBlockElement::GetList(array(), array('IBLOCK_ID' => 17, 'PROPERTY_SIMPLEREPLACE_PRODUCTS' => $product_id, 'ACTIVE' => 'Y'), false, false, array('ID'));

            $models[$product_id] = [];

            if ($res) {
                while ($model = $res->GetNext()) {

                    $models[$product_id][$model['ID']] = $model['ID'];

                }

            }

        }

    }

    //id id + id товара разделенный подчеркиванием
    //название и описание мы берем из шаблона как детальной

    $bAllSections = False;
    $arSections = array();
    if (is_array($V)) {
        foreach ($V as $key => $value) {
            if (trim($value) == "0") {
                $bAllSections = True;
                break;
            }
            $value = (int)$value;
            if ($value > 0) {
                $arSections[] = $value;
            }
        }
    }

    if (!$bAllSections && count($arSections) <= 0) {
        $arRunErrors[] = GetMessage('YANDEX_ERR_NO_SECTION_LIST');
    }
}

if (!empty($XML_DATA['PRICE'])) {
    if ((int)$XML_DATA['PRICE'] > 0) {
        $rsCatalogGroups = CCatalogGroup::GetGroupsList(array('CATALOG_GROUP_ID' => $XML_DATA['PRICE'], 'GROUP_ID' => 2));
        if (!($arCatalogGroup = $rsCatalogGroups->Fetch())) {
            $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
        }
    } else {
        $arRunErrors[] = GetMessage('YANDEX_ERR_BAD_PRICE_TYPE');
    }
}

if (mb_strlen($SETUP_FILE_NAME) <= 0) {
    $arRunErrors[] = GetMessage("CATI_NO_SAVE_FILE");
} elseif (preg_match(BX_CATALOG_FILENAME_REG, $SETUP_FILE_NAME)) {
    $arRunErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
} else {
    $SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
}
if (empty($arRunErrors)) {
    /*	if ($GLOBALS["APPLICATION"]->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
        {
            $arRunErrors[] = str_replace('#FILE#', $SETUP_FILE_NAME,GetMessage('YANDEX_ERR_FILE_ACCESS_DENIED'));
        } */
}

$fileName = pathinfo($SETUP_FILE_NAME, PATHINFO_FILENAME) . '_tmp';
$fileExt = pathinfo($SETUP_FILE_NAME, PATHINFO_EXTENSION);
$fileDir = pathinfo($SETUP_FILE_NAME, PATHINFO_DIRNAME);

$tDir = ltrim($fileDir,'/') . '/' . $fileName . '.xml';

if (empty($arRunErrors)) {

    CheckDirPath($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME);

    if (!$fp = @fopen($_SERVER["DOCUMENT_ROOT"] . $tDir, "wb")) {
        $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
    } else {

        if (!@fwrite($fp, '<?xml version="1.0" encoding="utf-8"?>')) {
            $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, GetMessage('YANDEX_ERR_SETUP_FILE_WRITE'));
        }

    }

}


if (empty($arRunErrors)) {
    @fwrite($fp, "\n<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n");
    @fwrite($fp, "<yml_catalog date=\"" . Date("Y-m-d H:i") . "\">\n");
    @fwrite($fp, "<shop>\n");

    @fwrite($fp, "<name>" . htmlspecialcharsbx(COption::GetOptionString("main", "site_name", "")) . "</name>\n");

    @fwrite($fp, "<company>" . htmlspecialcharsbx(COption::GetOptionString("main", "site_name", "")) . "</company>\n");
    @fwrite($fp, "<url>" . $protocol . htmlspecialcharsbx($ar_iblock['SERVER_NAME']) . "</url>\n");
    @fwrite($fp, "<platform>1C-Bitrix</platform>\n");

    $strTmp = "<currencies>\n";

    $arCurrencyAllowed = array($RUR, 'USD', 'EUR', 'UAH', 'BYR', 'KZT');

    $BASE_CURRENCY = CCurrency::GetBaseCurrency();
    if (is_array($XML_DATA['CURRENCY'])) {


        foreach ($XML_DATA['CURRENCY'] as $CURRENCY => $arCurData) {
            if (in_array($CURRENCY, $arCurrencyAllowed)) {
                $strTmp .= "<currency id=\"" . $CURRENCY . "\""
                    . " rate=\"" . ($arCurData['rate'] == 'SITE' ? CCurrencyRates::ConvertCurrency(1, $CURRENCY, $RUR) : $arCurData['rate']) . "\""
                    . ($arCurData['plus'] > 0 ? ' plus="' . intval($arCurData['plus']) . '"' : '')
                    . " />\n";
            }
        }
    } else {
        $by = "sort";
        $order = "asc";
        $db_acc = CCurrency::GetList($by, $order);
        while ($arAcc = $db_acc->Fetch()) {
            if (in_array($arAcc['CURRENCY'], $arCurrencyAllowed))
                $strTmp .= '<currency id="' . $arAcc["CURRENCY"] . '" rate="' . (CCurrencyRates::ConvertCurrency(1, $arAcc["CURRENCY"], $RUR)) . '" />' . "\n";
        }
    }
    $strTmp .= "</currencies>\n";

    @fwrite($fp, $strTmp);
    unset($strTmp);

    //*****************************************//


    //*****************************************//
    $intMaxSectionID = 0;

    $strTmpCat = "";
    $strTmpOff = "";


    $arAvailGroups = array();
    if (!$bAllSections) {
        for ($i = 0, $intSectionsCount = count($arSections); $i < $intSectionsCount; $i++) {
            $filter_tmp = $filter;
            $db_res = CIBlockSection::GetNavChain($IBLOCK_ID, $arSections[$i]);
            $curLEFT_MARGIN = 0;
            $curRIGHT_MARGIN = 0;
            while ($ar_res = $db_res->Fetch()) {
                $curLEFT_MARGIN = (int)$ar_res["LEFT_MARGIN"];
                $curRIGHT_MARGIN = (int)$ar_res["RIGHT_MARGIN"];
                $arAvailGroups[$ar_res["ID"]] = array(
                    "ID" => (int)$ar_res["ID"],
                    "IBLOCK_SECTION_ID" => (int)$ar_res["IBLOCK_SECTION_ID"],
                    "NAME" => $ar_res["NAME"]
                );
                if ($intMaxSectionID < $ar_res["ID"])
                    $intMaxSectionID = $ar_res["ID"];
            }

            $filter = array("IBLOCK_ID" => $IBLOCK_ID, ">LEFT_MARGIN" => $curLEFT_MARGIN, "<RIGHT_MARGIN" => $curRIGHT_MARGIN, "ACTIVE" => "Y", "IBLOCK_ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y");
            $db_res = CIBlockSection::GetList(array("left_margin" => "asc"), $filter);
            while ($ar_res = $db_res->Fetch()) {
                $arAvailGroups[$ar_res["ID"]] = array(
                    "ID" => (int)$ar_res["ID"],
                    "IBLOCK_SECTION_ID" => (int)$ar_res["IBLOCK_SECTION_ID"],
                    "NAME" => $ar_res["NAME"]
                );
                if ($intMaxSectionID < $ar_res["ID"])
                    $intMaxSectionID = $ar_res["ID"];
            }
        }
    } else {
        $filter = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "IBLOCK_ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y");
        $db_res = CIBlockSection::GetList(array("left_margin" => "asc"), $filter);
        while ($ar_res = $db_res->Fetch()) {
            $arAvailGroups[$ar_res["ID"]] = array(
                "ID" => (int)$ar_res["ID"],
                "IBLOCK_SECTION_ID" => (int)$ar_res["IBLOCK_SECTION_ID"],
                "NAME" => $ar_res["NAME"]
            );
            if ($intMaxSectionID < $ar_res["ID"])
                $intMaxSectionID = $ar_res["ID"];
        }
    }

    $auProducts = array();
    $arSectionIDs = array();
    foreach ($arAvailGroups as &$value) {
        $strTmpCat .= "<category id=\"" . $value["ID"] . "\"" . ((int)$value["IBLOCK_SECTION_ID"] > 0 ? " parentId=\"" . $value["IBLOCK_SECTION_ID"] . "\"" : "") . ">" . yandex_text2xml($value["NAME"], true) . "</category>\n";
    }

    if (isset($value))
        unset($value);

    if (!empty($arAvailGroups))
        $arSectionIDs = array_keys($arAvailGroups);

    //$intMaxSectionID += 100000000;

    //*****************************************//
    $boolNeedRootSection = false;

    $arSelect = array("ID", "LID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE", "LANG_DIR", "DETAIL_PAGE_URL", "PROPERTY_MARKET_NAME", "PROPERTY_MANUFACTURER_DETAIL", "PROPERTY_ARTNUMBER", "PROPERTY_OLD_PRICE", "PROPERTY_MORE_PHOTO", "PROPERTY_MODEL_HTML", "PROPERTY_HIDE_MODELS");

    $filter = array("IBLOCK_ID" => $IBLOCK_ID);
    if (!$bAllSections && !empty($arSectionIDs)) {
        $filter["INCLUDE_SUBSECTIONS"] = "Y";
        $filter["SECTION_ID"] = $arSectionIDs;
    }

    $filter["ACTIVE"] = "Y";
    $filter["ACTIVE_DATE"] = "Y";

    if (!empty($QUALITY)) {
        $filter["PROPERTY_QUALITY"] = $QUALITY;
    }

    @fwrite($fp, "<categories>\n");
    @fwrite($fp, $strTmpCat);
    @fwrite($fp, "</categories>\n");

    @fwrite($fp, "<offers>\n");

    $res = CIBlockElement::GetList(array(), $filter, false, false, $arSelect);

    $total_sum = 0;
    $is_exists = false;
    $cnt = 0;

    while ($obElement = $res->GetNextElement()) {

        $apItems = [];

        $arAcc = $obElement->GetFields();

        if (is_array($XML_DATA['XML_DATA'])) {
            $arAcc["PROPERTIES"] = $obElement->GetProperties();
        }

        $apItems[] = $arAcc;

        if (isset($models[$arAcc['ID']])) {
            foreach ($models[$arAcc['ID']] as $model_id) {

                $product_id = $arAcc['ID'];
                $product = twigReplaceModel::getReplaces($model_id, $product_id);

                $product['DETAIL_PAGE_URL'] = $product['replaces']['DETAIL_PAGE_URL'];
                $product['DETAIL_PAGE_URL'] = rtrim($product['DETAIL_PAGE_URL'],'/').'/'.$product_id.'/';
                $product['IS_MODEL'] = $product['~IS_MODEL'] = true;
                $product["PROPERTY_HIDE_MODELS_VALUE"] = 'Скрыть';
                $product['PROPERTY_ARTNUMBER_VALUE'] = $product['~PROPERTY_ARTNUMBER_VALUE'] = $product['replaces']['artnumber'] .'w'. $model_id;

                $product['NAME'] = $product['~NAME'] = $product['replaces']['elt_models_title'];
                $product['PREVIEW_TEXT'] = $product['~PREVIEW_TEXT'] = $product['replaces']['text_for_elt_models'];
                $product['PROPERTY_MANUFACTURER_DETAIL_VALUE'] = $product['~PROPERTY_MANUFACTURER_DETAIL_VALUE'] = $product['replaces']['brand_model'];

                $apItems[] = array_merge($arAcc,$product);

                unset($product);
            }
        }

        foreach ($apItems as $arAcc) {

            if (isset($auProducts[$arAcc['PROPERTY_ARTNUMBER_VALUE']]) || isset($skip_products[$arAcc['ID']])) {
                continue;
            }

            $auProducts[$arAcc['PROPERTY_ARTNUMBER_VALUE']] = $arAcc['PROPERTY_ARTNUMBER_VALUE'];

            $strTmpOff = '';

            $quantity_product = 0;

            if (($quantity_product = get_quantity_product($arAcc['ID'], false, ($arStores = array(3, 6, 9, 10)))) > 0) {

                if (!canYouBuy($arAcc['ID'])) {
                    continue;
                }

                $arAcc['CATALOG_AVAILABLE'] = 'Y';
                $rsProducts = CCatalogProduct::GetList(
                    array(),
                    array('ID' => $arAcc['ID']),
                    false,
                    false,
                    array('ID', 'QUANTITY', 'QUANTITY_TRACE', 'CAN_BUY_ZERO')
                );
                if ($arProduct = $rsProducts->Fetch()) {
                    $arProduct['QUANTITY'] = doubleval($arProduct['QUANTITY']);
                    if (0 >= $arProduct['QUANTITY'] && 'Y' == $arProduct['QUANTITY_TRACE'] && 'N' == $arProduct['CAN_BUY_ZERO'])
                        $arAcc['CATALOG_AVAILABLE'] = 'N';
                }

                $arAcc['CATALOG_AVAILABLE'] = 'Y';

                $str_AVAILABLE = ' available="' . ('Y' == $arAcc['CATALOG_AVAILABLE'] ? 'true' : 'false') . '"';

                $minPrice = 0;
                $minPriceRUR = 0;
                $minPriceGroup = 0;
                $minPriceCurrency = "";

                if ($XML_DATA['PRICE'] > 0) {

                    $buy_id = getBondsProduct($arAcc['ID']);

                    $rsPrices = CPrice::GetListEx(array(), array(
                            'PRODUCT_ID' => $buy_id,
                            'CATALOG_GROUP_ID' => $XML_DATA['PRICE'],
                            'CAN_BUY' => 'Y',
                            'GROUP_GROUP_ID' => array(2),
                            '+<=QUANTITY_FROM' => 1,
                            '+>=QUANTITY_TO' => 1,
                        )
                    );
                    if ($arPrice = $rsPrices->Fetch()) {
                        if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                            $buy_id,
                            1,
                            array(2), // anonymous
                            'N',
                            array($arPrice),
                            $site_id
                        )) {
                            $minPrice = $arOptimalPrice['PRICE']['PRICE'];
                            $minPriceCurrency = $RUR;
                            $minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $arOptimalPrice["PRICE"]["CURRENCY"], $RUR);
                            $minPrice = CCurrencyRates::ConvertCurrency($minPrice, $arOptimalPrice["PRICE"]["CURRENCY"], $RUR);

                            $minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
                        }
                    }
                } else {

                    $buy_id = getBondsProduct($arAcc['ID']);

                    if ($arPrice = CCatalogProduct::GetOptimalPrice(
                        $buy_id,
                        1,
                        array(2), // anonymous
                        'N',
                        array(),
                        $site_id
                    )) {
                        $minPrice = $arPrice['DISCOUNT_PRICE'];
                        $minPriceCurrency = $RUR;
                        $minPriceRUR = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']["CURRENCY"], $RUR);
                        $minPrice = CCurrencyRates::ConvertCurrency($arPrice['PRICE']['PRICE'], $arPrice['PRICE']["CURRENCY"], $RUR);

                        $minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
                    }
                }

                if ($minPrice <= 0) continue;

                $boolCurrentSections = false;
                $bNoActiveGroup = true;

                $first_category = "";

                $strTmpOff_tmp = "";
                $db_res1 = CIBlockElement::GetElementGroups($arAcc["ID"], false, array('ID', 'ADDITIONAL_PROPERTY_ID', 'IBLOCK_ID', 'NAME'));
                while ($ar_res1 = $db_res1->Fetch()) {
                    if (0 < (int)$ar_res1['ADDITIONAL_PROPERTY_ID'])
                        continue;
                    $boolCurrentSections = true;
                    if (in_array((int)$ar_res1["ID"], $arSectionIDs)) {


                        $strTmpOff_tmp .= "<categoryId>" . $ar_res1["ID"] . "</categoryId>\n";

                        if (empty($first_category)) {

                            $uf_arresult = CIBlockSection::GetList(array("SORT" => "ASC"), array("ID" => $ar_res1["ID"], 'IBLOCK_ID' => $ar_res1['IBLOCK_ID']), false, array("UF_GOOGLE_SECTION"));
                            if ($uf_value = $uf_arresult->Fetch()):
                                if (mb_strlen($uf_value["UF_GOOGLE_SECTION"]) > 0):
                                    $first_category = $uf_value["UF_GOOGLE_SECTION"];
                                endif;
                            endif;

                        }

                        $bNoActiveGroup = False;

                    }
                }

                if (!$boolCurrentSections) {
                    $boolNeedRootSection = true;
                    $strTmpOff_tmp .= "<categoryId>" . $intMaxSectionID . "</categoryId>\n";


                    if (empty($first_category)) {

                        $uf_arresult = CIBlockSection::GetList(array("SORT" => "ASC"), array("ID" => $intMaxSectionID, 'IBLOCK_ID' => $ar_res1['IBLOCK_ID']), false, array("UF_GOOGLE_SECTION"));
                        if ($uf_value = $uf_arresult->Fetch()):
                            if (mb_strlen($uf_value["UF_GOOGLE_SECTION"]) > 0):
                                $first_category = $uf_value["UF_GOOGLE_SECTION"];
                            endif;
                        endif;

                    }

                } else {
                    if ($bNoActiveGroup)
                        continue;
                }

                if (mb_strlen($arAcc['DETAIL_PAGE_URL']) <= 0)
                    $arAcc['DETAIL_PAGE_URL'] = '/';
                else
                    $arAcc['DETAIL_PAGE_URL'] = str_replace(' ', '%20', $arAcc['DETAIL_PAGE_URL']);

                if (is_array($XML_DATA) && $XML_DATA['TYPE'] && $XML_DATA['TYPE'] != 'none')
                    $str_TYPE = ' type="' . htmlspecialcharsbx($XML_DATA['TYPE']) . '"';
                else
                    $str_TYPE = '';

                $str_AVAILABLE = ' available="' . (canYouBuy($arAcc["ID"]) ? 'true' : 'false') . '"';

                $artnumber = ((isset($arAcc["~PROPERTY_ARTNUMBER_VALUE"]) && !empty($arAcc["~PROPERTY_ARTNUMBER_VALUE"])) ? $arAcc["~PROPERTY_ARTNUMBER_VALUE"] : '');

                if (!empty($artnumber)) {

                    if (mb_strlen($artnumber) > 70) {
                        $artnumber = substr_by_word($artnumber, 70);
                    };

                };

                if (empty($artnumber))
                    continue;

                $strTmpOff .= "<offer id=\"" . $artnumber . "\"" . $str_TYPE . $str_AVAILABLE . ">\n";
                $strTmpOff .= "<url>" . $protocol . $ar_iblock['SERVER_NAME'] . htmlspecialcharsbx($arAcc["DETAIL_PAGE_URL"]) . (mb_strstr($arAcc['DETAIL_PAGE_URL'], '?') === false ? '' : '') . "</url>\n";
                //$strTmpOff .= "<count>" . $quantity_product . "</count>\n";

                $strTmpOff .= getDimensionsAndWeight($arAcc['ID']);

                $strTmpOff .= "<price>" . preg_replace('~\s+~', '', CCurrencyLang::CurrencyFormat($minPrice, $RUR, false)) . "</price>\n";

                $oldPrice = ((isset($arAcc["~PROPERTY_OLD_PRICE_VALUE"]) && !empty($arAcc["~PROPERTY_OLD_PRICE_VALUE"])) ? $arAcc["~PROPERTY_OLD_PRICE_VALUE"] : '');

                if (!empty($oldPrice) && $oldPrice > $minPrice) {
                    $strTmpOff .= "<oldprice>" . yandex_text2xml($oldPrice, true, false, true) . "</oldprice>\n";
                };

                $manufacturer = ((isset($arAcc["~PROPERTY_MANUFACTURER_DETAIL_VALUE"]) && !empty($arAcc["~PROPERTY_MANUFACTURER_DETAIL_VALUE"])) ? $arAcc["~PROPERTY_MANUFACTURER_DETAIL_VALUE"] : '');

                $manreplaces = [];
                $rpdb = CIBlockElement::GetProperty($IBLOCK_ID,$arAcc['ID'],[],['CODE' => 'MANUFACTURER_REPLACE']);

                if ($rpdb) {
                    while ($apdb = $rpdb->GetNext()) {
                        $current = explode(':',$apdb['VALUE']);
                        $current = array_map('trim',$current);

                        if (isset($current[2]) && $current[2] == $PROFILE_ID) {
                            $manreplaces[$current[0]] = $current[1];
                        }

                    }
                }

                $manufacturer = isset($manreplaces[$manufacturer]) ? $manreplaces[$manufacturer] : $manufacturer;

                if (!empty($manufacturer) && mb_stripos($strTmpOff, '<vendor>') === false) {

                    $strTmpOff .= "<vendor>" . yandex_text2xml($manufacturer, true, false, false) . "</vendor>\n";

                    if (mb_strlen($manufacturer) > 70) {
                        $manufacturer = substr_by_word($manufacturer, 70);
                    };


                };

                $artnumber = ((isset($arAcc["~PROPERTY_ARTNUMBER_VALUE"]) && !empty($arAcc["~PROPERTY_ARTNUMBER_VALUE"])) ? $arAcc["~PROPERTY_ARTNUMBER_VALUE"] : '');

                if (!empty($artnumber)) {

                    $strTmpOff .= "<vendorCode>" . yandex_text2xml($artnumber, true, false, true) . "</vendorCode>\n";

                    if (mb_strlen($artnumber) > 70) {
                        $artnumber = substr_by_word($artnumber, 70);
                    };

                };

                $strTmpOff .= "<currencyId>" . $RUR . "</currencyId>\n";
                $strTmpOff .= $strTmpOff_tmp;

                $arAcc["DETAIL_PICTURE"] = (int)$arAcc["DETAIL_PICTURE"];
                $arAcc["PREVIEW_PICTURE"] = (int)$arAcc["PREVIEW_PICTURE"];

                //$morePhoto = ((isset($arAcc["PROPERTY_MORE_PHOTO_VALUE"]) && !empty($arAcc["PROPERTY_MORE_PHOTO_VALUE"])) ? $arAcc["PROPERTY_MORE_PHOTO_VALUE"] : array());
                //$morePhoto = !is_array($morePhoto) && !empty($morePhoto) ? array((int)$morePhoto) : $morePhoto;

                $morePhoto = array();
                $arMoreProductsFilter = array("CODE" => "MORE_PHOTO");
                $resMoreProductsDB = impelCIBlockElement::GetProperty($arAcc['IBLOCK_ID'], $arAcc['ID'], array(), $arMoreProductsFilter);

                if ($resMoreProductsDB) {

                    while ($productsMoreFields = $resMoreProductsDB->GetNext()) {

                        if (isset($productsMoreFields['VALUE'])
                            && !empty($productsMoreFields['VALUE'])
                        ) {

                            $morePhoto[] = $productsMoreFields['VALUE'];

                        }

                    }

                }


                //if ($arAcc["DETAIL_PICTURE"] > 0 || $arAcc["PREVIEW_PICTURE"] > 0) {
                //$pictNo = ($arAcc["PREVIEW_PICTURE"] > 0 ? $arAcc["PREVIEW_PICTURE"] : $arAcc["DETAIL_PICTURE"]);
                //array_unshift($morePhoto,$pictNo);
                //}

                if (!empty($morePhoto)) {

                    foreach ($morePhoto as $pictNo) {

                        if ($ar_file = convert_file_path($pictNo)) {

                            if (mb_substr($ar_file["SRC"], 0, 1) == "/")
                                $strFile = $protocol . $ar_iblock['SERVER_NAME'] . implode("/", array_map("rawurlencode", explode("/", $ar_file["SRC"])));
                            elseif (preg_match("/^(http|https):\\/\\/(.*?)\\/(.*)\$/", $ar_file["SRC"], $match))
                                $strFile = $protocol . $match[2] . '/' . implode("/", array_map("rawurlencode", explode("/", $match[3])));
                            else
                                $strFile = $ar_file["SRC"];

                            if (preg_match('~' . $protocol . $ar_iblock['SERVER_NAME'] . '~is', $strFile)) {
                                $strFile = preg_replace('~' . $protocol . $ar_iblock['SERVER_NAME'] . '~is', '', $strFile);

                                if (!empty($strFile) && function_exists('rectangleImage')) {

                                    //$strFile = rectangleImage($_SERVER['DOCUMENT_ROOT'] . $strFile, $product_image_width, $product_image_height, $strFile, "",true,true);

                                };

                                if (mb_substr($strFile, 0, 1) == "/") {
                                    $strFile = $protocol . $ar_iblock['SERVER_NAME'] . $strFile;
                                };
                            };

                            $strTmpOff .= "<picture>" . $strFile . "</picture>\n";

                        }


                    };

                } else {
                    continue;
                }

                $y = 0;
                $strReplace = '';

                foreach ($arYandexFields as $key) {
                    switch ($key) {
                        case 'name':

                            $title = ((isset($arAcc["~PROPERTY_MARKET_NAME_VALUE"]) && !empty($arAcc["~PROPERTY_MARKET_NAME_VALUE"])) ? $arAcc["~PROPERTY_MARKET_NAME_VALUE"] : $arAcc["~NAME"]);

                            if (mb_strlen($title) > 150) {
                                $title = substr_by_word($title, 150);
                            };

                            if (is_array($XML_DATA) && ($XML_DATA['TYPE'] == 'vendor.model' || $XML_DATA['TYPE'] == 'artist.title'))
                                continue;

                            $arAcc["~NAME"] = preg_replace('~[&#]+?[^;]+;~isu','',$arAcc["~NAME"]);
                            $arAcc["~NAME"] = preg_replace('~[\s\t\n\r\b]+~isu',' ',$arAcc["~NAME"]);
                            $arAcc["~NAME"] = preg_replace('~\s+?\s+~isu',' ',$arAcc["~NAME"]);

                            $strTmpOff .= "<name>" . yandex_text2xml($arAcc["~NAME"], true) . "</name>\n";
                            break;
                        case 'description':

                            $DETAIL_TEXT = !empty($arAcc["DETAIL_TEXT"]) ? $arAcc["DETAIL_TEXT"] : $arAcc["PREVIEW_TEXT"];

                            $DETAIL_TEXT =
                                ($arAcc["DETAIL_TEXT_TYPE"] == "html" ?
                                    strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $DETAIL_TEXT))
                                    : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $DETAIL_TEXT));

                            $DETAIL_TEXT = yandex_text2xml(
                                mb_strlen($DETAIL_TEXT) > 3000
                                    ? substr_by_word($DETAIL_TEXT, 3000)
                                    : $DETAIL_TEXT, true);

                            $DETAIL_TEXT = '<p>' . $DETAIL_TEXT . '</p>';

                            if (!(isset($arAcc["PROPERTY_HIDE_MODELS_VALUE"])
                                && $arAcc["PROPERTY_HIDE_MODELS_VALUE"] == 'Скрыть')) {

                                $eltMhId = $arAcc["PROPERTY_MODEL_HTML_VALUE"];

                                if ($eltMhId) {
                                    $dbMhElt = CIBlockElement::GetByID($eltMhId);

                                    if ($dbMhElt
                                        && $arMhElt = $dbMhElt->Fetch()) {

                                        if (isset($arMhElt['DETAIL_TEXT'])
                                            && !empty($arMhElt['DETAIL_TEXT'])) {

                                            $arMhElt['DETAIL_TEXT'] = str_ireplace('</a>', '</a>, ', $arMhElt['DETAIL_TEXT']);
                                            $arMhElt['DETAIL_TEXT'] = str_ireplace("\n", '', $arMhElt['DETAIL_TEXT']);
                                            $arMhElt['DETAIL_TEXT'] = trim($arMhElt['DETAIL_TEXT']);
                                            $arMhElt['DETAIL_TEXT'] = trim($arMhElt['DETAIL_TEXT'], ",");

                                            $arMhElt['DETAIL_TEXT'] = ($arMhElt["DETAIL_TEXT_TYPE"] == "html" ?
                                                strip_tags(preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arMhElt['DETAIL_TEXT']))
                                                : preg_replace_callback("'&[^;]*;'", "yandex_replace_special", $arMhElt['DETAIL_TEXT']));

                                            $DETAIL_TEXT .= '<!--replace--><p>Подходит к моделям: ' . "<br /><br />" . $arMhElt['DETAIL_TEXT'] . '</p>';

                                        }

                                    }

                                }

                            }

                            break;
                        case 'param':

                            if (is_array($XML_DATA) && is_array($XML_DATA['XML_DATA']) && is_array($XML_DATA['XML_DATA']['PARAMS'])) {
                                foreach ($XML_DATA['XML_DATA']['PARAMS'] as $key => $prop_id) {

                                    $strParamValue = '';
                                    if ($prop_id) {
                                        $strParamValue = yandex_get_value($arAcc, 'PARAM_' . $key, $prop_id, $arProperties, $arUserTypeFormat, true, true);

                                    }

                                    if ('' != $strParamValue)
                                        $strReplace .= ($strReplace ? ', ' : '') . $strParamValue;
                                }
                            }
                            break;
                        case 'model':
                        case 'title':
                            if (!is_array($XML_DATA) || !is_array($XML_DATA['XML_DATA']) || !$XML_DATA['XML_DATA'][$key]) {
                                if (
                                    $key == 'model' && $XML_DATA['TYPE'] == 'vendor.model'
                                    ||
                                    $key == 'title' && $XML_DATA['TYPE'] == 'artist.title'
                                )

                                    $strTmpOff .= "<" . $key . ">" . yandex_text2xml($arAcc["~NAME"], true) . "</" . $key . ">\n";
                            } else {
                                $strValue = '';
                                $strValue = yandex_get_value($arAcc, $key, $XML_DATA['XML_DATA'][$key], $arProperties, $arUserTypeFormat);
                                if ('' != $strValue)
                                    $strTmpOff .= $strValue . "\n";
                            }
                            break;
                        case 'year':
                            $y++;
                            if ($XML_DATA['TYPE'] == 'artist.title') {
                                if ($y == 1) continue;
                            } else {
                                if ($y > 1) continue;
                            }

                        // no break here

                        default:

                            if ($key == 'vendor' && !empty($manufacturer))
                                break;

                            if (is_array($XML_DATA) && is_array($XML_DATA['XML_DATA']) && $XML_DATA['XML_DATA'][$key]) {
                                $strValue = '';
                                $strValue = yandex_get_value($arAcc, $key, $XML_DATA['XML_DATA'][$key], $arProperties, $arUserTypeFormat);
                                if ('' != $strValue)
                                    $strTmpOff .= $strValue . "\n";


                            }


                    }

                }

                if (!empty($strReplace)) {
                    $DETAIL_TEXT = str_ireplace('<!--replace-->', '<p>' . $strReplace . '</p>', $DETAIL_TEXT);
                }

                $strTmpOff .=
                    "<description><![CDATA[" . $DETAIL_TEXT . "]]></description>\n";

                $strTmpOff .= "<pickup>true</pickup>\n";
                $strTmpOff .= "<delivery>true</delivery>\n";
                $strTmpOff .= "<store>true</store>\n";
                $strTmpOff .= "</offer>\n";

                @fwrite($fp, $strTmpOff);

                $cnt++;

                if (100 <= $cnt) {

                    $cnt = 0;
                    CCatalogDiscount::ClearDiscountCache(array(
                        'PRODUCT' => true,
                        'SECTIONS' => true,
                        'PROPERTIES' => true
                    ));


                }

            }

        }

    }

    @fwrite($fp, "</offers>\n");

    @fwrite($fp, "</shop>\n");
    @fwrite($fp, "</yml_catalog>\n");

    @fclose($fp);

    if (file_exists($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME)) {
        unlink($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME);
    }

    file_put_contents($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME,
        '<?php 
$sUri = "https://youtwig.ru/bitrix/catalog_export/".pathinfo(__FILE__,PATHINFO_FILENAME).".xml";
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$sUri);
header("Connection: close"); ?>');

    $rename = rename($_SERVER["DOCUMENT_ROOT"] . $tDir,str_ireplace('.php','.xml',$_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME));

    if (file_exists($_SERVER["DOCUMENT_ROOT"] . $tDir)) {
        //unlink($_SERVER["DOCUMENT_ROOT"] . $tDir);
    }

}

CCatalogDiscountSave::Enable();

if (!empty($arRunErrors))
    $strExportErrorMessage = implode('<br />', $arRunErrors);

if ($bTmpUserCreated) {
    unset($USER);
    if (isset($USER_TMP)) {
        $USER = $USER_TMP;
        unset($USER_TMP);
    }
}