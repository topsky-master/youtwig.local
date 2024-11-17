<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;

switch ($arParams['VIEW_MODE'])
{
	case 'BANNER':

        __IncludeLang($templateFolder."/banner/lang/".LANGUAGE_ID."/template.php");
        $APPLICATION->AddHeadScript($templateFolder.'/banner/script.js');
		$APPLICATION->SetAdditionalCSS($templateFolder.'/banner/style.css');

        if(change_to_mobile){
            $APPLICATION->SetAdditionalCSS($templateFolder.'/banner/mobile.css');
        } else {
            $APPLICATION->SetAdditionalCSS($templateFolder.'/banner/mediaquery.css');
        }


    case 'SLIDER':

        __IncludeLang($templateFolder."/slider/lang/".LANGUAGE_ID."/template.php");
    	$APPLICATION->AddHeadScript($templateFolder.'/slider/script.js');
		$APPLICATION->SetAdditionalCSS($templateFolder.'/slider/style.css');

        $APPLICATION->SetAdditionalCSS($templateFolder."/slider/css/flexslider.css");
        $APPLICATION->AddHeadScript($templateFolder."/slider/js/jquery.easing.js");
        $APPLICATION->AddHeadScript($templateFolder."/slider/js/jquery.mousewheel.js");
        $APPLICATION->AddHeadScript($templateFolder."/slider/js/jquery.flexslider-min.js");

        if(change_to_mobile){
            $APPLICATION->SetAdditionalCSS($templateFolder.'/slider/mobile.css');
        } else {
            $APPLICATION->SetAdditionalCSS($templateFolder.'/slider/mediaquery.css');
        }

        break;
	case 'SECTION':
	default:

        __IncludeLang($templateFolder."/section/lang/".LANGUAGE_ID."/template.php");
        $APPLICATION->AddHeadScript($templateFolder.'/section/script.js');
		$APPLICATION->SetAdditionalCSS($templateFolder.'/section/style.css');

        if(change_to_mobile){
            $APPLICATION->SetAdditionalCSS($templateFolder.'/section/mobile.css');
        } else {
            $APPLICATION->SetAdditionalCSS($templateFolder.'/section/mediaquery.css');
        }

        break;
}

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

?>