<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?


if(!class_exists('impelDeliveryInterval')){
    class impelDeliveryInterval{

        static private function isTodayWeekend($date) {
            return in_array(date("l",$date), ["Saturday", "Sunday"]);
        }

        static private function getSubset(){
            $sOrderWeekends = Bitrix\Main\Config\Option::Get("my.stat", "order_weekends", "");
            $sOrderWeekends = explode("\n",$sOrderWeekends);
            $sOrderWeekends = array_map("trim",$sOrderWeekends);
            $sOrderWeekends = array_unique($sOrderWeekends);
            $sOrderWeekends = array_filter($sOrderWeekends);

            return $sOrderWeekends;
        }

        static public function deliveryDaysInterval($maxDays = 5, $sday = ''){

            $days = array();
            $sday = !empty($sday) ? strtotime($sday) : time();

            $subset = static::getSubset();

            $i = 0;

            while($i < $maxDays){

                if($i == 0){

                    $mTime = ((int)strftime('%H',$sday) > 11) ? ' +1 day' : '';

                    if($mTime != ''){
                        $maxDays++;
                        $i++;
                        continue;
                    }

                    $today = strtotime(strftime('%F 00:00:00 '.$mTime,$sday));


                } else {

                    $today = strtotime(strftime('%F 00:00:00  +'.$i.' day',$sday));

                }

                $cDate = strftime('%F',$today);

                if(in_array($cDate,$subset)){

                    ++$maxDays;
                    ++$i;

                    if(static::isTodayWeekend($today)){
                        ++$maxDays;
                        ++$i;
                    }

                } else if(static::isTodayWeekend($today)){

                    ++$maxDays;
                    ++$i;

                } else {

                    $days[] = strftime('%d.%m.%Y',$today);
                    ++$i;

                }

            }

            return $days;

        }

    }

}


if(!function_exists('properties_usort')){
    function properties_usort($a, $b){
        if ($a['SORT'] == $b['SORT']) {
            return 0;
        }
        return ($a['SORT'] < $b['SORT']) ? -1 : 1;
    }
}

if (!function_exists("showFilePropertyField"))
{
    function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
    {
        $res = "";

        if (!is_array($values) || empty($values))
            $values = array(
                "n0" => 0,
            );

        if ($property_fields["MULTIPLE"] == "N")
        {
            $res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\" class=\"has_tooltip\"></label>";
        }
        else
        {
            $res = '
                <script type="text/javascript">
                    function addControl(item)
                    {
                        var current_name = item.id.mb_split("[")[0],
                            current_id = item.id.mb_split("[")[1].replace("[", "").replace("]", ""),
                            next_id = parseInt(current_id) + 1;

                        var newInput = document.createElement("input");
                        newInput.type = "file";
                        newInput.name = current_name + "[" + next_id + "]";
                        newInput.id = current_name + "[" + next_id + "]";
                        newInput.onchange = function() { addControl(this); };
                        var br = document.createElement("br");
                        var br2 = document.createElement("br");

                        BX(item.id).parentNode.appendChild(br);
                        BX(item.id).parentNode.appendChild(br2);
                        BX(item.id).parentNode.appendChild(newInput);
                    }
                </script>
                ';

            $res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\" class=\"has_tooltip\" /></label>";
            $res .= "<br/><br/>";
            $res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
        }

        return $res;
    }
}

if (!function_exists("PrintPropsForm"))
{
    function PrintPropsForm($arSource = array(), $locationTemplate = ".default", &$counter, $arResult, $relatedProperties = array(), $delivery_name = '', $paysystem_name = '')
    {


        if(sizeof($relatedProperties)){

            $db_props 					= CSaleOrderProps::GetList(
                array("SORT" 		=> "ASC"),
                array(
                    "@CODE" 	=> array('delivery','paysystem','comments'),
                ),
                false,
                false
            );



            while($props				= $db_props->Fetch()){

                $arSource[]				= $props;
            }

            usort($arSource,'properties_usort');

        };


		$sCountry = 'Россия';
		
        if (!empty($arSource))
        {
            ?>

            <?

            foreach ($arSource as $arProperties){

                $is_filled                  = false;
                $error_find			  		= false;

                foreach($arResult["ERROR"] as $v){
                    if(mb_stripos($v,''.trim($arProperties["NAME"]).'') !== false){

                        $error_find	 		= $v;
                        $error_find			= str_ireplace('"','',$error_find);
                        $error_find			= str_ireplace('\'','',$error_find);
                        $error_find			= htmlspecialchars($error_find,ENT_HTML5,LANG_CHARSET);

                        break;
                    };
                };

                if(	(
                        $arProperties["CODE"] == 'paysystem'
                        || $arProperties["CODE"] == 'delivery'
                        || $arProperties["CODE"] == 'comments'
                    )
                    && sizeof($relatedProperties)

                ):

                    echo $relatedProperties[$arProperties["CODE"]];

                else:

                    if(!$error_find){

                        if ($arProperties["TYPE"] == "CHECKBOX"){

                            if ($arProperties["CHECKED"]=="Y"){
                                $is_filled = true;
                            }

                        }
                        elseif ($arProperties["TYPE"] == "TEXT")
                        {

                            if(!empty($arProperties["VALUE"])){
                                $is_filled  = true;
                            }

                        }
                        elseif ($arProperties["TYPE"] == "SELECT")
                        {

                            if (is_array($arProperties["VARIANTS"])){

                                foreach($arProperties["VARIANTS"] as $arVariants):

                                    if ($arVariants["SELECTED"] == "Y"){
                                        $is_filled = true;
                                        break;

                                    };

                                endforeach;

                            }

                        }
                        elseif ($arProperties["TYPE"] == "MULTISELECT")
                        {
                            if (is_array($arProperties["VARIANTS"])){
                                foreach($arProperties["VARIANTS"] as $arVariants):
                                    if ($arVariants["SELECTED"] == "Y"){

                                        $is_filled = true;
                                        break;

                                    };
                                endforeach;
                            };

                        }
                        elseif ($arProperties["TYPE"] == "TEXTAREA")
                        {
                            if(!empty($arProperties["VALUE"])){
                                $is_filled = true;
                            }

                        }
                        elseif ($arProperties["TYPE"] == "LOCATION")
                        {

                            if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0){

                                foreach ($arProperties["VARIANTS"] as $arVariant){

                                    if ($arVariant["SELECTED"] == "Y"){

										$sCountry = trim($arVariant["COUNTRY_NAME"]);
                                        $is_filled = true;
                                        break;

                                    }
                                }
                            }

                        }
                        elseif ($arProperties["TYPE"] == "RADIO")
                        {

                            if (is_array($arProperties["VARIANTS"])){
                                foreach($arProperties["VARIANTS"] as $arVariants):

                                    if($arVariants["CHECKED"] == "Y"){
                                        $is_filled = true;
                                        break;
                                    }

                                endforeach;
                            }

                        }
                        elseif ($arProperties["TYPE"] == "FILE")
                        {
                            if(!empty($arProperties["VALUE"])){
                                $is_filled = true;
                            }

                        }

                    }

                    ?>
                    <div class="row order-properties <?php echo $delivery_name == '' ? 'not-selected-delivery' : $delivery_name; ?> <?php echo $paysystem_name; ?> <?=$arProperties["FIELD_NAME"]?> <?=$arProperties["CODE"]?> <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>required<?php endif; ?> <?php if($is_filled && !$error_find): ?>has-success<?php endif; ?> <?php if($error_find): ?>has_order_error<?php endif; ?>">
                        <div class="col-xs-12 col-sm-12 col-lg-10 col-md-12 <?=$arProperties["FIELD_NAME"]?> <?=$arProperties["CODE"]?> <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>required<?php endif; ?> <?php if($is_filled && !$error_find): ?>has-success<?php endif; ?> <?php if($error_find): ?>has_order_error<?php endif; ?>" <?php if($error_find): ?> data:error="<?php echo $error_find; ?>"<?php endif; ?>>
						<?php 
							if ($arProperties["TYPE"] == "LOCATION")
                            {
						?>
						<input type="hidden" id="countryName" value="<?php echo trim($sCountry); ?>" />
						<?php		
							}

                            if(CSaleLocation::isLocationProMigrated())
                            {
                                $propertyAttributes = array(
                                    'type' => $arProperties["TYPE"],
                                    'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form'
                                );

                                if(intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
                                    $propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);

                                if(intval($arProperties['INPUT_FIELD_LOCATION']))
                                    $propertyAttributes['altLocationPropId'] = intval($arProperties['INPUT_FIELD_LOCATION']);

                                if($arProperties['IS_ZIP'] == 'Y')
                                    $propertyAttributes['isZip'] = true;
                            }
                            ?>
                            <div class="cart-fields" data-property-id-row="<?=intval($arProperties["ID"])?>">
                                <?
                                if ($arProperties["TYPE"] == "CHECKBOX")
                                {
                                    ?>
                                    <label for="<?=$arProperties["FIELD_NAME"]?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="">
                                        <input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y"){ echo " checked"; $is_filled = true; };?> class="has_tooltip" />
                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>


                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "TEXT")
                                {

                                    if(!empty($arProperties["VALUE"])){
                                        $is_filled  = true;
                                    }

                                    ?>
                                    <label for="<?=$arProperties["FIELD_NAME"]?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?

                                        if($arProperties["CODE"] == 'DAYOFDELIVERY') {

                                            $days = impelDeliveryInterval::deliveryDaysInterval();

                                            ?>
                                            <select class="selectpicker form-control has_tooltip"
                                                    name="<?= $arProperties["FIELD_NAME"] ?>"
                                                    id="<?= $arProperties["FIELD_NAME"] ?>">
                                                <option value=""><?= GetMessage('SOA_DAYOFDELIVERY'); ?></option>
                                                <?php foreach ($days as $dayvalue): ?>
                                                    <option<?php if (isset($arProperties["VALUE"]) && $arProperties["VALUE"] == $dayvalue): ?> selected="selected"<? endif; ?>
                                                        value="<?= $dayvalue; ?>"><?= $dayvalue; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?

                                        } else if($arProperties["CODE"] == 'TIMEOFDELIVERY') {

                                            $timeInterval = array('12 - 20','12 - 16', '16 - 20');

                                            ?>
                                            <select class="selectpicker form-control has_tooltip"
                                                    name="<?= $arProperties["FIELD_NAME"] ?>"
                                                    id="<?= $arProperties["FIELD_NAME"] ?>">
                                                <option value=""><?= GetMessage('SOA_TIMEOFDELIVERY'); ?></option>
                                                <?php foreach ($timeInterval as $number => $timevalue): ?>
                                                    <option<?php if ((isset($arProperties["VALUE"]) && $arProperties["VALUE"] == $timevalue) || (!(isset($arProperties["VALUE"]) && $arProperties["VALUE"]) && $number == 0)): ?> selected="selected"<? endif; ?>
                                                        value="<?= $timevalue; ?>"><?= $timevalue; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?


                                        } else {


                                            ?>

                                            <input type="text" maxlength="250" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" class="form-control has_tooltip" />

                                            <?

                                        }

                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "SELECT")
                                {
                                    ?>
                                    <label for="<?=$arProperties["FIELD_NAME"]?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" class="selectpicker form-control has_tooltip">
                                            <?
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y"){ echo " selected"; $is_filled = true; };?>><?=$arVariants["NAME"]?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "MULTISELECT")
                                {
                                    ?>
                                    <label for="<?=$arProperties["FIELD_NAME"]?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <select multiple="multiple" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>" class="selectpicker form-control has_tooltip">
                                            <?
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y"){ echo " selected"; $is_filled = true; };?>><?=$arVariants["NAME"]?></option>
                                            <?
                                            endforeach;
                                            ?>
                                        </select>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "TEXTAREA")
                                {
                                    $rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];

                                    if(!empty($arProperties["VALUE"])){
                                        $is_filled = true;
                                    }

                                    ?>
                                    <label for="<?=$arProperties["FIELD_NAME"]?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <textarea class="form-control has_tooltip" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description clearfix clear">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "LOCATION")
                                {
                                    ?>
                                    <label class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">

                                        <?
                                        $value = 0;
                                        if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
                                        {
                                            foreach ($arProperties["VARIANTS"] as $arVariant)
                                            {
                                                if ($arVariant["SELECTED"] == "Y")
                                                {

                                                    $is_filled = true;
                                                    $value = $arVariant["ID"];
                                                    break;
                                                }
                                            }
                                        }

                                        ?>

                                        <?CSaleLocation::proxySaleAjaxLocationsComponent(array(
                                            "AJAX_CALL" => "N",
                                            "COUNTRY_INPUT_NAME" => "COUNTRY",
                                            "REGION_INPUT_NAME" => "REGION",
                                            "CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
                                            "CITY_OUT_LOCATION" => "Y",
                                            "LOCATION_VALUE" => $value,
                                            "ORDER_PROPS_ID" => $arProperties["ID"],
                                            "ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
                                            "SIZE1" => $arProperties["SIZE1"],
                                        ),
                                            array(
                                                "ID" => $arProperties["VALUE"],
                                                "CODE" => "",
                                                "SHOW_DEFAULT_LOCATIONS" => "Y",

                                                // function called on each location change caused by user or by program
                                                // it may be replaced with global component dispatch mechanism coming soon
                                                "JS_CALLBACK" => "submitFormProxy", //($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitFormProxy" : "",

                                                // function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
                                                // it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
                                                "JS_CONTROL_DEFERRED_INIT" => intval($arProperties["ID"]),

                                                // an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
                                                // it may be replaced with global component dispatch mechanism coming soon
                                                "JS_CONTROL_GLOBAL_ID" => intval($arProperties["ID"]),

                                                "DISABLE_KEYBOARD_INPUT" => 'Y'
                                            ),
                                            $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplate,
                                            true,
                                            'location-block-wrapper has_tooltip'
                                        )?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>

                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "RADIO")
                                {
                                    ?>
                                    <label class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?
                                        if (is_array($arProperties["VARIANTS"]))
                                        {
                                            foreach($arProperties["VARIANTS"] as $arVariants):
                                                ?>
                                                <input
                                                    type="radio"
                                                    name="<?=$arProperties["FIELD_NAME"]?>"
                                                    id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"
                                                    value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y"){ echo " checked"; $is_filled = true; };?>
                                                    class="has_tooltip" />

                                                <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label></br>
                                            <?
                                            endforeach;
                                        }
                                        ?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>

                                    <?
                                }
                                elseif ($arProperties["TYPE"] == "FILE")
                                {
                                    if(!empty($arProperties["VALUE"])){
                                        $is_filled = true;
                                    }

                                    ?>
                                    <label for="<?php echo "ORDER_PROP_".$arProperties["ID"]; ?>" class="col-lg-3 col-xs-4 col-sm-12">
                                        <?=$arProperties["NAME"]?>
                                        <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                            <span class="bx_sof_req">*</span>
                                        <?endif;?>
                                    </label>
                                    <div class="col-lg-9 col-xs-8 col-sm-12">
                                        <?=showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>

                                        <?
                                        if (mb_strlen(trim($arProperties["DESCRIPTION"])) > 0):
                                            ?>
                                            <div class="bx_description">
                                                <?=$arProperties["DESCRIPTION"]?>
                                            </div>
                                        <?
                                        endif;
                                        ?>
                                    </div>


                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <?if(CSaleLocation::isLocationProEnabled()):?>
                    <script type="text/javascript">
                        //<!--
                        (window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
                            'id' => intval($arProperties["ID"]),
                            'attributes' => $propertyAttributes
                        ))?>);
                        //-->
                    </script>
                <?endif?>
                <?

                ++$counter;
            }
            ?>

            <?
        }
    }
}
?>