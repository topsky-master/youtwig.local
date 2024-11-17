<?php

//https://youtwig.ru/local/crontab/merge_doubles.php?intestwetrust=1

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

function errx() {
	//print_r(error_get_last());
}

register_shutdown_function('errx');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('DisableEventsCheck', true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
define('STOP_STATISTICS', true);
define('PERFMON_STOP', true);
define('WORKING_DIR',dirname(dirname(__DIR__)).'/bitrix/tmp/');

set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}
	
if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetDoublesEmpty{

    private static $maxCount = 2;
    private static $rdFp = false;
    private static $rFp = false;
	
	private static $aMainProduct = ['PROPERTY_TYPE_OF_PRODUCT_VALUE' => 'Духовой шкаф'];
	private static $manufacturer = "Beko";
	private static $products = array(695775);
    private static $aNames = array();
    private static $aCodes = array();
	
    public static function getList(){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkList();

        static::getRedirect($modelLastPropId);

    }
	
	public static function getDefaults () {
		
		static $aDefaults;
		
		if (empty($aDefaults)) {
		
			$skipProdId = 0;

			$arProdFilter = Array(
				"CODE" => "bez_tovara",
				"IBLOCK_ID" => 11
			);

			$arProdSelect = Array("ID");

			$resProdDB = impelCIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

			$resProdArr = Array();

			if($resProdDB) {
				$resProdArr = $resProdDB->GetNext();

				if(isset($resProdArr['ID'])
					&& !empty($resProdArr['ID'])){

					$skipProdId = $resProdArr['ID'];
				}
			}

			$skipViewId = 0;

			$arViewFilter = Array(
				"CODE" => "bez_vida",
				"IBLOCK_ID" => 34
			);

			$arViewSelect = Array("ID");

			$resViewDB = impelCIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

			$resViewArr = Array();

			if($resViewDB) {
				$resViewArr = $resViewDB->GetNext();

				if(isset($resViewArr['ID'])
					&& !empty($resViewArr['ID'])){

					$skipViewId = $resViewArr['ID'];
				}
			}

			$skipIndCodeId = 0;

			$arCodeFilter = Array(
				"CODE" => "bez_ind_koda",
				"IBLOCK_ID" => 35
			);

			$arCodeSelect = Array("ID");

			$resCodeDB = impelCIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

			$resCodeArr = Array();

			if($resCodeDB) {
				$resCodeArr = $resCodeDB->GetNext();

				if(isset($resCodeArr['ID'])
					&& !empty($resCodeArr['ID'])){

					$skipIndCodeId = $resCodeArr['ID'];

				}
			}
			
			$aDefaults = ['product_id' => $skipProdId,'view_id' => $skipViewId,'indcode_id' => $skipIndCodeId, 'posiiton' => '-'];
			
		}
		
		return $aDefaults;

	}

    private static function checkList() {

        if(!file_exists(WORKING_DIR.'impel_getdoublesu_last.txt')){
            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt',0);
        }

        $skip = trim(file_get_contents(WORKING_DIR.'impel_getdoublesu_last.txt'));
        $mFound = 0;

        if ($skip > 0) {

            static::$rFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'a+');
            static::$rdFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'a+');

        } else {

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', 0);
            static::$rFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'w+');
            static::$rdFp = fopen(WORKING_DIR.'impel_getdoublesu.csv', 'w+');

			$skip = 1;
		}

        $aMSelect = Array(
            "ID",
			"NAME",
			"PROPERTY_model_new_link",
			"PROPERTY_type_of_product"
		);

        $aMFilter = Array(
            "IBLOCK_ID" => 17,
		    "ACTIVE" => "Y",
			"PROPERTY_SIMPLEREPLACE_PRODUCTS" => static::$products,
			"PROPERTY_manufacturer_VALUE" => static::$manufacturer 
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
			"PROPERTY_SIMPLEREPLACE_POSITION",
			"PROPERTY_SIMPLEREPLACE_VIEW",
			"PROPERTY_SIMPLEREPLACE_INDCODE",
            "PROPERTY_PRODUCTS_REMOVED",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT"
        );

        //MANUFACTURER
        //TYPE_OF_PRODUCT
		//codes?
		
        $aMFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_model_new_link" => $modelId,
            "ACTIVE" => "Y",
			"PROPERTY_manufacturer_VALUE" => static::$manufacturer
		);

		$iModels = impelCIBlockElement::GetList(
            Array(
                'ID' => 'ASC'
            ),
            $aMFilter,
            [],
            false,
            $aMSelect);
		
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
                $auModels['VALUES'][$modelId][$aModels['ID']] = $aModels;
                static::$aCodes[$aModels['ID']] = trim($aModels['CODE']);
			}
		
        }

    }
	
	
	private static function mergeModeles($aModels) {
		
		$indCodes = [];
		
		foreach ($aModels as $sProp => $aValues) {
			
			if ($sProp == 'PROPERTY_SIMPLEREPLACE_INDCODE_VALUE' && !empty($aValues)) {
				
				foreach ($aValues as $sKey => $sValue) {
				
					$sHash = $aModels["PROPERTY_SIMPLEREPLACE_PRODUCTS_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_POSITION_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_VIEW_VALUE"][$sKey].';'.$aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey];
					$indCodes['hash'][$sHash] = $sHash;
					$indCodes['id'][$aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey]] = $aModels["PROPERTY_SIMPLEREPLACE_INDCODE_VALUE"][$sKey];					
				
				}
			
			}
			
		}
		
		$indCodes['id'] = array_filter($indCodes['id'],function($strval){
			$adefaults = static::getDefaults();
			return $strval == $adefaults['indcode_id'] ? false : true;
		});
		
		return $indCodes;
		
	}

    private static function checkDoubles($aCheck) {

        $oiElt = new CIBlockElement;

		foreach($aCheck['VALUES'] as $sNameId => $aProps){
				
				$aDoubles = array(
                    'ORIGINAL' => array(),
                    'REDIRECT' => array()
                );

				foreach($aProps as $imId => $aProds) {
					
					$indCodes = static::mergeModeles($aProds);
					
					if (empty($indCodes)) {
						continue;
					}
					
					if (empty($aDoubles['ORIGINAL'])) {
					  
						$bIsMain = false;
						
						foreach (static::$aMainProduct as $smKey => $smValue) {
							
							if (static::$aMainProduct[$smKey] == $aProds[$smKey]) {
								$bIsMain = true;		
							}
						}
							
						if ($bIsMain) {
							
							$aDoubles['ORIGINAL']['ID'] = $imId;
							$aDoubles['ORIGINAL']['CODE'] = static::$aCodes[$imId];
							
							$aDoubles['ORIGINAL']['INDCODE'] = $indCodes;
							
							fputcsv(
								static::$rFp,
								array(
									$sNameId,
									$imId,
									$aProds['NAME'],
									((CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$imId.'&lang=ru&find_section_section=0&WF=Y'),
									''
								), ';');

						
						}

					} else {
						
						if (array_intersect($aDoubles['ORIGINAL']['INDCODE']['id'],$indCodes['id'])) {
							$aDoubles['REDIRECT']['CODE'][$imId] = static::$aCodes[$imId];
							$aDoubles['REDIRECT']['INDCODE'][$imId] = $indCodes['hash'];
							$aDoubles['ORIGINAL']['INDCODE']['hash'] = array_merge($indCodes['hash'],$aDoubles['ORIGINAL']['INDCODE']['hash']);
						}
							
					}
				
                }
	
				if(!empty($aDoubles['ORIGINAL'])
                && !empty($aDoubles['REDIRECT'])
                ){
					
					foreach ($aDoubles['REDIRECT']['CODE'] as $iDoubles => $sRedirect) {
					
						static::setMergeProps($aDoubles['ORIGINAL']['ID'],$aDoubles['ORIGINAL']['INDCODE']['hash']);
						
						fputcsv(static::$rFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$aDoubles['ORIGINAL']['ID'].'&find_section_section=0&WF=Y','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID='.$iDoubles.'&find_section_section=0&WF=Y'],';');
						fputcsv(static::$rdFp,[$aDoubles['ORIGINAL']['ID'], $iDoubles, 'https://youtwig.ru/model/'.$sRedirect.'/','https://youtwig.ru/model/'.$aDoubles['ORIGINAL']['CODE'].'/'],';');
						
						static::addRedirect(
							'/model/'.$sRedirect.'/',
							'/model/'.$aDoubles['ORIGINAL']['CODE'].'/'
						);

						$oiElt->Update($iDoubles,Array('ACTIVE' => 'N', 'TIMESTAMP_X' => true));
					
					}
					
				}
		
            

        }

    }

	private static function setMergeProps($id,$props){
		
		$aProps = [];

		foreach ($props as $str) {
			$arr = explode(';',$str);
			$aProps['SIMPLEREPLACE_PRODUCTS'][] = $arr[0];
			$aProps['SIMPLEREPLACE_POSITION'][] = $arr[1];
			$aProps['SIMPLEREPLACE_VIEW'][] = $arr[2];
			$aProps['SIMPLEREPLACE_INDCODE'][] = $arr[3];
		}
		
		impelCIBlockElement::SetPropertyValuesEx($id, 17, $aProps);
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $id);
		
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

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', $skip);
            die('<html><head><meta HTTP-EQUIV="refresh" content="'.mt_rand(0,3).';url='.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/merge_doubles.php?intestwetrust=1&time='.time().'&PageSpeed=off" /></head><body><h1>'.time().'</h1></body></html>');

        } else {

            file_put_contents(WORKING_DIR.'impel_getdoublesu_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")) {
    impelGetDoublesEmpty::getList();
}