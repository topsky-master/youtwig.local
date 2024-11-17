<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

ob_start();

$APPLICATION->IncludeComponent(
    "bitrix:forum.topic.reviews",
    "ampcommentajax",
    Array(
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "360000",
        "MESSAGES_PER_PAGE" => 15,
        "USE_CAPTCHA" => "Y",
        "FORUM_ID" => 1,
        "SHOW_LINK_TO_FORUM" => "N",
        "ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
        "IBLOCK_ID" => 11,
        "SHOW_MINIMIZED" => "N",
        "AJAX_MODE" => "N",
		"NO_REDIRECT_AFTER_SUBMIT" => "Y",
        "AJAX_POST" => "N",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "FILES_COUNT" => "2",
        "IBLOCK_TYPE" => "catalog",
        "PAGE_NAVIGATION_TEMPLATE" => "oldpager",
        "NAME_TEMPLATE" => "",
        "PREORDER" => "Y",
        "RATING_TYPE" => "",
        "SHOW_AVATAR" => "Y",
        "SHOW_LINK_TO_FORUM" => "N",
        "SHOW_MINIMIZED" => "N",
        "SHOW_RATING" => "N",
        "URL_TEMPLATES_DETAIL" => "",
        "URL_TEMPLATES_PROFILE_VIEW" => "",
        "URL_TEMPLATES_READ" => ""
    )
);

$reviewsContent = ob_get_clean();
$reviewsContent = preg_replace('~<script[^>]*?>.*?</script>~isu','',$reviewsContent);
$reviewsContent = preg_replace('~<script[^>]*?>~isu','',$reviewsContent);

header('HTTP/1.1 200 OK');
header("access-control-allow-credentials:true");
header("AMP-Same-Origin: true");
header("Access-Control-Allow-Origin: ". (CMain::IsHTTPS() ? 'https' : 'http') .'://'. preg_replace('~\:.*?$~is', '',$_SERVER['HTTP_HOST']) . "");
header("amp-access-control-allow-source-origin: ". (CMain::IsHTTPS() ? 'https' : 'http') .'://'.  preg_replace('~\:.*?$~is', '',$_SERVER['HTTP_HOST']) . "");
header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
header("access-control-allow-methods:POST, GET, OPTIONS");
header("Content-Type: application/json");

echo $reviewsContent;
die();
