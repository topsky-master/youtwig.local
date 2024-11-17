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

$tmpl_main_icons_list_title = isset($arParams['DISPLAY__LIST_TITLE'])
    ? trim($arParams['DISPLAY__LIST_TITLE'])
    : '';

if(is_array($arResult["ITEMS"])
    && !empty(is_array($arResult["ITEMS"]))):

    ?>
    <? if(!empty($tmpl_main_icons_list_title)): ?>
    <h1 class="section-title"><?=$tmpl_main_icons_list_title;?></h1>
    <? endif; ?>
    <div class="section-icons">
        <div class="category-images">
            <div class="row clearfix">
                <?foreach($arResult["ITEMS"] as $arItem):?>
                    <?


                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                    $smLink = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        : '';
                    ?>
                    <div class="small-pading col-xs-12 col-sm-6 col-md-3 col-lg-3" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                        <div class="element">
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
                    </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
<?php endif; ?>