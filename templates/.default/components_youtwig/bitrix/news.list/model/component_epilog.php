<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//$APPLICATION->AddHeadScript($templateFolder."/js/chained.js");

if(change_to_mobile){
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.css');
}