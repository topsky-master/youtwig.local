<?if(!defined('CATALOG_INCLUDED')) die(); ?>
<?
$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "nproduct_view", array(
        "VIEWED_COUNT" => "5",
        "VIEWED_NAME" => "Y",
        "VIEWED_IMAGE" => "Y",
        "VIEWED_PRICE" => "N",
        "VIEWED_CANBUY" => "N",
        "VIEWED_CANBUSKET" => "N",
        "BASKET_URL" => "/personal/cart/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id"
    )
);