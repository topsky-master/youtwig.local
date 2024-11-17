#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/seodetailsection.php?intestwetrust=1
$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

\CModule::IncludeModule('iblock');

$aSelect = Array(
    'ID',
    'IBLOCK_ID',
	'CODE'
);

$aFilter = Array(
    'IBLOCK_ID' => 11,
    'GLOBAL_ACTIVE' => 'Y'
);

CModule::IncludeModule("iblock");

$aValues = array();

$dSect = CIBlockSection::GetList(
    ($aOrder = Array("NAME" => "ASC")),
    $aFilter,
    false,
	$aSelect
);

if($dSect) {
	while($aSect = $dSect->GetNext()) {
		$aValues[$aSect['ID']] = $aSect['NAME'].' ('.$aSect['CODE'].')';	
	}
}

$aModelTypes = array();

$typePropertyDB = \CIBlockPropertyEnum::GetList(
	Array(
		"DEF"=>"DESC",
        "SORT"=>"ASC"),
    Array(
		"IBLOCK_ID" => 42,
        "CODE" => 'DETAIL_SECTIONS'
    )
);

if($typePropertyDB){
	while($typePropertyFields = $typePropertyDB->GetNext()){

		if(isset($typePropertyFields["VALUE"])){

			$aModelTypes[$typePropertyFields["VALUE"]] = $typePropertyFields["VALUE"];

        }

	}

}
 
$aModelTypes = array_map('trim',$aModelTypes);
$aDiff = array_diff($aValues,$aModelTypes);

if(!empty($aDiff)){

	$typeProperties = CIBlockProperty::GetList(
        Array(
			"sort"=>"asc",
            "name"=>"asc"
        ),
        Array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => 42,
            "CODE" => "DETAIL_SECTIONS")
    );

    if($typeProperties) {

        while ($typeFields = $typeProperties->GetNext()) {

            $typePropertyID = $typeFields["ID"];

        }

    }

    $enumTypeNew = new CIBlockPropertyEnum;

    foreach($aDiff as $sValue){
		
		$xml_id = array_search($sValue,$aValues);

		$enumTypeNew->Add(
			Array(
				'PROPERTY_ID' => $typePropertyID,
                'VALUE' => trim($sValue),
				'XML_ID' => $xml_id
            )
        );

    }

}
