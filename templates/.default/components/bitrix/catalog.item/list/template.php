<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(false);

if (isset($arResult['ITEM']))
{
    $item = $arResult['ITEM'];
    $areaId = $arResult['AREA_ID'];
    $itemIds = array(
        'ID' => $areaId,
        'PICT' => $areaId.'_pict',
        'SECOND_PICT' => $areaId.'_secondpict',
        'PICT_SLIDER' => $areaId.'_pict_slider',
        'STICKER_ID' => $areaId.'_sticker',
        'SECOND_STICKER_ID' => $areaId.'_secondsticker',
        'QUANTITY' => $areaId.'_quantity',
        'QUANTITY_DOWN' => $areaId.'_quant_down',
        'QUANTITY_UP' => $areaId.'_quant_up',
        'QUANTITY_MEASURE' => $areaId.'_quant_measure',
        'QUANTITY_LIMIT' => $areaId.'_quant_limit',
        'BUY_LINK' => $areaId.'_buy_link',
        'BASKET_ACTIONS' => $areaId.'_basket_actions',
        'NOT_AVAILABLE_MESS' => $areaId.'_not_avail',
        'SUBSCRIBE_LINK' => $areaId.'_subscribe',
        'COMPARE_LINK' => $areaId.'_compare_link',
        'PRICE' => $areaId.'_price',
        'PRICE_OLD' => $areaId.'_price_old',
        'PRICE_TOTAL' => $areaId.'_price_total',
        'DSC_PERC' => $areaId.'_dsc_perc',
        'SECOND_DSC_PERC' => $areaId.'_second_dsc_perc',
        'PROP_DIV' => $areaId.'_sku_tree',
        'PROP' => $areaId.'_prop_',
        'DISPLAY_PROP_DIV' => $areaId.'_sku_prop',
        'BASKET_PROP_DIV' => $areaId.'_basket_prop',
    );
    $obName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);
    $isBig = isset($arResult['BIG']) && $arResult['BIG'] === 'Y';

    $productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
        ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
        : $item['NAME'];

    $imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
        ? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
        : $item['NAME'];

    $skuProps = array();

    $haveOffers = !empty($item['OFFERS']);

    $actualItem = $item;
    $price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
    $measureRatio = $price['MIN_QUANTITY'];
    $morePhoto = $actualItem['MORE_PHOTO'];

    $showSlider = is_array($morePhoto) && count($morePhoto) > 1;
    $showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && $item['CATALOG_SUBSCRIBE'] === 'Y';

    $discountPositionClass = isset($arResult['BIG_DISCOUNT_PERCENT']) && $arResult['BIG_DISCOUNT_PERCENT'] === 'Y'
        ? 'product-item-label-big'
        : 'product-item-label-small';
    $discountPositionClass .= $arParams['DISCOUNT_POSITION_CLASS'];

    $labelPositionClass = isset($arResult['BIG_LABEL']) && $arResult['BIG_LABEL'] === 'Y'
        ? 'product-item-label-big'
        : 'product-item-label-small';
    $labelPositionClass .= $arParams['LABEL_POSITION_CLASS'];

    $buttonSizeClass = isset($arResult['BIG_BUTTONS']) && $arResult['BIG_BUTTONS'] === 'Y' ? 'btn-md' : 'btn-sm';

	$item['MORE_PHOTO_COUNT'] = 0;
        {

            $dProps = CIBlockElement::GetProperty(
                11,
                $item['ID'],
                Array(),
                Array(
                    "CODE" => "PRIMARY_IMAGES"
                )
            );

            if($dProps){

                while ($aProps = $dProps->GetNext()) {

                    $imgSrc = $imgId = $aProps["VALUE"];

                    if ($imgId && is_numeric($imgId)) {
                        $imgSrc = CFile::GetPath($imgId);
                    }

                    if (file_exists($_SERVER['DOCUMENT_ROOT'].$imgSrc)) {

                        $imgSrc = rectangleImage(
                            $_SERVER['DOCUMENT_ROOT'].$imgSrc,
                            370,
                            370,
                            $imgSrc,
                            '#ffffff'
                        );

                        if (!empty($imgSrc)) {
                            $item['MORE_PHOTO'][$item['MORE_PHOTO_COUNT']]['SRC'] = $imgSrc;
                            ++$item['MORE_PHOTO_COUNT'];
                        }

                    }

                }

                if ($item['MORE_PHOTO_COUNT'] > 0) {
                    //array_unshift($item['MORE_PHOTO'],['SRC' => $item['PREVIEW_PICTURE']['SRC']]);
                    //++$item['MORE_PHOTO_COUNT'];
                }

            }

        }

		 

	?>
    <div class="product-item list-item" id="<?=$areaId?>" data-entity="item" itemscope itemtype="http://schema.org/Product">
        <?
        $documentRoot = Main\Application::getDocumentRoot();
        $templatePath = mb_strtolower($arResult['TYPE']).'/template.php';
        $file = new Main\IO\File($documentRoot.$templateFolder.'/'.$templatePath);
        if ($file->isExists())
        {
            include($file->getPath());
        }


        $jsParams = array(
            'PRODUCT_TYPE' => $item['CATALOG_TYPE'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_ADD_BASKET_BTN' => false,
            'SHOW_BUY_BTN' => true,
            'SHOW_ABSENT' => true,
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'BIG_DATA' => $item['BIG_DATA'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'VIEW_MODE' => $arResult['TYPE'],
            'USE_SUBSCRIBE' => $showSubscribe,
            'PRODUCT' => array(
                'ID' => $item['ID'],
                'NAME' => $productTitle,
                'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
                'PICT' => $item['SECOND_PICT'] ? $item['PREVIEW_PICTURE_SECOND'] : $item['PREVIEW_PICTURE'],
                'CAN_BUY' => $item['CAN_BUY'],
                'CHECK_QUANTITY' => $item['CHECK_QUANTITY'],
                'MAX_QUANTITY' => $item['CATALOG_QUANTITY'],
                'STEP_QUANTITY' => $item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
                'QUANTITY_FLOAT' => is_float($item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
                'ITEM_PRICE_MODE' => $item['ITEM_PRICE_MODE'],
                'ITEM_PRICES' => $item['ITEM_PRICES'],
                'ITEM_PRICE_SELECTED' => $item['ITEM_PRICE_SELECTED'],
                'ITEM_QUANTITY_RANGES' => $item['ITEM_QUANTITY_RANGES'],
                'ITEM_QUANTITY_RANGE_SELECTED' => $item['ITEM_QUANTITY_RANGE_SELECTED'],
                'ITEM_MEASURE_RATIOS' => $item['ITEM_MEASURE_RATIOS'],
                'ITEM_MEASURE_RATIO_SELECTED' => $item['ITEM_MEASURE_RATIO_SELECTED'],
                'MORE_PHOTO' => $item['MORE_PHOTO'],
                'MORE_PHOTO_COUNT' => $item['MORE_PHOTO_COUNT']
            ),
            'BASKET' => array(
                'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
                'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                'EMPTY_PROPS' => empty($item['PRODUCT_PROPERTIES']),
                'BASKET_URL' => $arParams['~BASKET_URL'],
                'ADD_URL_TEMPLATE' => $arParams['~ADD_URL_TEMPLATE'],
                'BUY_URL_TEMPLATE' => $arParams['~BUY_URL_TEMPLATE']
            ),
            'VISUAL' => array(
                'ID' => $itemIds['ID'],
                'PICT_ID' => $item['SECOND_PICT'] ? $itemIds['SECOND_PICT'] : $itemIds['PICT'],
                'PICT_SLIDER_ID' => $itemIds['PICT_SLIDER'],
                'QUANTITY_ID' => $itemIds['QUANTITY'],
                'QUANTITY_UP_ID' => $itemIds['QUANTITY_UP'],
                'QUANTITY_DOWN_ID' => $itemIds['QUANTITY_DOWN'],
                'PRICE_ID' => $itemIds['PRICE'],
                'PRICE_OLD_ID' => $itemIds['PRICE_OLD'],
                'PRICE_TOTAL_ID' => $itemIds['PRICE_TOTAL'],
                'BUY_ID' => $itemIds['BUY_LINK'],
                'BASKET_PROP_DIV' => $itemIds['BASKET_PROP_DIV'],
                'BASKET_ACTIONS_ID' => $itemIds['BASKET_ACTIONS'],
                'NOT_AVAILABLE_MESS' => $itemIds['NOT_AVAILABLE_MESS'],
                'COMPARE_LINK_ID' => $itemIds['COMPARE_LINK'],
                'SUBSCRIBE_ID' => $itemIds['SUBSCRIBE_LINK']
            )
        );


        if ($arParams['DISPLAY_COMPARE'])
        {
            $jsParams['COMPARE'] = array(
                'COMPARE_URL_TEMPLATE' => $arParams['~COMPARE_URL_TEMPLATE'],
                'COMPARE_DELETE_URL_TEMPLATE' => $arParams['~COMPARE_DELETE_URL_TEMPLATE'],
                'COMPARE_PATH' => $arParams['COMPARE_PATH']
            );
        }

        if ($item['BIG_DATA'])
        {
            $jsParams['PRODUCT']['RCM_ID'] = $item['RCM_ID'];
        }

        $jsParams['PRODUCT_DISPLAY_MODE'] = $arParams['PRODUCT_DISPLAY_MODE'];
        $jsParams['USE_ENHANCED_ECOMMERCE'] = $arParams['USE_ENHANCED_ECOMMERCE'];
        $jsParams['DATA_LAYER_NAME'] = $arParams['DATA_LAYER_NAME'];
        $jsParams['BRAND_PROPERTY'] = !empty($item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
            ? $item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
            : null;

        $templateData = array(
            'JS_OBJ' => $obName,
            'ITEM' => array(
                'ID' => $item['ID'],
                'IBLOCK_ID' => $item['IBLOCK_ID'],
                'OFFERS_SELECTED' => $item['OFFERS_SELECTED'],
                'JS_OFFERS' => $item['JS_OFFERS']
            )
        );
        ?>
    </div>
    <?
    unset($item, $actualItem, $minOffer, $itemIds, $jsParams);
}