<?

//https://youtwig.ru/local/crontab/empty_base_products.php?intestwetrust=1

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$skip = isset($_REQUEST['skip']) && !empty($_REQUEST['skip']) ? trim($_REQUEST['skip']) : 0;

$lArSelect = array('ID','NAME');
$lArFilter = array(
    'IBLOCK_ID' => 16,
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
    array('nTopCount' => 10),
    $lArSelect);

$lastId = 0;

if($lDBRes){

    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/empty_base_products.csv',$mode);

    while($arFields = $lDBRes->GetNext()){

        //MAIN_PRODUCTS

        $skip = $lastId = $arFields['ID'];

        $mArSelect = array('ID');
        $mArFilter = array(
            'IBLOCK_ID' => 11,
            'PROPERTY_MAIN_PRODUCTS' => $arFields['ID']
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

        if(!$mFound && (get_quantity_product($arFields['ID']) > 0)) {
            fputcsv($fp,array($arFields['ID'],$arFields['NAME'],'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=16&type=catalog&ID='.$arFields['ID'].'&lang=ru&find_section_section=0&WF=Y'),';');
        }
    }

    fclose($fp);

}

if(!empty($lastId)){
    die('<html><header><meta http-equiv="refresh" content="'.mt_rand(0,1).';URL=\''.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/empty_base_products.php?intestwetrust=1&skip='.$skip.'&time='.time().'&PageSpeed=off\'" /><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/empty_base_products.php?intestwetrust=1&skip='.$skip.'&time='.time().'&PageSpeed=off";},'.mt_rand(500,700).');</script></header><body><h1>'.time().'</h1></body></html>');
} else {
    echo 'done';
}

