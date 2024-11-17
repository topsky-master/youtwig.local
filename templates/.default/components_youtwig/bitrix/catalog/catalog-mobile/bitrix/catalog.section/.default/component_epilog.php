<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->AddHeadScript('/bitrix/templates/.default/components/bitrix/sale.order.ajax/main_test/js/jquery.maskedinput.min.js');
$howSort = $arResult['howSort'];

$sort_values = array_keys($howSort);

$sort_code_param = 'sort:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

$sord_default = $arParams["ELEMENT_SORT_FIELD"].":".$arParams["ELEMENT_SORT_ORDER"];
$_SESSION[$sort_code_param] = !isset($_SESSION[$sort_code_param]) ? $sord_default : $_SESSION[$sort_code_param];

$sort_code = ((isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) ? (urldecode($_REQUEST['sort'])) : ($_SESSION[$sort_code_param]));


if(empty($sort_code) && (($APPLICATION->get_cookie($sort_code_param)))){
    $sort_code = $APPLICATION->get_cookie($sort_code_param);
}

if(!(!empty($sort_code) && (in_array($sort_code,$sort_values)))){
    $sort_code = $sord_default;
}

$_SESSION[$sort_code_param] = $sort_code;
$APPLICATION->set_cookie($sort_code_param,$sort_code);


if(!empty($sort_code) && in_array($sort_code,$sort_values)){

    list($arParams["ELEMENT_SORT_FIELD"],$arParams["ELEMENT_SORT_ORDER"]) = explode(":",$sort_code);
    list($arParams["ELEMENT_SORT_FIELD2"],$arParams["ELEMENT_SORT_ORDER2"]) = explode(":",$sort_code);

}

ob_start();
?>
    <div class="compare sort clearfix col-xs-12 col-sm-12 col-md-12 col-lg-12" id="compare-sort">
        <div class="sort clearfix col-xs-6 col-sm-7 col-md-10 col-lg-10" id="sort">
            <label for="section-sort">
                <?php echo GetMessage("SECTION_SORT"); ?>
            </label>
            <select id="sort-select" name="sort-select" class="select-field selectpicker">
                <?php foreach ($howSort as $value=>$name): ?>
                    <option data-content="<?=htmlspecialcharsbx($name);?>" value="<?php echo urlencode($value); ?>"<?php if($value == $sort_code): ?> selected="selected"<?php endif; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid-list col-xs-3 col-sm-5 col-md-2 col-lg-2 hidden-xs">
            <a href="#" id="grid-toogle"></a>
            <a href="#" id="list-toogle"></a>
        </div>
        <div class="grid-list-mobile col-xs-6 hidden-lg hidden-md hidden-sm">
            <a href="#filterModel" id="mobile-filters-toogle" data-toggle="modal">
                <?=GetMessage('TMPL_OPEN_MOBILE_FILTERS');?>
                <i class="fa fa-sliders" aria-hidden="true"></i>
            </a>
        </div>
    </div>
<?

$rightSideBreadcrumb = ob_get_clean();
$APPLICATION->SetPageProperty('RIGHT_SIDE_BREADCRUMB', $rightSideBreadcrumb);


if (isset($templateData['TEMPLATE_THEME']))
{
    $APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
    $loadCurrency = false;
    if (!empty($templateData['CURRENCIES']))
        $loadCurrency = Loader::includeModule('currency');
    CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
    if ($loadCurrency)
    {
        ?>
        <script type="text/javascript">
            BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
        </script>
        <?
    }
}

if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){
    ?>
    <script type="text/javascript">
        //<!--
        reAttachCatalogJS();
        //-->
    </script>
    <?

}

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

        $filter_set = (int)$APPLICATION->GetPageProperty('filter_set', '');

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
        $APPLICATION->SetPageProperty('pagenum', $arResult["NavPageNomer"]);

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

if(change_to_mobile){
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.css');
}


?>