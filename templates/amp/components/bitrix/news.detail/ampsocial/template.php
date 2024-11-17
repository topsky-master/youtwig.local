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

if(	isset($arResult["PROPERTIES"]["SOCIAL_ICONS"]["VALUE"])
    && !empty($arResult["PROPERTIES"]["SOCIAL_ICONS"]["VALUE"])
    && isset($arResult["PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"])
    && !empty($arResult["PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"])
):

    ?>
    <div class="social-icons">
        <? if($arParams["DISPLAY_NAME"] != "N"
            && $arResult["NAME"]):?>
        <span class="h3">
            <?php echo $arResult['NAME']; ?>
        </span>
        <? endif; ?>
        <?php

        $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["VALUE"] = !is_array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["VALUE"])
            : $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["VALUE"];

        $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"] = !is_array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"])
            ? array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"])
            : $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"];


        if(isset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"])
            && !empty($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"])
        ):

            $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"] = !is_array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"])
                ? array($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"])
                : $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"];


            foreach($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"] as $key  => $value):

                $pathValue = '';
                $pathValue = CFile::GetPath($value);

                if(!empty($pathValue)):

                    $pathValue = rectangleImage(
                        $_SERVER['DOCUMENT_ROOT'].$pathValue,
                        26,
                        26,
                        $pathValue,
                        '#ffffff'
                    ); 

                    $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"][$key] = $pathValue;

                else:
                    unset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"][$key]);
                endif;

            endforeach;

        endif;

        ?>
        <ul class="social-icons">
            <?php

            foreach($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["VALUE"] as $key => $value):

                $tooltip = '';
                if(isset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_TITLES"]["VALUE"][$key])
                    && !empty($arResult["DISPLAY_PROPERTIES"]["SOCIAL_TITLES"]["VALUE"][$key])
                ):

                    $tooltip = CUtil::JSEscape($arResult["DISPLAY_PROPERTIES"]["SOCIAL_TITLES"]["VALUE"][$key]);

                endif;

                ?>
                <li>
                    <?php if(isset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"][$key])
                    && !empty($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"][$key])
                    ):?>
                    <a rel="nofollow" href="<?php echo $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"][$key]; ?>">
                        <?php endif; ?>

                        <?php if(isset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"][$key])): ?>
                            <i>
                                <amp-img width="26" height="26" src="<?php echo $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"][$key]; ?>" alt="<?php echo $tooltip; ?>">
                                    <noscript>
                                        <img src="<?php echo $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_FILES"]["VALUE"][$key]; ?>" width="26" height="26" alt="<?php echo $tooltip; ?>" />
                                    </noscript>
                                </amp-img>
                            </i>
                        <?php else: ?>
                            <?php echo $arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS"]["~VALUE"][$key]; ?>
                        <?php endif; ?>
                        <?php if(isset($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"][$key])
                        && !empty($arResult["DISPLAY_PROPERTIES"]["SOCIAL_ICONS_LINKS"]["VALUE"][$key])
                        ):?>
                    </a>
                <?php endif; ?>
                </li>
            <?php

            endforeach;

            ?>
        </ul>
    </div>
<?php endif; ?>