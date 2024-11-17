<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
$arParams["form_index"] = $this->randString(4);

$arParams["FORM_ID"] = "REPLIER".$arParams["form_index"];
$arParams["jsObjName"] = "oLHE";
$arParams["LheId"] = "idLHE".$arParams["form_index"];

$arParams["tabIndex"] = intVal(intval($arParams["TAB_INDEX"]) > 0 ? $arParams["TAB_INDEX"] : 10);


$arParams["EDITOR_CODE_DEFAULT"] = ($arParams["EDITOR_CODE_DEFAULT"] == "Y" ? "Y" : "N");
$arResult["QUESTIONS"] = (is_array($arResult["QUESTIONS"]) ? array_values($arResult["QUESTIONS"]) : array());

$ibCommentsId = false;

$ibRes = CIBlock::GetList(
    Array(),
    Array(
        'CODE'=>'commentsrating',

    )
);


if($ibRes
    && $arIblock = $ibRes->Fetch()){

    $ibCommentsId = $arIblock['ID'];

    if($ibCommentsId){

        if(isset($arParams['ELEMENT_ID'])
            && !empty($arParams['ELEMENT_ID'])){
            $ibERes = CIBlockElement::GetByID((int)$arParams['ELEMENT_ID']);

            if($ibERes && $ibEAr = $ibERes->GetNext()){
                $arResult["ELEMENT_NAME"] = $ibEAr["NAME"];
            }

        }

        foreach ($arResult["MESSAGES"] as $kmes => $mes){

            $dbFRes = CIBlockElement::GetList(
                Array(),
                Array(
                    "IBLOCK_ID" => $ibCommentsId,
                    "PROPERTY_forum_topic_id" => $mes["ID"]
                ),
                false,
                false,
                Array("ID","PROPERTY_vote_count","PROPERTY_vote_sum","PROPERTY_rating")
            );

            if($dbFRes && $dbFAr = $dbFRes->GetNext()){

                $arResult["MESSAGES"][$kmes]["vote_count"] = $dbFAr["PROPERTY_VOTE_COUNT_VALUE"];
                $arResult["MESSAGES"][$kmes]["vote_sum"] = $dbFAr["PROPERTY_VOTE_SUM_VALUE"];
                $arResult["MESSAGES"][$kmes]["rating"] = $dbFAr["PROPERTY_RATING_VALUE"];
            }

        }

    }

}

if ($arParams['AJAX_POST']=='Y' && ($_REQUEST["save_product_review"] == "Y"))
{
    ob_start();
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

?>
