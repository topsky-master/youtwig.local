<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;

$APPLICATION->AddHeadScript($templateFolder.'/script.min.js');
$APPLICATION->SetAdditionalCSS($templateFolder.'/style.min.css');

?>