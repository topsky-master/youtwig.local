<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arSFilter = Array(
    'IBLOCK_ID' => $arResult['IBLOCK_ID']);

if(isset($arResult['IBLOCK_SECTION_ID'])
    && !empty($arResult['IBLOCK_SECTION_ID'])){

    $arSFilter['ID'] = $arResult['IBLOCK_SECTION_ID'];

} if(isset($arParams['SECTION_CODE'])
    && !empty($arParams['SECTION_CODE'])){

    $arSFilter['CODE'] = $arParams['SECTION_CODE'];

} else if(isset($arParams['SECTION_ID'])
    && !empty($arParams['SECTION_ID'])){

    $arSFilter['ID'] = $arParams['SECTION_ID'];

}

$rsSection = CIBlockSection::GetList(Array(), $arSFilter, false, array('SECTION_PAGE_URL'));
$rsSection->SetUrlTemplates("", "/amp/sections/#SECTION_CODE#/");

$arResult['SECTION_PAGE_URL'] = '';

if($rsSection){

    $arSection = $rsSection->GetNext();
    $arResult['SECTION_PAGE_URL'] = $arSection['SECTION_PAGE_URL'];

}

$arEmptyPreview = false;
$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
{
    $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);

    if (!empty($arSizes))
    {
        $arEmptyPreview = array(
            'src' => $strEmptyPreview,
            'width' => (int)$arSizes[0],
            'height' => (int)$arSizes[1],
            'alt' => $arResult['NAME']
        );

    }
    unset($arSizes);
}
unset($strEmptyPreview);

$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

$arResult['BUY_ID'] = getBondsProduct($arResult['ID']);

if($arResult["BUY_ID"]	!=$arResult['ID']){
    $arResult["CAN_BUY"] = canYouBuy($arResult['ID']);

    if(checkQuantityRigths()){

        $bondsArSelect = Array("NAME","PROPERTY_SHELF","PROPERTY_RACK");
        $bondsArFilter = Array(
            "ID"=>(int)($arResult['BUY_ID'])
        );

        $bondResDB = CIBlockElement::GetList(Array(), $bondsArFilter, false, false, $bondsArSelect);

        if($bondResDB && ($bondResArr = $bondResDB->GetNext())){
            if(isset($bondResArr['NAME']) && !empty($bondResArr['NAME'])){
                $arResult['BONDS_NAME'] = $bondResArr['NAME'];
            }

            if(isset($bondResArr['PROPERTY_SHELF_VALUE']) && !empty($bondResArr['PROPERTY_SHELF_VALUE'])){
                $arResult['SHELF'] = $bondResArr['PROPERTY_SHELF_VALUE'];
            }

            if(isset($bondResArr['PROPERTY_RACK_VALUE']) && !empty($bondResArr['PROPERTY_RACK_VALUE'])){
                $arResult['RACK'] = $bondResArr['PROPERTY_RACK_VALUE'];
            }
        }

    }


    $rsProducts = CCatalogProduct::GetList(
        array(),
        array('ID' => $arResult['BUY_ID']),
        false,
        false,
        array(
            'ID',
            'CAN_BUY_ZERO',
            'QUANTITY_TRACE',
            'QUANTITY'
        )
    );

    if ($arCatalogProduct = $rsProducts->Fetch()){

        //$arResult['CHECK_QUANTITY'] = ($arCatalogProduct["CAN_BUY_ZERO"] != 'Y' && $arCatalogProduct["QUANTITY_TRACE"] == 'Y');
        $arResult['CHECK_QUANTITY'] = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');

        $arResultDbRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $arResult['BUY_ID']), false, false, array("RATIO"));
        $arResult['CATALOG_MEASURE_RATIO'] = 1;

        if($arResultRatio = $arResultDbRatio->Fetch()){
            if(isset($arResultRatio['RATIO']) && !empty($arResultRatio['RATIO'])){
                $arResult['CATALOG_MEASURE_RATIO'] = $arResultRatio['RATIO'];
            };
        };

        $arResult['CATALOG_QUANTITY'] = (
        0 < $arCatalogProduct["QUANTITY"] && is_float($arResult['CATALOG_MEASURE_RATIO'])
            ? floatval($arCatalogProduct["QUANTITY"])
            : intval($arCatalogProduct["QUANTITY"])
        );


    }

}

$arResult['PRINT_QUANTITY'] = get_quantity_product($arResult['ID']);
$arResult['CAN_BUY'] = $arResult['PRINT_QUANTITY'] > 0 ? $arResult['CAN_BUY'] : false;
$arResult['PRINT_QUANTITY'] = checkQuantityRigths() ? $arResult['PRINT_QUANTITY'] : false;

if(isset($arResult['PROPERTIES'])
    &&isset($arResult['PROPERTIES']['LINKED_ELEMETS'])
    &&isset($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])
    &&!empty($arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'])){

    $arResult['LINKED_ELEMETS'] = $arResult['PROPERTIES']['LINKED_ELEMETS']['VALUE'];
    unset($arResult['PROPERTIES']['LINKED_ELEMETS']);
}

$cacheTime = isset($arParams['CACHE_TIME']) ? (int)$arParams['CACHE_TIME'] : 3600;

$obCache = new CPHPCache;
$cacheID = 'tabsdetail';
$tabs = array();

if($obCache->InitCache($cacheTime, $cacheID, "/impel/")){

    $tmp = array();
    $tmp = $obCache->GetVars();

    if(isset($tmp[$cacheID])){
        $tabs = $tmp[$cacheID];
    }

} else {

    $arFilter = Array(
        "SECTION_CODE" => "amptabs",
        "IBLOCK_CODE" => "options",
        "ACTIVE" => "Y"
    );

    $tabsResDB = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","PREVIEW_TEXT","PREVIEW_PICTURE"));

    if($tabsResDB){

        while($tabsResArr = $tabsResDB->Fetch()){

            if(
                isset($tabsResArr['ID'])
                && isset($tabsResArr['NAME'])
                && isset($tabsResArr['PREVIEW_TEXT'])
                && !empty($tabsResArr['PREVIEW_TEXT'])
            ){

                $tabs['tab_headers'][$tabsResArr['ID']]	= $tabsResArr['NAME'];

                $amp_content_obj = new AMP_Content( $tabsResArr['PREVIEW_TEXT'],
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

                $tabsResArr['PREVIEW_TEXT'] = $amp_content_obj->get_amp_content();
                $tabs['tab_panels'][$tabsResArr['ID']] = $tabsResArr['PREVIEW_TEXT'];


                if(	isset($tabsResArr['PREVIEW_PICTURE'])
                    && !empty($tabsResArr['PREVIEW_PICTURE'])
                ){

                    $image_path	= CFile::GetPath($tabsResArr['PREVIEW_PICTURE']);
                    if($image_path){
                        $tabs['tab_images'][$tabsResArr['ID']] = $image_path;
                    };

                };
            };
        };

        if(isset($arResult["PROPERTIES"]["MODEL_HTML"])
            && isset($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"])
            && !empty($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"])) {

            $modelHTMLId = $arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"];

            $dbMHTMLId = CIBlockElement::GetByID($arResult["PROPERTIES"]["MODEL_HTML"]["~VALUE"]);

            if ($dbMHTMLId
                && $arMHTMLId = $dbMHTMLId->GetNext()) {

                $tabText = '';
                $tabText .= str_ireplace('href="', 'href="/amp', $arMHTMLId['~DETAIL_TEXT']);
                $tabText = trim($tabText);

                if (!empty($tabText)) {
                    $tabs['tab_headers']['cbsm'] = GetMessage('CT_BCE_SUITABLE_MODELS');
                    $tabs['tab_panels']['cbsm'] = '<div class="suitable-models">' . $tabText . '</div>';
                }

            }

        }

    };

    if($obCache->StartDataCache()){

        $obCache->EndDataCache(
            array(
                $cacheID => $tabs
            )
        );

    };

};



unset($arResult["PROPERTIES"]["MODEL"],$arResult["DISPLAY_PROPERTIES"]["MODEL"],$arResult["PROPERTIES"]["MODEL_HTML"],$arResult["DISPLAY_PROPERTIES"]["MODEL_HTML"]);

$arResult['TABS'] = $tabs;

$gallery = array();

if(isset($arResult["DISPLAY_PROPERTIES"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&is_array($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&sizeof($arResult["PROPERTIES"]["MORE_PHOTO"])
    &&isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
    &&!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
){

    foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $number => $file_id){

        if(is_numeric($file_id)){
            $gallery[$number]['src'] = CFile::GetPath($file_id);
        } else {
            $gallery[$number]['src'] = $file_id;
        }

        if(!empty($gallery[$number]['src'])){

            $gallery[$number]['width'] = 640;
            $gallery[$number]['height'] = 480;


            if(file_exists($_SERVER['DOCUMENT_ROOT'].$gallery[$number]['src'])
                && filesize($_SERVER['DOCUMENT_ROOT'].$gallery[$number]['src']) > 0
                && is_readable($_SERVER['DOCUMENT_ROOT'].$gallery[$number]['src'])){

                $sizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery[$number]['src']);

                if($sizes
                    && is_array($sizes)
                    && isset($sizes[0])
                    && isset($sizes[1])
                    && !empty($sizes[0])
                    && !empty($sizes[1])
                ){

                    $gallery[$number]['width'] = $sizes[0];
                    $gallery[$number]['height'] = $sizes[1];

                }


            }

            $srcSetHTML = '';
            $gallery[$number]['srcset'] = '';
            $srcSetHTML = createAMPSRCSetHTML($gallery[$number]['src']);
            $gallery[$number]['srcset'] = $srcSetHTML;

        }

    }

};


if(isset($arResult["DETAIL_PICTURE"])
    && isset($arResult["DETAIL_PICTURE"]["SRC"])
    && !empty($arResult["DETAIL_PICTURE"]["SRC"])){

    $number = sizeof($gallery);
    $gallery[$number]['src'] = $arResult["DETAIL_PICTURE"]["SRC"];
    $gallery[$number]['width'] = $arResult["DETAIL_PICTURE"]["WIDTH"];
    $gallery[$number]['height'] = $arResult["DETAIL_PICTURE"]["HEIGHT"];

    $srcSetHTML = '';
    $gallery[$number]['srcset'] = '';
    $srcSetHTML = createAMPSRCSetHTML($gallery[$number]['src']);
    $gallery[$number]['srcset'] = $srcSetHTML;

};

if(empty($gallery)){
    $number = sizeof($gallery);
    $gallery[$number] = $arEmptyPreview;
    $srcSetHTML = createAMPSRCSetHTML($arEmptyPreview['src']);
    $gallery[$number]['srcset'] = $srcSetHTML;
}

foreach($gallery as $key => $image) {
    $gallery[$key]["src"] = rectangleImage($_SERVER['DOCUMENT_ROOT'].$image["src"],255,255,$image["src"],"",true,false);
}

$arResult['GALLERY'] = $gallery;

$canonical_url = '';
$canonicalResDB = CIBlockElement::GetList(Array("SORT" => "ASC"),Array('ID' => $arResult['ID'], 'IBLOCK_ID' => $arResult['IBLOCK_ID']), false, false, array('DETAIL_PAGE_URL'));

if($canonicalResDB
    && $canonicalResArr = $canonicalResDB->getNext()){

    if(isset($canonicalResArr['DETAIL_PAGE_URL']) && !empty($canonicalResArr['DETAIL_PAGE_URL'])){
        $canonical_url = $canonicalResArr['DETAIL_PAGE_URL'];
    };

};

if(!empty($arResult["PREVIEW_TEXT"])){
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
            'content_max_width' => 320,
        )
    );

    $arResult["PREVIEW_TEXT"] = $amp_content_obj->get_amp_content();
}

if(!empty($arResult["DETAIL_TEXT"])){

    $amp_content_obj = new AMP_Content( $arResult["DETAIL_TEXT"],
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

    $arResult["DETAIL_TEXT"] = $amp_content_obj->get_amp_content();
}


$arResult['CANONICAL_URL'] = $canonical_url;

if (is_object($this->__component))
{
    $resultCacheKeys = array_keys($arResult);

    $this->__component->SetResultCacheKeys(
        $resultCacheKeys
    );

    foreach($resultCacheKeys as $resultCacheKey){

        if (!isset($arResult[$resultCacheKey]))
            $arResult[$resultCacheKey] = $this->__component->arResult[$resultCacheKey];

    };

};
