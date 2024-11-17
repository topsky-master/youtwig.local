<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$this->setFrameMode(true);

if (!empty($arResult)):?>
    <amp-sidebar id="sidebar" layout="nodisplay" side="left">
        <amp-img class="cross" src="/" width="20" height="20" alt="close sidebar" on="tap:sidebar.close" role="button" tabindex="0">
            <i class="fa fa-times" aria-hidden="true"></i>
        </amp-img>
        <div class="side-menu" role="navigation">
            <ul>
                <?

                $arResult['MENU_DESCRIPTION'] = '';

                if (IBLOCK_INCLUDED){

                    $arDMSelect = Array(
                        "PREVIEW_TEXT"
                    );
                    $arDMFilter = Array(
                        "IBLOCK_ID" => 18,
                        "CODE" => "logotype"
                    );

                    $dbDMRes = CIBlockElement::GetList(
                        Array(),
                        $arDMFilter,
                        false,
                        false,
                        $arDMSelect
                    );

                    if($dbDMRes){

                        $arDMRes = $dbDMRes->GetNext();

                        if($arDMRes["PREVIEW_TEXT"]){

                            $amp_content_obj = new AMP_Content( $arDMRes["PREVIEW_TEXT"],
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

                            $arDMRes["PREVIEW_TEXT"] = $amp_content_obj->get_amp_content();

                            ?>
                            <div class="menu-description">
                                <?php echo $arDMRes["PREVIEW_TEXT"]; ?>
                            </div>
                            <?php
                        }

                    }

                }


                foreach($arResult as $arItem):

                    if(!empty($arParams["MAX_LEVEL"])
                        && $arItem["DEPTH_LEVEL"] > $arParams["MAX_LEVEL"])
                        continue;

                    if(empty($arItem["LINK"]) || empty($arItem["TEXT"]))
                        continue;

                    ?>
                    <?if($arItem["SELECTED"]):?>
                    <li>
                        <a href="<?=$arItem["LINK"]?>" class="selected">
                            <?=$arItem["TEXT"]?>
                        </a>
                    </li>
                <?else:?>
                    <li class="<?=$menu_class;?>">
                        <a href="<?=$arItem["LINK"]?>">
                            <?=$arItem["TEXT"]?>
                        </a>
                    </li>
                <?endif?>
                <?endforeach?>
            </ul>
        </div>
    </amp-sidebar>
    <a id="left-menu-button" on="tap:sidebar.toggle">
        <button class="menu-button cross" on="tap:sidebar.toggle" role="button" tabindex="0">
            <i class="fa fa-bars" aria-hidden="true">
            </i>
        </button>
    </a>
<?endif?>