<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;

use Api\Seo;
Seo::redirectToEndPageNav();

IncludeTemplateLangFile(__FILE__);
require_once $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH.'/template_config.php';
?>
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
    <link rel="preload" href="<?php echo SITE_TEMPLATE_PATH; ?>/fonts/FontAwesome.woff2" as="font" type="font/woff2" crossorigin="anonymous" />
    <link rel="preload" href="<?php echo SITE_TEMPLATE_PATH; ?>/fonts/glyphicons-halflings-regular.woff" as="font" type="font/woff" crossorigin="anonymous" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?$APPLICATION->ShowTitle()?></title>
    <meta name='yandex-verification' content="4ed89756b1a3512c" />
    <meta name="yandex-verification" content="949cd2a7e179152b" />
    <meta name="yandex-verification" content="43c4e5c98cd0d2a1" />
    <meta name="yandex-verification" content="6742e5b344147f67" />
    <meta name="google-site-verification" content="LKadqUVVCy5GFZFaopWIV9Yf6dwpxyjTdphrWeymslA" />
    <meta name='wmail-verification' content='1da92fcdb7eabca1' />
    <meta name="msvalidate.01" content="2F744F87BA88F923E30BA8C702A39A0D" />
    <meta name="wot-verification" content="8ebff69048ecb1ba29e3"/>
    <meta name="cypr-verification" content="9560fa408865797f3b0ff2f5a5052e5f"/>
    <meta name="mailru-verification" content="d3ca87c1d94cea05" />
    <meta name="facebook-domain-verification" content="bvzdkgjlzeszg6rjt2inm7yty0ukti" />
    <meta name="majestic-site-verification" content="MJ12_e54747bd-edc9-4905-81f4-3b020848f5c0">
    <meta name="msapplication-config" content="<?=SITE_TEMPLATE_PATH?>/favicon/browserconfig.xml" />
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?=SITE_TEMPLATE_PATH?>/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=SITE_TEMPLATE_PATH?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=SITE_TEMPLATE_PATH?>/favicon/favicon-16x16.png">
    <link rel="manifest" href="https://youtwig.ru/local/templates/nmain/favicon/manifest.json">
    <link rel="mask-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@TWiG_News">
    <meta name="twitter:title" content="<?$APPLICATION->ShowTitle()?>">
    <meta property="og:title" content="<?$APPLICATION->ShowTitle()?>"/>
    <?/*<meta property="og:description" content="<?=$APPLICATION->AddBufferContent("GetOgDescription");?>" />*/?>
    <meta property="og:image" content="<?=$APPLICATION->AddBufferContent("GetOgImage");?>">
    <meta property="og:type" content="website"/>
    <meta property="og:url" content= "<?=$APPLICATION->AddBufferContent("GetOgUrl");?>" />
    <script type='application/ld+json'>
        {
            "@context": "http://www.schema.org",
            "@type": "WPHeader",
            "name": "<?$APPLICATION->ShowTitle()?>",
      "description": "<?=$APPLICATION->AddBufferContent("GetOgDescription");?>",
      "inLanguage": "ru",
      "image": {
          "@type": "ImageObject",
          "image": "https://youtwig.ru/upload/iblock/068/0684d5ef70dba50bcac1a16f90f6bd1d.png"
      }
  }
    </script>
    <script src="//code.jivo.ru/widget/qllQjdC8le" async></script>
    <?php if (false): ?>
        <script data-skip-moving='true' async src='https://antisovetnic.ru/anti/65316c28245a278857cc37b933291969'></script>
    <?php endif; ?>
    <script async src='https://www.google.com/recaptcha/api.js'></script>

    <?

    $APPLICATION->ShowMeta("robots", false, $bXhtmlStyle);
    //$APPLICATION->ShowMeta("keywords", false, $bXhtmlStyle);
    $APPLICATION->ShowMeta("description", false, $bXhtmlStyle);

    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/bootstrap-select.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/language/defaults-en.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap_select/language/defaults-ru.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/main.min.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.cookie.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/kick.index.js");
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/pwa.js");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap_select/bootstrap-select.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/main.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/mediaquery.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-theme.min.css");

    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/font-awesome.min.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/font-awesome-animation.min.css");
    $APPLICATION->SetAdditionalCSS("/bitrix/templates/.default/components/bitrix/news.list/nmobile-menu/style.min.css");
    
    // $canonicalUrl = getCanonicalUrl();
    // var_dump($canonicalUrl);
    // if ($canonicalUrl !== null) {
    //     $APPLICATION->AddHeadString('<link rel="canonical" href="' . $canonicalUrl . '">', true);
    // }

    if($cssPath){
        $APPLICATION->SetAdditionalCSS($cssPath);
    }

    $oTwigMainPage = new twigMainPage();
    $oTwigMainPage->ShowHeadStrings();
    $oTwigMainPage->ShowHeadScripts();
    $oTwigMainPage->ShowCSS();

    ?>
</head>
<body id="page" class="pos_page <?=$page_class;?>" itemscope itemtype="http://schema.org/WebPage">
<?php if(is_object($USER) && $USER->IsAdmin()){?>
    <div id="panel">
        <?$APPLICATION->ShowPanel();?>
    </div>
<?php }; ?>
<div class="page">
    <header>
        <div class="container">
            <div class="col-md-6 col-sm-12 lw">
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
            <div class="col-md-6 col-sm-12 mcw">
                <div class="col-md-6 mw">
                    <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "nlist",
                            array(
                                "ROOT_MENU_TYPE" => "personal-question",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        );
                    ?>
                </div>
                <div class="col-md-6 cw">
                    <?$APPLICATION->IncludeComponent("twofingers:location", "head", Array(

                    ),
                        false
                    );?>
                    <button class="icon-control icon-control--favorite"><span class="fa fa-heart-o fa-2x"></span><span class="fav-count sup-count">0</span></button>
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:sale.basket.basket.line",
                        "nminicart",
                        array(
                            "COMPONENT_TEMPLATE" => "minicart",
                            "PATH_TO_BASKET" => "/personal/cart/",
                            "PATH_TO_ORDER" => "/personal/cart/",
                            "SHOW_DELAY" => "N",
                            "SHOW_NOTAVAIL" => "N",
                            "SHOW_SUBSCRIBE" => "N",
                            "SHOW_NUM_PRODUCTS" => "Y",
                            "SHOW_TOTAL_PRICE" => "Y",
                            "SHOW_EMPTY_VALUES" => "Y",
                            "SHOW_PERSONAL_LINK" => "N",
                            "PATH_TO_PERSONAL" => SITE_DIR."personal/cart/",
                            "SHOW_AUTHOR" => "N",
                            "PATH_TO_REGISTER" => SITE_DIR."registration/",
                            "PATH_TO_PROFILE" => SITE_DIR."personal/cart/",
                            "SHOW_PRODUCTS" => "N",
                            "POSITION_FIXED" => "N",
                            "HIDE_ON_BASKET_PAGES" => "N"
                        ),
                        false
                    );
                    ?>
                </div>
            </div>
        </div>
    </header>
    <div class="tnw">
        <div class="container">
            <div class="col-sm-4 col-md-3 dw">
                <?
                $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "ntop-menu",
                    array(
                        "ROOT_MENU_TYPE" => "catalog",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "2",
                        "CHILD_MENU_TYPE" => "catalog-left",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );?>
            </div>
            <script type="application/ld+json">
                {
                    "@context": "http://schema.org",
                    "@type": "WebSite",
                    "url": "https://youtwig.ru/",
                    "potentialAction": {
                        "@type": "SearchAction",
                        "target": "https://youtwig.ru/search/index.php?q={query}",
                        "query": "required",
                        "query-input": "required name=query"
                    }
                }
            </script>
            <input type="checkbox" id="searchboxchk" class="hidden" />
            <div class="col-sm-8 col-md-5 col-lg-6 sw">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:search.form",
                    "nbootstrap-suggest",
                    Array(
                        "USE_SUGGEST" => "N",
                        "PAGE" => "#SITE_DIR#search/index.php"
                    )
                );?>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-3 imw">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "nlist",
                    array(
                        "ROOT_MENU_TYPE" => "top-first",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "0",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "Y"
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</div>
<div id="columns">
    <div class="container">
        <?php if(!IN_MAIN): ?>
            <div class="row bcb">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "nmain",
                    array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "s1"
                    ),
                    false
                );

                ?>
            </div>
        <?php endif; ?>