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
$this->setFrameMode(true);

$image_thumb_width = 253;
$image_thumb_height = 253;

$isVersion       = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"])
&&isset($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"])
&& $arResult["DISPLAY_PROPERTIES"]["VERSION"]["VALUE"] == 'Да'
    ? true
    : false;

$default_title  = isset($arParams['DEFAULT_TITLE'])
&&!empty($arParams['DEFAULT_TITLE'])
    ? trim($arParams['DEFAULT_TITLE'])
    : '';

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
    '[indcode]' => ''
);

$curPage = $APPLICATION->GetCurPage();

if(preg_match('~/indcode/([^/]+?)/~isu',$curPage,$matches)){

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

if(!$isVersion) {

    $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models', '', SITE_ID);
    $models_h1 = \COption::GetOptionString('my.stat', 'models_h1', '', SITE_ID);

} else {

    $text_for_models = \COption::GetOptionString('my.stat', 'text_for_models_version', '', SITE_ID);
    $models_h1 = \COption::GetOptionString('my.stat', 'models_version_h1', '', SITE_ID);

}

$text_for_models = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$text_for_models);
$models_h1 = str_ireplace(array_keys($dreplaces),array_values($dreplaces),$models_h1);

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

$products       = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["products"])
&&isset($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["products"]["VALUE"])
    : '';

$model_name     = isset($arResult["DISPLAY_PROPERTIES"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"])
&&isset($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
&&!empty($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
    ? ($arResult["DISPLAY_PROPERTIES"]["model_name"]["VALUE"])
    : '';

?>
<div class="about-model-area clearfix">
    <?
    if(!empty($default_title) || !empty($models_h1)){
        $default_title = sprintf($default_title,' '.$manufacturer.' '.$model_new.'');
        ?>
        <div class="row title-row text-center">
            <h1>
                <?php echo !empty($models_h1) ? $models_h1 : $default_title; ?>
            </h1>
        </div>
        <?

    }

    ?>
    <div class="row about-model-area clearfix">
        <?php if(!$isVersion): ?>
            <div class="col-md-3 col-sm-12 col-xs-12 about-model-info">
                <div class="about-model-download">
                    <div class="col-md-12 col-sm-6">
                        <?if(!is_array($arResult["PREVIEW_PICTURE"])):?>
                            <?$arResult["PREVIEW_PICTURE"] = array("SRC" => $templateFolder."/images/noimage.png", "ALT" => "", "TITLE" => "");?>
                        <?endif;?>
                        <?if(is_array($arResult["PREVIEW_PICTURE"])):

                            $src = $arResult["PREVIEW_PICTURE"]["SRC"];
                            $src = rectangleImage(
                                $_SERVER['DOCUMENT_ROOT'].$src,
                                $image_thumb_width,
                                $image_thumb_height,
                                $src,
                                '#ffffff'
                            );

                            ?>
                            <img src="<?=$src?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>" class="img-responsive" />
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
                    <?php if(!empty($arResult["PREVIEW_TEXT"]) || $text_for_models){?>
                        <div class="about-instruction col-md-12 col-sm-6">
                            <?echo $arResult["PREVIEW_TEXT"];?>
                            <?=$text_for_models;?>
                        </div>
                    <?php }?>
                    <?php

                    if(     isset($arResult["DISPLAY_PROPERTIES"])
                        &&  isset($arResult["DISPLAY_PROPERTIES"]["INDCODE"])
                        &&  isset($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"])
                        && !empty($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"])){

                        $indcode = array();
                        $hasCode = array();

                        foreach($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["VALUE"] as $productNum => $productId){
                            if($productId != $arResult["skipIndCodeId"]){
                                $indcode[] = trim(strip_tags($arResult["DISPLAY_PROPERTIES"]["INDCODE"]["DISPLAY_VALUE"][$productNum]));
                            }
                        }



                        if(!empty($indcode)){

                            $basePage = $APPLICATION->GetCurPage();

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
                                            <a href="<?=$linkPage;?>">
                                                <?=trim(strip_tags($value));?>
                                            </a>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            </div>
                            <script type="text/javascript">
                                //<!--

                                $(function(){

                                    $('.indcode-list li:not(:first-child) a').each(function(){

                                        var linkHref = this.href.replace(/[^\/]+?\/\/[^\/]+?\//,'/');
                                        linkHref = linkHref.replace(/[^\/]*?\?.*$/,'/');

                                        var cPathname = location.pathname;

                                        if(cPathname == linkHref){

                                            $(this).addClass('active');
                                            $(this).on('click',function(event){

                                                event.preventDefault();
                                                location.href = '<?=$basePage;?>';
                                                return false;

                                            });

                                        };

                                    });

                                });

                                //-->
                            </script>
                            <?php

                        }

                    }

                    ?>
                </div>
            </div>
        <?php endif; ?>
