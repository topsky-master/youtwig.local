<?if(!defined('CATALOG_INCLUDED')) die(); ?>
<?$APPLICATION->IncludeComponent(
    "impel:availability",
    "",
    array(
        'PRICE' => $arParams['PRICE'],
        'NOT_MUCH' => $arParams['NOT_MUCH'],
        'PRODUCT_ID' => $arParams['PRODUCT_ID'],
        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
        'ONE_CLICK_ORDER' => $arParams['ONE_CLICK_ORDER'],
        'SCHEMA_AVAIL' => $arParams['SCHEMA_AVAIL'],
        'HAS_PRICE' => isset($arParams['HAS_PRICE']) ? $arParams['HAS_PRICE'] : null,
        'ONE_CLICK_PREORDER' => $arParams['ONE_CLICK_PREORDER'],
        'IN_STOCK_LABEL' => $arParams['IN_STOCK_LABEL'],
        'STORES_TOOLTIP' => isset($arParams['STORES_TOOLTIP']) ? $arParams['STORES_TOOLTIP'] : 'N',
        'PRODUCT_URL' => (isset($arParams['PRODUCT_URL']) && !empty($arParams['PRODUCT_URL']) ? $arParams['PRODUCT_URL'] : '')
    )
);
?>