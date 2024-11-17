<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */


    $showDisplayProps = !empty($item['DISPLAY_PROPERTIES']);
    $showProductProps = $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !empty($item['PRODUCT_PROPERTIES']);
    $showPropsBlock = $showDisplayProps || $showProductProps;
    $showSkuBlock = false;

?>
<div class="row product-item">
    <div class="col-xs-12">
        <div class="product-item-title">
            <a href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$productTitle?>"><?=$productTitle?></a>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
        <a class="product-item-image-wrapper" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$imgTitle?>"
           data-entity="image-wrapper">
			<span class="product-item-image-slider-slide-container slide" id="<?=$itemIds['PICT_SLIDER']?>"
                <?=($showSlider ? '' : 'style="display: none;"')?>
                  data-slider-interval="<?=$arParams['SLIDER_INTERVAL']?>" data-slider-wrap="true">
				<?
                if ($showSlider)
                {
                    foreach ($morePhoto as $key => $photo)
                    {
                        ?>
                        <span class="product-item-image-slide item <?=($key == 0 ? 'active' : '')?>"
                              style="background-image: url('<?=$photo['SRC']?>');">
						</span>
                        <?
                    }
                }
                ?>
			</span>
            <span class="product-item-image-original" id="<?=$itemIds['PICT']?>"
                  style="background-image: url('<?=$item['PREVIEW_PICTURE']['SRC']?>'); <?=($showSlider ? 'display: none;' : '')?>">
			</span>
            <?
            if ($item['SECOND_PICT'])
            {
                $bgImage = !empty($item['PREVIEW_PICTURE_SECOND']) ? $item['PREVIEW_PICTURE_SECOND']['SRC'] : $item['PREVIEW_PICTURE']['SRC'];
                ?>
                <span class="product-item-image-alternative" id="<?=$itemIds['SECOND_PICT']?>"
                      style="background-image: url('<?=$bgImage?>'); <?=($showSlider ? 'display: none;' : '')?>">
				</span>
                <?
            }

            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
            {
                ?>
                <div class="product-item-label-ring <?=$discountPositionClass?>" id="<?=$itemIds['DSC_PERC']?>"
                    <?=($price['PERCENT'] > 0 ? '' : 'style="display: none;"')?>>
                    <span><?=-$price['PERCENT']?>%</span>
                </div>
                <?
            }

            if ($item['LABEL'])
            {
                ?>
                <div class="product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>">
                    <?
                    if (!empty($item['LABEL_ARRAY_VALUE']))
                    {
                        foreach ($item['LABEL_ARRAY_VALUE'] as $code => $value)
                        {
                            ?>
                            <div<?=(!isset($item['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
                                <span title="<?=$value?>"><?=$value?></span>
                            </div>
                            <?
                        }
                    }
                    ?>
                </div>
                <?
            }
            ?>
            <div class="product-item-image-slider-control-container" id="<?=$itemIds['PICT_SLIDER']?>_indicator"
                <?=($showSlider ? '' : 'style="display: none;"')?>>
                <?
                if ($showSlider)
                {
                    foreach ($morePhoto as $key => $photo)
                    {
                        ?>
                        <div class="product-item-image-slider-control<?=($key == 0 ? ' active' : '')?>" data-go-to="<?=$key?>"></div>
                        <?
                    }
                }
                ?>
            </div>
            <?
            if ($arParams['SLIDER_PROGRESS'] === 'Y')
            {
                ?>
                <div class="product-item-image-slider-progress-bar-container">
                    <div class="product-item-image-slider-progress-bar" id="<?=$itemIds['PICT_SLIDER']?>_progress_bar" style="width: 0;"></div>
                </div>
                <?
            }
            ?>
        </a>
    </div>
    <?

    if ($showPropsBlock)
    {
        ?>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-7">
            <?
            if ($showDisplayProps)
            {
                ?>
                <div class="product-item-info-container" data-entity="props-block">
                    <dl class="product-item-properties">
                        <?
                        foreach ($item['DISPLAY_PROPERTIES'] as $code => $displayProperty)
                        {
                            ?>
                            <dt<?=(!isset($item['PROPERTY_CODE_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
                                <?=$displayProperty['NAME']?>
                            </dt>
                            <dd<?=(!isset($item['PROPERTY_CODE_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
                                <?=(is_array($displayProperty['DISPLAY_VALUE'])
                                    ? implode(' / ', $displayProperty['DISPLAY_VALUE'])
                                    : $displayProperty['DISPLAY_VALUE'])?>
                            </dd>
                            <?
                        }
                        ?>
                    </dl>
                </div>
                <?
            }

            if ($showProductProps)
            {
                ?>
                <div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
                    <?
                    if (!empty($item['PRODUCT_PROPERTIES_FILL']))
                    {
                        foreach ($item['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo)
                        {
                            ?>
                            <input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propID?>]"
                                   value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
                            <?
                            unset($item['PRODUCT_PROPERTIES'][$propID]);
                        }
                    }

                    if (!empty($item['PRODUCT_PROPERTIES']))
                    {
                        ?>
                        <table>
                            <?
                            foreach ($item['PRODUCT_PROPERTIES'] as $propID => $propInfo)
                            {
                                ?>
                                <tr>
                                    <td><?=$item['PROPERTIES'][$propID]['NAME']?></td>
                                    <td>
                                        <?
                                        if (
                                            $item['PROPERTIES'][$propID]['PROPERTY_TYPE'] === 'L'
                                            && $item['PROPERTIES'][$propID]['LIST_TYPE'] === 'C'
                                        )
                                        {
                                            foreach ($propInfo['VALUES'] as $valueID => $value)
                                            {
                                                ?>
                                                <label>
                                                    <? $checked = $valueID === $propInfo['SELECTED'] ? 'checked' : ''; ?>
                                                    <input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propID?>]"
                                                           value="<?=$valueID?>" <?=$checked?>>
                                                    <?=$value?>
                                                </label>
                                                <br />
                                                <?
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propID?>]">
                                                <?
                                                foreach ($propInfo['VALUES'] as $valueID => $value)
                                                {
                                                    $selected = $valueID === $propInfo['SELECTED'] ? 'selected' : '';
                                                    ?>
                                                    <option value="<?=$valueID?>" <?=$selected?>>
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
            ?>
        </div>
        <?
    }


    ?>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3<?=($showPropsBlock || $showSkuBlock ? '' : ' col-md-offset-6 col-lg-offset-7')?>">
        <div class="product-line-item-info-right-container">
            <?
            foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName)
            {
                switch ($blockName)
                {
                    case 'price': ?>
                        <div class="product-item-info-container product-item-price-container" data-entity="price-block">
                            <?
                            if ($arParams['SHOW_OLD_PRICE'] === 'Y')
                            {
                                ?>
                                <span class="product-item-price-old" id="<?=$itemIds['PRICE_OLD']?>"
                                    <?=($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? 'style="display: none;"' : '')?>>
									<?=$price['PRINT_RATIO_BASE_PRICE']?>
								</span>&nbsp;
                                <?
                            }
                            ?>
                            <span class="product-item-price-current" id="<?=$itemIds['PRICE']?>">
								<?
                                if (!empty($price))
                                {
                                    echo $price['PRINT_RATIO_PRICE'];
                                }
                                ?>
							</span>
                        </div>
                        <?
                        break;

                    case 'quantityLimit':
                        if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
                        {

                            if (
                                $measureRatio
                                && (float)$actualItem['CATALOG_QUANTITY'] > 0
                                && $actualItem['CATALOG_QUANTITY_TRACE'] === 'Y'
                                && $actualItem['CATALOG_CAN_BUY_ZERO'] === 'N'
                            )
                            {
                                ?>
                                <div class="product-item-info-container product-item-hidden" id="<?=$itemIds['QUANTITY_LIMIT']?>">
                                    <div class="product-item-info-container-title">
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

                        break;

                    case 'quantity':

                        if ($actualItem['CAN_BUY'] && $arParams['USE_PRODUCT_QUANTITY'])
                        {
                            ?>
                            <div class="product-item-info-container" data-entity="quantity-block">
                                <div class="product-item-amount">
                                    <div class="product-item-amount-field-container">
                                        <span class="product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN']?>"></span>
                                        <input class="product-item-amount-field" id="<?=$itemIds['QUANTITY']?>" type="number"
                                               name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>"
                                               value="<?=$measureRatio?>">
                                        <span class="product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP']?>"></span>
                                        <div class="product-item-amount-description-container">
												<span id="<?=$itemIds['QUANTITY_MEASURE']?>">
													<?=$actualItem['ITEM_MEASURE']['TITLE']?>
												</span>
                                            <span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                        }

                        break;

                    case 'buttons':
                        ?>
                        <div class="product-item-info-container" data-entity="buttons-block">
                            <?

                            if ($actualItem['CAN_BUY'])
                            {
                                ?>
                                <div class="product-item-button-container" id="<?=$itemIds['BASKET_ACTIONS']?>">
                                    <a class="btn btn-default <?=$buttonSizeClass?>" id="<?=$itemIds['BUY_LINK']?>"
                                       href="javascript:void(0)" rel="nofollow">
                                        <?=($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?>
                                    </a>
                                </div>
                                <?
                            }
                            else
                            {
                                ?>
                                <div class="product-item-button-container">
                                    <?
                                    if ($showSubscribe)
                                    {
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:catalog.product.subscribe',
                                            '',
                                            array(
                                                'PRODUCT_ID' => $actualItem['ID'],
                                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                                'BUTTON_CLASS' => 'btn btn-default '.$buttonSizeClass,
                                                'DEFAULT_DISPLAY' => true,
                                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                            ),
                                            $component,
                                            array('HIDE_ICONS' => 'Y')
                                        );
                                    }
                                    ?>
                                    <a class="btn btn-link <?=$buttonSizeClass?>" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
                                       href="javascript:void(0)" rel="nofollow">
                                        <?=$arParams['MESS_NOT_AVAILABLE']?>
                                    </a>
                                </div>
                                <?
                            }

                            ?>
                        </div>
                        <?
                        break;

                }
            }
            ?>
        </div>
    </div>
</div>