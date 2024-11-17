<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(false);
?>
<div class="modal fade" id="sharemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="basket_distribution">
                    <div class="social">
                        <h4><?=GetMessage("skyweb24_SHAREBASKET_SHARE_WITH_FRIENDS")?>:</h4>
                        <div class="basketLink">
                            <a class="url" id="url_get" onclick="show_url();"></a>
                            <a id="BasUrl" style="display:none"></a>
                            <span id="sub_url_before"><?=GetMessage("skyweb24_SHAREBASKET_CLICK_ON_THE_LINK")?></span>
                            <span id="sub_url" style="display:none;"><?=GetMessage("skyweb24_SHAREBASKET_REFERENCE_IS_COPIED")?></span>
                        </div>
                        <div class="socialButtons">
                            <?if(isset($arResult['SOCIAL']['VK'])){?>
                                <a href="<?=$arResult['SOCIAL']['VK']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_VK")?>"><img src="<?=$templateFolder?>/img/vk.jpg"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['ODNOKLASSNIKI'])){?>
                                <a href="<?=$arResult['SOCIAL']['ODNOKLASSNIKI']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_OK")?>"><img src="<?=$templateFolder?>/img/odnoklassniki.jpg"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['FACEBOOK'])){?>
                                <a href="<?=$arResult['SOCIAL']['FACEBOOK']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_FB")?>"><img src="<?=$templateFolder?>/img/faceb.jpg"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['TWITTER'])){?>
                                <a href="<?=$arResult['SOCIAL']['TWITTER']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_TWITTER")?>"><img src="<?=$templateFolder?>/img/tw.jpg"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['TELEGRAM'])){?>
                                <a href="<?=$arResult['SOCIAL']['TELEGRAM']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_TELEGRAM")?>"><img src="<?=$templateFolder?>/img/teleg.png"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['WHATSAPP'])){?>
                                <a href="<?=$arResult['SOCIAL']['WHATSAPP']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_WHATSAPP")?>"><img src="<?=$templateFolder?>/img/wa.png"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['VIBER'])){?>
                                <a id="viber_share" href="<?=$arResult['SOCIAL']['VIBER']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_VIBER")?>"><img src="<?=$templateFolder?>/img/viber.png"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['MOYMIR'])){?>
                                <a href="<?=$arResult['SOCIAL']['MOYMIR']?>" target="_blank" title="<?=GetMessage("skyweb24_SHAREBASKET_SHARE_IN_MM")?>"><img src="<?=$templateFolder?>/img/mail.jpg"></a>
                            <?}?>
                            <?if(isset($arResult['SOCIAL']['EMAIL'])){?>
                                <a onclick="email();"><img src="<?=$templateFolder?>/img/email.jpg" title="<?=GetMessage("skyweb24_SHAREBASKET_SEND_LINK")?>"></a>
                                <form action="" name="sendEmailToFriends" id="BasEmail" method="post" onsubmit="sendEmail();return false;" style="display:none;">
                                    <div class="error" style="display: none;"><?=GetMessage("skyweb24_SHAREBASKET_ERROR")?>
                                        <p id="p_error_form"></p>
                                    </div>
                                    <input name="skyweb24_hidden_form_code" type="hidden">
                                    <? global $USER;
                                    if (!$USER->IsAuthorized()) {?>
                                        <input required type="text" id="BasketUser" name="fio_user" placeholder="<?=GetMessage("skyweb24_SHAREBASKET_GET_FIO")?>" autofocus>
                                        <br />
                                        <?if (isset($arResult["CAPTCHA"])) {?>
                                            <div id="captcha">
                                                <img id="captcha_pic">
                                                <a onclick="updateCaptcha();" rel="nofollow" class="update-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a>

                                                <input id="captcha_code" name="captcha_code" type="hidden">
                                                <input required id="captcha_word" name="captcha_word" type="text" placeholder="<?=GetMessage("skyweb24_SHAREBASKET_CAPTCHA_PLACEHOLDER")?>">
                                            </div>
                                        <?}?>
                                    <?}?>
                                    <label>
                                        <input required type="email" id="BasketEmail" name="email_friend" placeholder="<?=GetMessage("skyweb24_SHAREBASKET_GET_EMAIL_FRIEND")?>" >
                                    </label>
                                    <button type="submit"><?=GetMessage("skyweb24_SHAREBASKET_SEND")?></button>
                                </form>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    var componentPath = "<?=$componentPath?>";
    var url_for_friends="";
    var thanksForMail="<?=GetMessage("skyweb24_SHAREBASKET_MAIL_SUCCESS_SEND")?>";
	createShareBasketLink();
</script>