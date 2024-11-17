#!/usr/bin/php -q
<?php

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

function sync_by_property($sProp,$iblockId = 17){

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

                $aModelTypes[$typePropertyFields["VALUE"]] = $typePropertyFields["VALUE"];

            }

        }

    }

    $aCommentModelTypes = array();

    $typePropertyDB = CIBlockPropertyEnum::GetList(
        Array(
            "DEF"=>"DESC",
            "SORT"=>"ASC"),
        Array(
            "IBLOCK_ID" => 38,
            "CODE" => $sProp
        )
    );

    if($typePropertyDB){
        while($typePropertyFields = $typePropertyDB->GetNext()){

            if(isset($typePropertyFields["VALUE"])){

                $aCommentModelTypes[$typePropertyFields["VALUE"]] = $typePropertyFields["VALUE"];

            }

        }

    }

    $aCommentModelTypes = array_map('trim',$aCommentModelTypes);
    $aModelTypes = array_map('trim',$aModelTypes);

    $aDiff = array_diff($aModelTypes,$aCommentModelTypes);

    if(!empty($aDiff)){

        $typeProperties = CIBlockProperty::GetList(
            Array(
                "sort"=>"asc",
                "name"=>"asc"
            ),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => 38,
                "CODE" => $sProp)
        );

        if($typeProperties) {

            while ($typeFields = $typeProperties->GetNext()) {

                $typePropertyID = $typeFields["ID"];

            }

        }

        $enumTypeNew = new CIBlockPropertyEnum;

        foreach($aDiff as $sValue){

            $enumTypeNew->Add(
                Array(
                    'PROPERTY_ID' => $typePropertyID,
                    'VALUE' => trim($sValue)
                )
            );

        }

    }

}

sync_by_property("type_of_product");
sync_by_property("manufacturer");
sync_by_property("TYPEPRODUCT",11);