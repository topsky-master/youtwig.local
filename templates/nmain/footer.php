<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<?php
if(defined('ERROR_404')):
    require_once $_SERVER['DOCUMENT_ROOT'].'/404_catalog.php';
endif;
?>
</div>
</div>
<footer>
    <script type='application/ld+json'>
        {
            "@context": "http://www.schema.org",
            "@type": "WPFooter",
            "copyrightYear": "2024",
            "copyrightHolder": "Интернет-магазин TWiG"
        }
    </script>
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "LocalBusiness",
          "address": {
            "@type": "PostalAddress",
            "postalCode":"129323 ",
            "addressLocality": "Москва",
            "streetAddress": "Снежная 26"
          },
          "name": "Интернет-магазин TWiG",
          "priceRange": "<?=min_price;?>-<?=max_price;?>RUB",
            "telephone": "+74951505183",
            "email": "info@youtwig.ru",
            "image": {
                "@type": "ImageObject",
                "url": "https://youtwig.ru/upload/iblock/068/0684d5ef70dba50bcac1a16f90f6bd1d.png"
            }
          }
    </script>
    <div class="container">
        <div class="block-vl col-md-3 col-sm-6">
            <div class="au">
               <?
                $APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_DIR."include/".IMPEL_SERVER_NAME."/footer.php",
                    "EDIT_TEMPLATE" => "standard.php"
                ),
                    false
                );
            ?>
                          </div>
            <div class="aum">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "nlist",
                    Array(
                        "ROOT_MENU_TYPE" => "bottom1",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "personal",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "Y"
                    )
                );?>
            </div>
            <div class="aus">
            <?


$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
    "AREA_FILE_SHOW" => "file",
    "PATH" => SITE_DIR."include/social.php",
    "EDIT_TEMPLATE" => "standard.php"
),
    false
);
?>
            </div>
        </div>
        <div class="block-vl col-md-3 col-sm-6">
            <div class="mt ptp">
                <?

                if(($suString = twigTMPLHFCache::returnTmplHFBlock("nbottom-sotrudnichestvo")) === true){

                } else {

                    ob_start();

                    $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "nbottom",
                        array(
                            "DISPLAY_DATE" => "N",
                            "DISPLAY_NAME" => "Y",
                            "DISPLAY_PICTURE" => "N",
                            "DISPLAY_PREVIEW_TEXT" => "N",
                            "USE_SHARE" => "N",
                            "AJAX_MODE" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "18",
                            "ELEMENT_ID" => "",
                            "ELEMENT_CODE" => "sotrudnichestvo",
                            "CHECK_DATES" => "Y",
                            "FIELD_CODE" => array(
                                0 => "",
                                1 => "",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "LINK",
                                1 => "",
                            ),
                            "IBLOCK_URL" => "",
                            "SET_TITLE" => "N",
                            "SET_CANONICAL_URL" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "BROWSER_TITLE" => "-",
                            "SET_META_KEYWORDS" => "N",
                            "META_KEYWORDS" => "-",
                            "SET_META_DESCRIPTION" => "N",
                            "META_DESCRIPTION" => "-",
                            "SET_STATUS_404" => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "USE_PERMISSIONS" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_GROUPS" => "Y",
                            "PAGER_TEMPLATE" => "",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => "Страница",
                            "PAGER_SHOW_ALL" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "COMPONENT_TEMPLATE" => "bottom",
                            "AJAX_OPTION_ADDITIONAL" => ""
                        ),
                        false
                    );

                    $shBlock = ob_get_clean();
                    twigTMPLHFCache::setTmplHFCache($shBlock,$suString);

                }

                ?>
            </div>
            <div class="ptpm">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "list",
                    Array(
                        "ROOT_MENU_TYPE" => "bottom2",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "personal",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "Y"
                    )
                );?>
            </div>
            <div class="pmt">
                <?


$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
    "AREA_FILE_SHOW" => "file",
    "PATH" => SITE_DIR."include/oplata.php",
    "EDIT_TEMPLATE" => "standard.php"
),
    false
);
?>

             
            </div>
        </div>
        <div class="block-vl col-md-3 col-sm-6">
            <div class="mt qa">
                <?

                if(($suString = twigTMPLHFCache::returnTmplHFBlock("nbottom-voprosy-i-otvety")) === true){

                } else {

                    ob_start();

                    $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "nbottom",
                        array(
                            "DISPLAY_DATE" => "N",
                            "DISPLAY_NAME" => "Y",
                            "DISPLAY_PICTURE" => "N",
                            "DISPLAY_PREVIEW_TEXT" => "N",
                            "USE_SHARE" => "N",
                            "AJAX_MODE" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "18",
                            "ELEMENT_ID" => "",
                            "ELEMENT_CODE" => "voprosy_i_otvety",
                            "CHECK_DATES" => "Y",
                            "FIELD_CODE" => array(
                                0 => "",
                                1 => "",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "LINK",
                                1 => "",
                            ),
                            "IBLOCK_URL" => "",
                            "SET_TITLE" => "N",
                            "SET_CANONICAL_URL" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "BROWSER_TITLE" => "-",
                            "SET_META_KEYWORDS" => "N",
                            "META_KEYWORDS" => "-",
                            "SET_META_DESCRIPTION" => "N",
                            "META_DESCRIPTION" => "-",
                            "SET_STATUS_404" => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "USE_PERMISSIONS" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_GROUPS" => "Y",
                            "PAGER_TEMPLATE" => "",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => "Страница",
                            "PAGER_SHOW_ALL" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "COMPONENT_TEMPLATE" => "bottom",
                            "AJAX_OPTION_ADDITIONAL" => ""
                        ),
                        false
                    );

                    $shBlock = ob_get_clean();
                    twigTMPLHFCache::setTmplHFCache($shBlock,$suString);

                }

                ?>
            </div>
            <div class="qam">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "list",
                    Array(
                        "ROOT_MENU_TYPE" => "bottom3",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "personal",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "Y"
                    )
                );?>
            </div>
            <div class="cap">
                <?

                if(($suString = twigTMPLHFCache::returnTmplHFBlock("nbottom-created-by")) === true){

                } else {

                    ob_start();

                    $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "nbottom",
                        array(
                            "DISPLAY_DATE" => "N",
                            "DISPLAY_NAME" => "N",
                            "DISPLAY_PICTURE" => "N",
                            "DISPLAY_PREVIEW_TEXT" => "Y",
                            "USE_SHARE" => "N",
                            "AJAX_MODE" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "18",
                            "ELEMENT_ID" => "",
                            "ELEMENT_CODE" => "created_by",
                            "CHECK_DATES" => "Y",
                            "FIELD_CODE" => array(
                                0 => "PREVIEW_TEXT",
                                1 => "",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "",
                                1 => "",
                            ),
                            "IBLOCK_URL" => "",
                            "SET_TITLE" => "N",
                            "SET_CANONICAL_URL" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "BROWSER_TITLE" => "-",
                            "SET_META_KEYWORDS" => "N",
                            "META_KEYWORDS" => "-",
                            "SET_META_DESCRIPTION" => "N",
                            "META_DESCRIPTION" => "-",
                            "SET_STATUS_404" => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "USE_PERMISSIONS" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_GROUPS" => "Y",
                            "PAGER_TEMPLATE" => "",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => "Страница",
                            "PAGER_SHOW_ALL" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "COMPONENT_TEMPLATE" => "bottom",
                            "AJAX_OPTION_ADDITIONAL" => ""
                        ),
                        false
                    );

                    $shBlock = ob_get_clean();
                    twigTMPLHFCache::setTmplHFCache($shBlock,$suString);

                }

                ?>
            </div>
        </div>
        <div class="block-vl col-md-3 col-sm-6">
            <div class="mt ctg">
                <?

                if(($suString = twigTMPLHFCache::returnTmplHFBlock("nbottom-products-catalog")) === true){

                } else {

                    ob_start();

                    $APPLICATION->IncludeComponent(
                        "bitrix:news.detail",
                        "nbottom",
                        array(
                            "DISPLAY_DATE" => "N",
                            "DISPLAY_NAME" => "Y",
                            "DISPLAY_PICTURE" => "N",
                            "DISPLAY_PREVIEW_TEXT" => "N",
                            "USE_SHARE" => "N",
                            "AJAX_MODE" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "18",
                            "ELEMENT_ID" => "",
                            "ELEMENT_CODE" => "products_catalog",
                            "CHECK_DATES" => "Y",
                            "FIELD_CODE" => array(
                                0 => "",
                                1 => "",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "LINK",
                                1 => "",
                            ),
                            "IBLOCK_URL" => "",
                            "SET_TITLE" => "N",
                            "SET_CANONICAL_URL" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "BROWSER_TITLE" => "-",
                            "SET_META_KEYWORDS" => "N",
                            "META_KEYWORDS" => "-",
                            "SET_META_DESCRIPTION" => "N",
                            "META_DESCRIPTION" => "-",
                            "SET_STATUS_404" => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "USE_PERMISSIONS" => "N",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_GROUPS" => "Y",
                            "PAGER_TEMPLATE" => "",
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => "Страница",
                            "PAGER_SHOW_ALL" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "COMPONENT_TEMPLATE" => "bottom",
                            "AJAX_OPTION_ADDITIONAL" => ""
                        ),
                        false
                    );

                    $shBlock = ob_get_clean();
                    twigTMPLHFCache::setTmplHFCache($shBlock,$suString);

                }

                ?>
            </div>
            <div class="cse">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "list",
                    Array(
                        "ROOT_MENU_TYPE" => "bottom4",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "personal",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "Y"
                    )
                );?>
            </div>
        </div>
    </div>
</footer>
<div class="mobile_preview" id="mobile_overlay"></div>
<?

if(($suString = twigTMPLHFCache::returnTmplHFBlock("nmobile-menu")) === true){

} else {

    ob_start();

    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "nmobile-menu",
        Array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_DATE" => "N",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "FIELD_CODE" => array("ID","CODE","NAME","PREVIEW_TEXT","PREVIEW_PICTURE",""),
            "FILTER_NAME" => "",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "IBLOCK_ID" => "18",
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "999",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "",
            "PARENT_SECTION" => "452",
            "PARENT_SECTION_CODE" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "PROPERTY_CODE" => array("LINK","LINK_ATRIBUTE"),
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SORT_BY1" => "ACTIVE_FROM",
            "SORT_BY2" => "SORT",
            "SORT_ORDER1" => "DESC",
            "SORT_ORDER2" => "ASC",
            "STRICT_SECTION_CHECK" => "N",
            "COMPOSITE_FRAME_MODE" => "N",
            "COMPOSITE_FRAME_TYPE" => ""
        )
    );

    $shBlock = ob_get_clean();
    twigTMPLHFCache::setTmplHFCache($shBlock,$suString);

}

twigTMPLHFCache::finalizeTmplHFBlock();

?>
<div class="modal fade" id="askfordetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
                <p class="h4 modal-title">
                    <?=GetMessage("TMPL_ASK_FOR_DETAIL");?>
                </p>
            </div>
            <div class="modal-body">
                <?

                $APPLICATION->IncludeComponent(
                    "bitrix:iblock.element.add.form",
                    "contacts",
                    array(
                        "COMPONENT_TEMPLATE" => "contacts",
                        "IBLOCK_TYPE" => "",
                        "IBLOCK_ID" => "28",
                        "STATUS_NEW" => "N",
                        "LIST_URL" => "",
                        "USE_CAPTCHA" => "N",
                        "USER_MESSAGE_EDIT" => "Спасибо. Мы с Вами свяжемся в ближайшее время.",
                        "USER_MESSAGE_ADD" => "Спасибо. Мы с Вами свяжемся в ближайшее время.",
                        "DEFAULT_INPUT_SIZE" => "30",
                        "RESIZE_IMAGES" => "N",
                        "PROPERTY_CODES" => array("NAME",239,130,131,132,133),
                        "PROPERTY_CODES_REQUIRED" => array("NAME",130,131,132),
                        "GROUPS" => array(
                            0 => "2",
                        ),
                        "STATUS" => "INACTIVE",
                        "ELEMENT_ASSOC" => "CREATED_BY",
                        "MAX_USER_ENTRIES" => "100000",
                        "MAX_LEVELS" => "100000",
                        "LEVEL_LAST" => "Y",
                        "MAX_FILE_SIZE" => "0",
                        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
                        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
                        "SEF_MODE" => "N",
                        "CUSTOM_TITLE_NAME" => "E-mail",
                        "CUSTOM_TITLE_TAGS" => "",
                        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
                        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
                        "CUSTOM_TITLE_IBLOCK_SECTION" => "",
                        "CUSTOM_TITLE_PREVIEW_TEXT" => "",
                        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
                        "CUSTOM_TITLE_DETAIL_TEXT" => "",
                        "CUSTOM_TITLE_DETAIL_PICTURE" => "",
                        "CUSTOM_ID" => "2",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "-1",
                        "CACHE_NOTES" => "",
                        "CACHE_GROUPS" => "N",
                        "AFTER_TEXT" => "",
                        "BEFORE_TEXT" => "Если вы не нашли интересующий вас товар на сайте, пожалуйста, отправьте нам заявку заполнив поля ниже.<br />Наши менеджеры подберут деталь и свяжуться с Вами в течении дня. "
                    ),
                    false
                );



                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>