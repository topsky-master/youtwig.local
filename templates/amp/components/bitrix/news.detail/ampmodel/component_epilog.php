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
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

twigModels::getFilterParams($arResult);

$basePage = $APPLICATION->GetCurPage();

$default_title  = isset($arParams['DEFAULT_TITLE'])
&&!empty($arParams['DEFAULT_TITLE'])
    ? trim($arParams['DEFAULT_TITLE'])
    : '';

$curViewString = $curView = '';

if(preg_match('~/view/([^/]+?)/~isu',$basePage,$matches)) {

    if (isset($matches[1]) && !empty($matches[1])) {
        $curView = trim($matches[1]);
    }
}

$products = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"])
&&isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_PRODUCTS"]["VALUE"])
    : '';

if(isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
    && is_array($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
    && !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"])
){
    foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"]
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

            $curViewString = $productName;

        } else if(!empty($curView)){
			
			unset($products[$productNum]);
        	
		}


    }

}

$isVersion       = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&& $arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"] == 'Да'
    ? true
    : false;

if(!$isVersion) {

    $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models', '', SITE_ID);
    $models_h1 = \COption::GetOptionString('my.stat', 'models_h1', '', SITE_ID);

} else {

    $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models_version', '', SITE_ID);
    $models_h1 = \COption::GetOptionString('my.stat', 'models_version_h1', '', SITE_ID);

}

if(!$isVersion){

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_models', array(), SITE_ID));

} else {

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_series_models', array(), SITE_ID));

}

$dreplaces = array(
    '[product_type_dec]' => '',
    '[product_type]' => '',
    '[brand]' => '',
    '[model]' => '',
    '[indcode]' => '',
    '[view]' => $curViewString
);

$curPage = $APPLICATION->GetCurPage();

if(preg_match('~/indcode/([^/]+?)/~isu',$curPage,$matches)){

    if(isset($matches[1]) && !empty($matches[1])){
        $curIndcode = trim($matches[1]);

        foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"]
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

            if($productCode == $curIndcode){
                $dreplaces['[indcode]'] = trim(strip_tags($productName));
                break;
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

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])){

    $dreplaces['[product_type]'] = $arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"];

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


$manufacturer   = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"])
&&isset($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
    ? trim($arResult["DISPLAY_PROPERTIES"]["manufacturer"]["VALUE"])
    : '';

$model_new      = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_new"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
    ? trim($arResult["DISPLAY_PROPERTIES"]["model_new"]["VALUE"])
    : '';


$model_name     = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
    : '';

$isVersion = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&& $arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"] == 'Да'
    ? true
    : false;

if(!$isVersion){

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_models', array(), SITE_ID));

} else {

    $declension_models = unserialize(\COption::GetOptionString('my.stat', 'declension_series_models', array(), SITE_ID));

}


if(preg_match('~/indcode/([^/]+?)/~isu',$curPage,$matches) && !$isVersion){

    if(isset($matches[1]) && !empty($matches[1])){
        $curIndcode = trim($matches[1]);

        foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"]
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

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"])
    &&isset($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])
    &&!empty($arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"])){

    $dreplaces['[product_type]'] = $arResult["DISPLAY_PROPERTIES"]["type_of_product"]["VALUE"];

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

$models_keywords = twigModels::replaceFilterTypes($models_keywords,$arResult['ftypes']);
$models_description = twigModels::replaceFilterTypes($models_description,$arResult['ftypes']);
$models_title = twigModels::replaceFilterTypes($models_title,$arResult['ftypes']);


$text_for_models = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$text_for_models);
$models_h1 = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_h1);

$models_title = str_ireplace(',',' ',$models_title);
$models_description = str_ireplace(',',' ',$models_description);
$models_keywords = str_ireplace(',',' ',$models_keywords);


?>
    <div class="about-model-area clearfix">
        <?
        if(!empty($default_title) || !empty($models_h1)){

            $default_title = sprintf($default_title,' '.$manufacturer.' '.$model_new.'');
            $print_title = !empty($models_h1) ? $models_h1 : $default_title;
            $print_title = twigModels::replaceFilterTypes($print_title,$arResult['ftypes']);

            $amp_content_obj = new AMP_Content( $print_title,
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
                    'content_max_width' => 320
                )
            );

            $print_title = $amp_content_obj->get_amp_content();
			$print_title = str_ireplace(',',' ',$print_title);
			
            ?>
            <div class="title-row text-center">
                <h1>
                    <?php echo $print_title; ?>
                </h1>
            </div>
            <?

        }
		
		
		
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
                ? ($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
                : '';

            $positions = array();

            $views = isset($arResult["DISPLAY_PROPERTIES"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"])
            &&isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
            &&!empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
                ? ($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"])
                : array();


$sFilter = '';
$svFilter = '';



 if(preg_match('~/view/([^/]+?)/~isu',$curPage,$matches) &&  !$isVersion){
						
						if(isset($matches[1]) && !empty($matches[1])){

                            foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["DISPLAY_VALUE"]
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

                                    $dbRes = CIBlockElement::GetByID($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_VIEW"]["VALUE"][$productNum]);

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
                                            '#ffffff',
											true,
											false
                                        );

                                        if(!$hasBigImage){
                                            $hasBigImage = true;

                                        }

                                    }

                                } else {
                                    unset($products[$productNum]);
                                }

                            }


                        }
                    }

                    if($hasBigImage && !empty($products)){

                        foreach($products as $productNum => $productId){

                            if(isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"][$productNum])){

                                $position = trim($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_POSITION"]["VALUE"][$productNum]);

                                if(!empty($position)
                                    && $position != '-'
                                    && $position != ' '){
                                    $positions[$productId] = $position;
                                }

                            }

                        }

                    }




if(!empty($products)){

    ob_start();

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
            "nvhcompare",
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
                'DISPLAY_CODES' => 'TYPEPRODUCT,MANUFACTURER_DETAIL',
                'SEF_RULE' => $filterPath . 'filter/#SMART_FILTER_PATH#/',
                "HIDE_CURRENTLY_SELECTED" => "Y"

            ),
            false
        );
		
		$bCheckOne = true;
		
		$bHideMan = true;
		
		if((isset($arrFilter['=PROPERTY_46']) 
			&& !empty($arrFilter['=PROPERTY_46']))
		){
			$bHideMan = false;
		}
	
    }
	
    foreach ($arrFilter as $filter_key => $filter_value) {
        if (mb_stripos($filter_key, '=PROPERTY_') !== false) {
            $filterError = false;
            break;
        };
    };
	
	unset($arrFilter['FACET_OPTIONS']);

    ?>
    <div id="comp_smart_filter">
        <?


        if(!($filterError && $hasFilter)){

            $asParams = Array(
                            "ACTION_VARIABLE" => "action",
                            "ADD_PROPERTIES_TO_BASKET" => "Y",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_TO_BASKET_ACTION" => "ADD",
                            "AJAX_MODE" => "N",
                            "AJAX_OPTION_ADDITIONAL" => "",
                            "AJAX_OPTION_HISTORY" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "BACKGROUND_IMAGE" => "-",
                            "BASKET_URL" => "/personal/basket.php",
                            "BROWSER_TITLE" => "-",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "COMPATIBLE_MODE" => "Y",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                            "CONVERT_CURRENCY" => "Y",
                            "CURRENCY_ID" => "RUB",
                            "CUSTOM_FILTER" => "",
                            "DETAIL_URL" => (!$isVersion ? "/amp/catalog/#ELEMENT_CODE#/" : "/amp/model/#ELEMENT_CODE#/"),
                            "DISABLE_INIT_JS_IN_COMPONENT" => "Y",
                            "DISPLAY_BOTTOM_PAGER" => "Y",
                            "DISPLAY_COMPARE" => "N",
                            "DISPLAY_TOP_PAGER" => "N",
                            "ELEMENT_SORT_FIELD" => "show_counter",
                            "ELEMENT_SORT_FIELD2" => "show_counter",
                            "ELEMENT_SORT_ORDER" => "desc",
                            "ELEMENT_SORT_ORDER2" => "desc",
                            "FILTER_NAME" => "arrFilter",
                            "HIDE_NOT_AVAILABLE" => "N",
                            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                            "IBLOCK_ID" => (!$isVersion ? 11 : 17),
                            "IBLOCK_TYPE" => "catalog",
                            "INCLUDE_SUBSECTIONS" => "Y",
                            "LAZY_LOAD" => "N",
                            "LINE_ELEMENT_COUNT" => "3",
                            "LOAD_ON_SCROLL" => "N",
                            "MESSAGE_404" => "",
                            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                            "MESS_BTN_BUY" => "Купить",
                            "MESS_BTN_DETAIL" => "Подробнее",
                            "MESS_BTN_SUBSCRIBE" => "Подписаться",
                            "MESS_NOT_AVAILABLE" => "Нет в наличии",
                            "META_DESCRIPTION" => "-",
                            "META_KEYWORDS" => "-",
                            "OFFERS_LIMIT" => "5",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => "pager",
                            "PAGER_TITLE" => "Товары",
                            "PAGE_ELEMENT_COUNT" => "9999",
                            "PARTIAL_PRODUCT_PROPERTIES" => "N",
                            "PRICE_CODE" => array("Розничная"),
                            "PRICE_VAT_INCLUDE" => "Y",
                            "PRODUCT_ID_VARIABLE" => "id",
                            "PRODUCT_PROPERTIES" => array(),
                            "PRODUCT_PROPS_VARIABLE" => "prop",
                            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                            "PRODUCT_SUBSCRIPTION" => "Y",
                            "PROPERTY_CODE" => array("COM_BLACK", "NEWPRODUCT", "SALEPRODUCT", "TYPEPRODUCT", "MANUFACTURER"),
                            "RCM_PROD_ID" => "",
                            "RCM_TYPE" => "personal",
                            "SECTION_CODE" => "",
                            "SECTION_CODE_PATH" => "",
                            "SECTION_ID" => "",
                            "SECTION_ID_VARIABLE" => "SECTION_ID",
                            "SECTION_URL" => "/amp/model/#SECTION_CODE#/",
                            "SECTION_USER_FIELDS" => array("",""),
                            "SEF_MODE" => "Y",
                            "SEF_RULE" => "/amp/model/#SECTION_CODE#/",
                            "SET_BROWSER_TITLE" => "Y",
                            "SET_LAST_MODIFIED" => "N",
                            "SET_META_DESCRIPTION" => "Y",
                            "SET_META_KEYWORDS" => "Y",
                            "SET_STATUS_404" => "N",
                            "SET_TITLE" => "Y",
                            "SHOW_404" => "N",
                            "SHOW_ALL_WO_SECTION" => "Y",
                            "SHOW_CLOSE_POPUP" => "N",
                            "SHOW_DISCOUNT_PERCENT" => "N",
                            "SHOW_FROM_SECTION" => "N",
                            "SHOW_MAX_QUANTITY" => "N",
                            "SHOW_OLD_PRICE" => "N",
                            "SHOW_PRICE_COUNT" => "1",
                            "TEMPLATE_THEME" => "blue",
                            "USE_ENHANCED_ECOMMERCE" => "N",
                            "USE_MAIN_ELEMENT_SECTION" => "N",
                            "USE_PRICE_COUNT" => "N",
                            "USE_PRODUCT_QUANTITY" => "N",
                            "IS_MODEL_LIST" => "Y"
                        );

            if(!$isVersion){

                $asParams["MODEL_NAME"] = $model_name;
                $asParams["MODEL_NEW"] = $model_new;

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

			global $NavNum; $NavNum = 0;
			
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "models",
                $asParams
            );

        }

        ?>
    </div>
    <?

    $sFilter = ob_get_clean();
	
	if(mb_stripos($sFilter,'<!-- start: mdetail -->') !== false){
		
		$aFilter = explode('<!-- start: mdetail -->',$sFilter,3);
		$aFilter = array_map('trim',$aFilter);
		
		if(isset($aFilter[2])){
			$sFilter = $aFilter[0].$aFilter[2];
			$stFilter = $aFilter[1];
			
			preg_match_all('~<a[^>]+?>~isu',$stFilter,$aMatches);
			
			if($bCheckOne && !$bHideMan) {
				
				if(isset($aMatches[0]) && !empty($aMatches[0]) && sizeof($aMatches[0]) > 1){
					$svFilter = $stFilter;
				}
			
			} 
			
			unset($aMatches,$stFilter);
			
		}
	}
	
}



        ?>
        <div class="about-model-area clearfix">
            <?php if(!$isVersion): ?>
                <div class="about-model-info indcode">
                    <div class="about-model-download">
                        <div>
                            <?if(is_array($arResult["PREVIEW_PICTURE"])):
								$arResult["PREVIEW_PICTURE"]["SRC"] = rectangleImage($_SERVER['DOCUMENT_ROOT'].$arResult["PREVIEW_PICTURE"]["SRC"],255,255,$arResult["PREVIEW_PICTURE"]["SRC"],"",true,false);
							?>
                                <amp-img itemprop="image" alt="<?=htmlspecialchars($arResult["PREVIEW_PICTURE"]["ALT"],ENT_QUOTES,LANG_CHARSET);?>" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"];?>" layout="responsive"<?=$arResult["PREVIEW_PICTURE"]["srcset"];?>>
                                    <noscript>
                                        <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"];?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"];?>" alt="<?=htmlspecialchars($arResult["PREVIEW_PICTURE"]["ALT"],ENT_QUOTES,LANG_CHARSET);?>" />
                                    </noscript>
                                </amp-img>
                            <?endif?>
                            <?php $instructions = array();
                            if(     isset($arResult["DISPLAY_PROPERTIES"])
                                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"])
                                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"])
                                &&  isset($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"])
                                && !empty($arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"])){
                                $file_src = $arResult["DISPLAY_PROPERTIES"]["instruction"]["FILE_VALUE"]["SRC"];
                                $file_extension = mb_strtoupper(pathinfo($file_src,PATHINFO_EXTENSION));
                                $file_basename = mb_strtoupper(pathinfo($file_src,PATHINFO_BASENAME));
                                ?>
                                <div class="download-instruction">
                                    <a href="<?php echo $file_src; ?>" download="<?php echo $file_basename; ?>">
                                        <?php echo sprintf(GetMessage('DOWNLOAD_INSTRUCTION'),$file_extension); ?>
                                    </a>
                                </div>

                            <?php } ?>
                        </div>
                        <?php if(!empty($arResult["PREVIEW_TEXT"]) || $text_for_models){

                            $amp_content_obj = new AMP_Content( $arResult["PREVIEW_TEXT"],
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
                                    'content_max_width' => 320
                                )
                            );

                            $preview_text = $amp_content_obj->get_amp_content();

                            $text_for_models = twigModels::replaceFilterTypes($text_for_models,$arResult['ftypes']);

                            $amp_content_obj = new AMP_Content( $text_for_models,
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
                                    'content_max_width' => 320
                                )
                            );

                            $text_for_models = $amp_content_obj->get_amp_content();


                            ?>
                            <div class="about-instruction">
                                <?=$preview_text;?>
                                <?=$text_for_models;?>
                            </div>
                        <?php }?>
						<?php if(!empty($svFilter)){ ?>
							<div id="comp_vsmart_filter">
								<?php echo $svFilter; ?>
							</div>
						<?php } ?>
						<?php

                        if(     isset($arResult["DISPLAY_PROPERTIES"])
                            &&  isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"])
                            &&  isset($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])
                            && !empty($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"])){

                            $indcode = array();
                            $hasCode = array();

                            foreach($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["VALUE"] as $productNum => $productId){
                                if($productId != $arResult["skipIndCodeId"]){
                                    $indcode[] = trim(strip_tags($arResult["DISPLAY_PROPERTIES"]["SIMPLEREPLACE_INDCODE"]["DISPLAY_VALUE"][$productNum]));
                                }
                            }



                            if(!empty($indcode)){

                                $hasFilter = mb_stripos($basePage,'/filter/') !== false ? true : false;

                                if(preg_match('~/indcode/[^/]+?/~', $basePage)){
                                    $basePage = preg_replace('~/indcode/[^/]+?/~','/',$basePage);
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
                                <div class="indcode col-md-12 col-sm-6">
                                    <p class="indcode-choose-mod">
                                        <?=GetMessage('TPL_CHOOSE_MOD');?>
                                    </p>
                                    <ul class="indcode-list">
                                        <li>
                                            <a href="<?=$basePage;?>">
                                                <?=GetMessage('TPL_WITHOUT_MOD');?>
                                            </a>
                                        </li>
                                        <? foreach($indcode as $value):

                                            if(in_array($value, $hasCode))
                                                continue;

                                            $hasCode[] = $value;

                                            $code = trim(CUtil::translit(trim(strip_tags($value)), LANGUAGE_ID, $trParams));

                                            if(mb_stripos($basePage,'/filter/') !== false){

                                                $parts = explode('/filter/',$basePage,2);
                                                $linkPage = rtrim($parts[0],'/').'/indcode/'.$code.'/';


                                                if(!empty($parts[1])){
                                                    $linkPage .= 'filter/'.trim($parts[1],'/').'/';
                                                }

                                            } else {

                                                $linkPage = '/'.trim($basePage,'/').'/indcode/'.$code.'/';

                                            }

                                            ?>
                                            <li>
                                                <a href="<?=$linkPage;?>"<?php if($linkPage == $APPLICATION->GetCurPage()): ?> class="active"<?php endif; ?>>
                                                    <?=trim(strip_tags($value));?>
                                                </a>
                                            </li>
                                        <? endforeach; ?>
                                    </ul>
                                </div>
                                <?php

                            }

                        }

                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php

            $APPLICATION->SetPageProperty('page_keywords', $models_keywords);
            $APPLICATION->SetPageProperty('page_title', $models_title);
            $APPLICATION->SetPageProperty('page_description', $models_description);
			
            ?>
            <div class="<?php if($isVersion): ?>series<?php endif; ?> products-list-info">
                <?
				
				

                if(!empty($products)){

                    if(!empty($sFilter)){
						echo $sFilter;
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
            </div>
        </div>
    </div>
<?php

if(file_exists(__DIR__.'/amp_style.css')){

    $amp_style = file_get_contents(__DIR__.'/amp_style.css');
    if(get_class($this->__template)!=="CBitrixComponentTemplate")
        $this->InitComponentTemplate();

    $this->__template->SetViewTarget("AMP_STYLE");
    echo $amp_style;
    $this->__template->EndViewTarget();

}


if(isset($arResult['CANONICAL_URL'])){

    $canonical_url = $arResult['CANONICAL_URL'];

    if(isset($canonical_url) && !empty($canonical_url)){

        $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
        $canonical_url = preg_replace('~\:\/\/(www\.)*m\.~','://',$canonical_url);

        $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
        $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
        $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);

        if($DETAIL_PAGE_URL != $SERVER_PAGE_URL){

            if(get_class($this->__template)!=="CBitrixComponentTemplate")
                $this->InitComponentTemplate();

            $this->__template->SetViewTarget("CANONICAL_PROPERTY");
            ?>
            <link href="<?=$canonical_url;?>" rel="canonical" />
            <?
            $this->__template->EndViewTarget();

        };

    };
};