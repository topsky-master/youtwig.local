<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names_last.txt')){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names_last.txt',1);
}

$skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names_last.txt'));
$skip = empty($skip) ? 1 : $skip;

$maxCount = 50;
$currentCount = 0;

$arNavParams = array(
    'nTopCount' => false,
    'nPageSize' => $maxCount,
    'iNumPage' => $skip,
    'checkOutOfRange' => true
);

$arModelNameSelect = Array(
    "ID"
);

$arModelNameFilter = Array(
    "IBLOCK_ID" => 27,
    'ACTIVE' => 'Y'
);

$resModelName = CIBlockElement::GetList(
    ($order = Array('ID' => 'DESC')),
    $arModelNameFilter,
    false,
    $arNavParams,
    $arModelNameSelect
);

$modelEl = new CIBlockElement;

if($resModelName){



    while($arModelNameFields = $resModelName->GetNext()){



        if(isset($arModelNameFields['ID'])
            && !empty($arModelNameFields['ID'])){

            ++$currentCount;

            $arModelSelect = array(
                'ID'
            );

            $arModelFilter = array(
                'IBLOCK_ID' => 17,
                'PROPERTY_model_new_link' => $arModelNameFields['ID'],
                'ACTIVE' => 'Y'
            );

            $countModel = CIBlockElement::GetList(
                ($order = Array('ID' => 'DESC')),
                $arModelFilter,
                Array(),
                false,
                $arModelSelect
            );

            if(!$countModel) {

                $modelEl->Update($arModelNameFields['ID'],
                    Array(
                        'ACTIVE' => 'N',
                        'TIMESTAMP_X' => true
                    )
                );

                file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names.csv',(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=27&type=catalog&ID='.$arModelNameFields['ID']."\n",FILE_APPEND);

            }

        }

    }

}

if(!empty($currentCount)){
    $skip++;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names_last.txt', $skip);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/deactivate_model_names.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/deactivate_model_names_last.txt', 0);
    echo 'done';
    die();
}

