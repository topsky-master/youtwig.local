<?
define("STOP_STATISTICS", true);
define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$aResult = array();

if(isset($_SESSION['Subjects']) && !empty($_SESSION['Subjects'])){
	$sCode = isset($_REQUEST['code']) ? trim(filter_var(urldecode($_REQUEST['code']))) : '';
	if(!empty($sCode) && isset($_SESSION['Subjects'][$sCode])){
		$aResult = $_SESSION['Subjects'][$sCode];
	}
}

die(json_encode($aResult,true));