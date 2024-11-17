<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->AddHeadScript($this->GetFolder() . '/ru/script.js');
$chargeBackArray = Array();
$fieldOrder = $arParams["SET_SHOW_USER_FIELD"];
$required_fields = $arParams["REQUIRED_FIELDS"];
$required_fields = explode(',',$required_fields);
$required_fields = !empty($required_fields) && !is_array($required_fields)
                ? array($required_fields)
                : $required_fields;


?>
<div class="ticket add-support-ticket edit-support-ticket">

    <?

        if($arResult["ERROR_MESSAGE"] != ""):

        $arResult["ERROR_MESSAGE"] = str_ireplace(GetMessage('SUP_FORGOT_TITLE'),GetMessage('SUP_FORGOT_TITLE_M'),$arResult["ERROR_MESSAGE"]);
        $arResult["ERROR_MESSAGE"] = str_ireplace(GetMessage('SUP_FORGOT_MESSAGE'),GetMessage('SUP_FORGOT_MESSAGE_M'),$arResult["ERROR_MESSAGE"]);

    ?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?=ShowError($arResult["ERROR_MESSAGE"]);?>
    </div>
    <? endif; ?>

<?
/*$hkInst=CHotKeys::getInstance();
$arHK = array("B", "I", "U", "QUOTE", "CODE", "TRANSLIT");
foreach($arHK as $n => $s)
{
    $arExecs = $hkInst->GetCodeByClassName("TICKET_EDIT_$s");
    echo $hkInst->PrintJSExecs($arExecs);
}*/
?>
<?
if (!empty($arResult["TICKET"])):

    if (!empty($arResult["ONLINE"]))
    {
?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
    <?  $time = intval($arResult["OPTIONS"]["ONLINE_INTERVAL"]/60)." ".GetMessage("SUP_MIN");?>
        <?=str_replace("#TIME#",$time,GetMessage("SUP_USERS_ONLINE"));?>:<br />
        <?foreach($arResult["ONLINE"] as $arOnlineUser):?>
        <small>(<?=$arOnlineUser["USER_LOGIN"]?>) <?=$arOnlineUser["USER_NAME"]?> [<?=$arOnlineUser["TIMESTAMP_X"]?>]</small><br />
        <?endforeach?>
    </div>
<?
    }
?>
<table class="table-bordered table-hover table table-striped support-tickets">

    <tr>
        <th><?=GetMessage("SUP_TICKET")?></th>
    </tr>

    <tr>
        <td>

        <?=GetMessage("SUP_SOURCE")." / ".GetMessage("SUP_FROM")?>:

            <?if (mb_strlen($arResult["TICKET"]["SOURCE_NAME"])>0):?>
                [<?=$arResult["TICKET"]["SOURCE_NAME"]?>]
            <?else:?>
                [web]
            <?endif?>

            <?if (mb_strlen($arResult["TICKET"]["OWNER_SID"])>0):?>
                <?=$arResult["TICKET"]["OWNER_SID"]?>
            <?endif?>

            <?if (intval($arResult["TICKET"]["OWNER_USER_ID"])>0):?>
                [<?=$arResult["TICKET"]["OWNER_USER_ID"]?>]
                (<?=$arResult["TICKET"]["OWNER_LOGIN"]?>)
                <?=$arResult["TICKET"]["OWNER_NAME"]?>
            <?endif?>
        <br />


        <?=GetMessage("SUP_CREATE")?>: <?=$arResult["TICKET"]["DATE_CREATE"]?>

        <?if (mb_strlen($arResult["TICKET"]["CREATED_MODULE_NAME"])<=0 || $arResult["TICKET"]["CREATED_MODULE_NAME"]=="support"):?>
            [<?=$arResult["TICKET"]["CREATED_USER_ID"]?>]
            (<?=$arResult["TICKET"]["CREATED_LOGIN"]?>)
            <?=$arResult["TICKET"]["CREATED_NAME"]?>
        <?else:?>
            <?=$arResult["TICKET"]["CREATED_MODULE_NAME"]?>
        <?endif?>
        <br />


        <?if ($arResult["TICKET"]["DATE_CREATE"]!=$arResult["TICKET"]["TIMESTAMP_X"]):?>
                <?=GetMessage("SUP_TIMESTAMP")?>: <?=$arResult["TICKET"]["TIMESTAMP_X"]?>

                <?if (mb_strlen($arResult["TICKET"]["MODIFIED_MODULE_NAME"])<=0 || $arResult["TICKET"]["MODIFIED_MODULE_NAME"]=="support"):?>
                    [<?=$arResult["TICKET"]["MODIFIED_USER_ID"]?>]
                    (<?=$arResult["TICKET"]["MODIFIED_BY_LOGIN"]?>)
                    <?=$arResult["TICKET"]["MODIFIED_BY_NAME"]?>
                <?else:?>
                    <?=$arResult["TICKET"]["MODIFIED_MODULE_NAME"]?>
                <?endif?>

                <br />
        <?endif?>


        <? if (mb_strlen($arResult["TICKET"]["DATE_CLOSE"])>0): ?>
            <?=GetMessage("SUP_CLOSE")?>: <?=$arResult["TICKET"]["DATE_CLOSE"]?>
        <?endif?>


        <?if (mb_strlen($arResult["TICKET"]["STATUS_NAME"])>0) :?>
                <?=GetMessage("SUP_STATUS")?>: <span title="<?=$arResult["TICKET"]["STATUS_DESC"]?>"><?=$arResult["TICKET"]["STATUS_NAME"]?></span><br />
        <?endif;?>


        <?if (mb_strlen($arResult["TICKET"]["CATEGORY_NAME"]) > 0):?>
                <?=GetMessage("SUP_CATEGORY")?>: <span title="<?=$arResult["TICKET"]["CATEGORY_DESC"]?>"><?=$arResult["TICKET"]["CATEGORY_NAME"]?></span><br />
        <?endif?>


        <?if(mb_strlen($arResult["TICKET"]["CRITICALITY_NAME"])>0) :?>
                <?=GetMessage("SUP_CRITICALITY")?>: <span title="<?=$arResult["TICKET"]["CRITICALITY_DESC"]?>"><?=$arResult["TICKET"]["CRITICALITY_NAME"]?></span><br />
        <?endif?>


        <?if (intval($arResult["RESPONSIBLE_USER_ID"])>0):?>
            <?=GetMessage("SUP_RESPONSIBLE")?>: [<?=$arResult["TICKET"]["RESPONSIBLE_USER_ID"]?>]
            (<?=$arResult["TICKET"]["RESPONSIBLE_LOGIN"]?>) <?=$arResult["TICKET"]["RESPONSIBLE_NAME"]?><br />
        <?endif?>


        <?if (mb_strlen($arResult["TICKET"]["SLA_NAME"])>0) :?>
            <?=GetMessage("SUP_SLA")?>:
            <span title="<?=$arResult["TICKET"]["SLA_DESCRIPTION"]?>"><?=$arResult["TICKET"]["SLA_NAME"]?></span>
        <?endif?>


        </td>
    </tr>


    <tr>
        <th><?=GetMessage("SUP_DISCUSSION")?></th>
    </tr>


    <tr>
        <td>
    <?=$arResult["NAV_STRING"]?>

    <?foreach ($arResult["MESSAGES"] as $arMessage):?>
        <div class="ticket-edit-message">

        <div class="support-float-quote">[&nbsp;<a href="#postform" OnMouseDown="javascript:SupQuoteMessage('quotetd<? echo $arMessage["ID"]; ?>')" title="<?=GetMessage("SUP_QUOTE_LINK_DESCR");?>"><?echo GetMessage("SUP_QUOTE_LINK");?></a>&nbsp;]</div>


        <div align="left"><b><?=GetMessage("SUP_TIME")?></b>: <?=$arMessage["DATE_CREATE"]?></div>
        <b><?=GetMessage("SUP_FROM")?></b>:


        <?=$arMessage["OWNER_SID"]?>

        <?if (intval($arMessage["OWNER_USER_ID"])>0):?>
            [<?=$arMessage["OWNER_USER_ID"]?>]
            (<?=$arMessage["OWNER_LOGIN"]?>)
            <?=$arMessage["OWNER_NAME"]?>
        <?endif?>
        <br />


        <?
        $aImg = array("gif", "png", "jpg", "jpeg", "bmp");
        foreach ($arMessage["FILES"] as $arFile):
        ?>
        <div class="support-paperclip"></div>
        <?if(in_array(mb_strtolower(GetFileExtension($arFile["NAME"])), $aImg)):?>
            <a title="<?=GetMessage("SUP_VIEW_ALT")?>" href="<?=$componentPath?>/ticket_show_file.php?hash=<?echo $arFile["HASH"]?>&amp;lang=<?=LANG?>"><?=$arFile["NAME"]?></a>
        <?else:?>
            <?=$arFile["NAME"]?>
        <?endif?>
        (<? echo CFile::FormatSize($arFile["FILE_SIZE"]); ?>)
        [ <a title="<?=str_replace("#FILE_NAME#", $arFile["NAME"], GetMessage("SUP_DOWNLOAD_ALT"))?>" href="<?=$componentPath?>/ticket_show_file.php?hash=<?=$arFile["HASH"]?>&amp;lang=<?=LANG?>&amp;action=download"><?=GetMessage("SUP_DOWNLOAD")?></a> ]
        <br class="clear" />
        <?endforeach?>


        <br /><div id="quotetd<? echo $arMessage["ID"]; ?>"><?=$arMessage["MESSAGE"]?></div>

        </div>
    <?endforeach?>

    <?=$arResult["NAV_STRING"]?>

        </td>

    </tr>
</table>
<?endif;?>
    <?if (empty($arResult["TICKET"])):?>
    <?  $chargeBackArray["TITLE"] = "";
        ob_start();
    ?>
    <div class="container-fluid chargeback-row title-area row-required">
        <div class="field-name col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <?=GetMessage("SUP_TITLE")?>
            <span class="starrequired">
                *
            </span>
        </div>
        <div class="field-value col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <input type="text" name="TITLE" value="<?=htmlspecialcharsbx($_REQUEST["TITLE"])?>" class="form-control" />
        </div>
        <div class="field-help<? if(GetMessage("SUP_TITLTE_HELP") == ""): ?> hidden<? endif; ?> col-lg-5 col-md-12 col-sm-12 col-xs-12">
            <?=nl2br(GetMessage("SUP_TITLTE_HELP"));?>
        </div>
    </div>
    <? $chargeBackArray["TITLE"] = ob_get_clean(); ?>
    <?else:?>
    <?  $chargeBackArray["TITLE"] = "";
        ob_start();
    ?>
    <div class="container-fluid chargeback-row title-area">
        <div class="field-name col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <?=GetMessage("SUP_ANSWER")?>
        </div>
        <div class="field-value col-lg-4 col-md-6 col-sm-6 col-xs-12">
        </div>
    </div>
    <? $chargeBackArray["TITLE"] = ob_get_clean(); ?>
    <?endif?>


    <?if (mb_strlen($arResult["TICKET"]["DATE_CLOSE"]) <= 0):?>
    <?  $chargeBackArray["MESSAGE"] = "";
        ob_start();
    ?>
    <div class="container-fluid chargeback-row description-area row-required">
        <div class="field-name col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <?=GetMessage("SUP_MESSAGE")?>
            <span class="starrequired">
                *
            </span>
        </div>
        <div class="field-value col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <textarea name="MESSAGE" class="form-control" id="MESSAGE" rows="20" cols="45" wrap="virtual"><?=htmlspecialcharsbx($_REQUEST["MESSAGE"])?></textarea>
        </div>
        <div class="field-help<? if(GetMessage("SUP_MESSAGE_HELP") == ""): ?> hidden<? endif; ?> col-lg-5 col-md-12 col-sm-12 col-xs-12">
            <?=nl2br(GetMessage("SUP_MESSAGE_HELP"))?>
        </div>
    </div>
    <? $chargeBackArray["MESSAGE"] = ob_get_clean(); ?>

    <?endif?>

    <?
        global $USER_FIELD_MANAGER;
        if( isset( $arParams["SET_SHOW_USER_FIELD_T"] ) )
        {
            foreach( $arParams["SET_SHOW_USER_FIELD_T"] as $k => $v )
            {
                if(!isset($v["ALL"]) && !isset($v["ALL"]["FIELD_NAME"])) continue;

                $v["ALL"]["VALUE"] = $arParams[$k];

                ob_start();

                echo '<div class="container-fluid chargeback-row'.(($v["ALL"]["MANDATORY"] == "Y" || (!empty($required_fields) && in_array($v["ALL"]["FIELD_NAME"],$required_fields))) ? ' row-required' : '').'">
                                            <div class="field-name '. (mb_stripos($v["ALL"]["XML_ID"],'_TITLE') === false ? 'col-lg-3 col-md-6 col-sm-6' : 'form_title') .' col-xs-12">'
                                                . htmlspecialcharsbx( $v["NAME_F"] ) . (($v["ALL"]["MANDATORY"] == "Y" || (!empty($required_fields) && in_array($v["ALL"]["FIELD_NAME"],$required_fields))) ? '<span class="starrequired">*</span>' : '') .'
                                            </div>
                                            <div class="field-value '. (mb_stripos($v["ALL"]["XML_ID"],'_TITLE') === false ? '' : 'hidden') .' col-lg-4 col-md-6 col-sm-6 col-xs-12">';
                                                $APPLICATION->IncludeComponent(
                                                        'bitrix:system.field.edit',
                                                        $v["ALL"]['USER_TYPE_ID'],
                                                        array(
                                                            'arUserField' => $v["ALL"],
                                                        ),
                                                        null,
                                                        array('HIDE_ICONS' => 'Y')
                                                );
                echo '                      </div>
                                            <div class="field-help'.(isset($v["ALL"]["HELP_MESSAGE"]) && !empty($v["ALL"]["HELP_MESSAGE"]) ? '' : ' hidden').' '. (mb_stripos($v["ALL"]["XML_ID"],'_TITLE') === false ? '' : 'hidden') .' col-lg-5 col-md-12 col-sm-12 col-xs-12">
                                                '.(isset($v["ALL"]["HELP_MESSAGE"]) && !empty($v["ALL"]["HELP_MESSAGE"]) ? nl2br($v["ALL"]["HELP_MESSAGE"]) : '').'
                                            </div>
                                        </div>';

                $chargeBackArray[$v["ALL"]["FIELD_NAME"]] = ob_get_clean();
            }
        }
?>

<? if(!empty($chargeBackArray)): ?>
<form name="support_edit" id="support_edit" method="post" action="<?=$arResult["REAL_FILE_PATH"]?>" enctype="multipart/form-data">

<? if(isset($arParams["TITLE"]) && !empty($arParams["TITLE"])): ?>
<p class="h3 h3-title h3-chargeback-title">
    <?=$arParams["TITLE"];?>
</p>
<? endif; ?>

<? if(isset($arParams["DESCRIPTION"]) && !empty($arParams["DESCRIPTION"])): ?>
<div class="chargeback-description">
    <?=html_entity_decode(nl2br($arParams["DESCRIPTION"]),ENT_HTML5,LANG_CHARSET);?>
</div>
<? endif; ?>

<?=bitrix_sessid_post()?>
<input type="hidden" name="set_default" value="Y" />
<input type="hidden" name="ID" value=<?=(empty($arResult["TICKET"]) ? 0 : $arResult["TICKET"]["ID"])?> />
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="edit" value="1" />
<?if (isset($arParams["CATEGORY"]) && !empty($arParams["CATEGORY"])): ?>
<input type="hidden" name="CATEGORY_ID" id="CATEGORY_ID" value="<?=$arParams["CATEGORY"];?>" />
<?endif?>
<? foreach($fieldOrder as $field): ?>
<?
        $chargeBackArray[$field] = preg_replace('~(<select[^>]+?)class="[^"]+?"~isu',"$1",$chargeBackArray[$field]);
        $chargeBackArray[$field] = preg_replace('~(<input[^>]+?)class="[^"]+?"~isu',"$1",$chargeBackArray[$field]);
        $chargeBackArray[$field] = preg_replace('~(<textarea[^>]+?)class="[^"]+?"~isu',"$1",$chargeBackArray[$field]);

        $chargeBackArray[$field] = preg_replace('~<select~isu',"<select class=\"selectpicker\"",$chargeBackArray[$field]);
        $chargeBackArray[$field] = preg_replace('~<input~isu',"<input class=\"form-control\"",$chargeBackArray[$field]);
        $chargeBackArray[$field] = preg_replace('~<textarea~isu',"<input class=\"form-control\"",$chargeBackArray[$field]);

?>
<?=$chargeBackArray[$field];?>
<? endforeach; ?>

<? if(isset($arParams["RULES_ID"]) && !empty($arParams["RULES_ID"])):


    $rulesID = (int)$arParams["RULES_ID"];

    $ruleResDB = CIBlockElement::GetByID($rulesID);

    if($ruleResDB && ($ruleResArr = $ruleResDB->GetNext())):

        if(isset($ruleResArr["PREVIEW_TEXT"])
        && !empty($ruleResArr["PREVIEW_TEXT"])):

?>

    <div class="container-fluid i-agree-row">
        <div class="col-xs-12">
            <label>
                <input type="checkbox" id="i_agree" />
                <span class="checkbox-style">
                </span>
            </label>
            <span class="title">
                <?=GetMessage('TMPL_I_AGREE_CHARGEBACK_RULES');?>
            </span>
        </div>
    </div>
    <script type="text/javascript">
    //<!--
        $(function(){
            $('#i_agree').click(function(){

                if(this.checked == true){
                    $('#apply').attr('disabled',false);
                } else {
                    $('#apply').attr('disabled',true);
                };

            });

        });
    //-->
    </script>
    <div id="rulesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p class="h4 modal-title">
                        <?=$ruleResArr["NAME"];?>
                    </p>
                </div>
                <div class="modal-body">
                    <?=$ruleResArr["PREVIEW_TEXT"];?>
                </div>
            </div>
        </div>
    </div>
<?

        endif;

    endif;

?>

<? endif; ?>

    <div class="container-fluid buttons-row">
        <div class="col-xs-12">
            <input type="submit" id="apply" name="apply" value="<?=GetMessage("SUP_APPLY");?>" class="btn btn-info"<? if(isset($arParams["RULES_ID"]) && !empty($arParams["RULES_ID"])): ?> disabled="true"<? endif; ?> />
            <input type="hidden" value="Y" name="apply" />
        </div>
    </div>
<script type="text/javascript">
BX.ready(function(){
    var buttons = BX.findChildren(document.forms['support_edit'], {attr:{type:'submit'}});
    for (i in buttons)
    {
        BX.bind(buttons[i], "click", function(e) {
            setTimeout(function(){
                var _buttons = BX.findChildren(document.forms['support_edit'], {attr:{type:'submit'}});
                for (j in _buttons)
                {
                    _buttons[j].disabled = true;
                }

            }, 30);
        });
    }
});
<? if(!empty($required_fields)): ?>
            $(function(){
                if($('input,textarea,select','.row-required').get(0)){
                        $('input,textarea,select','.row-required').each(function(){
                            var nElt	= this;
                            var nParent = this.parentNode;
                            var cForm 	= $(this).parents("form").get(0);
                            if(cForm){
                                $(cForm).bind("submit",function(event){

                                    $(nParent).removeClass("has-success has-error");

                                    $(nElt).attr('placeholder','');

                                    isChecked	= true;
                                    switch(nElt.nodeName.toLowerCase()){
                                    case "input":

                                        switch(nElt.type){
                                        case "radio":
                                        case "checkbox":

                                            if(!nElt.checked){
                                                isChecked = false;
                                            };

                                            break;
                                        case "text":
                                        case "file":

                                            if($.trim(nElt.value) == ""){
                                                isChecked = false;
                                            };

                                            break;

                                        }

                                        break;
                                        case "select":
                                            if(typeof nElt.selectedIndex == "undefined"
                                            || nElt.options[nElt.selectedIndex].value == ""
                                            || nElt.selectedIndex == "-1"
                                            ){
                                                isChecked = false;
                                            };

                                        break;
                                        case "textarea":

                                            if($.trim(nElt.value) == ""){
                                                isChecked = false;
                                            };

                                        break;
                                    };

                                    if(!isChecked){

                                        $(nElt).attr('placeholder','<?php echo(GetMessage('ERROR_REQUIRED'));?>');
                                        $(nParent).addClass("has-error");
                                        event.preventDefault();
                                        return false;
                                        
                                    } else {
                                        $(nParent).addClass("has-success");
                                    };

                                });
                            };
                        });
                    };
                });
<? endif; ?>
</script>

</form>
<? endif; ?>
</div>