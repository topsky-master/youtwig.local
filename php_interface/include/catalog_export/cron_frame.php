#!/usr/bin/php -q
<?php
$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

if(!function_exists('log_error_handler_to_file')){
    function log_error_handler_to_file(){

        $last_error = error_get_last();

        if($last_error['type'] === E_ERROR) {

            $error_string = $last_error['type'].'-'.$last_error['message'].'-'.$last_error['file'].'-'.$last_error['line'].'-'.date('Y-m-d H:i:s')."\n";
            file_put_contents($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/catalog_export/logs/cron_logs.txt',$error_string,FILE_APPEND);
            echo $error_string;
        }
    }

}

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

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

$profile_id = $argv[1];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

register_shutdown_function('log_error_handler_to_file');

@ini_set("memory_limit", "4024M");
ignore_user_abort();

if (CModule::IncludeModule("catalog"))
{
    $profile_id = intval($profile_id);
    if ($profile_id<=0) die();

    $ar_profile = CCatalogExport::GetByID($profile_id);
    if (!$ar_profile) die();

    $strFile = CATALOG_PATH2EXPORTS.$ar_profile["FILE_NAME"]."_run.php";
    if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
    {
        $strFile = CATALOG_PATH2EXPORTS_DEF.$ar_profile["FILE_NAME"]."_run.php";
        if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
        {
            die();
        }
    }

    $arSetupVars = array();
    $intSetupVarsCount = 0;
    if ('Y' != $ar_profile["DEFAULT_PROFILE"])
    {
        mb_parse_str($ar_profile["SETUP_VARS"], $arSetupVars);
        if (!empty($arSetupVars) && is_array($arSetupVars))
        {
            $intSetupVarsCount = extract($arSetupVars, EXTR_SKIP);
        }
    }

    CCatalogDiscountSave::Disable();
    include($_SERVER["DOCUMENT_ROOT"].$strFile);
    CCatalogDiscountSave::Enable();

    CCatalogExport::Update($profile_id, array(
            "=LAST_USE" => $DB->GetNowFunction()
        )
    );
}
?>