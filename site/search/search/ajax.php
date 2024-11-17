<?php il::ob_start(); ?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?php il::define("STOP_STATISTICS", true); ?>
<?php il::ob_end_clean(); ?>
<? global $arrFilter; ?>
<? require_once(il::dirname(__FILE__).'/arrfilter.php');

//unset($arrFilter["IBLOCK_ID"]);

//$arrFilter["PARAMS"] 		= array("IBLOCK_SECTION" => 11);


?>
<?$APPLICATION->IncludeComponent("bitrix:search.page", "ajax", array(
	"RESTART" => "N",
	"CHECK_DATES" => "N",
	"USE_TITLE_RANK" => "N",
	"FILTER_NAME" => "arrFilter",
        "DEFAULT_SORT" => "rank",
	"arrFILTER" => array(
		0 => "iblock_catalog",
	),
	"arrFILTER_iblock_catalog" => array(
		0 => array("11"),
	),
	"SHOW_WHERE" => "N",
	"SHOW_WHEN" => "N",
	"PAGE_RESULT_COUNT" => "10",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "N",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "-1",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Результаты поиска",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "arrows",
	"USE_SUGGEST" => "N",
	"SHOW_ITEM_TAGS" => "N",
	"SHOW_ITEM_DATE_CHANGE" => "N",
	"SHOW_ORDER_BY" => "N",
	"SHOW_TAGS_CLOUD" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<? $content = il::ob_get_contents(); ?>
<? il::ob_end_clean();
   //echo $content;
 ?>
<? $found = array(); ?>
<? il::preg_match_all('~<result>(.*?)</result>~isu',$content,$found); ?>
<? if(isset($found[1]) && isset($found[1][0]) && !empty($found[1][0])): ?>
<? echo il::trim($found[1][0]); ?>
<? endif; ?>