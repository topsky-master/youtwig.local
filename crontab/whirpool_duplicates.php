<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelWhirpoolDuplicatesModels{

    private static $countStrings = 200;

    private static function gatherData(){

        $modelNameIds = array();

        $skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt'));

        $arModelNameSelect = Array(
            "ID",
            "NAME"
        );

        $arModelNameFilter = Array(
            "IBLOCK_ID" => 27,
            "ACTIVE" => "Y"
        );

        if($skip > 0){
            $arModelNameFilter["<ID"] = $skip;
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates.txt', "");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.txt',0);
        }

        $modelEl = new CIBlockElement;

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

                    $renameName = preg_replace('~[^a-z0-9]+~isu','',$arModelNameFields['NAME']);
                    $renameName = mb_strtolower($renameName);

                    $modelNameIds[$arModelNameFields['ID']] = $renameName;

                    CIBlockElement::SetPropertyValuesEx($arModelNameFields['ID'], 27, ($modelPropArr = array('CLEAR_NAME' => $renameName)));
					//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(27, $arModelNameFields['ID']);

                    if ($modelEl->Update($arModelNameFields['ID'], Array('TIMESTAMP_X' => true))) {

                    }

                }
            }
        }

        $sizeof = sizeof($modelNameIds);

        $sSizeof = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.txt');
        $sSizeof = (int)trim($sSizeof);

        $sSizeof += $sizeof;
        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_get.txt',$sSizeof);

        return $modelNameIds;

    }

    public static function checkModels($modelsId = array()){

        global $USER;

        $modelNameIds = static::gatherData();

        $modelLastPropId = 0;

        $modelLastPropId = static::checkFamiliarModels($modelNameIds);

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels($modelNameIds){

        $modelLastPropId = 0;
        $modelEl = new CIBlockElement;

        foreach($modelNameIds as $modelPropNameId => $modelPropName){

            $modelLastPropId = $modelPropNameId;

            $arModelSelect = Array(
                "ID",
                "NAME",
                "PROPERTY_type_of_product",
                "PROPERTY_manufacturer"
            );

            $arModelFilter = Array(
                "IBLOCK_ID" => 17,
                "PROPERTY_model_new_link" => $modelPropNameId,
                "ACTIVE" => "Y",
                //"PROPERTY_manufacturer_VALUE" => "Whirlpool"
            );

            $resModel = CIBlockElement::GetList(
                Array(),
                $arModelFilter,
                false,
                false,
                $arModelSelect
            );

            if ($resModel) {

                while ($arModel = $resModel->GetNext()) {

                    $renameName = '';


                    if(isset($arModel['PROPERTY_MANUFACTURER_VALUE'])
                        && !empty($arModel['PROPERTY_MANUFACTURER_VALUE'])
                        && isset($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                        && !empty($arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                    ){

                        $renameName .= $arModel['PROPERTY_TYPE_OF_PRODUCT_VALUE'];
                        $renameName .= ' '.$arModel['PROPERTY_MANUFACTURER_VALUE'];
                        $renameName .= ' '.$modelPropName;

                        CIBlockElement::SetPropertyValuesEx($arModel['ID'], 17, ($modelPropArr = array('CLEAR_NAME' => $renameName)));
						//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $arModel['ID']);

                        if ($modelEl->Update($arModel['ID'], Array('TIMESTAMP_X' => true))) {

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
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', $skip);
            echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/whirpool_duplicates.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
            die();
        } else {
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/whirpool_duplicates_last.txt', 0);
            echo 'done';
            die();
        }

    }
}

if(CModule::IncludeModule("iblock"))
    impelWhirpoolDuplicatesModels::checkModels();