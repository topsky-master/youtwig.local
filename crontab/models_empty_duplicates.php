<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelRemapModels{

    private static $countStrings = 50;
    private static $skip = 0;
    private static $skipManufacturers = array(
        'Bosch',
        'Indesit',
        'Hotpoint-Ariston',
        'Ariston'
    );


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

        $maxCount = 50;

        if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models_log_last.txt')){
            //$_SESSION['count_duplicates'] = 0;
        }

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models_log_last.txt'));
        static::$skip = $skip = empty($skip) ? 1 : $skip;
        static::$skipManufacturers = array_map('trim',static::$skipManufacturers);
        static::$skipManufacturers = array_map('strtolower',static::$skipManufacturers);


        $arNameNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $arNameSelect = Array(
            "ID",
            "PROPERTY_model_new_link",
            "PROPERTY_manufacturer",
            "PROPERTY_type_of_product",
            "DETAIL_PAGE_URL"
        );

        $arNameFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_products" => false,
            "!PROPERTY_PRODUCTS_REMOVED" => false
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

            while($arNameFields = $resName->GetNext()){

                if(isset($arNameFields['ID'])
                    && !empty($arNameFields['ID'])){

                    $modelLastPropId = $arNameFields['ID'];

                    if(!in_array(mb_strtolower(trim($arNameFields['PROPERTY_MANUFACTURER_VALUE'])),static::$skipManufacturers)
                        && isset($arNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'])
                        && !empty($arNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'])
                    ){

                        //DETAIL_PAGE_URL

                        $arNameModelSelect = Array(
                            "NAME"
                        );

                        $arNameModelFilter = Array(
                            "IBLOCK_ID" => 27,
                            "ACTIVE_DATE" => "Y",
                            "ACTIVE" => "Y",
                            "ID" => $arNameFields['PROPERTY_MODEL_NEW_LINK_VALUE']
                        );

                        $dbModelName = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arNameModelFilter,
                            false,
                            false,
                            $arNameModelSelect
                        );

                        if($dbModelName
                            && $arModelName = $dbModelName->GetNext()){

                            if(isset($arModelName['NAME'])
                                && !empty($arModelName['NAME'])){

                                $arSNameModelFilter = Array(
                                    "IBLOCK_ID" => 27,
                                    "ACTIVE_DATE" => "Y",
                                    "ACTIVE" => "Y",
                                    "=NAME" => trim($arModelName['NAME'])
                                );

                                $arSNameModelSelect = Array(
                                    "ID"
                                );

                                $dbSModelName = CIBlockElement::GetList(
                                    ($order = Array('ID' => 'DESC')),
                                    $arSNameModelFilter,
                                    false,
                                    false,
                                    $arSNameModelSelect
                                );


                                if($dbSModelName){

                                    $hasFound = false;

                                    while($arSModelName = $dbSModelName->GetNext()){

                                        if($arNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'] != $arSModelName['ID']){

                                            $arNNameSelect = Array(
                                                "ID",
                                                "DETAIL_PAGE_URL"
                                            );

                                            $arNNameFilter = Array(
                                                "IBLOCK_ID" => 17,
                                                "PROPERTY_model_new_link" => $arSModelName['ID'],
                                                "ACTIVE_DATE" => "Y",
                                                "ACTIVE" => "Y",
                                                "!PROPERTY_products" => false,
                                                "PROPERTY_manufacturer_VALUE" => $arNameFields['PROPERTY_MANUFACTURER_VALUE'],
                                                "PROPERTY_type_of_product_VALUE" => $arNameFields['PROPERTY_TYPE_OF_PRODUCT_VALUE']
                                            );


                                            $hasFound = true;
                                            echo $arSModelName['ID'].'-'.$arNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'].'<br />';
                                            continue;

                                            $resNName = CIBlockElement::GetList(
                                                ($order = Array('ID' => 'DESC')),
                                                $arNNameFilter,
                                                false,
                                                false,
                                                $arNNameSelect
                                            );

                                            if($resNName
                                                && $arNName = $resNName->GetNext()){

                                                print_r($arNNameFilter);
                                                echo $arNameFields['DETAIL_PAGE_URL'].'-';
                                                echo $arNName['DETAIL_PAGE_URL'];
                                                die();

                                            }

                                        }

                                    }

                                    if($hasFound){
                                        die();
                                    }

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
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models_log_last.txt', static::$skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_empty_duplicates.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_models_log_last.txt', 0);
            //echo $_SESSION['count_duplicates'].'<br />';
            //$_SESSION['count_duplicates'] = 0;
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock"))
    impelRemapModels::checkModels();