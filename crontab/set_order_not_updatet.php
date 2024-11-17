<?

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

$id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 21700;
//$id = 21700;
$skip = 0;

$arFilter = Array(
    '<ID' => $id
);

$db_sales = CSaleOrder::GetList(
    array("ID" => "DESC"),
    $arFilter,
    false,
    false,
    array('*')
);

global $last;

register_shutdown_function('tryReload');

function tryReload(){

    global $last;
    echo '<html><header><script>setTimeout(function(){location.href="' . (CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/local/crontab/set_order_not_updatet.php?intestwetrust=1&time=' . time() . '&id='.$last.'";},' . mt_rand(150, 300) . ');</script></header></html>';
}

if($db_sales){

    while($ar_sales = $db_sales->getNext()) {

        ++$skip;

        $arFields = array(
            'EXTERNAL_ORDER' => 'Y'
        );

        echo $ar_sales["ID"]."<br />\n";

        CSaleOrder::Update($ar_sales["ID"], $arFields);

        $last = $ar_sales["ID"];

        if($skip > 100){

            echo '<html><header><script>setTimeout(function(){location.href="' . (CMain::IsHTTPS() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/local/crontab/set_order_not_updatet.php?intestwetrust=1&time=' . time() . '&id='.$last.'";},' . mt_rand(150, 300) . ');</script></header></html>';
            die();

        }
    }



}



echo 'done';