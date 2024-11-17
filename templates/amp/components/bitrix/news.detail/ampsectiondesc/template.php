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
$this->setFrameMode(false);?>
<? if((isset($arResult['DISPLAY_PROPERTIES'])
        && isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM'])
        && isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'])) ||
    ($arResult['PREVIEW_TEXT'] != ""
        && $arParams["DISPLAY_PREVIEW_TEXT"] != "N")){

    $amp_content_obj = new AMP_Content($arResult["PREVIEW_TEXT"],
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

    $amp_content_obj = new AMP_Content($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'],
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

    $arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'] = $amp_content_obj->get_amp_content();
?>

	<div class="section-info">
		<? if(isset($arResult['DISPLAY_PROPERTIES'])
            && isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM'])
            && isset($arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'])){ ?>
            <h1>
                <? echo $arResult['DISPLAY_PROPERTIES']['H1_BOTTOM']['DISPLAY_VALUE'];?>
            </h1>
        <? }; ?>
	</div>
	<!-- description -->
	<div class="section-info">
		<? if($arResult['PREVIEW_TEXT'] != "" && $arParams["DISPLAY_PREVIEW_TEXT"] != "N"){?>
            <? if(isset($arResult['PREVIEW_TEXT'])
                &&!empty($arResult['PREVIEW_TEXT'])): ?>
                <? echo $arResult['PREVIEW_TEXT']; ?>
            <? endif; ?>
        <?};?>
	</div>
<? };?>