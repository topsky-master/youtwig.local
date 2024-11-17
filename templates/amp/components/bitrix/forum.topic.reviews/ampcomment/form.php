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

$POST_FORM_ACTION_URI = (CMain::IsHTTPS() ? 'https' : 'http') .'://'. IMPEL_SERVER_NAME . "".'/amp/ajax/amp_comments.php';

if (empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"])):
    ?>
    <div class="reviews-note-box reviews-note-note">
        <?=strip_tags($arResult["OK_MESSAGE"]);?>
    </div>
<?
endif;

if (!empty($arResult["ERROR_MESSAGE"])):
    $arResult["ERROR_MESSAGE"] = preg_replace(array("/<br(.*?)><br(.*?)>/is", "/<br(.*?)>$/is"), array("<br />", ""), $arResult["ERROR_MESSAGE"]);
    ?>
    <div class="reviews-note-error reviews-note-box">
        <?=strip_tags($arResult["ERROR_MESSAGE"], "reviews-note-error");?>
    </div>
<?

endif;

?>
<div class="reviews-reply-form" id="reviews-reply-form">
    <form method="post" action-xhr="<?=$POST_FORM_ACTION_URI;?>" target="_top">
        <div class="errors" submit-success>
            <template type="amp-mustache">
                {{#ERROR}}
                <div class="result result-error reviews-note-error reviews-note-box">{{ERROR}}</div>
                {{/ERROR}}
                {{#SUCCESS}}
                <div class="result result-success reviews-note-box reviews-note-note">{{SUCCESS}}</div>
                {{/SUCCESS}}
            </template>
        </div>
        <div submit-error>
            <template type="amp-mustache">
                {{#ERROR}}
                <div class="result result-error reviews-note-error reviews-note-box">{{ERROR}}</div>
                {{/ERROR}}
                {{^ERROR}}
                <div class="result result-error reviews-note-error reviews-note-box"><?php echo GetMessage("F_MESS_FRTRWL_ERROR"); ?></div>
                {{/ERROR}}
            </template>
        </div>
        <input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams["form_index"])?>" />
        <input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
        <input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
        <input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
        <input type="hidden" name="save_product_review" value="Y" />
        <input type="hidden" name="preview_comment" value="N" />
        <input type="hidden" name="amp_comment" value="Y" />
        <input type="hidden" name="AJAX_POST" value="<?=$arParams["AJAX_POST"]?>" />
        <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
        <div>
            <?
            /* GUEST PANEL */
            if (!$arResult["IS_AUTHORIZED"]):
                ?>
                <div class="reviews-reply-field reviews-reply-fields">
                    <div class="reviews-reply-field-user">
                        <div class="reviews-reply-field">
                            <label for="REVIEW_AUTHOR<?=$arParams["form_index"]?>">
                                <?=GetMessage("OPINIONS_NAME")?>
                                <span class="reviews-required-field">
                                *
                                </span>
                            </label>
                            <span>
                            <input name="REVIEW_AUTHOR" id="REVIEW_AUTHOR<?=$arParams["form_index"]?>" size="30" type="text" value="<?=$arResult["REVIEW_AUTHOR"]?>" tabindex="<?=$tabIndex++;?>" />
                        </span>
                        </div>
                        <?
                        if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):
                            ?>
                            <div class="reviews-reply-field-user-sep">
                                &nbsp;
                            </div>
                            <div class="reviews-reply-field-email">
                                <label for="REVIEW_EMAIL<?=$arParams["form_index"]?>">
                                    <?=GetMessage("OPINIONS_EMAIL")?>
                                </label>
                                <span>
                            <input type="text" name="REVIEW_EMAIL" id="REVIEW_EMAIL<?=$arParams["form_index"]?>" size="30" value="<?=$arResult["REVIEW_EMAIL"]?>" tabindex="<?=$tabIndex++;?>" />
                        </span>
                            </div>
                        <?
                        endif;
                        ?>
                    </div>
                    <div class="reviews-reply-rating">
                        <?php for ($starCount = $maxStars - 1; $starCount > 0; $starCount --): ?>
                            <input id="element<?=$starCount;?>" type="radio" name="vote" value="<?=$starCount;?>" />
                            <label class="fa star fa-star-o" for="element<?=$starCount;?>">
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            <?
            endif;
            ?>
            <div<?php if ($arResult["IS_AUTHORIZED"]): ?> class="reviews-reply-field reviews-reply"<?php endif; ?>>
                <?php if ($arResult["IS_AUTHORIZED"]): ?>
                    <div class="reviews-reply-rating">
                        <?php for ($starCount = $maxStars - 1; $starCount > 0; $starCount --): ?>
                            <input id="element<?=$starCount;?>" type="radio" name="vote" value="<?=$starCount;?>" />
                            <label class="fa star fa-star-o" for="element<?=$starCount;?>">
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <div class="reviews-reply-header">
                    <span>
                        <?=$arParams["MESSAGE_TITLE"]?>
                    </span>
                    <span class="reviews-required-field">
                        *
                    </span>
                </div>
            </div>
            <div class="reviews-reply-field reviews-reply-field-text">
                <textarea id="REVIEW_TEXT" name="REVIEW_TEXT" placeholder="<?=GetMessage('F_MESS_PLACEHOLDER');?>"><?php
                    echo isset($arResult["REVIEW_TEXT"])
                        ? htmlspecialchars($arResult["REVIEW_TEXT"],ENT_HTML5,LANG_CHARSET)
                        : "";
                    ?></textarea>
            </div>
            <?

            /* CAPTHCA */
            if (mb_strlen($arResult["CAPTCHA_CODE"]) > 0):
                ?>
                <div class="reviews-reply-field reviews-reply-field-captcha">
                    <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
                    <div class="reviews-reply-field-captcha-label">
                        <label for="captcha_word">
                            <?=GetMessage("F_CAPTCHA_PROMT")?>
                            <span class="reviews-required-field">
                            *
                            </span>
                        </label>
                        <span>
                            <input type="text" size="30" name="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" />
                        </span>
                    </div>
                    <div class="reviews-reply-field-captcha-image">
                        <amp-img width="180" height="40" src="/bitrix/tools/captcha.php?captcha_code=<?=$APPLICATION->CaptchaGetCode();?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>"></amp-img>
                    </div>
                </div>
            <? endif; ?>
            <div class="reviews-reply-field reviews-reply-buttons">
                <input class="btn btn-info" name="send_button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" tabindex="<?=$tabIndex++;?>" />
            </div>
        </div>
    </form>
</div>