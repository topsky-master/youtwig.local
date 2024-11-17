<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Всероссийский Эндокринологический Конгресс");
?>
<div class="col-lg-12">
	<?$APPLICATION->IncludeComponent("bitrix:main.register", "register", array(
	"SHOW_FIELDS" => array(
		0 => "NAME",
		1 => "SECOND_NAME",
		2 => "LAST_NAME",
		3 => "PERSONAL_PROFESSION",
		4 => "PERSONAL_WWW",
		5 => "PERSONAL_MOBILE",
		6 => "PERSONAL_STREET",
		7 => "PERSONAL_CITY",
		8 => "PERSONAL_ZIP",
		9 => "PERSONAL_COUNTRY",
		10 => "WORK_COMPANY",
		11 => "WORK_POSITION",
		
	),
	"REQUIRED_FIELDS" => array(
	),
	"AUTH" => "Y",
	"USE_BACKURL" => "Y",
	"SUCCESS_PAGE" => "/personal/",
	"SET_TITLE" => "Y",
	"USER_PROPERTY" => array(
	),
	"USER_PROPERTY_NAME" => "",
	 
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