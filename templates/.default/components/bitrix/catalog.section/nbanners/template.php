<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(false);

if(isset($arResult["ITEMS"])
    && is_array($arResult["ITEMS"])
    && sizeof($arResult["ITEMS"])): ?>
    <div class="banner-static-contain">
        <?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
            <?if(is_array($arElement["PREVIEW_PICTURE"]) || is_array($arElement["DETAIL_PICTURE"])):?>
                <div class="banner-box">
                    <a rel="nofollow" href="<?php if(isset($arElement["PROPERTIES"]["URL_BANNER"]["VALUE"])): ?><?php echo $arElement["PROPERTIES"]["URL_BANNER"]["VALUE"]; ?><?php else : ?><?=$arElement["DETAIL_PAGE_URL"]?><?php endif; ?>">
                        <?if(is_array($arElement["PREVIEW_PICTURE"])):?>
                            <img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" class="img-responsive" alt="<?=htmlspecialchars($arElement["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                        <?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
                            <img src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" class="img-responsive"  alt="<?=htmlspecialchars($arElement["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                        <?endif?>
                    </a>
                </div>
            <?endif?>
        <?endforeach; ?>
    </div>
<?php endif; ?>