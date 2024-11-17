<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelPasscodeComponent extends CBitrixComponent
{
    private $errorMsg = '';
    private $resultMsg = '';

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function sendUserPassword(){

        global $APPLICATION;

        if(isset($_SESSION['pass_user_id'])
            && !empty($_SESSION['pass_user_id'])
            && isset($_SESSION['pass_email'])
            && !empty($_SESSION['pass_email'])
            && isset($_SESSION['pass_string'])
            && !empty($_SESSION['pass_string'])
            && check_bitrix_sessid()
        ){


            $aEventFields = array(
                "SIMPLE_PASSWORD" => $_SESSION['pass_string'],
                "SIMPLE_EMAIL" => $_SESSION['pass_email'],
                "SIMPLE_PASSWORD_LINK" => (CMain::IsHTTPS() ? 'https' : 'http' ). '://' . $_SERVER['SERVER_NAME'] . $APPLICATION->GetCurDir()
            );

            $this->resultMsg = GetMessage('CT_BNL_ELEMENT_CHECK_SEND');

            $context = \Bitrix\Main\Context::getCurrent();
            $siteId = $context->getSite();

            CEvent::Send("SIMPLE_PASSWORD", $siteId, $aEventFields);
            $this->arResult['pass_action'] = 'checkpassword';


        } else {

            $this->errorMsg = GetMessage('CT_BNL_ELEMENT_ERROR_CHECK_SEND');

        }

    }

    private function genPassword(){

        if(check_bitrix_sessid()){

            if(!(isset($_SESSION['pass_string'])
                && !empty($_SESSION['pass_string']))
            ){

                $passString = randString(
                    mt_rand(4,5),
                    array(
                        "0123456789"
                    )
                );

                $_SESSION['pass_string'] = $passString;


            }

            $this->setUserLogin();


        }

    }

    private function setUserLogin(){

        $_SESSION['pass_user_id'] = false;
        $context = \Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();

        if(isset($request['pass_user'])
            && !empty($request['pass_user'])
            && check_bitrix_sessid()) {


            $isEMail = (mb_stripos($request['pass_user'],'@') !== false
                && filter_var($request['pass_user'],FILTER_VALIDATE_EMAIL))
                ? true
                : false;

            if($user_id = $this->checkUser($request['pass_user'],$isEMail)){

                $this->sendUserPassword();

            }


        } else {

            $this->errorMsg = GetMessage('CT_BNL_ELEMENT_ERROR_USER_IS_EMPTY');

        }

    }

    private function checkEmail() {

        $return = array();
        $context = \Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();
        $return['emailFound'] = $this->checkUser($request['pass_user'],true);
        $return = json_encode($return);

        return $return;

    }

    private function checkUser($user,$isEMail = false){

        $user_id = $_SESSION['pass_user_id'] = false;
        $aUserFilter = array();

        if($isEMail){

            $aUserFilter["=EMAIL"] = filter_var($user,FILTER_VALIDATE_EMAIL);

        } else {

            $aUserFilter["LOGIN_EQUAL"] = filter_var($user);

        }

        $rUsers = CUser::GetList(
            ($by = "id"),
            ($order = "desc"),
            $aUserFilter
        );

        if($rUsers
            && $aUsers = $rUsers->Fetch()){


            if(isset($aUsers['ID'])
                && !empty($aUsers['ID'])){

                $_SESSION['pass_email'] = isset($aUsers['EMAIL'])
                    ? $aUsers['EMAIL']
                    : false;

                $user_id = $_SESSION['pass_user_id'] = $aUsers['ID'];

            }

        }

        if(!$user_id){
            $this->errorMsg = GetMessage('CT_BNL_ELEMENT_ERROR_USER_NOT_FOUND');
        }

        return $user_id;

    }

    private function checkPassword(){

        $context = \Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();

        return (isset($request['pass_string'])
            && !empty($request['pass_string'])
            && isset($request['pass_string'])
            && !empty($request['pass_string'])
            && ($request['pass_string'] == $_SESSION['pass_string']))
            ? true
            : false;

    }

    private function executeAction(){

        global $USER;

        switch ($this->arResult['pass_action']){

            case 'genpassword':

                $this->genPassword();

                break;

            case 'checkpassword':

                if($this->checkPassword()
                    && isset($_SESSION['pass_user_id'])
                    && !empty($_SESSION['pass_user_id'])
                    && is_numeric($_SESSION['pass_user_id'])){

                    $basket = Bitrix\Sale\Basket::loadItemsForFUser(
                        Bitrix\Sale\Fuser::getId(),
                        Bitrix\Main\Context::getCurrent()->getSite()
                    );

                    $basketItems = $basket->getBasketItems();

                    $newBasket = array();

                    foreach ($basketItems as $basketItem) {

                        $aFilter = Array(
                            "IBLOCK_ID" => 11,
                            "=NAME" => $basketItem->getField('NAME'));

                        $aSelect = Array(
                            'ID'
                        );

                        $dRes = CIBlockElement::GetList(
                            Array(),
                            $aFilter,
                            false,
                            false,
                            $aSelect
                        );

                        $productId = 0;

                        if($dRes
                            && $aRes = $dRes->GetNext()) {
                            $productId = $aRes['ID'];
                        }

                        if($productId)
                            $newBasket[$productId] = $basketItem->getField('QUANTITY');



                    }

                    if($USER->Authorize($_SESSION['pass_user_id'])){
                        $this->arResult['pass_action'] = 'is_authtorized';
                        $this->arResult['need_reload'] = true;

                        $basket = Bitrix\Sale\Basket::loadItemsForFUser(
                            Bitrix\Sale\Fuser::getId(),
                            Bitrix\Main\Context::getCurrent()->getSite()
                        );

                        $basketItems = $basket->getBasketItems();

                        foreach ($basketItems as $basketItem) {

                            $basketItem->delete();
                            $basketItem->save();

                        }

                        $basket->save();


                        if(!empty($newBasket)){

                            foreach ($newBasket as $productId => $quantity){

                                $this->Add2BasketByProductID($productId,$quantity);

                            }

                        }

                    }

                } else {

                    $this->errorMsg = GetMessage('CT_BNL_ELEMENT_ERROR_CHECK_PASSWORD');

                }

                break;

            case 'checkemail':

                if(!headers_sent())
                    ob_end_clean();

                echo $this->checkEmail();

                die();

                break;

        }


    }

    private function Add2BasketByProductID($productId,$quantity){
        if(!empty($productId)) {

            $properties = array();

            $message = '';


            $context = \Bitrix\Main\Context::getCurrent();

            $product_url = '';

            $dName = CIBlockElement::GetList(
                array(),
                ($aFilter = array(
                    'ID' => $productId
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

            if ($dName
                && $product_data = $dName->GetNext()) {

            }

            $product_name = isset($product_data['NAME']) ? $product_data['NAME'] : '';
            $product_url = isset($product_data['DETAIL_PAGE_URL']) ? $product_data['DETAIL_PAGE_URL'] : '';


            if ($product_name
                && $product_url) {

                $message = sprintf(GetMessage("TMPL_CAN_NOT_BUY_MORE"), $product_name);

                $product_buy_id = getBondsProduct($productId);

                $outnumber = get_quantity_product($product_buy_id);

                if ($outnumber > 0) {

                    $price = CCatalogProduct::GetOptimalPrice($productId, 1);

                    if (isset($price['PRICE'])
                        && isset($price['PRICE']['PRICE'])
                        && $price['PRICE']['PRICE'] > 0
                        && isset($price['PRICE']['CURRENCY'])) {

                        $aPrice = array();

                        $group_name = '';

                        $dPrice = CPrice::GetList(
                            array(),
                            array(
                                "PRODUCT_ID" => $productId,
                                "CATALOG_GROUP_ID" => $price['PRICE']['CATALOG_GROUP_ID']
                            )
                        );

                        if ($dPrice
                            && $aPrice = $dPrice->Fetch()) {
                            if (isset($aPrice['CATALOG_GROUP_NAME']))
                                $group_name = $aPrice['CATALOG_GROUP_NAME'];
                        }

                        $default_currency = getCurrentCurrencyCode();

                        if ($default_currency != $price['PRICE']['CURRENCY']) {
                            $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $default_currency, "", $product_buy_id);
                            $price['PRICE']['CURRENCY'] = $default_currency;
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
                            'NOTES' => $group_name
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

                        if ($productId != $product_buy_id) {

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

                            if ($dName
                                && $base_data = $dName->GetNext()) {

                            }

                            if (isset($base_data['NAME'])) {

                                $fields['CATALOG_XML_ID'] = $properties[0]['VALUE'] = $base_data['IBLOCK_EXTERNAL_ID'];
                                $fields['PRODUCT_XML_ID'] = $properties[1]['VALUE'] = $base_data['XML_ID'];

                                $properties[] =
                                    array(
                                        'NAME' => 'Базовый товар',
                                        'CODE' => 'BASIC',
                                        'VALUE' => $base_data['NAME'],
                                        'SORT' => 100
                                    );

                            }

                            unset($base_data);

                        }

                        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                            \Bitrix\Sale\Fuser::getId(),
                            \Bitrix\Main\Context::getCurrent()->getSite()
                        );


                        $itemExists = $basket->createItem('catalog', $product_buy_id);
                        $itemExists->setFields($fields);
                        $itemExists->save();

                        if (!empty($properties)) {
                            $props = $itemExists->getPropertyCollection();
                            $props->setProperty($properties);
                            $props->save();
                        }

                        $basket->save();

                        $aResult = array(
                            'STATUS' => 'OK',
                            'MESSAGE' => GetMessage('TMPL_BASKET_ADD_OK')
                        );


                    }

                }


                unset($product_data);

            }



        }
    }

    public function executeComponent()
    {

        global $APPLICATION;

        $this->arResult = array();

        $context = \Bitrix\Main\Context::getCurrent();
        $request = $context->getRequest();

        $action = isset($request['pass_action']) ? $request['pass_action'] : 'genpassword';

        $this->arResult['pass_action'] = $action;

        $this->arResult['is_authtorized'] = CUser::IsAuthorized();

        if(!$this->arResult['is_authtorized']){
            $this->executeAction();
        } else {
            $this->arResult['pass_action'] = 'is_authtorized';
        }

        $this->arResult['error_msg'] = $this->errorMsg;
        $this->arResult['result_msg'] = $this->resultMsg;

        $this->includeComponentTemplate();

    }

}