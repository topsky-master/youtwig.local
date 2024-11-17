#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/set_sections.php?intestwetrust=1

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

if (isset($argc)
    && isset($argv)
    && $argc > 0
    && $argv[0]) {

    $_REQUEST['intestwetrust'] = 1;

}

if(!isset($_REQUEST['intestwetrust'])) 
	die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$aMainFilter = Array(
	'IBLOCK_ID' => 11
);

$aMainSelect = Array('ID');

$rmDb = impelCIBlockElement::GetList(Array(), $aMainFilter, false, false, $aMainSelect);

$aProducts = array();
$aSections = array();

if($rmDb) {
    
	while($amDb = $rmDb->GetNext()) {

		$product_buy_id = getBondsProduct($amDb['ID']);
		
		if(isset($amDb['ID'])
			&& !empty($amDb['ID'])){
		
			if($product_buy_id 
				&& $product_buy_id != $amDb['ID']){
			
				$imId = $amDb['ID'];
				$rsDb = CIBlockElement::GetElementGroups($imId);
				$aSects = array();
				
				if($rsDb) {
					while($asDb = $rsDb->GetNext()) {
						$aSects[$asDb['NAME']] = trim($asDb['NAME']);			
					}
				}
				
				if(!empty($aSects)){
				
					if(!isset($aProducts[$product_buy_id])) {
						$aProducts[$product_buy_id] = array();
					}
					
					$aProducts[$product_buy_id] = array_merge($aProducts[$product_buy_id],$aSects);
					$aSections = array_merge($aSections,$aSects);		
				}
			
			}	
		
		}
		
	}

    
}

natsort($aSections);
$aSectToProp = array();

foreach ($aSections as $sSection)
{
	
	$typePropID = false;
	
	$sSection = trim($sSection);
    $PROPERTY_CODE = 'SECTION';

    $typeProperties = CIBlockProperty::GetList(Array(
        "sort" => "asc",
        "name" => "asc"
    ) , Array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => 16,
        "CODE" => $PROPERTY_CODE
    ));

    if ($typeProperties)
    {

        while ($typeFields = $typeProperties->GetNext())
        {

            $typePropertyID = $typeFields["ID"];
            $enumTypeNew = new CIBlockPropertyEnum;

            $typePropertyDB = CIBlockPropertyEnum::GetList(Array(
                "DEF" => "DESC",
                "SORT" => "ASC"
            ) , Array(
                "IBLOCK_ID" => 16,
                "VALUE" => $sSection,
                "CODE" => $PROPERTY_CODE
            ));

            if ($typePropertyDB)
            {
                while ($typePropertyFields = $typePropertyDB->GetNext())
                {

                    if (isset($typePropertyFields["ID"]))
                    {
                        $typePropID = $typePropertyFields["ID"];
                    }

                }
            }

            if (!$typePropID)
            {
                if ($typePropID = $enumTypeNew->Add(Array(
                    'PROPERTY_ID' => $typePropertyID,
                    'VALUE' => $sSection
                )))
                {

                } else {

                    if(isset($enumTypeNew->LAST_ERROR)){
						echo $enumTypeNew->LAST_ERROR;
					}

                }
                
            }
			
			if($typePropID)
				$aSectToProp[$sSection] = $typePropID;

        }

    }

}

$elt = new CIBlockElement;

foreach($aProducts as $iProductId => $aSections){
	
	$aValues = array();
	
	foreach($aSections as $sSection) {
		if(isset($aSectToProp[$sSection]) && !empty($aSectToProp[$sSection])){
			$iPropId = $aSectToProp[$sSection];
			$aValues[] = $iPropId;	
		};
	}
	
	if(!empty($aValues)){
		
		CIBlockElement::SetPropertyValuesEx($iProductId, 16, Array('SECTION' => $aValues));
		$elt->Update($iProductId, Array('TIMESTAMP_X' => true));
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $iProductId);
	}
	
}