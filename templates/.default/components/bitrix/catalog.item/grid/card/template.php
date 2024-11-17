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

$inStock = get_quantity_product($item['ID']);

?>
    <div class="item-images">
        <?php if(isset($item['ARTNUMBER'])
            && !empty($item['ARTNUMBER'])): ?>
            <div class="item-scu">
                <meta itemprop="sku" content="<?=$item['ARTNUMBER']['VALUE'];?>">
                <meta itemprop="brand" content="<?=$item['MANUFACTURER_DETAIL']['VALUE'];?>">
                <?=$item['ARTNUMBER']['NAME'];?>
                <span>
            <?=$item['ARTNUMBER']['VALUE'];?>
        </span>
            </div>
        <?php endif; ?>
        <meta itemprop="image" content="<?=$item['PREVIEW_PICTURE']['SRC']?>">
        <?php

        if($item['MORE_PHOTO_COUNT'] > 0) {

            if($item['MORE_PHOTO_COUNT'] > 1) {
                ?>
                <div class="slider slider-more">
                <div class="swiper-container-wrapper">
                <div class="swiper-container gallery-top">
                <div class="swiper-wrapper">
            <?php } ?>
            <?php foreach ($item['MORE_PHOTO'] as $key => $photo) { ?>
                <div class="swiper-slide<?php if($key == 0): ?> swiper-slide-active<?php endif; ?>">
                    <div class="slider__image">
                        <a class="piiw" href="<?=$item['DETAIL_PAGE_URL']?>"><img itemprop="image" src="<?=$photo['SRC']?>" alt="<?=htmlspecialchars($productTitle,ENT_QUOTES,LANG_CHARSET);?>" class="img-responsive" /></a>
                    </div>
                </div>
            <?php }; ?>
            <?php if($item['MORE_PHOTO_COUNT'] > 1) { ?>
                </div>
                <div class="swiper-pagination"></div>
                </div>
                </div>
                </div>
            <?php } ?>
            <?php

        } else {

            ?>
            <a class="piiw" href="<?=$item['DETAIL_PAGE_URL']?>">
                <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" class="img-responsive" alt="<?=htmlspecialchars($productTitle,ENT_QUOTES,LANG_CHARSET);?>" />
            </a>
            <?php
        }

        ?>
        <?php if(isset($item['OLD_PRICE_PERCENTS'])
            && !empty($item['OLD_PRICE_PERCENTS'])): ?>
            <div class="old-bage">
                -<?=$item['OLD_PRICE_PERCENTS'];?>%
            </div>
        <?php endif; ?>
    </div>
    <div class="item-desc">
        <div class="item-title">
            <meta itemprop="name" content="<?=$productTitle?>">
            <a href="<?=$item['DETAIL_PAGE_URL']?>">
                <?=$productTitle?>
            </a>
        </div>
        <?php if(isset($item['PREVIEW_TEXT'])): ?>
            <div class="item-text">
                <meta itemprop="description" content="<?=$item['PREVIEW_TEXT']?>">

            </div>
        <?php endif; ?>
        <?php if(!empty($item['ADMIN_MESSAGES'])){ ?>
            <?php

            $dataContent = '';

            foreach ($item['ADMIN_MESSAGES'] as $adminMessage){

                $dataContent .=
                    '<li class="list-group-item">'
                    . '<strong>'
                    . $adminMessage['NAME']
                    . '</strong>'
                    . ' &ndash; '
                    . $adminMessage['VALUE']
                    .'</li>';

            }


            if(!empty($dataContent)){
                $dataContent =
                    '<ul class="list-group admin-messages">'
                    . $dataContent
                    .'</ul>';


                echo $dataContent;

            }

        }; ?>
    </div>

    <?php

$APPLICATION->IncludeComponent(
    "bitrix:iblock.vote",
    "schema",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'] ?? 'catalog',
        "IBLOCK_ID" => $arParams['IBLOCK_ID'] ?? 11,
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

    <div class="item-prices"<?php if(isset($price['RATIO_PRICE']) && $price['RATIO_PRICE'] > 0): ?>  itemprop="offers" itemscope itemtype="http://schema.org/Offer"<?php endif; ?>>

        <?php if(isset($price['RATIO_PRICE']) && $price['RATIO_PRICE'] > 0): ?>
            <meta itemprop="priceValidUntil" content="<?=date('Y-m-d H:i:s',(time() + (86400 * 363)));?>" />
            <meta itemprop="price" content="<?=$price['RATIO_PRICE'] ?>"/>
            <meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>"/>
        <?php endif; ?>

        <meta itemprop="sku" content="<?=$item['ARTNUMBER']['VALUE'];?>">
        <link itemprop="availability" href="http://schema.org/<?=($inStock ? 'InStock' : 'OutOfStock');?>" />
        <?
        if (!empty($item['OLD_PRICE'])) {
            ?>
            <span class="item-old">
            <?=$item['OLD_PRICE'];?>
        </span>
            <?
        }
        ?>
        <span class="item-price">
    <?
    if (!empty($price)) {
        echo $price['PRINT_RATIO_PRICE'];
    }
    ?>
    </span>
    </div>



    <div class="item-links">
        <?

        if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
            CAPIUncachedArea::includeFile(
                "/local/include/pricesList.php",
                array(
                    'PRICE' => $price,
                    'NOT_MUCH' => $arParams['NOT_MUCH'],
                    'PRODUCT_ID' => $item['ID'],
                    'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                    'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                    'ONE_CLICK_PREORDER' => 'Y',
                    'ONE_CLICK_ORDER' => 'Y',
                    'IN_STOCK_LABEL' => 'Y',
                    'HAS_PRICE' => (!empty($price) && isset($price['PRINT_RATIO_PRICE']) ? true : false)
                )
            );
        }

        ?>
    </div>
