<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 11:26
 *
 * 
 */

use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Options;

if (TfLocationComponent::$templateLoaded) return;

Loc::loadMessages(__DIR__ . '/template.php');

/** colors */
if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'] = '#ffffff';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER'] = '#333333';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'] ='#2b7de0';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'] = '#468de4';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'] = '#337ab7';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER'] = '#039be5';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'] = '#f5f5f5';

if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER']))
    $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'] = '#f5f5f5';

/** phrases */
if (empty($arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER']))
    $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER'] = Loc::getMessage("tfl__check-location");

if (empty($arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER']))
    $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'] =Loc::getMessage("tfl__check-location_placeholder");

/** js callback */
$arResult['JS_CALLBACK'] = str_replace("'", "\'", $arResult['SETTINGS'][Options::CALLBACK]).'; ';
if(!empty($arParams['PARAMS']['ONCITYCHANGE']))
    $arResult['JS_CALLBACK'] .= $arParams['PARAMS']['ONCITYCHANGE'].'(); ';

if(!empty($arParams['PARAMS']['JS_CALLBACK']))
    $arResult['JS_CALLBACK'] .= $arParams['PARAMS']['JS_CALLBACK'].'(); ';

$arResult['JS_CALLBACK'] = str_replace(';;', ';', $arResult['JS_CALLBACK']);

/** js params */
$arResult['JS_PARAMS'] = [
    'path'          => $arResult['COMPONENT_PATH'],
    'request_uri'   => $_SERVER['REQUEST_URI'],
    'ajax_search'   => $arResult['AJAX_SEARCH'],
    'mobile_width'  => $arResult['SETTINGS'][Options::LIST_MOBILE_BREAKPOINT],
    'load_type'     => $arResult['SETTINGS'][Options::LIST_LOCATIONS_LOAD]
];

$arResult['JS_PARAMS'] = Json::encode($arResult['JS_PARAMS']);

/** define popup text */
if (strlen($arResult['CITY_ID']))
{
    if (empty($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_TEXT']))
        $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_TEXT'] = Loc::getMessage('tfl__your-location');

    $arResult['CONFIRM_POPUP_TEXT'] = str_replace('#location#', $arResult['CITY_NAME'], $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_TEXT']);
}
else
{
    $arResult['CONFIRM_POPUP_TEXT'] = $arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT']
        ? : Loc::getMessage('tfl__no-location');
}

$this->__component->SetResultCacheKeys(array('SETTINGS'));