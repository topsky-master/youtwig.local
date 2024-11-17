<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$arResult['MENU_DESCRIPTION'] = '';

if (IBLOCK_INCLUDED){

    $arDMSelect = Array(
                    "PREVIEW_TEXT"
                );
    $arDMFilter = Array(
                    "IBLOCK_ID" => 18,
                    "CODE" => "mobile-info"
                );

    $dbDMRes = CIBlockElement::GetList(
                    Array(),
                    $arDMFilter,
                    false,
                    false,
                    $arDMSelect
                );

    if($dbDMRes){
        $arDMRes = $dbDMRes->GetNext();
        $arResult['MENU_DESCRIPTION'] = $arDMRes["PREVIEW_TEXT"];
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