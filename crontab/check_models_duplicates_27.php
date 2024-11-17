<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelCheckDuplModels{

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

        $getModels = array();
        $countDups = 0;

        $skip = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_log_last.txt');

        if(empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_list.txt', "");

        } else {

            $countDups = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_last.txt');

        }

        ++$skip;

        include dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_27.php';

        $getModels = array_slice($getModels, ($skip - 1) * static::$countStrings, static::$countStrings);

        foreach($getModels as $modelName => $modelIds){

            $modelLastPropId = $modelName;

            if($modelIds[0] > 1){

                if(sizeof($modelIds[1]) > 1){

                    $remapModels = false;

                    foreach($modelIds[1] as $modelId){

                        $resModelName = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            Array(
                                'IBLOCK_ID' => 17,
                                'PROPERTY_model_new_link' => $modelId,
                                'ACTIVE' => 'Y'
                            ),
                            Array(),
                            false
                        );

                        if($resModelName == 0){

                            $elUpdate->Update($modelId,($arFields = array('ACTIVE' => 'N', 'TIMESTAMP_X' => true)));
                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_list.txt', $modelName.'-'.$modelId."\n",FILE_APPEND);
                            ++$countDups;

                        } else {

                            $remapModels = true;
                            break;

                        }

                    }

                    if($remapModels){

                        $modelNameFirst = current($modelIds[1]);
                        $modelNameIds = array_slice($modelIds[1],1);

                        foreach($modelNameIds as $modelNameId){

                            $arModelNameFilter = array(
                                'IBLOCK_ID' => 17,
                                'PROPERTY_model_new_link' => $modelNameId
                            );

                            $arModelNameSelect = array(
                                'ID'
                            );

                            $dbModelName = CIBlockElement::GetList(
                                Array(),
                                $arModelNameFilter,
                                false,
                                false,
                                $arModelNameSelect
                            );

                            if($dbModelName
                                && ($arModelName = $dbModelName->GetNext())){

                                CIBlockElement::SetPropertyValuesEx(
                                    $arModelName['ID'],
                                    17,
                                    ($modelNewLinkArr = Array('model_new_link' => Array("VALUE" => $modelNameFirst)))
                                );

								//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arModelName['ID']);
        
                                $elUpdate->Update($modelNameId,($arFields = array('ACTIVE' => 'N', 'TIMESTAMP_X' => true)));

                            }

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

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_log_last.txt', $skip);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_last.txt', static::$countDups);

            die ('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/check_models_duplicates_27.php?intestwetrust=1&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/check_equal_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")){
    impelCheckDuplModels::checkModels();
}