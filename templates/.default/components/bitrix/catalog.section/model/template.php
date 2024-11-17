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
//$this->addExternalCss('/bitrix/css/main/bootstrap.css');

if (!empty($arResult['ITEMS']) && (($arParams['hasError404'] != 'Y'))) {
    ?>
    <div class="models-list row">
        <?
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

        $arParams['MESS_BTN_LAZY_LOAD'] = $arParams['MESS_BTN_LAZY_LOAD'] ?: Loc::getMessage('CT_BCS_CATALOG_MESS_BTN_LAZY_LOAD');

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
            'NOT_MUCH' => $arParams['NOT_MUCH']
        );

        $obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
        $containerName = 'container-'.$navParams['NavNum'];

        if (!empty($arResult['ITEMS']) && !empty($arResult['ITEM_ROWS']))
        {
            $areaIds = array();

            foreach ($arResult['ITEMS'] as $item)
            {
                $uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
                $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
                $this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
                $this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);
            }


            foreach ($arResult['ITEM_ROWS'] as $rowData)
            {
                $rowItems = array_splice($arResult['ITEMS'], 0, $rowData['COUNT']);
                $rowData['VARIANT'] = 9;

                foreach ($rowItems as $number => $item)
                {

                    $generalParams["MODEL_NEW"] = isset($arParams['MODEL_NEW'])
                        ? $arParams['MODEL_NEW']
                        : '';

                    $generalParams["MODEL_NAME"] = isset($arParams['MODEL_NAME'])
                        ? $arParams['MODEL_NAME']
                        : '';

                    $generalParams["MANUFACTURER"] = isset($arParams['MANUFACTURER'])
                        ? $arParams['MANUFACTURER']
                        : '';

                    $generalParams["TYPE_OF_PRODUCT"] = isset($arParams['TYPE_OF_PRODUCT'])
                        ? $arParams['TYPE_OF_PRODUCT']
                        : '';

                    $generalParams["POSITION"] = isset($arParams['POSITION'][$item['ID']])
                        ? $arParams['POSITION'][$item['ID']]
                        : '';

                    $generalParams["IS_VERSION"] = !empty($arParams['IS_VERSION'])
                        ? $arParams['IS_VERSION']
                        : '';

                    $generalParams["INDCODE"] = isset($arParams['INDCODE'])
                        ? $arParams['INDCODE']
                        : '';

                    $APPLICATION->IncludeComponent(
                        'bitrix:catalog.item',
                        'model',
                        array(
                            'RESULT' => array(
                                'ITEM' => $item,
                                'AREA_ID' => $areaIds[$item['ID']],
                                'TYPE' => $rowData['TYPE'],
                                'BIG_LABEL' => 'N',
                                'BIG_DISCOUNT_PERCENT' => 'N',
                                'BIG_BUTTONS' => 'N'
                            ),
                            'PARAMS' => $generalParams
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );

                }


            }
            unset($generalParams, $rowItems);

        }
        ?>
    </div>
    <?

    if(Bitrix\Main\Loader::includeModule('api.uncachedarea')) {
        CAPIUncachedArea::includeFile(
            "/local/include/preorderList.php",
            array(
                "CONSENT_PROCESSING_TEXT" => $arResult["CONSENT_PROCESSING_TEXT"]

            )
        );
    }

} else {

    ?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert">
        <? if(isset($arResult['SECTION_PAGE_URL'])
            && !empty($arResult['SECTION_PAGE_URL'])): ?>
            <?  echo sprintf(GetMessage('CT_BCS_TPL_ELEMENTS_NOT_FOUND'),$arResult['SECTION_PAGE_URL']); ?>
        <? elseif(isset($arResult['LIST_PAGE_URL'])
            && !empty($arResult['LIST_PAGE_URL'])): ?>
            <?  echo sprintf(GetMessage('CT_BCS_TPL_ELEMENTS_NOT_FOUND'),$arResult['LIST_PAGE_URL']); ?>
        <? else: ?>
            <?  echo sprintf(GetMessage('CT_BCS_TPL_ELEMENTS_NOT_FOUND'),"javascript:history.go(-1);"); ?>
        <? endif; ?>
    </div>
    <?

}

?>