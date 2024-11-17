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

?>
<form method="GET" class="search-amp-form" action="<?=$arParams["FORM_ACTION"];?>" target="_top">
	<div>
	   <input
            <?if($arParams["INPUT_SIZE"] > 0):?>
                size="<?echo $arParams["INPUT_SIZE"]?>"
            <?endif?>
            name="<?echo $arParams["NAME"]?>"
            id="<?echo $arResult["ID"]?>"
            value="<?echo $arParams["VALUE"]?>"
            class="search-suggest"
            type="text"
            autocomplete="off"
			placeholder="<?=GetMessage('TMPL_SEARCH');?>"
        />
    	<button type="submit" value="<?=GetMessage('TMPL_SEARCH');?>" class="ampstart-btn caps">
        	<i class="fa fa-search" aria-hidden="true"></i>
    	</button>
	</div>
</form>