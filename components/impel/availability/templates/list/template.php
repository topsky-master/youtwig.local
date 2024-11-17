<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?php

$notavailable = !empty($arParams['PRICE']['PRICE']) ? false : true;
$inStock = $notavailable || !$arResult['CAN_BUY'] ? false : true;
$not_much = isset($arParams['NOT_MUCH']) && !empty($arParams['NOT_MUCH']) ? (int)$arParams['NOT_MUCH'] : 5;
$not_much = empty($not_much) ? 5 : $not_much;

if(isset($arParams['SCHEMA_AVAIL'])
    && $arParams['SCHEMA_AVAIL'] == 'Y'):
    ?>
    <link itemprop="availability" href="http://schema.org/<?=($inStock ? 'InStock' : 'OutOfStock');?>" />
<?
endif;
?>
<?php if(isset($arParams['IN_STOCK_LABEL'])
    && ($arParams['IN_STOCK_LABEL'] == 'Y')): ?>
    <div class="in-stock-label"<?php if(!empty($arResult['STORES_TOOLTIP'])): ?><?php if (IMPEL_SERVER_NAME == 'youtwig.ru'): ?> data-toggle="popover"<?php endif; ?> data-placement="top" data-trigger="hover" data-html="true" data-content="<?=htmlspecialchars("<p>".join("</p><p>",$arResult['STORES_TOOLTIP'])."</p>",ENT_QUOTES, LANG_CHARSET);?>"<?php endif; ?>>
        <?php if(!$arResult['HAS_PROVIDER']): ?>
            <?=GetMessage('CT_BCS_CATALOG_IN_STOCK');?>
        <?php else: ?>
            <?=GetMessage('CT_BNL_AVAIL_REMOTE');?>
        <?php endif; ?>
        <span class="<?=$arResult['HAS_PROVIDER']?'in-remote':($inStock?'in-stock':'out-of-stock');?>
        <?=($inStock && $arResult['PRINT_QUANTITY'] < $not_much)?(' not-much'):'';?>">
        <?=$arResult['HAS_PROVIDER'] ? '' : ($inStock?(($arResult['PRINT_QUANTITY'] < $not_much) ? GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH') : GetMessage('CT_BCS_CATALOG_IN_STOCK_YES')):GetMessage('CT_BCS_CATALOG_IN_STOCK_NO'));?>
        </span>
    </div>
<?php endif; ?>
<div class="item-links" aaa>
    <?php if(isset($arParams['PRODUCT_URL'])
        && !empty($arParams['PRODUCT_URL'])): ?>
        <a class="btn btn-info" href="<?=$arParams['PRODUCT_URL'];?>">
            <?=($arParams['MESS_BTN_BUY'])?>
        </a>
    <?php elseif($arResult['CAN_BUY']): ?>
        <input type="text" class="order_quantity line_quantity" value="1" min="1" max="<?=$arResult['MAX_QUANTITY'];?>" />
        <button onClick="yaCounter21503785.reachGoal('kupit');" class="btn btn-info btn-buy" data-toggle="modal" data-target="#modalCart" data-product-id="<?=$arResult['PRODUCT_ID'];?>" itemscope="" itemtype="http://schema.org/BuyAction">
            <?=($arParams['MESS_BTN_ADD_TO_BASKET'])?>
        </button>
        <?php if(isset($arParams['ONE_CLICK_ORDER'])
            && !$arResult['HAS_PROVIDER']
            && ($arParams['ONE_CLICK_ORDER'] == 'Y')): ?>
            <button class="btn btn-default btn-oneclick btn-preorder-<?=$arResult['PRODUCT_ID'];?>"<?php if($arResult['IS_PREODERED']): ?> disabled="disabled"<?php endif; ?> data-toggle="modal" data-target="#modalOCBuy" data-product-id="<?=$arResult['PRODUCT_ID'];?>">
                <?=GetMessage('CATALOG_ONE_CLICK_ORDER');?>
            </button>
        <?php endif; ?>
        <?php if($arResult['HAS_PROVIDER']): ?>
            <div class="remote-cost hidden"><?php echo GetMessage('CT_BNL_AVAIL_REMOTE_COST'); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <?php if(isset($arParams['ONE_CLICK_PREORDER'])
            && ($arParams['ONE_CLICK_PREORDER'] == 'Y')
            && $arResult['HAS_PRICE']): ?>
            <button class="btn btn-default btn-preorder btn-preorder-<?=$arResult['PRODUCT_ID'];?>"<?php if($arResult['IS_PREODERED']): ?> disabled="disabled"<?php endif; ?> data-toggle="modal" data-target="#modalOCBuy" data-product-id="<?=$arResult['PRODUCT_ID'];?>">
                <?=GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>