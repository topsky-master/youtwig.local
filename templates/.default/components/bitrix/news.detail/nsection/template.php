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
<? if((isset($arResult['PROPERTIES'])
        && isset($arResult['PROPERTIES']['H1_BOTTOM'])
        && isset($arResult['PROPERTIES']['H1_BOTTOM']['VALUE'])) ||
    ($arResult['PREVIEW_TEXT'] != ""
        && $arParams["DISPLAY_PREVIEW_TEXT"] != "N")){ ?>
    <div class="info">
        <? if(isset($arParams['H1_BOTTOM'])
            && isset($arParams['H1_BOTTOM'])
            && isset($arParams['H1_BOTTOM'])){ ?>
            <h1>
                <? echo $arParams['H1_BOTTOM'];?>
            </h1>
        <? } elseif(isset($arResult['PROPERTIES'])
            && isset($arResult['PROPERTIES']['H1_BOTTOM'])
            && isset($arResult['PROPERTIES']['H1_BOTTOM']['VALUE'])){ ?>
            <h1>
                <? echo $arResult['PROPERTIES']['H1_BOTTOM']['VALUE'];?>
            </h1>
        <? }; ?>
        <? if(isset($arParams['META_PREVIEW_TEXT'])){
			// $spText = trim($arParams['META_PREVIEW_TEXT']);
			$spText = $arParams['META_PREVIEW_TEXT'];
            {
                $arResult['PREVIEW_TEXT'] = $spText;
            }
        ?>
        <? } ?>
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
    <div class="banners">
        <? if(!empty($additional_link)): ?>
        <a href="<?php echo $additional_link; ?>" class="section-banner-link">
            <? endif; ?>
            <img src="<?=$src;?>" class="img-responsive img-banner" />
            <? if(!empty($additional_link)): ?>
        </a>
    <? endif; ?>
    </div>
<?endif?>