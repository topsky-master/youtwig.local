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
//$this->setFrameMode(true);
__IncludeLang(__DIR__."/lang/".LANGUAGE_ID."/template.php");

$isRelatedTo = isset($arParams['IS_RELATED_TO'])
&& $arParams['IS_RELATED_TO'] == 'Y'
    ? true
    : false;

if (!empty($arResult['ITEMS']))
{

    $arParams['LIST_IMAGE_WIDTH'] = isset($arParams['LIST_IMAGE_WIDTH']) ? (int)$arParams['LIST_IMAGE_WIDTH'] : 201;
    $arParams['LIST_IMAGE_HEIGHT'] = isset($arParams['LIST_IMAGE_HEIGHT']) ? (int)$arParams['LIST_IMAGE_HEIGHT'] : 201;

    $sliderID = $this->randString();

    $templateLibrary = array('popup');
    $currencyList = '';
    if (!empty($arResult['CURRENCIES']))
    {
        $templateLibrary[] = 'currency';
        $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
    }
    $templateData = array(
        'TEMPLATE_THEME' => __DIR__.'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
        'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME'],
        'TEMPLATE_LIBRARY' => $templateLibrary,
        'CURRENCIES' => $currencyList
    );
    unset($currencyList, $templateLibrary);

    $skuTemplate = array();
    if (!empty($arResult['SKU_PROPS']))
    {
        foreach ($arResult['SKU_PROPS'] as $arProp)
        {
            $propId = $arProp['ID'];
            $skuTemplate[$propId] = array(
                'SCROLL' => array(
                    'START' => '',
                    'FINISH' => '',
                ),
                'FULL' => array(
                    'START' => '',
                    'FINISH' => '',
                ),
                'ITEMS' => array()
            );
            $templateRow = '';
            if ('TEXT' == $arProp['SHOW_MODE'])
            {
                $skuTemplate[$propId]['SCROLL']['START'] = '<div class="bx_item_detail_size full" id="#ITEM#_prop_'.$propId.'_cont">'.
                    '<span class="bx_item_section_name_gray">'.htmlspecialcharsbx($arProp['NAME']).'</span>'.
                    '<div class="bx_size_scroller_container"><div class="bx_size"><ul id="#ITEM#_prop_'.$propId.'_list" class="width#WIDTH#">';;
                $skuTemplate[$propId]['SCROLL']['FINISH'] = '</ul></div>'.
                    '<div class="bx_slide_left" id="#ITEM#_prop_'.$propId.'_left" data-treevalue="'.$propId.'"></div>'.
                    '<div class="bx_slide_right" id="#ITEM#_prop_'.$propId.'_right" data-treevalue="'.$propId.'"></div>'.
                    '</div></div>';

                $skuTemplate[$propId]['FULL']['START'] = '<div class="bx_item_detail_size" id="#ITEM#_prop_'.$propId.'_cont">'.
                    '<span class="bx_item_section_name_gray">'.htmlspecialcharsbx($arProp['NAME']).'</span>'.
                    '<div class="bx_size_scroller_container"><div class="bx_size"><ul id="#ITEM#_prop_'.$propId.'_list" class="width#WIDTH#">';;
                $skuTemplate[$propId]['FULL']['FINISH'] = '</ul></div>'.
                    '<div class="bx_slide_left display-none" id="#ITEM#_prop_'.$propId.'_left" data-treevalue="'.$propId.'"></div>'.
                    '<div class="bx_slide_right display-none" id="#ITEM#_prop_'.$propId.'_right" data-treevalue="'.$propId.'"></div>'.
                    '</div></div>';
                foreach ($arProp['VALUES'] as $value)
                {
                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                    $skuTemplate[$propId]['ITEMS'][$value['ID']] = '<li data-treevalue="'.$propId.'_'.$value['ID'].
                        '" data-onevalue="'.$value['ID'].'" class="width#WIDTH#" title="'.$value['NAME'].'"><i></i><span class="cnt">'.$value['NAME'].'</span></li>';
                }
                unset($value);
            }
            elseif ('PICT' == $arProp['SHOW_MODE'])
            {
                $skuTemplate[$propId]['SCROLL']['START'] = '<div class="bx_item_detail_scu full" id="#ITEM#_prop_'.$propId.'_cont">'.
                    '<span class="bx_item_section_name_gray">'.htmlspecialcharsbx($arProp['NAME']).'</span>'.
                    '<div class="bx_scu_scroller_container"><div class="bx_scu"><ul id="#ITEM#_prop_'.$propId.'_list" class="width#WIDTH#">';
                $skuTemplate[$propId]['SCROLL']['FINISH'] = '</ul></div>'.
                    '<div class="bx_slide_left" id="#ITEM#_prop_'.$propId.'_left" data-treevalue="'.$propId.'"></div>'.
                    '<div class="bx_slide_right" id="#ITEM#_prop_'.$propId.'_right" data-treevalue="'.$propId.'"></div>'.
                    '</div></div>';

                $skuTemplate[$propId]['FULL']['START'] = '<div class="bx_item_detail_scu" id="#ITEM#_prop_'.$propId.'_cont">'.
                    '<span class="bx_item_section_name_gray">'.htmlspecialcharsbx($arProp['NAME']).'</span>'.
                    '<div class="bx_scu_scroller_container"><div class="bx_scu"><ul id="#ITEM#_prop_'.$propId.'_list" class="width#WIDTH#">';
                $skuTemplate[$propId]['FULL']['FINISH'] = '</ul></div>'.
                    '<div class="bx_slide_left display-none" id="#ITEM#_prop_'.$propId.'_left" data-treevalue="'.$propId.'"></div>'.
                    '<div class="bx_slide_right display-none" id="#ITEM#_prop_'.$propId.'_right" data-treevalue="'.$propId.'"></div>'.
                    '</div></div>';
                foreach ($arProp['VALUES'] as $value)
                {
                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                    $skuTemplate[$propId]['ITEMS'][$value['ID']] = '<li data-treevalue="'.$propId.'_'.$value['ID'].
                        '" data-onevalue="'.$value['ID'].'" class="width#WIDTH#"><i title="'.$value['NAME'].'"></i>'.
                        '<span class="cnt"><img class="cnt_item" src="'.$value['PICT']['SRC'].'" title="'.$value['NAME'].'" /></span></span></li>';
                }
                unset($value);
            }
        }
        unset($templateRow, $arProp);
    }

    $strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
    $strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
    $arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

    $elementClass = 4;




    ?>
    <div id="flexslider<?=$sliderID;?>" class="flexslider carousel bx_catalog_list_top pos-top-products top-products-slider catalog clearfix <? echo $templateData['TEMPLATE_CLASS']; ?>">
        <ul class="slides">
            <?

            foreach ($arResult['ITEMS'] as $key => $arItem){


                $newproduct = isset($arItem['PROPERTIES'])
                &&isset($arItem['PROPERTIES']['NEWPRODUCT'])
                &&isset($arItem['PROPERTIES']['NEWPRODUCT']['VALUE'])
                &&!empty($arItem['PROPERTIES']['NEWPRODUCT']['VALUE'])
                &&mb_strtolower($arItem['PROPERTIES']['NEWPRODUCT']['VALUE']) == 'да'
                    ? true
                    : false;

                $saleproduct = isset($arItem['PROPERTIES'])
                &&isset($arItem['PROPERTIES']['SALEPRODUCT'])
                &&isset($arItem['PROPERTIES']['SALEPRODUCT']['VALUE'])
                &&!empty($arItem['PROPERTIES']['SALEPRODUCT']['VALUE'])
                &&mb_strtolower($arItem['PROPERTIES']['SALEPRODUCT']['VALUE']) == 'да'
                    ? true
                    : false;

                $com_black  = isset($arItem['PROPERTIES'])
                &&isset($arItem['PROPERTIES']['COM_BLACK'])
                &&isset($arItem['PROPERTIES']['COM_BLACK']['VALUE'])
                &&!empty($arItem['PROPERTIES']['COM_BLACK']['VALUE'])
                    ? $arItem['PROPERTIES']['COM_BLACK']['VALUE']
                    : '';

                $quality = isset($arItem['PROPERTIES'])
                &&isset($arItem['PROPERTIES']['QUALITY'])
                &&isset($arItem['PROPERTIES']['QUALITY']['VALUE'])
                &&!empty($arItem['PROPERTIES']['QUALITY']['VALUE'])
                    ? $arItem['PROPERTIES']['QUALITY']['VALUE']
                    : '';

                $quantity   = $arItem['PRINT_QUANTITY'];

                unset($arItem['PROPERTIES']['QUALITY'], $arItem['PROPERTIES']['COM_BLACK'], $arItem['PROPERTIES']['NEWPRODUCT'], $arItem['PROPERTIES']['SALEPRODUCT']);

                ?>
                <li<?php if($isRelatedTo){ ?> itemprop="isRelatedTo"<?php } ?> itemscope itemtype="http://schema.org/Product" class="slider-item">
                    <input type="hidden" class="product_id" name="product_id[]" value="<?=$arItem['ID'];?>" />
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                    $strMainID = $this->GetEditAreaId($arItem['ID']);

                    $arItemIDs = array(
                        'ID' => $strMainID.'',
                        'PICT' => $strMainID.'_pict',
                        'SECOND_PICT' => $strMainID.'_pict',
                        'STICKER_ID' => $strMainID.'_sticker',
                        'SECOND_STICKER_ID' => $strMainID.'_secondsticker',
                        'QUANTITY' => $strMainID.'_quantity',
                        'QUANTITY_DOWN' => $strMainID.'_quant_down',
                        'QUANTITY_UP' => $strMainID.'_quant_up',
                        'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
                        'BUY_LINK' => $strMainID.'_buy_link',
                        'BUY_LINK_ICON' => $strMainID.'_buy_link_icon',
                        'BASKET_ACTIONS' => $strMainID.'_basket_actions',
                        'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
                        'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
                        'COMPARE_LINK' => $strMainID.'_compare_link',

                        'PRICE' => $strMainID.'_price',
                        'DSC_PERC' => $strMainID.'_dsc_perc',
                        'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
                        'PROP_DIV' => $strMainID.'_sku_tree',
                        'PROP' => $strMainID.'_prop_',
                        'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
                        'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
                    );

                    $strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

                    $productTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])&& $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                        : $arItem['NAME']
                    );
                    $imgTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
                        : $arItem['NAME']
                    );

                    $imgTitle = CUtil::JSEscape($imgTitle);

                    $minPrice = false;
                    if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
                        $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);

                    ?>
                    <div class="bx_catalog_item">
                        <div class="bx_catalog_item_container" id="<? echo $strMainID; ?>">
                            <div class="bx_catalog_item_images">
                                <? if($newproduct){?>
                                    <span class="new">
							    <?=GetMessage("CRL_NEW"); ?>
							</span>
                                <?}?>
                                <? if($saleproduct){?>
                                    <span class="sale">
							    <?=GetMessage("CRL_SALE"); ?>
							</span>
                                <?}?>
                                <ul class="add-to-links add-to-links<? echo $strMainID; ?>">
                                    <li class="addtoCartElt addtoCartElt<?=$arItem['ID'];?><? if ($arItem['CAN_BUY']){ ?> hidden<? } ?> addtoCartElt<? echo $strMainID; ?>">
                                    <span class="ajax_add_to_cart_button ajax_add_to_cart_button<? echo $strMainID; ?>" id="<?=$arItemIDs['BUY_LINK_ICON'];?>">
                                    </span>
                                    </li>
                                    <li>
                                        <span class="lnk_view" onclick="location.href = '<? echo $arItem['DETAIL_PAGE_URL']; ?>';">
                                        </span>
                                    </li>
                                </ul>
                                <a id="<? echo $arItemIDs['PICT']; ?>" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" title="<?=$imgTitle; ?>">

                                    <?


                                    if(is_array($arItem["PREVIEW_PICTURE"])){

                                        $src = $arItem['PREVIEW_PICTURE']['SRC'];
                                        $image_thumb_width  = (int)$arParams['LIST_IMAGE_WIDTH'];

                                        $image_thumb_height = (int)$arParams['LIST_IMAGE_HEIGHT'];

                                        $src = rectangleImage(
                                            $_SERVER['DOCUMENT_ROOT'].$src,
                                            $image_thumb_width,
                                            $image_thumb_height,
                                            $src,
                                            '#ffffff'
                                        );



                                        ?>
                                        <img src="<? echo $src; ?>" alt="<?=($imgTitle); ?>" class="img-responsive" />
                                        <?

                                    }

                                    if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT'])
                                    {
                                        ?>
                                        <div id="<? echo $arItemIDs['DSC_PERC']; ?>" class="bx_stick_disc right bottom<? echo (0 < $minPrice['DISCOUNT_DIFF_PERCENT'] ? '' : ' display-none'); ?>">
                                            -<? echo $minPrice['DISCOUNT_DIFF_PERCENT']; ?>%
                                        </div>
                                        <?
                                    }
                                    if ($arItem['LABEL'])
                                    {
                                        ?>
                                        <div id="<? echo $arItemIDs['STICKER_ID']; ?>" class="bx_stick average left top" title="<? echo $arItem['LABEL_VALUE']; ?>">
                                            <? echo $arItem['LABEL_VALUE']; ?>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </a>
                            </div>
                            <div class="bx_catalog_item_title">
                                <a itemprop="url" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" title="<? echo $productTitle; ?>">
                                    <span itemprop="name"><? echo $productTitle; ?></span>
                                </a>
                            </div>
                            <?php if($quantity != ""){ ?>
                                <h5 class="on_stock">
                                    <?php echo GetMessage("CRL_QUANTITY"); ?><?=$quantity; ?>
                                </h5>
                            <?php } ?>
                            <?php if($com_black != ""){ ?>
                                <h5 class="on_stock">
                                    <?php echo GetMessage("COMMENT_TO_PRODUCT"); ?><?=$com_black; ?>
                                </h5>
                            <?php } ?>
                            <?php if($quality != ""){ ?>
                                <h5 class="on_stock">
                                    <?php echo GetMessage("CRL_QUALITY"); ?><?=join("/",$quality); ?>
                                </h5>
                            <?php } ?>
                            <?php

                            if($arItem["BONDS_NAME"]){ ?>
                                <h5 class="on_stock">
                                    <?php  echo GetMessage("CRL_BONDS_NAME"); ?><?php  echo $arItem["BONDS_NAME"]; ?>
                                </h5>
                                <?
                            };

                            if($arItem["SHELF"]){ ?>
                                <h5 class="on_stock">
                                    <?php  echo GetMessage("CRL_PROPERTY_RACK"); ?><?php  echo $arItem["SHELF"]; ?>
                                </h5>
                                <?
                            };

                            if($arItem["RACK"]){ ?>
                                <h5 class="on_stock">
                                    <?php  echo GetMessage("CRL_PROPERTY_SHELF"); ?><?php  echo $arItem["RACK"]; ?>
                                </h5>
                                <?
                            };
                            ?>
                            <div class="bx_catalog_item_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                <div id="<? echo $arItemIDs['PRICE']; ?>" class="bx_price">
                                    <?
                                    if (!empty($minPrice))
                                    {
                                        if ('N' == $arParams['PRODUCT_DISPLAY_MODE'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
                                        {
                                            echo GetMessage(
                                                'CT_BCS_TPL_MESS_PRICE_SIMPLE_MODE',
                                                array(
                                                    '#PRICE#' => $minPrice['PRINT_DISCOUNT_VALUE'],
                                                    '#MEASURE#' => GetMessage(
                                                        'CT_BCS_TPL_MESS_MEASURE_SIMPLE_MODE',
                                                        array(
                                                            '#VALUE#' => $minPrice['CATALOG_MEASURE_RATIO'],
                                                            '#UNIT#' => $minPrice['CATALOG_MEASURE_NAME']
                                                        )
                                                    )
                                                )
                                            );
                                        }
                                        else
                                        {
                                            echo $minPrice['PRINT_DISCOUNT_VALUE'];
                                        }
                                        if ('Y' == $arParams['SHOW_OLD_PRICE'] && $minPrice['DISCOUNT_VALUE'] < $minPrice['VALUE'])
                                        {
                                            ?>                  <span>
                                    <? echo $minPrice['PRINT_VALUE']; ?>
                                </span>
                                            <?
                                        }
                                    }
                                    ?>
                                    <meta itemprop="price" content="<? echo str_ireplace(',','.',trim($minPrice['DISCOUNT_VALUE'])); ?>" />
                                    <?  if(isset($minPrice['CURRENCY']) && !empty($minPrice['CURRENCY'])){?>
                                        <meta itemprop="priceCurrency" content="<?=$minPrice['CURRENCY'];?>" />
                                    <? } ?>
                                    <? if($arItem['CAN_BUY']){?>
                                        <link itemprop="availability" href="http://schema.org/InStock" />
                                    <? }

                                    unset($minPrice);
                                    ?>
                                </div>
                            </div>
                            <?
                            if($arItem['CATALOG_SUBSCRIBE'] == 'Y')
                                $showSubscribeBtn = true;
                            else
                                $showSubscribeBtn = false;
                            $compareBtnMessage = ($arParams['MESS_BTN_COMPARE'] != '' ? $arParams['MESS_BTN_COMPARE'] : GetMessage('CT_BCS_TPL_MESS_BTN_COMPARE'));
                            if (!isset($arItem['OFFERS']) || empty($arItem['OFFERS']))
                            {
                                ?>
                                <div class="bx_catalog_item_controls row hidden">
                                    <?

                                    {
                                        ?>

                                        <?
                                        if ('Y' == $arParams['USE_PRODUCT_QUANTITY'])
                                        {
                                            ?>
                                            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 form-count-area">
                                                <div class="form-inline form-count">
                                                    <a id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>" href="javascript:void(0)" class="btn btn-default" rel="nofollow">
                                                        -
                                                    </a>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="<? echo $arItemIDs['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<? echo $arItem['CATALOG_MEASURE_RATIO']; ?>">
                                                    </div>
                                                    <a id="<? echo $arItemIDs['QUANTITY_UP']; ?>" href="javascript:void(0)" class="btn btn-default" rel="nofollow">
                                                        +
                                                    </a>
                                                    <span id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>">
                                        <? echo $arItem['CATALOG_MEASURE_NAME']; ?>
                                    </span>
                                                </div>
                                            </div>
                                            <?
                                        }
                                        ?>
                                        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 form-button-area">
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>" class="bx_catalog_item_controls_blocktwo">
                                                        <a id="<? echo $arItemIDs['BUY_LINK']; ?>"<? if(isset($arItem['BUY_ID']) && !empty($arItem['BUY_ID']) && $arItem['BUY_ID'] != $arItem['ID']): ?> data-buy-id="<?=$arItem['BUY_ID'];?>"<? endif; ?> class="btn btn-info" href="javascript:void(0)" rel="nofollow">
                                                            <?
                                                            if ($arParams['ADD_TO_BASKET_ACTION'] == 'BUY')
                                                            {
                                                                echo ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCS_TPL_MESS_BTN_BUY'));
                                                            }
                                                            else
                                                            {
                                                                echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? $arParams['MESS_BTN_ADD_TO_BASKET'] : GetMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET'));
                                                            }
                                                            ?>
                                                        </a>
                                                    </div>


                                                    <?
                                                    if ($arParams['DISPLAY_COMPARE'])
                                                    {
                                                        ?>
                                                        <div class="bx_catalog_item_controls_blocktwo">
                                                            <a id="<? echo $arItemIDs['COMPARE_LINK']; ?>" class="btn btn-info" href="javascript:void(0)">
                                                                <? echo $compareBtnMessage; ?>
                                                            </a>
                                                        </div>
                                                        <?
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?
                                    }

                                    {
                                        if(false){
                                            ?>
                                            <div id="<? echo $arItemIDs['NOT_AVAILABLE_MESS']; ?>" class="bx_catalog_item_controls_blockone">
                                <span class="bx_notavailable">
                <?
                echo ('' != $arParams['MESS_NOT_AVAILABLE'] ? $arParams['MESS_NOT_AVAILABLE'] : GetMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE'));
                ?>
                                </span>
                                            </div>
                                            <?
                                        }

                                        if($showSubscribeBtn):
                                            $APPLICATION->includeComponent('bitrix:catalog.product.subscribe','subscribe',
                                                array(
                                                    'PRODUCT_ID' => $arItem['ID'],
                                                    'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                                    'BUTTON_CLASS' => 'subscribe-btn',
                                                    'DEFAULT_DISPLAY' => true,
                                                    //'BUY_ID' => $arItem['BUY_ID']
                                                ),
                                                $component, array('HIDE_ICONS' => 'Y')
                                            );
                                        endif;
                                        ?><?
                                        if ($arParams['DISPLAY_COMPARE'])
                                        {
                                            ?>
                                            <div class="bx_catalog_item_controls_blocktwo"><?
                                                if ($arParams['DISPLAY_COMPARE'])
                                                {
                                                    ?>
                                                    <a id="<? echo $arItemIDs['COMPARE_LINK']; ?>" class="bx_bt_button_type_2 bx_medium" href="javascript:void(0)">
                                                        <? echo $compareBtnMessage; ?>
                                                    </a>
                                                    <?
                                                }?>
                                            </div>
                                            <?
                                        }
                                    }
                                    ?>              </div><?
                            if (isset($arItem['DISPLAY_PROPERTIES']) && !empty($arItem['DISPLAY_PROPERTIES']))
                            {
                            ?>
                                <div class="bx_catalog_item_articul">
                                    <?
                                    foreach ($arItem['DISPLAY_PROPERTIES'] as $arOneProp)
                                    {
                                        ?>
                                        <p>
                                        <strong>
                                            <? echo $arOneProp['NAME']; ?>
                                        </strong> <?
                                        echo (
                                        is_array($arOneProp['DISPLAY_VALUE'])
                                            ? implode('<br>', $arOneProp['DISPLAY_VALUE'])
                                            : $arOneProp['DISPLAY_VALUE']
                                        );
                                        ?>          </p><?
                                    }
                                    ?>
                                </div>
                            <?
                            }
                            $emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
                            if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties)
                            {
                            ?>
                                <div id="<? echo $arItemIDs['BASKET_PROP_DIV']; ?>" class="display-none">
                                    <?
                                    if (!empty($arItem['PRODUCT_PROPERTIES_FILL']))
                                    {
                                        foreach ($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo)
                                        {
                                            ?>
                                            <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
                                            <?
                                            if (isset($arItem['PRODUCT_PROPERTIES'][$propID]))
                                                unset($arItem['PRODUCT_PROPERTIES'][$propID]);
                                        }
                                    }
                                    $emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
                                    if (!$emptyProductProperties)
                                    {
                                        ?>
                                        <table>
                                            <?
                                            foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo)
                                            {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <? echo $arItem['PROPERTIES'][$propID]['NAME']; ?>
                                                    </td>
                                                    <td>
                                                        <?
                                                        if(
                                                            'L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE']
                                                            && 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE']
                                                        )
                                                        {
                                                            foreach($propInfo['VALUES'] as $valueID => $value)
                                                            {
                                                                ?>
                                                                <label>
                                                                    <input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>>
                                                                    <? echo $value; ?>
                                                                </label>
                                                                <br>
                                                                <?
                                                            }
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
                                                            foreach($propInfo['VALUES'] as $valueID => $value)
                                                            {
                                                                ?>
                                                                <option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? 'selected' : ''); ?>>
                                                                    <? echo $value; ?>
                                                                </option>
                                                                <?
                                                            }
                                                            ?>
                                                            </select><?
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
                                $arJSParams = array(
                                    'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
                                    'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
                                    'SHOW_ADD_BASKET_BTN' => false,
                                    'SHOW_BUY_BTN' => true,
                                    'SHOW_ABSENT' => true,
                                    'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
                                    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                                    'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                                    'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
                                    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                                    'PRODUCT' => array(
                                        'ID' => $arItem['ID'],
                                        'NAME' => $productTitle,
                                        'PICT' => ('Y' == $arItem['SECOND_PICT'] ? $arItem['PREVIEW_PICTURE_SECOND'] : $arItem['PREVIEW_PICTURE']),
                                        //'CAN_BUY' => $arItem["CAN_BUY"],
                                        'CAN_BUY' => true,
                                        'SUBSCRIPTION' => ('Y' == $arItem['CATALOG_SUBSCRIPTION']),
                                        'CHECK_QUANTITY' => $arItem['CHECK_QUANTITY'],
                                        'MAX_QUANTITY' => $arItem['CATALOG_QUANTITY'],
                                        'STEP_QUANTITY' => $arItem['CATALOG_MEASURE_RATIO'],
                                        'QUANTITY_FLOAT' => is_double($arItem['CATALOG_MEASURE_RATIO']),
                                        'SUBSCRIBE_URL' => $arItem['~SUBSCRIBE_URL'],
                                        'BASIS_PRICE' => $arItem['MIN_BASIS_PRICE']
                                    ),
                                    'BASKET' => array(
                                        'ADD_PROPS' => ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET']),
                                        'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                        'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                        'EMPTY_PROPS' => $emptyProductProperties,
                                        'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                                        'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                                    ),
                                    'VISUAL' => array(
                                        'ID' => $arItemIDs['ID'],
                                        'PICT_ID' => ('Y' == $arItem['SECOND_PICT'] ? $arItemIDs['SECOND_PICT'] : $arItemIDs['PICT']),
                                        'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                                        'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                                        'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                                        'PRICE_ID' => $arItemIDs['PRICE'],
                                        'BUY_ID' => $arItemIDs['BUY_LINK'],
                                        'BASKET_PROP_DIV' => $arItemIDs['BASKET_PROP_DIV'],
                                        'BASKET_ACTIONS_ID' => $arItemIDs['BASKET_ACTIONS'],
                                        'NOT_AVAILABLE_MESS' => $arItemIDs['NOT_AVAILABLE_MESS'],
                                        'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
                                        'SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                        'BUY_LINK_ICON' => $arItemIDs['BUY_LINK_ICON']
                                    ),
                                    'LAST_ELEMENT' => $arItem['LAST_ELEMENT']
                                );
                                if ($arParams['DISPLAY_COMPARE'])
                                {
                                    $arJSParams['COMPARE'] = array(
                                        'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                                        'COMPARE_PATH' => $arParams['COMPARE_PATH']
                                    );
                                }
                                unset($emptyProductProperties);
                                ?><script type="text/javascript">
                                //<!--
                                var <? echo $strObName; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                                //-->
                            </script><?
                            }
                            else
                            {
                            if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
                            {
                            $canBuy = $arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY'];
                            ?>
                                <div class="bx_catalog_item_controls row">
                                    <?
                                    if ('Y' == $arParams['USE_PRODUCT_QUANTITY'])
                                    {
                                        ?>
                                        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 form-count-area">
                                            <div class="form-inline form-count">
                                                <a id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>" href="javascript:void(0)" class="btn btn-default" rel="nofollow">
                                                    -
                                                </a>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="<? echo $arItemIDs['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<? echo $arItem['CATALOG_MEASURE_RATIO']; ?>">
                                                </div>
                                                <a id="<? echo $arItemIDs['QUANTITY_UP']; ?>" href="javascript:void(0)" class="btn btn-default" rel="nofollow">
                                                    +
                                                </a>
                                                <span id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>">
                                    </span>
                                            </div>
                                        </div>
                                        <?
                                    }

                                    ?>
                                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 form-button-area">
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <?

                                                if($showSubscribeBtn):
                                                    $APPLICATION->includeComponent('bitrix:catalog.product.subscribe','subscribe',
                                                        array(
                                                            'PRODUCT_ID' => $arItem['ID'],
                                                            'BUTTON_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                                            'BUTTON_CLASS' => 'subscribe-btn',
                                                            'DEFAULT_DISPLAY' => !$canBuy,
                                                            //'BUY_ID' => $arItem['BUY_ID']
                                                        ),
                                                        $component, array('HIDE_ICONS' => 'Y')
                                                    );
                                                endif;

                                                ?>

                                                <div id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>" class="bx_catalog_item_controls_blocktwo<? echo ($canBuy ? '' : ' display-none'); ?>">
                                                    <a id="<? echo $arItemIDs['BUY_LINK']; ?>" class="btn btn-info" href="javascript:void(0)" rel="nofollow"><?
                                                        if ($arParams['ADD_TO_BASKET_ACTION'] == 'BUY')
                                                        {
                                                            echo ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCS_TPL_MESS_BTN_BUY'));
                                                        }
                                                        else
                                                        {
                                                            echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? $arParams['MESS_BTN_ADD_TO_BASKET'] : GetMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET'));
                                                        }
                                                        ?>
                                                    </a>
                                                </div>

                                                <?
                                                if ($arParams['DISPLAY_COMPARE'])
                                                {
                                                ?>
                                                <div class="bx_catalog_item_controls_blocktwo">
                                                    <a id="<? echo $arItemIDs['COMPARE_LINK']; ?>" class="btn btn-info" href="javascript:void(0)">
                                                        <? echo $compareBtnMessage; ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?
                                } ?>
                                </div>
                            <?
                            unset($canBuy);
                            }
                            ?>
                                <div class="bx_catalog_item_controls">
                                    <a class="btn btn-info" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"><?
                                        echo ('' != $arParams['MESS_BTN_DETAIL'] ? $arParams['MESS_BTN_DETAIL'] : GetMessage('CT_BCS_TPL_MESS_BTN_DETAIL'));
                                        ?>
                                    </a>
                                </div>
                            <?
                            $boolShowOfferProps = ('Y' == $arParams['PRODUCT_DISPLAY_MODE'] && $arItem['OFFERS_PROPS_DISPLAY']);
                            $boolShowProductProps = (isset($arItem['DISPLAY_PROPERTIES']) && !empty($arItem['DISPLAY_PROPERTIES']));
                            if ($boolShowProductProps || $boolShowOfferProps)
                            {
                            ?>
                                <div class="bx_catalog_item_articul">
                                    <?
                                    if ($boolShowProductProps)
                                    {
                                        foreach ($arItem['DISPLAY_PROPERTIES'] as $arOneProp)
                                        {
                                            ?>
                                            <p>
                                                <strong>
                                                    <? echo $arOneProp['NAME']; ?>
                                                </strong>
                                                <?
                                                echo (
                                                is_array($arOneProp['DISPLAY_VALUE'])
                                                    ? implode(' / ', $arOneProp['DISPLAY_VALUE'])
                                                    : $arOneProp['DISPLAY_VALUE']
                                                );
                                                ?>
                                            </p>
                                            <?
                                        }
                                    }
                                    if ($boolShowOfferProps)
                                    {
                                        ?>
                                        <span id="<? echo $arItemIDs['DISPLAY_PROP_DIV']; ?>" class="display-none">
                            </span>
                                        <?
                                    }
                                    ?>
                                </div>
                            <?
                            }
                            if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
                            {
                            if (!empty($arItem['OFFERS_PROP']))
                            {
                            $arSkuProps = array();
                            ?>
                                <div class="bx_catalog_item_scu" id="<? echo $arItemIDs['PROP_DIV']; ?>">
                                    <?
                                    foreach ($skuTemplate as $propId => $propTemplate)
                                    {
                                        if (!isset($arItem['SKU_TREE_VALUES'][$propId]))
                                            continue;
                                        $valueCount = count($arItem['SKU_TREE_VALUES'][$propId]);
                                        if ($valueCount > 5)
                                        {
                                            $fullWidth = ($valueCount*20).'';
                                            $itemWidth = (100/$valueCount).'';
                                            $rowTemplate = $propTemplate['SCROLL'];
                                        }
                                        else
                                        {
                                            $fullWidth = '100';
                                            $itemWidth = '20';
                                            $rowTemplate = $propTemplate['FULL'];
                                        }
                                        unset($valueCount);
                                        echo '<div>', str_replace(array('#ITEM#_prop_', '#WIDTH#'), array($arItemIDs['PROP'], $fullWidth), $rowTemplate['START']);
                                        foreach ($propTemplate['ITEMS'] as $value => $valueItem)
                                        {
                                            if (!isset($arItem['SKU_TREE_VALUES'][$propId][$value]))
                                                continue;
                                            echo str_replace(array('#ITEM#_prop_', '#WIDTH#'), array($arItemIDs['PROP'], $itemWidth), $valueItem);
                                        }
                                        unset($value, $valueItem);
                                        echo str_replace('#ITEM#_prop_', $arItemIDs['PROP'], $rowTemplate['FINISH']), '</div>';
                                    }
                                    unset($propId, $propTemplate);
                                    foreach ($arResult['SKU_PROPS'] as $arOneProp)
                                    {
                                        if (!isset($arItem['OFFERS_PROP'][$arOneProp['CODE']]))
                                            continue;
                                        $arSkuProps[] = array(
                                            'ID' => $arOneProp['ID'],
                                            'SHOW_MODE' => $arOneProp['SHOW_MODE'],
                                            'VALUES_COUNT' => $arOneProp['VALUES_COUNT']
                                        );
                                    }
                                    foreach ($arItem['JS_OFFERS'] as &$arOneJs)
                                    {
                                        if (0 < $arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'])
                                        {
                                            $arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'] = '-'.$arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'].'%';
                                            $arOneJs['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'] = '-'.$arOneJs['BASIS_PRICE']['DISCOUNT_DIFF_PERCENT'].'%';
                                        }
                                    }
                                    unset($arOneJs);
                                    ?>
                                </div>
                            <?
                            if ($arItem['OFFERS_PROPS_DISPLAY'])
                            {
                                foreach ($arItem['JS_OFFERS'] as $keyOffer => $arJSOffer)
                                {
                                    $strProps = '';
                                    if (!empty($arJSOffer['DISPLAY_PROPERTIES']))
                                    {
                                        foreach ($arJSOffer['DISPLAY_PROPERTIES'] as $arOneProp)
                                        {
                                            $strProps .= '<br>'.$arOneProp['NAME'].' <strong>'.(
                                                is_array($arOneProp['VALUE'])
                                                    ? implode(' / ', $arOneProp['VALUE'])
                                                    : $arOneProp['VALUE']
                                                ).'</strong>';
                                        }
                                    }
                                    $arItem['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = !empty($strProps) ? '<p>'.$strProps.'</p>' : '';
                                }
                            }
                            $arJSParams = array(
                                'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
                                'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
                                'SHOW_ADD_BASKET_BTN' => false,
                                'SHOW_BUY_BTN' => true,
                                'SHOW_ABSENT' => true,
                                'SHOW_SKU_PROPS' => $arItem['OFFERS_PROPS_DISPLAY'],
                                'SECOND_PICT' => $arItem['SECOND_PICT'],
                                'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
                                'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
                                'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                                'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                                'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                                'DEFAULT_PICTURE' => array(
                                    'PICTURE' => $arItem['PRODUCT_PREVIEW'],
                                    'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
                                ),
                                'VISUAL' => array(
                                    'ID' => $arItemIDs['ID'],
                                    'PICT_ID' => $arItemIDs['PICT'],
                                    'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
                                    'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                                    'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                                    'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                                    'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
                                    'PRICE_ID' => $arItemIDs['PRICE'],
                                    'TREE_ID' => $arItemIDs['PROP_DIV'],
                                    'TREE_ITEM_ID' => $arItemIDs['PROP'],
                                    'BUY_ID' => $arItemIDs['BUY_LINK'],
                                    'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
                                    'DSC_PERC' => $arItemIDs['DSC_PERC'],
                                    'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
                                    'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
                                    'BASKET_ACTIONS_ID' => $arItemIDs['BASKET_ACTIONS'],
                                    'NOT_AVAILABLE_MESS' => $arItemIDs['NOT_AVAILABLE_MESS'],
                                    'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
                                    'SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                ),
                                'BASKET' => array(
                                    'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                    'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                    'SKU_PROPS' => $arItem['OFFERS_PROP_CODES'],
                                    'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                                    'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                                ),
                                'PRODUCT' => array(
                                    'ID' => $arItem['ID'],
                                    'NAME' => $productTitle
                                ),
                                'OFFERS' => $arItem['JS_OFFERS'],
                                'OFFER_SELECTED' => $arItem['OFFERS_SELECTED'],
                                'TREE_PROPS' => $arSkuProps,
                                'LAST_ELEMENT' => $arItem['LAST_ELEMENT']
                            );
                            if ($arParams['DISPLAY_COMPARE'])
                            {
                                $arJSParams['COMPARE'] = array(
                                    'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                                    'COMPARE_PATH' => $arParams['COMPARE_PATH']
                                );
                            }
                            ?>
                                <script type="text/javascript">
                                    //<!--
                                    var <? echo $strObName; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                                    //-->
                                </script>
                            <?
                            }
                            }
                            else
                            {
                            $arJSParams = array(
                                'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
                                'SHOW_QUANTITY' => false,
                                'SHOW_ADD_BASKET_BTN' => false,
                                'SHOW_BUY_BTN' => false,
                                'SHOW_ABSENT' => false,
                                'SHOW_SKU_PROPS' => false,
                                'SECOND_PICT' => $arItem['SECOND_PICT'],
                                'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
                                'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
                                'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                                'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                                'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                                'DEFAULT_PICTURE' => array(
                                    'PICTURE' => $arItem['PRODUCT_PREVIEW'],
                                    'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
                                ),
                                'VISUAL' => array(
                                    'ID' => $arItemIDs['ID'],
                                    'PICT_ID' => $arItemIDs['PICT'],
                                    'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
                                    'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                                    'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                                    'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                                    'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
                                    'PRICE_ID' => $arItemIDs['PRICE'],
                                    'TREE_ID' => $arItemIDs['PROP_DIV'],
                                    'TREE_ITEM_ID' => $arItemIDs['PROP'],
                                    'BUY_ID' => $arItemIDs['BUY_LINK'],
                                    'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
                                    'DSC_PERC' => $arItemIDs['DSC_PERC'],
                                    'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
                                    'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
                                    'BASKET_ACTIONS_ID' => $arItemIDs['BASKET_ACTIONS'],
                                    'NOT_AVAILABLE_MESS' => $arItemIDs['NOT_AVAILABLE_MESS'],
                                    'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
                                    'SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_LINK'],
                                ),
                                'BASKET' => array(
                                    'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                    'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                                    'SKU_PROPS' => $arItem['OFFERS_PROP_CODES'],
                                    'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                                    'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                                ),
                                'PRODUCT' => array(
                                    'ID' => $arItem['ID'],
                                    'NAME' => $productTitle
                                ),
                                'OFFERS' => array(),
                                'OFFER_SELECTED' => 0,
                                'TREE_PROPS' => array(),
                                'LAST_ELEMENT' => $arItem['LAST_ELEMENT']
                            );
                            if ($arParams['DISPLAY_COMPARE'])
                            {
                                $arJSParams['COMPARE'] = array(
                                    'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                                    'COMPARE_PATH' => $arParams['COMPARE_PATH']
                                );
                            }
                            ?>
                                <script type="text/javascript">
                                    //<!--
                                    var <? echo $strObName; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                                    //-->
                                </script>
                                <?
                            }
                            }
                            ?>                  </div>
                    </div>
                    <meta itemprop="description" class="hidden" content="<?=htmlspecialcharsbx($arItem['PREVIEW_TEXT']);?>" />
                </li>
                <?
            }
            ?>
        </ul>
    </div>
    <style type="text/css">
        #flexslider<?=$sliderID;?> .slides>li{
            max-width: <?php echo ((int)$arParams['LIST_IMAGE_WIDTH']); ?>px;
        }
    </style>
    <script type="text/javascript">
        //<!--

        $(function(){
            $('#flexslider<?=$sliderID;?>').flexslider({
                slideshow: 0,
                itemWidth: <?php echo ((int)$arParams['LIST_IMAGE_WIDTH']); ?>,
                itemMargin: 9,
                minItems: 1,
                maxItems: <?php echo (isset($arParams["LINE_ELEMENT_COUNT"]) ? (int)$arParams["LINE_ELEMENT_COUNT"] : 5) ;?>,
                slideshowSpeed:  <?php echo (isset($arParams["ROTATE_TIMER"]) ? (int)$arParams["ROTATE_TIMER"] : 3000) ;?>,
                animationSpeed: <?php echo (isset($arParams["ROTATE_TIMER"]) ? (int)$arParams["ROTATE_TIMER"] : 600) ;?>,
                directionNav: 1,
                animation: "slide",
                controlNav: false,
                prevText: "",
                nextText: ""
            });
        })

        BX.message({
            BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
            BASKET_URL: '<? echo $arParams["BASKET_URL"]; ?>',
            ADD_TO_BASKET_OK: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            TITLE_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
            TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
            TITLE_SUCCESSFUL: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
            BTN_MESSAGE_SEND_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
            BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>',
            BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
            COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK') ?>',
            COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
            COMPARE_TITLE: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE') ?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
            SITE_ID: '<? echo SITE_ID; ?>'
        });
        //-->
    </script>
    <?
}
?>