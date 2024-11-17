<?php
//https://youtwig.ru/local/crontab/trunc/index.php?intestwetrust=1
//https://twig.d6r.ru/local/crontab/trunc/index.php?intestwetrust=1


$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname(__DIR__)));

ini_set('default_charset', 'utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

define('N_MAX_COUNT', 50);

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if (!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$rUri = (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/trunc/index.php?intestwetrust=1';

ignore_user_abort();

ini_set('max_execution_time', 0);
ini_set('max_input_time', 0);

define('__LAST__FILE__ID__USED__', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/eplast.txt');

$spFile = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/providerrtk.txt';
$mainElt = new CIBlockElement;

$iCount = 0;

if (CModule::IncludeModule("iblock")) {

    if (!file_exists(__LAST__FILE__ID__USED__)) {
        file_put_contents(__LAST__FILE__ID__USED__, 0);
    }

    $iLastId = file_get_contents(__LAST__FILE__ID__USED__);
    $iLastId = (int)trim($iLastId);
    file_put_contents($spFile, "");

    $arPSelect = array(
        "ID",
        "PROPERTY_REMOTE_STORE",
    );

    $arPFilter = array(
        "IBLOCK_ID" => 16,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
    );

    if ($iLastId) {
        $arPFilter['>ID'] = $iLastId;
    }

    $order = array(
        "ID" => "ASC"
    );

    $dbPres = CIBlockElement::GetList($order, $arPFilter, false, ['nTopCount' => N_MAX_COUNT], $arPSelect);

    if ($dbPres) {

        while ($apResult = $dbPres->GetNext()) {

            CIBlockElement::SetPropertyValuesEx($apResult['ID'], 16, array('REMOTE_STORE' => ''));

            $iCount = $apResult['ID'];

            $rsStore = CCatalogStoreProduct::GetList(
                array(),
                array(
                    'PRODUCT_ID' => $apResult['ID'],
                    'STORE_ID' => 9),
                false,
                false,
                array('ID'));

            if ($arStore = $rsStore->Fetch()) {
                $iCID = $arStore['ID'];
            }

            $asFields = array(
                "PRODUCT_ID" => $apResult['ID'],
                "STORE_ID" => 9,
                "AMOUNT" => 0
            );

            if ($iCID) {

                $iUpd = CCatalogStoreProduct::Update(
                    $iCID,
                    $asFields
                );

            }

            echo '.';

        }

    }

}

if ($iCount) {
    file_put_contents(__LAST__FILE__ID__USED__, $iCount);
    die('<html><header><meta http-equiv="refresh" content="'.mt_rand(0,1).';URL=\''.$rUri.'&time='.time().'&PageSpeed=off\'" /><script>setTimeout(function(){location.href="'.$rUri.'&time='.time().'&PageSpeed=off";},'.mt_rand(500,700).');</script></header><body><h1>'.time().'</h1></body></html>');
} else {
    file_put_contents(__LAST__FILE__ID__USED__, 0);
    echo 'done';
}