<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */


global $APPLICATION;
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");


if(isset($arResult["TOLEFT"])
    && isset($arResult["TOLEFT"]["URL"])
    && !empty($arResult["TOLEFT"]["URL"])){
    $APPLICATION->AddHeadString('<link rel="prev" href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/'.ltrim($arResult["TOLEFT"]["URL"],'/').'" />');
}

if(isset($arResult["TORIGHT"])
    && isset($arResult["TORIGHT"]["URL"])
    && !empty($arResult["TORIGHT"]["URL"])){
    $APPLICATION->AddHeadString('<link rel="next" href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/'.ltrim($arResult["TORIGHT"]["URL"],'/').'" />');
}


/*$APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/news/' . $arResult['CODE'] . '.html'.'" />');*/


?>