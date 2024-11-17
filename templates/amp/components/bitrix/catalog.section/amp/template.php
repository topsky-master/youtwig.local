<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(false);

if (!empty($arResult['NAV_RESULT']))
{
    $navParams =  array(
        'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
        'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
        'NavNum' => $arResult['NAV_RESULT']->NavNum
    );
}
else
{
    $navParams = array(
        'NavPageCount' => 1,
        'NavPageNomer' => 1,
        'NavNum' => $this->randString()
    );
}

$showTopPager = false;
$showBottomPager = false;
$showLazyLoad = false;

if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1)
{
    $showTopPager = $arParams['DISPLAY_TOP_PAGER'];
    $showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'];
    $showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
}

$templateLibrary = array('popup', 'ajax', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES']))
{
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
);
unset($currencyList, $templateLibrary);

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$positionClassMap = array(
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
    {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
    {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
    }
}

$arParams['~MESS_BTN_BUY'] = $arParams['~MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_BUY');
$arParams['~MESS_BTN_DETAIL'] = $arParams['~MESS_BTN_DETAIL'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_DETAIL');
$arParams['~MESS_BTN_COMPARE'] = $arParams['~MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_COMPARE');
$arParams['~MESS_BTN_SUBSCRIBE'] = $arParams['~MESS_BTN_SUBSCRIBE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_SUBSCRIBE');
$arParams['~MESS_BTN_ADD_TO_BASKET'] = $arParams['~MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET');
$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE');
$arParams['~MESS_SHOW_MAX_QUANTITY'] = $arParams['~MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCS_CATALOG_SHOW_MAX_QUANTITY');
$arParams['~MESS_RELATIVE_QUANTITY_MANY'] = $arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['~MESS_RELATIVE_QUANTITY_FEW'] = $arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW');

$generalParams = array(
    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
    'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
    'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
    'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
    'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],
    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
    'COMPARE_PATH' => $arParams['COMPARE_PATH'],
    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
    'LABEL_POSITION_CLASS' => $labelPositionClass,
    'DISCOUNT_POSITION_CLASS' => $discountPositionClass,
    'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
    'SLIDER_PROGRESS' => $arParams['SLIDER_PROGRESS'],
    '~BASKET_URL' => $arParams['~BASKET_URL'],
    '~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
    '~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
    '~COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
    '~COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
    'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY'],
    'MESS_BTN_BUY' => $arParams['~MESS_BTN_BUY'],
    'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
    'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
    'MESS_BTN_ADD_TO_BASKET' => $arParams['~MESS_BTN_ADD_TO_BASKET'],
    'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'],
    "MODEL_NEW" => $arParams['MODEL_NEW'],
    "MODEL_NAME" => $arParams['MODEL_NAME'],
    "MANUFACTURER" => $arParams['MANUFACTURER'],
    "IS_MODEL_LIST" => "Y"

);

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
$containerName = 'container-'.$navParams['NavNum'];

if (!empty($arResult['ITEMS'])){

    $howSort = $arResult['howSort'];

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
        0 =>15);

    if(empty($element_count) && (($APPLICATION->get_cookie($element_count_param)))){
        $element_count = $APPLICATION->get_cookie($element_count_param);
    }

    $element_count = (int)$element_count;
    $element_count = !in_array($element_count,$pager) ? 15 : $element_count;
    $element_count = empty($element_count) ? 15 : $element_count;

    $arParams["PAGE_ELEMENT_COUNT"] = $element_count;

    $APPLICATION->set_cookie($element_count_param,$element_count);

    if ($showTopPager){
        ?>
        <form action="<?=$APPLICATION->GetCurUri();?>"
              target="_top"
              class="section-sort"
              id="sortform"
              name="sortform">
            <div class="ampstart-input inline-block">
                <select on="change:sortform.submit" id="sort" name="sort" class="select-field selectpicker">
                    <?php foreach ($howSort as $value=>$name): ?>
                        <option value="<?php echo urlencode($value); ?>"<?php if($value == $sort_code): ?> selected="selected"<?php endif; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if(isset($_REQUEST['PAGE_ELEMENT_COUNT']) && !empty($_REQUEST['PAGE_ELEMENT_COUNT'])): ?>
                <input type="hidden" value="<?=(int)($_REQUEST['PAGE_ELEMENT_COUNT']);?>" name="PAGE_ELEMENT_COUNT" />
            <?php endif; ?>
            <?php if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])): ?>
                <input type="hidden" value="<?=htmlentities($_REQUEST['q'],ENT_QUOTES,LANG_CHARSET);?>" name="q" />
            <?php endif; ?>
        </form>
    <? } ?>
    <div class="catalog-section-list" data-entity="<?=$containerName?>">
        <?

        $areaIds = array();

        ?>
        <?
        foreach ($arResult['ITEMS'] as $item)
        {

            $uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
            $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);


            ?>
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:catalog.item',
                'amp',
                array(
                    'RESULT' => array(
                        'ITEM' => $item,
                        'AREA_ID' => $areaIds[$item['ID']],
                        'TYPE' => 'CARD',
                        'BIG_LABEL' => 'N',
                        'BIG_DISCOUNT_PERCENT' => 'N',
                        'BIG_BUTTONS' => 'N',
                        'SCALABLE' => 'N'
                    ),
                    'PARAMS' => $generalParams
                        + array('SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']])
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );
            ?>
            <?
        }

        unset($generalParams, $rowItems);
        ?>
        <?


        ?>
    </div>
    <?php

    if ($showBottomPager){

        ?>
        <div class="pagination-wrapper">
            <form action="<?=$APPLICATION->GetCurUri();?>"
                  target="_top"
                  class="section-count"
                  id="countform"
                  name="countform">
                <?php if(isset($_REQUEST['PAGE_ELEMENT_COUNT']) && !empty($_REQUEST['PAGE_ELEMENT_COUNT'])): ?>
                    <input type="hidden" value="<?=(int)($_REQUEST['PAGE_ELEMENT_COUNT']);?>" name="PAGE_ELEMENT_COUNT" />
                <?php endif; ?>
                <?php if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])): ?>
                    <input type="hidden" value="<?=htmlentities($_REQUEST['sort'],ENT_QUOTES,LANG_CHARSET);?>" name="PAGE_ELEMENT_COUNT" />
                <?php endif; ?>
                <?php if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])): ?>
                    <input type="hidden" value="<?=htmlentities($_REQUEST['q'],ENT_QUOTES,LANG_CHARSET);?>" name="q" />
                <?php endif; ?>
            </form>
            <div data-pagination-num="<?=$navParams['NavNum']?>">
                <!-- pagination-container -->
                <?=$arResult['NAV_STRING']?>
                <!-- pagination-container -->
            </div>
            <?
            ?>
        </div>
        <?

    }

} else {


    $curPage = $APPLICATION->GetCurUri();
    $curPage = preg_replace('~filter/.*?$~', '', $curPage);

    ?>
    <div class="text-bottom error-404">
        <p><? echo sprintf(GetMessage('TMPL_NOT_FOUD_GO_BACK'),$curPage); ?></p>
    </div>
    <?

}

?>