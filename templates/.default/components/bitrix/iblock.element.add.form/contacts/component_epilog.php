<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;

if(defined('change_to_mobile') && change_to_mobile) {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.min.css');
}
