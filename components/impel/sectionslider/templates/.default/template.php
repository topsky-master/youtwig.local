<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); $iNum = 0; ?>
<?php 
if(count($arResult['items']) > 0): ?>
	<div class="b-crumbs">
		<div class="snap-slider">
			<div class="snap-slider__slider">
				<?php foreach($arResult['items'] as $slName => $slHref){ ?>
				<div class="snap-slider__item<?php if($arResult['current_uri'] == $slHref): ?> active__item<?php endif; ?>">
					<a href="<?php if($arResult['current_uri'] == $slHref){ echo $arResult['clear_uri']; } else { echo $slHref; }?>" class="b-crumbs__link">
						<?php echo $slName; ?>
					</a>
				</div>
				       
				<?php } ?>
			</div>
		</div>
	</div>
<?php endif;?>