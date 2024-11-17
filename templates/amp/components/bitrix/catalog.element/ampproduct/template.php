<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(false);

?>
<div class="product-detail" itemscope itemtype="http://schema.org/Product">
    <?

    if(isset($arResult["PROPERTIES"])
        && isset($arResult["PROPERTIES"]["ARTNUMBER"])
        && isset($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])
        && !empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])){
        ?>
        <div class="bx_catalog_item_articul">
            <a class="section-link" href="<?=$arResult['SECTION_PAGE_URL'];?>">
                <?=GetMessage('TMPL_SECTION_LIST_BACK');?>
            </a>
            <p>
                <strong>
                    <?php  echo $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"]; ?>
                </strong>
                <span>
                    <?php  echo $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]; ?>
                </span>
            </p>
            <meta itemprop="sku" content="<?=htmlspecialcharsbx($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]);?>" >
        </div>
        <?
        unset($arResult["PROPERTIES"]["ARTNUMBER"],$arResult["DISPLAY_PROPERTIES"]["ARTNUMBER"]);

    };

    if(!empty($arResult['GALLERY'])){
        ?>
        <div class="product-image">
            <amp-carousel width="640" height="480" layout="responsive" type="slides">
                <?php foreach($arResult['GALLERY'] as $image){?>
                    <amp-img itemprop="image" alt="<?=htmlspecialchars($arResult['NAME'],ENT_QUOTES,LANG_CHARSET);?>" src="<?=$image["src"];?>" width="<?=$image["width"];?>" height="<?=$image["height"];?>" layout="responsive"<?=$image["srcset"];?>>
                        <noscript>
                            <img src="<?=$image["src"];?>" width="<?=$image["width"];?>" height="<?=$image["width"];?>" alt="<?=htmlspecialchars($arResult['NAME'],ENT_QUOTES,LANG_CHARSET);?>" />
                        </noscript>
                    </amp-img>
                <?php   }?>
            </amp-carousel>
        </div>
    <?php } ?>
    <div class="main-content">
        <div class="products-detail">
            <div class="product-info">
                <?php

                $minPrice = $arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']];
                $notavailable = !empty($minPrice['RATIO_PRICE']) ? false : true;
                $inStock = $notavailable || !$arResult['CAN_BUY'] ? false : true;

                $not_much = isset($arParams['NOT_MUCH']) && !empty($arParams['NOT_MUCH']) ? (int)$arParams['NOT_MUCH'] : 5;
                $not_much = empty($not_much) ? 5 : $not_much;
                ?>
                <div class="in-stock-label" id="in-stock-<?=$arResult['ID'];?>">
                    <?=GetMessage('CT_BCS_CATALOG_IN_STOCK');?>
                    <span id="in-stock-label" class="<?=$inStock?'in-stock':'out-of-stock';?><?=($inStock && $arResult['PRINT_QUANTITY'] < $not_much)?(' not-much'):'';?>">
                        <?=$inStock?(($arResult['PRINT_QUANTITY'] < $not_much) ? GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH') : GetMessage('CT_BCS_CATALOG_IN_STOCK_YES')):GetMessage('CT_BCS_CATALOG_IN_STOCK_NO');?>
                    </span>
                </div>
                <h1 itemprop="name" id="title-page">
                    <?=$arResult['NAME'];?>
                </h1>
                <?php

                if (!empty($arResult['DISPLAY_PROPERTIES'])){ ?>
                    <dl class="properties">
                        <? foreach ($arResult['DISPLAY_PROPERTIES'] as $pid => $arOneProp){

                            if(
                            in_array($pid,array(
                                'ORIGINALS_CODES',
                                'QUALITY',
                                'COM_BLACK',
                                'INSTRUCTION',
                                'ARTNUMBER',
                                'LINKED_ELEMETS',
                                'SEO_TEXT'
                            ))){
                                continue;
                            }


                            ?>
                            <dt>
                            <span>
                                <? echo $arOneProp['NAME']; ?>
                            </span>
                            </dt>
                            <dd>
                                <?
                                echo (is_array($arOneProp['DISPLAY_VALUE'])
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

                ?>
                <?php if(isset($arResult["PROPERTIES"])
                    &&isset($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"])
                    &&isset($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
                    &&!empty($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"])
                ){ ?>
                    <meta itemprop="manufacturer" content="<?=htmlspecialchars(is_array($arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"]) ? join(', ',$arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"]) : $arResult["PROPERTIES"]["MANUFACTURER_DETAIL"]["VALUE"],ENT_QUOTES,LANG_CHARSET);?>" >
                <?php } ?>
                <div class="item_price<?php if(!$arResult['CAN_BUY']){ ?> not-buy<?php }; ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <?php $minPrice = $arResult['ITEM_PRICES'][$arResult['ITEM_PRICE_SELECTED']]; ?>
                    <div class="item-price">
                        <? echo $minPrice['PRINT_RATIO_PRICE']; ?>
                        <meta itemprop="price" content="<?=abs((float)$minPrice['RATIO_BASE_PRICE']); ?>" />
                        <? if(isset($minPrice['CURRENCY']) && !empty($minPrice['CURRENCY'])){?>
                            <meta itemprop="priceCurrency" content="<?=$minPrice['CURRENCY'];?>" />
                        <?php } ?>
                    </div>
                    <div class="buy-buttons">
                        <? if($arResult['CAN_BUY']){?>
                            <link itemprop="availability" href="http://schema.org/InStock" />
                            <a rel="nofollow" href="<?=$APPLICATION->GetCurPage();?>?action=add2basketamp&<?php if(isset($arResult["BUY_ID"]) && !empty($arResult["BUY_ID"])){ ?>id=<?=$arResult["BUY_ID"];?>&PRODUCT_BUY_ID=<?=$arResult["ID"];?><?php } else { ?>id=<?=$arResult["ID"];?><?php } ?>" class="btn btn-block btn-large btn-buy">
                                <?=GetMessage('CT_BCE_CATALOG_ADD');?>
                            </a>
                        <?php } else { ?>
                            <link itemprop="availability" href="http://schema.org/OutOfStock" />
                            <div class="preorder<?=$arResult['ID'];?>">
                                <button on="tap:modalOCBuy<?=$arResult['ID'];?>g" class="btn btn-block btn-large btn-preorder" id="order-one-click<?=$arResult['ID'];?>g">
                                    <?php echo GetMessage('CATALOG_ONE_CLICK_PRE_ORDER'); ?>
                                </button>
                                <amp-lightbox id="modalOCBuy<?=$arResult['ID'];?>g" layout="nodisplay" tabindex="-1" role="dialog" aria-labelledby="order-one-click<?=$arResult['ID'];?>g" aria-hidden="true">
                                    <div class="lightbox">
                                        <form class="modal-preorder bx-filter" id="modalOCForm<?=$arResult['ID'];?>g" method="post" action-xhr="<?php echo CMain::IsHTTPS() ? 'https:' : ''; ?>//<?php echo IMPEL_SERVER_NAME; ?>/ajax_cart/preorder.php" target="_top">
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
                                                    <button type="button" class="button-close-filters" on="tap:modalOCBuy<?=$arResult['ID'];?>g.close" aria-label="Close">
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
                                                    <div class="order_one_click_form clearfix" id="order_one_click_form<?=$arResult['ID'];?>g">
                                                        <div class="form-group input-group name-group clearfix">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-user" aria-hidden="true">
                                                        </span>
                                                    </span>
                                                            <input type="text" id="PAYER_NAME<?=$arResult['ID'];?>g" name="PAYER_NAME" value="" placeholder="<?php echo GetMessage('OC_PAYER_NAME'); ?>" class="form-control" required />
                                                            <span class="input-group-addon">
                                                        *
                                                    </span>
                                                        </div>
                                                        <div class="form-group input-group phone-group clearfix">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-mobile" aria-hidden="true"></span>
                                                    </span>
                                                            <input type="text" id="PAYER_PHONE<?=$arResult['ID'];?>g" name="PAYER_PHONE" value="" placeholder="<?php echo GetMessage('OC_PAYER_PHONE'); ?>" class="form-control" required />
                                                            <span class="input-group-addon">
                                                        *
                                                    </span>
                                                        </div>
                                                        <div class="form-group input-group email-group clearfix">
                                                    <span class="input-group-addon">
                                                        @
                                                    </span>
                                                            <input type="email" id="PAYER_EMAIL<?=$arResult['ID'];?>g" name="PAYER_EMAIL" value="" placeholder="<?php echo GetMessage('OC_PAYER_EMAIL'); ?>" class="form-control" />
                                                            <span class="input-group-addon">
                                                    </span>
                                                        </div>
                                                        <div class="clearfix buttons text-center">
                                                            <button id="PAY_ONE_CLICK<?=$arResult['ID'];?>g" class="btn btn-block btn-large">
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
                                                        <input type="hidden" id="PRODUCT_ID<?=$arResult['ID'];?>g" name="PRODUCT_ID" value="<?php echo $arResult['ID']; ?>" />
                                                    </div>
                                                </div>

                                            </div>
                                            <input type="hidden" name="amppreorder" value="true" />
                                        </form>
                                    </div>
                                </amp-lightbox>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="detail-tabs">
                <div id="detail-tabs">
                    <?php if(!empty($arResult['DETAIL_TEXT'])){ ?>
                        <div class="description sublevel" itemprop="description">
                            <input type="checkbox" id="tdescription" checked="checked" />
                            <label for="tdescription">
                                <?=GetMessage('CT_BCE_CATALOG_DESCRIPTION');?>
                            </label>
                            <div class="tab-content">
                                <?php if(!empty($arResult['DETAIL_TEXT'])){?>
                                    <div class="preview-text">
                                        <?=$arResult['DETAIL_TEXT'];?>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php

                    if(isset($arResult['TABS'])
                        && !empty($arResult['TABS'])){
                        ?>

                        <?php

                        $tabs = $arResult['TABS'];

                        foreach($tabs['tab_headers'] as $tab_count => $tab_name){?>
                            <? if(isset($tabs['tab_panels'][$tab_count]) && !empty($tabs['tab_panels'][$tab_count])){?>
                                <div class="sublevel">
									<input type="checkbox" id="t<?php echo $tab_count; ?>" <?php if(!($tab_count == 'cbsm')): ?> checked="checked"<? endif; ?> />
                                    <label for="t<?php echo $tab_count; ?>">
                                        <?=$tab_name;?>
                                    </label>
                                    <div class="tab-content">
                                        <? if(isset($tabs['tab_panels'][$tab_count])){?>
                                            <?=$tabs['tab_panels'][$tab_count];?>
                                        <?php }?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>

                        <?php
                    }

                    if (
                        isset($arResult['DISPLAY_PROPERTIES'])
                        && isset($arResult['DISPLAY_PROPERTIES']['ORIGINALS_CODES'])
                        && isset($arResult['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])
                        && !empty($arResult['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'])

                    )
                    {

                        ?>
                        <div class="sublevel description">
                            <input type="checkbox" id="todescription" /> 
							<label for="todescription">
                                <?=GetMessage('TMPL_ORIGINAL_CODES_TITLE');?>
                            </label>
                            <div class="tab-content">
							    <dl class="properties">
                                    <?
                                    foreach ($arResult['DISPLAY_PROPERTIES']['ORIGINALS_CODES']['DISPLAY_VALUE'] as $property)
                                    {

                                        if(!empty($property)){

                                            $property = explode(':',$property, 2);

                                            if(empty($property[0]) || empty($property[1]))
                                                continue;

                                            ?>
                                            <dt><span><?=$property[0]?></span></dt>
                                            <dd><?=$property[1]?>
                                            </dd>
                                            <?

                                        }
                                    }

                                    unset($property);
                                    ?>
                                </dl>
								<?php

                                unset($arResult['PROPERTIES']['ORIGINALS_CODES']);

								?>
                            </div>
                        </div>
                    <?php  } ?>
