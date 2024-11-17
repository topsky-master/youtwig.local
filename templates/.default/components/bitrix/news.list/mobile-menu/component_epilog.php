<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->SetAdditionalCSS($templateFolder."/css/jquery.mmenu.css");
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.mmenu.all.js");

if(change_to_mobile){
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mobile.css');
} else {
    $APPLICATION->SetAdditionalCSS($templateFolder.'/mediaquery.css');
}

if(isset($_REQUEST['mobile_cart_count'])
    && !empty($_REQUEST['mobile_cart_count'])
    && SALE_INCLUDED){

    $count = CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), array(), false, array('ID'));

    $APPLICATION->RestartBuffer();
    echo json_encode(array('mobile_cart_count' => $count),JSON_FORCE_OBJECT);
    die();

}

?>
<script>
	//<!--
	$(function(){
		if($("#mobilemenucart").length){
			$("#mobilemenucart").html("<?=CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), array(), false, array('ID'));?>");
		};
	});
	//-->
</script>


