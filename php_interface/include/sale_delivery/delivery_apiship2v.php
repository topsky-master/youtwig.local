<?php
$module_id = "ipol.apiship2v";
CModule::IncludeModule($module_id);

// Register module delivery service
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/classes/general/apishipdelivery.php'))
	AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliveryapiship2v', 'Init'));