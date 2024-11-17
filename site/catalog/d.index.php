<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Каталог товаров, twig, TWiG, запасные части, запчасти");
$APPLICATION->SetPageProperty("description", "Каталог запасных частей для бытовой техники в Интернет-магазине TWiG");
$APPLICATION->AddHeadString('<link rel="amphtml" href="'.(IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/sections/" />'));


$APPLICATION->SetTitle("Каталог товаров");
?>
<?

    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main-icons-section",
        Array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_DATE" => "N",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "FIELD_CODE" => array("ID","NAME","PREVIEW_PICTURE",""),
            "FILTER_NAME" => "",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "IBLOCK_ID" => 33,
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "999",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Новости",
            "PARENT_SECTION" => 712,
            "PARENT_SECTION_CODE" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "PROPERTY_CODE" => array("LINK",""),
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SORT_BY1" => "ACTIVE_FROM",
            "SORT_BY2" => "SORT",
            "SORT_ORDER1" => "DESC",
            "SORT_ORDER2" => "ASC",
            "STRICT_SECTION_CHECK" => "Y",
            "DISPLAY__LIST_TITLE" => "Разделы"
        )
    );

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list",
        "sections",
        array(
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => 33,
            "SECTION_ID" => 711,
            "SECTION_USER_FIELDS" => array("UF_ANOTHER_LINK"),
            "ADD_SECTIONS_CHAIN" => "N",
            "VIEW_MODE" => "LIST",
            "TOP_DEPTH" => 4
        )
    );


?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>