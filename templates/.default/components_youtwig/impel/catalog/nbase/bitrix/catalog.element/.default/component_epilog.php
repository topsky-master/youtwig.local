<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

global $APPLICATION;

twigElement::printSeoAndTitlesAtEpilog($arResult,$arParams);
twigElement::incScriptsAtEpilog($arResult,$arParams);
twigElement::incStylesAtEpilog($arResult,$arParams);

