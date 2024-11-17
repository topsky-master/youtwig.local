<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];


$arResult["GRID"]["HEADERS"] = array (
    0 =>
        array (
            'id' => 'NAME',
            'name' => GetMessage('SOA_NAME'),
        ),
    1 =>
        array (
            'id' => 'QUANTITY',
            'name' => GetMessage('SOA_COUNT'),
        ),
    2 =>
        array (
            'id' => 'SUM',
            'name' => GetMessage('SOA_PRICE'),
        ),
    3 =>
        array (
            'id' => 'ACTIONS',
            'name' => '',
        ),
);

$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;

$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = true; // flat to show name and picture column in one column
$currency	= getCurrentCurrencyCode();
$AllSum		= 0;
$AllSumCur	= $currency;


?>
<div class="ordercart">
    <p class="h4 h4-order-title">
        <?=GetMessage("SALE_PRODUCTS_SUMMARY");?>
    </p>
    <div>
        <table class="table table-order">
            <thead>
            <tr>
                <?
                $bPreviewPicture = false;
                $bDetailPicture = false;
                $imgCount = 0;

                // prelimenary column handling

                foreach ($arResult["GRID"]["HEADERS"] as $id => $arColumn)
                {

                    if ($arColumn["id"] == "PROPS")
                        $bPropsColumn = true;

                    if ($arColumn["id"] == "NOTES")
                        $bPriceType = true;

                    if ($arColumn["id"] == "PREVIEW_PICTURE")
                        $bPreviewPicture = true;

                    if ($arColumn["id"] == "DETAIL_PICTURE")
                        $bDetailPicture = true;
                }

                if ($bPreviewPicture || $bDetailPicture)
                    $bShowNameWithPicture = true;


                foreach ($arResult["GRID"]["HEADERS"] as $id => $arColumn):


                    if (in_array($arColumn["id"], array("TYPE", "NOTES"))) // some values are not shown in columns in this template
                        continue;

                    if ($arColumn["id"] == "PREVIEW_PICTURE" && $bShowNameWithPicture)
                        continue;

                    if ($arColumn["id"] == "NAME" && $bShowNameWithPicture):
                        ?>
                        <th class="item" colspan="2">
                        <?
                        echo GetMessage("SALE_PRODUCTS");
                    elseif ($arColumn["id"] == "NAME" && !$bShowNameWithPicture):
                        ?>
                        <th class="item">
                        <?
                        echo $arColumn["name"];
                    elseif ($arColumn["id"] == "PRICE"):
                        ?>
                        <th class="price">
                        <?
                        echo $arColumn["name"];
                    else:
                        ?>
                        <th class="custom <?php echo mb_strtolower($arColumn["id"]);?>">
                        <?
                        echo $arColumn["name"];
                    endif;
                    ?>
                    </th>
                <?endforeach;?>

            </tr>
            </thead>
            <tbody>
            <?foreach ($arResult["GRID"]["ROWS"] as $k => $arData):?>
                <tr>
                    <?

                    if ($bShowNameWithPicture):
                        ?>
                        <td class="itemphoto">
                            <?

                            if (($arData["data"]["PREVIEW_PICTURE"]) > 0):
                                $url = CFile::getPath($arData["data"]["PREVIEW_PICTURE"]);
                            elseif (($arData["data"]["DETAIL_PICTURE"]) > 0):
                                $url = CFile::getPath($arData["data"]["DETAIL_PICTURE"]);
                            else:
                                $url = $templateFolder."/images/no_photo.png";
                            endif;

                            $url = rectangleImage($_SERVER['DOCUMENT_ROOT'].'/'.$url,132,132,$url);

                            if (mb_strlen($arData["data"]["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arData["data"]["DETAIL_PAGE_URL"] ?>"><?endif;?>
                                <img src="<?=$url?>" class="img-thumbnail" />
                                <?if (mb_strlen($arData["data"]["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
                            <?
                            if (!empty($arData["data"]["BRAND"])):
                                ?>
                                <div class="bx_ordercart_brand">
                                    <img src="<?=$arData["data"]["BRAND"]?>" />
                                </div>
                            <?
                            endif;
                            ?>
                        </td>
                    <?
                    endif;

                    // prelimenary check for images to count column width
                    foreach ($arResult["GRID"]["HEADERS"] as $id => $arColumn)
                    {
                        $arItem = (isset($arData["columns"][$arColumn["id"]])) ? $arData["columns"] : $arData["data"];

                        if (is_array($arItem[$arColumn["id"]]))
                        {
                            foreach ($arItem[$arColumn["id"]] as $arValues)
                            {
                                if ($arValues["type"] == "image")
                                    $imgCount++;
                            }
                        }
                    }

                    foreach ($arResult["GRID"]["HEADERS"] as $id => $arColumn):

                        $class = ($arColumn["id"] == "PRICE_FORMATED") ? "price" : "";

                        if (in_array($arColumn["id"], array("TYPE", "NOTES"))) // some values are not shown in columns in this template
                            continue;

                        if ($arColumn["id"] == "PREVIEW_PICTURE" && $bShowNameWithPicture)
                            continue;

                        $arItem = (isset($arData["columns"][$arColumn["id"]])) ? $arData["columns"] : $arData["data"];

                        if ($arColumn["id"] == "NAME"):

                            $articul = '';

                            $articulDb = CIBlockElement::GetList(
                                ($arOrder = Array("SORT" => "ASC")),
                                ($arFilter = Array(
                                    "NAME" => $arItem["NAME"],
                                    'IBLOCK_ID' => 11
                                )
                                ),
                                false,
                                false,
                                array('PROPERTY_ARTNUMBER')
                            );

                            if($articulDb
                                && $articulAr = $articulDb->Fetch()){

                                $articul = $articulAr['PROPERTY_ARTNUMBER_VALUE'];
                            }


                            $width = 70 - ($imgCount * 20);
                            ?>
                            <td class="item">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <?if (mb_strlen($arItem["DETAIL_PAGE_URL"]) > 0):?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"] ?>">
                                    <?endif;?>
                                    <?=$arItem["NAME"]?>
                                    <? if(!empty($articul)): ?>
                                        <span class="articul">
                                        <?=GetMessage('SOA_TEMPL_ARTICUL');?>
                                            <i><?=$articul;?></i>
                                    </span>
                                    <? endif; ?>
                                    <?if (mb_strlen($arItem["DETAIL_PAGE_URL"]) > 0):?>
                                </a>
                            <?endif;?>


                                <?
                                if (is_array($arItem["SKU_DATA"])):
                                    foreach ($arItem["SKU_DATA"] as $propId => $arProp):

                                        // is image property
                                        $isImgProperty = false;
                                        foreach ($arProp["VALUES"] as $id => $arVal)
                                        {
                                            if (isset($arVal["PICT"]) && !empty($arVal["PICT"]))
                                            {
                                                $isImgProperty = true;
                                                break;
                                            }
                                        }

                                        $full = (count($arProp["VALUES"]) > 5) ? "full" : "";

                                        if ($isImgProperty): // iblock element relation property
                                            ?>
                                            <div class="bx_item_detail_scu_small_noadaptive <?=$full?>">

                                                <span class="bx_item_section_name_gray">
                                                    <?=$arProp["NAME"]?>:
                                                </span>

                                                <div class="bx_scu_scroller_container">

                                                    <div class="bx_scu">
                                                        <ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>" style="width: 200%;margin-left:0%;">
                                                            <?
                                                            foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

                                                                $selected = "";
                                                                foreach ($arItem["PROPS"] as $arItemProp):
                                                                    if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
                                                                    {
                                                                        if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
                                                                            $selected = "class=\"bx_active\"";
                                                                    }
                                                                endforeach;
                                                                ?>
                                                                <li style="width:10%;" <?=$selected?>>
                                                                    <a href="javascript:void(0);">
                                                                        <span style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>)"></span>
                                                                    </a>
                                                                </li>
                                                            <?
                                                            endforeach;
                                                            ?>
                                                        </ul>
                                                    </div>

                                                    <div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
                                                    <div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
                                                </div>

                                            </div>
                                        <?
                                        else:
                                            ?>
                                            <div class="bx_item_detail_size_small_noadaptive <?=$full?>">

                                                <span class="bx_item_section_name_gray">
                                                    <?=$arProp["NAME"]?>:
                                                </span>

                                                <div class="bx_size_scroller_container">
                                                    <div class="bx_size">
                                                        <ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>" style="width: 200%; margin-left:0%;">
                                                            <?
                                                            foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

                                                                $selected = "";
                                                                foreach ($arItem["PROPS"] as $arItemProp):
                                                                    if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
                                                                    {
                                                                        if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
                                                                            $selected = "class=\"bx_active\"";
                                                                    }
                                                                endforeach;
                                                                ?>
                                                                <li style="width:10%;" <?=$selected?>>
                                                                    <a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
                                                                </li>
                                                            <?
                                                            endforeach;
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
                                                    <div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
                                                </div>

                                            </div>
                                        <?
                                        endif;
                                    endforeach;
                                endif;
                                ?>
                            </td>
                        <?
                        elseif ($arColumn["id"] == "PRICE_FORMATED"):
                            ?>
                            <td class="price right">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <?php

                                if($arItem["CURRENCY"] 			!= $currency){
                                    $arItem["PRICE"] 			= CCurrencyRates::ConvertCurrency($arItem["PRICE"],$arItem["CURRENCY"],$currency,'',$arItem["PRODUCT_ID"]);
                                    $arItem["DISCOUNT_PRICE"]	= CCurrencyRates::ConvertCurrency($arItem["DISCOUNT_PRICE"],$arItem["CURRENCY"],$currency,'',$arItem["PRODUCT_ID"]);
                                };

                                $arItem["PRICE"]	    		= $arItem["PRICE"];
                                ?>
                                <div class="current_price"><? echo CurrencyFormat($arItem["PRICE"], $currency); ?></div>
                                <div class="old_price right">
                                    <?
                                    if (doubleval($arItem["DISCOUNT_PRICE"]) > 0):
                                        echo SaleFormatCurrency($arItem["PRICE"] + $arItem["DISCOUNT_PRICE"], $currency);
                                        $bUseDiscount 			= true;
                                    endif;
                                    ?>
                                </div>

                                <?if ($bPriceType && mb_strlen($arItem["NOTES"]) > 0):?>
                                    <div style="text-align: left">
                                        <div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
                                        <div class="type_price_value"><?=$arItem["NOTES"]?></div>
                                    </div>
                                <?endif;?>
                            </td>
                        <?
                        elseif ($arColumn["id"] == "DISCOUNT"):
                            ?>
                            <td class="custom right <?php echo mb_strtolower($arColumn["id"]);?>">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <span><?=getColumnName($arColumn)?>:</span>
                                <?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
                            </td>
                        <?
                        elseif ($arColumn["id"] == "DETAIL_PICTURE" && $bPreviewPicture):
                            ?>
                            <td class="itemphoto">
                                <?
                                $url = "";
                                if ($arColumn["id"] == "DETAIL_PICTURE" && ($arData["data"]["DETAIL_PICTURE"]) > 0)
                                    $url = CFile::GetPath($arData["data"]["DETAIL_PICTURE"]);

                                if ($url == "")
                                    $url = $templateFolder."/images/no_photo.png";

                                $url = rectangleImage($_SERVER['DOCUMENT_ROOT'].'/'.$url,132,132,$url);

                                if (mb_strlen($arData["data"]["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arData["data"]["DETAIL_PAGE_URL"] ?>"><?endif;?>
                                    <img src="<?=$url?>')" class="img-thumbnail" />
                                    <?if (mb_strlen($arData["data"]["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
                            </td>
                        <?
                        elseif ($arColumn["id"] == "PROPS"):
                            ?>
                            <td class="custom props">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                <? echo $arColumn["name"]; ?>
                            </span>
                                <? endif; ?>
                                <div class="bx_ordercart_itemart">
                                    <?
                                    if ($bPropsColumn):
                                        foreach ($arItem["PROPS"] as $val):
                                            echo '<p>'.$val["NAME"].":&nbsp;<span>".$val["VALUE"]."<span></p>";
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </td>
                        <?
                        elseif ($arColumn["id"] == "SUM"):
                            ?>
                            <td class="custom right <?php echo mb_strtolower($arColumn["id"]); ?>">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <?php

                                if($arItem["CURRENCY"] 			!= $currency){
                                    $arItem["PRICE"] 			= CCurrencyRates::ConvertCurrency($arItem["PRICE"],$arItem["CURRENCY"],$currency,'',$arItem["PRODUCT_ID"]);
                                };

                                $arItem["PRICE"]	    		= $arItem["PRICE"]*$arItem["QUANTITY"];
                                $AllSum 				   		+= $arItem["PRICE"];

                                echo SaleFormatCurrency($arItem["PRICE"], $currency);

                                ?>
                            </td>
                        <?php

                        elseif ($arColumn["id"] == "ACTIONS"):
                            ?>
                            <td class="custom text-center <?php echo mb_strtolower($arColumn["id"]); ?>">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <a href="<?=$templateFolder;?>/sale_order_ajax.php?action=delete&product_id[<?php echo $arData['data']['ID']; ?>]=1" class="remove">
                                    <i class="fa fa-close"></i>
                                </a>
                            </td>
                        <?php

                        elseif (in_array($arColumn["id"], array("QUANTITY", "WEIGHT_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED"))):
                            ?>
                            <td class="custom right <?php echo mb_strtolower($arColumn["id"]); ?>">
                                <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                    <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                <? endif; ?>
                                <?php if($arColumn["id"] != "QUANTITY"): ?>
                                    <?=$arItem[$arColumn["id"]]?>
                                <?php else:

                                    $product_id                                 = $arData['data']['PRODUCT_ID'];

                                    $max_quantity                               = false;
                                    $buy_id		                                = getBondsProduct($product_id);
                                    $check_quantity                             = false;

                                    $rsProducts                                 = CCatalogProduct::GetList(
                                        array(),
                                        array('ID' => $buy_id),
                                        false,
                                        false,
                                        array(
                                            'ID',
                                            'CAN_BUY_ZERO',
                                            'QUANTITY_TRACE',
                                            'QUANTITY'
                                        )
                                    );

                                    if ($rsProducts && $arCatalogProduct        = $rsProducts->Fetch()){

                                        $check_quantity                         = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');
                                        if($check_quantity){
                                            $max_quantity                       = get_quantity_product($product_id);
                                        }

                                    }



                                    ?>
                                    <input type="text" id="product_<?php echo $arData['data']['PRODUCT_ID']; ?>" data:id="<?php echo $arData['data']['ID']; ?>" class="order_quantity" value="<?php echo preg_replace('~[^0-9]+~','',($max_quantity && $max_quantity < $arItem["QUANTITY"] ? $max_quantity : $arItem["QUANTITY"])); ?>" min="1"<? if($max_quantity): ?> max="<?=$max_quantity;?>"<? endif; ?> />
                                <?php endif; ?>
                            </td>
                        <?
                        else: // some property value

                            if (is_array($arItem[$arColumn["id"]])):

                                foreach ($arItem[$arColumn["id"]] as $arValues)
                                    if ($arValues["type"] == "image")
                                        $columnStyle = "width:20%";
                                ?>
                                <td class="custom <?php echo $arColumn["id"];?>">
                                    <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                        <span class="hidden-lg hidden-md column-title">
                                            <? echo $arColumn["name"]; ?>
                                        </span>
                                    <? endif; ?>
                                    <span><?=getColumnName($arColumn)?>:</span>
                                    <?
                                    foreach ($arItem[$arColumn["id"]] as $arValues):
                                        if ($arValues["type"] == "image"):
                                            ?>
                                            <img src="<?=$arValues["value"]?>" class="img-thumbnail" />
                                        <?
                                        else: // not image
                                            echo $arValues["value"]."<br/>";
                                        endif;
                                    endforeach;
                                    ?>
                                </td>
                            <?
                            else: // not array, but simple value
                                ?>
                                <td class="custom <?php echo $arColumn["id"];?>">
                                    <? if(isset($arColumn["name"]) && !empty($arColumn["name"])): ?>
                                        <span class="hidden-lg hidden-md column-title">
                                    <? echo $arColumn["name"]; ?>
                                </span>
                                    <? endif; ?>
                                    <span><?=getColumnName($arColumn)?>:</span>
                                    <?
                                    echo $arItem[$arColumn["id"]];
                                    ?>
                                </td>
                            <?
                            endif;
                        endif;

                    endforeach;
                    ?>
                </tr>
            <?endforeach;?>
            </tbody>
            <tfoot>
            <tr class="hidden-xs">
                <td colspan="2" rowspan="3" class="coupon-list">
                    <div class="form-inline">
                        <div class="form-group">

                            <input type="text" class="form-control" name="coupon" id="coupon" placeholder="<?php echo GetMessage("SOA_TEMPL_PROMO");  ?>">
                        </div>
                        <input type="button" class="btn btn-default" id="recount" value="<?php echo GetMessage("SOA_TEMPL_RECOUNT");  ?>" />
                        <span id="coupon-result">
                            <?php

                            $coupons = '';

                            $arCoupons = Bitrix\Sale\DiscountCouponsManager::get(true, array(), true, true);
                            if (!empty($arCoupons)) {
                                foreach ($arCoupons as &$oneCoupon) {

                                    if(isset($oneCoupon['COUPON'])
                                        && !empty($oneCoupon['COUPON'])){

                                        $coupons .= (!empty($coupons) ? ', ' : '') . $oneCoupon['COUPON'];

                                    };

                                }
                            }

                            if(!empty($coupons)){
                                echo $coupons.'<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
                            }

                            ?>
                        </span>
                    </div>
                </td>
            </tr>
            <?

            if (doubleval($arResult["DELIVERY_PRICE"]) > 0)
            {
                $AllSum		= $AllSum + $arResult["DELIVERY_PRICE"];
                ?>
                <tr class="hidden-xs">
                    <td colspan="<?=$colspan - 2;?>" class="itog custom_t1"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>
                    <td colspan="2" class="price custom_t2"><?=$arResult["DELIVERY_PRICE_FORMATED"]?></td>
                </tr>
                <?
            }
            ?>
            <tr>
                <td colspan="<?=$colspan - 2;?>" class="custom_t1 fwb itog"><div class="label-summ"><?=GetMessage("SOA_TEMPL_SUM_IT")?></div></td>
                <td colspan="2" class="custom_t2 fwb price"><div class="value-summ"><? echo CurrencyFormat($AllSum, $AllSumCur); ?></div></td>
            </tr>
            <?
            ?>
            </tfoot>
        </table>
    </div>
    <div class="clear clearfix"></div>
</div>
