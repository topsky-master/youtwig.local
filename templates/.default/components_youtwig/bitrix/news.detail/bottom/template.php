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
<div class="bottom-module-area clearfix">
    <? if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
    <? if(isset($arResult['DISPLAY_PROPERTIES'])
        && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
        && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
        && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
    ): ?>
    <a href="<? echo $arResult['DISPLAY_PROPERTIES']['LINK']['VALUE']; ?>">
    <? endif; ?>
        <span class="h3">
            <?php echo $arResult['NAME']; ?>
        </span>
    <? if(isset($arResult['DISPLAY_PROPERTIES'])
        && isset($arResult['DISPLAY_PROPERTIES']['LINK'])
        && isset($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
        && !empty($arResult['DISPLAY_PROPERTIES']['LINK']['VALUE'])
    ): ?>
    </a>
    <? endif; ?>
    <? endif; ?>
    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["PREVIEW_PICTURE"])):?>
    <div class="img-area">
        <img class="img-responsive"	src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=htmlspecialcharsbx(trim($arResult["PREVIEW_PICTURE"]["ALT"]));?>" />
    </div>
    <?endif?>
    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["PREVIEW_TEXT"]):?>
    <div class="preview-text-area">
        <?=$arResult["PREVIEW_TEXT"];?>
    </div>
    <?endif;?>
</div>