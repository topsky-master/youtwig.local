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
$this->setFrameMode(true);
?>
<?

            global $APPLICATION, $arrFilter;

            if(!is_array($arrFilter)):
                $arrFilter = array();
            endif;

            $howSort = array(
                "sort:asc" => GetMessage("SORT_SORT_ASC"),
                "sort:desc" => GetMessage("SORT_SORT_DESC"),
                "name:asc" => GetMessage("SORT_NAME_ASC"),
                "name:desc" => GetMessage("SORT_NAME_DESC"),
                "catalog_QUANTITY:asc" => GetMessage("SORT_QUANTITY_ASC"),
                "catalog_QUANTITY:desc" => GetMessage("SORT_QUANTITY_DESC"),
                "show_counter:asc" => GetMessage("SORT_SHOW_COUNTER_ASC"),
                "show_counter:desc" => GetMessage("SORT_SHOW_COUNTER_DESC"),
                "created_date:asc" => GetMessage("SORT_CREATED_ASC"),
                "created_date:desc" => GetMessage("SORT_CREATED_DESC"),
                "HAS_PREVIEW_PICTURE:asc" => GetMessage("SORT_PREVIEW_PICTURE_ASC"),
                "HAS_PREVIEW_PICTURE:desc" => GetMessage("SORT_PREVIEW_PICTURE_DESC")
            );

            if(isset($arParams["PRICE_CODE"])
                && !empty($arParams["PRICE_CODE"])){

                $obCache = new CPHPCache;
                $cacheID = 'catalog_price_code';


                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $catalog_price_code = $tmp[$cacheID];
                    }

                    foreach ($catalog_price_code as $ar_res){
                        $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                        $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                    }

                } else {

                    foreach ($arParams["PRICE_CODE"] as $price_name){

                        $db_res = CCatalogGroup::GetList(
                            array(
                                "SORT" =>"ASC"
                            ),
                            array(
                                "NAME" => $price_name
                            ),
                            false,
                            false,
                            array("ID")
                        );

                        $catalog_price_code = array();

                        if(is_object($db_res)){
                            while ($ar_res = $db_res->Fetch()){
                                $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                                $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                                $catalog_price_code[] = $ar_res["ID"];
                            }
                        }

                    }


                    if($obCache->StartDataCache()){

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $catalog_price_code
                            )
                        );

                    };

                };

            };

            $sort_values = array_keys($howSort);

            $sort_code_param = 'sort:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $sort_code = ((isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) ? (urldecode($_REQUEST['sort'])) : ($_SESSION[$sort_code_param]));


            if(empty($sort_code) && (($APPLICATION->get_cookie($sort_code_param)))){
                $sort_code = $APPLICATION->get_cookie($sort_code_param);
            }

            if(!(!empty($sort_code) && (in_array($sort_code,$sort_values)))){
                $sort_code = "sort:asc";
            }

            $_SESSION[$sort_code_param] = $sort_code;
            $APPLICATION->set_cookie($sort_code_param,$sort_code);


            if(!empty($sort_code) && in_array($sort_code,$sort_values)){

                list($arParams["ELEMENT_SORT_FIELD"],$arParams["ELEMENT_SORT_ORDER"]) = explode(":",$sort_code);
                list($arParams["ELEMENT_SORT_FIELD2"],$arParams["ELEMENT_SORT_ORDER2"]) = explode(":",$sort_code);

            }

            $element_count_param = 'PAGE_ELEMENT_COUNT:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $element_count = $_REQUEST['PAGE_ELEMENT_COUNT'];

            $pager = array(
                0 =>15,
                1 =>30,
                2 =>90);


            if(empty($element_count) && (($APPLICATION->get_cookie($element_count_param)))){
                $element_count = $APPLICATION->get_cookie($element_count_param);
            }

            $element_count = (int)$element_count;
            $element_count = !in_array($element_count,$pager) ? 15 : $element_count;
            $element_count = empty($element_count) ? 15 : $element_count;

            $arParams["PAGE_ELEMENT_COUNT"] = $element_count;
            $APPLICATION->set_cookie($element_count_param,$element_count);

            if(isset($_REQUEST['LIST_TYPE']) && !empty($_REQUEST['LIST_TYPE'])){

                $list_type = isset($_REQUEST['LIST_TYPE']) && in_array((string)$_REQUEST['LIST_TYPE'],array('GRID','LIST')) ? trim($_REQUEST['LIST_TYPE']) : $arParams['LIST_TYPE'];
                $_SESSION['LIST_TYPE'] = $list_type;
                $APPLICATION->set_cookie('LIST_TYPE',$list_type);

            } else {

                $list_type = $APPLICATION->get_cookie('LIST_TYPE');
                $list_type = !empty($list_type) && in_array((string)$list_type,array('GRID','LIST')) ? trim($list_type) : '';

                if(empty($list_type) && !empty($_SESSION['LIST_TYPE']) && in_array((string)$_SESSION['LIST_TYPE'],array('GRID','LIST'))){
                    $list_type = $_SESSION['LIST_TYPE'];
                };

                if(empty($list_type)){
                    $list_type = $arParams['LIST_TYPE'];
                };

            };

$arElements = $APPLICATION->IncludeComponent(
    "bitrix:search.page",
    ".default",
    Array(
        "RESTART" => "Y",
        "NO_WORD_LOGIC" => "Y",
        "USE_LANGUAGE_GUESS" => "N",
        "CHECK_DATES" => $arParams["CHECK_DATES"],
        "arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
        "arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
        "USE_TITLE_RANK" => "N",
        "DEFAULT_SORT" => "rank",
        "FILTER_NAME" => "arrFilter",
        "SHOW_WHERE" => "N",
        "arrWHERE" => array(),
        "SHOW_WHEN" => "N",
        "PAGE_RESULT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"] * 6,
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "N",
    ),
    $component,
    array('HIDE_ICONS' => 'Y')
);
if (!empty($arElements) && is_array($arElements))
{
    		global $searchFilter;
    		$searchFilter = array(
    			"=ID" => $arElements,
    		);


            if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){

                $APPLICATION->RestartBuffer();

            }
?>
<div id="comp_smart_filter">
<?

			global $NavNum; $NavNum = 0;
			$APPLICATION->IncludeComponent(
    		"bitrix:catalog.section",
    		".default",
    		array(
    			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
    			"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
    			"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
    			"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
    			"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
    			"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
    			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
    			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
    			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
    			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
    			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
    			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
    			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
    			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
    			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
    			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
    			"SECTION_URL" => $arParams["SECTION_URL"],
    			"DETAIL_URL" => $arParams["DETAIL_URL"],
    			"BASKET_URL" => $arParams["BASKET_URL"],
    			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
    			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
    			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
    			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
    			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
    			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
    			"CACHE_TIME" => $arParams["CACHE_TIME"],
    			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
    			"PRICE_CODE" => $arParams["PRICE_CODE"],
    			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
    			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
    			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
    			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
    			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
    			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
    			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
    			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
    			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
    			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
    			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
    			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
    			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
    			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
    			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
    			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
    			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
    			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
    			"FILTER_NAME" => "searchFilter",
    			"SECTION_ID" => "",
    			"SECTION_CODE" => "",
    			"SECTION_USER_FIELDS" => array(),
    			"INCLUDE_SUBSECTIONS" => "Y",
    			"SHOW_ALL_WO_SECTION" => "Y",
    			"META_KEYWORDS" => "",
    			"META_DESCRIPTION" => "",
    			"BROWSER_TITLE" => "",
    			"ADD_SECTIONS_CHAIN" => "N",
    			"SET_TITLE" => "N",
    			"SET_STATUS_404" => "N",
    			"CACHE_FILTER" => "N",
    			"CACHE_GROUPS" => "N",

    			'LABEL_PROP' => $arParams['LABEL_PROP'],
    			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
    			'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

    			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
    			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
    			'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    			'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
    			'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
    			'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
    			'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
    			'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

    			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    			'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
    			'SHOW_CLOSE_POPUP' => (isset($arParams['SHOW_CLOSE_POPUP']) ? $arParams['SHOW_CLOSE_POPUP'] : ''),
    			'COMPARE_PATH' => $arParams['COMPARE_PATH'],
                'LIST_TYPE' => $list_type
    		),
    		$arResult["THEME_COMPONENT"],
    		array('HIDE_ICONS' => 'Y')
    	);
?>
</div>
<?

        if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){
            die();
        }

}
elseif (is_array($arElements))
{
    echo GetMessage("CT_BCSE_NOT_FOUND");
}
?>