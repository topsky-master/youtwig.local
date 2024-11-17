<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();
twigElement::applyTemplateModifications($arResult,$arParams);


if (is_object($this->__component))
{
    $resultCacheKeys = array(
        'CANONICAL_URL',
        'ID',
        'LINKED_ELEMETS',
        'IBLOCK_ID',
        'IBLOCK_TYPE',
        'MORE_PHOTO',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'CODE',
		'MORE_PHOTO_COUNT',
		'PRSET',
		'ARTNUMBER'
    );

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};