<?php

//https://youtwig.ru/local/crontab/remove_model_properties.php?intestwetrust=1&time=1562030100&PageSpeed=off
//https://twig.d6r.ru/local/crontab/remove_model_properties.php?intestwetrust=1&time=1562030100&PageSpeed=off

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('MODEL_IBLOCK_ID', 17);
define('MODEL_NAME_IBLOCK_ID', 27);

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

    class impelGetRemove
    {
        private static $auKeys = [];
        private static $sfNotFound = '/bitrix/tmp/models_not_found.csv';

        public static function suggestModel($sModelName)
        {

            $amOrder = [
                'ID' => 'DESC'
            ];

            $amSelect = [
                "ID"
            ];

            $amFilter = [
                "IBLOCK_ID" => MODEL_NAME_IBLOCK_ID,
                "=NAME" => $sModelName
            ];

            $rModels = CIBlockElement::GetList(
                $amOrder,
                $amFilter,
                false,
                false,
                $amSelect);

            $iModelId = 0;

            if ($rModels && $aModels = $rModels->GetNext()) {

                $iModelId = $aModels['ID'];

            }

            return $iModelId;

        }

        public static function getModel($afProps)
        {
            static $modelEl;

            if (!is_object($modelEl)) {
                $modelEl = new impelCIBlockElement;
            }

            $amOrder = [
                'ID' => 'DESC'
            ];

            $amSelect = [
                "ID",
            ];

            $amFilter = [
                "IBLOCK_ID" => MODEL_IBLOCK_ID,
            ];

            $amFilter = array_merge($amFilter,$afProps);

            $rModels = CIBlockElement::GetList(
                $amOrder,
                $amFilter,
                false,
                false,
                $amSelect);

            $iModelId = 0;

            if ($rModels) {

                while ($aModels = $rModels->GetNext()) {

                    $aClearProps = array(
                        'SIMPLEREPLACE_PRODUCTS' => false,
                        'SIMPLEREPLACE_VIEW' => false,
                        'SIMPLEREPLACE_INDCODE' => false,
                        'SIMPLEREPLACE_POSITION' => false
                    );

                    impelCIBlockElement::SetPropertyValuesEx($aModels['ID'], MODEL_IBLOCK_ID, $aClearProps);
					//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(MODEL_IBLOCK_ID, $aModels['ID']);

                    if ($modelEl->Update($aModels['ID'], Array('TIMESTAMP_X' => true))) {

                    }

                    $iModelId = $aModels['ID'];

                }

            }

            return $iModelId;

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

                        $bModelFound = false;

                        $astr = array_map('trim',$astr);

                        $aProperty = [];
                        $bError = false;

                        foreach ($aMap as $iNum => $sVal) {

                            if (isset($astr[$iNum]) && !empty($astr[$iNum])) {
                                $aProperty[$sVal] = $astr[$iNum];
                            } else {
                                $bError = true;
                            }

                        }

                        $cKeys = join(';',$aProperty[$sVal]);

                        if (isset(static::$auKeys[$cKeys])) {
                            $bError = true;
                        }

                        static::$auKeys[$cKeys] = $cKeys;

                        if (!$bError) {

                            $mnId = 0;

                            if (isset($aProperty['PROPERTY_model_new_link'])) {
                                $mnId = static::suggestModel($aProperty['PROPERTY_model_new_link']);
                            }

                            if ($mnId > 0) {

                                $aProperty['PROPERTY_model_new_link'] = $mnId;

                                $mnId = static::getModel($aProperty);

                                if (!$mnId) {
                                    $bError = true;
                                }

                            } else {
                                $bError = true;
                            }

                        }

                        if ($bError) {
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
        impelGetRemove::getList($sFile,$aMap,$bFirst);
    }

} catch (Exception $oException) {
    echo $oException->getMessage();
}