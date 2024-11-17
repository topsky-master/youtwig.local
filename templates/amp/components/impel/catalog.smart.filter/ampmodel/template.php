<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

$display_codes = $arParams["DISPLAY_CODES"];
$display_codes = explode(',',$display_codes);

$display_codes =!empty($display_codes) && !is_array($display_codes)
    ? array($display_codes)
    : $display_codes;

$display_codes = array_map('trim',$display_codes);
$display_codes = array_unique($display_codes);
$display_codes = array_filter($display_codes);

if(!function_exists('tryToCreateForwardFilterLink')){
    function tryToCreateForwardFilterLink($arResult,$arCurrents,$arParams){

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
        $uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
        $pageURL = $uri->GetUri();

        $JS_FILTER_PARAMS = array();
        if ($arParams["SEF_MODE"] == "Y")
        {
            $section = false;
            if ($arParams['SECTION_ID'] > 0)
            {
                $sectionList = CIBlockSection::GetList(array(), array(
                    "=ID" => (int)$arParams['SECTION_ID'],
                    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                ), false, array("ID", "IBLOCK_ID", "SECTION_PAGE_URL"));
                $sectionList->SetUrlTemplates($arParams["SEF_RULE"]);
                $section = $sectionList->GetNext();
            }

            if ($section)
            {
                $url = $section["DETAIL_PAGE_URL"];
            }
            else
            {
                $url = CIBlock::ReplaceSectionUrl($arParams["SEF_RULE"], array());
            }

            $CBitrixCatalogSmartFilter = new CBitrixCatalogSmartFilter;

            $CBitrixCatalogSmartFilter->arResult["ITEMS"] = $arCurrents;

            $JS_FILTER_PARAMS["SEF_SET_FILTER_URL"] = $CBitrixCatalogSmartFilter->makeSmartUrl($url, true);

        }

        $paramsToDelete = array("set_filter", "del_filter", "ajax", "bxajaxid", "AJAX_CALL", "mode");

        foreach($arResult["ITEMS"] as $arItem)
        {
            foreach($arItem["VALUES"] as $key => $ar)
            {
                $paramsToDelete[] = $ar["CONTROL_NAME"];
                $paramsToDelete[] = $ar["CONTROL_NAME_ALT"];
            }
        }

        $clearURL = CHTTP::urlDeleteParams($pageURL, $paramsToDelete, array("delete_system_params" => true));

        if ($JS_FILTER_PARAMS["SEF_SET_FILTER_URL"])
        {
            $FILTER_URL = $JS_FILTER_PARAMS["SEF_SET_FILTER_URL"];
        }
        else
        {
            $paramsToAdd = array(
                "set_filter" => "y",
            );
            foreach($CBitrixCatalogSmartFilter->arResult["ITEMS"] as $arItem)
            {
                foreach($arItem["VALUES"] as $key => $ar)
                {
                    foreach($arCurrents["VALUES"] as $arCurrent){

                        if(isset($arCurrent[$ar["CONTROL_NAME"]]))
                        {
                            if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
                                $paramsToAdd[$ar["CONTROL_NAME"]] = $arCurrent[$ar["CONTROL_NAME"]];
                            elseif($arCurrent[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
                                $paramsToAdd[$ar["CONTROL_NAME"]] = $arCurrent[$ar["CONTROL_NAME"]];
                        }
                        elseif(isset($arCurrent[$ar["CONTROL_NAME_ALT"]]))
                        {
                            if ($arCurrent[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"])
                                $paramsToAdd[$ar["CONTROL_NAME_ALT"]] = $arCurrent[$ar["CONTROL_NAME_ALT"]];
                        }

                    }
                }
            }


            $FILTER_URL = htmlspecialcharsbx(CHTTP::urlAddParams($clearURL, $paramsToAdd, array(
                "skip_empty" => true,
                "encode" => true,
            )));

        }


        return $FILTER_URL;

    }
}

$buttonTitle = '';

foreach($arResult["ITEMS"] as $key=>$arItem){

    if(
        empty($arItem["VALUES"])
        || isset($arItem["PRICE"])
        || (in_array($arItem["DISPLAY_TYPE"],array('A','B','U')))
    ){
        unset($arResult["ITEMS"][$key]);
        continue;
    }

    if (
        $arItem["DISPLAY_TYPE"] == "A"
        && (
            $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0
        )
    )
        continue;


    if(!empty($display_codes)
        && !in_array($arItem["CODE"],$display_codes)){
        continue;
    }

    $arCur = current($arItem["VALUES"]);
    switch ($arItem["DISPLAY_TYPE"]){
        case "A"://NUMBERS_WITH_SLIDER
            break;
        case "B"://NUMBERS
            break;
        case "U"://CALENDAR
            break;
        default://CHECKBOXES

            foreach($arItem["VALUES"] as $val => $ar):

                $arCurrent = $arItem;
                $cChecked = $ar["CHECKED"];

                if($cChecked){
                    $buttonTitle .= (!empty($buttonTitle) ? ', ' : '').$ar["VALUE"];
                }

            endforeach;

    }

}

$buttonTitle = empty($buttonTitle)
    ? GetMessage('CT_BCSF_FILTERS')
    : $buttonTitle;

?>
<div class="model-filters">
    <?

    $arCurrentResult = $arResult["ITEMS"];

    //not prices
    foreach($arCurrentResult as $key=>$arItem)
    {

        if(
            empty($arItem["VALUES"])
            || isset($arItem["PRICE"])
        )
            continue;

        if (
            $arItem["DISPLAY_TYPE"] == "A"
            && (
                $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0
            )
        )
            continue;


        if(!empty($display_codes)
            && !in_array($arItem["CODE"],$display_codes)){
            continue;
        }

        ?>
        <section class="filter-parameters-box" <?if ($arItem["DISPLAY_EXPANDED"]== "Y") { ?><?php } ?>>
            <h4>
                <?=$arItem["NAME"]?>
            </h4>
            <div class="tab-content">
                <?

                $arCur = current($arItem["VALUES"]);
                switch ($arItem["DISPLAY_TYPE"]){
                    case "A"://NUMBERS_WITH_SLIDER
                        break;
                    case "B"://NUMBERS
                        break;
                    case "U"://CALENDAR
                        break;
                    default://CHECKBOXES

                        foreach($arItem["VALUES"] as $val => $ar):

                            $arCurrent = $arItem;

                            $cChecked = $ar["CHECKED"];

                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = true;


                            $filterURL = tryToCreateForwardFilterLink($arResult,$arCurrentResult,$arParams);

                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;

                            ?>
                            <a href="<?=$filterURL;?>"<?php if($APPLICATION->GetCurDir() == $filterURL): ?> class="checked"<?php endif; ?>>
                                <?=$ar["VALUE"];?>
                            </a>
                        <?

                        endforeach;

                }
                ?>
            </div>
        </section>
        <?
    }
    ?>
</div>
