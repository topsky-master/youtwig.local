<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
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

ob_start();

if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
    CAPIUncachedArea::includeFile(
        "/include/productreview.php",
        array(
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'ID' => $item['ID'],
            'IBLOCK_ID' => $arParams["IBLOCK_ID"],
            'IBLOCK_TYPE' => $arParams["IBLOCK_TYPE"]
        )
    );
}

$tabReviews = ob_get_clean();

$item['TABS']['tab_panels']['REVIEWS'] = $tabReviews;

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
$hide_models = $arResult["PROPERTIES"]["HIDE_MODELS"]["~VALUE"] == "Скрыть" ? true : false;


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
$cSlidesCount = 3;

?>
    <div id="forReviews" itemscope itemtype="http://schema.org/Product">
        <div class="product-item row" id="product-item" data-hasprice="<?=$hasPrice;?>" data-product-id="<?=$item['ID'];?>">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 item-image">
                <?php
                if (!empty($item['MORE_PHOTO']))
                {
                    if($item['MORE_PHOTO_COUNT'] > 1) {
                        ?>
                        <div id="pslider" data-interval="false" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                        <?php
                    }

                    foreach ($item['MORE_PHOTO'] as $key => $photo)
                    {
                        ?>
                        <div data-slide-to="<?=$key;?>" class="piiw item<?=$key!=0?'':' active';?>">

                            <?php if(isset($item['MORE_PHOTO_BIG'])
                            && isset($item['MORE_PHOTO_BIG'][$key])
                            && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                            <a href="<?=$item['MORE_PHOTO_BIG'][$key]['SRC'];?>" class="lightbox" data-gallery="item-gallery" data-type="image" data-title="<?=$alt?>">
                                <?php endif; ?>
                                <img class="img-responsive" src="<?=$photo['SRC']?>" alt="<?=$alt?>"<?=($key == 0 ? ' itemprop="image"' : '')?> />
                                <?php if(isset($item['MORE_PHOTO_BIG'])
                                && isset($item['MORE_PHOTO_BIG'][$key])
                                && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                            </a>
                        <?php endif; ?>
                        </div>
                        <?
                    }

                    if($item['MORE_PHOTO_COUNT'] > 1) {
                        ?>
                        <a class="left carousel-control" rel="nofollow" href="#pslider" data-slide="prev"></a>
                        <a class="right carousel-control" rel="nofollow" href="#pslider" data-slide="next"></a>
                        </div>
                        </div>
                        <?php
                    }

                    if($item['MORE_PHOTO_COUNT'] > 1) {
                        ?>
                        <div class="psthumbs hidden-xs">
                            <?

                            if($item['MORE_PHOTO_COUNT'] > $cSlidesCount) {
                            ?>
                            <div id="psslider" data-interval="false" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <?php
                                    }
                                    foreach ($item['MORE_PHOTO_THUMB'] as $key => $photo)
                                    {
                                        ?>
                                        <div class="item<?php if(!($item['MORE_PHOTO_COUNT'] > $cSlidesCount)): ?> col-sm-3<?php endif; ?><?=$key!=0?'':' active';?>">
                                            <div class="<?php if($item['MORE_PHOTO_COUNT'] > $cSlidesCount): ?>col-sm-3<?php endif; ?>">
                                                <img data-slide-to="<?=$key;?>" class="img-responsive" src="<?=$photo['SRC']?>" alt="<?=$alt?>" />
                                            </div>
                                        </div>
                                        <?
                                    }

                                    if($item['MORE_PHOTO_COUNT'] > $cSlidesCount) {

                                    ?>
                                </div>
                            </div>
                        <?php

                        }

                        ?>
                        </div>
                        <?

                    }

                }
                ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 item-info">
                <h1 class="item-title" itemprop="name">
                    <?=$item['NAME']?>
                </h1>
                <?php
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
                        "DISPLAY_AS_RATING" => (empty($arParams['VOTE_DISPLAY_AS_RATING']) ? "vote_avg" : $arParams['VOTE_DISPLAY_AS_RATING']),
                        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                        "CACHE_TIME" => $arParams['CACHE_TIME']
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );

                ?>
                <?php if(!empty($manufacturer)){ ?>
                    <meta itemprop="manufacturer" content="<?=$manufacturer;?>" />
                <?php } ?>
                <meta itemprop="category" content="<?=$category;?>" />
                <?php if(isset($item['~PREVIEW_TEXT'])): ?>
                    <meta itemprop="description" content="<?=htmlspecialcharsbx(trim(strip_tags($item['~PREVIEW_TEXT'])));?>"  />
                <?php endif; ?>

                <?php if(!empty($item['TABS'])):
                    $first_key = false;
                    $tabs = $item['TABS'];
                    ?>
                    <?php foreach($tabs['tab_panels'] as $tab_key => $tab_content):?>
                    <?php if($tab_key == 'REVIEWS'){  ?>
                    <div id="<?=$tab_key;?>" class="tt">
                        <?=$tab_content;?>
                    </div>
                    <?php
                        break;
                    }; ?>
                <?php endforeach; ?>
                    <?php
                    unset($tabs);
                endif;
                ?>
                <?php unset($item['TABS']); ?>
            </div>
        </div>
    </div>
<?php

unset($emptyProductProperties, $item, $itemIds, $jsParams);
