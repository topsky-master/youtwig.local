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
//$this->setFrameMode(true);

$cartId = "bx_smart_filter_compare";

?>
<div id="filterModel">
    <div>
        <div>
            <div>
                <div id="<?=$cartId;?>">
                    <?

                    $frame = $this->createFrame($cartId, false)->begin('<i class="fa fa-spinner faa-spin animated"></i>'.GetMessage('CT_BCSF_LOAD_FILTER'));


                    $templateData                               = array(
                        'TEMPLATE_THEME'                        =>$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/colors.css',
                        'TEMPLATE_CLASS'                        =>'bx-'.$arParams['TEMPLATE_THEME']
                    );

                    $elementsCount                              = isset($arParams["ELEMENTS_COLLAPSE"])
                    &&!empty($arParams["ELEMENTS_COLLAPSE"])
                        ? (int)$arParams["ELEMENTS_COLLAPSE"]
                        : 0;


                    $elementsCount                              = empty($elementsCount)
                        ? 0
                        : $elementsCount;


                    $display_codes                              = $arParams["DISPLAY_CODES"];
                    $display_codes                              = explode(',',$display_codes);

                    $display_codes                              =!empty($display_codes) && !is_array($display_codes)
                        ? array($display_codes)
                        : $display_codes;

                    $display_codes                              = array_map('trim',$display_codes);
                    $display_codes                              = array_unique($display_codes);
                    $display_codes                              = array_filter($display_codes);


                    $filter_view_hsearch                        = isset($arParams["DISPLAY_AS_SELECTS"])
                    &&!empty($arParams["DISPLAY_AS_SELECTS"])
                        ? explode(',',$arParams["DISPLAY_AS_SELECTS"])
                        : array();

                    $filter_view_hsearch                        = !is_array($filter_view_hsearch)
                        ? array($filter_view_hsearch)
                        : $filter_view_hsearch;


                    $item_counter                               = -1;

                    $formFolder                                 = trim($arParams["FORM_CATALOG"]);

                    if(!empty($formFolder)){
                        $arResult["FORM_ACTION"]                = $formFolder.$arResult["FORM_ACTION"];
                    }

                    $filter_button_name                         = trim($arParams["FILTER_BUTTON_NAME"]);
                    $filter_button_name                         = !empty($filter_button_name)
                        ? $filter_button_name
                        : GetMessage("CT_BCSF_SET_FILTER");

                    ?>
                    <div class="bx-filter compare <?=$templateData["TEMPLATE_CLASS"]?> <?if ($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL"){ echo "bx-filter-horizontal"; } else { echo "bx-filter-vertical"; } ?>">
                        <div class="bx-filter-section container-fluid">
                            <div class="row"><div class="col-lg-12 bx-filter-title"><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></div></div>
                            <form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
                                <?foreach($arResult["HIDDEN"] as $arItem):?>
                                    <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
                                <?endforeach;?>
                                <div class="hidden-sm hidden-md hidden-lg" id="active-filters">
                                    <?php
                                    foreach($arResult["ITEMS"] as $key => $arItem){

                                        switch ($arItem["DISPLAY_TYPE"]){

                                            default:

                                                foreach($arItem["VALUES"] as $val=>$ar){

                                                    if($ar["CHECKED"]){

                                                        ?>
                                                        <div class="checkbox checkbox-selected">
                                                            <label data-role="label_<?= $ar["CONTROL_ID"] ?>" class="bx-filter-param-label <? echo $ar["DISABLED"] ? 'disabled' : '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
                                            <span class="bx-filter-input-checkbox">
                                                <span class="bx-filter-param-text" title="<?= $ar["VALUE"]; ?>">
                                                    <?=$ar["VALUE"]; ?>
                                                    <?
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N"
                                                        && isset($ar["ELEMENT_COUNT"])){ ?>
                                                        (
                                                        <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                <? echo $ar["ELEMENT_COUNT"]; ?>
                                                            </span>
                                                        )
                                                    <? }; ?>
                                                </span>
                                            </span>
                                                                <span aria-hidden="true">
                                                &times;
                                            </span>
                                                            </label>
                                                        </div>
                                                        <?php

                                                    };

                                                };

                                                break;

                                        }

                                    }

                                    ?>
                                </div>
                                <?

                                if(false) //hide prices
                                    foreach($arResult["ITEMS"] as $key=>$arItem)//prices
                                    {

                                        if(!empty($display_codes) && !in_array($arItem["CODE"],$display_codes)){
                                            continue;
                                        }

                                        $key = $arItem["ENCODED_ID"];
                                        if(isset($arItem["PRICE"])):
                                            if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
                                                continue;

                                            $precision = 2;
                                            if (Bitrix\Main\Loader::includeModule("currency"))
                                            {
                                                $res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
                                                $precision = $res['DECIMALS'];
                                            }

                                            ++$item_counter;

                                            if(mb_stripos($arItem["NAME"],' ') !== false){

                                                $arItem["NAME"] = join('</b> <b>', explode(' ',$arItem["NAME"]));
                                            };

                                            ?>

                                            <div class="col-lg-12 bx-filter-parameters-box bx-active <? echo mb_strtolower(preg_replace('~[^a-z0-9\-\_]~is','',$arItem['CODE'])); ?>">
                                                <span class="bx-filter-container-modef">
                                                </span>
                                                <div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)">
                                                    <span>
                                                        <b><?=$arItem["NAME"]?></b>
                                                        <i data-role="prop_angle" class="fa fa-angle-<?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?>up<?else:?>down<?endif?>">
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="bx-filter-block" data-role="bx_filter_block">
                                                    <div class="row bx-filter-parameters-box-container">
                                                        <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                                            <div class="form-group">
                                                                <label class="bx-ft-sub col-sm-2 control-label" for="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>">
                                                                    <?=GetMessage("CT_BCSF_FILTER_FROM")?>
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input
                                                                            class="min-price form-control"
                                                                            type="text"
                                                                            name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                                            id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                                            value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                                                            size="5"
                                                                            onkeyup="smartFilter.keyup(this)"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                                            <div class="form-group">
                                                                <label class="bx-ft-sub col-sm-2 control-label" for="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>">
                                                                    <?=GetMessage("CT_BCSF_FILTER_TO")?>
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input
                                                                            class="max-price form-control"
                                                                            type="text"
                                                                            name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                                            id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                                            value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                                                            size="5"
                                                                            onkeyup="smartFilter.keyup(this)"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 bx-ui-slider-track-container">
                                                            <div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
                                                                <?
                                                                $precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
                                                                $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
                                                                $price1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
                                                                $price2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
                                                                $price3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
                                                                $price4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
                                                                $price5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
                                                                ?>
                                                                <div class="bx-ui-slider-part p1"><span><?=$price1?></span></div>
                                                                <div class="bx-ui-slider-part p2"><span><?=$price2?></span></div>
                                                                <div class="bx-ui-slider-part p3"><span><?=$price3?></span></div>
                                                                <div class="bx-ui-slider-part p4"><span><?=$price4?></span></div>
                                                                <div class="bx-ui-slider-part p5"><span><?=$price5?></span></div>

                                                                <div class="bx-ui-slider-pricebar-vd" id="colorUnavailableActive_<?=$key?>"></div>
                                                                <div class="bx-ui-slider-pricebar-vn" id="colorAvailableInactive_<?=$key?>"></div>
                                                                <div class="bx-ui-slider-pricebar-v" id="colorAvailableActive_<?=$key?>"></div>
                                                                <div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>"  style="left: 0%; right: 0%;">
                                                                    <a class="bx-ui-slider-handle left" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
                                                                    <a class="bx-ui-slider-handle right" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?
                                        $arJsParams = array(
                                            "leftSlider" => 'left_slider_'.$key,
                                            "rightSlider" => 'right_slider_'.$key,
                                            "tracker" => "drag_tracker_".$key,
                                            "trackerWrap" => "drag_track_".$key,
                                            "minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
                                            "maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
                                            "minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
                                            "maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
                                            "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                            "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                            "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
                                            "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                                            "precision" => $precision,
                                            "colorUnavailableActive" => 'colorUnavailableActive_'.$key,
                                            "colorAvailableActive" => 'colorAvailableActive_'.$key,
                                            "colorAvailableInactive" => 'colorAvailableInactive_'.$key,
                                        );
                                        ?>
                                            <script type="text/javascript">
                                                BX.ready(function(){
                                                    window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
                                                });
                                            </script>
                                        <?endif;
                                    }

                                //not prices

                                $main_parameter = array();
                                $main_parameter_sizeof = \COption::GetOptionString('my.stat', "main_parameter_sizeof", 0, SITE_ID);

                                if($main_parameter_sizeof > 0)
                                    for($i = 0; $i < $main_parameter_sizeof; $i ++){

                                        $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id".$i, "", SITE_ID);
                                        $main_parameter['chain'][$i] = \COption::GetOptionString('my.stat', "main_parameter_chain".$i, "", SITE_ID);
                                        $main_parameter['value'][$i] = \COption::GetOptionString('my.stat', "main_parameter_value".$i, "", SITE_ID);

                                    }

                                if(isset($main_parameter['value']) && !empty($main_parameter['value'])){

                                    $cValues = $main_parameter['value'];

                                    foreach($cValues as $number => $value){
                                        if(mb_stripos($value,',') !== false){

                                            $values = explode(',',$value);
                                            $values = array_map('trim',$values);
                                            $current = current($values);
                                            $main_parameter['value'][$number] = $current;
                                            unset($values[0]);

                                            $main_parameter['value'] = array_merge($main_parameter['value'],$values);
                                            $main_parameter['id'] = array_merge($main_parameter['id'],array_fill(0,sizeof($values),$main_parameter['id'][$number]));
                                            $main_parameter['chain'] = array_merge($main_parameter['chain'],array_fill(0,sizeof($values),$main_parameter['chain'][$number]));

                                        }


                                    }


                                }


                                $allHidden = $main_parameter;

                                $hideClass = array();

                                if(isset($main_parameter['chain'])
                                    && !empty($main_parameter['chain'])
                                ){

                                    $main_parameter['chain'] = !empty($main_parameter['chain'])
                                    && !is_array($main_parameter['chain'])
                                        ? array($main_parameter['chain'])
                                        : $main_parameter['chain'];

                                    foreach($main_parameter['chain'] as $cnumber => $id){

                                        $pid = $main_parameter['id'][$cnumber];
                                        $pvalue = $main_parameter['value'][$cnumber];
                                        $anyChecked = false;

                                        if(!isset($hideClass[$id])){
                                            $hideClass[$id] = true;
                                        }

                                        $isHidden = true;

                                        if(!empty($pvalue)){

                                            foreach($arResult["ITEMS"] as $key => $arItem){

                                                switch ($arItem["DISPLAY_TYPE"]) {
                                                    default:

                                                        foreach ($arItem["VALUES"] as $val => $ar) {

                                                            if($ar['CHECKED']
                                                                && $arItem['ID'] == $pid
                                                                && trim($ar['VALUE']) == trim($pvalue)
                                                            ){
                                                                $isHidden = false;
                                                            }

                                                        }

                                                        break;
                                                }

                                            }

                                        }

                                        if(!$isHidden){
                                            $hideClass[$id] = false;
                                        }

                                    }

                                }

                                foreach($arResult["ITEMS"] as $key=>$arItem)
                                {

                                    if(!empty($display_codes) && !in_array($arItem["CODE"],$display_codes)){
                                        continue;
                                    }

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

                                    ++$item_counter;

                                    if(mb_stripos($arItem["NAME"],' ') !== false){

                                        $arItem["NAME"] = join('</b> <b>', explode(' ',$arItem["NAME"]));

                                    };

                                    if(isset($main_parameter['chain'])
                                        && in_array($arItem['ID'],$main_parameter['chain'])){
                                        switch ($arItem["DISPLAY_TYPE"]) {
                                            default:

                                                $isOneCanChecked = false;

                                                foreach($arItem["VALUES"] as $val => $ar){
                                                    if(!$ar["DISABLED"]){
                                                        $isOneCanChecked = true;
                                                    }
                                                }

                                                if(!$isOneCanChecked){

                                                    $hideClass[$arItem['ID']] = true;

                                                }

                                                break;
                                        }


                                    }

                                    ?>
                                    <div class="col-lg-12 bx-filter-parameters-box  bx-filter-parameters-box-<? echo mb_strtolower(preg_replace('~[^a-z0-9\-\_]~is','',$arItem['ID'])); ?><?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?> bx-active<?endif?><? if(isset($hideClass[$arItem['ID']]) && $hideClass[$arItem['ID']]):?> hidden<? endif; ?> <? echo mb_strtolower(preg_replace('~[^a-z0-9\-\_]~is','',$arItem['CODE'])); ?>">
                                        <span class="bx-filter-container-modef"></span>
                                        <div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)">
                                            <span class="bx-filter-parameters-box-hint"><b><?=$arItem["NAME"]?></b>
                                                <?if ($arItem["FILTER_HINT"] <> ""):?>
                                                    <i id="item_title_hint_<?echo $arItem["ID"]?>" class="fa fa-question-circle"></i>
                                                    <script type="text/javascript">
                                                        new top.BX.CHint({
                                                            parent: top.BX("item_title_hint_<?echo $arItem["ID"]?>"),
                                                            show_timeout: 10,
                                                            hide_timeout: 200,
                                                            dx: 2,
                                                            preventHide: true,
                                                            min_width: 250,
                                                            hint: '<?= CUtil::JSEscape($arItem["FILTER_HINT"])?>'
                                                        });
                                                    </script>
                                                <?endif?>
                                                <i data-role="prop_angle" class="fa fa-angle-<?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?>up<?else:?>down<?endif?>">
                                                </i>
                                            </span>
                                        </div>

                                        <div class="bx-filter-block" data-role="bx_filter_block">
                                            <div class="bx-filter-parameters-box-container">
                                                <?
                                                $arCur = current($arItem["VALUES"]);
                                                switch ($arItem["DISPLAY_TYPE"])
                                                {
                                                case "A"://NUMBERS_WITH_SLIDER
                                                    ?>
                                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                                        <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
                                                        <div class="bx-filter-input-container">
                                                            <input
                                                                    class="min-price"
                                                                    type="text"
                                                                    name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                                    id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                                    value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                                                    size="5"
                                                                    onkeyup="smartFilter.keyup(this)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                                        <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
                                                        <div class="bx-filter-input-container">
                                                            <input
                                                                    class="max-price"
                                                                    type="text"
                                                                    name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                                    id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                                    value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                                                    size="5"
                                                                    onkeyup="smartFilter.keyup(this)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-10 col-xs-offset-1 bx-ui-slider-track-container">
                                                        <div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
                                                            <?
                                                            $precision = $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0;
                                                            $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
                                                            $value1 = number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "");
                                                            $value2 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step, $precision, ".", "");
                                                            $value3 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 2, $precision, ".", "");
                                                            $value4 = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * 3, $precision, ".", "");
                                                            $value5 = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
                                                            ?>
                                                            <div class="bx-ui-slider-part p1"><span><?=$value1?></span></div>
                                                            <div class="bx-ui-slider-part p2"><span><?=$value2?></span></div>
                                                            <div class="bx-ui-slider-part p3"><span><?=$value3?></span></div>
                                                            <div class="bx-ui-slider-part p4"><span><?=$value4?></span></div>
                                                            <div class="bx-ui-slider-part p5"><span><?=$value5?></span></div>

                                                            <div class="bx-ui-slider-pricebar-vd" id="colorUnavailableActive_<?=$key?>"></div>
                                                            <div class="bx-ui-slider-pricebar-vn" id="colorAvailableInactive_<?=$key?>"></div>
                                                            <div class="bx-ui-slider-pricebar-v"  id="colorAvailableActive_<?=$key?>"></div>
                                                            <div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>"  style="left: 0;right: 0;">
                                                                <a class="bx-ui-slider-handle left"  href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
                                                                <a class="bx-ui-slider-handle right" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?
                                                $arJsParams = array(
                                                    "leftSlider" => 'left_slider_'.$key,
                                                    "rightSlider" => 'right_slider_'.$key,
                                                    "tracker" => "drag_tracker_".$key,
                                                    "trackerWrap" => "drag_track_".$key,
                                                    "minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
                                                    "maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
                                                    "minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
                                                    "maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
                                                    "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                                    "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                                    "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
                                                    "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                                                    "precision" => $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0,
                                                    "colorUnavailableActive" => 'colorUnavailableActive_'.$key,
                                                    "colorAvailableActive" => 'colorAvailableActive_'.$key,
                                                    "colorAvailableInactive" => 'colorAvailableInactive_'.$key,
                                                );
                                                ?>
                                                    <script type="text/javascript">
                                                        BX.ready(function(){
                                                            window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
                                                        });
                                                    </script>
                                                <?
                                                break;
                                                case "B"://NUMBERS

                                                ?>
                                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                                        <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
                                                        <div class="bx-filter-input-container">
                                                            <input
                                                                    class="min-price"
                                                                    type="text"
                                                                    name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                                                    id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                                                    value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                                                    size="5"
                                                                    onkeyup="smartFilter.keyup(this)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                                        <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
                                                        <div class="bx-filter-input-container">
                                                            <input
                                                                    class="max-price"
                                                                    type="text"
                                                                    name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                                                    id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                                                    value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                                                    size="5"
                                                                    onkeyup="smartFilter.keyup(this)"
                                                            />
                                                        </div>
                                                    </div>
                                                <?

                                                break;
                                                case "G"://CHECKBOXES_WITH_PICTURES
                                                ?>
                                                    <div class="bx-filter-param-btn-inline">
                                                        <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                                            <input
                                                                    class="hidden"
                                                                    type="checkbox"
                                                                    name="<?=$ar["CONTROL_NAME"]?>"
                                                                    id="<?=$ar["CONTROL_ID"]?>"
                                                                    value="<?=$ar["HTML_VALUE"]?>"
                                                                <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                            />
                                                            <?
                                                            $class = "";
                                                            if ($ar["CHECKED"])
                                                                $class.= " bx-active";
                                                            if ($ar["DISABLED"])
                                                                $class.= " disabled";
                                                            ?>
                                                            <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
                                                                <span class="bx-filter-param-btn bx-color-sl">
                                                                    <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                        <span class="bx-filter-btn-color-icon">
                                                                        <img src="<?=$ar["FILE"]["SRC"]?>" />
                                                                    </span>
                                                                    <?endif?>
                                                                </span>
                                                            </label>
                                                        <?endforeach?>
                                                    </div>
                                                <?
                                                break;
                                                case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
                                                ?>
                                                    <div class="bx-filter-param-btn-block">
                                                        <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                                            <input
                                                                    class="hidden"
                                                                    type="checkbox"
                                                                    name="<?=$ar["CONTROL_NAME"]?>"
                                                                    id="<?=$ar["CONTROL_ID"]?>"
                                                                    value="<?=$ar["HTML_VALUE"]?>"
                                                                <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                            />
                                                            <?
                                                            $class = "";
                                                            if ($ar["CHECKED"])
                                                                $class.= " bx-active";
                                                            if ($ar["DISABLED"])
                                                                $class.= " disabled";
                                                            ?>
                                                            <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
                                                                <span class="bx-filter-param-btn bx-color-sl">
                                                                    <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                        <span class="bx-filter-btn-color-icon">
                                                                            <img src="<?=$ar["FILE"]["SRC"]?>" />
                                                                        </span>
                                                                    <?endif?>
                                                                </span>
                                                                <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                                        ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                                    endif;?></span>
                                                            </label>
                                                        <?endforeach?>
                                                    </div>
                                                <?
                                                break;
                                                case "P"://DROPDOWN
                                                $checkedItemExist = false;
                                                ?>
                                                    <div class="hidden">
                                                        <?
                                                        foreach($arItem["VALUES"] as $val                   =>$ar):

                                                            $elementXMLID                                   = preg_replace('~[^0-9\-_a-z]+~i','',$ar['URL_ID']);
                                                            ?>
                                                            <div class="click<? echo $elementXMLID; ?>">
                                                                <input
                                                                        type="checkbox"
                                                                        value="<? echo $ar["HTML_VALUE"] ?>"
                                                                        name="<? echo $ar["CONTROL_NAME"] ?>"
                                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                                        class="<? echo $ar["CONTROL_ID"] ?>"
                                                                    <? echo $ar["CHECKED"]? 'checked="true"': '' ?>
                                                                        onclick="smartFilter.click(this)"
                                                                />
                                                            </div>
                                                            <?
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                <?
                                                $filterCode                                         = $arItem['CODE'];

                                                $mParams 				                            = Array(
                                                    "max_len" 			                            =>"200",
                                                    "change_case" 		                            =>"L",
                                                    "replace_space" 	                            =>"_",
                                                    "replace_other" 	                            =>"_",
                                                    "delete_repeat_replace"                         =>"true",
                                                );

                                                $filterCode				                            = CUtil::translit($filterCode, LANGUAGE_ID, $mParams);

                                                $collapseid                                         = $this->randString();
                                                $chosenid                                           = $filterCode.$collapseid;

                                                ?>
                                                    <select id="chosen<? echo $chosenid; ?>" title="<? echo newsListMainTemplateTools::prepareTitle($arItem["NAME"]); ?>" data-live-search="true" class="selectchosen selectchosen<? echo $chosenid; ?>" multiple="multiple" onchange="selectchosen<? echo $chosenid; ?>(this);">
                                                        <?
                                                        foreach($arItem["VALUES"] as $val                   =>$ar):?>
                                                            <option value="<?=$ar["VALUE"];?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):?> (<? echo $ar["ELEMENT_COUNT"]; ?>)<? endif;?>" data:id=".<? echo $ar["CONTROL_ID"] ?>"<? if($ar["CHECKED"]): ?> selected="true"<? endif; ?>><?=$ar["VALUE"];?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):?> (<? echo $ar["ELEMENT_COUNT"]; ?>)<? endif;?></option>
                                                            <?
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                    <script type="text/javascript">
                                                        //<!--
                                                        function selectchosen<? echo $chosenid; ?>(sSelect){

                                                            $("option",sSelect).each(function(){

                                                                var vSelected                               = this.selected;
                                                                vSelected                                   = vSelected == true ? true : false;

                                                                var vChecked                                = $($(this).attr("data:id"))[0].checked;
                                                                vChecked                                    = vChecked == true ? true : false;

                                                                if(vSelected                                !=vChecked){
                                                                    $($(this).attr("data:id")).click();
                                                                    $($(this).attr("data:id")).attr("checked",vSelected);
                                                                };

                                                            });

                                                            $('#chosen<? echo $chosenid; ?>').selectpicker('refresh');

                                                        };

                                                        $('#chosen<? echo $chosenid; ?>').selectpicker({
                                                            //liveSearchPlaceholder: "<? echo GetMessage("CT_BCSF_FILTER_START_TYPING"); ?>"
                                                        });

                                                        //-->
                                                    </script>
                                                <?
                                                break;
                                                case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
                                                ?>
                                                    <div class="bx-filter-select-container">
                                                        <div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
                                                            <div class="bx-filter-select-text fix" data-role="currentOption">
                                                                <?
                                                                $checkedItemExist = false;
                                                                foreach ($arItem["VALUES"] as $val => $ar):
                                                                    if ($ar["CHECKED"])
                                                                    {
                                                                        ?>
                                                                        <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                        <span class="bx-filter-btn-color-icon">
                                                                            <img src="<?=$ar["FILE"]["SRC"]?>" />
                                                                        </span>
                                                                    <?endif?>
                                                                        <span class="bx-filter-param-text">
                                                                            <?=$ar["VALUE"]?>
                                                                        </span>
                                                                        <?
                                                                        $checkedItemExist = true;
                                                                    }
                                                                endforeach;
                                                                if (!$checkedItemExist)
                                                                {
                                                                    ?><span class="bx-filter-btn-color-icon all"></span> <?
                                                                    echo GetMessage("CT_BCSF_FILTER_ALL");
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="bx-filter-select-arrow"></div>
                                                            <input
                                                                    class="hidden"
                                                                    type="radio"
                                                                    name="<?=$arCur["CONTROL_NAME_ALT"]?>"
                                                                    id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                                                    value=""
                                                            />
                                                            <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                                                <input
                                                                        class="hidden"
                                                                        type="radio"
                                                                        name="<?=$ar["CONTROL_NAME_ALT"]?>"
                                                                        id="<?=$ar["CONTROL_ID"]?>"
                                                                        value="<?=$ar["HTML_VALUE_ALT"]?>"
                                                                    <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                                />
                                                            <?endforeach?>
                                                            <div class="bx-filter-select-popup" data-role="dropdownContent" class="display-none">
                                                                <ul>
                                                                    <li>
                                                                        <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                                            <span class="bx-filter-btn-color-icon all"></span>
                                                                            <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
                                                                        </label>
                                                                    </li>
                                                                    <?
                                                                    foreach ($arItem["VALUES"] as $val => $ar):
                                                                        $class = "";
                                                                        if ($ar["CHECKED"])
                                                                            $class.= " selected";
                                                                        if ($ar["DISABLED"])
                                                                            $class.= " disabled";
                                                                        ?>
                                                                        <li>
                                                                            <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
                                                                                <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                                    <span class="bx-filter-btn-color-icon">
                                                                                        <img src="<?=$ar["FILE"]["SRC"]?>" />
                                                                                    </span>
                                                                                <?endif?>
                                                                                <span class="bx-filter-param-text">
                                                                                    <?=$ar["VALUE"]?>
                                                                                </span>
                                                                            </label>
                                                                        </li>
                                                                    <?endforeach?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?
                                                break;
                                                case "K"://RADIO_BUTTONS
                                                ?>
                                                    <div class="radio">
                                                        <label class="bx-filter-param-label" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
                                                            <span class="bx-filter-input-checkbox">
                                                                <input
                                                                        type="radio"
                                                                        value=""
                                                                        name="<? echo $arCur["CONTROL_NAME_ALT"] ?>"
                                                                        id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                                                        onclick="smartFilter.click(this)"
                                                                />
                                                                <span class="bx-filter-param-text"><? echo GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <?foreach($arItem["VALUES"] as $val => $ar):?>
                                                    <div class="radio">
                                                        <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label" for="<? echo $ar["CONTROL_ID"] ?>">
                                                            <span class="bx-filter-input-checkbox <? echo $ar["DISABLED"] ? 'disabled': '' ?>">
                                                                <input
                                                                        type="radio"
                                                                        value="<? echo $ar["HTML_VALUE_ALT"] ?>"
                                                                        name="<? echo $ar["CONTROL_NAME_ALT"] ?>"
                                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                                    <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                                        onclick="smartFilter.click(this)"
                                                                />
                                                                <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                                        ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                                    endif;?></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?endforeach;?>
                                                <?
                                                break;
                                                case "U"://CALENDAR
                                                ?>
                                                    <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                                            <?$APPLICATION->IncludeComponent(
                                                                'bitrix:main.calendar',
                                                                '',
                                                                array(
                                                                    'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
                                                                    'SHOW_INPUT' => 'Y',
                                                                    'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                                                    'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
                                                                    'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                                                    'SHOW_TIME' => 'N',
                                                                    'HIDE_TIMEBAR' => 'Y',
                                                                ),
                                                                null,
                                                                array('HIDE_ICONS' => 'Y')
                                                            );?>
                                                        </div>
                                                    </div>
                                                    <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                                            <?$APPLICATION->IncludeComponent(
                                                                'bitrix:main.calendar',
                                                                '',
                                                                array(
                                                                    'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
                                                                    'SHOW_INPUT' => 'Y',
                                                                    'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                                                    'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
                                                                    'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                                                    'SHOW_TIME' => 'N',
                                                                    'HIDE_TIMEBAR' => 'Y',
                                                                ),
                                                                null,
                                                                array('HIDE_ICONS' => 'Y')
                                                            );?>
                                                        </div></div>
                                                <?
                                                break;
                                                default://CHECKBOXES

                                                $selectWithSearch                                       =(!empty($filter_view_hsearch)
                                                    &&in_array($arItem['CODE'],$filter_view_hsearch))
                                                    ? true
                                                    : false;


                                                if(!$selectWithSearch){

                                                $elementsCounter                                        = 0;
                                                $currentIterator                                        = 0;
                                                $isOpened                                               = false;
                                                $checkboxID                                             = mb_strtolower(preg_replace('~[^a-z0-9\-\_]~is','',$arItem['CODE']));

                                                ?>
                                                    <div class="checkbox-wrapper<?php if(isset($main_parameter['chain']) && in_array($arItem['ID'],$main_parameter['chain'])):?> force-disabled<?php endif; ?>" data-id="<?=$checkboxID;?>" id="wrapper<?=$checkboxID;?>">
                                                        <?


                                                        foreach($arItem["VALUES"] as $val                       =>$ar){

                                                            $url_id                                         = $ar['URL_ID'];
                                                            $mParams 					                    = Array(
                                                                "max_len" 				                    =>"200",
                                                                "change_case" 			                    =>"L",
                                                                "replace_space" 		                    =>"_",
                                                                "replace_other" 		                    =>"_",
                                                                "delete_repeat_replace"                     =>"true",
                                                            );

                                                            $url_id				                            = CUtil::translit($url_id, LANGUAGE_ID, $mParams);
                                                            $elementXMLID                                   = preg_replace('~[^0-9\-_a-z]+~i','',$ar['URL_ID']);

                                                            ?>
                                                            <div class="checkbox click<? echo $elementXMLID; ?>">
                                                                <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label bx-filter-param-label-<? echo $url_id; ?> <? echo $ar["DISABLED"] ? 'disabled': '' ?>">
                                                                    <span class="bx-filter-input-checkbox">
                                                                        <input
                                                                                type="checkbox"
                                                                                value="<? echo $ar["HTML_VALUE"] ?>"
                                                                                name="<? echo $ar["CONTROL_NAME"] ?>"
                                                                                id="<? echo $ar["CONTROL_ID"] ?>"
                                                                            <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                                                onclick="smartFilter.click(this)"
                                                                                data:value="<?=htmlspecialcharsbx($ar["VALUE"]);?>"
                                                                                data:itemid="<?=$arItem['ID'];?>"
                                                                        />
                                                                        <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>">
                                                                            <?=$ar["VALUE"];?>
                                                                            <?
                                                                            if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                                                ?>
                                                                                (
                                                                                <span data-role="count_<?=$ar["CONTROL_ID"]?>">
                                                                                        <? echo $ar["ELEMENT_COUNT"]; ?>
                                                                                    </span>
                                                                                )
                                                                                <?
                                                                            endif;?>
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <?

                                                        };

                                                        ?>
                                                    </div>
                                                    <button class="btn btn-more collapsed display-none" id="open<?=$checkboxID;?>" data-id="<?=$checkboxID;?>" type="button">
                                                        <span>
                                                            <i class="fa fa-angle-down"></i>
                                                            <span>
                                                                <? echo GetMessage('CT_BCSF_BTN_MORE'); ?>
                                                            </span>
                                                        </span>
                                                        <span>
                                                            <i class="fa fa-angle-up"></i>
                                                            <span>
                                                                <? echo GetMessage('CT_BCSF_BTN_HIDE'); ?>
                                                            </span>
                                                        </span>
                                                    </button>
                                                <?



                                                } else {


                                                ?>
                                                    <div class="hidden">
                                                        <?
                                                        foreach($arItem["VALUES"] as $val               =>$ar):

                                                            $elementXMLID                               = preg_replace('~[^0-9\-_a-z]+~i','',$ar['URL_ID']);


                                                            ?>
                                                            <div class="click<? echo $elementXMLID; ?>">
                                                                <input
                                                                        type="checkbox"
                                                                        value="<? echo $ar["HTML_VALUE"] ?>"
                                                                        name="<? echo $ar["CONTROL_NAME"] ?>"
                                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                                    <? echo $ar["CHECKED"]? 'checked="true"': '' ?>
                                                                        onclick="smartFilter.click(this)"
                                                                        data:value="<?=htmlspecialcharsbx($ar["VALUE"]);?>"
                                                                        data:itemid="<?=$arItem['ID'];?>"
                                                                />
                                                            </div>
                                                            <?
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                <?
                                                $filterCode                                 = $arItem['CODE'];

                                                $mParams 				                    = Array(
                                                    "max_len" 			                    =>"200",
                                                    "change_case" 		                    =>"L",
                                                    "replace_space" 	                    =>"_",
                                                    "replace_other" 	                    =>"_",
                                                    "delete_repeat_replace"                 =>"true",
                                                );

                                                $filterCode				                    = CUtil::translit($filterCode, LANGUAGE_ID, $mParams);

                                                $collapseid                                 = $this->randString();
                                                $chosenid                                   = $filterCode.$collapseid;
                                                ?>
                                                    <select id="chosen<? echo $chosenid; ?>" title="<? echo newsListMainTemplateTools::prepareTitle($arItem["NAME"]); ?>" data-live-search="true" class="selectchosen selectchosen<? echo $chosenid; ?>" multiple="multiple" onchange="selectchosen<? echo $chosenid; ?>(this);">
                                                        <?
                                                        foreach($arItem["VALUES"] as $val               =>$ar):?>
                                                            <option value="<?=$ar["VALUE"];?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):?> (<? echo $ar["ELEMENT_COUNT"]; ?>)<? endif;?>" data:id="#<? echo $ar["CONTROL_ID"] ?>"<? if($ar["CHECKED"]): ?> selected="true"<? endif; ?>><?=$ar["VALUE"];?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):?> (<? echo $ar["ELEMENT_COUNT"]; ?>)<? endif;?></option>
                                                            <?
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                    <script type="text/javascript">
                                                        //<!--
                                                        function selectchosen<? echo $chosenid; ?>(sSelect){

                                                            $("option",sSelect).each(function(){

                                                                var vSelected                               = this.selected;
                                                                vSelected                                   = vSelected == true ? true : false;

                                                                var vChecked                                = $($(this).attr("data:id"))[0].checked;
                                                                vChecked                                    = vChecked == true ? true : false;

                                                                if(vSelected                                !=vChecked){
                                                                    $($(this).attr("data:id")).click();
                                                                    $($(this).attr("data:id")).attr("checked",vSelected);
                                                                };

                                                            });

                                                            $('#chosen<? echo $chosenid; ?>').selectpicker('refresh');

                                                        };

                                                        $('#chosen<? echo $chosenid; ?>').selectpicker({
                                                            //liveSearchPlaceholder: "<? echo GetMessage("CT_BCSF_FILTER_START_TYPING"); ?>"
                                                        });

                                                        //-->
                                                    </script>
                                                    <?

                                                };

                                                }
                                                ?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                    <?
                                }
                                ?>
                                <?

                                ++$item_counter;

                                ?>
                                <div class="col-lg-12 bx-filter-button-box">
                                    <div class="bx-filter-block">
                                        <div class="bx-filter-parameters-box-container">
                                            <input
                                                    class="btn btn-themes"
                                                    type="submit"
                                                    id="set_filter"
                                                    name="set_filter"
                                                    value="<?=$filter_button_name?>"
                                            />
                                            <input
                                                    class="btn btn-link"
                                                    type="submit"
                                                    id="del_filter"
                                                    name="del_filter"
                                                    value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>"
                                            />
                                            <div class="bx-filter-popup-result <?if ($arParams["FILTER_VIEW_MODE"] == "VERTICAL") echo $arParams["POPUP_POSITION"]?><?if(!isset($arResult["ELEMENT_COUNT"])) echo ' display-none';?>" id="modef">
                                                <?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
                                                <span class="arrow"></span>
                                                <br/>
                                                <a href="<?echo $arResult["FILTER_URL"]?>">
                                                    <?echo GetMessage("CT_BCSF_FILTER_SHOW")?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="bxajaxid" value="smart_filter" />
                            </form>
                        </div>
                    </div>
                    <script type="text/javascript">
                        //<!--

                        allHidden = <?=json_encode($allHidden,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);?>;

                        function recheckOptionsCompareHeight(){

                            var wrapperHeight = 0;
                            var checkboxIterator = 0;

                            var cDataId = $(this).attr("data-id");

                            $('#open'+cDataId).attr("data-height", "");
                            $('#wrapper'+cDataId).css({height: "auto", overflow: "visible"});
                            $('#open'+cDataId).addClass('display-none');
                            $('#open'+cDataId).unbind("click");

                            $('#wrapper'+cDataId+' > div').each(function(){

                                if($(this).height() > 0
                                    && checkboxIterator < maxCheckCount){

                                    ++checkboxIterator;
                                    wrapperHeight += $(this).outerHeight();

                                };

                            });

                            if(wrapperHeight > 0
                                && checkboxIterator >= maxCheckCount){

                                $('#open'+cDataId).removeClass('display-none');
                                $('#wrapper'+cDataId).css({height: wrapperHeight + "px", overflow: "hidden"});
                                $('#open'+cDataId).attr("data-height", wrapperHeight);

                                $('#open'+cDataId).click(function(){

                                    var cDataId = $(this).attr("data-id");
                                    var cDataHeight = $(this).attr("data-height");


                                    if($('#wrapper'+cDataId).height() == cDataHeight){
                                        $('#wrapper'+cDataId).css({height: "auto", overflow: "visible"});
                                        $(this).removeClass("collapsed");
                                    } else {
                                        $('#wrapper'+cDataId).css({height: wrapperHeight + "px", overflow: "hidden"});
                                        $(this).addClass("collapsed");
                                    };

                                });

                            };

                        }

                        function changeCompareOptionsHeight(){

                            if($(window).width() < 992){

                                maxCheckCount = 3;

                            } else {

                                <? if($elementsCount > 0): ?>
                                maxCheckCount = <?=$elementsCount;?>;
                                <? else: ?>
                                maxCheckCount = 0;
                                <? endif; ?>

                            }

                            $('.checkbox-wrapper').each(recheckOptionsCompareHeight);

                        }

                        function hideOptionsCompare(){

                            changeCompareOptionsHeight();
                            $(window).bind('resize',changeCompareOptionsHeight);
                        };

                        if (typeof window.frameCacheVars != 'undefined') {
                            BX.addCustomEvent("onFrameDataReceived", function(){

                                smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);

                                hideOptionsCompare();

                            });
                        } else {
                            $(function(){

                                smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
                                hideOptionsCompare();

                            });
                        };



                        //-->
                    </script>
                    <? $frame->end(); ?>
                </div>
                <button type="button" class="close hidden" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>
    </div>
</div>