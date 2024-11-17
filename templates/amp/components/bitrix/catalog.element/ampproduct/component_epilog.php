<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

global $APPLICATION;
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$amp_scripts = '<script async custom-template="amp-mustache" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
    <script async custom-element="amp-carousel" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
    <script async custom-element="amp-lightbox" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>
';


if(mb_stripos($arResult["DETAIL_TEXT"],'video') !== false 
|| mb_stripos($arResult["PREVIEW_TEXT"],'video') !== false
|| mb_stripos($arResult["DETAIL_TEXT"],'youtube') !== false 
|| mb_stripos($arResult["PREVIEW_TEXT"],'youtube') !== false
) {
	$amp_scripts .= '<script async custom-element="amp-youtube" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>
    <script async custom-element="amp-video" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>';
}	

if(mb_stripos($arResult["DETAIL_TEXT"],'iframe') !== false 
|| mb_stripos($arResult["PREVIEW_TEXT"],'iframe') !== false) {
    $amp_scripts .= '<script async custom-element="amp-iframe" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';
}    
	
$APPLICATION->AddViewContent('AMP_SCRIPTS',$amp_scripts);

if(isset($arResult["PROPERTIES"])
    &&isset($arResult["PROPERTIES"]["ANALOGUE"])
    &&isset($arResult["PROPERTIES"]["ANALOGUE"]["VALUE"])
    &&!empty($arResult["PROPERTIES"]["ANALOGUE"]["VALUE"])
) {

    $dKey = array_search($arResult['ID'], $arResult["PROPERTIES"]["ANALOGUE"]["VALUE"]);

    if ($dKey !== false) {
        unset($arResult["PROPERTIES"]["ANALOGUE"]["VALUE"][$dKey]);
    }

    global $arrAFilter;

    $arrAFilter = array('ID' => $arResult["PROPERTIES"]["ANALOGUE"]["VALUE"]);
    ?>
    <div class="sublevel">
		<input id="tanalog" type="checkbox" />
        <label for="tanalog">
            <?=GetMessage('TMPL_ORIGINAL_ANALOGUE_TITLE');?>
        </label>
        <div class="tab-content">
            <?php

            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "ampsimilar",
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
                    "AJAX_OPTION_STYLE" => "N",
                    "BACKGROUND_IMAGE" => "-",
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "BROWSER_TITLE" => "-",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "COMPATIBLE_MODE" => "Y",
                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                    "CUSTOM_FILTER" => "",
                    "DETAIL_URL" => isset($arParams["SEF_URL_TEMPLATES"]) ? $arParams["SEF_URL_TEMPLATES"]["element"] : $arParams["DETAIL_URL"] ,
                    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "DISPLAY_COMPARE" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "ELEMENT_SORT_FIELD" => "sort",
                    "ELEMENT_SORT_FIELD2" => "sort",
                    "ELEMENT_SORT_ORDER" => "asc",
                    "ELEMENT_SORT_ORDER2" => "desc",
                    "ENLARGE_PRODUCT" => "STRICT",
                    "FILTER_NAME" => "arrAFilter",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                    "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                    "IBLOCK_TYPE" => $arResult["IBLOCK_TYPE"],
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "LABEL_PROP" => array(),
                    "LAZY_LOAD" => "N",
                    "LINE_ELEMENT_COUNT" => "3",
                    "LOAD_ON_SCROLL" => "N",
                    "MESSAGE_404" => "",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "MESS_BTN_BUY" => "Купить",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_BTN_SUBSCRIBE" => "Подписаться",
                    "MESS_NOT_AVAILABLE" => GetMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE'),
                    "META_DESCRIPTION" => "-",
                    "META_KEYWORDS" => "-",
                    "OFFERS_LIMIT" => "8",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => ".default",
                    "PAGER_TITLE" => "Товары",
                    "PAGE_ELEMENT_COUNT" => "18",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "PRICE_VAT_INCLUDE" => "Y",
                    "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "PRODUCT_PROPERTIES" => array(),
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                    "PRODUCT_SUBSCRIPTION" => "N",
                    "PROPERTY_CODE" => array("COM_BLACK", "NEWPRODUCT", "SALEPRODUCT", ""),
                    "PROPERTY_CODE_MOBILE" => array(),
                    "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                    "RCM_TYPE" => "personal",
                    "SECTION_CODE" => "",
                    "SECTION_CODE_PATH" => "",
                    "SECTION_ID" => "",
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "SECTION_URL" => isset($arParams["SEF_URL_TEMPLATES"]) ? $arParams["SEF_URL_TEMPLATES"]["section"] : $arParams["SECTION_URL"],
                    "SECTION_USER_FIELDS" => array("",""),
                    "SEF_MODE" => "Y",
                    "SEF_RULE" => isset($arParams["SEF_URL_TEMPLATES"]) ?$arParams["SEF_URL_TEMPLATES"]["element"] : $arParams["DETAIL_URL"],
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "SHOW_CLOSE_POPUP" => "N",
                    "SHOW_DISCOUNT_PERCENT" => "N",
                    "SHOW_FROM_SECTION" => "N",
                    "SHOW_MAX_QUANTITY" => "N",
                    "SHOW_OLD_PRICE" => "N",
                    "SHOW_PRICE_COUNT" => "1",
                    "SHOW_SLIDER" => "Y",
                    "SLIDER_INTERVAL" => "3000",
                    "SLIDER_PROGRESS" => "N",
                    "TEMPLATE_THEME" => "blue",
                    "USE_ENHANCED_ECOMMERCE" => "N",
                    "USE_MAIN_ELEMENT_SECTION" => "N",
                    "USE_PRICE_COUNT" => "N",
                    "USE_PRODUCT_QUANTITY" => "N",
                    "BLOCK_TITLE" => "",
                    "HAS_READMORE" => "Y"
                )
            );

            ?>
        </div>
    </div>
    <?
}

?>
    <div class="sublevel">
        <input id="tcomment" type="checkbox" checked="checked" />
		<label for="tcomment">
            <?=GetMessage('CT_BCE_CATALOG_COMMENTARY');?>
        </label>
        <div class="tab-content">
            <?php


            ob_start();

            $APPLICATION->IncludeComponent(
                "bitrix:forum.topic.reviews",
                "ampcomment",
                Array(
                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                    "MESSAGES_PER_PAGE" => 15,
                    "USE_CAPTCHA" => "Y",
                    "FORUM_ID" => 1,
                    "SHOW_LINK_TO_FORUM" => "N",
                    "ELEMENT_ID" => $arResult['ID'],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SHOW_MINIMIZED" => "N",
                    "AJAX_MODE" => "N",
                    "AJAX_POST" => "N",
                    "COMPOSITE_FRAME_MODE" => "N",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "FILES_COUNT" => "2",
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "PAGE_NAVIGATION_TEMPLATE" => "oldpager",
                    "NAME_TEMPLATE" => "",
                    "PREORDER" => "Y",
                    "RATING_TYPE" => "",
                    "SHOW_AVATAR" => "Y",
                    "SHOW_LINK_TO_FORUM" => "N",
                    "SHOW_MINIMIZED" => "N",
                    "SHOW_RATING" => "N",
                    "URL_TEMPLATES_DETAIL" => "",
                    "URL_TEMPLATES_PROFILE_VIEW" => "",
                    "URL_TEMPLATES_READ" => "",
                )
            );

            $reviewsContent = ob_get_clean();
            $reviewsContent = preg_replace('~<script[^>]*?>.*?</script>~isu','',$reviewsContent);
            $reviewsContent = preg_replace('~<script[^>]*?>~isu','',$reviewsContent);

            echo $reviewsContent;

            ?>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
<?php

if(isset($arResult['CANONICAL_URL'])){

    $canonical_url = $arResult['CANONICAL_URL'];

    if(isset($canonical_url) && !empty($canonical_url)){

        $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
        $canonical_url = preg_replace('~\:\/\/(www\.)*m\.~','://',$canonical_url);

        $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
        $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
        $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);

        if($DETAIL_PAGE_URL != $SERVER_PAGE_URL){

            if(get_class($this->__template)!=="CBitrixComponentTemplate")
                $this->InitComponentTemplate();

            $this->__template->SetViewTarget("CANONICAL_PROPERTY");
            ?>
            <link href="<?=$canonical_url;?>" rel="canonical" />
            <?
            $this->__template->EndViewTarget();

        };

    };
};

if(file_exists(__DIR__.'/amp_style.css')){

    $amp_style = file_get_contents(__DIR__.'/amp_style.css');
    if(get_class($this->__template)!=="CBitrixComponentTemplate")
        $this->InitComponentTemplate();

    $this->__template->SetViewTarget("AMP_STYLE");
    echo $amp_style;
    $this->__template->EndViewTarget();

}

$useVoteRating = ('Y' == $arParams['USE_VOTE_RATING']);


if ($useVoteRating){

    $APPLICATION->IncludeComponent(
        "bitrix:iblock.vote",
        "ampstars",
        array(
            "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
            "ELEMENT_ID" => $arResult['ID'],
            "ELEMENT_CODE" => "",
            "MAX_VOTE" => "5",
            "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
            "SET_STATUS_404" => "N",
            "DISPLAY_AS_RATING" => (empty($arParams['VOTE_DISPLAY_AS_RATING']) ? "vote_avg" : $arParams['VOTE_DISPLAY_AS_RATING']),
            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
            "CACHE_TIME" => $arParams['CACHE_TIME']
        ),
        $component,
        array("HIDE_ICONS" => "Y")
    );
}

global $arrTFilter;

if(isset($arResult['LINKED_ELEMETS'])
    && !empty($arResult['LINKED_ELEMETS'])){

    $arrTFilter = array(
        "ID" => $arResult['LINKED_ELEMETS']
    );

} else {

    $arrTFilter = array(
        "!PROPERTY_VIEW_POPULAR" => false,
        "!ID" => $arResult["ID"]
    );

};



$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "ampsimilar",
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
        "AJAX_OPTION_STYLE" => "N",
        "BACKGROUND_IMAGE" => "-",
        "BASKET_URL" => $arParams["BASKET_URL"],
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "Y",
        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
        "CURRENCY_ID" => $arParams["CURRENCY_ID"],
        "CUSTOM_FILTER" => "",
        "DETAIL_URL" => isset($arParams["SEF_URL_TEMPLATES"]) ? $arParams["SEF_URL_TEMPLATES"]["element"] : $arParams["DETAIL_URL"] ,
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "sort",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "arrTFilter",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => $arResult["IBLOCK_ID"],
        "IBLOCK_TYPE" => $arResult["IBLOCK_TYPE"],
        "INCLUDE_SUBSECTIONS" => "Y",
        "LABEL_PROP" => array(),
        "LAZY_LOAD" => "N",
        "LINE_ELEMENT_COUNT" => "3",
        "LOAD_ON_SCROLL" => "N",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => GetMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE'),
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "OFFERS_LIMIT" => "8",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "9",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PROPERTY_CODE" => array("COM_BLACK", "NEWPRODUCT", "SALEPRODUCT", ""),
        "PROPERTY_CODE_MOBILE" => array(),
        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
        "RCM_TYPE" => "personal",
        "SECTION_CODE" => "",
        "SECTION_CODE_PATH" => "",
        "SECTION_ID" => "",
        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
        "SECTION_URL" => isset($arParams["SEF_URL_TEMPLATES"]) ? $arParams["SEF_URL_TEMPLATES"]["section"] : $arParams["SECTION_URL"],
        "SECTION_USER_FIELDS" => array("",""),
        "SEF_MODE" => "Y",
        "SEF_RULE" => isset($arParams["SEF_URL_TEMPLATES"]) ?$arParams["SEF_URL_TEMPLATES"]["element"] : $arParams["DETAIL_URL"],
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_ALL_WO_SECTION" => "Y",
        "SHOW_CLOSE_POPUP" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_FROM_SECTION" => "N",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_SLIDER" => "Y",
        "SLIDER_INTERVAL" => "3000",
        "SLIDER_PROGRESS" => "N",
        "TEMPLATE_THEME" => "blue",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "BLOCK_TITLE" => "Похожие товары"
    )
);?>
    </div>
<?


