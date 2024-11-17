<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$skipProdId = 0;

$arProdFilter = Array(
    "CODE" => "bez_tovara",
    "IBLOCK_ID" => 11
);

$arProdSelect = Array("ID");

$resProdDB = CIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

$resProdArr = Array();

if($resProdDB) {
    $resProdArr = $resProdDB->GetNext();

    if(isset($resProdArr['ID'])
        && !empty($resProdArr['ID'])){

        $skipProdId = $resProdArr['ID'];
    }
}

$arResult['skipProdId'] = $skipProdId;

$skipViewId = 0;

$arViewFilter = Array(
    "CODE" => "bez_vida",
    "IBLOCK_ID" => 34
);

$arViewSelect = Array("ID");

$resViewDB = CIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

$resViewArr = Array();

if($resViewDB) {
    $resViewArr = $resViewDB->GetNext();

    if(isset($resViewArr['ID'])
        && !empty($resViewArr['ID'])){

        $skipViewId = $resViewArr['ID'];
    }
}

$arResult['skipViewId'] = $skipViewId;

$skipIndCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_ind_koda",
    "IBLOCK_ID" => 35
);

$arCodeSelect = Array("ID");

$resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipIndCodeId = $resCodeArr['ID'];

    }
}

$arResult['skipIndCodeId'] = $skipIndCodeId;

$skipComCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_com_koda",
    "IBLOCK_ID" => 36
);

$arCodeSelect = Array("ID");

$resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipComCodeId = $resCodeArr['ID'];

    }
}

$arResult['skipComCodeId'] = $skipComCodeId;

if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["INDCODE"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"];

    $arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"];
}

if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["VIEW"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"];

    $arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"];
}

if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["COMCODE"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["COMCODE"]["DISPLAY_VALUE"];


    $arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"];
}



if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["POSITION"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["POSITION"]["DISPLAY_VALUE"];


    $arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"];
}

/* if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["RESTORE_PRODUCTS"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["RESTORE_PRODUCTS"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["products"] = $arResult["DISPLAY_PROPERTIES"]["RESTORE_PRODUCTS"];
    $arResult["PROPERTIES"]["products"] = $arResult["PROPERTIES"]["RESTORE_PRODUCTS"];

} */

if(
    isset($arResult["DISPLAY_PROPERTIES"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["products"]) &&
    isset($arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])){

    $arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"];

    $arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"] =
        !empty($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
        && is_string($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
            :  $arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"];
}

$products = $arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"];
$indcodes = $arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"];
$views = $arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"];
$comcodes = $arResult["DISPLAY_PROPERTIES"]["COMCODE"]["VALUE"];
$positions = $arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"];

$forCompare = array();

foreach($views as $number => $view){

    $forCompare[$view][] = $indcodes[$number].'-'.$comcodes[$number].'-'.$positions[$number].'-'.$products[$number];

}

if(!empty($forCompare)
    && (sizeof($forCompare) > 1)
    &&(
        !empty($indcodes)
        ||  !empty($comcodes)
        ||  !empty($positions))
){

    $prev = $diff = array();

    foreach($forCompare as $view){

        if(!empty($view)){
            $diff = array_diff($prev,$view);

            if(!empty($diff)){
                break;
            }

        }

        $prev = $view;
    }

    if(empty($diff)){

        //$arResult["DISPLAY_PROPERTIES"]["products"] = $arResult["DISPLAY_PROPERTIES"]["RESTORE_PRODUCTS"];
        //$arResult["PROPERTIES"]["products"] = $arResult["PROPERTIES"]["RESTORE_PRODUCTS"];

        unset(
            $arResult["DISPLAY_PROPERTIES"]["VIEW"],
            $arResult["PROPERTIES"]["VIEW"],
            $arResult["DISPLAY_PROPERTIES"]["INDCODE"],
            $arResult["PROPERTIES"]["INDCODE"],
            $arResult["DISPLAY_PROPERTIES"]["COMCODE"],
            $arResult["PROPERTIES"]["COMCODE"],
            $arResult["DISPLAY_PROPERTIES"]["POSITION"],
            $arResult["PROPERTIES"]["POSITION"]
        );

        CIBlockElement::SetPropertyValuesEx(
            $arResult['ID'],
            17,
            array('NEED_CHECK' => array('VALUE' => 56435))
        );
		
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arResult['ID']);


    }



}

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["model_new_link"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"])){

    $model_new_link = trim($arResult["DISPLAY_PROPERTIES"]["model_new_link"]["VALUE"]);
    $arModelLinkFilter = Array(
        "ID" => $model_new_link,
        "IBLOCK_ID" => 27,
    );

    $arModelLinkSelect = Array("NAME");

    $resModelLinkDB = CIBlockElement::GetList(Array("PROPERTY_MODEL_NEW_VALUE" => "ASC"), $arModelLinkFilter, false, false, $arModelLinkSelect);

    $resModelLinkArr = Array();

    if($resModelLinkDB){
        $resModelLinkArr = $resModelLinkDB->GetNext();

        if(isset($resModelLinkArr["NAME"])
            && !empty($resModelLinkArr["NAME"])){

            $arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"] = $resModelLinkArr["NAME"];

        }

    }


}

if (is_object($this->__component))
{

    $resultCacheKeys = array_keys($arResult);

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};