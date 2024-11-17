<?  

die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule('iblock');

$count = isset($_REQUEST['count']) ? (int)$_REQUEST['count'] : 0;
$max = 250;

$models = file(dirname(dirname(__DIR__)).'/bitrix/tmp/models.csv');
$ciblockelement = new CIBlockElement;

if($count == 0)
unset($models[0]);

$models = array_slice($models,$count,$max);

$ibpenum = new CIBlockPropertyEnum;

$found = sizeof($models) > 0 ? true : false;

foreach ($models as $model){



    $modelsAarr = str_getcsv($model, ';');

    //Тип товара - список type_of_product
    //Производитель - список /iblock_list_admin.php?IBLOCK_ID=17&type=catalog&lang=ru&find_section_section=0 manufacturer
    //Новая модель - линковка на инфоблок iblock_edit.php?type=catalog&lang=ru&ID=17&admin=Y model_new_link
    //Товары - линковка products множественное

    $modelsAarr = array_map('trim',$modelsAarr);

    $typeOfProductId = 0;
    $typeOfProduct = $modelsAarr[0];

    $typeOfProductDB = CIBlockPropertyEnum::GetList(
        Array(
            "SORT" => "ASC"
        ),
        Array(
            "IBLOCK_ID" => 17,
            "CODE" => "type_of_product",
            "VALUE" => $typeOfProduct
        )
    );


    if($typeOfProductDB){
        while($typeOfProductArr = $typeOfProductDB->GetNext()){
            $typeOfProductId = $typeOfProductArr['ID'];
        }
    }

    if(empty($typeOfProductId)) {

        $typeOfProductPropDB = CIBlockProperty::GetList(
            Array(
                "sort" => "asc",
                "name" => "asc"
            ),
            Array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => 17,
                "CODE" => 'type_of_product'
            )
        );


        while ($typeOfProductPropArr = $typeOfProductPropDB->GetNext()) {
            $typeOfProductPropID = $typeOfProductPropArr["ID"];
        }

        $typeOfProductId = $ibpenum->Add(
            Array(
                'PROPERTY_ID' => $typeOfProductPropID,
                'VALUE' => $typeOfProduct
            )
        );

    }

    $manufacturerId = 0;
    $manufacturer = $modelsAarr[1];

    $manufacturerDB = CIBlockPropertyEnum::GetList(
        Array(
            "SORT" => "ASC"
        ),
        Array(
            "IBLOCK_ID" => 17,
            "CODE" => "manufacturer",
            "VALUE" => $manufacturer
        )
    );


    if($manufacturerDB){
        while($manufacturerArr = $manufacturerDB->GetNext()){
            $manufacturerId = $manufacturerArr['ID'];
        }
    }

    if(empty($manufacturerId)){

        $manufacturerPropDB  = CIBlockProperty::GetList(
            Array (
                "sort" => "asc",
                "name" => "asc"
            ),
            Array (
                "ACTIVE" => "Y",
                "IBLOCK_ID" => 17,
                "CODE" => 'manufacturer'
            )
        );


        while($manufacturerPropArr  =  $manufacturerPropDB->GetNext()){
            $manufacturerPropID = $manufacturerPropArr["ID"];
        }

        $manufacturerId = $ibpenum->Add(
            Array(
                'PROPERTY_ID' => $manufacturerPropID,
                'VALUE' => $manufacturer
            )
        );

    }

    //$modelsAarr[0] + $modelsAarr[1] + $modelsAarr[2] Тип продукта + Производитель + Модель

    $model_name = $modelsAarr[0] .' '. $modelsAarr[1] .' '. $modelsAarr[2];

    $modelsDB = CIBlockElement::GetList(
        array(),
        array(
            'NAME' => $modelsAarr[2],
            'IBLOCK_ID' => 27),
        false,
        false,
        array("ID")
    );

    $modelId = 0;

    if($modelsDB){
        while($modelsArr = $modelsDB->GetNext()) {
            $modelId = $modelsArr['ID'];
        }
    }

    if(empty($modelId)){

        $ciblockelementArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID"      => 27,
            "NAME"           => $modelsAarr[2],
            "ACTIVE"         => "Y",            // активен
            "PREVIEW_TEXT"   => " ",
            "DETAIL_TEXT"    => " ",
        );

        $modelId = $ciblockelement->Add($ciblockelementArray);
    }

    $modelsDB = CIBlockElement::GetList(
        array(),
        array(
            'NAME' => $model_name,
            'IBLOCK_ID' => 17),
        false,
        false,
        array("ID")
    );

    $modelNewLinkId = $modelId;

    $modelId = 0;

    if($modelsDB){
        while($modelsArr = $modelsDB->GetNext()) {
            $modelId = $modelsArr['ID'];
        }
    }

    if(empty($modelId)){

        $trParams = Array(
            "max_len" => "200",
            "change_case" => "L",
            "replace_space" => "_",
            "replace_other" => "_",
            "delete_repeat_replace" => "true",
        );

        $model_name_code = ' '.CUtil::translit($model_name, LANGUAGE_ID, $trParams);


        $ciblockelementArray = Array(
            "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_ID" => 17,
            "NAME" => $model_name,
            "ACTIVE" => "Y",            // активен
            "PREVIEW_TEXT" => " ",
            "DETAIL_TEXT" => " ",
            "CODE" => $model_name_code
        );

        $modelId = $ciblockelement->Add($ciblockelementArray);
    }



    CIBlockElement::SetPropertyValuesEx($modelId,17,
        array(
            'type_of_product' => $typeOfProductId,
            'manufacturer' => $manufacturerId,
            'model_new_link' => $modelNewLinkId,
            'products' => $modelsAarr[3]
        )
    );

	//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $modelId);



}


if($found){
    $count += $max;
    echo $count.'<br />';
    LocalRedirect('/test.php?count='.$count);
}

?>