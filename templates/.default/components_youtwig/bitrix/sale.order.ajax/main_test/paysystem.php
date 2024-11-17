<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

if(sizeof($arResult["PAY_SYSTEM"])){

    global $delivery_name, $USER;

    $is_partner = $USER->IsAuthorized() && $USER->getID() && in_array(16,$USER->GetUserGroup($USER->getID())) ? true : false;

    $is_filled = false;
    $paysystem_name = '';
    $paysystem_cid = '';

    $SKIP_PAYSYSTEM = $arParams['SKIP_PAYSYSTEM'];
    $SKIP_PAYSYSTEM = explode(',',$SKIP_PAYSYSTEM);
    $SKIP_PAYSYSTEM = is_array($SKIP_PAYSYSTEM) ? $SKIP_PAYSYSTEM : array($SKIP_PAYSYSTEM);
    $SKIP_PAYSYSTEM = array_map('trim',$SKIP_PAYSYSTEM);
    $SKIP_PAYSYSTEM = array_map('intval',$SKIP_PAYSYSTEM);
    $SKIP_PAYSYSTEM = array_unique($SKIP_PAYSYSTEM);
    $SKIP_PAYSYSTEM = array_filter($SKIP_PAYSYSTEM);

    foreach($arResult["PAY_SYSTEM"] as $arNumber => $arPaySystem){

        if(!empty($SKIP_PAYSYSTEM)
            && in_array($arPaySystem["ID"],$SKIP_PAYSYSTEM)){
            unset($arResult["PAY_SYSTEM"][$arNumber]);
            continue;
        }


        if($arPaySystem["CHECKED"]=="Y"):

            $is_filled	= true;

        endif;

        if ($arPaySystem["CHECKED"]=="Y"
            && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"
                && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")):

            $arTParams = array("replace_space"=>"_","replace_other"=>"_");
            $trans = Cutil::translit($arPaySystem["PSA_NAME"],"ru",$arTParams);
            $paysystem_name = 'paysystem_'.$trans.' paysystem_'.$arPaySystem["ID"];

        endif;



    }

    ?>
    <div class="order-properties ORDER_PROP_PAY_SYSTEM_ID <?=$delivery_name;?> <?=$paysystem_name;?> row">
        <div class="paysystem col-xs-12 col-sm-12 col-lg-10 col-md-12 <?php if($is_filled): ?>has-success<? endif; ?> required">
            <div class="cart-fields" data-property-id-row="paysystem">
                <label for="PAY_SYSTEM_ID" class="col-lg-3 col-xs-4 col-sm-12">
                    <?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?>
                    <span class="bx_sof_req">*</span>
                </label>
                <div class="col-lg-9 col-xs-8 col-sm-12">
                    <?

                    uasort($arResult["PAY_SYSTEM"], "cmpBySort"); // resort arrays according to SORT value

                    ?>
                    <select id="PAY_SYSTEM_ID" name="PAY_SYSTEM_ID" onchange="submitForm();" class="selectpicker form-control">
                        <?php

                        $selected_paySystem	= array();

                        $is_partner = $USER->IsAuthorized() && $USER->getID() && in_array(16,$USER->GetUserGroup($USER->getID())) ? true : false;
                        $is_roznica = $USER->IsAuthorized() && $USER->getID() && in_array(17,$USER->GetUserGroup($USER->getID())) ? true : false;


                        foreach($arResult["PAY_SYSTEM"] as $arPaySystem){

                            if (($is_partner || $is_roznica) && $arPaySystem["ID"] != 31) {
                                continue;
                            }
                            
                            if(	$arPaySystem["CHECKED"]=="Y" &&
                                !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")
                                && !(mb_strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) == 0 && intval($arPaySystem["PRICE"]) == 0)
                            ):
                                $selected_paySystem	= $arPaySystem;
                            endif;

                            ?>
                            <option value="<?=$arPaySystem["ID"]?>"<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")):?> selected="selected"<?php endif; ?>>
                                <?=$arPaySystem["PSA_NAME"];?>
                            </option>
                            <?

                        }
                        ?>
                    </select>
                    <?php
                    if(sizeof($selected_paySystem)):
                        ?>
                        <div class="paysystem-info clearfix hidden-xs">
                            <?php
                            if (intval($selected_paySystem["PRICE"]) > 0)
                                echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($selected_paySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
                            else
                                echo $selected_paySystem["DESCRIPTION"];
                            ?>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php


}
?>
