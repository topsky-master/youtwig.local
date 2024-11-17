<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<? // последние просмотренные элементы
		$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "product_view", array(
		   "VIEWED_COUNT" => "5", // количество товаров в истории, по умолчанию 5
		   "VIEWED_NAME" => "Y", // показывать ли имя, по умолчанию Y
		   "VIEWED_IMAGE" => "Y", // показывать ли картинку, по умолчанию Y
		   "VIEWED_PRICE" => "N", // показывать ли цену, по умолчанию Y
		   "VIEWED_CANBUY" => "N", // показывать ли кнопку купить, по умолчанию N
		   "VIEWED_CANBUSKET" => "N", // показывать ли кнопку положить в карзину, по умолчанию N
		   "VIEWED_IMG_HEIGHT" => 62, // высота изображения для ресайзера, по умолчанию 150
		   "VIEWED_IMG_WIDTH" => 62, // ширина изображения для ресайзера, по умолчанию 150
		   "BASKET_URL" => "/personal/basket.php",
		   "ACTION_VARIABLE" => "action",
		   "PRODUCT_ID_VARIABLE" => "id"
		   )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>