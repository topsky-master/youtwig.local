<?

define("STOP_STATISTICS", true);
define("ADMIN_SECTION",false);
$_GET['dataType'] = 'Y';

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/nmain/lang/'.LANGUAGE_ID.'/header.php');

global $APPLICATION;

global $NavNum; $NavNum = 0;
$APPLICATION->IncludeComponent(
    "bitrix:forum.topic.reviews",
    "ncomments",
    Array(
		"CACHE_TYPE" => 'N',
        "CACHE_TIME" => '-1',
        "MESSAGES_PER_PAGE" => 10,
        "USE_CAPTCHA" => "N",
        "FORUM_ID" => $_REQUEST['FORUM_ID'],
        "SHOW_LINK_TO_FORUM" => "N",
        "ELEMENT_ID" => $_REQUEST['ELEMENT_ID'],
        "IBLOCK_ID" => $_REQUEST['IBLOCK_ID'],
        "SHOW_MINIMIZED" => "N",
        "AJAX_MODE" => "N",
        "AJAX_POST" => "Y",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "FILES_COUNT" => "0",
        "IBLOCK_TYPE" => "catalog",
        "PAGE_NAVIGATION_TEMPLATE" => "oldpager",
        "NAME_TEMPLATE" => "",
        "PREORDER" => "N",
        "RATING_TYPE" => "",
        "SHOW_AVATAR" => "Y",
        "SHOW_RATING" => "N",
        "URL_TEMPLATES_DETAIL" => "",
        "URL_TEMPLATES_PROFILE_VIEW" => "",
        "URL_TEMPLATES_READ" => "",

    )
);



