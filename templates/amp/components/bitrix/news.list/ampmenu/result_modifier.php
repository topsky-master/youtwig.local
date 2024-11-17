<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$arResult['MENU_DESCRIPTION'] = '';

if (IBLOCK_INCLUDED){

    $arDMSelect = Array(
        "PREVIEW_TEXT"
    );
    $arDMFilter = Array(
        "IBLOCK_ID" => 18,
        "CODE" => "mobile-info"
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
        $arResult['MENU_DESCRIPTION'] = $arDMRes["PREVIEW_TEXT"];

    }

}

$cartCount = CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), array(), false, array('ID'));
$cartCount = (int)$cartCount;

$arResult['CART_COUNT'] = $cartCount;

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