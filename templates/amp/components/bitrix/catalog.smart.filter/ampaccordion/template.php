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

		static $url;

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
        $uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
        $pageURL = $uri->GetUri();

        $JS_FILTER_PARAMS = array();
        if ($arParams["SEF_MODE"] == "Y")
        {
			if(is_null($url)) {
			
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
<div class="filters-wrapper">
    <button on="tap:filter" class="button-open-filters">
        <?php echo $buttonTitle;?>
    </button>
    <amp-lightbox id="filter" scrollable layout="nodisplay">
        <div class="lightbox">
            <div class="bx-filter">
                <button on="tap:filter.close" class="button-close-filters">
                    <i class="fa fa-times" aria-hidden="true">
                    </i>
                </button>
                <div class="bx-filter-section">
                    <div class="row">
                        <div class="bx-filter-title">
                            <?echo GetMessage("CT_BCSF_FILTER_TITLE")?>
                        </div>
                    </div>
                    <div class="currently-selected">
                        <?

                        //not prices

                        $arCurrentResult = $arResult["ITEMS"];

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

                                        if($cChecked):

                                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = false;

                                            $filterURL = tryToCreateForwardFilterLink($arResult,$arCurrentResult,$arParams);

                                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;

                                            ?>
                                            <a href="<?=$filterURL;?>"<?php if($cChecked): ?> class="checked"<?php endif; ?>>
                                                <?=$ar["VALUE"];?>
                                            </a>
                                        <?

                                        endif;

                                    endforeach;

                            }

                        }
                        ?>
                    </div>
                    <ul>
                        <?
						
						$arCurrentResult = $arResult["ITEMS"];
						$arChecked = array();

						foreach($arResult["ITEMS"] as $tkey => $artItem){

							foreach($artItem["VALUES"] as $tval => $tar){

								if(isset($tar["CHECKED"]))
									$arChecked[$tkey][$tval] = $tar["CHECKED"];

							}

						}

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
                            <li class="filter-parameters-box" <?if ($arItem["DISPLAY_EXPANDED"]== "Y") { ?><?php } ?>>
                                <input type="checkbox" id="chkf<?php echo $key; ?>" />
								<label for="chkf<?php echo $key; ?>">
                                    <?=$arItem["NAME"]?>
                                </label>
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
												$cChecked = isset($arChecked[$key][$val]) ? true : false;
					
												/* if($arItem["ID"] == 44 || $arItem["ID"] == 243) { */
												
													$aCopy = twigmFilters($arCurrentResult,$key,$val,$arChecked,$ar["DISABLED"]);
													$filterURL = tryToCreateForwardFilterLink($arResult,$aCopy,$arParams);

												/* } else {

													if($cChecked){
														$arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = false;
													} else {
														$arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = true;
													}
								
													$filterURL = tryToCreateForwardFilterLink($arResult,$arCurrentResult,$arParams);

												} */
												
												
												$arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;

												$hasActive = isset($arChecked[$key][$val]) && !$hasActive ? true : $hasActive;

												?>
                                                <a href="<?=$filterURL;?>"<?php if($cChecked): ?> class="checked"<?php endif; ?>>
                                                    <?=$ar["VALUE"];?>
                                                </a>
                                            <?

                                            endforeach;

                                    }
                                    ?>
                                </div>
                            </li>
                            <?
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </amp-lightbox>
</div>