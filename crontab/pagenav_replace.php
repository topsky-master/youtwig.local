#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/pagenav_replace.php?intestwetrust=1
//define('ONLY_TEST',true);

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

require($_SERVER["DOCUMENT_ROOT"]."bitrix/modules/main/include/prolog_before.php");

$arFilter = [
    'IBLOCK_ID' => 45
];

$arSelect = [
    'ID',
    'PROPERTY_SEO_DECRIPTION_PAGEN',
    'PROPERTY_SEO_TITLE_PAGEN',
];

$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

if ($res) {

    $cibElement = new CIBlockElement;

    while($rule = $res->Fetch()) {

        $ruleId = $rule['ID'];

        $props = [];

        if (isset($rule['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'])
            && !empty($rule['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'])
            && (mb_stripos($rule['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'],'[pagenum]') !== false
            && mb_stripos($rule['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'],'страница') === false)) {
            $props['SEO_DECRIPTION_PAGEN'] = $rule['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'];
            $props['SEO_DECRIPTION_PAGEN'] = str_ireplace('[pagenum]','страница [pagenum]',$props['SEO_DECRIPTION_PAGEN']);
        }

        if (isset($rule['PROPERTY_SEO_TITLE_PAGEN_VALUE'])
            && !empty($rule['PROPERTY_SEO_TITLE_PAGEN_VALUE'])
            && (mb_stripos($rule['PROPERTY_SEO_TITLE_PAGEN_VALUE'],'[pagenum]') !== false
                && mb_stripos($rule['PROPERTY_SEO_TITLE_PAGEN_VALUE'],'страница') === false)) {
            $props['SEO_TITLE_PAGEN'] = $rule['PROPERTY_SEO_TITLE_PAGEN_VALUE'];
            $props['SEO_TITLE_PAGEN'] = str_ireplace('[pagenum]','страница [pagenum]',$props['SEO_TITLE_PAGEN']);
        }

        if (!empty($props)) {
            impelCIBlockElement::SetPropertyValuesEx($ruleId, 45, $props);
            $cibElement->Update($ruleId,['TIMESTAMP_X' => true]);
        }
    }
}
