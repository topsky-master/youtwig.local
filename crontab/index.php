<?

//тип продукта;производитель;модель;товар;ком код;инд код;вид код;вид поз;вид изображение;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

//usleep(2500);

global $USER;

function checkIsSkipped($manValue = '',$typeValue = '') {

    static $skipTypeManufacturers,$skipType;

    $return = false;

    if(!is_array($skipTypeManufacturers)){

        $skipTypeManFile = dirname(dirname(__DIR__)).'/bitrix/tmp/skiptypeman.csv';
        $skipTypeManufacturers = array();
        $skipType = array();

        if(file_exists($skipTypeManFile)){
            $skipLines = file($skipTypeManFile);

            foreach($skipLines as $line){

                $lines = str_getcsv($line,";");
                $lines = array_map('trim',$lines);

                $types = array_slice($lines,1);


                if(!isset($skipTypeManufacturers[$lines[0]])){
                    $skipTypeManufacturers[$lines[0]] = array();
                }

                $types = array_filter($types);

                if(!empty($types)) {

                    if (!empty($lines[0])) {

                        $skipTypeManufacturers[$lines[0]] = array_unique(array_merge($skipTypeManufacturers[$lines[0]], $types));

                    } else {

                        foreach ($types as $type) {
                            $skipType[$type] = $type;
                        }

                    }
                }

            }

        }

    }

    if(isset($skipTypeManufacturers[$manValue])){

        if(empty($skipTypeManufacturers[$manValue])){

            $return = true;

        } else {

            foreach($skipTypeManufacturers[$manValue] as $type){

                if($type == $typeValue){
                    $return = true;
                    break;
                }

            };

        }

    }

    if(!$return && isset($skipType[$typeValue])){
        $return = true;
    }

    return $return;

}

$skipProdId = 0;

$arProdFilter = Array(
    "CODE" => "bez_tovara",
    "IBLOCK_ID" => 11
);

$arProdSelect = Array("ID");

$resProdDB = CIBlockElement::GetList(Array(), $arProdFilter, false, false, $arProdSelect);

$resProdArr = Array();

if($resProdDB) {
    $resProdArr = $resProdDB->GetNext();

    if(isset($resProdArr['ID'])
        && !empty($resProdArr['ID'])){

        $skipProdId = $resProdArr['ID'];
    }
}


$skipViewId = 0;

$arViewFilter = Array(
    "CODE" => "bez_vida",
    "IBLOCK_ID" => 34
);

$arViewSelect = Array("ID");

$resViewDB = CIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

$resViewArr = Array();

if($resViewDB) {
    $resViewArr = $resViewDB->GetNext();

    if(isset($resViewArr['ID'])
        && !empty($resViewArr['ID'])){

        $skipViewId = $resViewArr['ID'];
    }
}

$skipIndCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_ind_koda",
    "IBLOCK_ID" => 35
);

$arCodeSelect = Array("ID");

$resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipIndCodeId = $resCodeArr['ID'];

    }
}

$skipComCodeId = 0;

$arCodeFilter = Array(
    "CODE" => "bez_com_koda",
    "IBLOCK_ID" => 36
);

$arCodeSelect = Array("ID");

$resCodeDB = CIBlockElement::GetList(Array(), $arCodeFilter, false, false, $arCodeSelect);

$resCodeArr = Array();

if($resCodeDB) {
    $resCodeArr = $resCodeDB->GetNext();

    if(isset($resCodeArr['ID'])
        && !empty($resCodeArr['ID'])){

        $skipComCodeId = $resCodeArr['ID'];

    }
}

$file = isset($_REQUEST['file'])
&&!empty($_REQUEST['file'])
&&file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/'.urldecode(trim($_REQUEST['file'])))
    ? urldecode(trim($_REQUEST['file']))
    : 'models.csv';

$pfOpen = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/'.$file,'r');


$countStrings = 15;
$currentCount = 0;
$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? (int)$_REQUEST['skip'] : 0;

if(!empty($skip)){
    fseek($pfOpen,$skip);
}

function _remove_spaces($value){
    $value = preg_replace('~^[\s]+~is','',$value);
    $value = preg_replace('~[\s]+?$~is','',$value);
    return $value;
}

if(CModule::IncludeModule("iblock")){

    //IBLOCK_ID=17 model_new_link

    while($current = fgetcsv($pfOpen, 0 , ";")){

        $current = array_map('trim',$current);
        $current = array_map('_remove_spaces',$current);

        if(is_array($current)
            && !empty($current)
            && isset($current[0])
            && !empty($current[0])
            && isset($current[1])
            && !empty($current[1])
            && isset($current[2])
            && !empty($current[2])
            && !checkIsSkipped($current[1],$current[0])
        ){

            //тип продукта;производитель;

            $modelName = '';

            if(isset($current[0]) && !empty($current[0])){

                $modelName = trim($current[0]);

                $PROPERTY_CODE = 'type_of_product';

                $typeProperties = CIBlockProperty::GetList(
                    Array(
                        "sort"=>"asc",
                        "name"=>"asc"
                    ),
                    Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => 17,
                        "CODE" => $PROPERTY_CODE)
                );



                if($typeProperties){

                    while ($typeFields = $typeProperties->GetNext()){

                        $typePropertyID = $typeFields["ID"];
                        $enumTypeNew = new CIBlockPropertyEnum;

                        $typePropertyDB = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => 17, "VALUE" => $current[0], "CODE" => $PROPERTY_CODE));

                        if($typePropertyDB){
                            while($typePropertyFields = $typePropertyDB->GetNext()){

                                if(isset($typePropertyFields["ID"])){
                                    $typePropID = $typePropertyFields["ID"];
                                }

                            }
                        }


                        if(!$typePropID){
                            if($typePropID = $enumTypeNew->Add(
                                Array(
                                    'PROPERTY_ID' => $typePropertyID,
                                    'VALUE' => trim($current[0]))
                            )
                            ){



                            } else {

                                if(isset($enumTypeNew->LAST_ERROR)){
                                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'typePropID: '.$current[0].', '.$enumTypeNew->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                                }

                            }

                        }

                    }

                }

            }

            if(!empty($typePropID)){
                $modelProperties['type_of_product'] = Array("VALUE" => $typePropID);
            }

            $manufacturerPropID = false;

            if(isset($current[1]) && !empty($current[1])){

                $modelName .= (!empty($modelName) ? ' ': '') . trim($current[1]);

                $PROPERTY_CODE = 'manufacturer';

                $manufacturerProperties = CIBlockProperty::GetList(
                    Array(
                        "sort"=>"asc",
                        "name"=>"asc"
                    ),
                    Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => 17,
                        "CODE" => $PROPERTY_CODE)
                );



                if($manufacturerProperties){

                    while ($manufacturerFields = $manufacturerProperties->GetNext()){

                        $manufacturerPropertyID = $manufacturerFields["ID"];
                        $enummanufacturerNew = new CIBlockPropertyEnum;

                        $manufacturerPropertyDB = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=> 17, "VALUE" => $current[1], "CODE" => $PROPERTY_CODE));

                        if($manufacturerPropertyDB){
                            while($manufacturerPropertyFields = $manufacturerPropertyDB->GetNext()){

                                if(isset($manufacturerPropertyFields["ID"])){
                                    $manufacturerPropID = $manufacturerPropertyFields["ID"];
                                }

                            }
                        }


                        if(!$manufacturerPropID){
                            if($manufacturerPropID = $enummanufacturerNew->Add(
                                Array(
                                    'PROPERTY_ID' => $manufacturerPropertyID,
                                    'VALUE' => trim($current[1]))
                            )
                            ){



                            } else {

                                if(isset($enummanufacturerNew->LAST_ERROR)){
                                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt','enummanufacturerNew: '.$current[1].', '.$enummanufacturerNew->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                                }

                            }

                        }

                    }

                }

            }

            if(!empty($manufacturerPropID)){
                $modelProperties['manufacturer'] = Array("VALUE" => $manufacturerPropID);
            }

            $arModelSelect = Array("ID");
            $arModelFilter = Array(
                "IBLOCK_ID" => 27,
                "ACTIVE" => "Y",
                "=NAME" => trim($current[2])
            );

            $resModel = CIBlockElement::GetList(Array(), $arModelFilter, false, false, $arModelSelect);

            $modelPropIds = array();

            if($resModel){

                while($arModelFields = $resModel->GetNext()) {

                    if (isset($arModelFields["ID"])
                        && !empty($arModelFields["ID"])
                        && !in_array($arModelFields["ID"],$modelPropIds)) {

                        $modelPropIds[] = $arModelFields["ID"];

                    }

                }

                if(empty($modelPropIds)) {

                    $params = Array(
                        "max_len" => "100",
                        "change_case" => "L",
                        "replace_space" => "_",
                        "replace_other" => "_",
                        "delete_repeat_replace" => "true",
                    );

                    $arModelArray = Array(
                        "NAME" => trim($current[2]),
                        "ACTIVE" => "Y",
                        "CODE" => trim(CUtil::translit(trim($current[2]), LANGUAGE_ID, $params)),
                        "IBLOCK_ID" => 27,
                        "PREVIEW_TEXT" => " ",
                        "DETAIL_TEXT" => " ",
                    );

                    $modelEl = new CIBlockElement;

                    if ($modelNewPropId = $modelEl->Add($arModelArray)) {

                        $modelPropIds[] = $modelNewPropId;

                    } else {

                        if(isset($modelEl->LAST_ERROR)){
                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt','Model new name IB 27: '.trim($current[2]).', Code: '.trim(CUtil::translit(trim($current[2]), LANGUAGE_ID, $params)).', '.$modelEl->LAST_ERROR." - ". date('Y-m-d H:i:s')."\n",FILE_APPEND);
                        }
                    }
                };

                if (isset($current[2]) && !empty($current[2])) {

                    $modelName .= (!empty($modelName) ? ' ' : '') . trim($current[2]);

                }


                if(!empty($modelPropIds))
                    foreach($modelPropIds as $modelPropId) {

                        if ($modelPropId) {
                            $modelProperties['model_new_link'] = Array("VALUE" => $modelPropId);
                        }

                        if (!empty($modelName)
                            && !empty($typePropID)
                            && !empty($manufacturerPropID)
                            && !empty($modelPropId)) {


                            $arModelSelect = Array("ID");
                            $arModelFilter = Array(
                                "IBLOCK_ID" => 17,
                                //"=NAME" => trim($modelName),
                                "PROPERTY_type_of_product" => $typePropID,
                                "PROPERTY_manufacturer" => $manufacturerPropID,
                                "PROPERTY_model_new_link" => $modelPropId,
                                "ACTIVE" => "Y"
                            );

                            $resModel = CIBlockElement::GetList(Array(), $arModelFilter, false, false, $arModelSelect);

                            $foundModels = array();
                            $foundModel = false;

                            if ($resModel) {

                                while ($arFields = $resModel->GetNext()) {

                                    if (isset($arFields["ID"])
                                        && !empty($arFields["ID"])
                                    ) {

                                        $foundModels[$arFields["ID"]] = $arFields["ID"];

                                    }


                                }

                            }

                            $modelPropID = false;
                            $newModel = false;

                            if (empty($foundModels)) {

                                $params = Array(
                                    "max_len" => "100",
                                    "change_case" => "L",
                                    "replace_space" => "_",
                                    "replace_other" => "_",
                                    "delete_repeat_replace" => "true",
                                );

                                $arModelArray = Array(
                                    "NAME" => trim($modelName),
                                    "ACTIVE" => "Y",
                                    "CODE" => trim(CUtil::translit($modelName, LANGUAGE_ID, $params)),
                                    "PROPERTY_VALUES" => $modelProperties,
                                    "IBLOCK_ID" => 17,
                                    "PREVIEW_TEXT" => " ",
                                    "DETAIL_TEXT" => " ",
                                );


                                $modelEl = new CIBlockElement;


                                if ($foundModels[] = $modelEl->Add($arModelArray)) {

                                    $newModel = true;

                                } else {
                                    if (isset($modelEl->LAST_ERROR)) {
                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 17: ' . trim($modelName) . ',' . ', Code: ' . trim(CUtil::translit($modelName, LANGUAGE_ID, $params)) . ', ' . $modelEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                    }
                                }

                            }

                        }

                        if (!empty($foundModels)
                            && isset($current[3])
                            && !empty($current[3])) {

                            $current[3] = trim($current[3]);
                            $current[3] = (int)$current[3];

                            foreach($foundModels as $foundModel){

                                if ($current[3]) {

                                    $prodId = $current[3];

                                    $productsArray = array();

                                    $arModelProductsFilter = Array("CODE" => "products");

                                    $resProductsModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelProductsFilter);

                                    if ($resProductsModelDB) {

                                        while ($productsModelFields = $resProductsModelDB->GetNext()) {

                                            if (isset($productsModelFields['VALUE'])
                                                && !empty($productsModelFields['VALUE'])
                                            ) {

                                                $productsArray[] = $productsModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $viewsArray = array();

                                    $arModelViewsFilter = Array("CODE" => "VIEW");
                                    $resViewsModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelViewsFilter);

                                    if ($resViewsModelDB) {

                                        while ($viewsModelFields = $resViewsModelDB->GetNext()) {

                                            if (isset($viewsModelFields['VALUE'])
                                                && !empty($viewsModelFields['VALUE'])
                                            ) {

                                                $viewsArray[] = $viewsModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $indcodeArray = array();
                                    $arModelIndcodeFilter = Array("CODE" => "INDCODE");

                                    $resIndcodeModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelIndcodeFilter);

                                    if ($resIndcodeModelDB) {

                                        while ($indcodeModelFields = $resIndcodeModelDB->GetNext()) {

                                            if (isset($indcodeModelFields['VALUE'])
                                                && !empty($indcodeModelFields['VALUE'])
                                            ) {

                                                $indcodeArray[] = $indcodeModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $comcodeArray = array();

                                    $arModelComcodeFilter = Array("CODE" => "COMCODE");

                                    $resComcodeModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelComcodeFilter);

                                    if ($resComcodeModelDB) {

                                        while ($comcodeModelFields = $resComcodeModelDB->GetNext()) {

                                            if (isset($comcodeModelFields['VALUE'])
                                                && !empty($comcodeModelFields['VALUE'])
                                            ) {

                                                $comcodeArray[] = $comcodeModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $posArray = array();

                                    $arModelPosFilter = Array("CODE" => "POSITION");

                                    $resPosModelDB = CIBlockElement::GetProperty(17, $foundModel, array(), $arModelPosFilter);

                                    if ($resPosModelDB) {

                                        while ($posModelFields = $resPosModelDB->GetNext()) {

                                            if (isset($posModelFields['VALUE'])
                                                && !empty($posModelFields['VALUE'])) {

                                                $posArray[] = $posModelFields['VALUE'];

                                            }

                                        }

                                    }

                                    $max = array();
                                    $max[] = sizeof($indcodeArray);
                                    $max[] = sizeof($viewsArray);
                                    $max[] = sizeof($comcodeArray);
                                    $max[] = sizeof($productsArray);
                                    $max[] = sizeof($posArray);

                                    $sizeof = max($max);

                                    if (sizeof($indcodeArray) < $sizeof) {
                                        $indcodeArray = array_merge($indcodeArray, array_fill(sizeof($indcodeArray), ($sizeof - sizeof($indcodeArray)), $skipIndCodeId));
                                    }

                                    if (sizeof($viewsArray) < $sizeof) {
                                        $viewsArray = array_merge($viewsArray, array_fill(sizeof($viewsArray), ($sizeof - sizeof($viewsArray)), $skipViewId));
                                    }

                                    if (sizeof($comcodeArray) < $sizeof) {
                                        $comcodeArray = array_merge($comcodeArray, array_fill(sizeof($comcodeArray), ($sizeof - sizeof($comcodeArray)), $skipComCodeId));
                                    }

                                    if (sizeof($productsArray) < $sizeof) {
                                        $productsArray = array_merge($productsArray, array_fill(sizeof($productsArray), ($sizeof - sizeof($productsArray)), $skipProdId));
                                    }

                                    if (sizeof($posArray) < $sizeof) {
                                        $posArray = array_merge($posArray, array_fill(sizeof($posArray), ($sizeof - sizeof($posArray)), '-'));
                                    }

                                    $viewPosSt = '-';

                                    if (isset($current[7]) && !empty($current[7])) {
                                        $viewPosSt = trim($current[7]);
                                    }

                                    $viewId = false;

                                    if (!(isset($current[6]) && !empty($current[6]))
                                        && (isset($current[8]) && !empty($current[8]))) {

                                        $current[6] = pathinfo(dirname(dirname(__DIR__)).'/bitrix/tmp/' . $current[8], PATHINFO_FILENAME);

                                    }

                                    if (isset($current[6]) && !empty($current[6])) {

                                        $arViewSelect = Array("ID");
                                        $arViewFilter = Array(
                                            "IBLOCK_ID" => 34,
                                            "=NAME" => trim($current[6]),
                                            "PROPERTY_MODEL_LINK" => $foundModel
                                        );

                                        $resView = CIBlockElement::GetList(Array(), $arViewFilter, false, false, $arViewSelect);

                                        if ($resView) {

                                            while ($arFields = $resView->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $viewId = $arFields["ID"];

                                                }
                                            }
                                        }

                                        if (!empty($viewId)) {

                                            $arMViewSelect = Array("PROPERTY_MODEL_LINK");
                                            $arMViewFilter = Array(
                                                "IBLOCK_ID" => 34,
                                                "ID" => $viewId,
                                            );

                                            $resMView = CIBlockElement::GetList(Array(), $arMViewSelect, false, false, $arMViewFilter);

                                            $viewModelLink = array();

                                            if ($resMView) {

                                                while ($arMView = $resMView->GetNext()) {

                                                    if (isset($arMView['PROPERTY_MODEL_LINK_VALUE'])
                                                        && !empty($arMView['PROPERTY_MODEL_LINK_VALUE'])) {
                                                        $viewModelLink[] = array('VALUE' => $arMView['PROPERTY_MODEL_LINK_VALUE'], 'DESCRIPTION' => '');
                                                    }

                                                }

                                            }

                                            $viewModelLink[] = array('VALUE' => $foundModel, 'DESCRIPTION' => '');
                                            CIBlockElement::SetPropertyValuesEx($viewId, 34, ($viewModelPropArr = array('MODEL_LINK' => $viewModelLink)));


                                        }

                                    }

                                    if (empty($viewId)
                                        && isset($current[6]) && !empty($current[6])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arViewArray = Array(
                                            "NAME" => trim($current[6]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[6]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 34,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        $viewEl = new CIBlockElement;

                                        if ($viewId = $viewEl->Add($arViewArray)) {

                                            CIBlockElement::SetPropertyValuesEx($viewId, 34, ($viewModelPropArr = array('MODEL_LINK' => array(array('VALUE' => $foundModel, 'DESCRIPTION' => '')))));

                                        } else {

                                            if (isset($viewEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'View new name IB 34: ' . trim($current[6]) . ', Code: ' . trim(CUtil::translit(trim($current[6]), LANGUAGE_ID, $params)) . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }

                                    if ($viewId) {

                                        if (isset($current[8]) && !empty($current[8])) {

                                            $image = dirname(dirname(__DIR__)).'/bitrix/tmp/' . $current[8];

                                            if (file_exists($image)) {

                                                $viewProperties['PREVIEW_PICTURE'] = CFile::MakeFileArray($image);

                                                $viewEl = new CIBlockElement;

                                                if ($viewEl->Update($viewId, $viewProperties)) {

                                                } else {
                                                    if (isset($viewEl->LAST_ERROR)) {
                                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Model IB 34: ' . trim($image) . ',' . ', ' . $viewEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                                    }
                                                }

                                            }

                                        }

                                    }

                                    if (!$viewId) {
                                        $viewId = $skipViewId;
                                    }

                                    $comCodeId = false;

                                    if (isset($current[4]) && !empty($current[4])) {

                                        $arComCodeSelect = Array("ID");
                                        $arComCodeFilter = Array(
                                            "IBLOCK_ID" => 36,
                                            "=NAME" => trim($current[4])
                                        );

                                        $resComCode = CIBlockElement::GetList(Array(), $arComCodeFilter, false, false, $arComCodeSelect);

                                        if ($resComCode) {

                                            while ($arFields = $resComCode->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $comCodeId = $arFields["ID"];

                                                }
                                            }
                                        }

                                    }

                                    if (empty($comCodeId)
                                        && isset($current[4]) && !empty($current[4])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arComCodeArray = Array(
                                            "NAME" => trim($current[4]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[4]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 36,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        $comCodeEl = new CIBlockElement;

                                        if ($comCodeId = $comCodeEl->Add($arComCodeArray)) {

                                        } else {

                                            if (isset($comCodeEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'ComCode new name IB 36: ' . trim($current[4]) . ', Code: ' . trim(CUtil::translit(trim($current[4]), LANGUAGE_ID, $params)) . ', ' . $comCodeEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }

                                    if (!$comCodeId) {
                                        $comCodeId = $skipComCodeId;
                                    }

                                    $indCodeId = false;

                                    if (isset($current[5]) && !empty($current[5])) {

                                        $arIndCodeSelect = Array("ID");
                                        $arIndCodeFilter = Array(
                                            "IBLOCK_ID" => 35,
                                            "=NAME" => trim($current[5])
                                        );

                                        $resIndCode = CIBlockElement::GetList(Array(), $arIndCodeFilter, false, false, $arIndCodeSelect);

                                        if ($resIndCode) {

                                            while ($arFields = $resIndCode->GetNext()) {

                                                if (isset($arFields["ID"])
                                                    && !empty($arFields["ID"])) {

                                                    $indCodeId = $arFields["ID"];

                                                }
                                            }
                                        }

                                    }

                                    if (empty($indCodeId)
                                        && isset($current[5]) && !empty($current[5])) {

                                        $params = Array(
                                            "max_len" => "100",
                                            "change_case" => "L",
                                            "replace_space" => "_",
                                            "replace_other" => "_",
                                            "delete_repeat_replace" => "true",
                                        );

                                        $arIndCodeArray = Array(
                                            "NAME" => trim($current[5]),
                                            "ACTIVE" => "Y",
                                            "CODE" => trim(CUtil::translit(trim($current[5]), LANGUAGE_ID, $params)),
                                            "IBLOCK_ID" => 35,
                                            "PREVIEW_TEXT" => " ",
                                            "DETAIL_TEXT" => " ",
                                        );

                                        $indCodeEl = new CIBlockElement;

                                        if ($indCodeId = $indCodeEl->Add($arIndCodeArray)) {

                                        } else {

                                            if (isset($indCodeEl->LAST_ERROR)) {
                                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'IndCode new name IB 35: ' . trim($current[5]) . ', Code: ' . trim(CUtil::translit(trim($current[5]), LANGUAGE_ID, $params)) . ', ' . $indCodeEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                            }
                                        }
                                    }

                                    if (!$indCodeId) {
                                        $indCodeId = $skipIndCodeId;
                                    }

                                    $toBaseProducts = array();

                                    $hasString = false;

                                    foreach ($productsArray as $productNum => $productsId) {

                                        if ($productsId == $prodId
                                            && (isset($indcodeArray[$productNum])
                                                && $indcodeArray[$productNum] == $indCodeId
                                                && isset($viewsArray[$productNum])
                                                && $viewsArray[$productNum] == $viewId
                                                && isset($comcodeArray[$productNum])
                                                && $comcodeArray[$productNum] == $comCodeId
                                                && isset($posArray[$productNum])
                                                && $posArray[$productNum] == $viewPosSt)
                                        ) {

                                            $hasString = true;
                                            break;

                                        }

                                    }

                                    foreach ($productsArray as $value) {
                                        $toBaseProducts['products'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($posArray as $value) {
                                        $toBaseProducts['POSITION'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($indcodeArray as $value) {
                                        $toBaseProducts['INDCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($viewsArray as $value) {
                                        $toBaseProducts['VIEW'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }

                                    foreach ($comcodeArray as $value) {
                                        $toBaseProducts['COMCODE'][] = array('VALUE' => $value, 'DESCRIPTION' => '');
                                    }


                                    if (!$hasString) {

                                        $toBaseProducts['products'][] = array('VALUE' => $prodId, 'DESCRIPTION' => '');
                                        $toBaseProducts['INDCODE'][] = array('VALUE' => $indCodeId, 'DESCRIPTION' => '');
                                        $toBaseProducts['POSITION'][] = array('VALUE' => $viewPosSt, 'DESCRIPTION' => '');
                                        $toBaseProducts['VIEW'][] = array('VALUE' => $viewId, 'DESCRIPTION' => '');
                                        $toBaseProducts['COMCODE'][] = array('VALUE' => $comCodeId, 'DESCRIPTION' => '');

                                    }

                                    $toBaseProducts['PRODUCTS_REMOVED'] = 56422;

                                    CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
									//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);

                                    $modelEl = new CIBlockElement;

                                    if ($modelEl->Update($foundModel, Array('TIMESTAMP_X' => true))) {

                                    } else {

                                        if (isset($modelEl->LAST_ERROR)) {
                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/models_log.txt', 'Update model id IB 17 timestamp: ' . $foundModel . ', ' . $modelEl->LAST_ERROR . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                                        }

                                    }

                                }

                            }

                        }

                    }

            }

        }

        echo '.';

        ++$currentCount;

        if($countStrings <= $currentCount){

            $skip = ftell($pfOpen);
            fclose($pfOpen);

            die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/?intestwetrust=1&skip='.$skip.'&file='.urlencode($_REQUEST['file']).'&time='.time().'";},'.mt_rand(5,30).');</script></header></html>');

        }

    }


}

fclose($pfOpen);