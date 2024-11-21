<?php

if(!defined('CATALOG_INCLUDED')) die();

/**
 * @param int $PRODUCT_ID
 * @param float|int $QUANTITY
 * @param array $arRewriteFields
 * @param bool|array $arProductParams
 * @return bool|int
 */

function Add2BasketByProductIDOld(
    $product_id,
    $quantity = 1,
    $arRewriteFields = array(),
    $arProductParams = false)
{

    global $USER;

    $return = false;

    if($product_id){

        $dName = CIBlockElement::GetList(
            array(),
            ($aFilter = array(
                'ID' => $product_id
            )
            ),
            false,
            false,
            ($aSelect = array(
                'NAME',
                'DETAIL_PAGE_URL',
                'IBLOCK_EXTERNAL_ID',
                'XML_ID'
            ))
        );

        if($dName
            && $product_data = $dName->GetNext()){

        }

        $product_name = isset($product_data['NAME']) ? $product_data['NAME'] : '';
        $product_url = isset($product_data['DETAIL_PAGE_URL']) ? $product_data['DETAIL_PAGE_URL'] : '';

        $message = sprintf(GetMessage("TMPL_CAN_NOT_BUY_MORE"),$product_name);

        $product_buy_id = getBondsProduct($product_id);

        $outnumber = get_quantity_product($product_buy_id);


        if($outnumber > 0 || isset($arRewriteFields['preorder'])){

            $price = CCatalogProduct::GetOptimalPrice($product_id,1);

            if(isset($price['PRICE'])
                && isset($price['PRICE']['PRICE'])
                && $price['PRICE']['PRICE'] > 0
                && isset($price['PRICE']['CURRENCY'])){

                $aPrice = array();

                $group_name = '';

                $dPrice = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $product_id,
                        "CATALOG_GROUP_ID" => $price['PRICE']['CATALOG_GROUP_ID']
                    )
                );

                if ($dPrice
                    && $aPrice = $dPrice->Fetch()) {
                    if(isset($aPrice['CATALOG_GROUP_NAME']))
                        $group_name = $aPrice['CATALOG_GROUP_NAME'];
                }

                $default_currency = getCurrentCurrencyCode();

                if($default_currency != $price['PRICE']['CURRENCY']){
                    $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                    $price['PRICE']['CURRENCY']	= $default_currency;
                }

                $fields = array(
                    'QUANTITY' => 1,
                    'NAME' => $product_name,
                    'PRICE' => $price['PRICE']['PRICE'],
                    'CURRENCY' => $price['PRICE']['CURRENCY'],
                    'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProviderCustom',
                    'DETAIL_PAGE_URL' => $product_url,
                    'CATALOG_XML_ID' => $product_data['IBLOCK_EXTERNAL_ID'],
                    'PRODUCT_XML_ID' => $product_data['XML_ID'],
                    'NOTES' => $group_name,
                    'CUSTOM_PRICE' => 'Y',
                );

                $properties = array(
                    array(
                        'NAME' => 'Catalog XML_ID',
                        'CODE' => 'CATALOG.XML_ID',
                        'VALUE' => $product_data['IBLOCK_EXTERNAL_ID'],
                        'SORT' => 100
                    ),
                    array(
                        'NAME' => 'Product XML_ID',
                        'CODE' => 'PRODUCT.XML_ID',
                        'VALUE' => $product_data['XML_ID'],
                        'SORT' => 100
                    ),
                );

                if($product_id != $product_buy_id) {

                    $dName = CIBlockElement::GetList(
                        array(),
                        ($aFilter = array(
                            'ID' => $product_buy_id
                        )
                        ),
                        false,
                        false,
                        ($aSelect = array(
                            'NAME',
                            'DETAIL_PAGE_URL',
                            'IBLOCK_EXTERNAL_ID',
                            'XML_ID'
                        ))
                    );

                    if($dName
                        && $base_data = $dName->GetNext()){

                    }

                    if(isset($base_data['NAME'])){

                        $fields['CATALOG_XML_ID'] = $properties[0]['VALUE'] = $base_data['IBLOCK_EXTERNAL_ID'];
                        $fields['PRODUCT_XML_ID'] = $properties[1]['VALUE'] = $base_data['XML_ID'];

                        $properties[] =
                            array(
                                'NAME' => 'Базовый товар',
                                'CODE' => 'BASIC',
                                'VALUE' => $base_data['NAME'],
                                'SORT' => 100
                            )
                        ;

                    }

                    unset($base_data);

                }

                $currencyCode = getCurrentCurrencyCode();

                if(!(isset($arRewriteFields['ORDER_ID'])
                    && !empty($arRewriteFields['ORDER_ID']))) {

                    $order = Bitrix\Sale\Order::create(
                        Bitrix\Main\Context::getCurrent()->getSite(),
                        $USER->GetID());

                } else {

                    $order = Bitrix\Sale\Order::load((int)$arRewriteFields['ORDER_ID']);

                }

                $order->setPersonTypeId(1);
                $order->setField('CURRENCY', $currencyCode);

                $basket = $order->getBasket();

                $itemExists = $basket->createItem('catalog', $product_buy_id);
                $itemExists->setFields($fields);
                $itemExists->save();

                if(!empty($properties)){
                    $props = $itemExists->getPropertyCollection();
                    $props->setProperty($properties);
                    $props->save();
                }

                $basket->save();
                $order->save();

                $return = true;


            }

        }

    }

    return $return;

}

AddEventHandler("main", "OnBeforeEventAdd", "OnSaleOrderSendEmail");
function OnSaleOrderSendEmail(&$event, &$lid, &$arFields, &$message_id) {

    global $APPLICATION;

    if ($event=="SALE_NEW_ORDER") {

        if ($arFields['ORDER_ID'] > 0
            && (!isset($arFields['ORDER_LIST'])
                || empty($arFields['ORDER_LIST']))
        ){

            return false;

        }

        if( $APPLICATION->GetCurPage() =='/bitrix/admin/sale_order_create.php'
            ||  $APPLICATION->GetCurPage() =='/bitrix/admin/sale_order_edit.php'){

            $strOrderList = '';

            require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
            $site_id = CMainPage::GetSiteByHost();
            $BASE_LANG_CURRENCY = CSaleLang::GetLangCurrency($site_id);

            foreach($_REQUEST['PRODUCT'] as $number => $name){

                if(
                    isset($name['PRODUCT_ID'])
                    && !empty($name['PRODUCT_ID'])
                ){

                    $measure = (isset($name["MEASURE_TEXT"])) ? $name["MEASURE_TEXT"] : GetMessage("SOA_SHT");
                    $strOrderList .= $name["NAME"]." - ".$name["QUANTITY"]." ".$measure.": ".SaleFormatCurrency($name["PRICE"], $BASE_LANG_CURRENCY);
                    $strOrderList .= "\n";
                    $arFields['ORDER_LIST'] = $strOrderList;

                }

            }

        }

        $arFields['ORDER_LIST'] = preg_replace('~\[((Модель)|(Производитель)|(Тип товара)|(Базовый товар)|(Catalog XML_ID)|(Product XML_ID))+?:[^\]]+?\]~isu','',$arFields['ORDER_LIST']);


        if(
            isset($_SESSION['REGISTER_LOGIN'])
            && !empty($_SESSION['REGISTER_LOGIN'])
            && isset($_SESSION['REGISTER_PASSWORD'])
            && !empty($_SESSION['REGISTER_PASSWORD'])
        ){
            $arFields['USER_LOGIN'] = $_SESSION['REGISTER_LOGIN'];
            $arFields['USER_PASSWORD'] = $_SESSION['REGISTER_PASSWORD'];
            unset($_SESSION['REGISTER_LOGIN'],$_SESSION['REGISTER_PASSWORD']);
        }
    }
}

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'OnSaleOrderSavedHandler'
);

function getPropertyIdByCode($propertyCollection, $code){
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code){

            return $property->getField('ORDER_PROPS_ID');
        }
    }
}

function hasDeliverySDEK($order)
{

    $hasSdek = false;

    $shipmentCollection = $order->getShipmentCollection();

    if($shipmentCollection)
        foreach ($shipmentCollection as $shipment) {

            if ($shipment &&
                !$shipment->isSystem()
                && !$shipment->getDeliveryId() == 0) {

                $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($shipment->getDeliveryId());

                $deliveryId = '';

                if (!empty($deliveryObj)) {

                    if ($deliveryObj->isProfile()) {
                        $deliveryId = $deliveryObj->getParentId();
                    } else {
                        $deliveryId = $shipment->getDeliveryId();
                    }
                }

                if (in_array($deliveryId, array(42, 31))) {

                    $hasSdek = true;
                    break;

                }

            }

        }

    return $hasSdek;

}

function setDeviceInfo($propertyCollection){

    $devInfo =  ' User-Agent: '.sprintf('%s',trim($_SERVER['HTTP_USER_AGENT']))."\n";

    if(isset($_SESSION['user_resolution'])){

        $devInfo .= ' Разрешение устройства: '.$_SESSION['user_resolution']."\n";

    }

    if(isset($_SESSION['deviceinfo'])){

        $devInfo .= ' Платформа: '.$_SESSION['deviceinfo']."\n";

    }

    if(isset($_SESSION['user_js'])){

        $devInfo .= ' Javascript: включен'."\n";

    }


    if(isset($_SERVER['REMOTE_ADDR'])
        && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)){

        $devInfo .= ' IP: '.$_SERVER['REMOTE_ADDR']."\n";

    }

    $addrPropertyId = getPropertyIdByCode($propertyCollection, 'OSUSERINFO');
    $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);


    if ($addrPropValue && $addrPropValue->GetValue() == '') {

        $addrPropValue->setValue($devInfo);
        $addrPropValue->save();

    }


}

function parseYDDayAndTime($propertyCollection,$order){

    $sTimeValue = '';
    $sTimeFrom = '';
    $sTimeTo = '';

    $todPropertyId = getPropertyIdByCode($propertyCollection, 'YD_TIME');

    $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($todPropertyId);

    if ($oTodPropValue) {

        $sTimeValue = $oTodPropValue->getValue();
        preg_match('~c\s+([^\s]+)\s+по\s+([^\s]+)~isu',$sTimeValue,$aTime);

        if(isset($aTime[1])) {
            $sTimeFrom = $aTime[1];
            list($sTimeFrom) = explode(':',$sTimeFrom);
        }

        if(isset($aTime[2])) {
            $sTimeTo = $aTime[2];
            list($sTimeTo) = explode(':',$sTimeTo);
        }

    }

    if (is_numeric($sTimeFrom)) {

        $dodPropertyId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERYFROM');

        $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($dodPropertyId);

        if ($oTodPropValue) {

            $sTimeFrom = '01.01.0001 '.$sTimeFrom.':00:00';
            $oTodPropValue->setValue($sTimeFrom);
            $oTodPropValue->save();

        }
    }

    if (is_numeric($sTimeTo)) {
        $dodPropertyId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERYTO');

        $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($dodPropertyId);

        if ($oTodPropValue) {

            $sTimeTo = '01.01.0001 '.$sTimeTo.':00:00';
            $oTodPropValue->setValue($sTimeTo);
            $oTodPropValue->save();

        }

    }

    $sDayValue = '';

    $todPropertyId = getPropertyIdByCode($propertyCollection, 'YD_DAYS');

    $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($todPropertyId);

    if ($oTodPropValue) {

        $sDayValue = mb_strtolower($oTodPropValue->getValue());

        if (!empty($sDayValue)) {

            $arDates = [
                'января' => 'January',
                'февраля' => 'February',
                'марта' => 'March',
                'апреля' => 'April',
                'мая' => 'May',
                'июня' => 'June',
                'июля' => 'July',
                'августа' => 'August',
                'сентября' => 'September',
                'октября' => 'October',
                'ноября' => 'November',
                'декабря' => 'December'];

            $aFild = array_keys($arDates);
            $aReplace = array_values($arDates);

            foreach ($arDates as $sFind => $sReplace) {

                $sDayValue = str_ireplace($sFind,$sReplace,$sDayValue);

            }

            $sDayValue = date('d.m.Y 0:00:00',strtotime($sDayValue));

            if (stripos($sDayValue,'1970') === false) {

                $dodPropertyId = getPropertyIdByCode($propertyCollection, 'DAYOFDELIVERY');

                $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($dodPropertyId);

                if ($oTodPropValue) {

                    $oTodPropValue->setValue($sDayValue);
                    $oTodPropValue->save();

                }

            }

        }

    }

}

function setFullAddressProperty($propertyCollection,$order){

    global $APPLICATION;

    setDeviceInfo($propertyCollection);

    $bInCart = $APPLICATION->GetCurPage() == '/personal/provider/' || $APPLICATION->GetCurPage() == '/personal/cart/' ? true : false;

    if(IBLOCK_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED && $bInCart) {

        $todPropertyId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERY');
        $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($todPropertyId);

        if ($oTodPropValue) {

            $oDodPropValue = false;
            $sDodPropValue = '';

            $dodPropertyId = getPropertyIdByCode($propertyCollection, 'DAYOFDELIVERY');

            if($dodPropertyId){

                $oDodPropValue = $propertyCollection->getItemByOrderPropertyId($dodPropertyId);

                if($oDodPropValue){

                    $sDodPropValue = $oDodPropValue->GetValue();

                    if(mb_stripos($sDodPropValue,' ') !== false){
                        list($sDodPropValue,$stime) = explode(' ',$sDodPropValue,2);
                    }



                }

            }

            $todPropValue = $oTodPropValue->GetValue();

            list($todValueFrom,$todValueTo) = array_map('trim',explode('-',$todPropValue,2));

            $todPropertyFromId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERYFROM');

            if($todPropertyFromId){

                $todValueFrom = empty($todValueFrom) ? '0' : $todValueFrom;
                $strProperty = $propertyCollection->getItemByOrderPropertyId($todPropertyFromId);
                $todValueFrom = '01.01.0001 '.$todValueFrom.':00:00';

                if($bInCart || $todValueFrom != $strProperty->GetValue()) {

                    $strProperty->setValue($todValueFrom);
                    $strProperty->save();

                }

            }

            $todPropertyToId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERYTO');

            if($todPropertyToId){

                $todValueTo = empty($todValueTo) ? '0' : $todValueTo;
                $strProperty = $propertyCollection->getItemByOrderPropertyId($todPropertyToId);
                $todValueTo = '01.01.0001 '.$todValueTo.':00:00';

                if($bInCart || $todValueTo != $strProperty->GetValue()) {

                    $strProperty->setValue($todValueTo);
                    $strProperty->save();

                }

            }

            if($oDodPropValue){

                $sDodPropValue = empty($sDodPropValue) ? '01.01.0001' : $sDodPropValue;
                $sDodPropValue .= ' 0:00:00';

                if($bInCart || $sDodPropValue != $oDodPropValue->GetValue()) {

                    $oDodPropValue->setValue($sDodPropValue);
                    $oDodPropValue->save();

                }

            }
        }

        $fullAddressValue = "";

        $hasSdek = hasDeliverySDEK($order);

        if ($hasSdek && isset($_REQUEST['SDEK_HELP']) && !empty($_REQUEST['SDEK_HELP'])) {

            $sHelp = trim(strip_tags(htmlspecialchars_decode($_REQUEST['SDEK_HELP'], ENT_QUOTES)));
            $sHelp = filter_var($sHelp);
            $fullAddressValue .= $sHelp;
            $bSkipStreet = true;
        }

        $hasBoxberry = hasDeliveryBoxberry($order);

        if ($hasBoxberry && isset($_REQUEST['BOXBERRY_HELP']) && !empty($_REQUEST['BOXBERRY_HELP'])) {

            $bHelp = trim(strip_tags(htmlspecialchars_decode($_REQUEST['BOXBERRY_HELP'], ENT_QUOTES)));
            $bHelp = filter_var($bHelp);
            $fullAddressValue .= $bHelp;
            $bSkipStreet = true;
        }

        $ydPvzId = getPropertyIdByCode($propertyCollection, 'YD_PVZ');
        $hasYdPvz = false;

        if ($ydPvzId) {

            $oYdPropValue = $propertyCollection->getItemByOrderPropertyId($ydPvzId);

            if ($oYdPropValue) {
                $sPropValue = $oYdPropValue->getValue();
                $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $sPropValue;
                $hasYdPvz = true;
            }

        }

        $zipPropertyId = getPropertyIdByCode($propertyCollection, 'ZIP');

        $zipPropValue = $propertyCollection->getItemByOrderPropertyId($zipPropertyId);

        if ($zipPropValue && !empty($zipPropValue->getValue()) && $zipPropValue->getValue() != 'По умолчанию') {

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $zipPropValue->getValue();

        }

        $locPropertyId = getPropertyIdByCode($propertyCollection, 'LOCATION');
        $locationProp = $propertyCollection->getItemByOrderPropertyId($locPropertyId);

        if ($locationProp) {

            $locationString = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay($locationProp->getValue());

            if (!empty($locationString)) {
                $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $locationString;
            }
        }

        $addrPropertyId = getPropertyIdByCode($propertyCollection, 'ADDRES_NOV');
        $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);

        if ($addrPropValue && !empty($addrPropValue->getValue()) && $addrPropValue->getValue() != 'По умолчанию') {

            $sPropValue = $addrPropValue->getValue();

            if(stripos($sPropValue,',') !== false) {

                $snPropValue = explode(',',$sPropValue);
                $snPropValue = array_map('trim',$snPropValue);
                $snPropValue = array_filter($snPropValue);
                $snPropValue = array_unique($snPropValue);
                $snPropValue = join(', ',$snPropValue);

                if($snPropValue != $sPropValue
                    && !empty($snPropValue)) {

                    $addrPropValue->setValue($snPropValue);
                    $addrPropValue->save();

                }

            } else {
                $snPropValue = $sPropValue;
            }

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $snPropValue;

        }


        $stationPropertyId = getPropertyIdByCode($propertyCollection, 'STATION');

        $stationPropValue = $propertyCollection->getItemByOrderPropertyId($stationPropertyId);

        if ($stationPropValue && !empty($stationPropValue->getValue()) && $stationPropValue->getValue() != 'По умолчанию') {

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $stationPropValue->getValue();

        }

        if (!empty($fullAddressValue)) {

            $faddrPropertyId = getPropertyIdByCode($propertyCollection, 'FULL_ADDRESS');
            $fullAddressProperty = $propertyCollection->getItemByOrderPropertyId($faddrPropertyId);

            if ($bInCart || $fullAddressValue != $fullAddressProperty->GetValue()) {

                $fullAddressProperty->setValue($fullAddressValue);
                $fullAddressProperty->save();

            }

        }


        $addrPropertyId = getPropertyIdByCode($propertyCollection, 'STREET');
        $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);

        if ($addrPropValue && !empty($addrPropValue->getValue()) && $addrPropValue->getValue() != 'По умолчанию') {

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $addrPropValue->getValue();

        }

        $addrPropertyId = getPropertyIdByCode($propertyCollection, 'HOUSE');
        $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);

        if ($addrPropValue && !empty($addrPropValue->getValue()) && $addrPropValue->getValue() != 'По умолчанию') {

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $addrPropValue->getValue();

        }

        $addrPropertyId = getPropertyIdByCode($propertyCollection, 'FLAT');
        $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);

        if ($addrPropValue && !empty($addrPropValue->getValue()) && $addrPropValue->getValue() != 'По умолчанию') {

            $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $addrPropValue->getValue();

        }

        $addrPropertyId = getPropertyIdByCode($propertyCollection, 'PVZ_CDEK');
        $addrPropValue = $propertyCollection->getItemByOrderPropertyId($addrPropertyId);
        $hasSdek = hasDeliverySDEK($order);

        if ($addrPropValue && !empty($addrPropValue->getValue()) && $addrPropValue->getValue() != 'По умолчанию') {

            if($hasSdek){

                if(stripos($fullAddressValue,$addrPropValue->getValue()) === false) {
                    $fullAddressValue .= (!empty($fullAddressValue) ? ', ' : '') . $addrPropValue->getValue();
                }

            } else {

                $fullAddressValue = trim(str_ireplace($addrPropValue->getValue(),'',$fullAddressValue));
                $fullAddressValue = trim($fullAddressValue,',');

                $addrPropValue->setValue('');
                $addrPropValue->save();

            }
        }

        if (!empty($fullAddressValue)) {

            $faddrPropertyId = getPropertyIdByCode($propertyCollection, 'FULL_ADDRESS');
            $fullAddressProperty = $propertyCollection->getItemByOrderPropertyId($faddrPropertyId);

            if($bInCart || $fullAddressValue != $fullAddressProperty->GetValue()) {

                if(stripos($fullAddressValue,',') !== false) {
                    $fullAddressValue = explode(',',$fullAddressValue);
                    $fullAddressValue = array_map('trim',$fullAddressValue);
                    $fullAddressValue = array_filter($fullAddressValue);
                    $fullAddressValue = array_unique($fullAddressValue);
                    $fullAddressValue = join(', ',$fullAddressValue);
                }

                if($bInCart || $fullAddressValue != $fullAddressProperty->GetValue()) {

                    $fullAddressProperty->setValue($fullAddressValue);
                    $fullAddressProperty->save();
                }

            }

        }

        if($locationProp && $strValue = getStreetValue($locationProp->getValue())){

            $strPropertyId = getPropertyIdByCode($propertyCollection, 'STREET');
            $strProperty = $propertyCollection->getItemByOrderPropertyId($strPropertyId);

            if($bInCart || $strValue != $strProperty->GetValue()) {

                $strProperty->setValue($strValue);
                $strProperty->save();

            }

        }

        $scValue = '';

        if($locationProp)
            getCityValue($locationProp->getValue(),$scValue,true);

        if($scValue){

            $strPropertyId = getPropertyIdByCode($propertyCollection, 'CITY');
            $strProperty = $propertyCollection->getItemByOrderPropertyId($strPropertyId);

            if($bInCart || $scValue != $strProperty->GetValue()) {

                $strProperty->setValue($scValue);
                $strProperty->save();

            }

        }

        parseYDDayAndTime($propertyCollection,$order);


    }

}


function getCityValue($pValue,&$locName,$isCode = false){

    static $types;

    if(!is_array($types)){

        $res = \Bitrix\Sale\Location\TypeTable::getList(array('select' => array('ID', 'CODE')));
        while ($item = $res->fetch()) {
            $types[$item['ID']] = $item['CODE'];
        }

    }

    $skCode = $isCode ? 'CODE' : 'ID';

    $data = array(
        'select' => array(
            '*',
            'LOC_NAME' => 'NAME.NAME',
        ),
        'filter' => array(
            $skCode => $pValue,
            '=NAME.LANGUAGE_ID' => LANGUAGE_ID
        )
    );

    $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

    if($rLoc) {

        $aLoc = $rLoc->fetch();
        $aLoc['TYPE_CODE'] = $types[$aLoc['TYPE_ID']];

        if(isset($aLoc['TYPE_CODE'])
            && $aLoc['TYPE_CODE'] == 'CITY'){
            $locName = $aLoc['LOC_NAME'];

        } elseif(isset($aLoc['PARENT_ID'])
            && !empty($aLoc['PARENT_ID'])
        ) {

            getCityValue($aLoc['PARENT_ID'],$locName);

        }

    }

}

function getStreetValue($locId){

    $locName = '';
    $res = \Bitrix\Sale\Location\TypeTable::getList(array('select' => array('ID', 'CODE')));
    while ($item = $res->fetch()) {
        $types[$item['ID']] = $item['CODE'];
    }

    $data = array(
        'select' => array(
            '*',
            'LOC_NAME' => 'NAME.NAME',
        ),
        'filter' => array(
            'CODE' => $locId,
            '=NAME.LANGUAGE_ID' => LANGUAGE_ID
        )
    );

    $rLoc = \Bitrix\Sale\Location\LocationTable::getList($data);

    if($rLoc) {

        $aLoc = $rLoc->fetch();
        $aLoc['TYPE_CODE'] = $types[$aLoc['TYPE_ID']];

        if($aLoc['TYPE_CODE'] == 'STREET') {
            $locName = $aLoc['LOC_NAME'];
        }

    }

    return $locName;

}

function sendSMSOnPayment(\Bitrix\Main\Event $event){

    $order = $event->getParameter("ENTITY");
    $smsPayTemplate = trim(Bitrix\Main\Config\Option::Get('my.stat', "sms_pay_template", ""));

    if($order
        && CModule::IncludeModule("imaginweb.sms")
        &&!empty($smsPayTemplate)) {

        $propertyCollection = $order->getPropertyCollection();

        if ($propertyCollection) {

            $phonePropertyId = getPropertyIdByCode($propertyCollection, 'PHONE');
            $phonePropValue = $propertyCollection->getItemByOrderPropertyId($phonePropertyId);

            if ($phonePropValue) {

                $orderPhone = trim($phonePropValue->getValue());
                $orderPhone = CIWebSMS::MakePhoneNumber($orderPhone);

                if(!empty($orderPhone)
                    && CIWebSMS::CheckPhoneNumber($orderPhone)){

                    $replaces = array(
                        '#ACCOUNT_NUMBER#' => $order->getField("ACCOUNT_NUMBER"),
                        '#ORDER_NUMBER#' => $order->getId(),
                        '#ORDER_SUMM#' => $order->getPrice(),
                        '#PRICE_DELIVERY#' => $order->getDeliveryPrice(),
                        '#PRICE#' => ($order->getPrice() - $order->getDeliveryPrice()) > 0 ? ($order->getPrice() - $order->getDeliveryPrice()) : 0
                    );

                    foreach($replaces as $template => $replace){
                        $smsPayTemplate = str_ireplace($template,$replace,$smsPayTemplate);
                    }

                    $smsPayTemplate = trim($smsPayTemplate);

                    if(!empty($smsPayTemplate)){
                        CIWebSMS::Send($orderPhone,$smsPayTemplate);
                    }

                }

            }

        }

    }

}

function tryToChangeStatusOrder($disallowOrder,$disallowValue,$statusValue,$orderId){


    if($disallowOrder
        && $disallowValue
    ){

        $statusValue = 'O';
        \CSaleOrder::CancelOrder($orderId, "Y");

    }

    if($statusValue !== false) {

        $arOrdFilter = Array(
            "ID" => $orderId
        );

        $dbSales = \CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arOrdFilter ,false,false,array("*"));

        if($dbSales && $arSales = $dbSales->Fetch()){

            if($statusValue != $arSales['STATUS_ID']) {

                \CSaleOrder::StatusOrder($orderId, $statusValue);

            }

        }

    }

}

function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event){

    $order = $event->getParameter("ENTITY");

    global $ORDERS_1C, $APPLICATION;
    static $order_updated;

    if(!is_array($order_updated)){
        $order_updated = array();
    }

    $id_1c = array();

    if($event){

        $order = $event->getParameter("ENTITY");

        $propertyCollection = $order->getPropertyCollection();
     

        $fieldValues = $order->getFields();

        $resultString = '';

        $oldValues = $event->getParameter("VALUES");

        $statusId = $order->getField('STATUS_ID');

        changeSaleProperties($event);

        $sendInstructionValue = (in_array($statusId,array('JJ'))
            &&
            ((
                    isset($oldValues['STATUS_ID'])
                    && !empty($oldValues['STATUS_ID'])
                    && $statusId != $oldValues['STATUS_ID']
                ) || (
                    isset($oldValues['VERSION'])
                    && $oldValues['VERSION'] == 0
                ))
        );

        $paymentCollection = $order->getPaymentCollection();
        $paymentIds = $order->getPaymentSystemId();

        if($sendInstructionValue
            && !empty($paymentIds)
            && in_array(39,$paymentIds)
        ){
            sendSMSOnPayment($event);
        }

        if($order){

            $orderId = $order->GetField('ID');

            $needToSave = false;

            if(!empty($orderId) && !isset($order_updated[$orderId])){

                $disallowOrder = isset($ORDERS_1C['orders'])
                && isset($ORDERS_1C['orders'][$orderId])
                && isset($ORDERS_1C['orders'][$orderId]['disallowOrder'])
                && !empty($ORDERS_1C['orders'][$orderId]['disallowOrder'])
                    ? true
                    : false;

                $disallowValue = isset($ORDERS_1C['orders'])
                && isset($ORDERS_1C['orders'][$orderId])
                && isset($ORDERS_1C['orders'][$orderId]['disallowValue'])
                && !empty($ORDERS_1C['orders'][$orderId]['disallowValue'])
                    ? true
                    : false;

                $statusValue = isset($ORDERS_1C['orders'])
                && isset($ORDERS_1C['orders'][$orderId])
                && isset($ORDERS_1C['orders'][$orderId]['statusValue'])
                && !empty($ORDERS_1C['orders'][$orderId]['statusValue'])
                    ? trim($ORDERS_1C['orders'][$orderId]['statusValue'])
                    : false;

                $skipStatusChange = false;
                $skipCancel = false;

                if(!$order->isCanceled()){

                    $shipmentCollection = $order->getShipmentCollection();

                    foreach ($shipmentCollection as $shipment) {

                        $shipmentItemCollection = $shipment->getShipmentItemCollection();
                        $emptyShipment = true;

                        if(($shipmentItemCollection
                            && is_object($shipmentItemCollection))){

                            if(sizeof($shipmentItemCollection)){

                                foreach($shipmentItemCollection as $item) {

                                    $basketItem = $item->getBasketItem();

                                    if($basketItem->getProductId()){

                                        $emptyShipment = false;
                                    }

                                }

                            }

                        };


                        if(!$shipment->isSystem()
                            && !$shipment->getDeliveryId() == 0
                            && !$emptyShipment){

                            if(in_array($shipment->getDeliveryId(),array(41,5))
                                && isset($ORDERS_1C['orders'][$orderId])
                            ){

                                doArchiveOrder($orderId);
                                return true;
                            }

                            $shipmentFields = array();

                            if(isset($ORDERS_1C['orders'])
                                && isset($ORDERS_1C['orders'][$orderId])
                                && isset($ORDERS_1C['orders'][$orderId]['trackingNumber'])){
                                $trackingNumber = $ORDERS_1C['orders'][$orderId]['trackingNumber'];

                                $oldTrackingNumber = $shipment->GetField('TRACKING_NUMBER');

                                if($oldTrackingNumber != $trackingNumber){
                                    $shipmentFields['TRACKING_NUMBER'] = $trackingNumber;
                                }

                            }

                            $shipmtentId = $shipment->getId();


                            if(isset($ORDERS_1C['orders'])
                                && isset($ORDERS_1C['orders'][$orderId])
                                && isset($ORDERS_1C['orders'][$orderId]['deliveryIdValue'])){

                                $deliveryId = $ORDERS_1C['orders'][$orderId]['deliveryIdValue'];

                                $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($deliveryId);

                                $deliveryName = '';

                                if (!empty($deliveryObj)) {

                                    if ($deliveryObj->isProfile()) {
                                        $deliveryName = $deliveryObj->getNameWithParent();
                                    } else {
                                        $deliveryName = $deliveryObj->getName();
                                    }
                                }

                                if(!empty($deliveryName)
                                    && $shipment->getDeliveryId() != $deliveryId){

                                    $shipmentFields['DELIVERY_ID'] = $deliveryId;
                                    $shipmentFields['DELIVERY_NAME'] = $deliveryName;

                                }

                            }

                            if(isset($ORDERS_1C['shipments'])
                                && isset($ORDERS_1C['shipments'][$shipmtentId])
                                && isset($ORDERS_1C['shipments'][$shipmtentId]['deliverySum'])){

                                $oldDeliveryPrice = $shipment->GetField('PRICE_DELIVERY');
                                $shipmentCost = $ORDERS_1C['shipments'][$shipmtentId]['deliverySum'];

                                if(in_array($shipment->getDeliveryId(),array(2,41,5))){
                                    $shipmentCost = 0;
                                }

                                if((float)$oldDeliveryPrice != (float)$shipmentCost){

                                    $shipmentFields['PRICE_DELIVERY'] = $shipmentCost;
                                    $shipmentFields['CURRENCY'] = $order->getCurrency();

                                }

                            }

                            if(!empty($shipmentFields)){

                                $oldFields = array();
                                $restoreFields = array();

                                /* if($shipment->getField('ALLOW_DELIVERY') == 'Y'){
                                    $oldFields['ALLOW_DELIVERY'] = 'N';
                                    $restoreFields['ALLOW_DELIVERY'] = 'Y';
                                }

                                if($shipment->getField('DEDUCTED') == 'Y') {
                                    $oldFields['DEDUCTED'] = 'N';
                                    $restoreFields['DEDUCTED'] = 'Y';
                                }

                                if($shipment->getField('STATUS_ID') == 'DF') {
                                    $oldFields['STATUS_ID'] = 'DN';
                                    $restoreFields['STATUS_ID'] = 'DF';
                                } */

                                if(isset($ORDERS_1C['shipments'])
                                    && isset($ORDERS_1C['shipments'][$shipmtentId])
                                    && isset($ORDERS_1C['shipments'][$shipmtentId]['allowDelivery'])
                                ){
                                    $restoreFields['ALLOW_DELIVERY'] = $ORDERS_1C['shipments'][$shipmtentId]['allowDelivery'];
                                }

                                if(isset($ORDERS_1C['shipments'])
                                    && isset($ORDERS_1C['shipments'][$shipmtentId])
                                    && isset($ORDERS_1C['shipments'][$shipmtentId]['deducted'])
                                ){
                                    $restoreFields['DEDUCTED'] = $ORDERS_1C['shipments'][$shipmtentId]['deducted'];

                                    if($ORDERS_1C['shipments'][$shipmtentId]['deducted'] == 'Y'){
                                        $restoreFields['STATUS_ID'] = 'DF';
                                    } else {
                                        $restoreFields['STATUS_ID'] = 'DN';
                                    }

                                }

                                if(!empty($oldFields)){
                                    $shipment->setFields($oldFields);
                                    $shipment->save();
                                }

                                if(!empty($restoreFields)){
                                    $shipmentFields = array_merge($shipmentFields,$restoreFields);
                                }

                                $shipment->setFields($shipmentFields);

                                $shipment->save();

                                $needToSave = true;

                                if(isset($ORDERS_1C['shipments'][$shipmtentId]['isWdsNull'])
                                    && $ORDERS_1C['shipments'][$shipmtentId]['isWdsNull']
                                    && in_array($order->getField("STATUS_ID"),array('K','M'))
                                ){
                                    doArchiveOrder($orderId);
                                    return true;
                                }


                            }

                        }

                    }



                    $paymentId = (isset($ORDERS_1C['orders'])
                        && isset($ORDERS_1C['orders'][$orderId])
                        && isset($ORDERS_1C['orders'][$orderId]['payment']))
                        ? (int)$ORDERS_1C['orders'][$orderId]['payment']
                        : 0;


                    if($paymentId > 0){

                        if($arPaySys = CSalePaySystem::GetByID($paymentId)){

                            $paysystemName = $arPaySys["NAME"];
                            $foundPayment = false;

                            $paymentFields = array(
                                'PAY_SYSTEM_ID' => $paymentId,
                                'PAY_SYSTEM_NAME' => $paysystemName,
                            );

                            if (($paymentCollection = $order->getPaymentCollection())
                                && count($paymentCollection)) {

                                foreach($paymentCollection as $onePayment){

                                    if($paymentId != $onePayment->getPaymentSystemId()){

                                        $oldFields = array();

                                        /* if($onePayment->getField('PAID') == 'Y'){
                                            $onePayment->setPaid("N");
                                            $oldFields['PAID'] = 'Y';
                                        }

                                        if($onePayment->getField('IS_RETURN') == 'Y'){
                                            $onePayment->setReturn("N");
                                            $oldFields['IS_RETURN'] = 'Y';
                                        }

                                        if(!empty($oldFields)){
                                            $onePayment->save();
                                            $paymentFields = array_merge($paymentFields,$oldFields);
                                        } */

                                        $onePayment->setFields($paymentFields);
                                        $onePayment->save();

                                        $needToSave = true;
                                        $foundPayment = true;

                                    } else {
                                        $foundPayment = true;
                                    }

                                }

                            }

                            if(!$foundPayment){

                                $paymentCollection = $order->getPaymentCollection();
                                $onePayment = $paymentCollection->createItem();

                                $paymentFields = array_merge(
                                    $paymentFields,
                                    array(
                                        'SUM' => $order->getPrice(),
                                        'CURRENCY' => $order->getCurrency()
                                    )
                                );

                                $onePayment->setFields($paymentFields);
                                $onePayment->save();
                                $needToSave = true;

                            }

                        }

                    }

                    if($needToSave){

                        if(($basket = $order->getBasket())){

                            $shipments = $order->getShipmentCollection();

                            foreach ($shipments as $shipment)
                            {
                                if(!$shipment->isSystem())
                                {

                                    $fields = $shipment->getFieldValues();

                                    $deliveryId = isset($fields['DELIVERY_ID']) && !empty($fields['DELIVERY_ID'])
                                        ? $fields['DELIVERY_ID']
                                        : \Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();

                                    unset($fields);

                                    $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($deliveryId);

                                    $shipmentCollection = $shipment->getCollection();

                                    if (!empty($deliveryObj)) {

                                        if ($deliveryObj->isProfile()) {
                                            $name = $deliveryObj->getNameWithParent();
                                        } else {
                                            $name = $deliveryObj->getName();
                                        }

                                        $shipment->setFields(array(
                                            'DELIVERY_ID' => $deliveryObj->getId(),
                                            'DELIVERY_NAME' => $name,
                                            'CURRENCY' => $order->getCurrency()
                                        ));


                                    }

                                    $shipmentCollection->calculateDelivery();

                                }

                            }

                            $discount = $order->getDiscount();
                            \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
                            \Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
                            $discount->setOrderRefresh(true);
                            $discount->setApplyResult(array());
                            $basket->refreshData(array('PRICE', 'COUPONS', 'PRICE_DELIVERY'));
                            $discount->calculate();

                            if (!$order->isCanceled() && !$order->isPaid()){

                                if (($paymentCollection = $order->getPaymentCollection())
                                    && count($paymentCollection) == 1) {

                                    if (($payment = $paymentCollection->rewind())
                                        && !$payment->isPaid()){

                                        $payment->setFieldNoDemand('SUM', $order->getPrice());

                                    }
                                }
                            }


                            $order_updated[$orderId] = true;
                            $order->save();

                        }
                    }


                    tryToChangeStatusOrder($disallowOrder,$disallowValue,$statusValue,$orderId);

                }

                $order_updated[$orderId] = true;

            }

            if(($basket = $order->getBasket())
                && ($APPLICATION->GetCurPage() == '/bitrix/admin/1c_exchange.php')){

                $basketItems = $basket->getBasketItems();

                $basketChanged = false;

                foreach($basketItems as $product){

                    $productId = $product->getProductId();
                    $productName = $product->getField('NAME');

                    $productDB = CIBlockElement::GetList(Array(), Array('ID' => $productId), false, false, array('IBLOCK_ID','NAME','PREVIEW_TEXT'));
                    if($productDB
                        && $productArr = $productDB->GetNext()){

                        $iblockId = $productArr['IBLOCK_ID'];

                        if($iblockId == 16
                            && (($productArr['NAME'] == $productName))
                        ){

                            $copyName = $productArr['PREVIEW_TEXT'];
                            $copyName = strip_tags($copyName);
                            $copyName = trim($copyName);

                            if(!empty($copyName)){

                                $product->setField('NAME', $copyName);
                                $product->save();
                                $basketChanged = true;

                            }

                        }

                    }

                }

                if($basketChanged)
                    $basket->save();

            }

            $propertyCollection = $order->getPropertyCollection();

            if($propertyCollection){

                setFullAddressProperty($propertyCollection,$order);

                $sdiPropertyId = getPropertyIdByCode($propertyCollection, 'SEND_DELIVERY_INSTRUCTION');
                $instructionProperty = $propertyCollection->getItemByOrderPropertyId($sdiPropertyId);

                if($instructionProperty){

                    $instructionValue =  $instructionProperty->getValue();

                    if($instructionValue == 'Y' || $instructionValue == 'Да'){

                        $paymentCollection = $order->getPaymentCollection();

                        if($paymentCollection
                            && $order->getId()){

                            global $APPLICATION,$USER;

                            $site_id = $order->getSiteId();
                            $user_id = $order->getUserId();

                            $smail = $USER->GetByID($user_id);
                            $smail = $smail->Fetch();

                            $arMail = array(
                                "USER_EMAIL" => $smail["EMAIL"],
                                "USER_NAME" => $smail["NAME"],
                                "USER_LOGIN" => $smail["LOGIN"],
                                "USER_LAST_NAME" => $smail["LAST_NAME"],
                                "USER_SECOND_NAME" => $smail["SECOND_NAME"],
                            );

                            $strHashes = array(':+','.+','-+','!+','++','*+','~+','=+');
                            $randSalt = md5($strHashes[mt_rand(0,sizeof($strHashes) - 1)].$user_id);
                            $salt = '?&order_id='.$order->getID().'&check_hash='.md5($user_id.'-'.$order->getId()).':'.$randSalt;

                            $arMail['ORDER_URL'] = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/personal/order/detail/'.$order->getId().'/'.$salt;

                            $smail['GROUPS_ID'] = CUser::GetUserGroup($user_id);

                            /* if(is_array($smail['GROUPS_ID']) && sizeof($smail['GROUPS_ID'])
                                && sizeof(array_intersect($smail['GROUPS_ID'],array(1,6,7)) == 0)
                            ){

                                //REGISTER_PASSWORD

                                $password_chars = array(
                                    "abcdefghijklnmopqrstuvwxyz",
                                    "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
                                    "0123456789",
                                );

                                $password_min_length = mt_rand(6,9);

                                $newPassword = randString($password_min_length+2, $password_chars);
                                $newPassword = isset($_SESSION['REGISTER_PASSWORD'])
                                && !empty($_SESSION['REGISTER_PASSWORD'])
                                    ? $_SESSION['REGISTER_PASSWORD']
                                    : $newPassword;

                                $arMail["USER_PASSWORD"] = $newPassword;

                                $USER->Update($user_id,  Array(
                                    "PASSWORD" => $newPassword,
                                    "CONFIRM_PASSWORD" => $newPassword,
                                ));

                            } else */ {
                                $arMail["USER_PASSWORD"] = "";
                            }



                            $arFields = CSaleOrder::GetByID($order->getId());

                            foreach($arFields as $key=>$value){
                                $arMail["ORDER_".$key] = $value;
                            }

                            if(isset($arMail["ORDER_PERSON_TYPE_ID"])
                                && !empty($arMail["ORDER_PERSON_TYPE_ID"])){

                                $arProps = array();
                                $db_props = CSaleOrderProps::GetList(
                                    array("SORT" => "ASC"),
                                    array(
                                        "PERSON_TYPE_ID" => $arMail["ORDER_PERSON_TYPE_ID"]
                                    ),
                                    false,
                                    false,
                                    array("*")
                                );

                                if ($db_props
                                    && is_object($db_props)
                                    && method_exists($db_props,'Fetch')){

                                    while($arProps = $db_props->Fetch()){

                                        $db_vals = CSaleOrderPropsValue::GetList(
                                            array("SORT" => "ASC"),
                                            array(
                                                "ORDER_ID" => $order->getId(),
                                                "ORDER_PROPS_ID" => $arProps["ID"]
                                            )
                                        );

                                        if($db_vals
                                            && is_object($db_vals)
                                            && method_exists($db_vals,'Fetch')
                                            && $arVals = $db_vals->Fetch()){


                                            if(isset($arVals['CODE']) && !empty($arVals['CODE'])
                                                && isset($arVals['VALUE']) && !empty($arVals['VALUE'])
                                            ){

                                                if($arVals['CODE'] != 'LOCATION'){
                                                    $arMail["ORDER_VALUE_".$arVals['CODE']] = $arVals['VALUE'];
                                                } else {

                                                    $arLocs	= "";
                                                    $arLocs = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $arVals['VALUE'] );

                                                    if(!empty($arLocs)){
                                                        $arMail["ORDER_VALUE_COUNTRY_NAME"] = $arLocs;
                                                    };

                                                };

                                            };

                                        };

                                    };

                                };

                            };

                            if(isset($arMail["ORDER_DELIVERY_ID"])
                                && !empty($arMail["ORDER_DELIVERY_ID"])){

                                $arDeliv = false;

                                if($arDeliv = CSaleDelivery::GetByID($arMail["ORDER_DELIVERY_ID"])){
                                    $arMail["ORDER_DELIVERY_NAME"]	= $arDeliv["NAME"];
                                };

                            };

                            foreach($paymentCollection as $onePayment){
                                $arMail["ORDER_PAY_SYSTEM_NAME"] = $onePayment->getPaymentSystemName();
                            }

                            $sdiPropertyId = getPropertyIdByCode($propertyCollection, 'SEND_DELIVERY_INSTRUCTION');
                            $instructionPropValue = $propertyCollection->getItemByOrderPropertyId($sdiPropertyId);

                            if($instructionPropValue){
                                $instructionPropValue->setValue("Нет");
                                $instructionPropValue->save();
                            }

                            $shipments = $order->getShipmentCollection();

                            foreach ($shipments as $oneShipment) {

                                $fields = $oneShipment->getFieldValues();

                                if($fields
                                    && isset($fields['ORDER_ID'])
                                    && isset($fields['DELIVERY_NAME'])){

                                    $arMail["ORDER_DELIVERY_NAME"] = $fields['DELIVERY_NAME'];
                                    $event_name = 'DELIVERY_INSTRUCTION_'.$fields['ORDER_ID'];

                                    CEvent::SendImmediate($event_name, $site_id, $arMail);
                                }

                            }




                        }

                    }

                }

                $siPropertyId = getPropertyIdByCode($propertyCollection, 'SEND_INSTRUCTION');
                $instructionProperty = $propertyCollection->getItemByOrderPropertyId($siPropertyId);

                if($instructionProperty){

                    $instructionValue =  $instructionProperty->getValue();

                    if($instructionValue == 'Y'
                        || $instructionValue == 'Да'
                        || $sendInstructionValue
                    ){

                        $paymentCollection = $order->getPaymentCollection();

                        if($paymentCollection
                            && $order->getId()){

                            global $APPLICATION,$USER;

                            $site_id = $order->getSiteId();
                            $user_id = $order->getUserId();

                            $smail = $USER->GetByID($user_id);
                            $smail = $smail->Fetch();

                            $arMail = array(
                                "USER_EMAIL" => $smail["EMAIL"],
                                "USER_NAME" => $smail["NAME"],
                                "USER_LOGIN" => $smail["LOGIN"],
                                "USER_LAST_NAME" => $smail["LAST_NAME"],
                                "USER_SECOND_NAME" => $smail["SECOND_NAME"],
                            );

                            $strHashes = array(':+','.+','-+','!+','++','*+','~+','=+');
                            $randSalt = md5($strHashes[mt_rand(0,sizeof($strHashes) - 1)].$user_id);
                            $salt = '?&order_id='.$order->getID().'&check_hash='.md5($user_id.'-'.$order->getId()).':'.$randSalt;

                            $arMail['ORDER_URL'] = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.'/personal/order/detail/'.$order->getId().'/'.$salt;

                            $smail['GROUPS_ID'] = CUser::GetUserGroup($user_id);

                            /* if(is_array($smail['GROUPS_ID']) && sizeof($smail['GROUPS_ID'])
                                && sizeof(array_intersect($smail['GROUPS_ID'],array(1,6,7))) == 0){

                                $password_chars = array(
                                    "abcdefghijklnmopqrstuvwxyz",
                                    "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
                                    "0123456789",
                                );

                                $password_min_length = mt_rand(6,9);

                                $newPassword = randString($password_min_length+2, $password_chars);
                                $newPassword = isset($_SESSION['REGISTER_PASSWORD'])
                                && !empty($_SESSION['REGISTER_PASSWORD'])
                                    ? $_SESSION['REGISTER_PASSWORD']
                                    : $newPassword;

                                $arMail["USER_PASSWORD"] = $newPassword;

                                $USER->Update($user_id,  Array(
                                    "PASSWORD" => $newPassword,
                                    "CONFIRM_PASSWORD" => $newPassword,
                                ));

                            } else */ {
                                $arMail["USER_PASSWORD"] = "";
                            }

                            $arFields = CSaleOrder::GetByID($order->getId());

                            foreach($arFields as $key=>$value){
                                $arMail["ORDER_".$key] = $value;
                            }

                            if(isset($arMail["ORDER_PERSON_TYPE_ID"])
                                && !empty($arMail["ORDER_PERSON_TYPE_ID"])){

                                $arProps = array();
                                $db_props = CSaleOrderProps::GetList(
                                    array("SORT" => "ASC"),
                                    array(
                                        "PERSON_TYPE_ID" => $arMail["ORDER_PERSON_TYPE_ID"]
                                    ),
                                    false,
                                    false,
                                    array("*")
                                );

                                if ($db_props
                                    && is_object($db_props)
                                    && method_exists($db_props,'Fetch')){

                                    while($arProps = $db_props->Fetch()){

                                        $db_vals = CSaleOrderPropsValue::GetList(
                                            array("SORT" => "ASC"),
                                            array(
                                                "ORDER_ID" => $order->getId(),
                                                "ORDER_PROPS_ID" => $arProps["ID"]
                                            )
                                        );

                                        if($db_vals
                                            && is_object($db_vals)
                                            && method_exists($db_vals,'Fetch')
                                            && $arVals = $db_vals->Fetch()){


                                            if(isset($arVals['CODE']) && !empty($arVals['CODE'])
                                                && isset($arVals['VALUE']) && !empty($arVals['VALUE'])
                                            ){

                                                if($arVals['CODE'] != 'LOCATION'){
                                                    $arMail["ORDER_VALUE_".$arVals['CODE']] = $arVals['VALUE'];
                                                } else {

                                                    $arLocs	= "";
                                                    $arLocs = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $arVals['VALUE'] );

                                                    if(!empty($arLocs)){
                                                        $arMail["ORDER_VALUE_COUNTRY_NAME"] = $arLocs;
                                                    };

                                                };

                                            };

                                        };

                                    };

                                };

                            };

                            if(isset($arMail["ORDER_DELIVERY_ID"])
                                && !empty($arMail["ORDER_DELIVERY_ID"])){

                                $arDeliv = false;

                                if($arDeliv = CSaleDelivery::GetByID($arMail["ORDER_DELIVERY_ID"])){
                                    $arMail["ORDER_DELIVERY_NAME"]	= $arDeliv["NAME"];
                                };

                            };

                            if($instructionValue == 'Y' || $instructionValue == 'Да'){

                                $siPropertyId = getPropertyIdByCode($propertyCollection, 'SEND_INSTRUCTION');
                                $instructionPropValue = $propertyCollection->getItemByOrderPropertyId($siPropertyId);

                                if($instructionPropValue){
                                    $instructionPropValue->setValue("Нет");
                                    $instructionPropValue->save();
                                }

                            }

                            foreach($paymentCollection as $onePayment){

                                $arMail["ORDER_PAY_SYSTEM_NAME"] = $onePayment->getPaymentSystemName();
                                $event_name = 'PAYSYSTEM_INSTRUCTION_'.$onePayment->getPaymentSystemId();

                                CEvent::SendImmediate($event_name, $site_id, $arMail);

                            }

                        }


                        $paymentCollection = $order->getPaymentCollection();

                        if($paymentCollection){

                            foreach ($paymentCollection as $onePayment) {

                                $psName = $onePayment->getPaymentSystemName();

                                if($psName && $psName == 'Квитанция Сбербанка') {

                                    if($order->getId()
                                        && !file_exists($_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/sberbank_".$order->getId().".pdf")){

                                        $_REQUEST["ORDER_ID"] = $order->getId();
                                        $_REQUEST["SAVE"] = "Y";

                                        $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($onePayment->getPaymentSystemId());
                                        $context = \Bitrix\Main\Application::getInstance()->getContext();
                                        $service->initiatePay($onePayment, $context->getRequest());

                                    }

                                    if(file_exists($_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/sberbank_".$order->getId().".pdf")){

                                        global $APPLICATION,$USER;

                                        if(IBLOCK_INCLUDED && CATALOG_INCLUDED){

                                            $site_id = $order->getSiteId();

                                            $backet	= array();
                                            $backet	= CSaleBasket::GetList(
                                                false,
                                                array(
                                                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                                                    "LID" => $site_id,
                                                    "ORDER_ID" => $order->getId()
                                                )
                                            );


                                            $products = array();
                                            $user_id = $order->getUserId();

                                            if(is_object($backet) && method_exists($backet,'Fetch')){
                                                while($ar_props = $backet->Fetch()){
                                                    if($ar_props && is_array($ar_props) && isset($ar_props['ID'])){
                                                        $products[] = $ar_props;

                                                        $db_res = CSaleBasket::GetPropsList(
                                                            array(
                                                                "SORT" => "ASC",
                                                                "NAME" => "ASC"
                                                            ),
                                                            array("BASKET_ID" => $ar_props['ID'])
                                                        );


                                                        if(is_object($db_res) && method_exists($db_res,'Fetch')){
                                                            while ($ar_res = $db_res->Fetch()){
                                                                $properties[$ar_res["CODE"]] = $ar_res["VALUE"];
                                                            }
                                                        }

                                                    }
                                                }
                                            }

                                            {

                                                $iblock_id = "";

                                                $smail = $USER->GetByID($user_id);
                                                $smail = $smail->Fetch();

                                                $arMail = array(
                                                    "USER_EMAIL" => $smail["EMAIL"],
                                                    "USER_NAME" => $smail["NAME"],
                                                    "USER_LOGIN" => $smail["LOGIN"],
                                                    "USER_LAST_NAME" => $smail["LAST_NAME"],
                                                    "USER_SECOND_NAME" => $smail["SECOND_NAME"],
                                                );

                                                $last = 0;
                                                $products_id = array();
                                                $strOrderList = '';

                                                if(sizeof($products)){
                                                    foreach($products as $next=>$product){

                                                        if(
                                                            isset($product['PRODUCT_ID'])
                                                            && !empty($product['PRODUCT_ID'])
                                                        ){

                                                            $measure = GetMessage("SOA_SHT");
                                                            $strOrderList .= $product["NAME"]." - ".$product["QUANTITY"]." ".$measure.": ".SaleFormatCurrency($product["PRICE"], $product["CURRENCY"]);
                                                            $strOrderList .= "\n";

                                                        }

                                                        $product_id	= $product['PRODUCT_ID'];
                                                        $products_id[] = $product_id;
                                                        $res = CIBlockElement::GetByID($product_id);

                                                        if(is_object($res) && method_exists($res,'GetNext')){
                                                            if($ar_res = $res->GetNext()){
                                                                $iblock_id = $ar_res['IBLOCK_ID'];

                                                                foreach($product as $key=>$value){
                                                                    $arMail["PRODUCT_".$next."_".$key] = $value;
                                                                }

                                                            }
                                                        }

                                                        $last = $next;
                                                    }

                                                    $arMail['ORDER_LIST'] = $strOrderList;
                                                    $arMail['ORDER_LIST'] = preg_replace('~\[Базовый товар:[^\]]+?\]~isu','',$arMail['ORDER_LIST']);
                                                    $arMail['ORDER_LIST'] = preg_replace('~\[Модель:[^\]]+?\]~isu','',$arMail['ORDER_LIST']);
                                                    $arMail['ORDER_LIST'] = preg_replace('~\[Производитель:[^\]]+?\]~isu','',$arMail['ORDER_LIST']);
                                                    $arMail['ORDER_LIST'] = preg_replace('~\[Тип товара:[^\]]+?\]~isu','',$arMail['ORDER_LIST']);

                                                    if($last < 14){
                                                        for($k = $last + 1; $k < 15; $k ++){
                                                            $arMail["PRODUCT_".$k."_NAME"] = "";
                                                            $arMail["PRODUCT_".$k."_PRICE"] = "";
                                                            $arMail["PRODUCT_".$k."_CURRENCY"] = "";
                                                        }
                                                    }

                                                    foreach($properties as $key=>$value){
                                                        $arMail["PROPERTY_".$key] = $value;
                                                    }

                                                }


                                                $event_name = "USER_SBERBANK_BILL";

                                                foreach($_REQUEST as $key=>$value){
                                                    $arMail["REQUEST_".$key] = $value;
                                                }

                                                $arFields = CSaleOrder::GetByID($order->getId());

                                                foreach($arFields as $key=>$value){
                                                    $arMail["ORDER_".$key] = $value;
                                                }


                                                if(isset($arMail["ORDER_PAY_SYSTEM_ID"])
                                                    && !empty($arMail["ORDER_PAY_SYSTEM_ID"])
                                                    && isset($arMail["ORDER_PERSON_TYPE_ID"])
                                                    && !empty($arMail["ORDER_PERSON_TYPE_ID"])){

                                                    $arPaySys = false;
                                                    if($arPaySys = CSalePaySystem::GetByID($arMail["ORDER_PAY_SYSTEM_ID"],$arMail["ORDER_PERSON_TYPE_ID"])){
                                                        $arMail["ORDER_PAY_SYSTEM_NAME"] = $arPaySys["NAME"];
                                                    };

                                                };

                                                if(isset($arMail["ORDER_PERSON_TYPE_ID"])
                                                    && !empty($arMail["ORDER_PERSON_TYPE_ID"])){

                                                    $arProps = array();
                                                    $db_props = CSaleOrderProps::GetList(
                                                        array("SORT" => "ASC"),
                                                        array(
                                                            "PERSON_TYPE_ID" => $arMail["ORDER_PERSON_TYPE_ID"]
                                                        ),
                                                        false,
                                                        false,
                                                        array("*")
                                                    );

                                                    if ($db_props
                                                        && is_object($db_props)
                                                        && method_exists($db_props,'Fetch')){

                                                        while($arProps = $db_props->Fetch()){

                                                            $db_vals = CSaleOrderPropsValue::GetList(
                                                                array("SORT" => "ASC"),
                                                                array(
                                                                    "ORDER_ID" => $order->getId(),
                                                                    "ORDER_PROPS_ID" => $arProps["ID"]
                                                                )
                                                            );

                                                            if($db_vals
                                                                && is_object($db_vals)
                                                                && method_exists($db_vals,'Fetch')
                                                                && $arVals = $db_vals->Fetch()){



                                                                if(isset($arVals['CODE']) && !empty($arVals['CODE'])
                                                                    && isset($arVals['VALUE']) && !empty($arVals['VALUE'])
                                                                ){
                                                                    if($arVals['CODE'] != 'LOCATION'){
                                                                        $arMail["ORDER_VALUE_".$arVals['CODE']] = $arVals['VALUE'];
                                                                    } else {

                                                                        $arLocs	= "";
                                                                        $arLocs = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $arVals['VALUE'] );

                                                                        if(!empty($arLocs)){
                                                                            $arMail["ORDER_VALUE_COUNTRY_NAME"] = $arLocs;
                                                                        };
                                                                    };
                                                                };

                                                            };
                                                        };

                                                    };

                                                };

                                                if(isset($arMail["ORDER_DELIVERY_ID"])
                                                    && !empty($arMail["ORDER_DELIVERY_ID"])){

                                                    $arDeliv = false;

                                                    if($arDeliv = CSaleDelivery::GetByID($arMail["ORDER_DELIVERY_ID"])){
                                                        $arMail["ORDER_DELIVERY_NAME"]	= $arDeliv["NAME"];

                                                    };

                                                };


                                                CEvent::Send($event_name, $site_id, $arMail, "Y", "", array($_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/sberbank_".$order->getId().".pdf"));

                                                $siPropertyId = getPropertyIdByCode($propertyCollection, 'SEND_INSTRUCTION');
                                                $instructionPropValue = $propertyCollection->getItemByOrderPropertyId($siPropertyId);

                                                if($instructionPropValue){
                                                    $instructionPropValue->setValue("N");
                                                    $instructionPropValue->save();
                                                }

                                            };

                                        };

                                    }

                                }

                            }

                        }

                    }

                }

            }

            changeSDEKDelivery($orderId);

        }

    }

}

AddEventHandler("sale", "OnOrderUpdate", "checkStatusOfSaleOrderAdmin");

function checkStatusOfSaleOrderAdmin($ID,$arFields){



    if($GLOBALS["APPLICATION"]->GetCurPage() 	== "/bitrix/admin/sale_order.php"
        || $GLOBALS["APPLICATION"]->GetCurPage() 	== "/bitrix/admin/sale_order_ajax.php"){


        $val									= $arFields['STATUS_ID'];

        checkInterval($ID, $val);

        if(($val == 'F' || $val == 'M' || $val 	== 'L' || $val 	== 'Q' || $val 	== 'K') && !empty($ID) && false){


            $arOrdFilter = Array(
                "ID" => $ID
            );

            $dbSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arOrdFilter ,false,false,array("*"));

            if($dbSales && $arSales = $dbSales->Fetch()){

                if($arSales['DEDUCTED'] != 'Y'
                    && $arSales['MARKED'] != 'Y'){

                    CSaleOrder::DeductOrder($ID,"Y");

                }

                if($val == 'F'
                    && $arSales['PAYED'] != 'Y'
                    && $arSales['MARKED'] != 'Y'
                ){

                    CSaleOrder::PayOrder($ID, "Y", false, false, 0, array());

                };

            }


        }

    }


}

function checkInterval($ID, $val)
{
    global $DB;

    if ($val != 'M') {
        return;
    }

    $rDb = $DB->Query('SELECT COUNT(*) as count FROM b_intervals WHERE order_id=' . (int)$ID . '');
    $iOrderFound = 0;

    if ($rDb) {
        $aDb = $rDb->Fetch();
        $iOrderFound = $aDb['count'];
    }

    if (!$iOrderFound) {

        //TIMEOFDELIVERY
        $order = \Bitrix\Sale\Order::loadByAccountNumber($ID);

        $propertyCollection = $order->getPropertyCollection();
        $todPropertyId = getPropertyIdByCode($propertyCollection, 'TIMEOFDELIVERY');
        $oTodPropValue = $propertyCollection->getItemByOrderPropertyId($todPropertyId);

        if ($oTodPropValue) {

            $dodPropertyId = getPropertyIdByCode($propertyCollection, 'DAYOFDELIVERY');
            $oDodPropValue = $propertyCollection->getItemByOrderPropertyId($dodPropertyId);

            if ($oDodPropValue) {
                $oDodPropValue = date('d.m.Y', strtotime($oDodPropValue->GetValue()));

                if ($oTodPropValue && $oDodPropValue) {
                    $DB->Query('INSERT INTO b_intervals(id,intervaluse,date,order_id) VALUES(\'NULL\',\'' . $DB->ForSql($oTodPropValue->GetValue()) . '\',\'' . $DB->ForSql($oDodPropValue) . '\',\'' . (int)$ID . '\')');
                }
            }
        }

    }
}

AddEventHandler("sale", "OnSaleStatusOrder", "checkStatusOfSaleOrder");

function checkStatusOfSaleOrder($ID,$val){

    global $USER;

    checkInterval($ID, $val);

    if(($val == 'Q' || $val == 'K' || $val 	== 'M') && !empty($ID)) {

        $select = array(
            'ID',
            'PAYED',
            'CANCELED',
            'STATUS_ID',
            "USER_ID",
            "USER_EMAIL" => "USER.EMAIL",
            'DATE_INSERT'
        );

        $arFilter = array();
        $arFilter['STATUS_ID'] = $val;
        $arFilter['=ID'] = $ID;

        $getListParams = array(
            'filter' => $arFilter,
            'select' => $select,
            'runtime' => array()
        );

        $getListParams['order'] = array('DATE_INSERT' => 'DESC');

        $usePageNavigation = true;

        $orderClassName = '\Bitrix\Sale\Order';

        $dbOrders = new \CDBResult($orderClassName::getList($getListParams));

        if ($dbOrders) {
            while ($arOrder = $dbOrders->GetNext()) {
                $listOrders[$arOrder['ID']] = array();
            }
        }

        if (!empty($listOrders)) {

            $orderIds = array_keys($listOrders);

            $basketClassName = '\Bitrix\Sale\Basket';
            /** @var Main\DB\Result $listBaskets */
            $listBaskets = $basketClassName::getList(array(
                'select' => array("*"),
                'filter' => array("=ORDER_ID" => $orderIds),
                'order' => array('ID' => 'ASC')
            ));

            while ($basket = $listBaskets->fetch()) {

                $listOrders[$basket['ORDER_ID']]['basket'][] = $basket;

                if(isset($basket['PRODUCT_ID'])
                    && !empty($basket['PRODUCT_ID'])){

                    $rpDb = CIBlockElement::GetProperty(16,$basket['PRODUCT_ID'],array(),array("CODE" => "REMOTE_STORE"));

                    if($rpDb
                        && $apDb = $rpDb->Fetch()) {

                        if(isset($apDb['VALUE']) && !empty($apDb['VALUE'])) {

                            $iQuantity = (int)trim($apDb['VALUE']);

                            if($iQuantity > 0) {

                                --$iQuantity;

                                CIBlockElement::SetPropertyValuesEx($basket['PRODUCT_ID'], 16, array('REMOTE_STORE' => $iQuantity));
                                //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $basket['PRODUCT_ID']);

                                $rsStore = CCatalogStoreProduct::GetList(
                                    array(),
                                    array(
                                        'PRODUCT_ID' => $basket['PRODUCT_ID'],
                                        'STORE_ID' => 9),
                                    false,
                                    false,
                                    array('ID'));

                                if ($arStore = $rsStore->Fetch()){
                                    $iCID = $arStore['ID'];
                                }

                                $asFields = array(
                                    "PRODUCT_ID" => $basket['PRODUCT_ID'],
                                    "STORE_ID" => 9,
                                    "AMOUNT" => $iQuantity
                                );

                                if($iCID) {

                                    $iUpd = CCatalogStoreProduct::Update(
                                        $iCID,
                                        $asFields
                                    );


                                } else {

                                    $iUpd = CCatalogStoreProduct::Add($asFields);

                                }

                            }


                        }

                    }

                }

            }

        }

    }


    if(($val == 'F' || $val == 'M' || $val 	== 'L' || $val 	== 'Q' || $val 	== 'K') && !empty($ID) && false
    ){

        $arOrdFilter = Array(
            "ID" => $ID
        );

        $dbSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arOrdFilter ,false,false,array("*"));

        if($dbSales && $arSales = $dbSales->Fetch()){


            if($arSales['DEDUCTED'] != 'Y'
                && $arSales['MARKED'] != 'Y'){

                CSaleOrder::DeductOrder($ID,"Y");

            }

            if($val == 'F'
                && $arSales['PAYED'] != 'Y'
                && $arSales['MARKED'] != 'Y'
            ){

                CSaleOrder::PayOrder($ID, "Y", false, false, 0, array());


            };

        }

    }
}

AddEventHandler("sale", "OnSaleComponentOrderOneStepComplete", "send_afterbuy_event");

function send_afterbuy_event($ID,$arFields){
    global $APPLICATION,$USER,$MESS;

    if(IBLOCK_INCLUDED && CATALOG_INCLUDED):

        $backet					= array();
        $backet				    = CSaleBasket::GetList(
            false,
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => $ID)
        );


        $products				= array();

        if(is_object($backet) && method_exists($backet,'Fetch')){
            while($ar_props 					= $backet->Fetch()){
                if($ar_props && is_array($ar_props) && isset($ar_props['ID'])){
                    $products[] 				= $ar_props;

                    $db_res = CSaleBasket::GetPropsList(
                        array(
                            "SORT" => "ASC",
                            "NAME" => "ASC"
                        ),
                        array("BASKET_ID" => $ar_props['ID'])
                    );


                    if(is_object($db_res) && method_exists($db_res,'Fetch')){
                        while ($ar_res = $db_res->Fetch()){
                            $properties[$ar_res["CODE"]]    = $ar_res["VALUE"];
                        }
                    }

                }
            }
        }

        if(isset($products[0])){

            $iblock_id									= "";

            if($USER->GetID()){

                $smail 					  				= $USER->GetByID($USER->GetID());
                $smail	 				  				= $smail->Fetch();

                $arMail 								= array(
                    "USER_EMAIL" 	 				=> $smail["EMAIL"],
                    "USER_NAME" 	 				=> $smail["NAME"],
                    "USER_LAST_NAME" 				=> $smail["LAST_NAME"],
                    "USER_SECOND_NAME" 				=> $smail["SECOND_NAME"],
                );

            } else {

                $arMail									= array();
            }

            $last										= 0;
            $products_id								= array();

            foreach($products	 						as $next=>$product){

                $product_id								= $product['PRODUCT_ID'];
                $products_id[]							= $product_id;
                $res		 						  	= CIBlockElement::GetByID($product_id);

                if(is_object($res) && method_exists($res,'GetNext')){
                    if($ar_res 						  	= $res->GetNext()){
                        $iblock_id					  	= $ar_res['IBLOCK_ID'];

                        foreach($product as $key=>$value){
                            $arMail["PRODUCT_".$next."_".$key] = $value;
                        }

                    }
                }

                $last									= $next;
            }

            if($last < 14){
                for($k = $last + 1; $k < 15; $k ++){
                    $arMail["PRODUCT_".$k."_NAME"] 			= "";
                    $arMail["PRODUCT_".$k."_PRICE"] 		= "";
                    $arMail["PRODUCT_".$k."_CURRENCY"] 		= "";
                }
            }


            $event_name1			  					= "USER_AFTER_BUY";

            if(!empty($iblock_id)){
                $event_name				  				= "USER_AFTER_BUY_".$iblock_id;
            } else {
                $event_name								= $event_name1;
            }


            foreach($_REQUEST as $key=>$value){
                $arMail["REQUEST_".$key] 				= $value;
            }

            foreach($arFields as $key=>$value){
                $arMail["ORDER_".$key] 					= $value;
            }

            foreach($properties as $key=>$value){
                $arMail["PROPERTY_".$key] = $value;
            }



            if(isset($arMail["ORDER_PAY_SYSTEM_ID"])
                && !empty($arMail["ORDER_PAY_SYSTEM_ID"])
                && isset($arMail["ORDER_PERSON_TYPE_ID"])
                && !empty($arMail["ORDER_PERSON_TYPE_ID"])){

                $arPaySys 							= false;
                if($arPaySys 						= CSalePaySystem::GetByID($arMail["ORDER_PAY_SYSTEM_ID"],$arMail["ORDER_PERSON_TYPE_ID"])){
                    $arMail["ORDER_PAY_SYSTEM_NAME"]= $arPaySys["NAME"];
                };

            };

            $arProps									= array();
            $db_props 									= CSaleOrderProps::GetList(
                array("SORT" => "ASC"),
                array(
                    "PERSON_TYPE_ID" 			=> $arMail["ORDER_PERSON_TYPE_ID"]
                ),
                false,
                false,
                array("*")
            );

            if ($db_props
                && is_object($db_props)
                && method_exists($db_props,'Fetch')){

                while($arProps 												= $db_props->Fetch()){

                    $db_vals 												= CSaleOrderPropsValue::GetList(
                        array(	"SORT" 									=> "ASC"),
                        array(
                            "ORDER_ID" 								=> $ID,
                            "ORDER_PROPS_ID" 						=> $arProps["ID"]
                        )
                    );

                    if($db_vals
                        && is_object($db_vals)
                        && method_exists($db_vals,'Fetch')
                        && $arVals 											= $db_vals->Fetch()){



                        if(isset($arVals['CODE']) && !empty($arVals['CODE'])
                            && isset($arVals['VALUE']) && !empty($arVals['VALUE'])
                        ){
                            if($arVals['CODE'] 								!= 'LOCATION'){
                                $arMail["ORDER_VALUE_".$arVals['CODE']] 	= $arVals['VALUE'];
                            } else {

                                $arLocs										= "";
                                $arLocs 									= Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $arVals['VALUE'] );

                                if(!empty($arLocs)){
                                    $arMail["ORDER_VALUE_COUNTRY_NAME"] 	= $arLocs;
                                };
                            };
                        };

                    };
                };

            };

            if(isset($arMail["ORDER_DELIVERY_ID"])
                && !empty($arMail["ORDER_DELIVERY_ID"])){

                $arDeliv 							= false;

                if($arDeliv 						= CSaleDelivery::GetByID($arMail["ORDER_DELIVERY_ID"])){
                    $arMail["ORDER_DELIVERY_NAME"]	= $arDeliv["NAME"];

                };

            };

            foreach ($products_id as $product_id){

                $arSelect 									= Array("IBLOCK_ID", "CATALOG_QUANTITY");
                $arFilter 									= Array("ID"=>$product_id);
                $res 										= CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                $product									= array();

                while($res
                    && ($product						= $res->Fetch())){
                    if(isset($product['IBLOCK_ID'])
                        && !empty($product['IBLOCK_ID'])
                    ){

                        switch($product['IBLOCK_ID']){
                            case 11:

                                $arSelect 					= Array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                                $arFilter 					= Array("IBLOCK_ID"=>$product['IBLOCK_ID'], "ID"=>$product_id);
                                $res 						= CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                                $product					= array();

                                while($res
                                    && ($product		= $res->Fetch())){

                                    $iblock_id				= $product['IBLOCK_ID'];
                                    $PROPERTY_CODE 			= "QUANTITY";
                                    $PROPERTY_VALUE 		= $product['CATALOG_QUANTITY'];

                                    CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                                    if ($iblock_id == 11)
                                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);
                                }

                                break;
                            case 16:

                                $arSelect 					= Array("ID", "IBLOCK_ID", "NAME", "CATALOG_QUANTITY");
                                $arFilter 					= Array("IBLOCK_ID"=>11, "PROPERTY_MAIN_PRODUCTS"=>$product_id);
                                $quantity					= $product['CATALOG_QUANTITY'];
                                $res 						= CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

                                $iblock_id					= $product['IBLOCK_ID'];
                                $PROPERTY_CODE 				= "QUANTITY";
                                $PROPERTY_VALUE 			= $quantity;
                                CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                                if ($iblock_id == 11)
                                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                                $product					= array();

                                while($res
                                    && ($product		= $res->Fetch())){

                                    $product_id				= $product['ID'];
                                    $iblock_id				= $product['IBLOCK_ID'];
                                    $PROPERTY_CODE 			= "QUANTITY";
                                    $PROPERTY_VALUE 		= $quantity;

                                    CIBlockElement::SetPropertyValues($product_id, $iblock_id, $PROPERTY_VALUE, $PROPERTY_CODE);

                                    if ($iblock_id == 11)
                                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblock_id, $product_id);

                                }

                                break;
                        }

                    }
                }

            }


            CEvent::SendImmediate($event_name1, SITE_ID, $arMail);
            CEvent::SendImmediate($event_name, SITE_ID, $arMail);

        };

    endif;
}

AddEventHandler("sale", "OnSaleComponentOrderOneStepComplete", "change_sms_settings");

function change_sms_settings($ID,$arOrder){
    global $USER,$APPLICATION;

    if(!$ID)
        return false;

    $order = \Bitrix\Sale\Order::loadByAccountNumber($ID);
    $propertyCollection = $order->getPropertyCollection();

    if(isset($_REQUEST['SMS_INFO'])){

        if($USER->IsAuthorized()){
            $fields 		        = Array(
                "UF_SMS_INFORM" => (isset($_REQUEST['SMS_INFO']) && !empty($_REQUEST['SMS_INFO']) ? 1 : 0),
            );

            $USER->Update($USER->GetID(), $fields);
        };

        $APPLICATION->set_cookie('SMS_INFO', (!empty($_REQUEST['SMS_INFO']) ? 1 : 0), time()+60*60*24*30*12*2, "/");

    };

    if(!(isset($_REQUEST['SMS_INFO'])
        && !empty($_REQUEST['SMS_INFO']))){

        $dbProp                    = CSaleOrderProps::GetList(array(), array('CODE' => 'SMS_SEND'));

        if ($dbProp && $arProp     = $dbProp->Fetch()) {

            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

            if($propertyValue) {
                $propertyValue->setField('VALUE', "Y");
                $propertyValue->save();
            }
        }

    } else {
        $dbProp                    = CSaleOrderProps::GetList(array(), array('CODE' => 'SMS_SEND'));

        if ($dbProp && $arProp     = $dbProp->Fetch()) {

            $propertyValue = $propertyCollection->getItemByOrderPropertyId($arProp['ID']);

            if($propertyValue){
                $propertyValue->setField('VALUE', "N");
                $propertyValue->save();
            }
        }
    }

}

function setOrderReferer($orderId){

    $order = \Bitrix\Sale\Order::loadByAccountNumber($orderId);
    $propertyCollection = $order->getPropertyCollection();
    $arProps = $propertyCollection->getArray();
    $refPropId = 0;

    if(isset($arProps['properties'])){
        foreach($arProps['properties'] as $property){
            if(isset($property['CODE'])
                && $property['CODE'] == 'REFERER'){

                $refPropId = $property['ID'];

            }
        }
    }

    $nPropValue = $propertyCollection->getItemByOrderPropertyId($refPropId);

    if($nPropValue){

        global $USER;

        if($USER
            && $USER->IsAuthorized()
            && class_exists('CGuest')){

            $auFilter = array(
                "USER_ID" => $USER->GetId()
            );

            $rsGuest = CGuest::GetList(
                ($sby = "s_id"),
                ($sorder = "desc"),
                $auFilter,
                ($bisFiltered = false)
            );

            while ($arGuest = $rsGuest->Fetch())
            {

                $referer = '';

                for($it = 1; $it < 3; $it++){

                    if(empty($referer)
                        && isset($arGuest['FIRST_REFERER'.$it])
                        && !empty($arGuest['FIRST_REFERER'.$it])
                    ){

                        $referer = $arGuest['FIRST_REFERER'.$it];
                        break;

                    }
                }

                if(!empty($referer)){

                    $nPropValue->setValue($referer);
                    $nPropValue->save();
                    $order->save();

                }


            }

        }




    }
}


AddEventHandler("sale", "OnBeforeOrderAdd", "OnBeforeOrderAddHandler");

function OnBeforeOrderAddHandler($arFields){

    if($arFields['USER_ID'] != 97142
        && isset($_SERVER['DOCUMENT_URI'])
        && (stripos($_SERVER['DOCUMENT_URI'],'trading/turbo') !== false)) {

        $arFields['USER_ID'] = 97142;

    }

}

AddEventHandler("sale", "OnBeforeOrderUpdate", "OnBeforeOrderUpdateHandler");

function OnBeforeOrderUpdateHandler($ID, $arFields){

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

                $_REQUEST['PRODUCT'][$number]['PRODUCT_PROVIDER_CLASS'] = $product_provider_class;


                if(     isset($arFields['BASKET_ITEMS'])
                    && !empty($arFields['BASKET_ITEMS'])){

                    foreach($arFields['BASKET_ITEMS'] as $basket_number => $basket_items){

                        if(     isset($basket_items['PRODUCT_ID'])
                            && !empty($basket_items['PRODUCT_ID'])
                            &&  isset($name['PRODUCT_ID'])
                            && !empty($name['PRODUCT_ID'])
                            &&  isset($basket_items['NAME'])
                            && !empty($basket_items['NAME'])
                            &&  isset($name['NAME'])
                            && !empty($name['NAME'])
                            && $basket_items['PRODUCT_ID'] == $name['PRODUCT_ID']
                        ){

                            $arFields['BASKET_ITEMS'][$basket_number]['NAME'] = trim($name['NAME']);

                        }

                    }

                }

            }

        }

        if(     isset($arFields['BASKET_ITEMS'])
            &&  is_array($arFields['BASKET_ITEMS'])
            && !empty($arFields['BASKET_ITEMS'])){

            foreach($arFields['BASKET_ITEMS'] as $number => $item){

                $arFields['BASKET_ITEMS'][$number]['PRODUCT_PROVIDER_CLASS'] = $product_provider_class;
                $arFields['BASKET_ITEMS'][$number]['CUSTOM_PRICE'] = 'Y';
            }

        }

    }


    return true;

}

AddEventHandler("sale", "OnOrderSave", "change_amount_value_save");

function change_amount_value_save($orderId, $arFields, $arOrder, $isNew){

    change_quantity_product($orderId);

}

AddEventHandler("sale", "OnBeforeOrderDelete", "change_amount_value_delete");

function change_amount_value_delete($orderId){
    change_quantity_product($orderId);
}

AddEventHandler("sale", "OnSaleCancelOrder", "change_amount_value_cancel");

function change_amount_value_cancel($orderId,$value,$description){
    change_quantity_product($orderId);
}

AddEventHandler("sale", "OnOrderUpdate", "change_amount_value_update");

function change_amount_value_update($orderId,$arFields){
    change_quantity_product($orderId);
}

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    'OnSaleOrderBeforeSavedHandler'
);

function OnSaleOrderBeforeSavedHandler(\Bitrix\Main\Event $event){
    global $USER, $APPLICATION;

    if(IBLOCK_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED){

        $from_catalog_id = isset($_REQUEST['PRODUCT_BUY_ID']) && $_REQUEST['PRODUCT_BUY_ID'] !== false ? (int)$_REQUEST['PRODUCT_BUY_ID'] : false;

        if($event){

            $order = $event->getParameter("ENTITY");

            if(!$from_catalog_id
                && !in_array($APPLICATION->GetCurPage(),array(
                    '/bitrix/admin/sale_order_create.php',
                    '/bitrix/admin/sale_order_edit.php',
                    '/personal/cart/',
                    '/personal/provider/',
                    '/ajax_cart/fastoder.php',
                    '/ajax_cart/preorder.php'
                ))
            ) {

                $basket = $order->getBasket();
                $basketItems = $basket->getBasketItems();

                foreach ($basketItems as $basketItem) {

                    $product_id = $basketItem->getProductId();
                    $product_buy_id	= getBondsProduct($product_id);

                    if($product_buy_id && $product_id
                        && $product_buy_id != $basketItem->getProductId()){

                        $dName = CIBlockElement::GetList(
                            array(),
                            ($aFilter = array(
                                'ID' => $product_id
                            )
                            ),
                            false,
                            false,
                            ($aSelect = array(
                                'NAME',
                                'DETAIL_PAGE_URL',
                                'IBLOCK_EXTERNAL_ID',
                                'XML_ID'
                            ))
                        );

                        if($dName
                            && $product_data = $dName->GetNext()){

                        }

                        $product_name = (isset($product_data['NAME']) ? $product_data['NAME'] : '');

                        global $amNames;

                        if (is_array($amNames)
                            && isset($amNames[$product_id])
                            && !empty($amNames[$product_id])) {
                            $product_name = $amNames[$product_id];
                        }

                        $product_url = isset($product_data['DETAIL_PAGE_URL']) ? $product_data['DETAIL_PAGE_URL'] : '';

                        $product_buy_id = getBondsProduct($product_id);

                        if ($product_buy_id > 0) {

                            $outnumber = get_quantity_product($product_buy_id);

                            if($outnumber > 0){

                                $price = CCatalogProduct::GetOptimalPrice($product_id,1);

                                if(isset($price['PRICE'])
                                    && isset($price['PRICE']['PRICE'])
                                    && $price['PRICE']['PRICE'] > 0
                                    && isset($price['PRICE']['CURRENCY'])){

                                    $aPrice = array();

                                    if ($basketItem->getField('PRICE_TYPE_ID') == 9) {
                                        $price['PRICE']['CATALOG_GROUP_ID'] = $basketItem->getField('PRICE_TYPE_ID');
                                    }

                                    $group_name = '';

                                    $dPrice = CPrice::GetList(
                                        array(),
                                        array(
                                            "PRODUCT_ID" => $product_id,
                                            "CATALOG_GROUP_ID" => $price['PRICE']['CATALOG_GROUP_ID']
                                        )
                                    );

                                    if ($dPrice
                                        && $aPrice = $dPrice->Fetch()) {
                                        if(isset($aPrice['CATALOG_GROUP_NAME']))
                                            $group_name = $aPrice['CATALOG_GROUP_NAME'];
                                    }

                                    $default_currency = getCurrentCurrencyCode();

                                    if($default_currency != $price['PRICE']['CURRENCY']){
                                        $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                                        $price['PRICE']['CURRENCY']	= $default_currency;
                                    }

                                    $qty = $basketItem->getQuantity();

                                    $fixedPrice = notChangePrice($order);

                                    if ($fixedPrice || $basketItem->getField('PRICE_TYPE_ID') == 9 || (defined('ADMIN_MODULE_NAME') && in_array(ADMIN_MODULE_NAME,array('acrit.exportproplus')))) {
                                        $price['PRICE']['PRICE'] = $basketItem->getPrice();

                                        if ($order && is_object($order)) {

                                            $propertyCollection = $order->getPropertyCollection();

                                            if($propertyCollection) {

                                                $fpricePropertyId = getPropertyIdByCode($propertyCollection, 'FIXEDPRICE');

                                                if ($fpricePropertyId) {

                                                    $fpricePropValue = $propertyCollection->getItemByOrderPropertyId($fpricePropertyId);

                                                    if ($fpricePropValue && is_object($fpricePropValue)) {

                                                        $fpricePropValue->setValue('Да');
                                                        //$fpricePropValue->save();

                                                    }

                                                }

                                            }

                                        }

                                    }

                                    $fields = array(
                                        'QUANTITY' => ($qty ? $qty : 1),
                                        'PRICE_TYPE_ID' => $price['PRICE']['CATALOG_GROUP_ID'],
                                        'NAME' => $product_name,
                                        'PRICE' => $price['PRICE']['PRICE'],
                                        'CURRENCY' => $price['PRICE']['CURRENCY'],
                                        'LID' => Bitrix\Main\Context::getCurrent()->getSite() ?? 's1',
                                        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProviderCustom',
                                        'DETAIL_PAGE_URL' => $product_url,
                                        'CATALOG_XML_ID' => $product_data['IBLOCK_EXTERNAL_ID'],
                                        'PRODUCT_XML_ID' => $product_data['XML_ID'],
                                        'NOTES' => $group_name,
                                        'CUSTOM_PRICE' => 'Y',
                                    );

                                    $properties = array(
                                        array(
                                            'NAME' => 'Catalog XML_ID',
                                            'CODE' => 'CATALOG.XML_ID',
                                            'VALUE' => $product_data['IBLOCK_EXTERNAL_ID'],
                                            'SORT' => 100
                                        ),
                                        array(
                                            'NAME' => 'Product XML_ID',
                                            'CODE' => 'PRODUCT.XML_ID',
                                            'VALUE' => $product_data['XML_ID'],
                                            'SORT' => 100
                                        ),
                                    );

                                    if($product_id != $product_buy_id) {

                                        $dName = CIBlockElement::GetList(
                                            array(),
                                            ($aFilter = array(
                                                'ID' => $product_buy_id
                                            )
                                            ),
                                            false,
                                            false,
                                            ($aSelect = array(
                                                'NAME',
                                                'DETAIL_PAGE_URL',
                                                'IBLOCK_EXTERNAL_ID',
                                                'XML_ID'
                                            ))
                                        );

                                        if($dName
                                            && $base_data = $dName->GetNext()){

                                        }

                                        if(isset($base_data['NAME'])){

                                            $fields['CATALOG_XML_ID'] = $properties[0]['VALUE'] = $base_data['IBLOCK_EXTERNAL_ID'];
                                            $fields['PRODUCT_XML_ID'] = $properties[1]['VALUE'] = $base_data['XML_ID'];

                                            $properties[] =
                                                array(
                                                    'NAME' => 'Базовый товар',
                                                    'CODE' => 'BASIC',
                                                    'VALUE' => $base_data['NAME'],
                                                    'SORT' => 100
                                                )
                                            ;

                                        }

                                        unset($base_data);

                                    }

                                    $basketItem->setFields($fields);
                                    $basketItem->save();

                                    if(!empty($properties)){
                                        $props = $basketItem->getPropertyCollection();
                                        $props->setProperty($properties);
                                        $props->save();
                                    }


                                }

                            }

                        }

                    }

                }

                $basket->save();

                if (!$order->isCanceled() && !$order->isPaid()){

                    $bHasAny = true;

                    if (($paymentCollection = $order->getPaymentCollection())
                        && count($paymentCollection) == 0) {

                        $bHasAny = false;

                    }

                    if(!$bHasAny){

                        $paymentCollection = $order->getPaymentCollection();
                        $payment = $paymentCollection->createItem();
                        $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById(18);
                        $payment->setFields(array(
                            'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
                            'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
                        ));

                        $payment->setFieldNoDemand('SUM', $order->getPrice());
                        $payment->save();

                    }

                }

            }

        }

    }


    if(IBLOCK_INCLUDED && SALE_INCLUDED && CATALOG_INCLUDED
        && ($APPLICATION->GetCurPage() == '/personal/cart/' || $APPLICATION->GetCurPage() == '/personal/provider/')){

        $backet				    			= CSaleBasket::GetList(
            false,
            array("FUSER_ID" => \CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL")
        );

        global $USER;

        $session = \Bitrix\Main\Application::getInstance()->getSession();
        $session->set('order_error', NULL);

        if(is_object($backet) && method_exists($backet,'Fetch')){
            while($ar_props 				= $backet->Fetch()){

                if($ar_props
                    && is_array($ar_props)
                    && isset($ar_props['PRODUCT_ID'])
                    && $ar_props['PRODUCT_ID'] > 0){

                    $outnumber              = get_quantity_product($ar_props['PRODUCT_ID']);
                    if(!($outnumber > 0 && $ar_props['QUANTITY'] && $ar_props['QUANTITY'] <= $outnumber)){

                        $session->set('order_error', '<div>'.(sprintf(GetMessage("TMPL_LESS_ZERO_QUANTITY"),$ar_props['NAME'])).'</div>');

                    }

                }

            }

        }


        //ORDER_PROP_3

        //ORDER_PROP_6 | ORDER_PROP_18

        $locPropId = isset($_REQUEST['ORDER_PROP_6']) ? $_REQUEST['ORDER_PROP_6'] : false;
        $locPropId = !$locPropId && isset($_REQUEST['ORDER_PROP_18'])? $_REQUEST['ORDER_PROP_18'] : $locPropId;
        $scName = '';

        if($locPropId){

            $arLocs = \CSaleLocation::GetByID($locPropId, LANGUAGE_ID);

            if(isset($arLocs['COUNTRY_NAME']) && !empty($arLocs['COUNTRY_NAME'])){
                $scName = trim($arLocs['COUNTRY_NAME']);
            } elseif(isset($arLocs['COUNTRY_ID'])
                && !empty($arLocs['COUNTRY_ID'])){

                $dbCountries = \CSaleLocation::GetCountryList(array(),array('ID' => $arLocs['COUNTRY_ID']));
                $arCountries = $dbCountries->Fetch();
                $scName = trim($arCountrie['NAME']);
            }
        }

        $scName = empty($scName) ? 'Россия' : $scName;

        $spMask = array('Россия' => '+79000000000', 'Казахстан' => '+77000000000', 'Беларусь' => '+375000000000');
        $sPhoneError = true;

        $sPhone = isset($_REQUEST['ORDER_PROP_3']) ? $_REQUEST['ORDER_PROP_3'] : false;
        $sPhone = !$sPhone && isset($_REQUEST['ORDER_PROP_14'])? $_REQUEST['ORDER_PROP_14'] : $sPhone;
        $sPhone = trim($sPhone);

        if(!empty($scName) && isset($spMask[$scName])){

            $sPhone = trim($sPhone);

            if(!empty($sPhone)
                && preg_match('~[\+0-9]+~is',$sPhone)
                && strlen($sPhone) === strlen($spMask[$scName])) {

                $smStart = str_ireplace('0','',$spMask[$scName]);

                if(stripos($sPhone,$smStart) === 0) {
                    $sPhoneError = false;
                }

            }

        } else {
            $sPhoneError = false;
        }


        if($sPhoneError) {
            $_SESSION['order_error'] = 'Не правильно задан телефон. Формат телефона: '.$spMask[$scName].'. Вы ввели: '.$sPhone;
        }


        if(!empty($_SESSION['order_error'])){

            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::ERROR,
                new \Bitrix\Sale\ResultError($_SESSION['order_error']),
                'sale'
            );
        };


    }

}


function changeSaleProperties($event){

    global $APPLICATION;

    if($event instanceof \Bitrix\Main\Event){

        $parameters = $event->getParameters();
        $order = $parameters['ENTITY'];

    } elseif(is_numeric($event)) {

        $order = \Bitrix\Sale\Order::load($event);
    }

    if (!$order instanceof \Bitrix\Sale\Order) {
        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::ERROR,
            new \Bitrix\Sale\ResultError('Неверный объект заказа', 'SALE_EVENT_WRONG_ORDER'),
            'sale'
        );
    }

    if(
        $APPLICATION->GetCurPage() != '/bitrix/admin/1c_exchange.php'
    ) {

        $chained_sizeof = Bitrix\Main\Config\Option::Get('my.stat', "chained_sizeof", 0);

        $chained = array();
        $props = array();
        
        for ($i = 0; $i < $chained_sizeof; $i++) {

            $props[$i] = unserialize(Bitrix\Main\Config\Option::Get('my.stat', "chained_prop" . $i, ""));
            $chained['payments'][$i] = Bitrix\Main\Config\Option::Get('my.stat', "chained_payments" . $i, "");
            $chained['deliveries'][$i] = Bitrix\Main\Config\Option::Get('my.stat', "chained_deliveries" . $i, "");
        }

        foreach ($props as $number => $parr) {
            foreach ($parr as $propid => $value) {
                $chained['prop'][$propid][$number] = $value;
            }
        }

        unset($props, $i, $chained_sizeof);


        if (!empty($chained)) {

            $paymentIds = $order->getPaymentSystemId(); // массив id способов оплат
            $deliveryIds = $order->getDeliverySystemId(); // массив id способов доставки
            $propertyCollection = $order->loadPropertyCollection();

            $paymentIds = array_unique($paymentIds);
            $deliveryIds = array_unique($deliveryIds);

            $deliveryIds = !empty($deliveryIds)
            && sizeof($deliveryIds) > 1
                ? array(current($deliveryIds))
                : $deliveryIds;
                                
file_put_contents(__DIR__.'/info.log','333');

            $providerCodeKey = 75; // shipping provider code
            $currentProp = $propertyCollection->getItemByOrderPropertyId($providerCodeKey);
            if($currentProp != null) {          
                $providerCode = $currentProp->getValue();
            }

            if(empty($providerCode)) {
                $providerCodeKey = 143; // shipping provider code
                $currentProp = $propertyCollection->getItemByOrderPropertyId($providerCodeKey);
                if($currentProp != null) {        
                    $providerCode = $currentProp->getValue();
                }
            }

file_put_contents(__DIR__.'/info.log','chained = ', FILE_APPEND);
file_put_contents(__DIR__.'/info.log', var_export($chained, true), FILE_APPEND);

            if (isset($chained['deliveries'])) {

                foreach ($chained['deliveries'] as $number => $deliveryID) {
                    if (!empty($deliveryID)
                        && in_array($deliveryID, $deliveryIds)
                        && isset($chained['prop'])
                        && is_array($chained['prop'])
                        && !empty($chained['prop'] || empty($deliveryID))
                    ) {
                        $paymentID = $chained['payments'][$number];

                        if((!empty($paymentID)
                            && is_array($chained['payments'])
                            && in_array($paymentID, $paymentIds)
                            && isset($chained['prop'])
                            && is_array($chained['prop'])
                            && !empty($chained['prop'])) || empty($paymentID)) {    
                                $providerPropValue = $chained['prop'][145];
                
// file_put_contents(__DIR__.'/info.log','providerPropValue = ', FILE_APPEND);
// file_put_contents(__DIR__.'/info.log',var_export($providerPropValue[$number],true), FILE_APPEND);
                                
                                if($currentProp && $providerCode != "" && $providerPropValue[$number] != "" && $providerCode != $providerPropValue[$number]) {
                                    continue;
                                } else {
                                    updateDefaultSaleOrderProperties($order, $chained['prop'], $number);
                                    break;
                                }                                
                        }
                    }
                }
            }

                    
        }
    }
}


function updateDefaultSaleOrderProperties($order, $propsArray, $number){
    foreach($propsArray as $propId => $propValue){
        if(isset($propValue[$number])
            && trim($propValue[$number]) != ""){

            $propertyCollection = $order->getPropertyCollection();
            $currentProp = $propertyCollection->getItemByOrderPropertyId($propId);

            if($currentProp){
                $currentProp->setValue($propValue[$number]);
                $currentProp->save();
            }
        }
    }
}

function setOrderAjaxVoting($orderId,$check = false){

    if(mb_stripos(IMPEL_SERVER_NAME,'youtwig.ru') === false)
        return false;

    global $APPLICATION,$USER;
    $order = Bitrix\Sale\Order::load($orderId);

    setOrderReferer($orderId);

    $paymentid_sao_renderoptin = Bitrix\Main\Config\Option::Get('my.stat', "paymentid_sao_renderoptin", "");
    $paymentid_sao_renderoptin = explode(',',$paymentid_sao_renderoptin);
    $paymentid_sao_renderoptin = !empty($paymentid_sao_renderoptin)
    &&!is_array($paymentid_sao_renderoptin)
        ? array($paymentid_sao_renderoptin)
        : $paymentid_sao_renderoptin;

    $agmCookieName = "agmq".$order->getField("ID");
    $canVote = $APPLICATION->get_cookie($agmCookieName);

    if(
        (!$canVote && (is_array($paymentid_sao_renderoptin)
                && !empty($paymentid_sao_renderoptin)
                && in_array($order->getField("PAY_SYSTEM_ID"),$paymentid_sao_renderoptin))
            || $check)
    ){

        $sao_delivery_sizeof = Bitrix\Main\Config\Option::Get('my.stat', "sao_delivery_sizeof", "");

        $sao_delivery_to_date = array();
        $sao_default_delivery_days = 3;


        for($dcount = 0; $dcount < $sao_delivery_sizeof; $dcount++){
            $sao_delivery_id = Bitrix\Main\Config\Option::Get('my.stat', "sao_delivery_id".$dcount, "");
            $sao_delivery_days = Bitrix\Main\Config\Option::Get('my.stat', "sao_delivery_days".$dcount, "");
            $sao_delivery_days = abs((int)trim($sao_delivery_days));
            $sao_delivery_days = empty($sao_delivery_days)
                ? $sao_default_delivery_days
                : $sao_delivery_days;

            $sao_delivery_to_date[$sao_delivery_id] = $sao_delivery_days;

        }

        $country_code_iso3 = 'RU';

        if(!empty($sao_delivery_to_date)){

            if(is_object($order->getField("DATE_INSERT"))
            ){

                $objDateTime = $order->getField("DATE_INSERT");

                $countDays = (isset($sao_delivery_days[$order->getField("DELIVERY_ID")])
                    && !empty($sao_delivery_days[$order->getField("DELIVERY_ID")]))
                    ? $sao_delivery_days[$order->getField("DELIVERY_ID")]
                    : $sao_default_delivery_days;

                $objDateTime->add($countDays." day");


                $propertyCollection = $order->getPropertyCollection();

                $locPropId = 0;

                $emailPropValue = "";
                $emailProp = $propertyCollection->getUserEmail();
                $locProp = $propertyCollection->getDeliveryLocation();

                if($locProp){

                    $locPropId = $locProp->getValue();

                    if($locPropId){
                        $arLocs = \CSaleLocation::GetByID($locPropId, LANGUAGE_ID);

                        if(isset($arLocs['COUNTRY_ID'])
                            && !empty($arLocs['COUNTRY_ID'])){

                            $dbCountries = \CSaleLocation::GetCountryList(array(),array('ID' => $arLocs['COUNTRY_ID']));
                            $arCountries = $dbCountries->Fetch();

                            if(isset($arCountries['NAME_ORIG'])
                                && !empty($arCountries['NAME_ORIG'])){

                                require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/sale.order.ajax/main_test/classes/ISO3166.php';

                                $data = (new \League\ISO3166\ISO3166)->name($arCountries['NAME_ORIG']);

                                if($data
                                    && isset($data['alpha2'])
                                    && !empty($data['alpha2'])){

                                    $country_code_iso3 = $data['alpha2'];

                                }
                            }
                        }
                    }
                }

                if($emailProp){
                    $emailPropValue = $emailProp->getValue();
                }

                $APPLICATION->set_cookie($agmCookieName, true, time()+60*60*24*30*12, "/");

                ?>
                <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async="async" defer="defer"></script>
                <script>
                    window.renderOptIn = function() {
                        window.gapi.load('surveyoptin', function() {
                            window.gapi.surveyoptin.render(
                                {
                                    "merchant_id": "10360823",
                                    "order_id": "<?php echo $order->getId(); ?>",
                                    "email": "<?php echo $emailPropValue; ?>",
                                    "delivery_country": "<?php echo $country_code_iso3; ?>",
                                    "estimated_delivery_date": "<?php echo $objDateTime->format("Y-m-d"); ?>",
                                    "opt_in_style": "CENTER_DIALOG"
                                });
                        });
                    }
                </script>
                <?php

            }

        }

    }

}


\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'OnSalePaymentEntitySavedHandler'
);

/*
В обработчике определяется, что, если оплата выполняется через внутренний счет,
то выполняются какие-то свои действия при сохранении сущности:
*/

function OnSalePaymentEntitySavedHandler(\Bitrix\Main\Event $event)
{
    global $USER;

    /** @var Payment $payment */
    $order = $event->getParameter("ENTITY");

    if($order && is_object($order)){
        $orderId = $order->getId();

        if($orderId
            && (((isset($_POST['BX_HANDLER'])
                && $_POST['BX_HANDLER'] == 'YANDEX'
                && isset($_SERVER['HTTP_USER_AGENT'])
                && (mb_stripos($_SERVER['HTTP_USER_AGENT'],'Yandex.Money') !== false)
                && $_SERVER['REQUEST_URI'] == '/bitrix/tools/sale_ps_result.php')
            ))
        ){

            $order = \Bitrix\Sale\Order::load($orderId);
            $getUserId = $order->getUserId();

            if($getUserId){

                $userOrderArr = array('UF_LAST_POID' => $orderId);
                $USER->Update($getUserId, $userOrderArr);

            }

        }

    }

}

function getGoogleVotingAfterOrder(){
    global $USER, $APPLICATION;

    if(mb_stripos(IMPEL_SERVER_NAME,'youtwig.ru') === false)
        return false;

    if($USER->IsAuthorized()){
        $fUser = $USER->GetByID($USER->GetId());

        if($fUser){

            $arUser = $fUser->Fetch();

            if(isset($arUser['UF_LAST_POID'])
                && !empty($arUser['UF_LAST_POID'])){

                $orderId = (int)trim($arUser['UF_LAST_POID']);
                $order = Bitrix\Sale\Order::load($orderId);

                if($order->isPaid()){

                    setOrderAjaxVoting($orderId,true);

                    $userOrderArr = array('UF_LAST_POID' => 0);
                    $USER->Update($USER->GetId(), $userOrderArr);

                }

            }

        }

    }

}

AddEventHandler("main", "OnBeforeProlog", "changeSDEKDelivery");

function changeSDEKDelivery($orderId = 0) {

    global $APPLICATION, $USER;

    if(empty($orderId)
        && ($APPLICATION->GetCurPage() == '/personal/cart/'
            || $APPLICATION->GetCurPage() == '/personal/provider/')){
        $orderId = (int)$_REQUEST['ORDER_ID'];
    }

    if(empty($orderId)){
        return false;
    }

    $order = \Bitrix\Sale\Order::load($orderId);

    if(empty($order)){
        return false;
    }

    if($order->getUserId() != $USER->GetID()
        && (
            $APPLICATION->GetCurPage() == '/personal/cart/'
            || $APPLICATION->GetCurPage() == '/personal/provider/'
        )){
        return false;
    }

    if(!$order->isCanceled()){

        $shipmentCollection = $order->getShipmentCollection();

        foreach ($shipmentCollection as $shipment) {

            $shipmentItemCollection = $shipment->getShipmentItemCollection();
            $emptyShipment = true;

            if(($shipmentItemCollection
                && is_object($shipmentItemCollection))){

                if(sizeof($shipmentItemCollection)){

                    foreach($shipmentItemCollection as $item) {

                        $basketItem = $item->getBasketItem();

                        if($basketItem->getProductId()){

                            $emptyShipment = false;
                        }

                    }

                }

            };


            if(!$shipment->isSystem()
                && !$shipment->getDeliveryId() == 0
                && !$emptyShipment
                && in_array($shipment->getDeliveryId(),array(32,33,43,44))
            ){

                $deliveryId = $shipment->getDeliveryId();

                $propertyCollection = $order->getPropertyCollection();

                if($propertyCollection) {

                    $sdekPropertyId = getPropertyIdByCode($propertyCollection, 'IPOLSDEK_CNTDTARIF');
                    $ipolsdekCntdtarifProp = $propertyCollection->getItemByOrderPropertyId($sdekPropertyId);

                    if(!empty($ipolsdekCntdtarifProp)){

                        $ipolsdekCntdtarif = $ipolsdekCntdtarifProp->getValue();

                        if($ipolsdekCntdtarif == 234
                            && $deliveryId != 44){
                            $changeDeliveryId = 44;
                        }

                        if($ipolsdekCntdtarif == 136
                            && $deliveryId != 33){
                            $changeDeliveryId = 33;
                        }

                        if($ipolsdekCntdtarif == 137
                            && $deliveryId != 32){
                            $changeDeliveryId = 32;
                        }


                        if($ipolsdekCntdtarif == 233
                            && $deliveryId != 43){
                            $changeDeliveryId = 43;
                        }

                    }


                }

                if($changeDeliveryId > 0){

                    $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($changeDeliveryId);

                    $shipmentFields = array();
                    $deliveryName = '';

                    if (!empty($deliveryObj)) {

                        if ($deliveryObj->isProfile()) {
                            $deliveryName = $deliveryObj->getNameWithParent();
                        } else {
                            $deliveryName = $deliveryObj->getName();
                        }
                    }

                    if(!empty($deliveryName)
                        && $shipment->getDeliveryId() != $changeDeliveryId){

                        $shipmentFields['DELIVERY_ID'] = $changeDeliveryId;
                        $shipmentFields['DELIVERY_NAME'] = $deliveryName;

                    }

                    if(!empty($shipmentFields)
                        && $basket = $order->getBasket()){

                        $shipment->setFields($shipmentFields);
                        $shipment->save();

                        $discount = $order->getDiscount();
                        \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
                        \Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
                        $discount->setOrderRefresh(true);
                        $discount->setApplyResult(array());
                        $basket->refreshData(array('PRICE', 'COUPONS', 'PRICE_DELIVERY'));
                        $discount->calculate();

                        if (!$order->isCanceled() && !$order->isPaid()){

                            if (($paymentCollection = $order->getPaymentCollection())
                                && count($paymentCollection) == 1) {

                                if (($payment = $paymentCollection->rewind())
                                    && !$payment->isPaid()){

                                    $payment->setFieldNoDemand('SUM', $order->getPrice());

                                }
                            }
                        }

                        $order->save();

                    }

                }


            }


        }

        $provider_percent = COption::GetOptionString("my.stat", "provider_percent", 0);

        if($APPLICATION->GetCurPage() == '/personal/provider/' && !isset($_REQUEST["change_once"])) {

            $_REQUEST["change_once"] = true;

            $shipmentCollection = $order->getShipmentCollection();

            foreach ($shipmentCollection as $shipment) {

                $shipmentItemCollection = $shipment->getShipmentItemCollection();
                $emptyShipment = true;

                if(($shipmentItemCollection
                    && is_object($shipmentItemCollection))){

                    if(sizeof($shipmentItemCollection)){

                        foreach($shipmentItemCollection as $item) {

                            $basketItem = $item->getBasketItem();

                            if($basketItem->getProductId()){

                                $emptyShipment = false;
                            }

                        }

                    }

                };


                if(!$shipment->isSystem()
                    && !$shipment->getDeliveryId() == 0
                    && !$emptyShipment
                ){

                    if($shipment->getDeliveryId() > 0){

                        $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($shipment->getDeliveryId());

                        $shipmentFields = array();
                        $deliveryName = '';

                        if (!empty($deliveryObj)) {

                            if ($deliveryObj->isProfile()) {
                                $deliveryName = $deliveryObj->getNameWithParent();
                            } else {
                                $deliveryName = $deliveryObj->getName();
                            }
                        }

                        if(!empty($deliveryName)
                            && $shipment->getDeliveryId()){

                            $shipmentFields['DELIVERY_ID'] = $shipment->getDeliveryId();
                            $shipmentFields['DELIVERY_NAME'] = $deliveryName;

                        }

                        if(!empty($shipmentFields)
                            && $basket = $order->getBasket()){

                            $shipment->setFields($shipmentFields);
                            $shipment->save();

                            $discount = $order->getDiscount();
                            \Bitrix\Sale\DiscountCouponsManager::clearApply(true);
                            \Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
                            $discount->setOrderRefresh(true);
                            $discount->setApplyResult(array());
                            $basket->refreshData(array('PRICE', 'COUPONS', 'PRICE_DELIVERY'));
                            $discount->calculate();

                            if (!$order->isCanceled() && !$order->isPaid()){

                                if (($paymentCollection = $order->getPaymentCollection())
                                    && count($paymentCollection) == 1) {

                                    if (($payment = $paymentCollection->rewind())
                                        && !$payment->isPaid()
                                    ){

                                        $iPrice = $order->getPrice() - $order->getDeliveryPrice();
                                        $ioPrice = ceil($provider_percent * $iPrice / 100);
                                        $payment->setFieldNoDemand('SUM', $ioPrice);

                                    }
                                }
                            }

                            $order->save();

                        }

                    }


                }


            }

        }

    }


}

function notChangePrice($order){

    if ($order && is_object($order)) {

        $propertyCollection = $order->getPropertyCollection();

        $fPrice = '';

        if($propertyCollection) {

            $fpricePropertyId = getPropertyIdByCode($propertyCollection, 'FIXEDPRICE');

            if ($fpricePropertyId) {

                $fPrice = $propertyCollection->getItemByOrderPropertyId($fpricePropertyId);

            }

        }

    }

    return $fPrice;
}

function saoChangeTypeId($orderId)
{
    global $DB;

    $order = \Bitrix\Sale\Order::loadByAccountNumber($orderId);

    if($order){

        $sao_typeid_sizeof = 0;
        $sao_payment_id = 0;
        $sao_persontype_id = 0;
        $sao_payment_to_person = array();

        $sao_typeid_sizeof = Bitrix\Main\Config\Option::Get('my.stat', "sao_typeid_sizeof", "");


        if($sao_typeid_sizeof){

            for($i = 0; $i < $sao_typeid_sizeof; $i++){

                $sao_payment_id = Bitrix\Main\Config\Option::Get('my.stat', "sao_payment_id".$i, "");
                $sao_persontype_id = Bitrix\Main\Config\Option::Get('my.stat', "sao_persontype_id".$i, "");
                $sao_payment_to_person[$sao_payment_id] = $sao_persontype_id;

            }

        }

        $personTypeId = $order->getPersonTypeId();

        $paymentIds = $order->getPaymentSystemId();


        $typeChanged = false;

        foreach($paymentIds as $paymentId){
            if(isset($sao_payment_to_person[$paymentId])){
                $typeChanged = $sao_payment_to_person[$paymentId];
                break;
            }

        }

        if($typeChanged === false
            && $personTypeId != 1){
            $typeChanged = 1;
        }

        if($typeChanged
            && $typeChanged != $personTypeId){

            $propertyCollection = $order->getPropertyCollection();
            $aProperties = $propertyCollection->getArray();

            if(isset($aProperties['properties'])
                && !empty($aProperties['properties'])){

                //$order->setField('PERSON_TYPE_ID', intval($typeChanged));
                $order->setPersonTypeId($typeChanged);
                $order->save();

                $DB->query('UPDATE b_sale_order SET PERSON_TYPE_ID = '.(int)$typeChanged.' WHERE ID = '.(int)$orderId.' LIMIT 1');

                $properties = $aProperties['properties'];

                $order = \Bitrix\Sale\Order::loadByAccountNumber($orderId);
                $propertyCollection = $order->getPropertyCollection();
                $newProps = array();

                $doProps = \Bitrix\Sale\Internals\OrderPropsTable::getList(
                    array('filter' =>
                        array(
                            '=PERSON_TYPE_ID' => $typeChanged,
                            '=ACTIVE' => 'Y'
                        ),
                        'order' => array(
                            'SORT' => 'ASC',
                            'NAME' => 'ASC'
                        ),
                        'select' => array(
                            'ID',
                            'CODE',
                        )
                    )
                );

                $aProps = $doProps->fetchAll();

                foreach($aProps as $aProp){

                    $newProps[$aProp['CODE']] = $aProp['ID'];

                }

                foreach($properties as $property){

                    if(isset($property['CODE'])
                        && !empty($property['CODE'])
                        && isset($newProps[$property['CODE']])
                    ){

                        $nPropValue = $propertyCollection->getItemByOrderPropertyId($newProps[$property['CODE']]);

                        if($nPropValue){

                            $nPropValue->setValue(current($property['VALUE']));
                            $nPropValue->save();

                        }

                    }

                    if(isset($property['ID'])){
                        $oPropValue = $propertyCollection->getItemByOrderPropertyId($property['ID']);

                        if($oPropValue){
                            $oPropValue->delete();
                        }

                    }

                }

                $order->save();

            }


        }

    }


}

function doArchiveOrder($ID){

    \Bitrix\Sale\Archive\Manager::archiveOrders(
        array(
            "ID" => array($ID)
        )
    );


}

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'onSaleDeliveryServiceCalculate',
    'calculateDelivery'
);

function calculateDelivery(\Bitrix\Main\Event $event)
{
    global $USER;

    /** @var Delivery\CalculationResult $baseResult */
    $baseResult = $event->getParameter('RESULT');
    $shipment = $event->getParameter('SHIPMENT');

    if($shipment &&
        !$shipment->isSystem()
        && !$shipment->getDeliveryId() == 0){

        $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($shipment->getDeliveryId());

        $deliveryId = '';

        if (!empty($deliveryObj)) {

            if ($deliveryObj->isProfile()) {
                $deliveryId = $deliveryObj->getParentId();
            } else {
                $deliveryId = $shipment->getDeliveryId();
            }
        }



        if($deliveryId == 45){

            $price = (float)$baseResult->getDeliveryPrice();

            $per_dvr_price = Bitrix\Main\Config\Option::Get('my.stat', "per_dvr_price", "");
            $min_dvr_price = Bitrix\Main\Config\Option::Get('my.stat', "min_dvr_price", "");

            $anyChanged = false;

            if($per_dvr_price){
                $price = $price / 100 * $per_dvr_price + $price;
                $price = ceil($price);
                $anyChanged = true;
            }


            if($min_dvr_price){
                $price = $price < $min_dvr_price ? $min_dvr_price : $price;
                $anyChanged = true;
            }

            if($anyChanged){

                $baseResult->setDeliveryPrice($price);

            }



        }

    }

    $event->addResult(
        new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::SUCCESS, array('RESULT' => $baseResult)
        )
    );
}

function hasDeliveryBoxberry($order)
{

    $hasBoxberry = false;

    $shipmentCollection = $order->getShipmentCollection();

    if ($shipmentCollection)
        foreach ($shipmentCollection as $shipment) {

            if ($shipment &&
                !$shipment->isSystem()
                && !$shipment->getDeliveryId() == 0) {

                $deliveryObj = \Bitrix\Sale\Delivery\Services\Manager::getService($shipment->getDeliveryId());

                $deliveryId = '';

                if (!empty($deliveryObj)) {

                    if ($deliveryObj->isProfile()) {
                        $deliveryId = $deliveryObj->getParentId();
                    } else {
                        $deliveryId = $shipment->getDeliveryId();
                    }
                }

                if (in_array($deliveryId, array(54, 55))) {

                    $hasBoxberry = true;
                    break;

                }

            }

        }

    return $hasBoxberry;

}
