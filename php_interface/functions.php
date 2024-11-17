<?php
if (!defined('CATALOG_INCLUDED')) die();

function getCanonicalUrl() {
    global $APPLICATION;

    $requestUri = $APPLICATION->GetCurUri();

  // Правильное регулярное выражение
    if (preg_match('~^/model/.*/\d+(/.*)?$~', $requestUri)) {
        return null; // Не выводим каноническую ссылку
    }

    $canonicalUri = preg_replace('~\?.*?$~', '', $requestUri);
    return $canonicalUri;
}

function get_quantity_product_w($product_id, $warehouse = [7])
{

    $quantity = 0;

    if (IBLOCK_INCLUDED
        && SALE_INCLUDED
        && CATALOG_INCLUDED) {

        $buy_id = getBondsProduct($product_id);

        $rsStore = CCatalogStoreProduct::GetList(
            array(),
            array('PRODUCT_ID' => $buy_id, "STORE_ID" => $warehouse),
            false,
            false
        );

        if ($rsStore) {

            while ($arStore = $rsStore->Fetch()) {

                $amount = (float)$arStore['AMOUNT'];
                $quantity += $amount;

            }

        }


    }

    return (int)$quantity;
}

if (!function_exists('twigFilters')) {

    function twigFilters($artCurrentResult, $key, $val)
    {

        foreach ($artCurrentResult as $tkey => $artItem) {

            foreach ($artItem["VALUES"] as $tval => $tar) {

                if ($tkey == $key
                    && $tval == $val) {

                    $artCurrentResult[$tkey]["VALUES"][$tval]["CHECKED"] = true;

                } else {

                    $artCurrentResult[$tkey]["VALUES"][$tval]["CHECKED"] = false;

                }

            }

        }

        return $artCurrentResult;

    }

}

if (!function_exists('twigmFilters')) {

    function twigmFilters($artCurrentResult, $key, $val, $aChecked, $bDisabled = false)
    {

        global $USER;

        $arCorrect = $artCurrentResult;

        foreach ($arCorrect as $tkey => $artItem) {

            foreach ($artItem["VALUES"] as $tval => $tar) {

                if (
                    (($tkey == $key
                            && $tval == $val
                        ) ||
                        ($tkey != $key
                            && isset($aChecked[$tkey])
                            && isset($aChecked[$tkey][$tval])
                            && $aChecked[$tkey][$tval]
                            && !$bDisabled
                        ))) {

                    if ($tkey == $key
                        && $tval == $val
                        && isset($aChecked[$tkey])
                        && isset($aChecked[$tkey][$tval])
                        && $aChecked[$tkey][$tval]
                    ) {
                        $arCorrect[$tkey]["VALUES"][$tval]["CHECKED"] = false;
                    } else {
                        $arCorrect[$tkey]["VALUES"][$tval]["CHECKED"] = true;
                    }

                } else {

                    $arCorrect[$tkey]["VALUES"][$tval]["CHECKED"] = false;

                }

            }

        }

        return $arCorrect;
    }

}

function viewModelComments($sTypeOfProduct, $sManufacturer, $aProducts)
{

    $sTypeOfProduct = trim($sTypeOfProduct);
    $sManufacturer = trim($sManufacturer);

    if (checkQuantityRigths()
        && (!empty($sTypeOfProduct) || !empty($sManufacturer) || !empty($aProducts))) {


        $aProducts = !is_array($aProducts) ? array($aProducts) : $aProducts;

        $aProducts = array_map('trim', $aProducts);
        $aProducts = array_map('intval', $aProducts);
        $aProducts = array_map('abs', $aProducts);
        $aProducts = array_unique($aProducts);
        if(is_array($aProducts)){
            $aProducts = array_filter($aProducts);
        }

        $apTypes = array();

        foreach ($aProducts as $iProductId) {

            $rProp = CIBlockElement::GetProperty(
                11,
                $iProductId,
                array("sort" => "asc"),
                array("CODE" => "TYPEPRODUCT")
            );

            if ($aProp = $rProp->Fetch()) {

                if (isset($aProp['VALUE_ENUM'])
                    && !empty($aProp['VALUE_ENUM'])) {

                    $apTypes[$aProp['VALUE_ENUM']] = trim($aProp['VALUE_ENUM']);

                }

            }

        };

        $aOrder = array("SORT" => "ASC");

        if (!empty($sTypeOfProduct)) {

            $aFilter[] = array(
                'LOGIC' => 'OR',
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_type_of_product_VALUE' => $sTypeOfProduct),
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_type_of_product_VALUE' => false)
            );

        }

        if (!empty($sManufacturer)) {
            $aFilter[] = array(
                'LOGIC' => 'OR',
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_manufacturer_VALUE' => $sManufacturer),
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_manufacturer_VALUE' => false)
            );

        }

        if (!empty($apTypes)) {

            $aFilter[] = array(
                'LOGIC' => 'OR',
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_TYPEPRODUCT_VALUE' => $apTypes),
                array("ACTIVE" => "Y", "IBLOCK_ID" => 38, 'PROPERTY_TYPEPRODUCT_VALUE' => false)
            );
        }

        $aFilter["IBLOCK_ID"] = 38;
        $aFilter["ACTIVE"] = "Y";

        $aSelectFields = array('PREVIEW_TEXT');

        $rDb = \CIBlockElement::GetList(
            $aOrder,
            $aFilter,
            false,
            false,
            $aSelectFields
        );

        if ($rDb) {

            while ($aDb = $rDb->GetNext()) {
                $spText = trim(html_entity_decode($aDb['PREVIEW_TEXT'], ENT_QUOTES, LANG_CHARSET));
                if ($spText != "") {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade in"
                         role="alert"><?php echo $spText; ?></div><?php
                }
            }

        }

    }
}


function replace_pagenav($url)
{

    $removeParams = 'PAGEN,CLEAR_CACHE,SECTION_CODE,ELEMENT_CODE,SECTION_CODE_PATH,CODE,BACKURL,BXAJAXID,SET_FILTER,ACTION,BXRAND,SECTION_ID,ARRFILTER_,AJAX,RESETFILTER,LID';

    if (mb_stripos($removeParams, ',') !== false) {
        $removeParams = explode(',', $removeParams);
    } else {
        $removeParams = array($removeParams);
    }

    $pageNum = '';
    $pageVal = '';
    $arQuery = array();

    if (!empty($url)) {
        mb_parse_str(htmlspecialcharsback(preg_replace('~^.+?\?~is', '', $url)), $arQuery);
        foreach ($arQuery as $k => $v) {

            if (mb_stripos($k, 'PAGEN') !== false) {

                $pageNum = preg_replace('~PAGEN_~i', '', $k);
                $pageNum = $pageNum == 1 ? '' : (int)trim($pageNum);
                $pageVal = $v;

            }

            foreach ($removeParams as $deleteParam) {
                if (mb_stripos($k, $deleteParam) !== false) {
                    unset($arQuery[$k]);
                }
            }

            if ((mb_stripos(htmlspecialcharsback($k), '/') !== false)) {
                unset($arQuery[$k]);
            }
        }


    }


    if (!empty($arQuery)) {
        $strQuery = http_build_query($arQuery, '', '&');
    } else {
        $strQuery = "";
    }

    $url = preg_replace('#\?.*?$#is', '', $url);

    if ($pageVal) {
        $url = preg_replace('#/pages([\d]*?)-([\d]+)#is', '', $url);

        if ($pageVal > 1)
            $url = rtrim($url, '/') . '/pages' . $pageNum . '-' . $pageVal . '/';
    }

    if (!empty($strQuery))
        $url .= '?' . $strQuery;

    return $url;

}


function getMobileAlterate()
{
    global $APPLICATION;

    $mobile_alterate = $APPLICATION->GetProperty('MOBILE_ALTERATE');

    if (empty($mobile_alterate)) {

        $host = preg_replace('~^m\.~isu', '', IMPEL_SERVER_NAME);
        $mobile_alterate = (CMain::IsHTTPS() ? 'https' : 'http') . '://m.' . $host . $_SERVER['REQUEST_URI'];
    }

    $mobile_alterate = replace_pagenav($mobile_alterate);

    return $mobile_alterate;
}

function getMobileCanonical()
{
    global $APPLICATION;

    $mobile_canonical = $APPLICATION->GetProperty('MOBILE_CANONICAL');

    if (empty($mobile_canonical)) {
        $host = preg_replace('~^m\.~isu', '', IMPEL_SERVER_NAME);
        $mobile_canonical = (CMain::IsHTTPS() ? 'https' : 'http') . '://' . $host . $_SERVER['REQUEST_URI'];
    }

    $mobile_canonical = replace_pagenav($mobile_canonical);

    return $mobile_canonical;
}

function seoTitleAtTop()
{
    global $APPLICATION;

    $seo_text = $APPLICATION->GetProperty('SEO_TITLE_H1');
    // var_dump("functioni => {$seo_text}");
    $seo_text = trim($seo_text);
    return $seo_text;
}

function seoTextAtTop()
{
    global $APPLICATION;

    $seo_text = $APPLICATION->GetProperty('SEO_TEXT');
    $seo_text = trim($seo_text);
    return $seo_text;
}

function rightSideBreadcrumb()
{
    global $APPLICATION;

    $right_side_breadcrumb = $APPLICATION->GetProperty('RIGHT_SIDE_BREADCRUMB');
    return $right_side_breadcrumb;
}

function GetOgUrl()
{

    global $APPLICATION;

    $canonical = "";

    if ($APPLICATION->GetProperty('canonical') != "") {

        $canonical = $APPLICATION->GetProperty('canonical');

    };

    if (empty($canonical)) {

        $canonical = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $_SERVER['REQUEST_URI'];
        $canonical = preg_replace('~\?.*?$~isu', '', $canonical);

    };

    return $canonical;
}

function GetOgImage()
{

    global $APPLICATION;
    $image = "";

    if ($APPLICATION->GetProperty('ogimage') != "") {

        $image = $APPLICATION->GetProperty('ogimage');

    };

    if (empty($image)) {
        $image = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/templates/nmain/images/logo.png';
    };

    return $image;
}

function GetOgDescription()
{
    global $APPLICATION;

    $description = "";

    if ($APPLICATION->GetProperty('DESCRIPTION') != "") {

        $description = $APPLICATION->GetProperty('DESCRIPTION');

    };

    return $description;
}

AddEventHandler("main", "OnAfterEpilog", "OnAfterEpilogHandler");

function OnAfterEpilogHandler()
{
    global $APPLICATION;

    if ($APPLICATION->GetCurPage() == '/bitrix/admin/iblock_data_export.php'
        && $_SERVER["REQUEST_METHOD"] == "POST"
        && isset($_REQUEST['STEP'])
        && $_REQUEST['STEP'] == 3
        && isset($_REQUEST['IBLOCK_ID'])
        && $_REQUEST['IBLOCK_ID'] == 17
        && isset($_REQUEST['DATA_FILE_NAME'])
        && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $_REQUEST['DATA_FILE_NAME'])
        && isset($_REQUEST['delimiter_r'])
        && !empty($_REQUEST['delimiter_r'])
    ) {

        $csv_content = file($_SERVER['DOCUMENT_ROOT'] . '/' . $_REQUEST['DATA_FILE_NAME']);

        $delimiter_r = trim($_REQUEST['delimiter_r']);

        if (!empty($csv_content) && is_array($csv_content)) {

            $delimiter_r_char = "";
            switch ($delimiter_r) {
                case "TAB":
                    $delimiter_r_char = "\t";
                    break;
                case "ZPT":
                    $delimiter_r_char = ",";
                    break;
                case "SPS":
                    $delimiter_r_char = " ";
                    break;
                case "OTR":
                    $delimiter_r_char = mb_substr($delimiter_other_r, 0, 1);
                    break;
                case "TZP":
                    $delimiter_r_char = ";";
                    break;
            }

            if (!empty($delimiter_r_char)) {

                //IP_PROP70
                $headers = str_getcsv($csv_content[0], $delimiter_r_char);


                $number_key = array_search('IP_PROP70', $headers);

                if ($number_key !== false) {

                    $csvfp = fopen($_SERVER['DOCUMENT_ROOT'] . '/' . $_REQUEST['DATA_FILE_NAME'], 'wb');

                    if ($csvfp) {

                        $headers[] = 'IP_PROP_TYPEPRODUCT';

                        fputcsv($csvfp, $headers, $delimiter_r_char);

                        for ($string_number = 1; $string_number < sizeof($csv_content); $string_number++) {

                            $current_array = str_getcsv($csv_content[$string_number], $delimiter_r_char);
                            $typeofproduct_number = sizeof($current_array);
                            $current_array[$typeofproduct_number] = '';

                            if (isset($current_array[$number_key])
                                && is_numeric($current_array[$number_key])) {

                                $iblockRes = CIBlockElement::GetByID($current_array[$number_key]);

                                if ($ariblock_res = $iblockRes->GetNext()) {

                                    if (isset($ariblock_res['NAME'])) {

                                        $current_array[$number_key] = $ariblock_res['NAME'];
                                        $dbProperty_props = CIBlockElement::GetProperty($ariblock_res['IBLOCK_ID'], $ariblock_res['ID'], array("sort" => "asc"), array("CODE" => "TYPEPRODUCT"));

                                        if ($arProperty_props = $dbProperty_props->Fetch()) {

                                            if (isset($arProperty_props['VALUE_ENUM'])
                                                && !empty($arProperty_props['VALUE_ENUM'])) {

                                                $current_array[$typeofproduct_number] = trim($arProperty_props['VALUE_ENUM']);

                                            }

                                        }

                                    }

                                }

                            }

                            fputcsv($csvfp, $current_array, $delimiter_r_char);

                        }

                        fclose($csvfp);

                    }

                }

            }

        }

    }


}

AddEventHandler("main", "OnBeforeUserAdd", "check_recaptcha");

function check_recaptcha(&$arFields)
{

    global $APPLICATION;
    $return = true;

    if ($APPLICATION->GetCurPage() == '/registration/') {

        $return = getRecaptchaResponse();

        if ($return) {

            if (isset($arFields['LOGIN']) && !empty($arFields['LOGIN'])
                && isset($arFields['PASSWORD']) && !empty($arFields['PASSWORD'])) {
                $_SESSION['REGISTER_LOGIN'] = $arFields['LOGIN'];
                $_SESSION['REGISTER_PASSWORD'] = $arFields['PASSWORD'];
            }

        }

    } else {

        if (isset($arFields['LOGIN']) && !empty($arFields['LOGIN'])
            && isset($arFields['PASSWORD']) && !empty($arFields['PASSWORD'])) {
            $_SESSION['REGISTER_LOGIN'] = $arFields['LOGIN'];
            $_SESSION['REGISTER_PASSWORD'] = $arFields['PASSWORD'];
        }
    }

    return $return;

}

function getRecaptchaResponse()
{

    $isVerified = 0;
    $response = isset($_REQUEST['g-recaptcha-response']) && !empty($_REQUEST['g-recaptcha-response']) ? trim($_REQUEST['g-recaptcha-response']) : '';
    $return = true;

    if (!empty($response)) {
        $secret = '6LeFVAcTAAAAAKW5xx8kC5tGtApeYJwSF18_A44t';
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($ip)) {
            $uri = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $response . '&remoteip=' . $ip;
            $content = getURIContent($uri);

            if (!empty($content)) {
                $content = json_decode($content);

                if (is_object($content)
                    && isset($content->success)
                    && $content->success
                ) {
                    $isVerified = 1;
                }

            }

        }

    }

    if (!$isVerified) {
        global $APPLICATION;
        $APPLICATION->throwException("Вы не верно указали проверочный код.");
        $return = false;
    }

    return $return;
}

function getURIContent($url, $method = "get", $data = "", $coockie_file = "", $http_code = false)
{

    $tuCurl = curl_init();
    $tuData = '';

    if ($tuCurl && is_resource($tuCurl)) {


        switch ($method) {

            case 'post':

                $opts = array(CURLOPT_URL => $url,
                    CURLOPT_POST => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_AUTOREFERER => 1,
                    CURLOPT_CONNECTTIMEOUT => 12,

                );

                break;
            case 'file':

                $opts = array(CURLOPT_URL => $url,
                    CURLOPT_HTTPGET => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_AUTOREFERER => 1,
                    CURLOPT_CONNECTTIMEOUT => 60,
                    CURLOPT_FILE => $data,

                );


                break;
            case 'get':
            default:

                $opts = array(CURLOPT_URL => $url,
                    CURLOPT_HTTPGET => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_AUTOREFERER => 1,
                    CURLOPT_CONNECTTIMEOUT => 12,
                );

                break;


        }


    };


    foreach ($opts as $key => $value) {
        curl_setopt($tuCurl, $key, $value);
    }

    $tuData = curl_exec($tuCurl);

    if ($http_code) {
        $tuData = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
    }

    curl_close($tuCurl);

    if ($data && is_resource($data)) {
        fclose($data);
    }

    return $tuData;

}

AddEventHandler('ipol.sdek', 'onCompabilityBefore', 'onCompabilityBeforeSDEK');
function onCompabilityBeforeSDEK($order, $conf, $keys)
{
    if ($order['LOCATION_TO'] == '20') {
        return false;//полностью исключаем город
    }
    return true;
}

function get_quantity_product_provider($product_id, $is_checked = false)
{

    $quantity = 0;

    if (IBLOCK_INCLUDED
        && SALE_INCLUDED
        && CATALOG_INCLUDED) {

        $buy_id = getBondsProduct($product_id);

        $rsStore = CCatalogStoreProduct::GetList(
            array(),
            array('PRODUCT_ID' => $buy_id, "STORE_ID" => 9, 10),
            false,
            false
        );

        if ($rsStore) {

            while ($arStore = $rsStore->Fetch()) {
                $amount = (float)$arStore['AMOUNT'];
                $quantity += $amount;

            }

        }


    }

    return (int)$quantity;
}

function get_quantity_product($product_id, $is_checked = false, $also_remove = array(3, 6, 10))
{

    $quantity = 0;

    if (IBLOCK_INCLUDED
        && SALE_INCLUDED
        && CATALOG_INCLUDED) {

        $buy_id = getBondsProduct($product_id);

        $rsStore = CCatalogStoreProduct::GetList(
            array(),
            array('PRODUCT_ID' => $buy_id, "!STORE_ID" => $also_remove),
            false,
            false
        );

        if ($rsStore) {

            while ($arStore = $rsStore->Fetch()) {

                $amount = (float)$arStore['AMOUNT'];
                $quantity += $amount;

            }

        }


    }

    return (int)$quantity;
}

function checkQuantityRigths()
{

    global $USER;

    $diff = array();

    if ($USER->IsAuthorized()) {

        $admin_groups = array(1, 7, 6);
        $Usr = $USER->GetByID($USER->GetID());
        $CUser = $Usr->Fetch();
        $arGroups = CUser::GetUserGroup($USER->GetID());
        $diff = array_diff($admin_groups, $arGroups);

        return sizeof($diff) == sizeof($admin_groups) ? false : true;

    } else {
        return false;
    }

}

AddEventHandler("catalog", "OnStoreProductUpdate", "updateProductProperty");
AddEventHandler("catalog", "OnStoreProductAdd", "updateProductProperty");

function updateProductProperty($ID, $arFields)
{

    if (IBLOCK_INCLUDED && CATALOG_INCLUDED) {

        $arSelect = array("IBLOCK_ID", "CATALOG_QUANTITY");
        $arFilter = array("ID" => $arFields['PRODUCT_ID']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $product = array();

        while ($res
            && ($product = $res->Fetch())) {
            if (isset($product['IBLOCK_ID'])
                && !empty($product['IBLOCK_ID'])
            ) {
                switch ($product['IBLOCK_ID']) {
                    case 11:

                        $arSelect = array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                        $arFilter = array("IBLOCK_ID" => $product['IBLOCK_ID'], "ID" => $arFields['PRODUCT_ID']);
                        $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                        $product = array();

                        while ($res
                            && ($product = $res->Fetch())) {

                            $product_id = $product['ID'];
                            $iblock_id = $product['IBLOCK_ID'];

                            $PROPERTY_CODE = "QUANTITY";
                            $PROPERTY_VALUE = $arFields['AMOUNT'];

                            CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                            if ($iblock_id == 11)
                                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                        }

                        break;
                    case 16:

                        $arSelect = array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                        $arFilter = array("IBLOCK_ID" => 11, "PROPERTY_MAIN_PRODUCTS" => $arFields['PRODUCT_ID']);
                        $quantity = $product['CATALOG_QUANTITY'];
                        $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

                        $product_id = $arFields['PRODUCT_ID'];
                        $iblock_id = $product['IBLOCK_ID'];
                        $PROPERTY_CODE = "QUANTITY";
                        $PROPERTY_VALUE = $arFields['AMOUNT'];
                        CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                        if ($iblock_id == 11)
                            \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);


                        $product = array();

                        while ($res
                            && ($product = $res->Fetch())) {

                            $product_id = $product['ID'];
                            $iblock_id = $product['IBLOCK_ID'];
                            $PROPERTY_CODE = "QUANTITY";
                            $PROPERTY_VALUE = $arFields['AMOUNT'];

                            CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);
                            if ($iblock_id == 11)
                                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);
                        }

                        break;
                }

            }
        }
    }

}

function correctCurrencyRate($product_id, &$curFromRate, &$curFromRateCnt, &$curToRate, &$curToRateCnt, $curFrom, $curTo)
{

    if (!empty($product_id)) {

        $rs = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $product_id
            ),
            false,
            false,
            array("ID", "IBLOCK_ID")
        );

        if ($rs && is_object($rs) && method_exists($rs, 'GetNext')) {

            while ($ar = $rs->GetNext()) {
                if (isset($ar["ID"]) && $ar["ID"] == $product_id
                    && isset($ar["IBLOCK_ID"]) && !empty($ar["IBLOCK_ID"])) {

                    $res = CIBlock::GetProperties(
                        $ar["IBLOCK_ID"],
                        array(),
                        array("CODE" => "convertCourse")
                    );

                    if ($res && is_object($res) && method_exists($res, 'GetNext')) {
                        if ($res_arr = $res->Fetch()) {
                            if (isset($res_arr["DEFAULT_VALUE"]) && $res_arr["DEFAULT_VALUE"] != "") {

                                $values = array();

                                $values = explode(" ", $res_arr["DEFAULT_VALUE"]);
                                $values[0] = (float)$values[0];

                                if (is_numeric($values[0])
                                    && mb_strtolower($values[1]) == mb_strtolower($curTo)
                                    && $curFromRate != 1) {
                                    $curFromRate += ((double)$values[0] * $curFromRateCnt);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function getBondsProduct($product_id, $only_new = false)
{

    $bonds_products = cacheBondsProducts();

    if (isset($bonds_products[$product_id])
        && !$only_new) {

        $is_main = $bonds_products[$product_id];
        $is_found = !empty($is_main) ? true : false;

    } else {

        $is_main = false;
        $is_found = false;

        $res = CIBlockElement::GetByID($product_id);
        if (is_object($res) && method_exists($res, 'GetNext')) {
            if ($ar_res = $res->GetNext()) {
                $iblock_id = $ar_res['IBLOCK_ID'];
                $db_props = CIBlockElement::GetProperty($iblock_id, $product_id, array("sort" => "asc"), array("CODE" => "MAIN_PRODUCTS"));

                if (is_object($db_props) && method_exists($db_props, 'Fetch')) {
                    while ($ar_props = $db_props->Fetch()) {

                        $is_main = $ar_props["VALUE"];

                        if (!empty($is_main)) {

                            $res = CIBlockElement::GetByID($is_main);


                            if (is_object($res) && method_exists($res, 'GetNext')) {
                                if ($ar_res = $res->GetNext()) {
                                    $is_found = true;
                                };
                            };
                        };

                    };
                };
            };
        }

    }

    return ($is_found && $is_main) ? $is_main : $product_id;
}

function canYouBuy($product_id, $hasPrice = null, $only_new = false)
{


    if (SALE_INCLUDED) {

        if (is_null($hasPrice)) {

            $arPrice = CCatalogProduct::GetOptimalPrice($product_id, 1);
            if (!(
                isset($arPrice['PRICE'])
                && isset($arPrice['PRICE']['PRICE'])
                && !empty($arPrice['PRICE']['PRICE'])
            )
            ) {
                return false;
            }

        }

        $product_id = getBondsProduct($product_id, $only_new);
        return Add2BasketCheck($product_id);

    } else {
        return false;
    }
}

function rectangleImage($image, $width = "", $height = "", $url = "", $background_color = "", $ratio_fill = true, $disable_watermark = true, $start_path = "/upload/cache/")
{

    static $uploderIncluded = false;
    global $APPLICATION;

    $return_uri = $start_path;
    $return = $url;

    if (!$disable_watermark) {
        $watermark = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/templates/nmain/images/watermark.png";
    };


    if (!$uploderIncluded && file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/class.upload.php")) {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/class.upload.php";
        $uploderIncluded = true;
    };

    if (class_exists('Upload') && file_exists($image) && !is_writable($image)) {
        chmod($image, 0777);
    }


    $filesize = filesize($image);


    if (class_exists('Upload') && file_exists($image) && is_writable($image) && $filesize) {

        $info = array();
        $info = pathinfo($image);

        if (isset($info["extension"]) && in_array(mb_strtolower($info["extension"]), array('jpeg', 'jpg', 'gif', 'png'))) {

            $path = dirname($image);
            $image_name = false;

            $base_name = isset($info['filename']) ? $info['filename'] : str_replace('.' . $info['extension'], '', $info['basename']);
            $base_name .= '_' . $filesize;

            if (!empty($width) && !empty($height)) {
                $base_name .= '_' . $width . '_' . $height;
            } elseif (!empty($width)) {
                $base_name .= '_w' . $width;
            } elseif (!empty($height)) {
                $base_name .= '_h' . $height;
            }

            if (!empty($background_color)) {
                $base_name .= preg_replace('~[^a-z0-9\-\_]~isu', '', $background_color);
            };

            $is_wm = false;

            if (file_exists($watermark)
                && is_readable($watermark)) {
                $wSizes = getimagesize($watermark);
                if (isset($wSizes[0]) && !empty($wSizes[0])
                    && isset($wSizes[1]) && !empty($wSizes[1])
                ) {

                    $sizes = array();
                    $sizes = getimagesize($image);

                    if (isset($sizes[0]) && !empty($sizes[0])
                        && isset($sizes[1]) && !empty($sizes[1])
                    ) {
                        $base_name .= '_w';
                        $is_wm = true;
                    }

                }
            }

            if (!$is_wm) {
                $watermark = false;
            }

            $cache = $_SERVER['DOCUMENT_ROOT'] . $start_path;

            if (!is_dir($cache . '/thumbs/')) {
                mkdir($cache . '/thumbs/', 0775);
            };

            if (!is_writable($cache . '/thumbs/')) {
                chmod($cache . '/thumbs/', 0777);
            };

            if (is_dir($cache . '/thumbs/')) {
                $cache .= '/thumbs/';
                $return_uri .= 'thumbs/';
            };

            $cache_exists = true;

            if (!file_exists($cache . $base_name . '.jpg')) {
                $cache_exists = false;
            };

            $cache_imagesize = array();

            if (file_exists($cache . $base_name . '.jpg')) {
                $cache_filesieze = filesize($cache . $base_name . '.jpg');

                if (!$cache_filesieze) {
                    $cache_exists = false;
                } else {
                    $cache_imagesize = getimagesize($cache . $base_name . '.jpg');
                    if (empty($cache_imagesize[0]) || empty($cache_imagesize[1])) {
                        $cache_exists = false;
                    }
                };
            };

            if ($cache_exists) {
                $return = $return_uri . $base_name . '.jpg';

            } elseif (!$cache_exists) {

                $sizes = array();
                $sizes = getimagesize($image);

                if (isset($sizes[0]) && !empty($sizes[0])
                    && isset($sizes[1]) && !empty($sizes[1])
                ) {

                    $handle = new Upload($image);

                    $handle->image_resize = true;

                    if (!empty($width) && !empty($height) && $ratio_fill) {
                        $handle->image_ratio_fill
                            = true;
                    }

                    if (!$ratio_fill) {
                        $handle->image_ratio_no_zoom_out
                            = true;
                    }

                    if (!empty($background_color)) {
                        $handle->image_background_color = $background_color;
                    }

                    $handle->jpeg_quality = 98;
                    $handle->file_new_name_body = $base_name;

                    $handle->file_auto_rename = false;
                    $handle->file_overwrite = true;
                    $handle->image_convert = 'jpg';
                    //$handle->mime_check         = false;

                    $handle->allowed = array('image/*');


                    if (!empty($width) && !empty($height)) {
                        $handle->image_x = $width;
                        $handle->image_y = $height;
                        $handle->image_ratio = true;
                    } else if (!empty($width)) {
                        $handle->image_x = $width;
                        $handle->image_ratio_y = true;
                    } else if (!empty($height)) {
                        $handle->image_y = $height;
                        $handle->image_ratio_x = true;
                    }

                    if ($watermark
                        && file_exists($watermark)
                        && is_readable($watermark)) {
                        $handle->image_watermark = $watermark;
                        //$handle->image_watermark_position 	= "BR";
                    }

                    $handle->Process($cache);

                    //print_r($handle);

                    if ($handle->processed && file_exists($cache . $base_name . '.jpg')) {
                        $imagesize = getimagesize($cache . $base_name . '.jpg');
                        if (isset($imagesize[0]) && !empty($imagesize[0])
                            && isset($imagesize[1]) && !empty($imagesize[1])
                        ) {
                            $return = $return_uri . $base_name . '.jpg';
                        }

                    }

                }

            }

        }
    }

    return $return;
}

function ShowH1()
{
    global $APPLICATION;
    if ($APPLICATION->GetPageProperty("ADDITIONAL_H1"))
        return $APPLICATION->GetPageProperty("ADDITIONAL_H1");
    elseif ($APPLICATION->GetPageProperty("ADDITIONAL_TITLE"))
        return $APPLICATION->GetPageProperty("ADDITIONAL_TITLE");
    else
        return $APPLICATION->GetTitle(false);
}


function ShowTitleOrHeader()
{
    global $APPLICATION;
    if ($APPLICATION->GetPageProperty("ADDITIONAL_TITLE"))
        return $APPLICATION->GetPageProperty("ADDITIONAL_TITLE");
    else
        return $APPLICATION->GetTitle(false);
}

function getCurrentCurrencyCode()
{
    global $USER;

    if (CURRENCY_INCLUDED) {

        $CURRENCY_DEFAULT = CCurrency::GetBaseCurrency();


        require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/mainpage.php");
        $site_id = CMainPage::GetSiteByHost();
        $CURRENCY_CODE = COption::GetOptionString("sale", "default_currency", $CURRENCY_DEFAULT, $site_id);

        return $CURRENCY_CODE;
    }

}

AddEventHandler("main", "OnAfterEpilog", "OnAfterEpilogCode");

function OnAfterEpilogCode()
{
    //echo time();
    //print_r(\Bitrix\Main\Diag\Helper::getBackTrace());
}

AddEventHandler("main", "OnBeforeProlog", "clearCurrencyCodeCache");

function startSqlCount()
{

    if (isset($_REQUEST['nTwigCheck'])
        && class_exists('CDebugInfo')) {

        global $DB;
        $DB->ShowSqlStat = true;

        $GLOBALS['dTwigObject'] = $debug = new CDebugInfo();
        $debug->Start();

    }


}

function printSqlCount()
{

    if (isset($_REQUEST['nTwigCheck'])
        && class_exists('CDebugInfo')
        && isset($GLOBALS['dTwigObject'])
        && is_object($GLOBALS['dTwigObject'])
    ) {

        $debug = $GLOBALS['dTwigObject'];
        $debug->Stop();
        $arDebug = array();
        $arDebug['run_time'] = round($debug->arResult['TIME'], 4);
        $arDebug['sql_time'] = round($debug->arResult['QUERY_TIME'], 4);
        $arDebug['sql_count'] = $debug->arResult['QUERY_COUNT'];
        echo '<pre>';
        print_r($arDebug);
        echo '</pre>';

    }

}

function tinkoffPayCheck()
{

    $stDir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/tinkoff';

    include(GetLangFileName($stDir . '/', "/tinkoff.php"));
    include($stDir . "/sdk/tinkoff_autoload.php");

    $request = json_decode(file_get_contents("php://input"));

    $orderID = $request->OrderId;
    $order = CSaleOrder::GetByID($orderID);

    if (!$order) {
        $arFilter = array(
            "ACCOUNT_NUMBER" => $orderID,
        );
        $accountNumberList = CSaleOrder::GetList(array("ACCOUNT_NUMBER" => "ASC"), $arFilter);
        $order = $accountNumberList->arResult[0];
    }

    if ($order) {
        $orderID = $order['ID'];
    } else {
        die('NOTOK'); // ORDER NOT FOUND
    }

    CSalePaySystemAction::InitParamArrays($orderID, $orderID);
    $notificationModel = new TinkoffNotification(CSalePaySystemAction::GetParamValue("TERMINAL_ID"), CSalePaySystemAction::GetParamValue("SHOP_SECRET_WORD"));

    try {
        $notificationModel->checkNotification($request);
    } catch (TinkoffException $e) {
        die($e->getMessage());
    }

    if ($notificationModel->isOrderFailed()) {
        CSaleOrder::PayOrder($orderID, 'N');
    } elseif ($notificationModel->isOrderPaid()) {
        CSaleOrder::PayOrder($orderID, 'Y');
        //CSaleOrder::StatusOrder($orderID, "P");
    } elseif ($notificationModel->isOrderRefunded()) {
        CSaleOrder::PayOrder($orderID, 'N');
        CSaleOrder::StatusOrder($orderID, "N");
        CSaleOrder::CancelOrder($orderID, "Y", GetMessage("SALE_TINKOFF_PAYMENT_CANCELED"));
    } else {
        die('OK');
    }

    die('OK');

}

function clearCurrencyCodeCache()
{


    global $APPLICATION, $USER;

    global $argv, $argc;

    // var_dump("function => start", $argv, $argc);

    if (isset($argv[0]) && !empty($argv[0])) {

        $sPath = trim($argv[0]);
        $sPath = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $sPath);

        if ($sPath
            && (stripos($sPath, 'local/crontab') !== false)
            && (stripos($sPath, 'local/crontab/index_cmanufacturer.php') === false)
        ) {

            // var_dump("functions =>", $sPath);

            ob_start();
            passthru('ps aux | grep ' . $sPath);
            $text = ob_get_clean();
            $pCount = 0;

            $aText = explode("\n", $text);

            $sMail = '';
            $iLast = time();
            $iHour = date('H',$iLast);

            $fTmp = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/lcrontab.txt';

            if (!file_exists($fTmp)) {
                file_put_contents($fTmp,'');
            }

            $ilTime = filemtime($fTmp);

            // var_dump("functions => aText", $aText);

            foreach ($aText as $sText) {

                if (mb_stripos($sText, $sPath) !== false && mb_stripos($sText, 'bin/php') !== false) {
                    ++$pCount;
                }

                $aColumns = explode(" ",$sText);
                $aColumns = array_values(array_filter($aColumns));

                if (mb_stripos($sText,IMPEL_SERVER_NAME) !== false
                    && preg_match("~[a-z]{3}[0-9]+~isu",$aColumns[8])) {

                    $sMail .= $sText . "\n";

                }

            }

            // var_dump("functions => pCount", $pCount);

            if (!empty($sMail) && ($iHour % 3 == 0) && ($iLast > ($ilTime + 3600))) {

                file_put_contents($fTmp,'');

                CModule::IncludeModule("main");

                $event_name = 'MAIL_CRONJOB_FAIL';

                $arrSites = array();
                $objSites = CSite::GetList(($by = "sort"), ($order = "asc"));

                while ($arrSite = $objSites->Fetch()) {
                    $arrSites[] = $arrSite["ID"];
                };

                $arFields['MAIL_TEXT'] = $sMail;
                CEvent::SendImmediate($event_name, $arrSites, $arFields);

            }

            if ($pCount > 1) {
                die('Processed...');
            }

        }

    }

    if (mb_stripos($APPLICATION->GetCurPage(), '/personal/order/notification.php') === 0) {
        tinkoffPayCheck();
    }

    if (class_exists('CBXShortUri'))
        CBXShortUri::CheckUri();

    if (mb_stripos($APPLICATION->GetCurPage(), '/personal/order/detail/') === 0 && false) {
        $orderUrl = str_ireplace('/personal/order/detail/', '', $APPLICATION->GetCurPage());
        $orderId = preg_replace('~[^0-9]+~', '', $orderUrl);

        if (!empty($orderId) && SALE_INCLUDED && $USER->IsAuthorized()) {
            $arOrder = CSaleOrder::GetByID($orderId);

            if (!(isset($arOrder['USER_ID']) && $arOrder['USER_ID'] > 0 && $arOrder['USER_ID'] == $USER->GetId())) {
                LocalRedirect('/personal/order/');
            }

        }

    }

    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/robots.txt')) {
        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/robots.txt')) {
            chmod($_SERVER['DOCUMENT_ROOT'] . '/robots.txt', 0775);
        }

        @unlink($_SERVER['DOCUMENT_ROOT'] . '/robots.txt');
    }


    if (empty($_REQUEST) && empty($_GET)
        && isset($_SERVER["REQUEST_URI"])
        && !empty($_SERVER["REQUEST_URI"])) {

        if (mb_strpos($_SERVER["REQUEST_URI"], "?") !== false) {
            $curi = "";
            list($trunc, $curi) = explode("?", $_SERVER["REQUEST_URI"], 2);

            if ($curi && !empty($curi)) {
                $cArr = array();
                mb_parse_str(urldecode($curi), $cArr);
                $_REQUEST = $cArr;
                $_GET = $cArr;
            }
        }

    };

    if (\CHTTP::GetLastStatus() == '404 Not Found' && !defined('ERROR_404')) {
        define('ERROR_404', 'Y');
    };

}

AddEventHandler("main", "OnAfterUserLogin", "OnAfterUserLoginHandler");

function OnAfterUserLoginHandler()
{

    global $USER;

    $userDB = $USER->GetByID($USER->GetID());
    $userFields = $userDB->Fetch();

    $ids = isset($_COOKIE['favorites_cookies']) ? trim($_COOKIE['favorites_cookies']) : '';
    $ids = explode(',',$ids);
    $ids = array_map('trim',$ids);
    $ids = array_map('intval',$ids);
    $ids = array_filter($ids);
    $ids = array_unique($ids);

    $ufFavorites = (isset($userFields["UF_FAVORITES"]) && !empty($userFields["UF_FAVORITES"])) ? $userFields["UF_FAVORITES"] : '';
    $ufFavorites = explode(',',$ufFavorites);

    $ufFavorites = array_map('trim',$ufFavorites);
    $ufFavorites = array_map('intval',$ufFavorites);
    $ufFavorites = array_filter($ufFavorites);
    $ufFavorites = array_unique($ufFavorites);
    $ids = array_merge($ids,$ufFavorites);

    $ids = array_unique($ids);

    $strIds = join(',',$ids);

    if (!empty($strIds)) {

        $fields 		= Array(
            "UF_FAVORITES" => $strIds
        );

        $USER->Update($USER->GetID(), $fields);

        setcookie('favorites_cookies',$strIds,(time() + (86400 * 30)),"/",$_SERVER['SERVER_NAME'],true);

    }

    if (IBLOCK_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED && isset($_SESSION['FIRST_TIME']) && !empty($_SESSION['FIRST_TIME'])) {
        $ID = (int)$_SESSION['FIRST_TIME'];

        if ($ID) {
            $_SESSION['IN_FIRST_TIME'] = true;
            Add2BasketByProductID(
                $ID,
                1,
                array(),
                array()
            );


        };

        unset($_SESSION['FIRST_TIME']);

    };
}

;

AddEventHandler("main", "OnAfterUserAuthorize", "OnAfterUserAuthorizeHandler");

function OnAfterUserAuthorizeHandler($user_fields)
{
    global $APPLICATION, $USER;

    if (isset($_REQUEST['SMS_INFO'])) {

        if ($USER->IsAuthorized()) {
            $fields = array(
                "UF_SMS_INFORM" => (!empty($_REQUEST['SMS_INFO']) ? 1 : 0),
            );

            $USER->Update($USER->GetID(), $fields);
        };
    };
}

;

AddEventHandler("main", "OnAfterUserAdd", "check_registration_type");

function check_registration_type(&$arFields)
{

    if (isset($_REQUEST['UF_TYPE']) && !empty($_REQUEST['UF_TYPE'])) {
        $_SESSION['FIRST_TIME'] = (int)$_REQUEST['UF_TYPE'];
    };

    if (isset($arFields['ID'])) {

        CModule::IncludeModule("main");

        $event_name = 'USER_AFTER_ADD';

        $arrSites = array();
        $objSites = CSite::GetList(($by = "sort"), ($order = "asc"));

        while ($arrSite = $objSites->Fetch()) {
            $arrSites[] = $arrSite["ID"];
        };

        if (isset($arFields['PASSWORD_CONFIRM']) && !empty($arFields['PASSWORD_CONFIRM'])) {
            $arFields['CONFIRM_PASSWORD'] = $arFields['PASSWORD_CONFIRM'];
        };

        CEvent::SendImmediate($event_name, $arrSites, $arFields);
    };

}

;

function ceilRubPrice($price)
{
    return ($price > 1 ? ceil($price / 10) * 10 : $price);
}


function cacheBondsProducts()
{

    static $bondProducts;

    $cacheTime = 21600;

    if (empty($bondProducts)) {

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/bonds_cache.php'))
            require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/bonds_cache.php';

        if (!(isset($bondProducts) && !empty($bondProducts))) {

            $bondProducts = array();

            if (IBLOCK_INCLUDED) {

                $obCache = new CPHPCache;
                $cacheID = 'bonds_products';

                if ($obCache->InitCache($cacheTime, $cacheID, "/impel/")) {

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if (isset($tmp[$cacheID])) {
                        $bondProducts = $tmp[$cacheID];
                    }

                } else {

                    $arSelect = array("ID", "PROPERTY_MAIN_PRODUCTS");
                    $arFilter = array("IBLOCK_ID" => 11);
                    $mainProductsRes = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);


                    if ($mainProductsRes) {

                        while ($mainProductsArr = $mainProductsRes->GetNext()) {

                            $bondProducts[$mainProductsArr['ID']] = isset($mainProductsArr['PROPERTY_MAIN_PRODUCTS_VALUE'])
                            && !empty($mainProductsArr['PROPERTY_MAIN_PRODUCTS_VALUE'])
                            && is_numeric(trim($mainProductsArr['PROPERTY_MAIN_PRODUCTS_VALUE']))
                                ? $mainProductsArr['PROPERTY_MAIN_PRODUCTS_VALUE']
                                : false;

                        }

                    }

                    if ($obCache->StartDataCache()) {

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $bondProducts
                            )
                        );

                    };

                }

            }

        }

    }

    return $bondProducts;

}

function change_quantity_product($orderId)
{
    if (IBLOCK_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED) {
        $backet = CSaleBasket::GetList(
            false,
            array("ORDER_ID" => $orderId)
        );


        $products_id = array();

        if (is_object($backet) && method_exists($backet, 'Fetch')) {
            while ($ar_props = $backet->Fetch()) {
                if ($ar_props && is_array($ar_props) && isset($ar_props['ID'])) {
                    $products_id[] = $ar_props['PRODUCT_ID'];
                }
            }
        }

        foreach ($products_id as $product_id) {

            $arSelect = array("IBLOCK_ID", "CATALOG_QUANTITY");
            $arFilter = array("ID" => $product_id);
            $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            $product = array();

            while ($res
                && ($product = $res->Fetch())) {
                if (isset($product['IBLOCK_ID'])
                    && !empty($product['IBLOCK_ID'])
                ) {

                    switch ($product['IBLOCK_ID']) {
                        case 11:

                            $arSelect = array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                            $arFilter = array("IBLOCK_ID" => $product['IBLOCK_ID'], "ID" => $product_id);
                            $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                            $product = array();

                            while ($res
                                && ($product = $res->Fetch())) {

                                $iblock_id = $product['IBLOCK_ID'];
                                $PROPERTY_CODE = "QUANTITY";
                                $PROPERTY_VALUE = $product['CATALOG_QUANTITY'];

                                CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                                if ($iblock_id == 11)
                                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                            }

                            break;
                        case 16:

                            $arSelect = array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                            $arFilter = array("IBLOCK_ID" => 11, "PROPERTY_MAIN_PRODUCTS" => $product_id);
                            $quantity = $product['CATALOG_QUANTITY'];
                            $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

                            $iblock_id = $product['IBLOCK_ID'];
                            $PROPERTY_CODE = "QUANTITY";
                            $PROPERTY_VALUE = $quantity;
                            CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                            if ($iblock_id == 11)
                                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                            $product = array();

                            while ($res
                                && ($product = $res->Fetch())) {

                                $product_id = $product['ID'];
                                $iblock_id = $product['IBLOCK_ID'];
                                $PROPERTY_CODE = "QUANTITY";
                                $PROPERTY_VALUE = $quantity;

                                CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                                if ($iblock_id == 11)
                                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                            }

                            break;
                    }

                }
            }
        }
    }
}

function OnModelUpdateHandler($arFields, $deleteFrom = false)
{
    /*
    $dbModels = CIBlockElement::GetList(
        array("sort" => "ASC"),
        array(
            "IBLOCK_ID" => 17,
            "!PROPERTY_products" => false,
            "ID" => $arFields["ID"]
        ),
        false,
        false,
        array("ID","NAME","PROPERTY_products")
    );

    if($dbModels){
        while($arModels = $dbModels->GetNext()) {

            $dbModelProps = CIBlockElement::GetProperty(17, $arModels['ID'], array("sort" => "asc"), Array("CODE"=>"products"));

            $modelId = $arModels['NAME'];

            $modelProducts = array();

            if($dbModelProps){

                while($arModelProps = $dbModelProps->Fetch()){

                    $dbProducts = CIBlockElement::GetList(
                        array(),
                        array(
                            "IBLOCK_ID" => 11,
                            "ID" => $arModelProps['VALUE']
                        ),
                        false,
                        false,
                        array("ID","PROPERTY_MODEL")
                    );

                    if($dbProducts){

                        $models = array();

                        $arProducts = $dbProducts->GetNext();

                        if(     isset($arProducts['PROPERTY_MODEL_VALUE'])
                            &&  isset($arProducts['PROPERTY_MODEL_VALUE']['TEXT'])
                            &&  !empty($arProducts['PROPERTY_MODEL_VALUE']['TEXT'])
                        ){

                            $models = explode("\n",$arProducts['PROPERTY_MODEL_VALUE']['TEXT']);
                            $models = array_map("trim",$models);
                            $models = array_filter($models);

                        }

                        if(!$deleteFrom){

                            if(!in_array($modelId,$models)){

                                $models[] = $modelId;
                                $models = join("\n",$models);
                                CIBlockElement::SetPropertyValuesEx($arModelProps['VALUE'], 11, array('MODEL' => array("TEXT" => $models)));

                            }

                        } else {

                            if(in_array($modelId,$models)){

                                $models = array_diff($models, array($modelId));
                                $models = join("\n",$models);
                                CIBlockElement::SetPropertyValuesEx($arModelProps['VALUE'], 11, array('MODEL' => array("TEXT" => $models)));

                            }

                        }

                    }

                }

            }

        }

    } */
}


AddEventHandler("iblock", "OnAfterIBlockElementDelete", "OnAfterIBlockElementDeleteHandler");

function OnAfterIBlockElementDeleteHandler(&$arFields)
{
    twigTMPLHFCache::skipTmplHFCache($arFields);
}

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");

function OnAfterIBlockElementAddHandler(&$arFields)
{

    twigTMPLHFCache::skipTmplHFCache($arFields);

    if ($arFields['IBLOCK_ID'] == 28
        && $arFields["ID"] > 0
    ) {

        $arMail = array();

        if (isset($arFields['NAME'])
            && !empty($arFields['NAME'])
        ) {
            $arMail['NAME'] = trim($arFields['NAME']);
        }

        if (isset($arFields['PROPERTY_VALUES'])
            && isset($arFields['PROPERTY_VALUES'][130])
            && !empty($arFields['PROPERTY_VALUES'][130])
        ) {
            $arMail['PRODUCT'] = trim($arFields['PROPERTY_VALUES'][130]);
        }


        if (isset($arFields['PROPERTY_VALUES'])
            && isset($arFields['PROPERTY_VALUES'][131])
            && !empty($arFields['PROPERTY_VALUES'][131])
        ) {
            $arMail['MODEL_NUMBER'] = trim($arFields['PROPERTY_VALUES'][131]);
        }

        $arMail['MODEL_PHONE'] = '';

        if (isset($arFields['PROPERTY_VALUES'])
            && isset($arFields['PROPERTY_VALUES'][239])
            && !empty($arFields['PROPERTY_VALUES'][239])
        ) {
            $arMail['MODEL_PHONE'] = trim($arFields['PROPERTY_VALUES'][239]);
        }

        if (isset($arFields['PROPERTY_VALUES'])
            && isset($arFields['PROPERTY_VALUES'][132])
            && !empty($arFields['PROPERTY_VALUES'][132])
        ) {
            $arMail['I_SEARCH'] = trim($arFields['PROPERTY_VALUES'][132]);
        }

        CEvent::SendImmediate('NEED_PRODUCT', SITE_ID, $arMail);

    }

    if ($arFields['IBLOCK_ID'] == 17
        && $arFields["ID"] > 0
        && false) {

        OnModelUpdateHandler($arFields);

    }


}


AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandler");

function OnAfterIBlockElementUpdateHandler(&$arFields)
{

    twigTMPLHFCache::skipTmplHFCache($arFields);

    if ($arFields['IBLOCK_ID'] == 17
        && $arFields["ID"] > 0) {

        OnModelUpdateHandler($arFields);

    }

}

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnStoreUpdateHandler");

function OnStoreUpdateHandler(&$arFields)
{

    if ($arFields['IBLOCK_ID'] == 16
        && $arFields["ID"] > 0) {

        $dbFields = CIBlockElement::GetProperty(16, $arFields['ID'], array("sort" => "asc"), array("CODE" => "REMOTE_STORE"));
        $iCount = 0;

        if ($dbFields) {

            while ($apFields = $dbFields->Fetch()) {
                $iCount = (int)trim($apFields['VALUE']);
            }
        }

        if ($iCount) {

            $isCount = 0;

            $rsStore = CCatalogStoreProduct::GetList(
                array(),
                array('PRODUCT_ID' => $arFields['ID'], "STORE_ID" => 9),
                false,
                false
            );

            if ($rsStore) {

                while ($arStore = $rsStore->Fetch()) {

                    $isCount = (float)$arStore['AMOUNT'];

                }

            }

            if ($iCount > 0) {

                if (isset($_POST['AR_AMOUNT']) && isset($_POST['AR_AMOUNT'][9]) && $_POST['AR_AMOUNT'][9] != $iCount) {
                    $_REQUEST['AR_AMOUNT'][9] = $_POST['AR_AMOUNT'][9] = $iCount;
                }

            }

        }

    }

}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementAddHandler");

function OnBeforeIBlockElementAddHandler(&$arFields)
{

    global $APPLICATION, $USER;

    $return = true;

    if ($arFields['IBLOCK_ID'] == 28
        && !$USER->IsAuthorized()
        && (mb_stripos($APPLICATION->GetCurPage(), '/bitrix/admin/') !== 0)) {

        $return = getRecaptchaResponse();

    }

    return $return;

}


AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "OnBeforeIBlockElementDeleteHandler");

function OnBeforeIBlockElementDeleteHandler($ID)
{

    $dbModelDel = CIBlockElement::GetByID($ID);

    if ($dbModelDel) {

        if ($arFields = $dbModelDel->GetNext()) {

            if ($arFields['IBLOCK_ID'] == 17
                && $arFields["ID"] > 0) {

                OnModelUpdateHandler($arFields, true);

            }

        }

    }


}

function createAMPSRCSetHTML($strFile)
{
    global $USER;

    return '';

    $returnSetAttrHTML = '';
    $acSrc = array();

    $returnSet = array();

    if (mb_stripos($strFile, $_SERVER['DOCUMENT_ROOT']) === false) {
        $checkFile = $_SERVER['DOCUMENT_ROOT'] . $strFile;
    } else {
        $checkFile = $strFile;
    }

    if (file_exists($checkFile)
        && filesize($checkFile) > 0
        && is_readable($checkFile)) {

        $deviceSizes = array('320w' => 320,
            '480w' => 480,
            '640w' => 640,
            '960w' => 960,
            '1036w' => 960,
            '1334w' => 960,
            '1920w' => 960);

        foreach ($deviceSizes as $currentWidth => $currentSize) {

            if (!isset($acSrc[$currentSize])) {

                $sImage = $acSrc[$currentSize] = $returnSet[$currentWidth] = rectangleImage($checkFile, $currentSize, $currentSize, str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $strFile));

                $sExt = mb_strtolower(trim(pathinfo($sImage, PATHINFO_EXTENSION)));
                $sBase = pathinfo($sImage, PATHINFO_FILENAME);
                $sDir = pathinfo($sImage, PATHINFO_DIRNAME);
                $sCache = '/upload/webp/' . trim($sDir, '/') . '/';

                if ($sExt != "webp") {

                    $sWebpPath = $sCache . $sBase . '.webp';

                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {


                        $sWebpPathDir = '/' . trim(dirname($_SERVER['DOCUMENT_ROOT'] . $sWebpPath), '/') . '/';

                        if (!file_exists($sWebpPathDir)) {
                            @mkdir($sWebpPathDir, 0775, true);
                        }

                        @passthru('cwebp -q 95 ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sImage) . ' -o ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sWebpPath) . ' -quiet');

                    }

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {

                        $returnSet[$currentWidth] = $acSrc[$currentSize] = $sWebpPath;

                    }

                }


            } else {
                $returnSet[$currentWidth] = $acSrc[$currentSize];
            }
        }

    }


    if (!empty($returnSet)) {
        foreach ($returnSet as $dimension => $file) {
            $returnSetAttrHTML .= (!empty($returnSetAttrHTML) ? ',' : '') . $file . ' ' . $dimension;
        }

        if (!empty($returnSetAttrHTML)) {
            $returnSetAttrHTML = ' srcset="' . $returnSetAttrHTML . '"';
        }

    }

    return $returnSetAttrHTML;
}

function checkIfModels()
{

    $return = array();
    $curDir = $_SERVER['REQUEST_URI'];


    if (mb_stripos($curDir, '/amp/model/') !== false) {
        $curDir = preg_replace('~.+?\/\/[^\/]+~', '', $curDir);
        $curDir = trim($curDir, '/');
        $curDir = explode('/', $curDir);


        if (isset($curDir[2])
            && !empty($curDir[2])) {

            $aSelect = array(
                "ID",
                "PROPERTY_type_of_product",
                "PROPERTY_manufacturer",
                "PROPERTY_model_new_link"
            );


            $curDir[2] = preg_replace('~[^A-Z0-9\-\_]~is', '', $curDir[2]);

            $aFilter = array(
                "CODE" => $curDir[2],
                "IBLOCK_ID" => 17,
                "ACTIVE" => "Y"
            );

            $rmDB = CIBlockElement::GetList(
                array(),
                $aFilter,
                false,
                array("nPageSize" => 1),
                $aSelect);

            if ($rmDB) {
                while ($am = $rmDB->GetNext()) {

                    if (isset($am['PROPERTY_MODEL_NEW_LINK_VALUE'])
                        && !empty($am['PROPERTY_MODEL_NEW_LINK_VALUE'])) {


                        $rnmDB = CIBlockElement::GetById($am['PROPERTY_MODEL_NEW_LINK_VALUE']);

                        if ($rnmDB) {

                            $arnm = $rnmDB->GetNext();

                            if (isset($arnm['NAME'])
                                && !empty($arnm['NAME'])
                                && isset($am['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                && !empty($am['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                && isset($am['PROPERTY_MANUFACTURER_VALUE'])
                                && !empty($am['PROPERTY_MANUFACTURER_VALUE'])

                            ) {

                                $return[] = array('NAME' => 'Тип товара',
                                    'CODE' => 'TYPE_OF_PRODUCT',
                                    'VALUE' => $am['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                    'SORT' => 101);

                                $return[] = array('NAME' => 'Производитель',
                                    'CODE' => 'MANUFACTURER',
                                    'VALUE' => $am['PROPERTY_MANUFACTURER_VALUE'],
                                    'SORT' => 102);

                                $return[] = array('NAME' => 'Модель',
                                    'CODE' => 'MODEL',
                                    'VALUE' => $arnm['NAME'],
                                    'SORT' => 103);

                            }

                        }

                    }

                }
            }

        }
    }

    return $return;

}

AddEventHandler("main", "OnBeforeProlog", "checkBuyAMPActionHandler");

function checkBuyAMPActionHandler()
{
    global $APPLICATION;


    if(mb_stripos($_SERVER['ORIG_REQUEST_URI'],'/catalog/pages-') !== false
        || mb_stripos($_SERVER['ORIG_REQUEST_URI'],'/catalog/category-zapchasti-mbt/pages-') !== false
    ) {
        $currentUri = $_SERVER['ORIG_REQUEST_URI'];
        $currentUri = preg_replace('~/pages[^/]+/~isu','/',$currentUri);
        LocalRedirect($currentUri);
    }

    if(mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false) {

        $currentURL = $APPLICATION->GetCurDir();
        if(preg_match('~/manufacturer-[^/]+?/typeproduct-[^/]+?/~isu',$currentURL)){
            $currentURL = preg_replace('~/(manufacturer-[^/]+?)/(typeproduct-[^/]+?)/~isu',"/$2/$1/",$currentURL);
            LocalRedirect($currentURL);
        }
    }

    if(mb_stripos($_SERVER['ORIG_REQUEST_URI'],'/pages') !== false) {

        $currentURL = $origUrl = $_SERVER['ORIG_REQUEST_URI'];

        if (mb_stripos($currentURL,'pages0') !== false) {
            $currentURL = str_ireplace('pages0','pages',$currentURL);
        }

        if(preg_match('~/pages-[^/]+?/[^/]+?/~isu',$currentURL)){
            $currentURL = preg_replace('~/(pages-[^/]+?)/([^\?]+)(\?.*)*$~isu',"/$2/$1/$3",$currentURL);
            $currentURL = str_ireplace('//','/',$currentURL);
        }

        if ($currentURL != $origUrl) {
            LocalRedirect($currentURL);
        }


    }

    $sAction = '';

    foreach ($_REQUEST as $key => $value) {
        if (mb_stripos($key, 'action') === 0) {
            $sAction = $value;
            break;
        }
    }

    if (!empty($sAction)
        && (($sAction == 'add2basketamp')
            || ($sAction == 'ADD2BASKET'
                && isset($_REQUEST['ajax_basket'])
                && $_REQUEST['ajax_basket'] == 'Y'))
        && isset($_REQUEST['id'])
        && !empty($_REQUEST['id'])
    ) {

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        $product_id = isset($_REQUEST['PRODUCT_BUY_ID'])
        && !empty($_REQUEST['PRODUCT_BUY_ID'])
            ? abs((int)$_REQUEST['PRODUCT_BUY_ID'])
            : abs((int)$_REQUEST['id']);

        if ($sAction == 'ADD2BASKET') {
            $APPLICATION->RestartBuffer();
            $aResult = array(
                'STATUS' => 'ERROR',
                'MESSAGE' => GetMessage('TMPL_BASKET_ADD_ERROR')
            );
        }

        if (!empty($product_id)) {

            $properties = array();

            $message = '';

            if (Bitrix\Main\Loader::includeModule("catalog")) {

                $context = \Bitrix\Main\Context::getCurrent();

                $product_url = '';

                $dName = CIBlockElement::GetList(
                    array(),
                    ($aFilter = array(
                        'ID' => $product_id
                    )
                    ),
                    false,
                    false,
                    ($aSelect = array(
                        'NAME',
                        'DETAIL_PAGE_URL',
                        'IBLOCK_EXTERNAL_ID',
                        'XML_ID'
                    ))
                );

                if ($dName
                    && $product_data = $dName->GetNext()) {

                }

                $product_name = isset($product_data['NAME']) ? $product_data['NAME'] : '';
                $product_name = isset($request['product_name']) && !empty($request['product_name']) ? trim($request['product_name']) : $product_name;

                $product_url = isset($product_data['DETAIL_PAGE_URL']) ? $product_data['DETAIL_PAGE_URL'] : '';


                if ($product_name
                    && $product_url) {

                    $message = sprintf(GetMessage("TMPL_CAN_NOT_BUY_MORE"), $product_name);

                    $product_buy_id = getBondsProduct($product_id);

                    $outnumber = get_quantity_product($product_buy_id);
                    $poutnumber = get_quantity_product_provider($product_buy_id);

                    if ($outnumber > 0) {

                        $price = CCatalogProduct::GetOptimalPrice($product_id, 1);

                        if (isset($price['PRICE'])
                            && isset($price['PRICE']['PRICE'])
                            && $price['PRICE']['PRICE'] > 0
                            && isset($price['PRICE']['CURRENCY'])) {

                            $aPrice = array();

                            $group_name = '';

                            $dPrice = CPrice::GetList(
                                array(),
                                array(
                                    "PRODUCT_ID" => $product_id,
                                    "CATALOG_GROUP_ID" => $price['PRICE']['CATALOG_GROUP_ID']
                                )
                            );

                            if ($dPrice
                                && $aPrice = $dPrice->Fetch()) {
                                if (isset($aPrice['CATALOG_GROUP_NAME']))
                                    $group_name = $aPrice['CATALOG_GROUP_NAME'];
                            }

                            $default_currency = getCurrentCurrencyCode();

                            if ($default_currency != $price['PRICE']['CURRENCY']) {
                                $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                                $price['PRICE']['CURRENCY'] = $default_currency;
                            }

                            $fields = array(
                                'QUANTITY' => 1,
                                'NAME' => $product_name,
                                'PRICE' => $price['PRICE']['PRICE'],
                                'CURRENCY' => $price['PRICE']['CURRENCY'],
                                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProviderCustom',
                                'DETAIL_PAGE_URL' => $product_url,
                                'CATALOG_XML_ID' => $product_data['IBLOCK_EXTERNAL_ID'],
                                'PRODUCT_XML_ID' => $product_data['XML_ID'],
                                'NOTES' => $group_name,
                                'CUSTOM_PRICE' => 'Y',
                            );

                            $provider_percent = COption::GetOptionString("my.stat", "provider_percent", 0);

                            $properties = array(
                                array(
                                    'NAME' => 'Catalog XML_ID',
                                    'CODE' => 'CATALOG.XML_ID',
                                    'VALUE' => $product_data['IBLOCK_EXTERNAL_ID'],
                                    'SORT' => 100
                                ),
                                array(
                                    'NAME' => 'Product XML_ID',
                                    'CODE' => 'PRODUCT.XML_ID',
                                    'VALUE' => $product_data['XML_ID'],
                                    'SORT' => 100
                                ),
                            );

                            if ($poutnumber == $outnumber && $provider_percent > 0) {
                                $fields['DELAY'] = 'Y';
                                $bHasProvider = true;
                                $aResult['FROM_PROVIDER'] = true;
                            } else {
                                $bHasProvider = false;
                            }


                            if ($product_id != $product_buy_id) {

                                $dName = CIBlockElement::GetList(
                                    array(),
                                    ($aFilter = array(
                                        'ID' => $product_buy_id
                                    )
                                    ),
                                    false,
                                    false,
                                    ($aSelect = array(
                                        'NAME',
                                        'DETAIL_PAGE_URL',
                                        'IBLOCK_EXTERNAL_ID',
                                        'XML_ID'
                                    ))
                                );

                                if ($dName
                                    && $base_data = $dName->GetNext()) {

                                }

                                if (isset($base_data['NAME'])) {

                                    $fields['CATALOG_XML_ID'] = $properties[0]['VALUE'] = $base_data['IBLOCK_EXTERNAL_ID'];
                                    $fields['PRODUCT_XML_ID'] = $properties[1]['VALUE'] = $base_data['XML_ID'];

                                    $properties[] =
                                        array(
                                            'NAME' => 'Базовый товар',
                                            'CODE' => 'BASIC',
                                            'VALUE' => $base_data['NAME'],
                                            'SORT' => 100
                                        );

                                }

                                unset($base_data);

                            }

                            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                                \Bitrix\Sale\Fuser::getId(),
                                \Bitrix\Main\Context::getCurrent()->getSite()
                            );

                            $itemExists = false;

                            foreach ($basket as $basketItem) {

                                if ($fields['NAME'] == $basketItem->getField('NAME')
                                    && $product_buy_id == $basketItem->getField('PRODUCT_ID')
                                ) {

                                    $itemExists = $basketItem;

                                }

                            }


                            if ($itemExists) {

                                $itemExists->setField('QUANTITY', $itemExists->getQuantity() + 1);
                                $itemExists->setField('DETAIL_PAGE_URL', $product_url);


                            } else {

                                $itemExists = $basket->createItem('catalog', $product_buy_id);
                                $itemExists->setFields($fields);
                                $itemExists->save();

                                $mproperties = checkIfModels();
                                if (!empty($properties)) {
                                    $mproperties = array_merge($properties, $mproperties);
                                }

                                if (!empty($mproperties)) {

                                    $props = $itemExists->getPropertyCollection();
                                    $props->setProperty($mproperties);

                                    $props->save();

                                }

                            }

                            $basket->save();

                            $aResult = array(
                                'STATUS' => 'OK',
                                'MESSAGE' => GetMessage('TMPL_BASKET_ADD_OK')
                            );


                        }

                    }


                    unset($product_data);

                }

            }

            if ($sAction == 'ADD2BASKET') {
                echo json_encode($aResult);
                die();
            }

            if (!$bHasProvider) {
                LocalRedirect("/personal/cart/");
            } else {
                LocalRedirect("/personal/provider/");
            }

        }


    }

}

function getAmountInStores($product_id)
{

    $arStores = array();

    $can_buy = canYouBuy($product_id);
    $quantity = get_quantity_product($product_id);
    $can_buy = $quantity > 0 ? $can_buy : false;

    if ($can_buy) {

        $buy_id = getBondsProduct($product_id);

        $rsStore = CCatalogStoreProduct::GetList(
            array(),
            array('PRODUCT_ID' => $buy_id, "!STORE_ID" => array(3, 10)),
            false,
            false
        );

        $arResult['STORES'] = array();
        $in_stock_label = '';

        if ($rsStore) {

            while ($arStore = $rsStore->Fetch()) {

                if (isset($arStore['AMOUNT'])
                    && !empty($arStore['AMOUNT'])) {

                    $arStores[] = array('STORE_ID' => $arStore['STORE_ID'], 'STORE_NAME' => $arStore['STORE_NAME'], 'AMOUNT' => $arStore['AMOUNT']);

                }

            }

        }

    }

    return $arStores;

}

AddEventHandler("forum", "onBeforeMessageAdd", "onBeforeMessageAddHandler");

function onBeforeMessageAddHandler(&$arFields)
{

    global $APPLICATION, $USER;

    $forumId = isset($_REQUEST['FORUM_ID']) ? (int) trim($_REQUEST['FORUM_ID']) : 0;
    $arFields['FORUM_ID'] = $forumId ? $forumId : $arFields['FORUM_ID'];

    if (!$USER->IsAuthorized()
        && mb_stripos($APPLICATION->GetCurDir(), '/forum/') === 0)
        return false;

    if (!$USER->IsAuthorized()
        && mb_stripos($APPLICATION->GetCurDir(), '/amp/') !== 0
        && mb_stripos($APPLICATION->GetCurDir(), '/forum/') !== 0
        && mb_stripos($APPLICATION->GetCurDir(), '/bitrix/') !== 0) {

        $return = getRecaptchaResponse();

        if (!$return)
            return $return;
    }

    if (isset($_REQUEST['REVIEW_ADVANTAGES'])) {

        $post_message = '';

        if (!(isset($_REQUEST['REVIEW_AUTHOR'])
            && !empty($_REQUEST['REVIEW_AUTHOR']))) {
            $_REQUEST['REVIEW_AUTHOR'] = $arFields['AUTHOR_NAME'];
        }

        if (isset($_REQUEST['REVIEW_AUTHOR'])
            && !empty($_REQUEST['REVIEW_AUTHOR'])) {

            $_REQUEST['REVIEW_AUTHOR'] = trim($_REQUEST['REVIEW_AUTHOR']);
            $_REQUEST['REVIEW_AUTHOR'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_AUTHOR']);
            $post_message .= '[i]' . $_REQUEST['REVIEW_AUTHOR'] . '[/i]' . "\n";

        }

        if (isset($_REQUEST['REVIEW_TOWN'])
            && !empty($_REQUEST['REVIEW_TOWN'])) {

            $_REQUEST['REVIEW_TOWN'] = trim($_REQUEST['REVIEW_TOWN']);
            $_REQUEST['REVIEW_TOWN'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_TOWN']);
            $_REQUEST['REVIEW_TOWN'] .= ', ';

        } else {
            $_REQUEST['REVIEW_TOWN'] = '';
        }

        $post_message .= '[i]' . $_REQUEST['REVIEW_TOWN'] . ' ' . date('d.m.Y') . '[/i]' . "\n";

        if (isset($_REQUEST['REVIEW_ADVANTAGES'])
            && !empty($_REQUEST['REVIEW_ADVANTAGES'])) {

            $_REQUEST['REVIEW_ADVANTAGES'] = trim($_REQUEST['REVIEW_ADVANTAGES']);
            $_REQUEST['REVIEW_ADVANTAGES'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_ADVANTAGES']);
            $post_message .= '[b]' . GetMessage('F_ADVANTAGES') . '[/b] [i]' . $_REQUEST['REVIEW_ADVANTAGES'] . '[/i]' . "\n";

        } else {

            //$_REQUEST['REVIEW_ADVANTAGES'] = GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');

        }


        if (isset($_REQUEST['REVIEW_DISADVANTAGES'])
            && !empty($_REQUEST['REVIEW_DISADVANTAGES'])) {

            $_REQUEST['REVIEW_DISADVANTAGES'] = trim($_REQUEST['REVIEW_DISADVANTAGES']);
            $_REQUEST['REVIEW_DISADVANTAGES'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_DISADVANTAGES']);
            $post_message .= '[b]' . GetMessage('F_DISADVANTAGES') . '[/b] [i]' . $_REQUEST['REVIEW_DISADVANTAGES'] . '[/i]' . "\n";

        } else {

            //$_REQUEST['REVIEW_DISADVANTAGES'] = GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');
        }


        $arFields['POST_MESSAGE'] = trim($arFields['POST_MESSAGE']);
        $arFields['POST_MESSAGE'] = preg_replace('~\[[^\]]+?\]~', '', $arFields['POST_MESSAGE']);
        $arFields['POST_MESSAGE'] = $post_message . $arFields['POST_MESSAGE'] . '';

    } else if (isset($_REQUEST['REVIEW_PHONE'])) {


        if (empty($_REQUEST['REVIEW_EMAIL']) && empty($_REQUEST['REVIEW_PHONE'])) {
            global $APPLICATION;
            $APPLICATION->throwException("Укажите телефон или email");
            return false;
        }

        if (!empty($_REQUEST['REVIEW_EMAIL'])
            && (stripos($_REQUEST['REVIEW_EMAIL'],'@') === false)) {
            $APPLICATION->throwException("Email указан не верно");
            return false;
        }

        $_REQUEST['REVIEW_PHONE'] = preg_replace('~[^0-9\+]+~isu','',$_REQUEST['REVIEW_PHONE']);

        if (!empty($_REQUEST['REVIEW_PHONE'])
            && (strlen($_REQUEST['REVIEW_PHONE']) != 12)) {
            $APPLICATION->throwException("Проверьте указанный телефон");
            return false;
        }

        $post_message = '[i]';

        if (!(isset($_REQUEST['REVIEW_AUTHOR'])
            && !empty($_REQUEST['REVIEW_AUTHOR']))) {
            $_REQUEST['REVIEW_AUTHOR'] = $arFields['AUTHOR_NAME'];
        }

        if (isset($_REQUEST['REVIEW_AUTHOR'])
            && !empty($_REQUEST['REVIEW_AUTHOR'])) {

            $_REQUEST['REVIEW_AUTHOR'] = trim($_REQUEST['REVIEW_AUTHOR']);
            $_REQUEST['REVIEW_AUTHOR'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_AUTHOR']);
            $post_message .= '' . $_REQUEST['REVIEW_AUTHOR'] . ', ' . date('d.m.Y') . '';

        }

        $post_message .= '[/i]'. "\n";

        $post_message .= '[b]';

        if (isset($_REQUEST['REVIEW_PHONE'])
            && !empty($_REQUEST['REVIEW_PHONE'])) {

            $_REQUEST['REVIEW_PHONE'] = trim($_REQUEST['REVIEW_PHONE']);
            $_REQUEST['REVIEW_PHONE'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_PHONE']);
            $_REQUEST['REVIEW_PHONE'] .= ', ';
            $post_message .= $_REQUEST['REVIEW_PHONE'];

        } else {
            $_REQUEST['REVIEW_PHONE'] = '';
        }

        if (isset($_REQUEST['REVIEW_EMAIL'])
            && !empty($_REQUEST['REVIEW_EMAIL'])) {

            $_REQUEST['REVIEW_EMAIL'] = trim($_REQUEST['REVIEW_EMAIL']);
            $_REQUEST['REVIEW_EMAIL'] = preg_replace('~\[[^\]]+?\]~', '', $_REQUEST['REVIEW_EMAIL']);
            $post_message .= $_REQUEST['REVIEW_EMAIL'];

        } else {

            //$_REQUEST['REVIEW_ADVANTAGES'] = GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');

        }

        $post_message .= '[/b]';

        $arFields['POST_MESSAGE'] = trim($arFields['POST_MESSAGE']);
        $arFields['POST_MESSAGE'] = preg_replace('~\[[^\]]+?\]~', '', $arFields['POST_MESSAGE']);
        $arFields['POST_MESSAGE'] = ''. trim(trim(trim($post_message),',')) . "\n" . $arFields['POST_MESSAGE'] . '';

    } else if (isset($_REQUEST['REVIEW_ANSWER'])) {

        if ((isset($_REQUEST['REVIEW_AUTHOR'])
            && !empty($_REQUEST['REVIEW_AUTHOR']))) {
            $arFields['AUTHOR_NAME'] = $_REQUEST['REVIEW_AUTHOR'];
        }

        if ((isset($_REQUEST['REVIEW_EMAIL'])
            && !empty($_REQUEST['REVIEW_EMAIL'])
            && filter_var($_REQUEST['REVIEW_EMAIL'], FILTER_VALIDATE_EMAIL)
        )) {
            $arFields['AUTHOR_EMAIL'] = $_REQUEST['REVIEW_EMAIL'];
        }

    }

    if (!checkQuantityRigths()) {
        if (preg_match('~(<a.*?>)|(\[url.*?\])|(www\.)|(http://)|(https://)|(://)~isu', $arFields['POST_MESSAGE'])
            || preg_match('~(<a.*?>)|(\[url.*?\])|(www\.)|(http://)|(https://)|(://)~isu', $arFields['AUTHOR_NAME'])) {

            global $APPLICATION;
            $arFields['APPROVED'] = 'N';
            $APPLICATION->throwException("Ваше сообщение будет проверено и опубликовано.");
        }

    }

}

function DeleteUnapprovedMessages()
{
    $uTime = time() - 7 * 86400;
    $rfDB = CForumMessage::GetList(
        array(
            "ID" => "ASC"
        ),
        array(
            "APPROVED" => "N",
            ">=POST_DATE" => date('d.m.Y 00:00:00', $uTime))
    );

    if ($rfDB) {
        while ($afpDb = $rfDB->Fetch()) {
            if (isset($afpDb["ID"]) && !empty($afpDb["ID"])) {
                \CForumMessage::Delete($afpDb["ID"]);
            }
        }
    }
}

AddEventHandler("forum", "onAfterMessageAdd", "ChangeItemRating");

function ChangeItemRating($ID, $arFields)
{

    DeleteUnapprovedMessages();

    $vote = isset($_REQUEST['vote'])
    && !empty($_REQUEST['vote'])
        ? (int)$_REQUEST['vote']
        : 0;

    if (isset($_REQUEST['REVIEW_ANSWER'])) {


        $irAnswer = (int)trim($_REQUEST['REVIEW_ANSWER']);
        $irPostId = (int)trim($_REQUEST['ELEMENT_ID']);

        if ($irAnswer
            && !empty($irAnswer)) {
            $sSql = 'INSERT INTO `b_comment_answer`(`id`,`topic_id`,`review_id`,`post_id`) VALUES(\'NULL\',' . $irAnswer . ',' . (int)$ID . ',' . $irPostId . ')';
            global $DB;
            $DB->Query($sSql);
        }
    }

    if ($vote
        && in_array($vote, array(1, 2, 3, 4, 5))) {

        $ibCommentsId = false;

        $ibRes = CIBlock::GetList(
            array(),
            array(
                'CODE' => 'commentsrating',

            )
        );

        if ($ibRes
            && $arIblock = $ibRes->Fetch()) {

            $ibCommentsId = $arIblock['ID'];

            if ($ibCommentsId) {

                $productId = (int)$_REQUEST['ELEMENT_ID'];

                $message_title = '';

                $dbERes = CIBlockElement::GetByID($productId);
                $productIbId = 0;

                if ($dbERes && $dbEArr = $dbERes->GetNext()) {
                    $message_title .= $dbEArr['NAME'];
                    $productIbId = $dbEArr['IBLOCK_ID'];
                }

                $message_title .= ' - ' . mb_substr(strip_tags($arFields['POST_MESSAGE']), 0, 120) . ' [' . $productId . ',' . $ID . ']';

                $params = array(
                    "max_len" => "250",
                    "change_case" => "L",
                    "replace_space" => "_",
                    "replace_other" => "_",
                    "delete_repeat_replace" => "true",
                );

                $arComment = array(
                    "NAME" => trim($message_title),
                    "ACTIVE" => "Y",
                    "CODE" => trim(CUtil::translit(trim($message_title), LANGUAGE_ID, $params)),
                    "IBLOCK_ID" => $ibCommentsId,
                    "PREVIEW_TEXT" => " ",
                    "DETAIL_TEXT" => " ",
                    "PROPERTY_VALUES" => array(
                        "forum_topic_id" => $ID,
                        "product_id" => $productId
                    ),
                );

                $commentEl = new CIBlockElement;

                if ($commentID = $commentEl->Add($arComment)) {

                    $rating = round(($vote + 31.25 / 5 * 5) / (1 + 10), 2);

                    CIBlockElement::SetPropertyValuesEx($commentID, $ibCommentsId, array(
                        "vote_count" => array(
                            "VALUE" => 1,
                            "DESCRIPTION" => "",
                        ),
                        "vote_sum" => array(
                            "VALUE" => $vote,
                            "DESCRIPTION" => "",
                        ),
                        "rating" => array(
                            "VALUE" => $rating,
                            "DESCRIPTION" => "",
                        ),
                    ));

                    //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($ibCommentsId, $commentID);

                    $dbFRes = CIBlockElement::GetList(
                        array(),
                        array(
                            "IBLOCK_ID" => $productIbId,
                            "ID" => $productId
                        ),
                        false,
                        false,
                        array("ID", "PROPERTY_vote_count", "PROPERTY_vote_sum", "PROPERTY_rating")
                    );

                    if ($dbFRes && $dbFAr = $dbFRes->GetNext()) {

                        $pVoteCount = (int)$dbFAr["PROPERTY_VOTE_COUNT_VALUE"];
                        $pVoteSum = (int)$dbFAr["PROPERTY_VOTE_SUM_VALUE"];
                        $pRating = (int)$dbFAr["PROPERTY_RATING_VALUE"];


                        $pVoteCount = intval($pVoteCount) + 1;
                        $pVoteSum = intval($pVoteSum) + $vote;
                        $pRating = round(($pVoteSum + 31.25 / 5 * 5) / ($pVoteCount + 10), 2);

                        CIBlockElement::SetPropertyValuesEx($productId, $productIbId, array(
                            "vote_count" => array(
                                "VALUE" => $pVoteCount,
                                "DESCRIPTION" => "",
                            ),
                            "vote_sum" => array(
                                "VALUE" => $pVoteSum,
                                "DESCRIPTION" => "",
                            ),
                            "rating" => array(
                                "VALUE" => $pRating,
                                "DESCRIPTION" => "",
                            ),
                        ));

                        if ($productIbId == 11)
                            \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($productIbId, $productId);


                    }

                }

            }

        }

    }

}

function checkOrderIdSalt()
{
    global $USER;

    if (CATALOG_INCLUDED
        && SALE_INCLUDED
        && isset($_REQUEST['check_hash'])
        && !empty($_REQUEST['check_hash'])
        && isset($_REQUEST['order_id'])
        && !empty($_REQUEST['order_id'])) {

        $order_id = (int)$_REQUEST['order_id'];

        $arFilter = array(
            'ID' => $order_id
        );

        $db_sales = CSaleOrder::GetList(
            array(
                "DATE_INSERT" => "DESC",
                "DATE_UPDATE" => "DESC"),
            $arFilter,
            false,
            false,
            array('*')
        );

        $bPassed = false;

        if ($db_sales
            && is_object($db_sales)
            && method_exists($db_sales, 'Fetch')) {

            while ($ar_sales = $db_sales->Fetch()) {

                if (isset($ar_sales['USER_ID'])
                    && !empty($ar_sales['USER_ID'])) {

                    $strHashes = array(':+', '.+', '-+', '!+', '++', '*+', '~+', '=+');

                    foreach ($strHashes as $strHash) {
                        $hashes[] = md5($ar_sales['USER_ID'] . '-' . $order_id) . ':' . md5($strHash . $ar_sales['USER_ID']);
                    }

                    if (isset($ar_sales['DATE_INSERT'])
                        && !empty($ar_sales['DATE_INSERT'])) {

                        $date_insert = (string)$ar_sales['DATE_INSERT'];

                        $check_hash = $_REQUEST['check_hash'];

                        {

                            if (!empty($check_hash)
                                && in_array($check_hash, $hashes)
                            ) {

                                $USER->Authorize($ar_sales['USER_ID']);
                                $bPassed = true;

                            }

                        }

                    }

                }

            }

        }

        if (!$bPassed) {

            $select = array(
                'ID',
                'ORDER_ID',
                'PAYED',
                'CANCELED',
                'STATUS_ID',
                "USER_ID",
                'DATE_INSERT'
            );

            $arFilter = array();
            $arFilter['ORDER_ID'] = $order_id;

            $getListParams = array(
                'filter' => $arFilter,
                'select' => $select,
                'order' => array('DATE_INSERT' => 'DESC')
            );

            $db_sales = new \CDBResult(\Bitrix\Sale\Internals\OrderArchiveTable::getList($getListParams));

            if ($db_sales)
                while ($ar_sales = $db_sales->GetNext()) {

                    if (isset($ar_sales['USER_ID'])
                        && !empty($ar_sales['USER_ID'])) {

                        $strHashes = array(':+', '.+', '-+', '!+', '++', '*+', '~+', '=+');

                        foreach ($strHashes as $strHash) {
                            $hashes[] = md5($ar_sales['USER_ID'] . '-' . $order_id) . ':' . md5($strHash . $ar_sales['USER_ID']);
                        }

                        if (isset($ar_sales['DATE_INSERT'])
                            && !empty($ar_sales['DATE_INSERT'])) {

                            $date_insert = (string)$ar_sales['DATE_INSERT'];

                            $check_hash = $_REQUEST['check_hash'];

                            {

                                if (!empty($check_hash)
                                    && in_array($check_hash, $hashes)
                                ) {
                                    $USER->Authorize($ar_sales['USER_ID']);
                                }

                            }

                        }

                    }

                }

        }

    }


    if (CATALOG_INCLUDED
        && SALE_INCLUDED
        && isset($_REQUEST['check_hash'])
        && !empty($_REQUEST['check_hash'])
        && isset($_REQUEST['FUSER_ID'])
        && !empty($_REQUEST['FUSER_ID'])) {

        $FUSER_ID = (int)$_REQUEST['FUSER_ID'];

        $isChecked = false;
        $strHashes = array(':+', '.+', '-+', '!+', '++', '*+', '~+', '=+');
        $check_hash = $_REQUEST['check_hash'];
        $hashes = array();

        for ($counter = 0; $counter < 6; $counter++) {

            foreach ($strHashes as $strHash) {

                $hashes[] = md5($FUSER_ID . '-' . $counter) . ':' . md5($strHash . $FUSER_ID);

            }

        }

        if (!empty($check_hash)
            && in_array($check_hash, $hashes)) {

            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "FUSER_ID" => $FUSER_ID,
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL"
                ),
                false,
                false,
                array("ID",
                    "USER_ID")
            );

            if ($dbBasketItems) {

                $arItems = $dbBasketItems->Fetch();

                if (isset($arItems['USER_ID'])
                    && !empty($arItems['USER_ID'])) {

                    $USER->Authorize($arItems['USER_ID']);

                }

            }
        }
    }
}

AddEventHandler('catalog', 'OnCompleteCatalogImport1C', "OnCompleteCatalogImport1CHandler");

function OnCompleteCatalogImport1CHandler($arParams, $ABS_FILE_NAME)
{

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/exchange_log/rests_updating.txt')) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/exchange_log/rests_updating.txt');
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/exchange_log/need_update.txt', date('Y-m-d H:i:s'));
    }

}

AddEventHandler("main", "OnBeforeProlog", "OnPrologExchangeHandler");

function OnPrologExchangeHandler()
{
    if (isset($_REQUEST['recovery'])) {

        $_REQUEST = array_merge($_REQUEST,
            array('confirmorder' => 'N',
                'profile_change' => 'N',
                'is_ajax_post' => 'N',
                'json' => 'N',
                'save' => 'Y'));

        if (isset($_REQUEST['sessid'])
            && preg_match('~^[a-z0-9]+$~isu', $_REQUEST['sessid'])) {

            $sessid = trim($_REQUEST['sessid']);
            $_REQUEST = serialize($_REQUEST);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/' . $sessid . '.txt', $_REQUEST);

        }

    }

    if (!isset($_REQUEST['recovery']) && isset($_REQUEST['tryrecovery'])) {
        $_POST['sessid'] = $_REQUEST['sessid'] = bitrix_sessid();
    }

    global $APPLICATION, $ORDERS_1C;

    checkOrderIdSalt();

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/urlrewrite.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/urlrewrite.php';

        if (is_array($arUrlRewrite) && !empty($arUrlRewrite)) {

            $changed = false;

            foreach ($arUrlRewrite as $number => $rewriteSub) {

                if (isset($rewriteSub["ID"])
                    && !empty($rewriteSub["ID"])
                    && preg_match('~\:catalog\.top~is', $rewriteSub["ID"])
                ) {

                    $changed = true;
                    unset($arUrlRewrite[$number]);

                }

            }

            if ($changed) {

                $strRepresent = '<? $arUrlRewrite = ' . var_export($arUrlRewrite, true) . '; ?>';
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/urlrewrite.php', $strRepresent);

            }

        }

    }


    if (
        $APPLICATION->GetCurPage() == '/bitrix/admin/1c_exchange.php'
    ) {

        //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/exchange.txt',date('Y-m-d H:i:s').var_export($_REQUEST,true)."\n",FILE_APPEND);

        if (isset($_REQUEST['mode'])
            && $_REQUEST['mode'] == 'import'
            && isset($_REQUEST['filename'])
            && !empty($_REQUEST['filename'])
            && (mb_stripos($_REQUEST['filename'], 'rests__') !== false)) {

            //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/exchange_log/rests_updating.txt', date('Y-m-d H:i:s'));

        }

        if (isset($_REQUEST['mode'])
            && $_REQUEST['mode'] == 'import'
            && isset($_REQUEST['type'])
            && $_REQUEST['type'] == 'sale'
            && isset($_REQUEST['filename'])
            && !empty($_REQUEST['filename'])
            && (mb_stripos($_REQUEST['filename'], 'documents__') !== false)
        ) {

            $file_exists = (int)file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/1c_exchange/' . $_REQUEST['filename']);

            if ($file_exists) {

                //copy($_SERVER['DOCUMENT_ROOT'].'/upload/1c_exchange/'.$_REQUEST['filename'],$_SERVER['DOCUMENT_ROOT'].'/exchange_log/'.$_REQUEST['filename']);

                $first_copy = false;

                $fp = @fopen($_SERVER['DOCUMENT_ROOT'] . '/upload/1c_exchange/' . $_REQUEST['filename'], 'rb');

                if ($fp
                    && is_resource($fp)) {

                    $deliveryIdcounter = 1;

                    $deliveryId_map = array();

                    if (SALE_INCLUDED) {

                        $deliveryId_delivery_sizeof = Bitrix\Main\Config\Option::Get("my.stat", "DeliveryId_delivery_sizeof", "");

                        for ($i = 0; $i < $deliveryId_delivery_sizeof; $i++) {
                            $deliveryId_delivery_id = Bitrix\Main\Config\Option::Get("my.stat", "DeliveryId_delivery_id" . $i, "");
                            $deliveryId = Bitrix\Main\Config\Option::Get("my.stat", "DeliveryId" . $i, "");
                            $deliveryId_map[$deliveryId] = $deliveryId_delivery_id;
                        }

                    }

                    $startStatusSearch = false;

                    while (($string = fgets($fp)) !== false) {

                        //$string = iconv('windows-1251','utf-8//IGNORE',$string);

                        if (mb_stripos($string, '<Документ') !== false) {

                            $orderId = 0;
                            $dOrderId = 0;

                            $isOrder = false;
                            $isShipment = false;
                            $hasDelivery = false;

                            $deliverySum = '';


                            $needToSaveOrder = false;
                            $statusValue = '';
                            $deliveryIdValue = '';
                            $trackingNumber = '';
                            $nextTrackingNumber = false;

                            $payment = '';
                            $nextPayment = false;

                            $phone = '';
                            $nextPhone = false;

                            $dayofdelivery = '';
                            $nextDayofdelivery = false;

                            $timeofdeliveryfrom = '';
                            $nextTimeofdeliveryfrom = false;

                            $timeofdeliveryto = '';
                            $nextTimeofdeliveryto = false;

                            $fullName = '';
                            $nextFullName = false;

                            $address = '';
                            $nextAddress = false;

                            $handedCourier = '';
                            $nextHandedCourier = false;

                            $packed = '';
                            $nextPacked = false;

                            $deliveryType = '';
                            $nextDeliveryType = false;

                            $email = '';
                            $nextEmail = false;

                            $priznak = '';
                            $nextPriznak = false;

                            $zonaTarif = '';
                            $nextZonaTarif = false;

                            $sklad = '';
                            $nextSklad = false;

                            $deliveryMethod1C = '';
                            $nextDeliveryMethod1C = false;

                            $deliveryService1C = '';
                            $nextDeliveryService1C = false;

                            $nextShipmentConducted = false;
                            $shipmentConducted = '';

                            $nextShipmentCanceled = false;
                            $shipmentCanceled = '';

                            $nextShipmentDeducted = false;
                            $shipmentDeducted = '';

                            $posDelete = false;

                        }

                        if (empty($orderId)
                            && mb_stripos($string, '<ПометкаУдаления>true</ПометкаУдаления>') !== false) {
                            $posDelete = true;
                        }

                        if (!$isOrder
                            && mb_stripos($string, '<ХозОперация>Заказ товара</ХозОперация>') !== false) {

                            $isOrder = true;

                        }

                        if (!$isShipment
                            && mb_stripos($string, '<ХозОперация>Отпуск товара</ХозОперация>') !== false) {

                            $isShipment = true;

                        }

                        if ($isShipment
                            && preg_match('~<Основание>(.*?)</Основание>~isu', $string, $sMatches)) {

                            if (isset($sMatches[1])
                                && !empty($sMatches[1]))
                                $dOrderId = trim($sMatches[1]);
                        }

                        if ($isShipment
                            && mb_stripos($string, '<Ид>ORDER_DELIVERY</Ид>') !== false) {

                            $hasDelivery = true;

                        }

                        if (empty($orderId)
                            && preg_match('~<Номер>([\d\s]+?)</Номер>~isu', $string, $orderIds)) {

                            $orderId = trim($orderIds[1]);
                        }


                        if ($nextDeliveryIdValue
                            && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextDeliveryIdMatches)) {

                            $deliveryIdValue = trim($nextDeliveryIdMatches[1]);

                        }

                        if ($isOrder
                            && mb_stripos($string, 'Метод доставки ИД') !== false) {
                            $nextDeliveryIdValue = true;
                        } else {
                            $nextDeliveryIdValue = false;
                        }

                        if ($hasDelivery
                            && preg_match('~<Сумма>([\d\s\.\,]+?)</Сумма>~isu', $string, $deliverySumPrice)) {

                            $deliverySum = trim($deliverySumPrice[1]);
                            $deliverySum = str_replace(',', '.', $deliverySum);
                            $deliverySum = (float)$deliverySum;
                            $deliverySum = round($deliverySum, 2);

                        }

                        if (!empty($orderId)) {

                            if (mb_stripos($string, '</ЗначенияРеквизитов>') !== false) {


                                $startStatusSearch = false;

                                //if(!(isset($deliveryId_map[$deliveryIdValue])
                                //&& !empty($deliveryId_map[$deliveryIdValue]))){
                                //$deliveryIdValue = $deliveryType;
                                //}

                                //if(isset($deliveryId_map[$deliveryIdValue])
                                //&& !empty($deliveryId_map[$deliveryIdValue]))

                                if ($isShipment) {

                                    if ($shipmentConducted !== '') {
                                        if ($shipmentConducted == 'true') {
                                            $shipmentAllowDelivery = 'Y';
                                        } else {
                                            $shipmentAllowDelivery = 'N';
                                        }
                                    }

                                    if ($shipmentCanceled !== ''
                                        && $shipmentConducted == 'true') {
                                        $shipmentAllowDelivery = 'N';
                                    }

                                    if ($shipmentDeducted !== '') {

                                        if ($shipmentDeducted == 'true') {
                                            $shipmentDeducted = 'Y';
                                        } else {
                                            $shipmentDeducted = 'N';
                                        }

                                    }

                                    if ($shipmentAllowDelivery !== '' && !$posDelete) {
                                        $ORDERS_1C['shipments'][$orderId]['allowDelivery'] = $shipmentAllowDelivery;
                                    }

                                    if ($shipmentDeducted !== '' && !$posDelete) {
                                        $ORDERS_1C['shipments'][$orderId]['deducted'] = $shipmentDeducted;
                                    }


                                    $nextShipmentConducted = false;
                                    $shipmentConducted = '';

                                    $nextShipmentCanceled = false;
                                    $shipmentCanceled = '';

                                    $nextShipmentDeducted = false;
                                    $shipmentDeducted = '';


                                }

                                if ($isOrder) {

                                    $orderProps = array();

                                    if (!$posDelete) {
                                        $ORDERS_1C['orders'][$orderId]['deliveryIdValue'] = $deliveryIdValue;
                                        $ORDERS_1C['orders'][$orderId]['trackingNumber'] = $trackingNumber;
                                        $ORDERS_1C['orders'][$orderId]['payment'] = $payment;
                                    }

                                    if (!empty($fullName)) {
                                        $orderProps[1] = $fullName;
                                    }

                                    if (!empty($phone)) {
                                        $orderProps[3] = $phone;
                                    }

                                    if (!empty($dayofdelivery)) {
                                        $orderProps[77] = date('d.m.Y G:i:s',strtotime($dayofdelivery));
                                    }

                                    if (!empty($timeofdeliveryfrom)) {
                                        $orderProps[81] = date('d.m.Y G:i:s',strtotime($timeofdeliveryfrom));
                                    }

                                    if (!empty($timeofdeliveryto)) {
                                        $orderProps[83] = date('d.m.Y G:i:s',strtotime($timeofdeliveryto));
                                    }

                                    if (!empty($address)) {
                                        $orderProps[20] = $address;
                                        $orderProps[44] = $address;
                                    }

                                    if (!empty($email)) {
                                        $orderProps[2] = $email;
                                    }

                                    if (!empty($zonaTarif)) {
                                        $orderProps[37] = $zonaTarif;
                                    }

                                    if (!empty($sklad)) {
                                        $orderProps[43] = $sklad;
                                    }

                                    if (!empty($deliveryMethod1C)) {
                                        $orderProps[38] = $deliveryMethod1C;
                                    }

                                    if (!empty($deliveryService1C)) {
                                        $orderProps[39] = $deliveryService1C;
                                    }

                                    $orderProps[40] = ($priznak == 'false' || $priznak == 'Нет' || $priznak == 'N') && $priznak !== '' ? 'Нет' : 'Да';
                                    $orderProps[33] = ($handedCourier == 'false' || $handedCourier == 'Нет' || $handedCourier == 'N') && $handedCourier !== '' ? 'Нет' : 'Да';
                                    $orderProps[32] = ($packed == 'false' || $packed == 'Нет' || $packed == 'N') && $packed !== '' ? 'Нет' : 'Да';

                                    if (!empty($orderProps))
                                        setVariousOrderProperties($orderId, $orderProps);

                                    if (!$posDelete) {
                                        $ORDERS_1C['orders'][$orderId]['statusValue'] = $statusValue;
                                        $ORDERS_1C['orders'][$orderId]['disallowOrder'] = $disallowOrder;
                                        $ORDERS_1C['orders'][$orderId]['disallowValue'] = $disallowValue;
                                    }

                                    $order = \Bitrix\Sale\Order::load($orderId);

                                    if ($order
                                        && $order->isCanceled()
                                        && $statusValue != 'O'
                                        && !($disallowValue
                                            && $disallowOrder)) {

                                        \CSaleOrder::CancelOrder($orderId, "N");
                                        tryToChangeStatusOrder($disallowOrder, $disallowValue, $statusValue, $orderId);

                                    }

                                    //setOrderStatusFrom1C($orderId,$statusValue,$disallowOrder,$disallowValue);

                                    $disallowOrder = false;
                                    $disallowValue = -1;

                                    $nextStatusValue = false;
                                    $statusValue = '';

                                    $trackingNumber = '';
                                    $nextTrackingNumber = false;

                                    $shipmentCost = '';
                                    $nextShipmentCost = false;

                                    $payment = '';
                                    $nextPayment = false;

                                    $phone = '';
                                    $nextPhone = false;

                                    $dayofdelivery = '';
                                    $nextDayofdelivery = false;

                                    $timeofdeliveryfrom = '';
                                    $nextTimeofdeliveryfrom = false;

                                    $timeofdeliveryto = '';
                                    $nextTimeofdeliveryto = false;

                                    $fullName = '';
                                    $nextFullName = false;

                                    $address = '';
                                    $nextAddress = false;

                                    $handedCourier = '';
                                    $nextHandedCourier = false;

                                    $packed = '';
                                    $nextPacked = false;

                                    $deliveryType = '';
                                    $nextDeliveryType = false;

                                    $email = '';
                                    $nextEmail = false;

                                    $priznak = '';
                                    $nextPriznak = false;

                                    $zonaTarif = '';
                                    $nextZonaTarif = false;

                                    $sklad = '';
                                    $nextSklad = false;

                                    $deliveryMethod1C = '';
                                    $nextDeliveryMethod1C = false;

                                    $deliveryService1C = '';
                                    $nextDeliveryService1C = false;

                                    $isOrder = false;
                                }

                            }

                            if (mb_stripos($string, '</Документ>') !== false) {

                                if ($isShipment) {


                                    if (!$posDelete) {
                                        $ORDERS_1C['shipments'][$orderId]['deliverySum'] = $deliverySum;

                                        /* закомментировать здесь start */

                                        if (empty($deliverySum)
                                            && !empty($dOrderId)) {

                                            $ORDERS_1C['shipments'][$orderId]['isWdsNull'] = true;

                                            $dOrderId = (int)$dOrderId;

                                            if ($dOrderId) {

                                                $order = Bitrix\Sale\Order::load($dOrderId);

                                                if ($order) {

                                                    $shipmentCollection = $order->getShipmentCollection();

                                                    if (($shipmentCollection
                                                        && is_object($shipmentCollection))) {

                                                        if (sizeof($shipmentCollection)) {

                                                            foreach ($shipmentCollection as $shipment) {

                                                                $shipmentItemCollection = $shipment->getShipmentItemCollection();
                                                                $emptyShipment = true;

                                                                if (($shipmentItemCollection
                                                                    && is_object($shipmentItemCollection))) {

                                                                    if (sizeof($shipmentItemCollection)) {

                                                                        foreach ($shipmentItemCollection as $item) {

                                                                            $basketItem = $item->getBasketItem();

                                                                            if ($basketItem->getProductId()) {

                                                                                $emptyShipment = false;
                                                                            }

                                                                        }

                                                                    }

                                                                };

                                                                if (!$shipment->isSystem()
                                                                    && !$shipment->getDeliveryId() == 0
                                                                    && !$emptyShipment
                                                                ) {

                                                                    $price = (float)$shipment->getField('PRICE_DELIVERY');

                                                                    if ($price > 0) {

                                                                        $ORDERS_1C['shipments'][$shipment->getId()]['deliverySum'] = $price;
                                                                        $ORDERS_1C['shipments'][$shipment->getId()]['isWdsNull'] = true;

                                                                    }

                                                                }

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                        /* закомментировать здесь end */

                                    }

                                    $posDelete = false;

                                    $disallowOrder = false;
                                    $disallowValue = -1;

                                    $nextStatusValue = false;
                                    $statusValue = '';

                                    $nextDeliveryIdValue = false;

                                    $trackingNumber = '';
                                    $nextTrackingNumber = false;

                                    $shipmentCost = '';
                                    $nextShipmentCost = false;

                                    $payment = '';
                                    $nextPayment = false;

                                    $phone = '';
                                    $nextPhone = false;

                                    $dayofdelivery = '';
                                    $nextDayofdelivery = false;

                                    $timeofdeliveryfrom = '';
                                    $nextTimeofdeliveryfrom = false;

                                    $timeofdeliveryto = '';
                                    $nextTimeofdeliveryto = false;

                                    $fullName = '';
                                    $nextFullName = false;

                                    $address = '';
                                    $nextAddress = false;

                                    $handedCourier = '';
                                    $nextHandedCourier = false;

                                    $packed = '';
                                    $nextPacked = false;

                                    $deliveryType = '';
                                    $nextDeliveryType = false;

                                    $email = '';
                                    $nextEmail = false;

                                    $priznak = '';
                                    $nextPriznak = false;

                                    $zonaTarif = '';
                                    $nextZonaTarif = false;

                                    $sklad = '';
                                    $nextSklad = false;

                                    $deliveryMethod1C = '';
                                    $nextDeliveryMethod1C = false;

                                    $deliveryService1C = '';
                                    $nextDeliveryService1C = false;

                                    $isShipment = false;
                                    $hasDelivery = false;
                                    $deliverySum = '';

                                    $nextShipmentConducted = false;
                                    $shipmentConducted = '';

                                    $nextShipmentCanceled = false;
                                    $shipmentCanceled = '';

                                    $nextShipmentDeducted = false;
                                    $shipmentDeducted = '';

                                }

                                $orderId = 0;
                                $dOrderId = 0;
                                $deliveryIdValue = '';
                                $nextDeliveryIdValue = false;


                            }

                            if (mb_stripos($string, '<ЗначенияРеквизитов') !== false) {

                                $startStatusSearch = true;
                                $disallowOrder = false;

                                $disallowValue = -1;
                                $nextStatusValue = false;

                                $trackingNumber = '';
                                $nextTrackingNumber = false;

                                $shipmentCost = '';
                                $nextShipmentCost = false;

                                $payment = '';
                                $nextPayment = false;

                                $phone = '';
                                $nextPhone = false;

                                $dayofdelivery = '';
                                $nextDayofdelivery = false;

                                $timeofdeliveryfrom = '';
                                $nextTimeofdeliveryfrom = false;

                                $timeofdeliveryto = '';
                                $nextTimeofdeliveryto = false;

                                $fullName = '';
                                $nextFullName = false;

                                $address = '';
                                $nextAddress = false;

                                $handedCourier = '';
                                $nextHandedCourier = false;

                                $packed = '';
                                $nextPacked = false;

                                $deliveryType = '';
                                $nextDeliveryType = false;

                                $email = '';
                                $nextEmail = false;

                                $priznak = '';
                                $nextPriznak = false;

                                $zonaTarif = '';

                                $sklad = '';
                                $nextSklad = false;

                                $deliveryMethod1C = '';
                                $nextDeliveryMethod1C = false;

                                $deliveryService1C = '';
                                $nextDeliveryService1C = false;

                                $nextZonaTarif = false;

                                $nextShipmentConducted = false;
                                $shipmentConducted = '';

                                $nextShipmentCanceled = false;
                                $shipmentCanceled = '';

                                $nextShipmentDeducted = false;
                                $shipmentDeducted = '';


                            } else if ($startStatusSearch) {


                                if ($nextShipmentConducted
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextShipmentConductedMatches)) {

                                    $shipmentConducted = trim($nextShipmentConductedMatches[1]);

                                }

                                if ($nextShipmentCanceled
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextShipmentCanceledMatches)) {

                                    $shipmentCanceled = trim($nextShipmentCanceledMatches[1]);

                                }

                                if ($nextShipmentDeducted
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextShipmentDeductedMatches)) {

                                    $shipmentDeducted = trim($nextShipmentDeductedMatches[1]);

                                }

                                if ($nextZonaTarif
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextZonaTarifMatches)) {

                                    $zonaTarif = trim($nextZonaTarifMatches[1]);

                                }

                                if ($nextSklad
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextSkladMatches)) {

                                    $sklad = trim($nextSkladMatches[1]);

                                }

                                if ($nextPriznak
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextPriznakMatches)) {

                                    $priznak = trim($nextPriznakMatches[1]);

                                }

                                if ($nextStatusValue
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $statusValueMatches)) {

                                    $statusValue = trim($statusValueMatches[1]);

                                }

                                if ($nextTrackingNumber
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextTrackingNumberMatches)) {

                                    $trackingNumber = trim($nextTrackingNumberMatches[1]);

                                }

                                if ($nextShipmentCost
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextShipmentCostMatches)) {

                                    $shipmentCost = trim($nextShipmentCostMatches[1]);

                                }

                                if ($nextPayment
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextPaymentMatches)) {

                                    $payment = trim($nextPaymentMatches[1]);

                                }

                                if ($nextPhone
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextPhoneMatches)) {

                                    $phone = trim($nextPhoneMatches[1]);

                                }

                                if ($nextDayofdelivery
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextDayofdeliveryMatches)) {

                                    $dayofdelivery = trim($nextDayofdeliveryMatches[1]);

                                }


                                if ($nextTimeofdeliveryfrom
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextTimeofdeliveryfromMatches )) {

                                    $timeofdeliveryfrom = trim($nextTimeofdeliveryfromMatches[1]);

                                }

                                if ($nextTimeofdeliveryto
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextTimeofdeliverytoMatches )) {

                                    $timeofdeliveryto = trim($nextTimeofdeliverytoMatches[1]);

                                }

                                if ($nextFullName
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextFullNameMatches)) {

                                    $fullName = trim($nextFullNameMatches[1]);

                                }

                                if ($nextAddress
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextAddressMatches)) {

                                    $address = trim($nextAddressMatches[1]);

                                }

                                if ($nextHandedCourier
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextHandedCourierMatches)) {

                                    $handedCourier = trim($nextHandedCourierMatches[1]);

                                }

                                if ($nextPacked
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextPackedMatches)) {

                                    $packed = trim($nextPackedMatches[1]);

                                }

                                if ($nextEmail
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextEmailMatches)) {

                                    $email = trim($nextEmailMatches[1]);

                                }

                                if ($nextDeliveryType
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextDeliveryTypeMatches)) {

                                    $deliveryType = trim($nextDeliveryTypeMatches[1]);

                                }


                                if ($disallowOrder
                                    && $disallowValue === -1
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $statusValueMatches)) {

                                    $disallowValue = trim($statusValueMatches[1]);
                                    $disallowValue = mb_strtolower($disallowValue) == 'true' ? true : false;

                                }

                                if ($nextDeliveryMethod1C
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $deliveryMethod1CMatches)) {

                                    $deliveryMethod1C = trim($deliveryMethod1CMatches[1]);

                                }

                                if ($nextDeliveryService1C
                                    && preg_match('~<Значение>([^<]+?)</Значение>~isu', $string, $nextDeliveryService1CMatches)) {

                                    $deliveryService1C = trim($nextDeliveryService1CMatches[1]);

                                }

                                if ($isShipment && mb_stripos($string, 'Проведен') !== false) {
                                    $nextShipmentConducted = true;
                                } else {
                                    $nextShipmentConducted = false;
                                }

                                if ($isShipment && mb_stripos($string, 'Отменен') !== false) {
                                    $nextShipmentCanceled = true;
                                } else {
                                    $nextShipmentCanceled = false;
                                }

                                if ($isShipment && mb_stripos($string, 'Отгружен') !== false) {
                                    $nextShipmentDeducted = true;
                                } else {
                                    $nextShipmentDeducted = false;
                                }

                                if ($isOrder && mb_stripos($string, 'Отменен') !== false) {
                                    $disallowOrder = true;
                                } else {
                                    $disallowOrder = false;
                                }

                                if ($isOrder && mb_stripos($string, 'Способ Доставки 1С') !== false) {
                                    $nextDeliveryMethod1C = true;
                                } else {
                                    $nextDeliveryMethod1C = false;
                                }

                                if ($isOrder && mb_stripos($string, 'Служба Доставки 1С') !== false) {
                                    $nextDeliveryService1C = true;
                                } else {
                                    $nextDeliveryService1C = false;
                                }

                                if (mb_stripos($string, 'Email') !== false) {
                                    $nextEmail = true;
                                } else {
                                    $nextEmail = false;
                                }

                                if (mb_stripos($string, 'Признак наложенного платежа') !== false) {
                                    $nextPriznak = true;
                                } else {
                                    $nextPriznak = false;
                                }

                                if (mb_stripos($string, 'Зона/Тариф') !== false) {
                                    $nextZonaTarif = true;
                                } else {
                                    $nextZonaTarif = false;
                                }

                                if (mb_stripos($string, 'Склад<') !== false) {
                                    $nextSklad = true;
                                } else {
                                    $nextSklad = false;
                                }

                                if (mb_stripos($string, 'Статуса заказа ИД') !== false) {
                                    $nextStatusValue = true;
                                } else {
                                    $nextStatusValue = false;
                                }

                                if (mb_stripos($string, 'Трек-номер') !== false) {
                                    $nextTrackingNumber = true;
                                } else {
                                    $nextTrackingNumber = false;
                                }

                                if (mb_stripos($string, 'Стоимость доставки') !== false) {
                                    $nextShipmentCost = true;
                                } else {
                                    $nextShipmentCost = false;
                                }

                                if (mb_stripos($string, 'Метод оплаты ИД') !== false) {
                                    $nextPayment = true;
                                } else {
                                    $nextPayment = false;
                                }

                                if (mb_stripos($string, 'Телефон') !== false) {
                                    $nextPhone = true;
                                } else {
                                    $nextPhone = false;
                                }

                                if (mb_stripos($string, 'Ожидаемая дата вручения') !== false) {
                                    $nextDayofdelivery = true;
                                } else {
                                    $nextDayofdelivery = false;
                                }

                                if (mb_stripos($string, 'Время доставки С') !== false) {
                                    $nextTimeofdeliveryfrom = true;
                                } else {
                                    $nextTimeofdeliveryfrom = false;
                                }


                                if (mb_stripos($string, 'Время доставки По') !== false) {
                                    $nextTimeofdeliveryto = true;
                                } else {
                                    $nextTimeofdeliveryto = false;
                                }

                                if (mb_stripos($string, 'ФИО') !== false) {
                                    $nextFullName = true;
                                } else {
                                    $nextFullName = false;
                                }

                                if (mb_stripos($string, 'Адрес доставки') !== false) {
                                    $nextAddress = true;
                                } else {
                                    $nextAddress = false;
                                }

                                if (mb_stripos($string, 'Передан курьеру') !== false) {
                                    $nextHandedCourier = true;
                                } else {
                                    $nextHandedCourier = false;
                                }

                                if (mb_stripos($string, 'Собран') !== false) {
                                    $nextPacked = true;
                                } else {
                                    $nextPacked = false;
                                }

                                if (mb_stripos($string, 'Способ доставки') !== false) {
                                    $nextDeliveryType = true;
                                } else {
                                    $nextDeliveryType = false;
                                }

                            }


                        }

                    }

                }

                fclose($fp);

            }

        }

    }


}

function setPaymentDeliveryFrom1C($orderId, $deliveryId, $trackingNumber = '', $shipmentCost = 0, $paymentId)
{

    /* global $exchangeDelivery;

    if(!is_array($exchangeDelivery)){
        $exchangeDelivery = array();
    }

    if(!isset($exchangeDelivery[$orderId])){
        $exchangeDelivery[$orderId] = array();
    } */

    $orderId = (int)$orderId;
    $needToSave = false;

    if (!empty($orderId)) {

        $trackingNumber = trim($trackingNumber);
        $shipmentCost = (float)trim($shipmentCost);

        $order = \Bitrix\Sale\Order::load($orderId);
        if (
            $order
            && ($order->getField('STATUS_ID') != 'F')
            //&& !$order->isAllowDelivery()
            //&& !$order->isPaid()
        ) {

            if (($paymentCollection = $order->getPaymentCollection())
                && count($paymentCollection)) {

                $paymentId = (int)trim($paymentId);


                if ($paymentId > 0)
                    foreach ($paymentCollection as $onePayment) {


                        /* if($paymentId != $onePayment->getPaymentSystemId()
                            && !$order->isAllowDelivery()){
                            $onePayment->setPaid("N");
                            $onePayment->setReturn("N");

                            //if($orderId != 40419)
                            $onePayment->delete();

                            $onePayment->save();
                            $needToSave = true;
                        }; */

                        //if($onePayment->getId() != $paymentId)

                        //$onePayment->setPaid("N");
                        //$onePayment->setReturn("N");

                    }


            }

            $propertyCollection = $order->getPropertyCollection();

            if ($propertyCollection) {

                $shipmentDeliveryId = array();

                $shipmentCollection = $order->getShipmentCollection();


                foreach ($shipmentCollection as $shipment) {

                    if (!$shipment->isSystem()) {

                        /*&& if($shipment->getDeliveryId() != $deliveryId
                            !$order->isPaid()){

                            $shipment->setField('ALLOW_DELIVERY','N');
                            $shipment->setField('DEDUCTED','N');
                            $shipment->setField('STATUS_ID','DN');

                            //if($orderId != 40419)
                            $shipment->delete();

                            $shipment->Save();
                            $needToSave = true;

                        } else { */

                        $shipmentDeliveryId[] = $shipment->getDeliveryId();

                        //}

                    }

                }


                if (!empty($deliveryId)
                    && in_array($deliveryId, $shipmentDeliveryId)) {

                    foreach ($shipmentCollection as $shipment) {

                        if (!$shipment->isSystem()) {

                            $shipmentFields = array(
                                'CURRENCY' => $order->getCurrency()
                            );

                            $oldTrackingNumber = $shipment->GetField('TRACKING_NUMBER');

                            if ($oldTrackingNumber != $trackingNumber) {
                                $shipmentFields['TRACKING_NUMBER'] = $trackingNumber;
                                $needToSave = true;
                            }

                            $oldDeliveryPrice = $shipment->GetField('PRICE_DELIVERY');

                            if ((float)$oldDeliveryPrice != (float)$shipmentCost) {

                                $shipmentFields['ALLOW_DELIVERY'] = 'N';
                                $shipmentFields['DEDUCTED'] = 'N';
                                $shipmentFields['STATUS_ID'] = 'DN';


                                //$exchangeDelivery[$orderId][$shipment->getId()] = $shipmentCost;

                                $needToSave = true;

                            }

                            if ($needToSave) {
                                $shipment->setFields($shipmentFields);
                                $shipmentCollection->calculateDelivery();
                            }

                        }

                    }

                }


                if ($needToSave) {

                    needToRecountAndSaveOrder($order);

                }

            }

        }

    }

}

function needToRecountAndSaveOrder($order)
{

    if (($basket = $order->getBasket())) {

        $discount = $order->getDiscount();
        \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
        \Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
        $discount->setOrderRefresh(true);
        $discount->setApplyResult(array());
        $basket->refreshData(array('PRICE', 'COUPONS', 'PRICE_DELIVERY'));
        $discount->calculate();

        if (!$order->isCanceled() && !$order->isPaid()) {

            if (($paymentCollection = $order->getPaymentCollection())
                && count($paymentCollection) == 1) {

                if (($payment = $paymentCollection->rewind())
                    && !$payment->isPaid()) {

                    $payment->setFieldNoDemand('SUM', $order->getPrice());

                }
            }
        }

        $order->save();

    }
}

function setOrderStatusFrom1C($orderId, $statusValue, $disallowOrder, $disallowValue)
{


    if (!empty($orderId)
        && !empty($statusValue)) {

        if ($disallowOrder
            && $disallowValue) {
            $statusValue = 'O';
            CSaleOrder::CancelOrder($orderId, "Y");
        }

        $arOrdFilter = array(
            "ID" => $orderId
        );

        $dbSales = \CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arOrdFilter, false, false, array("*"));

        if ($dbSales && $arSales = $dbSales->Fetch()) {

            if ($statusValue != $arSales['STATUS_ID']
            ) {

                \CSaleOrder::StatusOrder($orderId, $statusValue);

            }

        }

    }

}

function setVariousOrderProperties($orderId, $orderProps)
{

    $orderId = (int)$orderId;

    if (!empty($orderId) && !empty($orderProps)) {

        $order = \Bitrix\Sale\Order::load($orderId);
        if ($order) {

            $propertyCollection = $order->getPropertyCollection();

            if ($propertyCollection) {

                foreach ($orderProps as $propID => $propValue) {

                    $propValueObj = $propertyCollection->getItemByOrderPropertyId($propID);

                    if ($propValueObj) {


                        if ($propValueObj->getValue() != $propValue) {

                            $propValueObj->setValue($propValue);
                            $propValueObj->save();

                        }

                    }

                }

            }

        }

    }

}

AddEventHandler("sender", "OnPostingSendRecipientEmail", "OnPostingSendRecipientEmailHandler");

function OnPostingSendRecipientEmailHandler($eventMailParams)
{


    $eventMailParams['BODY'] = str_ireplace(' width="640"', ' width="100%"', $eventMailParams['BODY']);

    if (preg_match('~<hr class="empty_cart" />~isu', $eventMailParams['BODY'])) {

        return ($error = new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::ERROR,
            new \Bitrix\Sale\ResultError('Корзина в письме пуста', 'SENDER_EVENT_EMPTY_CART'),
            'sender'
        ));
    }


}

function replaceDomainProperty($page_prop) {

    global $APPLICATION;

    $rsProps = CIBlockProperty::GetList(
        array('SORT' => 'ASC', 'NAME' => 'ASC'),
        array('IBLOCK_ID' => 11, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N', 'USER_TYPE' => 'ElementSiteCity')
    );

    $replaces = [];

    if ($rsProps) {
        while ($arProp = $rsProps->Fetch()) {
            $replaces[] = $arProp['CODE'];
        }
    }

    $aDomainProps = unserialize(Bitrix\Main\Config\Option::get('my.stat', "references_domain_props", array()) || "");
    $iDomainPropsSizeof = (isset($aDomainProps['props']) && !empty($aDomainProps['props']))
        ?  count($aDomainProps['props']) : 0;

    $element_id = $APPLICATION->GetPageProperty('element_id','');

    foreach ($replaces as $replace) {

        $replaced = false;

        if (!empty($page_prop) && (preg_match('~'.preg_quote('[['.$replace.']]','~').'~isu',$page_prop) || preg_match('~'.preg_quote('{'.$replace.'}','~').'~isu',$page_prop))) {


            if (!empty($element_id)) {

                $rProp = \CIBlockElement::GetProperty(
                    11,
                    $element_id,
                    array(),
                    array("CODE" => $replace));

                $aValues = [];

                if ($rProp) {
                    while ($aProp = $rProp->GetNext()) {
                        $sValue = isset($aProp['VALUE']) && !empty($aProp['VALUE']) ? $aProp['VALUE'] : '';

                        if (!empty($sValue)) {

                            list($domain,$city) = CIBlockPropertySiteCity::getManValueTxt($sValue);
                            $domain = trim($domain);
                            $city = trim($city).' ';

                            if (!empty($domain) && !empty($city)
                                && $domain == IMPEL_SERVER_NAME
                            ) {

                                $page_prop = preg_replace('~'.preg_quote('[['.$replace.']]','~').'~isu',$city,$page_prop);
                                $replaced = true;

                                break;
                            }

                        }

                    }

                }

            }


            if (!$replaced && (preg_match('~'.preg_quote('[['.$replace.']]','~').'~isu',$page_prop) || preg_match('~'.preg_quote('{'.$replace.'}','~').'~isu',$page_prop))) {

                for($counter = 0; $counter < $iDomainPropsSizeof; $counter++) {

                    if (isset($aDomainProps['props'][$counter])
                        && isset($aDomainProps['props'][$counter])
                        && $aDomainProps['props'][$counter] == $replace
                    ) {

                        $domain = trim($aDomainProps['domains'][$counter]);
                        $city = $aDomainProps['codes'][$counter];

                        if (isset($_REQUEST['test'])) {
                            echo $city.'-'.$domain.'-'.$replace;
                        }

                        if (!empty($domain) && !empty($city)
                            && $domain == IMPEL_SERVER_NAME) {

                            $page_prop = preg_replace('~'.preg_quote('[['.$replace.']]','~').'~isu',$city,$page_prop);
                            $page_prop = preg_replace('~'.preg_quote('{'.$replace.'}','~').'~isu',$city,$page_prop);

                        }

                    }

                }

            }

        }

    }

    foreach ($replaces as $replace) {
        if (preg_match('~'.preg_quote('[['.$replace.']]','~').'~isu',$page_prop)) {
            $page_prop = preg_replace('~'.preg_quote('[['.$replace.']]','~').'~isu','',$page_prop);
        }

        if (preg_match('~'.preg_quote('{'.$replace.'}','~').'~isu',$page_prop)) {
            $page_prop = preg_replace('~'.preg_quote('{'.$replace.'}','~').'~isu','',$page_prop);
        }
    }

    return $page_prop;
}
 

AddEventHandler("main", "OnEndBufferContent", "OnEndBufferContentHandler");

function OnEndBufferContentHandler(&$content)
{
    global $USER, $APPLICATION;

    global $isOnEndBufferContentRunning;

    $isOnEndBufferContentRunning = true;

    checkIfPag404();

    if (defined('CART_UPDATE')) {
        LocalRedirect('/personal/cart/');
    }

    if (mb_stripos($APPLICATION->GetCurDir(), '/bitrix/tools/') === 0)
        return;

    if (mb_stripos($APPLICATION->GetCurDir(), '/bitrix/admin/') === false) {
        $content = replaceDomainProperty($content);
    }

    if (defined('SITE_TEMPLATE_PATH')
        && SITE_TEMPLATE_PATH == '/local/templates/amp') {

        preg_match('~<link[^>]+?rel="canonical"[^>]+?>~isu', $content, $aMatches);

        if (isset($aMatches[0]) && !empty($aMatches[0])) {

            $sublink = $aMatches[0];

            preg_match('~href="([^"]+?)"~isu', $sublink, $aMatches);

            if (isset($aMatches[1]) && !empty($aMatches[1])) {

                $sublink = trim($aMatches[1]);

                if (!empty($sublink) && !headers_sent()) {
                    LocalRedirect($sublink);
                }


            }

        }

        preg_match('~<style amp-custom>(.*?)</style>~isu', $content, $aMatches);

        if (isset($aMatches[1]) && !empty($aMatches[1])) {

            amp_simple_minify_css($aMatches[1]);
            $content = str_ireplace($aMatches[0], '<style amp-custom>' . $aMatches[1] . '</style>', $content);
            $content = preg_replace(array("/(\s)+/si"), array(" "), $content);

        }

    }

    if (defined('SITE_TEMPLATE_PATH')
        && SITE_TEMPLATE_PATH == '/local/templates/npersonal') {

        $arPatternsToRemove = array(
            '~<script(\s+?type="text/javascript"\s*?)*src="[^"]+?jquery-[^"]+?".*?>.*?<\/script>~isu',
            '~IPOL_JSloader\.loadScript(\'[^\']+?jquery-1\.8[^\']+?\',true);~isu',
            '~<link[^>]+?href="[^"]+?ui.font.opensans.min.css[^"]*?"[^>]+>~isu',
            '~<script[^>]+src="[^"]+?api-maps\.yandex\.ru/2\.1/[^"]+?"[^>]+>.*?</script>~isu',
            '~<link\s+?href="[^"]+?popup\.min\.css[^"]*?"[^>]+>~isu',
            '~<link\s+?href="[^"]+?main\.popup\.bundle\.min\.css[^"]*?"[^>]+>~isu',
        );

        $content = preg_replace($arPatternsToRemove, "", $content);

    }

    if (defined('SITE_TEMPLATE_PATH')) {

        if (SITE_TEMPLATE_PATH == '/local/templates/amp') {

            preg_match_all('~<(amp-)*?img([^>]*)>(.*?)</(amp-)*?img>~isu', $content, $aMatches);

            if (isset($aMatches[2])) {

                foreach ($aMatches[2] as $iMatch => $sImageHtml) {

                    preg_match('~\s+?src="([^"]+?)"~isu', $sImageHtml, $asMatches);

                    $sImageHtmlR = $sImageHtml;

                    if (isset($asMatches[1]) && !empty($asMatches[1])) {

                        $sImages = $asMatches[1];

                        if (mb_stripos($sImages, '/bitrix/') === 0 || mb_stripos($sImages, '/upload/') === 0) {

                            $sExt = mb_strtolower(trim(pathinfo($sImages, PATHINFO_EXTENSION)));
                            $sBase = pathinfo($sImages, PATHINFO_FILENAME);
                            $sDir = pathinfo($sImages, PATHINFO_DIRNAME);

                            if (stripos($sDir, 'upload/cache/thumbs') === false) {
                                $imTime = md5(filesize($_SERVER['DOCUMENT_ROOT'] . $sImages));
                                $sBase .= '_' . $imTime;
                            }

                            $sCache = '/upload/webp/' . trim(str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $sDir), '/') . '/';

                            if ($sExt != "webp") {

                                $sWebpPath = $sCache . $sBase . '.webp';

                                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)
                                    || filemtime($_SERVER['DOCUMENT_ROOT'] . $sWebpPath) < 1611864025) {


                                    $sWebpPathDir = '/' . trim(dirname($_SERVER['DOCUMENT_ROOT'] . $sWebpPath), '/') . '/';

                                    if (!file_exists($sWebpPathDir)) {
                                        @mkdir($sWebpPathDir, 0775, true);
                                    }

                                    @passthru('cwebp -q 95 ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sImages) . ' -o ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sWebpPath) . ' -quiet');

                                }

                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {

                                    $sWebpPath = ' src="' . $sWebpPath . '"';
                                    $sImageHtmlR = str_ireplace($asMatches[0], $sWebpPath, $sImageHtmlR);

                                }

                            }

                        }

                    }

                    $sOldImageHtml = trim($aMatches[2][$iMatch]);
                    $sOldImageHtml = preg_replace('~\.webp~isu', '.jpg', $sOldImageHtml);
                    $sOldImageHtml = str_ireplace('/upload/webp/', '/', $sOldImageHtml);

                    $sImageHtmlR = '' . $sImageHtmlR . '>' . '<amp-img fallback ' . $sOldImageHtml . '></amp-img';
                    $content = str_ireplace($sImageHtml, $sImageHtmlR, $content);

                }

            }


        } else {

            preg_match_all('~<img([^>]*)>~isu', $content, $aMatches);

            if (isset($aMatches[1])) {

                foreach ($aMatches[1] as $iMatch => $sImageHtml) {

                    preg_match('~src="([^"]+?)"~isu', $sImageHtml, $asMatches);

                    $sImageHtmlR = $sImageHtml;

                    if (isset($asMatches[1]) && !empty($asMatches[1])) {

                        $sImages = $asMatches[1];

                        if (mb_stripos($sImages, '/bitrix/') === 0 || mb_stripos($sImages, '/upload/') === 0) {

                            $sExt = mb_strtolower(trim(pathinfo($sImages, PATHINFO_EXTENSION)));
                            $sBase = pathinfo($sImages, PATHINFO_FILENAME);
                            $sDir = pathinfo($sImages, PATHINFO_DIRNAME);

                            if (stripos($sDir, 'upload/cache/thumbs') === false) {
                                $imTime = md5(filesize($_SERVER['DOCUMENT_ROOT'] . $sImages));
                                $sBase .= '_' . $imTime;
                            }

                            $sCache = '/upload/webp/' . trim(str_ireplace($_SERVER['DOCUMENT_ROOT'], '', $sDir), '/') . '/';

                            if ($sExt != "webp") {

                                $sWebpPath = $sCache . $sBase . '.webp';

                                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {


                                    $sWebpPathDir = '/' . trim(dirname($_SERVER['DOCUMENT_ROOT'] . $sWebpPath), '/') . '/';

                                    if (!file_exists($sWebpPathDir)) {
                                        @mkdir($sWebpPathDir, 0775, true);
                                    }

                                    @passthru('cwebp -q 95 ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sImages) . ' -o ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sWebpPath) . ' -quiet');


                                }

                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {

                                    $sWebpPath = ' src="' . $sWebpPath . '"';
                                    $sWebpPath .= ' onerror="this.onerror=null; this.src=\'' . $asMatches[1] . '\'"';

                                    $sImageHtmlR = str_ireplace($asMatches[0], $sWebpPath, $sImageHtmlR);

                                }

                            }

                        }

                    }

                    $content = str_ireplace($sImageHtml, $sImageHtmlR, $content);

                }

            }

        }

    }

    global $argv, $argc;

    if (defined('SITE_TEMPLATE_PATH')
        && SITE_TEMPLATE_PATH == '/local/templates/nmain'
        && $USER && !$USER->isAdmin()
        && isset($argc)
        && !empty($argc)
        && (mb_stripos($APPLICATION->GetCurDir(), '/personal/') !== 0)
    ) {

        $content = preg_replace('~<link[^>]*?rel="amphtml"[^>]*?>~isu','',$content);

        $arPatternsToRemove = array(
            '~<link[^>]+?href="[^"]+?ui.font.opensans.min.css[^"]*?"[^>]+>~isu',
            '~<script(\s+?type="text/javascript")*(\s+?data-skip-moving="true")*>.*?<\/script>~isu',
            '~<script[^>]*?src="[^>]*?bitrix\/js\/main\/core\/core[^>]*?"[^>]*?><\/script\>~isu',
            '~<script[^>]*?src="[^>]*?/kernel_main[^>]*?\.js[^>]*?"><\/script\>~isu',
            '~<script[^>]*?src="[^>]*?/loadext[^>]*?\.js\?\d+"><\/script\>~isu',
            '~<link[^>]*?href="[^>]*?/kernel_main[^>]*?\.css[^>]*?"[^>]*>~isu',
            '~<link[^>]*?href="[^>]*?bitrix\/js\/main\/core\/css\/core[^>]*?"[^>]*>~isu',
            '~<style[^>]*?>[^>]*?<\/style>~isu',
            '~<script(\s+?type="text/javascript"\s*?)*src="[^"]+?jquery-1\.8[^"]+?".*?>.*?<\/script>~isu',
            '~<link\s+?href="[^"]+?popup\.min\.css[^"]*?"[^>]+>~isu',
            '~<script(\s+?type="text/javascript"\s*?)*src="[^"]+?main\.popup[^"]+?".*?>.*?<\/script>~isu',
        );

        $filters_preg = \COption::GetOptionString('my.stat', "filters_preg", "", SITE_ID);

        $APPLICATION->SetPageProperty("robots", "noindex, nofollow");

        if (!empty($filters_preg)) {
            $filters_preg = explode("\n", $filters_preg);
            $filters_preg = array_unique($filters_preg);
            $filters_preg = array_map("trim", $filters_preg);
            $filters_preg = array_filter($filters_preg);
            if (!empty($filters_preg)) {
                $bNoIndexNofollow = false;
                $sCurDir = isset($_SERVER['ORIG_REQUEST_URI']) ? $_SERVER['ORIG_REQUEST_URI'] : $_SERVER['REQUEST_URI'];
                $sCurDir = preg_replace('~\?.*?$~is', '', $sCurDir);

                foreach ($filters_preg as $filter_preg) {
                    if (preg_match('~' . $filter_preg . '~is', $sCurDir)) {
                        $bNoIndexNofollow = true;
                        break;
                    }
                }

                if ($bNoIndexNofollow) {
                    $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
                }
            }
        }

        $srParams = 'bx_sender_conversion_id';
        $arParams = explode(';', $srParams);
        $arParams = is_array($arParams) ? $arParams : array($arParams);
        $arKeys = array_keys($_REQUEST);

        if (!defined('NEED_CANONICAL') && (mb_stripos($APPLICATION->GetCurUri(), '?') !== false
                || mb_stripos(isSet($_SERVER['ORIG_REQUEST_URI']) ? $_SERVER['ORIG_REQUEST_URI'] : '', '?') !== false)) {
            $aruPatternsToRemove = array('~previews=[^&]+~isu',
                '~PAGEN_[^&]+?=[^&]+~isu',
                '~BRAND_SMART_FILTER_PATH=[^&]+~isu');

            $sUri = $APPLICATION->GetCurUri();
            $sUri = preg_replace($aruPatternsToRemove, '', $sUri);
            $sUri = trim($sUri, '');
            $sUri = trim($sUri, '?');

            if (stripos($sUri, '?') !== false || preg_match('~\?$~', $_SERVER['ORIG_REQUEST_URI'])) {

                define('NEED_CANONICAL', true);

            }

        }


        if ((defined('NEED_CANONICAL') || (stripos($_SERVER['REQUEST_URI'], '/filter/clear/') !== false) || sizeof(array_intersect($arParams, $arKeys)) > 0)) {

            $stUri = preg_replace('~\?(.*)$~', '', $_SERVER['REQUEST_URI']);
            $stUri = str_ireplace('/filter/clear/', '/', $stUri);

            foreach ($_REQUEST as $sKey => $sVal) {
                if (stripos($sKey, 'PAGEN_') !== false) {

                    $ipNum = preg_replace('#PAGEN_([0-9]+)?=', "$1", $sKey);
                    $ipNum = $ipNum == 1 ? '' : $ipNum;
                    $stUri = rtrim($stUri, '/') . '/pages' . $ipNum . '-' . $sVal . '/';
                    break;

                }
            }

            if (mb_stripos($content, 'canonical') === false) {
                $content = str_ireplace('</head>', '<link rel=canonical href="' . IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $stUri . '"/></head>', $content);
            } else {

                preg_match('~<link[^>]*?canonical[^>]*?>~isu', $content, $acMatches);

                if (isset($acMatches[0]) && !empty($acMatches[0])) {

                    preg_match('~href=[^\"\s>]+~isu', $acMatches[0], $aMatches);

                    if (isset($aMatches[0]) && !empty($aMatches[0])) {

                        $aMatches[0] = trim($aMatches[0]);
                        $aMatches[0] = str_ireplace('href=', '', $aMatches[0]);
                        $scUri = trim($aMatches[0], '"');

                        if (stripos($scUri, '?') !== false) {
                            $scUri = preg_replace('~\?.*$~isu', '', $scUri);
                            $content = str_ireplace($acMatches[0], '<link rel=canonical href="' . $scUri . '"/></head>', $content);
                        }

                    }

                }
            }
        }

        $content = preg_replace($arPatternsToRemove, "", $content);

    }


    if ($USER && !$USER->IsAuthorized()
        && mb_stripos($APPLICATION->GetCurDir(), '/forum/') === 0) {

        $arPatternsToRemove = array(
            '~<form[^>]*?name="REPLIER"[^>]*?>.*?<\/form>~isu',
        );

        $content = preg_replace($arPatternsToRemove, "", $content);

    }

    $isOnEndBufferContentRunning = false;
}

AddEventHandler("main", "OnGetFileSRC", "OnGetFileSRCHandler");

function OnGetFileSRCHandler($arFile)
{

    $upload_dir = COption::GetOptionString("main", "upload_dir", "upload");

    $src = "/" . $upload_dir . "/" . $arFile["SUBDIR"] . "/" . $arFile["FILE_NAME"];

    $src = str_replace("//", "/", $src);

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
        $src = '/bitrix/templates/nmain/images/no_photo.png';
    }

    return $src;

}

function testRedirectSpam()
{
    if (mb_stripos($_SERVER['REQUEST_URI'], '/bitrix/redirect.php') === 0) {

        if (isset($_REQUEST['goto'])
            && !empty($_REQUEST['goto'])) {

            $goto = urldecode($_REQUEST['goto']);
            $goto = sprintf('%s', $goto);
            $url = parse_url($goto, PHP_URL_HOST);

            if ((isset($_REQUEST['internal'])
                    && !empty($_REQUEST['internal'])) || true) {

                if (!(isset($_REQUEST['internal'])
                    && (md5($_REQUEST['goto'] . '~youtwig') == $_REQUEST['internal']))) {

                    if (mb_stripos('youtwig.ru', $url) === false || mb_stripos('twig.su', $url) === false) {

                        header("HTTP/1.0 404 Not Found");
                        die();

                    }

                }

            }


        }

    }

}

function process_redirects($stValue)
{

    global $USER;

    preg_match_all('~(<a.*?href=")([^"]+?)(")~isu', $stValue, $aMatches);


    if (isset($aMatches[2])
        && !empty($aMatches[2])) {

        foreach ($aMatches[2] as $aLink) {

            $sDomain = parse_url($aLink, PHP_URL_HOST);

            if (mb_stripos('youtwig.ru', $sDomain) === false
                || mb_stripos('twig.su', $sDomain) === false) {

                if (mb_stripos($aLink, '/bitrix/redirect.php') !== 0) {
                    $srUri = 'href="/bitrix/redirect.php?goto=' . urlencode($aLink) . '&internal=' . md5($aLink . '~youtwig') . '"';
                    if (mb_stripos($stValue, $srUri) === false)
                        $stValue = str_ireplace('href="' . $aLink . '"', $srUri, $stValue);
                }

            }

        }

    }

    return $stValue;
}

function bshowQuantityRigths()
{
    global $USER;

    $bShow = false;

    if ($USER->IsAuthorized()) {

        $arGroups = CUser::GetUserGroup($USER->GetID());
        $bShow = sizeof(array_intersect(array(6, 1), (array)$arGroups)) ? true : false;

    }

    return $bShow;

}

function getWebpSrc($sImage)
{

    $sReturn = $sImage;
    $sExt = mb_strtolower(trim(pathinfo($sImage, PATHINFO_EXTENSION)));
    $sBase = pathinfo($sImage, PATHINFO_FILENAME);
    $sDir = pathinfo($sImage, PATHINFO_DIRNAME);
    $sCache = '/upload/webp/' . trim($sDir, '/') . '/';

    if ($sExt != "webp") {

        $sWebpPath = $sCache . $sBase . '.webp';

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {


            $sWebpPathDir = '/' . trim(dirname($_SERVER['DOCUMENT_ROOT'] . $sWebpPath), '/') . '/';

            if (!file_exists($sWebpPathDir)) {
                @mkdir($sWebpPathDir, 0775, true);
            }

            @passthru('cwebp -q 95 ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sImage) . ' -o ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . $sWebpPath) . ' -quiet');

        }

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $sWebpPath)) {

            $sReturn = $sWebpPath;

        }

    }

    return $sReturn;
}

function amp_simple_minify_css(&$sOutput)
{
    $sOutput = preg_replace('#/\*.*?\*/#s', '', $sOutput);
    $sOutput = preg_replace('/\s*([{}|:;,])\s+/', '$1', $sOutput);
    $sOutput = preg_replace('/\s\s+(.*)/', '$1', $sOutput);
    $sOutput = str_replace(';}', '}', $sOutput);
}

testRedirectSpam();

AddEventHandler("main", "OnBeforeProlog", "addJqueryFirst");

function addJqueryFirst()
{

    global $APPLICATION;

    if (defined('SITE_TEMPLATE_PATH')
        && SITE_TEMPLATE_PATH == '/local/templates/nmain') {
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/jquery.min.js");

    }

}

AddEventHandler("main", "OnBeforeProlog", "changeCart");

function changeCart()
{
    global $APPLICATION;

    $bInCart = false;
    $bInProvider = false;

    if ($APPLICATION->GetCurPage() == '/personal/cart/') {
        $bInCart = true;
    } else if ($APPLICATION->GetCurPage() == '/personal/provider/') {
        $bInCart = true;
        $bInProvider = true;
    }

    if ($bInCart) {

        fixBasketCountSets();

        $backet = CSaleBasket::GetList(
            false,
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => NULL
            )
        );

        $products = array();

        if (is_object($backet) && method_exists($backet, 'Fetch')) {
            while ($arFields = $backet->Fetch()) {
                if ($arFields
                    && is_array($arFields)
                    && isset($arFields['PRODUCT_ID'])) {

                    $quantity = get_quantity_product($arFields['PRODUCT_ID']);
                    $pquantity = get_quantity_product_provider($arFields['PRODUCT_ID']);
                    $bDelayed = 'N';

                    if ($quantity == $pquantity) {

                        if ($bInProvider) {
                            $bDelayed = 'N';
                        } else {
                            $bDelayed = 'Y';
                        }

                    } else {

                        if ($bInProvider) {
                            $bDelayed = 'Y';
                        } else {
                            $bDelayed = 'N';
                        }
                    }

                    if ($bDelayed != $arFields['DELAY']) {
                        $arFields['DELAY'] = $bDelayed;
                        CSaleBasket::Update($arFields['ID'], $arFields);
                    }

                }

            }

        }

    }

}


if (!class_exists('impelDeliveryInterval')) {
    class impelDeliveryInterval
    {

        const TIME_INTERVAL = array('12:00 - 20:00' => '12 - 20', '12:00 - 16:00' => '12 - 16', '16:00 - 20:00' => '16 - 20');

        static public function getTimeInterval(){
            return static::TIME_INTERVAL;
        }

        static private function isTodayWeekend($date)
        {
            return in_array(date("l", $date), ["Saturday", "Sunday"]);
        }

        static private function getSubset()
        {
            $sOrderWeekends = Bitrix\Main\Config\Option::Get("my.stat", "order_weekends", "");
            $sOrderWeekends = explode("\n", $sOrderWeekends);
            $sOrderWeekends = array_map("trim", $sOrderWeekends);
            $sOrderWeekends = array_unique($sOrderWeekends);
            $sOrderWeekends = array_filter($sOrderWeekends);

            foreach ($sOrderWeekends as $sKey => $sOrderWeekend) {
                $sOrderWeekends[$sKey] = strftime($sOrderWeekend);
            }

            return $sOrderWeekends;
        }

        static public function isTodayDeliveryTime($sday = false)
        {
            $sday = $sday === false ? time() : $sday;
            $sday = is_numeric($sday) ? $sday : strtotime($sday);

            $sHour = trim(strftime('%k', $sday));
            $sMinute = trim(ltrim(strftime('%M', $sday), 0));

            return (($sHour > 8 && $sHour < 16) || ($sHour == 16 && $sMinute < 31));

        }

        static public function isTodayDeliveryDay($sday = false)
        {

            global $USER;

            $sday = $sday === false ? time() : $sday;
            $sday = is_numeric($sday) ? $sday : strtotime($sday);

            $is_weekend = false;

            $subset = static::getSubset();

            $today = strftime('%F', $sday);

            $yestarday = strftime('%F', $sday - 86400);
            $byestarday = strftime('%F', $sday - 2 * 86400);

            if (in_array($today, $subset) || static::isTodayWeekend(strtotime($today))) {
                $is_weekend = true;
            } else {

                if (in_array($yestarday, $subset) && static::isTodayWeekend(strtotime($yestarday))) {
                    $is_weekend = true;
                } else if (in_array($byestarday, $subset) && static::isTodayWeekend(strtotime($byestarday))) {
                    $is_weekend = true;
                }

            }

            return !$is_weekend;

        }

        static public function deliveryDaysInterval($maxDays = 5, $sday = '')
        {

            $days = array();
            $sday = !empty($sday) ? strtotime($sday) : time();

            $subset = static::getSubset();

            $i = 0;

            while ($i < $maxDays) {

                if ($i == 0) {

                    $mTime = ((int)strftime('%H', $sday) > 11) ? ' +1 day' : '';

                    if ($mTime != '') {
                        $maxDays++;
                        $i++;
                        continue;
                    }

                    $today = strtotime(strftime('%F 00:00:00 ' . $mTime, $sday));


                } else {

                    $today = strtotime(strftime('%F 00:00:00  +' . $i . ' day', $sday));

                }

                $cDate = strftime('%F', $today);

                if (in_array($cDate, $subset)) {

                    ++$maxDays;
                    ++$i;

                    if (static::isTodayWeekend($today)) {
                        ++$maxDays;
                        ++$i;
                    }

                } else if (static::isTodayWeekend($today)) {

                    ++$maxDays;
                    ++$i;

                } else {

                    $days[] = strftime('%d.%m.%Y', $today);
                    ++$i;

                }

            }

            return $days;

        }

    }

}

function fixBasketCountSets()
{
    $backet	= CSaleBasket::GetList(
        false,
        array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => NULL
        )
    );

    if(is_object($backet) && method_exists($backet,'Fetch')){
        while($arFields = $backet->Fetch()){
            if($arFields && is_array($arFields) && isset($arFields['ID'])){

                $aFilter = ['NAME' => $arFields['NAME'], 'IBLOCK_ID' => 11];
                $ID = $arFields['ID'];

                $aFilter['NAME'] = $arFields['NAME'];
                $aFilter['IBLOCK_ID'] = 11;

                $dName = CIBlockElement::GetList(
                    array(),
                    ($aFilter),
                    false,
                    false,
                    ($aSelect = array(
                        'ID',
                    ))
                );

                if($dName
                    && $aData = $dName->GetNext()){

                    if(isset($aData['ID'])) {

                        $dSet = CIBlockElement::GetProperty(
                            $aFilter['IBLOCK_ID'],
                            $aData['ID'],
                            array(),
                            array(
                                "CODE" => "SET"
                            )
                        );

                        $bSetValid = $bHaveSet = false;

                        if ($dSet) {

                            $bCanBySet = twigSet::canIByProduct($aData['ID']);

                            while ($aSet = $dSet->fetch()) {
                                if ($aSet && isset($aSet['VALUE'])
                                    && $aSet['VALUE'] > 0
                                ) {

                                    $bHaveSet = true;
                                    $bSetValid = twigSet::canIByProduct($aSet['VALUE']);

                                    if (!$bSetValid) {
                                        break;
                                    }
                                }

                            }

                            if ((!$bSetValid || !$bCanBySet) && $bHaveSet) {
                                CSaleBasket::Delete($ID);
                            }

                        }

                    }

                }

            }

        }

    }

}

class DelayCron {

    private static int $iCount = 0;
    const DELAY_FILE = '/bitrix/tmp/cron_count.txt';

    private static function getCount ():int {
        return (int)file_get_contents($_SERVER['DOCUMENT_ROOT'].self::DELAY_FILE);
    }

    private static function setCount (int $iCount):void {
        $iCount = $iCount < 0 ? 0 : $iCount;
        file_put_contents($_SERVER['DOCUMENT_ROOT'].self::DELAY_FILE,$iCount);
    }

    private static function test():bool {
        global $argv;
        return isset($argv) && is_array($argv) && sizeof($argv) > 0 ? true : false;
    }

    public static function execute():void {

        if (static::test()) {

            static::$iCount = static::getCount();
            ++static::$iCount;
            static::setCount(static::$iCount);

            $iDelay = mt_rand(32,91) * 10 + (static::$iCount - 1) * 1000;
            usleep($iDelay);

        }

    }

    public static function stop():void {

        if (static::test()) {

            static::$iCount = static::getCount();
            --static::$iCount;
            static::setCount(static::$iCount);

        }

    }

}

register_shutdown_function('end_buffer');

function end_buffer() {
    global $argv, $argc;

    DelayCron::stop();

}

function checkIfDoubles() {

    global $argv, $argc;

    DelayCron::execute();

    $sUri = trim($_SERVER['REQUEST_URI']);

    $aUri = explode('/',$sUri);
    if(is_array($aUri)){
        $aUri = array_filter($aUri);
    }
    $aUri = array_map('trim',$aUri);

    $aCounts = array_count_values($aUri);

    $sLast = end($aUri);

    $iMax = $aCounts ? max($aCounts) : 0;

    if ($iMax > 1
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET') {

        $aUri = array_unique($aUri);
        $aUri = array_slice($aUri,0,-1);

        $sUri = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/'.trim(join('/',$aUri),'/').'/';

        if (preg_match('~PAGEN_([0-9]+)=([0-9]+)~isu',$sLast,$aMatches)) {
            $sUri .= 'pages'.($aMatches[1] > 1 ? $aMatches[1] : '').'-'.$aMatches[2].'/';
        }

        $sLast = preg_replace('~[&]*PAGEN_([0-9]+)=([0-9]+)~isu','',$sLast);
        $sLast = $sLast == '?' ? '' : $sLast;
        $sUri .= $sLast;

        $sCode = getURIContent($sUri, "get", "", "", true);

        if ($sCode != 200) {
            if (!headers_sent()) {
                header("HTTP/1.0 404 Not Found");
            }
            define('ERROR_404', 'Y');
        } else {
            if (!headers_sent()) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$sUri);
            } else {
                LocalRedirect($sUri);
            }
        }
    }
}

checkIfDoubles();

function checkIfPag404() {

    global $APPLICATION;

    $sUri = trim($_SERVER['REQUEST_URI']);

    if (preg_match('~(/model/[^>/]+?/)[0-9]+/~isu',$sUri,$aMatches)
        && defined('ERROR_404')
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET'
    ) {
        $sUriLast = trim($aMatches[1]);

        if ($sUriLast != $sUri) {
            LocalRedirect($sUriLast);
        }
    }


    if (stripos($sUri,'/catalog/akcii/') !== false
        && defined('ERROR_404')
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET'
    ) {

        $aUri = explode('/',$sUri);
        $aUri = array_map('trim',$aUri);
        if(is_array($aUri)){
            $aUri = array_filter($aUri);
        }
        $sCode = end($aUri);

        if (!empty($sCode) && $sCode != 'akcii') {
            $bRes = CIBlockElement::GetList([], ['CODE' => $sCode, 'IBLOCK_ID' => 11, 'ACTIVE' => 'Y'], false, ['nTopCount' => 1], ['ID','DETAIL_PAGE_URL']);

            if ($bRes && $aRes = $bRes->GetNext()) {

                if (isset($aRes['DETAIL_PAGE_URL']) && !empty($aRes['DETAIL_PAGE_URL'])) {

                    $redirect = trim($aRes['DETAIL_PAGE_URL']);
                    LocalRedirect($redirect);

                }

            }
        }

    }

    if (stripos($sUri,'/filter/') !== false
        && (defined('ERROR_404')
        || \CHTTP::GetLastStatus() == '404 Not Found')
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET'
    ) {
        if (strpos($sUri, "-or-") > 0) {
            $temp = array();
            $uriParmas = explode('/', $sUri);
            if(is_array($uriParams)) {
                $temp = array_map(function($item) {
                    if(strpos($item, "-or-")){
                        return explode('-or-', $item)[0];
                    } else{
                        return $item;
                    }
                }, $uriParams);
            }

            if(is_array($temp)) {
                $sUri =  join('/', $temp);
            }

        } else {
            $sUri = preg_replace('/\/[^\/]+\/$/', '/', $sUri);
        }

        var_dump("functions => sUri", $sUri);
        
        if($sUri != '' && strpos($sUri, '/filter/') > 0) {
            LocalRedirect($sUri);
        }
    }

    if (stripos($sUri,'/amp/') !== false
        && defined('ERROR_404')
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET'
    ) {
        LocalRedirect('/');
    }

    $aUri = explode('/',$sUri);
    if(is_array($aUri)){
        $aUri = array_filter($aUri);
    }
    $aUri = array_map('trim',$aUri);

    $sLast = end($aUri);



    if ((preg_match('~PAGEN_([0-9]+)=([0-9]+)~isu',$sLast,$aMatches)
            || preg_match('~(pages\-)([0-9]+)~isu',$sLast,$aMatches))
        && defined('ERROR_404')
        && isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] == 'GET'
    ) {

        $aUri = array_unique($aUri);
        $aUri = array_slice($aUri,0,-1);

        $sUri = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/'.trim(join('/',$aUri),'/').'/';

        if (preg_match('~PAGEN_([0-9]+)=([0-9]+)~isu',$sLast,$aMatches)) {
            $sLast = preg_replace('~[&]*PAGEN_([0-9]+)=([0-9]+)~isu','',$sLast);
            $sLast = $sLast == '?' ? '' : $sLast;
        } else {
            $sLast = '';
        }

        $sUri .= $sLast;

        $sCode = getURIContent($sUri, "get", "", "", true);

        if ($sCode != 200) {
            if (!headers_sent()) {
                header("HTTP/1.0 404 Not Found");
            }
            define('ERROR_404', 'Y');
        } else {
            if (!headers_sent()) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$sUri);
            } else {
                LocalRedirect($sUri);
            }
        }

    }


}


function setEltPropertyValuesEx(
    $ELEMENT_ID,
    $IBLOCK_ID,
    $PROPERTY_VALUES,
    $FLAGS = array()){

    if ($IBLOCK_ID == 11) {

        foreach ($PROPERTY_VALUES as $sCode => $mValues) {

            $bChanged = checkEltPropertyChange($mValues,$sCode,$ELEMENT_ID,$IBLOCK_ID);

            if ($bChanged) {

                $mValues = is_array($mValues) && sizeof($mValues) == 0 ? false : $mValues;

                impelCIBlockElement::SetPropertyValuesEx(
                    $ELEMENT_ID,
                    $IBLOCK_ID,
                    [$sCode => $mValues]
                );

                $ipRes = CIBlockElement::GetList([],['ID' => $ELEMENT_ID, 'IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'],[],false,['ID']);

                if ($ipRes) {
                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ELEMENT_ID);
                }

            }

        }

    } else {

        impelCIBlockElement::SetPropertyValuesEx(
            $ELEMENT_ID,
            $IBLOCK_ID,
            $PROPERTY_VALUES,
            $FLAGS
        );

    }

}

function checkEltPropertyChange($mValues,$sCode,$iEltId,$iBlockId){
    $aValues = getEltProperty($sCode,$iEltId,$iBlockId);
    return (array)$aValues == (array)$mValues ? false : true;
}


function getEltProperty($sCode,$iEltId,$iBlockId){

    $aValues = [];

    $aFilter = Array("CODE" => $sCode);
    $rPropDB = impelCIBlockElement::GetProperty(
        $iBlockId,
        $iEltId,
        array(),
        $aFilter
    );


    if ($rPropDB) {

        while ($aProp = $rPropDB->GetNext()) {

            $aValues[] = trim($aProp['VALUE']);
        }
    }

    return $aValues;
}

function getJsonProperty($iEltId){
    $hashFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/json/'.$iEltId.'.txt';
    $content = '';
    if (file_exists($hashFile)) {
        $content = file_get_contents($hashFile);
    }
    return $content;
}

function getMinMaxPrice()
{

    $json = ['min_price' => 0, 'max_price' => 0];

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/price_range.txt')) {
        $json = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/price_range.txt') || "");
    }

    define('min_price', (int)$json['min_price']);
    define('max_price', (int)$json['max_price']);
}

getMinMaxPrice();


function get_user_id_by_phone ($phone) {

    $userId = false;

    $rsUsers = \Bitrix\Main\UserPhoneAuthTable::getList([
        'filter' => ['=PHONE_NUMBER' => $phone ],
        'select' => ['USER_ID', 'PHONE_NUMBER'],
        'order' => ['USER_ID' => 'DESC']
    ]);

    if ($rsUsers && ($arUser = $rsUsers->Fetch())) {
        $userId = $arUser['USER_ID'] ?? false;
    }

    return $userId;
}

function send_sms($phone,$smsCode = '') {
    global $APPLICATION, $USER;

    $return = false;

    $phone = preg_replace('~[^0-9\+]+~','',$phone);
    $phone = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($phone);

    if (empty($phone)) {
        $APPLICATION->throwException("Проверьте номер телефона.");
        $_SESSION['USER_PHONE'] =  '';
        return false;
    } else {
        $_SESSION['USER_PHONE'] =  $phone;
    }

    $userId = get_user_id_by_phone($phone);

    if (!$userId) {
        $_SESSION['USER_PHONE'] =  '';
        $APPLICATION->throwException("Ваш телефон не найден. Пожалуйста, оформите новый заказ, и Вы будете зарегистрированы.");
        return false;
    }

    if ($userId && $smsCode) {

        $smsCode = trim($smsCode);

        if (!empty($smsCode)) {

            $phoneRecord = \Bitrix\Main\UserPhoneAuthTable::getList([
                'filter' => [
                    '=USER_ID' => $userId
                ],
                'select' => ['USER_ID', 'PHONE_NUMBER', 'USER.ID', 'USER.ACTIVE'],
            ])->fetchObject();

            if(\CUser::VerifyPhoneCode($phoneRecord->getPhoneNumber(), $smsCode)) {
                if($phoneRecord->getUser()->getActive() && !$USER->IsAuthorized()) {
                    $APPLICATION->throwException("Пользователь успешно авторизирован.");
                    $USER->Authorize($userId);
                    unset($_SESSION['USER_PHONE']);
                    LocalRedirect('/personal/');
                }

            }


        }

    }

    $APPLICATION->throwException("Отправьте смс еще раз");
    return false;
}

function resizeImageGD($image, $width, $height, $output)
{
    if (!file_exists($image) || !is_readable($image)) {
        return false;
    }

    list($original_width, $original_height) = getimagesize($image);

    $new_image = imagecreatetruecolor($width, $height);
    $image_extension = pathinfo($image, PATHINFO_EXTENSION);

    switch ($image_extension) {
        case 'jpeg':
        case 'jpg':
            $source = imagecreatefromjpeg($image);
            break;
        case 'png':
            $source = imagecreatefrompng($image);
            break;
        case 'gif':
            $source = imagecreatefromgif($image);
            break;
        default:
            return false;
    }

    // Пропорциональное изменение размера
    $ratio = min($width / $original_width, $height / $original_height);
    $new_width = intval($original_width * $ratio);
    $new_height = intval($original_height * $ratio);
    $x_offset = intval(($width - $new_width) / 2);
    $y_offset = intval(($height - $new_height) / 2);

    // Заливка фоном
    $background_color = imagecolorallocate($new_image, 255, 255, 255);
    imagefill($new_image, 0, 0, $background_color);

    imagecopyresampled($new_image, $source, $x_offset, $y_offset, 0, 0, $new_width, $new_height, $original_width, $original_height);

    switch ($image_extension) {
        case 'jpeg':
        case 'jpg':
            imagejpeg($new_image, $output, 98);
            break;
        case 'png':
            imagepng($new_image, $output);
            break;
        case 'gif':
            imagegif($new_image, $output);
            break;
    }

    imagedestroy($new_image);
    imagedestroy($source);

    return $output;
}



function get_sms_code ($phone) {

    global $APPLICATION, $USER;

    $try = isset($_SESSION['sms_try']) ? $_SESSION['sms_try'] : false;

    if ($try && ($try > time())) {
        $APPLICATION->throwException(sprintf("Повторите попытку через %s секунд.",($try - time())));
        return false;
    }

    unset($_SESSION['sms_try']);

    $return = false;

    $phone = preg_replace('~[^0-9\+]+~','',$phone);
    $phone = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($phone);

    if (empty($phone)) {
        $APPLICATION->throwException("Проверьте номер телефона.");
        $_SESSION['USER_PHONE'] =  '';
        return false;
    } else {
        $_SESSION['USER_PHONE'] =  $phone;
    }

    $userId = get_user_id_by_phone($phone);

    if (!$userId) {
        $_SESSION['USER_PHONE'] =  '';
        $APPLICATION->throwException("Ваш телефон не найден. Пожалуйста, оформите новый заказ, и Вы будете зарегистрированы.");
        return false;
    }

    if ($userId) {

        \Bitrix\Main\UserPhoneAuthTable::add([
            'USER_ID' => $userId,
            'PHONE_NUMBER' => $phone,
        ]);

        list($smsCode, $phoneNumber) = \CUser::GeneratePhoneCode($userId);

        if ($smsCode && $phoneNumber) {

            $sms = new \Bitrix\Main\Sms\Event(
                'SMS_USER_CONFIRM_NUMBER', // SMS_USER_RESTORE_PASSWORD - для восстановления
                [
                    'USER_PHONE' => $phoneNumber,
                    'CODE' => $smsCode,
                ]
            );

            require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/mainpage.php");

            $site_id = \CMainPage::GetSiteByHost();

            $sms->setSite($site_id);
            $sms->setLanguage(LANGUAGE_ID);

            $_SESSION['sms_try'] = time() + SMS_TRY;

            $return = $sms->send();
            $_SESSION['USER_PHONE'] =  $phone;

            if ($return) {
                $APPLICATION->throwException("Проверочный код успешно отправлен.");
                return false;
            } else {
                $APPLICATION->throwException("Ошибка с отправкой смс. Попробуйте позже.");
                return false;
            }

        }

    }

    $APPLICATION->throwException("Отправьте смс еще раз");
    return false;
}

function check_sms() {

    if (isset($_REQUEST['USER_PHONE'])
        && !empty($_REQUEST['USER_PHONE'])
        && isset($_REQUEST['Login'])
        && $_REQUEST['Login'] == 'send'
    ) {
        return get_sms_code($_REQUEST['USER_PHONE']);
    }

    if (isset($_REQUEST['USER_PHONE'])
        && !empty($_REQUEST['USER_PHONE'])
        && isset($_REQUEST['Login'])
        && $_REQUEST['Login'] == 'confirm'
        && isset($_REQUEST['USER_CONFIRM'])
        && !empty($_REQUEST['USER_CONFIRM'])
    ) {
        return send_sms($_REQUEST['USER_PHONE'],$_REQUEST['USER_CONFIRM']);
    }

}

AddEventHandler("main", "OnBeforeUserLogin", "check_sms");

// Функция обработки текста
function processText($strValue, $allowedWords) {
    // Используем регулярное выражение для поиска латинских слов с возможными прилегающими символами
    return preg_replace_callback('/(\S*?[a-zA-Z]+\S*?)/u', function($matches) use ($allowedWords) {
        // Убираем символы, если они идут перед или после слова
        $cleanedWord = trim($matches[0], " \t\n\r\0\x0B!@#$%^&*()_+-=,.<>?/\\|[]{}:;\"'`~");

        // Проверяем длину слова, и если оно меньше 3 символов, возвращаем без проверки
        if (strlen($cleanedWord) < 3) {
            return $matches[0];
        }

        // Проверяем, есть ли очищенное слово (независимо от регистра) в массиве разрешенных
        return in_array(strtolower($cleanedWord), $allowedWords) ? $matches[0] : '';
    }, $strValue);
}
