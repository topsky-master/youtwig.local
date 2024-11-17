<?
if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? trim($_REQUEST['skip']) : 0;

$lArSelect = array('ID','NAME');
$lArFilter = array(
    'IBLOCK_ID' => 11,
    "ACTIVE" => "Y",
);

if($skip) {
    $lArFilter['>ID'] = $skip;
    $mode = 'a+';
} else {
    $mode = 'w+';
}

$lDBRes = CIBlockElement::GetList(
    ($lOrder = Array('ID' => 'ASC')),
    $lArFilter,
    false,
    array('nTopCount' => 5),
    $lArSelect);

$lastId = 0;

if($lDBRes){

    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/products_whithout_models.csv',$mode);

    while($arFields = $lDBRes->GetNext()){

        //MAIN_PRODUCTS

        $skip = $lastId = $arFields['ID'];

        $mArSelect = array('ID');
        $mArFilter = array(
            'IBLOCK_ID' => 17,
            'PROPERTY_products' => $arFields['ID']
        );

        $mDBRes = CIBlockElement::GetList(Array(), $mArFilter, false, array('nTopCount' => 1), $mArSelect);
        $mFound = false;

        if($mDBRes
            && ($mDBArr = $mDBRes->GetNext())){

            if(isset($mDBArr['ID'])
                && !empty($mDBArr['ID'])) {

                $mFound = true;

            }

        }

        if(!$mFound)
            fputcsv($fp,array($arFields['ID'],$arFields['NAME'],'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$arFields['ID'].'&lang=ru&find_section_section=0&WF=Y'),';');

    }

    fclose($fp);

}

if(!empty($lastId)){
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/products_whithout_models.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(5,30).');</script></header></html>');
} else {
    echo 'done';
}

