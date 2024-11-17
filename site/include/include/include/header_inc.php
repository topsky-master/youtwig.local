<div class="first-top"> 
  <div class="countainer"> 
    <div class="row"> 
      <div class="col-lg-6"> 	 
        <div class="country-switch"></div>
       	 
        <div class="valute-switch"></div>
       </div>
     
      <div class="col-lg-6"> 	 
        <div class="first-top-menu pull-right"> 	<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"first-top-menu",
	Array(
		"ROOT_MENU_TYPE" => "top-first",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "top",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "Y"
	)
);?> </div>
       </div>
     </div>
   </div>
 </div>
 
<div class="main-top"> 	 
  <div class="countainer"> 	 
    <div class="row"> 		 
      <div class="col-lg-12"> 		 
        <div class="welcome"></div>
       		 
        <div class="logo"></div>
       		 
        <div class="cart"></div>
       		</div>
     	</div>
   </div>
 </div>
 	 
<div class="top-menu"> 		<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"top-menu",
	Array(
		"ROOT_MENU_TYPE" => "top",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => "",
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "personal",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "Y"
	)
);?>	</div>
 
<div id="serch"> <?$APPLICATION->IncludeComponent(
	"bitrix:search.form",
	"bootstrap-suggest",
	Array(
		"USE_SUGGEST" => "N",
		"PAGE" => "#SITE_DIR#search/index.php"
	)
);?> </div>
<div id="cart">
<?$APPLICATION->IncludeComponent(
         "bitrix:sale.basket.basket.small",
            "ajax_cart",
            Array(
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_ORDER" => "/personal/cart/"
	    ),
            false
   );?>
</div>
 