<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
?>
<div class="bx-auth-reg">

    <?if($USER->IsAuthorized()):?>

        <p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

    <?else:?>
        <?
        if (count($arResult["ERRORS"]) > 0):
            foreach ($arResult["ERRORS"] as $key => $error)
                if (intval($key) == 0 && $key !== 0)
                    $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

            ShowError(implode("<br />", $arResult["ERRORS"]));

        elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
            ?>
            <p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
        <?endif?>

        <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
            <?
            if($arResult["BACKURL"] <> ''):
                ?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?
            endif;
            ?>

            <table>
                <thead>
                <tr>
                    <td colspan="2"><b><?=GetMessage("AUTH_REGISTER")?></b></td>
                </tr>
                </thead>
                <tbody>
                <?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
                    <?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true):?>
                        <tr>
                            <td><?echo GetMessage("main_profile_time_zones_auto")?><?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?></td>
                            <td>
                                <select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')" class="select-field selectpicker">
                                    <option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
                                    <option value="Y"<?=$arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
                                    <option value="N"<?=$arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?echo GetMessage("main_profile_time_zones_zones")?></td>
                            <td>
                                <select name="REGISTER[TIME_ZONE]"<?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?> class="select-field selectpicker">
                                    <?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
                                        <option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
                                    <?endforeach?>
                                </select>
                            </td>
                        </tr>
                    <?else:?>
                        <tr>
                            <td><?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?></td>
                            <td><?
                                switch ($FIELD)
                                {
                                    case "PASSWORD":
                                        ?><input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="form-control" />
                                    <?if($arResult["SECURE_AUTH"]):?>
                                        <span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
                    <div class="bx-auth-secure-icon"></div>
                </span>
                                        <noscript>
                <span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
                    <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                </span>
                                        </noscript>
                                        <script type="text/javascript">
                                            document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                        </script>
                                    <?endif?>
                                        <?
                                        break;
                                    case "CONFIRM_PASSWORD":
                                        ?><input size="30" class="form-control" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" /><?
                                        break;

                                    case "PERSONAL_GENDER":
                                        ?><select name="REGISTER[<?=$FIELD?>]" class="select-field selectpicker">
                                        <option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
                                        <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
                                        <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
                                        </select><?
                                        break;

                                    case "PERSONAL_COUNTRY":
                                    case "WORK_COUNTRY":
                                        ?><select name="REGISTER[<?=$FIELD?>]" class="select-field selectpicker"><?
                                        foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value)
                                        {
                                            ?><option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                            <?
                                        }
                                        ?></select><?
                                        break;

                                    case "PERSONAL_PHOTO":
                                    case "WORK_LOGO":
                                        ?><input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" /><?
                                        break;

                                    case "PERSONAL_NOTES":
                                    case "WORK_NOTES":
                                        ?><textarea class="form-control" cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea><?
                                        break;
                                    default:
                                        if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
                                        ?><input size="30" class="form-control" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
                                        if ($FIELD == "PERSONAL_BIRTHDAY")
                                            $APPLICATION->IncludeComponent(
                                                'bitrix:main.calendar',
                                                '',
                                                array(
                                                    'SHOW_INPUT' => 'N',
                                                    'FORM_NAME' => 'regform',
                                                    'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                    'SHOW_TIME' => 'N'
                                                ),
                                                null,
                                                array("HIDE_ICONS"=>"Y")
                                            );
                                        ?><?
                                }?></td>
                        </tr>
                    <?endif?>
                <?endforeach?>
                <?// ********************* User properties ***************************************************?>
                <?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
                    <tr><td colspan="2"><?=mb_strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></td></tr>
                    <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
                        <tr><td><?=$arUserField["EDIT_FORM_LABEL"]?>:<?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?></td><td>
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                    array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
                    <?endforeach;?>
                <?endif;?>
                <?// ******************** /User properties ***************************************************?>
                <?
                /* CAPTCHA */
                if ($arResult["USE_CAPTCHA"] == "Y")
                {
                    ?>
                    <tr>
                        <td colspan="2"><b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                        </td>
                    </tr>
                    <tr>
                        <td><?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:<span class="starrequired">*</span></td>
                        <td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
                    </tr>
                    <?
                }
                /* !CAPTCHA */
                ?>
                <tr>
                    <td colspan="2">
                        <div>
                            <?=GetMessage("REGISTER_CAPTCHA_TITLE")?>
                            <span class="required">*</span>
                        </div>
                        <script src='https://www.google.com/recaptcha/api.js'></script>
                        <div class="g-recaptcha" data-sitekey="6LeFVAcTAAAAAC_gnLQ20Xdk9mplMRumVZhvVNEc"></div>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" class="btn btn-success" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?

                        $consent_processing_link = COption::GetOptionString("my.stat", "consent_processing_link", "");
                        $consent_processing_text = GetMessage('SOA_CONSENT_PROCESSING_LINK');

                        if(!empty($consent_processing_link)){
                            $consent_processing_text = str_ireplace('href="#"','href="'.$consent_processing_link.'"',$consent_processing_text);
                        } else {
                            $consent_processing_text = strip_tags($consent_processing_text);
                        }


                        ?>
                        <p class="consent-processing"><?=$consent_processing_text;?></p>
                    </td>
                </tr>
                </tfoot>
            </table>
            <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
            <p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>
        </form>
        
             <script>

                $('head').append('<script'+' type="text/javascript" src="/local/templates/nmain/js/jquery.maskedinput.min.js"></'+'script>');

                var maskOptions =  {

                    onComplete: function(cep) {
                        if (!$('.login.send').hasClass('try')) {
                            $('.login.send').attr('disabled',false).prop('disabled',false);
                        }
                    },

                    onKeyPress: function(cep, event, currentField, options){

                        let spValue = $(currentField).val();
                        spValue = spValue.replace(/^[\w\+]+$/gi,'');

                        if (spValue[0] == '9' || spValue[0] == '8') {
                            spValue = '7' + spValue;
                            $(currentField).val(spValue);
                        }

                        if($(currentField).get(0)
                            && $(currentField).val().indexOf('+78') === 0){
                            $(currentField).val($(currentField).val().replace('+78','+79'));
                        }

                        $(currentField).trigger('change');

                    }

                };



                let phonePlaceholder = '+79_________';
                let phoneMask = '+79000000000';

                $('[name="REGISTER[PHONE_NUMBER]"]').attr('autocomplete',false);
                $('[name="REGISTER[PHONE_NUMBER]"]').attr('placeholder', phonePlaceholder);
                $('[name="REGISTER[PHONE_NUMBER]"]').mask(phoneMask,maskOptions);

                $('.bx-auth-input').on('change',function(){
                    let smsCode = $.trim($(this).val());
                    if (smsCode.length > 3) {
                        $('.login.confirm').attr('disabled',false).prop('disabled',false);
                    }
                });

                <?php if ($try): ?>

                var tryTimer = <?=($try - time()); ?> * 1000;

                var enableBtnInt = setInterval(function(){

                    tryTimer -= 1000;

                    if (tryTimer <= 0) {
                        clearInterval(enableBtnInt);
                        $('.login.send').attr('disabled',false).prop('disabled',false).html('<?=GetMessage("SEND_SMS")?>');

                    } else {
                        $('.login.send span').html(Math.ceil(tryTimer/1000));
                    }

                },1000);

                $('.bx-auth-input').on('change keyup',function(){
                    let smsCode = $.trim($(this).val());
                    if (smsCode.length > 3) {
                        $('.login.confirm').attr('disabled',false).prop('disabled',false);
                    }
                });
                <?php endif; ?>

            </script>
    <?endif?>
</div>