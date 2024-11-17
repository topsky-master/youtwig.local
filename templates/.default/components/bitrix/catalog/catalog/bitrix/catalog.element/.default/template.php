<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(false);

$item = $arResult;

unset($arResult);

$name = !empty($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $item['NAME'];
$title = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $item['NAME'];
$alt = !empty($item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $item['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $item['NAME'];

if(    isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"])
    && isset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
    && !empty($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"])
){

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '<div class="full-detail-text">'.$item["DISPLAY_PROPERTIES"]["SEO_TEXT"]["~VALUE"]["TEXT"].'</div>';

};

unset($item["DISPLAY_PROPERTIES"]["SEO_TEXT"]);

if(     isset($item["DISPLAY_PROPERTIES"]["MODEL_HTML"])
    &&  isset($item["DISPLAY_PROPERTIES"]["MODEL_HTML"]["~VALUE"])
){

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '
        <p class="h4 h4-models-title">'.GetMessage('TMPL_SUITABLE_MODELS').'</p>
        <div class="suitable-models">';

    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= $item["DISPLAY_PROPERTIES"]["MODEL_HTML"]["~VALUE"];
    $item['TABS']['tab_panels']['DETAIL_TEXT'] .= '
        </div>';

}

unset($item["DISPLAY_PROPERTIES"]["MODEL_HTML"]);

$videoHtml = '';

if(isset($item['TABS']['tab_panels']['VIDEO'])
    && is_array($item['TABS']['tab_panels']['VIDEO'])
    && !empty($item['TABS']['tab_panels']['VIDEO'])){

    foreach ($item['TABS']['tab_panels']['VIDEO']['VALUE'] as $key => $value){

        if($value != "-"
            && $value != ""){

            $videoHtml .= '
            <div class="video embed-responsive embed-responsive-16by9 row">
                <iframe class="embed-responsive-item" src="'.$value.'" frameborder="0" allowfullscreen="allowfullscreen">
                </iframe>
            </div>';
        };

    };

};

unset($item['TABS']['tab_panels']['VIDEO']);

if(!empty($videoHtml)){
    $item['TABS']['tab_panels']['VIDEO'] = $videoHtml;
}

unset($videoHtml);

$showSliderControls = $item['MORE_PHOTO_COUNT'] > 1;

$price = $item['ITEM_PRICES'][$item['ITEM_PRICE_SELECTED']];
$measureRatio = $item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];

?>
    <div class="product-item row" itemscope itemtype="http://schema.org/Product">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 item-image">
            <?php if(isset($item['OLD_PRICE_PERCENTS'])
                && !empty($item['OLD_PRICE_PERCENTS'])): ?>
                <div class="old-bage">
                    -<?=$item['OLD_PRICE_PERCENTS'];?>%
                </div>
            <?php endif; ?>
            <?php
            if (!empty($item['MORE_PHOTO']))
            {
                if($item['MORE_PHOTO_COUNT'] > 1) {
                    ?>
                    <div id="pslider" class="pslider">
                    <?php
                }

                foreach ($item['MORE_PHOTO'] as $key => $photo)
                {
                    ?>
                    <div class="slide piiw">
                        <?php if(isset($item['MORE_PHOTO_BIG'])
                        && isset($item['MORE_PHOTO_BIG'][$key])
                        && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                        <a href="<?=$item['MORE_PHOTO_BIG'][$key]['SRC'];?>" class="lightbox" data-gallery="item-gallery" data-type="image" data-title="<?=$alt?>">
                            <?php endif; ?>
                            <img class="img-responsive" src="<?=$photo['SRC']?>" alt="<?=$alt?>"<?=($key == 0 ? ' itemprop="image"' : '')?> />
                            <?php if(isset($item['MORE_PHOTO_BIG'])
                            && isset($item['MORE_PHOTO_BIG'][$key])
                            && isset($item['MORE_PHOTO_BIG'][$key]['SRC'])): ?>
                        </a>
                    <?php endif; ?>
                    </div>
                    <?
                }

                if($item['MORE_PHOTO_COUNT'] > 1) {
                    ?>
                    </div>
                    <?php
                }

                if($item['MORE_PHOTO_COUNT'] > 1) {
                    ?>
                    <div id="psslider" class="psslider">
                        <?php

                        foreach ($item['MORE_PHOTO_THUMB'] as $key => $photo)
                        {
                            ?>
                            <div>
                                <img class="img-responsive" src="<?=$photo['SRC']?>" alt="<?=$alt?>" />
                            </div>
                            <?
                        }

                        ?>
                    </div>
                    <?php
                }


            }
            ?>
        </div>
        <div class="col-xs-12 col-sm-7 col-md-8 col-lg-5 item-info">
            <?php if(isset($item['ARTNUMBER'])
                && !empty($item['ARTNUMBER'])): ?>
                <div class="item-scu">
                    <?=$item['ARTNUMBER']['NAME'];?>
                    <span>
                        <?=$item['ARTNUMBER']['VALUE'];?>
                    </span>
                </div>
            <?php endif; ?>
            <h1 class="item-title">
                <?=$item['NAME']?>
            </h1>
            <?php if(isset($item['PREVIEW_TEXT']) && false): ?>
                <div class="item-info">
                    <?=$item['PREVIEW_TEXT']?>
                </div>
            <?php endif; ?>
            <meta itemprop="name" content="<?=$name?>" />
            <?php
            if (!empty($item['DISPLAY_PROPERTIES']))
            {

                ?>
                <dl class="item-props">
                    <?
                    foreach ($item['DISPLAY_PROPERTIES'] as $property)
                    {

                        ?>
                        <dt><?=$property['NAME']?></dt>
                        <dd><?=(is_array($property['DISPLAY_VALUE'])
                                ? implode(' / ', $property['DISPLAY_VALUE'])
                                : $property['DISPLAY_VALUE'])?>
                        </dd>
                        <?
                    }

                    unset($property);
                    ?>
                </dl>
                <?php
            }
            ?>

            <?php

            if (
                    isset($item['PROPERTIES'])
                    && isset($item['PROPERTIES']['ORIGINALS_CODES'])
                    && isset($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'])
                    && !empty($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'])

            )
            {

                $item['PROPERTIES']['ORIGINALS_CODES']['VALUE'] = !is_array($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'])
                    && !empty($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'])
                    ? array($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'])
                    : $item['PROPERTIES']['ORIGINALS_CODES']['VALUE'];

                ?>
                <dl class="item-props">
                    <?
                    foreach ($item['PROPERTIES']['ORIGINALS_CODES']['VALUE'] as $property)
                    {

                        if(!empty($property)){

                        $property = explode(':',$property, 2);

                        ?>
                        <dt><?=$property[0]?></dt>
                        <dd><?=$property[1]?>
                        </dd>
                        <?

                        }
                    }

                    unset($property);
                    ?>
                </dl>
                <?php

                unset($item['PROPERTIES']['ORIGINALS_CODES']);
            }
            ?>

            <?php if(!empty($item['ADMIN_MESSAGES'])) {

                $dataContent = '';

                foreach ($item['ADMIN_MESSAGES'] as $adminMessage){

                    $dataContent .=
                        '<li class="list-group-item">'
                        . '<strong>'
                        . $adminMessage['NAME']
                        . '</strong>'
                        . ' &ndash; '
                        . $adminMessage['VALUE']
                        .'</li>';

                }


                if(!empty($dataContent)){
                    $dataContent =
                        '<ul class="list-group admin-messages">'
                        . $dataContent
                        .'</ul>';


                    echo $dataContent;

                }

            }; ?>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3 item-cart">
            <div class="item-prices">
                <?
                if (!empty($item['OLD_PRICE'])) {
                    ?>
                    <span class="item-old">
                    <?=$item['OLD_PRICE'];?>
                </span>
                    <?
                }
                ?>
                <span class="item-price">
                <?
                if (!empty($price)) {
                    echo $price['PRINT_RATIO_PRICE'];
                }
                ?>
                </span>
            </div>
            <?

            if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
                CAPIUncachedArea::includeFile(
                    "/include/prices.php",
                    array(
                        'PRICE' => $price,
                        'NOT_MUCH' => $arParams['NOT_MUCH'],
                        'PRODUCT_ID' => $item['ID'],
                        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                        'ONE_CLICK_ORDER' => 'Y'
                    )
                );
            }

            $APPLICATION->IncludeComponent(
                "impel:cartpreorder",
                "",
                Array(
                    "CONSENT_PROCESSING_TEXT" => $item["CONSENT_PROCESSING_TEXT"]
                ),
                false
            );

            ?>
            <div class="item-cart-desc">
                <?

                $cart_description = '';

                if(!empty($item['CART_DESCRIPTION'])) {

                    $lines = $item['CART_DESCRIPTION'];

                    foreach($lines["SOCIAL_ICONS_LINKS"] as $number => $cartResAr){

                        $currentLine = "";

                        if(isset($cartResAr)
                            && !empty($cartResAr)){
                            $currentLine .= $cartResAr;
                        };

                        if(isset($lines["SOCIAL_TITLES"][$number])
                            && !empty($lines["SOCIAL_TITLES"][$number])){

                            if(isset($lines["TOOLTIP_TEXT"])
                                && !empty($lines["TOOLTIP_TEXT"])
                                && isset($lines["TOOLTIP_TEXT"][$number])
                                && !empty($lines["TOOLTIP_TEXT"][$number])){
                                $currentLine .= '<span role="button" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="'.htmlspecialcharsbx($lines["TOOLTIP_TEXT"][$number]).'">';
                            };

                            $currentLine .= $lines["SOCIAL_TITLES"][$number];

                            if(isset($lines["TOOLTIP_TEXT"])
                                && !empty($lines["TOOLTIP_TEXT"])
                                && isset($lines["TOOLTIP_TEXT"][$number])
                                && !empty($lines["TOOLTIP_TEXT"][$number])){
                                $currentLine .= '</span>';
                            };

                            if(!empty($currentLine)){
                                $cart_description .= "<p>".$currentLine."</p>";
                            };


                        };

                    };


                };

                echo $cart_description;

                ?>
            </div>
            <?php if($item['HAS_WARRANTY']): ?>
                <div class="item-warranty">
                    <?=GetMessage('TMPL_HAS_WARRANTY');?>
                </div>
            <?php endif; ?>
            <?php if(!empty($item['STOCK_PRINT_RATIO_PRICE'])): ?>
                <div class="item-stock-price">
                    <?=GetMessage('TMPL_STOCK_PRINT_RATIO_PRICE');?>
                    <span>
                        <?=$item['STOCK_PRINT_RATIO_PRICE'];?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row product-item">
        <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
            <?php if(!empty($item['TABS'])):
                $first_key = false;
                $tabs = $item['TABS'];
                ?>
                <div id="tabs">
                    <ul class="nav nav-tabs">
                        <?php foreach($tabs['tab_headers'] as $tab_key => $tab_name):?>
                            <li role="presentation"<?php if(!$first_key): $first_key = true; ?> class="active"<?php endif; ?>>
                                <a href="#<?=$tab_key;?>" aria-controls="<?=$tab_key;?>" role="tab" data-toggle="tab">
                                    <?php echo $tab_name; ?>
                                </a>
                            </li>
                        <?php endforeach;
                        $first_key = false;
                        ?>
                    </ul>
                    <div class="tab-content">
                        <?php foreach($tabs['tab_panels'] as $tab_key => $tab_content):?>
                            <div role="tabpanel" id="<?=$tab_key;?>" class="tab-pane<?php if(!$first_key): $first_key = true; ?> active<?php endif; ?>">
                                <?=$tab_content;?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                unset($tabs);
            endif;
            ?>
            <?php unset($item['TABS']); ?>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
        </div>
    </div>
<?

unset($emptyProductProperties, $item, $itemIds, $jsParams);
