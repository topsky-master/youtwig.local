<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$iId = (int)trim($_REQUEST['id']);

$aBoxberryPvzFilter = [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => 43,
    'ID' => $iId
];

$aBoxberryPvzSelect = [
    'ID',
    'PROPERTY_WORKSHEDULE',
    'PROPERTY_TRIPDESCRIPTION',
    'PROPERTY_PHONE',
	'PROPERTY_ADDRESS',
	'PROPERTY_NAME',
];

$aBoxberryPvzs = [];
$rdBoxberryPvz = impelCIBlockElement::GetList(array(), $aBoxberryPvzFilter, false, false, $aBoxberryPvzSelect);
$iCount = 0;
$sHelp = '';

if ($rdBoxberryPvz) {
    while ($aBoxberryPvz = $rdBoxberryPvz->GetNext()) {

		$aAddress = [
            $aBoxberryPvz['PROPERTY_ADDRESS_VALUE'],
            $aBoxberryPvz['PROPERTY_NAME_VALUE'],
        ];

        $aHelp = [
            $aBoxberryPvz['PROPERTY_WORKSHEDULE_VALUE'],
            $aBoxberryPvz['PROPERTY_PHONE_VALUE'],
            $aBoxberryPvz['PROPERTY_TRIPDESCRIPTION_VALUE'],
        ];

		$aAddress = array_filter($aAddress);
        $sAddress = join('#',$aAddress);
        $sAddress = trim($sAddress);
        $sAddress = htmlspecialchars($sAddress,ENT_QUOTES,LANG_CHARSET);


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
