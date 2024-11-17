<?
define("STOP_STATISTICS", true);
define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket.line",
    "minicart",
    array(
        "COMPONENT_TEMPLATE" => "minicart",
        "PATH_TO_BASKET" => "/personal/cart/",
        "PATH_TO_ORDER" => "/personal/cart/",
        "SHOW_DELAY" => "N",
        "SHOW_NOTAVAIL" => "N",
        "SHOW_SUBSCRIBE" => "N",
        "SHOW_NUM_PRODUCTS" => "Y",
        "SHOW_TOTAL_PRICE" => "Y",
        "SHOW_EMPTY_VALUES" => "Y",
        "SHOW_PERSONAL_LINK" => "N",
        "PATH_TO_PERSONAL" => SITE_DIR."personal/cart/",
        "SHOW_AUTHOR" => "N",
        "PATH_TO_REGISTER" => SITE_DIR."registration/",
        "PATH_TO_PROFILE" => SITE_DIR."personal/cart/",
        "SHOW_PRODUCTS" => "N",
        "POSITION_FIXED" => "N",
        "HIDE_ON_BASKET_PAGES" => "N",
    ),
    false
);

?>
