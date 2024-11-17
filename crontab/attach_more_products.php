<?

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$lArSelect = array('ID','PROPERTY_products');
$lArFilter = array(
    'IBLOCK_ID' => 17,
    'PROPERTY_products' => array(11582)
);

$lDBRes = CIBlockElement::GetList(Array(), $lArFilter, false, false, $lArSelect);

if($lDBRes){

    while($arFields = $lDBRes->GetNext()){

        $productPropsDB = CIBlockElement::GetProperty(
            17,
            $arFields["ID"],
            array("sort" => "asc"),
            Array("CODE"=>"products")
        );

        $propertyProducts = array();

        if($productPropsDB){
            while($productPropsAr = $productPropsDB->fetch()){

                if(isset($productPropsAr['VALUE'])
                    && !empty($productPropsAr['VALUE'])){
                    $propertyProducts[] = $productPropsAr['VALUE'];
                }
            }
        }



        //if(sizeof(array_intersect(array(10702,10701),$propertyProducts)) == 2){


            $propertyProducts = array_merge($propertyProducts,array(4421));
            $propertyProducts = array_unique($propertyProducts);
            CIBlockElement::SetPropertyValuesEx($arFields['ID'], 17, array('products' => $propertyProducts));
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arFields['ID']);
        //}

        //CIBlockElement::SetPropertyValuesEx($arFields['ID'], 11, array('SEO_TEXT' => array('TEXT' => '')));

    }

}
