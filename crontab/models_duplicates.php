<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelRemapModels{

    private static $countStrings = 200;
    private static $similarManufacturers = array(
        'Hotpoint' => 26480,
        'Ariston' => 3075,
        'Hotpoint-Ariston' => 3077,
        'Indesit' => 3079);

    private static function gatherData(){

        $modelNameIds = array();

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt'));

        $arModelNameSelect = Array(
            "ID",
            "NAME"
        );

        $arModelNameFilter = Array(
            "IBLOCK_ID" => 27
        );

        if($skip > 0){
            $arModelNameFilter["<ID"] = $skip;
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.txt', "");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt',0);
        }

        $resModelName = CIBlockElement::GetList(
            ($order = Array('ID' => 'DESC')),
            $arModelNameFilter,
            false,
            ($pager = Array('nTopCount' => static::$countStrings)),
            $arModelNameSelect
        );

        if($resModelName){

            while($arModelNameFields = $resModelName->GetNext()){

                if(!isset($modelNameIds[$arModelNameFields['ID']])){
                    $modelNameIds[$arModelNameFields['ID']] = $arModelNameFields['NAME'];
                }
            }
        }

        $sizeof = sizeof($modelNameIds);

        $sSizeof = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt');
        $sSizeof = (int)trim($sSizeof);

        $sSizeof += $sizeof;
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt',$sSizeof);

        return $modelNameIds;

    }

    public static function getModelName($modelSearchName){

        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(str_ireplace('(','',$modelSearchName));
        $modelSearchName = trim(str_ireplace(')','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s+~isu','',$modelSearchName));

        return $modelSearchName;
    }

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelNameIds = static::gatherData();

        $modelLastPropId = 0;

        $modelLastPropId = static::checkFamiliarModels($modelNameIds);

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels($modelNameIds){

        $similarManufacturers = array_values(static::$similarManufacturers);

        $modelLastPropId = 0;

        foreach($modelNameIds as $modelPropNameId => $modelPropName){

            $arModelNameSelect = Array("ID","NAME");
            $arModelNameFilter = Array(
                "IBLOCK_ID" => 27,
                "NAME" => $modelPropName.'%'
            );

            $resModelName = CIBlockElement::GetList(
                ($order = Array("NAME" => "ASC", "CREATED" => "DESC")),
                $arModelNameFilter,
                false,
                false,
                $arModelNameSelect
            );

            $modelEl = new CIBlockElement;
            $modelLastPropId = $modelPropNameId;

            if($resModelName) {

                $getFirst = false;
                $canonicalURLs = array();

                $checkModels = array();

                $equalModels = array();
                $equalModelNames = array();
                $propertyProducts = array();

                $foundModel = 0;
                $instructionId = 0;
                $previewPictureId = 0;
                $detailPictureId = 0;
                $firstURL = '';

                $modelName = '';

                while($arModelNameFields = $resModelName->GetNext()) {

                    $modelPropId = $arModelNameFields['ID'];

                    if(($arModelNameFields['NAME'] == $modelPropName)
                        || (static::getModelName($arModelNameFields['NAME']) == static::getModelName($modelPropName))){

                        if($arModelNameFields['NAME'] != $modelPropName){
                            echo $modelPropName.'-similar-';
                            echo $arModelNameFields['NAME'].'-<br />';

                        } else if($arModelNameFields['NAME'] == $modelPropName) {
                            echo $modelPropName.'-equal-';
                            echo $arModelNameFields['NAME'].'-<br />';

                        }

                        $arModelSelect = Array(
                            "ID",
                            "PROPERTY_products",
                            "PROPERTY_instruction",
                            "PREVIEW_PICTURE",
                            "DETAIL_PICTURE",
                            "PROPERTY_type_of_product",
                            "PROPERTY_manufacturer",
                            "DETAIL_PAGE_URL"
                        );

                        $arModelFilter = Array(
                            "IBLOCK_ID" => 17,
                            "PROPERTY_model_new_link" => $modelPropId,
                            "ACTIVE" => "Y",
                        );

                        $resModel = CIBlockElement::GetList(
                            ($order = Array(
                                'PROPERTY_manufacturer' => 'asc',
                                'created' => 'desc'
                            )),
                            $arModelFilter,
                            false,
                            false,
                            $arModelSelect
                        );

                        if($resModel){

                            while($arModel = $resModel->GetNext()) {

                                if(in_array($arModel['ID'],$equalModels))
                                    continue;

                                if((//$arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'] == 'Кухонный комбайн' &&
                                    in_array(
                                        $arModel['PROPERTY_MANUFACTURER_VALUE'],
                                        array(
                                            'Tefal',
                                            'Krups',
                                            'Moulinex',
                                            'Braun'
                                        )
                                    )
                                    && ($arModelNameFields['NAME'] != $modelPropName)
                                ) //|| ($arModel['PROPERTY_MANUFACTURER_VALUE'] == 'Braun')
                                ) {

                                    continue;
                                }

                                if(in_array($arModel['PROPERTY_MANUFACTURER_ENUM_ID'],$similarManufacturers)){

                                    if(isset($arModel['DETAIL_PAGE_URL'])
                                        && !empty($arModel['DETAIL_PAGE_URL']))
                                        $canonicalURLs[] = $arModel['DETAIL_PAGE_URL'];

                                    if(!$getFirst){

                                        if(isset($arModel['DETAIL_PAGE_URL'])
                                            && !empty($arModel['DETAIL_PAGE_URL']))
                                            $firstURL = $arModel['DETAIL_PAGE_URL'];


                                        if(isset($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                            && !empty($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){

                                            $modelName .= $arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'];

                                        }

                                        if(isset($arModel['PROPERTY_MANUFACTURER_VALUE'])
                                            && !empty($arModel['PROPERTY_MANUFACTURER_VALUE'])){

                                            $modelName .= ' '.$arModel['PROPERTY_MANUFACTURER_VALUE'];

                                        }

                                        $foundModel = $arModel['ID'];

                                    }

                                    $equalModels[] = $arModel['ID'];
                                    $equalModelNames[] = $arModelNameFields['NAME'];

                                    if(isset($arModel['PROPERTY_INSTRUCTION_VALUE'])
                                        && !empty($arModel['PROPERTY_INSTRUCTION_VALUE'])
                                        && empty($instructionId)){

                                        $instructionId = $arModel['PROPERTY_INSTRUCTION_VALUE'];

                                    }

                                    if(isset($arModel['PREVIEW_PICTURE'])
                                        && !empty($arModel['PREVIEW_PICTURE'])
                                        && empty($previewPictureId)){

                                        $previewPictureId = $arModel['PREVIEW_PICTURE'];

                                    }

                                    if(isset($arModel['DETAIL_PICTURE'])
                                        && !empty($arModel['DETAIL_PICTURE'])
                                        && empty($detailPictureId)){

                                        $detailPictureId = $arModel['DETAIL_PICTURE'];

                                    }

                                    $productPropsDB = CIBlockElement::GetProperty(
                                        17,
                                        $arModel["ID"],
                                        array("sort" => "asc"),
                                        Array("CODE" => "products")
                                    );

                                    if($productPropsDB){
                                        while($productPropsAr = $productPropsDB->GetNext()){

                                            if(isset($productPropsAr['VALUE'])
                                                && !empty($productPropsAr['VALUE'])
                                                && !in_array($productPropsAr['VALUE'],$propertyProducts)
                                            ){
                                                $propertyProducts[] = $productPropsAr['VALUE'];
                                            }

                                        }

                                    }

                                    if(!$getFirst && !empty($modelName)){
                                        $modelName = $modelName.' '.$modelNameIds[$modelPropId];
                                    }

                                    $getFirst = true;

                                } else {

                                    if(!is_array($checkModels[$arModel['PROPERTY_MANUFACTURER_ENUM_ID']])){
                                        $checkModels[$arModel['PROPERTY_MANUFACTURER_ENUM_ID']] = array();
                                    }

                                    $arModel['MODEL_NAME'] = $arModelNameFields['NAME'];
                                    $checkModels[$arModel['PROPERTY_MANUFACTURER_ENUM_ID']][] = $arModel;
                                }

                            }

                        }

                    }

                }

                if(sizeof($equalModels) > 1){

                    $currentModel = array(
                        'TIMESTAMP_X' => true
                    );

                    if(!empty($modelName)){
                        $currentModel['NAME'] = $modelName;
                    }

                    if(!empty($previewPictureId)){
                        $currentModel['PREVIEW_PICTURE'] = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($previewPictureId));
                    }

                    if(!empty($detailPictureId)){
                        $currentModel['DETAIL_PICTURE'] = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($detailPictureId));
                    }

                    $toInstruction = array();

                    if(!empty($instructionId)){
                        $toInstruction['instruction'] = array('VALUE' => CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($instructionId)), 'DESCRIPTION' => '');
                    }

                    if(!empty($toInstruction)){
                        //CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toInstruction);
                    }

                    $toBaseProducts = array();

                    foreach($propertyProducts as $productsId){

                        $toBaseProducts['products'][] = array('VALUE' => $productsId, 'DESCRIPTION' => '');

                    }

                    if(!empty($toBaseProducts)){
                        //CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
                        //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);
                    }


                    /* if ($modelEl->Update($foundModel, $currentModel)) {

                    } else {

                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_error.txt',$foundModel.' id - '.$modelEl->LAST_ERROR."\n",FILE_APPEND);

                    } */

                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.txt',join(" - ",$equalModels)."\n",FILE_APPEND);
                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.txt',join(" - ",$equalModelNames)."\n",FILE_APPEND);

                    array_shift($equalModels);
                    array_shift($canonicalURLs);

                    /* foreach($equalModels as $fNumber => $foundModel) {

                        static::addRedirect($canonicalURLs[$fNumber],$firstURL);

                        if ($modelEl->Update($foundModel, array('ACTIVE' => 'N', 'TIMESTAMP_X' => true))) {

                        } else {

                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_error.txt',$foundModel.' id - '.$modelEl->LAST_ERROR."\n",FILE_APPEND);

                        }

                    } */

                }


                if(!empty($checkModels)){

                    foreach($checkModels as $manEnumId => $arModels){

                        if(sizeof($arModels) > 1){

                            $getFirst = false;
                            $canonicalURLs = array();

                            $checkModels = array();

                            $equalModels = array();
                            $equalModelNames = array();
                            $propertyProducts = array();

                            $foundModel = 0;
                            $instructionId = 0;
                            $previewPictureId = 0;
                            $detailPictureId = 0;
                            $firstURL = '';

                            $modelName = '';

                            foreach($arModels as $arModel){

                                if(in_array($arModel['ID'],$equalModels))
                                    continue;

                                if((//$arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'] == 'Кухонный комбайн' &&
                                    in_array(
                                        $arModel['PROPERTY_MANUFACTURER_VALUE'],
                                        array(
                                            'Tefal',
                                            'Krups',
                                            'Moulinex',
                                            'Braun'
                                        )
                                    )) && $arModel['MODEL_NAME'] != $modelPropName //|| ($arModel['PROPERTY_MANUFACTURER_VALUE'] == 'Braun')
                                ) {

                                    continue;
                                }

                                if(isset($arModel['DETAIL_PAGE_URL'])
                                    && !empty($arModel['DETAIL_PAGE_URL']))
                                    $canonicalURLs[] = $arModel['DETAIL_PAGE_URL'];

                                if(!$getFirst){

                                    if(isset($arModel['DETAIL_PAGE_URL'])
                                        && !empty($arModel['DETAIL_PAGE_URL']))
                                        $firstURL = $arModel['DETAIL_PAGE_URL'];


                                    if(isset($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                        && !empty($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){

                                        $modelName .= $arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'];

                                    }

                                    if(isset($arModel['PROPERTY_MANUFACTURER_VALUE'])
                                        && !empty($arModel['PROPERTY_MANUFACTURER_VALUE'])){

                                        $modelName .= ' '.$arModel['PROPERTY_MANUFACTURER_VALUE'];

                                    }

                                    $foundModel = $arModel['ID'];

                                }

                                $equalModels[] = $arModel['ID'];
                                $equalModelNames[] = $arModel['MODEL_NAME'];

                                if(isset($arModel['PROPERTY_INSTRUCTION_VALUE'])
                                    && !empty($arModel['PROPERTY_INSTRUCTION_VALUE'])
                                    && empty($instructionId)){

                                    $instructionId = $arModel['PROPERTY_INSTRUCTION_VALUE'];

                                }

                                if(isset($arModel['PREVIEW_PICTURE'])
                                    && !empty($arModel['PREVIEW_PICTURE'])
                                    && empty($previewPictureId)){

                                    $previewPictureId = $arModel['PREVIEW_PICTURE'];

                                }

                                if(isset($arModel['DETAIL_PICTURE'])
                                    && !empty($arModel['DETAIL_PICTURE'])
                                    && empty($detailPictureId)){

                                    $detailPictureId = $arModel['DETAIL_PICTURE'];

                                }

                                $productPropsDB = CIBlockElement::GetProperty(
                                    17,
                                    $arModel["ID"],
                                    array("sort" => "asc"),
                                    Array("CODE" => "products")
                                );

                                if($productPropsDB){
                                    while($productPropsAr = $productPropsDB->GetNext()){

                                        if(isset($productPropsAr['VALUE'])
                                            && !empty($productPropsAr['VALUE'])
                                            && !in_array($productPropsAr['VALUE'],$propertyProducts)
                                        ){
                                            $propertyProducts[] = $productPropsAr['VALUE'];
                                        }

                                    }

                                }

                                if(!$getFirst && !empty($modelName)){
                                    $modelName = $modelName.' '.$arModel['MODEL_NAME'];
                                }

                                $getFirst = true;

                            }

                            if(sizeof($equalModels) > 1){

                                $currentModel = array(
                                    'TIMESTAMP_X' => true
                                );

                                if(!empty($modelName)){
                                    $currentModel['NAME'] = $modelName;
                                }

                                if(!empty($previewPictureId)){
                                    $currentModel['PREVIEW_PICTURE'] = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($previewPictureId));
                                }

                                if(!empty($detailPictureId)){
                                    $currentModel['DETAIL_PICTURE'] = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($detailPictureId));
                                }

                                $toInstruction = array();

                                if(!empty($instructionId)){
                                    $toInstruction['instruction'] = array('VALUE' => CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/'.CFile::getPath($instructionId)), 'DESCRIPTION' => '');
                                }

                                if(!empty($toInstruction)){
                                    //CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toInstruction);
                                }

                                $toBaseProducts = array();

                                foreach($propertyProducts as $productsId){

                                    $toBaseProducts['products'][] = array('VALUE' => $productsId, 'DESCRIPTION' => '');

                                }

                                if(!empty($toBaseProducts)){
                                    //CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
                                    //\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);
                                }


                                /*if ($modelEl->Update($foundModel, $currentModel)) {

                                } else {

                                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_error.txt',$foundModel.' id - '.$modelEl->LAST_ERROR."\n",FILE_APPEND);

                                } */

                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.txt',join(" - ",$equalModels)."\n",FILE_APPEND);
                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.txt',join(" - ",$equalModelNames)."\n",FILE_APPEND);

                                array_shift($equalModels);
                                array_shift($canonicalURLs);

                                /* foreach($equalModels as $fNumber => $foundModel) {

                                    static::addRedirect($canonicalURLs[$fNumber],$firstURL);

                                    if ($modelEl->Update($foundModel, array('ACTIVE' => 'N', 'TIMESTAMP_X' => true))) {

                                    } else {

                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_error.txt',$foundModel.' id - '.$modelEl->LAST_ERROR."\n",FILE_APPEND);

                                    }

                                } */

                            }

                        }

                    }

                }

            }

        }

        return $modelLastPropId;

    }

    private static function addRedirect($what,$where){

        $what = trim($what);
        $what = preg_replace('~http(s*?)://[^/]+?/~isu','',$what);
        $what = rtrim($what,'/');

        $where = trim($where);
        $where = preg_replace('~http(s*?)://[^/]+~isu','',$where);
        $where = empty($where) ? "/" : $where;

        $show = false;
        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        $rsData = CBXShortUri::GetList(
            Array(),
            Array(
                "URI" => '/'.trim($what,'/').'/',
                "SHORT_URI" => trim($where,'/')
            )
        );

        while($arRes = $rsData->Fetch()) {
            $show = true;
            break;
        }

        if (!$show
            &&
            (
                (trim(mb_strtolower($where),'/') != trim(mb_strtolower($what),'/'))
                || (trim(($where),'/') != trim(($what),'/'))
            )
        ){

            $arShortFields = Array(
                "URI" => '/'.trim($where,'/').'/',
                "SHORT_URI" => trim($what,'/'),
                "STATUS" => "301",
            );

            CBXShortUri::Add($arShortFields);

        }

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_duplicates.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelRemapModels::checkModels();