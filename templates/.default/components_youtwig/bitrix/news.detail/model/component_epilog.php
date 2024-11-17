<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(change_to_mobile){
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.css');
}

$isVersion       = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&& $arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"] == 'Да'
    ? true
    : false;

$indCode = '';
$image_thumb_width = 253;
$image_thumb_height = 253;

$image_width = 847;
$image_height = 635;

$dreplaces = array(
    '[product_type_dec]' => '',
    '[product_type]' => '',
    '[brand]' => '',
    '[model]' => '',
    '[indcode]' => ''
);

if(!$isVersion){

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_models', array(), SITE_ID));

} else {

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_series_models', array(), SITE_ID));

}

$products       = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["products"])
&&isset($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
    : '';

$curPage = $APPLICATION->GetCurPage();

if(preg_match('~/indcode/([^/]+?)/~isu',$curPage,$matches) && !$isVersion){

    if(isset($matches[1]) && !empty($matches[1])){
        $curIndcode = trim($matches[1]);

        foreach($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"]
                as $productNum => $productName){

            $productName = trim(strip_tags($productName));

            $trParams = Array(
                "max_len" => "100",
                "change_case" => "L",
                "replace_space" => "_",
                "replace_other" => "_",
                "delete_repeat_replace" => "true",
            );

            $productCode = trim(CUtil::translit(trim(strip_tags($productName)), LANGUAGE_ID, $trParams));

            if($productCode != $curIndcode){
                unset($products[$productNum]);
            } else {
                $dreplaces['[indcode]'] = trim(strip_tags($productName));
                $indCode = trim(strip_tags($productName));
            }

        }

    }

}

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"])
    &&isset($declension_models["declension"])
    &&is_array($declension_models["declension"])
    &&sizeof($declension_models["declension"])){

    foreach($declension_models["type_of_product"] as $dnumber => $typeID){

        if($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE_ENUM_ID"] == $typeID
            &&isset($declension_models["declension"][$dnumber])
            &&trim($declension_models["declension"][$dnumber]) != ""
        ){
            $dreplaces['[product_type_dec]'] = trim($declension_models["declension"][$dnumber]);
        }
    }

}

$product_type = '';

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])){

    $product_type = $dreplaces['[product_type]'] = $arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"];

}

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])){

    $dreplaces['[brand]'] = $arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"];

}

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])){

    $dreplaces['[model]'] = ($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"]);

}

if(!$isVersion){

    $models_keywords = \COption::GetOptionString('my.stat', 'models_keywords', '', SITE_ID);
    $models_description = \COption::GetOptionString('my.stat', 'models_description', '', SITE_ID);
    $models_title = \COption::GetOptionString('my.stat', 'models_title', '', SITE_ID);

} else {

    $models_keywords = \COption::GetOptionString('my.stat', 'models_version_keywords', '', SITE_ID);
    $models_description = \COption::GetOptionString('my.stat', 'models_version_description', '', SITE_ID);
    $models_title = \COption::GetOptionString('my.stat', 'models_version_title', '', SITE_ID);

}

$models_keywords = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_keywords);
$models_description = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_description);
$models_title = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_title);

$APPLICATION->SetPageProperty('page_keywords', $models_keywords);
$APPLICATION->SetPageProperty('page_title', $models_title);
$APPLICATION->SetPageProperty('page_description', $models_description);

$manufacturer = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"])
&&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
    ? trim($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
    : '';

$model_new = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_new"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
    ? trim($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
    : '';

$model_name = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
&&!($manufacturer == 'Whirlpool'
    && $product_type == 'Стиральная машина'
)
    ? ($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
    : '';

$positions = array();

$views = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VIEW"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"])
    : array();

$basePage = $APPLICATION->GetCurPage();
$hasFilter = mb_stripos($basePage,'/filter/') !== false ? true : false;

if($hasFilter && $isVersion){

    CHTTP::SetStatus("404 Not Found");
}


?>
    <div class="<?=!$isVersion ? 'col-md-9' : 'series';?> col-sm-12 col-xs-12 products-list-info">
        <?
        if(!empty($views)){

            $hasViews = array();

            if(preg_match('~/view/[^/]+?/~', $basePage)){
                $basePage = preg_replace('~/view/[^/]+?/~','/',$basePage);
            }

            $basePage = '/'.trim($basePage,'/').'/';

            if(mb_stripos($basePage, '/filter/') === false && $hasFilter){
                $basePage .= 'filter/';
            }

            $basePage = str_ireplace('/filter/clear/','/',$basePage);

            $trParams = Array(
                "max_len" => "100",
                "change_case" => "L",
                "replace_space" => "_",
                "replace_other" => "_",
                "delete_repeat_replace" => "true",
            );

            ?>
            <div class="views row">
                <?

                foreach($views as $viewNum => $viewId){

                    if(($viewId == $arResult['skipViewId'])
                        || (in_array($viewId,$hasViews)))
                        continue;

                    $hasViews[] = $viewId;

                    $dbRes = CIBlockElement::GetByID($viewId);

                    if($dbRes){

                        $dbArr = $dbRes->GetNext();

                        $preview_picture = isset($dbArr["PREVIEW_PICTURE"])
                        &&!empty($dbArr["PREVIEW_PICTURE"])
                            ? $dbArr["PREVIEW_PICTURE"]
                            : '';

                        if(is_numeric($preview_picture)){
                            $preview_picture = CFile::GetPath($preview_picture);
                        }

                        $preview_picture = rectangleImage(
                            $_SERVER['DOCUMENT_ROOT'].$preview_picture,
                            $image_thumb_width,
                            $image_thumb_height,
                            $preview_picture,
                            '#ffffff'
                        );

                        $code = trim(CUtil::translit(trim(strip_tags($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"][$viewNum])), LANGUAGE_ID, $trParams));

                        if(mb_stripos($basePage,'/filter/') !== false){

                            $parts = explode('/filter/',$basePage,2);
                            $linkPage = rtrim($parts[0],'/').'/view/'.$code.'/';


                            if(!empty($parts[1])){
                                $linkPage .= 'filter/'.trim($parts[1],'/').'/';
                            }

                        } else {

                            $linkPage = '/'.trim($basePage,'/').'/view/'.$code.'/';

                        }

                        $curView = '';

                        if(preg_match('~/view/([^/]+?)/~isu',$curPage,$matches)) {

                            if (isset($matches[1]) && !empty($matches[1])) {
                                $curView = trim($matches[1]);
                            }
                        }

                        if(!empty($preview_picture)){
                            ?>
                            <div class="col-lg-3 col-md-6">
                                <a href="<?php if($curView == $code): ?><?=$basePage;?><?php else: ?><?=$linkPage;?><?php endif; ?>" id="view<?=$viewId;?>">
                                    <span class="hide-view"><?php if($curView == $code): ?>-<?php else: ?>+<?php endif; ?></span>
                                    <img src="<?=$preview_picture;?>" class="img-responsive" />
                                </a>
                            </div>
                            <?
                        }

                    }

                }


                ?>
            </div>
            <?
            $hasBigImage = false;

            if(preg_match('~/view/([^/]+?)/~isu',$curPage,$matches) && !$isVersion){

                if(isset($matches[1]) && !empty($matches[1])){
                    ?>
                    <div class="view-preview row" id="view-preview">
                    <a href="<?=$basePage;?>" class="hide-view">
                        -
                    </a>
                    <?
                    $curView = trim($matches[1]);

                    foreach($arResult["DISPLAY_PROPERTIES"]["VIEW"]["DISPLAY_VALUE"]
                            as $productNum => $productName){

                        $productName = trim(strip_tags($productName));

                        $trParams = Array(
                            "max_len" => "100",
                            "change_case" => "L",
                            "replace_space" => "_",
                            "replace_other" => "_",
                            "delete_repeat_replace" => "true",
                        );

                        $productCode = trim(CUtil::translit(trim(strip_tags($productName)), LANGUAGE_ID, $trParams));

                        if($curView == $productCode){

                            $dbRes = CIBlockElement::GetByID($arResult["DISPLAY_PROPERTIES"]["VIEW"]["VALUE"][$productNum]);

                            if($dbRes){

                                $dbArr = $dbRes->GetNext();

                                $preview_picture = isset($dbArr["PREVIEW_PICTURE"])
                                &&!empty($dbArr["PREVIEW_PICTURE"])
                                    ? $dbArr["PREVIEW_PICTURE"]
                                    : '';

                                if(is_numeric($preview_picture)){
                                    $preview_picture = CFile::GetPath($preview_picture);
                                }

                                $preview_picture = rectangleImage(
                                    $_SERVER['DOCUMENT_ROOT'].$preview_picture,
                                    $image_width,
                                    $image_height,
                                    $preview_picture,
                                    '#ffffff'
                                );

                                if(!$hasBigImage){
                                    ?>
                                    <img src="<?=$preview_picture;?>" class="img-responsive" />
                                    <?
                                    $hasBigImage = true;

                                }

                            }

                        } else {
                            unset($products[$productNum]);
                        }

                    }

                }
                ?>
                </div>
                <p id="hide-scheme" class="hide-scheme">
                    <a href="<?=$basePage;?>">
                    <span>
                        <?=GetMessage('TPL_HIDE_SHCEME');?>
                    </span>
                    </a>
                </p>
                <?

            }

            if($hasBigImage && !empty($products)){

                foreach($products as $productNum => $productId){

                    if(isset($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"][$productNum])){

                        $position = trim($arResult["DISPLAY_PROPERTIES"]["POSITION"]["VALUE"][$productNum]);

                        if(!empty($position)
                            && $position != '-'
                            && $position != ' '){
                            $positions[$productId] = $position;
                        }

                    }

                }

            }

        }

        if(!empty($products)){

            global $arrFilter;

            $basePage = $APPLICATION->GetCurPage();

            if(mb_stripos($basePage,'/view/') === false
                && mb_stripos($basePage,'/indcode/') === false){
                $products = array_unique($products);
            }

            $arrFilter["=ID"] = $products;

            $filterPath = preg_replace('~/filter/.*$~i','/',$APPLICATION->GetCurDir());
            $_REQUEST["SMART_FILTER_PATH"] = preg_replace('~^.*?/filter/~i','/',$APPLICATION->GetCurDir());

            if(!$isVersion) {

                $APPLICATION->IncludeComponent(
                    "impel:catalog.smart.filter",
                    "horizontal",
                    array(
                        'SHOW_ALL_WO_SECTION' => 'Y',
                        'IBLOCK_TYPE' => "catalog",
                        'IBLOCK_ID' => 11,
                        'FILTER_NAME' => 'arrFilter',
                        'PRICE_CODE' =>
                            array(
                                0 => 'Розничная',
                            ),
                        'CACHE_TYPE' => 'A',
                        'CACHE_TIME' => '3600',
                        'CACHE_GROUPS' => 'Y',
                        'SAVE_IN_SESSION' => 'N',
                        'FILTER_VIEW_MODE' => 'VERTICAL',
                        'XML_EXPORT' => 'Y',
                        'SECTION_TITLE' => 'NAME',
                        'SECTION_DESCRIPTION' => 'DESCRIPTION',
                        'HIDE_NOT_AVAILABLE' => 'N',
                        'TEMPLATE_THEME' => 'blue',
                        'CONVERT_CURRENCY' => 'Y',
                        'CURRENCY_ID' => 'RUB',
                        'SEF_MODE' => 'Y',
                        'SMART_FILTER_PATH' => $_REQUEST["SMART_FILTER_PATH"],
                        'PAGER_PARAMS_NAME' => 'arrPager',
                        'INSTANT_RELOAD' => 'Y',
                        'DISPLAY_ELEMENT_COUNT' => 'N',
                        'COMPOSITE_FRAME_MODE' => 'Y',
                        'COMPOSITE_FRAME_TYPE' => 'DYNAMIC_WITH_STUB',
                        'ELEMENTS_COLLAPSE' => 10,
                        'DISPLAY_CODES' => 'TYPEPRODUCT',
                        'SEF_RULE' => $filterPath . 'filter/#SMART_FILTER_PATH#/'

                    ),
                    false
                );

            }

            unset($arrFilter['FACET_OPTIONS']);

            if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){
                $APPLICATION->RestartBuffer();
            }

            ?>
            <div id="comp_smart_filter">
                <?

                $asParams = Array(
                    "IS_MODEL_LIST" => "Y",
                    "ACTION_VARIABLE" => "action",
                    "ADD_PICT_PROP" => "-",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "BASKET_URL" => "/personal/cart/",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => 360000000,
                    "CACHE_TYPE" => "A",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "CONVERT_CURRENCY" => "Y",
                    "CURRENCY_ID" => "RUB",
                    "DETAIL_URL" => (!$isVersion ? "/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/":"/model/#ELEMENT_CODE#/"),
                    "DISPLAY_COMPARE" => "N",
                    "ELEMENT_COUNT" => "9999",
                    "ELEMENT_SORT_FIELD" => "show_counter",
                    "ELEMENT_SORT_FIELD2" => "show_counter",
                    "ELEMENT_SORT_ORDER" => "desc",
                    "ELEMENT_SORT_ORDER2" => "desc",
                    "FILTER_NAME" => "arrFilter",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "IBLOCK_ID" => (!$isVersion ? 11 : 17),
                    "IBLOCK_TYPE" => "catalog",
                    "LABEL_PROP" => "-",
                    "LINE_ELEMENT_COUNT" => "3",
                    "MESS_BTN_ADD_TO_BASKET" => "Купить",
                    "MESS_BTN_BUY" => "Купить",
                    "MESS_BTN_COMPARE" => "Сравнить",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_NOT_AVAILABLE" => "(нет на складе)",
                    "OFFERS_LIMIT" => "5",
                    "PARTIAL_PRODUCT_PROPERTIES" => "Y",
                    "PRICE_CODE" => array("Розничная"),
                    "PRICE_VAT_INCLUDE" => "Y",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "PRODUCT_PROPERTIES" => array(),
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PRODUCT_QUANTITY_VARIABLE" => "",
                    "PROPERTY_CODE" => array("SPECIALOFFER","SALELEADER","OLD_PRICE", "ARTNUMBER", "COM_BLACK", "QUALITY", "NEWPRODUCT", "SALEPRODUCT", "TYPEPRODUCT", "MANUFACTURER"),
                    "ROTATE_TIMER" => "",
                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                    "SECTION_URL" => "/catalog/#SECTION_CODE_PATH#/",
                    "SEF_MODE" => "Y",
                    "SHOW_CLOSE_POPUP" => "Y",
                    "SHOW_DISCOUNT_PERCENT" => "N",
                    "SHOW_OLD_PRICE" => "N",
                    "SHOW_PAGINATION" => "Y",
                    "SHOW_PRICE_COUNT" => "1",
                    "TEMPLATE_THEME" => "",
                    "USE_PRICE_COUNT" => "N",
                    "USE_PRODUCT_QUANTITY" => "N",
                    "VIEW_MODE" => "BANNER",
                    "LIST_IMAGE_HEIGHT" => 204,
                    "LIST_IMAGE_WIDTH" => 204,
                );

                if(!$isVersion){

                    $asParams["MODEL_NEW"] = $model_new;
                    $asParams["MODEL_NAME"] = $model_name;
                    $asParams["MANUFACTURER"] = $manufacturer;
                    $asParams["POSITION"] = $positions;
                    $asParams["INDCODE"] = $indCode;

                    if(
                        isset($arResult["DISPLAY_PROPERTIES"])
                        && isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
                        && isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
                        && !empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
                    ){

                        $asParams["TYPE_OF_PRODUCT"] = $arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"];

                    }

                } else {

                    $asParams["IS_VERSION"] = true;

                }

                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.top",
                    "lists",
                    $asParams
                );

                ?>
            </div>
            <?

            if(isset($_REQUEST["bxajaxid"]) && $_REQUEST["bxajaxid"] == 'smart_filter'){
                die();
            }

        } else {

            $basePage = $APPLICATION->GetCurPage();
            $basePage = preg_replace('~/view/[^/]+?/~','/',$basePage);
            $basePage = preg_replace('~/indcode/[^/]+?/~','/',$basePage);
            $basePage = preg_replace('~/filter/[^/]+?/~','/',$basePage);
            $basePage = '/'.trim($basePage,'/').'/';

            ?>
            <div class="alert alert-danger">
                <?=sprintf(GetMessage('TPL_NOTHING_FOUND'),$basePage);?>
            </div>
            <?
        }

        ?>
        <div id="comments" class="comments row clearfix">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:forum.topic.reviews",
                "comments",
                Array(
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "MESSAGES_PER_PAGE" => 15,
                    "USE_CAPTCHA" => "N",
                    "FORUM_ID" => 4,
                    "SHOW_LINK_TO_FORUM" => "N",
                    "ELEMENT_ID" => $arResult['ID'],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SHOW_MINIMIZED" => "N",
                    "AJAX_MODE" => "N",
                )
            );
            ?>
        </div>
    </div>
    </div>
    </div>
<?php

if(preg_match('~/filter/.*$~i',$APPLICATION->GetCurDir())){
    $APPLICATION->AddHeadString('<link rel="canonical" href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.preg_replace('~/filter/.*$~i','/',$APPLICATION->GetCurDir()).'" />');
}


if(isset($arResult['CODE'])
    && !empty($arResult['CODE'])){

    //$APPLICATION->AddHeadString('<link rel="amphtml" href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/amp/model/'.$arResult['CODE'].'/" />');

}

$basePage = preg_replace('~/view/[^/]+?/~','/',$basePage);
$basePage = preg_replace('~/indcode/[^/]+?/~','/',$basePage);
$basePage = preg_replace('~/filter/[^/]+?/~','/',$basePage);
$basePage = '/'.trim($basePage,'/').'/';

if(mb_stripos($basePage,'/view/') !== false){

    $APPLICATION->AddHeadString('<link href="'.IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$basePage.'" rel="canonical" />');

}

$leftUri = trim(preg_replace('~/model/[^/]+?/~','/',$basePage),'/');

if($leftUri != ""){
    LocalRedirect('/model/'.$arResult['CODE'].'/');
}
