<?/*

$logoFilter = Array(
    "CODE" => "logotype",
    "ACTIVE" => "Y",
    'PROPERTY_DOMAIN_VALUE' => IMPEL_SERVER_NAME
);

$logoId = false;

$logoResDB = CIBlockElement::GetList(Array(), $logoFilter, false, false, array("ID"));

if($logoResDB){

    while($logoResArr = $logoResDB->Fetch()){

        if(
            isset($logoResArr['ID'])
            && !empty($logoResArr['ID'])
        ){

            $logoId = $logoResArr['ID'];

        };
    };
};

if (empty($logoId)) {

    $logoFilter = Array(
        "CODE" => "logotype",
        "ACTIVE" => "Y",
    );

    $logoResDB = CIBlockElement::GetList(Array(), $logoFilter, false, false, array("ID"));

    while($logoResArr = $logoResDB->Fetch()){

        if(
            isset($logoResArr['ID'])
            && !empty($logoResArr['ID'])
        ){

            $logoId = $logoResArr['ID'];

        };
    };
}


*/?>
<!DOCTYPE html>
<html lang="ru" data-time="<?php echo trim(strftime('%k')); ?>">
<head>
	<script>
	String.prototype.mb_split = function (separator, limit) {
		return this.split(separator, limit);
	};

	String.prototype.mb_substr = function (start, length) {
		return this.substr(start, length);
	};

	</script>
    <script data-skip-moving="true"  src="<?php echo SITE_TEMPLATE_PATH; ?>/js/jquery.min.js" type="text/javascript"></script>
	<link rel="preload" href="<?php echo SITE_TEMPLATE_PATH; ?>/fonts/FontAwesome.woff" as="font" type="font/woff" crossorigin="anonymous" />
	<link rel="preload" href="/local/templates/nmain/fonts/glyphicons-halflings-regular.woff" as="font" type="font/woff" crossorigin="anonymous" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?$APPLICATION->ShowTitle()?></title>
    <meta name='yandex-verification' content='4ed89756b1a3512c' />
    <meta name="yandex-verification" content="949cd2a7e179152b" />
    <meta name="google-site-verification" content="LKadqUVVCy5GFZFaopWIV9Yf6dwpxyjTdphrWeymslA" />
    <meta name='wmail-verification' content='1da92fcdb7eabca1' />
    <meta name="msvalidate.01" content="2F744F87BA88F923E30BA8C702A39A0D" />
    <meta name="wot-verification" content="8ebff69048ecb1ba29e3"/>
    <meta name="cypr-verification" content="9560fa408865797f3b0ff2f5a5052e5f"/>
    <meta name="mailru-verification" content="d3ca87c1d94cea05" />
    <meta name="majestic-site-verification" content="MJ12_e54747bd-edc9-4905-81f4-3b020848f5c0">
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@TWiG_News">
    <meta name="twitter:title" content="<?$APPLICATION->ShowTitle()?>">
    <meta property="og:title" content="<?$APPLICATION->ShowTitle()?>"/>
    <meta property="og:description" content="<?=$APPLICATION->AddBufferContent("GetOgDescription");?>" />
    <meta property="og:image" content="<?=$APPLICATION->AddBufferContent("GetOgImage");?>">
    <meta property="og:type" content="website"/>
    <meta property="og:url" content= "<?=$APPLICATION->AddBufferContent("GetOgUrl");?>" />
	
	<?

    $APPLICATION->ShowMeta("robots", false, $bXhtmlStyle);
    $APPLICATION->ShowMeta("keywords", false, $bXhtmlStyle);
    $APPLICATION->ShowMeta("description", false, $bXhtmlStyle);

    $APPLICATION->ShowHeadStrings();
    $APPLICATION->ShowHeadScripts();
    $APPLICATION->ShowCSS();

	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/device.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/bootstrap-select.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/language/defaults-en.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/language/defaults-ru.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/main.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.cookie.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/kick.index.js");

    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap_select/bootstrap-select.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/main.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/mediaquery.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
	//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-theme.min.css");

    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/font-awesome.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/font-awesome-animation.min.css");

    if($cssPath){
        $APPLICATION->SetAdditionalCSS($cssPath);
    }

    ?>
</head>
<body id="page" class="pos_page <?=$page_class;?>">

<?php if(is_object($USER) && $USER->IsAdmin()){?>
    <div id="panel">
        <?$APPLICATION->ShowPanel();?>
    </div>
<?php }; ?>
<div class="page">
    <header>
        <div class="container">
            <?/*$APPLICATION->IncludeComponent(
                "bitrix:news.detail",
                "nlogotype",
                Array(
                    "IBLOCK_TYPE" => "catalog",
                    "IBLOCK_ID" => "18",
                    "ELEMENT_ID" => $logoId,
                    "ELEMENT_CODE" => "",
                    "CHECK_DATES" => "Y",
                    "FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"PREVIEW_TEXT",2=>"",),
                    "PROPERTY_CODE" => array(0=>"LINK",1=>"",),
                    "IBLOCK_URL" => "",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600000",
                    "CACHE_GROUPS" => "Y",
                    "META_KEYWORDS" => "-",
                    "META_DESCRIPTION" => "-",
                    "BROWSER_TITLE" => "-",
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
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "USE_SHARE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => ""
                )
            );*/?>
            <?
                $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_DIR."include/".IMPEL_SERVER_NAME."/logo.php",
                    "EDIT_TEMPLATE" => "standard.php"
                ),
                    false
                );
            ?>
             
        </div>
    </header>
</div>
<div id="columns">
    <div class="container">