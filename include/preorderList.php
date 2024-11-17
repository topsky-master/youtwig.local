<?if(!defined('CATALOG_INCLUDED')) die(); ?>
<?$APPLICATION->IncludeComponent(
		"impel:cartpreorder",
		"list",
		Array(
			"CONSENT_PROCESSING_TEXT" => $arParams["CONSENT_PROCESSING_TEXT"]
		),
		false
	);?>