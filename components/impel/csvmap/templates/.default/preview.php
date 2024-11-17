<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if(is_array($arResult['aSheetData']) && sizeof($arResult['aSheetData'])): ?>
    <div class="table-responsive text-center">
        <p class="h3"><?php echo GetMessage('TMPL_GET_PREVIEW'); ?></p>
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <?php for($iCount = 0; $iCount < sizeof($arResult['aColumnsNum']); $iCount++): ?>
                    <th>
                        <select class="form-control colums" name="order_columns[<?php echo $arResult['aColumnsNum'][$iCount]; ?>]" onchange="return disableSelect();">
                            <?php foreach($arResult['aColumnsList'] as $soText => $soValue): ?>
                                <option<?php if(isset($arResult['order_columns']) && isset($arResult['order_columns'][$arResult['aColumnsNum'][$iCount]]) && $arResult['order_columns'][$arResult['aColumnsNum'][$iCount]] == $soValue): ?> selected="selected"<?php endif; ?> value="<?php echo $soValue; ?>"><?php echo $soText; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                <?php endfor; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($arResult['aSheetData'] as $irNum => $iRow): ?>
                <tr>
                    <td><?php echo $irNum; ?></td>
                    <?php foreach($iRow as $icNum => $iColumn): ?>
                        <td><?php echo $iColumn; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button id="process" class="btn btn-info" onclick="return getprocess(this,event);"><?=GetMessage('TMPL_PROFILE_START_BTN');?></button>
    </div>
    <div id="process_result" class="alert alert-warning hidden"></div>
    <script>
        disableSelect();
    </script>
<?php endif; ?>