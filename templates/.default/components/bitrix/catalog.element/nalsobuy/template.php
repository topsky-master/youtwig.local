<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 * @var array $arResult
 */

$this->setFrameMode(false);
$item = $arResult;

unset($arResult);

$name = !empty($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $item['NAME'];
$title = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $item['NAME'];
$alt = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $item['NAME'];

$showSliderControls = $item['MORE_PHOTO_COUNT'] > 1;

$price = $item['ITEM_PRICES'][$item['ITEM_PRICE_SELECTED']];

$hasPrice = !empty($price);

$measureRatio = $item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$photoSrc = $item['PHOTO_SRC'];

$cSlidesCount = 3;

if (!empty($item['SETLIST'])):

    ?>
    <p class="h4 buy-wtitle">
        <?=GetMessage('TMPL_WITH_THIS_PRODUCT_SET');?>
    </p>
    <div itemscope itemtype="http://schema.org/Product" class="product-item" data-src="<?=htmlspecialchars($photoSrc,ENT_QUOTES,LANG_CHARSET);?>" data-title="<?=htmlspecialchars($title,ENT_QUOTES,LANG_CHARSET);?>">
        <div class="col-xs-12 col-md-8 col-lg-9 itemset-list">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:iblock.vote",
                "schema",
                array(
                    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                    "ELEMENT_ID" => $item['ID'],
                    "ELEMENT_CODE" => "",
                    "MAX_VOTE" => "5",
                    "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                    "SET_STATUS_404" => "N",
                    "DISPLAY_AS_RATING" => "vote_avg",
                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
            ?>
            <meta itemprop="name" content="<?=htmlspecialchars($title,ENT_QUOTES,LANG_CHARSET);?>"/>
            <meta itemprop="description" name="description" content="<?=htmlspecialchars($title,ENT_QUOTES,LANG_CHARSET);?><?php if ($hasPrice) { ?> = <?=htmlspecialchars($price['PRINT_RATIO_PRICE'],ENT_QUOTES,LANG_CHARSET);;?><?php } ?>" />
            <?php foreach($item['SETLIST']['NAME'] as $number => $name): ?>
                <meta itemprop="image" content="<?=$item['SETLIST']['SRC'][$number];?>" />
                <?php break; ?>
            <?php endforeach; ?>
            <?php if($item['SETLIST_COUNT'] > $cSlidesCount): ?>
            <div id="alsoBuy" data-interval="false" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <?php endif; ?>
                    <?php foreach($item['SETLIST']['NAME'] as $number => $name): ?>
                        <div itemprop="isRelatedTo" itemscope itemtype="http://schema.org/Product" class="item<?php if(!($item['SETLIST_COUNT'] > $cSlidesCount)): ?> col-sm-4<?php endif; ?><?php if($number == 0): ?> active<?php endif; ?>">
                            <div class="<?php if($item['SETLIST_COUNT'] > $cSlidesCount): ?>col-sm-4<?php endif; ?>">
                                <?php if(isset($item['SETLIST']['SRC'][$number])): ?>
                                    <a href="<?=$item['SETLIST']['LINK'][$number];?>">
                                        <img itemprop="image" src="<?=$item['SETLIST']['SRC'][$number];?>" class="img-responsive" alt="<?=htmlspecialchars($name,ENT_QUOTES,LANG_CHARSET);?>" />
                                    </a>
                                <?php endif; ?>
                                <a itemprop="url" href="<?=$item['SETLIST']['LINK'][$number];?>" class="h4 also-title">
                                        <span itemprop="name">
                                            <?=$name;?>
                                        </span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if($item['SETLIST_COUNT'] > $cSlidesCount): ?>
                </div>
                <a class="left carousel-control" href="#alsoBuy" role="button" data-slide="prev">
                </a>
                <a class="right carousel-control" href="#alsoBuy" role="button" data-slide="next">
                </a>
            </div>
        <?php endif; ?>
        </div>
        <div class="col-xs-12 col-md-4 col-lg-3 itemset-cart"<?php if ($hasPrice): ?> itemprop="offers" itemscope itemtype="http://schema.org/Offer"<?php endif; ?>>
            <?php if(!empty($item['SETLIST'])): ?>
                <ul class="setlist">
                    <?php foreach($item['SETLIST']['NAME'] as $number => $name): ?>
                        <li>
                            <a href="<?=$item['SETLIST']['LINK'][$number];?>">
                                <?=$name;?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="item-prices">
                <?
                if (!empty($item['OLD_PRICE'])) {
                    ?>
                    <span class="item-old">
                    <?=$item['OLD_PRICE'];?>
                </span>
                    <?
                }

                if ($hasPrice) {
                    ?>
                    <span class="item-price">
                        <?
                        echo $price['PRINT_RATIO_PRICE'];
                        ?>
                        <meta itemprop="priceValidUntil" content="<?=date('Y-m-d H:i:s',(time() + (86400 * 363)));?>" />
                        <meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
                        <meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
                    </span>
                <? } ?>
            </div>
            <?

            if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {

                CAPIUncachedArea::includeFile(
                    "/include/prices.php",
                    array(
                        'PRICE' => $price,
                        'NOT_MUCH' => $arParams['NOT_MUCH'],
                        'PRODUCT_ID' => $item['ID'],
                        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
						'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_BUY'],
                        'ONE_CLICK_ORDER' => 'N',
                        'SCHEMA_AVAIL' => ($hasPrice ? 'Y' : 'N'),
                        'ONE_CLICK_PREORDER' => 'N'

                    )
                );


                CAPIUncachedArea::includeFile(
                    "/include/preorder.php",
                    array(
                        "CONSENT_PROCESSING_TEXT" => $item["CONSENT_PROCESSING_TEXT"]

                    )
                );
            }

            ?>
        </div>
    </div>
<?

endif;

unset($emptyProductProperties, $item, $itemIds, $jsParams);

