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
//$this->setFrameMode(true);
if(isset($arResult["ITEMS"])
    && sizeof($arResult["ITEMS"])
    && is_array($arResult["ITEMS"])):
    ?>
    <div class="news-list news-list-area clearfix" id="news-list-area">
		<h1>Новости</h1>
        <?if($arParams["DISPLAY_TOP_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="news-item row clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <?php
                    $detail_link = (isset($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) && !empty($arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"]) ? $arItem["DISPLAY_PROPERTIES"]["OTHER_LINK"]["VALUE"] : $arItem["DETAIL_PAGE_URL"]);

                    ?>
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 news-list-description">
                    <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                        <a href="<?=$detail_link;?>" class="h3 newst-title">
                            <?echo $arItem["NAME"]?>
                        </a>
                    <?endif;?>
                    <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                        <span class="news-date-time">
			         <?echo $arItem["DISPLAY_ACTIVE_FROM"]?>
			     </span>
                    <?endif?>
                    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                        <a href="<?=$detail_link;?>" class="preview-text">

                            <?php   $preview_text = $arItem["PREVIEW_TEXT"];
                            $preview_text = preg_replace('~<a[^>]*?>(.*?)</a>~isu',"$1",$preview_text);
                            echo $preview_text;
                            ?>

                        </a>
                        <a href="<?=$detail_link;?>" class="btn btn-info btn-readmore">
                            <?php echo GetMessage('CT_READ_MORE'); ?>
                        </a>
                    <?endif;?>
                </div>
            </div>
        <?endforeach;?>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
<?php endif; ?>
