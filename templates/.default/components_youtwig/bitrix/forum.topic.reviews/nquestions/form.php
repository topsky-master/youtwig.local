<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams, $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 * @var CUser $USER
 */

$tabIndex = 1;

if ($APPLICATION->GetException() != "") {
    $arResult["ERROR_MESSAGE"] = $APPLICATION->GetException();
}

$bReset = empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"]) ? true : false;

if (empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"])):

    ?>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?=strip_tags($arResult["OK_MESSAGE"]);?>
    </div>
<?
endif;

if (!empty($arResult["ERROR_MESSAGE"])):
    $arResult["ERROR_MESSAGE"] = preg_replace(array("/<br(.*?)><br(.*?)>/is", "/<br(.*?)>$/is"), array("<br />", ""), $arResult["ERROR_MESSAGE"]);
    ?>
    <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?=strip_tags($arResult["ERROR_MESSAGE"]);?>
    </div>
<?

endif;

?>
    <div class="reviews-reply-form" id="reviews-reply-form">
        <div id="answer-form" class="hidden">
            <form method="post" action="<?=POST_FORM_ACTION_URI;?>" target="_top">
                <input type="hidden" name="REVIEW_ANSWER" value="" />
                <?php

                if ($APPLICATION->GetException() != "") {
                    $arResult["ERROR_MESSAGE"] = $APPLICATION->GetException();
                }

                if (empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"])): ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                        </button>
                        <?=strip_tags($arResult["OK_MESSAGE"]);?>
                    </div>
                <?
                endif;

                if (!empty($arResult["ERROR_MESSAGE"])):
                    $arResult["ERROR_MESSAGE"] = preg_replace(array("/<br(.*?)><br(.*?)>/is", "/<br(.*?)>$/is"), array("<br />", ""), $arResult["ERROR_MESSAGE"]);
                    ?>
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                        </button>
                        <?=strip_tags($arResult["ERROR_MESSAGE"]);?>
                    </div>
                <?

                endif;

                ?>
                <input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams["form_index"])?>" />
                <input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
                <input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
                <input type="hidden" name="FORUM_ID" value="<?=$arParams["FORUM_ID"]?>" />
                <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
                <input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
                <input type="hidden" name="save_product_review" value="Y" />
                <input type="hidden" name="preview_comment" value="N" />
                <input type="hidden" name="AJAX_POST" value="N" />
                <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
                <div class="row">
                    <div class="col-xs-12 h3 h3-review-author">
                        <?=GetMessage('F_REVIEW_NAME');?>
                    </div>
                    <div class="col-xs-12 review-author">
                        <input class="form-control"<?php if($USER->IsAuthorized()): ?> required="required"<?php endif; ?> name="REVIEW_AUTHOR" id="REVIEW_AUTHOR<?=$arParams["form_index"]?>" type="text" value="<?=isset($_REQUEST["REVIEW_AUTHOR"]) ? htmlspecialchars(trim($_REQUEST["REVIEW_AUTHOR"]),ENT_HTML5,LANG_CHARSET) : ''?>" placeholder="<?=GetMessage("F_NAME")?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 h3 h3-review-phone">
                        <?=GetMessage("F_PHONE")?>
                    </div>
                    <div class="col-xs-12 review-author">
                        <input class="form-control" name="REVIEW_PHONE" id="REVIEW_PHONE" type="text" value="<?=isset($_REQUEST["REVIEW_PHONE"]) ? htmlspecialchars(trim($_REQUEST["REVIEW_PHONE"]),ENT_HTML5,LANG_CHARSET) : ''?>" placeholder="+79_________" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 h3 h3-review-email">
                        <?=GetMessage('OPINIONS_EMAIL');?>
                    </div>
                    <div class="col-xs-12 review-email">
                        <input<?php if($USER->IsAuthorized()): ?> readonly="readonly"<?php else: ?> required="required" placeholder="<?=GetMessage("OPINIONS_EMAIL")?>"<?php endif; ?> class="form-control" name="REVIEW_EMAIL" id="REVIEW_AUTHOR" type="text" value="<?=$USER->IsAuthorized() ? htmlspecialchars(trim($USER->GetEmail()),ENT_HTML5,LANG_CHARSET) : ''?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 h3 h3-review-title">
                        <?=GetMessage('F_REVIEW_MESSAGE');?>
                    </div>
                    <div class="col-xs-12 review-desc">
                        <textarea class="form-control" required="required" id="REVIEW_TEXT" name="REVIEW_TEXT" placeholder="<?=GetMessage('F_MESS_PLACEHOLDER');?>"><?=isset($_REQUEST["REVIEW_TEXT"]) && !$bReset ? htmlspecialchars(trim($_REQUEST["REVIEW_TEXT"]),ENT_HTML5,LANG_CHARSET) : ""?></textarea>
                    </div>
                </div>
                <?php if(!$USER->IsAuthorized()): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="g-recaptcha" data-sitekey="6LeFVAcTAAAAAC_gnLQ20Xdk9mplMRumVZhvVNEc"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row reviews-reply-buttons">
                    <div class="col-sm-12">
                        <label>
                            <input type="checkbox" onchange="if(this.checked) { document.getElementById('add-review').disabled = false; } else { document.getElementById('add-review').disabled = true; };" />
                            <?=GetMessage("F_GRANT_PERSONAL_DATA_PERMISSION")?>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <input class="btn btn-info" disabled="true" name="send_button" id="add-review" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" />
                    </div>
                </div>
                <input type="hidden" name="result" value="reply" />
            </form>
        </div>
        <form method="post" action="<?=POST_FORM_ACTION_URI;?>" target="_top">
            <input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams["form_index"])?>" />
            <input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
            <input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
            <input type="hidden" name="FORUM_ID" value="<?=$arParams["FORUM_ID"]?>" />
            <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
            <input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
            <input type="hidden" name="save_product_review" value="Y" />
            <input type="hidden" name="preview_comment" value="N" />
            <input type="hidden" name="AJAX_POST" value="N" />
            <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
            <div class="row">
                <div class="col-md-6 col-sm-6 h3 h3-review-title">
                    <?=GetMessage('F_REVIEW_QUESTION');?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" name="REVIEW_AUTHOR" id="REVIEW_AUTHOR<?=$arParams["form_index"]?>" type="text" value="<?=isset($_REQUEST["REVIEW_AUTHOR"]) ? htmlspecialchars(trim($_REQUEST["REVIEW_AUTHOR"]),ENT_HTML5,LANG_CHARSET) : ''?>" placeholder="<?=GetMessage("F_NAME")?>" tabindex="<?=$tabIndex++;?>" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" name="REVIEW_PHONE" id="REVIEW_PHONE<?=$arParams["form_index"]?>" type="text" value="<?=isset($_REQUEST["REVIEW_PHONE"]) ? htmlspecialchars(trim($_REQUEST["REVIEW_PHONE"]),ENT_HTML5,LANG_CHARSET) : ''?>" placeholder="+79_________" tabindex="<?=$tabIndex++;?>" />
                </div>
                <div class="col-md-4 col-sm-12">
                    <input class="form-control" name="REVIEW_EMAIL" id="REVIEW_EMAIL<?=$arParams["form_index"]?>" type="text" value="<?=isset($_REQUEST["REVIEW_EMAIL"]) ? htmlspecialchars(trim($_REQUEST["REVIEW_EMAIL"]),ENT_HTML5,LANG_CHARSET) : ''?>" placeholder="<?=GetMessage("F_EMAIL")?>" tabindex="<?=$tabIndex++;?>" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <textarea class="form-control" id="REVIEW_TEXT" name="REVIEW_TEXT" placeholder="<?=GetMessage('F_MESS_PLACEHOLDER');?>"><?=isset($_REQUEST["REVIEW_TEXT"]) && !$bReset ? htmlspecialchars(trim($_REQUEST["REVIEW_TEXT"]),ENT_HTML5,LANG_CHARSET) : ""?></textarea>
                </div>
            </div>
            <?php if(!$USER->IsAuthorized()): ?>
                <input type="hidden" id="notauth" value="true" />
                <div class="row">
                    <div class="col-sm-12">
                        <div class="g-recaptcha" data-sitekey="6LeFVAcTAAAAAC_gnLQ20Xdk9mplMRumVZhvVNEc"></div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row reviews-reply-buttons">
                <div class="col-sm-12">
                    <label>
                        <input type="checkbox" onchange="if(this.checked) { document.getElementById('add-question').disabled = false; } else { document.getElementById('add-question').disabled = true; };" />
                        <?=GetMessage("F_GRANT_PERSONAL_DATA_PERMISSION")?>
                    </label>
                </div>
                <div class="col-sm-12">
                    <input class="btn btn-info" disabled="true" id="add-question" name="send_button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" tabindex="<?=$tabIndex++;?>" />
                </div>
            </div>
            <?php if(isset($_REQUEST['previews'])): ?>
                <input type="hidden" name="previews" id="previews" value="true" />
            <?php endif; ?>
            <input type="hidden" name="result" value="reply" />
        </form>
    </div>
<?php if(isset($_REQUEST['previews'])): ?>
    <br />
<?php endif; ?>