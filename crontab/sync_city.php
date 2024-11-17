90#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/sync_city.php?intestwetrust=1

//замены по падежам ключ - что искать, значение - на что менять по всем значениям

$arReplace = [
    'по Санкт-Петербургу и Ленинградской области' => 'по Екатеринбургу и Свердловской области', //только корень слова, иначе получим Москве - Петербурге, если будетм склонять по падежам
	'по Санкт-Петербургу, Ленинградской области и по всей стране' => 'по Екатеринбургу, Свердловской области и по всей стране', //только корень слова, иначе получим Москве - Петербурге, если будетм склонять по падежам
	'купить в Санкт-Петербурге' => 'купить в Екатеринбурге', //только корень слова, иначе получим Москве - Петербурге, если будетм склонять по падежам
	', в Санкт-Петербурге,' => ', в Екатеринбурге,',
	' в Санкт-Петербурге' => ' в Екатеринбурге',
	'по Санкт-Петербургу.' => 'по Екатеринбургу.',
	'по Санкт-Петербургу' => 'по Екатеринбургу',
];

$sDomainFrom = 'spb.youtwig.ru'; //домен откуда копировать
$sDomainTo = 'ekaterinburg.youtwig.ru'; //домен куда копировать

$fromSectId = [2085]; //раздел откуда копировать (или оставить пустым, пока что подраздел нижнего уровня, верхние разделы создать вручную)
$toSectId = [2956]; //раздел куда копировать (или оставить пустым, пока что подраздел нижнего уровня, верхние разделы создать вручную)

$elemProps = ['NAME','PREVIEW_TEXT','DETAIL_TEXT', 'ACTIVE', 'SORT']; //значения элементов которые копируем

//свойства которые копируем

// define('ONLY_TEST',true);

$strProps = [
    'PROPERTY_SEO_TITLE',
    'PROPERTY_SEO_DECRIPTION',
    'PROPERTY_SEO_KEYWORDS',
    'PROPERTY_H1_BOTTOM',
    'PROPERTY_FOR_UNION_FILTERS',
    'PROPERTY_FOR_UNION_SECTIONS',
    'PROPERTY_FOR_UNION_FILTERS_NC',
    'PROPERTY_FOR_UNION_SECTIONS_NC',
    'PROPERTY_SEO_DECRIPTION_PAGEN',
    'PROPERTY_SEO_TITLE_PAGEN',
    'PROPERTY_FILTER_URL',
    'PROPERTY_SEO_CONSTRUCTOR',
    'PROPERTY_SEO_CHANGES',
    'PROPERTY_IS_REGEXP',
    //'DOMAIN',
];

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

$already = [];

function mapValueToId($code) {

    $propertyDB = CIBlockPropertyEnum::GetList(
        Array(
            "DEF"=>"DESC",
            "SORT"=>"ASC"
        ),
        Array(
            "IBLOCK_ID" => 45,
            "CODE" => $code
        )
    );

    $fillDomainArr = [];

    if($propertyDB){
        while($propertyFields = $propertyDB->GetNext()){

            if(isset($propertyFields["XML_ID"])
                && isset($propertyFields["VALUE"])
            ){
                $fillDomainArr[$propertyFields["VALUE"]] = $propertyFields["ID"];
            }

        }

    }

    return $fillDomainArr;

}

$fillDomainArr = mapValueToId('DOMAIN');
$fillRegexpArr = mapValueToId('IS_REGEXP');

$arSelect = ['PROPERTY_COPY_ID'];
$arFilter = Array("IBLOCK_ID" => 45, "PROPERTY_DOMAIN_VALUE" => $sDomainTo);

$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

if ($res) {
    while($rule = $res->Fetch()) {
        $already[$rule['PROPERTY_COPY_ID_VALUE']] = $rule['PROPERTY_COPY_ID_VALUE'];
    }
}

$already = array_unique($already);
$already = array_filter($already);

$arSelect = array_merge($strProps,$elemProps,['ID']);
$arFilter = Array("IBLOCK_ID" => 45, "PROPERTY_DOMAIN_VALUE" => $sDomainFrom, "!ID" => $already);
$fromSectId = array_unique($fromSectId);

if (!empty($fromSectId)) {
    $arFilter['SECTION_ID'] = $fromSectId;
}

$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

if ($res) {

    $cibElement = new CIBlockElement;
    $toSectId = array_unique($toSectId);
    $toSectId = array_filter($toSectId);
    $find = array_keys($arReplace);
    $replace = array_values($arReplace);

    while($rule = $res->Fetch()) {

        $aCopyElt = [];

        foreach ($elemProps as $prop) {
            $rule[$prop] = str_ireplace($find,$replace,$rule[$prop]);
            $aCopyElt[$prop] = $rule[$prop];
        }

        $aCopyElt["IBLOCK_SECTION"] = (empty($toSectId) ? false : $toSectId);
        $aCopyElt["IBLOCK_ID"] = 45;
        $aCopyElt["MODIFIED_BY"] = 1;

        if ($ruleId = $cibElement->add($aCopyElt)) {

            $props = [];

            foreach ($strProps as $prop) {

                $propValue = $prop.'_VALUE';
                $rule[$propValue] = str_ireplace($find,$replace,$rule[$propValue]);

                $code = trim(str_ireplace('PROPERTY_','',$prop));
                $props[$code]['VALUE'] = $rule[$propValue];

            }

            $props['DOMAIN']['VALUE'] = $fillDomainArr[$sDomainTo];
            $props['IS_REGEXP']['VALUE'] = $fillRegexpArr[$props['IS_REGEXP']['VALUE']];

            $props['COPY_ID']['VALUE'] = $rule['ID'];
            impelCIBlockElement::SetPropertyValuesEx($ruleId, 45, $props);

        } else {
            echo "Error: ".$cibElement->LAST_ERROR;
        }

        if (defined('ONLY_TEST')) {
            echo $rule['ID'].'-'.$ruleId;
            die();
        }
    }
}
