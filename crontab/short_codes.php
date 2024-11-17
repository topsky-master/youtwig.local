<?

//тип продукта;производитель;модель;товар;

if(!isset($_REQUEST['intestwetrust'])) die();

define("NO_KEEP_STATISTIC", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$arModelSelect = Array(
    "ID",
    "CODE",
    "NAME",
    "DETAIL_PAGE_URL"
);

$arModelFilter = Array(
    "IBLOCK_ID" => 17,
    "ACTIVE" => "N"
);

$resModel = CIBlockElement::GetList(
    ($order = Array(
        'PROPERTY_manufacturer' => 'asc',
        'created' => 'desc'
    )),
    $arModelFilter,
    false,
    false,
    $arModelSelect
);

if($resModel){

    while($arModel = $resModel->GetNext()) {

        $modelSearchName = trim(preg_replace('~\s*?\(.+\)\s*?$~isu','',$arModel['NAME']));

        $short_uri = trim($arModel["DETAIL_PAGE_URL"],'/');

        $rsRData = CBXShortUri::GetList(
            Array(),
            Array(
                'SHORT_URI' => $short_uri,
            )
        );

        $hasRedirect = false;

        if($rsRData){
            while($arrData = $rsRData->GetNext()){
                $hasRedirect = true;

                print_r($arrData);

            }
        }

        die();

        if(!$hasRedirect){




        }

    }

}