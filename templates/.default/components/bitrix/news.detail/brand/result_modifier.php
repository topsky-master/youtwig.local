<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$brand_filter_title = isset($arResult['DISPLAY_PROPERTIES'])
                    &&isset($arResult['DISPLAY_PROPERTIES']['BRAND_FILTER_TITLE'])
                    &&isset($arResult['DISPLAY_PROPERTIES']['BRAND_FILTER_TITLE']['VALUE'])
                    ? trim($arResult['DISPLAY_PROPERTIES']['BRAND_FILTER_TITLE']['VALUE'])
                    : '';

$arResult['PRODUCTS'] = array();

if (!empty($brand_filter_title)){

    $products = array();

	$arBrendSelect = Array("ID");
    $arBrendFilter = Array(
        "IBLOCK_ID" => 11,
		"IBLOCK_TYPE" => "catalog",
        "PROPERTY_MANUFACTURER_VALUE" => $brand_filter_title
	);

    $resBrend = CIBlockElement::GetList(Array(), $arBrendFilter, false, false, $arBrendSelect);

	if($resBrend)
    while($arBrend = $resBrend->GetNext()){
        $products[] = $arBrend['ID'];
    }

    $products = array_filter($products);
    $products = array_unique($products);

    if(!empty($products)){
        $arResult['PRODUCTS'] = $products;
    }
}

if (is_object($this->__component))
{

    $resultCacheKeys = array_keys($arResult);

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};