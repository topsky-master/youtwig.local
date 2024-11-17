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

$this->setFrameMode(true);

?>
<div class="la" itemscope itemtype="http://schema.org/Organization">
    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["PREVIEW_PICTURE"])):?>
        <div class="lia col-sm-6 col-md-5 col-lg-6">
            <? if(isset($arResult['DISPLAY_PROPERTIES'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
            && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
            && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
            ): ?>
            <a itemprop="url" href="<? echo $arResult['DISPLAY_PROPERTIES']['LINK']['VALUE']; ?>">
                <? endif; ?>
                <img itemprop="logo" class="img-responsive"	src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=htmlspecialcharsbx(trim($arResult["PREVIEW_PICTURE"]["ALT"]));?>" />
                <? if(isset($arResult['DISPLAY_PROPERTIES'])
                && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
                && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                ): ?>
            </a>
        <? endif; ?>
       <!-- <p>Запчасти для бытовой техники</p>-->
        </div>
    <?endif?>
    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["PREVIEW_TEXT"]):?>
        <div class="pa col-sm-6 col-md-7 col-lg-6">
            <?=$arResult["PREVIEW_TEXT"];?>
        </div>
    <?endif;?>
</div>