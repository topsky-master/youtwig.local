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
            <input class="bx-auth-input" type="text" name="USER_LOGIN" placeholder="<?=GetMessage("AUTH_LOGIN")?>" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
            <input class="bx-auth-input" type="password" placeholder="<?=GetMessage("AUTH_PASSWORD")?>" name="USER_PASSWORD" maxlength="255" />
            <?php if ($arResult["STORE_PASSWORD"] == "Y"):?>
                <div class="row remember">
                    <input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
                    <label for="USER_REMEMBER">
                        <?=GetMessage("AUTH_REMEMBER_ME")?>
                    </label>
                </div>
            <?php endif?>

            <?php if($arResult["CAPTCHA_CODE"]):?>
                <div class="row captcha">
                    <input type="hidden" name="captcha_sid" value="<?php echo $arResult["CAPTCHA_CODE"]?>" />
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?php echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                    <input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" placeholder="<?php echo GetMessage("AUTH_CAPTCHA_PROMT")?>" size="15" />
                </div>
            <?php endif;?>

            <button class="btn btn-info btn-block login" name="Login" type="button" onclick="location.href = '/registration/';">
                <?=GetMessage("AUTH_REGISTER")?>
            </button>

            <button class="btn btn-info btn-block login" name="Login" type="submit">
                <?=GetMessage("AUTH_AUTHORIZE")?>
            </button>
            <div class="row remember">
                <a class="" href="/auth/?forgot_password=yes">
                    <?=GetMessage("AUTH_FORGOT_PASSWORD_2")?>
                </a>
            </div>
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