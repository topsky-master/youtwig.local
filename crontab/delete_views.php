<?php

//https://youtwig.ru/local/crontab/delete_views.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetDoubleViews{

    private static $maxCount = 1;
    private static $rFp = false;

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews_last.txt'));
        $vFound = 0;

        static::$rFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews.csv', 'a+');


        $aVSelect = Array(
            "ID",
            "NAME"
        );

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "ACTIVE" => "Y"
        );

        $aVNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => 1,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );

        $rViews = CIBlockElement::GetList(
            Array(
                'CNT' => 'DESC'
            ),
            $aVFilter,
            Array('NAME'),
            $aVNavParams,
            $aVSelect);

        $subFound = false;

        if($rViews) {

            while ($aViews = $rViews->GetNext()) {

                $vFound = $aViews['NAME'];

                if ($aViews['CNT'] > 1
                    && $vFound != 'Без вида') {

                    $subFound = true;

                    $auViews = array();

                    static::getView($vFound,$auViews);
                    static::checkDoubles($auViews);
                }
            }
        }

        fclose(static::$rFp);

        return $subFound ? ++$skip : 0;

    }

    private static function getView($vName,&$auModels)
    {
        $aVSelect = Array(
            "ID",
            "NAME"
        );

        //MANUFACTURER
        //TYPE_OF_PRODUCT

        $aVFilter = Array(
            "IBLOCK_ID" => 34,
            "=NAME" => $vName,
            "ACTIVE" => "Y"
        );

        $rViews = CIBlockElement::GetList(
            Array(
                'PREVIEW_PICTURE' => 'DESC',
                'TIMESTAMP_X' => 'DESC'
            ),
            $aVFilter,
            false,
            false,
            $aVSelect);

        if($rViews){

            while($aViews = $rViews->GetNext()){
                $auModels[$aViews['ID']] = $aViews['NAME'];
            }

        }


    }

    private static function checkDoubles($avCheck){

        $oiElt = new CIBlockElement;

        if(sizeof($avCheck) > 1){

            echo sizeof($avCheck).'<br />';

            $icvId = key($avCheck);
            reset($avCheck);

            echo $icvId.'<br />';

            $iFirst = true;

            foreach($avCheck as $ivId => $ivName){

                echo $ivId.'<br />';

                if($iFirst){
                    $iFirst = false;
                    continue;
                }


                $aMFilter = Array(
                    'IBLOCK_ID' => 17,
                    'PROPERTY_VIEW' => $ivId,
                    'ACTIVE' => 'Y'
                );

                $aMSelect = Array(
                    'ID',
                    'NAME'
                );

                $rModels = CIBlockElement::GetList(
                    Array(
                    ),
                    $aMFilter,
                    false,
                    false,
                    $aMSelect);

                if($rModels){

                    while($aModels = $rModels->GetNext()){

                        if(isset($aModels['ID'])
                            && !empty($aModels['ID'])){

                            $rmDB = CIBlockElement::GetProperty(
                                17,
                                $aModels['ID'],
                                Array(),
                                Array("CODE" => "VIEW")
                            );

                            if ($rmDB) {

                                $amViews = array();

                                while ($amView = $rmDB->GetNext()) {

                                    if (isset($amView['VALUE'])
                                        && !empty($amView['VALUE'])
                                    ) {

                                        $amViews[] = $amView['VALUE'];

                                    }

                                }

                                $bHasChanged = false;

                                foreach($amViews as $imKey => $imId){

                                    if($imId == $ivId){

                                        $amViews[$imKey] = $icvId;
                                        $bHasChanged = true;

                                    }

                                }

                                if($bHasChanged){

                                    $acView = array();

                                    foreach($amViews as $iViewId){

                                        $acView['VIEW'][] = array(
                                            'VALUE' => $iViewId,
                                            'DESCRIPTION' => '');
                                    }

                                    CIBlockElement::SetPropertyValuesEx(
                                        $aModels['ID'],
                                        17,
                                        $acView);

									//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $aModels['ID']);
                                    $auModel = Array('TIMESTAMP_X' => true);

                                    if ($oiElt->Update($aModels['ID'], $auModel)) {

                                        fputcsv(static::$rFp,array($icvId,$ivId,$aModels[ID]),';');

                                    } else {

                                        if (isset($oiElt->LAST_ERROR)) {
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/views_log.txt', 'Model IB 17: ' . trim($aModels['ID']) . ',' . ', ' . $oiElt->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                        }

                                    }

                                }

                            }

                        }

                    }

                }

                $aView = Array(
                    'ACTIVE' => 'N',
                    'TIMESTAMP_X' => true
                );

                fputcsv(static::$rFp,array($icvId,$ivId),';');

                if ($oiElt->Update($ivId, $aView)) {

                } else {

                    if (isset($oiElt->LAST_ERROR)) {
                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/views_log.txt', 'Model IB 34: ' . trim($ivId) . ',' . ', ' . $oiElt->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                    }

                }

                die('<html><head><meta HTTP-EQUIV="refresh" content="0;url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/delete_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');

            }

        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="0;url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/delete_views.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getviews_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDoubleViews::getList();