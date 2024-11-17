<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelCartpreorderComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function checkIfModels(){
        global $APPLICATION;

        $return = array();
        $curDir = $_SERVER['HTTP_REFERER'];

        if(mb_stripos($curDir,'/model/') !== false){
            $curDir = preg_replace('~.+?\/\/[^\/]+~','',$curDir);
            $curDir = trim($curDir,'/');
            $curDir = explode('/',$curDir);

            if(isset($curDir[1])
                && !empty($curDir[1])){

                $aSelect = Array(
                    "ID",
                    "PROPERTY_type_of_product",
                    "PROPERTY_manufacturer",
                    "PROPERTY_model_new_link"
                );


                $curDir[1] = preg_replace('~[^A-Z0-9\-\_]~is','',$curDir[1]);

                $aFilter = Array(
                    "CODE" => $curDir[1],
                    "IBLOCK_ID" => 17,
                    "ACTIVE"=>"Y"
                );

                $rmDB = CIBlockElement::GetList(
                    Array(),
                    $aFilter,
                    false,
                    Array("nPageSize"=>1),
                    $aSelect);

                if($rmDB){
                    while($am = $rmDB->GetNext()) {

                        if(isset($am['PROPERTY_MODEL_NEW_LINK_VALUE'])
                            && !empty($am['PROPERTY_MODEL_NEW_LINK_VALUE'])){


                            $rnmDB = CIBlockElement::GetById($am['PROPERTY_MODEL_NEW_LINK_VALUE']);

                            if($rnmDB){

                                $arnm = $rnmDB->GetNext();

                                if(isset($arnm['NAME'])
                                    && !empty($arnm['NAME'])
                                    && isset($am['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                    && !empty($am['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                    && isset($am['PROPERTY_MANUFACTURER_VALUE'])
                                    && !empty($am['PROPERTY_MANUFACTURER_VALUE'])

                                ) {

                                    $return[] = array('NAME' => 'Тип товара',
                                        'CODE' => 'TYPE_OF_PRODUCT',
                                        'VALUE' => $am['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                        'SORT' => 101);

                                    $return[] = array('NAME' => 'Производитель',
                                        'CODE' => 'MANUFACTURER',
                                        'VALUE' => $am['PROPERTY_MANUFACTURER_VALUE'],
                                        'SORT' => 102);

                                    $return[] = array('NAME' => 'Модель',
                                        'CODE' => 'MODEL',
                                        'VALUE' => $arnm['NAME'],
                                        'SORT' => 103);

                                }

                            }

                        }

                    }

                }

            }

        }

        return $return;

    }

    private function isRoznica(int $product_id, array $price):array {

        global $USER;

        $is_roznica = $USER->IsAuthorized() && $USER->getID() && in_array(17,$USER->GetUserGroup($USER->getID())) ? true : false;

        if ($is_roznica) {

            static::setPartnerTypeId();

            $dPrice = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $product_id,
                    "CATALOG_GROUP_ID" => 1
                )
            );

            if ($dPrice
                && $aPrice = $dPrice->Fetch()) {
                $price['PRICE']['PRICE'] = $aPrice['PRICE'];
                $price['PRICE']['CURRENCY']	= $aPrice['CURRENCY'];
                $price['PRICE']['CATALOG_GROUP_ID']	= 1;
            }

        }

        return $price;

    }

    private function setPartnerTypeId():void {

        global $USER;

        $db_sales = CSaleOrderUserProps::GetList(
            array("DATE_UPDATE" => "DESC"),
            array("USER_ID" => $USER->GetID())
        );

        $pFound = false;

        if ($db_sales) {

            while ($ar_sales = $db_sales->Fetch())
            {

                $pFound = true;
                $arFields = ['PERSON_TYPE_ID' => 2];
                CSaleOrderUserProps::Update($ar_sales['ID'], $arFields);

            }
        }

        if (!$pFound) {

            $arFields = array(
                "NAME" => "Патрнер ".$USER->GetID()."",
                "USER_ID" => $USER->GetID(),
                "PERSON_TYPE_ID" => 2
            );

            CSaleOrderUserProps::Add($arFields);
        }

    }

    private function isPartner(int $product_id, array $price):array {

        global $USER;

        $is_partner = $USER->IsAuthorized() && $USER->getID() && in_array(16,$USER->GetUserGroup($USER->getID())) ? true : false;

        if ($is_partner) {

            static::setPartnerTypeId();

            $dPrice = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $product_id,
                    "CATALOG_GROUP_ID" => 8
                )
            );

            if ($dPrice
                && $aPrice = $dPrice->Fetch()) {
                $price['PRICE']['PRICE'] = $aPrice['PRICE'];
                $price['PRICE']['CURRENCY']	= $aPrice['CURRENCY'];
                $price['PRICE']['CATALOG_GROUP_ID']	= 8;
            }

        }

        return $price;

    }

    private function addbasket() {

        $aResult = array(
            'STATUS' => 'ERROR',
            'MESSAGE' => '');

        $properties = array();

        $message = '';

        if(Bitrix\Main\Loader::includeModule("catalog")) {

            $context = \Bitrix\Main\Context::getCurrent();
            $request = $context->getRequest();

            $product_id = isset($request['id'])
            && !empty($request['id'])
                ? (int)$request['id']
                : 0;

            $max_quantity = get_quantity_product($product_id);

            $quantity = isset($request['quantity'])
            && !empty($request['quantity'])
                ? (int)$request['quantity']
                : 1;

            $quantity = !empty($quantity) ? $quantity : 1;
            $quantity = $quantity > $max_quantity ? $max_quantity : $quantity;

            $product_url = '';

            if($product_id) {

                $product_data = $this->getProductData($product_id);
                $product_name = isset($request['product_name']) ? trim($request['product_name']) : $product_data['NAME'];
                $product_name = empty($product_name) ? (isset($product_data['NAME']) ? $product_data['NAME'] : '') : $product_name;
                $product_url = isset($product_data['DETAIL_PAGE_URL']) ? $product_data['DETAIL_PAGE_URL'] : '';

                if($product_name
                    && $product_url){

                    $message = sprintf(GetMessage("TMPL_CAN_NOT_BUY_MORE"),$product_name);

                    $product_buy_id = getBondsProduct($product_id);

                    $outnumber = get_quantity_product($product_buy_id);
                    $poutnumber = get_quantity_product_provider($product_buy_id);

                    if($outnumber > 0){

                        $price = CCatalogProduct::GetOptimalPrice($product_id,1);

                        if(isset($price['PRICE'])
                            && isset($price['PRICE']['PRICE'])
                            && $price['PRICE']['PRICE'] > 0
                            && isset($price['PRICE']['CURRENCY'])){

                            $price = static::isPartner($product_id, $price);
                            $price = static::isRoznica($product_id, $price);

                            $group_name = $this->getCatalogGroupName($product_id,$price);

                            $default_currency = getCurrentCurrencyCode();

                            if($default_currency != $price['PRICE']['CURRENCY']){
                                $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                                $price['PRICE']['CURRENCY']	= $default_currency;
                            }

                            $fields = array(
                                'QUANTITY' => $quantity,
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

                            $provider_percent = COption::GetOptionString("my.stat", "provider_percent", 0);

                            if($poutnumber == $outnumber && $provider_percent > 0){
                                $fields['DELAY'] = 'Y';
                                $bHasProvider = true;
                                $aResult['FROM_PROVIDER'] = true;
                            }

                            $aProperties = $this->checkIfModels();

                            if(!empty($aProperties)){
                                $properties = array_merge((array)$properties,(array)$aProperties);
                            }

                            if($product_id != $product_buy_id) {

                                $base_data = $this->getProductData($product_buy_id);

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

                            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                                \Bitrix\Sale\Fuser::getId(),
                                \Bitrix\Main\Context::getCurrent()->getSite()
                            );

                            $itemExists = $this->cartItemExists($fields,$basket,$product_buy_id);

                            if($itemExists){

                                $itemExists->setField('QUANTITY', $itemExists->getQuantity() + 1);
                                $itemExists->setField('DETAIL_PAGE_URL', $product_url);

                            } else {

                                $itemExists = $basket->createItem('catalog', $product_buy_id);
                                $itemExists->setFields($fields);
                                $itemExists->save();

                                if(!empty($properties)){
                                    $props = $itemExists->getPropertyCollection();
                                    $props->setProperty($properties);
                                    $props->save();
                                }

                            }

                            $basket->save();
                            $aResult['STATUS'] = 'OK';


                        }

                    }

                }

                unset($product_data);

            }

        }

        $aResult['MESSAGE'] = $message;

        return $aResult;

    }

    private function getCatalogGroupName($product_id,$price){

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

        return $group_name;

    }

    private function cartItemExists($fields,$basket,$product_buy_id){

        $existsItem = false;

        foreach ($basket as $basketItem) {

            if($fields['NAME'] == $basketItem->getField('NAME')
                && $product_buy_id == $basketItem->getField('PRODUCT_ID')
            ){

                $existsItem = $basketItem;

            }

        }

        return $existsItem;
    }

    private function getProductData($product_id) {

        $aName = array();

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
            && $aName = $dName->GetNext()){

        }

        return $aName;

    }

    public function executeComponent() {

        $context = \Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();

        $action = (isset($request['action'])
            && !empty($request['action']))
            ? trim($request['action'])
            : 'template';

        switch ($action){
            case 'addbasket':

                $json = $this->addbasket();
                echo json_encode($json);
                die();

                break;

            case 'addyandexbasket':

                $json = $this->addbasket();

                if($json['STATUS'] == 'ERROR') {
                    $_SESSION['order_error'] = $json['MESSAGE'];
                }

                LocalRedirect('/personal/cart/');
                die();

                break;


            case 'template':

            default:

                $this->includeComponentTemplate();

                break;

        }


    }
}