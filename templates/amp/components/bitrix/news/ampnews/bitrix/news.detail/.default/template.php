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
    <div class="news-detail clearfix row" id="news-detail">
        <?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
            <h1>
                <?=$arResult["NAME"]?>
            </h1>
        <?endif;?>
        <?if(is_array($arResult["PREVIEW_PICTURE"])):?>
            <?php if(isset($arResult["PREVIEW_PICTURE"])
                && is_array($arResult["PREVIEW_PICTURE"])
                && !empty($arResult["PREVIEW_PICTURE"])):
                ?>
            <div class="detail_image">
                <amp-img itemprop="image" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arResult["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" layout="responsive" <?=$arResult["PREVIEW_PICTURE"]["SRCSET"];?>>
                    <noscript>
                        <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arResult["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" />
                    </noscript>
                </amp-img>
            </div>
            <?php endif; ?>
        <?endif?>
        <?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
            <span class="news-date-time">
                <?=$arResult["DISPLAY_ACTIVE_FROM"]?>
            </span>
        <?endif;?>

        <?if(mb_strlen($arResult["DETAIL_TEXT"])>0 || mb_strlen($arResult["PREVIEW_TEXT"])>0):?>
            <div class="detail_text clearfix">
                <?if(mb_strlen($arResult["PREVIEW_TEXT"])>0):?>
                    <?php echo $arResult["PREVIEW_TEXT"];
                    ?>
                <?endif?>
                <?if(mb_strlen($arResult["DETAIL_TEXT"])>0):?>
                    <?php echo $arResult["DETAIL_TEXT"];
                    ?>
                <?endif?>
            </div>
        <?endif?>
    </div>
<?php

if(
    (
        isset($arResult["TOLEFT"])
        && is_array($arResult["TOLEFT"])
        && isset($arResult["TOLEFT"]["URL"])
        && isset($arResult["TOLEFT"]["NAME"])
        && !empty($arResult["TOLEFT"]["URL"])
        && !empty($arResult["TOLEFT"]["NAME"])
    ) ||
    (
        isset($arResult["TORIGHT"])
        && is_array($arResult["TORIGHT"])
        && isset($arResult["TORIGHT"]["URL"])
        && isset($arResult["TORIGHT"]["NAME"])
        && !empty($arResult["TORIGHT"]["URL"])
        && !empty($arResult["TORIGHT"]["NAME"])
    )
):?>
    <div id="pager-area" class="blog-page-navigation row clearfix">
        <ul class="pager">
            <?if(
                isset($arResult["TOLEFT"])
                &&  is_array($arResult["TOLEFT"])
                &&  isset($arResult["TOLEFT"]["URL"])
                &&  isset($arResult["TOLEFT"]["NAME"])
                &&  !empty($arResult["TOLEFT"]["URL"])
                &&  !empty($arResult["TOLEFT"]["NAME"])
            ):?>
                <li class="previous">
                    <a href="<?=$arResult["TOLEFT"]["URL"]?>">
                        <?=$arResult["TOLEFT"]["NAME"]?>
                    </a>
                </li>
            <?endif?>
            <?if(
                isset($arResult["TORIGHT"])
                &&  is_array($arResult["TORIGHT"])
                &&  isset($arResult["TORIGHT"]["URL"])
                &&  isset($arResult["TORIGHT"]["NAME"])
                &&  !empty($arResult["TORIGHT"]["URL"])
                &&  !empty($arResult["TORIGHT"]["NAME"])
            ):?>
                <li class="next">
                    <a href="<?=$arResult["TORIGHT"]["URL"]?>">
                        <?=$arResult["TORIGHT"]["NAME"]?>
                    </a>
                </li>
            <?endif?>
        </ul>
    </div>
<?endif?>