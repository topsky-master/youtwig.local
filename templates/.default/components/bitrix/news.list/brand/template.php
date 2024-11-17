
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED                             !==true)die();?>
<?php if(isset($arResult["ITEMS"])
    && is_array($arResult["ITEMS"])
    && sizeof($arResult["ITEMS"])): ?>
    <?php

    $iblock_title                                                                   = trim($arParams['IBLOCK_TITLE']);

    $image_width			                                                        = isset($arParams['IMAGE_WIDTH'])
    &&!empty($arParams['IMAGE_WIDTH'])
        ? (int)$arParams['IMAGE_WIDTH']
        : 0;

    $image_width			                                                        = empty($image_width)
        ? 0
        : $image_width;

    $image_height			                                                        = isset($arParams['IMAGE_HEIGHT'])
    &&!empty($arParams['IMAGE_HEIGHT'])
        ? (int)$arParams['IMAGE_HEIGHT']
        : 0;

    $image_height			                                                        = empty($image_height)
        ? 0
        : $image_height;

    $class                                                                          = 3;
    $num_items                                                                      = isset($arParams['NUM_ITEMS'])
    &&!empty($arParams['NUM_ITEMS'])
        ? (int)$arParams['NUM_ITEMS']
        : 4;

    switch($num_items):

        case 1:

            $class                                                                  = 12;

            break;
        case 2:

            $class                                                                  = 6;

            break;
        case 3:

            $class                                                                  = 4;

            break;
        case 4:
        default:

            $num_items                                                              = 4;
            $class                                                                  = 3;

            break;
        case 6:

            $class                                                                  = 2;

            break;
        case 12:

            $class                                                                  = 1;

            break;


    endswitch;




    ?>
    <?php if(!empty($iblock_title)){?>
        <span class="h2">
    <?php echo $iblock_title; ?>
</span>
    <?}?>
    <div class="brands brands-list clearfix" id="brands-list">

        <?foreach($arResult["ITEMS"] as $key                                                =>$arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            if($arParams["DISPLAY_PICTURE"]                                                 !="N"
                && !is_array($arItem["PREVIEW_PICTURE"])){
                $arItem["PREVIEW_PICTURE"]                                                  = array('SRC' => $templateFolder .'/images/noimage.png');
            }

            if(	$arParams["DISPLAY_PICTURE"]                                                !="N"
                && is_array($arItem["PREVIEW_PICTURE"])
                && isset($arItem["PREVIEW_PICTURE"]["SRC"])
                && !empty($arItem["PREVIEW_PICTURE"]["SRC"])
            ):


                $arItem["PREVIEW_PICTURE"]["SRC"]                                           = rectangleImage(
                    $_SERVER['DOCUMENT_ROOT'].$arItem["PREVIEW_PICTURE"]["SRC"],
                    $image_width,
                    $image_height,
                    $arItem["PREVIEW_PICTURE"]["SRC"],
                    '#ffffff');
            endif;

            $params 						                                                = Array(
                "max_len" 				    =>"200",
                "change_case" 			    =>"L",
                "replace_space" 		    =>"_",
                "replace_other" 		    =>"_",
                "delete_repeat_replace"     =>"true",
            );


            $name_class                                                                     = ' news'.$arItem['ID'];

            if(!empty($arItem["NAME"])){
                $name_class						                                            .= ' '.CUtil::translit($arItem["NAME"], LANGUAGE_ID, $params);
            };

            ?>

            <?php if($key % $num_items                                                      ==0): ?>
                <div class="row">
            <?php endif; ?>

            <div class="news-item brands-item col-lg-<?php echo $class; ?> col-md-<?php echo $class; ?> col-sm-<?php echo $class; ?> col-xs-6<?=$name_class;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <?php

                $link	                                                        	= (isset($arItem["DISPLAY_PROPERTIES"])
                    &&isset($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK"])
                    &&isset($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK"]["VALUE"]))
                &&!empty($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK"]["VALUE"])
                    ? current($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK"]["VALUE"])
                    : $arItem["DETAIL_PAGE_URL"];

                $another_link_title	                                                = (isset($arItem["DISPLAY_PROPERTIES"])
                    &&isset($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK_TITLE"])
                    &&isset($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK_TITLE"]["VALUE"]))
                &&!empty($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK_TITLE"]["VALUE"])
                    ? current($arItem["DISPLAY_PROPERTIES"]["ANOTHER_LINK_TITLE"]["VALUE"])
                    : sprintf(GetMessage('CT_BNL_ELEMENT_MORE'),'<span>'.$arItem["NAME"].'</span>');




                ?>
                <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
                    <div class="brands-img clearfix">
                        <?php if(!empty($link)): ?>
                        <a href="<?php echo $link; ?>">
                            <?php endif; ?>
                            <img class="img-responsive" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=CUtil::JSEscape($arItem["NAME"]);?>" />
                            <?php if(!empty($link)): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                <?endif?>

                <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                    <div class="brands-title h3 h3-title">
                        <?php if(!empty($link)): ?>
                        <a href="<?php echo $link; ?>">
                            <?php endif; ?>
                            <?=$arItem["NAME"];?>
                            <?php if(!empty($link)): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                <?endif;?>
                <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                    <div class="preview_text clearfix">

                        <?php if(!empty($link)): ?>
                        <a href="<?php echo $link; ?>">
                            <?php endif; ?>
                            <?=($arItem["PREVIEW_TEXT"]);?>
                            <?php if(!empty($link)): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                <?endif;?>

                <?if(!empty($another_link_title)):?>
                    <div class="read-more clearfix">
                        <?php if(!empty($link)): ?>
                        <a href="<?php echo $link; ?>" class="btn btn-info">
                            <?php endif; ?>
                            <?=($another_link_title);?>
                            <?php if(!empty($link)): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                <?endif;?>

            </div>

            <?php if((($key + 1) % $num_items                                            ==0)
                || ((sizeof($arResult["ITEMS"]) - 1)                              ==$key)): ?>
                </div>
            <?php endif; ?>


        <?endforeach;?>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
<?php endif; ?>