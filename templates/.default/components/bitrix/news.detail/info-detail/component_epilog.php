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

$APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/info/' . $arResult['CODE'] . '/'.'" />');

twigSeoSections::printSeoAndSetTitlesSection(0,$arParams);