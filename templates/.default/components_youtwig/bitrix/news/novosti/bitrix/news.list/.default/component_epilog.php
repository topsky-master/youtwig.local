<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

global $APPLICATION;

$strNavQueryString = "";

if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])){
    $strNavQueryString .= '&q='.$_REQUEST['q'].'';
}

$strNavQueryString = !empty($strNavQueryString) ? '?'.$strNavQueryString : $strNavQueryString;


if(isset($arResult["NAV_STRING"]) && !empty($arResult["NAV_STRING"])){

    $links = array();
    preg_match_all('~<a[^>]*?>(.*?)</a>~isu',$arResult["NAV_STRING"],$links);

    if(!empty($links[1])){
        $links[1] = array_map('strip_tags',$links[1]);
        $links[1] = array_map('trim',$links[1]);
        $links[1] = array_map('intval',$links[1]);
        $arResult["NavPageCount"] = max($links[1]);
    }

    $links = array();
    preg_match_all('~<li[^>]*?class="active[^"]+?"[^>]*?>(.*?)</li>~isu',$arResult["NAV_STRING"],$links);

    if(!empty($links[1])){
        $links[1] = array_map('strip_tags',$links[1]);
        $links[1] = array_map('trim',$links[1]);
        $links[1] = array_map('intval',$links[1]);
        $links[1] = current($links[1]);
        $arResult["NavPageNomer"] = $links[1];
    } else {
        $arResult["NavPageNomer"] = 1;
    }

    $links = array();
    preg_match_all('~<a[^>]*?href="([^"]+?)"~isu',$arResult["NAV_STRING"],$links);

    if(!(isset($arResult["NavNum"]) && !empty($arResult["NavNum"]))){
        $arResult["NavNum"] = 2;
    }

    if(!empty($links[1])){
        $links[1] = array_map('trim',$links[1]);
        $arResult["sUrlPath"] = end($links[1]);
        $pages = array();
        preg_match('#(/pages([\d]*?)-([\d]+)#is',$arResult["sUrlPath"],$pages);
        $arResult["NavNum"] = isset($pages[1]) && !empty($pages[1]) ? (int)trim($pages[1]) : 2;
    }

    if($arResult["NavPageNomer"] > 1){

        $pagenav_title = \COption::GetOptionString('my.stat', 'pagenav_news_title', '', SITE_ID);
        $pagenav_title = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title);

        $pagenav_title_default = \COption::GetOptionString('my.stat', 'pagenav_title_default', '', SITE_ID);
        $pagenav_title_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title_default);

        $pagenav_description = \COption::GetOptionString('my.stat', 'pagenav_news_description', '', SITE_ID);
        $pagenav_description = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description);

        $pagenav_description_default = \COption::GetOptionString('my.stat', 'pagenav_description_default', '', SITE_ID);
        $pagenav_description_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description_default);

    }

    if(isset($arResult["sUrlPath"])
        && !empty($arResult["sUrlPath"])
        && isset($arResult["NavPageNomer"])
        && !empty($arResult["NavPageNomer"])
        && isset($arResult["NavPageCount"])
        && !empty($arResult["NavPageCount"])){

        $arResult["sUrlPath"] = preg_replace('#(/pages([\d]*?)-([\d]+))#is', '', $arResult["sUrlPath"]);
        $arResult["sUrlPath"] = preg_replace('~[^/]*?$~','',$arResult["sUrlPath"]);
        $pageURL = 'pages'.($arResult["NavNum"] != 2 ? $arResult["NavNum"] : '').'-';

        if ($arResult["NavPageNomer"] > 1) {

            $link_prev = $arResult["sUrlPath"].(($arResult["NavPageNomer"] > 2) ? ($pageURL.($arResult["NavPageNomer"] - 1).'/') : '').$strNavQueryString;
            $link_prev = (preg_match('~http(s*?)://~',$link_prev) == 0) ? IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$link_prev : $link_prev;

            $APPLICATION->AddHeadString('<link rel="prev" href="'.$link_prev.'" />',true);

        }

        if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]){

            $link_next = $arResult["sUrlPath"]. $pageURL . ($arResult["NavPageNomer"] + 1) .'/'.$strNavQueryString;
            $link_next = (preg_match('~http(s*?)://~',$link_next) == 0) ? IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$link_next : $link_next;

            $APPLICATION->AddHeadString('<link rel="next" href="'.$link_next.'" />',true);
        }

    }

}

$pagenav_title = empty($pagenav_title) ? $pagenav_title_default : $pagenav_title;

if(!empty($pagenav_title) &&
    ($APPLICATION->GetPageProperty('title','') != ""
        || $APPLICATION->GetTitle() != "")){

    if(mb_stripos($pagenav_title, '[title]') !== false){

        if(!empty($APPLICATION->GetPageProperty('title',''))){
            $pagenav_title = str_ireplace('[title]',$APPLICATION->GetPageProperty('title',''),$pagenav_title);
        } else {
            $pagenav_title = str_ireplace('[title]',$APPLICATION->GetTitle(),$pagenav_title);
        }

    }

    if(mb_stripos($pagenav_title, '[pag_title]') !== false){

        if(!empty($APPLICATION->GetPageProperty('title',''))){
            $pagenav_title = str_ireplace('[pag_title]',$APPLICATION->GetPageProperty('title',''),$pagenav_title);
        } else {
            $pagenav_title = str_ireplace('[pag_title]',$APPLICATION->GetTitle(),$pagenav_title);
        }

    }

}

$APPLICATION->SetPageProperty('page_title', $pagenav_title);

$pagenav_description = empty($pagenav_description) ? $pagenav_description_default : $pagenav_description;

if(!empty($pagenav_description)
    && !empty($APPLICATION->GetPageProperty('description', ''))){

    if(mb_stripos($pagenav_description, '[description]') !== false) {
        $pagenav_description = str_ireplace('[description]', $APPLICATION->GetPageProperty('description', ''), $pagenav_description);
    }

    if(mb_stripos($pagenav_description, '[pag_description]') !== false) {
        $pagenav_description = str_ireplace('[pag_description]', $APPLICATION->GetPageProperty('description', ''), $pagenav_description);
    }

}

$APPLICATION->SetPageProperty('page_description', $pagenav_description);
//$APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL . preg_replace('~^m\.~is','',IMPEL_SERVER_NAME) . '/amp/news/'.'" />');