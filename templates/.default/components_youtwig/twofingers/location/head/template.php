<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Options;

Loc::loadMessages(__FILE__);

$this->setFrameMode($arParams['ORDER_TEMPLATE'] != 'Y');

if (!function_exists('tfLocationShowFavoritesContainer')) {
    function tfLocationShowFavoritesContainer()
    {
        ?>
        <div class="tfl-popup__scroll-container tfl-popup__defaults">
            <ul class="tfl-popup__list"></ul>
        </div><?php
    }
}

$frameName = $this->createFrame()->begin();

if ($arParams['ORDER_TEMPLATE'] == 'Y'): ?>
    <span class="tfl__link-container">
        <span>
            <a href="#"
               data-location-id="<?= $arResult['CITY_ID'] ?>"
               data-location-code="<?= $arResult['CITY_CODE'] ?>"
               data-order="true"
               class="<?= $arResult['SETTINGS'][Options::ORDER_LINK_CLASS] ?> tfl__link tfl__link_order"
            ><?= $arResult['CITY_NAME'] ?></a>
        </span>
        <input type="hidden" name="<?= $arParams['PARAMS']['INPUT_NAME'] ?>" class="tf_location_city_input"
               value="<?= $arResult['CITY_CODE'] ?>">
        <input type="hidden" autocomplete="off" class="bx-ui-sls-route" style="padding: 0px; margin: 0px;"
               value="<?= $arResult['CITY_NAME'] ?>">
    </span>
<? else: ?>
    <span class="tfl__link-container">
        <? if (strlen($arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT])):
            ?><span class="tfl__link-label"><?= $arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT]; ?></span><?php
        endif;
        ?><span>
            <a href="#"
               data-location-id="<?= $arResult['CITY_ID'] ?>"
               data-location-code="<?= $arResult['CITY_CODE'] ?>"
               class="<?= $arResult['SETTINGS'][Options::LIST_LINK_CLASS] ?> tfl__link"
            ><?= $arResult['CITY_NAME'] ?></a>
        </span>
    </span>
<?endif;

$frameName->beginStub();

?><span class="tfl__link-container">
    <? if (strlen($arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT])):
        ?><span class="tfl__link-label"><?= $arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT]; ?></span><?php
    endif;
    ?><span>
        <a href="#"
           class="<?= $arResult['SETTINGS'][Options::LIST_LINK_CLASS] ?> tfl__link"><?= Loc::getMessage('tfl__choose') ?></a>
    </span>
</span><?php

$frameName->end();

if (TfLocationComponent::$templateLoaded) {
    return;
}

TfLocationComponent::$templateLoaded = true;

$framePopup = $this->createFrame()->begin(''); // empty stub!!!

// include_once 'style.php';

?>
    <div class="tfl-popup-overlay" style="display:none;">
        <div class="tfl-popup favorites-<?= $arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION] ?>">
            <div class="tfl-popup__close tfl-popup__close_list"></div>
            <div class="tfl-popup__title-container">
                <? if (strlen($arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER'])): ?>
                    <div class="tfl-popup__title"><?= $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER'] ?></span></div>
                <?php endif; ?>
            </div>

            <div class="tfl-popup__search-wrapper">
                <?php if ($arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION] == 'above-search') {
                    tfLocationShowFavoritesContainer();
                } ?>
                <div class="tfl-popup__search">
                    <input
                            type="text"
                            autocomplete="off"
                            name="search"
                            placeholder="<?= $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'] ?>"
                            class="tfl-popup__search-input">
                    <a href="#" class="tfl-popup__clear-field">
                        <span class="tfl-popup__close"></span>
                    </a>
                    <div class="tfl-popup__search-icon">
                        <svg width="17" height="17" viewBox="0 0 17 17" aria-hidden="true">
                            <path class="cls-1"
                                  d="M16.709,16.719a1,1,0,0,1-1.412,0l-3.256-3.287A7.475,7.475,0,1,1,15,7.5a7.433,7.433,0,0,1-1.549,4.518l3.258,3.289A1,1,0,0,1,16.709,16.719ZM7.5,2A5.5,5.5,0,1,0,13,7.5,5.5,5.5,0,0,0,7.5,2Z"></path>
                        </svg>
                    </div>
                </div>

                <?php if ($arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION] == 'under-search') {
                    tfLocationShowFavoritesContainer();
                } ?>
            </div>

            <div class="tfl-popup__container">
                <?php if ($arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION] == 'left-locations') {
                    tfLocationShowFavoritesContainer();
                } ?>

                <div class="tfl-popup__scroll-container tfl-popup__locations">
                    <ul class="tfl-popup__list"></ul>
                    <div class="tfl-popup__nofound-mess"><?= $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_NO_FOUND'] ?></div>
                </div>

                <?php if ($arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION] == 'right-locations') {
                    tfLocationShowFavoritesContainer();
                } ?>
            </div>
        </div>
        <div class="tfl-popup-overlay__loader">
            <div class="tfl-popup-overlay__circle"></div>
            <div class="tfl-popup-overlay__circle"></div>
            <div class="tfl-popup-overlay__circle"></div>
        </div>
    </div>
    <div class="tfl-define-popup" style="display:none;">
        <div class="tfl-define-popup__text"><?= $arResult['CONFIRM_POPUP_TEXT'] ?></div>
        <div class="tfl-popup__close"></div>
        <div class="tfl-define-popup__buttons"
             style="border-radius: 0 0 <?= intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS']) ?>px <?= intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS']) ?>px">
            <?php if (strlen($arResult['CITY_ID'])): ?>
                <a href="#"
                   class="tfl-define-popup__button tfl-define-popup__main tfl-define-popup__yes"><?= Loc::getMessage('tfl__yes') ?></a>
                <a href="#"
                   class="tfl-define-popup__button tfl-define-popup__second tfl-define-popup__list"><?= Loc::getMessage('tfl__list') ?></a>
            <? else: ?>
                <a href="#"
                   class="tfl-define-popup__button tfl-define-popup__main tfl-define-popup__list"><?= Loc::getMessage('tfl__list') ?></a>
                <a href="#"
                   class="tfl-define-popup__button tfl-define-popup__second tfl-define-popup__yes"><?= Loc::getMessage('tfl__close') ?></a>
            <?php endif; ?>
        </div>
    </div>
    <script>

        var TFLocation;

        if (typeof BX != 'undefined') {
            if (window.frameCacheVars !== undefined) {
                BX.addCustomEvent("onFrameDataReceived", function (json) {
                    initTFLocation();
                });
            } else {
                BX.ready(function () {
                    if (window.frameCacheVars !== undefined) {
                        BX.addCustomEvent("onFrameDataReceived", function (json) {
                            initTFLocation();
                        });
                    } else {
                        initTFLocation();
                    }
                });
            }

            <?php if (!Options::isOrderSetTemplate() && Options::isOrderSetLocation()): ?>

            BX.addCustomEvent("onAjaxSuccess", function (data) {

                if ((TFLocation === undefined) || !data.hasOwnProperty('locations')) {
                    return;
                }

                var propertyId = Object.keys(data.locations)[0];

                if (data.locations[propertyId].lastValue === undefined) {
                    return;
                }

                let callback = function (response) {
                    if (response.hasOwnProperty('status') && (response.status === 'success') &&
                        response.hasOwnProperty('location')) {
                        TFLocation.updateLinks(response.location.id, response.location.name, response.location.code);
                    }
                }

                TFLocation.setLocation(data.locations[propertyId].lastValue, callback);
            });

            <?php endif; ?>

        } else {
            $(function () {
                initTFLocation();
            });
        }

        if (typeof initTFLocation === 'undefined') {
            initTFLocation = function () {
                TFLocation = new TfLocation(<?=$arResult['JS_PARAMS']?>, '<?=$arResult['JS_CALLBACK']?>');

                $(document).on('click', '.tfl__link', function (e) {
                    TFLocation.openLocationsPopup($(this));

                    e.stopPropagation();
                    e.preventDefault();

                    return false;
                });

                <? if($arResult['CALL_CONFIRM_POPUP'] == 'Y'):?>
                TFLocation.openConfirmPopup();
                <?php endif;

                if($arResult['CALL_LOCATION_POPUP'] == 'Y'):?>
                TFLocation.openLocationsPopup();
                <?php endif;?>
            }
        }


    </script>
<?php $framePopup->end();