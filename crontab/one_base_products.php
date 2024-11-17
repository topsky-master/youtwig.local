<?

//https://youtwig.ru/local/crontab/one_base_products.php?intestwetrust=1

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

    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/one_base_products.csv',$mode);

    if($mode == 'w+')
    fputcsv($fp,array('ID базового товара','ID товара в каталоге','Имя базового товара',"Имя основного товара",'Ссылка на базовый товар в админке','Ссылка на товар в каталоге в админке'),';');

    while($arFields = $lDBRes->GetNext()){

        //MAIN_PRODUCTS

        $skip = $lastId = $arFields['ID'];

        $mArSelect = array('ID','NAME');
        $mArFilter = array(
            'IBLOCK_ID' => 11,
            'PROPERTY_MAIN_PRODUCTS' => $arFields['ID']
        );

        $mDBRes = CIBlockElement::GetList(Array(), $mArFilter, false, array('nTopCount' => 2), $mArSelect);
        $mFound = false;

        $mCount = 0;

        $mDBArr = array();
        $fArray = array();


        if($mDBRes){
            while($mDBArr = $mDBRes->GetNext()){

                if(isset($mDBArr['ID'])
                    && !empty($mDBArr['ID'])) {

                    $mFound = true;
                    ++$mCount;

                    if(empty($fArray))
                        $fArray = $mDBArr;

                }

            }

        }

        if($mFound && $mCount == 1){
            fputcsv($fp,array($arFields['ID'],$fArray['ID'],$arFields['NAME'],$fArray['NAME'],'https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=16&type=catalog&ID='.$arFields['ID'].'&lang=ru&find_section_section=0&WF=Y','https://youtwig.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=11&type=catalog&ID='.$fArray['ID'].'&lang=ru&find_section_section=0&WF=Y'),';');

        }
    }

    fclose($fp);

}

if(!empty($lastId)){
    die('<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/one_base_products.php?intestwetrust=1&skip='.$skip.'&time='.time().'";},'.mt_rand(5,30).');</script></header></html>');
} else {
    echo 'done';
}

