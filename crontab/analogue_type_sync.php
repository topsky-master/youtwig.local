#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/analogue_type_sync.php?intestwetrust=1

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

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$IBLOCK_ID = 11;
$sProp = 'ANALOGUE_TYPE';

$dprop = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
$fields = [];

if ($dprop) {
    while ($aprop = $dprop->GetNext())
    {
        $fields[$aprop['CODE']] = $aprop['NAME'];
    }
}

$dbPropList = CIBlockProperty::GetList(
    Array(
        "sort" => "asc",
        "name" => "asc"
    ),
    Array(
        "ACTIVE" => "Y",
        "CODE" => $sProp,
        "IBLOCK_ID" => $IBLOCK_ID,
    )
);

$saved = array();
$typePropertyID = 0;

if ($dbPropList) {
    while ($arPropList = $dbPropList->GetNext()) {

        $typePropertyID = $arPropList["ID"];

        $propertyEnums = CIBlockPropertyEnum::GetList(
            Array(
                "SORT" => "ASC"
            ),
            Array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "PROPERTY_ID" => $typePropertyID
            )
        );

        if($propertyEnums) {
            while($enumFields = $propertyEnums->GetNext()) {
                $saved[$enumFields["XML_ID"]] = $enumFields["VALUE"];
            }
        }

        break;

    }

}

sort($saved, SORT_NATURAL);

$diff = array_diff($fields,$saved);

if (!empty($diff)) {

    $enumTypeNew = new CIBlockPropertyEnum;

    foreach($aDiff as $sValue){
		
		$xml_id = array_search($sValue,$aValues);

        $res = $enumTypeNew->GetList(
            Array(),
            Array(
                'PROPERTY_ID' => $typePropertyID,
                'XML_ID' => $xml_id
            )
        );
        if(mysqli_num_rows($res->result) == 0 ) {
            $enumTypeNew->Add(
                Array(
                    'PROPERTY_ID' => $typePropertyID,
                    'VALUE' => trim($sValue),
                    'XML_ID' => $xml_id
                )
            );
        }
        else {
            while ($row = $res->GetNext()) {
                $enumTypeNew->Update($row['ID'], array('VALUE' => trim($sValue)));
            }
        }
    }

}