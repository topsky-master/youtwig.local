<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
    return "";

$SEO_TEXT = seoTextAtTop();
$rightSideBreadcrumb = rightSideBreadcrumb();

$strReturn = '<div class="breadcrumb-wrapper row">
                <div class="'. ($rightSideBreadcrumb != "" ? 'col-xs-12 col-sm-12 col-md-12 col-lg-7' : 'col-xs-12').' hidden-xs">
                    <ul class="breadcrumb-navigation-main" itemscope itemtype="http://schema.org/BreadcrumbList">';



$navs		= array();
$pills		= array();

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    if($arResult[$index]["LINK"] == "#detail"){
        $arResult[$index]["LINK"]= "";
        $navs[] 				 = $arResult[$index];
    }else{
        $pills[]				 = $arResult[$index];
    }
}



$arResult						 = array_merge($pills, $navs);

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    if($index > 0)
        $strReturn .= '<li><span></span></li>';

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if($index == 0){
        $title = GetMessage('MAIN_PAGE');
    }

    if($arResult[$index]["LINK"] <> "")
        $strReturn .=       '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.$arResult[$index]["LINK"].'"><span itemprop="name"> '.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></span></li>';
    else
        $strReturn .=       '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index+1).'" /></span></li>';
}

$strReturn .= '         </ul>
                    </div>';

if(!empty($rightSideBreadcrumb)){
    $strReturn .=   '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">';
    $strReturn .=       $rightSideBreadcrumb;
    $strReturn .=   '</div>';
}

$strReturn .= '</div>';

if(!empty($SEO_TEXT))
    $strReturn .= '<div class="hidden-xs seo-text-wrapper">'.$SEO_TEXT.'</div>';

return $strReturn;
?>
