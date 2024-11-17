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
		'ARTNUMBER',
		'MODEL_HTML_IDS',
		"SECTION",
		'PRODUCT',
		'TABS',
		'HINTS',
		'TABS'.$arResult['ID'],
		'ANALOGUE',
    );

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};


//дополнительная кастомная сортировка свойств по айди товара и айди категории
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/include/propsSort.php");
$sortingProps = false;
if(isset($arSortByItem[$arResult['ID']])){
	$sortingProps = $arSortByItem[$arResult['ID']];
}elseif(isset($arSortByCategory[$arResult['IBLOCK_SECTION_ID']])){
	$sortingProps = $arSortByCategory[$arResult['IBLOCK_SECTION_ID']];
}
if($sortingProps && is_array($sortingProps)){
	$readyProps = [];
	foreach($sortingProps as $code){
		if(isset($arResult["DISPLAY_PROPERTIES"][$code])){
			$readyProps[$code] = $arResult["DISPLAY_PROPERTIES"][$code];
			unset($arResult["DISPLAY_PROPERTIES"][$code]);
		}
	}
	$arResult["DISPLAY_PROPERTIES"] = $readyProps + $arResult["DISPLAY_PROPERTIES"];
}


