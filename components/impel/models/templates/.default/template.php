<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$areaId = isset($arParams["AREA_ID"]) && !empty($arParams["AREA_ID"]) ? trim($arParams["AREA_ID"]) : '';
$modelId = "bx_model_list".$areaId;
?>
<div id="<?=$modelId;?>">
    <?

    $type_of_product = $arResult['TYPE_OF_PRODUCT'];

    $block_title = isset($arParams['BLOCK_TITLE'])
    && !empty($arParams['BLOCK_TITLE'])
        ? trim($arParams['BLOCK_TITLE'])
        : '';

    $block_columns = isset($arParams["BLOCK_COLUMNS"])
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

    ?>
    <div class="choose-area">
        <? if(!empty($block_title)){?>
            <div class="row block-title text-center">
            	<? echo $block_title; ?>
            </div>
        <?}?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
                <select id="type_of_product" class="selectpicker" data-live-search="true" data-container="body">
                    <option value="" selected="true"><?=GetMessage('CT_BNL_ELEMENT_CHOOSE_BRAND');?></option>
                    <?php foreach($type_of_product as $value => $text): ?>
                    <option value="<?=$value;?>"><?=$text;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
                <select id="manufacturer" data-title="<?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER');?>" class="selectpicker" data-live-search="true" data-container="body"></select>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
                <input id="model_new" class="form-control" disabled="disabled" data-no-results="<?=GetMessage('CT_BNL_ELEMENT_NO_RESULTS');?>" placeholder="<?=GetMessage('CT_BNL_ELEMENT_CHOOSE_MODEL');?>" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-<?=$block_columns_class;?>">
                <button class="btn btn-success" id="redirect" disabled="true">
                    <i class="fa fa-sliders" aria-hidden="true">
                    </i>
                    <?=GetMessage('CT_BNL_ELEMENT_CHOOSE_REDIRECT');?>
                </button>
            </div>
        </div>
        <input type="hidden" id="redirectwhere" value="" />
    </div>
</div>