<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$utime = microtime(true);

?>
<form class="form-replaces" id="form-replaces" method="post" data-id="<?=$utime;?>">
<input type="hidden" name="<?=$utime;?>" value="<?=$utime;?>" />
<p class="h3"><?php echo GetMessage("TMPL_REPLACES"); ?></p>
<?php if(!empty($arResult['aerrors'])): ?>
    <?php foreach($arResult['aerrors'] as $error): ?>
        <div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <div><?php echo $error; ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
    <ul class="replaces">
        <?php if(isset($arResult['replaces']) && !empty($arResult['replaces'])): ?>
            <?php foreach($arResult['replaces']['from'] as $iNum => $sReplace): ?>
                <li>
                    <input type="text" name="replaces[from][]" value="<?php echo htmlspecialcharsbx($arResult['replaces']['from'][$iNum]); ?>" placeholder="<?php echo GetMessage('TMPL_REPLACES_PLACEHOLDER_FROM');?>" class="form-control" />
                    <input type="text" name="replaces[to][]" value="<?php echo htmlspecialcharsbx($arResult['replaces']['to'][$iNum]); ?>" placeholder="<?php echo GetMessage('TMPL_REPLACES_PLACEHOLDER_TO');?>" class="form-control" />
                    <button name="remove-replace" class="btn btn-danber" onclick="$(this).parent().remove(); return false;">
                        <?php echo GetMessage('TMPL_REPLACES_REMOVE'); ?>
                    </button>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li>
            <button name="add-replace" value="1" class="btn btn-default" onclick="cloneReplaces(event);">
                <?php echo GetMessage('TMPL_REPLACES_ADD'); ?>
            </button>
        </li>
        <li>
            <button name="save-replace" value="1" class="btn btn-info" onclick="saveReplaces(event,this);">
                <?php echo GetMessage('TMPL_REPLACES_SAVE'); ?>
            </button>
        </li>
    </ul>
    <input type="hidden" name="action" value="replaces_save" />
</form>
