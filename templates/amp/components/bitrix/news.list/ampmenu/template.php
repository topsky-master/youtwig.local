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

if(is_array($arResult["ITEMS"])
    && !empty(is_array($arResult["ITEMS"]))):

    ?>
    <amp-sidebar id="sidebar" layout="nodisplay" side="left">
        <amp-img class="cross" src="/" width="20" height="20" alt="close sidebar" on="tap:sidebar.close" role="button" tabindex="0">
            <i class="fa fa-times" aria-hidden="true"></i>
        </amp-img>
        <nav class="side-menu">
            <?php if(isset($arResult['MENU_DESCRIPTION']) && !empty($arResult['MENU_DESCRIPTION'])){ ?>
            <div class="side-menu-description">
                <?=$arResult['MENU_DESCRIPTION'];?>
            </div>
            <?php } ?>
            <ul>
                <?foreach($arResult["ITEMS"] as $arItem):?>
                    <?

                    $smLink = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINKAMP'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINKAMP']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINKAMP']['VALUE'])
                        : '';

                    $smAtribute = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        : '';

                    ?>
                    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="menu-item-<?=$arItem['ID'];?><?php if(isset($arItem['CODE']) && !empty($arItem['CODE'])): ?> menu-item-<?=mb_strtolower($arItem['CODE']);?><?php endif; ?>">
                        <?if(!empty($smLink)):?>
                        <a href="<?=$smLink;?>" <?=$smAtribute;?>>
                        <?endif;?>
                            <span>
                            <?if($arParams["DISPLAY_PICTURE"]!="N"
                                && is_array($arItem["PREVIEW_PICTURE"])):?>
                                <amp-img class="img-responsive" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=htmlentities($arItem["PREVIEW_PICTURE"]["ALT"],ENT_QUOTES,LANG_CHARSET); ?>" width="20" height="20">
                                    <noscript>
                                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="20" height="20" alt="<?=htmlentities($arItem["PREVIEW_PICTURE"]["ALT"],ENT_QUOTES,LANG_CHARSET); ?>" />
                                    </noscript>
                                </amp-img>
                            <?endif?>
                                <?if($arParams["DISPLAY_NAME"]!="N"
                                    && $arItem["NAME"]):?>
                                    <?=$arItem["NAME"];?>
                                <?endif;?>
                                <?php if(isset($arItem['CODE'])
                                    && !empty($arItem['CODE'])): ?>

                                    <?php switch ($arItem['CODE']){
                                        case 'cart':
                                            ?>
                                            <span id="mobilemenu<?=mb_strtolower($arItem['CODE']);?>">
                                                <?=$arResult['CART_COUNT'];?>
                                            </span>
                                            <?php

                                            break;
                                    }; ?>
                                <?php endif; ?>
                            </span>
                        <?if(!empty($smLink)):?>
                        </a>
                        <?endif;?>
                    </li>
                <?endforeach;?>
            </ul>
        </nav>
    </amp-sidebar>
    <a id="left-menu-button" on="tap:sidebar.toggle">
        <button class="menu-button cross" on="tap:sidebar.toggle" role="button" tabindex="0">
            <i class="fa fa-bars" aria-hidden="true">
            </i>
        </button>
    </a>
<?php endif; ?>