<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;
global $APPLICATION;
global $DB;

$ORDER_ID        = 0;
$USER_ID = 71015;
$product_id   = intval($_REQUEST['PRODUCT_ID']);

if(isset($_SESSION['has_order'][$product_id])
    && is_array($_SESSION['has_order'][$product_id])
    && sizeof($_SESSION['has_order'][$product_id]) > 1
    && (($_SESSION['has_order'][$product_id][0] + 600) > time())
) {



    $APPLICATION->RestartBuffer();

    if (isset($_REQUEST['amppreorder']) && !empty($_REQUEST['amppreorder'])) {


        $sdOrigin = IMPEL_PROTOCOL . IMPEL_SERVER_NAME;
        if (isset($_SERVER['HTTP_ORIGIN'])
            && in_array($_SERVER['HTTP_ORIGIN'], array($sdOrigin, 'https://youtwig-ru.cdn.ampproject.org'))
        ) {
            $sdOrigin = $_SERVER['HTTP_ORIGIN'];
        }

        header('HTTP/1.1 200 OK');
        header("access-control-allow-credentials:true");
        header("AMP-Same-Origin: true");
        header("Access-Control-Allow-Origin: " . $sdOrigin . "");
        header("Access-Control-Allow-Source-Origin: " . IMPEL_PROTOCOL . IMPEL_SERVER_NAME. "");
        header("amp-access-control-allow-source-origin: " . IMPEL_PROTOCOL . IMPEL_SERVER_NAME . "");
        header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
        header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
        header("access-control-allow-methods:POST, GET, OPTIONS");
        header("Content-Type: application/json");

    }

    $ORDER_ID = $_SESSION['has_order'][$product_id][1];

    echo json_encode(array(
        "ORDER_ID" => $ORDER_ID
    ));

    die();

}

$needLogout = false;

if(!$USER->IsAuthorized()){
    $needLogout = true;
    $USER->Authorize($USER_ID);
}


$defaultEmail    = 'system@youtwig.ru'; //COption::GetOptionString("main","email_from", "nobody@nobody.com");
$newlyRegistered = false;

$arResult = array(
    "ERROR" => array()
);

if (isset($_REQUEST['PAYER_NAME']) && isset($_REQUEST['PRODUCT_ID']) && isset($_REQUEST['PAYER_PHONE']) && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")) {

    IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/');
    IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/catalog/');
    IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/bitrix/sale.order.ajax/');

    //$arResult["ERROR"] = 1;

    $arUserResult = array(
        "DELIVERY_ID" => 41,
        "PAY_SYSTEM_ID" => 19,
        "PERSON_TYPE_ID" => 1,
        "PAYER_NAME" => trim($_REQUEST["PAYER_NAME"]),
        "EVENT_NAME" => "PRE_ORDER",
        "PAYED" => "N",
        "CANCELED" => "N",
        "STATUS_ID" => "D"

    );

    $NEW_EMAIL = (isset($_REQUEST["PAYER_EMAIL"]) && !empty($_REQUEST["PAYER_EMAIL"])) ? trim($_REQUEST["PAYER_EMAIL"]) : $defaultEmail;
    $NEW_EMAIL = empty($NEW_EMAIL) ? $defaultEmail : $NEW_EMAIL;

    $USER_FIO = $NEW_LOGIN = $arUserResult["PAYER_NAME"];
    $USER_FIO = trim($USER_FIO);


    $USER_PHONE = $_REQUEST["PAYER_PHONE"];
    $USER_PHONE = trim($USER_PHONE);

    $USER_EMAIL = isset($_REQUEST["PAYER_EMAIL"]) ? trim($_REQUEST["PAYER_EMAIL"]) : '';
    $USER_EMAIL = trim($USER_EMAIL);

    $AJAX_LOCATION = isset($_REQUEST["AJAX_LOCATION"]) ? trim($_REQUEST["AJAX_LOCATION"]) : '';
    $AJAX_LOCATION = trim($AJAX_LOCATION);



    $arParams = array(
        "DELIVERY_TO_PAYSYSTEM" => "d2p"
    );
    $is_error = false;

    if (empty($arResult["ERROR"])) {

        $productPrice = CCatalogProduct::GetOptimalPrice($product_id, 1, $GROUP_ID);

        if(!empty($productPrice)
            && isset($productPrice['RESULT_PRICE'])
            && isset($productPrice['RESULT_PRICE']['BASE_PRICE'])
            && isset($productPrice['RESULT_PRICE']['CURRENCY'])
        ){

            $about_product['REMAINS_IN_STOCK'] = get_quantity_product($product_id, true);

            $arSelect = Array(
                "CATALOG_WEIGHT",
                "NAME",
                "IBLOCK_TYPE_ID",
                "IBLOCK_ID"
            );
            $arFilter = Array(
                "ID" => IntVal($product_id)
            );


            $res = CIBlockElement::GetList(Array(
                "SORT" => "ASC"
            ), $arFilter, false, Array(
                "nTopCount" => 1
            ), $arSelect);

            $arFields                   = array();
            $arFields["CATALOG_WEIGHT"] = 0;
            $arFields["MAX_DIMENSIONS"] = "";

            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();


                $arFields["MAX_DIMENSIONS"] = array(
                    $arFields["CATALOG_WIDTH"],
                    $arFields["CATALOG_HEIGHT"],
                    $arFields["CATALOG_LENGTH"]
                );

                $about_product['BASE_NAME'] = $arFields["NAME"];
                $about_product['BASE_URL']  = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $arFields['IBLOCK_ID'] . '&type=' . $arFields['IBLOCK_TYPE_ID'] . '&ID=' . $product_id;
            }


            $arResult = array(
                "ORDER_WEIGHT" => $arFields["CATALOG_WEIGHT"],
                "ORDER_PRICE" => $productPrice['RESULT_PRICE']['BASE_PRICE'],
                "BASE_LANG_CURRENCY" => getCurrentCurrencyCode()
            );

            if (!empty($arFields["MAX_DIMENSIONS"])) {
                $arResult["MAX_DIMENSIONS"] = $arFields["MAX_DIMENSIONS"];
            }
            ;


            $arFilter = array(
                "SID" => $arUserResult["DELIVERY_ID"],
                "COMPABILITY" => array(
                    "WEIGHT" => $arResult["ORDER_WEIGHT"],
                    "PRICE" => $arResult["ORDER_PRICE"]
                )
            );

            $bFirst               = true;
            $arDeliveryServiceAll = array();
            $bFound               = false;

            $rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array(
                "SORT" => "ASC"
            ), $arFilter);

            while ($arDeliveryService = $rsDeliveryServicesList->Fetch()) {
                if (!is_array($arDeliveryService) || !is_array($arDeliveryService["PROFILES"]))
                    continue;

                if (!empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") !== false) {
                    foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile) {
                        if ($arDeliveryProfile["ACTIVE"] == "Y") {
                            $delivery_id = $arDeliveryService["SID"];
                            if ($arUserResult["DELIVERY_ID"] == $delivery_id . ":" . $profile_id)
                                $bFound = true;
                        }
                    }
                }

                $arDeliveryServiceAll[] = $arDeliveryService;
            }

            if (!$bFound && !empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") !== false) {
                $arUserResult["DELIVERY_ID"]         = "";
                $arResult["DELIVERY_PRICE"]          = 0;
                $arResult["DELIVERY_PRICE_FORMATED"] = "";
            }

            //select delivery to paysystem
            $arUserResult["PAY_SYSTEM_ID"] = IntVal($arUserResult["PAY_SYSTEM_ID"]);
            $arUserResult["DELIVERY_ID"]   = trim($arUserResult["DELIVERY_ID"]);
            $bShowDefaultSelected          = True;
            $arD2P                         = array();
            $arP2D                         = array();
            $delivery                      = "";
            $bSelected                     = false;

            $dbRes = CSaleDelivery::GetDelivery2PaySystem(array());
            while ($arRes = $dbRes->Fetch()) {
                $arD2P[$arRes["DELIVERY_ID"]][$arRes["PAYSYSTEM_ID"]] = $arRes["PAYSYSTEM_ID"];
                $arP2D[$arRes["PAYSYSTEM_ID"]][$arRes["DELIVERY_ID"]] = $arRes["DELIVERY_ID"];
                $bShowDefaultSelected                                 = False;
            }

            if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
                $arP2D = array();

            if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d") {
                if (IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0) {
                    $bFirst      = True;
                    $arFilter    = array(
                        "ACTIVE" => "Y",
                        "PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
                        "PSA_HAVE_PAYMENT" => "Y"
                    );
                    $dbPaySystem = CSalePaySystem::GetList(array(
                        "SORT" => "ASC",
                        "PSA_NAME" => "ASC"
                    ), $arFilter);
                    while ($arPaySystem = $dbPaySystem->Fetch()) {
                        if (IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0 && $bFirst) {
                            $arPaySystem["CHECKED"]        = "Y";
                            $arUserResult["PAY_SYSTEM_ID"] = $arPaySystem["ID"];
                        }
                        $bFirst = false;
                    }
                }
            }

            $bFirst = True;
            $bFound = false;

            //select calc delivery
            foreach ($arDeliveryServiceAll as $arDeliveryService) {
                foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile) {
                    if ($arDeliveryProfile["ACTIVE"] == "Y" && (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) <= 0 || in_array($arDeliveryService["SID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]]) || empty($arD2P[$arDeliveryService["SID"]]))) {
                        $delivery_id = $arDeliveryService["SID"];
                        $arProfile   = array(
                            "SID" => $profile_id,
                            "TITLE" => $arDeliveryProfile["TITLE"],
                            "DESCRIPTION" => $arDeliveryProfile["DESCRIPTION"],
                            "FIELD_NAME" => "DELIVERY_ID"
                        );


                        if ((mb_strlen($arUserResult["DELIVERY_ID"]) > 0 && $arUserResult["DELIVERY_ID"] == $delivery_id . ":" . $profile_id)) {
                            $arProfile["CHECKED"]        = "Y";
                            $arUserResult["DELIVERY_ID"] = $delivery_id . ":" . $profile_id;
                            $bSelected                   = true;

                            $arOrderTmpDel = array(
                                "PRICE" => $arResult["ORDER_PRICE"],
                                "WEIGHT" => $arResult["ORDER_WEIGHT"],
                                "LOCATION_FROM" => COption::GetOptionInt('sale', 'location')
                            );

                            $arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($delivery_id, $profile_id, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

                            if ($arDeliveryPrice["RESULT"] == "ERROR") {
                                $arResult["ERROR"][] = $arDeliveryPrice["TEXT"];
                            } else {
                                $arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
                                $arResult["PACKS_COUNT"]    = $arDeliveryPrice["PACKS_COUNT"];
                            }
                        }

                        if (empty($arResult["DELIVERY"][$delivery_id])) {
                            $arResult["DELIVERY"][$delivery_id] = array(
                                "SID" => $delivery_id,
                                "SORT" => $arDeliveryService["SORT"],
                                "TITLE" => $arDeliveryService["NAME"],
                                "DESCRIPTION" => $arDeliveryService["DESCRIPTION"],
                                "PROFILES" => array()
                            );
                        }

                        $arDeliveryExtraParams = CSaleDeliveryHandler::GetHandlerExtraParams($delivery_id, $profile_id, $arOrderTmpDel, SITE_ID);

                        if (!empty($arDeliveryExtraParams)) {
                            $arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] = "Y";
                        } else {
                            $arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] = "N";
                        }

                        if (!empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") !== false) {
                            if ($arUserResult["DELIVERY_ID"] == $delivery_id . ":" . $profile_id)
                                $bFound = true;
                        }

                        $arResult["DELIVERY"][$delivery_id]["LOGOTIP"]               = $arDeliveryService["LOGOTIP"];
                        $arResult["DELIVERY"][$delivery_id]["PROFILES"][$profile_id] = $arProfile;
                        $bFirst                                                      = false;
                    }
                }
            }
            if (!$bFound && !empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") !== false)
                $arUserResult["DELIVERY_ID"] = "";

            /*Old Delivery*/
            $arStoreId     = array();
            $arDeliveryAll = array();
            $bFound        = false;
            $bFirst        = true;

            $dbDelivery = CSaleDelivery::GetList(array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ), array(
                "LID" => SITE_ID,
                "+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
                "+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
                "+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
                "+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
                "ACTIVE" => "Y"
            ));
            while ($arDelivery = $dbDelivery->Fetch()) {
                $arStore = array();
                if (mb_strlen($arDelivery["STORE"]) > 0) {
                    $arStore = unserialize($arDelivery["STORE"]);
                    foreach ($arStore as $val)
                        $arStoreId[$val] = $val;
                }

                $arDelivery["STORE"] = $arStore;

                if (isset($_POST["BUYER_STORE"]) && in_array($_POST["BUYER_STORE"], $arStore)) {
                    $arUserResult['DELIVERY_STORE'] = $arDelivery["ID"];
                }

                $arDeliveryDescription     = CSaleDelivery::GetByID($arDelivery["ID"]);
                $arDelivery["DESCRIPTION"] = htmlspecialcharsbx($arDeliveryDescription["DESCRIPTION"]);

                $arDeliveryAll[] = $arDelivery;

                if (!empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") === false) {
                    if (IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"]))
                        $bFound = true;
                }
                if (IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"])) {
                    $arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                }
            }
            if (!$bFound && !empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") === false) {
                $arUserResult["DELIVERY_ID"] = "";
            }

            $arStore = array();
            $dbList  = CCatalogStore::GetList(array(
                "SORT" => "DESC",
                "ID" => "DESC"
            ), array(
                "ACTIVE" => "Y",
                "ID" => $arStoreId,
                "ISSUING_CENTER" => "Y",
                "+SITE_ID" => SITE_ID
            ), false, false, array(
                "ID",
                "TITLE",
                "ADDRESS",
                "DESCRIPTION",
                "IMAGE_ID",
                "PHONE",
                "SCHEDULE",
                "GPS_N",
                "GPS_S",
                "ISSUING_CENTER",
                "SITE_ID"
            ));
            while ($arStoreTmp = $dbList->Fetch()) {
                if ($arStoreTmp["IMAGE_ID"] > 0)
                    $arStoreTmp["IMAGE_ID"] = CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);

                $arStore[$arStoreTmp["ID"]] = $arStoreTmp;
            }

            $arResult["STORE_LIST"] = $arStore;

            if (!$bFound && !empty($arUserResult["DELIVERY_ID"]) && mb_strpos($arUserResult["DELIVERY_ID"], ":") === false)
                $arUserResult["DELIVERY_ID"] = "";

            foreach ($arDeliveryAll as $arDelivery) {
                if (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) <= 0 || in_array($arDelivery["ID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]])) {
                    $arDelivery["FIELD_NAME"] = "DELIVERY_ID";
                    if ((IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"]))) {
                        $arDelivery["CHECKED"]       = "Y";
                        $arUserResult["DELIVERY_ID"] = $arDelivery["ID"];
                        $arResult["DELIVERY_PRICE"]  = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                        $bSelected                   = true;
                    }
                    if (IntVal($arDelivery["PERIOD_FROM"]) > 0 || IntVal($arDelivery["PERIOD_TO"]) > 0) {
                        $arDelivery["PERIOD_TEXT"] = GetMessage("SALE_DELIV_PERIOD");
                        if (IntVal($arDelivery["PERIOD_FROM"]) > 0)
                            $arDelivery["PERIOD_TEXT"] .= " " . GetMessage("SOA_FROM") . " " . IntVal($arDelivery["PERIOD_FROM"]);
                        if (IntVal($arDelivery["PERIOD_TO"]) > 0)
                            $arDelivery["PERIOD_TEXT"] .= " " . GetMessage("SOA_TO") . " " . IntVal($arDelivery["PERIOD_TO"]);
                        if ($arDelivery["PERIOD_TYPE"] == "H")
                            $arDelivery["PERIOD_TEXT"] .= " " . GetMessage("SOA_HOUR") . " ";
                        elseif ($arDelivery["PERIOD_TYPE"] == "M")
                            $arDelivery["PERIOD_TEXT"] .= " " . GetMessage("SOA_MONTH") . " ";
                        else
                            $arDelivery["PERIOD_TEXT"] .= " " . GetMessage("SOA_DAY") . " ";
                    }

                    if (intval($arDelivery["LOGOTIP"]) > 0)
                        $arDelivery["LOGOTIP"] = CFile::GetFileArray($arDelivery["LOGOTIP"]);

                    $arDelivery["PRICE_FORMATED"]            = SaleFormatCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"]);
                    $arResult["DELIVERY"][$arDelivery["ID"]] = $arDelivery;
                    $bFirst                                  = false;
                }
            }


            if (!$bSelected && !empty($arResult["DELIVERY"])) {
                $bf = true;
                foreach ($arResult["DELIVERY"] as $k => $v) {
                    if ($bf) {
                        if (IntVal($k) > 0) {
                            $arResult["DELIVERY"][$k]["CHECKED"] = "Y";
                            $arUserResult["DELIVERY_ID"]         = $k;
                            $bf                                  = false;

                            $arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arResult["DELIVERY"][$k]["PRICE"], $arResult["DELIVERY"][$k]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
                        } else {
                            foreach ($v["PROFILES"] as $kk => $vv) {
                                if ($bf) {
                                    $arResult["DELIVERY"][$k]["PROFILES"][$kk]["CHECKED"] = "Y";
                                    $arUserResult["DELIVERY_ID"]                          = $k . ":" . $kk;
                                    $bf                                                   = false;

                                    $arOrderTmpDel = array(
                                        "PRICE" => $arResult["ORDER_PRICE"],
                                        "WEIGHT" => $arResult["ORDER_WEIGHT"],
                                        "LOCATION_FROM" => COption::GetOptionInt('sale', 'location')
                                    );

                                    $arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($k, $kk, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

                                    if ($arDeliveryPrice["RESULT"] == "ERROR") {
                                        $arResult["ERROR"][] = $arDeliveryPrice["TEXT"];
                                    } else {
                                        $arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
                                        $arResult["PACKS_COUNT"]    = $arDeliveryPrice["PACKS_COUNT"];
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if ($arUserResult["PAY_SYSTEM_ID"] > 0 || mb_strlen($arUserResult["DELIVERY_ID"]) > 0) {
                if (mb_strlen($arUserResult["DELIVERY_ID"]) > 0 && $arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p") {
                    if (mb_strpos($arUserResult["DELIVERY_ID"], ":")) {
                        $tmp      = explode(":", $arUserResult["DELIVERY_ID"]);
                        $delivery = trim($tmp[0]);
                    } else
                        $delivery = intval($arUserResult["DELIVERY_ID"]);
                }
            }

            if (DoubleVal($arResult["DELIVERY_PRICE"]) > 0)
                $arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

            if (!empty($arResult["DELIVERY_PRICE"])) {
                $productPrice['RESULT_PRICE']['BASE_PRICE'] += $arResult["DELIVERY_PRICE"];
            }


            $arFields = array(
                "LID" => SITE_ID,
                "PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
                "PRICE" => $productPrice['RESULT_PRICE']['BASE_PRICE'],
                "PAYED" => $arUserResult["PAYED"],
                "CANCELED" => $arUserResult["CANCELED"],
                "STATUS_ID" => $arUserResult["STATUS_ID"],
                "CURRENCY" => $productPrice['RESULT_PRICE']['CURRENCY'],
                "USER_ID" => $USER_ID,
                "USER_DESCRIPTION" => "",
                "PAY_SYSTEM_ID" => $arUserResult["PAY_SYSTEM_ID"],
                "PRICE_DELIVERY" => $arResult["DELIVERY_PRICE"],
                "DELIVERY_ID" => $arUserResult["DELIVERY_ID"],
                "DISCOUNT_VALUE" => "",
                "TAX_VALUE" => 0.0
            );

            $fields = Array(
                "UF_SMS_INFORM" => 0
            );

            $ORDER_ID = IntVal(CSaleOrder::Add($arFields));

            //if($ex = $APPLICATION->GetException()) $arResult["ERROR"][] = $ex->GetString();

            if ($ORDER_ID > 0) {

                if (Add2BasketByProductIDOld($product_id, 1, array(
                    'ORDER_ID' => $ORDER_ID,
                    'preorder' => 1
                ), array())) {

                    $order = \Bitrix\Sale\Order::loadByAccountNumber($ORDER_ID);
                    $propertyCollection = $order->getPropertyCollection();

                    if ($arProp = CSaleOrderProps::GetList(array(), array(
                        'CODE' => 'PACKED'
                    ))) {

                        $arProp = $arProp->Fetch();

                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                        if($propertyValue) {

                            $propertyValue->setField('VALUE', "Нет");
                            $propertyValue->save();
                        }
                    }

                    if ($arProp = CSaleOrderProps::GetList(array(), array(
                        'CODE' => 'HANDED_COURIER'
                    ))) {

                        $arProp = $arProp->Fetch();

                        $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                        if($propertyValue) {

                            $propertyValue->setField('VALUE', "Нет");
                            $propertyValue->save();
                        }

                    }

                    if (!empty($USER_FIO)) {

                        if ($arProp = CSaleOrderProps::GetList(array(), array(
                            'CODE' => 'FIO'
                        ))) {

                            $arProp = $arProp->Fetch();

                            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                            if($propertyValue) {

                                $propertyValue->setField('VALUE', $USER_FIO);
                                $propertyValue->save();
                            }

                            $about_product['ORDER_USER_FIO'] = $USER_FIO;

                        }
                    }

                    if (!empty($AJAX_LOCATION)) {

                        if ($arProp = CSaleOrderProps::GetList(array(), array(
                            'CODE' => 'PREURL'
                        ))) {

                            $arProp = $arProp->Fetch();


                            $rElt = CIBlockElement::GetList([],['ID' => $product_id], false, ["nTopCount" => 1], ['ID', 'CODE', 'DETAIL_PAGE_URL']);

                            if ($rElt) {
                                while ($aElt = $rElt->GetNext()) {
                                    $code = '/'.$aElt['CODE'].'/';
                                    $id = '/'.$product_id.'/';

                                    if (stripos($AJAX_LOCATION,$code) === false && stripos($AJAX_LOCATION,$id) === false) {
                                        $AJAX_LOCATION = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . $aElt['DETAIL_PAGE_URL'];
                                    }
                                }
                            }


                            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                            if($propertyValue) {
                                $propertyValue->setField('VALUE', $AJAX_LOCATION);
                                $propertyValue->save();
                            }

                            $about_product['ORDER_USER_AJAX_LOCATION'] = $AJAX_LOCATION;

                        }

                    }


                    if (!empty($USER_EMAIL)) {

                        if ($arProp = CSaleOrderProps::GetList(array(), array(
                            'CODE' => 'EMAIL'
                        ))) {

                            $arProp = $arProp->Fetch();

                            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                            if($propertyValue) {
                                $propertyValue->setField('VALUE', $USER_EMAIL);
                                $propertyValue->save();
                            }

                            $about_product['ORDER_USER_EMAIL'] = $USER_EMAIL;

                        }

                    }

                    if (!empty($USER_PHONE)) {

                        if ($arProp = CSaleOrderProps::GetList(array(), array(
                            'CODE' => 'PHONE'
                        ))) {

                            $arProp = $arProp->Fetch();

                            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

                            if($propertyValue){
                                $propertyValue->setField('VALUE', $USER_PHONE);
                                $propertyValue->save();
                            }

                            $about_product['ORDER_USER_PHONE'] = $USER_PHONE;


                        }

                    }

                    $about_product['ORDER_ID']       = $ORDER_ID;
                    $about_product['ORDER_PRICE']    = SaleFormatCurrency($productPrice['RESULT_PRICE']['BASE_PRICE'], $productPrice['RESULT_PRICE']['CURRENCY']);
                    $about_product['ORDER_LINK']     = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/admin/sale_order_detail.php?ID=' . $ORDER_ID;
                    $about_product['PRICE_DELIVERY'] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $productPrice['RESULT_PRICE']['CURRENCY']);

                    CEvent::SendImmediate($arUserResult['EVENT_NAME'], SITE_ID, $about_product);

                    $arOrder = array(
                        'ID' => $ORDER_ID,
                        'STATUS_ID' => $arUserResult["STATUS_ID"],
                        'ACCOUNT_NUMBER' => '',
                        'PRICE' => ($productPrice['RESULT_PRICE']['BASE_PRICE'] + $arFields['PRICE_DELIVERY']),
                        'PRICE_DELIVERY' => $arFields['PRICE_DELIVERY'],
                        'DELIVERY_DOC_NUM' => '',
                        'DELIVERY_DOC_DATE' => '',
                        'DELIVERY_ID' => $arUserResult["DELIVERY_ID"],
                        'USER_ID' => $USER_ID,
                        'LID' => SITE_ID
                    );

                    //foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepSMS", true) as $arEvent)
                    //ExecuteModuleEventEx($arEvent, Array($ORDER_ID, $arOrder));

                    $strOrderList  = "";
                    $dbBasketItems = CSaleBasket::GetList(array(
                        "NAME" => "ASC"
                    ), array(
                        "ORDER_ID" => $ORDER_ID
                    ), false, false, array(
                        "ID",
                        "NAME",
                        "QUANTITY",
                        "PRICE",
                        "CURRENCY"
                    ));
                    while ($arBasketItems = $dbBasketItems->Fetch()) {
                        $strOrderList .= $arBasketItems["NAME"] . " - " . $arBasketItems["QUANTITY"] . " " . GetMessage("SOA_SHT") . ": " . SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
                        $strOrderList .= "\n";
                    }


                } else {

                    $eventName  = "UNSUCCESS_PREORDER";
                    $product_id = intval($_REQUEST['PRODUCT_ID']);

                    $arEventFields = Array(
                        "ORDER_ID" => $ORDER_ID,
                        "ORDER_LINK" => IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/admin/sale_order_view.php?ID=' . $ORDER_ID . '&lang=ru&filter=Y&set_filter=Y',
                        "PRODUCT_LINK" => IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID=' . $product_id . '&lang=ru&WF=Y'
                    );



                    if ($buy_id != $product_id) {
                        $arEventFields["BASE_LINK"] = IMPEL_PROTOCOL . IMPEL_SERVER_NAME . '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=16&type=catalog&ID=' . $buy_id . '&lang=ru&WF=Y';
                    }

                    $bError = array();

                    $event = new CEvent;
                    $event->SendImmediate($eventName, SITE_ID, $arEventFields, "N");

                    CEventLog::Add(array(
                        "SEVERITY" => "WARNING",
                        "AUDIT_TYPE_ID" => "PREORDER_TYPE",
                        "MODULE_ID" => "sale",
                        "ITEM_ID" => $ORDER_ID,
                        "DESCRIPTION" => "Не удалось добавить в предзаказ товар #" . $product_id . ', возможно, у товара отсутствует цена'

                    ));



                    $ORDER_ID = 0;



                }

            }
        }
    }
}

if (empty($ORDER_ID)) {
    $arResult["ERROR"][] = GetMessage('SI_MARKED');
}

if($needLogout){
    $USER->Logout();
}

$APPLICATION->RestartBuffer();

if (isset($_REQUEST['amppreorder']) && !empty($_REQUEST['amppreorder'])) {

    $sdOrigin = IMPEL_PROTOCOL . IMPEL_SERVER_NAME ;
    if(isset($_SERVER['HTTP_ORIGIN'])
        && in_array($_SERVER['HTTP_ORIGIN'],array($sdOrigin,'https://youtwig-ru.cdn.ampproject.org'))
    ){
        $sdOrigin = $_SERVER['HTTP_ORIGIN'];
    }

    header('HTTP/1.1 200 OK');
    header("access-control-allow-credentials:true");
    header("AMP-Same-Origin: true");
    header("Access-Control-Allow-Origin: " . $sdOrigin. "");
    header("Access-Control-Allow-Source-Origin: " . IMPEL_PROTOCOL . IMPEL_SERVER_NAME. "");
    header("amp-access-control-allow-source-origin: " . IMPEL_PROTOCOL . IMPEL_SERVER_NAME . "");
    header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
    header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
    header("access-control-allow-methods:POST, GET, OPTIONS");
    header("Content-Type: application/json");

}

if (isset($arResult["ERROR"]) && !empty($arResult["ERROR"])) {
    echo json_encode(array(
        "ERROR" => $arResult["ERROR"]
    ));
} else {

    $_SESSION['has_order'][$product_id] = array(time(),$ORDER_ID);
    echo json_encode(array(
        "ORDER_ID" => $ORDER_ID
    ));
};

session_write_close();

die();