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


	$this->__component->SetResultCacheKeys(array('COUNT_ITEMS'));

    if (!isset($arResult['COUNT_ITEMS']))
        $arResult['COUNT_ITEMS'] = $this->__component->arResult['COUNT_ITEMS'];


    $this->__component->SetResultCacheKeys(array('howSort'));

    if (!isset($arResult['howSort']))
        $arResult['howSort'] = $this->__component->arResult['howSort'];

}