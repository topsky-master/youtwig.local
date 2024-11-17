<?


$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('MERGE_IBLOCK_ID', 11);

set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

//https://youtwig.ru/local/crontab/bind_question.php?intestwetrust=1

CModule::IncludeModule('iblock');

$apFilter = [
    "IBLOCK_ID" => 11,
    "PROPERTY_QUESTION_TOPIC_ID" => false
];

$rProducts = CIBlockElement::GetList(
    [],
    $apFilter,
    false,
    false,
    ['ID','NAME']);

$aReturn = [];

$iElt = new CIBlockElement;

if ($rProducts) {

    while ($aProducts = $rProducts->GetNext()) {

        $aProducts = array_map('trim',$aProducts);

        if (isset($aProducts['ID'])
            && !empty($aProducts['ID'])) {

            if ($iEltId = $iElt->Add([
                    'NAME' => $aProducts['NAME'],
                    'PREVIEW_TEXT' => ' ',
                    'DETAIL_TEXT' => ' ',
                    'IBLOCK_ID' => 52
                ]
            )) {

                CIBlockElement::SetPropertyValuesEx($aProducts['ID'], 11, ['QUESTION_TOPIC_ID' => $iEltId]);
                \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(11, $aProducts['ID']);

            }

        }
    }
}