<?if(!defined('CATALOG_INCLUDED')) die(); ?>
<?php

global $NavNum; $NavNum = 0;
$APPLICATION->IncludeComponent(
    "bitrix:forum.topic.reviews",
    "ncomments",
    Array(
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "MESSAGES_PER_PAGE" => 10,
        "USE_CAPTCHA" => "N",
        "FORUM_ID" => 1,
        "SHOW_LINK_TO_FORUM" => "N",
        "ELEMENT_ID" => $arParams['ID'],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "SHOW_MINIMIZED" => "N",
        "AJAX_MODE" => "N",
        "AJAX_POST" => "N",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "FILES_COUNT" => "0",
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "PAGE_NAVIGATION_TEMPLATE" => "oldpager",
        "NAME_TEMPLATE" => "",
        "PREORDER" => "N",
        "RATING_TYPE" => "",
        "SHOW_AVATAR" => "Y",
        "SHOW_RATING" => "N",
        "URL_TEMPLATES_DETAIL" => "",
        "URL_TEMPLATES_PROFILE_VIEW" => "",
        "URL_TEMPLATES_READ" => "",
		'HAS_REVIEWS' => $arParams['HAS_REVIEWS']

    )
);



