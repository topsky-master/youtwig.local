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
$this->setFrameMode(false);
if(isset($arResult["ITEMS"])
    && sizeof($arResult["ITEMS"])
    && is_array($arResult["ITEMS"])):
    ?>
    <div class="news-list news-list-area clearfix" id="news-list-area">
        <?if($arParams["DISPLAY_TOP_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <div class="news-item row clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 news-list-image">
                    <?php
                    $detail_link = (isset($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) && !empty($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) ? $arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"] : $arItem["DETAIL_PAGE_URL"]);
                    ?>
                    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
                        <a href="<?=$detail_link?>">
                            <?php if(isset($arItem["PREVIEW_PICTURE"])
                                && is_array($arItem["PREVIEW_PICTURE"])
                                && !empty($arItem["PREVIEW_PICTURE"])):
                                ?>
                                <amp-img itemprop="image" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arItem["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" layout="responsive" <?=$arItem["PREVIEW_PICTURE"]["SRCSET"];?>>
                                    <noscript>
                                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arItem["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" />
                                    </noscript>
                                </amp-img>
                            <?php endif; ?>
                        </a>
                    <?endif?>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 news-list-description">
                    <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                        <a href="<?=$detail_link;?>" class="h3 newst-title">
                            <?echo $arItem["NAME"]?>
                        </a>
                    <?endif;?>
                    <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                        <span class="news-date-time">
			         <?echo $arItem["DISPLAY_ACTIVE_FROM"]?>
			     </span>
                    <?endif?>
                    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                        <a href="<?=$detail_link;?>" class="preview-text">
                            <?php
                                echo $arItem["PREVIEW_TEXT"];
                            ?>&hellip;
                        </a>
                        <a href="<?=$detail_link;?>" class="btn btn-info btn-readmore">
                            <?php echo GetMessage('CT_READ_MORE'); ?>
                        </a>
                    <?endif;?>
                </div>
            </div>
        <?endforeach;?>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
<?php endif; ?>
