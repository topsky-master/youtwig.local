<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


CModule::IncludeModule("sale");
$basketCount = CSaleBasket::GetList(false, array("DELAY" => "N","FUSER_ID" => CSaleBasket::GetBasketUserID(),"LID" => SITE_ID,"ORDER_ID" => "NULL"),false,false,array("ID" ))->SelectedRowsCount();

if($basketCount && !(isset($_REQUEST['ORDER_ID']) && !empty($_REQUEST['ORDER_ID']))){
    fixBasketCount();
}

if($basketCount || (isset($_REQUEST['ORDER_ID']) && !empty($_REQUEST['ORDER_ID']))){

    if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
    {
        if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
        {
            if(mb_strlen($arResult["REDIRECT_URL"]) > 0)
            {
                $APPLICATION->RestartBuffer();
                ?>
                <script type="text/javascript">
                    window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
                </script>
                <?
                die();
            }

        }
    }


    CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
    ?>

    <a name="order_form"></a>

    <div id="order_form_div" class="order-checkout">
        <?php if($basketCount || (isset($_REQUEST['ORDER_ID']) && !empty($_REQUEST['ORDER_ID']))){ ?>
            <style>
                #empty-cart {
                    display: none;
                }
            </style>
        <?php } ?>
        <NOSCRIPT>
            <div class="errortext">
                <?=GetMessage("SOA_NO_JS")?>
            </div>
        </NOSCRIPT>

        <?
        if (!function_exists("getColumnName"))
        {
            function getColumnName($arHeader)
            {
                return (mb_strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
            }
        }

        if (!function_exists("cmpBySort"))
        {
            function cmpBySort($array1, $array2)
            {
                if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
                    return -1;

                if ($array1["SORT"] > $array2["SORT"])
                    return 1;

                if ($array1["SORT"] < $array2["SORT"])
                    return -1;

                if ($array1["SORT"] == $array2["SORT"])
                    return 0;
            }
        }
        ?>

        <div class="bx_order_make">
            <?
            if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
            {
                if(!empty($arResult["ERROR"]))
                {
                    //foreach($arResult["ERROR"] as $v)
                    //echo ShowError($v);
                }
                elseif(!empty($arResult["OK_MESSAGE"]))
                {
                    foreach($arResult["OK_MESSAGE"] as $v)
                        echo ShowNote($v);
                }

                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
            }
            else
            {
            if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
            {
                if(mb_strlen($arResult["REDIRECT_URL"]) == 0)
                {
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
                }
            }
            else
            {
            ?>
            <script type="text/javascript">

                <?if(CSaleLocation::isLocationProEnabled()):?>

                <?
                // spike: for children of cities we place this prompt
                $city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
                ?>

                BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
                    'source' => $this->__component->getPath().'/get.php',
                    'cityTypeId' => intval($city['ID']),
                    'messages' => array(
                        'otherLocation' => '--- '.GetMessage('SOA_OTHER_LOCATION'),
                        'moreInfoLocation' => '--- '.GetMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                        'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
                                '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                                '#ANCHOR_END#' => '</a>'
                            )).'</div>'
                    )
                ))?>);

                <?endif?>

                function getQueryParams(rurl){

                    rurl = rurl.replace(/.*\?/,'');
                    qs = rurl.replace(/#.*?$/,'');

                    var qd = {};
                    if (qs) qs.mb_split("&").forEach(function(item) {var s = item.mb_split("="), k = s[0], v = s[1] && decodeURIComponent(s[1]); (qd[k] = qd[k] || []).push(v)})

                    return qd;
                };

                var BXFormPosting = false;
                function submitForm(val)
                {

                    if(BXFormPosting === true){
                        $('#ORDER_CONFIRM_BUTTON').parents('.bottom_background').removeClass('disabled');
                        return true;
                    }


                    if(val == 'Y' && jQuery('.calc-errors','#ORDER_FORM').get(0)){
                        $('#ORDER_CONFIRM_BUTTON').parents('.bottom_background').addClass('disabled');
                        return false;
                    }

                    if(hideOptionsDelivery.length){

                        for(var i = 0; i < hideOptionsDelivery.length; i++){

                            var cOptions = hideOptionsDelivery[i].mb_split('.');

                            for(var c = 0; c < cOptions.length; c++){
                                if(cOptions[c] != "" && cOptions[c].indexOf("ORDER_PROP") === 0){
                                    $('input[type="text"],textarea','.' + cOptions[c]).each(function(){
                                        if($.trim($(this).val()) == "По умолчанию"){
                                            $(this).val("");
                                        };
                                    });
                                };
                            };
                        };

                        for(var i = 0; i < hideOptionsDelivery.length; i++){

                            $('select',hideOptionsDelivery[i]).each(function(){
                                if(this.selectedIndex === -1){
                                    this.selectedIndex = 0;
                                }
                            });


                            $('input[type="text"],textarea',hideOptionsDelivery[i]).each(function(){

                                if($.trim($(this).val()) == ""){
                                    $(this).val("По умолчанию");
                                };



                            });


                        }


                    }

                    BXFormPosting = true;
                    if(val != 'Y')
                        BX('confirmorder').value = 'N';

                    var orderForm = BX('ORDER_FORM');
                    BX.showWait();

                    <?if(CSaleLocation::isLocationProEnabled()):?>
                    BX.saleOrderAjax.cleanUp();
                    <?endif?>

                    var pCity = $('.bx-ui-sls-fake').prop('title') ? $('.bx-ui-sls-fake').prop('title') : $('.bx-ui-sls-fake').attr('title');
                    $.cookie('OLD_CITY',pCity);

                    BX.ajax.submit(orderForm, ajaxResult);

                    return true;
                }

                function ajaxResult(res)
                {
                    var orderForm = BX('ORDER_FORM');
                    try
                    {
                        // if json came, it obviously a successfull order submit

                        var json = JSON.parse(res);
                        BX.closeWait();

                        if (json.error)
                        {
                            BXFormPosting = false;
                            return;
                        }
                        else if (json.redirect)
                        {
                            if(typeof json.success != "undefined"
                                && json.success == 'Y'){

                                qd = getQueryParams(json.redirect);

                                if(typeof qd.ORDER_ID != "undefined"
                                    && qd.ORDER_ID){

                                    var dataLayerLink = location.protocol + '//' + location.hostname + '/ajax_cart/dataLayerOrder.php?ORDER_ID=' + qd.ORDER_ID;

                                    jQuery.getJSON(dataLayerLink, function(dataLayerData) {

                                        if (dataLayerData
                                            && typeof dataLayerData.success != "undefined"
                                            && dataLayerData.success == true
                                        ) {

                                            window.dataLayer = window.dataLayer || [];

                                            try {
                                                yaCounter21503785.reachGoal('oformit-zakaz');
                                            } catch (e) {

                                            }
                                            
                                            var actionField = {id: dataLayerData.actionField[0].id, goal_id: 'cartorder'};
                                        
                                            dataLayer.push({
                                                "ecommerce": {
                                                    //"currencyCode": dataLayerData.currency,
                                                    "purchase": {
                                                        "actionField": actionField,
                                                        "products": dataLayerData.products
                                                    }
                                                }
                                            });

                                        };

                                    });

                                };

                            };

                            jQuery.get(json.redirect, function (data, textStatus, jqXHR) {
                                var sHTML = $.parseHTML(data);

                                if ($('#payment-wrapper form', sHTML).get(0)) {
                                    var sForm = $('#payment-wrapper form', sHTML).get(0).outerHTML;
                                    sForm = $(sForm);
                                    sForm.addClass('hidden');
                                    $('body').append(sForm);
                                    history.pushState(null, 'Ваш заказ ' + qd.ORDER_ID + ' успешно создан', json.redirect)
                                    $(sForm).trigger('submit');

                                } else {

                                    window.top.location.href = json.redirect;

                                }
                            });
                        }
                    }
                    catch (e)
                    {
                        // json parse failed, so it is a simple chunk of html

                        BXFormPosting = false;
                        BX('order_form_content').innerHTML = res;

                        <?if(CSaleLocation::isLocationProEnabled()):?>
                        BX.saleOrderAjax.initDeferredControl();
                        <?endif?>

                    }

                    BX.closeWait();
                    BX.onCustomEvent(orderForm, 'onAjaxSuccess');

                    if(jQuery('.calc-errors','#ORDER_FORM').get(0)){
                        $('#ORDER_CONFIRM_BUTTON').parents('.bottom_background').addClass('disabled');
                    } else {
                        $('#ORDER_CONFIRM_BUTTON').parents('.bottom_background').removeClass('disabled');
                    }

                }

                function SetContact(profileId)
                {
                    BX("profile_change").value = "Y";
                    submitForm();
                }
            </script>
            <?if($_POST["is_ajax_post"] != "Y")
            {
            ?><form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
                <?=bitrix_sessid_post()?>
                <div id="order_form_content">
                    <?
                    }
                    else
                    {
                        $APPLICATION->RestartBuffer();
                    }

                    $session = \Bitrix\Main\Application::getInstance()->getSession();

                    if(isset($session['order_error'])
                        && !empty($session['order_error'])
                        && $strError = $session['order_error']){

                        $session->set('order_error', NULL);
                        ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert" id="orderError">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">
									×
								</span>
                            </button>
                            <?=$strError;?>
                        </div>
                        <?
                    };

                    if($_REQUEST['PERMANENT_MODE_STEPS'] == 1)
                    {
                        ?>
                        <input type="hidden" name="PERMANENT_MODE_STEPS" value="1" />
                        <?
                    }

                    if(isset($arResult["ERROR"]))
                    {
                        foreach($arResult["ERROR"] as $aError):
                            foreach($aError as $strError):
                                ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert" id="orderError">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">
									×
								</span>
                                    </button>
                                    <?=$strError;?>
                                </div>
                            <?php
                            endforeach;
                        endforeach;
                    }

                    global $USER;

                    if(isset($arResult["ERROR_SORTED"]) && $USER->isAdmin())
                    {
                        foreach($arResult["ERROR_SORTED"] as $aError):
                            foreach($aError as $strError):
                                ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert" id="orderError">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">
									×
								</span>
                                    </button>
                                    <?=$strError;?>
                                </div>
                            <?php
                            endforeach;
                        endforeach;
                    }

                    if(isset($arResult["WARNING"]) && $USER->isAdmin()) {
                        foreach($arResult["WARNING"] as $aError):
                            foreach($aError as $strError):
                                ?>
                                <div class="alert alert-warning alert-dismissible fade in" role="alert" id="orderError">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">
									×
								</span>
                                    </button>
                                    <?=$strError;?>
                                </div>
                            <?php
                            endforeach;
                        endforeach;

                    }

                    if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
                    {

                        //foreach($arResult["ERROR"] as $v)
                        //echo ShowError($v);
                        ?>
                        <script type="text/javascript">
                            //top.BX.scrollToNode(top.BX('ORDER_FORM'));
                        </script>
                        <?
                    }

                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
                    $counter								= 0;
                    ?>
                    <div class="top_background clearfix">
                        <?php

                        $relatedProperties						= array();


                        if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d"){


                            ob_start();
                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
                            $relatedProperties['paysystem']		= ob_get_clean();

                            ob_start();
                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
                            $relatedProperties['delivery']		= ob_get_clean();

                            ob_start();
                            ?>
                            <div class="order-properties <?=$delivery_name;?> <?=$paysystem_name;?> ORDER_PROP_COMMENTS row">
                                <div class="form-group <?php if($arResult["USER_VALS"]["ORDER_DESCRIPTION"] != ""): ?>has-success<?php endif; ?> col-xs-12 col-sm-12 col-lg-10 col-md-12">
                                    <div class="cart-fields" data-property-id-row="comments">
                                        <label for="ORDER_DESCRIPTION" class="col-lg-3 col-xs-12">
                                            <?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?>
                                        </label>
                                        <div class="col-lg-9 col-xs-12">
                                            <textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" class="form-control"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
                                            <input type="hidden" name="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            $relatedProperties['comments']		= ob_get_clean();

                        } else {

                            ob_start();
                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
                            $relatedProperties['delivery']		= ob_get_clean();

                            ob_start();
                            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
                            $relatedProperties['paysystem']		= ob_get_clean();

                            ob_start();
                            ?>
                            <div class="order-properties <?=$delivery_name;?> <?=$paysystem_name;?>  ORDER_PROP_COMMENTS row">
                                <div class="form-group <?php if($arResult["USER_VALS"]["ORDER_DESCRIPTION"] != ""): ?>has-success<?php endif; ?> col-xs-12 col-sm-12 col-lg-10 col-md-12">
                                    <div class="cart-fields" data-property-id-row="comments">
                                        <label for="ORDER_DESCRIPTION" class="col-lg-3 col-xs-4 col-sm-12">
                                            <?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?>
                                        </label>
                                        <div class="col-lg-9 col-xs-8 col-sm-12">
                                            <textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" class="form-control"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
                                            <input type="hidden" name="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            $relatedProperties['comments']		= ob_get_clean();

                        };

                        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php");
                        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
                        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/related_props.php");


                        if(mb_strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
                            echo $arResult["PREPAY_ADIT_FIELDS"];

                        ?>

                        <?php
                        if($counter % 3 == 0):
                            ?>
                        <?php endif; ?>

                    </div>
                    <?php

                    global $USER, $APPLICATION;

                    $CUser								= array();
                    $set_sms_state						= false;
                    if($USER->GetID()){


                        $Usr 								= $USER->GetByID($USER->GetID());
                        $Currencies 						= Array();
                        $CUser 								= $Usr->Fetch();

                    } else {


                        $CUser['UF_SMS_INFORM']			= $APPLICATION->get_cookie('SMS_INFO');

                        $set_sms_state					= (empty($CUser['UF_SMS_INFORM']) && $CUser['UF_SMS_INFORM'] !== 0) ? true : false;
                        $CUser['UF_SMS_INFORM']			= $CUser['UF_SMS_INFORM'] != "" ? $CUser['UF_SMS_INFORM'] : 1;
                    }

                    ?>
                    <div class="bottom_background clearfix">
                        <div class="clear clearfix"></div>
                        <div class="SMS_INFO checkbox">
                            <label>
                                <input type="checkbox" name="SMS_INFO" id="SMS_INFO" checked="checked" />
                                <span class="checkbox-style"></span>
                                <?php echo GetMessage('SOA_SMS_INFO'); ?>
                            </label>
                        </div>
                    </div>
                    <script type="text/javascript">
                        //<!--
                        <?php if($set_sms_state) {?>
                        BX.ready(function(){
                            try{
                                jsAjaxUtil.PostData(location.protocol + '//' + location.hostname + '/ajax_cart/change_user_state.php', {SMS_INFO: ($('#SMS_INFO')[0].checked ? 1 : 0)},
                                    function(data)
                                    {
                                    }
                                );
                            } catch(e){

                            }
                        });
                        <?php }; ?>
                        //-->
                    </script>
                    <div class="row itogo">
                        <div class="col-xs-12">
                        <span class="summ-label">
                            <label>
                                <?=GetMessage("SOA_TEMPL_SUM_IT")?>
                            </label>
                        </span>
                            <span class="summ summ-value">
                            <strong>
                                <? echo CurrencyFormat($AllSum, $AllSumCur); ?>
                            </strong>
                        </span>
                        </div>
                    </div>
                </div>
                <?if($_POST["is_ajax_post"] != "Y")
                {
                ?>
        </div>
    <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
    <input type="hidden" name="profile_change" id="profile_change" value="N">
    <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
    <input type="hidden" name="json" value="Y">
        <div class="bottom_background clearfix">
            <div class="bx_ordercart_order_pay_center text-center">
                <button onclick="submitForm('Y');return false;" id="ORDER_CONFIRM_BUTTON" class="iblock_submit_top btn btn-info">
                    <?=GetMessage("SOA_TEMPL_BUTTON")?>
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
        </div>
        </form>
    <?
    if($arParams["DELIVERY_NO_AJAX"] == "N")
    {
    ?>
        <div class="hidden"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
    <?
    }
    }
    else
    {
    ?>
        <script type="text/javascript">
            top.BX('confirmorder').value = 'Y';
            top.BX('profile_change').value = 'N';
        </script>
    <?
    die();
    }
    }
    }
    ?>
    </div>
    </div>

    <?if(CSaleLocation::isLocationProEnabled()):?>

        <div class="hidden">
            <?// we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.steps",
                ".default",
                array(
                ),
                false
            );?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.search",
                ".default",
                array(
                ),
                false
            );?>
        </div>

    <?endif?>
    <?

} else { ?>
    <div class="alert alert-danger empty-cart" id="empty-cart" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        <?php echo GetMessage('SOA_TEMPL_EMPTY_CART'); ?>
    </div>
<?php }

if($_POST["is_ajax_post"] != "Y"){
    $APPLICATION->AddHeadScript($templateFolder."/js/jquery.maskedinput.min.js");

    $set_default = array();

    if(isset($arParams['SET_DEFAULT']) && !empty($arParams['SET_DEFAULT'])):

        $set_default = $arParams['SET_DEFAULT'];

        if(mb_stripos($set_default,',') !== false){
            $set_default = explode(',',$set_default);
        } else {
            $set_default = array($set_default);
        };

    endif;

    ?>
    <? if(!empty($set_default)): ?>
        <style>
            <? foreach($set_default as $number => $style): ?>
            <?=$style;?><? if(sizeof($set_default) - 1 != $number): ?>,<? endif; ?>
            <? endforeach;?>{
                display: none!important;
            }
        </style>
    <? endif; ?>
    <script type="text/javascript">
        //<!--
        msgErrorPhoneFormat = '<?=GetMessage('SOA_INVALID_PHONE');?>';
        hideOptionsDelivery = <?=json_encode($set_default);?>;

        jQuery(function(){
            if(jQuery('.calc-errors','#ORDER_FORM').get(0)){
                $('#ORDER_CONFIRM_BUTTON').parents('.bottom_background').addClass('disabled');
            }
        });

        //-->
    </script>
    <?
}

?>
<? if($USER && !$USER->IsAuthorized()) { ?>
    <div id="passcodeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p class="modal-title h4"><?=GetMessage('CT_BNL_ELEMENT_PASS_SUBMIT');?></p>
                </div>
                <div class="modal-body">
                    <?$APPLICATION->IncludeComponent(
                        "impel:smscode",
                        "",
                        Array(
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A"
                        )
                    );?>
                </div>
            </div>
        </div>
    </div>
<? } ?>
<div id="checkPhoneModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p class="modal-title h4"><?=GetMessage('TMPL_CHECK_PHONE');?></p>
            </div>
            <div class="modal-body">
                <div class="form-inline ORDER_PROP_3">
                    <div class="form-group">
                        <input type="text" class="form-control" id="checkphone" />
                        <button type="submit" class="btn btn-default"><?=GetMessage('TMPL_CHECK_PHONE_BTN');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>