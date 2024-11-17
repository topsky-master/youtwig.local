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
$this->setFrameMode(true);?>
<? if((isset($arResult['DISPLAY_PROPERTIES'])
&& isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM'])
&& isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'])) ||
($arResult['PREVIEW_TEXT'] != ""
&& $arParams["DISPLAY_PREVIEW_TEXT"] != "N")){ ?>
<div class="section-bottom-info row">
<? if(isset($arResult['DISPLAY_PROPERTIES'])
&& isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM'])
&& isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'])){ ?>
    <h1 class="down-page">
        <? echo $arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'];?>
    </h1>
<? }; ?>
<? if($arResult['PREVIEW_TEXT'] != "" && $arParams["DISPLAY_PREVIEW_TEXT"] != "N"){?>
    <? if(isset($arResult['PREVIEW_TEXT'])
        &&!empty($arResult['PREVIEW_TEXT'])): ?>
        <? echo $arResult['PREVIEW_TEXT']; ?>
    <? endif; ?>
<?};?>
</div>
<? };?>
<?
$image_thumb_width                                                          = isset($arParams['IMAGE_THUMB_WIDTH'])
                                                                            &&!empty($arParams['IMAGE_THUMB_WIDTH'])
                                                                            ? (int)$arParams['IMAGE_THUMB_WIDTH']
                                                                            : 0;

$image_thumb_height                                                         = isset($arParams['IMAGE_THUMB_HEIGHT'])
                                                                            &&!empty($arParams['IMAGE_THUMB_HEIGHT'])
                                                                            ? (int)$arParams['IMAGE_THUMB_HEIGHT']
                                                                            : 0;


if(is_array($arResult["PREVIEW_PICTURE"])):

$src                                                                        = newsListMainTemplateTools::rectangleImage(
                	                                                        $_SERVER['DOCUMENT_ROOT'].$arResult["PREVIEW_PICTURE"]["SRC"],
    	                                                                    $image_thumb_width,
    	                                                                    $image_thumb_height,
    	                                                                    $arResult["PREVIEW_PICTURE"]["SRC"]
                                                                            );

$additional_link                                                            = isset($arResult["DISPLAY_PROPERTIES"])
                                                                            &&isset($arResult["DISPLAY_PROPERTIES"]["ADDITIONAL_LINK"])
                                                                            &&isset($arResult["DISPLAY_PROPERTIES"]["ADDITIONAL_LINK"]["VALUE"])
                                                                            &&!empty($arResult["DISPLAY_PROPERTIES"]["ADDITIONAL_LINK"]["VALUE"])
                                                                            ?$arResult["DISPLAY_PROPERTIES"]["ADDITIONAL_LINK"]["VALUE"]
                                                                            :'';

?>
<div class="section-banners-area row">
    <? if(!empty($additional_link)): ?>
    <a href="<?php echo $additional_link; ?>" class="section-banner-link">
    <? endif; ?>
        <img src="<?=$src;?>" class="img-responsive img-banner" />
    <? if(!empty($additional_link)): ?>
    </a>
    <? endif; ?>
</div>
<?endif?>