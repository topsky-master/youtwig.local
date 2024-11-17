<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if(isset($arResult["ITEMS"]) && is_array($arResult["ITEMS"]) && sizeof($arResult["ITEMS"])): ?>
<div class="pos-footer-banner">
	<div class="container">
		<div class="container-inner">
			<div class="pos-footer-static">
				<div class="footer-static row-fluid">
					<? $arParams["LINE_ELEMENT_COUNT"] = 3; ?>
					<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
					<?
					$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
					?>
					<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
					<div class="row">
					<?endif;?>
					<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
					<div class="block_static_top">
						<div class="banner-box banner-box1">
							<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
								<img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
							</a>
							<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
								<img src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
							</a> 
							<?endif?>
							<h3>
								<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
									<?=$arElement["NAME"]?>
								</a>
							</h3>
							<? if(isset($arElement["PREVIEW_TEXT"]) && !empty($arElement["PREVIEW_TEXT"])): ?>
							<p class="read_more">
								<? echo strip_tags(htmlspecialchars_decode($arElement["PREVIEW_TEXT"],ENT_QUOTES));?>
							</p>
							<? endif; ?>							
						</div>
					</div>					
					</div>
					<?if(($cell + 1)%$arParams["LINE_ELEMENT_COUNT"] == 0 || sizeof($arResult["ITEMS"]) == $cell + 1):?>
					</div>
					<?endif;?>
					
					<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>