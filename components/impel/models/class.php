<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelModelComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    protected function getTypeOfProductData()
    {
        global $arParams;

        $type_of_product = array();

        $cacheTime = $arParams['CACHE_TIME'] ? (int)$arParams['CACHE_TIME'] : 360000000;
        $cacheTime = $cacheTime > 0 ? $cacheTime : 360000000;

        $obCache = new CPHPCache;
        $cacheID = 'model_type_of_product';

        if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

            $tmp = array();
            $tmp = $obCache->GetVars();

            if(isset($tmp[$cacheID])){
                $type_of_product = $tmp[$cacheID];
            }

        } else {

            if($obCache->StartDataCache()){

                if(CModule::IncludeModule("iblock")){

                    $typeProperties = CIBlockPropertyEnum::GetList(
                        Array(
                            "TYPE_OF_PRODUCT_VALUE" => "ASC"
                        ),
                        Array(
                            "IBLOCK_ID" => 17,
                            "CODE" => 'TYPE_OF_PRODUCT'
                        )
                    );


                    if($typeProperties){

                        while ($typeFields = $typeProperties->GetNext()){

                            $type_of_product['_'.$typeFields['ID']] = $typeFields['VALUE'];

                        }

                    }

                }


                $obCache->EndDataCache(
                    array(
                        $cacheID => $type_of_product
                    )
                );

            }


        }

        return $type_of_product;

    }

    public function chooseJsonType(){
        global $APPLICATION, $arParams;

        if(isset($_REQUEST['bxajaxid'])
            && $_REQUEST['bxajaxid'] == 'choose_type'
            && isset($_REQUEST['type_of_product'])
            && !empty($_REQUEST['type_of_product'])
            && CModule::IncludeModule("iblock")
        ){

            $APPLICATION->RestartBuffer();

            $type_of_product_json = array();
            $type_of_product = (int)($_REQUEST['type_of_product']);

            if(!empty($type_of_product)){

                $cacheTime = $arParams['CACHE_TIME'] ? (int)$arParams['CACHE_TIME'] : 360000000;
                $cacheTime = $cacheTime > 0 ? $cacheTime : 360000000;

                $obCache = new CPHPCache;
                $cacheID = 'model_type_of_product_'.$type_of_product;

                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $type_of_product_json = $tmp[$cacheID];
                    }


                } else {

                    if($obCache->StartDataCache()){

                        $arTypeSelect = Array("ID", "PROPERTY_MANUFACTURER");
                        $arTypeFilter = Array(
                            "IBLOCK_ID" => 17,
                            "ACTIVE_DATE" => "Y",
                            "ACTIVE" => "Y",
                            "!PROPERTY_VERSION" => 56776,
                            "PROPERTY_TYPE_OF_PRODUCT" => $type_of_product);

                        $resTypeDB = CIBlockElement::GetList(Array("PROPERTY_MANUFACTURER_VALUE" => "ASC"), $arTypeFilter, array('PROPERTY_MANUFACTURER'), false, $arTypeSelect);

                        if($resTypeDB){

                            while($resTypeArr = $resTypeDB->GetNext()){

                                if(isset($resTypeArr["PROPERTY_MANUFACTURER_VALUE"])
                                    && !empty($resTypeArr["PROPERTY_MANUFACTURER_VALUE"])
                                    && isset($resTypeArr["PROPERTY_MANUFACTURER_ENUM_ID"])
                                    && !empty($resTypeArr["PROPERTY_MANUFACTURER_ENUM_ID"])
                                ){
                                    $type_of_product_json['_'.$resTypeArr["PROPERTY_MANUFACTURER_ENUM_ID"]] = $resTypeArr["PROPERTY_MANUFACTURER_VALUE"];
                                }
                            }
                        }


                        $obCache->EndDataCache(
                            array(
                                $cacheID => $type_of_product_json
                            )
                        );

                    }


                }

            }

            echo json_encode($type_of_product_json,JSON_UNESCAPED_UNICODE);
            die();

        }
    }

    public function chooseJsonModelNew(){
        global $APPLICATION, $arParams;

        if(isset($_REQUEST['bxajaxid'])
            && $_REQUEST['bxajaxid'] == 'choose_model_new'
            && isset($_REQUEST['type_of_product'])
            && !empty($_REQUEST['type_of_product'])
            && isset($_REQUEST['manufacturer'])
            && !empty($_REQUEST['manufacturer'])
            && isset($_REQUEST['model_new'])
            && !empty($_REQUEST['model_new'])
            && CModule::IncludeModule("iblock")
        ){


            $type_of_product = (int)($_REQUEST['type_of_product']);
            $manufacturer = (int)($_REQUEST['manufacturer']);
            $model_new = trim($_REQUEST['model_new']);

            $APPLICATION->RestartBuffer();

            $model_new_json = array();

            if(!empty($manufacturer)
                && !empty($type_of_product)
                && !empty($model_new)){

                $arModelSelect = Array("ID", "PROPERTY_MODEL_NEW_LINK", "DETAIL_PAGE_URL");
                $arModelFilter = Array(
                    "IBLOCK_ID" => 17,
                    "ACTIVE_DATE" => "Y",
                    "!PROPERTY_VERSION" => 56776,
                    "ACTIVE" => "Y",
                    "PROPERTY_MANUFACTURER" => $manufacturer,
                    "PROPERTY_TYPE_OF_PRODUCT" => $type_of_product,
                );

                $results_count = isset($arParams["RESULTS_COUNT"])
                && !empty($arParams["RESULTS_COUNT"])
                    ? (int) $arParams["RESULTS_COUNT"]
                    : 30;

                $results_count = $results_count > 0
                    ? $results_count
                    : 30;

                $resModelDB = CIBlockElement::GetList(Array("PROPERTY_MODEL_NEW_VALUE" => "ASC"), $arModelFilter, false, false, $arModelSelect);
                $detail_url = "";

                if(!(isset($arParams["DETAIL_URL"]) && !empty($arParams["DETAIL_URL"]))){
                    $iblockResDB = CIBlock::GetList(
                        Array(),
                        Array(
                            'ID' => 17
                        )
                    );

                    if($iblockResDB && $arIblockArr = $iblockResDB->GetNext()){

                        if(isset($arIblockArr['DETAIL_PAGE_URL'])
                            && !empty($arIblockArr['DETAIL_PAGE_URL'])){

                            $detail_url = $arIblockArr['DETAIL_PAGE_URL'];

                        }

                    }
                } else {

                    $detail_url = $arParams["DETAIL_URL"];

                }



                $resModelDB->SetUrlTemplates($detail_url, "", "");

                if($resModelDB){

                    $resultArray = array();
                    $resultLinks = array();

                    while($resModelArr = $resModelDB->GetNext()){

                        $resultArray[$resModelArr["ID"]] = $resModelArr["PROPERTY_MODEL_NEW_LINK_VALUE"];
                        $resultLinks[$resModelArr["ID"]] = $resModelArr["DETAIL_PAGE_URL"];
                    }
                    $arModelLinkSelect = Array("ID","NAME");
                    $resultArray = array_unique($resultArray);

                    if(!empty($resultArray)){

                        $arModelLinkFilter = Array(
                            "=ID" => $resultArray,
                            "%NAME" => $model_new,
                            "ACTIVE" => "Y",
                            "IBLOCK_ID" => 27,
                        );

                        $resModelLinkDB = CIBlockElement::GetList(Array("PROPERTY_MODEL_NEW_VALUE" => "ASC"), $arModelLinkFilter, false, array("nTopCount" => $results_count), $arModelLinkSelect);

                        $resultLinkArray = Array();

                        if($resModelLinkDB){
                            while($resModelLinkArr = $resModelLinkDB->GetNext()){

                                $search_key = array_search($resModelLinkArr['ID'],$resultArray);

                                if($search_key !== false
                                    && isset($resModelLinkArr["NAME"])
                                    && !empty($resModelLinkArr["NAME"])
                                    && isset($resultLinks[$search_key])
                                    && !empty($resultLinks[$search_key])
                                ){
                                    $model_new_json[] = array(
                                        "name" => $resModelLinkArr["NAME"],
                                        "value" => $resultLinks[$search_key]
                                    );
                                }

                            }

                        }

                    }

                }


            }

            echo json_encode($model_new_json,JSON_UNESCAPED_UNICODE);
            die();

        }
    }


    public function checkJsonRequest(){

        $this->chooseJsonType();
        $this->chooseJsonModelNew();

    }

    public function executeComponent()
    {
        $this->arResult = array();

        $this->checkJsonRequest();

        $this->arResult["TYPE_OF_PRODUCT"] = $this->getTypeOfProductData();

        $this->includeComponentTemplate();
    }
}