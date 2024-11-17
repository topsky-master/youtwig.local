<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
    return "";

$SEO_TEXT = seoTitleAtTop();
$rightSideBreadcrumb = rightSideBreadcrumb();

$strReturn = '<div class="bdbw">
                <div class="'. ($rightSideBreadcrumb != "" ? 'col-sm-12 col-md-7 col-lg-8' : 'col-sm-12').'">
                    <ul class="bnav" itemscope itemtype="http://schema.org/BreadcrumbList">';



$navs = array();
$pills = array();

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    if(mb_stripos($arResult[$index]["LINK"],'#end') !== false) {
        $arResult[$index]["LINK"] = str_ireplace('#end','',$arResult[$index]["LINK"]);
        $navs[] = $arResult[$index];
    } elseif($arResult[$index]["LINK"] == "#detail"){
        $arResult[$index]["LINK"]= "";
        $navs[] = $arResult[$index];
    }else{
        $pills[] = $arResult[$index];
    }
}



$arResult = array_merge($pills, $navs);

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if($index == 0){
        $title = GetMessage('MAIN_PAGE');
    }
    
    if (preg_match('~#[^#]+?#~',$arResult[$index]["LINK"])) {
        continue;
    }

    if($arResult[$index]["LINK"] <> "")
        $strReturn .= '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.$arResult[$index]["LINK"].'"><span itemprop="name"> '.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></span></li>';
    else
        $strReturn .= '<li><span>'.$title.'</span></li>';
}

$strReturn .= ' </ul>
            </div>';

if(!empty($rightSideBreadcrumb)){
    $strReturn .=   '<div class="col-sm-12 col-md-5 col-lg-4">';
    $strReturn .=       $rightSideBreadcrumb;
    $strReturn .=   '</div>';
}

$strReturn .= '</div>';

if(!empty($SEO_TEXT))
    $strReturn .= '<div class="stwp"><h1>'.$SEO_TEXT.'</h1></div>';

return $strReturn;
