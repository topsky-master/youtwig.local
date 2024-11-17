<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="bx_order_list">
    <?if(mb_strlen($arResult["ERROR_MESSAGE"])):?>
        <?=ShowError($arResult["ERROR_MESSAGE"]);?>
    <?endif;?>
    <?if($arParams["SHOW_ORDER_BASKET"]=='Y'):?>
        <table width="100%" cellpadding="8" class="bx_order_list_table_order" style="border-collapse: collapse; border: 1px solid #ccc;">
            <thead>
            <tr style="border-collapse: collapse; border: 1px solid #ccc;">
                <?
                foreach ($arParams["CUSTOM_SELECT_PROPS"] as $headerId):
                    if($headerId == 'PICTURE' && in_array('NAME', $arParams["CUSTOM_SELECT_PROPS"]))
                        continue;

                    $colspan = "";
                    if($headerId == 'NAME' && in_array('PICTURE', $arParams["CUSTOM_SELECT_PROPS"]))
                        $colspan = 'colspan="2" ';

                    if($headerId == 'NAME')
                        $colspan .= ' width="65%" ';
                    else
                        $colspan .= ' align="center" ';

                    $headerName = GetMessage('SPOD_'.$headerId);
                    if(mb_strlen($headerName)<=0)
                    {
                        foreach(array_values($arResult['PROPERTY_DESCRIPTION']) as $prop_head_desc):
                            if(array_key_exists($headerId, $prop_head_desc))
                                $headerName = $prop_head_desc[$headerId]['NAME'];
                        endforeach;
                    }
                    ?><th <?=$colspan?> style="border-collapse: collapse; border: 1px solid #ccc;"><?=$headerName?></th><?
                endforeach;
                ?>
                <td style="border-collapse: collapse; border: 1px solid #ccc;">
                    &nbsp;
                </td>
            </tr>
            </thead>
            <tbody>
            <?//echo "<pre>".print_r($arParams['CUSTOM_SELECT_PROPS'], true).print_R($arResult["BASKET"], true)."</pre>"?>
            <?
            foreach($arResult["BASKET"] as $prod):
                ?><tr style="border-collapse: collapse; border: 1px solid #ccc;"><?

                $hasLink = !empty($prod["DETAIL_PAGE_URL"]);
                $actuallyHasProps = is_array($prod["PROPS"]) && !empty($prod["PROPS"]);

                $asHashes = array(':','.','-','!','+','*','~','=');
                $srSalt = md5($asHashes[mt_rand(0,sizeof($asHashes) - 1)].$arResult["USER_ID"]);
                $sSalt = '?&amp;order_id='.$arParams["ID"].'&amp;check_hash='.md5($arResult["USER_ID"].'-'.$arParams["ID"]).':'.$srSalt;
                $detailUrl = $prod["DETAIL_PAGE_URL"].'/reviews/'.$sSalt;

                foreach ($arParams["CUSTOM_SELECT_PROPS"] as $headerId):

                    ?><td <?php if($headerId != "NAME"): ?> align="center" <?php endif; ?> style="border-collapse: collapse; border: 1px solid #ccc;" class="custom"><?

                    if($headerId == "NAME"):

                        if($hasLink):
                            ?><a href="<?=$detailUrl?>" target="_blank"><?
                        endif;
                        ?><?=$prod["NAME"]?><?
                        if($hasLink):
                            ?></a><?
                        endif;

                    elseif($headerId == "QUANTITY"):

                        ?>
                        <?=$prod["QUANTITY"]?>
                        <?if(mb_strlen($prod['MEASURE_TEXT'])):?>
                        <?=$prod['MEASURE_TEXT']?>
                    <?else:?>
                        <?=GetMessage('SPOD_DEFAULT_MEASURE')?>
                    <?endif?>
                    <?

                    else:
                        $headerId = mb_strtoupper($headerId);
                        echo $prod[(mb_strpos($headerId, 'PROPERTY_')===0 ? $headerId."_VALUE" : $headerId)];
                    endif;

                    ?></td><?

                endforeach;

                ?>
                <td align="center" valign="middle" style="border-collapse: collapse; border: 1px solid #ccc;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="bxBlockContentButtonEdge" style="background-color:rgb(142,200,47);border-radius:3px;text-align:center;border-collapse:collapse;table-layout:fixed">
                        <tbody>
                            <tr>
                                <td valign="top">
                                    <a class="bxBlockContentButton" href="<?=$detailUrl;?>" style="display:inline-block;line-height:22px;color:#fff;padding:7px 15px;text-decoration:none;text-align:center;word-wrap:break-word" target="_blank"><?=GetMessage('SPOD_VOTE');?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                </tr><?

            endforeach;
            ?>
            </tbody>
        </table>
    <?endif?>
</div>