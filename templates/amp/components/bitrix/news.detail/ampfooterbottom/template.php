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

$amp_content_obj = new AMP_Content( $arResult["PREVIEW_TEXT"],
    array(
        //'AMP_YouTube_Embed_Handler' => array(),
    ),
    array(
        'AMP_Style_Sanitizer' => array(),
        'AMP_Blacklist_Sanitizer' => array(),
        'AMP_Img_Sanitizer' => array(),
        'AMP_Video_Sanitizer' => array(),
        'AMP_Audio_Sanitizer' => array(),
        'AMP_Iframe_Sanitizer' => array(
            'add_placeholder' => true,
        ),
    ),
    array(
        'content_max_width' => 600,
    )
);

$arResult["PREVIEW_TEXT"] = $amp_content_obj->get_amp_content();

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
            <amp-img class="img-responsive"	src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=htmlspecialcharsbx(trim($arResult["PREVIEW_PICTURE"]["ALT"]));?>">
                <noscript>
                    <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=htmlspecialcharsbx(trim($arResult["PREVIEW_PICTURE"]["ALT"]));?>" />
                </noscript>
            </amp-img>
        </div>
    <?endif?>
    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["PREVIEW_TEXT"]):?>
        <div class="preview-text-area">
            <?=$arResult["PREVIEW_TEXT"];?>
        </div>
    <?endif;?>
</div>