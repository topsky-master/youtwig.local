<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 * @var CUser $USER
 */
// ************************* Input params***************************************************************
$arParams["SHOW_LINK_TO_FORUM"] = ($arParams["SHOW_LINK_TO_FORUM"] == "N" ? "N" : "Y");
$arParams["FILES_COUNT"] = intval(intval($arParams["FILES_COUNT"]) > 0 ? $arParams["FILES_COUNT"] : 1);
$arParams["IMAGE_SIZE"] = (intval($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 100);

$maxStars = 6;


if ($APPLICATION->GetException() != "") {
    $arResult["ERROR_MESSAGE"] = $APPLICATION->GetException();
}

if($_REQUEST['save_product_review'] == 'Y' && empty($arResult["ERROR_MESSAGE"])){
    ?>
    <div class="container-fluid reviews">
        <div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">
					×
				</span>
            </button>
            Ваш вопрос оставлен!
        </div>
    </div>
    <?

    exit;
}

if (!isset($_REQUEST['previews']) && empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"])): ?>
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

if (!isset($_REQUEST['previews']) && !empty($arResult["ERROR_MESSAGE"])):
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
<? endif; ?>

<div class="container-fluid reviews"">
<?

if(isset($_REQUEST['previews'])){
    include(__DIR__."/form.php");
}

if (!empty($arResult["MESSAGES"]) && !isset($_REQUEST['previews'])):
    if ($arResult["NAV_RESULT"] && $arResult["NAV_RESULT"]->NavPageCount > 1 && false): ?>
        <div class="reviews-navigation-box reviews-navigation-top">
            <?=$arResult["NAV_STRING"]?>
        </div>
    <? endif; ?>
    <div class="reviews-block-container" id="<?=$arParams["FORM_ID"]?>container" >
        <?
        $iCount = 0;
        foreach ($arResult["MESSAGES"] as $reviews):
            $iCount++;

            $isReview = isset($reviews['REVIEWS']) ? true : false;

            if($isReview){

                ksort($reviews['REVIEWS']);
                array_unshift($reviews['REVIEWS'],$reviews);
                $reviews = $reviews['REVIEWS'];

            } else {

                $reviews = array($reviews);

            }

            foreach($reviews as $number => $res):

                $votesValue = 0;

                if($res["vote_count"]){
                    $votesValue = round($res["vote_sum"]/$res["vote_count"], 2);
                }

                $res["AUTHOR_NAME"] = process_redirects($res["AUTHOR_NAME"]);
                $res["POST_MESSAGE_TEXT"] = process_redirects($res["POST_MESSAGE_TEXT"]);
                $res["POST_MESSAGE_TEXT"] = preg_replace('~<b[^>]*?>.*?</b>~isu','',$res["POST_MESSAGE_TEXT"]);

                $iCount++;
                ?>
            <div<?php if($arParams['HAS_REVIEWS']): ?> <?php endif; ?> class="reviews-post-table<?=(($res["APPROVED"] == 'Y') ? "" : " reviews-post-hidden") ?><? echo ($isReview && $number > 0) ? " reply-for-review": "";?>" id="message<?=$res["ID"]?>">
                <?php if($arParams['HAS_REVIEWS']): ?>


            <?php endif; ?>
                <?
                if ($arParams["SHOW_AVATAR"] != "N")
                {

                    $avatarURI = '';

                    if(isset($res["AVATAR"])
                        && isset($res["AVATAR"]["FILE"])
                        && isset($res["AVATAR"]["FILE"]["src"])
                        && !empty($res["AVATAR"]["FILE"]["src"])){

                        $avatarURI = $res["AVATAR"]["FILE"]["src"];

                    }

                    ?>
                    <div class="review-avatar">
                        <?php if($avatarURI): ?>
                            <img src="<?=$avatarURI;?>" width="30" height="auto" alt="<?=htmlentities($arResult["ELEMENT_NAME"].', '.$res["AUTHOR_NAME"],ENT_QUOTES,LANG_CHARSET); ?>" />
                        <?php else: ?>
                            <i class="fa fa-user-o" aria-hidden="true"></i>
                        <?php endif; ?>
                    </div>
                    <?
                }
                ?>
                <span<? if($votesValue > 0){ ?> itemprop="review" itemscope itemtype="http://schema.org/Review"<?php } ?>>
                    <? if($votesValue > 0){ ?>
                        <div class="hidden" itemscope itemprop="author" itemtype="https://schema.org/Person">
                        <meta itemprop="name" content="<?=$res["AUTHOR_NAME"];?>">
                    </div>
                        <div itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating" class="hidden">
                        <meta itemprop="bestRating" content="5" />
                        <meta itemprop="ratingValue" content="<?=$votesValue;?>" />
                    </div>
                        <meta itemprop="datePublished" datetime="<?=date('Y-m-d',strtotime($res["~POST_DATE"]));?>" content="<?=date('Y-m-d',strtotime($res["~POST_DATE"]));?>" />
                    <? } ?>
                    <div<? if($votesValue > 0){ ?> itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"<?php } ?> class="reviews-text" id="message_text_<?=$res["ID"]?>">
                        <? if($votesValue > 0){ ?>
                            <meta itemprop="name" content="<?=htmlspecialcharsbx($arResult["ELEMENT_NAME"]);?>" />
                        <?php } ?>
                        <span<? if($votesValue > 0){ ?> itemprop="description"<?php } ?>><? if(mb_stripos($res["POST_MESSAGE_TEXT"],'<i>') !== 0): ?></span>
                            <i><?=$res["AUTHOR_NAME"];?></i><br />
                            <i><?=date('d.m.Y',strtotime($res["~POST_DATE"]));?></i><br />
                    <? endif; ?>
                        <?=$res["POST_MESSAGE_TEXT"]?>
                        <div class="mess-rating-info">
                            <?php

                            if(!empty($votesValue))
                                for($countStars = 1; $countStars < $maxStars; $countStars ++){
                                    ?>
                                    <span class="fa <?php if($votesValue >= $countStars){ ?>fa-star<?php } elseif($votesValue + 0.5 >= $countStars){ ?>fa-star-half-o<?php } else { ?>fa-star-o<?php } ?>"></span>
                                    <?php
                                }

                            ?>

                        </div>
                    </div>
                    <?php if(!$isReview || ($isReview &&  $number == 0)): ?>
                        <div class="add-answer">
                        <span class="answer-form-link" data-raview-id="<?=$res['ID'];?>">
                            <?=GetMessage('F_REVIEW_ANSWER');?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div><?
            endforeach;
        endforeach;
        ?>
    </div>
    <?

    if (mb_strlen($arResult["NAV_STRING"]) > 0 && $arResult["NAV_RESULT"]->NavPageCount > 1):
        ?>
        <div class="reviews-navigation-box reviews-navigation-bottom">
            <?=$arResult["NAV_STRING"]?>
        </div>
        <div class="text-center reviews-navigation-more">
            <button class="btn btn-default" data-pagen="<?=$arResult["NAV_RESULT"]->NavNum;?>" data-num="<?=$arResult["NAV_RESULT"]->NavPageCount;?>" data-current="1" id="more-reviews-btn">
                <?=GetMessage('TMPL_LOAD_MORE'); ?>
            </button>
        </div>
    <?
    endif;

endif;

if(!isset($_REQUEST['previews']))
    include(__DIR__."/form.php");
?>
</span>
</div>