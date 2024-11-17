<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y")
    $page = "canceled";
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y")
    $page = "completed";
elseif($_REQUEST["filter_history"] == "Y")
    $page = "all";
else
    $page = "active";
if($arResult['HAS_ARCHIVE'] && $arResult['ARCH_COUNT'] && $arResult['ORDINARY_COUNT']):
    ?>
    <div class="sort tabfilter">
        <span class="order-list-title">
            <?=GetMessage("STPOL_F_NAME")?>
        </span>
        <a class="sortbutton <?if(!$arResult['IS_ARCHIVE']) echo " disabled current"?>" href="<?if($arResult['IS_ARCHIVE']) echo $arResult["CURRENT_PAGE"]."?archive_list=0"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ORDINARY")?></a>
        <a class="sortbutton <?if($arResult['IS_ARCHIVE']) echo " disabled current"?>" href="<?if(!$arResult['IS_ARCHIVE']) echo $arResult["CURRENT_PAGE"]."?archive_list=1"; else echo "javascript:void(0)";?>"><?=GetMessage("STPOL_F_ARCHIVE")?></a>
    </div>
<?
endif;

$bNoOrder = true;
foreach($arResult["ORDERS"] as $key => $val)
{
    //echo '<pre>'; print_r($val); echo '</pre>';
    $bNoOrder 											= false;
    $statuses											= array();

    $db_sales_props										= CSaleOrderPropsValue::GetList(
        array("DATE_INSERT" => "ASC",
            "DATE_UPDATE" => "ASC"),
        array(
            "CODE"		=> "rupost_spy",
            "ORDER_ID"	=> $val['ORDER']['ID'],
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
    <table class="table table-striped table-bordered table-hover table-orders<?if ($val["ORDER"]["CANCELED"] == "Y"):?> canceled<?else: echo " ".toLower($val["ORDER"]["STATUS_ID"]); endif?>">
        <thead>
        <tr>
            <td>
                <span><?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ACCOUNT_NUMBER"]?>&nbsp;<?=GetMessage("STPOL_FROM")?>&nbsp;<?=$val["ORDER"]["DATE_INSERT"]; ?></span>
            </td>
            <td class="tar fwn btn-top" align="center">
                <a class="btn btn-default" title="<?echo GetMessage("STPOL_DETAIL")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>"><?echo GetMessage("STPOL_DETAIL")?></a>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><strong><?echo GetMessage("STPOL_SUM")?></strong> <?=$val["ORDER"]["FORMATED_PRICE"]?></td>
            <td align="center">
                <?if ($val["ORDER"]["CANCELED"] == "Y"):?>
                    <?=$val["ORDER"]["DATE_CANCEL"]?>
                <?else:?>
                    <?=$val["ORDER"]["DATE_STATUS"]?>
                <?endif;?>
            </td>
        </tr>
        <tr>
            <td>
                <strong><?=GetMessage("STPOL_PAYED")?></strong> <?echo (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?>
            </td>
            <td class="order_status" align="center">
                <?if ($val["ORDER"]["CANCELED"] == "Y"):?>
                    <strong><?=GetMessage("STPOL_CANCELED");?></strong>
                <?else:?>
                    <strong><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?></strong>
                <?endif;?>
            </td>
        </tr>
        <tr>
            <td  class="compositionorder">
                <?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0): ?>
                    <p>
                        <strong>
                            <?php echo "".GetMessage("P_PAY_SYS").""; ?>
                        </strong>
                        <?php echo $arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]; ?>
                    </p>
                <?php endif; ?>

                <?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0): ?>
                    <p>
                        <strong>
                            <?php echo "".GetMessage("P_DELIVERY").""; ?>
                        </strong>
                        <?php echo $arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"].""; ?>
                    </p>
                <?php elseif (mb_strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false): ?>
                    <p>
                        <strong>
                            <?php echo "".GetMessage("P_DELIVERY").""; ?>
                        </strong>
                        <?php $arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
                        echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")"."";
                        ?>
                    </p>
                <?php endif; ?>
                <?if(isset($statuses['NAME']) && isset($statuses['VALUE'])):?>
                    <p>
                        <strong>
                            <?php echo $statuses['NAME']; ?>:
                        </strong>
                        <?php echo $statuses['VALUE']; ?>
                    </p>
                <?endif;?>
                <?if(mb_strlen($val["ORDER"]["TRACKING_NUMBER"]) > 0): ?>
                    <p>
                        <strong>
                            <?php echo "".GetMessage("P_ORDER_TRACKING_NUMBER").""; ?>
                        </strong>
                        <?php
                        echo $val["ORDER"]["TRACKING_NUMBER"];
                        ?>
                    </p>
                <?php endif; ?>

                <h4>
                    <?echo GetMessage("STPOL_CONTENT")?>
                </h4>
                <table class="table table-striped table-bordered table-hover order-products-table">
                    <tbody>
                    <?
                    foreach($val["BASKET_ITEMS"] as $vvval)
                    {
                        ?>
                        <tr>
                            <td>
                                <?php echo $vvval["NAME"]; ?>
                            </td>
                            <td>
                                <?php
                                if($vvval["QUANTITY"] > 0):
                                    ?>
                                    <?php echo $vvval["QUANTITY"].GetMessage("STPOL_SHT"); ?>
                                <?php
                                endif;
                                ?>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                </table>
            </td>
            <td align="center">
                <?if (!isset($val["ORDER"]["ARCHIVE_ID"])):?>
                    <?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
                        <a class="bt2 db btn btn-danger btn-order-list" title="<?= GetMessage("STPOL_CANCEL") ?>" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?= GetMessage("STPOL_CANCEL") ?></a><br>
                    <?endif;?>
                    <a class="bt2 db btn btn-primary btn-order-list" title="<?= GetMessage("STPOL_REORDER") ?>" href="<?=str_replace('&COPY','&amp;COPY',$val["ORDER"]["URL_TO_COPY"]);?>"><?= GetMessage("STPOL_REORDER1") ?></a>
                <?endif;?>
            </td>
        </tr>
        </tbody>
    </table>
    <?
}

if ($bNoOrder)
{
    echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"));
}
?>


<?if(mb_strlen($arResult["NAV_STRING"]) > 0):?>
    <div class="navigation"><?=$arResult["NAV_STRING"]?></div>
<?endif?>