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

set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if (!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

// Массив исключений
$excludedProperties = array(513); // ID свойств, которые нужно исключить

foreach (array(11, 17, 38) as $IBLOCK_ID) {

    $dbPropList = CIBlockProperty::GetList(
        array(
            "sort" => "asc",
            "name" => "asc"
        ),
        array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_TYPE" => "L"
        )
    );

    while ($arPropList = $dbPropList->GetNext()) {

        // Проверка на исключение
        if (in_array($arPropList["ID"], $excludedProperties)) {
            continue; // Пропускаем обработку этого свойства
        }

        $propertyEnums = CIBlockPropertyEnum::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "PROPERTY_ID" => $arPropList["ID"]
            )
        );

        $arPrpopValues = array();

        if ($propertyEnums) {
            while ($enumFields = $propertyEnums->GetNext()) {
                $arPrpopValues[$enumFields["ID"]] = $enumFields["VALUE"];
            }
        }

        natcasesort($arPrpopValues);

        $sortOrder = 100;
        $ibpenum = new CIBlockPropertyEnum;

        foreach ($arPrpopValues as $enumFieldsId => $enumFieldsValue) {
            $ibpenum->Update($enumFieldsId, array('SORT' => $sortOrder));
            $sortOrder += 100;
        }

    }
}
