<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelManufacturersDuplicatesModels{

    private static $countStrings = 200;
    private static $modelEl = null;

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelLastPropId = 0;
        $modelLastPropId = static::checkFamiliarModels();

        static::getRedirect($modelLastPropId);

    }

    private static function getDoubles($typeOfProduct,$modelNewLink,$cName){

        $ids = array();

        $arModelSelect = Array(
            "ID",
            "NAME"
        );

        $arModelFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y",
            "PROPERTY_TYPE_OF_PRODUCT_VALUE" => $typeOfProduct,
            "PROPERTY_MODEL_NEW_LINK" => $modelNewLink
        );

        $resModel = CIBlockElement::GetList(
            ($order = Array(
                'PROPERTY_CLEAR_NAME' => 'DESC',
                'timestamp_x' => 'ASC')
            ),
            $arModelFilter,
            false,
            false,
            $arModelSelect
        );

        if($resModel){

            while($arModelFields = $resModel->GetNext()){

                $ids[$arModelFields['ID']] = $arModelFields['NAME'];

            }
        }

        return $ids;

    }

    private static function setDoublesRedirects($whereId,$redirects){

        $modelEl = static::$modelEl;

        $where = '';

        $arModelSelect = Array(
            "ID",
            "DETAIL_PAGE_URL"
        );

        $arModelFilter = Array(
            "IBLOCK_ID" => 17,
            "ID" => $whereId

        );

        $resModel = CIBlockElement::GetList(
            ($order = Array()),
            $arModelFilter,
            false,
            false,
            $arModelSelect
        );

        if($resModel
            && $arModel = $resModel->GetNext()) {

            if(isset($arModel['DETAIL_PAGE_URL'])
            && !empty($arModel['DETAIL_PAGE_URL'])){
                $where = $arModel['DETAIL_PAGE_URL'];
            }

        }

        foreach($redirects as $redirectId){


            $arModelSelect = Array(
                "ID",
                "DETAIL_PAGE_URL"
            );

            $arModelFilter = Array(
                "IBLOCK_ID" => 17,
                "ID" => $redirectId

            );

            $resModel = CIBlockElement::GetList(
                ($order = Array()),
                $arModelFilter,
                false,
                false,
                $arModelSelect
            );

            if($resModel
                && $arModel = $resModel->GetNext()) {

                if(isset($arModel['DETAIL_PAGE_URL'])
                    && !empty($arModel['DETAIL_PAGE_URL'])){
                    $what = $arModel['DETAIL_PAGE_URL'];
                }

            }

            static::addRedirect($what,$where);

            if ($modelEl->Update($redirectId,
                array(
                    'ACTIVE' => 'N',
                    'TIMESTAMP_X' => true)
            )) {

                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_deactivate.txt', $what .' - '.$where."\n",FILE_APPEND);
                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_deactivate.txt', (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$redirectId." - ".(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$whereId."\n",FILE_APPEND);

            }

        }


    }

    private static function checkFamiliarModels(){

        if(is_null(static::$modelEl )){
            static::$modelEl = new CIBlockElement;
        }

        $modelEl = static::$modelEl;

        $modelLastPropId = 0;

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_last.txt'));

        $arModelNameSelect = Array(
            "ID",
            "NAME",
            "PROPERTY_MANUFACTURER",
            "PROPERTY_TYPE_OF_PRODUCT",
            "PROPERTY_MODEL_NEW_LINK"
        );

        $arModelNameFilter = Array(
            "IBLOCK_ID" => 17,
            "ACTIVE" => "Y"
        );

        if($skip > 0){


        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_last.txt', 0);

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_deactivate.txt', "");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_rename.txt', "");

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_get.txt',0);
        }

        $skip = empty($skip) ? 1 : $skip;

        $arNameNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resModelName = CIBlockElement::GetList(
            ($order = Array(
                'PROPERTY_CLEAR_NAME' => 'DESC',
                'timestamp_x' => 'ASC')
            ),
            $arModelNameFilter,
            false,
            $arNameNavParams,
            $arModelNameSelect
        );

        if($resModelName){

            while($arModelNameFields = $resModelName->GetNext()){

                if(isset($arModelNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'])
                    && !empty($arModelNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'])){

                    $rMNLinkDb = CIBlockElement::GetByID($arModelNameFields['PROPERTY_MODEL_NEW_LINK_VALUE']);

                    $modelLastPropId = $arModelNameFields['ID'];

                    if($rMNLinkDb
                        && $aMNLink = $rMNLinkDb->Fetch()){

                        $sMName = $aMNLink['NAME'];

                        $cName = trim($arModelNameFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                            .' '.trim($arModelNameFields['PROPERTY_MANUFACTURER_VALUE'])
                            .' '.trim($sMName);

                        $arModelNameFields['NAME'] = preg_replace('~\s+~isu',' ',$arModelNameFields['NAME']);

                        if(strcasecmp(trim($arModelNameFields['NAME'])
                                ,trim($cName)) != 0
                            && mb_stripos(trim($arModelNameFields['NAME']),
                                trim($arModelNameFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                            ) !== false
                            && mb_stripos(trim($arModelNameFields['NAME']),
                                trim($aMNLink['NAME'])
                            ) !== false
                        ){

                            $ids = static::getDoubles(
                                $arModelNameFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'],
                                $arModelNameFields['PROPERTY_MODEL_NEW_LINK_VALUE'],
                                $cName
                            );

                            if(sizeof($ids) > 0){

                                $whereId = 0;

                                $rename = array();
                                $redirect = array();

                                foreach($ids as $mId => $mName){

                                    if(strcasecmp(trim($mName)
                                        ,trim($cName)) == 0){

                                        if(empty($whereId)){

                                            $whereId = $mId;

                                        } else {

                                            $redirect[] = $mId;
                                        }

                                    } else {

                                        $rename[] = $redirect[] = $mId;

                                    }

                                }

                                if(!empty($whereId)
                                && !empty($redirect)
                                ){

                                    static::setDoublesRedirects($whereId,$redirect);

                                }

                                if(!empty($rename)
                                    && empty($whereId)){

                                    foreach($rename as $modelRenameId){

                                        if ($modelEl->Update($modelRenameId,
                                            array(
                                                'NAME' => $cName,
                                                'TIMESTAMP_X' => true)
                                        )) {

                                            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_rename.txt', (CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&ID='.$modelRenameId."\n", FILE_APPEND);

                                        }

                                    }

                                }


                            }

                        }

                    }

                }

                //$arModelNameFields['PROPERTY_TYPE_OF_PRODUCT_VALUE']
                //$arModelNameFields['PROPERTY_MANUFACTURER_VALUE']
                //CIBlockElement::GetByID($arModelNameFields['MODEL_NEW_LINK'])



            }
        }

        ++$skip;

        return $modelLastPropId ? $skip : 0;

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
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'//local/crontab/manufacturers_mismatch_array.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/manufacturers_mismatch_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelManufacturersDuplicatesModels::checkModels();