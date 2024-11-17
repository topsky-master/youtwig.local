<?

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$file = isset($_REQUEST['file'])
&&!empty($_REQUEST['file'])
&&file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/'.urldecode(trim($_REQUEST['file'])))
    ? urldecode(trim($_REQUEST['file']))
    : 'common.csv';

$pfOpen = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$file,'r');

$countStrings = 200;
$currentCount = 0;
$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;

function getCommonSelectProperty($PROPERTY_CODE,$PROP_VALUE,$IBLOCK_ID = 16){

    if(isset($PROP_VALUE)
        && !empty($PROP_VALUE)){

        $params	= Array(
            "max_len" => "200",
            "change_case" => "L",
            "replace_space" => "-",
            "replace_other" => "",
            "delete_repeat_replace" => "true",
        );

        $properties = CIBlockProperty::GetList(
            Array(
                "sort"=>"asc",
                "name"=>"asc"
            ),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE" => $PROPERTY_CODE)
        );

        if($properties){

            while ($fields = $properties->GetNext()){

                $propID = false;
                $propertyID = $fields["ID"];

                $propertyDB = CIBlockPropertyEnum::GetList(
                    Array(
                        "DEF" => "DESC",
                        "SORT" => "ASC"),
                    Array(
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "VALUE" => trim($PROP_VALUE),
                        "CODE" => $PROPERTY_CODE
                    )
                );

                if($propertyDB){
                    while($propertyFields = $propertyDB->GetNext()){
                        if(isset($propertyFields["ID"])){
                            $propID = $propertyFields["ID"];
                        }
                    }
                }

                if(!$propID){

                    $XML_ID	= CUtil::translit($PROP_VALUE, LANGUAGE_ID, $params);

                    $propertyDB = CIBlockPropertyEnum::GetList(
                        Array(
                            "DEF" => "DESC",
                            "SORT" => "ASC"),
                        Array(
                            "IBLOCK_ID" => $IBLOCK_ID,
                            "XML_ID" => trim($XML_ID),
                            "CODE" => $PROPERTY_CODE
                        )
                    );

                    if($propertyDB){
                        while($propertyFields = $propertyDB->GetNext()){

                            if(isset($propertyFields["ID"])){
                                $propID = $propertyFields["ID"];
                            }

                        }
                    }

                }

                if(!$propID){

                    $propNew = new CIBlockPropertyEnum;

                    $XML_ID	= CUtil::translit($PROP_VALUE, LANGUAGE_ID, $params);

                    if($propID = $propNew->Add(
                        Array(
                            'PROPERTY_ID' => $propertyID,
                            'VALUE' => $PROP_VALUE,
                            'XML_ID' => $XML_ID)
                    )
                    ){

                    }

                }

            }

        }

    }

    return $propID;

}

function updateCommonProductProperty($PROPERTY_CODE,$ID,$NEW_VALUE,$IBLOCK_ID = 16){

    static $allProperties;

    if(!is_array($allProperties)){
        $allProperties = array();
    }

    if(!isset($allProperties[$IBLOCK_ID])){
        $allProperties[$IBLOCK_ID] = array();
    }

    if(!isset($allProperties[$IBLOCK_ID][$PROPERTY_CODE])){
        $allProperties[$IBLOCK_ID][$PROPERTY_CODE] = array();
    }

    $propValues = array();

    if(!in_array($ID,$allProperties[$IBLOCK_ID][$PROPERTY_CODE])){

        $allProperties[$IBLOCK_ID][$PROPERTY_CODE][] = $ID;

    } else {

        $dbProdPropResult = CIBlockElement::GetProperty(
            $IBLOCK_ID,
            $ID,
            Array(),
            Array(
                "CODE" => $PROPERTY_CODE
            )
        );

        if($dbProdPropResult){

            while($arProdProp = $dbProdPropResult->GetNext()){
                $propValues[] = (string)$arProdProp['VALUE'];
            };

        };

    }

    if(!in_array($NEW_VALUE,$propValues)){

        $propValues[] = $NEW_VALUE;
        CIBlockElement::SetPropertyValuesEx($ID, $IBLOCK_ID, array($PROPERTY_CODE => $propValues));
        if ($IBLOCK_ID == 11) {
			\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ID);
		}
	}

}

function updateLinkedProducts($PROPERTY_CODE,$ID,$NEW_VALUE){

    static $linkedID;

    if(!is_array($linkedID)){
        $linkedID = array();
    }

    if(!isset($linkedID[$ID])){

        $linkedID[$ID] = array();
        $linkedID = array();
        $lArSelect = Array("ID");
        $lArFilter = Array(
            "IBLOCK_ID" => 11,
            "PROPERTY_MAIN_PRODUCTS" => $ID
        );

        $lDBRes = CIBlockElement::GetList(Array(), $lArFilter, false, false, $lArSelect);

        if($lDBRes){

            while($arFields = $lDBRes->GetNext()){
                $linkedID[$ID][] = $arFields['ID'];
            }
        }

    }


    if(isset($linkedID[$ID])
        && !empty($linkedID[$ID])){

        foreach($linkedID[$ID] as $lID){

            echo $ID.'-'.$lID.'<br />';

            $MNEW_VALUE = getCommonSelectProperty($PROPERTY_CODE,$NEW_VALUE,11);
            updateCommonProductProperty($PROPERTY_CODE,$lID,$MNEW_VALUE,11);

        }

    }

}

if(CModule::IncludeModule("iblock")){

    while($current = fgetcsv($pfOpen, 0 , ";")){

        if($skip > $currentCount){

            ++$currentCount;
            continue;
        };

        $current = array_map('trim',$current);

        if(is_array($current)
            && !empty($current)
            && isset($current[0])
            && !empty($current[0])
        ){

            $ID = (int)$current[0];
            $dbMainProd = CIBlockElement::GetByID($ID);

            if($dbMainProd
                && $arMainProd = $dbMainProd->GetNext()){

                $updates = array();

                $mapColumns = array(
                    4 => 'COUNTRY',
                    5 => 'QUALITY',
                    6 => 'COLOR',
                    7 => 'DIAMETR',
                    8 => 'VNESHNIY_DIAMETR',
                    9 => 'VNUNTRENNIY_DIAMETR',
                    10 => 'VISOTA',
                    11 => 'DLINNA',
                    12 => 'TYPE_OF_PROFILE',
                    13 => 'SHIRINA',
                    14 => 'KOLICHESTVO_ZUBEV',
                    15 => 'POWER',
                    16 => 'TYPE_OF_MOUNT',
                    17 => 'PLACE_OF_CONTACTS',
                    18 => 'NUMBER_OF_CONTACTS',
                    19 => 'VOLUME',
                    20 => 'COVERING',
                    23 => 'TYPE_OF_FABRIC',
                    24 => 'FEATURES',
                    25 => 'COMPATIBILITY',
                );

                foreach($mapColumns as $number => $property_code){

                    if($property_code == 'TYPE_OF_FABRIC'
                        || $property_code == 'COMPATIBILITY'
                        && (mb_stripos($current[$number],';') !== false
                            || mb_stripos($current[$number],','))){

                        if(mb_stripos($current[$number],';') !== false){
                            $values = explode(';',$current[$number]);
                        } else {
                            $values = explode(',',$current[$number]);
                        }

                        foreach($values as $nextValue){

                            $updates[$property_code] = getCommonSelectProperty($property_code,$nextValue,16);
                            updateCommonProductProperty($property_code,$ID,$updates[$property_code],16);
                            updateLinkedProducts($property_code,$ID,$nextValue);
                        }

                    } else {

                        if(!empty($current[$number])){

                            $updates[$property_code] = getCommonSelectProperty($property_code,$current[$number],16);
                            updateCommonProductProperty($property_code,$ID,$updates[$property_code],16);
                            updateLinkedProducts($property_code,$ID,$current[$number]);

                        }

                    }

                }

            }


        }

        ++$currentCount;

        if(($countStrings + $skip) <= $currentCount){

            $skip += $countStrings;
            fclose($pfOpen);

            LocalRedirect('/local/crontab/product_props.php?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']));

        }



    }

}

fclose($pfOpen);