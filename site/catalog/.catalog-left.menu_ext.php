<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$IBLOCK_ID          =  29;

if(!defined('UF_ANOTHER_LINK')){
    define('UF_ANOTHER_LINK','UF_ANOTHER_LINK');
}

$aMenuLinksExt      =  $APPLICATION->IncludeComponent("impel:menu.sections", "", array(
    "IS_SEF"         => "N",
    "ID"             => 0,
    "IBLOCK_TYPE"    => "catalog",
    "IBLOCK_ID"      => $IBLOCK_ID,
    "SECTION_URL"    => "",
    "DEPTH_LEVEL"    => "10",
    "CACHE_TYPE"     => "A",
    "CACHE_TIME"     => "3600",
    "LINK_PROPERTY"  => UF_ANOTHER_LINK
),
    false
);

$aMenuLinks         = array_merge($aMenuLinks, $aMenuLinksExt);
?>