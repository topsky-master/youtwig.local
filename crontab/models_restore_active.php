<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelRemapModels{

    private static $countStrings = 500;
    private static $skip = 0;
    private static $similarManufacturers = array(
        'Hotpoint' => 26480,
        'Ariston' => 3075,
        'Hotpoint-Ariston' => 3077,
        'Indesit' => 3079);

    public static function getName($modelSearchName){

        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$modelSearchName));
        $modelSearchName = trim(str_ireplace('(','',$modelSearchName));
        $modelSearchName = trim(str_ireplace(')','',$modelSearchName));
        $modelSearchName = trim(preg_replace('~\s+~isu','',$modelSearchName));

        return $modelSearchName;
    }

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels(){



        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/active_models_log_last.txt'));
        static::$skip = $skip = empty($skip) ? 1 : $skip;
        $modelEl = new CIBlockElement;

        $similarManufacturers = array_values(static::$similarManufacturers);

        $arNameNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $arNameSelect = Array(
            "ID",
            "NAME",
            "ACTIVE"
        );

        $arNameFilter = Array(
            "IBLOCK_ID" => 27
        );

        $resName = CIBlockElement::GetList(
            ($order = Array('ID' => 'DESC')),
            $arNameFilter,
            false,
            $arNameNavParams,
            $arNameSelect
        );

        $modelEl = new CIBlockElement;
        $modelLastPropId = 0;

        if($resName){

            $count = 0;

            while($arNameFields = $resName->GetNext()){

                $modelLastPropId = $arNameFields["ID"];
                $modelName = $arNameFields["NAME"];

                if($arNameFields['ACTIVE'] == 'N') {

                    $arCNameSelect = Array(
                        "ID",
                        "NAME"
                    );

                    $arCNameFilter = Array(
                        "IBLOCK_ID" => 27,
                        "=NAME" => $modelName
                    );

                    $countName = CIBlockElement::GetList(
                        ($order = Array('ID' => 'DESC')),
                        $arCNameFilter,
                        array(),
                        false,
                        $arCNameSelect
                    );

                    if($countName < 2){

                        $arModelSelect = array('ID');

                        $arModelFilter = Array(
                            "IBLOCK_ID" => 17,
                            "PROPERTY_model_new_link" => $modelLastPropId,
                            "ACTIVE" => "Y"
                        );

                        $countModel = CIBlockElement::GetList(
                            ($order = Array(
                                'PROPERTY_manufacturer' => 'asc',
                                'created' => 'desc'
                            )),
                            $arModelFilter,
                            array(),
                            false,
                            $arModelSelect
                        );

                        if($countModel){

                            if ($modelEl->Update($modelLastPropId, Array('ACTIVE' => 'Y', 'TIMESTAMP_X' => true))) {

                            } else {

                            }


                        }


                    } else if($countName > 1){

                        $arCNameFilter = Array(
                            "IBLOCK_ID" => 27,
                            "=NAME" => $modelName,
                            "ACTIVE" => "Y"
                        );

                        $countName = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arCNameFilter,
                            array(),
                            false,
                            $arCNameSelect
                        );

                        if($countName){

                            $arModelSelect = array(
                                'ID',
                                'NAME',
                                "PROPERTY_manufacturer"
                            );

                            $arModelFilter = Array(
                                "IBLOCK_ID" => 17,
                                "PROPERTY_model_new_link" => $modelLastPropId,
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

                                while($arModel = $resModel->GetNext()){

                                    if(in_array($arModel['PROPERTY_MANUFACTURER_ENUM_ID'],$similarManufacturers)) {

                                        if ($modelEl->Update($arModel['ID'], Array('ACTIVE' => 'N', 'TIMESTAMP_X' => true))) {

                                        } else {

                                        }

                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivated_duplicates.txt','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$arModel['ID']."\n",FILE_APPEND);
                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivated_duplicates.txt',$arModel['NAME']."\n",FILE_APPEND);


                                    } else {

                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/maybe_duplicates.txt','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$arModel['ID']."\n",FILE_APPEND);
                                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/maybe_duplicates.txt',$arModel['NAME']."\n",FILE_APPEND);

                                    }

                                }

                            }

                        } else {

                            $arModelSelect = array(
                                'ID',
                                'NAME'
                            );

                            $arModelFilter = Array(
                                "IBLOCK_ID" => 17,
                                "PROPERTY_model_new_link" => $modelLastPropId,
                                "ACTIVE" => "Y",
                            );

                            $countActiveModel = CIBlockElement::GetList(
                                ($order = Array(
                                    'PROPERTY_manufacturer' => 'asc',
                                    'created' => 'desc'
                                )),
                                $arModelFilter,
                                Array(),
                                false,
                                $arModelSelect
                            );

                            if($countActiveModel){

                                if ($modelEl->Update($modelLastPropId, Array('ACTIVE' => 'Y', 'TIMESTAMP_X' => true))) {

                                } else {

                                }

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

    private static function getRedirect($modelLastPropId = ''){

        if(!empty($modelLastPropId)){
            ++static::$skip;
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/active_models_log_last.txt', static::$skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_restore_active.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/active_models_log_last.txt', 0);
            //echo $_SESSION['count_duplicates'].'<br />';
            //$_SESSION['count_duplicates'] = 0;
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelRemapModels::checkModels();