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
<div class="item-images 2">
    <a class="piiw" href="<?=$item['DETAIL_PAGE_URL']?>">
        <img itemprop="image" src="<?=$item['PREVIEW_PICTURE']['SRC']?>" class="img-responsive" alt="<?=htmlspecialchars($productTitle,ENT_QUOTES,LANG_CHARSET);?>" />
    </a>
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