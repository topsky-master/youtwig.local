<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */

?>
<div class="product-detail">
    <?php
    if(!empty($morePhoto)){
        ?>
        <div class="product-image">
            <?php $image = current($morePhoto);
			$image["SRC"] = rectangleImage($_SERVER['DOCUMENT_ROOT'].$image["SRC"],255,255,$image["SRC"],"",true,false);
			?>
            <a href="<?=$item['DETAIL_PAGE_URL']?>">
                <amp-img itemprop="image" alt="<?=htmlspecialchars($imgTitle,ENT_QUOTES,LANG_CHARSET);?>" src="<?=$image["SRC"];?>" width="<?=$image["WIDTH"];?>" height="<?=$image["WIDTH"];?>" layout="responsive"<?=$image["srcset"];?>>
                    <noscript>
                        <img src="<?=$image["SRC"];?>" width="<?=$image["WIDTH"];?>" height="<?=$image["WIDTH"];?>" alt="<?=htmlspecialchars($imgTitle,ENT_QUOTES,LANG_CHARSET);?>" />
                    </noscript>
                </amp-img>
            </a>
            <?php ?>
        </div>
    <?php } ?>
    <h2>
        <a href="<?=$item['DETAIL_PAGE_URL']?>">
            <?=$item['NAME'];?>
        </a>
    </h2>
    <div class="item_price">
        <? if (!empty($price)){ ?>
            <div class="item-price">
                <? echo $price['PRINT_RATIO_PRICE']; ?>
            </div>
        <? } ?>
        <? if($item['CAN_BUY']){?>
            <a <?php if($arParams['HAS_READMORE'] == 'Y'): ?> href="<?=$item['DETAIL_PAGE_URL'];?>"<?php else: ?> rel="nofollow" href="<?=$APPLICATION->GetCurPage();?>?action=add2basketamp&<?php if(isset($item["BUY_ID"]) && !empty($item["BUY_ID"])){ ?>id=<?=$item["BUY_ID"];?>&PRODUCT_BUY_ID=<?=$item["ID"];?><?php } else { ?>id=<?=$item["ID"];?><?php } ?>"<?php endif; ?> class="btn btn-block btn-large btn-buy">
                <?php if($arParams['HAS_READMORE'] == 'Y'): ?>
                    <?=GetMessage('CT_BCE_CATALOG_READMORE');?>
                <?php else: ?>
                    <?=GetMessage('CT_BCE_CATALOG_ADD');?>
                <?php endif; ?>
            </a>
        <?php } else if(!empty($price)) { ?>
            <div class="preorder<?=$item['ID'];?>">
                <?php if($arParams['HAS_READMORE'] == 'Y'): ?>
                    <a <?php if($arParams['HAS_READMORE'] == 'Y'): ?> href="<?=$item['DETAIL_PAGE_URL'];?>"<?php else: ?> rel="nofollow" href="<?=$APPLICATION->GetCurPage();?>?action=add2basketamp&<?php if(isset($item["BUY_ID"]) && !empty($item["BUY_ID"])){ ?>id=<?=$item["BUY_ID"];?>&PRODUCT_BUY_ID=<?=$item["ID"];?><?php } else { ?>id=<?=$item["ID"];?><?php } ?>"<?php endif; ?> class="btn btn-block btn-large btn-buy">
                        <?=GetMessage('CT_BCE_CATALOG_READMORE');?>
                    </a>
                <?php else: ?>
                    <button on="tap:modalOCBuy<?=$item['ID'];?>g" class="btn btn-block btn-large btn-preorder" id="order-one-click<?=$item['ID'];?>g">
                        <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                    </button>
                <?php endif; ?>
                <?php if($arParams['HAS_READMORE'] != 'Y'): ?>
                    <amp-lightbox id="modalOCBuy<?=$item['ID'];?>g" layout="nodisplay" tabindex="-1" role="dialog" aria-labelledby="order-one-click<?=$item['ID'];?>g" aria-hidden="true">
                        <div class="lightbox">
                            <form class="modal-preorder bx-filter" id="modalOCForm<?=$item['ID'];?>g" method="post" action-xhr="<?php echo CMain::IsHTTPS() ? 'https:' : ''; ?>//<?php echo IMPEL_SERVER_NAME; ?>/ajax_cart/preorder.php" target="_top">
                                <div class="errors" submit-success>
                                    <template type="amp-mustache">
                                        {{#ERROR}}
                                        <div class="result result-error"><?php echo GetMessage("OC_ERROR"); ?>:{{.}}</div>
                                        {{/ERROR}}
                                        {{#ORDER_ID}}
                                        <div class="result result-success"><?php echo GetMessage("OC_ORDER_ADDED_PRE"); ?>{{ORDER_ID}}<?php echo GetMessage("OC_ORDER_ADDED_AFTER"); ?></div>
                                        {{/ORDER_ID}}
                                    </template>
                                </div>
                                <div submit-error>
                                    <template type="amp-mustache">
                                        {{#ERROR}}
                                        <div class="result result-error"><?php echo GetMessage("OC_ERROR"); ?>:{{.}}</div>
                                        {{/ERROR}}
                                        {{^ERROR}}
                                        <div class="result result-error"><?php echo GetMessage("OC_ERROR"); ?></div>
                                        {{/ERROR}}
                                    </template>
                                </div>
                                <div class="filters-wrapper">
                                    <div class="modal-header">
                                        <button type="button" class="button-close-filters" on="tap:modalOCBuy<?=$item['ID'];?>g.close" aria-label="Close">
                                            <i class="fa fa-times" aria-hidden="true">
                                            </i>
                                        </button>
                                        <h4 class="modal-title">
                                                <span class="catalog_one_click_pre_order">
                                                    <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                                                </span>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="order_one_click_form clearfix" id="order_one_click_form<?=$item['ID'];?>g">
                                            <div class="form-group input-group name-group clearfix">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-user" aria-hidden="true">
                                                        </span>
                                                    </span>
                                                <input type="text" id="PAYER_NAME<?=$item['ID'];?>g" name="PAYER_NAME" value="" placeholder="<?php echo GetMessage('OC_PAYER_NAME'); ?>" class="form-control" required />
                                                <span class="input-group-addon">
                                                        *
                                                    </span>
                                            </div>
                                            <div class="form-group input-group phone-group clearfix">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-mobile" aria-hidden="true"></span>
                                                    </span>
                                                <input type="text" id="PAYER_PHONE<?=$item['ID'];?>g" name="PAYER_PHONE" value="" placeholder="<?php echo GetMessage('OC_PAYER_PHONE'); ?>" class="form-control" required />
                                                <span class="input-group-addon">
                                                        *
                                                    </span>
                                            </div>
                                            <div class="form-group input-group email-group clearfix">
                                                    <span class="input-group-addon">
                                                        @
                                                    </span>
                                                <input type="email" id="PAYER_EMAIL<?=$item['ID'];?>g" name="PAYER_EMAIL" value="" placeholder="<?php echo GetMessage('OC_PAYER_EMAIL'); ?>" class="form-control" />
                                                <span class="input-group-addon">
                                                    </span>
                                            </div>
                                            <div class="clearfix buttons text-center">
                                                <button id="PAY_ONE_CLICK<?=$item['ID'];?>g" class="btn btn-block btn-large">
                                                    <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
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
                                            <input type="hidden" id="PRODUCT_ID<?=$item['ID'];?>g" name="PRODUCT_ID" value="<?php echo $item['ID']; ?>" />
                                        </div>
                                    </div>

                                </div>
                                <input type="hidden" name="amppreorder" value="true" />
                            </form>
                        </div>
                    </amp-lightbox>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
</div>
