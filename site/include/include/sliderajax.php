<?
il::define("STOP_STATISTICS", true);
il::define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?><?
global $NavNum, $arrFilter; $NavNum = 0;

if(isset($_REQUEST['product_id'])){

    $product_id = il::trim($_REQUEST['product_id']);
    $product_id = il::explode(',',$product_id);

    $product_id = (!is_array($product_id)
        && !empty($product_id))
        ? array($product_id)
        : $product_id;

    $product_id = il::array_map('trim',$product_id);
    $product_id = il::array_map('intval',$product_id);
    $product_id = il::array_unique($product_id);
    $product_id = il::array_filter($product_id);

    $arrFilter = array('ID' => $product_id);

}

$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "kitslider",
    Array(
        "ACTION_VARIABLE" => "action",
        "ADD_PICT_PROP" => "-",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_TO_BASKET_ACTION" => "ADD",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "BACKGROUND_IMAGE" => "-",
        "BASKET_URL" => "/personal/cart/",
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "N",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "CONVERT_CURRENCY" => "Y",
        "CURRENCY_ID" => "RUB",
        "DETAIL_URL" => "/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_COUNT" => "4",
        "ELEMENT_SORT_FIELD" => "ID",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER" => $arrFilter["ID"],
		"ELEMENT_SORT_ORDER2" => "",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "arrFilter",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => 11,
        "IBLOCK_TYPE" => "catalog",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LABEL_PROP" => "-",
        "LAZY_LOAD" => "N",
        "LINE_ELEMENT_COUNT" => "4",
        "LIST_IMAGE_HEIGHT" => 204,
        "LIST_IMAGE_WIDTH" => 204,
        "LOAD_ON_SCROLL" => "N",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_COMPARE" => "Сравнение",
        "MESS_BTN_DETAIL" => "",
        "MESS_BTN_SUBSCRIBE" => "",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "MODULE_TITLE" => "",
        "OFFERS_LIMIT" => "5",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "pager",
        "PAGER_TITLE" => "",
        "PAGE_ELEMENT_COUNT" => "0",
        "PARTIAL_PRODUCT_PROPERTIES" => "Y",
        "PRICE_CODE" => array("Розничная"),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "PRODUCT_ROW_VARIANTS" => "[]",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PROPERTY_CODE" => array("","COM_BLACK","NEWPRODUCT","SALEPRODUCT","ARTNUMBER",""),
        "RCM_PROD_ID" => NULL,
        "RCM_TYPE" => "personal",
        "ROTATE_TIMER" => "",
        "SECTION_CODE" => "",
        "SECTION_CODE_PATH" => "",
        "SECTION_ID" => "",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_URL" => "/catalog/#SECTION_CODE_PATH#/",
        "SECTION_USER_FIELDS" => array("",""),
        "SEF_MODE" => "N",
        "SEF_RULE" => "",
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_ALL_WO_SECTION" => "Y",
        "SHOW_CLOSE_POPUP" => "Y",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_FROM_SECTION" => "N",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PAGINATION" => "Y",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_SLIDER" => "Y",
        "SLIDER_INTERVAL" => "3000",
        "SLIDER_PROGRESS" => "N",
        "TEMPLATE_THEME" => "",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "VIEW_MODE" => "SECTION"
    )
);?>