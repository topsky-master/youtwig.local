<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelClearModelsNames{

    private static $countStrings = 200;

    private static function gatherData(){

        $modelNameIds = array();
        $modelEl = new CIBlockElement;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log_last.txt'));

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
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log.txt', "");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_get.txt',0);
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

                if(!isset($modelNameIds[$arModelNameFields['ID']])) {

                    $compareModelName = static::getModelName($arModelNameFields['NAME']);
                    $modelNameIds[$arModelNameFields['ID']] = "";

                    if(mb_strtolower(trim($arModelNameFields['NAME'])) != mb_strtolower($compareModelName)){

                        $modelNameIds[$arModelNameFields['ID']] = $arModelNameFields['NAME'];

                    }
                }
            }
        }

        $sizeof = sizeof($modelNameIds);

        $sSizeof = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_get.txt');
        $sSizeof = (int)trim($sSizeof);

        $sSizeof += $sizeof;
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_get.txt',$sSizeof);

        return $modelNameIds;

    }

    public static function getModelName($modelSearchName){


        $originalName = '';

        if(preg_match('~\S+?\([\d\s]+\)\s*?$~isu',$modelSearchName)){
            $originalName = $modelSearchName;
        }

        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(str_ireplace('(','',$modelSearchName));
        $modelSearchName = trim(str_ireplace(')','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s+~isu','',$modelSearchName));

        if($originalName != ""){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whithout_spaces_model_names_log.txt','original: '.$originalName.', rename: '.$modelSearchName."\n",FILE_APPEND);
        }

        return $modelSearchName;

    }

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelNameIds = static::gatherData();

        $modelLastPropId = 0;

        $modelLastPropId = static::checkFamiliarModels($modelNameIds);

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels($modelNameIds) {

        $modelLastPropId = 0;
        $modelEl = new CIBlockElement;

        foreach($modelNameIds as $modelPropNameId => $modelPropName) {

            $isFirst = false;
            $modelLastPropId = $modelPropNameId;

            if(empty($modelPropName))
                continue;

            $equalModels = array();

            $arModelSelect = Array(
                "ID",
                "NAME",
                "PROPERTY_type_of_product",
                "PROPERTY_manufacturer",
            );

            $arModelFilter = Array(
                "IBLOCK_ID" => 17,
                "PROPERTY_model_new_link" => $modelPropNameId
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
                        )) //|| ($arModel['PROPERTY_MANUFACTURER_VALUE'] == 'Braun')
                    ) {

                        continue;
                    }

                    $compareModelName = static::getModelName($modelPropName);


                    if (!$isFirst
                        && $modelEl->Update($modelPropNameId,
                            ($currentModel = array('NAME' => $compareModelName))
                        )
                    ) {

                        $isFirst = true;
                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log.txt',$modelPropName.' - '.$compareModelName.' - '.$modelPropNameId." - 27\n",FILE_APPEND);

                    }

                    $modelPropName = $compareModelName;

                    $modelName = '';

                    if(isset($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                        && !empty($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){

                        $modelName .= $arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'];

                    }

                    if(isset($arModel['PROPERTY_MANUFACTURER_VALUE'])
                        && !empty($arModel['PROPERTY_MANUFACTURER_VALUE'])){

                        $modelName .= ' '.$arModel['PROPERTY_MANUFACTURER_VALUE'];

                    }

                    $equalModels[] = $arModel['ID'];

                    if(!empty($modelName)){

                        $modelNameNew = $modelName.' '.trim($modelPropName);
                        $modelNameOld = $arModel['NAME'];

                        if($modelNameNew != $modelNameOld){

                            if($modelEl->Update($arModel['ID'],
                                ($currentModel = array('NAME' => $modelNameNew)))){

                                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log.txt',$arModel['NAME'].' - '.$modelNameNew.' - '.$arModel['ID']." - 17\n",FILE_APPEND);

                            }

                        }

                    }

                }

            }

        }

        return $modelLastPropId;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].'/local/crontab/clear_models_names.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/rename_log_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelClearModelsNames::checkModels();