#!/usr/bin/php -q
<?php

//https://twig.qtwig.com/local/crontab/export_props_sync.php?intestwetrust=1

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

$iblockId = 11;
$sProp = 'DISABLE_EXPORT';

$aExport = [];

$db_profile = CCatalogExport::GetList(
    array(),
    array()
);
if ($db_profile) {
    while ($ar_profile = $db_profile->Fetch())
    {
        if (!empty($ar_profile['SETUP_VARS'])) {
            $aExport[$ar_profile['ID']] .= $ar_profile['NAME'] . ' (' . $ar_profile['FILE_NAME'] . ', ' . $ar_profile['ID'] .')';
        }
    }
}

$aModelTypes = array();

$typePropertyDB = \CIBlockPropertyEnum::GetList(
    Array(
        "DEF"=>"DESC",
        "SORT"=>"ASC"),
    Array(
        "IBLOCK_ID" => $iblockId,
        "CODE" => $sProp
    )
);

if($typePropertyDB){
    while($typePropertyFields = $typePropertyDB->GetNext()){

        if(isset($typePropertyFields["VALUE"])){
            
            $aModelTypes[$typePropertyFields["XML_ID"]] = $typePropertyFields["VALUE"];

        }

    }

}

$aModelTypes = array_map('trim',$aModelTypes);

$aDiff = array_diff($aExport,$aModelTypes);

if(!empty($aDiff)){

    $typeProperties = CIBlockProperty::GetList(
        Array(
            "sort"=>"asc",
            "name"=>"asc"
        ),
        Array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $iblockId,
            "CODE" => $sProp)
    );

    if($typeProperties) {

        while ($typeFields = $typeProperties->GetNext()) {

            $typePropertyID = $typeFields["ID"];

        }

    }

    $enumTypeNew = new CIBlockPropertyEnum;

    foreach($aDiff as $sKey => $sValue){

        $enumTypeNew->Add(
            Array(
                'XML_ID' => $sKey,
                'PROPERTY_ID' => $typePropertyID,
                'VALUE' => trim($sValue)
            )
        );

    }

}
