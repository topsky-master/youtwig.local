<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$APPLICATION->AddViewContent('AMP_SCRIPTS','<script async custom-element="amp-accordion" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>');

$this->setFrameMode(false);

if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '')
    $arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');

$isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
$isSidebar = ($arParams["SIDEBAR_SECTION_SHOW"] == "Y" && isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
$isFilter = ($arParams['USE_FILTER'] == 'Y');

if ($isFilter)
{
    $arFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
    );
    if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
    elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];

    $obCache = new CPHPCache();
    if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
    {
        $arCurSection = $obCache->GetVars();
    }
    elseif ($obCache->StartDataCache())
    {
        $arCurSection = array();
        if (Loader::includeModule("iblock"))
        {
            $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));

            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                if ($arCurSection = $dbRes->Fetch())
                    $CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

                $CACHE_MANAGER->EndTagCache();
            }
            else
            {
                if(!$arCurSection = $dbRes->Fetch())
                    $arCurSection = array();
            }
        }
        $obCache->EndDataCache($arCurSection);
    }
    if (!isset($arCurSection))
        $arCurSection = array();
}

if(isset($arParams["SET_SECTION_ID"])
    && !empty($arParams["SET_SECTION_ID"])){
    $arCurSection['ID'] = (int)trim($arParams["SET_SECTION_ID"]);
}

$arParams["INCLUDE_SUBSECTIONS"] = "Y";
$arParams["SHOW_ALL_WO_SECTION"] = "Y";
define('UF_ANOTHER_LINK','UF_ANOTHER_LINK_AMP');

if(!(isset($_REQUEST['q']) && !empty($_REQUEST['q']))):

?>
    <div class="mobile-menu-area">
    <?php

$APPLICATION->IncludeComponent(
    "bitrix:menu",
    "amp-menu-sections",
    array(
        "ROOT_MENU_TYPE" => "catalog",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "36000",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "4",
        "CHILD_MENU_TYPE" => "catalog-left",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "Y"
    ),
    false
);?>
    </div>
<?

else:

    include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/section_vertical.php");

endif;

if(isset($arParams['SECTIONS_CANONICAL'])
    && !empty($arParams['SECTIONS_CANONICAL'])){

	if(!(isset($_REQUEST['q']) && !empty($_REQUEST['q']))):
		$canonical_url = trim($arParams['SECTIONS_CANONICAL']);
		$canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
		$canonical_url = preg_replace('~\:\/\/(www\.)*m\.~','://',$canonical_url);
	

		$this->SetViewTarget("CANONICAL_PROPERTY");
	
		?><link href="<?=$canonical_url;?>" rel="canonical" /><?
	
		$this->EndViewTarget();
	endif;

}