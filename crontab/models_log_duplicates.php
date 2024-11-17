<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelDuplModels{

    private static $countStrings = 200;
    private static $getModels = array();
    private static $iblock_id = 0;

    public static function checkModels($iblock_id = 17){

        global $USER;

        static::$iblock_id = $iblock_id;

        $modelLastPropId = static::checkFamiliarModels($iblock_id);

        static::getRedirect($modelLastPropId);

    }

    private static function checkFamiliarModels($iblock_id){

        $modelLastPropId = 0;
        $currentCount = 0;

        $getModels = array();

        $arNameSelect = Array(
            "ID",
            "NAME"
        );

        $arNameFilter = Array(
            "IBLOCK_ID" => $iblock_id,
            "ACTIVE" => "Y"
        );

        $skip = (int)file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt');

        if($skip > 0){

            include dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.php';

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_number.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.php', "<?php ?>");
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt','');

        }

        ++$skip;

        $arNameOrder = Array(
            "NAME" => "ASC",
            "CREATED" => "ASC"
        );

        $arNameNavParams = array(
            'nTopCount' => false,
            'nPageSize' => static::$countStrings,
            'iNumPage' => $skip,
            'checkOutOfRange' => true
        );

        $resName = CIBlockElement::GetList(
            $arNameOrder,
            $arNameFilter,
            false,
            $arNameNavParams,
            $arNameSelect
        );

        if($resName){

            while($arName = $resName->GetNext()){

                ++$currentCount;

                $modelLastPropId = $arName['ID'];

                if(!isset($getModels[$arName['NAME']])){
                    $getModels[$arName['NAME']] = array(0,array());
                }

                $getModels[$arName['NAME']][0]++;
                $getModels[$arName['NAME']][1][] = $arName['ID'];


            }

        }

        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_number.txt', ((($skip - 1) * static::$countStrings) + $currentCount));

        static::$getModels = $getModels;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', $skip);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log.php', '<?php $getModels = '.var_export(static::$getModels,true).'; ?>');
            die ('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_log_duplicates.php?intestwetrust=1&iblock_id='.static::$iblock_id.'&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            foreach(static::$getModels as $modelName => $modelArr){

                if($modelArr[0] > 1) {

                    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt',$modelName."\n",FILE_APPEND);

                    foreach($modelArr[1] as $modelId){

                        file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_get.txt',(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.static::$iblock_id.'&type=catalog&ID='.$modelId."\n",FILE_APPEND);

                    }

                }

            }

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/equal_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")){
    $iblock_id = isset($_REQUEST['iblock_id'])
    && !empty($_REQUEST['iblock_id'])
        ? (int)$_REQUEST['iblock_id']
        : 27;

    impelDuplModels::checkModels($iblock_id);
}