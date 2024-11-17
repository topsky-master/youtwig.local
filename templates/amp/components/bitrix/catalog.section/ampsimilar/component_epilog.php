<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $templateData
 * @var string $templateFolder
 * @var CatalogSectionComponent $component
 */

global $APPLICATION;

if(file_exists(__DIR__.'/amp_style.css') && !defined('SIMILIAR_INCLUDED')){

	define('SIMILIAR_INCLUDED',true);

    $amp_style = file_get_contents(__DIR__.'/amp_style.css');
    if(get_class($this->__template)!=="CBitrixComponentTemplate")
        $this->InitComponentTemplate();

    $this->__template->SetViewTarget("AMP_STYLE");
    echo $amp_style;
    $this->__template->EndViewTarget();

}

$filter_set = false;

global ${$arParams["FILTER_NAME"]};

if((isset(${$arParams["FILTER_NAME"]})
    && !empty(${$arParams["FILTER_NAME"]}))
){

    foreach(${$arParams["FILTER_NAME"]} as $filter_key => $filter_value){
        if(mb_stripos($filter_key,'=PROPERTY_') !== false){
            $filter_set = true;
            break;
        };
    };

};


$canonical_url = $canonical_path = $filterPath = '';

if(isset($arResult['CANONICAL_URL']) && false){

    $canonical_url = $arResult['CANONICAL_URL'];

    if(isset($canonical_url) && !empty($canonical_url)){

        $canonical_path = $canonical_url;

        $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
        $canonical_url = preg_replace('~\:\/\/(www\.)*m\.~','://',$canonical_url);

        if($filter_set){

            $filterPath = preg_replace('~^.*?/filter/~','filter/',$APPLICATION->GetCurPage());

        }

        $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
        $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
        $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);

        if($DETAIL_PAGE_URL != $SERVER_PAGE_URL){

            if(get_class($this->__template)!=="CBitrixComponentTemplate")
                $this->InitComponentTemplate();

            $this->__template->SetViewTarget("CANONICAL_PROPERTY");
            ?>
            <link href="<?=$canonical_url.$filterPath;?>" rel="canonical" />
            <?
            $this->__template->EndViewTarget();

        };

    };

};

if(!empty($canonical_path)){
    $canonical_url = $canonical_path;
    $APPLICATION->SetPageProperty('canonical_url', $canonical_url);
};


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

        $pagenav_title = \COption::GetOptionString('my.stat', (!$filter_set ? 'pagenav_title' : 'pagenav_filter_title'), '', SITE_ID);
        $pagenav_title = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title);

        $APPLICATION->SetPageProperty('pagenav_title', $pagenav_title);

        $pagenav_title_default = \COption::GetOptionString('my.stat', 'pagenav_title_default', '', SITE_ID);
        $pagenav_title_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_title_default);

        $APPLICATION->SetPageProperty('pagenav_title_default', $pagenav_title_default);

        $pagenav_description = \COption::GetOptionString('my.stat', (!$filter_set ? 'pagenav_description' : 'pagenav_filter_description'), '', SITE_ID);
        $pagenav_description = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description);

        $APPLICATION->SetPageProperty('pagenav_description', $pagenav_description);

        $pagenav_description_default = \COption::GetOptionString('my.stat', 'pagenav_description_default', '', SITE_ID);
        $pagenav_description_default = str_ireplace('[pagenum]',$arResult["NavPageNomer"], $pagenav_description_default);

        $APPLICATION->SetPageProperty('pagenav_description_default', $pagenav_description_default);

    }

}


