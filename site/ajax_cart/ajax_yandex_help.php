<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$iId = (int)trim($_REQUEST['id']);

$aYandexPvzFilter = [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => 46,
    'ID' => $iId
];

$aYandexPvzSelect = [
    'ID',
    'PROPERTY_WORKSHEDULE',
    'PROPERTY_TRIPDESCRIPTION',
    'PROPERTY_PHONE',
	'PROPERTY_ADDRESS',
	'PROPERTY_NAME',
];

$aYandexPvzs = [];
$rdYandexPvz = impelCIBlockElement::GetList(array(), $aYandexPvzFilter, false, false, $aYandexPvzSelect);
$iCount = 0;
$sHelp = '';

if ($rdYandexPvz) {
    while ($aYandexPvz = $rdYandexPvz->GetNext()) {

		$aHelp = [
            $aYandexPvz['PROPERTY_NAME_VALUE'],
			$aYandexPvz['PROPERTY_WORKSHEDULE_VALUE'],
            $aYandexPvz['PROPERTY_PHONE_VALUE'],
            $aYandexPvz['PROPERTY_TRIPDESCRIPTION_VALUE'],
        ];

		$aHelp = array_filter($aHelp);
        $sHelp = join(', ',$aHelp);
        $sHelp = trim($sHelp);
        $sHelp = htmlspecialchars($sHelp,ENT_QUOTES,LANG_CHARSET);
    }
}

if(!headers_sent()) {
    header('Content-Type: application/json');
}

$return = array('message' => $sHelp, 'address' => $sAddress);
echo json_encode($return);
