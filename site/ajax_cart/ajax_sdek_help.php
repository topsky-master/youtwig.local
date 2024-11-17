<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$iId = (int)trim($_REQUEST['id']);

$aSdekPvzFilter = [
    'ACTIVE' => 'Y',
    'IBLOCK_ID' => 44,
    'ID' => $iId
];

$aSdekPvzSelect = [
    'ID',
    'PROPERTY_WORKTIME',
    'PROPERTY_ADDRESSCOMMENT',
    'PROPERTY_PHONE',
	'PROPERTY_FULLADDRESS',
	'PROPERTY_CODE',
];

$aSdekPvzs = [];
$rdSdekPvz = impelCIBlockElement::GetList(array(), $aSdekPvzFilter, false, false, $aSdekPvzSelect);
$iCount = 0;
$sHelp = '';

if ($rdSdekPvz) {
    while ($aSdekPvz = $rdSdekPvz->GetNext()) {

        $aHelp = [
            $aSdekPvz['PROPERTY_WORKTIME_VALUE'],
            $aSdekPvz['PROPERTY_PHONE_VALUE'],
            $aSdekPvz['PROPERTY_ADDRESSCOMMENT_VALUE'],
        ];

		$aAddress = [
            $aSdekPvz['PROPERTY_FULLADDRESS_VALUE'],
            $aSdekPvz['PROPERTY_CODE_VALUE'],
        ];


		$aAddress = array_filter($aAddress);
        $sAddress = join('#S',$aAddress);
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
