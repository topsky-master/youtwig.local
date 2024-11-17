<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Инструкции к технике");
$APPLICATION->SetPageProperty("description", "Инструкции к технике");
$APPLICATION->SetTitle("Инструкции к технике");

global $arrFilter;

$eorder 		 = (isset($_REQUEST['SORT_ORDER1']) && il::in_array($_REQUEST['SORT_ORDER1'],array('ASC','DESC'))) ? $_REQUEST['SORT_ORDER1'] : 'ASC';
$eproperties 	 = array("model", "instruction", "products", "manufacturer", "type_of_product");
$sproperties 	 = array("PROPERTY_model", "PROPERTY_instruction", "PROPERTY_products", "PROPERTY_manufacturer","PROPERTY_type_of_product");
$esort			 = (isset($_REQUEST['SORT_BY1']) && il::in_array($_REQUEST['SORT_BY1'],$sproperties)) ? ($_REQUEST['SORT_BY1']) : 'ACTIVE_FROM';
$manufacturer    = isset($_REQUEST['MANUFACTURER']) && !empty($_REQUEST['MANUFACTURER']) ? $_REQUEST['MANUFACTURER'] : '';
$type_of_product = isset($_REQUEST['TYPE_OF_PRODUCT']) && !empty($_REQUEST['TYPE_OF_PRODUCT']) ? $_REQUEST['TYPE_OF_PRODUCT'] : '';

if(!empty($manufacturer)){
    $arrFilter['PROPERTY_MANUFACTURER'] = $manufacturer;
}

if(!empty($type_of_product)){
    $arrFilter['PROPERTY_TYPE_OF_PRODUCT'] = $type_of_product;
}

?>
<?$APPLICATION->IncludeComponent(
    "impel:instructions",
    "",
    Array(
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "BLOCK_TITLE" => "Выберите интересующий вас тип товара или производитель",
        "MANUFACTURER" => $manufacturer,
        "TYPE_OF_PRODUCT" => $type_of_product
    )
);?>
<? if(empty($arrFilter)): ?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert"> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?=GetMessage('TMPL_CHOOSE_FILTERS_FOR_INSTRUCTIONS');?>
    </div>
<? else: ?>
<? global $NavNum; $NavNum = 0; ?>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "instructions",
    array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "-",
        "IBLOCK_ID" => "17",
        "NEWS_COUNT" => "50",
        "SORT_BY1" => $esort,
        "SORT_ORDER1" => $eorder,
        "SORT_BY2" => $esort,
        "SORT_ORDER2" => $eorder,
        "FILTER_NAME" => "arrFilter",
        "FIELD_CODE" => array(
            0 => "NAME",
            1 => "",
        ),
        "PROPERTY_CODE" => $eproperties,
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_META_DESCRIPTION" => "Y",
        "SET_STATUS_404" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "-1",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "PAGER_TEMPLATE" => "pager",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Новости",
        "PAGER_SHOW_ALWAYS" => "Y",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "Y",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => ""
    ),
    false
);?>
<? endif; ?>
<?$APPLICATION->IncludeComponent(
	"impel:meta",
	"",
	Array(
		"AREA_ID" => "",
		"BLOCK_COLUMNS" => "",
		"BLOCK_TITLE" => "",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
 
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>