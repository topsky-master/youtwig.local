<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$utime = microtime(true);
?>
<form class="path form" method="POST" data-id="<?=$utime;?>">
	<input type="hidden" name="<?=$utime;?>" value="<?=$utime;?>" />
	<div class="colums-form">
        <div class="form-group">
            <label for="manufacturer">
                <?php echo GetMessage('TMPL_MANUFACTURER_LIST'); ?>
            </label>
            <select id="manufacturer" name="manufacturer" class="form-control">
                <option value=""><?php echo GetMessage('TMPL_MANUFACTURER_LIST'); ?></option>
                <?php foreach($arResult['manufacturer_list'] as $sManufacturer): ?>
                    <option value="<?php echo $sManufacturer; ?>"<?php echo $sManufacturer == $arResult['manufacturer'] ? ' selected="selected"' : ''; ?>><?php echo $sManufacturer; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="type_of_product">
                <?php echo GetMessage('TMPL_TYPE_OF_PRODUCT_LIST'); ?>
            </label>
            <select id="type_of_product" name="type_of_product" class="form-control">
                <option value=""><?php echo GetMessage('TMPL_TYPE_OF_PRODUCT_LIST'); ?></option>
                <?php foreach($arResult['type_of_product_list'] as $sTypeofproduct): ?>
                    <option value="<?php echo $sTypeofproduct; ?>"<?php echo $sTypeofproduct == $arResult['type_of_product'] ? ' selected="selected"' : ''; ?>><?php echo $sTypeofproduct; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="product">
                <?php echo GetMessage('TMPL_PRODUCT'); ?>
            </label>
            <input id="product" class="form-control" name="product" type="text" value="<?php echo !empty($arResult['product']) ? $arResult['product'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="skip_strings">
                <?php echo GetMessage('TMPL_SKIP_STRINGS'); ?>
            </label>
            <input id="skip_strings" class="form-control" name="skip_strings" type="number" value="<?php echo !empty($arResult['skip_strings']) ? $arResult['skip_strings'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="encoding">
                <?php echo GetMessage('TMPL_ENCODING_LIST'); ?>
            </label>
            <select id="encoding" name="encoding" class="form-control">
                <option value=""><?php echo GetMessage('TMPL_ENCODING_LIST'); ?></option>
                <?php foreach($arResult['encoding_list'] as $sEncoding): ?>
                    <option value="<?php echo $sEncoding; ?>" <?php echo $sEncoding == $arResult['encoding'] ? ' checked="checked"' : ''; ?>><?php echo $sEncoding; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="check_manufacturer">
				<input id="check_manufacturer" name="check_manufacturer" type="checkbox"<?php if (isset($arResult['check_manufacturer']) && !empty($arResult['check_manufacturer'])) { ?> checked="checked"<?php } ?> />
                <?php echo GetMessage('TMPL_CHECK_MANUFACTURER'); ?>
            </label>
        </div>
        <div class="form-group">
            <label for="delimiter">
                <?php echo GetMessage('TMPL_DELIMITER'); ?>
            </label>
            <input id="delimiter" class="form-control" name="delimiter" type="text" value="<?php echo !empty($arResult['delimiter']) ? htmlspecialcharsbx($arResult['delimiter']) : ''; ?>" />
        </div>
        <div class="form-group profile_save">
            <label for="profile_save">
                <?php echo GetMessage('TMPL_PROFILE_SAVE'); ?>
            </label>
            <input id="profile_save" class="form-control" name="profile_save" type="text" value="<?php echo !empty($arResult['PROFILE_SAVE']) ? htmlspecialcharsbx($arResult['PROFILE_SAVE']) : ''; ?>" />
            <button id="profile_save_btn" onclick="return saveprofile(this,event);" type="submit" class="btn btn-default"><?php echo GetMessage('TMPL_PROFILE_SAVE_BTN'); ?></button>
        </div>
        <?php if(isset($arResult['saved_profiles']) && sizeof($arResult['saved_profiles']) > 0): ?>
            <div class="form-group profile_load">
                <label for="profile_load">
                    <?php echo GetMessage('TMPL_PROFILE_LOAD'); ?>
                </label>
                <select id="profile_load" class="form-control" name="profile_load">
                    <option value=""><?php echo GetMessage("TMPL_PROFILE_LOAD"); ?></option>
                    <?php foreach($arResult['saved_profiles'] as $sProfileName): ?>
                        <option value="<?php echo $sProfileName; ?>"<?php if(isset($arResult['SAVED_PROFILE']) && ($arResult['SAVED_PROFILE'] == $sProfileName)): ?> selected="selected"<?php endif; ?>><?php echo $sProfileName; ?></option>
                    <?php endforeach; ?>
                </select>
                <button id="profile_load_btn" onclick="return loadprofile(this,event);" type="submit" class="btn btn-default"><?php echo GetMessage('TMPL_PROFILE_LOAD_BTN'); ?></button>
            </div>
        <?php endif; ?>
        <div class="form-group text-center">
            <button class="btn btn-info" id="profile_preview" onclick="return getpreview(this,event);">
                <?php echo GetMessage('TMPL_PREVIEW'); ?>
            </button>
        </div>
        <?php if(!empty($arResult['aerrors'])): ?>
            <?php foreach($arResult['aerrors'] as $sError): ?>
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <div><?php echo $sError; ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if(!empty($arResult['amessages'])): ?>
            <?php foreach($arResult['amessages'] as $sInfo): ?>
                <div class="alert alert-info alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <div><?php echo $sInfo; ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="preview-result hidden"></div>
        <input type="hidden" id="json_order_columns" name="json_order_columns" value="<?php echo isset($arResult['order_columns']) ? htmlspecialcharsbx((string)$arResult['order_columns']) : '';?>" />
        <input type="hidden" id="preview-action" name="action" value="preview" />
        <input type="hidden" name="path" value="<?php echo htmlspecialcharsbx($arResult['path']); ?>" />
    </div>
</form>
<?php if($arResult['load_preview']): ?>
    <script>
        $('#profile_preview').trigger('click');
    </script>
<?php endif; ?>
