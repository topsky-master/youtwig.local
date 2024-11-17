<?php

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

$ids = isset($_COOKIE['favorites_cookies']) ? trim($_COOKIE['favorites_cookies']) : '';
$ids = explode(',',$ids);
$ids = array_map('trim',$ids);
$ids = array_map('intval',$ids);
$ids = array_filter($ids);
$ids = array_unique($ids);

if ($USER->IsAuthorized()) {

    $userDB = $USER->GetByID($USER->GetID());
    $userFields = $userDB->Fetch();

    $idString = join(',',$ids);

    $fields 		= Array(
        "UF_FAVORITES" => $idString
    );

    $USER->Update($USER->GetID(), $fields);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($ids);
