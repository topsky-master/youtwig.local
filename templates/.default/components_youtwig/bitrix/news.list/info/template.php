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
if(is_array($arResult["ITEMS"]) && sizeof($arResult["ITEMS"])):
?>
<div class="news-info-area clearfix">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <div class="pagenav clearfix">
        <?=$arResult["NAV_STRING"]?>
    </div>
<?endif;?>
<div class="news-info clearfix">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
        <h3>
        	<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>">
                <?echo $arItem["NAME"]?>
            </a>
        </h3>
        <?php if((isset($arItem["PREVIEW_TEXT"]) && !empty($arItem["PREVIEW_TEXT"]))): ?>
        <div class="preview_text clearfix">
            <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>">
                <?php if((isset($arItem["PREVIEW_TEXT"]) && !empty($arItem["PREVIEW_TEXT"]))): ?>
                    <? echo $arItem["PREVIEW_TEXT"];?>
                <?php endif; ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
<?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="pagenav clearfix">
        <?=$arResult["NAV_STRING"]?>
    </div>
<?endif;?>
</div>
<?php endif; ?>
