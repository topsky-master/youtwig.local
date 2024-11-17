<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelDeleteNameModels{

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

        $skip = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_log_last.txt');

        if(empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_last.txt', 0);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_list.txt', "");

        } else {

            $countDups = file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_last.txt');

        }

        ++$skip;

        $arModelSelect = Array("ID");

        $arModelFilter = Array(
            "IBLOCK_ID" => 27,
            "ACTIVE" => "N"
        );

        $arModelNav = Array(
            "nTopCount" => 500
        );

        $resModel = CIBlockElement::GetList(Array(), $arModelFilter, false, $arModelNav, $arModelSelect);

        while($arModels = $resModel->GetNext()){

            $modelLastPropId = $arModels["ID"];
            CIBlockElement::Delete($arModels["ID"]);
            ++$countDups;

        }

        static::$countDups = $countDups;

        return $modelLastPropId ? $skip : 0;

    }

    private static function getRedirect($skip = ''){

        if(!empty($skip)){

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_log_last.txt', $skip);
            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_last.txt', static::$countDups);

            die ('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/models_delete.php?intestwetrust=1&time='.time().'";},'.mt_rand(50,70).');</script></header></html>');

        } else {

            file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/delete_models_log_last.txt', 0);
            echo 'done';
            die();
        }

    }

}

if(CModule::IncludeModule("iblock")){
    impelDeleteNameModels::checkModels();
}