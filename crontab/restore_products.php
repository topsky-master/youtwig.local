<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if(!file_exists(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_products_last.txt')){
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_products_last.txt',1);
}

$skip = trim(file_get_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_products_last.txt'));
$skip = empty($skip) ? 1 : $skip;

$maxCount = 50;
$currentCount = 0;

$arNavParams = array(
    'nTopCount' => false,
    'nPageSize' => $maxCount,
    'iNumPage' => $skip,
    'checkOutOfRange' => true
);

$arModelSelect = array(
    'ID',
    'PROPERTY_type_of_product',
    'PROPERTY_manufacturer',
    'PROPERTY_RESTORE_PRODUCTS'
);

$arModelFilter = array(
    'IBLOCK_ID' => 17,
    'ACTIVE' => 'Y',
    '!PROPERTY_PRODUCTS_REMOVED' => false,
    'PROPERTY_products' => false,
    '!PROPERTY_RESTORE_PRODUCTS' => false
);

$resModel = CIBlockElement::GetList(
    ($order = Array('ID' => 'DESC')),
    $arModelFilter,
    false,
    $arNavParams,
    $arModelSelect
);

if($resModel) {

    while ($arModelFields = $resModel->GetNext()) {

        ++$currentCount;

        $foundModel = $arModelFields['ID'];

        $dbProductProps = CIBlockElement::GetProperty(
            17,
            $foundModel,
            Array("sort" => "asc"),
            Array("CODE"=>"RESTORE_PRODUCTS")
        );

        $products = array();

        if($dbProductProps){

            while($arProductProps = $dbProductProps->GetNext()){
                $products = explode(',',$arProductProps["VALUE"]);
            }

        }


        if(!empty($products)
            && sizeof($products) > 0){
            $toBaseProducts['products'] = $products;
        }

        CIBlockElement::SetPropertyValuesEx($foundModel, 17, $toBaseProducts);
		//\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(17, $foundModel);

    }


}


if(!empty($currentCount)){
    $skip++;
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_products_last.txt', $skip);
    echo '<html><header><script>setTimeout(function(){location.href="'.(CMain::IsHTTPS() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/local/crontab/restore_products.php?intestwetrust=1&time='.time().'";},'.mt_rand(500,700).');</script></header></html>';
    die();
} else {
    file_put_contents(dirname(dirname(__DIR__)).'/bitrix/tmp/restore_products_last.txt', 0);
    echo 'done';
    die();
}

