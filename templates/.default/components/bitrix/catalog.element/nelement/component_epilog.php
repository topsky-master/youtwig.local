<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var CUser $USER
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 * @global $arResult
 */

global $APPLICATION;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->SetAdditionalCSS('/local/templates/nmain/css/swiper-bundle.min.css'); 
$APPLICATION->AddHeadScript('/local/templates/nmain/js/swiper-bundle.min.js');

twigModelElement::printSeoAndTitlesAtEpilog($arResult,$arParams);
twigModelElement::incScriptsAtEpilog($arResult,$arParams);
twigModelElement::incStylesAtEpilog($arResult,$arParams);


$ids = twigModelElement::getBigData($arResult);

if(!empty($ids)) {

    global $arrFilter;
    $aSort = $aSort1 = $ids;
    $arrFilter = array('ID' => $ids);

    ?>
    <h2 class="h3 h3-kit-title"><?=GetMessage('TMPL_PRODUCTS_KIT');?></h2>
    <?

    $APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"kitslider", 
	array(
		"MODULE_TITLE" => "",
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
		"BASKET_URL" => $arParams["BASKET_URL"],
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "-1",
		"CACHE_TYPE" => "N",
		"COMPATIBLE_MODE" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONVERT_CURRENCY" => "N",
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"DISABLE_INIT_JS_IN_COMPONENT" => "Y",
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
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_TYPE" => "system",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => "-",
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "4",
		"LIST_IMAGE_HEIGHT" => "204",
		"LIST_IMAGE_WIDTH" => "204",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => $arParams["MESS_BTN_ADD_TO_BASKET"],
		"MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
		"MESS_BTN_COMPARE" => $arParams["MESS_BTN_COMPARE"],
		"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
		"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
		"MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_LIMIT" => "5",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "pager",
		"PAGER_TITLE" => "",
		"PAGE_ELEMENT_COUNT" => "20",
		"PARTIAL_PRODUCT_PROPERTIES" => "Y",
		"PRICE_CODE" => array(
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "COM_BLACK",
			2 => "NEWPRODUCT",
			3 => "SALEPRODUCT",
			4 => "ARTNUMBER",
			5 => "",
		),
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"RCM_TYPE" => "any_personal",
		"ROTATE_TIMER" => "",
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"SECTION_URL" => $arParams["SECTION_URL"],
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
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
		"VIEW_MODE" => "SECTION",
		"COMPONENT_TEMPLATE" => "kitslider"
	),
	false
);

}

if(empty($ids) && isset($arResult["MODEL_HTML_IDS"])
    &&!empty($arResult["MODEL_HTML_IDS"])
) {

    global $arrFilter;

    //shuffle($arResult["MODEL_HTML_IDS"]);
    $aSort = $aSort1 = array();

    foreach($arResult["MODEL_HTML_IDS"] as $product_id) {
        $can_buy = canYouBuy($product_id);
        $quantity = get_quantity_product($product_id);
        $can_buy = $quantity > 0 && $can_buy ? $quantity : 0;
        if($can_buy > 0) {
            $aSort[$product_id] = $can_buy;
        } else {
            $aSort1[$product_id] = $product_id;
        }
    }

    $aSort1 = array_values($aSort1);
    shuffle($aSort1);

    uasort($aSort,function($a,$b){
        if ($a == $b) {
            return 0;
        }
        return ($a > $b) ? -1 : 1;
    });

    $aSort = array_keys($aSort);

    if(!empty($aSort1)) {
        $aSort = array_merge($aSort,$aSort1);
    }

    $arrFilter = array('ID' => $aSort);

    ?>
    <p class="h3 h3-kit-title"><?=GetMessage('TMPL_PRODUCTS_KIT');?></p>
    <?

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "kitslider",
        Array(
            "MODULE_TITLE" => "",
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
            "BASKET_URL" => $arParams["BASKET_URL"],
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            // "CACHE_TIME" => $arParams["CACHE_TIME"],
            // "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => -1,
            "CACHE_TYPE" => "N",
            "COMPATIBLE_MODE" => "N",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
            "CURRENCY_ID" => $arParams['CURRENCY_ID'],
            "DETAIL_URL" => $arParams["DETAIL_URL"],
            "DISABLE_INIT_JS_IN_COMPONENT" => "Y",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "DISPLAY_COMPARE" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_COUNT" => "4",
            "ELEMENT_SORT_FIELD" => "ID",
            "ELEMENT_SORT_FIELD2" => "",
            "ELEMENT_SORT_ORDER" => $arrFilter['ID'],
            "ELEMENT_SORT_ORDER2" => "",
            "ENLARGE_PRODUCT" => "STRICT",
            "FILTER_NAME" => "arrFilter",
            "HIDE_NOT_AVAILABLE" => "N",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "INCLUDE_SUBSECTIONS" => "Y",
            "LABEL_PROP" => "-",
            "LAZY_LOAD" => "N",
            "LINE_ELEMENT_COUNT" => "4",
            "LIST_IMAGE_HEIGHT" => 204,
            "LIST_IMAGE_WIDTH" => 204,
            "LOAD_ON_SCROLL" => "N",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => $arParams["MESS_BTN_ADD_TO_BASKET"],
            "MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
            "MESS_BTN_COMPARE" => $arParams["MESS_BTN_COMPARE"],
            "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
            "MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
            "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_LIMIT" => "5",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "pager",
            "PAGER_TITLE" => "",
            "PAGE_ELEMENT_COUNT" => "4",
            "PARTIAL_PRODUCT_PROPERTIES" => "Y",
            "PRICE_CODE" => $arParams["PRICE_CODE"],
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPERTIES" => array(),
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "",
            "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
            "PRODUCT_SUBSCRIPTION" => "N",
            "PROPERTY_CODE" => array("","COM_BLACK","NEWPRODUCT","SALEPRODUCT","ARTNUMBER"),
            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE" => "personal",
            "ROTATE_TIMER" => "",
            "SECTION_CODE" => "",
            "SECTION_CODE_PATH" => "",
            "SECTION_ID" => "",
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "SECTION_URL" => $arParams['SECTION_URL'],
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
            "VIEW_MODE" => "SECTION",
        )
    );

}