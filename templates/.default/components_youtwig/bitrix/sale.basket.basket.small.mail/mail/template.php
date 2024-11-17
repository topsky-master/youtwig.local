<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$hasAnyBuy = false;

if ($arResult["ShowReady"]=="Y" || $arResult["ShowDelay"]=="Y" || $arResult["ShowSubscribe"]=="Y" || $arResult["ShowNotAvail"]=="Y")
{

    $codes = array();

	if(isset($arResult["ITEMS"]["AnDelCanBuy"])
        && is_array($arResult["ITEMS"]["AnDelCanBuy"])
        && !empty($arResult["ITEMS"]["AnDelCanBuy"])) {

        foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $rowNumber => $cartItem) {
            if (isset($cartItem['PRODUCT_ID'])
                && !empty($cartItem['PRODUCT_ID'])) {

                $product_id = (int)$cartItem['PRODUCT_ID'];



                $can_buy = canYouBuy($product_id);
                $quantity = get_quantity_product($product_id);
                $can_buy = $quantity > 0 ? $can_buy : false;

                if (!$can_buy) {
                    unset($arResult["ITEMS"]["AnDelCanBuy"][$rowNumber]);
                } else {
                    $hasAnyBuy = true;
                }

            }
        }

    }


    if(isset($arResult['GRID']['ROWS'])
        && is_array($arResult['GRID']['ROWS'])
        && !empty($arResult['GRID']['ROWS'])){




        $arResult['FUSER_ID'] = current($arResult['GRID']['ROWS'])['FUSER_ID'];
        $strHashes = array(':','.','-','!','+','*','~','=');
        $hashes = '&FUSER_ID='.$arResult['FUSER_ID'].'&check_hash='.md5($arResult['FUSER_ID'].'-'.mt_rand(0,5)).':'.md5($strHashes[mt_rand(0,sizeof($strHashes))].$arResult['FUSER_ID']);
        $arParams["PATH_TO_ORDER"] = ((mb_stripos($arParams["PATH_TO_ORDER"], '?') === false) ? ($arParams["PATH_TO_ORDER"].'?') : $arParams["PATH_TO_ORDER"]).$hashes;

    }

    foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
        $arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
        if ($arHeader["name"] == '')
        {
            $arResult["GRID"]["HEADERS"][$id]["name"] = GetMessage("SALE_".$arHeader["id"]);
            if(mb_strlen($arResult["GRID"]["HEADERS"][$id]["name"])==0)
                $arResult["GRID"]["HEADERS"][$id]["name"] = GetMessage("SALE_".str_replace("_FORMATED", "", $arHeader["id"]));
        }
    endforeach;

    if($hasAnyBuy){


        ?><table class="sale_basket_small" width="100%" cellspacing="0" cellpadding="0"><?
        if ($arResult["ShowReady"]=="Y")
        {
            ?><tr><td align="center"><? echo GetMessage("TSBS_READY"); ?></td></tr>
            <tr><td><br /><br /><table width="100%" cellpadding="4" cellspacing="0" align="center" border="1" style="border-collapse: collapse; border: 1px solid #ddd;"><?

                    ?><thead>
                    <tr><?

                        $counter = 0;

                        foreach ($arResult["ITEMS"]["AnDelCanBuy"] as &$v) {
                            foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader) {
                                if(isset($v[$arHeader['id']]) && !empty($v[$arHeader['id']]) && trim($arHeader['name']) != "")
                                {


                                    ?><th<? if($counter == 0): ?> width="55%"<? endif; ?> align="center" style="border-collapse: collapse; border: 1px solid #ddd;"><?=$arHeader['name']; ?></th><?


                                    ++$counter;

                                }

                            }

                            break;
                        }
                        ?>
                    </tr>
                    </thead><?

                    foreach ($arResult["ITEMS"]["AnDelCanBuy"] as &$v)
                    {
                        ?><tr><?
                        foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
                        {
                            if(isset($v[$arHeader['id']]) && !empty($v[$arHeader['id']]))
                            {
                                if(in_array($arHeader['id'], array("NAME")))
                                {
                                    if ('' != $v["DETAIL_PAGE_URL"])
                                    {
                                        ?><td width="55%" align="left" style="border-collapse: collapse; border: 1px solid #ddd;"><a href="<?echo $v["DETAIL_PAGE_URL"]; ?>"><b><?echo $v[$arHeader['id']]?></b></a></td><?
                                    }
                                    else
                                    {
                                        ?><td width="55%" align="center" style="border-collapse: collapse; border: 1px solid #ddd;"><b><?echo $v[$arHeader['id']]?></b></td><?
                                    }
                                }
                                else if(in_array($arHeader['id'], array("PRICE_FORMATED")))
                                {
                                    ?><td align="center" style="border-collapse: collapse; border: 1px solid #ddd;"><b><?echo $v[$arHeader['id']]?></b></td><?
                                }
                                else if($arHeader['name'] != "")
                                {
                                    ?><td align="center" style="border-collapse: collapse; border: 1px solid #ddd;"><?echo $v[$arHeader['id']]?></td><?
                                }
                            }
                        }
                        ?></tr><?
                    }
                    if (isset($v))
                        unset($v);
                    ?></table><br /><br /></td></tr><?
            if ('' != $arParams["PATH_TO_ORDER"])
            {
                ?><tr><td align="center">

                </td></tr><?
            }
        }
        if ($arResult["ShowDelay"]=="Y")
        {
            ?><tr><td align="center"><?= GetMessage("TSBS_DELAY") ?></td></tr>
            <tr><td><ul>
                    <?
                    foreach ($arResult["ITEMS"]["DelDelCanBuy"] as &$v)
                    {
                        ?><li><?
                        foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
                        {
                            if(isset($v[$arHeader['id']]) && !empty($v[$arHeader['id']]))
                            {
                                if(in_array($arHeader['id'], array("NAME")))
                                {
                                    if ('' != $v["DETAIL_PAGE_URL"])
                                    {
                                        ?><a href="<?echo $v["DETAIL_PAGE_URL"]; ?>"><b><?echo $v[$arHeader['id']]?></b></a><br /><?
                                    }
                                    else
                                    {
                                        ?><b><?echo $v[$arHeader['id']]?></b><br /><?
                                    }
                                }
                                else if(in_array($arHeader['id'], array("PRICE_FORMATED")))
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<b><?echo $v[$arHeader['id']]?></b><br /><?
                                }
                                else if($arHeader['name'] != "")
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<?echo $v[$arHeader['id']]?><br /><?
                                }
                            }
                        }
                        ?></li><?
                    }
                    if (isset($v))
                        unset($v);
                    ?></ul></td></tr><?
            if ('' != $arParams["PATH_TO_BASKET"])
            {
                ?><tr><td align="center"><a href="<?=$arParams["PATH_TO_BASKET"]?>"><?= GetMessage("TSBS_2BASKET") ?></a>
                </td></tr><?
            }
        }
        if ($arResult["ShowSubscribe"]=="Y")
        {
            ?><tr><td align="center"><?= GetMessage("TSBS_SUBSCRIBE") ?></td></tr>
            <tr><td><ul><?
                    foreach ($arResult["ITEMS"]["ProdSubscribe"] as &$v)
                    {
                        ?><li><?
                        foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
                        {
                            if(isset($v[$arHeader['id']]) && !empty($v[$arHeader['id']]))
                            {
                                if(in_array($arHeader['id'], array("NAME")))
                                {
                                    if ('' != $v["DETAIL_PAGE_URL"])
                                    {
                                        ?><a href="<?echo $v["DETAIL_PAGE_URL"]; ?>"><b><?echo $v[$arHeader['id']]?></b></a><br /><?
                                    }
                                    else
                                    {
                                        ?><b><?echo $v[$arHeader['id']]?></b><br /><?
                                    }
                                }
                                else if(in_array($arHeader['id'], array("PRICE_FORMATED")))
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<b><?echo $v[$arHeader['id']]?></b><br /><?
                                }
                                else if($arHeader['name'] != "")
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<?echo $v[$arHeader['id']]?><br /><?
                                }
                            }
                        }
                        ?></li><?
                    }
                    if (isset($v))
                        unset($v);
                    ?></ul></td></tr><?
        }
        if ($arResult["ShowNotAvail"]=="Y")
        {
            ?><tr><td align="center"><?= GetMessage("TSBS_UNAVAIL") ?></td></tr>
            <tr><td><ul><?
                    foreach ($arResult["ITEMS"]["nAnCanBuy"] as &$v)
                    {
                        ?><li><?
                        foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
                        {
                            if(isset($v[$arHeader['id']]) && !empty($v[$arHeader['id']]))
                            {
                                if(in_array($arHeader['id'], array("NAME")))
                                {
                                    if ('' != $v["DETAIL_PAGE_URL"])
                                    {
                                        ?><a href="<?echo $v["DETAIL_PAGE_URL"]; ?>"><b><?echo $v[$arHeader['id']]?></b></a><br /><?
                                    }
                                    else
                                    {
                                        ?><b><?echo $v[$arHeader['id']]?></b><br /><?
                                    }
                                }
                                else if(in_array($arHeader['id'], array("PRICE_FORMATED")))
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<b><?echo $v[$arHeader['id']]?></b><br /><?
                                }
                                else if($arHeader['name'] != "")
                                {
                                    ?><?= $arHeader['name']?>:&nbsp;<?echo $v[$arHeader['id']]?><br /><?
                                }
                            }
                        }
                        ?></li><?
                    }
                    if (isset($v))
                        unset($v);
                    ?></ul></td></tr><?
        }
        ?></table><?

        ?>
        <table align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#8ec82f" style="background-color:rgb(142,200,47);border-radius:3px;text-align:center;border-collapse:collapse;table-layout:fixed">
            <tbody>
            <tr>
                <td valign="top">
                    <a href="<?=$arParams["PATH_TO_ORDER"]?>" style="display:inline-block;line-height:22px;color:#fff;padding:7px 15px;text-decoration:none;text-align:center;word-wrap:break-word"><?= GetMessage("TSBS_2ORDER") ?></a>
                </td>
            </tr>
            </tbody>
        </table>
        <?

    } 

}

if(!$hasAnyBuy){
?>
<hr class="empty_cart" />
<?
}

?>