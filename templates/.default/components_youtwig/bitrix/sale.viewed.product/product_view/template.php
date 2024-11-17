<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
	global $APPLICATION;
	$product_viewed_image_width 	= 65;
	$product_viewed_image_height 	= 65;
	$dir 							= $_SERVER['DOCUMENT_ROOT'].'/';
	$APPLICATION->RestartBuffer();
 
?>
<?if (count($arResult) > 0):?>
<div class="view-list clearfix">
	<?foreach($arResult as $arItem):?>
		<div class="view-item clearfix">
			<div class="inner-padding clearfix">
			<?if($arParams["VIEWED_IMAGE"]=="Y" && is_array($arItem["PICTURE"]) && isset($arItem["PICTURE"]["src"])):?>
			<?if($arParams["VIEWED_NAME"]=="Y" 
				|| ($arParams["VIEWED_PRICE"]=="Y" && $arItem["CAN_BUY"]=="Y")
				|| ($arParams["VIEWED_CANBUY"]=="Y" && $arItem["CAN_BUY"]=="Y")
				|| ($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y")
				):?>
			<div class="product-image col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<?else:?>
			<div class="product-image clearfix">	
			<?endif;?>		
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="product-image">
						
						<? 
						if(!empty($arItem["PICTURE"]["src"]) && function_exists('rectangleImage')):
							$arItem["PICTURE"]["src"]	= rectangleImage($dir.$arItem["PICTURE"]["src"],$product_viewed_image_width,$product_viewed_image_height,$arItem["PICTURE"]["src"]);
						endif;
						
						?>
						<img src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=htmlspecialchars($arItem["NAME"], ENT_HTML401, LANG_CHARSET);?>" />
					</a>
			</div>
			<?endif?>
			
			
			<?if($arParams["VIEWED_NAME"]=="Y" 
				|| ($arParams["VIEWED_PRICE"]=="Y" && $arItem["CAN_BUY"]=="Y")
				|| ($arParams["VIEWED_CANBUY"]=="Y" && $arItem["CAN_BUY"]=="Y")
				|| ($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y")
				):?>
				<div class="col-xs-12<? if($arParams["VIEWED_IMAGE"]=="Y" && is_array($arItem["PICTURE"]) && isset($arItem["PICTURE"]["src"])): ?> col-sm-8 col-md-8 col-lg-8<?else: ?> col-sm-12 col-md-12 col-lg-12<? endif; ?>">
				
					<?if($arParams["VIEWED_NAME"]=="Y"):?>
						<h3 class="product-name">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
								<?=$arItem["NAME"]?>
							</a>
						</h3>
					<?endif?>
					
					<?if($arParams["VIEWED_PRICE"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
						<div class="price-formated">
							<?=$arItem["PRICE_FORMATED"]?>
						</div>
					<?endif?>
					
					<?if($arParams["VIEWED_CANBUY"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
						<noindex>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" rel="nofollow" class="product-detail">
								<?=GetMessage("PRODUCT_BUY")?>
							</a>
						</noindex>
					<?endif?>
					
					<?if($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
						<noindex>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" rel="nofollow" class="product-basket">
								<?=GetMessage("PRODUCT_BUSKET")?>
							</a>
						</noindex>
					<?endif;?>
				
				</div>
			<?endif;?>
			</div>
		</div>
	<?endforeach;?>
</div>
<?endif;?>
<?php die(); ?>