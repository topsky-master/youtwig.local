<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$skip = 0;

$arFilter = Array(
    'STATUS_ID'	=>"F"
);

$db_sales = CSaleOrder::GetList(
    array("DATE_INSERT" => "DESC",
        "DATE_UPDATE" => "DESC"),
    $arFilter,
    false,
    false,
    array('*')
);

if($db_sales){

    while($ar_sales = $db_sales->getNext()) {

        echo '.';
        ++$skip;

        CSaleOrder::StatusOrder($ar_sales["ID"],'FF');

        if($skip > 100){

            echo '<html><header><script>setTimeout(function(){location.href="' . (CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/local/crontab/set_order_status_ff.php?intestwetrust=1&time=' . time() . '";},' . mt_rand(150, 300) . ');</script></header></html>';
            die();

        }
    }



}

echo 'done';