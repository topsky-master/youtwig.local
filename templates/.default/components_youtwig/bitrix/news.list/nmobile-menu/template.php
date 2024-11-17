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
    <nav id="menuToggleContainer" role="navigation">
        <div id="menuToggle">
            <input type="checkbox" />
            <span></span>
            <span></span>
            <span></span>
            <ul id="mobileMenu">
                <li>
                    <?=$arResult['MENU_DESCRIPTION'];?>
                </li>
                <?foreach($arResult["ITEMS"] as $arItem):?>
                    <?

                    $smLink = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        : '';

                    $smAtribute = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        : '';

                    ?>
                    <li class="menu-item-<?=$arItem['ID'];?><?php if(isset($arItem['CODE']) && !empty($arItem['CODE'])): ?> menu-item-<?=mb_strtolower($arItem['CODE']);?><?php endif; ?>">
                        <?if(!empty($smLink)):?>
                        <a href="<?=$smLink;?>" <?=$smAtribute;?>>
                            <?endif;?>
                            <span>
                            <?if($arParams["DISPLAY_PICTURE"]!="N"
                                && is_array($arItem["PREVIEW_PICTURE"])):?>
                                <img class="img-responsive" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" />
                            <?endif?>
                                <?if($arParams["DISPLAY_NAME"]!="N"
                                    && $arItem["NAME"]):?>
                                    <?=$arItem["NAME"];?>
                                <?endif;?>
                                <?php if(isset($arItem['CODE'])
                                    && !empty($arItem['CODE'])): ?>

                                    <?php switch ($arItem['CODE']){
                                        case 'cart':

                                            if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
                                                CAPIUncachedArea::includeFile(
                                                    "/include/cartline.php",
                                                    array(
                                                    )
                                                );
                                            }

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
		</div>
	</nav>
<?php endif; ?>