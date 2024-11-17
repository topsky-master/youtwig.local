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


$cartId = "bx_model_list";

?>
<div id="<?=$cartId;?>">
<?

$frame = $this->createFrame($cartId, false)->begin('<i class="fa fa-spinner faa-spin animated"></i>'.GetMessage('CT_BNL_ELEMENT__LOAD_LIST'));

$model_new = array();
$manufacturer = array();
$type_of_product = array();

$params = Array(
    "max_len" => "200",
    "change_case" => "L",
    "replace_space" => "_",
    "replace_other" => "_",
    "delete_repeat_replace" => false
);

$block_columns  =isset($arParams["BLOCK_COLUMNS"])
                &&!empty($arParams["BLOCK_COLUMNS"])
                ? (int)$arParams["BLOCK_COLUMNS"]
                : 4;

$block_columns_class = 3;

switch($block_columns){
    case 1:
    case 2:
    case 3:
    case 4:

        $block_columns_class = 12 / $block_columns;

        break;
    default:

        $block_columns_class = 3;

        break;
}


foreach($arResult["ITEMS"] as $arItem):

    if(isset($arItem['DISPLAY_PROPERTIES'])){

        if(isset($arItem['DISPLAY_PROPERTIES']['type_of_product'])
            && isset($arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE'])
            && !empty($arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE'])
        ){


                $arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE'] = trim($arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']);

                if(!empty($arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE'])){

                    if(!isset($type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']])){
                        $type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']] = array();
                    };

                };

        };

        if(isset($arItem['DISPLAY_PROPERTIES']['manufacturer'])
            && isset($arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE'])
            && !empty($arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE'])
        ){

                $arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE'] = trim($arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE']);

                if(!empty($arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE'])){

                    if(!isset($type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']][$arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE']])){
                        $type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']][$arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE']] = array();
                    };

                }

        }

        if(isset($arItem['DISPLAY_PROPERTIES']['model_new'])
            && isset($arItem['DISPLAY_PROPERTIES']['model_new']['VALUE'])
            && !empty($arItem['DISPLAY_PROPERTIES']['model_new']['VALUE'])
        ){

                $arItem['DISPLAY_PROPERTIES']['model_new']['VALUE'] = trim($arItem['DISPLAY_PROPERTIES']['model_new']['VALUE']);

                if(!empty($arItem['DISPLAY_PROPERTIES']['model_new']['VALUE'])){

                    if(!isset($type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']][$arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE']][$arItem['DISPLAY_PROPERTIES']['model_new']['VALUE']]))
                    $type_of_product[$arItem['DISPLAY_PROPERTIES']['type_of_product']['VALUE']][$arItem['DISPLAY_PROPERTIES']['manufacturer']['VALUE']][$arItem['DISPLAY_PROPERTIES']['model_new']['VALUE']] = $arItem["DETAIL_PAGE_URL"];

                }

        }

    }

endforeach;


if(!empty($type_of_product)){

ksort($type_of_product,SORT_NATURAL);

foreach($type_of_product as $type => $manufacturer){
    ksort($type_of_product[$type],SORT_NATURAL);
}

foreach($type_of_product as $type => $manufacturers){
    foreach($manufacturers as $manufacturer => $model){
        ksort($type_of_product[$type][$manufacturer],SORT_NATURAL);
    }
}

$block_title = isset($arParams['BLOCK_TITLE'])
            && !empty($arParams['BLOCK_TITLE'])
            ? trim($arParams['BLOCK_TITLE'])
            : '';

?>
<div class="choose-area">
<? if(!empty($block_title)){?>
    <div class="row block-title text-center">
        <h2>
            <? echo $block_title; ?>
        </h2>
    </div>
<?}?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
            <select id="type_of_product" class="selectpicker" data-live-search="true" data-container="body"></select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
            <select id="manufacturer" class="selectpicker" data-live-search="true" data-container="body"></select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
            <select id="model_new" class="selectpicker" data-live-search="true" data-container="body"></select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
            <button class="btn btn-success" id="redirect" disabled="true">
                <i class="fa fa-sliders" aria-hidden="true">
                </i>
                <?=GetMessage('CT_BNL_ELEMENT_CHOOSE_REDIRECT');?>
            </button>
        </div>
    </div>
</div>
<?php

}

$frame->end();

?>
</div>
<script type="text/javascript">
//<!--
    type_of_product = <? echo json_encode($type_of_product,JSON_UNESCAPED_UNICODE);?>;

    function whatToChooseCreate(){

        $('#type_of_product').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_BRAND');?></option>');

        for(var type in type_of_product){

            $('#type_of_product').append('<option value="'+type+'">'+type+'</option>');

        };

        $('#manufacturer').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER');?></option>');
        $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');

        $('#type_of_product').change(function(){
            var tIndex = $('option:selected','#type_of_product').val();

            if(typeof type_of_product[tIndex] != "undefined" && type_of_product[tIndex]){
                $('#manufacturer').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER');?></option>');
                $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');

                for(manufacturer in type_of_product[tIndex]){
                    $('#manufacturer').append('<option value="'+manufacturer+'">'+manufacturer+'</option>');
                }
            };

            if(tIndex == ""){
                $('#manufacturer').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER');?></option>');
                $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');
            };


            $('#manufacturer').selectpicker('render');
            $('#model_new').selectpicker('render');

            $('#manufacturer').selectpicker('refresh');
            $('#model_new').selectpicker('refresh');

        });

        $('#manufacturer').change(function(){

            var tIndex = $('option:selected','#type_of_product').val();
            var mIndex = $('option:selected','#manufacturer').val();

            if(typeof type_of_product[tIndex] != "undefined" && type_of_product[tIndex]
            && typeof type_of_product[tIndex][mIndex] != "undefined" && type_of_product[tIndex][mIndex]
            ){
                $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');
                for(model_new in type_of_product[tIndex][mIndex]){
                    $('#model_new').append('<option value="'+type_of_product[tIndex][mIndex][model_new]+'">'+model_new+'</option>');
                }
            };

            if(tIndex == ""){
                $('#manufacturer').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER');?></option>');
                $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');
            };

            if(mIndex == ""){
                $('#model_new').html('<option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?></option>');
            };

            $('#manufacturer').selectpicker('render');
            $('#model_new').selectpicker('render');

            $('#manufacturer').selectpicker('refresh');
            $('#model_new').selectpicker('refresh');

        });

        $('#model_new').change(function(){

            var mdIndex =  $('option:selected',this).val();

            if(mdIndex != ""){
                $("#redirect").prop("disabled",false);
            } else {
                $("#redirect").prop("disabled",true);
            };

        });

        $("#redirect").click(function(){
            if(!this.disabled){
                var mdIndex =  $('option:selected','#model_new').val();
                if(mdIndex != ""){
                    location.href = mdIndex;
                };
            };
        });

    };

    if (typeof window.frameCacheVars != 'undefined') {
        BX.addCustomEvent("onFrameDataReceived", function(){
            //$('.selectpicker','#<?=$cartId;?>').selectpicker();
            whatToChooseCreate();
        });
    } else {
        $(function(){

            whatToChooseCreate();

        });
    };

//-->
</script>