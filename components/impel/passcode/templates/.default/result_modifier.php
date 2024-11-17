<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$dRes = CIBlockElement::GetList(
    ($aOrder = array()),
    ($aFilter = array(
        'IBLOCK_ID' => 18,
        'CODE' => 'fastuath'
    )),
    false,
    false,
    ($aSelect = array('PREVIEW_TEXT'))
    );

if($dRes
    && $aRes = $dRes->GetNext()){
    $arResult['FASTAUTH_INFO'] = $aRes['PREVIEW_TEXT'];
}