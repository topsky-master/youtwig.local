<?if(CUser::IsAuthorized()){
	
	
	$Usr 								= $USER->GetByID($USER->GetID());
 	$Currencies 						= Array();
 	$CUser 								= $Usr->Fetch();
 	$rsCurrencies 						= CUserFieldEnum::GetList(array(), array("USER_FIELD_ID" => 11));
 	$arGroups 							= CUser::GetUserGroup($USER->GetID());
 			
	while($arCurrency 					= $rsCurrencies->GetNext()){
 		$Currencies[$arCurrency["ID"]] 	= $arCurrency;
 		if($_REQUEST["cur"] && $_REQUEST["cur"] == $arCurrency["XML_ID"]){
 			$arGroups 					= il::array_diff($arGroups,Array(3,4,5,8,9));
 			il::array_push($arGroups, $arCurrency["XML_ID"]);
 					
 			CUser::SetUserGroup($USER->GetID(),$arGroups);
 			$USER->Authorize($USER->GetID());
 			$CUser["UF_CURRENCY"] 		= $arCurrency["XML_ID"];
 		}
 	}

 	?>
	<select id="usrCurrency">
	<? foreach($Currencies as $Currency){
		$select												 = false;
		if(il::in_array($Currency["XML_ID"],$arGroups)){ $select = true; };
		?>
		<option<? if($select): ?> selected="selected"<?php endif; ?> value="<?=$Currency["XML_ID"]; ?>">
		<?=$Currency["VALUE"];?>
		</option>';
	<?
	}
	?>
	</select>
<script>
	$(function(){
		$('#usrCurrency').change(function(){ //alert($(this).val());
			switch($(this).val()){
				case '5':	document.location.href='<?=$GLOBALS["APPLICATION"]->GetCurPageParam("cur=5", array("cur"))?>'; break;
				case '8':	document.location.href='<?=$GLOBALS["APPLICATION"]->GetCurPageParam("cur=8", array("cur"))?>'; break;
				case '9':	document.location.href='<?=$GLOBALS["APPLICATION"]->GetCurPageParam("cur=9", array("cur"))?>'; break;
			}
		});
	});
</script>
<?} else {?>
<?	

	global $APPLICATION;

	il::error_reporting(E_ALL);
	il::ini_set('display_error',1);
	function errx(){
		print_r(il::error_get_last());
	}
	
	il::register_shutdown_function('errx');
	
	if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("currency")){
	
		
	
	$CURRENCY_CODE = isset($_REQUEST['CURRENCY_CODE']) && !empty($_REQUEST['CURRENCY_CODE']) ? $_REQUEST['CURRENCY_CODE'] : $_SESSION['CURRENCY_CODE'];
				
	if(empty($CURRENCY_CODE) && (($APPLICATION->get_cookie('CURRENCY_CODE')))){
		$CURRENCY_CODE 	= $APPLICATION->get_cookie('CURRENCY_CODE');
	}	
	
	if(!(!empty($CURRENCY_CODE) &&  ($CURRENCY = CCurrency::GetByID($CURRENCY_CODE)))){
		$CURRENCY_CODE  = "RUB";
	}
	
	
    		echo CCurrency::SelectBox("CURRENCY_DEFAULT",
                          $CURRENCY_CODE,
                          "",
                          True, 
                          "",
                          'class="usrCurrency"');
    };
     
    
?>
<script>
	$(function(){
		$('.usrCurrency').change(function(){ //alert($(this).val());
				document.location.href='<?=$GLOBALS["APPLICATION"]->GetCurPageParam("CURRENCY_CODE=", array("CURRENCY_CODE"))?>'+$(this).val();
		});
	});
</script>
<? } ?>
