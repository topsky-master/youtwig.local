<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Всероссийский Эндокринологический Конгресс");
?>
<div class="col-lg-12">
	<?$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	"register", 
	array(
		"SHOW_FIELDS" => array(
			0 => "PHONE_NUMBER",
			1 => "NAME",
			2 => "SECOND_NAME",
			3 => "LAST_NAME",
		),
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
			1 => "PHONE_NUMBER",
		),
		"AUTH" => "Y",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => "/personal/",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => "",
		"COMPONENT_TEMPLATE" => "register"
	),
	false
);?>
<!--<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "auth-form", array(
	 "REGISTER_URL" => "/registration/",
      "PROFILE_URL" => "/personal/",
      "SHOW_ERRORS" => "Y"
	),
	false
);?>--> 
</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>