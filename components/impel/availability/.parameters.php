<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "CACHE_TIME" => array(
            "DEFAULT" => 36000000,
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("IMPEL_MODELS_CACHE_TIME"),
        ),
        "PRODUCT_ID" => array(
            "DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_PRODUCT_ID"),
        ),
        "PRICE" => array(
            "DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_PRICE"),
        ),
        "NOT_MUCH" => array(
            "DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_NOT_MUCH"),
        ),
        "MESS_BTN_BUY" => array(
            "DEFAULT" => "",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_MESS_BTN_BUY"),
        ),
        "ONE_CLICK_ORDER" => array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_ONE_CLICK_ORDER"),
        ),
        "SCHEMA_AVAIL" => array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_SCHEMA_AVAIL"),
        ),
        "ONE_CLICK_PREORDER" => array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_ONE_CLICK_PREORDER"),
        ),
        "IN_STOCK_LABEL" => array(
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "NAME" => GetMessage("IMPEL_AVAILABILITY_IN_STOCK_LABEL"),
        ),
        "HAS_PRICE" => array(
			"NAME" => GetMessage("IMPEL_AVAILABILITY_HAS_PRICE"),
        ),
		"PRODUCT_URL" => array(
			"NAME" => GetMessage("IMPEL_AVAILABILITY_PRODUCT_URL"),
        ),

    )
);
?>