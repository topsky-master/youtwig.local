<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
?>
<div id="miniCart" class="bx-hdr-profile">
    <ul id="header_nav" class="nav navbar-nav navbar-right">
        <li id="fat-menu" class="dropdown">
            <a href="<?=$arParams['PATH_TO_PERSONAL'];?>" id="drop3">
                <span class="ajax_cart_total">
                    <?=(GetMessage('TSB1_CART')); ?>
                </span>
                <span class="top-cart-icon"></span>
                <span class="ajax_cart_quantity">
                    <?=isset($arResult['NUM_PRODUCTS']) && !empty($arResult['NUM_PRODUCTS']) ? (int)$arResult['NUM_PRODUCTS'] : 0;?>
                </span>
            </a>
        </li>
    </ul>
</div>