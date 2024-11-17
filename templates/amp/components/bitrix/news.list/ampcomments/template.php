<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

if(sizeof($arResult["ITEMS"]) > 0):
    ?>
    <?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?><br />
<?endif;?>
    <section class="comment-list">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?

            $is_comment = false;

            if(isset($arItem['DISPLAY_PROPERTIES'])
                && isset($arItem['DISPLAY_PROPERTIES']['IS_COMMENT'])
                && isset($arItem['DISPLAY_PROPERTIES']['IS_COMMENT']['VALUE'])
                && !empty($arItem['DISPLAY_PROPERTIES']['IS_COMMENT']['VALUE'])):
                $is_comment = true;
            endif;

            $where = '<span class="user-info"><span class="fa fa-user hidden-sm hidden-md hidden-lg"> '.$arItem['NAME'].'</span>';

            if(isset($arItem['DISPLAY_PROPERTIES'])
                && isset($arItem['DISPLAY_PROPERTIES']['WHERE'])
                && isset($arItem['DISPLAY_PROPERTIES']['WHERE']['VALUE'])
                && !empty($arItem['DISPLAY_PROPERTIES']['WHERE']['VALUE'])):
                $where .= ', '.$arItem['DISPLAY_PROPERTIES']['WHERE']['VALUE'];
            endif;

            if(isset($arItem['DISPLAY_PROPERTIES'])
                && isset($arItem['DISPLAY_PROPERTIES']['DELIVERY_NAME'])
                && isset($arItem['DISPLAY_PROPERTIES']['DELIVERY_NAME']['VALUE'])
                && !empty($arItem['DISPLAY_PROPERTIES']['DELIVERY_NAME']['VALUE'])):
                $where .= ', '.$arItem['DISPLAY_PROPERTIES']['DELIVERY_NAME']['VALUE'];
            endif;

            $where .= '</span>';



            ?>
            <article class="row" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="col-md-2 col-sm-2<?php if($is_comment): ?> col-md-offset-1 col-sm-offset-0<?php endif; ?> hidden-xs">
                    <figure class="thumbnail">
                        <?php if(isset($arItem["PREVIEW_PICTURE"])
                            && is_array($arItem["PREVIEW_PICTURE"])
                            && !empty($arItem["PREVIEW_PICTURE"])):
                            ?>
                            <amp-img itemprop="image" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arItem["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" layout="responsive" <?=$arItem["PREVIEW_PICTURE"]["SRCSET"];?>>
                                <noscript>
                                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"];?>" alt="<?=htmlentities($arItem["NAME"],ENT_QUOTES,LANG_CHARSET); ?>" />
                                </noscript>
                            </amp-img>
                        <?php else: ?>
                            <i class="fa fa-user-o" aria-hidden="true"></i>
                        <?php endif; ?>
                        <figcaption class="text-center">
                            <?echo $arItem["NAME"]?>
                        </figcaption>
                    </figure>
                </div>
                <div class="<?php if($is_comment): ?>col-md-9 col-sm-9<?php else: ?>col-md-10 col-sm-10<?php endif; ?>">
                    <div class="panel panel-default arrow left">
                        <div class="panel-body">
                            <header class="text-left">
                                <?php if(isset($arItem['DISPLAY_ACTIVE_FROM'])
                                    && !empty($arItem['DISPLAY_ACTIVE_FROM'])): ?>
                                    <time class="comment-date" datetime="<?=date('d-m-Y H:i:s',strtotime($arItem['ACTIVE_FROM']));?>">
                                        <i class="fa fa-clock-o"></i>
                                        <?=trim($arItem['DISPLAY_ACTIVE_FROM']);?>
                                    </time>
                                <?php endif; ?>
                                <?php if(!empty($where)): ?>
                                    <?=$where;?>
                                <?php endif; ?>
                                <?php if(isset($arItem['DISPLAY_PROPERTIES'])
                                    && isset($arItem['DISPLAY_PROPERTIES']['rating'])
                                    && isset($arItem['DISPLAY_PROPERTIES']['rating']['VALUE'])
                                    && !empty($arItem['DISPLAY_PROPERTIES']['rating']['VALUE'])): ?>
                                    <?php $rating = (int)trim($arItem['DISPLAY_PROPERTIES']['rating']['VALUE']); ?>
                                    <div class="rating">
                                        <?php for($i = 0; $i < 5; $i ++): ?>
                                            <?php if($i <= $rating): ?>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star-o" aria-hidden="true"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </header>
                            <div class="comment-post">
                                <?echo $arItem["PREVIEW_TEXT"];?>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        <?endforeach;?>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </section>
<?endif;?>