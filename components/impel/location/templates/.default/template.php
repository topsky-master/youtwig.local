<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$areaId = isset($arParams["AREA_ID"]) && !empty($arParams["AREA_ID"]) ? trim($arParams["AREA_ID"]) : '';
$modelId = "bx_location_list".$areaId;

$spCode = trim($arParams['PROPERTY_CODE']);
$spZipCode = trim($arParams['PROPERTY_ZIP_CODE']);

$spValue = trim($arParams['PROPERTY_VALUE']);
$sCity = isset($arResult['CITY']) ? trim($arResult['CITY']) : '';
$sStreet = isset($arResult['STREET']) ? trim($arResult['STREET']) : '';

$iCityId = isset($arResult['CITY_ID']) ? trim($arResult['CITY_ID']) : '';
$iStreetId = isset($arResult['STREET_ID']) ? trim($arResult['STREET_ID']) : '';
$bHasStreets = isset($arResult['HAS_STREETS']) && $arResult['HAS_STREETS'] ? true : false;
$sjAction = isset($arParams['ACTION']) && $arParams['ACTION'] ? trim($arParams['ACTION']) : '';
$sClasses = isset($arParams['CLASSES']) && $arParams['CLASSES'] ? (' '.trim($arParams['CLASSES'])) : '';
?>
<div id="<?=$modelId;?>" class="chooselocation">
    <div class="row">
        <label class="col-lg-3 col-xs-4 col-sm-12">
            <?=GetMessage('SOA_TEMPL_CITY');?>
            <span class="bx_sof_req">*</span>
        </label>
        <div class="col-lg-9 col-xs-8 col-sm-12">
            <input type="text" data-no-results="<?=GetMessage('TMPL_NOTFOUND_CITY');?>" data-typeid="city" data-value="<?=($iCityId);?>" class="loc city form-control<?=htmlspecialchars($sClasses,ENT_QUOTES,LANG_CHARSET);?>" value="<?=htmlspecialchars($sCity,ENT_QUOTES,LANG_CHARSET);?>" placeholder="<?=GetMessage('TMPL_CHOOSE_CITY');?>" />
        </div>
    </div>
    <div class="row<?php if(!$bHasStreets): ?> hidden<? endif; ?>">
        <label class="col-lg-3 col-xs-4 col-sm-12">
            <?=GetMessage('SOA_TEMPL_STREET');?>
            <span class="bx_sof_req">*</span>
        </label>
        <div class="col-lg-9 col-xs-8 col-sm-12">
            <input type="text" data-no-results="<?=GetMessage('TMPL_NOTFOUND_STREET');?>" data-typeid="street" data-value="<?=($iStreetId);?>" class="loc street form-control<?=htmlspecialchars($sClasses,ENT_QUOTES,LANG_CHARSET);?>" value="<?=htmlspecialchars($sStreet,ENT_QUOTES,LANG_CHARSET);?>" placeholder="<?=GetMessage('TMPL_CHOOSE_STREET');?>" />
        </div>
    </div>
    <input type="hidden" class="locid" name="<?=$spCode;?>" value="<?=$spValue;?>" />
    <input type="hidden" class="zipcopy" value="<?=$spZipCode;?>" />
    <? if($sjAction): ?>
        <input type="hidden" class="jsaction" value="<?=htmlspecialchars($sjAction,ENT_QUOTES,LANG_CHARSET);?>" />
    <? endif; ?>
</div>