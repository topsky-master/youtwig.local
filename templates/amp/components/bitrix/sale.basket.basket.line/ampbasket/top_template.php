<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */

?>
<? if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
<a href="<?= $arParams['PATH_TO_BASKET'] ?>">
    <?endif?>
    <amp-img src="/bitrix/templates/amp/images/mobile-cart.png" layout="responsive" height="24" width="24" class="cart-img">
    </amp-img>
    <span>
    <?php
    if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y'
    && ($arResult['NUM_PRODUCTS'] > 0
        || $arParams['SHOW_EMPTY_VALUES'] == 'Y')) {
    ?>

<?php echo $arResult['NUM_PRODUCTS']; ?>
<?php

    }
?>
    </span>
    <? if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
</a>
<? endif?>

