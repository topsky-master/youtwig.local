<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
    Bitrix\Main\ModuleManager;

global ${$arParams["FILTER_NAME"]};

$filter_set = false;
$cacheTime = 24 * 7 * 3600;
$active_filters = array();

?>
    <div class="row products-list">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 catalog-menu-area">
            <? global $USER;

            $arGroups = CUser::GetUserGroup($USER->GetID());
            $viewMenu = sizeof(array_intersect(array(6,1,7),(array)$arGroups)) ? true : false;
            ?>
            <? if($viewMenu){ ?>
                <div id="admin-catalog-menu" class="admin-catalog-menu clearfix">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "list",
                        array(
                            "ROOT_MENU_TYPE" => "catalog-admin",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => array(
                            ),
                            "MAX_LEVEL" => "1",
                            "CHILD_MENU_TYPE" => "",
                            "USE_EXT" => "N",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N"
                        ),
                        false
                    );?>
                </div>
            <? } ?>
            <div class="smart_filter clearfix" id="smart_filter">
                <?

                

                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "compare",
                    array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "SECTION_ID" => $arCurSection['ID'],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SAVE_IN_SESSION" => "N",
                        "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                        "XML_EXPORT" => "Y",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                        "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        "SEF_MODE" => $arParams["SEF_MODE"],
                        "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                        "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                        "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                        "DISPLAY_ELEMENT_COUNT" => "N",
                        "COMPOSITE_FRAME_MODE" => "Y",
                        "COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITH_STUB",
                        "ELEMENTS_COLLAPSE" => $arParams["ELEMENTS_COLLAPSE"],
                        "INSTANT_RELOAD" => "Y",
                        "DISPLAY_CODES" => $arParams["DISPLAY_CODES_VERTICAL"]
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );

                if((isset(${$arParams["FILTER_NAME"]})
                    && !empty(${$arParams["FILTER_NAME"]}))
                ){

                    foreach(${$arParams["FILTER_NAME"]} as $filter_key => $filter_value){
                        if(mb_stripos($filter_key,'=PROPERTY_') !== false){
                            $filter_set = true;
                            break;
                        };
                    };

                };

                $APPLICATION->SetPageProperty('filter_set', (int)$filter_set);

                ?>
            </div>
            <div class="section-model-filter-wrapper clearfix hidden-xs">
                <?$APPLICATION->IncludeComponent(
                    "impel:models",
                    "",
                    Array(
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "BLOCK_TITLE" => "Подбор запчасти",
                        "BLOCK_COLUMNS" => "1",
                        "AREA_ID" => "section"
                    )
                );?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 product-list">
            <?

            $APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                "horizontal",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arCurSection['ID'],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SAVE_IN_SESSION" => "N",
                    "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                    "XML_EXPORT" => "Y",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    "SEF_MODE" => $arParams["SEF_MODE"],
                    "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                    "DISPLAY_ELEMENT_COUNT" => "N",
                    "COMPOSITE_FRAME_MODE" => "Y",
                    "COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITH_STUB",
                    "ELEMENTS_COLLAPSE" => 0,
                    "INSTANT_RELOAD" => "Y",
                    "DISPLAY_CODES" =>  $arParams["DISPLAY_CODES_HORIZONTAL"]
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );

            if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y')
                $basketAction = (isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '');
            else
                $basketAction = (isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '');

            $intSectionID = 0;
            ?>
            <?
            global $APPLICATION, $arrFilter;

            if(!is_array($arrFilter)):
                $arrFilter = array();
            endif;

            $howSort = array(
                "sort:asc" => GetMessage("SORT_SORT_ASC"),
                "sort:desc" => GetMessage("SORT_SORT_DESC"),
                "name:asc" => GetMessage("SORT_NAME_ASC"),
                "name:desc" => GetMessage("SORT_NAME_DESC"),
                "catalog_QUANTITY:asc" => GetMessage("SORT_QUANTITY_ASC"),
                "catalog_QUANTITY:desc" => GetMessage("SORT_QUANTITY_DESC"),
                "show_counter:asc" => GetMessage("SORT_SHOW_COUNTER_ASC"),
                "show_counter:desc" => GetMessage("SORT_SHOW_COUNTER_DESC"),
                "created_date:asc" => GetMessage("SORT_CREATED_ASC"),
                "created_date:desc" => GetMessage("SORT_CREATED_DESC"),
                "HAS_PREVIEW_PICTURE:asc" => GetMessage("SORT_PREVIEW_PICTURE_ASC"),
                "HAS_PREVIEW_PICTURE:desc" => GetMessage("SORT_PREVIEW_PICTURE_DESC"),
                "propertysort_ONSTOCK:asc" => GetMessage("SORT_ONSTOCK_ASC"),
                "propertysort_ONSTOCK:desc" => GetMessage("SORT_ONSTOCK_DESC"),
            );

            if(isset($arParams["PRICE_CODE"])
                && !empty($arParams["PRICE_CODE"])){

                $obCache = new CPHPCache;
                $cacheID = 'catalog_price_code';


                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $catalog_price_code = $tmp[$cacheID];
                    }

                    foreach ($catalog_price_code as $ar_res){
                        $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                        $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                    }

                } else {

                    foreach ($arParams["PRICE_CODE"] as $price_name){

                        $db_res = CCatalogGroup::GetList(
                            array(
                                "SORT" =>"ASC"
                            ),
                            array(
                                "NAME" => $price_name
                            ),
                            false,
                            false,
                            array("ID")
                        );

                        $catalog_price_code = array();

                        if(is_object($db_res)){
                            while ($ar_res = $db_res->Fetch()){
                                $howSort["catalog_PRICE_".$ar_res["ID"].":asc"] = (GetMessage("PRICE_ASC"));
                                $howSort["catalog_PRICE_".$ar_res["ID"].":desc"] = (GetMessage("PRICE_DESC"));
                                $catalog_price_code[] = $ar_res["ID"];
                            }
                        }

                    }


                    if($obCache->StartDataCache()){

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $catalog_price_code
                            )
                        );

                    };

                };

            };

            $sort_values = array_keys($howSort);

            $sort_code_param = 'sort:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $sord_default = $arParams["ELEMENT_SORT_FIELD"].":".$arParams["ELEMENT_SORT_ORDER"];
            $_SESSION[$sort_code_param] = !isset($_SESSION[$sort_code_param]) ? $sord_default : $_SESSION[$sort_code_param];

            $sort_code = ((isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])) ? (urldecode($_REQUEST['sort'])) : ($_SESSION[$sort_code_param]));


            if(empty($sort_code) && (($APPLICATION->get_cookie($sort_code_param)))){
                $sort_code = $APPLICATION->get_cookie($sort_code_param);
            }

            if(!(!empty($sort_code) && (in_array($sort_code,$sort_values)))){
                $sort_code = $sord_default;
            }

            $_SESSION[$sort_code_param] = $sort_code;
            $APPLICATION->set_cookie($sort_code_param,$sort_code);


            if(!empty($sort_code) && in_array($sort_code,$sort_values)){

                list($arParams["ELEMENT_SORT_FIELD"],$arParams["ELEMENT_SORT_ORDER"]) = explode(":",$sort_code);
                list($arParams["ELEMENT_SORT_FIELD2"],$arParams["ELEMENT_SORT_ORDER2"]) = explode(":",$sort_code);

            }

            $element_count_param = 'PAGE_ELEMENT_COUNT:section:'.$arParams["IBLOCK_ID"].':'.$arParams["SECTION_CODE_PATH"];

            $element_count = $_REQUEST['PAGE_ELEMENT_COUNT'];

            $pager = array(
                0 =>15,
                1 =>30,
                2 =>90);


            if(empty($element_count) && (($APPLICATION->get_cookie($element_count_param)))){
                $element_count = $APPLICATION->get_cookie($element_count_param);
            }

            $element_count = (int)$element_count;
            $element_count = !in_array($element_count,$pager) ? 15 : $element_count;
            $element_count = empty($element_count) ? 15 : $element_count;

            $arParams["PAGE_ELEMENT_COUNT"] = $element_count;
            $APPLICATION->set_cookie($element_count_param,$element_count);

            if(isset($_REQUEST['LIST_TYPE']) && !empty($_REQUEST['LIST_TYPE'])){

                $list_type = isset($_REQUEST['LIST_TYPE']) && in_array((string)$_REQUEST['LIST_TYPE'],array('GRID','LIST')) ? trim($_REQUEST['LIST_TYPE']) : $arParams['LIST_TYPE'];
                $_SESSION['LIST_TYPE'] = $list_type;
                $APPLICATION->set_cookie('LIST_TYPE',$list_type);

            } else {

                $list_type = $APPLICATION->get_cookie('LIST_TYPE');
                $list_type = !empty($list_type) && in_array((string)$list_type,array('GRID','LIST')) ? trim($list_type) : '';

                if(empty($list_type) && !empty($_SESSION['LIST_TYPE']) && in_array((string)$_SESSION['LIST_TYPE'],array('GRID','LIST'))){
                    $list_type = $_SESSION['LIST_TYPE'];
                };

                if(empty($list_type)){
                    $list_type = $arParams['LIST_TYPE'];
                };

            };

            unset($_REQUEST['SECTION_CODE_PATH'],$_REQUEST['ELEMENT_CODE'],$_GET['SECTION_CODE_PATH'],$_GET['ELEMENT_CODE'],$_REQUEST['filter'],$_GET['filter']);

            $_REQUEST['SECTION_CODE_PATH'] = null;
            $_REQUEST['ELEMENT_CODE'] = null;
            $_REQUEST['filter'] = null;

            $_GET['SECTION_CODE_PATH'] = null;
            $_GET['ELEMENT_CODE'] = null;
            $_GET['filter'] = null;

            if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){

                $APPLICATION->RestartBuffer();

            }

            ?>
            <div id="comp_smart_filter">
                <?
                global $NavNum; $NavNum = 0;

                $hasError404 = false;

                $currentURL = $APPLICATION->GetCurPage();
                $currentURL = trim($currentURL);

                $filterURL = preg_replace('~.*filter/(.*)$~isu',"$1",$currentURL);
                $filterURL = trim($filterURL);

                $filterParts = array();

                if(!empty($filterURL)){

                    if(mb_stripos($filterURL,'/') !== false){

                        $filterURLs = explode('/',$filterURL);

                    } else {
                        $filterURLs = array($filterURL);
                    }

                    foreach($filterURLs as $smartPart){

                        $smartPart = preg_split("/-(is|or)-/", $smartPart, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $startParts = false;

                        if(is_array($smartPart) && sizeof($smartPart) > 0){

                            $startFrom = $smartPart[0];

                            foreach($smartPart as $smartElement){

                                if($smartElement == 'is'){
                                    $startParts = true;
                                }

                                if($startParts
                                    && $smartElement != 'or'
                                    && $smartElement != 'is'){

                                    $filterEnumsDB = CIBlockPropertyEnum::GetList(
                                        Array(
                                            "DEF" => "DESC",
                                            "SORT" => "ASC"),
                                        Array(
                                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                            "CODE" => $startFrom,
                                            "XML_ID" => $smartElement)
                                    );

                                    $hasError404 = true;

                                    if($filterEnumsDB){

                                        if($filterEnumsArr = $filterEnumsDB->GetNext()) {

                                            $hasError404 = false;

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

                if($hasError404){
                    CHTTP::SetStatus("404 Not Found");
                }

                $intSectionID = $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "",
                    array(
                        "hasError404" => ($hasError404 ? 'Y' : 'N'),
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                        "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                        "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                        "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                        "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                        "BASKET_URL" => $arParams["BASKET_URL"],
                        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SET_TITLE" => $arParams["SET_TITLE"],
                        "MESSAGE_404" => $arParams["MESSAGE_404"],
                        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                        "SHOW_404" => $arParams["SHOW_404"],
                        "FILE_404" => $arParams["FILE_404"],
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                        "SHOW_ALL_WO_SECTION" => isset($arParams["SHOW_ALL_WO_SECTION"]) && $arParams["SHOW_ALL_WO_SECTION"] == "Y" ? "Y" : "N",
                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                        "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                        "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

                        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                        "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                        "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                        "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                        "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                        "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                        "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                        'LABEL_PROP' => $arParams['LABEL_PROP'],
                        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                        'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                        'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                        'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                        'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

                        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        "ADD_SECTIONS_CHAIN" => $arParams['ADD_SECTIONS_CHAIN'],
                        'ADD_TO_BASKET_ACTION' => $basketAction,
                        'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                        'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                        'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                        'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                        'LIST_TYPE' => $list_type,
                        'NOT_MUCH' => (int)$arParams['NOT_MUCH']
                    ),
                    $component
                );

                ?>
            </div>
            <?

            if($filter_set){


                if((isset(${$arParams["FILTER_NAME"]})
                    && !empty(${$arParams["FILTER_NAME"]}))
                ){

                    foreach(${$arParams["FILTER_NAME"]} as $filter_key => $filter_value){
                        if(mb_stripos($filter_key,'=PROPERTY_') !== false){
                            $active_filters[$filter_key] = $filter_value;
                        };
                    };

                };

                $main_parameter = array();
                $main_parameter_sizeof = \COption::GetOptionString('my.stat', "main_parameter_sizeof", 0, SITE_ID);

                $valueid = array();
                $obCache = new CPHPCache;
                $cacheID = 'chainvalueid';

                if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                    $tmp = array();
                    $tmp = $obCache->GetVars();

                    if(isset($tmp[$cacheID])){
                        $valueid = $tmp[$cacheID];
                    }

                } else {

                    if ($main_parameter_sizeof > 0) {

                        for ($i = 0; $i < $main_parameter_sizeof; $i++) {

                            $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id" . $i, "", SITE_ID);
                            $main_parameter['value'][$i] = \COption::GetOptionString('my.stat', "main_parameter_value" . $i, "", SITE_ID);

                            $main_parameter['value'][$i] = mb_stripos($main_parameter['value'][$i], ',') !== false ? explode(',', $main_parameter['value'][$i]) : array($main_parameter['value'][$i]);
                            $main_parameter['value'][$i] = array_map('trim', $main_parameter['value'][$i]);
                            $main_parameter['value'][$i] = array_unique($main_parameter['value'][$i]);
                            $main_parameter['value'][$i] = array_filter($main_parameter['value'][$i]);

                            foreach ($main_parameter['value'][$i] as $enumValue) {

                                $peDB = CIBlockPropertyEnum::GetList(
                                    array(),
                                    array(
                                        'VALUE' => $enumValue,
                                        'PROPERTY_ID' => $main_parameter['id'][$i]
                                    )
                                );

                                if ($peDB && ($peArr = $peDB->GetNext())) {

                                    if (!is_array($valueid[$main_parameter['id'][$i]])) {
                                        $valueid[$main_parameter['id'][$i]] = array();
                                    }

                                    if (!in_array($peArr['ID'], $valueid[$main_parameter['id'][$i]])) {
                                        $valueid[$main_parameter['id'][$i]][] = $peArr['ID'];
                                    }

                                }

                            }

                        }

                    };

                    if ($obCache->StartDataCache()) {

                        $obCache->EndDataCache(
                            array(
                                $cacheID => $valueid
                            )
                        );

                    };
                }

                if($main_parameter_sizeof
                    && !empty($valueid))
                    for($i = 0; $i < $main_parameter_sizeof; $i ++) {

                        $main_parameter['id'][$i] = \COption::GetOptionString('my.stat', "main_parameter_id" . $i, "", SITE_ID);
                        $main_parameter['chain'][$i] = \COption::GetOptionString('my.stat', "main_parameter_chain" . $i, "", SITE_ID);

                        if((!sizeof(array_intersect($valueid[$main_parameter['id'][$i]],$active_filters['=PROPERTY_'.$main_parameter['id'][$i]]))
                                || !isset($active_filters['=PROPERTY_'.$main_parameter['id'][$i]])
                                || empty($active_filters['=PROPERTY_'.$main_parameter['id'][$i]])
                            )
                            && isset($active_filters['=PROPERTY_'.$main_parameter['chain'][$i]])
                            && !empty($active_filters['=PROPERTY_'.$main_parameter['chain'][$i]])
                        ){

                            $redirectUrl = isset($arParams['SEF_FOLDER'])
                            && !empty($arParams['SEF_FOLDER'])
                                ? trim($arParams['SEF_FOLDER'])
                                : '';

                            if(isset($intSectionID)
                                && !empty($intSectionID)){

                                $sectionIterator = CIBlockSection::GetList(array(), array('ID' => $intSectionID), false, array('SECTION_PAGE_URL'));

                                if($sectionIterator){

                                    $sectionIterator->SetUrlTemplates('', $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"]);
                                    $sectionResult = $sectionIterator->GetNext();

                                    if(isset($sectionResult['SECTION_PAGE_URL'])
                                        && !empty($sectionResult['SECTION_PAGE_URL'])){

                                        $redirectUrl = $sectionResult['SECTION_PAGE_URL'];
                                    }

                                }
                            }

                            if(!empty($redirectUrl)){

                                if(isset($_REQUEST["bxajaxid"])
                                    && $_REQUEST["bxajaxid"] == 'smart_filter'){
                                    ?>
                                    <script>
                                        location.href = '<?=$redirectUrl;?>';
                                    </script>
                                    <?
                                } else {
                                    LocalRedirect($redirectUrl);
                                }

                            }

                        }

                    }

            }

            if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){
                die();
            }

            $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;
            unset($basketAction);

            ?>
        </div>
    </div>
<?php

ob_start();

$arFields  = array();

if($filter_set){

    $currPage = $APPLICATION->GetCurPage();

    $obCache = new CPHPCache;
    $cacheID = 'infoFilterSection'.md5($currPage);

    if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

        $tmp = array();
        $tmp = $obCache->GetVars();

        if(isset($tmp[$cacheID])){
            $arFields = $tmp[$cacheID];
        }

    } else {


        $sectionabout = array();

        $currPage = trim($currPage);

        $arSelect = Array(
            'ID',
            'NAME',
            'PROPERTY_SEO_TITLE',
            'PROPERTY_SEO_DECRIPTION',
            'PROPERTY_SEO_KEYWORDS',
            'PREVIEW_TEXT',
            'IBLOCK_TYPE_ID',
            'IBLOCK_ID'
        );

        $arFilter = Array(
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_FILTER_URL" => $currPage,
            "IBLOCK_ID" => 15);

        $res = CIBlockElement::GetList(
            Array(),
            $arFilter,
            false,
            false,
            $arSelect
        );

        if( $res &&
            $ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();
        }

        if($obCache->StartDataCache()){

            $obCache->EndDataCache(
                array(
                    $cacheID => $arFields
                )
            );

        };

    };

    if(!empty($arFields)){

        $APPLICATION->IncludeComponent(
            "bitrix:news.detail",
            "section",
            array(
                "DISPLAY_DATE" => "Y",
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => "Y",
                "DISPLAY_PREVIEW_TEXT" => "Y",
                "USE_SHARE" => "N",
                "AJAX_MODE" => "N",
                "IBLOCK_TYPE" => $arFields['IBLOCK_TYPE_ID'],
                "IBLOCK_ID" => $arFields['IBLOCK_ID'],
                "ELEMENT_ID" => $arFields['ID'],
                "ELEMENT_CODE" => "",
                "CHECK_DATES" => "Y",
                "FIELD_CODE" => array(
                    0 => "NAME",
                    1 => "PREVIEW_PICTURE",
                    2 => "PREVIEW_TEXT",
                ),
                "PROPERTY_CODE" => array(
                    0 => "ADDITIONAL_LINK",
                    1 => "H1_BOTTOM",
                ),
                "IBLOCK_URL" => "",
                "META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? ("SEO_KEYWORDS") : '',
                "META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? ("SEO_DECRIPTION") : '',
                "BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? ("SEO_TITLE") : '',
                "SET_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                "SET_STATUS_404" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "USE_PERMISSIONS" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_NOTES" => "",
                "CACHE_GROUPS" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "Страница",
                "PAGER_TEMPLATE" => "",
                "PAGER_SHOW_ALL" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "SET_BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                "SET_META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? "Y" : "N",
                "SET_META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? "Y" : "N",
                "ADD_ELEMENT_CHAIN" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "COMPONENT_TEMPLATE" => "section",
                "DETAIL_URL" => "",
                "SET_CANONICAL_URL" => "N",
                "SET_LAST_MODIFIED" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
                "IMAGE_THUMB_WIDTH" => "870",
                "IMAGE_THUMB_HEIGHT" => ""
            ),
            false
        );

    } else {

        $skip_tmpl_check = isset($active_filters['=PROPERTY_46'])
        &&is_array($active_filters['=PROPERTY_46'])
        &&sizeof($active_filters['=PROPERTY_46']) > 1
            ? true
            : false;

        $skip_tmpl_check = false;

        $is_manufacturer = isset($active_filters['=PROPERTY_44'])
        &&is_array($active_filters['=PROPERTY_44'])
        &&sizeof($active_filters['=PROPERTY_44']) > 1
        &&!isset($active_filters['=PROPERTY_46'])
            ? true
            : false;

        $filter_parameter = array();
        $filter_parameter_sizeof = \COption::GetOptionString('my.stat', "filter_parameter_sizeof", 0, SITE_ID);

        if($filter_parameter_sizeof > 0){
            for($i = 0; $i < $filter_parameter_sizeof; $i ++){

                $filter_parameter['code'][$i] = \COption::GetOptionString('my.stat', "filter_parameter_id".$i, "", SITE_ID);
                $filter_parameter['value'][$i] = \COption::GetOptionString('my.stat', "filter_parameter_value".$i, "", SITE_ID);

            }
        }

        $currentURL = $APPLICATION->GetCurPage();
        $currentURL = trim($currentURL);

        $for_union_sections = '';
        $for_union_sections_nc = '';

        $keys = array();
        $keys_nc = array();
        $keys_tmpl = array();

        $arByFilter = array('keys' => '', 'keys_nc' => '',  'section' => '', 'section_nc' => '');

        $categoryPath = $filterURL = preg_replace('~(.*)filter.*$~isu',"$1",$currentURL);

        $filters_glue_description = \COption::GetOptionString('my.stat', 'filters_glue_description', ' ', SITE_ID);
        $prevStartFrom = '';

        if(mb_stripos($currentURL,'filter/') !== false){

            $obCache = new CPHPCache;
            $cacheID = 'infoFiltersSection'.md5($currPage);

            if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

                $tmp = array();
                $tmp = $obCache->GetVars();

                if(isset($tmp[$cacheID])){
                    $arByFilter = $tmp[$cacheID];
                }


            } else {

                $filterURL = preg_replace('~.*filter/(.*)$~isu',"$1",$currentURL);
                $filterURL = trim($filterURL);

                if(!empty($filterURL)){

                    if(mb_stripos($filterURL,'/') !== false){

                        $filterURLs = explode('/',$filterURL);

                    } else {
                        $filterURLs = array($filterURL);
                    }

                    foreach($filterURLs as $smartPart){

                        $smartPart = preg_split("/-(is|or)-/", $smartPart, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $startParts = false;

                        if(is_array($smartPart) && sizeof($smartPart) > 0){
                            $startFrom = $smartPart[0];

                            foreach($smartPart as $smartElement){

                                if($smartElement == 'is'){
                                    $startParts = true;
                                }

                                if($startParts
                                    && $smartElement != 'or'
                                    && $smartElement != 'is'){

                                    $checkFilter = $categoryPath.'filter/'.$startFrom.'-is-'.$smartElement.'/';

                                    if(($skip_tmpl_check && $startFrom == 'typeproduct')
                                        || !$skip_tmpl_check){

                                        $checkFilter = trim($checkFilter);

                                        $arSelect = Array(
                                            'PROPERTY_FOR_UNION_FILTERS',
                                            'PROPERTY_FOR_UNION_FILTERS_NC'
                                        );

                                        $arFilter = Array(
                                            "ACTIVE_DATE" => "Y",
                                            "ACTIVE" => "Y",
                                            "PROPERTY_FILTER_URL" => $checkFilter,
                                            "IBLOCK_ID" => 15);

                                        $res = CIBlockElement::GetList(
                                            Array(),
                                            $arFilter,
                                            false,
                                            false,
                                            $arSelect
                                        );

                                        if(!isset($keys[$startFrom])){
                                            $keys[$startFrom] = array();
                                        }

                                        if(!isset($keys_nc[$startFrom])){
                                            $keys_nc[$startFrom] = array();
                                        }

                                        if(!isset($keys_tmpl[$startFrom])) {
                                            $keys_tmpl[$startFrom] = '';
                                        }

                                        if($res){

                                            $arFields = $res->GetNext();

                                            $dValue = '';

                                            if(!empty($arFields["PROPERTY_FOR_UNION_FILTERS_VALUE"])){

                                                $keys[$startFrom][] = $arFields["PROPERTY_FOR_UNION_FILTERS_VALUE"];
                                                $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && $prevStartFrom != 'typeproduct' && $startFrom != 'typeproduct' ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')).'[values]';

                                            } else {

                                                if(empty($dValue)){

                                                    $peDB = CIBlockPropertyEnum::GetList(
                                                        Array(
                                                            "DEF" => "DESC",
                                                            "SORT" => "ASC"),
                                                        Array(
                                                            "XML_ID" => trim($smartElement),
                                                            "CODE" => trim($startFrom)

                                                        )
                                                    );

                                                    if($peDB && ($peArr = $peDB->GetNext())){
                                                        $dValue = isset($peArr["VALUE"]) ? trim($peArr["VALUE"]) : '';
                                                    }

                                                    $keys[$startFrom][] = $dValue;

                                                }

                                            }

                                            if(!empty($arFields["PROPERTY_FOR_UNION_FILTERS_NC_VALUE"])) {

                                                $keys_nc[$startFrom][] = $arFields["PROPERTY_FOR_UNION_FILTERS_NC_VALUE"];
                                                $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && $prevStartFrom != 'typeproduct' && $startFrom != 'typeproduct' ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')).'[values]';

                                            } else {

                                                if(empty($dValue)){

                                                    $peDB = CIBlockPropertyEnum::GetList(
                                                        Array(
                                                            "DEF" => "DESC",
                                                            "SORT" => "ASC"),
                                                        Array(
                                                            "XML_ID" => trim($smartElement),
                                                            "CODE" => trim($startFrom)

                                                        )
                                                    );

                                                    if($peDB && ($peArr = $peDB->GetNext())){
                                                        $dValue = isset($peArr["VALUE"]) ? trim($peArr["VALUE"]) : '';
                                                    }

                                                    $keys_nc[$startFrom][] = $dValue;

                                                }

                                            }

                                            if(!$skip_tmpl_check){

                                                $key_found = array_search($startFrom,$filter_parameter['code']);
                                                if($key_found !== false){
                                                    $keys_tmpl[$startFrom] = (!empty($prevStartFrom) && $prevStartFrom != 'typeproduct' && $startFrom != 'typeproduct' ? $filters_glue_description : (!empty($prevStartFrom) ? ' ' : '')).$filter_parameter['value'][$key_found];
                                                }

                                            }

                                        }



                                    }

                                }

                            }

                        }

                        $prevStartFrom = $startFrom;

                    }

                }

                if(!empty($keys)
                    && !empty($intSectionID)){

                    $sectionResult = CIBlockSection::GetList(
                        array(
                            "SORT" =>"ASC"
                        ),
                        array(
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "ID" => (int)$intSectionID
                        ),
                        false,
                        $arSelect = array("UF_DESC_RU")
                    );

                    if(is_object($sectionResult)
                        && method_exists($sectionResult,"GetNext")){

                        while ($sectionProp = $sectionResult->GetNext()){

                            $product_id = false;

                            if(isset($sectionProp["UF_DESC_RU"])
                                && !empty($sectionProp["UF_DESC_RU"])){
                                $product_id = (int)$sectionProp["UF_DESC_RU"];
                            }


                            if($product_id
                                && !empty($product_id)){

                                $dbBlockResult = CIBlockElement::GetProperty(
                                    15,
                                    $product_id,
                                    Array(),
                                    Array(
                                        "CODE" => "FOR_UNION_SECTIONS"
                                    )
                                );

                                if($dbBlockResult){

                                    while($arBlockResult = $dbBlockResult->Fetch()){

                                        $for_union_sections = trim($arBlockResult["VALUE"]);

                                    };

                                };

                                $dbBlockResult = CIBlockElement::GetProperty(
                                    15,
                                    $product_id,
                                    Array(),
                                    Array(
                                        "CODE" => "FOR_UNION_SECTIONS_NC"
                                    )
                                );

                                if($dbBlockResult){

                                    while($arBlockResult = $dbBlockResult->Fetch()){

                                        $for_union_sections_nc = trim($arBlockResult["VALUE"]);

                                    };

                                };


                            }

                        }

                    }

                    $arByFilter = array('keys' => $keys, 'keys_nc' => $keys_nc, 'section' => $for_union_sections, 'section_nc' => $for_union_sections_nc, 'keys_tmpl' => $keys_tmpl);

                }


                if($obCache->StartDataCache()){

                    $obCache->EndDataCache(
                        array(
                            $cacheID => $arByFilter
                        )
                    );

                };

            }

        }

        if(!empty($arByFilter['keys'])){

            $filter_title = \COption::GetOptionString('my.stat', 'filter_title', '', SITE_ID);
            $filter_h1 = \COption::GetOptionString('my.stat', 'filter_h1', '', SITE_ID);
            $filter_description = \COption::GetOptionString('my.stat', 'filter_description', '', SITE_ID);
            $filter_keywords = \COption::GetOptionString('my.stat', 'filter_keywords', '', SITE_ID);

            if($is_manufacturer){

                $filter_title = \COption::GetOptionString('my.stat', 'filter_manufacturer_title', '', SITE_ID);
                $filter_h1 = \COption::GetOptionString('my.stat', 'filter_manufacturer_h1', '', SITE_ID);
                $filter_description = \COption::GetOptionString('my.stat', 'filter_manufacturer_description', '', SITE_ID);
                $filter_keywords = \COption::GetOptionString('my.stat', 'filter_manufacturer_keywords', '', SITE_ID);

            }

            $page_num = $APPLICATION->GetPageProperty('pagenum', '');

            if($page_num){

                $filter_title = \COption::GetOptionString('my.stat', 'pagenav_filter_title', '', SITE_ID);

                if($is_manufacturer){

                    $filter_title = \COption::GetOptionString('my.stat', 'pagenav_filter_manufacturer_title', '', SITE_ID);

                }

                $filter_title = str_ireplace('[pagenum]',$page_num,$filter_title);

                $filter_description = $pagenav_description = \COption::GetOptionString('my.stat', 'pagenav_filter_description', '', SITE_ID);

                if($is_manufacturer){

                    $filter_description = $pagenav_description = \COption::GetOptionString('my.stat', 'pagenav_filter_manufacturer_description', '', SITE_ID);

                }

                $filter_description = str_ireplace('[pagenum]',$page_num,$filter_description);

            }

            $keys = '';
            $keys_nc = '';
            $manufacturers = '';
            $manufacturers_nc = '';

            $keys_tmpl = $arByFilter['keys_tmpl'];


            if(isset($keys_tmpl['manufacturer'])){

                $startFrom = 'manufacturer';

                $keys_value = $keys_tmpl[$startFrom];

                unset($keys_tmpl[$startFrom]);

                $arByFilter['keys'][$startFrom] = array_filter($arByFilter['keys'][$startFrom],function($var){
                    return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                });

                $ckeys = join(', ',array_unique($arByFilter['keys'][$startFrom]));
                $ckeys = str_ireplace('[values]',$ckeys,$keys_value);
                $manufacturers = $ckeys;

                $arByFilter['keys_nc'][$startFrom] = empty($arByFilter['keys_nc'][$startFrom]) ? $arByFilter['keys'][$startFrom] : $arByFilter['keys_nc'][$startFrom];

                $arByFilter['keys_nc'][$startFrom] = array_filter($arByFilter['keys_nc'][$startFrom],function($var){
                    return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                });

                $ckeys_nc = join(', ',array_unique($arByFilter['keys_nc'][$startFrom]));
                $ckeys_nc = str_ireplace('[values]',$ckeys_nc,$keys_value);
                $manufacturers_nc = $ckeys_nc;

            }

            foreach($keys_tmpl as $startFrom => $keys_value){

                $arByFilter['keys'][$startFrom] = array_filter($arByFilter['keys'][$startFrom],function($var){
                    return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                });

                $ckeys = join(', ',array_unique($arByFilter['keys'][$startFrom]));
                $ckeys = str_ireplace('[values]',$ckeys,$keys_value);
                $keys .= $ckeys;

                $arByFilter['keys_nc'][$startFrom] = empty($arByFilter['keys_nc'][$startFrom]) ? $arByFilter['keys'][$startFrom] : $arByFilter['keys_nc'][$startFrom];

                $arByFilter['keys_nc'][$startFrom] = array_filter($arByFilter['keys_nc'][$startFrom],function($var){
                    return in_array(mb_strtolower($var),array('да','нет')) ? false : true;
                });

                $ckeys_nc = join(', ',array_unique($arByFilter['keys_nc'][$startFrom]));
                $ckeys_nc = str_ireplace('[values]',$ckeys_nc,$keys_value);
                $keys_nc .= $ckeys_nc;

            }

            $keys = mb_strtolower($keys);
            $keys_nc = mb_strtolower($keys_nc);
            $manufacturers = mb_strtolower($manufacturers);
            $manufacturers_nc = mb_strtolower($manufacturers_nc);

            $filter_title = str_ireplace('[manufacturers]', $manufacturers, $filter_title);
            $filter_h1 = str_ireplace('[manufacturers]', $manufacturers, $filter_h1);
            $filter_description = str_ireplace('[manufacturers]', $manufacturers, $filter_description);
            $filter_keywords = str_ireplace('[manufacturers]', $manufacturers, $filter_keywords);

            $filter_title = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_title);
            $filter_h1 = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_h1);
            $filter_description = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_description);
            $filter_keywords = str_ireplace('[manufacturers_nc]', $manufacturers_nc, $filter_keywords);


            $filter_title = str_ireplace('[keys]', $keys, $filter_title);
            $filter_h1 = str_ireplace('[keys]', $keys, $filter_h1);
            $filter_description = str_ireplace('[keys]', $keys, $filter_description);
            $filter_keywords = str_ireplace('[keys]', $keys, $filter_keywords);

            $filter_title = str_ireplace('[keys_nc]', $keys_nc, $filter_title);
            $filter_h1 = str_ireplace('[keys_nc]', $keys_nc, $filter_h1);
            $filter_description = str_ireplace('[keys_nc]', $keys_nc, $filter_description);
            $filter_keywords = str_ireplace('[keys_nc]', $keys_nc, $filter_keywords);

            $filter_title = str_ireplace('[section]', $arByFilter['section'], $filter_title);
            $filter_h1 = str_ireplace('[section]', $arByFilter['section'], $filter_h1);
            $filter_description = str_ireplace('[section]', $arByFilter['section'], $filter_description);
            $filter_keywords = str_ireplace('[section]', $arByFilter['section'], $filter_keywords);

            $filter_title = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_title);
            $filter_h1 = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_h1);
            $filter_description = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_description);
            $filter_keywords = str_ireplace('[section_nc]', $arByFilter['section_nc'], $filter_keywords);


            if(!empty($filter_keywords))
                $APPLICATION->SetPageProperty("keywords", $filter_keywords);

            if(!empty($filter_description))
                $APPLICATION->SetPageProperty("description", $filter_description);

            if(!empty($filter_title))
                $APPLICATION->SetTitle($filter_title);

            if(!empty($filter_title))
                $APPLICATION->SetPageProperty("title", $filter_title);

            if(!empty($filter_h1)){
                ?>
                <div class="section-bottom-info row">
                    <h1 class="down-page">
                        <?=$filter_h1;?>
                    </h1>
                </div>
                <?php
            }


            if(!empty($filter_keywords)
                || !empty($filter_h1)
                || !empty($filter_title)
                || !empty($filter_description)){
                $arFields['set_filters'] = true;
            };
        }

    }

};

if(empty($arFields) && isset($intSectionID) && !empty($intSectionID)){

    $obCache = new CPageCache;

    $cacheID = 'sectionabout'.$arParams["IBLOCK_ID"].'.'.$intSectionID;

    $sectionabout = array();

    if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

        $obCache->Output();

    } else {

        if($obCache->StartDataCache($cacheTime, $cacheID, "/impel/")){

            $sectionResult = CIBlockSection::GetList(
                array(
                    "SORT" =>"ASC"
                ),
                array(
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ID" => (int)$intSectionID
                ),
                false,
                $arSelect = array("UF_DESC_RU")
            );

            if(is_object($sectionResult)
                && method_exists($sectionResult,"GetNext")){

                while ($sectionProp = $sectionResult->GetNext()){

                    $product_id = false;


                    if(isset($sectionProp["UF_DESC_RU"])
                        && !empty($sectionProp["UF_DESC_RU"])){
                        $product_id = (int)$sectionProp["UF_DESC_RU"];
                    }


                    if($product_id && !empty($product_id)){

                        $infodb = CIBlockElement::GetByID($product_id);

                        if(is_object($infodb)
                            && method_exists($infodb,"GetNext")){

                            while ($arFields = $infodb->GetNext()){

                                $APPLICATION->IncludeComponent(
                                    "bitrix:news.detail",
                                    "section",
                                    array(
                                        "DISPLAY_DATE" => "Y",
                                        "DISPLAY_NAME" => "Y",
                                        "DISPLAY_PICTURE" => "Y",
                                        "DISPLAY_PREVIEW_TEXT" => "Y",
                                        "USE_SHARE" => "N",
                                        "AJAX_MODE" => "N",
                                        "IBLOCK_TYPE" => $arFields['IBLOCK_TYPE_ID'],
                                        "IBLOCK_ID" => $arFields['IBLOCK_ID'],
                                        "ELEMENT_ID" => $arFields['ID'],
                                        "ELEMENT_CODE" => "",
                                        "CHECK_DATES" => "Y",
                                        "FIELD_CODE" => array(
                                            0 => "NAME",
                                            1 => "PREVIEW_PICTURE",
                                            2 => "PREVIEW_TEXT",
                                        ),
                                        "PROPERTY_CODE" => array(
                                            0 => "ADDITIONAL_LINK",
                                            1 => "H1_BOTTOM",
                                        ),
                                        "IBLOCK_URL" => "",
                                        "META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? ("SEO_KEYWORDS") : '',
                                        "META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? ("SEO_DECRIPTION") : '',
                                        "BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? ("SEO_TITLE") : '',
                                        "SET_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                                        "SET_STATUS_404" => "N",
                                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                        "ADD_SECTIONS_CHAIN" => "N",
                                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                        "USE_PERMISSIONS" => "N",
                                        "CACHE_TYPE" => "A",
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_NOTES" => "",
                                        "CACHE_GROUPS" => "Y",
                                        "DISPLAY_TOP_PAGER" => "N",
                                        "DISPLAY_BOTTOM_PAGER" => "Y",
                                        "PAGER_TITLE" => "Страница",
                                        "PAGER_TEMPLATE" => "",
                                        "PAGER_SHOW_ALL" => "Y",
                                        "AJAX_OPTION_JUMP" => "N",
                                        "AJAX_OPTION_STYLE" => "Y",
                                        "AJAX_OPTION_HISTORY" => "N",
                                        "SET_BROWSER_TITLE" => isset($arFields["PROPERTY_SEO_TITLE_VALUE"]) && !empty($arFields["PROPERTY_SEO_TITLE_VALUE"]) ? "Y" : "N",
                                        "SET_META_KEYWORDS" => isset($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) && !empty($arFields["PROPERTY_SEO_KEYWORDS_VALUE"]) ? "Y" : "N",
                                        "SET_META_DESCRIPTION" => isset($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) && !empty($arFields["PROPERTY_SEO_DECRIPTION_VALUE"]) ? "Y" : "N",
                                        "ADD_ELEMENT_CHAIN" => "N",
                                        "AJAX_OPTION_ADDITIONAL" => "",
                                        "COMPONENT_TEMPLATE" => "section",
                                        "DETAIL_URL" => "",
                                        "SET_CANONICAL_URL" => "N",
                                        "SET_LAST_MODIFIED" => "N",
                                        "PAGER_BASE_LINK_ENABLE" => "N",
                                        "SHOW_404" => "N",
                                        "MESSAGE_404" => "",
                                        "IMAGE_THUMB_WIDTH" => "870",
                                        "IMAGE_THUMB_HEIGHT" => ""
                                    ),
                                    false
                                );

                            }

                        }

                    }

                }

            }

            $obCache->EndDataCache();
        };

    };

    $currPage = $APPLICATION->GetCurPage();
    $obCache = new CPHPCache;
    $cacheID = 'infoPagenSection'.md5($currPage);

    if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

        $tmp = array();
        $tmp = $obCache->GetVars();

        if(isset($tmp[$cacheID])){
            $cacheResults = $tmp[$cacheID];

            $pag_title = $cacheResults['pag_title'];
            $pag_description = $cacheResults['pag_description'];

        }

    } else {

        $sectionResult = CIBlockSection::GetList(
            array(
                "SORT" =>"ASC"
            ),
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ID" => (int)$intSectionID
            ),
            false,
            $arSelect = array("UF_DESC_RU")
        );

        if(is_object($sectionResult)
            && method_exists($sectionResult,"GetNext")) {

            while ($sectionProp = $sectionResult->GetNext()) {

                $product_id = false;


                if (isset($sectionProp["UF_DESC_RU"])
                    && !empty($sectionProp["UF_DESC_RU"])
                ) {
                    $product_id = (int)$sectionProp["UF_DESC_RU"];
                }


                if ($product_id && !empty($product_id)) {

                    $infodb = CIBlockElement::GetList(
                        array(),
                        array('ID' => $product_id),
                        false,
                        false,
                        array(
                            'PROPERTY_SEO_DECRIPTION_PAGEN',
                            'PROPERTY_SEO_TITLE_PAGEN')
                    );

                    if (is_object($infodb)
                        && method_exists($infodb, "GetNext")
                    ){

                        while ($arFields = $infodb->GetNext()) {

                            $pag_title = $arFields['PROPERTY_SEO_TITLE_PAGEN_VALUE'];
                            $pag_description = $arFields['PROPERTY_SEO_DECRIPTION_PAGEN_VALUE'];

                        }

                    }

                }

            }

        }

        $cacheResults = array(
            'pag_title' => $pag_title,
            'pag_description' => $pag_description
        );

        if($obCache->StartDataCache()){

            $obCache->EndDataCache(
                array(
                    $cacheID => $cacheResults
                )
            );

        };

    }

};

$SEO_TEXT = ob_get_clean();
$SEO_TEXT = trim($SEO_TEXT);

$APPLICATION->SetPageProperty('SEO_TEXT', $SEO_TEXT);

if(!($page_num
    && $filter_set)){

    $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title','');

    if(!empty($pagenav_title)){

        if(!empty($pag_title)) {

            if (mb_stripos($pagenav_title, '[pag_title]') !== false) {
                $pagenav_title = str_ireplace('[pag_title]', $pag_title, $pagenav_title);
            }

        } else {

            $pagenav_title = $APPLICATION->GetPageProperty('pagenav_title_default','');

            if(mb_stripos($pagenav_title, '[title]') !== false){

                if(!empty($APPLICATION->GetPageProperty('title',''))){
                    $pagenav_title = str_ireplace('[title]',$APPLICATION->GetPageProperty('title',''),$pagenav_title);
                } else {
                    $pagenav_title = str_ireplace('[title]',$APPLICATION->GetTitle(),$pagenav_title);
                }

            }

        }

        if(!empty($pagenav_title))
            $APPLICATION->SetTitle($pagenav_title);

        if(!empty($pagenav_title))
            $APPLICATION->SetPageProperty("title", $pagenav_title);

        $APPLICATION->SetPageProperty('pagenav_title', '');
        $APPLICATION->SetPageProperty('pagenav_title_default', '');

    }


    $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description','');


    if(!empty($pagenav_description)){

        if(!empty($pag_description)){

            if(mb_stripos($pagenav_description, '[pag_description]') !== false){
                $pagenav_description = str_ireplace('[pag_description]', $pag_description, $pagenav_description);
            }

        } else {

            $pagenav_description = $APPLICATION->GetPageProperty('pagenav_description_default','');

            if(mb_stripos($pagenav_description, '[description]') !== false) {
                $pagenav_description = str_ireplace('[description]', $APPLICATION->GetPageProperty('description', ''), $pagenav_description);
            }

        }

        if(!empty($pagenav_description))
            $APPLICATION->SetPageProperty("description", $pagenav_description);

        $APPLICATION->SetPageProperty('pagenav_description', '');
        $APPLICATION->SetPageProperty('pagenav_description_default', '');

    }

}


$page_num = $APPLICATION->GetPageProperty('pagenum', '');

$APPLICATION->SetPageProperty('pagenum', '');

if(isset($arResult['VARIABLES'])
    && isset($arResult['VARIABLES']['SECTION_CODE'])
    && !empty($arResult['VARIABLES']['SECTION_CODE'])){

    $filterPath = '';

    if(mb_stripos($APPLICATION->GetCurDir(),'/filter/') !== false){
        $filterPath = $APPLICATION->GetCurDir();
        $filterPath = preg_replace('~.*?/filter/~is','/filter/',$filterPath);
    }

    if($page_num){
        $filterPath .= 'pages-'.$page_num.'/';
    }

    if(!empty($filterPath))
        $filterPath = ltrim($filterPath,'/');

    //$filterPath = mb_stripos($filterPath,'-or-') === false ? $filterPath : '';

    //$APPLICATION->AddHeadString('<link rel="amphtml" href="'.(IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/sections/' . $arResult['VARIABLES']['SECTION_CODE'] . '/' . $filterPath . '" />'));



} else {

    $filterPath = '';

   // $APPLICATION->AddHeadString('<link rel="amphtml" href="'.(IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/amp/sections/'. $filterPath .'" />'));

}
?>