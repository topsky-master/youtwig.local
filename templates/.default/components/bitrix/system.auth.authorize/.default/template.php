<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
/**
 * @var $arParams = [];
 * @var $arResult = [];
 *
 **/
?>
<div class="login-container">
    <div id="output">
        <?php
        $auth_result = "";
        ob_start();
        ShowMessage($arParams["~AUTH_RESULT"]);
        $auth_result = ob_get_clean();
        $auth_result = trim($auth_result);
        if(!empty($auth_result)):
            ?>
            <div id="output" class="animated fadeInUp alert alert-danger">
                <?php  echo $auth_result; ?>
            </div>
        <?php
        endif;

        $auth_result = "";
        ob_start();
        ShowMessage($arResult['ERROR_MESSAGE']);
        $auth_result = ob_get_clean();
        $auth_result = trim($auth_result);
        if(!empty($auth_result)):
            ?>
            <div id="output" class="animated fadeInUp alert alert-danger">
                <?php  echo $auth_result; ?>
            </div>
        <?php
        endif;
        ?>
    </div>
    <div class="form-box">
        <form class="well well-lg" name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
            <input type="hidden" name="AUTH_FORM" value="Y" />
            <input type="hidden" name="TYPE" value="AUTH" />
            <?php if (mb_strlen($arResult["BACKURL"]) > 0):?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <?php endif?>
            <?php foreach ($arResult["POST"] as $key => $value):?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
            <?php endforeach?>
            <?php $try = isset($_SESSION['sms_try']) ? $_SESSION['sms_try'] : false; ?>
            <?php if (!$try) { unset($_SESSION['sms_try']); } ?>
            <?php $USER_PHONE = $_SESSION["USER_PHONE"] ?? ''; ?>
            <input class="bx-auth-input" type="text" name="USER_PHONE" placeholder="<?=GetMessage("AUTH_PHONE")?>" maxlength="255" value="<?=$USER_PHONE;?>" />
            <?php if (!empty($USER_PHONE)): ?>
                <input class="bx-auth-input" type="text" placeholder="<?=GetMessage("AUTH_CONFIRM")?>" name="USER_CONFIRM" maxlength="255" />
            <?php endif; ?>
            <?php if($arResult["CAPTCHA_CODE"]):?>
                <div class="row captcha">
                    <input type="hidden" name="captcha_sid" value="<?php echo $arResult["CAPTCHA_CODE"]?>" />
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?php echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                    <input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" placeholder="<?php echo GetMessage("AUTH_CAPTCHA_PROMT")?>" size="15" />
                </div>
            <?php endif;?>
            <button disabled="disabled" class="login send <?php if ($try): ?> try<?php endif; ?>" name="Login" value="send" type="submit" onClick="yaCounter21503785.reachGoal('otpravil-sms');">
                <?php if (!$try): ?> <?=GetMessage("SEND_SMS")?> <?php else: ?> <?=GetMessage("TRY_SMS")?> <span class="counter"></span> <?=GetMessage('TRY_SMS_SECONDS');?><?php endif; ?>
            </button>
            <?php if (!empty($USER_PHONE)): ?>
                <button class="btn btn-info btn-block login confirm" disabled="disabled" name="Login" value="confirm" type="submit" onClick="yaCounter21503785.reachGoal('podtverdili-kod');">
                    <?=GetMessage("CONFIRM_SMS")?>
                </button>
            <?php endif;?>
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

                $('[name="USER_PHONE"]').attr('autocomplete',false);
                $('[name="USER_PHONE"]').attr('placeholder', phonePlaceholder);
                $('[name="USER_PHONE"]').mask(phoneMask,maskOptions);

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
        </form>
    </div>
</div>
<script type="text/javascript">
    //<!--
    <?php if (mb_strlen($arResult["LAST_LOGIN"])>0):?>
    try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
    <?php else:?>
    try{document.form_auth.USER_LOGIN.focus();}catch(e){}
    <?php endif?>
    //-->
</script>