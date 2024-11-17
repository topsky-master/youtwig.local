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
?>
<div class="item-images">
    <?php if(isset($item['ARTNUMBER'])
        && !empty($item['ARTNUMBER'])): ?>
        <div class="item-scu">
            <?=$item['ARTNUMBER']['NAME'];?>
            <span>
            <?=$item['ARTNUMBER']['VALUE'];?>
        </span>
        </div>
    <?php endif; ?>
    <a class="piiw" href="<?=$item['DETAIL_PAGE_URL']?>">
        <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" class="img-responsive" alt="<?=htmlspecialchars($productTitle,ENT_QUOTES,LANG_CHARSET);?>" />
    </a>
    <?php if(isset($item['OLD_PRICE_PERCENTS'])
        && !empty($item['OLD_PRICE_PERCENTS'])): ?>
        <div class="old-bage">
            -<?=$item['OLD_PRICE_PERCENTS'];?>%
        </div>
    <?php endif; ?>
</div>
<div class="item-desc">
    <div class="item-title">
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
            <?=$productTitle?>
        </a>
    </div>
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
<div class="item-prices">
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
            "/include/prices.php",
            array(
                'PRICE' => $price,
                'NOT_MUCH' => $arParams['NOT_MUCH'],
                'PRODUCT_ID' => $item['ID'],
                'MESS_BTN_BUY' => GetMessage('CT_BCI_TPL_MESS_READ_MORE'),
                'ONE_CLICK_PREORDER' => 'Y',
                'IN_STOCK_LABEL' => 'Y',
				'ONE_CLICK_ORDER' => 'Y',
				'HAS_PRICE' => (!empty($price) && isset($price['PRINT_RATIO_PRICE']) ? true : false),
                'PRODUCT_URL' => $item['DETAIL_PAGE_URL'],

            )
        );
    }

    ?>
</div>