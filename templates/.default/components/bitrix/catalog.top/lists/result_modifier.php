<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$arViewModeList = array('BANNER','SLIDER', 'SECTION');

$arDefaultParams = array(
    'VIEW_MODE' => 'SECTION',
    'TEMPLATE_THEME' => 'blue',
    'PRODUCT_DISPLAY_MODE' => 'N',
    'ADD_PICT_PROP' => '-',
    'LABEL_PROP' => '-',
    'OFFER_ADD_PICT_PROP' => '-',
    'OFFER_TREE_PROPS' => array('-'),
    'SHOW_DISCOUNT_PERCENT' => 'N',
    'SHOW_OLD_PRICE' => 'N',
    'ADD_TO_BASKET_ACTION' => 'ADD',
    'SHOW_CLOSE_POPUP' => 'N',
    'ROTATE_TIMER' => 30,
    'SHOW_PAGINATION' => 'Y',
    'MESS_BTN_BUY' => '',
    'MESS_BTN_ADD_TO_BASKET' => '',
    'MESS_BTN_COMPARE' => '',
    'MESS_BTN_DETAIL' => '',
    'MESS_NOT_AVAILABLE' => ''
);

$arParams = array_merge($arDefaultParams, $arParams);

if (!isset($arParams['LINE_ELEMENT_COUNT']))
    $arParams['LINE_ELEMENT_COUNT'] = 3;
$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);
//if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
//$arParams['LINE_ELEMENT_COUNT'] = 3;

if (!in_array($arParams['VIEW_MODE'], $arViewModeList))
    $arParams['VIEW_MODE'] = 'SECTION';
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
        if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/'.ToLower($arParams['VIEW_MODE']).'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
            $arParams['TEMPLATE_THEME'] = '';
    }
}
if ('' == $arParams['TEMPLATE_THEME'])
    $arParams['TEMPLATE_THEME'] = 'blue';

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
    $arParams['PRODUCT_DISPLAY_MODE'] = 'N';
//if ('BANNER' == $arParams['VIEW_MODE'])
//$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
    $arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
    $arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
    $arParams['OFFER_ADD_PICT_PROP'] = '';
if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
{
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
}
else
{
    $arParams['OFFER_TREE_PROPS'] = array();
}
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
    $arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
    $arParams['SHOW_OLD_PRICE'] = 'N';
if ($arParams['ADD_TO_BASKET_ACTION'] != 'BUY')
    $arParams['ADD_TO_BASKET_ACTION'] = 'ADD';
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y')
    $arParams['SHOW_CLOSE_POPUP'] = 'N';
$arParams['ROTATE_TIMER'] = intval($arParams['ROTATE_TIMER']);
if (0 > $arParams['ROTATE_TIMER'])
    $arParams['ROTATE_TIMER'] = 30;
$arParams['ROTATE_TIMER'] *= 1000;
if ('N' != $arParams['SHOW_PAGINATION'])
    $arParams['SHOW_PAGINATION'] = 'Y';
$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_BTN_DETAIL'] = trim($arParams['MESS_BTN_DETAIL']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);

$arResult['VIEW_MODE_LIST'] = $arViewModeList;

if (!empty($arResult['ITEMS']))
{
    $arEmptyPreview = false;
    $strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
    if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
    {
        $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
        if (!empty($arSizes))
        {
            $arEmptyPreview = array(
                'SRC' => $strEmptyPreview,
                'WIDTH' => intval($arSizes[0]),
                'HEIGHT' => intval($arSizes[1])
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
        if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']) && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'])
        {
            $arSKUPropList = CIBlockPriceTools::getTreeProperties(
                $arSKU,
                $arParams['OFFER_TREE_PROPS'],
                array(
                    'PICT' => $arEmptyPreview,
                    'NAME' => '-'
                )
            );

            $arNeedValues = array();
            CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
            $arSKUPropIDs = array_keys($arSKUPropList);
            if (empty($arSKUPropIDs))
                $arParams['PRODUCT_DISPLAY_MODE'] = 'N';
            else
                $arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);
        }
    }

    $arItemsPositions = array();

    if(isset($arParams['POSITION'])
        && !empty($arParams['POSITION'])){
        $arItemsPositions = ($arParams['POSITION']);
    }

    $arNewItemsList = array();
    foreach ($arResult['ITEMS'] as $key => $arItem)
    {

        if(isset($arItemsPositions[$arItem['ID']])){

            $arItem['DISPLAY_PROPERTIES']['POSITION']['NAME'] = GetMessage('TMPL_POSITION');
            $arItem['DISPLAY_PROPERTIES']['POSITION']['DISPLAY_VALUE'] = $arItemsPositions[$arItem['ID']];

        }

        $arItem['CHECK_QUANTITY'] = false;
        if (!isset($arItem['CATALOG_MEASURE_RATIO']))
            $arItem['CATALOG_MEASURE_RATIO'] = 1;
        if (!isset($arItem['CATALOG_QUANTITY']))
            $arItem['CATALOG_QUANTITY'] = 0;
        $arItem['CATALOG_QUANTITY'] = (
        0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
            ? floatval($arItem['CATALOG_QUANTITY'])
            : intval($arItem['CATALOG_QUANTITY'])
        );
        $arItem['CATALOG'] = false;
        if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
            $arItem['CATALOG_SUBSCRIPTION'] = 'N';

        CIBlockPriceTools::getLabel($arItem, $arParams['LABEL_PROP']);

        $productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, $arParams['ADD_PICT_PROP']);
        if (empty($productPictures['PICT']))
            $productPictures['PICT'] = $arEmptyPreview;
        if (empty($productPictures['SECOND_PICT']))
            $productPictures['SECOND_PICT'] = $productPictures['PICT'];

        $arItem['PREVIEW_PICTURE'] = $productPictures['PICT'];
        $arItem['PREVIEW_PICTURE_SECOND'] = $productPictures['SECOND_PICT'];
        $arItem['SECOND_PICT'] = true;
        $arItem['PRODUCT_PREVIEW'] = $productPictures['PICT'];
        $arItem['PRODUCT_PREVIEW_SECOND'] = $productPictures['SECOND_PICT'];

        if ($arResult['MODULES']['catalog'])
        {
            $arItem['CATALOG'] = true;
            if (!isset($arItem['CATALOG_TYPE']))
                $arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
            if (
                (CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
                && !empty($arItem['OFFERS'])
            )
            {
                $arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
            }
            switch ($arItem['CATALOG_TYPE'])
            {
                case CCatalogProduct::TYPE_SET:
                    $arItem['OFFERS'] = array();
                    //$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
                    $arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE']);


                    break;
                case CCatalogProduct::TYPE_SKU:
                    break;
                case CCatalogProduct::TYPE_PRODUCT:
                default:
                    //$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
                    $arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE']);

                    break;
            }
        }
        else
        {
            $arItem['CATALOG_TYPE'] = 0;
            $arItem['OFFERS'] = array();
        }

        if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
        {
            if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
            {
                $arMatrixFields = $arSKUPropKeys;
                $arMatrix = array();

                $arNewOffers = array();
                $boolSKUDisplayProperties = false;
                $arItem['OFFERS_PROP'] = false;
                $arItem['SKU_TREE_VALUES'] = array();

                $arDouble = array();
                foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
                {
                    $arOffer['ID'] = (int)$arOffer['ID'];
                    if (isset($arDouble[$arOffer['ID']]))
                        continue;
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
                                $arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
                            }
                            elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
                            {
                                $arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
                            }
                            $arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
                        }
                        $arRow[$strOneCode] = $arCell;
                    }
                    $arMatrix[$keyOffer] = $arRow;

                    CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']);

                    $arOffer['SECOND_PICT'] = false;
                    $arOffer['PREVIEW_PICTURE_SECOND'] = false;

                    CIBlockPriceTools::setRatioMinPrice($arOffer, false);

                    $offerPictures = CIBlockPriceTools::getDoublePicturesForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP']);
                    $arOffer['OWNER_PICT'] = empty($offerPictures['PICT']);
                    $arOffer['PREVIEW_PICTURE'] = false;
                    $arOffer['PREVIEW_PICTURE_SECOND'] = false;
                    $arOffer['SECOND_PICT'] = true;
                    if (!$arOffer['OWNER_PICT'])
                    {
                        if (empty($offerPictures['SECOND_PICT']))
                            $offerPictures['SECOND_PICT'] = $offerPictures['PICT'];
                        $arOffer['PREVIEW_PICTURE'] = $offerPictures['PICT'];
                        $arOffer['PREVIEW_PICTURE_SECOND'] = $offerPictures['SECOND_PICT'];
                    }
                    if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
                        unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);

                    $arDouble[$arOffer['ID']] = true;
                    $arNewOffers[$keyOffer] = $arOffer;
                }
                $arItem['OFFERS'] = $arNewOffers;

                $arUsedFields = array();
                $arSortFields = array();

                $matrixKeys = array_keys($arMatrix);
                foreach ($arSKUPropIDs as $propkey => $propCode)
                {
                    $boolExist = $arMatrixFields[$propCode];
                    foreach ($matrixKeys as $keyOffer)
                    {
                        if ($boolExist)
                        {
                            if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
                                $arItem['OFFERS'][$keyOffer]['TREE'] = array();
                            $propId = $arSKUPropList[$propCode]['ID'];
                            $value = $arMatrix[$keyOffer][$propCode]['VALUE'];
                            if (!isset($arItem['SKU_TREE_VALUES'][$propId]))
                                $arItem['SKU_TREE_VALUES'][$propId] = array();
                            $arItem['SKU_TREE_VALUES'][$propId][$value] = true;
                            $arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$propId] = $value;
                            $arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$propCode] = $arMatrix[$keyOffer][$propCode]['SORT'];
                            $arUsedFields[$propCode] = true;
                            $arSortFields['SKU_SORT_'.$propCode] = SORT_NUMERIC;
                            unset($value, $propId);
                        }
                        else
                        {
                            unset($arMatrix[$keyOffer][$propCode]);
                        }
                    }
                    unset($keyOffer);
                }
                unset($propkey, $propCode);
                unset($matrixKeys);
                $arItem['OFFERS_PROP'] = $arUsedFields;
                $arItem['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

                Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

                $arMatrix = array();
                $intSelected = -1;
                $arItem['MIN_PRICE'] = false;
                $arItem['MIN_BASIS_PRICE'] = false;
                foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
                {
                    if (empty($arItem['MIN_PRICE']))
                    {
                        if ($arItem['OFFER_ID_SELECTED'] > 0)
                            $foundOffer = ($arItem['OFFER_ID_SELECTED'] == $arOffer['ID']);
                        else
                            $foundOffer = $arOffer['CAN_BUY'];
                        if ($foundOffer)
                        {
                            $intSelected = $keyOffer;
                            $arItem['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
                            $arItem['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
                        }
                        unset($foundOffer);
                    }

                    $arSKUProps = false;
                    if (!empty($arOffer['DISPLAY_PROPERTIES']))
                    {
                        $boolSKUDisplayProperties = true;
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

                    $arOneRow = array(
                        'ID' => $arOffer['ID'],
                        'NAME' => $arOffer['~NAME'],
                        'TREE' => $arOffer['TREE'],
                        'DISPLAY_PROPERTIES' => $arSKUProps,
                        'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
                        'BASIS_PRICE' => $arOffer['MIN_PRICE'],
                        'SECOND_PICT' => $arOffer['SECOND_PICT'],
                        'OWNER_PICT' => $arOffer['OWNER_PICT'],
                        'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
                        'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
                        'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
                        'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
                        'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
                        'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
                        'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
                        'CAN_BUY' => $arOffer['CAN_BUY'],
                    );
                    $arMatrix[$keyOffer] = $arOneRow;
                }
                if (-1 == $intSelected)
                {
                    $intSelected = 0;
                    $arItem['MIN_PRICE'] = (isset($arItem['OFFERS'][0]['RATIO_PRICE']) ? $arItem['OFFERS'][0]['RATIO_PRICE'] : $arItem['OFFERS'][0]['MIN_PRICE']);
                    $arItem['MIN_BASIS_PRICE'] = $arItem['OFFERS'][0]['MIN_PRICE'];
                }
                if (!$arMatrix[$intSelected]['OWNER_PICT'])
                {
                    $arItem['PREVIEW_PICTURE'] = $arMatrix[$intSelected]['PREVIEW_PICTURE'];
                    $arItem['PREVIEW_PICTURE_SECOND'] = $arMatrix[$intSelected]['PREVIEW_PICTURE_SECOND'];
                }
                $arItem['JS_OFFERS'] = $arMatrix;
                $arItem['OFFERS_SELECTED'] = $intSelected;
                $arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
            }
            else
            {
                $arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
                    $arItem['OFFERS'],
                    $boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
                );
            }
        }

        if (
            $arResult['MODULES']['catalog']
            && $arItem['CATALOG']
            &&
            ($arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
                || $arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
        )
        {
            CIBlockPriceTools::setRatioMinPrice($arItem, false);
            $arItem['MIN_BASIS_PRICE'] = $arItem['MIN_PRICE'];
        }

        if (!empty($arItem['DISPLAY_PROPERTIES']))
        {
            foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
            {
                if ('F' == $arDispProp['PROPERTY_TYPE'])
                    unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
            }
        }
        $arItem['LAST_ELEMENT'] = 'N';

        $arItem['BUY_ID']		= getBondsProduct($arItem['ID']);
        if($arItem["BUY_ID"]	!=$arItem['ID']){
            $arItem["CAN_BUY"]	= canYouBuy($arItem['ID']);

            if(checkQuantityRigths()){

                $bondsArSelect = Array("NAME","PROPERTY_SHELF","PROPERTY_RACK");
                $bondsArFilter = Array(
                    "ID"=>(int)($arItem['BUY_ID'])
                );

                $bondResDB = CIBlockElement::GetList(Array(), $bondsArFilter, false, false, $bondsArSelect);

                if($bondResDB && ($bondResArr = $bondResDB->GetNext())){
                    if(isset($bondResArr['NAME']) && !empty($bondResArr['NAME'])){
                        $arItem['BONDS_NAME'] = $bondResArr['NAME'];
                    }

                    if(isset($bondResArr['PROPERTY_SHELF_VALUE']) && !empty($bondResArr['PROPERTY_SHELF_VALUE'])){
                        $arItem['SHELF'] = $bondResArr['PROPERTY_SHELF_VALUE'];
                    }

                    if(isset($bondResArr['PROPERTY_RACK_VALUE']) && !empty($bondResArr['PROPERTY_RACK_VALUE'])){
                        $arItem['RACK'] = $bondResArr['PROPERTY_RACK_VALUE'];
                    }
                }

            }

            $rsProducts = CCatalogProduct::GetList(
                array(),
                array('ID' => $arItem['BUY_ID']),
                false,
                false,
                array(
                    'ID',
                    'CAN_BUY_ZERO',
                    'QUANTITY_TRACE',
                    'QUANTITY'
                )
            );

            if ($arCatalogProduct = $rsProducts->Fetch())
            {

                //$arItem['CHECK_QUANTITY'] = ($arCatalogProduct["CAN_BUY_ZERO"] != 'Y' && $arCatalogProduct["QUANTITY_TRACE"] == 'Y');
                $arItem['CHECK_QUANTITY'] = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');

                $arItemDbRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $arItem['BUY_ID']), false, false, array("RATIO"));
                $arItem['CATALOG_MEASURE_RATIO'] = 1;

                if($arItemRatio = $arItemDbRatio->Fetch()){
                    if(isset($arItemRatio['RATIO']) && !empty($arItemRatio['RATIO'])){
                        $arItem['CATALOG_MEASURE_RATIO'] = $arItemRatio['RATIO'];
                    };
                };

                $arItem['CATALOG_QUANTITY'] = (
                0 < $arCatalogProduct["QUANTITY"] && is_float($arItem['CATALOG_MEASURE_RATIO'])
                    ? floatval($arCatalogProduct["QUANTITY"])
                    : intval($arCatalogProduct["QUANTITY"])
                );


            }

        }

        if(isset($arItem['PROPERTIES'])
            &&isset($arItem['PROPERTIES']['COM_BLACK'])
            &&isset($arItem['PROPERTIES']['COM_BLACK']['VALUE'])
            &&!empty($arItem['PROPERTIES']['COM_BLACK']['VALUE'])
            &&!checkQuantityRigths()){
            unset($arItem['PROPERTIES']['COM_BLACK']);
        }

        if(isset($arItem['PROPERTIES'])
            &&isset($arItem['PROPERTIES']['QUALITY'])
            &&isset($arItem['PROPERTIES']['QUALITY']['VALUE'])
            &&!empty($arItem['PROPERTIES']['QUALITY']['VALUE'])
            &&!checkQuantityRigths()){
            unset($arItem['PROPERTIES']['QUALITY']);
        }

        $arItem['PRINT_QUANTITY'] = get_quantity_product($arItem['ID']);
        $arItem['CAN_BUY'] = $arItem['PRINT_QUANTITY'] > 0 ? $arItem['CAN_BUY'] : false;
        $arItem['PRINT_QUANTITY'] = checkQuantityRigths() ? $arItem['PRINT_QUANTITY'] : false;

        $arNewItemsList[$key] = $arItem;
    }
    $arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
    $arResult['ITEMS'] = $arNewItemsList;
    $arResult['SKU_PROPS'] = $arSKUPropList;
    $arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

    /* if ('SLIDER' == $arParams['VIEW_MODE'])
    {
        $arRows = array();
        while(!empty($arResult["ITEMS"]))
        {
            $arRows[] = array_splice($arResult["ITEMS"], 0, $arParams["LINE_ELEMENT_COUNT"]);
        }
        $arResult["ITEMS"] = $arRows;
    } */
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
}

if (is_object($this->__component))
{
    $this->__component->SetResultCacheKeys(array('ITEMS'));
    if (!isset($arResult['ITEMS']))
        $arResult['ITEMS'] = $this->__component->arResult['ITEMS'];

    $this->__component->SetResultCacheKeys(array('howSort'));

    if (!isset($arResult['howSort']))
        $arResult['howSort'] = $this->__component->arResult['howSort'];

}