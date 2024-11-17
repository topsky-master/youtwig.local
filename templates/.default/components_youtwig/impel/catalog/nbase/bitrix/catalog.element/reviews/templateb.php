<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(false);
//$this->addExternalCss('/bitrix/css/main/bootstrap.css');

/*
 * if(!empty($lines)){

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
 */

$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

$haveOffers = !empty($arResult['OFFERS']);

$actualItem = $arResult;
$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['CATALOG_SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');
$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
    {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
    {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}
?>
    <div class="bx-catalog-element bx-<?=$arParams['TEMPLATE_THEME']?>" id="<?=$itemIds['ID']?>"
         itemscope itemtype="http://schema.org/Product">
        <div class="container-fluid">
            <?
            if ($arParams['DISPLAY_NAME'] === 'Y')
            {
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="bx-title"><?=$name?></h1>
                    </div>
                </div>
                <?
            }
            ?>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="product-item-detail-slider-container" id="<?=$itemIds['BIG_SLIDER_ID']?>">
                        <span class="product-item-detail-slider-close" data-entity="close-popup"></span>
                        <div class="product-item-detail-slider-block
						<?=($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '')?>"
                             data-entity="images-slider-block">
                            <span class="product-item-detail-slider-left" data-entity="slider-control-left" style="display: none;"></span>
                            <span class="product-item-detail-slider-right" data-entity="slider-control-right" style="display: none;"></span>
                            <div class="product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>"
                                <?=(!$arResult['LABEL'] ? 'style="display: none;"' : '' )?>>
                                <?
                                if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE']))
                                {
                                    foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value)
                                    {
                                        ?>
                                        <div<?=(!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
                                            <span title="<?=$value?>"><?=$value?></span>
                                        </div>
                                        <?
                                    }
                                }
                                ?>
                            </div>
                            <?
                            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
                            {

                                {
                                    if ($price['DISCOUNT'] > 0)
                                    {
                                        ?>
                                        <div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
                                             title="<?=-$price['PERCENT']?>%">
                                            <span><?=-$price['PERCENT']?>%</span>
                                        </div>
                                        <?
                                    }
                                }
                            }
                            ?>
                            <div class="product-item-detail-slider-images-container" data-entity="images-container">
                                <?
                                if (!empty($actualItem['MORE_PHOTO']))
                                {
                                    foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
                                    {
                                        ?>
                                        <div class="product-item-detail-slider-image<?=($key == 0 ? ' active' : '')?>" data-entity="image" data-id="<?=$photo['ID']?>">
                                            <img src="<?=$photo['SRC']?>" alt="<?=$alt?>" title="<?=$title?>"<?=($key == 0 ? ' itemprop="image"' : '')?>>
                                        </div>
                                        <?
                                    }
                                }

                                if ($arParams['SLIDER_PROGRESS'] === 'Y')
                                {
                                    ?>
                                    <div class="product-item-detail-slider-progress-bar" data-entity="slider-progress-bar" style="width: 0;"></div>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <?
                        if ($showSliderControls)
                        {

                            {
                                ?>
                                <div class="product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_ID']?>">
                                    <?
                                    if (!empty($actualItem['MORE_PHOTO']))
                                    {
                                        foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
                                        {
                                            ?>
                                            <div class="product-item-detail-slider-controls-image<?=($key == 0 ? ' active' : '')?>"
                                                 data-entity="slider-control" data-value="<?=$photo['ID']?>">
                                                <img src="<?=$photo['SRC']?>">
                                            </div>
                                            <?
                                        }
                                    }
                                    ?>
                                </div>
                                <?
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="product-item-detail-info-section">
                                <?
                                foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
                                {
                                    switch ($blockName)
                                    {
                                        case 'sku':


                                            break;

                                        case 'props':
                                            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?
                                                    if (!empty($arResult['DISPLAY_PROPERTIES']))
                                                    {
                                                        ?>
                                                        <dl class="product-item-detail-properties">
                                                            <?
                                                            foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
                                                            {
                                                                if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']]))
                                                                {
                                                                    ?>
                                                                    <dt><?=$property['NAME']?></dt>
                                                                    <dd><?=(is_array($property['DISPLAY_VALUE'])
                                                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                                                            : $property['DISPLAY_VALUE'])?>
                                                                    </dd>
                                                                    <?
                                                                }
                                                            }
                                                            unset($property);
                                                            ?>
                                                        </dl>
                                                        <?
                                                    }

                                                    if ($arResult['SHOW_OFFERS_PROPS'])
                                                    {
                                                        ?>
                                                        <dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></dl>
                                                        <?
                                                    }
                                                    ?>
                                                </div>
                                                <?
                                            }

                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="product-item-detail-pay-block">
                                <?
                                foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
                                {
                                    switch ($blockName)
                                    {
                                        case 'rating':
                                            if ($arParams['USE_VOTE_RATING'] === 'Y')
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?
                                                    $APPLICATION->IncludeComponent(
                                                        'bitrix:iblock.vote',
                                                        'stars',
                                                        array(
                                                            'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                            'ELEMENT_ID' => $arResult['ID'],
                                                            'ELEMENT_CODE' => '',
                                                            'MAX_VOTE' => '5',
                                                            'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
                                                            'SET_STATUS_404' => 'N',
                                                            'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
                                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                            'CACHE_TIME' => $arParams['CACHE_TIME']
                                                        ),
                                                        $component,
                                                        array('HIDE_ICONS' => 'Y')
                                                    );
                                                    ?>
                                                </div>
                                                <?
                                            }

                                            break;

                                        case 'price':
                                            ?>
                                            <div class="product-item-detail-info-container">
                                                <?
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                                                {
                                                    ?>
                                                    <div class="product-item-detail-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"
                                                         style="display: <?=($showDiscount ? '' : 'none')?>;">
                                                        <?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?>
                                                    </div>
                                                    <?
                                                }
                                                ?>
                                                <div class="product-item-detail-price-current" id="<?=$itemIds['PRICE_ID']?>">
                                                    <?=$price['PRINT_RATIO_PRICE']?>
                                                </div>
                                                <?
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                                                {
                                                    ?>
                                                    <div class="item_economy_price" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"
                                                         style="display: <?=($showDiscount ? '' : 'none')?>;">
                                                        <?
                                                        if ($showDiscount)
                                                        {
                                                            echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
                                                        }
                                                        ?>
                                                    </div>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                            <?
                                            break;

                                        case 'priceRanges':
                                            if ($arParams['USE_PRICE_COUNT'])
                                            {
                                                $showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
                                                $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
                                                ?>
                                                <div class="product-item-detail-info-container"
                                                    <?=$showRanges ? '' : 'style="display: none;"'?>
                                                     data-entity="price-ranges-block">
                                                    <div class="product-item-detail-info-container-title">
                                                        <?=$arParams['MESS_PRICE_RANGES_TITLE']?>
                                                        <span data-entity="price-ranges-ratio-header">
														(<?=(Loc::getMessage(
                                                                'CT_BCE_CATALOG_RATIO_PRICE',
                                                                array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                            ))?>)
													</span>
                                                    </div>
                                                    <dl class="product-item-detail-properties" data-entity="price-ranges-body">
                                                        <?
                                                        if ($showRanges)
                                                        {
                                                            foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
                                                            {
                                                                if ($range['HASH'] !== 'ZERO-INF')
                                                                {
                                                                    $itemPrice = false;

                                                                    foreach ($arResult['ITEM_PRICES'] as $itemPrice)
                                                                    {
                                                                        if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
                                                                        {
                                                                            break;
                                                                        }
                                                                    }

                                                                    if ($itemPrice)
                                                                    {
                                                                        ?>
                                                                        <dt>
                                                                            <?
                                                                            echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_FROM',
                                                                                    array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                                                ).' ';

                                                                            if (is_infinite($range['SORT_TO']))
                                                                            {
                                                                                echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                                                                            }
                                                                            else
                                                                            {
                                                                                echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_TO',
                                                                                    array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
                                                                                );
                                                                            }
                                                                            ?>
                                                                        </dt>
                                                                        <dd><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></dd>
                                                                        <?
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </dl>
                                                </div>
                                                <?
                                                unset($showRanges, $useRatio, $itemPrice, $range);
                                            }

                                            break;

                                        case 'quantityLimit':
                                            if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
                                            {

                                                {
                                                    if (
                                                        $measureRatio
                                                        && (float)$actualItem['CATALOG_QUANTITY'] > 0
                                                        && $actualItem['CATALOG_QUANTITY_TRACE'] === 'Y'
                                                        && $actualItem['CATALOG_CAN_BUY_ZERO'] === 'N'
                                                    )
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
                                                            <div class="product-item-detail-info-container-title">
                                                                <?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
                                                                <span class="product-item-quantity" data-entity="quantity-limit-value">
																<?
                                                                if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
                                                                {
                                                                    if ((float)$actualItem['CATALOG_QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
                                                                    {
                                                                        echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                                                                    }
                                                                    else
                                                                    {
                                                                        echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    echo $actualItem['CATALOG_QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
                                                                }
                                                                ?>
															</span>
                                                            </div>
                                                        </div>
                                                        <?
                                                    }
                                                }
                                            }

                                            break;

                                        case 'quantity':
                                            if ($arParams['USE_PRODUCT_QUANTITY'])
                                            {
                                                ?>
                                                <div class="product-item-detail-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>"
                                                     data-entity="quantity-block">
                                                    <div class="product-item-detail-info-container-title"><?=Loc::getMessage('CATALOG_QUANTITY')?></div>
                                                    <div class="product-item-amount">
                                                        <div class="product-item-amount-field-container">
                                                            <span class="product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN_ID']?>"></span>
                                                            <input class="product-item-amount-field" id="<?=$itemIds['QUANTITY_ID']?>" type="number"
                                                                   value="<?=$price['MIN_QUANTITY']?>">
                                                            <span class="product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP_ID']?>"></span>
                                                            <span class="product-item-amount-description-container">
															<span id="<?=$itemIds['QUANTITY_MEASURE']?>">
																<?=$actualItem['ITEM_MEASURE']['TITLE']?>
															</span>
															<span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
														</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?
                                            }

                                            break;

                                        case 'buttons':
                                            ?>
                                            <div data-entity="main-button-container">
                                                <div id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
                                                    <?
                                                    if ($showAddBtn)
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?=$showButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['ADD_BASKET_LINK']?>"
                                                               href="javascript:void(0);">
                                                                <span><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></span>
                                                            </a>
                                                        </div>
                                                        <?
                                                    }

                                                    if ($showBuyBtn)
                                                    {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
                                                               href="javascript:void(0);">
                                                                <span><?=$arParams['MESS_BTN_BUY']?></span>
                                                            </a>
                                                        </div>
                                                        <?
                                                    }
                                                    ?>
                                                </div>
                                                <?
                                                if ($showSubscribe)
                                                {
                                                    ?>
                                                    <div class="product-item-detail-info-container">
                                                        <?
                                                        $APPLICATION->IncludeComponent(
                                                            'bitrix:catalog.product.subscribe',
                                                            '',
                                                            array(
                                                                'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                                                'PRODUCT_ID' => $arResult['ID'],
                                                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                                                'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
                                                                'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                                            ),
                                                            $component,
                                                            array('HIDE_ICONS' => 'Y')
                                                        );
                                                        ?>
                                                    </div>
                                                    <?
                                                }
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
                                                       href="javascript:void(0)"
                                                       rel="nofollow" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
                                                        <?=$arParams['MESS_NOT_AVAILABLE']?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?
                                            break;
                                    }
                                }

                                if ($arParams['DISPLAY_COMPARE'])
                                {
                                    ?>
                                    <div class="product-item-detail-compare-container">
                                        <div class="product-item-detail-compare">
                                            <div class="checkbox">
                                                <label id="<?=$itemIds['COMPARE_LINK']?>">
                                                    <input type="checkbox" data-entity="compare-checkbox">
                                                    <span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?

                    {
                        if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP'])
                        {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.set.constructor',
                                '.default',
                                array(
                                    'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arResult['ID'],
                                    'PRICE_CODE' => $arParams['PRICE_CODE'],
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
                                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                    'CURRENCY_ID' => $arParams['CURRENCY_ID']
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-md-9">
                    <div class="row" id="<?=$itemIds['TABS_ID']?>">
                        <div class="col-xs-12">
                            <div class="product-item-detail-tabs-container">
                                <ul class="product-item-detail-tabs-list">
                                    <?
                                    if ($showDescription)
                                    {
                                        ?>
                                        <li class="product-item-detail-tab active" data-entity="tab" data-value="description">
                                            <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                <span><?=$arParams['MESS_DESCRIPTION_TAB']?></span>
                                            </a>
                                        </li>
                                        <?
                                    }

                                    if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                                    {
                                        ?>
                                        <li class="product-item-detail-tab" data-entity="tab" data-value="properties">
                                            <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                <span><?=$arParams['MESS_PROPERTIES_TAB']?></span>
                                            </a>
                                        </li>
                                        <?
                                    }

                                    if ($arParams['USE_COMMENTS'] === 'Y')
                                    {
                                        ?>
                                        <li class="product-item-detail-tab" data-entity="tab" data-value="comments">
                                            <a href="javascript:void(0);" class="product-item-detail-tab-link">
                                                <span><?=$arParams['MESS_COMMENTS_TAB']?></span>
                                            </a>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="<?=$itemIds['TAB_CONTAINERS_ID']?>">
                        <div class="col-xs-12">
                            <?
                            if ($showDescription)
                            {
                                ?>
                                <div class="product-item-detail-tab-content active" data-entity="tab-container" data-value="description"
                                     itemprop="description">
                                    <?
                                    if (
                                        $arResult['PREVIEW_TEXT'] != ''
                                        && (
                                            $arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
                                            || ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
                                        )
                                    )
                                    {
                                        echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>';
                                    }

                                    if ($arResult['DETAIL_TEXT'] != '')
                                    {
                                        echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>'.$arResult['DETAIL_TEXT'].'</p>';
                                    }
                                    ?>
                                </div>
                                <?
                            }

                            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                            {
                                ?>
                                <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="properties">
                                    <?
                                    if (!empty($arResult['DISPLAY_PROPERTIES']))
                                    {
                                        ?>
                                        <dl class="product-item-detail-properties">
                                            <?
                                            foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
                                            {
                                                ?>
                                                <dt><?=$property['NAME']?></dt>
                                                <dd><?=(
                                                    is_array($property['DISPLAY_VALUE'])
                                                        ? implode(' / ', $property['DISPLAY_VALUE'])
                                                        : $property['DISPLAY_VALUE']
                                                    )?>
                                                </dd>
                                                <?
                                            }
                                            unset($property);
                                            ?>
                                        </dl>
                                        <?
                                    }

                                    if ($arResult['SHOW_OFFERS_PROPS'])
                                    {
                                        ?>
                                        <dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_PROP_DIV']?>"></dl>
                                        <?
                                    }
                                    ?>
                                </div>
                                <?
                            }

                            if ($arParams['USE_COMMENTS'] === 'Y')
                            {
                                ?>
                                <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="comments" style="display: none;">
                                    <?
                                    $componentCommentsParams = array(
                                        'ELEMENT_ID' => $arResult['ID'],
                                        'ELEMENT_CODE' => '',
                                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                        'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
                                        'URL_TO_COMMENT' => '',
                                        'WIDTH' => '',
                                        'COMMENTS_COUNT' => '5',
                                        'BLOG_USE' => $arParams['BLOG_USE'],
                                        'FB_USE' => $arParams['FB_USE'],
                                        'FB_APP_ID' => $arParams['FB_APP_ID'],
                                        'VK_USE' => $arParams['VK_USE'],
                                        'VK_API_ID' => $arParams['VK_API_ID'],
                                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                        'BLOG_TITLE' => '',
                                        'BLOG_URL' => $arParams['BLOG_URL'],
                                        'PATH_TO_SMILE' => '',
                                        'EMAIL_NOTIFY' => $arParams['BLOG_EMAIL_NOTIFY'],
                                        'AJAX_POST' => 'Y',
                                        'SHOW_SPAM' => 'Y',
                                        'SHOW_RATING' => 'N',
                                        'FB_TITLE' => '',
                                        'FB_USER_ADMIN_ID' => '',
                                        'FB_COLORSCHEME' => 'light',
                                        'FB_ORDER_BY' => 'reverse_time',
                                        'VK_TITLE' => '',
                                        'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME']
                                    );
                                    if(isset($arParams["USER_CONSENT"]))
                                        $componentCommentsParams["USER_CONSENT"] = $arParams["USER_CONSENT"];
                                    if(isset($arParams["USER_CONSENT_ID"]))
                                        $componentCommentsParams["USER_CONSENT_ID"] = $arParams["USER_CONSENT_ID"];
                                    if(isset($arParams["USER_CONSENT_IS_CHECKED"]))
                                        $componentCommentsParams["USER_CONSENT_IS_CHECKED"] = $arParams["USER_CONSENT_IS_CHECKED"];
                                    if(isset($arParams["USER_CONSENT_IS_LOADED"]))
                                        $componentCommentsParams["USER_CONSENT_IS_LOADED"] = $arParams["USER_CONSENT_IS_LOADED"];
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:catalog.comments',
                                        '',
                                        $componentCommentsParams,
                                        $component,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                    ?>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <div>
                        <?
                        if ($arParams['BRAND_USE'] === 'Y')
                        {
                            $APPLICATION->IncludeComponent(
                                'bitrix:catalog.brandblock',
                                '.default',
                                array(
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arResult['ID'],
                                    'ELEMENT_CODE' => '',
                                    'PROP_CODE' => $arParams['BRAND_PROP_CODE'],
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'WIDTH' => '',
                                    'HEIGHT' => ''
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?
                    if ($arResult['CATALOG'] && $actualItem['CAN_BUY'] && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                    {
                        $APPLICATION->IncludeComponent(
                            'bitrix:sale.prediction.product.detail',
                            '.default',
                            array(
                                'BUTTON_ID' => $showBuyBtn ? $itemIds['BUY_LINK'] : $itemIds['ADD_BASKET_LINK'],
                                'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                'POTENTIAL_PRODUCT_TO_BUY' => array(
                                    'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
                                    'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
                                    'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
                                    'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
                                    'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

                                    'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
                                    'SECTION' => array(
                                        'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
                                        'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
                                        'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
                                        'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
                                    ),
                                )
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }

                    if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                    {
                        ?>
                        <div data-entity="parent-container">
                            <?
                            if (!isset($arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
                            {
                                ?>
                                <div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                    <?=($arParams['GIFTS_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFT_BLOCK_TITLE_DEFAULT'))?>
                                </div>
                                <?
                            }

                            CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');
                            $APPLICATION->IncludeComponent(
                                'bitrix:sale.products.gift',
                                '.default',
                                array(
                                    'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                    'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                                    'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],

                                    'PRODUCT_ROW_VARIANTS' => "",
                                    'PAGE_ELEMENT_COUNT' => 0,
                                    'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
                                        SaleProductsGiftComponent::predictRowVariants(
                                            $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                                            $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
                                        )
                                    ),
                                    'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

                                    'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                                    'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                                    'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
                                    'PRODUCT_DISPLAY_MODE' => 'Y',
                                    'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
                                    'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                                    'SLIDER_INTERVAL' => isset($arParams['GIFTS_SLIDER_INTERVAL']) ? $arParams['GIFTS_SLIDER_INTERVAL'] : '',
                                    'SLIDER_PROGRESS' => isset($arParams['GIFTS_SLIDER_PROGRESS']) ? $arParams['GIFTS_SLIDER_PROGRESS'] : '',

                                    'TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

                                    'LABEL_PROP_'.$arParams['IBLOCK_ID'] => array(),
                                    'LABEL_PROP_MOBILE_'.$arParams['IBLOCK_ID'] => array(),
                                    'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

                                    'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
                                    'MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
                                    'MESS_BTN_ADD_TO_BASKET' => $arParams['~GIFTS_MESS_BTN_BUY'],
                                    'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
                                    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],

                                    'SHOW_PRODUCTS_'.$arParams['IBLOCK_ID'] => 'Y',
                                    'PROPERTY_CODE_'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
                                    'PROPERTY_CODE_MOBILE'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE_MOBILE'],
                                    'PROPERTY_CODE_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                                    'OFFER_TREE_PROPS_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
                                    'CART_PROPERTIES_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
                                    'ADDITIONAL_PICT_PROP_'.$arParams['IBLOCK_ID'] => (isset($arParams['ADD_PICT_PROP']) ? $arParams['ADD_PICT_PROP'] : ''),
                                    'ADDITIONAL_PICT_PROP_'.$arResult['OFFERS_IBLOCK'] => (isset($arParams['OFFER_ADD_PICT_PROP']) ? $arParams['OFFER_ADD_PICT_PROP'] : ''),

                                    'HIDE_NOT_AVAILABLE' => 'Y',
                                    'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                                    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                                    'PRICE_CODE' => $arParams['PRICE_CODE'],
                                    'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
                                    'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
                                    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                    'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
                                    'USE_PRODUCT_QUANTITY' => 'N',
                                    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'POTENTIAL_PRODUCT_TO_BUY' => array(
                                        'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
                                        'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
                                        'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
                                        'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
                                        'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

                                        'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
                                            ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']
                                            : null,
                                        'SECTION' => array(
                                            'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
                                            'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
                                            'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
                                            'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
                                        ),
                                    ),

                                    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                                    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                                    'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </div>
                        <?
                    }

                    if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'))
                    {
                        ?>
                        <div data-entity="parent-container">
                            <?
                            if (!isset($arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE']) || $arParams['GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE'] !== 'Y')
                            {
                                ?>
                                <div class="catalog-block-header" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                    <?=($arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_GIFTS_MAIN_BLOCK_TITLE_DEFAULT'))?>
                                </div>
                                <?
                            }

                            $APPLICATION->IncludeComponent(
                                'bitrix:sale.gift.main.products',
                                '.default',
                                array(
                                    'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                    'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                                    'LINE_ELEMENT_COUNT' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                                    'HIDE_BLOCK_TITLE' => 'Y',
                                    'BLOCK_TITLE' => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

                                    'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
                                    'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],

                                    'AJAX_MODE' => $arParams['AJAX_MODE'],
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],

                                    'ELEMENT_SORT_FIELD' => 'ID',
                                    'ELEMENT_SORT_ORDER' => 'DESC',
                                    //'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                                    //'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
                                    'FILTER_NAME' => 'searchFilter',
                                    'SECTION_URL' => $arParams['SECTION_URL'],
                                    'DETAIL_URL' => $arParams['DETAIL_URL'],
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                                    'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                                    'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],

                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],

                                    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                    'SET_TITLE' => $arParams['SET_TITLE'],
                                    'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
                                    'PRICE_CODE' => $arParams['PRICE_CODE'],
                                    'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                                    'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                                    'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                                    'HIDE_NOT_AVAILABLE' => 'Y',
                                    'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                                    'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                                    'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],

                                    'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
                                    'SLIDER_INTERVAL' => isset($arParams['GIFTS_SLIDER_INTERVAL']) ? $arParams['GIFTS_SLIDER_INTERVAL'] : '',
                                    'SLIDER_PROGRESS' => isset($arParams['GIFTS_SLIDER_PROGRESS']) ? $arParams['GIFTS_SLIDER_PROGRESS'] : '',

                                    'ADD_PICT_PROP' => (isset($arParams['ADD_PICT_PROP']) ? $arParams['ADD_PICT_PROP'] : ''),
                                    'LABEL_PROP' => (isset($arParams['LABEL_PROP']) ? $arParams['LABEL_PROP'] : ''),
                                    'LABEL_PROP_MOBILE' => (isset($arParams['LABEL_PROP_MOBILE']) ? $arParams['LABEL_PROP_MOBILE'] : ''),
                                    'LABEL_PROP_POSITION' => (isset($arParams['LABEL_PROP_POSITION']) ? $arParams['LABEL_PROP_POSITION'] : ''),
                                    'OFFER_ADD_PICT_PROP' => (isset($arParams['OFFER_ADD_PICT_PROP']) ? $arParams['OFFER_ADD_PICT_PROP'] : ''),
                                    'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : ''),
                                    'SHOW_DISCOUNT_PERCENT' => (isset($arParams['SHOW_DISCOUNT_PERCENT']) ? $arParams['SHOW_DISCOUNT_PERCENT'] : ''),
                                    'DISCOUNT_PERCENT_POSITION' => (isset($arParams['DISCOUNT_PERCENT_POSITION']) ? $arParams['DISCOUNT_PERCENT_POSITION'] : ''),
                                    'SHOW_OLD_PRICE' => (isset($arParams['SHOW_OLD_PRICE']) ? $arParams['SHOW_OLD_PRICE'] : ''),
                                    'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
                                    'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
                                    'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
                                    'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
                                    'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
                                    'SHOW_CLOSE_POPUP' => (isset($arParams['SHOW_CLOSE_POPUP']) ? $arParams['SHOW_CLOSE_POPUP'] : ''),
                                    'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
                                    'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
                                )
                                + array(
                                    'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
                                        ? $arResult['ID']
                                        : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
                                    'SECTION_ID' => $arResult['SECTION']['ID'],
                                    'ELEMENT_ID' => $arResult['ID'],

                                    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                                    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                                    'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </div>
        </div>
        <!--Small Card-->
        <div class="product-item-detail-short-card-fixed hidden-xs" id="<?=$itemIds['SMALL_CARD_PANEL_ID']?>">
            <div class="product-item-detail-short-card-content-container">
                <table>
                    <tr>
                        <td rowspan="2" class="product-item-detail-short-card-image">
                            <img src="" style="height: 65px;" data-entity="panel-picture">
                        </td>
                        <td class="product-item-detail-short-title-container" data-entity="panel-title">
                            <span class="product-item-detail-short-title-text"><?=$name?></span>
                        </td>
                        <td rowspan="2" class="product-item-detail-short-card-price">
                            <?
                            if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                            {
                                ?>
                                <div class="product-item-detail-price-old" style="display: <?=($showDiscount ? '' : 'none')?>;"
                                     data-entity="panel-old-price">
                                    <?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?>
                                </div>
                                <?
                            }
                            ?>
                            <div class="product-item-detail-price-current" data-entity="panel-price">
                                <?=$price['PRINT_RATIO_PRICE']?>
                            </div>
                        </td>
                        <?
                        if ($showAddBtn)
                        {
                            ?>
                            <td rowspan="2" class="product-item-detail-short-card-btn"
                                style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;"
                                data-entity="panel-add-button">
                                <a class="btn <?=$showButtonClassName?> product-item-detail-buy-button"
                                   id="<?=$itemIds['ADD_BASKET_LINK']?>"
                                   href="javascript:void(0);">
                                    <span><?=$arParams['MESS_BTN_ADD_TO_BASKET']?></span>
                                </a>
                            </td>
                            <?
                        }

                        if ($showBuyBtn)
                        {
                            ?>
                            <td rowspan="2" class="product-item-detail-short-card-btn"
                                style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;"
                                data-entity="panel-buy-button">
                                <a class="btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
                                   href="javascript:void(0);">
                                    <span><?=$arParams['MESS_BTN_BUY']?></span>
                                </a>
                            </td>
                            <?
                        }
                        ?>
                        <td rowspan="2" class="product-item-detail-short-card-btn"
                            style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;"
                            data-entity="panel-not-available-button">
                            <a class="btn btn-link product-item-detail-buy-button" href="javascript:void(0)"
                               rel="nofollow">
                                <?=$arParams['MESS_NOT_AVAILABLE']?>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!--Top tabs-->
        <div class="product-item-detail-tabs-container-fixed hidden-xs" id="<?=$itemIds['TABS_PANEL_ID']?>">
            <ul class="product-item-detail-tabs-list">
                <?
                if ($showDescription)
                {
                    ?>
                    <li class="product-item-detail-tab active" data-entity="tab" data-value="description">
                        <a href="javascript:void(0);" class="product-item-detail-tab-link">
                            <span><?=$arParams['MESS_DESCRIPTION_TAB']?></span>
                        </a>
                    </li>
                    <?
                }

                if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
                {
                    ?>
                    <li class="product-item-detail-tab" data-entity="tab" data-value="properties">
                        <a href="javascript:void(0);" class="product-item-detail-tab-link">
                            <span><?=$arParams['MESS_PROPERTIES_TAB']?></span>
                        </a>
                    </li>
                    <?
                }

                if ($arParams['USE_COMMENTS'] === 'Y')
                {
                    ?>
                    <li class="product-item-detail-tab" data-entity="tab" data-value="comments">
                        <a href="javascript:void(0);" class="product-item-detail-tab-link">
                            <span><?=$arParams['MESS_COMMENTS_TAB']?></span>
                        </a>
                    </li>
                    <?
                }
                ?>
            </ul>
        </div>

        <meta itemprop="name" content="<?=$name?>" />
        <meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
        <?

        {
            ?>
            <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
			<meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
			<link itemprop="availability" href="http://schema.org/<?=($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
		</span>
            <?
        }
        ?>
    </div>
<?

$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
{
    ?>
    <div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
        <?
        if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
        {
            foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
            {
                ?>
                <input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
                <?
                unset($arResult['PRODUCT_PROPERTIES'][$propId]);
            }
        }

        $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
        if (!$emptyProductProperties)
        {
            ?>
            <table>
                <?
                foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
                {
                    ?>
                    <tr>
                        <td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
                        <td>
                            <?
                            if (
                                $arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
                                && $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
                            )
                            {
                                foreach ($propInfo['VALUES'] as $valueId => $value)
                                {
                                    ?>
                                    <label>
                                        <input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
                                               value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"checked"' : '')?>>
                                        <?=$value?>
                                    </label>
                                    <br>
                                    <?
                                }
                            }
                            else
                            {
                                ?>
                                <select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
                                    <?
                                    foreach ($propInfo['VALUES'] as $valueId => $value)
                                    {
                                        ?>
                                        <option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? '"selected"' : '')?>>
                                            <?=$value?>
                                        </option>
                                        <?
                                    }
                                    ?>
                                </select>
                                <?
                            }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
            </table>
            <?
        }
        ?>
    </div>
    <?
}

unset($emptyProductProperties, $actualItem, $itemIds, $jsParams);
