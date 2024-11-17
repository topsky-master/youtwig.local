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

$logo_link = isset($arParams['LOGO_LINK'])
           &&!empty($arParams['LOGO_LINK'])
           ? trim($arParams['LOGO_LINK'])
           : '';

?>
<div class="logo-area">
    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["PREVIEW_PICTURE"])):?>
        <div class="logo-image-area">
            <? if($logo_link): ?>
            <a href="<? echo $logo_link; ?>">
            <? endif; ?>
                <amp-img class="img-responsive" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"];?>" layout="responsive"  src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" alt="<?=htmlspecialchars(trim($arResult["PREVIEW_PICTURE"]["ALT"]),ENT_QUOTES,LANG_CHARSET);?>">
                    <noscript>
                        <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"];?>" alt="<?=htmlspecialchars(trim($arResult["PREVIEW_PICTURE"]["ALT"]),ENT_QUOTES,LANG_CHARSET);?>" />
                    </noscript>
                </amp-img>
            <? if($logo_link): ?>
            </a>
            <? endif; ?>
        </div>
    <?endif?>
    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["PREVIEW_TEXT"]):?>
        <div class="phone-area">
            <?php

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
            <?=($arResult["PREVIEW_TEXT"]);?>
        </div>
    <?endif;?>
</div>