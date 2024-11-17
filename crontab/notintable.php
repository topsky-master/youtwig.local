<?

die();

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$file = isset($_REQUEST['file'])
&&!empty($_REQUEST['file'])
&&file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/'.urldecode(trim($_REQUEST['file'])))
    ? urldecode(trim($_REQUEST['file']))
    : 'parameters.csv';

if(file_exists($file)){

    $strings = file($file);
    $product_ids = array();

    foreach($strings as $string){

        $string = str_getcsv($string,';');
        $string = array_map('trim',$string);

        if(!in_array($string[0],$product_ids)){
            $product_ids[] = $string[0];
        }

    }

    $lArSelect = array('ID','NAME','PROPERTY_TYPEPRODUCT');
    $lArFilter = array(
        '!ID' => $product_ids,
        'IBLOCK_ID' => 16
    );


    $fp = fopen(dirname(dirname(__DIR__)).'/bitrix/tmp/notintable.csv','w+');

    $linkedID = array('id','название','тип товара');

    fputcsv($fp,$linkedID,";");

    $lDBRes = CIBlockElement::GetList(Array(), $lArFilter, false, false, $lArSelect);

    if($lDBRes){

        while($arFields = $lDBRes->GetNext()){
            fputcsv($fp,($linkedID = array($arFields['ID'],$arFields['NAME'],$arFields['PROPERTY_TYPEPRODUCT_VALUE'])),";");
        }

    }

    fclose($fp);

}
