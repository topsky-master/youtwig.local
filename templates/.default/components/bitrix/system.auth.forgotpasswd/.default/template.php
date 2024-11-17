<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<div class="bx-auth-reg">
<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?
if (mb_strlen($arResult["BACKURL"]) > 0)
{
?>
    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
}
?>
    <input type="hidden" name="AUTH_FORM" value="Y">
    <input type="hidden" name="TYPE" value="SEND_PWD">
    <p>
    <?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
    </p>

<table class="data-table bx-forgotpass-table">
    <thead>
        <tr>
            <td colspan="2"><b><?=GetMessage("AUTH_GET_CHECK_STRING")?></b></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?=GetMessage("AUTH_LOGIN")?></td>
            <td><input type="text" class="form-control" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
        </tr>
        <tr >
            <td colspan="2" align="center"><?=GetMessage("AUTH_OR")?></td>
        </tr>
        <tr>
            <td><?=GetMessage("AUTH_EMAIL")?></td>
            <td><input type="text" class="form-control" name="USER_EMAIL" maxlength="255" /></td>
        </tr>
    </tbody>

</table>
<br>
<input type="submit" class="btn btn-info btn-block login" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>"</input>

<br>
<p>
<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
</p>
</form>
</div>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
