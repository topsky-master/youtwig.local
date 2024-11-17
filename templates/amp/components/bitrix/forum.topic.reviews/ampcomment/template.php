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

// *************************/Input params***************************************************************
if (!empty($arResult["MESSAGES"])):
    if ($arResult["NAV_RESULT"] && $arResult["NAV_RESULT"]->NavPageCount > 1):
        ?>
        <div class="reviews-navigation-box reviews-navigation-top">
            <?=$arResult["NAV_STRING"]?>
        </div>
    <? endif; ?>
    <div class="reviews-block-container" id="<?=$arParams["FORM_ID"]?>container">
        <div class="reviews-block-outer">
            <div class="reviews-block-inner">
                <?
                $iCount = 0;
                foreach ($arResult["MESSAGES"] as $res):
                    $iCount++;
                    $res["AUTHOR_NAME"] = process_redirects($res["AUTHOR_NAME"]);
                    $res["POST_MESSAGE_TEXT"] = process_redirects($res["POST_MESSAGE_TEXT"]);

                    ?>
                <div itemprop="review" itemscope itemtype="http://schema.org/Review" class="reviews-post-table<?=(($res["APPROVED"] == 'Y') ? "" : " reviews-post-hidden") ?>" id="message<?=$res["ID"]?>">
                    <meta itemprop="name" content="<?=htmlspecialcharsbx($arResult["ELEMENT_NAME"]);?>" />
                    <div class="container-fluid">
                        <div class="mess-author-info">
                            <?
                            if ($arParams["SHOW_AVATAR"] != "N")
                            {

                                $avatarURI = '/bitrix/components/bitrix/forum.topic.reviews/templates/.default/images/noavatar.gif';

                                if(isset($res["AVATAR"])
                                    && isset($res["AVATAR"]["FILE"])
                                    && isset($res["AVATAR"]["FILE"]["src"])
                                    && !empty($res["AVATAR"]["FILE"]["src"])){

                                    $avatarURI = $res["AVATAR"]["FILE"]["src"];

                                }

                                ?>
                                <div class="review-avatar">
                                    <amp-img src="<?=$avatarURI;?>" width="30" height="30" alt="<?=htmlentities($arResult["ELEMENT_NAME"].', '.$res["AUTHOR_NAME"],ENT_QUOTES,LANG_CHARSET); ?>">
                                        <noscript>
                                            <img src="<?=$avatarURI;?>" width="30" height="30" alt="<?=htmlentities($arResult["ELEMENT_NAME"].', '.$res["AUTHOR_NAME"],ENT_QUOTES,LANG_CHARSET); ?>" />
                                        </noscript>
                                    </amp-img>
                                </div>
                                <?
                            }
                            ?>
                            <div>
                                <strong><?
                                    if (intval($res["AUTHOR_ID"]) > 0 && !empty($res["AUTHOR_URL"])):
                                        ?>
                                    <a href="<?=$res["AUTHOR_URL"]?>">
                                        <span itemprop="author">
                                            <?=$res["AUTHOR_NAME"]?>
                                        </span>
                                        </a><?
                                    else:
                                        ?>
                                        <span itemprop="author">
                                        <?=$res["AUTHOR_NAME"]?>
                                        </span><?
                                    endif;
                                    ?>
                                </strong>
                                <time class="message-post-date" itemprop="datePublished" datetime="<?=date('Y-m-d',strtotime($res["POST_DATE"]));?>">
                                    <?=$res["POST_DATE"]?>
                                </time>
                            </div>
                        </div>
                        <div class="mess-rating-info">
                            <?php

                            $votesValue = 0;

                            if($res["vote_count"]){
                                $votesValue = round($res["vote_sum"]/$res["vote_count"], 2);
                            }

                            if(!empty($votesValue))
                                for($countStars = 1; $countStars < $maxStars; $countStars ++){
                                    ?>
                                    <span class="fa <?php if($votesValue >= $countStars){ ?>fa-star<?php } elseif($votesValue + 0.5 >= $countStars){ ?>fa-star-half-o<?php } else { ?>fa-star-o<?php } ?>">
                            </span>
                                    <?php
                                }

                            ?>
                            <? if($votesValue > 0){ ?>
                                <div itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating" class="hidden">
                                    <meta itemprop="worstRating" content="1" />
                                    <meta itemprop="bestRating" content="5" />
                                    <meta itemprop="ratingValue" content="<?=$votesValue;?>" />
                                </div>
                            <? } ?>
                        </div>
                    </div>
                    <div class="reviews-text" itemprop="description" id="message_text_<?=$res["ID"]?>">
                        <?php

                        $amp_content_obj = new AMP_Content($res["POST_MESSAGE_TEXT"],
                            array(
                                //'AMP_YouTube_Embed_Handler' => array(),
                            ),
                            array(
                                'AMP_Style_Sanitizer' => array(),
                                'AMP_Blacklist_Sanitizer' => array(),
                                'AMP_Img_Sanitizer' => array(),
                                'AMP_Video_Sanitizer' => array(),
                                'AMP_Audio_Sanitizer' => array(),
                                'AMP_Iframe_Sanitizer' => array(
                                    'add_placeholder' => true,
                                ),
                            ),
                            array(
                                'content_max_width' => 600,
                            )
                        );

                        $res["POST_MESSAGE_TEXT"] = $amp_content_obj->get_amp_content();
                        ?>
                        <?=$res["POST_MESSAGE_TEXT"]?>
                    </div>
                    </div><?
                endforeach;
                ?>
            </div>
        </div>
    </div>
    <?

    if (mb_strlen($arResult["NAV_STRING"]) > 0 && $arResult["NAV_RESULT"]->NavPageCount > 1):
        ?>
        <div class="reviews-navigation-box reviews-navigation-bottom">
            <?=$arResult["NAV_STRING"]?>
        </div>
    <?
    endif;

endif;


include(__DIR__."/form.php");

?>