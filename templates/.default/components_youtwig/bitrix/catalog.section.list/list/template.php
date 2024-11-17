	<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<?
	$strTitle = "";
	?>
	<div class="catalog-section-list-wrapper clearfix">
		<?
		$TOP_DEPTH = $arResult["SECTION"]["DEPTH_LEVEL"];
		$CURRENT_DEPTH = $TOP_DEPTH;
	
		foreach($arResult["SECTIONS"] as $arSection)
		{
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
			if($CURRENT_DEPTH < $arSection["DEPTH_LEVEL"])
			{
				echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"]-$TOP_DEPTH),"<ul>";
			}
			elseif($CURRENT_DEPTH == $arSection["DEPTH_LEVEL"])
			{
				echo "</li>";
			}
			else
			{
				while($CURRENT_DEPTH > $arSection["DEPTH_LEVEL"])
				{
					echo "</li>";
					echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
					$CURRENT_DEPTH--;
				}
				echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</li>";
			}
	
			$count = $arParams["COUNT_ELEMENTS"] && $arSection["ELEMENT_CNT"] ? "&nbsp;(".$arSection["ELEMENT_CNT"].")" : "";
	
			if ($_REQUEST['SECTION_ID']==$arSection['ID'])
			{
				$link = '<b>'.$arSection["NAME"].$count.'</b>';
				$strTitle = $arSection["NAME"];
			}
			else
			{
				$link = '<a href="'.$arSection["SECTION_PAGE_URL"].'">'.$arSection["NAME"].$count.'</a>';
			}
	
			echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"]-$TOP_DEPTH);
			?><li id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?=$link?><?
	
			$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
		}
	
		while($CURRENT_DEPTH > $TOP_DEPTH)
		{
			echo "</li>";
			echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
			$CURRENT_DEPTH--;
		}
		?>
	</div>
	<?=($strTitle?'<h2>'.$strTitle.'</h2>':'')?>
<script type="text/javascript">
	//<!--
		$(function(){
			if($(".catalog-section-list-wrapper") && $(".catalog-section-list-wrapper").length){
				$(".catalog-section-list-wrapper li").each(function(){
					
					if($(this).children("ul").get(0) && $(this).children("a").get(0)){
						$(this).children("a").eq(0).after('<span class="toggle"></span>');
					};
					
					$(".toggle",$(this).parents("div.catalog-section-list-wrapper").get(0)).unbind("click");					
					$(".toggle",$(this).parents("div.catalog-section-list-wrapper").get(0)).bind("click",function(){
						$(this).next("ul").eq(0).toggle();
						if($(this).next("ul").eq(0).css("display") == "none"){
							$(this).removeClass("openToggle");
						} else {
							$(this).addClass("openToggle");
						};
					});
					
					$(".toggle",$(this).parents("div.catalog-section-list-wrapper").get(0)).each(function(){
						if($(this).next("ul").eq(0).css("display") == "none"){
							$(this).removeClass("openToggle");
						} else {
							$(this).addClass("openToggle");
						};
					});
					
				});
				
				$(".catalog-section-list-wrapper a").each(function(){
						
						var inSearch     = this.href.replace(location.protocol + '//' + location.hostname, '');
						
						if(inSearch 	 == location.pathname){
						
							var pNode    = this.parentNode;
							
							
							
							
							while(pNode && pNode.className.indexOf("catalog-section-list-wrapper") == -1){
								
									
								if(pNode.nodeName.toLowerCase() == "ul"){
									$(pNode).show();
									$(pNode).parent().find(".toggle").addClass("openToggle");
								};	
								
								pNode  = pNode.parentNode;
								
							};						
						};				
				});
					
			};	
		});
	//-->
</script>
