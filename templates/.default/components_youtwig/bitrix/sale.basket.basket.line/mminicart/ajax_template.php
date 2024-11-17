<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); ?>
<i id="mobilemenucart">
<?=isset($arResult['NUM_PRODUCTS']) && !empty($arResult['NUM_PRODUCTS']) && is_numeric($arResult['NUM_PRODUCTS']) ? (int)$arResult['NUM_PRODUCTS'] : 0;?>
</i>