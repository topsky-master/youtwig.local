<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

if(!function_exists('disableRusPostDevliery')){
    function disableRusPostDevliery(&$arResult){

        $aBasketItems = $arResult['BASKET_ITEMS'];

        $bDisableRusPostDel = false;

        foreach($aBasketItems as $aBasketItem){

            if(isset($aBasketItem['PRODUCT_ID'])
                && !empty($aBasketItem['PRODUCT_ID'])){

                $rbItems = CIBlockElement::GetList(
                    array(),
                    array(
                        "ID" => $aBasketItem['PRODUCT_ID']
                    ),
                    false,
                    false,
                    array(
                        "ID",
                        "IBLOCK_ID",
                        "PROPERTY_DISABLE_RUSPOST"
                    )
                );

                if ($rbItems && $aProduct = $rbItems->GetNext()){

                    if(isset($aProduct["PROPERTY_DISABLE_RUSPOST_VALUE"])
                        && !empty($aProduct["PROPERTY_DISABLE_RUSPOST_VALUE"])
                    ){

                        $bDisableRusPostDel = true;

                    }

                    if(!$bDisableRusPostDel
                        && isset($aProduct['IBLOCK_ID'])
                        && $aProduct['IBLOCK_ID'] == 16
                        && isset($aBasketItem['NAME'])
                        && !empty($aBasketItem['NAME'])
                    ){

                        $rbcItems = CIBlockElement::GetList(
                            array(),
                            array(
                                "IBLOCK_ID" => 11,
                                "NAME" => $aBasketItem['NAME']
                            ),
                            false,
                            false,
                            array(
                                "ID",
                                "IBLOCK_ID",
                                "PROPERTY_DISABLE_RUSPOST"
                            )
                        );

                        if ($rbcItems
                            && $acProduct = $rbcItems->GetNext()){

                            if(isset($acProduct["PROPERTY_DISABLE_RUSPOST_VALUE"])
                                && !empty($acProduct["PROPERTY_DISABLE_RUSPOST_VALUE"])
                            ){

                                $bDisableRusPostDel = true;

                            }


                        }

                    }


                }

            }

        }

        if($bDisableRusPostDel && !empty($arResult["DELIVERY"])){


            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
            {
                if(mb_stripos($arDelivery['NAME'],'Почта России') !== false){
                    unset($arResult["DELIVERY"][$delivery_id]);
                }

            }

        }

    }

}

disableRusPostDevliery($arResult);

global $delivery_name;

$hasErrors = false;
$delivery_name = '';
$delivery_cid = '';
$hasDostavitsa = (impelDeliveryInterval::isTodayDeliveryDay() && impelDeliveryInterval::isTodayDeliveryTime());

$is_filled = false;

if(!empty($arResult["DELIVERY"])){

    foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
    {


        if ($delivery_id !== 0 && intval($delivery_id) <= 0)
        {

            foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
            {

                if($arProfile["CHECKED"] == "Y"){
                    $is_filled = true;

                    $delivery_name = $arDelivery["NAME"].' '.$arProfile["TITLE"];
                    $delivery_cid = $profile_id;

                    break;
                }

            } // endforeach
        }
        else // stores and courier
        {

            if ($arDelivery["CHECKED"]=="Y"){

                $is_filled = true;
                $delivery_name = $arDelivery["NAME"];
                $delivery_cid = $arDelivery["ID"];

                break;
            }
        }
    }
}



if(!empty($delivery_name)){
    $params = Array(
        "max_len" => "200",
        "change_case" => "L",
        "replace_space" => "_",
        "replace_other" => "_",
        "delete_repeat_replace" => "true",
    );

    $delivery_name	= 'delivery_'.CUtil::translit($delivery_name, LANGUAGE_ID, $params).' delivery_'.$delivery_cid;
}

if(empty($delivery_name)){
    ?>
    <div class="alert alert-delivery alert-warning alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?php echo (GetMessage('SOA_TEMPL_CHOOSE_DELIVERY')); ?>
    </div>
    <?php

}

?>
<script type="text/javascript">
    function fShowStore(id, showImages, formWidth, siteId)
    {
        var strUrl = '<?=$templateFolder?>' + '/map.php';
        var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

        var storeForm = new BX.CDialog({
            'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
            head: '',
            'content_url': strUrl,
            'content_post': strUrlPost,
            'width': formWidth,
            'height':450,
            'resizable':false,
            'draggable':false
        });

        var button = [
            {
                title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
                id: 'crmOk',
                'action': function ()
                {
                    GetBuyerStore();
                    BX.WindowManager.Get().Close();
                }
            },
            BX.CDialog.btnCancel
        ];
        storeForm.ClearButtons();
        storeForm.SetButtons(button);
        storeForm.Show();
    }

    function GetBuyerStore()
    {
        BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
        //BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
        BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
        BX.show(BX('select_store'));
    }

    function showExtraParamsDialog(deliveryId)
    {
        var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
        var formName = 'extra_params_form';
        var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

        if(window.BX.SaleDeliveryExtraParams)
        {
            for(var i in window.BX.SaleDeliveryExtraParams)
            {
                strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
            }
        }

        var paramsDialog = new BX.CDialog({
            'title': '<?=GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
            head: '',
            'content_url': strUrl,
            'content_post': strUrlPost,
            'width': 500,
            'height':200,
            'resizable':true,
            'draggable':false
        });

        var button = [
            {
                title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
                id: 'saleDeliveryExtraParamsOk',
                'action': function ()
                {
                    insertParamsToForm(deliveryId, formName);
                    BX.WindowManager.Get().Close();
                }
            },
            BX.CDialog.btnCancel
        ];

        paramsDialog.ClearButtons();
        paramsDialog.SetButtons(button);
        //paramsDialog.adjustSizeEx();
        paramsDialog.Show();
    }

    function insertParamsToForm(deliveryId, paramsFormName)
    {
        var orderForm = BX("ORDER_FORM"),
            paramsForm = BX(paramsFormName);
        wrapDivId = deliveryId + "_extra_params";

        var wrapDiv = BX(wrapDivId);
        window.BX.SaleDeliveryExtraParams = {};

        if(wrapDiv)
            wrapDiv.parentNode.removeChild(wrapDiv);

        wrapDiv = BX.create('div', {props: { id: wrapDivId}});

        for(var i = paramsForm.elements.length-1; i >= 0; i--)
        {
            var input = BX.create('input', {
                    props: {
                        type: 'hidden',
                        name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
                        value: paramsForm.elements[i].value
                    }
                }
            );

            window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

            wrapDiv.appendChild(input);
        }

        orderForm.appendChild(wrapDiv);

        BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
    }
</script>
<?php global $delivery_disable; ?>
<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
<div class="row delivery order-properties">
    <div class="order_form_delivery col-xs-12 required">
        <?

        $errorSkip = $hasCalcErrors =  false;

        if(!empty($arResult["DELIVERY"]))
        {
            $width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;

            global $USER;

            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
            {



                if($delivery_id == 63 && !$hasDostavitsa)
                    continue;


                if($delivery_id                             == 41
                    || $delivery_id                             == 40
                    || $delivery_id                             == 5
                    || $delivery_id                             == 64
                    || $delivery_id                             == 68){
                    continue;
                }

                if ($delivery_id !== 0 && intval($delivery_id) <= 0)
                {
                    foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
                    {
                        ?>
                        <div <?php if($delivery_disable): ?> data:error="<?php echo GetMessage('SOA_TEMPL_ENTER_THE_CITY'); ?>"<?php endif; ?> class="bx_block w100 <?php if($delivery_disable): ?> has_order_error<?php endif;?> <?=$arProfile["CHECKED"] == "Y" && !$delivery_disable ? "has-success" : "";?> vertical">
                            <div class="bx_element">

                                <input
                                        type="radio"
                                        id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
                                        name="<?=htmlspecialcharsbx($arProfile["FIELD_NAME"])?>"
                                        value="<?=$delivery_id.":".$profile_id;?>"
                                    <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
                                        onclick="submitForm();"
                                />

                                <label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">

                                    <?
                                    /* if (count($arDelivery["LOGOTIP"]) > 0):

                                        $arFileTmp = CFile::ResizeImageGet(
                                            $arDelivery["LOGOTIP"]["ID"],
                                            array("width" => "95", "height" =>"55"),
                                            BX_RESIZE_IMAGE_PROPORTIONAL,
                                            true
                                        );

                                        $deliveryImgURL = $arFileTmp["src"];
                                    else:
                                        $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                                    endif;*/

                                    if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
                                        $extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
                                    else
                                        $extraParams = "";

                                    if(false):
                                        ?>
                                        <div class="bx_logotype" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();">
                                            <span style='background-image:url(<?=$deliveryImgURL?>);'></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="bx_description">
                                        <p class="about-delivery" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;submitForm();">
                                            <strong class="name" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();">
                                                <?=htmlspecialcharsbx($arDelivery["TITLE"])." (".htmlspecialcharsbx($arProfile["TITLE"]).")";?>:
                                            </strong>
                                            <?if (mb_strlen($arProfile["DESCRIPTION"]) > 0):?>
                                                <?=nl2br($arProfile["DESCRIPTION"])?>
                                            <?else:?>
                                                <?=nl2br($arDelivery["DESCRIPTION"])?>
                                            <?endif;?>
                                        </p>

                                        <p class="bx_result_price"><!-- click on this should not cause form submit -->
                                            <?
                                            if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0):
                                            ?>
                                            <?=GetMessage("SALE_DELIV_PRICE")?> <?=$arResult["DELIVERY_PRICE_FORMATED"]?>
                                            <span>
                                        <?
                                        if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1):
                                            echo GetMessage('SALE_PACKS_COUNT').': <b>'.$arResult["PACKS_COUNT"].'</b>';
                                        endif;

                                        else:
                                            $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                                "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                                "DELIVERY" => $delivery_id,
                                                "PROFILE" => $profile_id,
                                                "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                                "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                                "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                                "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                                "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                                "ITEMS" => $arResult["BASKET_ITEMS"],
                                                "EXTRA_PARAMS_CALLBACK" => $extraParams
                                            ), null, array('HIDE_ICONS' => 'Y'));
                                        endif;
                                        ?>
                                                <?php if($delivery_id == 33): ?>
                                                    <span id="SDEK" class="clearfix">
                                            </span>
                                                <?php endif; ?>
                                        </span>
                                        </p>
                                        <? if(isset($arDelivery['CALCULATE_ERRORS']) || (!(doubleval($arDelivery['PRICE']) > 0) && $arProfile["CHECKED"] != "Y" && $is_filled && $arResult["USER_VALS"]["DELIVERY_LOCATION"])): ?>
                                            <? $hasErrors = true; ?>
                                        <? endif; ?>
                                        <? if($arDelivery['CALCULATE_ERRORS']): ?>
                                            <? $hasCalcErrors = true; ?>
                                        <? endif; ?>

                                    </div>

                                </label>

                            </div>
                        </div>
                        <?
                    } // endforeach
                }
                else // stores and courier
                {
                    if (count($arDelivery["STORE"]) > 0)
                        $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."')\";";
                    else
                        $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
                    ?>
                    <div <?php if($delivery_disable): ?> data:error="<?php echo GetMessage('SOA_TEMPL_ENTER_THE_CITY'); ?>"<?php endif; ?> class="bx_block w100 <?php if($delivery_disable): ?> has_order_error<?php endif;?> <?=$arDelivery["CHECKED"]=="Y" && !$delivery_disable ? "has-success" : "";?> vertical">

                        <div class="bx_element">

                            <input type="radio"
                                   id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                   name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                                   value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
                                   onclick="submitForm();"
                            />

                            <label for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>" <?=$clickHandler?>>

                                <?
                                /* if (count($arDelivery["LOGOTIP"]) > 0):

                                    $arFileTmp = CFile::ResizeImageGet(
                                        $arDelivery["LOGOTIP"]["ID"],
                                        array("width" => "95", "height" =>"55"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                                    );

                                    $deliveryImgURL = $arFileTmp["src"];
                                else:
                                    $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                                endif; */

                                if(false):
                                    ?>
                                    <div class="bx_logotype"><span style='background-image:url(<?=$deliveryImgURL?>);'></span></div>
                                <?php endif; ?>
                                <div class="bx_description">
                                    <p class="about-delivery">
                                        <strong class="name">
                                            <?= htmlspecialcharsbx($arDelivery["NAME"])?>
                                        </strong>
                                        <?
                                        if (mb_strlen($arDelivery["DESCRIPTION"])>0)
                                            echo $arDelivery["DESCRIPTION"]."<br />";

                                        if (count($arDelivery["STORE"]) > 0):
                                            ?>
                                            <span id="select_store"<?if(mb_strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
                                            <span class="select_store">
                                                <?=GetMessage('SOA_ORDER_GIVE_TITLE');?>:
                                            </span>
                                            <span class="ora-store" id="store_desc">
                                                <?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?>
                                            </span>
                                        </span>
                                        <?
                                        endif;
                                        ?>
                                        <span>
                                        <?
                                        if (mb_strlen($arDelivery["PERIOD_TEXT"])>0)
                                        {
                                            echo $arDelivery["PERIOD_TEXT"];
                                        }
                                        ?>
                                        </span>
                                    </p>
                                    <p class="bx_result_price">
                                        <?=GetMessage("SALE_DELIV_PRICE");?> <?=((isset($arDelivery["DELIVERY_DISCOUNT_PRICE_FORMATED"]) && !empty($arDelivery["DELIVERY_DISCOUNT_PRICE_FORMATED"])) ? $arDelivery["DELIVERY_DISCOUNT_PRICE_FORMATED"] : $arDelivery["PRICE_FORMATED"]);?>
                                        <?php if($delivery_id == 33): ?>
                                            <span id="SDEK" class="clearfix">
                                        </span>
                                        <?php endif; ?>
                                        <?php if($delivery_id == 55): ?>
                                            <span id="pvz_boxberry"></span><span id="pvz_boxberry_desc" class="clearfix"></span>
                                        <?php endif; ?>

                                    </p>
                                    <? if(isset($arDelivery['CALCULATE_ERRORS']) || (!(doubleval($arDelivery['PRICE']) > 0) && $arDelivery["CHECKED"] != "Y" && $is_filled && $arResult["USER_VALS"]["DELIVERY_LOCATION"])): ?>
                                        <? $hasErrors = true; ?>
                                    <? endif; ?>
                                    <? if($arDelivery['CALCULATE_ERRORS']): ?>
                                        <? $hasCalcErrors = true; ?>
                                    <? endif; ?>
                                    <? if(in_array($delivery_id,array(2))): ?>
                                        <? $errorSkip = true; ?>
                                    <? endif; ?>

                                </div>

                            </label>

                            <div class="clear"></div>
                        </div>
                    </div>
                    <?
                }
            }
        }
        ?>
    </div>
</div>
<?

$errorSkip = isset($arDelivery['ID'])
    && ($delivery_cid != $arDelivery['ID'])
    ?? (($hasErrors || $hasCalcErrors) && !$errorSkip)
        ? true : $errorSkip;


if(($hasErrors || $hasCalcErrors) && !$errorSkip):
    ?>
    <div class="alert calc-errors alert-delivery alert-warning alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?php echo (GetMessage('SOA_CALCULATE_ERRORS')); ?>
        <?php if($USER->isAdmin()): ?>
            <?php echo $arDelivery['CALCULATE_ERRORS']; ?>
        <?php endif; ?>
    </div>
    <style>
        .calc-errors ~ .order-properties{
            display: none!important;
        }
    </style>
<? endif; ?>
