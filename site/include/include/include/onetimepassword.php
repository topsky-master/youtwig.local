<? 

il::define("STOP_STATISTICS", true);
il::define("ADMIN_SECTION",false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/nmain/lang/'.LANGUAGE_ID.'/header.php');

?>
<?$APPLICATION->IncludeComponent(
	"impel:passcode",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A"
	)
);?>