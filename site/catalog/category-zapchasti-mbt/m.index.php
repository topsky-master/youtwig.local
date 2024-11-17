<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Интернет магазин TWiG");
?><?
global $arrFilter;
//$arrFilter = Array("SECTION_ID" => Array(25),);

?>
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog",
    "ncatalog",
    Array(
        "ACTION_VARIABLE" => "action",
        "ADD_ELEMENT_CHAIN" => "Y",
        "ADD_PICT_PROP" => "-",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "ALSO_BUY_ELEMENT_COUNT" => "3",
        "ALSO_BUY_MIN_BUYES" => "2",
        "BASKET_URL" => "/personal/cart/",
        "BIG_DATA_RCM_TYPE" => "bestsell",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "COMMON_ADD_TO_BASKET_ACTION" => "",
        "COMMON_SHOW_CLOSE_POPUP" => "Y",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "CONVERT_CURRENCY" => "Y",
        "CURRENCY_ID" => "RUB",
        "DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
        "DETAIL_ADD_TO_BASKET_ACTION" => array("ADD"),
        "DETAIL_BACKGROUND_IMAGE" => "-",
        "DETAIL_BRAND_USE" => "N",
        "DETAIL_BROWSER_TITLE" => "TITLE",
        "DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
        "DETAIL_DETAIL_PICTURE_MODE" => "IMG",
        "DETAIL_DISPLAY_NAME" => "Y",
        "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
        "DETAIL_META_DESCRIPTION" => "DESCRIPTION",
        "DETAIL_META_KEYWORDS" => "KEYWORDS",
       "DETAIL_PROPERTY_CODE" => array(
			0 => "VNUNTRENNIY_DIAMETR",
			1 => "VNESHNIY_DIAMETR",
			2 => "SHIRINA",
			3 => "SEO_TEXT",
			4 => "VNUTRENNIY_KVADRAT",
			5 => "VISOTA",
			6 => "DIAMETR",
			7 => "WHEEL_DIAMETR",
			8 => "DLINNA",
			9 => "ARTNUMBER",
			10 => "NUMBER_OF_CONTACTS",
			11 => "KOLICHESTVO_ZUBEV",
			12 => "COM_BLACK",
			13 => "KOMPLEKT",
			14 => "MATERIAL",
			15 => "POWER",
			16 => "VOLTAGE",
			17 => "NEWPRODUCT",
			18 => "TURNS",
			19 => "OBSHIY_RAZMER",
			20 => "VOLUME",
			21 => "HOLE",
			22 => "COVERING",
			23 => "MANUFACTURER_DETAIL",
			24 => "PLACE_OF_CONTACTS",
			25 => "LINKED_ELEMETS",
			26 => "RESISTANCE",
			27 => "AMPERAGE",
			28 => "MODEL_HTML",
			29 => "OLD_PRICE",
			30 => "COUNTRY",
			31 => "TYPE_OF_MOUNT",
			32 => "TYPE_OF_PROFILE",
			33 => "TYPE_OF_BELT",
			34 => "TOLSHCINA",
			35 => "ANGLE",
			36 => "COLOR",
			37 => "TYPE_OF_BORE",
			38 => "WARRANTY",
			39 => "PURPOSE_OF_NOZZLE",
			40 => "FEATURES",
			41 => "",
		),
        "DETAIL_SET_CANONICAL_URL" => "Y",
        "DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
        "DETAIL_SHOW_BASIS_PRICE" => "Y",
        "DETAIL_SHOW_MAX_QUANTITY" => "N",
        "DETAIL_USE_COMMENTS" => "N",
        "DETAIL_USE_VOTE_RATING" => "Y",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_CODES_HORIZONTAL" => "-",
        "DISPLAY_CODES_VERTICAL" => "TYPEPRODUCT,MANUFACTURER,ONSTOCK",
        "FILTER_PROPERTY_CODE" => array(
            0 => "TYPEPRODUCT",
            1 => "MANUFACTURER",
            2 => "MODEL",
            3 => "VNUNTRENNIY_DIAMETR",
            4 => "TYPE_OF_FABRIC",
            5 => "PLACE_OF_CONTACTS",
            6 => "TYPE_OF_MOUNT",
            7 => "POWER",
            8 => "TYPE_OF_PROFILE",
            9 => "KOLICHESTVO_ZUBEV",
            10 => "SHIRINA",
            11 => "DLINNA",
            12 => "VISOTA",
            13 => "VNESHNIY_DIAMETR",
            14 => "DIAMETR",
            15 => "COLOR",
            16 => "QUALITY",
            17 => "COUNTRY",
            18 => "RESISTANCE",
            19 => "HOLE",
            20 => "ANGLE",
            21 => "NUMBER_OF_CONTACTS",
            22 => "TYPE_OF_BELT"
        ),
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENTS_COLLAPSE" => "10",
        "ELEMENT_SORT_FIELD" => "show_counter",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FIELDS" => array(0=>"",1=>"",),
        "FILTER_FIELD_CODE" => array("ID","PREVIEW_PICTURE","DETAIL_PICTURE",""),
        "FILTER_NAME" => "arrFilter",
        "FILTER_PRICE_CODE" => array("Розничная"),
        "FILTER_PROPERTY_CODE" => array(
            0 => "TYPEPRODUCT",
            1 => "MANUFACTURER",
            2 => "MODEL",
            3 => "VNUNTRENNIY_DIAMETR",
            4 => "TYPE_OF_FABRIC",
            5 => "PLACE_OF_CONTACTS",
            6 => "TYPE_OF_MOUNT",
            7 => "POWER",
            8 => "TYPE_OF_PROFILE",
            9 => "KOLICHESTVO_ZUBEV",
            10 => "SHIRINA",
            11 => "DLINNA",
            12 => "VISOTA",
            13 => "VNESHNIY_DIAMETR",
            14 => "DIAMETR",
            15 => "COLOR",
            16 => "QUALITY",
            17 => "COUNTRY",
            18 => "RESISTANCE",
            19 => "HOLE",
            20 => "ANGLE",
            21 => "NUMBER_OF_CONTACTS"
        ),
        "FILTER_VIEW_MODE" => "VERTICAL",
        "GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
        "GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_MESS_BTN_BUY" => "Выбрать",
        "GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
        "GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
        "GIFTS_SHOW_IMAGE" => "Y",
        "GIFTS_SHOW_NAME" => "Y",
        "GIFTS_SHOW_OLD_PRICE" => "Y",
        "HIDE_NOT_AVAILABLE" => "Y",
        "IBLOCK_ID" => "11",
        "IBLOCK_TYPE" => "catalog",
        "INCLUDE_SUBSECTIONS" => "Y",
        "INSTANT_RELOAD" => "Y",
        "LABEL_PROP" => "-",
        "LINE_ELEMENT_COUNT" => "3",
        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        "LINK_IBLOCK_ID" => "",
        "LINK_IBLOCK_TYPE" => "",
        "LINK_PROPERTY_SID" => "",
        "LIST_BROWSER_TITLE" => "UF_TITLE",
        "LIST_META_DESCRIPTION" => "UF_DESCP",
        "LIST_META_KEYWORDS" => "UF_KEYWORDS",
        "LIST_PROPERTY_CODE" => array("NEWPRODUCT","SALELEADER","SPECIALOFFER","ARTNUMBER","OLD_PRICE"),
        "LIST_TYPE" => "LIST",
        "MAIN_TITLE" => "Наличие на складах",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_COMPARE" => "Сравнение",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "pager",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "15",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => array(0=>"Розничная",),
        "PRICE_VAT_INCLUDE" => "N",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "QUANTITY_FLOAT" => "N",
        "SECTIONS_SHOW_PARENT_NAME" => "Y",
        "SECTIONS_VIEW_MODE" => "LIST",
        "SECTION_ADD_TO_BASKET_ACTION" => "ADD",
        "SECTION_BACKGROUND_IMAGE" => "-",
        "SECTION_COUNT_ELEMENTS" => "N",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_TOP_DEPTH" => "2",
        "SEF_FOLDER" => "/catalog/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => Array("compare"=>"compare/","element"=>"#SECTION_CODE_PATH#/#ELEMENT_CODE#/","section"=>"#SECTION_CODE_PATH#/","sections"=>"","smart_filter"=>"#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/"),
        "SET_LAST_MODIFIED" => "N",
        "SET_STATUS_404" => "Y",
        "SET_TITLE" => "Y",
        "SHOW_404" => "Y",
        "SHOW_DEACTIVATED" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_EMPTY_STORE" => "Y",
        "SHOW_GENERAL_STORE_INFORMATION" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_TOP_ELEMENTS" => "N",
        "SIDEBAR_DETAIL_SHOW" => "Y",
        "SIDEBAR_SECTION_SHOW" => "Y",
        "STORES" => array(0=>"3",),
        "STORE_PATH" => "/store/#store_id#",
        "TEMPLATE_THEME" => "blue",
        "TOP_ADD_TO_BASKET_ACTION" => "ADD",
        "TOP_VIEW_MODE" => "GRID",
        "USER_FIELDS" => array(0=>"",1=>"",),
        "USE_ALSO_BUY" => "Y",
        "USE_BIG_DATA" => "Y",
        "USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
        "USE_COMPARE" => "N",
        "USE_ELEMENT_COUNTER" => "Y",
        "USE_FILTER" => "Y",
        "USE_GIFTS_DETAIL" => "Y",
        "USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
        "USE_GIFTS_SECTION" => "Y",
        "USE_MAIN_ELEMENT_SECTION" => "Y",
        "USE_MIN_AMOUNT" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "Y",
        "USE_REVIEW" => "N",
        "USE_SALE_BESTSELLERS" => "Y",
        "USE_STORE" => "N",
        "USE_STORE_PHONE" => "Y",
        "USE_STORE_SCHEDULE" => "Y",
        "NOT_MUCH" => 5,
    )
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>