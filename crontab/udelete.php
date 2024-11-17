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

$aNavParams = array('NAV_PARAMS' => array('nTopCount' => 100));

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
    $aNavParams = false;
}

if(!isset($_REQUEST['intestwetrust'])) die();


//https://youtwig.ru/local/crontab/udelete.php?intestwetrust=1&time=1562030100&PageSpeed=off


if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/udeleted.csv')){
    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/udeleted.csv','a+');

} else {
    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/udeleted.csv','w+');
}

$skip = 0;

if(file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/udellast.txt')){
    $skip = (int)file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/udellast.txt');
}

$groups = array (
    0 => '3',
    1 => '4',
    2 => '5',
    3 => '2',
);

$filter = Array(
    'GROUPS_ID' => $groups
);

if($skip){
    $filter['>ID'] = $skip;
}

$rUs = CUser::GetList(
    ($by="id"),
    ($order="asc"),
    $filter,
    $aNavParams
);

$cUser = new Cuser;
$uDel = false;

while($aUs = $rUs->GetNext()){

    if(isset($aUs['ID'])
        && !empty($aUs['ID'])){

        $uDel = $aUs['ID'];

        $ugroups = CUser::GetUserGroup($aUs['ID']);
        $array_diff = array_diff($ugroups,$groups);

        if(empty($array_diff)
            && $cUser->Delete($aUs['ID'])){
            fputcsv($fp,$aUs,';');
        }

    }

}

file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/udellast.txt',(int)$uDel);

fclose($fp);

if($uDel){
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/udelete.php?intestwetrust=1&PageSpeed=off&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
} else {
    echo 'done';
}