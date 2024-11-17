<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

__IncludeLang(__DIR__."/lang/".LANGUAGE_ID."/template.php");

$result = array(
    'error' => 1
);

$arID = array();

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(array(
    "NAME" => "ASC",
    "ID" => "ASC"
), array(
    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
    "LID" => SITE_ID,
    "ORDER_ID" => "NULL"
), false, false, array(
    "ID",
    "CALLBACK_FUNC",
    "MODULE",
    "PRODUCT_ID",
    "QUANTITY",
    "PRODUCT_PROVIDER_CLASS"
));

if ($dbBasketItems) {
    while ($arItems = $dbBasketItems->Fetch()) {
        if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"]) {
            $arID[] = $arItems["ID"];
        }
    }
}
if (!empty($arID)) {
    $dbBasketItems = CSaleBasket::GetList(array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ), array(
        "ID" => $arID,
        "ORDER_ID" => "NULL"
    ), false, false, array(
        "ID",
        "CALLBACK_FUNC",
        "MODULE",
        "PRODUCT_ID",
        "QUANTITY",
        "DELAY",
        "CAN_BUY",
        "PRICE",
        "WEIGHT",
        "PRODUCT_PROVIDER_CLASS",
        "NAME",
        "DETAIL_PAGE_URL"
    ));
    while ($arItems = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arItems;
    }
}

if (isset($_REQUEST['product_id'])
    && !empty($_REQUEST['product_id'])
    && is_array($_REQUEST['product_id'])
    && isset($_REQUEST['action'])
    && !empty($_REQUEST['action'])
    && isset($arBasketItems)
    && !empty($arBasketItems)) {

    $action = trim((string) $_REQUEST['action']);

    switch ($action) {

        case 'get_artnumber':

            foreach ($_REQUEST['product_id'] as $basket_id => $quantity) {

                foreach ($arBasketItems as $arBasketItem) {

                    if ($arBasketItem['ID'] == $basket_id) {

                        $parts = explode('/',$arBasketItem['DETAIL_PAGE_URL']);
                        $parts = array_filter($parts);
                        $code = trim(end($parts));

                        if (!empty($code)) {

                            $dArtnumber = CIBlockElement::GetList(
                                array(),
                                ($aFilter = array(
                                    'CODE' => $code,
                                    'IBLOCK_ID' => 11
                                )
                                ),
                                false,
                                false,
                                ($aSelect = array(
                                    'ID',
                                    'PROPERTY_ARTNUMBER'
                                ))
                            );

                            if ($dArtnumber
                                && $product_data = $dArtnumber->GetNext()) {

                                $result['value'] = $product_data['PROPERTY_ARTNUMBER_VALUE'];
                                $result['name'] = GetMessage('TMPL_ARTNUMBER');

                                $result['updated'] = 1;

                            }

                        }

                    };

                };

            };

            break;

        case 'delete':

            foreach ($_REQUEST['product_id'] as $basket_id => $quantity) {

                $basket_id = (int) $basket_id;

                if (!empty($basket_id)) {
                    $ress = CSaleBasket::Delete($basket_id);
                    $result['updated'] = 1;
                };

            };

            $items_cnt = CSaleBasket::GetList(array(), array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ), array());

            $result['deleted'] = $items_cnt;

            break;

        case 'update':

            $result['VALUES'] = array();

            foreach ($_REQUEST['product_id'] as $basket_id => $quantity) {

                foreach ($arBasketItems as $arBasketItem) {
                    if ($arBasketItem['ID'] == $basket_id) {

                        $product_id = (int)$arBasketItem['PRODUCT_ID'];

                        $max_quantity   = false;
                        $check_quantity = false;

                        $rsProducts = CCatalogProduct::GetList(array(), array(
                            'ID' => $product_id
                        ), false, false, array(
                            'ID',
                            'CAN_BUY_ZERO',
                            'QUANTITY_TRACE',
                            'QUANTITY'
                        ));

                        if ($rsProducts && $arCatalogProduct = $rsProducts->Fetch()) {

                            $check_quantity = ($arCatalogProduct["QUANTITY_TRACE"] == 'Y');
                            if ($check_quantity) {
                                $max_quantity = get_quantity_product($product_id);
                            }

                        }

                        $quantity = $max_quantity
                        && $max_quantity < $quantity
                            ? (int) $max_quantity
                            : (int) $quantity;

                        if (!empty($quantity)) {

                            $arFields = array(
                                "QUANTITY" => $quantity
                            );

                            $result['VALUES'][] = CSaleBasket::Update($arBasketItem['ID'], $arFields);
                            $result['updated'] = 1;

                        }

                    }

                }

            }

            break;

    }

}

if (isset($result['updated'])) {
    unset($result['error']);
}

if (!headers_sent()) {
    header('Content-type: application/json');
}

echo json_encode($result);
