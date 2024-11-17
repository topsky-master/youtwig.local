<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<form method="post" class="form-inline passcode" id="passcode">
    <?php if(isset($arResult['FASTAUTH_INFO'])
        && !empty($arResult['FASTAUTH_INFO'])){?>
        <div class="fastauth-info">
            <?=$arResult['FASTAUTH_INFO'];?>
        </div>
    <? } ?>
    <?=bitrix_sessid_post()?>
    <?php if(isset($arResult['error_msg'])
        && !empty($arResult['error_msg'])) { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">
                    ×
                </span>
            </button>
            <?=$arResult['error_msg'];?>
        </div>
    <? } ?>
    <?php if(isset($arResult['result_msg'])
        && !empty($arResult['result_msg'])) { ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">
                    ×
                </span>
            </button>
            <?=$arResult['result_msg'];?>
        </div>
    <? } ?>
    <? switch ($arResult['pass_action']){

        case 'is_authtorized':
            ?>
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
                <?=GetMessage('CT_BNL_ELEMENT_ERROR_ALREADY_AUTH');?>
            </div>
            <?

            break;

        case 'genpassword':

            ?>
            <div class="form-group">
                <input type="text" required="required" class="form-control"<?php if(isset($_REQUEST['pass_user'])): ?> value="<?=htmlspecialchars($_REQUEST['pass_user'], ENT_QUOTES,LANG_CHARSET);?>"<?php endif; ?> name="pass_user" placeholder="<?=GetMessage('CT_BNL_ELEMENT_PASS_USER');?>" />
                <input type="submit" class="btn btn-default" name="genpassword" value="<?=GetMessage('CT_BNL_ELEMENT_PASS_SUBMIT');?>" />
            </div>
            <?

            break;

        case 'checkpassword':

            ?>
            <div class="form-group">
                <input type="text" required="required" class="form-control"<?php if(isset($_REQUEST['pass_string'])): ?> value="<?=htmlspecialchars($_REQUEST['pass_string'], ENT_QUOTES,LANG_CHARSET);?>"<?php endif; ?> name="pass_string" placeholder="<?=GetMessage('CT_BNL_ELEMENT_PASS_STRING');?>" />
                <input type="submit" class="btn btn-default" name="checkpassword" value="<?=GetMessage('CT_BNL_ELEMENT_PASS_LOGIN');?>" />
            </div>
            <?

            break;

    } ?>
    <?php if(isset($arResult['need_reload'])){?>
        <input type="hidden" id="need_reload" value="true" />
    <? } ?>
    <input type="hidden" name="pass_action" value="<?=$arResult['pass_action'];?>" />
</form>