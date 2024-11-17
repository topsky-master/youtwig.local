<?php

//https://youtwig.ru/local/crontab/get_model_views.php?intestwetrust=1&time=1562030100&PageSpeed=off
//https://twig.d6r.ru/local/crontab/get_model_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('MODEL_VIEW_IBLOCK_ID', 34);
define('MODEL_INCDOE_IBLOCK_ID', 35);

set_time_limit(0);
define("LANG", "s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if (!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$sFile = 'braun.csv';
$bFirst = true;
$aMap = [ 0 => 'PROPERTY_type_of_product_VALUE', 1 => 'PROPERTY_manufacturer_VALUE', 2 => 'PROPERTY_model_new_link'];

try {

    class impelGetView
    {

        private static $sfNotFound = '/bitrix/tmp/braun1.csv';

        public static function suggestView($sModelId)
        {

            $amOrder = [
                'ID' => 'DESC'
            ];

            $amSelect = [
                "NAME"
            ];

            $amFilter = [
                "IBLOCK_ID" => MODEL_VIEW_IBLOCK_ID,
                "ID" => $sModelId
            ];

            $sViewName = '';

            $rModels = CIBlockElement::GetList(
                $amOrder,
                $amFilter,
                false,
                false,
                $amSelect);

            if ($rModels && $aModels = $rModels->GetNext()) {

                $sViewName = $aModels['NAME'];

            }

            return $sViewName;

        }

        public static function suggestIndcode($sModelId)
        {

            $amOrder = [
                'ID' => 'DESC'
            ];

            $amSelect = [
                "NAME"
            ];

            $amFilter = [
                "IBLOCK_ID" => MODEL_INCDOE_IBLOCK_ID,
                "ID" => $sModelId
            ];

            $sIndName = '';

            $rModels = CIBlockElement::GetList(
                $amOrder,
                $amFilter,
                false,
                false,
                $amSelect);

            if ($rModels && $aModels = $rModels->GetNext()) {

                $sIndName = $aModels['NAME'];

            }

            return $sIndName;

        }

        public static function getList($sFile, $aMap, $bFirst = true)
        {

            $sFile = $_SERVER["DOCUMENT_ROOT"].'/bitrix/tmp/'.$sFile;
            $sNotFoundFile = static::$sfNotFound;

            if (file_exists($sFile) && is_file($sFile)) {

                $rfp = fopen($sFile,'r+');
                $rfp1 = fopen($_SERVER['DOCUMENT_ROOT'].$sNotFoundFile,'w+');

                if ($rfp) {

                    while ($astr = fgetcsv($rfp,0,';')) {

                        if ($bFirst) {
                            $bFirst = false;
                            continue;
                        }

                        $astr = array_map('trim',$astr);

                        $sViewName = static::suggestView($astr[4]);

                        $bError = false;

                        if ($sViewName) {
                            $astr[4] = $sViewName;
                        } else {
                            $bError = true;
                        }

                        $sIndName = static::suggestIndcode($astr[6]);

                        if ($sIndName) {
                            $astr[6] = $sIndName;
                        }  else {
                            $bError = true;
                        }

                        if (!$bError) {
                            fputcsv($rfp1,$astr,';');
                        }


                    }

                }

                fclose($rfp);
                fclose($rfp1);

                echo 'finished';

            } else {
                throw new Exception('Файл с моделями не найден');
            }

        }

    }

    if (CModule::IncludeModule("iblock")) {
        impelGetView::getList($sFile,$aMap,$bFirst);
    }

} catch (Exception $oException) {
    echo $oException->getMessage();
}