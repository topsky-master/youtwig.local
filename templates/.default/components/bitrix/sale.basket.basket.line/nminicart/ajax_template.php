<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
?>
<div id="miniCart">
    <a href="<?=$arParams['PATH_TO_PERSONAL'];?>">
        <div class="counter-icon"><svg data-v-0016e1fe="" viewBox="0 0 24 24" width="24" height="24" class="icon"><use xlink:href="/bitrix/templates/nmain/images/sprite.svg#orders" href="/bitrix/templates/nmain/images/sprite.svg#orders"></use></svg></div>
        <span class="acq sup-count">
            <?=isset($arResult['NUM_PRODUCTS']) && !empty($arResult['NUM_PRODUCTS']) ? (int)$arResult['NUM_PRODUCTS'] : 0;?>
        </span>
    </a>
</div>