<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $delivery_disable;
$delivery_disable	=  false;

//foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepDelivery", true) as $arEvent){
//ExecuteModuleEventEx($arEvent, array(&$arResult, &$arResult['USER_VALS'], &$arParams));
//}

if(is_array($arResult['DELIVERY']) && !empty($arResult['DELIVERY'])){

    foreach ($arResult['DELIVERY'] as $key	=>$value){
        if(isset($value['NAME'])
            && !empty($value['NAME'])
            && $value['NAME'] 				== "Купить в один клик"
            || $value['NAME'] 				== "Без доставки"
            || $value['NAME'] 				== "Предзаказ"
        ){
            unset($arResult['DELIVERY'][$key]);

        };
    };

};
