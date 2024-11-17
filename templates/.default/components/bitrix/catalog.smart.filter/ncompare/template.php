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

$buttonTitle = '';

foreach($arResult["ITEMS"] as $key=>$arItem){

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

    switch ($arItem["DISPLAY_TYPE"]){
        case "A"://NUMBERS_WITH_SLIDER
            break;
        case "B"://NUMBERS
            break;
        case "U"://CALENDAR
            break;
        default://CHECKBOXES

            foreach($arItem["VALUES"] as $val => $ar):

                if($ar["DISABLED"] == true)
                    unset($arItem["VALUES"][$val]);

                $arCurrent = $arItem;
                $cChecked = $ar["CHECKED"];

                if($cChecked){
                    $buttonTitle .= (!empty($buttonTitle) ? ', ' : '').$ar["VALUE"];
                }

            endforeach;

    }

    $arResult["ITEMS"][$key] = $arItem;

}

$buttonTitle = empty($buttonTitle)
    ? GetMessage('CT_BCSF_FILTERS')
    : $buttonTitle;

$displayElementCount = isset($arParams['HIDE_ELEMENT_COUNT'])
&& (int)$arParams['HIDE_ELEMENT_COUNT'] > 0
    ? (int)$arParams['HIDE_ELEMENT_COUNT']
    : 0;

$modals = [];

?>
    <button type="button" class="btn hidden-md hidden-lg" data-toggle="modal" data-target="#filterModal">
        <?=GetMessage('CT_BCSF_OPEN_FILTERS');?>
    </button>
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="modal-content">
                <div class="modal-body">
                    <div class="bx-filter-section">
                        <?

                        $arCurrentResult = $arResult["ITEMS"];
                        $arChecked = array();

                        foreach($arResult["ITEMS"] as $tkey => $artItem){

                            foreach($artItem["VALUES"] as $tval => $tar){

                                if(isset($tar["CHECKED"]) && $tar["CHECKED"])
                                    $arChecked[$tkey][$tval] = $tar["CHECKED"];

                            }

                        }

                        $arCurrentResult = $arResult["ITEMS"];

                        $anySelectedContent = '';

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

                                            $hasAnySelected = true;

                                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = false;

                                            $filterURL = twigSmartFilters::tryToCreateForwardFilterLink($arResult,$arCurrentResult,$arParams);

                                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;

                                            $anySelectedContent .= '
                            <a href="'.$filterURL.'"'.(($cChecked) ? ' class="checked"' : '').'>
                                '.$ar["VALUE"].'
                            </a>';

                                        endif;

                                    endforeach;

                            }

                        }

                        if($anySelectedContent
                            && $arParams['HIDE_CURRENTLY_SELECTED'] != 'Y') {
                            ?>
                            <div class="currently-selected">
                                <?=$anySelectedContent;?>
                            </div>
                            <?

                        }

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

                            $needCollapse = $displayElementCount
                            && sizeof($arItem["VALUES"]) > $displayElementCount
                                ? true
                                : false;

                            switch ($arItem["DISPLAY_TYPE"]){
                                case "A"://NUMBERS_WITH_SLIDER
                                case "B"://NUMBERS
                                case "U"://CALENDAR
                                    break;
                                default://CHECKBOXES

                                    ?>
                                    <section class="filter-parameters-box<?if ($arItem["DISPLAY_EXPANDED"]== "Y") { ?> expanded<?php } ?>">
                                        <?if ($arItem["FILTER_HINT"] <> ""):?>
                                            <?php $modals[$arItem["ID"]] = ['title' => $arItem["NAME"],'description' => $arItem["FILTER_HINT"]]; ?>
                                            <i id="item_title_hint_<?echo $arItem["ID"]?>" class="fa fa-question-circle" data-toggle="modal" data-target="#paramsModal<?=$arItem["ID"];?>"></i>
                                        <?endif?>
                                        <p class="filter-title">
                                            <?=$arItem["NAME"]?>
                                        </p>
                                        <?

                                        $filterContent = '';
                                        $hasActive = false;

                                        foreach($arItem["VALUES"] as $val => $ar):

                                            foreach($arResult["ITEMS"] as $tkey => $artItem){

                                                foreach($artItem["VALUES"] as $tval => $tar){

                                                    if(isset($arChecked[$tkey][$tval])) {
                                                        $arCurrentResult[$tkey]["VALUES"][$tval]["CHECKED"] = true;
                                                    } else {
                                                        $arCurrentResult[$tkey]["VALUES"][$tval]["CHECKED"] = false;
                                                    }

                                                }

                                            }

                                            $cChecked = isset($arChecked[$key][$val]) ? true : false;

                                            /* if($arItem["ID"] == 44 || $arItem["ID"] == 243) { */

                                            $aCopy = twigmFilters($arCurrentResult,$key,$val,$arChecked,$ar["DISABLED"]);
                                            $filterURL = twigSmartFilters::tryToCreateForwardFilterLink($arResult,$aCopy,$arParams);

                                            /*} else {
    
                                                if($cChecked){
                                                    $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = false;
                                                } else {
                                                    $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = true;
                                                }
    
                                                $filterURL = twigSmartFilters::tryToCreateForwardFilterLink($arResult,$arCurrentResult,$arParams);
    
                                            }*/

                                            $arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;

                                            $hasActive = isset($arChecked[$key][$val]) && !$hasActive ? true : $hasActive;

                                            $filterContent .= '<a href="'.$filterURL.'" '.(($cChecked) ?' class="checked"' : '').'>'.$ar["VALUE"].'</a>';


                                        endforeach;

                                        ?>
                                        <div class="filter-parameters-content<?php if($needCollapse): ?> filter-collapse<?php endif; ?><? if($hasActive): ?> filter-expanded<? endif; ?>"<?php if($needCollapse): ?> data-collapse-number="<?=$displayElementCount;?>"<?php endif; ?>>
                                            <?php echo $filterContent; ?>
                                            <?php if($needCollapse): ?>
                                                <p>
                                <span>
                                    <?php echo GetMessage('CT_BCSF_SHOW_FILTER');?>
                                </span>
                                                    <span>
                                    <?php echo GetMessage('CT_BCSF_HIDE_FILTER');?>
                                </span>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </section>
                                    <?
                                    break;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if (!empty($modals)): ?>
    <?php foreach($modals as $id => $hint): ?>
        <div class="modal params-modal fade" id="paramsModal<?=$id;?>" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <div class="modal-title"><?=$hint['title'];?></div>
                    </div>
                    <div class="modal-body">
                        <?=$hint['description'];?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>