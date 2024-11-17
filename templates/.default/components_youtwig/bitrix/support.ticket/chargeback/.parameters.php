<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters['CATEGORY'] = array(
    'NAME' => GetMessage('SUP_TICKET_CATEGORY_SID'),
    'TYPE' => 'STRING'
);

$arTemplateParameters['DESCRIPTION'] = array(
    'NAME' => GetMessage('SUP_TICKET_DESCRIPTION'),
    'TYPE' => 'STRING',
    'ROWS' => 10,
    'COLS' => 50
);

$arTemplateParameters['TITLE'] = array(
    'NAME' => GetMessage('SUP_TICKET_NAME'),
    'TYPE' => 'STRING',

);

$arTemplateParameters['RULES_ID'] = array(
    'NAME' => GetMessage('SUP_TICKET_RULES_ID'),
    'TYPE' => 'STRING',

);

$arTemplateParameters['REQUIRED_FIELDS'] = array(
    'NAME' => GetMessage('SUP_REQUIRED_FIELDS'),
    'TYPE' => 'STRING',

);

?>
