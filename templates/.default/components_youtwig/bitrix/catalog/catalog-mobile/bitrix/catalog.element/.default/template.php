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

$thumb_width = 74;
$thumb_height = 74;

$product_image_width = 370;
$product_image_height = 370;
$dir = $_SERVER['DOCUMENT_ROOT'].'/';
$gallery = array();
$hasVideo = false;

//$arResult["DETAIL_TEXT"] = function_exists('tidy_repair_string')
    //? tidy_repair_string($arResult["DETAIL_TEXT"], array('show-body-only' => true), "utf8")
    //: $arResult["DETAIL_TEXT"];

if(     isset($arResult["PROPERTIES"]["SEO_TEXT"])
    &&  isset($arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    &&  isset($arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    &&  isset($arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    &&  isset($arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
    &&  !empty($arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
){

    $arResult["DETAIL_TEXT"] .= '<div class="full-detail-text">'.$arResult["PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"].'</div>';

};

unset($arResult["PROPERTIES"]["SEO_TEXT"],$arResult["DISPLAY_PROPERTIES"]["SEO_TEXT"]);


$not_much = isset($arParams['NOT_MUCH']) && !empty($arParams['NOT_MUCH']) ? (int)$arParams['NOT_MUCH'] : 5;
$not_much = empty($not_much) ? 5 : $not_much;


if(     isset($arResult["PROPERTIES"]["MODEL_HTML"])
    &&  isset($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"])
){

    $arResult["DETAIL_TEXT"] .= '
        <p class="h4 h4-models-title">'.GetMessage('TMPL_SUITABLE_MODELS').'</p>
        <div class="suitable-models">';

    $arResult["DETAIL_TEXT"] .= $arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"];

    $arResult["DETAIL_TEXT"] .= '
        </div>';

}

unset($arResult["PROPERTIES"]["MODEL"],$arResult["DISPLAY_PROPERTIES"]["MODEL"],$arResult["PROPERTIES"]["MODEL_HTML"],$arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"]);

if(isset($arResult["PROPERTIES"]["VIDEO"])
    && isset($arResult["PROPERTIES"]["VIDEO"]["VALUE"])
    && isset($arResult["PROPERTIES"]["VIDEO"]["VALUE"])
    && !empty($arResult["PROPERTIES"]["VIDEO"]["VALUE"])
    && is_array($arResult["PROPERTIES"]["VIDEO"]["VALUE"])
){
    foreach ($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $key => $value){
        $value = trim($value);
        if($value != "-"){
            if($value != ""){
                $hasVideo = true;
            };
        };
    };

};

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&is_array($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&sizeof($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
    &&!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
){

    $gallery = $arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"];

};

$galleryShowCaptions = true;
$product_image_big_width = 900;
$product_image_big_height = 900;

$templateLibrary = array('popup');
$currencyList = '';

if (!empty($arResult['CURRENCIES']))
{
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
    'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
    'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
);
unset($currencyList, $templateLibrary);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
    'ID' => $strMainID,
    'PICT' => $strMainID.'_pict',
    'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
    'STICKER_ID' => $strMainID.'_sticker',
    'BIG_SLIDER_ID' => $strMainID.'_big_slider',
    'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
    'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
    'SLIDER_LIST' => $strMainID.'_slider_list',
    'SLIDER_LEFT' => $strMainID.'_slider_left',
    'SLIDER_RIGHT' => $strMainID.'_slider_right',
    'OLD_PRICE' => $strMainID.'_old_price',
    'PRICE' => $strMainID.'_price',
    'DISCOUNT_PRICE' => $strMainID.'_price_discount',
    'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
    'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
    'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
    'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
    'QUANTITY' => $strMainID.'_quantity',
    'QUANTITY_DOWN' => $strMainID.'_quant_down',
    'QUANTITY_UP' => $strMainID.'_quant_up',
    'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
    'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
    'BASIS_PRICE' => $strMainID.'_basis_price',
    'BUY_LINK' => $strMainID.'_buy_link',
    'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
    'BASKET_ACTIONS' => $strMainID.'_basket_actions',
    'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
    'COMPARE_LINK' => $strMainID.'_compare_link',
    'PROP' => $strMainID.'_prop_',
    'PROP_DIV' => $strMainID.'_skudiv',
    'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
    'OFFER_GROUP' => $strMainID.'_set_group_',
    'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
    'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
    : $arResult['NAME']
);
$strAlt = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
    : $arResult['NAME']
);
?>
<div class="bx_item_detail <? echo $templateData['TEMPLATE_CLASS']; ?>" id="<? echo $arItemIDs['ID']; ?>">
    <?
    //reset($arResult['MORE_PHOTO']);
    //$arFirstPhoto = current($arResult['MORE_PHOTO']);

    $noImage = '/images/no_photo.png';
    $noImageURI = $templateFolder.$noImage;
    $noImagePath = __DIR__.$noImage;
    $noImageSizes = getimagesize($noImagePath);

    if(isset($noImageSizes[0]) && !empty($noImageSizes[0])
        && isset($noImageSizes[1]) && !empty($noImageSizes[1])){
        $arFirstPhoto = array(
            'SRC' => $noImageURI,
            'WIDTH' => $noImageSizes[0],
            'HEIGHT' => $noImageSizes[1]
        );
    };

    $arResult['MORE_PHOTO'] = array();
    $arResult['MORE_PHOTO_COUNT'] = 0;


    ?>

    <div class="bx_item_container row" itemscope itemtype="http://schema.org/Product">
        <div class="col-xs-12 col-sm-7 col-md-8 col-lg-9 item-info">
            <div class="row">
                <input type="hidden" id="product_id_detail" name="product_id[]" value="<?=$arResult['ID'];?>" />
                <?php   if($arParams["DISPLAY_PICTURE"]!="N"): ?>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 item-gallery">
                        <?php       if(sizeof($gallery) && is_array($gallery)):?>
                            <div class="advanced-slider<?php if(sizeof($gallery) < 6): ?> without-arrows<?php endif; ?>" id="responsive-slider">
                                <ul class="slides">
                                    <?php
                                    foreach ($gallery as $key=>$image):

                                        if(!empty($image) && function_exists('rectangleImage')):

                                            $ligthbox_image = rectangleImage($dir.$image,$product_image_big_width,$product_image_big_height,$image,false);
                                            $image = rectangleImage($dir.$image,$product_image_width,$product_image_height,$image);
                                            $thumb = rectangleImage($dir.$image,$thumb_width,$thumb_height,$image);

                                            if($key == 0){

                                                $arFirstPhoto = array(
                                                    'SRC' => $image,
                                                    'WIDTH' => $product_image_width,
                                                    'HEIGHT' => $product_image_height
                                                );

                                            }

                                        endif;

                                        if(!empty($image) && !empty($thumb)) :

                                            if(isset($arResult["PROPERTIES"]["GALLERY_VIDEO"])
                                                && isset($arResult["PROPERTIES"]["GALLERY_VIDEO"]["VALUE"])
                                                && isset($arResult["PROPERTIES"]["GALLERY_VIDEO"]["VALUE"][$key])
                                                && $arResult["PROPERTIES"]["GALLERY_VIDEO"]["VALUE"][$key] != "-"):

                                                $ligthbox_image = $arResult["PROPERTIES"]["GALLERY_VIDEO"]["VALUE"][$key];

                                            endif;


                                            ?>

                                            <li class="slide">
                                                <?php   if($galleryShowCaptions && isset($arResult["PROPERTIES"]["GALLERY_TITLES"])
                                                    && isset($arResult["PROPERTIES"]["GALLERY_TITLES"]["VALUE"])
                                                    && isset($arResult["PROPERTIES"]["GALLERY_TITLES"]["VALUE"][$key])
                                                    && $arResult["PROPERTIES"]["GALLERY_TITLES"]["VALUE"][$key] != "-"): ?>
                                                    <div class="caption">
                                                        <?php  echo $arResult["PROPERTIES"]["GALLERY_TITLES"]["VALUE"][$key]; ?>
                                                    </div>
                                                <?php       endif;?>
                                                <img<? if($key == 0): ?> id="<?=$strMainID.'_pict';?>"<? endif; ?> itemprop="image" class="image" src="<?php  echo $image; ?>" alt="<?php echo htmlspecialchars($arResult["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                                                <img class="thumbnail" src="<?php  echo $thumb; ?>" alt="<?php echo htmlspecialchars($arResult["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                                            </li>
                                        <?php endif; ?>
                                    <?php   endforeach; ?>
                                </ul>
                            </div>
                            <script type="text/javascript">
                                //<!--
                                $(function($){
                                    $('#responsive-slider').advancedSlider({width: <?php  echo $product_image_width; ?>,
                                        height: <?php  echo $product_image_height; ?>,
                                        responsive: true,
                                        skin: 'glossy-square-gray',
                                        shadow: false,
                                        slideshow: false,
                                        thumbnailButtons: false,
                                        pauseSlideshowOnHover: true,
                                        thumbnailType: 'scroller',
                                        thumbnailMouseScroll: true,
                                        thumbnailScrollerResponsive: true,
                                        thumbnailScrollbar: false,
                                        keyboardNavigation: true,
                                        scrollbarSkin: 'scrollbar-7-light',
                                        thumbnailArrows: true,
                                        thumbnailTooltip: true,
                                        slideButtons: true,
                                        thumbnailWidth: <?php  echo $thumb_width; ?>,
                                        thumbnailHeight: <?php  echo $thumb_height; ?>,
                                        slideArrows: true,
                                        slideArrowsToggle: false,
                                        effectType: "swipe",
                                        captionSize: 34
                                    });

                                    /* $("a.image-box").prettyPhoto({
                                        openLightbox: function() {
                                            if (slider.getSlideshowState() == 'playing') {
                                                slider.pauseSlideshow();
                                            }
                                        },
                                        callback: function() {
                                            if (slider.getSlideshowState() == 'paused') {
                                                slider.resumeSlideshow();
                                            }
                                        }
                                    }); */

                                });
                                //-->
                            </script>
                        <?php       elseif($arParams["DISPLAY_PICTURE"]!="N"):
                        if(isset($arResult["PREVIEW_PICTURE"])
                            && isset($arResult["PREVIEW_PICTURE"]["SRC"])
                            && !empty($arResult["PREVIEW_PICTURE"]["SRC"])):

                            if(function_exists("rectangleImage")):
                                $arResult["PREVIEW_PICTURE"]["SRC"] = rectangleImage($dir.$arResult["PREVIEW_PICTURE"]["SRC"],$product_image_width,$product_image_height,$arResult["PREVIEW_PICTURE"]["SRC"]);
                            endif;

                        elseif(isset($arResult["DETAIL_PICTURE"])
                            && isset($arResult["DETAIL_PICTURE"]["SRC"])
                            && !empty($arResult["DETAIL_PICTURE"]["SRC"])):

                            $arResult["PREVIEW_PICTURE"]		= array("SRC"=>"","WIDTH"=>$arResult["DETAIL_PICTURE"]["WIDTH"],"HEIGHT"=>$arResult["DETAIL_PICTURE"]["HEIGHT"]);
                            if(function_exists("rectangleImage")):
                                $arResult["PREVIEW_PICTURE"]["SRC"] = rectangleImage($dir.$arResult["DETAIL_PICTURE"]["SRC"],$product_image_width,$product_image_height,$arResult["DETAIL_PICTURE"]["SRC"]);
                            endif;

                        endif;

                        if(isset($arResult["PREVIEW_PICTURE"]["SRC"])
                        && isset($arResult["PREVIEW_PICTURE"]["WIDTH"])
                        && isset($arResult["PREVIEW_PICTURE"]["HEIGHT"])):

                        $arFirstPhoto = array(
                            'SRC' => $arResult["PREVIEW_PICTURE"]["SRC"],
                            'WIDTH' => $arResult["PREVIEW_PICTURE"]["WIDTH"],
                            'HEIGHT' => $arResult["PREVIEW_PICTURE"]["HEIGHT"]
                        );

                        ?>
                            <div id="detail_picture_wrapper" class="detail_picture_wrapper">
                                <img id="<?=$strMainID.'_pict';?>" itemprop="image" class="detail_picture img-responsive" src="<?php echo $arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?php echo htmlspecialchars($arResult["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                            </div>
                        <?php  else: ?>
                            <div id="detail_picture_wrapper" class="detail_picture_wrapper">
                                <img id="<?=$strMainID.'_pict';?>" itemprop="image" class="detail_picture img-responsive" src="<?=$noImageURI;?>" alt="<?php echo htmlspecialchars($arResult["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                                <?

                                ?>
                            </div>
                        <?php  endif;?>
                        <?php
                        endif;?>
                    </div>
                <?              endif;?>
                <div class="item-description col-xs-12<?php  if($arParams["DISPLAY_PICTURE"]!="N"): ?> col-sm-12 col-md-6 col-lg-6<?php else: ?> col-sm-12 col-md-12 col-lg-12<?php  endif; ?>">
                    <? if ('Y' == $arParams['DISPLAY_NAME']){ ?>
                        <?

                        if(isset($arResult["PROPERTIES"])
                            && isset($arResult["PROPERTIES"]["ARTNUMBER"])
                            && isset($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])
                            && !empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])){
                            ?>
                            <div class="bx_catalog_item_articul">
                                <p>
                                    <strong>
                                        <?php  echo $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"]; ?>
                                    </strong>
                                    <span>
                                                    <?php  echo $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]; ?>
                                                </span>
                                </p>
                                <meta itemprop="sku" content="<?=htmlspecialcharsbx($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]);?>" >
                                <a class="hidden-lg hidden-md hidden-sm section-link" href="<?=$arResult['SECTION_PAGE_URL'];?>">
                                    <?=GetMessage('TMPL_SECTION_LIST_BACK');?>
                                </a>
                            </div>
                            <?
                            unset($arResult["PROPERTIES"]["ARTNUMBER"],$arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]);

                        };
                        ?>
                        <div class="bx_item_title">
                            <h1 itemprop="name">
                                <?
                                echo (
                                isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"])
                                && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
                                    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                                    : $arResult["NAME"]
                                );
                                ?>
                            </h1>
                            <?php
                            $minPrice = (isset($arResult['RATIO_PRICE']) ? $arResult['RATIO_PRICE'] : $arResult['MIN_PRICE']);
                            $notavailable = !empty($minPrice['VALUE']) ? false : true;
                            $inStock = $notavailable || !$arResult['CAN_BUY'] ? false : true;
                            ?>
                            <div class="in-stock-label text-center hidden-lg hidden-md hidden-sm" id="in-stock-<?=$arResult['ID'];?>">
                                <?=GetMessage('CT_BCS_CATALOG_IN_STOCK');?>
                                <span id="in-stock-label" class="<?=$inStock?'in-stock':'out-of-stock';?><?=($inStock && $arResult['PRINT_QUANTITY'] < $not_much)?(' not-much'):'';?>">
                                    <?=$inStock?(($arResult['PRINT_QUANTITY'] < $not_much) ? GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH') : GetMessage('CT_BCS_CATALOG_IN_STOCK_YES')):GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');?>
                                </span>
                            </div>
                            <?php

                            $manufacturer = '';

                            if(isset($arResult["PROPERTIES"])
                                &&isset($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"])
                                &&isset($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
                                &&!empty($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
                            ){

                                $manufacturer = htmlspecialcharsbx(join(', ',$arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"]));

                                ?>
                                <meta itemprop="manufacturer" content="<?=htmlspecialcharsbx(join(', ',$arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"]));?>" >
                            <?php } ?>
                        </div>
                        <?
                    }

                    $in_stock_label = '';

                    if(     isset($arResult['MANAGER_STORES'])
                        && !empty($arResult['MANAGER_STORES'])
                    ){
                        foreach($arResult['MANAGER_STORES'] as $store){

                            $in_stock_label .= '<h5 class="on_stock">' . ((GetMessage('CT_BCE_CATALOG_STORE_'.$store['STORE_ID']) != "" ? GetMessage('CT_BCE_CATALOG_STORE_'.$store['STORE_ID']) : $store['STORE_NAME']).' &ndash; '.$store['AMOUNT']. GetMessage('CT_BCE_CATALOG_STORE_AMOUNT') . '</h5>');

                        }

                        if(!empty($in_stock_label))
                            echo $in_stock_label;

                    }

                    $quantity = $arResult['PRINT_QUANTITY'];
                    if($quantity != ""){ ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("CRL_QUANTITY"); ?><?php  echo $quantity; ?>
                        </h5>
                    <?			    };

                    if($arResult["BONDS_NAME"]){ ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("CRL_BONDS_NAME"); ?><?php  echo $arResult["BONDS_NAME"]; ?>
                        </h5>
                        <?
                    };

                    if($arResult["SHELF"]){ ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("CRL_PROPERTY_RACK"); ?><?php  echo $arResult["SHELF"]; ?>
                        </h5>
                        <?
                    };

                    if($arResult["RACK"]){ ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("CRL_PROPERTY_SHELF"); ?><?php  echo $arResult["RACK"]; ?>
                        </h5>
                        <?
                    };

                    if(isset($arResult["PROPERTIES"])
                        && isset($arResult["PROPERTIES"]["COM_BLACK"])
                        && isset($arResult["PROPERTIES"]["COM_BLACK"]["VALUE"])
                        && !empty($arResult["PROPERTIES"]["COM_BLACK"]["VALUE"])){
                        ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("COMMENT_TO_PRODUCT"); ?><?php  echo $arResult["PROPERTIES"]["COM_BLACK"]["VALUE"]; ?>
                        </h5>
                        <?
                        unset($arResult["PROPERTIES"]["COM_BLACK"],$arResult["DISPLAY_PROPERTIES"]["COM_BLACK"]);

                    };

                    if(isset($arResult["PROPERTIES"])
                        && isset($arResult["PROPERTIES"]["QUALITY"])
                        && isset($arResult["PROPERTIES"]["QUALITY"]["VALUE"])
                        && !empty($arResult["PROPERTIES"]["QUALITY"]["VALUE"])){
                        ?>
                        <h5 class="on_stock">
                            <?php  echo GetMessage("CRL_QUALITY"); ?><?php  echo $arResult["PROPERTIES"]["QUALITY"]["VALUE"]; ?>
                        </h5>
                        <?
                        unset($arResult["PROPERTIES"]["QUALITY"],$arResult["DISPLAY_PROPERTIES"]["QUALITY"]);

                    };



                    ?>
                    <?              if($arResult["PREVIEW_TEXT"]):?>
                        <meta itemprop="description" content="<?=htmlspecialcharsbx(trim(strip_tags($arResult["PREVIEW_TEXT"])));?>"  />
                    <?              endif;?>
                    <?
                    if(!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']){
                        ?>
                        <div class="item_info_section">
                            <?
                            if (!empty($arResult['DISPLAY_PROPERTIES'])){
                                ?>
                                <dl class="properties">
                                    <?
                                    foreach ($arResult['DISPLAY_PROPERTIES'] as $pid => $arOneProp){

                                        if($pid == 'COM_BLACK' || $pid == 'INSTRUCTION' || $pid == 'ARTNUMBER' || $pid == 'LINKED_ELEMETS') continue;

                                        ?>
                                        <dt>
                                            <span><?                                      echo $arOneProp['NAME'];
                                                ?>
                                            </span>
                                        </dt>
                                        <dd>
                                            <?
                                            echo (
                                            is_array($arOneProp['DISPLAY_VALUE'])
                                                ? implode(' / ', $arOneProp['DISPLAY_VALUE'])
                                                : $arOneProp['DISPLAY_VALUE']
                                            );
                                            ?>
                                        </dd>
                                        <?
                                    }
                                    unset($arOneProp);
                                    ?>
                                </dl>
                                <?
                            }

                            if ($arResult['SHOW_OFFERS_PROPS']){
                                ?>
                                <dl id="<? echo $arItemIDs['DISPLAY_PROP_DIV'] ?>" class="display-none">
                                </dl>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }

                    if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])){
                        $canBuy = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'];
                    } else {
                        $canBuy = $arResult['CAN_BUY'];
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
            <div class="propose prices buy<? if(!$canBuy){?> not-buy<?}?> clearfix" id="propose_prices">
                <?

                $minPrice = (isset($arResult['RATIO_PRICE']) ? $arResult['RATIO_PRICE'] : $arResult['MIN_PRICE']);

                $useBrands = ('Y' == $arParams['BRAND_USE']);
                if ($useBrands){
                    ?>
                    <div class="bx_optionblock">
                        <?

                        if ($useBrands){

                            $APPLICATION->IncludeComponent("bitrix:catalog.brandblock", ".default", array(
                                "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                                "ELEMENT_ID" => $arResult['ID'],
                                "ELEMENT_CODE" => "",
                                "PROP_CODE" => $arParams['BRAND_PROP_CODE'],
                                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                "CACHE_TIME" => $arParams['CACHE_TIME'],
                                "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                                "WIDTH" => "",
                                "HEIGHT" => ""
                            ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            );
                        }
                        ?>
                    </div>
                    <?
                }
                unset($useBrands);
                ?>
                <div class="item_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <?

                    $boolDiscountShow = (0 < $minPrice['DISCOUNT_DIFF']);

                    if ($arParams['SHOW_OLD_PRICE'] == 'Y'){
                        ?>
                        <div class="item_old_price<? echo($boolDiscountShow ? '' : ' display-none'); ?>" id="<? echo $arItemIDs['OLD_PRICE']; ?>">
                            <? echo($boolDiscountShow ? $minPrice['PRINT_VALUE'] : ''); ?>
                        </div>
                        <?
                    }
                    ?>
                    <div class="item_current_price" id="<? echo $arItemIDs['PRICE']; ?>">
                        <? echo $minPrice['PRINT_DISCOUNT_VALUE']; ?>
                    </div>
                    <meta itemprop="price" content="<? echo str_ireplace(',','.',trim($minPrice['DISCOUNT_VALUE'])); ?>" />

                    <?              if(isset($minPrice['CURRENCY']) && !empty($minPrice['CURRENCY'])){?>
                        <meta itemprop="priceCurrency" content="<?=$minPrice['CURRENCY'];?>" />
                    <?              }?>
                    <?              if($canBuy){?>
                        <link itemprop="availability" href="http://schema.org/InStock" />
                    <?              }?>

                    <?
                    if ($arParams['SHOW_OLD_PRICE'] == 'Y'){
                        ?>
                        <div class="item_economy_price<? echo($boolDiscountShow ? '' : ' display-none'); ?>" id="<? echo $arItemIDs['DISCOUNT_PRICE']; ?>">
                            <? echo($boolDiscountShow ? GetMessage('CT_BCE_CATALOG_ECONOMY_INFO', array('#ECONOMY#' => $minPrice['PRINT_DISCOUNT_DIFF'])) : ''); ?>
                        </div>
                        <?
                    }



                    ?>
                </div>
                <div class="item_info_section">
                    <?


                    $buyBtnMessage = ($arParams['MESS_BTN_BUY'] != '' ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));
                    $addToBasketBtnMessage = ($arParams['MESS_BTN_BUY'] != '' ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));
                    $notAvailableMessage = ($arParams['MESS_NOT_AVAILABLE'] != '' && false ? $arParams['MESS_NOT_AVAILABLE'] : GetMessageJS('CT_BCE_CATALOG_NOT_AVAILABLE'));
                    $showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
                    $showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);

                    if($arResult['CATALOG_SUBSCRIBE'] == 'Y')
                        $showSubscribeBtn = true;
                    else
                        $showSubscribeBtn = false;

                    $showSubscribeBtn = false;

                    $compareBtnMessage = ($arParams['MESS_BTN_COMPARE'] != '' ? $arParams['MESS_BTN_COMPARE'] : GetMessage('CT_BCE_CATALOG_COMPARE'));

                    if ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'){

                    if ($arParams['SHOW_BASIS_PRICE'] == 'Y'){

                        $basisPriceInfo = array(
                            '#PRICE#' => $arResult['MIN_BASIS_PRICE']['PRINT_DISCOUNT_VALUE'],
                            '#MEASURE#' => (isset($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : '')
                        );
                        ?>
                        <p id="<? echo $arItemIDs['BASIS_PRICE']; ?>" class="item_section_name_gray display-none">
                                                <span>
                                                        <? echo GetMessage('CT_BCE_CATALOG_MESS_BASIS_PRICE', $basisPriceInfo); ?>
                                                </span>
                        </p>
                    <?
                    }

                    if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])){
                        $canBuy = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CAN_BUY'];
                    } else {
                        $canBuy = $arResult['CAN_BUY'];
                    }

                    ?>
                        <span class="item_section_name_gray display-none">
                                                <? echo GetMessage('CATALOG_QUANTITY'); ?>
                                        </span>
                        <div class="item_buttons vam">
                        <span class="item_buttons_counter_block<? echo ($canBuy && false ? '' : ' display-none'); ?> hidden">
                            <a href="javascript:void(0)" class="bx_bt_button_type_2 bx_small bx_fwb" id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>">
                                                                -
                                                        </a>
                            <input id="<? echo $arItemIDs['QUANTITY']; ?>" type="text" class="tac transparent_input" value="<? echo (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']) ? 1 : $arResult['CATALOG_MEASURE_RATIO']); ?>">
                            <a href="javascript:void(0)" class="bx_bt_button_type_2 bx_small bx_fwb" id="<? echo $arItemIDs['QUANTITY_UP']; ?>">
                                                                +
                                                        </a>
                            <span class="bx_cnt_desc" id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>">
                                                                <? echo (isset($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : ''); ?>
                                                        </span>
                        </span>
                            <span class="item_buttons_counter_block<? echo ($canBuy ? '' : ' display-none'); ?>" id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>">
<?
if ($showBuyBtn){
    ?>
    <a href="javascript:void(0);"<? if(isset($arResult['BUY_ID']) && !empty($arResult['BUY_ID']) && $arResult['BUY_ID'] != $arResult['ID']): ?> data-buy-id="<?=$arResult['BUY_ID'];?>"<? endif; ?> class="bx_big bx_bt_button bx_cart" id="<? echo $arItemIDs['BUY_LINK']; ?>">
    </a>
    <? echo $buyBtnMessage; ?>
    <?
}

if ($showAddBtn){
    ?>
    <a href="javascript:void(0);" class="bx_big bx_bt_button bx_cart"<? if(isset($arResult['BUY_ID']) && !empty($arResult['BUY_ID']) && $arResult['BUY_ID'] != $arResult['ID']): ?> data-buy-id="<?=$arResult['BUY_ID'];?>"<? endif; ?> id="<? echo $arItemIDs['ADD_BASKET_LINK']; ?>">
    </a>
    <? echo $addToBasketBtnMessage; ?>
    <?
}
?>
                                                </span>
                            <?
                            if(false){
                                ?>
                                <span id="<? echo $arItemIDs['NOT_AVAILABLE_MESS']; ?>" class="bx_notavailable<?=($showSubscribeBtn ? ' bx_notavailable_subscribe' : ''); ?><? echo ($notavailable ? '' : ' display-none'); ?>">
                                                        <? echo $notAvailableMessage; ?>
                                                </span>
                                <?
                            }

                            if($showSubscribeBtn && !$canBuy){

                                $APPLICATION->includeComponent('bitrix:catalog.product.subscribe','subscribe',
                                    array(
                                        'PRODUCT_ID' => $arResult['ID'],
                                        'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                        'BUTTON_CLASS' => 'bx_big bx_bt_button',
                                        'DEFAULT_DISPLAY' => !$canBuy,
                                    ),
                                    $component, array('HIDE_ICONS' => 'Y')
                                );
                            }
                            ?>
                            <?
                            if ($arParams['DISPLAY_COMPARE']){
                                ?>
                                <span class="item_buttons_counter_block">
                            <a href="javascript:void(0);" class="bx_big bx_bt_button_type_2 bx_cart" id="<? echo $arItemIDs['COMPARE_LINK']; ?>">
                                                                <? echo $compareBtnMessage; ?>
                                                        </a>
                        </span>
                                <?
                            }
                            ?>
                        </div>
                    <?
                    $notavailable = !empty($minPrice['VALUE']) ? false : true;
                    $inStock = $notavailable || !$arResult['CAN_BUY'] ? false : true;

                    $in_stock_label = '';

                    if($inStock
                        && isset($arResult['STORES'])
                        && !empty($arResult['STORES'])
                    ){
                        foreach($arResult['STORES'] as $store){

                            $amount = (float)$store['AMOUNT'];

                            $in_stock_label .= '<p>' . (GetMessage('CT_BCE_CATALOG_STORE_'.$store['STORE_ID']) != "" ? GetMessage('CT_BCE_CATALOG_STORE_'.$store['STORE_ID']) : $store['STORE_NAME']). ' ';

                            if($amount <= 0){
                                $in_stock_label .= GetMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
                            } elseif($amount <= 10){
                                $in_stock_label .= GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH_LABEL');
                            } elseif($amount > 10){
                                $in_stock_label .= GetMessage('CT_BCS_CATALOG_IN_STOCK_MUCH_LABEL');
                            }

                            $in_stock_label .= '</p>';
                        }
                    }

                    ?>
                        <div class="in-stock-label text-center hidden-xs" id="in-stock-<?=$arResult['ID'];?>">
                            <?=GetMessage('CT_BCS_CATALOG_IN_STOCK');?>
                            <span id="in-stock-label"<?php if(!empty($in_stock_label)){ ?> data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="<?php echo htmlspecialcharsbx($in_stock_label);?>" <?php }; ?> class="<?=$inStock?'in-stock':'out-of-stock';?><?=($inStock && $arResult['PRINT_QUANTITY'] < $not_much)?(' not-much'):'';?>">
                                                <?=$inStock?(($arResult['PRINT_QUANTITY'] < $not_much) ? GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH') : GetMessage('CT_BCS_CATALOG_IN_STOCK_YES')):GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');?>
                                            </span>
                        </div>
                    <?

                    if(!empty($arResult['CART_DESCRIPTION'])){?>
                        <div class="cart_description<? if(!$canBuy): ?> hidden<? endif; ?> clearfix">
                            <?php echo $arResult['CART_DESCRIPTION']; ?>
                        </div>
                    <?                  }


                    $notavailable = !empty($minPrice['VALUE']) ? false : true;

                    if(!$notavailable){
                    ?>
                        <noindex>
                            <button class="btn<? if($canBuy){ ?> btn-default<? } else { ?> btn-danger<? }?>"  data-toggle="modal" data-target="#modalOCBuy" id="order-one-click">
                                <span class="glyphicon glyphicon-hand-up" aria-hidden="true"></span>
                                <span class="catalog_one_click_order<? if(!$canBuy): ?> hidden<? endif; ?>">
                                                <?php echo GetMessage('CATALOG_ONE_CLICK_ORDER'); ?>
                                        </span>
                                <span class="catalog_one_click_pre_order<? if($canBuy): ?> hidden<? endif; ?>">
                                                <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                                        </span>
                            </button>
                            <div class="modal<? if(!$canBuy): ?> preorder-section-modal<? endif; ?> bs-example-modal-sx" id="modalOCBuy" tabindex="-1" role="dialog" aria-labelledby="modalOCBuyLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">
                                                                                ×
                                                                        </span>
                                            </button>
                                            <h4 class="modal-title">
                                                                        <span class="catalog_one_click_order<? if(!$canBuy): ?> hidden<? endif; ?>">
                                                                                <?php echo GetMessage('CATALOG_ONE_CLICK_ORDER'); ?>
                                                                        </span>
                                                <span class="catalog_one_click_pre_order<? if($canBuy): ?> hidden<? endif; ?>">
                                                                                <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                                                                        </span>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="order_one_click_form clearfix" id="order_one_click_form">
                                                <div id="oc_error" class="clearfix hidden">
                                                    <div class="alert alert-danger text-left" role="alert">
        									    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">
        									    </span>
                                                        <span class="sr-only">
        									    </span>
                                                        <?php echo GetMessage("OC_ERROR"); ?>
                                                    </div>
                                                    <div class="errors">
                                                    </div>
                                                </div>
                                                <div id="oc_order" class="clearfix hidden alert alert-success" role="alert">
                                                </div>
                                                <div class="form-group input-group name-group clearfix">
        								    <span class="input-group-addon">
        									    <span class="glyphicon glyphicon-user" aria-hidden="true">
                                                                                        </span>
        									</span>
                                                    <input type="text" id="PAYER_NAME" name="PAYER_NAME" value="" placeholder="<?php echo GetMessage('OC_PAYER_NAME'); ?>" class="form-control" />
                                                    <span class="input-group-addon">
        									    *
        									</span>
                                                </div>
                                                <div class="form-group input-group phone-group clearfix">
        								    <span class="input-group-addon">
        									    <span class="glyphicon glyphicon-phone" aria-hidden="true">
                                                                                        </span>
        									</span>
                                                    <input type="text" id="PAYER_PHONE" name="PAYER_PHONE" value="" placeholder="<?php echo GetMessage('OC_PAYER_PHONE'); ?>" class="form-control" />
                                                    <span class="input-group-addon">
        									    *
        									</span>
                                                </div>
                                                <div class="form-group input-group email-group clearfix">
        								    <span class="input-group-addon">
                                                                                        @
                                                                                </span>
                                                    <input type="text" id="PAYER_EMAIL" name="PAYER_EMAIL" value="" placeholder="<?php echo GetMessage('OC_PAYER_EMAIL'); ?>" class="form-control" />
                                                </div>
                                                <div class="form-group clearfix text-center">
                                                    <button id="PAY_ONE_CLICK" class="btn<? if($canBuy): ?> btn-success<?php else: ?> btn-info<?php endif; ?>">
                                                        <? if($canBuy): ?>
                                                        <i class="fa fa-shopping-cart">
                                                            <? endif; ?>
                                                        </i>
                                                        <span class="catalog_one_click_order<? if(!$canBuy): ?> hidden<? endif; ?>">
                                                                                                <?php echo GetMessage("CATALOG_BUY")?>
                                                                                        </span>
                                                        <span class="catalog_one_click_pre_order<? if($canBuy): ?> hidden<? endif; ?>">
                                                                                                <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                                                                                        </span>

                                                    </button>
                                                    <?

                                                    $consent_processing_link = COption::GetOptionString("my.stat", "consent_processing_link", "");
                                                    $consent_processing_text = GetMessage('SOA_CONSENT_PROCESSING_LINK');

                                                    if(!empty($consent_processing_link)){
                                                        $consent_processing_text = str_ireplace('href="#"','href="'.$consent_processing_link.'"',$consent_processing_text);
                                                    } else {
                                                        $consent_processing_text = strip_tags($consent_processing_text);
                                                    }


                                                    ?>
                                                    <p class="consent-processing"><?=$consent_processing_text;?></p>
                                                </div>
                                                <input type="hidden" id="PRODUCT_ID" name="PRODUCT_ID" value="<?php echo $arResult['ID']; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </noindex>
                        <script type="text/javascript">
                            //<!--
                            $(function(){

                                phonePlaceholder = '+79_________';
                                phoneMask = '+79000000000';

                                $('#PAYER_PHONE').attr('autocomplete',false);
                                $('#PAYER_PHONE').attr('placeholder', phonePlaceholder);

                                var maskOptions =  {

                                    onKeyPress: function(cep, event, currentField, options){
                                        if($(currentField).get(0)
                                            && $(currentField).val().indexOf('+78') === 0){
                                            $(currentField).val($(currentField).val().replace('+78','+79'));
                                        };
                                    }

                                };

                                $('#PAYER_PHONE').mask(phoneMask,maskOptions);

                                if($.cookie("preorder<?=$arResult['ID'];?>")){
                                    $("#PAY_ONE_CLICK").attr('disabled',true);
                                };

                                $("#PAY_ONE_CLICK").bind("click",function(event){

                                    if($('.catalog_one_click_order').hasClass('hidden')){
                                        var ajax_cart_action = '/ajax_cart/preorder.php';
                                    } else {
                                        var ajax_cart_action = '/ajax_cart/fastoder.php';
                                    };

                                    var ocProductId = $("#PRODUCT_ID")[0].value;

                                    if(!$.cookie("preorder" + ocProductId)){

                                        $(this).attr("disabled",false);

                                        $('#order_one_click_form .form-group').removeClass("has-error");
                                        $('#order_one_click_form .form-group').removeClass("has-success");

                                        var ocPayerName = $("#PAYER_NAME")[0].value;
                                        ocPayerName	= $.trim(ocPayerName);

                                        var hasErrors = false;

                                        if(ocPayerName.length > 3){
                                            $('.name-group').addClass('has-success');
                                        } else {
                                            $('.name-group').addClass('has-error');
                                            hasErrors = true;
                                        };

                                        var ocPayerPhone = $("#PAYER_PHONE")[0].value;
                                        ocPayerPhone = $.trim(ocPayerPhone);


                                        if(ocPayerPhone.length  > 3){
                                            $('.phone-group').addClass('has-success');
                                        } else {
                                            $('.phone-group').addClass('has-error');
                                            hasErrors = true;
                                        };

                                        var ocPayerEmail = $("#PAYER_EMAIL")[0].value;
                                        ocPayerEmail = $.trim(ocPayerEmail);

                                        if((ocPayerEmail.length > 5
                                                && ocPayerEmail.indexOf('@') !== -1
                                                && ocPayerEmail.indexOf('.') !== -1)
                                            || !ocPayerEmail.length
                                        ){
                                            $('.email-group').addClass('has-success');
                                        } else {
                                            $('.email-group').addClass('has-error');
                                            hasErrors = true;
                                        };

                                        $('#oc_error .errors')[0].innerHTML = "";
                                        $('#oc_error,#oc_order').addClass("hidden");

                                        BX.closeWait();

                                        if(!hasErrors){

                                            $("#PAY_ONE_CLICK").attr('disabled',true);
                                            BX.showWait();

                                            BX.ajax({
                                                url: ajax_cart_action,
                                                method: 'post',
                                                dataType: 'json',
                                                async: true,
                                                processData: true,
                                                emulateOnload: true,
                                                start: true,
                                                data: {
                                                    'PRODUCT_ID': ocProductId,
                                                    'PAYER_NAME': ocPayerName,
                                                    'PAYER_PHONE': ocPayerPhone,
                                                    'PAYER_EMAIL': ocPayerEmail
                                                },
                                                onsuccess: function(result){
                                                    BX.closeWait();

                                                    if(result.ERROR){
                                                        $('#oc_error').removeClass("hidden");
                                                        $("#PAY_ONE_CLICK").attr('disabled',false);

                                                        if(result.ERROR && result.ERROR.length){
                                                            for(var i = 0; i < result.ERROR.length; i ++){
                                                                $('#oc_error .errors')[0].innerHTML += ''
                                                                    +'<div class="alert alert-danger text-left" role="alert">'
                                                                    +'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">'
                                                                    +'</span>'
                                                                    +'<span class="sr-only">'
                                                                    +'Error:'
                                                                    +'</span>'
                                                                    +' '
                                                                    +result.ERROR[i]
                                                                    +'</div>';
                                                            };

                                                        };

                                                    } else if(result.ORDER_ID){

                                                        if($('.catalog_one_click_order').hasClass('hidden')){
                                                            var resultAnswer = '<?php echo GetMessage("OC_PREORDER_ADDED"); ?>';
                                                        } else {
                                                            var resultAnswer = '<?php echo GetMessage("OC_ORDER_ADDED"); ?>';
                                                        };

                                                        $('#modalOCBuy .form-group').css('display','none');

                                                        $('#oc_order')[0].innerHTML = resultAnswer.replace('#',result.ORDER_ID);
                                                        $('#oc_order').removeClass("hidden");
                                                        $.cookie("preorder" + ocProductId, "1",{expires: 1/24, path: '/'});

                                                    };
                                                },
                                                onfailure: function(type, e){
                                                    BX.closeWait();
                                                }
                                            });
                                        };

                                    };

                                    event.preventDefault();
                                    return false;

                                });

                            });
                            //-->
                        </script>
                    <?
                    };

                    //unset($minPrice);

                    if (isset($arResult['OFFERS'])
                    && !empty($arResult['OFFERS'])
                    && !empty($arResult['OFFERS_PROP'])){

                    $arSkuProps = array();
                    ?>
                        <div class="item_info_section" id="<? echo $arItemIDs['PROP_DIV']; ?>">
                            <?
                            foreach ($arResult['SKU_PROPS'] as &$arProp){

                                if (!isset($arResult['OFFERS_PROP'][$arProp['CODE']]))
                                    continue;

                                $arSkuProps[] = array(
                                    'ID' => $arProp['ID'],
                                    'SHOW_MODE' => $arProp['SHOW_MODE'],
                                    'VALUES_COUNT' => $arProp['VALUES_COUNT']
                                );

                                if ('TEXT' == $arProp['SHOW_MODE']){

                                    if (5 < $arProp['VALUES_COUNT'])
                                    {
                                        $strClass = 'bx_item_detail_size full';
                                        $strOneWidth = 'width'.(100/$arProp['VALUES_COUNT']).'';
                                        $strWidth = 'width'.(20*$arProp['VALUES_COUNT']).'';
                                        $strSlideStyle = '';
                                    } else {
                                        $strClass = 'bx_item_detail_size';
                                        $strOneWidth = 'width20';
                                        $strWidth = 'width100';
                                        $strSlideStyle = ' display-none';
                                    }
                                    ?>
                                    <div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
                        <span class="bx_item_section_name_gray">
                                                        <? echo htmlspecialcharsEx($arProp['NAME']); ?>
                                                </span>
                                        <div class="bx_size_scroller_container">
                                            <div class="bx_size">
                                                <ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" class="<? echo $strWidth; ?>">
                                                    <?
                                                    foreach ($arProp['VALUES'] as $arOneValue){

                                                        $arOneValue['NAME'] = htmlspecialcharsbx($arOneValue['NAME']);
                                                        ?>
                                                        <li data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>" data-onevalue="<? echo $arOneValue['ID']; ?>" class="<? echo $strOneWidth; ?> display-none">
                                                            <i title="<? echo $arOneValue['NAME']; ?>">
                                                            </i>
                                                            <span class="cnt" title="<? echo $arOneValue['NAME']; ?>">
                                                                                        <? echo $arOneValue['NAME']; ?>
                                                                                </span>
                                                        </li>
                                                        <?
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="bx_slide_left<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>">
                                            </div>
                                            <div class="bx_slide_right<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                } elseif ('PICT' == $arProp['SHOW_MODE']){

                                    if (5 < $arProp['VALUES_COUNT']){
                                        $strClass = 'bx_item_detail_scu full';
                                        $strOneWidth = 'width'.(100/$arProp['VALUES_COUNT']).'';
                                        $strWidth = 'width'.(20*$arProp['VALUES_COUNT']).'';
                                        $strSlideStyle = '';
                                    } else {
                                        $strClass = 'bx_item_detail_scu';
                                        $strOneWidth = 'width20';
                                        $strWidth = 'width100';
                                        $strSlideStyle = ' display-none;';
                                    }
                                    ?>
                                    <div class="<? echo $strClass; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
                        <span class="bx_item_section_name_gray">
                                                        <? echo htmlspecialcharsEx($arProp['NAME']); ?>
                                                </span>
                                        <div class="bx_scu_scroller_container">
                                            <div class="bx_scu">
                                                <ul id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list" class="<? echo $strWidth; ?>">
                                                    <?
                                                    foreach ($arProp['VALUES'] as $arOneValue){

                                                        $arOneValue['NAME'] = htmlspecialcharsbx($arOneValue['NAME']);
                                                        ?>
                                                        <li data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>" data-onevalue="<? echo $arOneValue['ID']; ?>" class="<? echo $strOneWidth; ?> display-none">
                                                            <i title="<? echo $arOneValue['NAME']; ?>">
                                                            </i>
                                                            <span class="cnt">
                                                                                        <span class="cnt_item" title="<? echo $arOneValue['NAME']; ?>">
                                                                                                <img src="<? echo $arOneValue['PICT']['SRC']; ?>" alt="<?=$arOneValue['NAME'];?>" />
                                                                                        </span>
                                                                                </span>
                                                        </li>
                                                        <?
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="bx_slide_left<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>">
                                            </div>
                                            <div class="bx_slide_right<? echo $strSlideStyle; ?>" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                }
                            }
                            unset($arProp);
                            ?>
                        </div>
                    <?
                    }
                    ?>


                    <?                  if ('Y' == $arParams['SHOW_MAX_QUANTITY']){

                    if (isset($arResult['OFFERS'])
                    && !empty($arResult['OFFERS'])){
                    ?>
                        <p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>" class="display-none">
                            <? echo GetMessage('OSTATOK'); ?>:
                            <span>
                                                </span>
                        </p>
                        <?
                    } else {

                    if ('Y' == $arResult['CATALOG_QUANTITY_TRACE']
                        && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']){
                        ?>
                        <p id="<? echo $arItemIDs['QUANTITY_LIMIT']; ?>">
                            <? echo GetMessage('OSTATOK'); ?>:
                            <span>
                                                        <? echo $arResult['CATALOG_QUANTITY']; ?>
                                                </span>
                        </p>
                    <?
                    }
                    }
                    }
                    } else {
                    ?>
                        <div class="item_buttons vam">
                        <span class="item_buttons_counter_block<? echo ($canBuy ? '' : ' display-none'); ?>" id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>">
<?
if ($showBuyBtn){
    ?>
    <a href="javascript:void(0);" class="bx_big bx_bt_button bx_cart"<? if(isset($arResult['BUY_ID']) && !empty($arResult['BUY_ID']) && $arResult['BUY_ID'] != $arResult['ID']): ?> data-buy-id="<?=$arResult['BUY_ID'];?>"<? endif; ?> id="<? echo $arItemIDs['BUY_LINK']; ?>">
                                                                <i class="fa fa-shopping-cart">
                                                                </i>
        <? echo $buyBtnMessage; ?>
                                                        </a>
    <?
}

if ($showAddBtn){
    ?>
    <a href="javascript:void(0);" class="bx_big bx_bt_button bx_cart"<? if(isset($arResult['BUY_ID']) && !empty($arResult['BUY_ID']) && $arResult['BUY_ID'] != $arResult['ID']): ?> data-buy-id="<?=$arResult['BUY_ID'];?>"<? endif; ?> id="<? echo $arItemIDs['ADD_BASKET_LINK']; ?>">
                                                                <i class="fa fa-shopping-cart">
                                                                </i>
        <? echo $addToBasketBtnMessage; ?>
                                                        </a>
    <?
}
?>
                                                </span>
                            <?
                            if(false){
                                ?>
                                <span id="<? echo $arItemIDs['NOT_AVAILABLE_MESS']; ?>" class="bx_notavailable<?=($showSubscribeBtn ? ' bx_notavailable_subscribe' : ''); ?><? echo (!$canBuy ? '' : ' display-none'); ?>">
                                                        <? echo $notAvailableMessage; ?>
                                                </span>
                                <?
                            }

                            if($showSubscribeBtn && !$canBuy){

                                $APPLICATION->IncludeComponent('bitrix:catalog.product.subscribe','subscribe',
                                    array(
                                        'PRODUCT_ID' => $arResult['ID'],
                                        'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                        'BUTTON_CLASS' => 'bx_big bx_bt_button',
                                        'DEFAULT_DISPLAY' => !$canBuy,
                                    ),
                                    $component, array('HIDE_ICONS' => 'Y')
                                );
                            };
                            ?>


                            <?
                            if($arParams['DISPLAY_COMPARE']){
                                ?>
                                <span class="item_buttons_counter_block">
<?
if ($arParams['DISPLAY_COMPARE']){
    ?>
    <a href="javascript:void(0);" class="bx_big bx_bt_button_type_2 bx_cart" id="<? echo $arItemIDs['COMPARE_LINK']; ?>">
                                                                <? echo $compareBtnMessage; ?>
                                                        </a>
    <?
}
?>
                                                </span>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }

                    unset($showAddBtn, $showBuyBtn);
                    ?>
                </div>
            </div>
        </div>
        <div class="row product-tabs additional-information">
            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                <?
                $instruction = '';
                $file_name = '';
                $instruction_name = '';

                $tab_headers = array();
                $tab_panels	= array();
                $tab_images	= array();

                if(isset($arResult['TABS']) && !empty($arResult['TABS'])){

                    if(isset($arResult['TABS']['tab_headers']) && !empty($arResult['TABS']['tab_headers'])){
                        $tab_headers = $arResult['TABS']['tab_headers'];
                    }

                    if(isset($arResult['TABS']['tab_panels']) && !empty($arResult['TABS']['tab_panels'])){
                        $tab_panels = $arResult['TABS']['tab_panels'];
                    }

                    if(isset($arResult['TABS']['tab_images']) && !empty($arResult['TABS']['tab_images'])){
                        $tab_images = $arResult['TABS']['tab_images'];
                    }

                }

                if(isset($arResult['INSTRUCTION']) && !empty($arResult['INSTRUCTION'])){

                    if(isset($arResult['INSTRUCTION']['href']) && !empty($arResult['INSTRUCTION']['href'])){
                        $instruction = $arResult['INSTRUCTION']['href'];
                    }

                    if(isset($arResult['INSTRUCTION']['download']) && !empty($arResult['INSTRUCTION']['download'])){
                        $file_name = $arResult['INSTRUCTION']['download'];
                    }

                    if(isset($arResult['INSTRUCTION']['name']) && !empty($arResult['INSTRUCTION']['name'])){
                        $instruction_name = $arResult['INSTRUCTION']['name'];
                    }

                }

                $hasActiveTabTitle = false;

                ?>
                <div id="tabs" class="clearfix tabs invisible">
                    <ul id="tabs-header-area" class="col-xs-12 nav nav-tabs" role="tablist">

                        <?
                        if($arResult["DETAIL_TEXT"]){
                            ?>

                            <li role="presentation" class="<?php if(!$hasActiveTabTitle): ?>active <?php endif; ?>detail clearfix">
                                <a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">
                                    <?php  echo GetMessage("CT_ABOUT_PRODUCT"); ?>
                                </a>
                            </li>
                            <?
                            $hasActiveTabTitle = true;
                        };
                        ?>
                        <li role="presentation" class="<?php if(!$hasActiveTabTitle): ?>active <?php endif; ?>comments clearfix">
                            <a href="#comments" aria-controls="comments" role="tab" data-toggle="tab">
                                <?php  echo GetMessage("CT_COMMENTS"); ?>
                            </a>
                        </li>
                        <?      if($hasVideo){
                            ?>
                            <li role="presentation" class="<?php if(!$hasActiveTabTitle): ?>active <?php endif; ?>video clearfix">
                                <a href="#video" aria-controls="video" role="tab" data-toggle="tab">
                                    <?php  echo GetMessage("CT_VIDEO"); ?>
                                </a>
                            </li>
                            <?
                            $hasActiveTabTitle = true;

                        };
                        ?>
                        <?      if(count($arResult["LINKED_ELEMENTS"])>0){
                            ?>
                            <li role="presentation" class="<?php if(!$hasActiveTabTitle): ?>active <?php endif; ?>linked clearfix">
                                <a href="#linked" aria-controls="linked" role="tab" data-toggle="tab">
                                    <?php echo $arResult["LINKED_ELEMENTS"][0]["IBLOCK_NAME"]?>
                                </a>
                            </li>
                            <?
                            $hasActiveTabTitle = true;


                        };
                        ?>
                        <?      if(sizeof($tab_headers)){

                            foreach ($tab_headers as $tkey => $tvalue){

                                ?>
                                <li role="presentation" class="<?php if(!$hasActiveTabTitle): ?>active <?php endif; ?>link<?php echo $tkey; ?> link clearfix">
                                    <a href="#link<?php echo $tkey; ?>" aria-controls="link<?php echo $tkey; ?>" role="tab" data-toggle="tab">
                                        <?php echo $tvalue; ?>
                                    </a>
                                </li>
                                <?
                                $hasActiveTabTitle = true;


                            };
                            ?>
                        <?      };

                        $hasActiveTabContent = false;

                        ?>
                    </ul>
                    <div class="tab-content">
                        <?      if($arResult["DETAIL_TEXT"]){
                            ?>
                            <div role="tabpanel" id="detail" class="tab-pane<?php if(!$hasActiveTabContent): ?> active<?php endif; ?> linked elements clearfix" itemprop="description">
                                <?php   echo $arResult["DETAIL_TEXT"];
                                ?>
                            </div>
                            <?
                            $hasActiveTabContent = true;
                        };?>
                        <?      if($hasVideo){ ?>
                            <div role="tabpanel" id="video" class="tab-pane<?php if(!$hasActiveTabContent): ?> active<?php endif; ?> video elements clearfix">
                                <?          foreach ($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $key=>$value){
                                    if($value != "-" && $value != ""){
                                        ?>
                                        <div class="video row">
                                            <iframe width="640" height="390" src="<?php  echo $value; ?>" frameborder="0" allowfullscreen="allowfullscreen">
                                            </iframe>
                                        </div>
                                    <?              };

                                    ?>
                                <?          }; ?>
                            </div>
                            <?
                            $hasActiveTabContent = true;

                        }; ?>
                        <?      if(count($arResult["LINKED_ELEMENTS"])>0){ ?>
                            <div role="tabpanel" id="linked" class="tab-pane<?php if(!$hasActiveTabContent): ?> active<?php endif; ?> linked elements clearfix">
                                <ul class="linked">
                                    <?          foreach($arResult["LINKED_ELEMENTS"] as $arElement){ ?>
                                        <li>
                                            <a href="<?php echo $arElement["DETAIL_PAGE_URL"]?>">
                                                <?php echo $arElement["NAME"]?>
                                            </a>
                                        </li>
                                    <?          };
                                    ?>
                                </ul>
                            </div>
                            <?
                            $hasActiveTabContent = true;
                        }; ?>
                        <?      if(sizeof($tab_panels)){ ?>
                            <?          foreach ($tab_panels as $tkey=>$tvalue){ ?>
                                <div role="tabpanel" class="tab-pane<?php if(!$hasActiveTabContent): ?> active<?php endif; ?> linked elements clearfix" id="link<?php echo $tkey; ?>">
                                    <?              if(isset($tab_images[$tkey])){ ?>
                                    <div class="row clearfix">
                                        <div class="tab-image col-sx-12 col-sm-12 col-md-4">
                                            <img src="<?php echo $tab_images[$tkey]; ?>" class="img-responsive" alt="<?php echo htmlspecialchars($tab_headers[$tkey], ENT_HTML401, LANG_CHARSET);?>" />
                                        </div>
                                        <div class="tab-description col-sx-12 col-sm-12 col-md-8">
                                            <?              }; ?>
                                            <?php echo $tvalue; ?>
                                            <?              if(isset($tab_images[$tkey])){ ?>
                                        </div>
                                    </div>
                                <?              }; ?>
                                </div>
                                <?
                                $hasActiveTabContent = true;
                            }; ?>
                        <?      }; ?>
                        <?
                        if (isset($arResult['OFFERS'])
                            && !empty($arResult['OFFERS'])){

                            foreach ($arResult['JS_OFFERS'] as &$arOneJS){

                                if ($arOneJS['PRICE']['DISCOUNT_VALUE'] != $arOneJS['PRICE']['VALUE']){

                                    $arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arOneJS['PRICE']['DISCOUNT_DIFF_PERCENT'];
                                    $arOneJS['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arOneJS['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'];

                                }

                                $strProps = '';
                                if ($arResult['SHOW_OFFERS_PROPS']){

                                    if (!empty($arOneJS['DISPLAY_PROPERTIES'])){

                                        foreach ($arOneJS['DISPLAY_PROPERTIES'] as $arOneProp){

                                            $strProps   .= '<dt>'.$arOneProp['NAME'].'</dt><dd>'.(
                                                is_array($arOneProp['VALUE'])
                                                    ? implode(' / ', $arOneProp['VALUE'])
                                                    : $arOneProp['VALUE']
                                                ).'</dd>';
                                        }
                                    }
                                }

                                $arOneJS['DISPLAY_PROPERTIES'] = $strProps;
                            }

                            if (isset($arOneJS))
                                unset($arOneJS);

                            $arJSParams = array(
                                'CONFIG' => array(
                                    'USE_CATALOG' => $arResult['CATALOG'],
                                    'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                                    'SHOW_PRICE' => true,
                                    'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] == 'Y'),
                                    'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] == 'Y'),
                                    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                                    'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
                                    'OFFER_GROUP' => $arResult['OFFER_GROUP'],
                                    'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
                                    'SHOW_BASIS_PRICE' => ($arParams['SHOW_BASIS_PRICE'] == 'Y'),
                                    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                                    'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                                    'USE_STICKERS' => true,
                                    'USE_SUBSCRIBE' => $showSubscribeBtn,
                                ),
                                'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
                                'VISUAL' => array(
                                    'ID' => $arItemIDs['ID'],
                                ),
                                'DEFAULT_PICTURE' => array(
                                    'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
                                    'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
                                ),
                                'PRODUCT' => array(
                                    'ID' => $arResult['ID'],
                                    'NAME' => $arResult['~NAME']
                                ),
                                'BASKET' => array(
                                    'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
                                    'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                                    'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                                ),
                                'OFFERS' => $arResult['JS_OFFERS'],
                                'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
                                'TREE_PROPS' => $arSkuProps
                            );

                            if ($arParams['DISPLAY_COMPARE']){
                                $arJSParams['COMPARE'] = array(
                                    'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                                    'COMPARE_PATH' => $arParams['COMPARE_PATH']
                                );
                            }

                        } else {

                            $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
                            if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties){
                                ?>
                                <div id="<? echo $arItemIDs['BASKET_PROP_DIV']; ?>" class="display-none">
                                    <?
                                    if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])){

                                        foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){
                                            ?>
                                            <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
                                            <?
                                            if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
                                                unset($arResult['PRODUCT_PROPERTIES'][$propID]);
                                        }
                                    }

                                    $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);

                                    if (!$emptyProductProperties){
                                        ?>
                                        <table>
                                            <?
                                            foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo){
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?                  echo $arResult['PROPERTIES'][$propID]['NAME']; ?>
                                                    </td>
                                                    <td>
                                                        <?
                                                        if(
                                                            'L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE']
                                                            && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']
                                                        ){

                                                            foreach($propInfo['VALUES'] as $valueID => $value){
                                                                ?>
                                                                <label>
                                                                    <input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>>
                                                                    <? echo $value; ?>
                                                                </label>
                                                                <br />
                                                                <?
                                                            }

                                                        } else {
                                                            ?>
                                                            <select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]">
                                                                <?
                                                                foreach($propInfo['VALUES'] as $valueID => $value){
                                                                    ?>
                                                                    <option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>>
                                                                        <?      echo $value; ?>
                                                                    </option>
                                                                    <?
                                                                }
                                                                ?>
                                                            </select>
                                                            <?
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?
                                            }
                                            ?>
                                        </table>
                                        <?
                                    }
                                    ?>
                                </div>
                                <?
                            }

                            if ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] != $arResult['MIN_PRICE']['VALUE']){

                                $arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'];
                                $arResult['MIN_BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'] = -$arResult['MIN_BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'];

                            }

                            $arJSParams = array(
                                'CONFIG' => array(
                                    'USE_CATALOG' => $arResult['CATALOG'],
                                    'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                                    'SHOW_PRICE' => (isset($arResult['MIN_PRICE']) && !empty($arResult['MIN_PRICE']) && is_array($arResult['MIN_PRICE'])),
                                    'SHOW_DISCOUNT_PERCENT' => ($arParams['SHOW_DISCOUNT_PERCENT'] == 'Y'),
                                    'SHOW_OLD_PRICE' => ($arParams['SHOW_OLD_PRICE'] == 'Y'),
                                    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                                    'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
                                    'SHOW_BASIS_PRICE' => ($arParams['SHOW_BASIS_PRICE'] == 'Y'),
                                    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                                    'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                                    'USE_STICKERS' => true,
                                    'USE_SUBSCRIBE' => $showSubscribeBtn,
                                ),
                                'VISUAL' => array(
                                    'ID' => $arItemIDs['ID'],
                                ),
                                'PRODUCT_TYPE' => $arResult['CATALOG_TYPE'],
                                'PRODUCT' => array(
                                    'ID' => $arResult['ID'],
                                    'PICT' => $arFirstPhoto,
                                    'NAME' => $arResult['~NAME'],
                                    'SUBSCRIPTION' => true,
                                    'PRICE' => $arResult['MIN_PRICE'],
                                    'BASIS_PRICE' => $arResult['MIN_BASIS_PRICE'],
                                    'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
                                    'SLIDER' => $arResult['MORE_PHOTO'],
                                    //'CAN_BUY' => $arResult['CAN_BUY'],
                                    'CAN_BUY' => true,
                                    'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
                                    'QUANTITY_FLOAT' => is_double($arResult['CATALOG_MEASURE_RATIO']),
                                    'MAX_QUANTITY' => $arResult['CATALOG_QUANTITY'],
                                    'STEP_QUANTITY' => $arResult['CATALOG_MEASURE_RATIO'],
                                ),
                                'BASKET' => array(
                                    'ADD_PROPS' => ($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y'),
                                    'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                    'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                    'EMPTY_PROPS' => $emptyProductProperties,
                                    'BASKET_URL' => $arParams['BASKET_URL'],
                                    'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                                    'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                                )
                            );

                            if ($arParams['DISPLAY_COMPARE']){
                                $arJSParams['COMPARE'] = array(
                                    'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                                    'COMPARE_PATH' => $arParams['COMPARE_PATH']
                                );
                            }

                            unset($emptyProductProperties);
                        }
                        ?>
                        <script type="text/javascript">
                            //<!--
                            var <? echo $strObName; ?> = new JCCatalogElement(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                            BX.message({
                                ECONOMY_INFO_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO'); ?>',
                                BASIS_PRICE_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_BASIS_PRICE') ?>',
                                TITLE_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
                                TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
                                BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
                                BTN_SEND_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS'); ?>',
                                BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
                                BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE'); ?>',
                                BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
                                TITLE_SUCCESSFUL: '<? echo GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>',
                                COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
                                COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
                                COMPARE_TITLE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
                                BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
                                PRODUCT_GIFT_LABEL: '<? echo GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL') ?>',
                                BTN_MESSAGE_IN_STOCK_YES: '<? echo GetMessageJS('CT_BCS_CATALOG_IN_STOCK_YES') ?>',
                                BTN_MESSAGE_IN_STOCK_NO: '<? echo GetMessageJS('CT_BCS_CATALOG_IN_STOCK_NO') ?>',
                                BTN_MESSAGE_IN_STOCK_NOT_MUCH: '<? echo GetMessageJS('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH') ?>',
                                COUNT_NOT_MUCH: <?=$not_much;?>,
                                MODELS_MORE: '<?=GetMessage('TMPL_SUITABLE_MODELS_MORE');?>',
                                READ_MORE: '<?php  echo GetMessage('READ_MORE'); ?>',
                                SITE_ID: '<? echo SITE_ID; ?>'
                            });

                            jQuery(document).on('yacounter21503785inited', function(){

                                window.dataLayer = window.dataLayer || [];
                                dataLayer.push({
                                    "ecommerce": {
                                        "currencyCode": "<?php echo htmlspecialcharsbx($minPrice['CURRENCY']); ?>",
                                        "detail": {
                                            "products": [
                                                {
                                                    "id": "<?php echo htmlspecialcharsbx($arResult['~ID']);?>",
                                                    "name" : "<?php echo htmlspecialcharsbx($arResult['~NAME']);?>",
                                                    <?php if(!empty($minPrice['DISCOUNT_VALUE'])): ?>"price": <?php echo round((float)($minPrice['DISCOUNT_VALUE']),0); ?>,<?php endif; ?>
                                                    <?php if(!empty($manufacturer)): ?>"brand": "<? echo $manufacturer; ?>",<?php endif; ?>
                                                    "category": "<?php echo htmlspecialcharsbx($arResult['SECTION_NAME']);?>"
                                                }
                                            ]
                                        }
                                    }
                                });




                            });
                            //-->
                        </script>