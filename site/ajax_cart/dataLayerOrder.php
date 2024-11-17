<?
define("STOP_STATISTICS", true);
define("ADMIN_SECTION",false);

ini_set('default_charset','utf-8');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$return = array(
    'success' => false,
);

$currency = '';
$price = 0;

$order_id = (int)trim($_REQUEST['ORDER_ID']);

if(!empty($order_id)){

    $return["actionField"] = array(
        array('id' => $order_id)
    );

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

echo json_encode($return);

