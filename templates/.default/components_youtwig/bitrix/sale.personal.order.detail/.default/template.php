<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$payment = false;

if(isset($arResult["PAYMENT"])
){
    $payment = current($arResult["PAYMENT"]);
};

?>
<div class="order-detail">
    <a name="tb"></a>
    <a href="<?=$arResult["URL_TO_LIST"]?>" class="btn btn-primary"><?=GetMessage("SPOD_RECORDS_LIST")?></a>
    <?if(mb_strlen($arResult["ERROR_MESSAGE"])<=0):?>
        <table class="table table-striped table-bordered table-hover order-detail-table">
            <tr>
                <th colspan="2" align="center"><b><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ACCOUNT_NUMBER"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></b></th>
            </tr>
            <tr>
                <td align="right" width="40%"><?echo GetMessage("SPOD_ORDER_STATUS")?></td>
                <td width="60%"><?=$arResult["STATUS"]["NAME"]?><?=GetMessage("SPOD_ORDER_FROM")?><?=$arResult["DATE_STATUS"]?>)</td>
            </tr>
            <tr>
                <td align="right" width="40%"><?=GetMessage("P_ORDER_PRICE")?>:</td>
                <td width="60%"><?
                    echo "<b>".$arResult["PRICE_FORMATED"]."</b>";
                    if (DoubleVal($arResult["SUM_PAID"]) > 0)
                        echo " (".GetMessage("SPOD_ALREADY_PAID")."&nbsp;<b>".$arResult["SUM_PAID_FORMATED"]."</b>)";
                    ?></td>
            </tr>
            <tr>
                <td align="right" width="40%"><?= GetMessage("P_ORDER_CANCELED") ?>:</td>
                <td width="60%"><?
                    echo (($arResult["CANCELED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
                    if ($arResult["CANCELED"] == "Y")
                    {
                        echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_CANCELED"].")";
                        if (mb_strlen($arResult["REASON_CANCELED"]) > 0)
                            echo "<br />".$arResult["REASON_CANCELED"];
                    }
                    elseif ($arResult["CAN_CANCEL"]=="Y")
                    {
                        ?>&nbsp;<a href="<?=$arResult["URL_TO_CANCEL"]?>"><?=GetMessage("SALE_CANCEL_ORDER")?>&gt;&gt;</a><?
                    }
                    ?></td>
            </tr>
            <tr>
                <th align="right" colspan="2"><img src="/bitrix/images/1.gif" width="1" height="8"></th>
            </tr>
            <?if (IntVal($arResult["USER_ID"])>0):?>
                <tr>
                    <th colspan="2"><b><?echo GetMessage("SPOD_ACCOUNT_DATA")?></b></th>
                </tr>
                <tr>
                    <td align="right" width="40%"><?= GetMessage("SPOD_ACCOUNT") ?>:</td>
                    <td width="60%"><?=$arResult["USER_NAME"]?></td>
                </tr>
                <tr>
                    <td align="right" width="40%"><?= GetMessage("SPOD_LOGIN") ?>:</td>
                    <td width="60%"><?=$arResult["USER"]["LOGIN"]?></td>
                </tr>
                <tr>
                    <td align="right" width="40%"><?echo GetMessage("SPOD_EMAIL")?></td>
                    <td width="60%"><a href="mailto:<?=$arResult["USER"]["EMAIL"]?>"><?=$arResult["USER"]["EMAIL"]?></a></td>
                </tr>
                <tr>
                    <th align="right" colspan="2"><img src="/bitrix/images/1.gif" width="1" height="8"></th>
                </tr>
            <?endif;?>
            <tr>
                <th colspan="2" align="center"><b><?=GetMessage("P_ORDER_USER")?></b></th>
            </tr>
            <tr>
                <td align="right" width="40%"><?=GetMessage("P_ORDER_PERS_TYPE")?>:</td>
                <td width="60%"><?=$arResult["PERSON_TYPE"]["NAME"]?></td>
            </tr>
            <?
            if(!empty($arResult["ORDER_PROPS"]))
            {
                foreach($arResult["ORDER_PROPS"] as $val)
                {
                    if ($val["SHOW_GROUP_NAME"] == "Y")
                    {
                        ?>
                        <tr>
                            <th colspan="2" align="center"><b><?=$val["GROUP_NAME"];?></b></th>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td width="40%" align="right"><?echo $val["NAME"] ?>:</td>
                        <td width="60%"><?
                            if ($val["TYPE"] == "CHECKBOX")
                            {
                                if ($val["VALUE"] == "Y")
                                    echo GetMessage("SALE_YES");
                                else
                                    echo GetMessage("SALE_NO");
                            }
                            else
                                echo $val["VALUE"];
                            ?></td>
                    </tr>
                    <?
                }
            }
            if (mb_strlen($arResult["USER_DESCRIPTION"])>0)
            {
                ?>
                <tr>
                    <th align="right" colspan="2">
                        <img src="/bitrix/images/1.gif" width="1" height="8">
                    </th>
                </tr>
                <tr>
                    <td align="right" width="40%"><?=GetMessage("P_ORDER_USER_COMMENT")?>:</td>
                    <td width="60%"><?=$arResult["USER_DESCRIPTION"]?></td>
                </tr>
                <?
            }
            ?>
            <tr>
                <th align="right" colspan="2">
                    <img src="/bitrix/images/1.gif" width="1" height="8">
                </th>
            </tr>

            <tr>
                <th colspan="2"><b><?=GetMessage("P_ORDER_PAYMENT")?></b></th>
            </tr>
            <tr>
                <td align="right" width="40%"><?=GetMessage("P_ORDER_PAY_SYSTEM")?>:</td>
                <td width="60%"><?
                    if (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
                        echo $arResult["PAY_SYSTEM"]["NAME"];
                    else
                        echo GetMessage("SPOD_NONE");
                    ?></td>
            </tr>
            <tr>
                <td align="right" width="40%"><?echo GetMessage("P_ORDER_PAYED") ?>:</td>
                <td width="60%">
                    <?
                    echo (($arResult["PAYED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
                    if ($arResult["PAYED"] == "Y")
                        echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_PAYED"].")";
                    ?>

                </td>
            </tr>
            <?

            if ($payment["CAN_REPAY"]=="Y"
                && !(isset($arResult["STATUS"])
                    && isset($arResult["STATUS"]["ID"])
                    && $arResult["STATUS"]["ID"] == "FF"))
            {

                ?>
                <tr>
                    <td colspan="2" align="center">
                        <?
                        if ($payment && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y"){
                            ?>
                            <div class="payment-output new-window">
                                <?
                                if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y')
                                {
                                    ?>
                                    <a class="btn btn-info new-window" target="_blank" href="<?=htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE'])?>">
                                        <?= GetMessage('SALE_REPEAT_PAY') ?>
                                    </a>
                                    <?
                                }
                                ?>
                            </div>
                            <?
                        }

                        if ($payment
                            && $payment["PAID"] !== "Y"
                            && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y"
                            && $payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y'
                            && $arResult['CANCELED'] !== 'Y'){
                            ?>
                            <div class="payment-output">
                                <?=$payment['BUFFERED_OUTPUT']?>
                            </div>
                            <?

                        }

                        ?>
                    </td>
                </tr>
                <?
            }
            ?>
            <tr>
                <th align="right" colspan="2">
                    <img src="/bitrix/images/1.gif" width="1" height="8">
                </th>
            </tr>

            <tr>
                <th colspan="2" align="center"><b><?= GetMessage("P_ORDER_DELIVERY")?></b></th>
            </tr>
            <tr>
                <td align="right" width="40%"><?=GetMessage("P_ORDER_DELIVERY")?>:</td>
                <td width="60%"><?
                    if (mb_strpos($arResult["DELIVERY_ID"], ":") !== false || IntVal($arResult["DELIVERY_ID"]) > 0)
                    {
                        echo $arResult["DELIVERY"]["NAME"];
                    }
                    else
                    {
                        echo GetMessage("SPOD_NONE");
                    }
                    ?></td>
            </tr>
            <?php
            $statuses											= array();

            $db_sales_props										= CSaleOrderPropsValue::GetList(
                array("DATE_INSERT" => "ASC",
                    "DATE_UPDATE" => "ASC"),
                array(
                    "CODE"		=> "rupost_spy",
                    "ORDER_ID"	=> $arResult['ID'],
                ),
                false,
                false,
                array()
            );

            $previous_status									= '';

            $ar_sales_props										= array();
            if($db_sales_props
                && is_object($db_sales_props)
                && method_exists($db_sales_props,'Fetch')
                && $ar_sales_props									= $db_sales_props->Fetch()
            ){


                if(isset($ar_sales_props['VALUE'])){

                    ++$at_count;

                    $ar_sales_statuses								= array();

                    $db_sales_statuses								= CSaleOrderPropsVariant::GetList(
                        array("SORT" 		=> "ASC"),
                        array(
                            "CODE"		=> "rupost_spy",
                        ),
                        false,
                        false,
                        array());

                    if($db_sales_statuses
                        && is_object($db_sales_statuses)
                        && method_exists($db_sales_statuses,'Fetch')
                    ){

                        while($ar_sales_statuses_tmp				= $db_sales_statuses->Fetch()){
                            $ar_sales_statuses[$ar_sales_statuses_tmp['VALUE']] = $ar_sales_statuses_tmp['NAME'];
                        }
                    }

                    $statuses['NAME']							= $ar_sales_props['NAME'];
                    $statuses['VALUE']							= $ar_sales_statuses[$ar_sales_props['VALUE']];

                }
            }

            ?>
            <? if(isset($statuses["NAME"]) && isset($statuses["VALUE"])): ?>
                <tr>
                    <td align="right" width="40%"><?=$statuses["NAME"];?>:</td>
                    <td width="60%"><?
                        echo $statuses["VALUE"];
                        ?></td>
                </tr>
            <? endif; ?>
            <tr>
                <th align="right" colspan="2">
                    <img src="/bitrix/images/1.gif" width="1" height="8">
                </th>
            </tr>

            <tr>
                <th colspan="2" align="center"><b><?=GetMessage("P_ORDER_BASKET")?></b></th>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="table table-striped table-hover table-bordered table-prices">
                        <tr>
                            <th><?= GetMessage("SPOD_NAME") ?></th>
                            <th><?= GetMessage("SPOD_PROPS") ?></th>
                            <!-- <th><?= GetMessage("SPOD_DISCOUNT") ?></th> -->
                            <th><?= GetMessage("SPOD_PRICETYPE") ?></th>
                            <th><?= GetMessage("SPOD_QUANTITY") ?></th>
                            <th class="prod-price"><?= GetMessage("SPOD_PRICE") ?></th>
                        </tr>
                        <?
                        foreach($arResult["BASKET"] as $val)
                        {

                            if(!empty($val["PROPS"]))
                                foreach($val["PROPS"] as $kk => $vv)
                                {


                                    if($vv['CODE'] == 'TYPE_OF_PRODUCT'
                                        || $vv['CODE'] == 'MANUFACTURER'
                                        || $vv['CODE'] == 'MODEL'
                                        || $vv['CODE'] == 'BASIC'
                                        || $vv['CODE'] == 'PRODUCT'
                                        || $vv['CODE'] == 'CATALOG'){
                                        unset($val["PROPS"][$kk]);
                                    }

                                }

                            ?>
                            <tr>
                                <td><?
                                    echo htmlspecialcharsEx($val["NAME"]);
                                    ?>
                                </td>
                                <td> <?
                                    if(!empty($val["PROPS"])):?>
                                        <table class="table table-striped table-hover table-properties">
                                            <?
                                            foreach($val["PROPS"] as $vv)
                                            {
                                                ?>
                                                <tr>
                                                    <td style="border:0px; padding:1px;"><?=$vv["NAME"]?>:</td>
                                                    <td style="border:0px; padding:1px;"><?=$vv["VALUE"]?></td>
                                                </tr>
                                                <?
                                            }
                                            ?>
                                        </table>
                                    <?endif;?></td>
                                <!-- <td><?=$val["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>  -->
                                <td><?=htmlspecialcharsEx($val["NOTES"])?></td>
                                <td><?=$val["QUANTITY"]?></td>
                                <td align="right"><?=$val["PRICE_FORMATED"]?></td>
                            </tr>
                            <?
                        }
                        ?>
                        <?if(mb_strlen($arResult["DISCOUNT_VALUE_FORMATED"]) > 0):?>
                            <tr>
                                <td align="right"><b><?=GetMessage("SPOD_DISCOUNT")?>:</b></td>
                                <td align="right" colspan="5"><?=$arResult["DISCOUNT_VALUE_FORMATED"]?></td>
                            </tr>
                        <?endif;?>
                        <?
                        foreach($arResult["TAX_LIST"] as $val)
                        {
                            ?>
                            <tr>
                                <td align="right"><?
                                    echo $val["TAX_NAME"];
                                    echo $val["VALUE_FORMATED"];
                                    ?>:</td>
                                <td align="right" colspan="5"><?=$val["VALUE_MONEY_FORMATED"]?></td>
                            </tr>
                            <?
                        }
                        ?>
                        <?if(mb_strlen($arResult["TAX_VALUE_FORMATED"]) > 0):?>
                            <tr>
                                <td align="right"><b><?=GetMessage("SPOD_TAX")?>:</b></td>
                                <td align="right" colspan="5"><?=$arResult["TAX_VALUE_FORMATED"]?></td>
                            </tr>
                        <?endif;?>
                        <?if(mb_strlen($arResult["PRICE_DELIVERY_FORMATED"]) > 0):?>
                            <tr>
                                <td align="right"><b><?=GetMessage("SPOD_DELIVERY")?>:</b></td>
                                <td align="right" colspan="5"><?=$arResult["PRICE_DELIVERY_FORMATED"]?></td>
                            </tr>
                        <?endif;?>
                        <tr>
                            <td align="right"><b><?=GetMessage("SPOD_ITOG")?>:</b></td>
                            <td align="right" colspan="5"><?=$arResult["PRICE_FORMATED"]?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?else:?>
        <?=ShowError($arResult["ERROR_MESSAGE"]);?>
    <?endif;?>
</div>