<?
define("STOP_STATISTICS", true);
define("ADMIN_SECTION",false);

ini_set('default_charset','utf-8');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$return = array('success' => false);

$currency = '';
$price = 0;

$product_id = (int)trim($_REQUEST['ID']);
$product_buy_id = (int)trim($_REQUEST['PRODUCT_BUY_ID']);
$product_buy_id = empty($product_buy_id) ? $product_id : $product_buy_id;

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

echo json_encode($return);