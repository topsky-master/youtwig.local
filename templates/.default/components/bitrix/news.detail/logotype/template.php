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

?>
<div class="logo-area clearfix">
    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["PREVIEW_PICTURE"])):?>
    <div class="logo-image-area text-center col-xs-12 col-sm-6 col-md-6">
        <? if(isset($arResult['DISPLAY_PROPERTIES'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
            && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
        ): ?>
        <a href="<? echo $arResult['DISPLAY_PROPERTIES']['LINK']['VALUE']; ?>">
        <? endif; ?>
            <img class="img-responsive"	src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=htmlspecialcharsbx(trim($arResult["PREVIEW_PICTURE"]["ALT"]));?>" />
        <? if(isset($arResult['DISPLAY_PROPERTIES'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
            && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
        ): ?>
        </a>
        <p>Запчасти для бытовой техники</p>
        <? endif; ?>
    </div>
    <?endif?>
    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["PREVIEW_TEXT"]):?>
    <div class="phone-area hidden-xs col-xs-12 col-sm-6 col-md-6">
        <?=$arResult["PREVIEW_TEXT"];?>
    </div>
    <?endif;?>
</div>