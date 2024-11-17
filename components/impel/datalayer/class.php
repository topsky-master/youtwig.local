<?

use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED  !== true)
    die();

class ImpelDatalayerComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function dataLayer($product_id){

        $return = array('success' => false);

        $currency = '';
        $price = 0;

        $product_buy_id = getBondsProduct($product_id);

        $product_name = '';
        $arElFilter = array(
            'ID' => $product_id
        );

        $arElSelect = array(
            'IBLOCK_SECTION_ID',
            'NAME',
            'IBLOCK_ID'
        );

        $sectionId = '';

        $pDBRes = CIBlockElement::GetList(Array(),$arElFilter,false,false,$arElSelect);

        $manufacturer = '';
        $manufacturerarr = array();

        if($pDBRes
            && $pArRes = $pDBRes->GetNext()) {

            $product_name = $pArRes['NAME'];

            if(isset($pArRes['IBLOCK_SECTION_ID'])
                && !empty($pArRes['IBLOCK_SECTION_ID'])){
                $sectionId = $pArRes['IBLOCK_SECTION_ID'];
            };

            $pDBPRes = CIBlockElement::GetProperty(
                $pArRes['IBLOCK_ID'],
                $product_id,
                Array("sort" => "asc"),
                Array("CODE" => "MANUFACTURER_DETAIL")
            );

            if($pDBPRes){
                while($pArPRes = $pDBPRes->Fetch()){
                    if(isset($pArPRes['VALUE_ENUM'])
                        && !empty($pArPRes['VALUE_ENUM'])){

                        $manufacturerarr[] = $pArPRes['VALUE_ENUM'];
                    }

                }

            }

        }

        if(!empty($manufacturerarr)){
            $manufacturer = join(',', $manufacturerarr);
        }

        $sectionName = '';

        if(!empty($sectionId)){

            $rsSection = CIBlockSection::GetList(Array(), Array('ID' => $sectionId), false, array('NAME'));

            if($rsSection){

                $arSection = $rsSection->GetNext();

                if(isset($arSection['NAME'])
                    && !empty($arSection['NAME'])){
                    $sectionName = $arSection['NAME'];
                }
            }
        }

        $basketRes = \Bitrix\Sale\Internals\BasketTable::getList(array(
            'filter' => array(
                'FUSER_ID' => \Bitrix\Sale\Fuser::getId(),
                'ORDER_ID' => null,
                'LID' => SITE_ID,
                'CAN_BUY' => 'Y',
                'NAME' => $product_name,
                'PRODUCT_ID' => $product_buy_id
            )
        ));

        if($basketRes){

            while ($item = $basketRes->fetch()) {

                if(isset($item['PRICE'])
                    && !empty($item['PRICE'])){

                    $price = (int)$item['PRICE'];

                };

                if(isset($item['CURRENCY'])
                    && !empty($item['CURRENCY'])){

                    $currency = $item['CURRENCY'];

                };
            }


        }

        if(!empty($product_name)
            && !empty($price)){

            $return = array(
                "currencyCode" => $currency,
                "id" => $product_id,
                "name" => $product_name,
                "price" => $price,
                "brand" => $manufacturer,
                "category" => $sectionName,
                'success' => true
            );
        }

        return $return;
    }

    private function dataLayerOrder($order_id){

        $return = array(
            'success' => false,
        );

        $currency = '';
        $price = 0;

        if(!empty($order_id)){

            $return["products"] = array();

            $order = \Bitrix\Sale\Order::load($order_id);
            $propertyCollection = $order->getPropertyCollection();

            $basket = $order->getBasket();
            $basketItems = $basket->getBasketItems();

            if($basketItems){
                foreach($basketItems as $basketItem) {

                    $price = 0;
                    $product_id = 0;
                    $product_name = $basketItem->getField('NAME');
                    $main_product_id = 0;
                    $quantitiy = 1;
                    $sectionName = '';
                    $manufacturer = '';
                    $sectionId = '';
                    $manufacturerarr = array();

                    $basketPropertyCollection = $basketItem->getPropertyCollection();

                    if($basketPropertyCollection){
                        foreach($basketPropertyCollection as $property)
                        {
                            if($property
                                && trim($property->getField('CODE')) == 'BASIC'){
                                $main_product_id = $basketItem->getProductId();

                            }
                        }
                    }

                    $currency = $basketItem->getField('CURRENCY');

                    if(!isset($return["currency"])){
                        $return["currency"] = $currency;
                    }

                    $price = $basketItem->getPrice();
                    $quantitiy = $basketItem->getQuantity();   // Количество

                    $arElFilter = array(
                        '=NAME' => $product_name,
                        'IBLOCK_ID' => 11
                    );

                    if(!empty($main_product_id)){
                        $arElFilter["PROPERTY_MAIN_PRODUCTS"] = $main_product_id;
                    } else {
                        $arElFilter["ID"] = $basketItem->getProductId();
                    }

                    $arElSelect = array(
                        'IBLOCK_SECTION_ID',
                        'ID',
                        'IBLOCK_ID'
                    );

                    $pDBCount = CIBlockElement::GetList(Array(),$arElFilter,Array(),false,$arElSelect);

                    if(!$pDBCount
                        && isset($arElFilter["PROPERTY_MAIN_PRODUCTS"])
                        && !empty($arElFilter["PROPERTY_MAIN_PRODUCTS"])){
                        unset($arElFilter["PROPERTY_MAIN_PRODUCTS"]);

                    }

                    $pDBRes = CIBlockElement::GetList(Array(),$arElFilter,false,false,$arElSelect);

                    if($pDBRes
                        && $pArRes = $pDBRes->GetNext()) {

                        $product_id = $pArRes['ID'];

                        if(isset($pArRes['IBLOCK_SECTION_ID'])
                            && !empty($pArRes['IBLOCK_SECTION_ID'])){
                            $sectionId = $pArRes['IBLOCK_SECTION_ID'];
                        };

                        $pDBPRes = CIBlockElement::GetProperty(
                            $pArRes['IBLOCK_ID'],
                            $product_id,
                            Array("sort" => "asc"),
                            Array("CODE" => "MANUFACTURER_DETAIL")
                        );

                        if($pDBPRes){
                            while($pArPRes = $pDBPRes->Fetch()){
                                if(isset($pArPRes['VALUE_ENUM'])
                                    && !empty($pArPRes['VALUE_ENUM'])){

                                    $manufacturerarr[] = $pArPRes['VALUE_ENUM'];
                                }

                            }

                        }

                        if(!empty($manufacturerarr)){
                            $manufacturer = join(',', $manufacturerarr);
                        }

                        if(!empty($sectionId)){

                            $rsSection = CIBlockSection::GetList(Array(), Array('ID' => $sectionId), false, array('NAME'));

                            if($rsSection){

                                $arSection = $rsSection->GetNext();

                                if(isset($arSection['NAME'])
                                    && !empty($arSection['NAME'])){
                                    $sectionName = $arSection['NAME'];
                                }
                            }
                        }


                        if(!empty($product_name)
                            && !empty($price)){

                            $return['products'][] = array(

                                "id" => $product_id,
                                "name" => $product_name,
                                "price" => $price,
                                "brand" => $manufacturer,
                                "category" => $sectionName,
                                "quantitiy" => $quantitiy,

                            );

                            $return['success'] = true;

                        }

                    }

                }

            }

        }

        return $return;

    }

    public function executeComponent()
    {

        if(isset($_REQUEST['ID'])){

            $product_id = (int)trim($_REQUEST['ID']);

            if(!empty($product_id)){
                $return = $this->dataLayer($product_id);
                die(json_encode($return));
            }

        }

        if(isset($_REQUEST['ORDER_ID'])){

            $order_id = (int)trim($_REQUEST['ORDER_ID']);

            if(!empty($order_id)){
                $return = $this->dataLayerOrder($order_id);
                die(json_encode($return));
            }
        }

    }

}