<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$modelId = "bx_instruction_filter_list";

$type_of_product = $arResult['TYPE_OF_PRODUCT'];
$manufacturer = $arResult["MANUFACTURER"];

$select_type_of_product = isset($arParams['TYPE_OF_PRODUCT'])
&&!empty($arParams['TYPE_OF_PRODUCT'])
    ? $arParams['TYPE_OF_PRODUCT']
    : '';

$select_manufacturer = isset($arParams['MANUFACTURER'])
&&!empty($arParams['MANUFACTURER'])
    ? $arParams['MANUFACTURER']
    : '';

$block_title = isset($arParams['BLOCK_TITLE'])
&& !empty($arParams['BLOCK_TITLE'])
    ? trim($arParams['BLOCK_TITLE'])
    : '';

?>
<div id="<?=$modelId;?>">
    <?

    $frame = $this->createFrame($modelId, false)->begin('<i class="fa fa-spinner faa-spin animated"></i>'.GetMessage('CT_BNL_ELEMENT__LOAD_LIST'));

    ?>
    <div class="choose-area" id="choose-area">
        <form action="<?=$APPLICATION->GetCurPageParam("",array("TYPE_OF_PRODUCT","MANUFACTURER"));?>">
            <? if(!empty($block_title)){?>
                <div class="row block-title text-center">
                    <h2>
                        <? echo $block_title; ?>
                    </h2>
                </div>
            <? } ?>
            <div class="row choose-area-selects">
                <div class="col-xs-12 col-sm-12 col-md-3 hidden-xs hidden-sm">
                    <label for="type_of_product">
                        <?php echo GetMessage('CT_BNL_ELEMENT_CHOOSE_BRAND'); ?>
                        <label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3">
                    <select id="type_of_product" name="TYPE_OF_PRODUCT" class="selectpicker" data-live-search="true" data-container="body">
                        <option value="">
                            <?php echo GetMessage('CT_BNL_ELEMENT_CHOOSE_BRAND'); ?>
                        </option>
                        <?php foreach($type_of_product as $type_id => $type_value){ ?>
                            <option<?php if(!empty($select_type_of_product) && $select_type_of_product == $type_id){ ?> selected="selected"<?php }?> value="<?=$type_id;?>">
                                <?=$type_value;?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 hidden-xs hidden-sm">
                    <label for="manufacturer">
                        <?php echo GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER'); ?>
                    </label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3">
                    <select id="manufacturer" name="MANUFACTURER" class="selectpicker" data-live-search="true" data-container="body">
                        <option value="">
                            <?php echo GetMessage('CT_BNL_ELEMENT_CHOOSE_MANUFACTURER'); ?>
                        </option>
                        <?php foreach($manufacturer as $manufacturer_id => $manufacturer_value){ ?>
                            <option<?php if(!empty($select_manufacturer) && $select_manufacturer == $manufacturer_id){ ?> selected="selected"<?php }?> value="<?=$manufacturer_id;?>">
                                <?=$manufacturer_value;?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <?
    $frame->end();
    ?>
</div>
<script type="text/javascript">
    //<!--

    function submitSelectForm(){
        $('select.selectpicker','#choose-area').change(function(){
            var rURI = $(this).parents('form').eq(0).attr('action');
            rURI = rURI.indexOf('?') == -1 ? rURI + '?' : rURI;
            rURI += '&TYPE_OF_PRODUCT=' + encodeURIComponent($('#type_of_product').val());
            rURI += '&MANUFACTURER=' + encodeURIComponent($('#manufacturer').val());
            location.href = rURI;
        });
    };

    if (typeof window.frameCacheVars != 'undefined') {
        BX.addCustomEvent("onFrameDataReceived", function(){
            $('.selectpicker','#choose-area').selectpicker();
            submitSelectForm();
        });
    } else {
        $(function(){
            submitSelectForm();
        });
    };
    //-->
</script>