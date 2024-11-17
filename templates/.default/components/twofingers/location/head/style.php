<?php

use TwoFingers\Location\Options;

?>
<style>
    .tfl-popup {
        border-radius: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_RADIUS])?>px;
        width: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_WIDTH])?>px;
        max-height: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_HEIGHT])?>px;
        padding-top: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING_TOP])?>px;
        padding-bottom: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING_BOTTOM])?>px;
        padding-left: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING_LEFT])?>px;
        padding-right: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING_RIGHT])?>px;
    }

    .tfl-define-popup {
        border-radius: <?=intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS'])?>px;
    }

    .tfl-define-popup__main {
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG']?>;
    }

    .tfl-define-popup__main:hover {
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER']?>;
    }

    .tfl-define-popup__second {
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG']?>;
    }

    .tfl-define-popup__second:hover {
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER']?>;
    }

    .tfl-popup__title {
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_TITLE_FONT_SIZE]?>px;
    <?php if ($arResult['SETTINGS'][Options::LIST_TITLE_FONT_FAMILY]): ?> font-family: '<?=$arResult['SETTINGS'][Options::LIST_TITLE_FONT_FAMILY]?>', sans-serif;
    <?php endif; ?>
    }

    .tfl-popup .tfl-popup__search {
        margin-top: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_OFFSET_TOP])?>px;
        margin-bottom: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_OFFSET_BOTTOM])?>px;
    }

    .tfl-popup .tfl-popup__search-input {
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_FONT_SIZE]?>px;
    <?php if ($arResult['SETTINGS'][Options::LIST_TITLE_FONT_FAMILY]): ?> font-family: '<?=$arResult['SETTINGS'][Options::LIST_ITEMS_FONT_FAMILY]?>', sans-serif;
    <?php endif; ?>
    }

    .tfl-popup > .tfl-popup__close {
        width: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_AREA_SIZE])?>px;
        height: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_AREA_SIZE])?>px;
        top: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_AREA_OFFSET_TOP])?>px;
        right: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_AREA_OFFSET_RIGHT])?>px;
        z-index: 9;
    }

    .tfl-popup > .tfl-popup__close:before, .tfl-popup > .tfl-popup__close:after {
        width: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_LINE_WIDTH])?>px;
        height: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_CLOSE_LINE_HEIGHT])?>px;
    }

    .tfl-popup__location-link {
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_ITEMS_FONT_SIZE]?>px;
    <?php if ($arResult['SETTINGS'][Options::LIST_TITLE_FONT_FAMILY]): ?> font-family: '<?=$arResult['SETTINGS'][Options::LIST_ITEMS_FONT_FAMILY]?>', sans-serif;
    <?php endif; ?>
    }

    .tfl-popup__nofound-mess-show {
    <?php if ($arResult['SETTINGS'][Options::LIST_TITLE_FONT_FAMILY]): ?> font-family: '<?=$arResult['SETTINGS'][Options::LIST_ITEMS_FONT_FAMILY]?>', sans-serif;
    <?php endif; ?>
    }

    .tfl-define-popup {
        padding-top: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_PADDING_TOP])?>px;
        padding-bottom: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_PADDING_BOTTOM])?>px;
        padding-left: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_PADDING_LEFT])?>px;
        padding-right: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_PADDING_RIGHT])?>px;
    <?php if ($arResult['SETTINGS'][Options::CONFIRM_TEXT_FONT_FAMILY]): ?> font-family: '<?=$arResult['SETTINGS'][Options::CONFIRM_TEXT_FONT_FAMILY]?>', sans-serif;
    <?php endif; ?>
    }

    .tfl-define-popup__text {
        padding-bottom: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_BUTTON_TOP_PADDING])?>px;
        font-size: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_TEXT_FONT_SIZE])?>px;
    }

    .tfl-define-popup__buttons {
        font-size: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_BUTTON_FONT_SIZE])?>px;
        grid-template-columns: repeat(2, calc(50% - <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_BUTTON_BETWEEN_PADDING] / 2)?>px));
        grid-gap: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_BUTTON_BETWEEN_PADDING])?>px;
    }

    .tfl-define-popup__desktop {
        width: <?=intval($arResult['SETTINGS'][Options::CONFIRM_DESKTOP_WIDTH])?>px;
    }

    .tfl-popup .tfl-popup__search-input {
        background-image:
                linear-gradient(to top, <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_FOCUS_BORDER_COLOR]?> <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_FOCUS_BORDER_WIDTH])?>px, rgba(255, 86, 5, 0) <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_FOCUS_BORDER_WIDTH])?>px),
                linear-gradient(to top, rgb(189, 189, 189) 1px, rgba(189, 189, 189, 0) 1px);
    }

    @media screen and (max-width: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_BREAKPOINT]?>px) {
        .tfl-popup {
            width: 100%;
            height: 100%;
            top: 50%;
            border-radius: 0;
            z-index: 9999999;
            /* grid-template-rows: auto auto minmax(50%, max-content);*/
            grid-template-rows: auto auto minmax(50%, 1fr);
            grid-template-columns: 100%;
            padding-top: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_PADDING_TOP])?>px;
            padding-bottom: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_PADDING_BOTTOM])?>px;
            padding-left: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_PADDING_LEFT])?>px;
            padding-right: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_PADDING_RIGHT])?>px;
            max-height: 100%;
        }

        .tfl-popup.tfl-popup_loaded {
            top: 50%;
        }

        .tfl-popup.tfl-popup_loading {
            height: 100%;
        }

        .tfl-popup__container {
            height: 100%;
        }

        .tfl-popup .tfl-popup__search {
            margin-top: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_OFFSET_TOP])?>px;
            margin-bottom: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_OFFSET_BOTTOM])?>px;
        }

        .tfl-popup > .tfl-popup__close {
            width: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_AREA_SIZE])?>px;
            height: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_AREA_SIZE])?>px;
            top: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_AREA_OFFSET_TOP])?>px;
            right: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_AREA_OFFSET_RIGHT])?>px;
        }

        .tfl-popup > .tfl-popup__close:before, .tfl-popup > .tfl-popup__close:after {
            width: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_LINE_WIDTH])?>px;
            height: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_CLOSE_LINE_HEIGHT])?>px;
        }

        .tfl-popup__with-locations.tfl-popup__with-defaults.tfl-popup__with-locations .tfl-popup__container,
        .tfl-popup__with-locations.tfl-popup__with-defaults .tfl-popup__container {
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr;
        }

        .tfl-popup__scroll-container + .tfl-popup__scroll-container {
            padding-left: 0;
        }

        .tfl-popup__with-defaults .tfl-popup__defaults {
            margin-bottom: 1rem;
            height: auto;
        }

        .tfl-popup .tfl-popup__search-input {
            max-width: none;
            width: 100%;
        }

        .tfl-popup__list {
            width: 100%;
        }

        .tfl-popup__title {
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_TITLE_FONT_SIZE]?>px;
        }

        .tfl-popup .tfl-popup__search-input {
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_FONT_SIZE]?>px;
        }

        .tfl-popup__location-link {
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_ITEMS_FONT_SIZE]?>px;
        }

        .tfl-body-freeze {
            margin-right: 0;
        }

        .tfl-define-popup {
            padding-top: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_PADDING_TOP])?>px;
            padding-bottom: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_PADDING_BOTTOM])?>px;
            padding-left: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_PADDING_LEFT])?>px;
            padding-right: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_PADDING_RIGHT])?>px;
        }

        .tfl-define-popup__text {
            font-size: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_TEXT_FONT_SIZE])?>px;
            padding-bottom: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_BUTTON_TOP_PADDING])?>px;
        }

        .tfl-define-popup__buttons {
            font-size: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_BUTTON_FONT_SIZE])?>px;
            grid-template-columns: repeat(2, calc(50% - <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_BUTTON_BETWEEN_PADDING] / 2)?>px));
            grid-gap: <?=intval($arResult['SETTINGS'][Options::CONFIRM_MOBILE_BUTTON_BETWEEN_PADDING])?>px;
        }

        .tfl-popup .tfl-popup__search-input {
            background-image:
                    linear-gradient(to top, <?=$arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_FOCUS_BORDER_COLOR]?> <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_FOCUS_BORDER_WIDTH])?>px, rgba(255, 86, 5, 0) <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_FOCUS_BORDER_WIDTH])?>px),
                    linear-gradient(to top, rgb(189, 189, 189) 1px, rgba(189, 189, 189, 0) 1px);
        }
    }
</style>