<?php

if(!defined('CATALOG_INCLUDED')) die();

AddEventHandler("sale", "OnBasketUpdate", "OnBasketUpdateHandler");

function OnBasketUpdateHandler($ID, $arFields){

    global $APPLICATION;

    if(     $APPLICATION->GetCurPage()      =='/bitrix/admin/sale_order_create.php'
        ||  $APPLICATION->GetCurPage()      =='/bitrix/admin/sale_order_edit.php'){

        if(isset($_REQUEST['PRODUCT']) && !empty($_REQUEST['PRODUCT'])){

            if(class_exists('CCatalogProductProviderCustom')){
                $product_provider_class = 'CCatalogProductProviderCustom';
            } else {
                $product_provider_class	= 'CCatalogProductProvider';
            };


            foreach($_REQUEST['PRODUCT'] as $number => $name){

                if(     isset($arFields['PRODUCT_ID'])
                    && !empty($arFields['PRODUCT_ID'])
                    &&  isset($name['PRODUCT_ID'])
                    && !empty($name['PRODUCT_ID'])
                    &&  isset($arFields['NAME'])
                    && !empty($arFields['NAME'])
                    &&  isset($name['NAME'])
                    && !empty($name['NAME'])
                    && $arFields['PRODUCT_ID'] == $name['PRODUCT_ID']
                    && $name['NAME'] != $arFields['NAME']
                ){

                    $arFields['NAME'] = trim($name['NAME']);
                    CSaleBasket::Update($ID,$arFields);

                }

            }

        }

    }

    return true;
}

AddEventHandler("sale", "OnBeforeBasketUpdate", "change_cart");
AddEventHandler("sale", "OnBeforeBasketAdd", "change_cart");

function change_cart(&$arFields){

    global $APPLICATION,$USER,$MESS;

    $CURRENCY_CODE 							= getCurrentCurrencyCode();

    if($arFields["CURRENCY"] 				!= $CURRENCY_CODE && !empty($arFields["PRICE"])){
        $arFields["PRICE"] 				= CCurrencyRates::ConvertCurrency($arFields["PRICE"],$arFields["CURRENCY"],$CURRENCY_CODE,"",$arFields["PRODUCT_ID"]);
        $arFields["CURRENCY"]			= $CURRENCY_CODE;
    }

    if(IBLOCK_INCLUDED):

        if(isset($_SESSION['IN_FIRST_TIME']) && $_SESSION['IN_FIRST_TIME']){
            unset($_SESSION['IN_FIRST_TIME']);
            return true;
        }

        $onlyone								= false;

        $product_id								= $arFields["PRODUCT_ID"];

        $res		 							= CIBlockElement::GetByID($product_id);
        if(is_object($res) && method_exists($res,'GetNext')){
            if($ar_res 							= $res->GetNext()){
                $iblock_id						= $ar_res['IBLOCK_ID'];
                $db_props 						= CIBlockElement::GetProperty($iblock_id, $product_id, array("sort" => "asc"), Array("CODE"=>"onlyone"));

                if(is_object($db_props) && method_exists($db_props,'Fetch')){
                    while($ar_props 			= $db_props->Fetch()){
                        $onlyone				= $ar_props['VALUE_XML_ID'];
                    };
                };
            };
        };

        if($onlyone 							== 1){

            $backet								= array();
            $backet				    			= CSaleBasket::GetList(
                false,
                false,
                array(	'PRODUCT_ID'			=> $arFields['PRODUCT_ID'],
                    'USER_ID' 				=> $USER->GetID(),"ORDER_ID"=>"NULL")
            );

            if(is_object($backet) && method_exists($backet,'Fetch')){
                while($ar_props 				= $backet->Fetch()){
                    if($ar_props && is_array($ar_props) && isset($ar_props['ID'])){
                        echo GetMessage("ERROR_THIS_PRODUCT_IN_YOUR_CART");
                        return false;
                    }
                }
            }

            $rsOrder 							= CSaleOrder::GetList(array('ID' => 'DESC'), array('BASKET_PRODUCT_ID' => $arFields['PRODUCT_ID'], "USER_ID" => $USER->GetID(), "CANCELED" => "N"));

            if(is_object($rsOrder) && method_exists($rsOrder,'Fetch')){
                while($ar_props 				= $rsOrder->Fetch()){
                    if($ar_props && is_array($ar_props) && isset($ar_props['ID'])){

                        echo GetMessage("ERROR_YOU_ALREADY_ORDERERD_THIS_PRODUCT");
                        return false;
                    }
                }
            }


        };

    endif;


    return true;
}

AddEventHandler("sale", "OnBasketUpdate", "change_cart_products");
AddEventHandler("sale", "OnBasketAdd", "change_cart_products");

function change_cart_products($ID,$arFields){

    global $APPLICATION, $USER;

    if(in_array($APPLICATION->GetCurPage(),array(
            '/personal/cart/',
            '/personal/provider/'
        ))
        && isset($arFields['CUSTOM_PRICE'])
        && $arFields['CUSTOM_PRICE'] == 'N'
        && (!isset($_SESSION['try_fix_cart_price'.$ID])
            || (isset($_SESSION['try_fix_cart_price'.$ID])
                && $_SESSION['try_fix_cart_price'.$ID] < 3))
    ){

        if(!isset($_SESSION['try_fix_cart_price'.$ID]))
            $_SESSION['try_fix_cart_price'.$ID] = 0;

        $_SESSION['try_fix_cart_price'.$ID]++;

        $arFields['CUSTOM_PRICE'] = 'Y';

        $aFilter = array();

        if(isset($arFields['DETAIL_PAGE_URL']) && !empty($arFields['DETAIL_PAGE_URL'])){

            $aCode = explode('/',$arFields['DETAIL_PAGE_URL']);
            $aCode = array_map('trim',$aCode);
            $aCode = array_filter($aCode);
            $sCode = end($aCode);

            if(!empty($sCode)){
                $aFilter['CODE'] = $sCode;
            }

        } else if(isset($arFields['NAME']) && !empty($arFields['NAME'])){
            $aFilter['NAME'] = $arFields['NAME'];
        }

        if(!empty($aFilter)){

            $aFilter['IBLOCK_ID'] = 11;

            $dName = CIBlockElement::GetList(
                array(),
                ($aFilter),
                false,
                false,
                ($aSelect = array(
                    'ID',
                ))
            );

            if($dName
                && $aData = $dName->GetNext()){

                if(isset($aData['ID'])){

                    $price = CCatalogProduct::GetOptimalPrice($aData['ID'],1);

                    if(isset($price['PRICE'])
                        && isset($price['PRICE']['PRICE'])
                        && $price['PRICE']['PRICE'] > 0
                        && isset($price['PRICE']['CURRENCY'])){

                        $default_currency = getCurrentCurrencyCode();

                        if($default_currency != $price['PRICE']['CURRENCY']){
                            $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                            $price['PRICE']['CURRENCY']	= $default_currency;
                        }

                        $arFields['PRICE'] = $price['PRICE']['PRICE'];
                        $arFields['CURRENCY'] = $price['PRICE']['CURRENCY'];

                        CSaleBasket::Update($ID,$arFields);

                        if(!defined('CART_UPDATE'))
                            define('CART_UPDATE',true);

                    }

                }

            }

        }

    }

    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/exchange_log/order.txt',var_export($arFields,true)."\n",FILE_APPEND);

    if(     $APPLICATION->GetCurPage()      =='/bitrix/admin/sale_order_create.php'
        ||  $APPLICATION->GetCurPage()      =='/bitrix/admin/sale_order_edit.php'){


        if(isset($_REQUEST['PRODUCT']) && !empty($_REQUEST['PRODUCT'])){

            foreach($_REQUEST['PRODUCT'] as $number => $name){

                $currentName                = isset($name['NAME'])
                &&!empty($name['NAME'])
                    ? $name['NAME']
                    : '';


                $currentName                = trim($currentName);
                $product_id                 = isset($name['PRODUCT_ID'])
                &&!empty($name['PRODUCT_ID'])
                    ? (int)$name['PRODUCT_ID']
                    : 0;


                if(     !empty($currentName)
                    &&  !empty($product_id)){


                    if(     !empty($arFields['PRODUCT_ID'])
                        &&  $arFields['PRODUCT_ID'] == $product_id
                        && $arFields['NAME'] !=  $currentName
                    ){

                        $arFields['NAME'] =  $currentName;

                    }


                }

            }

        }


        if(class_exists('CCatalogProductProviderCustom')){
            $arFields['PRODUCT_PROVIDER_CLASS']   = 'CCatalogProductProviderCustom';
        } else {
            $arFields['PRODUCT_PROVIDER_CLASS']	= 'CCatalogProductProvider';
        };

        $arFields['IGNORE_CALLBACK_FUNC']			= 'Y';
        $arFields['CUSTOM_PRICE']					= 'Y';

        CSaleBasket::Update($ID,$arFields);

    }

    return true;

};

function getCartPriceAndCountInfo(){

    $count		   = 0;
    $price		   = 0;

    if(CURRENCY_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED){

        $CURRENCY_CODE	= CCurrency::GetBaseCurrency();

        if(function_exists('getCurrentCurrencyCode')){
            $CURRENCY_CODE 	= getCurrentCurrencyCode();
        }


        if ($CURRENCY_CODE){



            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL"
                ),
                false,
                false,
                array("ID",
                    "CALLBACK_FUNC",
                    "MODULE",
                    "PRODUCT_ID",
                    "QUANTITY",
                    "DELAY",
                    "CAN_BUY",
                    "PRICE",
                    "WEIGHT",
                    "CURRENCY")
            );

            while ($arItems = $dbBasketItems->Fetch()){

                if (mb_strlen($arItems["CALLBACK_FUNC"]) > 0)
                {
                    CSaleBasket::UpdatePrice($arItems["ID"],
                        $arItems["CALLBACK_FUNC"],
                        $arItems["MODULE"],
                        $arItems["PRODUCT_ID"],
                        $arItems["QUANTITY"]);
                    $arItems 	=  CSaleBasket::GetByID($arItems["ID"]);
                }

                if($arItems["CURRENCY"] != $CURRENCY_CODE){
                    $arItems["PRICE"]	=  CCurrencyRates::ConvertCurrency($arItems["PRICE"],$arItems["CURRENCY"],$CURRENCY_CODE,"",$arItems["PRODUCT_ID"]);
                }

                $price			+= ((int)$arItems["QUANTITY"] * $arItems["PRICE"]);
                $count			+= ((int)$arItems["QUANTITY"]);

            }


            if($price && $count){
                $price			= CurrencyFormat($price,$CURRENCY_CODE);
            }

        }

    }

    return array("price"=>$price,"count"=>$count);

}

function Add2BasketCheck($PRODUCT_ID, $QUANTITY = 1, $arRewriteFields = array(), $arProductParams = false){
    /** @global CMain $APPLICATION */
    global $APPLICATION;

    /* for old use */
    if ($arProductParams === false)
    {
        $arProductParams = $arRewriteFields;
        $arRewriteFields = array();
    }

    $boolRewrite = (!empty($arRewriteFields) && is_array($arRewriteFields));

    if ($boolRewrite && isset($arRewriteFields['SUBSCRIBE']) && $arRewriteFields['SUBSCRIBE'] == 'Y')
    {
        return false;
    }

    $PRODUCT_ID = (int)$PRODUCT_ID;
    if ($PRODUCT_ID <= 0)
    {
        return false;
    }

    $QUANTITY = (float)$QUANTITY;
    if ($QUANTITY <= 0)
        $QUANTITY = 1;

    if (!SALE_INCLUDED)
    {
        return false;
    }

    $rsProducts = CCatalogProduct::GetList(
        array(),
        array('ID' => $PRODUCT_ID),
        false,
        false,
        array(
            'ID',
            'CAN_BUY_ZERO',
            'QUANTITY_TRACE',
            'QUANTITY',
            'WEIGHT',
            'WIDTH',
            'HEIGHT',
            'LENGTH',
            'TYPE',
            'MEASURE'
        )
    );
    if (!($arCatalogProduct = $rsProducts->Fetch()))
    {
        return false;
    }
    if (
        ($arCatalogProduct['TYPE'] == Bitrix\Catalog\ProductTable::TYPE_SKU || $arCatalogProduct['TYPE'] == Bitrix\Catalog\ProductTable::TYPE_EMPTY_SKU)
        && (string)Bitrix\Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') != 'Y'
    )
    {
        return false;
    }
    $arCatalogProduct['MEASURE'] = (int)$arCatalogProduct['MEASURE'];
    $arCatalogProduct['MEASURE_NAME'] = '';
    $arCatalogProduct['MEASURE_CODE'] = 0;
    if ($arCatalogProduct['MEASURE'] <= 0)
    {
        $arMeasure = CCatalogMeasure::getDefaultMeasure(true, true);
        $arCatalogProduct['MEASURE_NAME'] = $arMeasure['~SYMBOL_RUS'];
        $arCatalogProduct['MEASURE_CODE'] = $arMeasure['CODE'];
    }
    else
    {
        $rsMeasures = CCatalogMeasure::getList(
            array(),
            array('ID' => $arCatalogProduct['MEASURE']),
            false,
            false,
            array('ID', 'SYMBOL_RUS', 'CODE')
        );
        if ($arMeasure = $rsMeasures->GetNext())
        {
            $arCatalogProduct['MEASURE_NAME'] = $arMeasure['~SYMBOL_RUS'];
            $arCatalogProduct['MEASURE_CODE'] = $arMeasure['CODE'];
        }
    }

    $dblQuantity = (float)$arCatalogProduct["QUANTITY"];
    $intQuantity = (int)$arCatalogProduct["QUANTITY"];
    $boolQuantity = ($arCatalogProduct["CAN_BUY_ZERO"] != 'Y' && $arCatalogProduct["QUANTITY_TRACE"] == 'Y');
    if ($boolQuantity && $dblQuantity <= 0)
    {
        return false;
    }

    $rsItems = CIBlockElement::GetList(
        array(),
        array(
            "ID" => $PRODUCT_ID,
            "ACTIVE" => "Y",
            "ACTIVE_DATE" => "Y",
            "CHECK_PERMISSIONS" => "Y",
            "MIN_PERMISSION" => "R",
        ),
        false,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "XML_ID",
            "NAME",
            "DETAIL_PAGE_URL",
        )
    );
    if (!($arProduct = $rsItems->GetNext()))
    {
        return false;
    }

    $strCallbackFunc = "";

    if(class_exists('CCatalogProductProviderCustom')){
        $strProductProviderClass = 'CCatalogProductProviderCustom';
    } else {
        $strProductProviderClass = 'CCatalogProductProvider';
    };


    if ($boolRewrite)
    {
        if (isset($arRewriteFields['CALLBACK_FUNC']))
            $strCallbackFunc = $arRewriteFields['CALLBACK_FUNC'];
        if (isset($arRewriteFields['PRODUCT_PROVIDER_CLASS']))
            $strProductProviderClass = $arRewriteFields['PRODUCT_PROVIDER_CLASS'];
    }

    $arCallbackPrice = false;
    if (!empty($strProductProviderClass))
    {
        if ($productProvider = CSaleBasket::GetProductProvider(array(
            'MODULE' => 'catalog',
            'PRODUCT_PROVIDER_CLASS' => $strProductProviderClass))
        )
        {
            $providerParams = array(
                'PRODUCT_ID' => $PRODUCT_ID,
                'QUANTITY' => $QUANTITY,
                'RENEWAL' => 'N'
            );
            $arCallbackPrice = $productProvider::GetProductData($providerParams);
            unset($providerParams);
        }
    }
    elseif (!empty($strCallbackFunc))
    {
        $arCallbackPrice = CSaleBasket::ExecuteCallbackFunction(
            $strCallbackFunc,
            'catalog',
            $PRODUCT_ID,
            $QUANTITY,
            'N'
        );
    }

    if (empty($arCallbackPrice) || !is_array($arCallbackPrice))
    {
        return false;
    }

    return true;
}

function fixBasketCount(){

    global $APPLICATION,$USER;

    if(in_array($APPLICATION->GetCurPage(),array(
        '/personal/cart/',
        '/personal/provider/'
    ))){


        $backet	= array();
        $backet	= CSaleBasket::GetList(
            false,
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => NULL
            )
        );

        $products = array();

        if(is_object($backet) && method_exists($backet,'Fetch')){
            while($arFields = $backet->Fetch()){
                if($arFields && is_array($arFields) && isset($arFields['ID'])){

                    $ID = $arFields['ID'];

                    if( isset($arFields['CUSTOM_PRICE'])
                        && $arFields['CUSTOM_PRICE'] == 'N'
                        && (!isset($_SESSION['try_fix_cart_price'.$ID])
                            || (isset($_SESSION['try_fix_cart_price'.$ID])
                                && $_SESSION['try_fix_cart_price'.$ID] < 3))
                    ){

                        if(!isset($_SESSION['try_fix_cart_price'.$ID]))
                            $_SESSION['try_fix_cart_price'.$ID] = 0;

                        $_SESSION['try_fix_cart_price'.$ID]++;

                        $arFields['CUSTOM_PRICE'] = 'Y';

                        $aFilter = array();

                        if(isset($arFields['DETAIL_PAGE_URL']) && !empty($arFields['DETAIL_PAGE_URL'])){

                            $aCode = explode('/',$arFields['DETAIL_PAGE_URL']);
                            $aCode = array_map('trim',$aCode);
                            $aCode = array_filter($aCode);
                            $sCode = end($aCode);

                            if(!empty($sCode)){
                                $aFilter['CODE'] = $sCode;
                            }

                        } else if(isset($arFields['NAME']) && !empty($arFields['NAME'])){
                            $aFilter['NAME'] = $arFields['NAME'];
                        }

                        if(!empty($aFilter)){

                            $aFilter['IBLOCK_ID'] = 11;

                            $dName = CIBlockElement::GetList(
                                array(),
                                ($aFilter),
                                false,
                                false,
                                ($aSelect = array(
                                    'ID',
                                ))
                            );

                            if($dName
                                && $aData = $dName->GetNext()){

                                if(isset($aData['ID'])){

                                    $price = CCatalogProduct::GetOptimalPrice($aData['ID'],1);

                                    if(isset($price['PRICE'])
                                        && isset($price['PRICE']['PRICE'])
                                        && $price['PRICE']['PRICE'] > 0
                                        && isset($price['PRICE']['CURRENCY'])){

                                        $default_currency = getCurrentCurrencyCode();

                                        if($default_currency != $price['PRICE']['CURRENCY']){
                                            $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                                            $price['PRICE']['CURRENCY']	= $default_currency;
                                        }

                                        $arFields['PRICE'] = $price['PRICE']['PRICE'];
                                        $arFields['CURRENCY'] = $price['PRICE']['CURRENCY'];

                                        CSaleBasket::Update($ID,$arFields);

                                        if(!defined('CART_UPDATE'))
                                            define('CART_UPDATE',true);

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }

}
