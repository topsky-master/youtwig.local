<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

if(isset($arResult["PREVIEW_PICTURE"])
    && isset($arResult["PREVIEW_PICTURE"]["SRC"])
    &&!empty($arResult["PREVIEW_PICTURE"]["SRC"])):

    $arResult["PREVIEW_PICTURE"]['SRCSET'] = createAMPSRCSetHTML($arResult["PREVIEW_PICTURE"]["SRC"]);

endif;

$amp_content_obj = new AMP_Content( $arResult['PREVIEW_TEXT'],
    array(
        //'AMP_YouTube_Embed_Handler' => array(),
    ),
    array(
        'AMP_Style_Sanitizer' => array(),
        'AMP_Blacklist_Sanitizer' => array(),
        'AMP_Img_Sanitizer' => array(),
        'AMP_Video_Sanitizer' => array(),
        'AMP_Audio_Sanitizer' => array(),
        'AMP_Iframe_Sanitizer' => array(
            'add_placeholder' => true,
        ),
    ),
    array(
        'content_max_width' => 600,
    )
);

$arResult['PREVIEW_TEXT'] = $amp_content_obj->get_amp_content();

$amp_content_obj = new AMP_Content( $arResult['DETAIL_TEXT'],
    array(
        //'AMP_YouTube_Embed_Handler' => array(),
    ),
    array(
        'AMP_Style_Sanitizer' => array(),
        'AMP_Blacklist_Sanitizer' => array(),
        'AMP_Img_Sanitizer' => array(),
        'AMP_Video_Sanitizer' => array(),
        'AMP_Audio_Sanitizer' => array(),
        'AMP_Iframe_Sanitizer' => array(
            'add_placeholder' => true,
        ),
    ),
    array(
        'content_max_width' => 600,
    )
);

$arResult['DETAIL_TEXT'] = $amp_content_obj->get_amp_content();

$arPSort = array(
    $arParams['SORT_BY1'] => $arParams['SORT_ORDER1'],
    $arParams['SORT_BY2'] => $arParams['SORT_ORDER2']
);


$arPSelect                                                                 = array(
    "ID",
    "NAME",
    "DETAIL_PAGE_URL"
);


$canonical_url = '';
$canonicalResDB = CIBlockElement::GetList(Array("SORT" => "ASC"),Array('ID' => $arResult['ID'], 'IBLOCK_ID' => $arResult['IBLOCK_ID']), false, false, array('DETAIL_PAGE_URL'));

if($canonicalResDB
    && $canonicalResArr = $canonicalResDB->getNext()){

    if(isset($canonicalResArr['DETAIL_PAGE_URL']) && !empty($canonicalResArr['DETAIL_PAGE_URL'])){
        $canonical_url = $canonicalResArr['DETAIL_PAGE_URL'];
    };

};

$arResult['CANONICAL_URL'] = $canonical_url;

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