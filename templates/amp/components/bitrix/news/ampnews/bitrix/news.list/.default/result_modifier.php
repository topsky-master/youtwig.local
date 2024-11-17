<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

foreach($arResult["ITEMS"] as $arKey => $arItem):

    if(isset($arItem["PREVIEW_PICTURE"])
        && isset($arItem["PREVIEW_PICTURE"]["SRC"])
        &&!empty($arItem["PREVIEW_PICTURE"]["SRC"])):

        $arResult["ITEMS"][$arKey]["PREVIEW_PICTURE"]['SRCSET'] = createAMPSRCSetHTML($arItem["PREVIEW_PICTURE"]["SRC"]);

    endif;

    if(isset($arItem["PREVIEW_TEXT"])
        &&!empty($arItem["PREVIEW_TEXT"])):

        $arItem["PREVIEW_TEXT"] = html_entity_decode($arItem["PREVIEW_TEXT"],ENT_HTML5,LANG_CHARSET);
        $arItem["PREVIEW_TEXT"] = strip_tags($arItem["PREVIEW_TEXT"]);
        

        $arItem["PREVIEW_TEXT"] = strip_tags($arItem["PREVIEW_TEXT"]);
        $arItem["PREVIEW_TEXT"] = trim($arItem["PREVIEW_TEXT"]);
        $arItem["PREVIEW_TEXT"] = mb_substr($arItem["PREVIEW_TEXT"],0,255);
        if(($pos = mb_strrpos($arItem["PREVIEW_TEXT"],' ')) !== false){
            $arItem["PREVIEW_TEXT"] = mb_substr($arItem["PREVIEW_TEXT"],0,$pos);
        }

        $amp_content_obj = new AMP_Content( $arItem["PREVIEW_TEXT"],
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

        $arResult["ITEMS"][$arKey]["PREVIEW_TEXT"] = $amp_content_obj->get_amp_content();

    endif;

endforeach;

$arResult['CANONICAL_URL'] = $arParams['CANONICAL_URL'];

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