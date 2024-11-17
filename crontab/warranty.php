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

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

class warrantySync{

    private static $mainWarrantyArr = array();
    private static $bondWarrantyArr = array();
    const MAIN_BLOCK_ID = 11;
    const BOND_BLOCK_ID = 16;
    private static $code = 'WARRANTY';

    private static $propertyId = '';

    public function __contstruct(){

    }

    public static function process(){

        $arPSelect = Array(
            "ID",
            "PROPERTY_WARRANTY"
        );

        $arPFilter = Array(
            "IBLOCK_ID" => self::MAIN_BLOCK_ID,
            "!PROPERTY_MAIN_PRODUCTS" => false
        );

        self::getPropertyId(self::MAIN_BLOCK_ID,self::$code);

        if(self::$propertyId){

            self::syncWarrantyIbProp();
            self::resetMainWarranty();

            $dbPres = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

            if($dbPres){
                while($arPRes = $dbPres->GetNext()){

                    $product_id = $arPRes['ID'];
                    $bonds_id = getBondsProduct($product_id);
					
                    if($bonds_id != $product_id){

						$propValues = self::getElPropertyValue(self::BOND_BLOCK_ID,$bonds_id,self::$code,self::$mainWarrantyArr);
						
						
						{
								
							if (checkEltPropertyChange($propValues,self::$code,$product_id,self::MAIN_BLOCK_ID)) {
								
								$fieldsUpdate = array(self::$code => $propValues);

								setEltPropertyValuesEx(
												$product_id,
												self::MAIN_BLOCK_ID,
												$fieldsUpdate);
										
							}
							
							
							

                        };
						
                    }

                }

            }

        }

    }

    public static function resetMainWarranty(){

        $arPSelect = Array(
            "ID",
        );

        $arPFilter = Array(
            "IBLOCK_ID" => self::MAIN_BLOCK_ID,
			"PROPERTY_MAIN_PRODUCTS" => false
		);

        $dbPres = CIBlockElement::GetList(Array(), $arPFilter, false, false, $arPSelect);

        if($dbPres){

            while($arPRes = $dbPres->GetNext()){

				$propValues = false;
				$product_id = $arPRes['ID'];
				
				if (checkEltPropertyChange($propValues,self::$code,$product_id,self::MAIN_BLOCK_ID)) {
								
					$fieldsUpdate = array(self::$code => $propValues);

					setEltPropertyValuesEx(
						$product_id,
						self::MAIN_BLOCK_ID,
						$fieldsUpdate);
										
				}
				
            }

        }

    }

    public static function getElPropertyValue($iblock_id,$element_id,$code,$mapArray){

        $props = array();

        $dbProps = CIBlockElement::GetProperty(
            $iblock_id,
            $element_id,
            array("sort" => "asc"),
            array("CODE" => $code)
        );

        if($dbProps){

            while($arProps = $dbProps->GetNext()){

                if(isset($arProps['VALUE_ENUM'])
                    && !empty($arProps['VALUE_ENUM'])
                    && isset($mapArray[mb_strtolower(trim($arProps['VALUE_ENUM']))])){
                    $props[] = $mapArray[mb_strtolower(trim($arProps['VALUE_ENUM']))];
                }
            }
        }


        return $props;

    }


    private static function syncWarrantyIbProp(){

        $mainWarranty = self::getPropertyValues(self::MAIN_BLOCK_ID,self::$code,self::$mainWarrantyArr);
        $bondWarranty = self::getPropertyValues(self::BOND_BLOCK_ID,self::$code,self::$bondWarrantyArr);
        $diffWarranty = array_diff($bondWarranty,$mainWarranty);

        if(!empty($diffWarranty)
            && is_array($diffWarranty)){

            foreach($diffWarranty as $mainWarrantyXmlId => $mainWarrantyValue){
                if(!isset($mainWarranty[$mainWarrantyXmlId]))
                    self::addEnumOption($mainWarrantyValue,$mainWarrantyXmlId,self::$mainWarrantyArr);
            }

        }

    }

    public static function getPropertyId($iblock_id,$property_code){

        $properties = CIBlockProperty::GetList(
            Array(
                "sort"=>"asc",
                "name"=>"asc"
            ),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $iblock_id,
                "CODE" => $property_code)
        );

        if($properties){

            while ($typeFields = $properties->GetNext()){

                self::$propertyId = $typeFields["ID"];

            }

        }

    }

    private static function getPropertyValues($iblock_id,$property_code,&$fillPropertyArr){

        $propertyValues = array();

        $propertyDB = CIBlockPropertyEnum::GetList(
            Array(
                "DEF"=>"DESC",
                "SORT"=>"ASC"
            ),
            Array(
                "IBLOCK_ID" => $iblock_id,
                "CODE" => $property_code
            )
        );

        if($propertyDB){
            while($propertyFields = $propertyDB->GetNext()){

                if(isset($propertyFields["XML_ID"])
                    && isset($propertyFields["VALUE"])
                ){
                    $propertyValues[mb_strtolower(trim($propertyFields["XML_ID"]))] = mb_strtolower(trim($propertyFields["VALUE"]));
                    $fillPropertyArr[mb_strtolower(trim($propertyFields["VALUE"]))] = $propertyFields["ID"];

                }

            }

        }

        return $propertyValues;


    }

    public static function addEnumOption($value,$xml_id,&$fillPropertyArr){

        $enumTypeNew = new CIBlockPropertyEnum;

        if($propID = $enumTypeNew->Add(
            Array(
                'PROPERTY_ID' => self::$propertyId,
                'VALUE' => mb_strtolower(trim($value)),
                'XML_ID' => mb_strtolower(trim($xml_id))
            )
        )
        ){

            $fillPropertyArr[mb_strtolower(trim($value))] = $propID;

        }


    }

}

if(CModule::IncludeModule("iblock"))
    warrantySync::process();

?>