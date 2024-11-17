<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 * @var array $arResult
 */

$this->setFrameMode(false);

$item = $arResult;

unset($arResult);

$name = !empty($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $item['NAME'];
$title = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $item['NAME'];
$alt = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $item['NAME'];

if(    isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
    && !empty($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
){

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '<div class="full-detail-text">'.$item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"].'</div>';

};


/*if($USER->isAdmin()){
	echo 123;
	$APPLICATION->IncludeComponent(
		"cn:delivery.cost",
		"",
		array(
			"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		)
	);
}*/

ob_start();

$APPLICATION->IncludeComponent(
    "bitrix:iblock.vote",
    "schema",
    array(
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "ELEMENT_ID" => $item['ID'],
        "ELEMENT_CODE" => "",
        "MAX_VOTE" => "5",
        "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
        "SET_STATUS_404" => "N",
        "DISPLAY_AS_RATING" => "vote_avg",
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
    ),
    $component,
    array("HIDE_ICONS" => "Y")
);

$sRating = ob_get_clean();

$bHasRating = stripos($sRating,'http://schema.org/AggregateRating') === false ? false: true;

ob_start();

if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
    CAPIUncachedArea::includeFile(
        "/include/productreview.php",
        array(
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'ID' => $item['ID'],
            'IBLOCK_ID' => $arParams["IBLOCK_ID"],
            'IBLOCK_TYPE' => $arParams["IBLOCK_TYPE"],
            'HAS_REVIEWS' => $bHasRating
        )
    );
}

$tabReviews = ob_get_clean();

$item['TABS']['tab_panels']['REVIEWS'] = $tabReviews;

if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {


    $qeltId = 0;

    $rmqDb = impelCIBlockElement::GetProperty(
        11,
        $item['ID'],
        array(),
        array('CODE' => 'QUESTION_TOPIC_ID')
    );

    if ($rmqDb) {

        while ($aqFields = $rmqDb->GetNext()) {
            $qeltId = trim($aqFields['VALUE']);
        }
    }

    if ($qeltId) {

        ob_start();

        CAPIUncachedArea::includeFile(
            "/include/productquestions.php",
            array(
                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                'CACHE_TIME' => $arParams['CACHE_TIME'],
                'ID' => $qeltId,
                'IBLOCK_ID' => 52,
                'IBLOCK_TYPE' => 'catalog',
                'HAS_REVIEWS' => false,
                'form_index' => 2,
            )
        );

        $tabQuestions = ob_get_clean();

        $item['TABS']['tab_panels']['QUESTIONS'] = $tabQuestions;

    }

}

if(isset($item["PROPERTIES"])
    &&isset($item["PROPERTIES"]["ANALOGUE"])
    &&isset($item["PROPERTIES"]["ANALOGUE"]["VALUE"])
    &&!empty($item["PROPERTIES"]["ANALOGUE"]["VALUE"])
) {

    ob_start();

    $dKey = array_search($item['ID'],$item["PROPERTIES"]["ANALOGUE"]["VALUE"]);

    if($dKey !== false){
        unset($item["PROPERTIES"]["ANALOGUE"]["VALUE"][$dKey]);
    }

    global $arrFilter;

    $arrFilter = array('ID' => $item["PROPERTIES"]["ANALOGUE"]["VALUE"]);

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "analogue",
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
            "CACHE_TIME" => $arParams['CACHE_TIME'],
            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
            "COMPATIBLE_MODE" => "N",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
            "CURRENCY_ID" => $arParams['CURRENCY_ID'],
            "DETAIL_URL" => $arParams["DETAIL_URL"],
            "DISABLE_INIT_JS_IN_COMPONENT" => "Y",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_COMPARE" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_COUNT" => "16",
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_FIELD2" => "id",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "desc",
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
            "PAGE_ELEMENT_COUNT" => "16",
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

    $tabAnalogue = ob_get_clean();

    $item['TABS']['tab_panels']['ANALOGUE'] = $tabAnalogue;
    $item['TABS']['tab_headers']['ANALOGUE'] = GetMessage('CT_BCE_CATALOG_TABS_ANALOGUE');

}

$manufacturer = '';

if(isset($item["DISPLAY_PROPERTIES"])
    &&isset($item["DISPLAY_PROPERTIES"]["MANUFACTURER_DETAIL"])
    &&isset($item["DISPLAY_PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
    &&!empty($item["DISPLAY_PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
) {

    $manufacturer = htmlspecialcharsbx(join(', ', $item["DISPLAY_PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"]));
}

$category = '';

if(isset($item['CATEGORY_PATH'])
    &&!empty($item['CATEGORY_PATH'])
) {

    $category = htmlspecialchars(
        is_array($item['CATEGORY_PATH'])
            ? join(', ', $item['CATEGORY_PATH'])
            : $item['CATEGORY_PATH'],
        ENT_QUOTES,
        LANG_CHARSET);
}

unset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]);
$hide_models = $item["PROPERTIES"]["HIDE_MODELS"]["~VALUE"] == "Скрыть" ? true : false;

if (isset($item['DISPLAY_PROPERTIES'])
    && isset($item['DISPLAY_PROPERTIES']['MANUFACTURER'])
    && !empty($item['DISPLAY_PROPERTIES']['MANUFACTURER'])
    && isset($item['DISPLAY_PROPERTIES']['MANUFACTURER']['DISPLAY_VALUE'])
    && !empty($item['DISPLAY_PROPERTIES']['MANUFACTURER']['DISPLAY_VALUE'])
)
{

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .=
        '<p class="suitable-for-manufacturers">'
        .'<strong>'.GetMessage('TMPL_SUITABLE_FOR_MANUFACTURERS').'</strong>'
        .(is_array($item['DISPLAY_PROPERTIES']['MANUFACTURER']['DISPLAY_VALUE'])
            ? join(', ',$item['DISPLAY_PROPERTIES']['MANUFACTURER']['DISPLAY_VALUE'])
            : $item['DISPLAY_PROPERTIES']['MANUFACTURER']['DISPLAY_VALUE'])
        .'</p>';

}


if(     isset($item["MODEL_HTML"])
    &&  !empty($item["MODEL_HTML"])
    &&  !$hide_models
){

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '
        <div class="suitable-models'.($item['MODEL_HTML_COUNT'] > 12 ? ' models-collapse' : '').'">
            <p class="h3 models-title">'.GetMessage('TMPL_SUITABLE_MODELS').'</p>';

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= $item["MODEL_HTML"];
    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '
            <p>
                <span>'.GetMessage('TMPL_ALL_MODELS_SHOW').'</span>
                <span>'.GetMessage('TMPL_ALL_MODELS_HIDE').'</span>
            </p>
        </div>';

}

unset($item["MODEL_HTML"]);

$videoHtml = '';

if(isset($item['TABS']['tab_panels']['VIDEO'])
    && is_array($item['TABS']['tab_panels']['VIDEO'])
    && !empty($item['TABS']['tab_panels']['VIDEO'])){

    foreach ($item['TABS']['tab_panels']['VIDEO']['VALUE'] as $key => $value){

        if($value != "-"
            && $value != ""){

            $videoHtml .= '
            <div class="video embed-responsive embed-responsive-16by9 row">
                <iframe class="embed-responsive-item" src="'.$value.'" frameborder="0" allowfullscreen="allowfullscreen">
                </iframe>
            </div>';
        };

    };

};

unset($item['TABS']['tab_panels']['VIDEO']);

if(!empty($videoHtml)){
    $item['TABS']['tab_panels']['VIDEO'] = $videoHtml;
}

unset($videoHtml);

$price = $item['ITEM_PRICES'][$item['ITEM_PRICE_SELECTED']];

$hasPrice = !empty($price);

$measureRatio = $item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$cSlidesCount = 4;

?>
    <div itemscope itemtype="http://schema.org/Product">
        <div class="product-item<?php if ($USER && $USER->isAdmin()): ?> new-slider<?php endif; ?> row" id="product-item" data-hasprice="<?=$hasPrice;?>" data-product-id="<?=$item['ID'];?>">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-offset-0 col-md-4 col-lg-4 item-image">
                <?php if(isset($item['OLD_PRICE_PERCENTS'])
                    && !empty($item['OLD_PRICE_PERCENTS'])): ?>
                    <div class="old-bage">
                        -<?=$item['OLD_PRICE_PERCENTS'];?>%
                    </div>
                <?php endif; ?>
                <?php

                if($item['MORE_PHOTO_COUNT'] > 0) {

                    if($item['MORE_PHOTO_COUNT'] > 1) {
                    ?>
                    <div id="slider">
                        <div class="swiper-container-wrapper">
                            <div class="swiper-container gallery-top">
                                <div class="swiper-wrapper">
                    <?php } ?>                
                                    <?php foreach ($item['MORE_PHOTO'] as $key => $photo) { ?>
                                        <div class="swiper-slide<?php if($key == 0): ?> swiper-slide-active<?php endif; ?>">
                                            <div class="slider__image">
                                                <?php if(isset($item['MORE_PHOTO_BIG'])
                                                && isset($item['MORE_PHOTO_BIG'][$key])
                                                && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                                                <a href="<?=$item['MORE_PHOTO_BIG'][$key]['SRC'];?>" class="lightbox" data-gallery="item-gallery" data-type="image" data-title="<?=$alt?>">
                                                    <?php endif; ?>
                                                    <img itemprop="image" src="<?=$photo['SRC']?>" alt="<?=$alt?>" class="img-responsive" />
                                                    <?php if(isset($item['MORE_PHOTO_BIG'])
                                                    && isset($item['MORE_PHOTO_BIG'][$key])
                                                    && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                                                </a>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php }; ?>
                    <?php if($item['MORE_PHOTO_COUNT'] > 1) { ?>
                    </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                            <div class="swiper-container gallery-thumbs">
                                <div class="swiper-wrapper">
                                    <?php foreach ($item['MORE_PHOTO_THUMB'] as $key => $photo) { ?>
                                        <div class="swiper-slide<?php if($key == 0): ?> swiper-slide-active<?php endif; ?>">
                                            <div class="slider__image"><img src="<?=$photo['SRC']?>" alt="<?=$alt?>"  class="img-responsive" /></div>
                                        </div>
                                    <?php }; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php

                }

                ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-5 item-info">
                <?php if(isset($item['ARTNUMBER'])
                    && !empty($item['ARTNUMBER'])): ?>
                    <div class="item-scu">
                        <?=$item['ARTNUMBER']['NAME'];?>
                        <span itemprop="sku">
                        <?=$item['ARTNUMBER']['VALUE'];?>
                        </span>
                    </div>
                    <meta itemprop="mpn" content="<?=$item['ARTNUMBER']['VALUE'];?>" />
                <?php endif; ?>
                <h1 class="item-title" itemprop="name">
                    <?=$item['NAME']?>
                </h1>
                <?php echo $sRating; ?>
                <?php if(!empty($manufacturer)){ ?>
                    <meta itemprop="manufacturer" content="<?=$manufacturer;?>" />
                    <meta itemprop="brand" content="<?=$manufacturer;?>" />
                <?php } ?>
                <meta itemprop="category" content="<?=$category;?>" />
                <?php if(isset($item['~PREVIEW_TEXT'])): ?>
                    <meta itemprop="description" content="<?=htmlspecialcharsbx(trim(strip_tags($item['~PREVIEW_TEXT'])));?>"  />
                <?php endif; ?>
                <?php
                if (!empty($item['DISPLAY_PROPERTIES']))
                {

                    ?>
                    <h2>Характеристики</h2>
                    <dl class="item-props">
                        <?
                        foreach ($item['DISPLAY_PROPERTIES'] as $property)
                        {
                            if(isset($property['CODE'])
                                && in_array($property['CODE'], array('ORIGINALS_CODES','MANUFACTURER','COM_BLACK'))){
                                continue;
                            }

                            ?>
                            <dt>
                                <span>
                                    <?=$property['NAME']?>
                                </span>
                            </dt>
                            <dd><?=(is_array($property['DISPLAY_VALUE'])
                                    ? implode(' / ', $property['DISPLAY_VALUE'])
                                    : $property['DISPLAY_VALUE'])?>
                                <?php if(isset($item['HINTS']) && isset($item['HINTS'][$property['ID']])): ?>
                                    <i class="fa fa-question-circle" data-toggle="modal" data-target="#paramsModal<?=$property['ID'];?>"></i>
                                <?php endif; ?>
                            </dd>
                            <?
                        }

                        unset($property);
                        ?>
                    </dl>
                    <?php
                }
                ?>
                <?php if(isset($item['ANALOGUE'])) { ?>
                    <div class="analogue-area">
                        <!-- <p class="h3 h3-title">/*<?=GetMessage('TMPL_ANALOGUE');?>*/:</p> -->
                        <?php
                        foreach($item['ANALOGUE'] as $analogue_properties) {
                            $first = current($analogue_properties);
                            $bFirst = true;
                            ?>
                            <p class="h3 h3-title"><?=$first['prop'];?>:</p>
                            <div class="btn-toolbar analogue-toolbar" role="toolbar">
                                <div class="btn-group" role="group">
                                    <?php foreach($analogue_properties as $analogue): ?>
                                        <?php $class = $analogue['can_buy'] ? '' : ' disabled'; ?>
                                        <?php $class .= $analogue['id'] == $item['ID'] && $bFirst ? ' active' : ''; ?>
                                        <?php $bFirst = $analogue['id'] == $item['ID'] && $bFirst ? false : $bFirst; ?>
                                        <?php if ($analogue['id'] <> $item['ID'] && (mb_stripos($class,'disabled') === false)): ?>
                                            <a href="<?=$analogue['url'];?>" class="btn <?=$class;?> btn-default">
                                        <?php else: ?>
                                            <span class="btn <?=$class;?> btn-default">
                                        <?php endif; ?>
                                        <?=$analogue['value'];?> (<?=$analogue['price_format'];?>)
                                        <?php if ($analogue['id'] <> $item['ID'] && (mb_stripos($class,'disabled') === false)): ?>
                                            </a>
                                        <?php else: ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php }
                        ?>
                    </div>
                    <?php
                }?>
                <?php



                if (
                    isset($item['DISPLAY_PROPERTIES'])
                    && isset($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES'])
                    && isset($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])
                    && !empty($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])

                )
                {

                    $item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'] =
                        !is_array($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])
                        && !empty($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])
                            ? array($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])
                            : $item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'];

                    ?>
                    <p class="h3 h3-title"><?=GetMessage('TMPL_ORIGINAL_CODES_TITLE');?></p>
                    <div class="original-props-area<?php if(sizeof($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE']) > 2):?> collapsed<?php endif; ?>">
                        <dl class="item-props original-props">
                            <?
                            foreach ($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'] as $property)
                            {

                                if(!empty($property)){

                                    $property = explode(':',$property, 2);

                                    if(empty($property[0]) || empty($property[1]))
                                        continue;

                                    ?>
                                    <dt>
                                        <span>
                                            <?=$property[0]?>
                                        </span>
                                    </dt>
                                    <dd>
                                        <?=$property[1]?>
                                    </dd>
                                    <?

                                }
                            }

                            unset($property);
                            ?>
                        </dl>
                        <?php if(sizeof($item['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE']) > 2):?>
                            <p class="read-more"><?=GetMessage('CT_BCE_CATALOG_MORE_CODES');?></p>
                        <?php endif; ?>
                    </div>
                    <?php

                    unset($item['PROPERTIES']['ORIGINALS_CODES']);
                }
                ?>

                <?php if(!empty($item['ADMIN_MESSAGES'])) {

                    $dataContent = '';

                    foreach ($item['ADMIN_MESSAGES'] as $adminMessage){

                        $dataContent .=
                            '<li class="list-group-item">'
                            . '<strong>'
                            . $adminMessage['NAME']
                            . '</strong>'
                            . ' &ndash; '
                            . $adminMessage['VALUE']
                            .'</li>';

                    }


                    if(!empty($dataContent)){
                        $dataContent =
                            '<ul class="list-group admin-messages">'
                            . $dataContent
                            .'</ul>';


                        echo $dataContent;

                    }

                }; ?>
            </div>
            <meta meta itemprop="productID" content="<?=$item['ID'];?>" />
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 item-cart"<?php if($hasPrice): ?> itemprop="offers" itemscope itemtype="http://schema.org/Offer"<?php endif; ?>>
                <link itemprop="url" href="<?=(IMPEL_PROTOCOL.IMPEL_SERVER_NAME . $item['DETAIL_PAGE_URL']);?>" />
                <? if ($hasPrice || !empty($item['OLD_PRICE'])) { ?>
                    <div class="item-prices item-prices-main">
                        <?
                        if (!empty($item['OLD_PRICE'])) { ?>
                            <span class="item-old">
                    <?= $item['OLD_PRICE']; ?>
                </span>
                            <?
                        }

                        if ($hasPrice) {

                            ?>
                            <span class="item-price">
                <?

                echo $price['PRINT_RATIO_PRICE'];

                ?>
                                <meta itemprop="priceValidUntil" content="<?=date('Y-m-d',(time() + 363 * 86400));?>"/>
                                <meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>"/>
                        <meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>"/>
                    </span>
                            <?

                        }

                        ?>
                    </div>
                    <?

                }

                if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
                    CAPIUncachedArea::includeFile(
                        "/include/prices.php",
                        array(
                            'PRICE' => $price,
                            'NOT_MUCH' => $arParams['NOT_MUCH'],
                            'PRODUCT_ID' => $item['ID'],
                            'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_BUY'],
                            'ONE_CLICK_ORDER' => 'Y',
                            'SCHEMA_AVAIL' => ($hasPrice ? 'Y' : 'N'),
                            'ONE_CLICK_PREORDER' => 'Y',
                            'IN_STOCK_LABEL' => 'Y',
                            'STORES_TOOLTIP' => 'Y',
                            'HAS_PRICE' => $hasPrice
                        )
                    );
                }

                if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
                    CAPIUncachedArea::includeFile(
                        "/include/preorder.php",
                        array(
                            "CONSENT_PROCESSING_TEXT" => $item["CONSENT_PROCESSING_TEXT"]

                        )
                    );
                }

                ?>
                <div class="item-cart-desc">
                    <?

                    $cart_description = '';

                    if(!empty($item['CART_DESCRIPTION'])) {

                        $lines = $item['CART_DESCRIPTION'];

                        foreach($lines["SOCIAL_ICONS_LINKS"] as $number => $cartResAr){

                            $currentLine = "";

                            if(isset($cartResAr)
                                && !empty($cartResAr)){
                                $currentLine .= $cartResAr;
                            };

                            if(isset($lines["SOCIAL_TITLES"][$number])
                                && !empty($lines["SOCIAL_TITLES"][$number])){

                                if(isset($lines["TOOLTIP_TEXT"])
                                    && !empty($lines["TOOLTIP_TEXT"])
                                    && isset($lines["TOOLTIP_TEXT"][$number])
                                    && !empty($lines["TOOLTIP_TEXT"][$number])){
                                    $currentLine .= '<span role="button" data-placement="top" data-toggle="popover" data-trigger="hover" data-html="true" data-content="'.htmlspecialcharsbx($lines["TOOLTIP_TEXT"][$number]).'">';
                                };

                                $currentLine .= $lines["SOCIAL_TITLES"][$number];

                                if(isset($lines["TOOLTIP_TEXT"])
                                    && !empty($lines["TOOLTIP_TEXT"])
                                    && isset($lines["TOOLTIP_TEXT"][$number])
                                    && !empty($lines["TOOLTIP_TEXT"][$number])){
                                    $currentLine .= '</span>';
                                };

                                if(!empty($currentLine)){
                                    $cart_description .= "<p>".$currentLine."</p>";
                                };


                            };

                        };


                    };

                    echo $cart_description;

                    ?>
                </div>
                <?php if($item['HAS_WARRANTY']): ?>
                    <div class="item-warranty">
                        <?=GetMessage('TMPL_HAS_WARRANTY');?>
                    </div>
                <?php endif; ?>
                <?php if(!empty($item['STOCK_PRINT_RATIO_PRICE'])): ?>
                    <div class="item-stock-price">
                        <?=GetMessage('TMPL_STOCK_PRINT_RATIO_PRICE');?>
                        <span>
                        <?=$item['STOCK_PRINT_RATIO_PRICE'];?>
                    </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="product-chained row">
            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                <?php if(!empty($item['TABS'])):
                    $first_key = false;
                    $tabs = $item['TABS'];
                    $tabs1 = $item['TABS'.$item['ID']];
                    ?>
                    <div id="tabs">
                        <ul class="nav nav-tabs">
                            <?php foreach($tabs['tab_headers'] as $tab_key => $tab_name):?>
                                <li role="presentation"<?php if(!$first_key): $first_key = true; ?> class="active"<?php endif; ?>>
                                    <a href="#<?=$tab_key;?>" aria-controls="<?=$tab_key;?>" role="tab" data-toggle="tab">
                                        <?php echo $tab_name; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php foreach($tabs1['tab_headers'] as $tab_key => $tab_name):?>
                                <li role="presentation"<?php if(!$first_key): $first_key = true; ?> class="active"<?php endif; ?>>
                                    <a href="#<?=$tab_key;?>" aria-controls="<?=$tab_key;?>" role="tab" data-toggle="tab">
                                        <?php echo $tab_name; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php
                            $first_key = false;
                            ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach($tabs['tab_panels'] as $tab_key => $tab_content):?>
                                <div role="tabpanel" id="<?=$tab_key;?>" class="tab-pane<?php if(!$first_key): $first_key = true; ?> active<?php endif; ?>">
                                    <?=$tab_content;?>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach($tabs1['tab_panels'] as $tab_key => $tab_content):?>
                                <div role="tabpanel" id="<?=$tab_key;?>" class="tab-pane<?php if(!$first_key): $first_key = true; ?> active<?php endif; ?>">
                                    <?=$tab_content;?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                    unset($tabs,$tabs1);
                endif;
                ?>
                <?php unset($item['TABS'],$item['TABS'.$item['ID']]); ?>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                <label class="h4 phone-title" for="oqQuickPhone">
                    <?=GetMessage('TMPL_CONSULTATION_CALLBACK');?>
                </label>
                <div id="oqPhoneOrder" data-product-id="<?=$item['ID'];?>">
                    <div class="form-group">
                        <input type="text" id="oqQuickPhone" class="form-control" placeholder="<?php echo GetMessage('OC_PAYER_PHONE'); ?>" />
                        <button id="oqCallbackme" class="btn">
                            <span class="fa fa-angle-right" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div id="oqError" class="clearfix hidden">
                        <div class="errors">
                        </div>
                    </div>
                    <div id="oqOrder" class="clearfix hidden alert alert-success" role="alert">
                    </div>
                    <div class="hidden" id="oqResultOk">
                        <?=GetMessage("OC_PREORDER_ADDED");?>
                    </div>
                </div>
				<!--<img src="/upload/detail_discount.svg" alt="" class="img-responsive">-->
                <?php

                if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
                    CAPIUncachedArea::includeFile(
                        "/include/viewed.php",
                        array(
                            "CONSENT_PROCESSING_TEXT" => $item["CONSENT_PROCESSING_TEXT"]

                        )
                    );
                }

                ?>
            </div>
        </div>
    </div>
<?php if ($item['HINTS']): ?>
    <!-- Modal -->
    <?php foreach ($item['HINTS'] as $propId => $hint): ?>
        <div id="paramsModal<?=$propId;?>" class="params-modal modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-title"><?=$hint['NAME'];?></div>
                    </div>
                    <div class="modal-body">
                        <?=$hint['HINT'];?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php

unset($emptyProductProperties, $item, $itemIds, $jsParams);