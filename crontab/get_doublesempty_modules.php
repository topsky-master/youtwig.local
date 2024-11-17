<?php

//https://youtwig.ru/local/crontab/get_doublesempty_modules.php?intestwetrust=1&time=1562030100&PageSpeed=off

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

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetDoublesEmpty{

    private static $maxCount = 5;
    private static $rdFp = false;
    private static $rFp = false;

    private static $aNames = array();
    private static $aCodes = array();

    public static function getList($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }

    private static function checkList(){

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt')){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt',0);
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt'));
        $mFound = 0;

        if($skip > 0){

            static::$rFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty.csv', 'a+');
            static::$rdFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getsdoubles.csv', 'a+');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt', 0);
            static::$rFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty.csv', 'w+');
            static::$rdFp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getsdoubles.csv', 'w+');

        }

        $aMSelect = Array(
            "ID",
			"PROPERTY_model_new_link"
        );

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
		    "ACTIVE" => "Y",
			//"PROPERTY_VERSION" => false,
			'PROPERTY_manufacturer_VALUE' => array('Gorenje'),
		);

        $aMNavParams = Array(
            'nTopCount' => false,
            'iNumPage' => $skip,
            'nPageSize' => static::$maxCount,
            'checkOutOfRange' => true
        );
		
		$rModels = impelCIBlockElement::GetList(
            Array(
                'ID' => 'ASC'
            ),
            $aMFilter,
            false,
            $aMNavParams,
            $aMSelect);

		if($rModels) {

            while ($aModels = $rModels->GetNext()) {

                $mFound = $aModels['PROPERTY_MODEL_NEW_LINK_VALUE'];
				
				if (!empty($mFound)) {
				
					static::getModelName($mFound);

					$auModels = array(
						'NAME' => static::$aNames[$mFound],
						'VALUES' => array()
					);

					static::getModel($mFound, $auModels);
					static::checkDoubles($auModels);
					
				}
            }
            
        }

        fclose(static::$rFp);
        fclose(static::$rdFp);

        return $mFound ? ++$skip : 0;

    }

    private static function getModelName($imId){

        if(!isset(static::$aNames[$imId])){
            $rMod = impelCIBlockElement::GetByID($imId);

            if($rMod
                && $aMod = $rMod->GetNext()){

                if(isset($aMod['NAME'])
                    && !empty($aMod['NAME'])){

                    $sModel = $aMod['NAME'];

                    static::$aNames[$imId] = $sModel;


                }

            }

        }



    }

    private static function getModel($modelId,&$auModels)
    {
        $aMSelect = Array(
            "ID",
            "NAME",
            "CODE",
			"PROPERTY_SIMPLEREPLACE_PRODUCTS",
            "PROPERTY_PRODUCTS_REMOVED",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT"
        );

        //MANUFACTURER
        //TYPE_OF_PRODUCT

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_model_new_link" => $modelId,
            "ACTIVE" => "Y",
			'PROPERTY_manufacturer_VALUE' => array('Gorenje'),
		);

        $rModels = impelCIBlockElement::GetList(
            Array(
                'ID' => 'ASC'
            ),
            $aMFilter,
            false,
            false,
            $aMSelect);

        if($rModels){

            while($aModels = $rModels->GetNext()){
                $auModels['VALUES'][$modelId][$aModels['ID']] = $aModels['PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE'];
                static::$aCodes[$aModels['ID']] = trim($aModels['CODE']);
            }

        }

    }

    private static function checkDoubles($aCheck){

        $oiElt = new CIBlockElement;

        foreach($aCheck['VALUES'] as $sNameId => $aProps){
			
			
                $aDoubles = array(
                    'ORIGINAL' => array(),
                    'REDIRECT' => array()
                );


                foreach($aProps as $imId => $aProds) {
					
					if(!empty($aProds)){
                        $aDoubles['ORIGINAL'][$imId] = static::$aCodes[$imId];
                    } else {
						$aDoubles['REDIRECT'][$imId] = static::$aCodes[$imId];
                    }
				
                }
	
			
                if(!empty($aDoubles['ORIGINAL'])
                && !empty($aDoubles['REDIRECT'])
                ){
					
					foreach ($aDoubles['REDIRECT'] as $iDoubles => $sRedirect) {
					
						foreach($aProps as $imId => $aProds) {

							fputcsv(
								static::$rFp,
								array(
									$sNameId,
									$imId,
									'',
									(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$imId.'&lang=ru&find_section_section=0&WF=Y',
									(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/model/'.static::$aCodes[$imId].'/'
								), ';');

						}
						
						static::addRedirect(
							'/model/'.$sRedirect.'/',
							'/model/'.current($aDoubles['ORIGINAL']).'/'
						);

						$oiElt->Update($iDoubles,Array('ACTIVE' => 'N', 'TIMESTAMP_X' => true));
						
					}
					
				}
		
            

        }

    }

    private static function addRedirect($what,$where){

        $what = trim($what);
        $what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
        $what = rtrim($what,'/');

        $where = trim($where);
        $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
        $where = empty($where) ? "/" : $where;

        $show = false;
        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($what,'/').'/',
                "SHORT_URI" => trim($where,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        if (!$show
            &&
            (
                (trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
                || (trim(($where),'/') != trim(($what),'/'))
            )
        ){

            $arShortFields = Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/'),
                "STATUS" => "301",
            );

            CBXShortUri::Add($arShortFields);

        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/get_doublesempty_modules.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');


        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/impel_getdoublesempty_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelGetDoublesEmpty::getList();