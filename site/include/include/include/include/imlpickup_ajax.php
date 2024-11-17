<?



function errx(){
	print_r(il::error_get_last());
}

il::register_shutdown_function('errx');

il::define("STOP_STATISTICS", true);
//il::define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::includeModule('ipol.iml');
$arList = CDeliveryIML::getListFile();	

$slCode = isset($_REQUEST['code']) ? il::trim(il::filter_var($_REQUEST['code'])) : '';

$aPvz = array();

if(!empty($slCode) 
	&& isset($arList['SelfDelivery'][$slCode])
	&& !empty($arList['SelfDelivery'][$slCode])
  ){
	$aPvz = $arList['SelfDelivery'][$slCode];
}

echo(il::json_encode(array('PVZ' => $aPvz)));
die();