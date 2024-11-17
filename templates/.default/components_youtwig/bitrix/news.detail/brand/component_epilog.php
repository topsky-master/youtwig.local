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
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
$currPage = $_SERVER['ORIG_REQUEST_URI'];
$filterPage = preg_replace('~\?.*$~isu','',$currPage);
$domain = $_SERVER['HTTP_HOST'];

// Список статичных текстов для каждого домена
$staticTexts = [
    'youtwig.ru' => ' - в Москве',
    'spb.youtwig.ru' => ' - в Санкт-Петербурге',
    'ekaterinburg.youtwig.ru' => ' - в Екатеринбурге',
    // Добавьте другие домены и соответствующие тексты
];

$staticText = isset($staticTexts[$domain]) ? $staticTexts[$domain] : '';



if (preg_match('~/filter/$~isu',$filterPage)) {
    $filterPage = str_ireplace('/filter/','/',$filterPage);
    LocalRedirect($filterPage);
}

?>
<div class="about-model-area about-brand-area clearfix">
    <div class="row about-model-area clearfix">
        <div class="col-md-3 col-sm-12 col-xs-12 about-model-info">
            <div class="about-model-download">
                <div class="col-md-12 col-sm-6">
                    <?if(!is_array($arResult["PREVIEW_PICTURE"])):?>
                        <?$arResult["PREVIEW_PICTURE"] = array("SRC" => $templateFolder."/images/noimage.png", "ALT" => "", "TITLE" => "");?>
                    <?endif;?>
                    <?if(is_array($arResult["PREVIEW_PICTURE"])):?>
                        <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arResult["PREVIEW_PICTURE"]["TITLE"]?>" class="img-responsive" />
                    <?endif?>
                </div>
                <?php if(!empty($arResult["PREVIEW_TEXT"]) && $arParams["DISPLAY_PREVIEW_TEXT"]!="N"){?>
                    <div class="about-instruction col-md-12 col-sm-6">
                        <?echo $arResult["PREVIEW_TEXT"];?>
                    </div>
                <?php }?>
            </div>

            <?

            $products       = isset($arResult["PRODUCTS"])
            &&!empty($arResult["PRODUCTS"])
                ? $arResult["PRODUCTS"]
                : array();

            if(!empty($products)) {

                global $arrFilter;

                $bFilterPath = preg_replace('~/filter/.*$~i','/',$APPLICATION->GetCurDir());
                $smartFilterPath = preg_replace('~.*?/filter/~i','/',(isset($_GET['BRAND_SMART_FILTER_PATH']) ? trim($_GET['BRAND_SMART_FILTER_PATH']) : $APPLICATION->GetCurDir()));

                global $USER;

                $arrFilter["=ID"] = $products;

                if($bFilterPath != $APPLICATION->GetCurDir() || !empty($smartFilterPath)){
                    $APPLICATION->AddHeadString('<link rel="canonical" href="'.(CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . $bFilterPath . '"/>');
                }

                ?>
                <div id="smart_filter">
                    <?

                    $bHideExpanded = 'N';

                    if(class_exists('\Bitrix\Conversion\Internals\MobileDetect')) {
                        $mDetect = new \Bitrix\Conversion\Internals\MobileDetect;
                        if($mDetect->isMobile()){
                            $bHideExpanded = 'Y';
                        };
                    }

                    global $arrFilter;

                    twigBuildSectionFilter::skipAnalogueFilter($arrFilter);

                    $APPLICATION->IncludeComponent(
                        "impel:catalog.smart.filter",
                        "nhcompare",
                        array(
                            'HIDE_EXPANDED' => $bHideExpanded,
                            'SHOW_ALL_WO_SECTION' => 'Y',
                            'IBLOCK_TYPE' => "catalog",
                            'IBLOCK_ID' => 11,
                            'FILTER_NAME' => 'arrFilter',
                            'PRICE_CODE' =>
                                array(
                                    0 => 'Розничная',
                                ),
                            'CACHE_TYPE' => 'A',
                            'CACHE_TIME' => '3600',
                            'CACHE_GROUPS' => 'Y',
                            'SAVE_IN_SESSION' => 'Y',
                            'FILTER_VIEW_MODE' => 'VERTICAL',
                            'XML_EXPORT' => 'Y',
                            'SECTION_TITLE' => 'NAME',
                            'SECTION_DESCRIPTION' => 'DESCRIPTION',
                            'HIDE_NOT_AVAILABLE' => 'Y',
                            'TEMPLATE_THEME' => 'blue',
                            'CONVERT_CURRENCY' => 'Y',
                            'CURRENCY_ID' => 'RUB',
                            'SEF_MODE' => 'Y',
                            'SMART_FILTER_PATH' => $smartFilterPath,
                            'PAGER_PARAMS_NAME' => 'arrPager',
                            'INSTANT_RELOAD' => 'N',
                            'DISPLAY_ELEMENT_COUNT' => 'N',
                            'COMPOSITE_FRAME_MODE' => 'N',
                            'COMPOSITE_FRAME_TYPE' => 'DYNAMIC_WITH_STUB',
                            'ELEMENTS_COLLAPSE' => 10,
                            'DISPLAY_CODES' => 'TYPEPRODUCT,ONSTOCK',
                            'SEF_RULE' => $bFilterPath . 'filter/#SMART_FILTER_PATH#/',
                            "HIDE_CURRENTLY_SELECTED" => "N"

                        ),
                        false
                    );

                    $filter_set = false;

                    global $arrFilter;

                    if ((isset($arrFilter)
                        && !empty($arrFilter))
                    ) {

                        foreach ($arrFilter as $filter_key => $filter_value) {
                            if (mb_stripos($filter_key, '=PROPERTY_') !== false) {
                                $filter_set = true;
                                break;
                            };
                        };

                    };

                    $APPLICATION->SetPageProperty('filter_set', (int)$filter_set);

                    unset($arrFilter['FACET_OPTIONS']);


                    ?>
                </div>
                <?
            }

            ?>
        </div>
        <div class="col-md-9 col-sm-12 col-xs-12 products-list-info">
            <?

            $code = $_REQUEST['ELEMENT_CODE'];
            unset($_REQUEST['ELEMENT_CODE'], $_GET['ELEMENT_CODE'], $_REQUEST['SECTION_CODE_PATH'], $_GET['SECTION_CODE_PATH']);

            if(!empty($products)){

                global $NavNum;
                $NavNum = 0;

                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "nbrand",
                    Array(
                        "MODULE_TITLE" => "Популярные товары",
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
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "DISPLAY_COMPARE" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "ELEMENT_COUNT" => "15",
                        "ELEMENT_SORT_FIELD" => "sort",
                        "ELEMENT_SORT_FIELD2" => "id",
                        "ELEMENT_SORT_ORDER" => "asc",
                        "ELEMENT_SORT_ORDER2" => "desc",
                        "ENLARGE_PRODUCT" => "STRICT",
                        "FILTER_NAME" => "arrFilter",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                        "IBLOCK_ID" => 11,
                        "IBLOCK_TYPE" => 'catalog',
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
                        "PAGER_TEMPLATE" => "pager",
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
                        "PROPERTY_CODE" => array("SPECIALOFFER","SALELEADER","OLD_PRICE", "ARTNUMBER", "COM_BLACK", "QUALITY", "NEWPRODUCT", "SALEPRODUCT", "TYPEPRODUCT", "MANUFACTURER"),
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
                        "VIEW_MODE" => "SECTION",
                    )
                );

                $APPLICATION->SetPageProperty("SEO_TITLE_H1", sprintf(GetMessage('TMPL_BRAND_TITLE'),$arResult['NAME']) . $staticText);

            };


            global $arrFilter;

            $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>11, "CODE"=>"MANUFACTURER","XML_ID" => $code));
            if($property_enums && $enum_fields = $property_enums->GetNext())
            {
                $arrFilter['=PROPERTY_44'] = [$enum_fields["ID"]];
            }

            if (isset($_SERVER['ORIG_REQUEST_URI'])
                && mb_stripos($_SERVER['ORIG_REQUEST_URI'],'/filter/') !== false) {

                $currPage = preg_replace('~\?.*$~','',$currPage);
                $currPage = rtrim($currPage,'/');
                $currPage .= '/manufacturer-is-'.$code.'/';

                $intSectionID = 0;
                $arParams['FILTER_NAME'] = 'arrFilter';

                twigSeoSections::printSeoAndSetTitles($intSectionID,$arParams,604800,$currPage);

            } else {

                $value = isset($enum_fields["VALUE"]) ? trim($enum_fields["VALUE"]) : '';
                $replaces = ['[manufacturer: {value} ]' => $value];

                twigSeoSections::printSeoAndSetTitlesSection(0,$arParams, 604800, '', $replaces);

            }

            ?>
        </div>
    </div>
</div>