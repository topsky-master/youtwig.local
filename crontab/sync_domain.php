#!/usr/bin/php -q
<?php

//https://youtwig.ru/local/crontab/sync_domain.php?intestwetrust=1

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

require($_SERVER["DOCUMENT_ROOT"]."bitrix/modules/main/include/prolog_before.php");

class propertySync{

    private static $mainPropArr = array();
    private static $bondPropArr = array();
    private static $MAIN_BLOCK_ID = 11;
    private static $BOND_BLOCK_ID = 18;
    private static $code = '';

    private static $propertyId = '';

    public static function process(array $properties, int $MAIN_BLOCK_ID, int $BOND_BLOCK_ID){

        static::$MAIN_BLOCK_ID = $MAIN_BLOCK_ID;
        static::$BOND_BLOCK_ID = $BOND_BLOCK_ID;

        foreach ($properties as $property) {

            static::$code = $property;

            self::getPropertyId(self::$MAIN_BLOCK_ID,self::$code);

            if(self::$propertyId){

                self::syncProp();

            }

        }

    }

    private static function syncProp(){

        $mainProp = self::getPropertyValues(self::$MAIN_BLOCK_ID,self::$code,self::$mainPropArr);
        $bondProp = self::getPropertyValues(self::$BOND_BLOCK_ID,self::$code,self::$bondPropArr);
        $diffProp = array_diff($bondProp,$mainProp);

        if(!empty($diffProp)
            && is_array($diffProp)){

            foreach($diffProp as $mainPropXmlId => $mainPropValue){
                if(!isset($mainProp[$mainPropXmlId]))
                    self::addEnumOption($mainPropValue,$mainPropXmlId,self::$mainPropArr);
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

if(CModule::IncludeModule("iblock")) {
    propertySync::process(['DOMAIN'],18,45);
    propertySync::process(['DOMAIN'],13,45);
}
