<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


global $USER;

$chained_products                               = array();

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")){

    $arSelect 									= Array("ID", "NAME", "PROPERTY_MAIN_PRODUCTS");
    $arFilter 									= Array("IBLOCK_ID" => 11, "!PROPERTY_MAIN_PRODUCTS" => false);
    $res 										= CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $product									= array();


    $quantity									= 0;

    while($res
        && ($product						= $res->Fetch())){

        if(isset($product['PROPERTY_MAIN_PRODUCTS_VALUE'])
            && !empty($product['PROPERTY_MAIN_PRODUCTS_VALUE'])){
            if(!isset($chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']])){
                $product[$chained_products['PROPERTY_MAIN_PRODUCTS_VALUE']] = array();
            }

            if(!in_array($product['NAME'],$chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']])){
                $chained_products[$product['PROPERTY_MAIN_PRODUCTS_VALUE']][] = $product['NAME'];
            }
        }

    }

    if(!empty($chained_products)){
        foreach($chained_products as $product_id=>$product_names){

            $product_names                      = join("\n",$product_names);
            $PROPERTY_CODE 				        = "CHAINED_PRODUCTS";
            $PROPERTY_VALUE 			        = $product_names;

            CIBlockElement::SetPropertyValues($product_id, 16, $PROPERTY_VALUE, $PROPERTY_CODE);
			//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(16, $product_id);

        }
    }


}



