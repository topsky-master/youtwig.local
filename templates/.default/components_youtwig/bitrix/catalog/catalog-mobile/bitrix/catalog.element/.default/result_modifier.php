<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$arSFilter = Array(
    'IBLOCK_ID' => $arResult['IBLOCK_ID']);

if(isset($arParams['SECTION_CODE'])
    && !empty($arParams['SECTION_CODE'])){

    $arSFilter['CODE'] = $arParams['SECTION_CODE'];

} else if(isset($arParams['SECTION_ID'])
    && !empty($arParams['SECTION_ID'])){

    $arSFilter['ID'] = $arParams['SECTION_ID'];

}

$rsSection = CIBlockSection::GetList(Array(), $arSFilter, false, array('SECTION_PAGE_URL','NAME'));
$rsSection->SetUrlTemplates("", $arResult["SECTION_URL"]);

$arResult['SECTION_PAGE_URL'] = '';
$arResult['SECTION_NAME'] = '';

if($rsSection){

    $arSection = $rsSection->GetNext();
    $arResult['SECTION_PAGE_URL'] = $arSection['SECTION_PAGE_URL'];
    $arResult['SECTION_NAME'] = $arSection['NAME'];
}

$cacheTime = 360000000;

$displayPreviewTextMode = array(
    'H' => true,
    'E' => true,
    'S' => true
);
$detailPictMode = array(
    'IMG' => true,
    'POPUP' => true,
    'MAGNIFIER' => true,
    'GALLERY' => true
);

$arDefaultParams = array(
    'TEMPLATE_THEME' => 'blue',
    'ADD_PICT_PROP' => '-',
    'LABEL_PROP' => '-',
    'OFFER_ADD_PICT_PROP' => '-',
    'OFFER_TREE_PROPS' => array('-'),
    'DISPLAY_NAME' => 'Y',
    'DETAIL_PICTURE_MODE' => 'IMG',
    'ADD_DETAIL_TO_SLIDER' => 'N',
    'DISPLAY_PREVIEW_TEXT_MODE' => 'E',
    'PRODUCT_SUBSCRIPTION' => 'N',
    'SHOW_DISCOUNT_PERCENT' => 'N',
    'SHOW_OLD_PRICE' => 'N',
    'SHOW_MAX_QUANTITY' => 'N',
    'SHOW_BASIS_PRICE' => 'N',
    'ADD_TO_BASKET_ACTION' => array('BUY'),
    'SHOW_CLOSE_POPUP' => 'N',
    'MESS_BTN_BUY' => '',
    'MESS_BTN_ADD_TO_BASKET' => '',
    'MESS_BTN_SUBSCRIBE' => '',
    'MESS_BTN_COMPARE' => '',
    'MESS_NOT_AVAILABLE' => '',
    'USE_VOTE_RATING' => 'N',
    'VOTE_DISPLAY_AS_RATING' => 'rating',
    'USE_COMMENTS' => 'N',
    'BLOG_USE' => 'N',
    'BLOG_URL' => 'catalog_comments',
    'BLOG_EMAIL_NOTIFY' => 'N',
    'VK_USE' => 'N',
    'VK_API_ID' => '',
    'FB_USE' => 'N',
    'FB_APP_ID' => '',
    'BRAND_USE' => 'N',
    'BRAND_PROP_CODE' => ''
);
$arParams = array_merge($arDefaultParams, $arParams);

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
    $arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
    if ('site' == $arParams['TEMPLATE_THEME'])
    {
        $templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
        $templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
        $arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
    }
    if ('' != $arParams['TEMPLATE_THEME'])
    {
        if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
            $arParams['TEMPLATE_THEME'] = '';
    }
}
if ('' == $arParams['TEMPLATE_THEME'])
    $arParams['TEMPLATE_THEME'] = 'blue';

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
    $arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
    $arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
    $arParams['OFFER_ADD_PICT_PROP'] = '';
if (!is_array($arParams['OFFER_TREE_PROPS']))
    $arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
{
    $value = (string)$value;
    if ('' == $value || '-' == $value)
        unset($arParams['OFFER_TREE_PROPS'][$key]);
}
if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']))
{
    $arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
    foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
    {
        $value = (string)$value;
        if ('' == $value || '-' == $value)
            unset($arParams['OFFER_TREE_PROPS'][$key]);
    }
}
if ('N' != $arParams['DISPLAY_NAME'])
    $arParams['DISPLAY_NAME'] = 'Y';
if (!isset($detailPictMode[$arParams['DETAIL_PICTURE_MODE']]))
    $arParams['DETAIL_PICTURE_MODE'] = 'IMG';
if ('Y' != $arParams['ADD_DETAIL_TO_SLIDER'])
    $arParams['ADD_DETAIL_TO_SLIDER'] = 'N';
if (!isset($displayPreviewTextMode[$arParams['DISPLAY_PREVIEW_TEXT_MODE']]))
    $arParams['DISPLAY_PREVIEW_TEXT_MODE'] = 'E';
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
    $arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
    $arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
    $arParams['SHOW_OLD_PRICE'] = 'N';
if ('Y' != $arParams['SHOW_MAX_QUANTITY'])
    $arParams['SHOW_MAX_QUANTITY'] = 'N';
if ($arParams['SHOW_BASIS_PRICE'] != 'Y')
    $arParams['SHOW_BASIS_PRICE'] = 'N';
if (!is_array($arParams['ADD_TO_BASKET_ACTION']))
    $arParams['ADD_TO_BASKET_ACTION'] = array($arParams['ADD_TO_BASKET_ACTION']);
$arParams['ADD_TO_BASKET_ACTION'] = array_filter($arParams['ADD_TO_BASKET_ACTION'], 'CIBlockParameters::checkParamValues');
if (empty($arParams['ADD_TO_BASKET_ACTION']) || (!in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']) && !in_array('BUY', $arParams['ADD_TO_BASKET_ACTION'])))
    $arParams['ADD_TO_BASKET_ACTION'] = array('BUY');
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y')
    $arParams['SHOW_CLOSE_POPUP'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ('Y' != $arParams['USE_VOTE_RATING'])
    $arParams['USE_VOTE_RATING'] = 'N';
if ('vote_avg' != $arParams['VOTE_DISPLAY_AS_RATING'])
    $arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
if ('Y' != $arParams['USE_COMMENTS'])
    $arParams['USE_COMMENTS'] = 'N';
if ('Y' != $arParams['BLOG_USE'])
    $arParams['BLOG_USE'] = 'N';
if ('Y' != $arParams['VK_USE'])
    $arParams['VK_USE'] = 'N';
if ('Y' != $arParams['FB_USE'])
    $arParams['FB_USE'] = 'N';
if ('Y' == $arParams['USE_COMMENTS'])
{
    if ('N' == $arParams['BLOG_USE'] && 'N' == $arParams['VK_USE'] && 'N' == $arParams['FB_USE'])
        $arParams['USE_COMMENTS'] = 'N';
}
if ('Y' != $arParams['BRAND_USE'])
    $arParams['BRAND_USE'] = 'N';
if ($arParams['BRAND_PROP_CODE'] == '')
    $arParams['BRAND_PROP_CODE'] = array();
if (!is_array($arParams['BRAND_PROP_CODE']))
    $arParams['BRAND_PROP_CODE'] = array($arParams['BRAND_PROP_CODE']);

$arEmptyPreview = false;
$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
{
    $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
    if (!empty($arSizes))
    {
        $arEmptyPreview = array(
            'SRC' => $strEmptyPreview,
            'WIDTH' => (int)$arSizes[0],
            'HEIGHT' => (int)$arSizes[1]
        );
    }
    unset($arSizes);
}
unset($strEmptyPreview);

$arSKUPropList = array();
$arSKUPropIDs = array();
$arSKUPropKeys = array();
$boolSKU = false;
$strBaseCurrency = '';
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

if ($arResult['MODULES']['catalog'])
{
    if (!$boolConvert)
        $strBaseCurrency = CCurrency::GetBaseCurrency();

    $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
    $boolSKU = !empty($arSKU) && is_array($arSKU);

    if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']))
    {
        $arSKUPropList = CIBlockPriceTools::getTreeProperties(
            $arSKU,
            $arParams['OFFER_TREE_PROPS'],
            array(
                'PICT' => $arEmptyPreview,
                'NAME' => '-'
            )
        );
        $arSKUPropIDs = array_keys($arSKUPropList);
    }
}

$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
    $arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
    $arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
    ? (float)$arResult['CATALOG_QUANTITY']
    : (int)$arResult['CATALOG_QUANTITY']
);
$arResult['CATALOG'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
    $arResult['CATALOG_SUBSCRIPTION'] = 'N';

CIBlockPriceTools::getLabel($arResult, $arParams['LABEL_PROP']);

$productSlider = CIBlockPriceTools::getSliderForItem($arResult, $arParams['ADD_PICT_PROP'], 'Y' == $arParams['ADD_DETAIL_TO_SLIDER']);
if (empty($productSlider))
{
    $productSlider = array(
        0 => $arEmptyPreview
    );
}
$productSliderCount = count($productSlider);
$arResult['SHOW_SLIDER'] = true;
$arResult['MORE_PHOTO'] = $productSlider;
$arResult['MORE_PHOTO_COUNT'] = count($productSlider);

if ($arResult['MODULES']['catalog'])
{
    $arResult['CATALOG'] = true;
    if (!isset($arResult['CATALOG_TYPE']))
        $arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
    if (
        (CCatalogProduct::TYPE_PRODUCT == $arResult['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['CATALOG_TYPE'])
        && !empty($arResult['OFFERS'])
    )
    {
        $arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
    }
    switch ($arResult['CATALOG_TYPE'])
    {
        case CCatalogProduct::TYPE_SET:
            $arResult['OFFERS'] = array();
            //$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
            $arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE']);


            break;
        case CCatalogProduct::TYPE_SKU:
            break;
        case CCatalogProduct::TYPE_PRODUCT:
        default:
            //$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
            $arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE']);

            break;
    }
}
else
{
    $arResult['CATALOG_TYPE'] = 0;
    $arResult['OFFERS'] = array();
}

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
    $boolSKUDisplayProps = false;

    $arResultSKUPropIDs = array();
    $arFilterProp = array();
    $arNeedValues = array();
    foreach ($arResult['OFFERS'] as &$arOffer)
    {
        foreach ($arSKUPropIDs as &$strOneCode)
        {
            if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
            {
                $arResultSKUPropIDs[$strOneCode] = true;
                if (!isset($arNeedValues[$arSKUPropList[$strOneCode]['ID']]))
                    $arNeedValues[$arSKUPropList[$strOneCode]['ID']] = array();
                $valueId = (
                $arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST
                    ? $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']
                    : $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']
                );
                $arNeedValues[$arSKUPropList[$strOneCode]['ID']][$valueId] = $valueId;
                unset($valueId);
                if (!isset($arFilterProp[$strOneCode]))
                    $arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
            }
        }
        unset($strOneCode);
    }
    unset($arOffer);

    CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
    $arSKUPropIDs = array_keys($arSKUPropList);
    $arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);


    $arMatrixFields = $arSKUPropKeys;
    $arMatrix = array();

    $arNewOffers = array();

    $arIDS = array($arResult['ID']);
    $arOfferSet = array();
    $arResult['OFFER_GROUP'] = false;
    $arResult['OFFERS_PROP'] = false;

    $arDouble = array();
    foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
    {
        $arOffer['ID'] = (int)$arOffer['ID'];
        if (isset($arDouble[$arOffer['ID']]))
            continue;
        $arIDS[] = $arOffer['ID'];
        $boolSKUDisplayProperties = false;
        $arOffer['OFFER_GROUP'] = false;
        $arRow = array();
        foreach ($arSKUPropIDs as $propkey => $strOneCode)
        {
            $arCell = array(
                'VALUE' => 0,
                'SORT' => PHP_INT_MAX,
                'NA' => true
            );
            if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
            {
                $arMatrixFields[$strOneCode] = true;
                $arCell['NA'] = false;
                if ('directory' == $arSKUPropList[$strOneCode]['USER_TYPE'])
                {
                    $intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
                    $arCell['VALUE'] = $intValue;
                }
                elseif ('L' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
                {
                    $arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID'];
                }
                elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
                {
                    $arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE'];
                }
                $arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
            }
            $arRow[$strOneCode] = $arCell;
        }
        $arMatrix[$keyOffer] = $arRow;

        CIBlockPriceTools::setRatioMinPrice($arOffer, false);

        $arOffer['MORE_PHOTO'] = array();
        $arOffer['MORE_PHOTO_COUNT'] = 0;
        $offerSlider = CIBlockPriceTools::getSliderForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP'], $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y');
        if (empty($offerSlider))
        {
            $offerSlider = $productSlider;
        }
        $arOffer['MORE_PHOTO'] = $offerSlider;
        $arOffer['MORE_PHOTO_COUNT'] = count($offerSlider);

        if (CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']))
        {
            $boolSKUDisplayProps = true;
        }

        $arDouble[$arOffer['ID']] = true;
        $arNewOffers[$keyOffer] = $arOffer;
    }
    $arResult['OFFERS'] = $arNewOffers;
    $arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

    $arUsedFields = array();
    $arSortFields = array();

    foreach ($arSKUPropIDs as $propkey => $strOneCode)
    {
        $boolExist = $arMatrixFields[$strOneCode];
        foreach ($arMatrix as $keyOffer => $arRow)
        {
            if ($boolExist)
            {
                if (!isset($arResult['OFFERS'][$keyOffer]['TREE']))
                    $arResult['OFFERS'][$keyOffer]['TREE'] = array();
                $arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
                $arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
                $arUsedFields[$strOneCode] = true;
                $arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
            }
            else
            {
                unset($arMatrix[$keyOffer][$strOneCode]);
            }
        }
    }
    $arResult['OFFERS_PROP'] = $arUsedFields;
    $arResult['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

    Collection::sortByColumn($arResult['OFFERS'], $arSortFields);

    $offerSet = array();
    if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet'))
    {
        $offerSet = array_fill_keys($arIDS, false);
        $rsSets = CCatalogProductSet::getList(
            array(),
            array(
                '@OWNER_ID' => $arIDS,
                '=SET_ID' => 0,
                '=TYPE' => CCatalogProductSet::TYPE_GROUP
            ),
            false,
            false,
            array('ID', 'OWNER_ID')
        );
        while ($arSet = $rsSets->Fetch())
        {
            $arSet['OWNER_ID'] = (int)$arSet['OWNER_ID'];
            $offerSet[$arSet['OWNER_ID']] = true;
            $arResult['OFFER_GROUP'] = true;
        }
        if ($offerSet[$arResult['ID']])
        {
            foreach ($offerSet as &$setOfferValue)
            {
                if ($setOfferValue === false)
                {
                    $setOfferValue = true;
                }
            }
            unset($setOfferValue);
            unset($offerSet[$arResult['ID']]);
        }
        if ($arResult['OFFER_GROUP'])
        {
            $offerSet = array_filter($offerSet);
            $arResult['OFFER_GROUP_VALUES'] = array_keys($offerSet);
        }
    }

    $arMatrix = array();
    $intSelected = -1;
    $arResult['MIN_PRICE'] = false;
    $arResult['MIN_BASIS_PRICE'] = false;
    foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
    {
        if (empty($arResult['MIN_PRICE']))
        {
            if ($arResult['OFFER_ID_SELECTED'] > 0)
                $foundOffer = ($arResult['OFFER_ID_SELECTED'] == $arOffer['ID']);
            else
                $foundOffer = $arOffer['CAN_BUY'];
            if ($foundOffer)
            {
                $intSelected = $keyOffer;
                $arResult['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
                $arResult['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
            }
            unset($foundOffer);
        }

        $arSKUProps = false;
        if (!empty($arOffer['DISPLAY_PROPERTIES']))
        {
            $boolSKUDisplayProps = true;
            $arSKUProps = array();
            foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
            {
                if ('F' == $arOneProp['PROPERTY_TYPE'])
                    continue;
                $arSKUProps[] = array(
                    'NAME' => $arOneProp['NAME'],
                    'VALUE' => $arOneProp['DISPLAY_VALUE']
                );
            }
            unset($arOneProp);
        }
        if (isset($arOfferSet[$arOffer['ID']]))
        {
            $arOffer['OFFER_GROUP'] = true;
            $arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
        }
        reset($arOffer['MORE_PHOTO']);
        $firstPhoto = current($arOffer['MORE_PHOTO']);
        $arOneRow = array(
            'ID' => $arOffer['ID'],
            'NAME' => $arOffer['~NAME'],
            'TREE' => $arOffer['TREE'],
            'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
            'BASIS_PRICE' => $arOffer['MIN_PRICE'],
            'DISPLAY_PROPERTIES' => $arSKUProps,
            'PREVIEW_PICTURE' => $firstPhoto,
            'DETAIL_PICTURE' => $firstPhoto,
            'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
            'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
            'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
            'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
            'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
            'OFFER_GROUP' => (isset($offerSet[$arOffer['ID']]) && $offerSet[$arOffer['ID']]),
            'CAN_BUY' => $arOffer['CAN_BUY'],
            'SLIDER' => $arOffer['MORE_PHOTO'],
            'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
        );
        $arMatrix[$keyOffer] = $arOneRow;
    }
    if (-1 == $intSelected)
    {
        $intSelected = 0;
        $arResult['MIN_PRICE'] = (isset($arResult['OFFERS'][0]['RATIO_PRICE']) ? $arResult['OFFERS'][0]['RATIO_PRICE'] : $arResult['OFFERS'][0]['MIN_PRICE']);
        $arResult['MIN_BASIS_PRICE'] = $arResult['OFFERS'][0]['MIN_PRICE'];
    }
    $arResult['JS_OFFERS'] = $arMatrix;
    $arResult['OFFERS_SELECTED'] = $intSelected;
    if ($arMatrix[$intSelected]['SLIDER_COUNT'] > 0)
    {
        $arResult['MORE_PHOTO'] = $arMatrix[$intSelected]['SLIDER'];
        $arResult['MORE_PHOTO_COUNT'] = $arMatrix[$intSelected]['SLIDER_COUNT'];
    }

    $arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
}

if ($arResult['MODULES']['catalog'] && $arResult['CATALOG'])
{
    if ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
    {
        CIBlockPriceTools::setRatioMinPrice($arResult, false);
        $arResult['MIN_BASIS_PRICE'] = $arResult['MIN_PRICE'];
    }
    if (
        CBXFeatures::IsFeatureEnabled('CatCompleteSet')
        && (
            $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
            || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET
        )
    )
    {
        $rsSets = CCatalogProductSet::getList(
            array(),
            array(
                '@OWNER_ID' => $arResult['ID'],
                '=SET_ID' => 0,
                '=TYPE' => CCatalogProductSet::TYPE_GROUP
            ),
            false,
            false,
            array('ID', 'OWNER_ID')
        );
        if ($arSet = $rsSets->Fetch())
        {
            $arResult['OFFER_GROUP'] = true;
        }
    }
}

if (!empty($arResult['DISPLAY_PROPERTIES']))
{
    foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
    {
        if ('F' == $arDispProp['PROPERTY_TYPE'])
            unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
    }
}

$arResult['SKU_PROPS'] = $arSKUPropList;
$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

$arResult['CURRENCIES'] = array();
if ($arResult['MODULES']['currency'])
{
    if ($boolConvert)
    {
        $currencyFormat = CCurrencyLang::GetFormatDescription($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
        $arResult['CURRENCIES'] = array(
            array(
                'CURRENCY' => $arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
                'FORMAT' => array(
                    'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
                    'DEC_POINT' => $currencyFormat['DEC_POINT'],
                    'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
                    'DECIMALS' => $currencyFormat['DECIMALS'],
                    'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
                    'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
                )
            )
        );
        unset($currencyFormat);
    }
    else
    {
        $currencyIterator = CurrencyTable::getList(array(
            'select' => array('CURRENCY')
        ));
        while ($currency = $currencyIterator->fetch())
        {
            $currencyFormat = CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
            $arResult['CURRENCIES'][] = array(
                'CURRENCY' => $currency['CURRENCY'],
                'FORMAT' => array(
                    'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
                    'DEC_POINT' => $currencyFormat['DEC_POINT'],
                    'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
                    'DECIMALS' => $currencyFormat['DECIMALS'],
                    'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
                    'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
                )
            );
        }
        unset($currencyFormat, $currency, $currencyIterator);
    }
}


$arResult['SHELF'] = '';
$arResult['RACK'] = '';

$arResult['BUY_ID'] = getBondsProduct($arResult['ID']);

if($arResult["BUY_ID"]	!=$arResult['ID']){
    $arResult["CAN_BUY"] = canYouBuy($arResult['ID']);

    if(checkQuantityRigths()){

        $bondsArSelect = Array("NAME","PROPERTY_SHELF","PROPERTY_RACK");
        $bondsArFilter = Array(
            "ID"=>(int)($arResult['BUY_ID'])
        );

        $bondResDB = CIBlockElement::GetList(Array(), $bondsArFilter, false, false, $bondsArSelect);

        if($bondResDB && ($bondResArr = $bondResDB->GetNext())){
            if(isset($bondResArr['NAME']) && !empty($bondResArr['NAME'])){
                $arResult['BONDS_NAME'] = $bondResArr['NAME'];
            }

            if(isset($bondResArr['PROPERTY_SHELF_VALUE']) && !empty($bondResArr['PROPERTY_SHELF_VALUE'])){
                $arResult['SHELF'] = $bondResArr['PROPERTY_SHELF_VALUE'];
            }

            if(isset($bondResArr['PROPERTY_RACK_VALUE']) && !empty($bondResArr['PROPERTY_RACK_VALUE'])){
                $arResult['RACK'] = $bondResArr['PROPERTY_RACK_VALUE'];
            }
        }

    }


    $rsProducts = CCatalogProduct::GetList(
        array(),
        array('ID' => $arResult['BUY_ID']),
        false,
        false,
        array(
            'ID',
            'CAN_BUY_ZERO',
            'QUANTITY_TRACE',
            'QUANTITY'
        )
    );

    if ($arCatalogProduct = $rsProducts->Fetch()){

        //$arResult['CHECK_QUANTITY'] = ($arCatalogProduct["CAN_BUY_ZERO"] != 'Y' && $arCatalogProduct["QUANTITY_TRACE"] == 'Y');
        $arResult['CHECK_QUANTITY'] = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');

        $arResultDbRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $arResult['BUY_ID']), false, false, array("RATIO"));
        $arResult['CATALOG_MEASURE_RATIO'] = 1;

        if($arResultRatio = $arResultDbRatio->Fetch()){
            if(isset($arResultRatio['RATIO']) && !empty($arResultRatio['RATIO'])){
                $arResult['CATALOG_MEASURE_RATIO'] = $arResultRatio['RATIO'];
            };
        };

        $arResult['CATALOG_QUANTITY'] = (
        0 < $arCatalogProduct["QUANTITY"] && is_float($arResult['CATALOG_MEASURE_RATIO'])
            ? floatval($arCatalogProduct["QUANTITY"])
            : intval($arCatalogProduct["QUANTITY"])
        );


    }

}

if(isset($arResult['DISPLAY_PROPERTIES'])
    &&isset($arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS'])
    &&isset($arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS']['VALUE'])
    &&!empty($arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS']['VALUE'])){

    $arResult['LINKED_ELEMETS'] = $arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS']['VALUE'];
    unset($arResult['DISPLAY_PROPERTIES']['LINKED_ELEMETS']);
}

if(isset($arResult['PROPERTIES'])
    &&isset($arResult['PROPERTIES']['LINKED_ELEMETS'])
    &&isset($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])
    &&!empty($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])){

    $arResult['LINKED_ELEMETS'] = $arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'];
    unset($arResult['PROPERTIES']['LINKED_ELEMETS']);
}

if(empty($arResult['LINKED_ELEMETS'])){
    $product_id = (int)$arResult['ID'];

    $dbLinkedRes = CIBlockElement::GetList(
        Array(),
        Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            "PROPERTY_products" => $product_id
        ),
        false,
        Array('nTopCount' => 10),
        Array(
            "ID",
            "PROPERTY_products"
        )
    );


    if($dbLinkedRes){
        while($dbLinkedAr = $dbLinkedRes->GetNext()){

            $dbLinkedPropsRes = CIBlockElement::GetProperty(17, $dbLinkedAr['ID'], array(), array("CODE" => "products"));

            if($dbLinkedPropsRes){

                while($dbLinkedPropsAr = $dbLinkedPropsRes->GetNext()){

                    if($dbLinkedPropsAr['VALUE'] != $product_id){
                        $arResult['LINKED_ELEMETS'][] = $dbLinkedPropsAr['VALUE'];
                    }

                }

                $arResult['LINKED_ELEMETS'] = array_unique($arResult['LINKED_ELEMETS']);

            }

        }

    }

}


if(isset($arResult['DISPLAY_PROPERTIES'])
    &&isset($arResult['DISPLAY_PROPERTIES']['COM_BLACK'])
    &&isset($arResult['DISPLAY_PROPERTIES']['COM_BLACK']['VALUE'])
    &&!empty($arResult['DISPLAY_PROPERTIES']['COM_BLACK']['VALUE'])
    &&!checkQuantityRigths()){
    unset($arResult['DISPLAY_PROPERTIES']['COM_BLACK']);
}

if(isset($arResult['DISPLAY_PROPERTIES'])
    &&isset($arResult['DISPLAY_PROPERTIES']['QUALITY'])
    &&isset($arResult['DISPLAY_PROPERTIES']['QUALITY']['VALUE'])
    &&!empty($arResult['DISPLAY_PROPERTIES']['QUALITY']['VALUE'])
    &&!checkQuantityRigths()){
    unset($arResult['DISPLAY_PROPERTIES']['QUALITY']);
}

$arResult['PRINT_QUANTITY'] = get_quantity_product($arResult['ID']);
$arResult['CAN_BUY'] = $arResult['PRINT_QUANTITY'] > 0 ? $arResult['CAN_BUY'] : false;
$arResult['PRINT_QUANTITY'] = checkQuantityRigths() ? $arResult['PRINT_QUANTITY'] : false;

$obCache = new CPHPCache;
$cacheID = 'aboutcartdetail';
$cart_description = '';

if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

    $tmp = array();
    $tmp = $obCache->GetVars();

    if(isset($tmp[$cacheID])){
        $cart_description = $tmp[$cacheID];
    }

} else {

    $cartArFilter = Array(
        "CODE" => "buy-panel-info",
        "IBLOCK_CODE" => "options"
    );

    $cartResDB = CIBlockElement::GetList(Array(), $cartArFilter, false, false, array("ID","IBLOCK_ID"));
    $cart_description = "";

    $lines = array("SOCIAL_ICONS_LINKS" => array(), "SOCIAL_TITLES" => array(), "TOOLTIP_TEXT" => array() );

    if($cartResDB){

        $cartResAr = $cartResDB->GetNext();

        if(isset($cartResAr["ID"]) && !empty($cartResAr["ID"])){

            $dbBlockResult  = CIBlockElement::GetProperty(
                $cartResAr["IBLOCK_ID"],
                $cartResAr["ID"],
                Array(),
                Array(
                    "CODE" => "SOCIAL_ICONS_LINKS"
                )
            );



            if($dbBlockResult){
                while($dbBlockArr = $dbBlockResult->fetch()){

                    if(isset($dbBlockArr["VALUE"]) && !empty($dbBlockArr["VALUE"])){
                        if(isset($dbBlockArr["VALUE"]))
                            $lines["SOCIAL_ICONS_LINKS"][] = $dbBlockArr["VALUE"];
                    }

                }
            }


            $dbBlockResult  = CIBlockElement::GetProperty(
                $cartResAr["IBLOCK_ID"],
                $cartResAr["ID"],
                Array(),
                Array(
                    "CODE" => "SOCIAL_TITLES"
                )
            );

            if($dbBlockResult){
                while($dbBlockArr = $dbBlockResult->fetch()){

                    if(isset($dbBlockArr["VALUE"]))
                        $lines["SOCIAL_TITLES"][] = $dbBlockArr["VALUE"];


                }
            }

            $dbBlockResult  = CIBlockElement::GetProperty(
                $cartResAr["IBLOCK_ID"],
                $cartResAr["ID"],
                Array(),
                Array(
                    "CODE" => "TOOLTIP_TEXT"
                )
            );

            if($dbBlockResult){
                while($dbBlockArr = $dbBlockResult->fetch()){
                    if(isset($dbBlockArr["VALUE"]) && isset($dbBlockArr["VALUE"]["TEXT"]))
                        $lines["TOOLTIP_TEXT"][] = $dbBlockArr["VALUE"]["TEXT"];
                }
            }


        }
        //print_r($cartResAr);
        //PROPERTY_SOCIAL_ICONS_LINKS_VALUE
        //PROPERTY_SOCIAL_TITLES_VALUE



        if(!empty($lines)){

            foreach($lines["SOCIAL_ICONS_LINKS"] as $number => $cartResAr){

                $currentLine = "";



                if(isset($cartResAr)
                    && !empty($cartResAr)){
                    $currentLine .= $cartResAr;
                };

                if(isset($lines["SOCIAL_TITLES"][$number])
                    && !empty($lines["SOCIAL_TITLES"][$number])){

                    if(isset($lines["TOOLTIP_TEXT"])
                        && !empty($lines["TOOLTIP_TEXT"])
                        && isset($lines["TOOLTIP_TEXT"][$number])
                        && !empty($lines["TOOLTIP_TEXT"][$number])){
                        $currentLine .= '<span role="button" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="'.htmlspecialcharsbx($lines["TOOLTIP_TEXT"][$number]).'">';
                    };

                    $currentLine .= $lines["SOCIAL_TITLES"][$number];

                    if(isset($lines["TOOLTIP_TEXT"])
                        && !empty($lines["TOOLTIP_TEXT"])
                        && isset($lines["TOOLTIP_TEXT"][$number])
                        && !empty($lines["TOOLTIP_TEXT"][$number])){
                        $currentLine .= '</span>';
                    };

                    if(!empty($currentLine)){
                        $cart_description .= "<p>".$currentLine."</p>";
                    };


                };

            };


        };

    };

    if($obCache->StartDataCache()){

        $obCache->EndDataCache(
            array(
                $cacheID => $cart_description
            )
        );

    };
};

$arResult['CART_DESCRIPTION'] = $cart_description;

$instructionArr = array();

if(isset($arResult["PROPERTIES"]["INSTRUCTION"])
    && isset($arResult["PROPERTIES"]["INSTRUCTION"]["VALUE"])
    && !empty($arResult["PROPERTIES"]["INSTRUCTION"]["VALUE"])){

    $instruction = '';
    $file_name = '';
    $instruction_name = '';

    $instruction_name = ($arResult["PROPERTIES"]["INSTRUCTION"]["NAME"]);
    $instruction_value = ($arResult["PROPERTIES"]["INSTRUCTION"]["VALUE"]);

    $instFilter = Array(
        "ID" =>(int)($instruction_value)
    );

    $instResDB = CIBlockElement::GetList(Array(), $instFilter, false, false, array("NAME","PROPERTY_instruction"));

    if($instResDB){
        while($instResArr = $instResDB->Fetch()){

            if(isset($instResArr['PROPERTY_INSTRUCTION_VALUE'])
                && !empty($instResArr['PROPERTY_INSTRUCTION_VALUE'])){

                $instruction = CFile::GetPath($instResArr['PROPERTY_INSTRUCTION_VALUE']);
                if($instruction){

                    $file_name = '';
                    $arTranslitParams = array(
                        "replace_space" => "_",
                        "replace_other" => "_"
                    );

                    $file_name = Cutil::translit($instruction_name.'_'.$arResult['NAME'],"ru",$arTranslitParams);

                    if(mb_strripos($instruction,'.') !== false){
                        $extension = mb_substr($instruction,mb_strripos($instruction,'.'),mb_strlen($instruction));
                        $file_name.= $extension;
                    };
                };
            };
        };
    };

    $instructionArr = array(
        'href' => $instruction,
        'download' => $file_name,
        'name' => $instruction_name
    );

    $arResult['INSTRUCTION'] = $instructionArr;

    unset($arResult["PROPERTIES"]["INSTRUCTION"]);
};

$obCache = new CPHPCache;
$cacheID = 'tabsdetail';
$tabs = array();

if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

    $tmp = array();
    $tmp = $obCache->GetVars();

    if(isset($tmp[$cacheID])){
        $tabs = $tmp[$cacheID];
    }

} else {

    $arFilter = Array(
        "SECTION_CODE" => "tabs",
        "IBLOCK_CODE" => "options",
        "ACTIVE" => "Y"
    );

    $tabsResDB = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE"));

    if($tabsResDB){

        while($tabsResArr = $tabsResDB->Fetch()){

            if(
                isset($tabsResArr['ID'])
                && isset($tabsResArr['NAME'])
                && isset($tabsResArr['PREVIEW_TEXT'])
                && !empty($tabsResArr['PREVIEW_TEXT'])
            ){

                $tabs['tab_headers'][$tabsResArr['ID']]	= $tabsResArr['NAME'];
                $tabs['tab_panels'][$tabsResArr['ID']] = $tabsResArr['PREVIEW_TEXT'];

                if(	isset($tabsResArr['PREVIEW_PICTURE'])
                    && !empty($tabsResArr['PREVIEW_PICTURE'])
                ){

                    $image_path	= CFile::GetPath($tabsResArr['PREVIEW_PICTURE']);
                    if($image_path){
                        $tabs['tab_images'][$tabsResArr['ID']] = $image_path;
                    };

                };
            };
        };
    };

    if($obCache->StartDataCache()){

        $obCache->EndDataCache(
            array(
                $cacheID => $tabs
            )
        );

    };

};

$arResult['TABS'] = $tabs;

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&is_array($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&sizeof($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
    &&!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
){

    foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $number => $file_id){

        if(is_numeric($file_id)){
            $arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][$number] = CFile::GetPath($file_id);
        }
    }


};

$canonical_url = '';
$canonicalResDB = CIBlockElement::GetList(Array("SORT" => "ASC"),Array('ID' => $arResult['ID'], 'IBLOCK_ID' => $arResult['IBLOCK_ID']), false, false, array('DETAIL_PAGE_URL'));

if($canonicalResDB
    && $canonicalResArr = $canonicalResDB->getNext()){

    if(isset($canonicalResArr['DETAIL_PAGE_URL']) && !empty($canonicalResArr['DETAIL_PAGE_URL'])){
        $canonical_url = $canonicalResArr['DETAIL_PAGE_URL'];
    };

};

$arResult['STORES'] = array();
$arResult['MANAGER_STORES'] = array();

if($arResult['CAN_BUY']){

    $rsStore = CCatalogStoreProduct::GetList(
        array(),
        array('PRODUCT_ID' => $arResult['BUY_ID'], "!STORE_ID" => 3),
        false,
        false
    );

    if ($rsStore){

        while($arStore = $rsStore->Fetch()){

            $arResult['STORES'][] = array(
                'STORE_NAME' => $arStore['STORE_NAME'],
                'AMOUNT' => $arStore['AMOUNT'],
                'STORE_ID' => $arStore['STORE_ID']
            );

        }

    }

    if(checkQuantityRigths()) {
        $arResult['MANAGER_STORES'] = $arResult['STORES'];
    }
}

$modelHtml = '';

if(isset($arResult["PROPERTIES"]["MODEL_HTML"])
    && isset($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"])
    && !empty($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"])){

    $modelHTMLId = $arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"];

    $dbMHTMLId = CIBlockElement::GetByID($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"]);

    if($dbMHTMLId
        && $arMHTMLId = $dbMHTMLId->GetNext()){

        $modelHtml = trim($arMHTMLId['~DETAIL_TEXT']);

    }

}

if(empty($modelHtml)){
    unset($arResult["PROPERTIES"]["MODEL_HTML"]);
} else {
    $arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"] = $modelHtml;
}

$arResult['CANONICAL_URL'] = $canonical_url;

if (is_object($this->__component))
{
    $resultCacheKeys = array_keys($arResult);

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};
