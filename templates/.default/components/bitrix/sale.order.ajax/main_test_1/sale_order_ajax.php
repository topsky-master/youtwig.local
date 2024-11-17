<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

__IncludeLang(__DIR__."/lang/".LANGUAGE_ID."/template.php");

$result = array(
    'error' => 1
);


if (isset($_REQUEST['coupon'])
    && !empty($_REQUEST['coupon'])) {

    $number_coupon = trim($_REQUEST['coupon']);

    $getCoupon = \Bitrix\Sale\DiscountCouponsManager::getData($number_coupon, true); // получаем информацио о купоне

    if ($getCoupon['ACTIVE'] == "Y" && !$_SESSION['CATALOG_USER_COUPONS']) {

        $discountName = $getCoupon['DISCOUNT_NAME'];
        $takeCoupon   = \Bitrix\Sale\DiscountCouponsManager::add($number_coupon); // true - купон есть / false - его нет

        if ($takeCoupon) {
            $result['success'] = 1;
            $result['msgTxt'] = GetMessage('SOA_TEMPL_PROMO_ACTIVE');
        } else {
            $result['msgTxt'] = GetMessage('SOA_TEMPL_PROMO_ERROR');
        }

    } else if (!$getCoupon['ACTIVE']) {
        $result['msgTxt'] = GetMessage('SOA_TEMPL_PROMO_NOT_FOUND');
    } else {
        $result['msgTxt'] = GetMessage('SOA_TEMPL_PROMO_ALREADY_APPLIED');
    }

}

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
while ($arItems = $dbBasketItems->Fetch()) {
    if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"]) {
        //CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"], "N", $arItems["PRODUCT_PROVIDER_CLASS"]);
        $arID[] = $arItems["ID"];
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
        "NAME"
    ));
    while ($arItems = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arItems;
    }
}

// Печатаем массив, содержащий актуальную на текущий момент корзину

if (isset($_REQUEST['product_id'])
    && !empty($_REQUEST['product_id'])
    && is_array($_REQUEST['product_id'])
    && isset($_REQUEST['action'])
    && !empty($_REQUEST['action'])
    && isset($arBasketItems)
    && !empty($arBasketItems)) {

    $action = (string) $_REQUEST['action'];
    $action = trim($action);

    switch ($action) {
        case 'delete':

            foreach ($_REQUEST['product_id'] as $basket_id => $quantity) {
                $basket_id = (int) $basket_id;

                if (!empty($basket_id)) {

                    foreach ($arBasketItems as $arBasketItem) {
                        if ($arBasketItem['ID'] == $basket_id) {

                            CSaleBasket::Delete($arBasketItem['ID']);
                            $result['deleted'] = 1;

                        }
                        ;
                    }
                    ;

                }
                ;

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
                        $buy_id = $product_id;
                        $check_quantity = false;

                        $rsProducts = CCatalogProduct::GetList(array(), array(
                            'ID' => $buy_id
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

            //$result['VALUES'] = $arBasketItems;

            break;

    }

}

echo json_encode($result);
