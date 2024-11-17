<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

global $APPLICATION,$USER,$MESS;
$APPLICATION->RestartBuffer();

	if(isset($_REQUEST['SMS_INFO'])){
		
		if($USER->IsAuthorized()){
			$fields 		= Array(
				"UF_SMS_INFORM" => (isset($_REQUEST['SMS_INFO']) && !empty($_REQUEST['SMS_INFO']) ? 1 : 0),
			);
			
			$USER->Update($USER->GetID(), $fields);
		};
		
		$APPLICATION->set_cookie('SMS_INFO', (!empty($_REQUEST['SMS_INFO']) ? 1 : 0), time()+60*60*24*30*12*2, "/");
	};
	
	
	die();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>