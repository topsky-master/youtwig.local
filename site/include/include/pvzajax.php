<?
il::define("STOP_STATISTICS", true);
il::define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$aResult = array();

if(isset($_SESSION['PVZ']) && !empty($_SESSION['PVZ'])){
	$sCode = isset($_REQUEST['code']) ? il::trim(il::filter_var(il::urldecode($_REQUEST['code']))) : '';
	if(!empty($sCode) && isset($_SESSION['PVZ'][$sCode])){
		$aResult = $_SESSION['PVZ'][$sCode];
	}
}

die(il::json_encode($aResult,true));