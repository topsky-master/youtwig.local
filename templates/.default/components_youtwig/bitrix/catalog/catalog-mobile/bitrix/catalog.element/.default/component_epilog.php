<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->SetAdditionalCSS($templateFolder."/css/advanced-slider-base.css");
$APPLICATION->SetAdditionalCSS("/bitrix/templates/.default/components/bitrix/sale.viewed.product/product_view/style.css");
$APPLICATION->AddHeadScript('/bitrix/templates/.default/components/bitrix/sale.order.ajax/main_test/js/jquery.maskedinput.min.js');
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.prettyPhoto.custom.min.js");
$APPLICATION->AddHeadScript($templateFolder."/js/froogaloop.min.js");
$APPLICATION->AddHeadScript($templateFolder."/js/video.min.js");
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.touchSwipe.min.js");
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.advancedSlider.min.js");
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.easing.js");
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.mousewheel.js");

if(isset($arResult['CANONICAL_URL'])){

    $canonical_url = $arResult['CANONICAL_URL'];

    if(isset($canonical_url) && !empty($canonical_url)){

        $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);

        $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
        $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
        $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);

        if($DETAIL_PAGE_URL != $SERVER_PAGE_URL){
            $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
            $canonical_url = preg_replace('~://m\.~isu','://',$canonical_url);
            $APPLICATION->SetPageProperty('MOBILE_CANONICAL', $canonical_url);
        };

    };
};


if (isset($templateData['TEMPLATE_THEME']))
{
    $APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}


unset($_REQUEST['SECTION_CODE_PATH'],$_REQUEST['ELEMENT_CODE'],$_GET['SECTION_CODE_PATH'],$_GET['ELEMENT_CODE']);

?>
    <div id="comments" class="tab-pane comments elements clearfix">
        <?

        $staticHTMLCache = \Bitrix\Main\Data\StaticHTMLCache::getInstance();
        $staticHTMLCache->disableVoting();

        $APPLICATION->IncludeComponent(
            "bitrix:iblock.vote",
            "schema",
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

        $APPLICATION->IncludeComponent(
            "bitrix:forum.topic.reviews",
            "comments",
            Array(
                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                "CACHE_TIME" => $arParams['CACHE_TIME'],
                "MESSAGES_PER_PAGE" => 15,
                "USE_CAPTCHA" => "N",
                "FORUM_ID" => 1,
                "SHOW_LINK_TO_FORUM" => "N",
                "ELEMENT_ID" => $arResult['ID'],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "SHOW_MINIMIZED" => "N",
                "AJAX_MODE" => "Y",
                "AJAX_POST" => "Y",
                "COMPOSITE_FRAME_MODE" => "A",
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
                "URL_TEMPLATES_READ" => ""
            )
        );

        $staticHTMLCache->enableVoting();
        $hasActiveTabContent = true;
        ?>
    </div>
    </div>
    </div>
    <div class="other products clearfix" id="other_products">
        <h3>
            <?php  echo GetMessage("OTHER_PRODUCTS");?>
        </h3>
        <div class="wrapper other products clearfix" id="other_products_wrapper">
            <?

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
                "bitrix:catalog.top",
                "lists",
                Array(
                    "ACTION_VARIABLE" => "action",
                    "ADD_PICT_PROP" => "-",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                    "DETAIL_URL" => $arParams["SEF_URL_TEMPLATES"]["element"],
                    "DISPLAY_COMPARE" => "N",
                    "ELEMENT_COUNT" => "15",
                    "ELEMENT_SORT_FIELD" => "sort",
                    "ELEMENT_SORT_FIELD2" => "id",
                    "ELEMENT_SORT_ORDER" => "asc",
                    "ELEMENT_SORT_ORDER2" => "desc",
                    "FILTER_NAME" => "arrTFilter",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                    "IBLOCK_TYPE" => $arResult["IBLOCK_TYPE"],
                    "LABEL_PROP" => "-",
                    "LINE_ELEMENT_COUNT" => "4",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "MESS_BTN_BUY" => "Купить",
                    "MESS_BTN_COMPARE" => "Сравнить",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_NOT_AVAILABLE" => GetMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE'),
                    "OFFERS_LIMIT" => "5",
                    "PARTIAL_PRODUCT_PROPERTIES" => "Y",
                    "PRICE_CODE" => array("Розничная"),
                    "PRICE_VAT_INCLUDE" => "Y",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "PRODUCT_PROPERTIES" => array(),
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PRODUCT_QUANTITY_VARIABLE" => "",
                    "PROPERTY_CODE" => array("QUALITY","COM_BLACK", "NEWPRODUCT", "SALEPRODUCT", ""),
                    "ROTATE_TIMER" => "3",
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "SECTION_URL" => $arParams["SEF_URL_TEMPLATES"]["section"],
                    "SEF_MODE" => "Y",
                    "SHOW_CLOSE_POPUP" => "Y",
                    "SHOW_DISCOUNT_PERCENT" => "N",
                    "SHOW_OLD_PRICE" => "N",
                    "SHOW_PAGINATION" => "Y",
                    "SHOW_PRICE_COUNT" => "1",
                    "TEMPLATE_THEME" => "",
                    "USE_PRICE_COUNT" => "N",
                    "USE_PRODUCT_QUANTITY" => "N",
                    "VIEW_MODE" => "SLIDER",
                    "LIST_IMAGE_HEIGHT" => 186,
                    "LIST_IMAGE_WIDTH" => 186,
                    "IS_RELATED_TO" => "Y"
                )
            );

            ?>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
<? $staticHTMLCache->enableVoting(); ?>
<?

if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
    $loadCurrency = false;
    if (!empty($templateData['CURRENCIES']))
        $loadCurrency = Loader::includeModule('currency');
    CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
    if ($loadCurrency)
    {
        ?>
        <script type="text/javascript">
            BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
        </script>
        <?
    }
}
if (isset($templateData['JS_OBJ']))
{
    ?><script type="text/javascript">
    BX.ready(BX.defer(function(){
        if (!!window.<? echo $templateData['JS_OBJ']; ?>)
        {
            window.<? echo $templateData['JS_OBJ']; ?>.allowViewedCount(true);
        }
    }));
</script><?
}

$image = '';

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&is_array($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&sizeof($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
    &&!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
){

    $gallery = $arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"];
    $image = current($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]);

} elseif($arParams["DISPLAY_PICTURE"]!="N"){

    if(isset($arResult["PREVIEW_PICTURE"])
        && isset($arResult["PREVIEW_PICTURE"]["SRC"])
        && !empty($arResult["PREVIEW_PICTURE"]["SRC"])){

        $image = $arResult["PREVIEW_PICTURE"]["SRC"];

    } elseif(isset($arResult["DETAIL_PICTURE"])
        && isset($arResult["DETAIL_PICTURE"]["SRC"])
        && !empty($arResult["DETAIL_PICTURE"]["SRC"])){

        $image = $arResult["PREVIEW_PICTURE"]["SRC"];

    };

};


if(!empty($image) && function_exists('rectangleImage')){

    $image = rectangleImage($_SERVER['DOCUMENT_ROOT'].'/'.$image,370,370,$image);

};


if(!empty($image)){
    $APPLICATION->SetPageProperty("ogimage", IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$image);
};

$APPLICATION->AddHeadString('<link rel="amphtml" href="'.((CMain::IsHTTPS() ? 'https' : 'http') . '://' . preg_replace('~^m\.~is','',IMPEL_SERVER_NAME) . '/amp/catalog/' . $arResult['CODE'] . '/'.'" />'));

if(change_to_mobile){
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.css');
}


?>