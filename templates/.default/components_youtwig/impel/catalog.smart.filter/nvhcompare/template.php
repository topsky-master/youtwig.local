<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

$displayElementCount = isset($arParams['HIDE_ELEMENT_COUNT'])
&& (int)$arParams['HIDE_ELEMENT_COUNT'] > 0
    ? (int)$arParams['HIDE_ELEMENT_COUNT']
    : 0;


?>
<div class="bx-filter-section">
    <?

    $arCurrentResult = $arResult["ITEMS"];
    $arChecked = array();

    foreach($arResult["ITEMS"] as $tkey => $artItem){

        foreach($artItem["VALUES"] as $tval => $tar){

            if(isset($tar["CHECKED"]))
                $arChecked[$tkey][$tval] = $tar["CHECKED"];

        }

    }

    foreach($arResult["ITEMS"] as $key=>$arItem)
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
            case "B"://NUMBERS
            case "U"://CALENDAR
                break;
            default://CHECKBOXES

                ?>
                <section class="filter-parameters-box<?if ($arItem["DISPLAY_EXPANDED"]== "Y") { ?> expanded<?php } ?>">
                    <p class="filter-title">
                        <?=$arItem["NAME"]?>
                    </p>
                    <?php

                    $vsArItem = sizeof($arItem["VALUES"]);
                    $filterContent = '';
                    $hasActive = false;

                    foreach($arItem["VALUES"] as $val => $ar):

                    if($ar["DISABLED"] == true && $key != 46){
                             //unset($arItem["VALUES"][$val]);
                             continue;
                    }

                    $arCurrent = $arItem;

                    $cChecked = (int)$arChecked[$key][$val];
                    $artCurrentResult = twigmFilters($arCurrentResult,$key,$val,$arChecked,($key == 46 ?  $ar["DISABLED"] : false));

                    //$arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked ? false : true;

                    $filterURL = twigSmartFilters::tryToCreateForwardFilterLink($arResult,$artCurrentResult,$arParams);
                    $filterURL = str_ireplace('/filter/clear/','/',$filterURL);

                    //$arCurrentResult[$key]["VALUES"][$val]["CHECKED"] = $cChecked;


                    $hasActive = ($cChecked && !$hasActive) ? true : $hasActive;

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
                <!-- start: mdetail -->
                <?
                break;
        }
    }
    ?>
</div>