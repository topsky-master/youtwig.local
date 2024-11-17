<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$areaId = isset($arParams["AREA_ID"]) && !empty($arParams["AREA_ID"]) ? trim($arParams["AREA_ID"]) : '';
$modelId = "bx_model_slist".$areaId;
?>
<div id="<?=$modelId;?>" class="hidden-xs">
    <div class="choose-sarea">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-10">
                <input id="model-snew" class="form-control" data-no-results="<?=GetMessage('CT_BNL_ELEMENT_NO_SRESULTS');?>" placeholder="<?=GetMessage('CT_BNL_ELEMENT_CHOOSE_SMODEL');?>" />
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2">
                <button class="btn btn-success" id="sredirect" disabled="true">
                    <i class="fa fa-sliders" aria-hidden="true">
                    </i>
                    <?=GetMessage('CT_BNL_ELEMENT_CHOOSE_SREDIRECT');?>
                </button>
            </div>
        </div>
        <input type="hidden" id="sredirectwhere" value="" />
    </div>
</div>