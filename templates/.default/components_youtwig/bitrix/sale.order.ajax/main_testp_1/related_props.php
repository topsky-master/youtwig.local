<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");

$style = (is_array($arResult["ORDER_PROP"]["RELATED"]) && count($arResult["ORDER_PROP"]["RELATED"])) ? "" : "display:none";

?>
<?php if(sizeof($arResult["ORDER_PROP"]["RELATED"])): ?>
<div class="bx_section" style="<?=$style?>">
	<h4><?=GetMessage("SOA_TEMPL_RELATED_PROPS")?></h4>
	<br />
	<?=PrintPropsForm($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"], $counter, $arResult, array(), $delivery_name, $paysystem_name);?>
</div>
<?php endif; ?>