<?



function errx(){
	print_r(error_get_last());
}

register_shutdown_function('errx');

define("STOP_STATISTICS", true);
//define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::includeModule('ipol.iml');
$arList = CDeliveryIML::getListFile();	

$slCode = isset($_REQUEST['code']) ? trim(filter_var($_REQUEST['code'])) : '';

$aPvz = array();

if(!empty($slCode) 
	&& isset($arList['SelfDelivery'][$slCode])
	&& !empty($arList['SelfDelivery'][$slCode])
  ){
	$aPvz = $arList['SelfDelivery'][$slCode];
}

echo(json_encode(array('PVZ' => $aPvz)));
die();