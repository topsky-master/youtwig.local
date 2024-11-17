<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

$arPSort                                                                   = array(
    $arParams['SORT_BY1']                                               =>$arParams['SORT_ORDER1'],
    $arParams['SORT_BY2']                                               =>$arParams['SORT_ORDER2']
);


$arPSelect                                                                 = array(
    "ID",
    "NAME",
    "DETAIL_PAGE_URL"
);

$arPFilter                                                                 = array(
    "IBLOCK_ID"                                                         =>$arResult["IBLOCK_ID"],
    "ACTIVE"                                                            =>"Y",
    "CHECK_PERMISSIONS"                                                 =>"Y",
);

if(isset($arResult["IBLOCK_SECTION_ID"]) && !empty($arResult["IBLOCK_SECTION_ID"])){
    $arPFilter["SECTION_ID"]	                                            = $arResult["IBLOCK_SECTION_ID"];
};

$arPNavParams                                                              = array(
    "nPageSize"                                                         =>1,
    "nElementID"                                                        =>$arResult["ID"]
);

$arItems                                                                   = Array();
$rsElement                                                                 = CIBlockElement::GetList(
    $arPSort,
    $arPFilter,
    false,
    $arPNavParams,
    $arPSelect);


$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);

if($rsElement){
    while($obElement                                                        = $rsElement->GetNextElement()){
        $arItems[]                                                          = $obElement->GetFields();
    };
};

if(count($arItems)                                                         ==3):
    $arResult["TORIGHT"]                                                    = Array(
        "NAME"                                                              =>$arItems[0]["NAME"],
        "URL"                                                               =>$arItems[0]["DETAIL_PAGE_URL"]);

    $arResult["TOLEFT"]                                                     = Array(
        "NAME"                                                              =>$arItems[2]["NAME"],
        "URL"                                                               =>$arItems[2]["DETAIL_PAGE_URL"]);

elseif(count($arItems)                                                     ==2):
    if($arItems[0]["ID"]                                                    !=$arResult["ID"])
        $arResult["TORIGHT"]                                                = Array(
            "NAME"                                                          =>$arItems[0]["NAME"],
            "URL"                                                           =>$arItems[0]["DETAIL_PAGE_URL"]);
    else
        $arResult["TOLEFT"]                                                 = Array(
            "NAME"                                                          =>$arItems[1]["NAME"],
            "URL"                                                           =>$arItems[1]["DETAIL_PAGE_URL"]);
endif;

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