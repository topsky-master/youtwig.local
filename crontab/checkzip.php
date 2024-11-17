<?php

//https://youtwig.ru/local/crontab/checkzip.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelCheckZipProps{

    private static $countStrings = 2000000;
    private static $aLocTypes = array();

    public static function getList(){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function getLocationTypes(){

        $types = array();
        $res = \Bitrix\Sale\Location\TypeTable::getList(array('select' => array('ID', 'CODE')));
        while ($item = $res->fetch()) {
            $types[$item['ID']] = $item['CODE'];
        }

        static::$aLocTypes = $types;

        return $types;

    }

    private static function checkList(){

        // require_once dirname(dirname(__DIR__)).'/bitrix/tmp/addresses.php';

        $skip = (int)trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_zip_props_last.txt'));
        $pfOpen = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/results_.csv','rb');

        if(!empty($skip)){
            fseek($pfOpen,$skip);
        }

        if($skip > 0){

            $flag = 'a+';
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/iloczips.csv',$flag);

        } else {

            $flag = 'w+';
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_zip_props_last.txt', 0);
            $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/iloczips.csv',$flag);
            fputcsv($fp,array('ID (сайт)','Адрес (сайт)','ZIP (сайт)','Адрес (Кладр)','ZIP (Кладр)', 'ID (Кладр)','Тип (кладр)','Сокр (кладр)'),';');

        }

        $iStrings = 0;

        $aLocTypes = static::getLocationTypes();

        //$addresses $code2sab

        while($aString = fgetcsv($pfOpen,0, ';')){

            $aString = array_map('trim',$aString);
            ++$iStrings;

            $skip = ftell($pfOpen);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_zip_props_last.txt', $skip);

            //if(static::$countStrings < $iStrings)
            //break;

            if(empty($aString[3])
                || !in_array($aString[2],array('STREET','CITY','VILLAGE')))
                continue;

            $locName = $aString[0];

            $aCsvLocParents = static::getParentsCsv($aString[1],$addresses,$code2sab);

            $bCsvStreetExists = array_search('STREET',$aCsvLocParents);
            $bCsvVillageExists = array_search('VILLAGE',$aCsvLocParents);
            $bCsvCityExists = array_search('CITY',$aCsvLocParents);

            if($bCsvStreetExists !== false
                && $bCsvVillageExists === false
                && $bCsvCityExists === false)
                continue;

            usleep(200);

            $aLocs = static::searchLocation($locName,array($aString[2]),$aString[3]);

            if(!empty($aLocs)){

                if(!empty($aCsvLocParents)){

                    foreach($aLocs as $aLoc){
                        $aLocParents = array();
                        static::getParentLeaf($aLoc['ID'],$aLocParents);

                        if(!empty($aLocParents)){

                            $anyFound = false;

                            foreach($aCsvLocParents as $acName => $acTypeid){

                                $acLocName = array_search($acTypeid,$aLocParents);
                                $bVillageExists = array_search('VILLAGE',$aLocParents);

                                if($bCsvStreetExists !== false
                                    && $bCsvVillageExists === false
                                    && $bVillageExists !== false){

                                    $anyFound = false;
                                    break;
                                }

                                if($acLocName !== false){

                                    if(mb_stripos($acLocName,$acName) !== false){
                                        $anyFound = true;
                                    } else {
                                        $anyFound = false;
                                        break;
                                    }

                                } else {

                                    $anyFound = false;
                                    break;

                                }
                            }

                            if($anyFound){

                                $locFullName = Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay($aLoc['CODE']);

                                $locFullNameKladr = join(', ',array_keys($aCsvLocParents));
                                fputcsv($fp,array($aLoc['ID'],$locFullName,$aLoc['ZIP'],$locFullNameKladr,$aString[3],$aString[4],$aString[5],$aString[6]),';');

                            }



                        }


                    }


                }


            }




        }

        $skip = ftell($pfOpen);
        fclose($pfOpen);

        return $iStrings ? $skip : 0;

    }

    private static function getParentLeaf($pValue,&$locParents){

        $data = array(
            'select' => array(
                '*',
                'LOC_NAME' => 'NAME.NAME',
                'ZIP' => 'EXTERNAL.XML_ID'
            ),
            'filter' => array(
                'ID' => $pValue,
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID
            )
        );

        $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

        if($rLoc) {

            $aLoc = $rLoc->fetch();
            $atTypes = (static::$aLocTypes);

            $locParents[$aLoc['LOC_NAME']] = $atTypes[$aLoc['TYPE_ID']];

            if(isset($aLoc['PARENT_ID'])
                && !empty($aLoc['PARENT_ID'])
            ) {

                static::getParentLeaf($aLoc['PARENT_ID'],$locParents);

            }

        }

    }

    private static function searchLocation($sPrase, array $aTypes, $zipCode){

        $offset = isset($_REQUEST['offset']) ? (int)trim($_REQUEST['offset']) : 0;
        $locType = isset($_REQUEST['loctype']) ? trim($_REQUEST['loctype']) : 'city';
        $atTypes = array_flip(static::$aLocTypes);

        if(empty($aTypes)){

            $filter = array(
                '%NAME.NAME' => $sPrase,
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID
            );

        } else {

            if(sizeof($aTypes) > 1){

                $filter = array(
                    'LOGIC' => 'OR');

                foreach($aTypes as $aType){

                    $filter[] = array(
                        '%NAME.NAME' => $sPrase,
                        '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                        '=TYPE_ID' => $atTypes[$aType]
                    );

                }

            } else {

                $filter = array(
                    '%NAME.NAME' => $sPrase,
                    '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                    '=TYPE_ID' => $atTypes[current($aTypes)]
                );

            }

        }

        $aData = array(
            'select' => array(
                '*',
                'LOC_NAME' => 'NAME.NAME',
                'ZIP' => 'EXTERNAL.XML_ID'
            ),
            'filter' => $filter,
            'order' => array(
                'LEFT_MARGIN' => 'asc'
            )
        );

        $rLoc = \Bitrix\Sale\Location\LocationTable::getList($aData);

        $arData = array();

        if($rLoc) {

            while($aLoc = $rLoc->fetch()){

                if($aLoc['ZIP'] == $zipCode){
                    break;
                }

                $arData[] = $aLoc;

            }

        }

        return $arData;
    }

    private function getParentsCsv($fullString,&$addresses,&$code2sab){
        $aLocParents = array();

        $pCodes = explode('~',$fullString);

        foreach($pCodes as $sCode){

            $aLocParents[$addresses[$sCode]] = $code2sab[$sCode];

        }

        return $aLocParents;
    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_zip_props_last.txt', $skip);
            header('Location: '.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/checkzip.php?intestwetrust=1&PageSpeed=off&time='.time().'');
            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/checkzip.php?intestwetrust=1&PageSpeed=off&time='.time().'";},'.mt_rand(500,700).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_zip_props_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelCheckZipProps::getList();