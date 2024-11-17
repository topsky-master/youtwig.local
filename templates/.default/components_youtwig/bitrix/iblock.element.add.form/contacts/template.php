<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;


$customID                                = isset($arParams["CUSTOM_ID"])
&&!empty($arParams["CUSTOM_ID"])
    ? $arParams["CUSTOM_ID"]
    : '';


$ajax_order_form                         = isset($_REQUEST['AJAX_ORDER_FORM'])
&&!empty($_REQUEST['AJAX_ORDER_FORM'])
    ? true
    : false;

$after_text                              = isset($arParams["AFTER_TEXT"])
&&!empty($arParams["AFTER_TEXT"])
    ? trim($arParams["AFTER_TEXT"])
    : '';

$after_text                              = html_entity_decode($after_text,ENT_HTML5,LANG_CHARSET);

$before_text                             = isset($arParams["BEFORE_TEXT"])
&&!empty($arParams["BEFORE_TEXT"])
    ? trim($arParams["BEFORE_TEXT"])
    : '';

$before_text                             = html_entity_decode($before_text,ENT_HTML5,LANG_CHARSET);

$submit_button                           = isset($arParams["SUBMIT_BUTTON"])
&&!empty($arParams["SUBMIT_BUTTON"])
    ? trim($arParams["SUBMIT_BUTTON"])
    : '';


$section_id                              = isset($arParams["SECTION_ID"])
&&!empty($arParams["SECTION_ID"])
    ? (int)$arParams["SECTION_ID"]
    : '';



?>
<div class="services clearfix" id="iblock_add_wrapper">
    <?

    if(isset($arParams['IBLOCK_ID']) && !empty($arParams['IBLOCK_ID'])):

        $res                                 = CIBlock::GetByID($arParams['IBLOCK_ID']);
        if($res && $ar_res                   = $res->GetNext()):
            if(isset($ar_res['~DESCRIPTION']) && !empty($ar_res['~DESCRIPTION'])):
                ?>
                <div class="text-center services-description">
                    <? echo $ar_res['~DESCRIPTION']; ?>
                </div>
            <?
            endif;
        endif;

    endif;

    ?>
    <form id="iblock_add" class="iblock_add iblock_add" action="<? if(defined('PAGE_404_PHP')): ?>/<?php else: ?><?php echo POST_FORM_ACTION_URI; ?><?php endif;?>" method="post">
        <?if (count($arResult["ERRORS"])):?>
            <div class="notes alert alert-warning alert-dismissible" id="errors" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
                </button>
                <span class="error">
            <? echo(implode('<br />', $arResult["ERRORS"]));?>
        </span>
            </div>
        <?endif?>
        <?if (mb_strlen($arResult["MESSAGE"]) > 0):?>
            <div class="notes alert alert-info alert-dismissible" id="errors" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
                </button>
                <span class="notes">
        	<?=ShowNote($arResult["MESSAGE"])?>
        </span>
            </div>
        <?endif?>
        <?=bitrix_sessid_post()?>
        <?if(!empty($before_text)){?>
            <div class="before-text">
                <?=$before_text;?>
            </div>
        <?}?>
        <?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

        <?  if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
            <?foreach ($arParams["PROPERTY_CODES"] as $key => $propertyID):?>
                <?php   $placeholder = "";
                ?>
                <?if (intval($propertyID) > 0):?><?php
                    $placeholder = $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]; ?>
                <?else:?>
                    <?php
                    $placeholder = (!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID));
                    ?>
                <?endif?>

                <?php   $placeholder.= (!empty($placeholder) && in_array($propertyID, $arResult["PROPERTY_REQUIRED"])) ? ' *' : ''; ?>

                <label class="container-fluid row-property property-<?php echo mb_strtolower(preg_replace('~[^a-z0-9\-\_]~i','',$propertyID)); ?>">
                    <div class="input-label col-xs-12 col-sm-3">
        					    <span class="<?php echo $arResult["PROPERTY_LIST_FULL"][$propertyID]["HINT"]; ?>">
        						</span>
                        <span>
    							    <?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?>
    							</span>
                    </div>
                    <div class="input-text col-xs-12 col-sm-3">
                        <?php
                        //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"]); echo "</pre>";
                        if (intval($propertyID) > 0)
                        {
                            if (
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
                                &&
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
                            )
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
                            elseif (
                                (
                                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
                                    ||
                                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
                                )
                                &&
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
                            )
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
                        }
                        elseif (($propertyID == "TAGS") && CModule::IncludeModule('search')){
                            $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";
                        }
                        elseif ($propertyID == "IBLOCK_SECTION") {

                            $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "IBLOCK_SECTION";

                        }

                        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y")
                        {
                            $inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
                            $inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
                        }
                        else
                        {
                            $inputNum = 1;
                        }

                        if($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
                            $INPUT_TYPE = "USER_TYPE";
                        else
                            $INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

                        switch ($INPUT_TYPE):
                        case "E":
                        if(isset($arParams["ORDERED_OBJECT"]) && !empty($arParams["ORDERED_OBJECT"])){
                            ?>
                        <input type="hidden" name="PROPERTY[<?=$propertyID?>][]" value="<? echo $arParams["ORDERED_OBJECT"]; ?>" />
                        <?
                        }

                        break;
                        case "IBLOCK_SECTION":

                        if(!empty($section_id)):
                        ?>
                        <input type="hidden" name="PROPERTY[IBLOCK_SECTION][]" value="<? echo $section_id; ?>" />
                        <?
                        endif;

                        break;

                        case "USER_TYPE":
                        for ($i = 0; $i<$inputNum; $i++)
                        {
                        if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                        {
                            $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
                            $description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
                        }
                        elseif ($i == 0)
                        {
                            $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                            $description = "";
                        }
                        else
                        {
                            $value = "";
                            $description = "";
                        }


                        echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
                            array(
                                $arResult["PROPERTY_LIST_FULL"][$propertyID],
                                array(
                                    "VALUE" => $value,
                                    "DESCRIPTION" => $description,
                                ),
                                array(
                                    "VALUE" => "PROPERTY[".$propertyID."][".$i."][VALUE]",
                                    "DESCRIPTION" => "PROPERTY[".$propertyID."][".$i."][DESCRIPTION]",
                                    "FORM_NAME"=>'iblock_add'.$customID,
                                ),
                            ));

                        ?>
                            <script type="text/javascript">
                                //<!--

                                if($("[name='PROPERTY[<? echo $propertyID; ?>][<? echo $i; ?>][VALUE]']",$("#iblock_add")).get(0)){
                                    var iNode = $("[name='PROPERTY[<? echo $propertyID; ?>][<? echo $i; ?>][VALUE]']",$("#iblock_add")).get(0);

                                    if((iNode.nodeName.toLowerCase() == "input" && iNode.type == "text")
                                        || iNode.nodeName.toLowerCase() == "textarea"){

                                        iNode.placeholder = "<? echo $placeholder; ?>";
                                    }

                                }
                                //-->
                            </script>

                        <?
                        }
                        break;
                        case "TAGS":
                            $APPLICATION->IncludeComponent(
                                "bitrix:search.tags.input",
                                "",
                                array(
                                    "VALUE" => $arResult["ELEMENT"][$propertyID],
                                    "NAME" => "PROPERTY[".$propertyID."][0]",
                                    "TEXT" => 'size="'.$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"].'"',
                                ), null, array("HIDE_ICONS"=>"Y")
                            );
                            break;
                        case "HTML":
                            $LHE = new CLightHTMLEditor;
                            $LHE->Show(array(
                                'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[".$propertyID."][0]"),
                                'width' => '100%',
                                'height' => '200px',
                                'inputName' => "PROPERTY[".$propertyID."][0]",
                                'content' => $arResult["ELEMENT"][$propertyID],
                                'bUseFileDialogs' => false,
                                'bFloatingToolbar' => false,
                                'bArisingToolbar' => false,
                                'toolbarConfig' => array(
                                    'Bold', 'Italic', 'Underline', 'RemoveFormat',
                                    'CreateLink', 'DeleteLink', 'Image', 'Video',
                                    'BackColor', 'ForeColor',
                                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
                                    'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
                                    'StyleList', 'HeaderList',
                                    'FontList', 'FontSizeList',
                                ),
                            ));
                            break;
                        case "T":
                        for ($i = 0; $i<$inputNum; $i++)
                        {

                        if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                        {
                            $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                        }
                        elseif ($i == 0)
                        {
                            $value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                        }
                        else
                        {
                            $value = "";
                        }
                        ?>
                            <textarea class="form-control" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" placeholder="<?php echo htmlspecialcharsbx($placeholder); ?>"><?=$value?></textarea>
                        <?
                        }
                        break;

                        case "S":
                        case "N":
                        for ($i = 0; $i<$inputNum; $i++)
                        {
                        if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                        {
                            $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                        }
                        elseif ($i == 0)
                        {
                            $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

                        }
                        else
                        {
                            $value = "";
                        }
                        ?>
                            <input type="text" class="form-control" id="PROPERTY[<? echo $propertyID; ?>][<? echo $i; ?>]" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" size="25" value="<?=$value?>" placeholder="<?php echo htmlspecialcharsbx($placeholder); ?>" /><?
                        if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?>11<?
                            $APPLICATION->IncludeComponent(
                                'bitrix:main.calendar',
                                'order',
                                array(
                                    'FORM_NAME' => 'iblock_add'.$customID,
                                    'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
                                    'INPUT_VALUE' => $value,
                                    'INPUT_ADDITIONAL_ATTR' => ' class="form-control" placeholder="'.htmlspecialcharsbx($placeholder).'" '
                                ),
                                null,
                                array('HIDE_ICONS' => 'Y')
                            );
                            ?><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
                        endif
                        ?><?
                        }
                        break;

                        case "F":
                        for ($i = 0; $i<$inputNum; $i++)
                        {
                        $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                        ?>
                        <input type="hidden" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
                        <input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" class="form-control" />
                            <?

                        if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
                        {
                            ?>
                        <input type="checkbox" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y" /><label for="file_delete_<?=$propertyID?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?></label>
                            <?

                        if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
                        {
                            ?>
                        <img src="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>" height="<?=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]?>" width="<?=$arResult["ELEMENT_FILES"][$value]["WIDTH"]?>" border="0" />
                            <?
                        }
                        else
                        {
                            ?>
                            <?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?>
                            <?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?> b
                            [<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]
                        <?
                        }
                        }
                        }

                        break;
                        case "L":

                        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
                            $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
                        else
                            $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

                        switch ($type):
                        case "checkbox":
                        case "radio":

                        //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]); echo "</pre>";

                        foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
                        {

                        ?>
                            <div class="row">
                                <?
                                $checked = false;
                                if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                {
                                    if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
                                    {
                                        foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
                                        {
                                            if ($arElEnum["VALUE"] == $key) {$checked = true; break;}
                                        }
                                    }
                                }
                                else
                                {
                                    if ($arEnum["DEF"] == "Y") $checked = true;
                                }

                                ?>
                                <input type="<?=$type?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label>
                                <?
                                ?>
                            </div>
                            <?

                        }
                            break;

                            case "dropdown":
                        case "multiselect":
                            ?>
                            <select name="PROPERTY[<?=$propertyID?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>" title="<? echo $placeholder; ?>" class="select-field selectpicker form-control">
                                <?
                                if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
                                else $sKey = "ELEMENT";

                                foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
                                {
                                    $checked = false;
                                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                    {
                                        foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
                                        {
                                            if ($key == $arElEnum["VALUE"]) {$checked = true; break;}
                                        }
                                    }
                                    else
                                    {
                                        if ($arEnum["DEF"] == "Y") $checked = true;
                                    }
                                    ?>
                                    <option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
                                    <?
                                }
                                ?>
                            </select>
                            <?
                            break;

                        endswitch;
                            break;
                            ?>

                        <?
                        endswitch;?>
                    </div>
                    <div class="col-xs-12 col-sm-6 hint-area">
                        <? if(isset($arResult["PROPERTY_LIST_FULL"][$propertyID]['~HINT'])
                            && !empty($arResult["PROPERTY_LIST_FULL"][$propertyID]['~HINT'])): ?>
                            <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]['~HINT'];?>
                        <? endif; ?>
                    </div>
                </label>
            <?endforeach;?>
            <?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>

                <div class="contact-row container-fluid"><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></div>
                <div class="contact-row container-fluid">
                    <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                </div>


                <div class="contact-row container-fluid"><?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?><span class="starrequired">*</span>:</div>
                <div class="contact-row container-fluid"><input type="text" name="captcha_word" maxlength="50" value=""></div>

            <?endif?>
        <?endif?>

        <?php if(!$USER->IsAuthorized()): ?>
            <div class="contact-row container-fluid">
                <div class="col-sm-12">
                    <input type="hidden" id="asknotauth" value="true" />
                    <div class="g-recaptchaask_feedback" data-sitekey="6LeFVAcTAAAAAC_gnLQ20Xdk9mplMRumVZhvVNEc"/>
                </div>
            </div>
        <?php endif; ?>

        <?if(!empty($after_text)){?>
            <div class="after-text">
                <?=$after_text;?>
            </div>
        <?}?>
        <div class="contact-row container-fluid all-price-list">
                    <span name="iblock_submit" class="iblock_submit_top btn btn-info" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" onclick="ajaxOrderFormSend(event, $(this).parents('form').get(0)); yaCounter21503785.reachGoal('otpravleno');"
                            <span>
    							<?=(!empty($submit_button) ? $submit_button : GetMessage("IBLOCK_FORM_SUBMIT"));?>
    						</span>
    					</span>
            <?if (mb_strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0):?><input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" /><?endif?>
        </div>
        <?if (mb_strlen($arParams["LIST_URL"]) > 0):?>
            <a href="<?=$arParams["LIST_URL"]?>">
                <?=GetMessage("IBLOCK_FORM_BACK")?>
            </a>
        <?endif?>
    </form>
</div>