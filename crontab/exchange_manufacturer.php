<?php

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

function saveBackupManufacturer(){

    $IBLOCK_ID = 11;

    $copyCode = 'MANUFACTURER_DETAIL';
    $copyPropertyID = 0;

    $properties = CIBlockProperty::GetList(
        Array(
            "sort"=>"asc",
            "name"=>"asc"
        ),
        Array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => $copyCode
        )
    );

    if($properties) {

        while ($fields = $properties->GetNext()) {

            $copyPropertyID = $fields["ID"];
        }

    }

    $propertyDB = CIBlockPropertyEnum::GetList(
        Array(
            "DEF" => "DESC",
            "SORT" => "ASC"),
        Array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => "MANUFACTURER_DETAIL"
        )
    );

    if(!($propertyDB && $propertyDB->Fetch())){

        $propertyDB = CIBlockPropertyEnum::GetList(
            Array(
                "DEF" => "DESC",
                "SORT" => "ASC"),
            Array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE" => "MANUFACTURER"
            )
        );

        if($copyPropertyID && $propertyDB){

            while($propertyFields = $propertyDB->GetNext()){

                if(isset($propertyFields["ID"])){

                    $propID = 0;

                    $propertyCDB = CIBlockPropertyEnum::GetList(
                        Array(
                            "DEF" => "DESC",
                            "SORT" => "ASC"),
                        Array(
                            "IBLOCK_ID" => $IBLOCK_ID,
                            "VALUE" => trim($propertyFields["VALUE"]),
                            "PROPERTY_ID" => $copyPropertyID
                        )
                    );

                    if($propertyCDB){
                        while($propertyCFields = $propertyCDB->GetNext()){
                            if(isset($propertyCFields["ID"])){
                                $propID = $propertyCFields["ID"];
                            }
                        }
                    }

                    if(!$propID){

                        $propNew = new CIBlockPropertyEnum;

                        $propID = $propNew->Add(
                            Array(
                                'PROPERTY_ID' => $copyPropertyID,
                                'VALUE' => $propertyFields["VALUE"],
                                'DEF' => $propertyFields["DEF"],
                                'SORT' => $propertyFields["SORT"],
                                'XML_ID' =>  $propertyFields["XML_ID"]
                            )
                        );

                    }

                    if($propID){

                        $cArSelect = Array("ID","PROPERTY_MANUFACTURER");
                        $cArFilter = Array(
                            "IBLOCK_ID" => $IBLOCK_ID,
                            "PROPERTY_MANUFACTURER" => $propertyFields["ID"]
                        );

                        $clDBRes = CIBlockElement::GetList(Array(), $cArFilter, false, false, $cArSelect);

                        if($clDBRes){

                            while($carFields = $clDBRes->GetNext()){

                                $propValues = array();

                                $propValues[] = $propID;

                                if(!empty($propValues)){

                                    CIBlockElement::SetPropertyValuesEx($carFields['ID'], $IBLOCK_ID, array('MANUFACTURER_DETAIL' => $propValues));
                                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $carFields['ID']);

                                }

                            }

                        }

                    }

                }

            }

        }

    }

}

saveBackupManufacturer();

function remapCompabilityToManufacturer(){

    $IBLOCK_ID = 11;

    $cValueEnum = array();

    $cPropertyDB = CIBlockPropertyEnum::GetList(
        Array(
            "DEF" => "DESC",
            "SORT" => "ASC"),
        Array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => "COMPATIBILITY"
        )
    );

    if($cPropertyDB){
        while($cPropertyFields = $cPropertyDB->GetNext()){

            $mPropID = 0;

            $mPropertyDB = CIBlockPropertyEnum::GetList(
                Array(
                    "DEF" => "DESC",
                    "SORT" => "ASC"),
                Array(
                    "IBLOCK_ID" => $IBLOCK_ID,
                    "VALUE" => trim($cPropertyFields['VALUE']),
                    "CODE" => 'MANUFACTURER'
                )
            );

            if($mPropertyDB){
                while($mPropertyFields = $mPropertyDB->GetNext()){
                    if(isset($mPropertyFields["ID"])){
                        $mPropID = $mPropertyFields["ID"];
                    }
                }
            }

            if(!$mPropID){

                $mPropertyDB = CIBlockPropertyEnum::GetList(
                    Array(
                        "DEF" => "DESC",
                        "SORT" => "ASC"),
                    Array(
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "XML_ID" => $cPropertyFields['XML_ID'],
                        "CODE" => 'MANUFACTURER'
                    )
                );

                if($mPropertyDB){
                    while($mPropertyFields = $mPropertyDB->GetNext()){

                        if(isset($mPropertyFields["ID"])){
                            $mPropID = $mPropertyFields["ID"];
                        }

                    }
                }

            }

            if(!$mPropID){

                $mPropNew = new CIBlockPropertyEnum;

                if($mPropID = $mPropNew->Add(
                    Array(
                        'PROPERTY_ID' => 44,
                        'VALUE' => $cPropertyFields["VALUE"],
                        'DEF' => $cPropertyFields["DEF"],
                        'SORT' => $cPropertyFields["SORT"],
                        'XML_ID' =>  $cPropertyFields["XML_ID"]
                    )
                )
                ){

                }

            }

            if($mPropID){
                $cValueEnum[$cPropertyFields["VALUE"]] = $mPropID;
            }

        }

    }

    $IBLOCK_ID = 11;

    $cPropertyDB = CIBlockPropertyEnum::GetList(
        Array(
            "DEF" => "DESC",
            "SORT" => "ASC"),
        Array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "CODE" => "COMPATIBILITY"
        )
    );

    if($cPropertyDB){
        while($cPropertyFields = $cPropertyDB->GetNext()){

            $cArSelect = Array("ID");
            $cArFilter = Array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "PROPERTY_COMPATIBILITY" => $cPropertyFields['ID']
            );

            $cDBRes = CIBlockElement::GetList(Array(), $cArFilter, false, false, $cArSelect);

            if($cDBRes){

                while($cFields = $cDBRes->GetNext()){
                    //$cFields['ID'];

                    $propValues = array();

                    $dbProdPropResult = CIBlockElement::GetProperty(
                        $IBLOCK_ID,
                        $cFields['ID'],
                        Array(),
                        Array(
                            "CODE" => "COMPATIBILITY"
                        )
                    );

                    if($dbProdPropResult){

                        while($arProdProp = $dbProdPropResult->GetNext()){
                            $value_enum = (string)$arProdProp['VALUE_ENUM'];
                            $propValues[] = $cValueEnum[$value_enum];
                        };

                    };

                    $dbProdPropResult = CIBlockElement::GetProperty(
                        $IBLOCK_ID,
                        $cFields['ID'],
                        Array(),
                        Array(
                            "CODE" => "MANUFACTURER"
                        )
                    );

                    $propMValues = array();

                    if($dbProdPropResult){

                        while($arProdProp = $dbProdPropResult->GetNext()){
                            $propMValues[] = (string)$arProdProp['VALUE'];
                        };

                    };

                    if(sizeof(array_intersect($propValues, $propMValues)) != sizeof($propValues)){

                        CIBlockElement::SetPropertyValuesEx($cFields['ID'], $IBLOCK_ID, array('MANUFACTURER' => $propValues));
                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $cFields['ID']);
                    }
                }
            }
        }
    }
}

remapCompabilityToManufacturer();
