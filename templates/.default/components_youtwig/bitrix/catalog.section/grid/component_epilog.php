<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
 

twigSeoSections::printSeoAndTitlesAtSectionEpilog($arResult,$arParams);

$APPLICATION->IncludeComponent(
    "impel:sort",
    "",
    Array(
        "howSort" => $arParams['howSort'],
        "sort_code" => $arParams['sort_code'],
        "LIST_TYPE" => "GRID"
    ),
    false
);

twigSeoSections::incScriptsAtSectionEpilog($arResult,$arParams);

$sCurDir = isset($_SERVER['ORIG_REQUEST_URI']) ? $_SERVER['ORIG_REQUEST_URI'] : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '')));

        $filters_preg = \COption::GetOptionString('my.stat', "filters_preg", "", SITE_ID);

      if (!empty($filters_preg)) {
    $filters_preg = explode("\n", $filters_preg);
    $filters_preg = array_unique($filters_preg);
    $filters_preg = array_map("trim", $filters_preg);
    $filters_preg = array_filter($filters_preg);
    if (!empty($filters_preg)) {
        $bNoIndexNofollow = false;
        // $sCurDir = isset($_SERVER['ORIG_REQUEST_URI']) ? $_SERVER['ORIG_REQUEST_URI'] : $_SERVER['REQUEST_URI'];
        $sCurDir = preg_replace('~\?.*?$~is', '', $sCurDir);

        foreach ($filters_preg as $filter_preg) {
            if (preg_match('~' . $filter_preg . '~is', $sCurDir)) {
                $bNoIndexNofollow = true;
                break;
            }
        }

        if ($bNoIndexNofollow) {
            $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
        }
    }
}

$APPLICATION->SetAdditionalCSS('/local/templates/nmain/css/swiper-bundle.min.css');
$APPLICATION->AddHeadScript('/local/templates/nmain/js/swiper-bundle.min.js');

 


