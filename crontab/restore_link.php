<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelRestoreLinkModels{

    private static $countStrings = 200;
    private static $getModels = array();
    private static $countDups = 0;

    public static function checkModels($iblock_id = 27){

        global $USER;

        $modelLastPropId = static::checkFamiliarModels($iblock_id);

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels($iblock_id){

        $elUpdate = new CIBlockElement;

        $modelLastPropId = 0;

        $params = Array(
            "max_len" => "100",
            "change_case" => "L",
            "replace_space" => "_",
            "replace_other" => "_",
            "delete_repeat_replace" => "true",
        );

        $getModels = array();
        $countDups = 0;

        $skip = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_log_last.txt');

        if(empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_list.txt', "");

        } else {

            $countDups = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_last.txt');

        }

        ++$skip;

        $arModelSelect = Array("ID","NAME","PROPERTY_type_of_product","PROPERTY_manufacturer");

        $arModelFilter = Array(
            "IBLOCK_ID" => 17,
            "PROPERTY_model_new_link" => false,
            "ACTIVE" => "Y"
        );

        $arModelNav = Array(
            "nTopCount" => 500
        );

        $resModel = CIBlockElement::GetList(Array(), $arModelFilter, false, $arModelNav, $arModelSelect);

        while($arModels = $resModel->GetNext()){

            $modelLastPropId = $arModels['ID'];

            if(isset($arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                && isset($arModels['PROPERTY_MANUFACTURER_VALUE'])){

                $PROPERTY_TYPE_OF_PRODUCT_VALUE = $arModels['PROPERTY_TYPE_OF_PRODUCT_VALUE'];
                $PROPERTY_MANUFACTURER_VALUE = $arModels['PROPERTY_MANUFACTURER_VALUE'];

                $modelLastName = str_ireplace($PROPERTY_TYPE_OF_PRODUCT_VALUE,'',$arModels["NAME"]);
                $modelLastName = str_ireplace($PROPERTY_MANUFACTURER_VALUE,'',$modelLastName);
                $modelLastName = trim($modelLastName);

                if(!empty($modelLastName)){

                    $arNModelSelect = Array("ID","NAME");

                    $arNModelFilter = Array(
                        "IBLOCK_ID" => 27,
                        "NAME" => $modelLastName,
                        "ACTIVE" => "Y"
                    );

                    $resNModel = CIBlockElement::GetList(Array(), $arNModelFilter, false, false, $arNModelSelect);

                    $foundNModel = false;

                    if($resNModel
                        && ($arNModel = $resNModel->GetNext())){

                        if(isset($arNModel['ID'])
                            && !empty($arNModel['ID'])){

                            $foundNModel = $arNModel['ID'];

                        }

                    }

                    if(!$foundNModel){

                        $arModelArray = Array(
                            "NAME" => trim($modelLastName),
                            "ACTIVE" => "Y",
                            "CODE" => trim(CUtil::translit(trim($modelLastName), LANGUAGE_ID, $params)),
                            "IBLOCK_ID" => 27,
                            "PREVIEW_TEXT" => " ",
                            "DETAIL_TEXT" => " ",
                        );

                        $modelEl = new CIBlockElement;

                        if ($foundNModel = $modelEl->Add($arModelArray)) {

                        }

                    }

                    if($foundNModel){

                        CIBlockElement::SetPropertyValuesEx($arModels['ID'], 17, ($arModelPropArr = array('model_new_link' => $foundNModel)));
						//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arModels['ID']);
                        $modelEl = new CIBlockElement;

                        if ($modelEl->Update($arModels['ID'], Array('TIMESTAMP_X' => true))) {

                        }

                    }

                }

            }


        }

        static::$countDups = $countDups;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_log_last.txt', $skip);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_last.txt', static::$countDups);

            die ('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/restore_link.php?intestwetrust=1&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_link_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")){
    impelRestoreLinkModels::checkModels();
}