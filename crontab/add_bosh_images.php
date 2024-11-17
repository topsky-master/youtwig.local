<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelAddImagesToModels{

    private static $fp = false;
    private static $countStrings = 100;

    public static function updateModels(){

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/bosh_log_last.txt'));
        $countStrings = 0;

        static::$fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/bosh_models.csv','r');
        $bosh_models = array();

        $arModelNameSelect = Array(
            "ID",
            "NAME"
        );

        $arModelNameFilter = Array(
            "IBLOCK_ID" => 27
        );

        $modelEl = new CIBlockElement;

        $anyFound = false;

        if(static::$fp){
            while((($string = fgetcsv(static::$fp,0,';')) !== false)
                && ($countStrings < (static::$countStrings + $skip))){

                $modelName = $string[0];
                $modelImage = $string[1];

                if($countStrings < $skip){
                    ++$countStrings;
                    continue;
                }

                if(!empty($modelImage)
                    && !empty($modelImage)){

                    $anyFound = true;

                    $modelImage = $_SERVER['DOCUMENT_ROOT'].'/local/crontab/'.$modelImage;

                    if(file_exists($modelImage)){

                        $arModelNameFilter['=NAME'] = $modelName;

                        $resModelName = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arModelNameFilter,
                            false,
                            false,
                            $arModelNameSelect
                        );

                        if($resModelName) {

                            while ($arModelNameFields = $resModelName->GetNext()) {

                                if(isset($arModelNameFields['ID'])
                                    && !empty($arModelNameFields['ID'])){

                                    $arModelSelect = array(
                                        'ID',
                                        'NAME'
                                    );

                                    $arModelFilter = array(
                                        'IBLOCK_ID' => 17,
                                        'PROPERTY_model_new_link' => $arModelNameFields['ID']
                                    );

                                    $resModel = CIBlockElement::GetList(
                                        ($order = Array('ID' => 'DESC')),
                                        $arModelFilter,
                                        false,
                                        false,
                                        $arModelSelect
                                    );

                                    if($resModel) {

                                        while ($arModelFields = $resModel->GetNext()) {

                                            $currentModel['PREVIEW_PICTURE'] = CFile::MakeFileArray($modelImage);
                                            $currentModel['DETAIL_PICTURE'] = CFile::MakeFileArray($modelImage);

                                            if ($modelEl->Update($arModelFields['ID'], $currentModel)) {

                                            }

                                        }

                                    }

                                }
                            }
                        }

                    }



                }

                ++$countStrings;

            }

        }

        if(!$anyFound){
            $countStrings = 0;
        }

        fclose(static::$fp);

        static::getRedirect($countStrings);

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/bosh_log_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/add_bosh_images.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/bosh_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelAddImagesToModels::updateModels();