<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
 
twigSeoSections::incScriptsAtSectionEpilog($arResult,$arParams);

$APPLICATION->SetAdditionalCSS('/local/templates/nmain/css/swiper-bundle.min.css');
$APPLICATION->AddHeadScript('/local/templates/nmain/js/swiper-bundle.min.js');
