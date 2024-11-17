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
<div class="product-detail">

    <?php
    if(!empty($morePhoto)){
        ?>
        <div class="product-image">
            <?php $image = current($morePhoto);
			$image["SRC"] = rectangleImage($_SERVER['DOCUMENT_ROOT'].$image["SRC"],255,255,$image["SRC"],"",true,false);
			?>
            <a href="<?=$item['DETAIL_PAGE_URL']?>">
                <amp-img itemprop="image" alt="<?=htmlspecialchars($imgTitle,ENT_QUOTES,LANG_CHARSET);?>" src="<?=$image["SRC"];?>" width="<?=$image["WIDTH"];?>" height="<?=$image["HEIGHT"];?>" layout="responsive"<?=$image["srcset"];?>>
                    <noscript>
                        <img src="<?=$image["SRC"];?>" width="<?=$image["WIDTH"];?>" height="<?=$image["WIDTH"];?>" alt="<?=htmlspecialchars($imgTitle,ENT_QUOTES,LANG_CHARSET);?>" />
                    </noscript>
                </amp-img>
            </a>
            <?php ?>
        </div>
    <?php } ?>
    <h2>
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
            <?=$item['NAME'];?>
        </a>
    </h2>
    <div class="item_price">
        <? if (!empty($price)){ ?>
            <div class="item-price">
                <? echo $price['PRINT_RATIO_PRICE']; ?>
            </div>
        <? } ?>
        <? if($item['CAN_BUY']){?>
            <a rel="nofollow" href="<?=$APPLICATION->GetCurPage();?>?&product_name=<?=urlencode($item['NAME']);?>&action=add2basketamp&<?php if(isset($item["BUY_ID"]) && !empty($item["BUY_ID"])){ ?>id=<?=$item["BUY_ID"];?>&PRODUCT_BUY_ID=<?=$item["ID"];?><?php } else { ?>id=<?=$item["ID"];?><?php } ?>" class="btn btn-block btn-large">
                <?=GetMessage('CT_BCE_CATALOG_ADD');?>
            </a>
        <?php } ?>
    </div>
</div>
