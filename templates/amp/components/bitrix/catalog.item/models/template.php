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

    if(isset($arParams['MODEL_NAME'])
        &&  isset($arParams['MODEL_NAME'][$key])
        && !empty($arParams['MODEL_NAME'][$key])
        && $arParams['MODEL_NAME'][$key] != "-"){

        $item['NAME'] = trim($arParams['MODEL_NAME'][$key]);

    } else {



        $typeproduct = isset($item['PROPERTIES'])
        && isset($item['PROPERTIES']['TYPEPRODUCT'])
        && isset($item['PROPERTIES']['TYPEPRODUCT']['VALUE'])
        && !empty($item['PROPERTIES']['TYPEPRODUCT']['VALUE'])
            ? trim($item['PROPERTIES']['TYPEPRODUCT']['VALUE'])
            : '';

        $manufacturer = isset($arParams['MANUFACTURER'])
        && !empty($arParams['MANUFACTURER'])
            ? trim($arParams['MANUFACTURER'])
            : (isset($item['PROPERTIES'])
            && isset($item['PROPERTIES']['MANUFACTURER'])
            && isset($item['PROPERTIES']['MANUFACTURER']['VALUE'])
            && !empty($item['PROPERTIES']['MANUFACTURER']['VALUE'])
                ? trim($item['PROPERTIES']['MANUFACTURER']['VALUE'])
                : '');

        $model_new = isset($arParams['MODEL_NEW'])
        && !empty($arParams['MODEL_NEW'])
            ? trim($arParams['MODEL_NEW'])
            : '';

        if(!empty($typeproduct)
            || !empty($manufacturer)
            || !empty($model_new)){

            $item['NAME'] = $typeproduct;
            $item['NAME'].= (!empty($item['NAME']) ? ' '.GetMessage('CATALOG_FOR').' ' : '') . $manufacturer;
            $item['NAME'].= (!empty($item['NAME']) ? ' ' : ' '.GetMessage('CATALOG_FOR').' ') . $model_new;

        };

    }

    $productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
        ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
        : $item['NAME'];

    $imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
        ? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
        : $item['NAME'];

    $skuProps = array();

    $haveOffers = !empty($item['OFFERS']);
    if ($haveOffers)
    {
        $actualItem = isset($item['OFFERS'][$item['OFFERS_SELECTED']])
            ? $item['OFFERS'][$item['OFFERS_SELECTED']]
            : reset($item['OFFERS']);
    }
    else
    {
        $actualItem = $item;
    }

    if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers)
    {
        $price = $item['ITEM_START_PRICE'];
        $minOffer = $item['OFFERS'][$item['ITEM_START_PRICE_SELECTED']];
        $measureRatio = $minOffer['ITEM_MEASURE_RATIOS'][$minOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
        $morePhoto = $item['MORE_PHOTO'];
    }
    else
    {
        $price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
        $measureRatio = $price['MIN_QUANTITY'];
        $morePhoto = $actualItem['MORE_PHOTO'];
    }

    if(empty($morePhoto)){
        $morePhoto = array();
    }

    if(empty($morePhoto)
        && isset($item['PREVIEW_PICTURE'])
        && isset($item['PREVIEW_PICTURE']['SRC'])){

        array_unshift($morePhoto,$item['PREVIEW_PICTURE']);

    }

    if(empty($morePhoto)
        && isset($item['PREVIEW_PICTURE_SECOND'])
        && isset($item['PREVIEW_PICTURE_SECOND']['SRC'])){

        array_unshift($morePhoto,$item['PREVIEW_PICTURE_SECOND']);

    }

    if(!empty($morePhoto)){

        foreach($morePhoto as $number => $photo){

            if(!empty($morePhoto[$number]['SRC'])){

                if(!isset($morePhoto[$number]['ID'])){
                    $morePhoto[$number]['ID'] = 0;
                }

                if(file_exists($_SERVER['DOCUMENT_ROOT'].$morePhoto[$number]['SRC'])
                    && filesize($_SERVER['DOCUMENT_ROOT'].$morePhoto[$number]['SRC']) > 0
                    && is_readable($_SERVER['DOCUMENT_ROOT'].$morePhoto[$number]['SRC'])){

                    $sizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$morePhoto[$number]['SRC']);

                    if($sizes
                        && is_array($sizes)
                        && isset($sizes[0])
                        && isset($sizes[1])
                        && !empty($sizes[0])
                        && !empty($sizes[1])
                    ){

                        $morePhoto[$number]['WIDTH'] = $sizes[0];
                        $morePhoto[$number]['HEIGHT'] = $sizes[1];

                    }


                }

                $srcSetHTML = '';
                $morePhoto[$number]['srcset'] = '';
                $srcSetHTML = createAMPSRCSetHTML($morePhoto[$number]['SRC']);
                $morePhoto[$number]['srcset'] = $srcSetHTML;

            }

        }

    }

    $showSlider = is_array($morePhoto) && count($morePhoto) > 1;
    $showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($item['CATALOG_SUBSCRIBE'] === 'Y' || $haveOffers);

    $discountPositionClass = isset($arResult['BIG_DISCOUNT_PERCENT']) && $arResult['BIG_DISCOUNT_PERCENT'] === 'Y'
        ? 'product-item-label-big'
        : 'product-item-label-small';
    $discountPositionClass .= $arParams['DISCOUNT_POSITION_CLASS'];

    $labelPositionClass = isset($arResult['BIG_LABEL']) && $arResult['BIG_LABEL'] === 'Y'
        ? 'product-item-label-big'
        : 'product-item-label-small';
    $labelPositionClass .= $arParams['LABEL_POSITION_CLASS'];

    $buttonSizeClass = isset($arResult['BIG_BUTTONS']) && $arResult['BIG_BUTTONS'] === 'Y' ? 'btn-md' : 'btn-sm';

    $item['BUY_ID'] = getBondsProduct($item['ID']);

    if($item["BUY_ID"]	!=$item['ID']){
        $item["CAN_BUY"] = canYouBuy($item['ID']);

        if(checkQuantityRigths()){

            $bondsArSelect = Array("NAME","PROPERTY_SHELF","PROPERTY_RACK");
            $bondsArFilter = Array(
                "ID"=>(int)($item['BUY_ID'])
            );

            $bondResDB = CIBlockElement::GetList(Array(), $bondsArFilter, false, false, $bondsArSelect);

            if($bondResDB && ($bondResArr = $bondResDB->GetNext())){
                if(isset($bondResArr['NAME']) && !empty($bondResArr['NAME'])){
                    $item['BONDS_NAME'] = $bondResArr['NAME'];
                }

                if(isset($bondResArr['PROPERTY_SHELF_VALUE']) && !empty($bondResArr['PROPERTY_SHELF_VALUE'])){
                    $item['SHELF'] = $bondResArr['PROPERTY_SHELF_VALUE'];
                }

                if(isset($bondResArr['PROPERTY_RACK_VALUE']) && !empty($bondResArr['PROPERTY_RACK_VALUE'])){
                    $item['RACK'] = $bondResArr['PROPERTY_RACK_VALUE'];
                }
            }

        }


        $rsProducts = CCatalogProduct::GetList(
            array(),
            array('ID' => $item['BUY_ID']),
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

            $item['CHECK_QUANTITY'] = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');

            $itemDbRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $item['BUY_ID']), false, false, array("RATIO"));
            $item['CATALOG_MEASURE_RATIO'] = 1;

            if($itemRatio = $itemDbRatio->Fetch()){
                if(isset($itemRatio['RATIO']) && !empty($itemRatio['RATIO'])){
                    $item['CATALOG_MEASURE_RATIO'] = $itemRatio['RATIO'];
                };
            };

            $item['CATALOG_QUANTITY'] = (
            0 < $arCatalogProduct["QUANTITY"] && is_float($item['CATALOG_MEASURE_RATIO'])
                ? floatval($arCatalogProduct["QUANTITY"])
                : intval($arCatalogProduct["QUANTITY"])
            );


        }

    }

    $item['PRINT_QUANTITY'] = get_quantity_product($item['ID']);
    $item['CAN_BUY'] = $item['PRINT_QUANTITY'] > 0 ? $item['CAN_BUY'] : false;
    $item['PRINT_QUANTITY'] = checkQuantityRigths() ? $item['PRINT_QUANTITY'] : false;

    if(!empty($item["PREVIEW_TEXT"])){
        $amp_content_obj = new AMP_Content($item["PREVIEW_TEXT"],
            array(
                //'AMP_YouTube_Embed_Handler' => array(),
            ),
            array(
                'AMP_Style_Sanitizer' => array(),
                'AMP_Blacklist_Sanitizer' => array(),
                'AMP_Img_Sanitizer' => array(),
                'AMP_Video_Sanitizer' => array(),
                'AMP_Audio_Sanitizer' => array(),
                'AMP_Iframe_Sanitizer' => array(
                    'add_placeholder' => true,
                ),
            ),
            array(
                'content_max_width' => 320,
            )
        );

        $item["PREVIEW_TEXT"] = $amp_content_obj->get_amp_content();
    }

    if(!empty($item["DETAIL_TEXT"])){
        $amp_content_obj = new AMP_Content( $item["DETAIL_TEXT"],
            array(
                //'AMP_YouTube_Embed_Handler' => array(),
            ),
            array(
                'AMP_Style_Sanitizer' => array(),
                'AMP_Blacklist_Sanitizer' => array(),
                'AMP_Img_Sanitizer' => array(),
                'AMP_Video_Sanitizer' => array(),
                'AMP_Audio_Sanitizer' => array(),
                'AMP_Iframe_Sanitizer' => array(
                    'add_placeholder' => true,
                ),
            ),
            array(
                'content_max_width' => 320
            )
        );

        $item["DETAIL_TEXT"] = $amp_content_obj->get_amp_content();
    }

    ?>

    <div class="product-item-container<?=(isset($arResult['SCALABLE']) && $arResult['SCALABLE'] === 'Y' ? ' product-item-scalable-card' : '')?>"
         id="<?=$areaId?>" data-entity="item">
        <?
        $documentRoot = Main\Application::getDocumentRoot();
        $templatePath = mb_strtolower($arResult['TYPE']).'/template.php';
        $file = new Main\IO\File($documentRoot.$templateFolder.'/'.$templatePath);
        if ($file->isExists())
        {
            include($file->getPath());
        }


        ?>
    </div>
    <?
    unset($item, $actualItem, $minOffer, $itemIds);
}