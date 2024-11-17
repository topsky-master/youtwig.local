<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global string $componentPath
 * @global string $templateName
 * @var CBitrixComponentTemplate $this
 */
$cartStyle = 'bx-basket';
$cartId = "bx_basket".$this->randString();
$arParams['cartId'] = $cartId;

$arResult['COMPOSITE_STUB'] = 'Y';
require(realpath(dirname(__FILE__)).'/top_template.php');
unset($arResult['COMPOSITE_STUB']);
