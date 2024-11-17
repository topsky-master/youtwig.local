<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!empty($arResult["ORDER"]))
{
    if(isset($arResult["ORDER"]["PAY_SYSTEM_ID"])
        && !empty($arResult["ORDER"]["PAY_SYSTEM_ID"])){
        setOrderAjaxVoting($arResult["ORDER"]["ID"]);

    }

    saoChangeTypeId($arResult["ORDER"]["ID"]);
    $order = \Bitrix\Sale\Order::load($arResult["ORDER"]["ID"]);

    $statusId = 'N';

    if($order){
        $statusId = $order->GetField('STATUS_ID');
    }

    ?>
    <p><b><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></b></p>
    <table class="sale_order_full_table confirm_order">
        <tr>
            <td>
                <p><?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?></p>
                <p><?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?></p>
            </td>
        </tr>
    </table>
    <?
    if (!empty($arResult["PAY_SYSTEM"])
        && $statusId != 'FF')
    {
        ?>
        <table class="sale_order_full_table confirm_order_payment">
            <tr>
                <td class="ps_logo">
                    <div class="pay_name"><?=GetMessage("SOA_TEMPL_PAY")?></div>
                    <?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
                    <div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div>
                </td>
            </tr>
            <?
            if (mb_strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
            {
                ?>
                <tr>
                    <td>
                        <?
                        if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
                        {
                            ?>
                            <script language="JavaScript">
                                window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
                            </script>
                        <?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
                        <?
                        if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
                        {
                        ?>
                            <p><?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?></p>
                        <?
                        }
                        }
                        else
                        {
                        if (mb_strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
                        {
                        ?>
                            <div id="payment-wrapper" class="payment-wrapper">
                                <?php
                                include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
                                ?>
                            </div>
                            <?php
                        }
                        }
                        ?>

                        <?if($arResult["PAY_SYSTEM"]["ACTION_FILE"] === "tinkoff") { echo '<div id="payment-wrapper" class="payment-wrapper">'.$arResult["PAY_SYSTEM"]["BUFFERED_OUTPUT"].'</div>'; }; ?>

                    </td>
                </tr>
                <?
            }
            ?>
        </table>
        <?
    }
}
else
{
    ?>
    <p><b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b></p>
    <table class="sale_order_full_table">
        <tr>
            <td>
                <?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
                <?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
            </td>
        </tr>
    </table>
    <?
}
?>