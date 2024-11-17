<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?>
<?php if(isset($arResult["ITEMS"]) && is_array($arResult["ITEMS"]) && sizeof($arResult["ITEMS"])): ?>
    <?php

    $counts 	= 12;

    switch ($arParams["LINE_ELEMENT_COUNT"]):
        case 12:
            $counts = 1;
            break;
        case 6:
            $counts = 2;
            break;
        case 4:
            $counts = 3;
            break;
        case 3:
            $counts = 4;
            break;
        case 2:
            $counts = 6;
            break;

    endswitch;

    ?>
    <div class="banner-static-contain">
        <div class="home-banner-static row-fluid">
            <? $arParams["LINE_ELEMENT_COUNT"] = 3; ?>
            <?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
                <?
                $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
                ?>


                <?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
                    <div class="row">
                <?endif;?>

                <div class="banner-box banner-box1 col-xs-12 col-sm-<?php echo $counts; ?> col-md-<?php echo $counts; ?> col-lg-<?php echo $counts; ?>">
                    <?if(is_array($arElement["PREVIEW_PICTURE"])):?>
                        <a rel="nofollow" href="<?php if(isset($arElement["PROPERTIES"]["URL_BANNER"]["VALUE"])): ?><?php echo $arElement["PROPERTIES"]["URL_BANNER"]["VALUE"]; ?><?php else : ?><?=$arElement["DETAIL_PAGE_URL"]?><?php endif; ?>">
                            <img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=htmlspecialchars($arElement["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                        </a>
                    <?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
                        <a rel="nofollow"  href="<?php if(isset($arElement["PROPERTIES"]["URL_BANNER"]["VALUE"])): ?><?php echo $arElement["PROPERTIES"]["URL_BANNER"]["VALUE"]; ?><?php else : ?><?=$arElement["DETAIL_PAGE_URL"]?><?php endif; ?>">
                            <img src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=htmlspecialchars($arElement["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
                        </a>
                    <?endif?>
                </div>

                <?if(($cell + 1)%$arParams["LINE_ELEMENT_COUNT"] == 0 || sizeof($arResult["ITEMS"]) == $cell + 1):?>
                    </div>
                <?endif;?>

            <?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
        </div>
    </div>
<?php endif; ?>