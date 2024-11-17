<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?$ElementID                                                = $APPLICATION->IncludeComponent(
    "bitrix:news.detail",
    "",
    Array(
        "DISPLAY_DATE"                                      =>$arParams["DISPLAY_DATE"],
        "DISPLAY_NAME"                                      =>$arParams["DISPLAY_NAME"],
        "DISPLAY_PICTURE"                                   =>$arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT"                              =>$arParams["DISPLAY_PREVIEW_TEXT"],
        "IBLOCK_TYPE"                                       =>$arParams["IBLOCK_TYPE"],
        "IBLOCK_ID"                                         =>$arParams["IBLOCK_ID"],
        "FIELD_CODE"                                        =>$arParams["DETAIL_FIELD_CODE"],
        "PROPERTY_CODE"                                     =>$arParams["DETAIL_PROPERTY_CODE"],
        "DETAIL_URL"	                                    =>$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL"	                                    =>$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "META_KEYWORDS"                                     =>$arParams["META_KEYWORDS"],
        "META_DESCRIPTION"                                  =>$arParams["META_DESCRIPTION"],
        "BROWSER_TITLE"                                     =>$arParams["BROWSER_TITLE"],
        "DISPLAY_PANEL"                                     =>$arParams["DISPLAY_PANEL"],
        "SET_TITLE"                                         =>$arParams["SET_TITLE"],
        "SET_STATUS_404"                                    =>$arParams["SET_STATUS_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN"                         =>$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
        "ADD_SECTIONS_CHAIN"                                =>$arParams["ADD_SECTIONS_CHAIN"],
        "ACTIVE_DATE_FORMAT"                                =>$arParams["DETAIL_ACTIVE_DATE_FORMAT"],
        "CACHE_TYPE"                                        =>$arParams["CACHE_TYPE"],
        "CACHE_TIME"                                        =>$arParams["CACHE_TIME"],
        "CACHE_GROUPS"                                      =>$arParams["CACHE_GROUPS"],
        "USE_PERMISSIONS"                                   =>$arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS"                                 =>$arParams["GROUP_PERMISSIONS"],
        "DISPLAY_TOP_PAGER"                                 =>$arParams["DETAIL_DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER"                              =>$arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE"                                       =>$arParams["DETAIL_PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS"                                 =>"N",
        "PAGER_TEMPLATE"                                    =>$arParams["DETAIL_PAGER_TEMPLATE"],
        "PAGER_SHOW_ALL"                                    =>$arParams["DETAIL_PAGER_SHOW_ALL"],
        "CHECK_DATES"                                       =>$arParams["CHECK_DATES"],
        "ELEMENT_ID"                                        =>$arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE"                                      =>$arResult["VARIABLES"]["ELEMENT_CODE"],
        "IBLOCK_URL"                                        =>$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
        "USE_SHARE" 			                            =>$arParams["USE_SHARE"],
        "SHARE_HIDE" 			                            =>$arParams["SHARE_HIDE"],
        "SHARE_TEMPLATE" 		                            =>$arParams["SHARE_TEMPLATE"],
        "SHARE_HANDLERS" 		                            =>$arParams["SHARE_HANDLERS"],
        "SHARE_SHORTEN_URL_LOGIN"	                        =>$arParams["SHARE_SHORTEN_URL_LOGIN"],
        "SHARE_SHORTEN_URL_KEY"                             =>$arParams["SHARE_SHORTEN_URL_KEY"],
        "ADD_ELEMENT_CHAIN"                                 =>(isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
        "SORT_BY1"                                          =>$arParams["SORT_BY1"],
        "SORT_BY2"                                          =>$arParams["SORT_BY2"],
        "SORT_ORDER1"                                       =>$arParams["SORT_ORDER1"],
        "SORT_ORDER2"                                       =>$arParams["SORT_ORDER2"],
    ),
    $component
);

$products = [];

$db_props = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ElementID, array("sort" => "asc"), Array("CODE"=>"PRODUCTS"));

if(is_object($db_props) && method_exists($db_props,'Fetch')){
    while($ar_props = $db_props->Fetch()){
        $ar_props['VALUE'] = trim($ar_props['VALUE']);
        if ($ar_props['VALUE']) {
            $products[] = $ar_props['VALUE'];
        }
    };
};

if (!empty($products)):

    global $arrNFilter;
    $arrNFilter['ID'] = $products;

    ?>
    <?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "articles",
    Array(
        "MODULE_TITLE" => "",
        "CUSTOM_ELEMENT_SORT" => $arrNFilter,
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
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "N",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "CONVERT_CURRENCY" => "Y",
        "CURRENCY_ID" => "RUB",
        "DETAIL_URL" => "/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
        "DISABLE_INIT_JS_IN_COMPONENT" => "Y",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_COUNT" => "8",
        "ELEMENT_SORT_FIELD" => "",
        "ELEMENT_SORT_FIELD2" => "",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "arrNFilter",
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
        "MESS_BTN_COMPARE" => "Сравнить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "(нет на складе)",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "OFFERS_LIMIT" => "5",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "20",
        "PARTIAL_PRODUCT_PROPERTIES" => "Y",
        "PRICE_CODE" => array("Розничная"),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PROPERTY_CODE" => array("ARTNUMBER","COM_BLACK","NEWPRODUCT","SALEPRODUCT","OLD_PRICE","SPECIALOFFER","QUALITY"),
        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
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
);
endif;