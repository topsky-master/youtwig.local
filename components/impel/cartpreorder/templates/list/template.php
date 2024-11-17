<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<noindex>
    <div class="modal bs-example-modal-sx cart-section-modal" id="modalCart" tabindex="-1" role="dialog" aria-labelledby="modalCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                    <p class="h4 modal-title">
                       <span class="catalog_one_click_pre_order">
                            <?=GetMessage('ADD_TO_BASKET_OK'); ?>
                        </span>
                    </p>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissible fade in hidden" role="alert">
                        <p id="errorResult">
                        </p>
                    </div>
                    <div class="cart-picture">
                    </div>
                    <p class="h4 text-center">
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info pull-left" data-dismiss="modal">
                        <?=GetMessage('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE'); ?>
                    </button>
                    <a href="/personal/cart/" rel="nofollow" class="btn btn-primary pull-right">
                        <?=GetMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal bs-example-modal-sx preorder-section-modal" id="modalOCBuy" tabindex="-1" role="dialog" aria-labelledby="modalOCBuyLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                    <p class="h4 modal-title">
                        <span class="catalog_one_click_pre_order pre_order">
                            <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                        </span>
                        <span class="catalog_one_click_pre_order order hidden">
                            <?php echo GetMessage('CATALOG_ONE_CLICK_ORDER'); ?>
                        </span>
                    </p>
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
                            <span class="input-group-addon">
                                *
                            </span>
                        </div>
                        <div class="form-group clearfix text-center">
                            <button id="PAY_ONE_CLICK" class="btn btn-info">
                                <span class="catalog_one_click_pre_order">
                                    <?php echo GetMessage('CATALOG_ONE_CLICK_ORDER_BUTTON'); ?>
                                </span>
                            </button>
                            <p class="consent-processing">
                                <?=html_entity_decode($arParams['CONSENT_PROCESSING_TEXT'],ENT_QUOTES,LANG_CHARSET);?>
                            </p>
                        </div>
                        <div class="hidden" id="resultOk">
                            <?=GetMessage("OC_PREORDER_ADDED");?>
                        </div>
                        <div class="hidden" id="resultpOk">
                            <?=GetMessage("OC_PREORDER_PADDED");?>
                        </div>
                        <input type="hidden" id="PRODUCT_ID" name="PRODUCT_ID" />
                        <input type="hidden" id="PREORDER_ACTION" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</noindex>
