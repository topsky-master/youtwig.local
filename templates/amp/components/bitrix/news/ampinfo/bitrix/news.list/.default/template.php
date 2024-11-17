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
            <?php
                $detail_link = (isset($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) && !empty($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) ? $arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"] : $arItem["DETAIL_PAGE_URL"]);
            ?>
            <div class="news-item row clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                    <a href="<?=$detail_link;?>" class="h3 newst-title">
                        <?echo $arItem["NAME"]?>
                    </a>
                <?endif;?>
            </div>
        <?endforeach;?>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
<?php endif; ?>