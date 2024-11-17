#!/usr/bin/php -q
<?php

if (isset($argc) && ($argc > 0) && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) { 
	die();
}

//https://youtwig.ru/local/crontab/analogue_cache.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

ini_set('default_charset','utf-8');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

$filter = Array
(
    "ACTIVE" => "Y",
    "!GROUPS_ID" => Array(1,6,7)
);

$count = 0;
$user = new CUser;

$rfp = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/userphone.csv','w+');

$rsUsers = CUser::GetList(($by="personal_country"), ($order="desc"), $filter);

if ($rsUsers) {
    while ($arUser = $rsUsers->Fetch()) {

        $phone = '';

        if (!empty($arUser['PERSONAL_PHONE'])) {
            $phone = $arUser['PERSONAL_PHONE'];
        }

        if (!empty($arUser['PERSONAL_MOBILE'])) {
            $phone = $arUser['PERSONAL_MOBILE'];

        }

        if (!empty($phone)) {

            $phoneRecord = \Bitrix\Main\UserPhoneAuthTable::getList([
                'filter' => [
                    '=USER_ID' => $arUser['ID']
                ],
                'select' => ['USER_ID', 'PHONE_NUMBER', 'USER.ID', 'USER.ACTIVE'],
            ])->fetch();

            if(!(isset($phoneRecord['PHONE_NUMBER']) && !empty($phoneRecord['PHONE_NUMBER']))) {

                $phone = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($phone);
                if (!empty($phone)) {

                    $fields = Array(
                        "PHONE_NUMBER"              => $phone,
                    );

                    $user->Update($arUser['ID'], $fields);
                    ++$count;
                    if($user->LAST_ERROR) {
                        fputcsv($rfp,['https://youtwig.ru/bitrix/admin/user_edit.php?lang=ru&ID='.$arUser['ID'].'&user_edit_active_tab=edit3',$phone,$user->LAST_ERROR],';');
                    }

                }
            }

        }

    }
}

fclose($rfp);

echo $count;