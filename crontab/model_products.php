<?

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$models = array(
    array('МХМ 1841','МХМ-1841','МХМ-1841'),
    array('МХМ 1842','МХМ-1842','МХМ-1842'),
    array('МХМ 1843','МХМ-1843','МХМ-1843'),
    array('МХМ 1844','МХМ-1844','МХМ-1844'),
    array('МХМ 1845','МХМ-1845','МХМ-1845'),
    array('МХМ 1847','МХМ-1847','МХМ-1847'),
    array('МХМ 1848','МХМ-1848','МХМ-1848'),
);

$models = array(
    array('МХМ 1841' => 141007,'МХМ-1841' => 139447,'МХМ-1841 2' => 77962),
    array('МХМ 1842' => 141008,'МХМ-1842' => 139449,'МХМ-1842 2' => 57882),
    array('МХМ 1843' => 141009,'МХМ-1843' => 139451,'МХМ-1843 2' => 69812),
    array('МХМ 1844' => 141010,'МХМ-1844' => 139453,'МХМ-1844 2' => 94871),
    array('МХМ 1845' => 141005,'МХМ-1845' => 139455,'МХМ-1845 2' => 62577),
    array('МХМ 1847' => 141011,'МХМ-1847' => 139457,'МХМ-1847 2' => 98484),
    array('МХМ 1848' => 141006,'МХМ-1848' => 139459,'МХМ-1848 2' => 81545),
);

foreach($models as $model_array){

    $products = array();
    $modelsIds = array();

    foreach($model_array as $model_name => $model_id){

                $mlArFilter = array(
                                    'IBLOCK_ID' => 17,
                                    'PROPERTY_MODEL_NEW_LINK' => $model_id
                                );

                $mlArSelect = array('ID');

                $mlDBRes = CIBlockElement::GetList(Array(), $mlArFilter, false, false, $mlArSelect);

        // echo '<pre>';
        // var_dump("model_products => mlDBRes = ", $mlDBRes);
        // echo '</pre>';
                if($mlDBRes){

                    while($mlArr = $mlDBRes->getNext()){

                        echo $mlArr['ID']." - model id\n";

                        if(!in_array($mlArr['ID'],$modelsIds)){

                            $modelsIds[] = $mlArr['ID'];

                            $dbMPropResult = CIBlockElement::GetProperty(
                                17,
                                $mlArr['ID'],
                                Array(),
                                Array(
                                    "CODE" => "products"
                                )
                            );

                            if($dbMPropResult){

            // echo '<pre>';
            // var_dump("model_products => dbPropResult =", $dbMPropResult);
            // echo '</pre>';
                                while($arProdProp = $dbMPropResult->GetNext()){


                                    if(!in_array($arProdProp['VALUE'],$products)){

                                        $products[] = (string)$arProdProp['VALUE'];
                                    }

                                };

                            };

                        }

                    };

                }


    }

    exit(0);

    foreach($modelsIds as $fmodel_id){

        CIBlockElement::SetPropertyValuesEx($fmodel_id, 17, array('products' => $products));
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $fmodel_id);
    };

}
