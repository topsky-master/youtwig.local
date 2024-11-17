<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

$arResult['AMP_HTML_URL'] = $arParams['AMP_HTML_URL'];

if (is_object($this->__component))
{
    $resultCacheKeys = array('NAV_STRING','AMP_HTML_URL');

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};