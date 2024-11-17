<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

twigSeoSections::applySectionTemplateModifications($arResult,$arParams);
twigSeoSections::printSortAtSectionResultModifier($arResult,$arParams);


if (is_object($this->__component))
{

	$this->__component->SetResultCacheKeys(array('NAV_STRING'));

    if (!isset($arResult['NAV_STRING']))
        $arResult['NAV_STRING'] = $this->__component->arResult['NAV_STRING'];

}