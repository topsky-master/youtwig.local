<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$cartStyle = 'bx-basket';
$cartId = "bx_basket_top";
$arParams['cartId'] = $cartId;

if ($arParams['POSITION_FIXED'] == 'Y')
{
	$cartStyle .= "-fixed {$arParams['POSITION_HORIZONTAL']} {$arParams['POSITION_VERTICAL']}";
	if ($arParams['SHOW_PRODUCTS'] == 'Y')
		$cartStyle .= ' bx-closed';
}
else
{
	$cartStyle .= ' bx-opener';
}
?>
<div id="<?=$cartId?>" class="<?=$cartStyle?>"><?
	/** @var \Bitrix\Main\Page\FrameBuffered $frame */

    require(realpath(dirname(__FILE__)).'/ajax_template.php');

?></div>
