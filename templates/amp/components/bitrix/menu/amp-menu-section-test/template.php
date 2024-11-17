<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul id="mu-menu">
	<li class="fbold">
		<input id="chk" type="checkbox" />
		<label for="chk"><?=GetMessage('TMPL_GO_SECTION');?></label>
		<ul> 
<?

$arResetMenu = array();

    foreach($arResult as $arItem):

        if($arItem["DEPTH_LEVEL"] == 1 || $arItem["DEPTH_LEVEL"] == 3) continue;

        if($arItem["DEPTH_LEVEL"] > 3){
            $arItem["DEPTH_LEVEL"] -= 1;
        }

        $arItem["DEPTH_LEVEL"] -= 1;

        $arResetMenu[] = $arItem;

    endforeach;

    $previousLevel = 0;
    $arResult = $arResetMenu;

    unset($arResetMenu);

$previousLevel = 0;
foreach($arResult as $iNum => $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li><input id="chk<?=$iNum;?>" type="checkbox" />
				<label for="chk<?=$iNum;?>"><?=$arItem["TEXT"]?></label>
				<ul class="sub-item">
					<li class="fbold"><a href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>"><?=GetMessage('TMPL_GO_SECTION');?></a></li>
		<?else:?>
			<li><input id="chk<?=$iNum;?>" type="checkbox" />
				<label for="chk<?=$iNum;?>"><?=$arItem["TEXT"]?></label>
				<ul>
					<li class="fbold"><a href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>"><?=GetMessage('TMPL_GO_SECTION');?></a></li>
		<?endif?>

	<?else:?>
	
		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li><input id="chk<?=$iNum;?>" type="checkbox" />
				<label for="chk<?=$iNum;?>"><?=$arItem["TEXT"]?></label>
				<ul>
					<li class="fbold"><a href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>"><?=GetMessage('TMPL_GO_SECTION');?></a></li>
				</ul>
			</li>
		<?else:?>
			<li><input id="chk<?=$iNum;?>" type="checkbox" />
				<label for="chk<?=$iNum;?>"><?=$arItem["TEXT"]?></label>
				<ul>
					<li class="fbold"><a href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>"><?=GetMessage('TMPL_GO_SECTION');?></a></li>
				</ul>
			</li>
		<?endif?>
		 
	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>
		</ul>	 
	</li>
</ul>
<?endif?>