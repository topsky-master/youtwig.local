<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$bDemo = (CTicket::IsDemo()) ? "Y" : "N";
$bAdmin = (CTicket::IsAdmin()) ? "Y" : "N";
$bSupportTeam = (CTicket::IsSupportTeam()) ? "Y" : "N";
$bADS = $bDemo == 'Y' || $bAdmin == 'Y' || $bSupportTeam == 'Y';

?>

<form method="post" action="<?=$arResult["NEW_TICKET_PAGE"]?>">
	<input type="submit" name="edit" value="<?=GetMessage("SUP_ASK")?>" class="btn btn-primary">
</form>

<br />

<?

$APPLICATION->IncludeComponent(
	"bitrix:main.interface.grid",
	"",
	array(
		"GRID_ID"=>$arResult["GRID_ID"],
		"HEADERS"=>array(
			array("id"=>"LAMP", "name"=> GetMessage('SUP_LAMP'), "sort"=>"s_lamp", "default"=>true),
			array("id"=>"ID", "name"=>GetMessage('SUP_ID'), "sort"=>"s_id", "default"=>true),
			array("id"=>"TITLE", "name"=>GetMessage('SUP_TITLE'), "default"=>true),
			array("id"=>"TIMESTAMP_X", "name"=>GetMessage('SUP_TIMESTAMP'), "sort"=>"s_timestamp_x", "default"=>true),
			array("id"=>"MODIFIED_BY", "name"=>GetMessage('SUP_MODIFIED_BY'), "default"=>true),
			array("id"=>"MESSAGES", "name"=>GetMessage('SUP_MESSAGES'),  "default"=>true),
			array("id"=>"STATUS_NAME", "name"=>GetMessage('SUP_STATUS'), "default"=>true)
		),
		"SORT"=>$arResult["SORT"],
		"SORT_VARS"=>$arResult["SORT_VARS"],
		"ROWS"=>$arResult["ROWS"],
		"FOOTER"=>array(array("title"=>GetMessage('SUP_TOTAL'), "value"=>$arResult["ROWS_COUNT"])),
		"ACTION_ALL_ROWS"=>true,
		"EDITABLE"=>false,
		"NAV_OBJECT"=>$arResult["NAV_OBJECT"],
		"AJAX_MODE"=>$arParams["AJAX_MODE"],
		"AJAX_ID"=>$arParams["AJAX_ID"],
		//"AJAX_OPTION_JUMP"=>"N",
		"AJAX_OPTION_STYLE"=>"Y",
		//"FILTER"=>$arResult["FILTER"],
	),
	$component
);
?>