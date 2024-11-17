<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
 

twigSeoSections::incScriptsAtSectionEpilog($arResult,$arParams);


//if(!isset($_REQUEST['PAGEN_1'])){
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/slick.min.js");
	//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/slick.css');
//}
