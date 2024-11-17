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


$tmpl_main_icons_list_title = isset($arParams['DISPLAY__LIST_TITLE'])
    ? trim($arParams['DISPLAY__LIST_TITLE'])
    : '';

if(is_array($arResult["ITEMS"])
    && !empty($arResult["ITEMS"])):
    ?>
    <div class="main-icons">
        <? if(!empty($tmpl_main_icons_list_title)){ ?>
            <div class="block-title">
                <?=$tmpl_main_icons_list_title;?>
            </div>
        <? } ?>
        <div class="category-home">
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <?
				
				if(isset($arItem["PREVIEW_PICTURE"]) && isset($arItem["PREVIEW_PICTURE"]["SRC"]))	
					$srcDetail = getWebpSrc($arItem["PREVIEW_PICTURE"]["SRC"]);

				if(isset($arItem["DETAIL_PICTURE"]) && isset($arItem["DETAIL_PICTURE"]["SRC"])) {
					
					$srcMobile = getWebpSrc($arItem["DETAIL_PICTURE"]["SRC"]);
					
					if(class_exists('\Bitrix\Conversion\Internals\MobileDetect')) {
						$mDetect = new \Bitrix\Conversion\Internals\MobileDetect;
						if($mDetect->isMobile()){
							$arItem["PREVIEW_PICTURE"]["SRC"] = $arItem["DETAIL_PICTURE"]["SRC"];
						};
					}
					
				}	
				
                $smLink = isset($arItem['DISPLAY_PROPERTIES'])
                &&isset($arItem['DISPLAY_PROPERTIES']['LINK'])
                &&isset($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                    ? trim($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                    : '';
                ?>
                <div class="small-pading col-sm-4 col-xs-6 col-md-3 col-lg-2" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <?if(!empty($smLink)):?>
                    <a href="<?=$smLink;?>">
                        <?endif;?>
                        <?if($arParams["DISPLAY_PICTURE"]!="N"
                            && is_array($arItem["PREVIEW_PICTURE"])):?>
                            <img class="img-responsive" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" />
                        <?endif?>
                        <?if($arParams["DISPLAY_NAME"]!="N"
                            && $arItem["NAME"]):?>
                            <h2>
                                <?=$arItem["NAME"];?>
                            </h2>
                        <?endif;?>
                        <?if(!empty($smLink)):?>
                    </a>
                <?endif;?>
                </div>
            <?endforeach;?>
        </div>
    </div>
<?php endif; ?>


