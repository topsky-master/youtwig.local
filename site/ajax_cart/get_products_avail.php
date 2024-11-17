<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/bitrix/catalog/catalog/bitrix/catalog.element/.default/lang/'.LANGUAGE_ID.'/template.php');

$result = array();
$product_ids = $_REQUEST['product_id'];

if(!empty($product_ids) && is_array($product_ids)){

    $product_ids = array_map('intval',$product_ids);
    $product_ids = array_map('abs',$product_ids);
    $product_ids = array_unique($product_ids);
    $product_ids = array_filter($product_ids);

    if(!empty($product_ids)){

        foreach($product_ids as $product_id){
            $can_buy = false;
            $quantity = 0;
            $can_buy = canYouBuy($product_id);
            $quantity = get_quantity_product($product_id);
            $can_buy = $quantity > 0 ? $can_buy : false;

            if($can_buy){


                $buy_id = getBondsProduct($product_id);

                $rsStore = CCatalogStoreProduct::GetList(
                    array(),
                    array('PRODUCT_ID' => $buy_id, "!STORE_ID" => array(3,10)),
                    false,
                    false
                );

                $arResult['STORES'] = array();
                $in_stock_label = '';

                if ($rsStore){

                    while($arStore = $rsStore->Fetch()){

                        $amount = (float)$arStore['AMOUNT'];

                        $in_stock_label .= '<p>' . (GetMessage('CT_BCE_CATALOG_STORE_'.$arStore['STORE_ID']) != "" ? GetMessage('CT_BCE_CATALOG_STORE_'.$arStore['STORE_ID']) : ($arStore['STORE_NAME'])).' ';

                        if($amount <= 0){
                            $in_stock_label .= GetMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
                        } elseif($amount <= 10){
                            $in_stock_label .= GetMessage('CT_BCS_CATALOG_IN_STOCK_NOT_MUCH_LABEL');
                        } elseif($amount > 10){
                            $in_stock_label .= GetMessage('CT_BCS_CATALOG_IN_STOCK_MUCH_LABEL');
                        }

                        $in_stock_label .= '</p>';

                    }

                }

            }

            $result[$product_id] = array('CAN_BUY' => $can_buy, 'QUANTITY' => $quantity, 'IN_STOCK_LABEL' => $in_stock_label);
        }

    }

}

echo json_encode($result);

?>