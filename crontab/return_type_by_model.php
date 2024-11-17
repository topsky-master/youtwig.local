<?

//тип продукта;производитель;модель;товар;

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if (isset($argc) && $argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

error_reporting(0);
ini_set('display_errors',0);
//https://youtwig.ru/local/crontab/return_type_by_model.php?intestwetrust=1&model=

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$aModels = array();
$sModelName = $_REQUEST['model'] ? trim($_REQUEST['model']) : ''; 
$sTypeProduct = '';
$sManProduct = '';

if(!empty($sModelName)){
	$arModelNameSelect = Array(
        "ID",
        "NAME"
    );

    $arModelNameFilter = Array(
        "IBLOCK_ID" => 27,
        "ACTIVE" => "Y",
	);
	
    {

        {

            $arModelNameFilter['=NAME'] = trim($sModelName);

            $resModelName = CIBlockElement::GetList(
                ($order = Array('ID' => 'DESC')),
                $arModelNameFilter,
                false,
                false,
                $arModelNameSelect
            );

            if(!$resModelName) {

                $resModelName = CIBlockElement::GetList(
                    ($order = Array('ID' => 'DESC')),
                    $arModelNameFilter,
                    false,
                    false,
                    $arModelNameSelect
                );
            }

            if($resModelName){

                $typeProduct = '';

                while($arModelNameFields = $resModelName->GetNext()){

                    if(isset($arModelNameFields['ID'])
                        && !empty($arModelNameFields['ID'])){
						
						$sModelName = $arModelNameFields['NAME'];
						
                        $arModelSelect = array(
                            'ID',
                            'PROPERTY_type_of_product',
                            'PROPERTY_manufacturer',

                        );

                        $arModelFilter = array(
                            'IBLOCK_ID' => 17,
                            'PROPERTY_model_new_link' => $arModelNameFields['ID'],
                            'ACTIVE' => 'Y'
                        );

                        $resModel = CIBlockElement::GetList(
                            ($order = Array('ID' => 'DESC')),
                            $arModelFilter,
                            false,
                            false,
                            $arModelSelect
                        );

                        if($resModel) {

                            while ($arModelFields = $resModel->GetNext()) {

                                if(isset($arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])
                                    && !empty($arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'])){

                                    $sTypeProduct = $arModelFields['PROPERTY_TYPE_OF_PRODUCT_VALUE'];
                                    $sManProduct = $arModelFields['PROPERTY_MANUFACTURER_VALUE'];
									
									$aModels = array(
										'model' => $sModelName,
										'type_of_product' => $sTypeProduct,
										'manufacturer' => $sManProduct

									);		
								
								}

                            }

                        }

                    }

                }

            }

        }

    }

}

header('Content-Type: application/json');
echo json_encode($aModels);
die();