<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters       = array(
	"DISPLAY_DATE"          =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE"              =>"CHECKBOX",
		"DEFAULT"           =>"Y",
	),
	"DISPLAY_NAME"          =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_NEWS_NAME"),
		"TYPE"              =>"CHECKBOX",
		"DEFAULT"           =>"Y",
	),
	"DISPLAY_PICTURE"       =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE"              =>"CHECKBOX",
		"DEFAULT"           =>"Y",
	),
	"DISPLAY_PREVIEW_TEXT"  =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE"              =>"CHECKBOX",
		"DEFAULT"           =>"Y",
	),
	"IMAGE_WIDTH"           =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_IMAGE_WIDTH"),
		"TYPE"              =>"TEXT",
		"DEFAULT"           =>"245",
	),
	"IMAGE_HEIGHT"          =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_IMAGE_HEIGHT"),
		"TYPE"              =>"TEXT",
		"DEFAULT"           =>"152",
	),
    "NUM_ITEMS"             =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_NUM_ITEMS"),
		"TYPE"              =>"TEXT",
		"DEFAULT"           =>"4",
	),
    "IBLOCK_TITLE"          =>Array(
		"NAME"              =>GetMessage("T_IBLOCK_DESC_IBLOCK_TITLE"),
		"TYPE"              =>"TEXT",
		"DEFAULT"           =>"",
	),
);
?>