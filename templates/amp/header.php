<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
global $APPLICATION;
require_once(dirname(__FILE__)."/classes/templateConfig.php");
?><!doctype html>
<html amp>
<head>
	<link rel="preload" as="script" href="https://cdn.ampproject.org/v0.js" />
	<script async data-skip-moving="true" src="https://cdn.ampproject.org/v0.js"></script>
	<meta charset="utf-8">
	<link rel="preload" href="https://youtwig.ru/bitrix/templates/nmain/fonts/FontAwesome.woff" as=font type="font/woff" crossorigin="anonymous" />
	<?
	if(is_object($USER) && $USER->IsAdmin()){

		$oTwigMainPage = new twigMainPage();
    	$oTwigMainPage->ShowHeadStrings();
    	$oTwigMainPage->ShowHeadScripts();
    	$oTwigMainPage->ShowCSS();

	}

    ?>
	<?php $APPLICATION->ShowMeta("robots", false, true); ?>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style amp-custom>
        <?=file_get_contents($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/css/main.css");?>
        <?$APPLICATION->ShowViewContent("AMP_STYLE");?>
    </style>
    <script async custom-element="amp-analytics" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
    <script async custom-element="amp-sidebar" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-fit-text" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
    <script async custom-element="amp-form" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
	<?$APPLICATION->ShowViewContent("AMP_SCRIPTS");?>
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<title>
        <?$APPLICATION->ShowTitle()?>
    </title>
	<?$APPLICATION->ShowViewContent("CANONICAL_PROPERTY");?>
	<?  
		$APPLICATION->ShowMeta("description", false, true);
		$APPLICATION->ShowMeta("keywords", false, true);
	?>
</head>
<body id="smart_template" data-spy="scroll" data-target="<?php if($second_menu == ""): ?>#bs-example-navbar-collapse-1<?php else: ?>#bs-example-navbar-collapse-3<?php endif; ?>" data-offset="110" class="<?php if(IN_MAIN): ?> in_main<?php else: ?> other-page<?php endif; ?> <?echo $page_class; ?> <?echo $lang_id; ?> <?php echo SITE_ID; ?>">
<?php if(is_object($USER) && $USER->IsAdmin()){?>
    <div id="panel">
        <?$APPLICATION->ShowPanel();?>
    </div>
<?php }; ?>
<header>
    <div class="logotype">
        <?$APPLICATION->IncludeComponent(
            "bitrix:news.detail",
            "amplogo",
            Array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "18",
                "ELEMENT_ID" => "",
                "ELEMENT_CODE" => "logotype",
                "CHECK_DATES" => "Y",
                "FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"",2=>"",),
                "PROPERTY_CODE" => array(0=>"LINK",1=>"",),
                "IBLOCK_URL" => "",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "360000",
                "CACHE_GROUPS" => "Y",
                "META_KEYWORDS" => "",
                "META_DESCRIPTION" => "",
                "BROWSER_TITLE" => "",
                "SET_TITLE" => "N",
                "SET_STATUS_404" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "USE_PERMISSIONS" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Страница",
                "PAGER_TEMPLATE" => "",
                "PAGER_SHOW_ALL" => "Y",
                "DISPLAY_DATE" => "N",
                "DISPLAY_NAME" => "N",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "N",
                "USE_SHARE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "LOGO_LINK" => "/amp/"

            )
        );?>
    </div>
    <?$APPLICATION->IncludeComponent(
        "bitrix:search.suggest.input",
        "amp",
        array(
            "NAME" => "q",
            "VALUE" => trim($_REQUEST["q"]),
            "INPUT_SIZE" => 40,
			"FORM_ACTION" => "/amp/sections/"
        )
    );?> 
</header>
<main>
    