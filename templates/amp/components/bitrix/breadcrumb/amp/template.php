<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
    return "";

$strReturn = '<div class="breadcrumb-wrapper">
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

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++){

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if($index == 0){
        $title = GetMessage('MAIN_PAGE');
    }

    if($arResult[$index]["LINK"] <> "")
        $strReturn .=       '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.$arResult[$index]["LINK"].'"><span itemprop="name"> '.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></span></li>';
    else
        $strReturn .=       '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index+1).'" /></span></li>';
}

$strReturn .= '         </ul>';

$strReturn .= '</div>';

return $strReturn;

?>