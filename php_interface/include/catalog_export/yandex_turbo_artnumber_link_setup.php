<?
//<title>Yandex turbo pages artnumber link</title>
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global string $ACTION */
/** @global array $arOldSetupVars */
/** @global int $IBLOCK_ID */
/** @global string $SETUP_FILE_NAME */
/** @global string $SETUP_SERVER_NAME */
/** @global mixed $V */
/** @global mixed $XML_DATA */
/** @global string $SETUP_PROFILE_NAME */
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_setup_templ.php');

global $APPLICATION, $USER;

$arSetupErrors = array();

$strAllowExportPath = COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/");

if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
    if (isset($arOldSetupVars['IBLOCK_ID']))
        $IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
    if (isset($arOldSetupVars['SETUP_FILE_NAME']))
        $SETUP_FILE_NAME = str_replace($strAllowExportPath,'',$arOldSetupVars['SETUP_FILE_NAME']);
    if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
        $SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
    if (isset($arOldSetupVars['V']))
        $V = $arOldSetupVars['V'];
    if (isset($arOldSetupVars['XML_DATA']))
    {
        if (get_magic_quotes_gpc())
            $XML_DATA = base64_encode(stripslashes($arOldSetupVars['XML_DATA']));
        else
            $XML_DATA = base64_encode($arOldSetupVars['XML_DATA']);
    }
    if (isset($arOldSetupVars['SETUP_SERVER_NAME']))
        $SETUP_SERVER_NAME = $arOldSetupVars['SETUP_SERVER_NAME'];
    if (isset($arOldSetupVars['USE_HTTPS']))
        $USE_HTTPS = $arOldSetupVars['USE_HTTPS'];
    if (isset($arOldSetupVars['FILTER_AVAILABLE']))
        $filterAvalable = $arOldSetupVars['FILTER_AVAILABLE'];
    if (isset($arOldSetupVars['DISABLE_REFERERS']))
        $disableReferers = $arOldSetupVars['DISABLE_REFERERS'];

    if (isset($arOldSetupVars['PROPS']))
        $PROPS = $arOldSetupVars['PROPS'];
	
	if (isset($arOldSetupVars['QUALITY']))
        $QUALITY = $arOldSetupVars['QUALITY'];


}



if ($STEP > 1)
{
    $IBLOCK_ID = (int)$IBLOCK_ID;
    $rsIBlocks = CIBlock::GetByID($IBLOCK_ID);
    if ($IBLOCK_ID <= 0 || !($arIBlock = $rsIBlocks->Fetch()))
    {
        $arSetupErrors[] = GetMessage("CET_ERROR_NO_IBLOCK1")." #".$IBLOCK_ID." ".GetMessage("CET_ERROR_NO_IBLOCK2");
    }
    else
    {
        $bRightBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
        if ($bRightBlock)
        {
            $arSetupErrors[] = str_replace('#IBLOCK_ID#',$IBLOCK_ID,GetMessage("CET_ERROR_IBLOCK_PERM"));
        }
    }

    if (mb_strlen($SETUP_FILE_NAME)<=0)
    {
        $arSetupErrors[] = GetMessage("CET_ERROR_NO_FILENAME");
    }
    elseif (preg_match(BX_CATALOG_FILENAME_REG, $strAllowExportPath.$SETUP_FILE_NAME))
    {
        $arSetupErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
    }
    elseif ($APPLICATION->GetFileAccessPermission($strAllowExportPath.$SETUP_FILE_NAME) < "W")
    {
        $arSetupErrors[] = str_replace("#FILE#", $strAllowExportPath.$SETUP_FILE_NAME, GetMessage('CET_YAND_RUN_ERR_SETUP_FILE_ACCESS_DENIED'));
    }

    $SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

    if (empty($arSetupErrors))
    {
        $bAllSections = false;
        $arSections = array();
        if (!empty($V) && is_array($V))
        {
            foreach ($V as $key => $value)
            {
                if (trim($value) == "0")
                {
                    $bAllSections = true;
                    break;
                }
                $value = (int)$value;
                if ($value > 0)
                    $arSections[] = $value;
            }
        }

        if (!$bAllSections && !empty($arSections))
        {
            $arCheckSections = array();
            $rsSections = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'ID' => $arSections), false, array('ID'));
            while ($arOneSection = $rsSections->Fetch())
            {
                $arCheckSections[] = $arOneSection['ID'];
            }
            $arSections = $arCheckSections;
        }

        if (!$bAllSections && empty($arSections))
        {
            $arSetupErrors[] = GetMessage("CET_ERROR_NO_GROUPS");
            $V = array();
        }
    }

    if (is_array($V))
    {
        $V = array_unique(array_values($V));
        $_REQUEST['V'] = $V;
    }

    $arCatalog = CCatalogSku::GetInfoByIBlock($IBLOCK_ID);
    if (CCatalogSku::TYPE_PRODUCT == $arCatalog['CATALOG_TYPE'] || CCatalogSku::TYPE_FULL == $arCatalog['CATALOG_TYPE'])
    {
        if (mb_strlen($XML_DATA) <= 0)
        {
            $arSetupErrors[] = GetMessage('YANDEX_ERR_SKU_SETTINGS_ABSENT');
        }
    }

    if (!isset($USE_HTTPS) || $USE_HTTPS != 'Y')
        $USE_HTTPS = 'N';
    if (!isset($filterAvalable) || $filterAvalable != 'Y')
        $filterAvalable = 'N';
    if (!isset($disableReferers) || $disableReferers != 'Y')
        $disableReferers = 'N';

    if (($ACTION=="EXPORT_SETUP" || $ACTION=="EXPORT_EDIT" || $ACTION=="EXPORT_COPY") && mb_strlen($SETUP_PROFILE_NAME)<=0)
        $arSetupErrors[] = GetMessage("CET_ERROR_NO_PROFILE_NAME");

    if (!empty($arSetupErrors))
    {
        $STEP = 1;
    }
}

$aMenu = array(
    array(
        "TEXT"=>GetMessage("CATI_ADM_RETURN_TO_LIST"),
        "TITLE"=>GetMessage("CATI_ADM_RETURN_TO_LIST_TITLE"),
        "LINK"=>"/bitrix/admin/cat_export_setup.php?lang=".LANGUAGE_ID,
        "ICON"=>"btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);

$context->Show();

if (!empty($arSetupErrors))
    ShowError(implode('<br>', $arSetupErrors));
?>
<!--suppress JSUnresolvedVariable -->
<form method="post" action="<?echo $APPLICATION->GetCurPage() ?>" name="yandex_setup_form" id="yandex_setup_form">
    <?
    $aTabs = array(
        array("DIV" => "yand_edit1", "TAB" => GetMessage("CAT_ADM_MISC_EXP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_MISC_EXP_TAB1_TITLE")),
        array("DIV" => "yand_edit2", "TAB" => GetMessage("CAT_ADM_MISC_EXP_TAB2"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_MISC_EXP_TAB2_TITLE")),
    );

    $tabControl = new CAdminTabControl("tabYandex", $aTabs, false, true);
    $tabControl->Begin();

    $tabControl->BeginNextTab();

    if ($STEP == 1)
    {
        ?><tr>
        <td width="40%"><? echo GetMessage('CET_SELECT_IBLOCK_EXT'); ?></td>
        <td width="60%"><?
            $arIBlockIDs = array();
            $rsCatalogs = CCatalog::GetList(
                array(),
                array('!PRODUCT_IBLOCK_ID' => 0),
                false,
                false,
                array('PRODUCT_IBLOCK_ID')
            );
            while ($arCatalog = $rsCatalogs->Fetch())
            {
                $arCatalog['PRODUCT_IBLOCK_ID'] = (int)$arCatalog['PRODUCT_IBLOCK_ID'];
                if ($arCatalog['PRODUCT_IBLOCK_ID'] > 0)
                    $arIBlockIDs[$arCatalog['PRODUCT_IBLOCK_ID']] = true;
            }
            $rsCatalogs = CCatalog::GetList(
                array(),
                array('PRODUCT_IBLOCK_ID' => 0),
                false,
                false,
                array('IBLOCK_ID')
            );
            while ($arCatalog = $rsCatalogs->Fetch())
            {
                $arCatalog['IBLOCK_ID'] = (int)$arCatalog['IBLOCK_ID'];
                if ($arCatalog['IBLOCK_ID'] > 0)
                    $arIBlockIDs[$arCatalog['IBLOCK_ID']] = true;
            }
            if (empty($arIBlockIDs))
                $arIBlockIDs[-1] = true;
            echo GetIBlockDropDownListEx(
                $IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',
                array(
                    'ID' => array_keys($arIBlockIDs), 'ACTIVE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'
                ),
                "ClearSelected(); BX('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID=0&'+'".bitrix_sessid_get()."';",
                "ClearSelected(); BX('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID='+this[this.selectedIndex].value+'&'+'".bitrix_sessid_get()."';",
                'class="adm-detail-iblock-types"',
                'class="adm-detail-iblock-list"'
            );
            ?>
            <script type="text/javascript">
                var TreeSelected = [];
                <?
                    $intCountSelected = 0;
                    if (isset($V) && !empty($V) && is_array($V))
                    {
                    foreach ($V as $oneKey)
                    {
                    ?>TreeSelected[<? echo $intCountSelected ?>] = <? echo intval($oneKey); ?>;
                <?
                $intCountSelected++;
                }
                }
                ?>
                function ClearSelected()
                {
                    BX.showWait();
                    TreeSelected = [];
                }
            </script>
        </td>
        </tr>
        <tr>
            <td width="40%" valign="top"><?echo GetMessage("CET_SELECT_GROUP");?></td>
            <td width="60%"><?
                if ($intCountSelected)
                {
                    foreach ($V as $oneKey)
                    {
                        $oneKey = (int)$oneKey;
                        ?><input type="hidden" value="<? echo $oneKey; ?>" name="V[]" id="oldV<? echo $oneKey; ?>"><?
                    }
                    unset($oneKey);
                }
                ?><div id="tree"></div>
                <script type="text/javascript">
                    BX.showWait();
                    clevel = 0;

                    function delOldV(obj)
                    {
                        if (!!obj)
                        {
                            var intSelKey = BX.util.array_search(obj.value, TreeSelected);
                            if (obj.checked == false)
                            {
                                if (-1 < intSelKey)
                                {
                                    TreeSelected = BX.util.deleteFromArray(TreeSelected, intSelKey);
                                }

                                var objOldVal = BX('oldV'+obj.value);
                                if (!!objOldVal)
                                {
                                    objOldVal.parentNode.removeChild(objOldVal);
                                    objOldVal = null;
                                }
                            }
                            else
                            {
                                if (-1 == intSelKey)
                                {
                                    TreeSelected[TreeSelected.length] = obj.value;
                                }
                            }
                        }
                    }

                    function buildNoMenu()
                    {
                        var buffer;
                        buffer = '<?echo GetMessageJS("CET_FIRST_SELECT_IBLOCK");?>';
                        BX('tree', true).innerHTML = buffer;
                        BX.closeWait();
                    }

                    function buildMenu()
                    {
                        var i,
                            buffer,
                            imgSpace,
                            space;

                        buffer = '<table border="0" cellspacing="0" cellpadding="0">';
                        buffer += '<tr>';
                        buffer += '<td colspan="2" valign="top" align="left"><input type="checkbox" name="V[]" value="0" id="v0"'+(BX.util.in_array(0,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="v0"><font class="text"><b><?echo CUtil::JSEscape(GetMessage("CET_ALL_GROUPS"));?></b></font></label></td>';
                        buffer += '</tr>';

                        for (i in Tree[0])
                        {
                            if (!Tree[0][i])
                            {
                                space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="V'+i+'"><span class="text">' + Tree[0][i][0] + '</span></label>';
                                imgSpace = '';
                            }
                            else
                            {
                                space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><span class="text"><b>' + Tree[0][i][0] + '</b></span></a>';
                                imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
                            }

                            buffer += '<tr>';
                            buffer += '<td width="20" valign="top" align="center">' + imgSpace + '</td>';
                            buffer += '<td id="node_' + i + '">' + space + '</td>';
                            buffer += '</tr>';
                        }

                        buffer += '</table>';

                        BX('tree', true).innerHTML = buffer;
                        BX.adminPanel.modifyFormElements('yandex_setup_form');
                        BX.closeWait();
                    }

                    function collapse(node)
                    {
                        if (!BX('table_' + node))
                        {
                            var i,
                                buffer,
                                imgSpace,
                                space;

                            buffer = '<table border="0" id="table_' + node + '" cellspacing="0" cellpadding="0">';

                            for (i in Tree[node])
                            {
                                if (!Tree[node][i])
                                {
                                    space = '<input type="checkbox" name="V[]" value="'+i+'" id="V'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><label for="V'+i+'"><font class="text">' + Tree[node][i][0] + '</font></label>';
                                    imgSpace = '';
                                }
                                else
                                {
                                    space = '<input type="checkbox" name="V[]" value="'+i+'"'+(BX.util.in_array(i,TreeSelected) ? ' checked' : '')+' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[node][i][0] + '</b></font></a>';
                                    imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
                                }

                                buffer += '<tr>';
                                buffer += '<td width="20" align="center" valign="top">' + imgSpace + '</td>';
                                buffer += '<td id="node_' + i + '">' + space + '</td>';
                                buffer += '</tr>';
                            }

                            buffer += '</table>';

                            BX('node_' + node).innerHTML += buffer;
                            BX('img_' + node).src = '/bitrix/images/catalog/load/minus.gif';
                        }
                        else
                        {
                            var tbl = BX('table_' + node);
                            tbl.parentNode.removeChild(tbl);
                            BX('img_' + node).src = '/bitrix/images/catalog/load/plus.gif';
                        }
                        BX.adminPanel.modifyFormElements('yandex_setup_form');
                    }
                </script>
                <iframe src="/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID=<?=intval($IBLOCK_ID)?>&<? echo bitrix_sessid_get(); ?>" id="id_ifr" name="ifr" style="display:none"></iframe>
            </td>
        </tr>
        <?php

        //PROPS
        $propRes = CIBlock::GetProperties(11, Array());

        ?>
        <tr>
            <td width="40%">Свойства:</td>
            <td width="60%">
                <select id="PROPS" name="PROPS[]" multiple="multiple">
                <?php
                while ($propArr = $propRes->Fetch()) {
                ?>
                    <option value="<?=$propArr["ID"]; ?>"<?php if(isset($_REQUEST["PROPS"]) && !empty($_REQUEST["PROPS"]) && in_array($propArr["ID"], $_REQUEST["PROPS"])){ ?><?php } elseif(isset($PROPS) && !empty($PROPS) && in_array($propArr["ID"], $PROPS)){ ?> selected="selected"<?php }; ?>><?=$propArr["NAME"]; ?> [<?=$propArr["ID"];?>]</option>
                <?php

                }

                ?>
                </select>
            </td>
        </tr>
		<?php

        //QUALITY

        $propRes = CIBlock::GetProperties(11, Array(), Array("CODE" => "QUALITY"));

        if ($propArr = $propRes->Fetch()) {

            ?>
            <tr>
                <td width="40%"><?=$propArr["NAME"]; ?>:</td>
                <td width="60%">
                    <select id="QUALITY" name="QUALITY[]" multiple="multiple">
                        <?php

                        $property_enums = CIBlockPropertyEnum::GetList(
                            Array(
                                "DEF" => "DESC",
                                "SORT" => "ASC"),
                            Array(
                                "IBLOCK_ID" => 11,
                                "CODE" => "QUALITY")
                        );

                        if ($property_enums)
                            while ($enum_fields = $property_enums->GetNext()) {
                                ?>
                                <option value="<?=$enum_fields["ID"]; ?>"<?php if(isset($_REQUEST["QUALITY"]) && !empty($_REQUEST["QUALITY"]) && in_array($enum_fields["ID"], $_REQUEST["QUALITY"])){ ?><?php } elseif(isset($QUALITY) && !empty($QUALITY) && in_array($enum_fields["ID"], $QUALITY)){ ?> selected="selected"<?php }; ?>><?= $enum_fields["VALUE"]; ?></option>
                                <?php
                            }

                        ?>
                    </select>
                </td>
            </tr>
        <?php }


        ?>
		<tr>
            <td width="40%"><?=GetMessage('CAT_DETAIL_PROPS')?>:</td>
            <td width="60%">
                <script type="text/javascript">
                    function showDetailPopup()
                    {
                        if (!obDetailWindow)
                        {
                            var s = BX('IBLOCK_ID');
                            var dat = BX('XML_DATA');
                            var obDetailWindow = new BX.CAdminDialog({
                                'content_url': '/bitrix/tools/catalog_export/yandex_detail.php?lang=<?=LANGUAGE_ID?>&bxpublic=Y&IBLOCK_ID=' + s[s.selectedIndex].value,
                                'content_post': 'XML_DATA='+BX.util.urlencode(dat.value)+'&'+'<?echo bitrix_sessid_get(); ?>',
                                'width': 900, 'height': 550,
                                'resizable': true
                            });
                            obDetailWindow.Show();
                        }
                    }

                    function setDetailData(data)
                    {
                        BX('XML_DATA').value = data;
                    }
                </script>
                <input type="button" onclick="showDetailPopup(); return false;" value="<? echo GetMessage('CAT_DETAIL_PROPS_RUN'); ?>">
                <input type="hidden" id="XML_DATA" name="XML_DATA" value="<? echo (mb_strlen($XML_DATA) > 0 ? $XML_DATA : ''); ?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><? echo GetMessage('CAT_YANDEX_FILTER_AVAILABLE'); ?></td>
            <td width="60%">
                <input type="hidden" name="FILTER_AVAILABLE" value="N">
                <input type="checkbox" name="FILTER_AVAILABLE" value="Y"<? echo ($filterAvalable == 'Y' ? ' checked' : ''); ?>
            </td>
        </tr>
        <tr>
            <td width="40%"><? echo GetMessage('CAT_YANDEX_USE_HTTPS'); ?></td>
            <td width="60%">
                <input type="hidden" name="USE_HTTPS" value="N">
                <input type="checkbox" name="USE_HTTPS" value="Y"<? echo ($USE_HTTPS == 'Y' ? ' checked' : ''); ?>
            </td>
        </tr>
        <tr>
            <td width="40%"><? echo GetMessage('CAT_YANDEX_DISABLE_REFERERS'); ?></td>
            <td width="60%">
                <input type="hidden" name="DISABLE_REFERERS" value="N">
                <input type="checkbox" name="DISABLE_REFERERS" value="Y"<? echo ($disableReferers == 'Y' ? ' checked' : ''); ?>
            </td>
        </tr>
        <tr>
            <td width="40%"><?echo GetMessage("CET_SERVER_NAME");?></td>
            <td width="60%">
                <input type="text" name="SETUP_SERVER_NAME" value="<?echo (mb_strlen($SETUP_SERVER_NAME)>0) ? htmlspecialcharsbx($SETUP_SERVER_NAME) : '' ?>" size="50" /> <input type="button" onclick="this.form['SETUP_SERVER_NAME'].value = window.location.host;" value="<?echo htmlspecialcharsbx(GetMessage('CET_SERVER_NAME_SET_CURRENT'))?>" />
            </td>
        </tr>
        <tr>
            <td width="40%"><?echo GetMessage("CET_SAVE_FILENAME");?></td>
            <td width="60%">
                <b><? echo htmlspecialcharsbx(COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/"));?></b><input type="text" name="SETUP_FILE_NAME" value="<?echo (mb_strlen($SETUP_FILE_NAME)>0) ? htmlspecialcharsbx($SETUP_FILE_NAME) : "yandex_".mt_rand(0, 999999).".php" ?>" size="50" />
            </td>
        </tr>
        <?
        if ($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
        {
            ?><tr>
            <td width="40%"><?echo GetMessage("CET_PROFILE_NAME");?></td>
            <td width="60%">
                <input type="text" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME) ?>" size="30">
            </td>
            </tr><?
        }
    }

    $tabControl->EndTab();

    $tabControl->BeginNextTab();

    if ($STEP==2)
    {
        $SETUP_FILE_NAME = $strAllowExportPath.$SETUP_FILE_NAME;
        if (mb_strlen($XML_DATA) > 0)
        {
            $XML_DATA = base64_decode($XML_DATA);
        }
        $SETUP_SERVER_NAME = htmlspecialcharsbx($SETUP_SERVER_NAME);
        $_POST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_POST['SETUP_SERVER_NAME']);
        $_REQUEST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_REQUEST['SETUP_SERVER_NAME']);

        $FINITE = true;
    }
    $tabControl->EndTab();

    $tabControl->Buttons();

    ?><? echo bitrix_sessid_post();?><?
    if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
    {
        ?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
    }

    if (2 > $STEP)
    {
        ?><input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
        <input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
        <input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
        <input type="hidden" name="STEP" value="<?echo intval($STEP) + 1 ?>">
        <input type="hidden" name="SETUP_FIELDS_LIST" value="QUALITY,PROPS,V,IBLOCK_ID,SETUP_SERVER_NAME,SETUP_FILE_NAME,XML_DATA,USE_HTTPS,FILTER_AVAILABLE,DISABLE_REFERERS">
        <input type="submit" value="<?echo ($ACTION=="EXPORT")?GetMessage("CET_EXPORT"):GetMessage("CET_SAVE")?>"><?
    }

    $tabControl->End();
    ?></form>
<script type="text/javascript">
    <?if ($STEP < 2):?>
    tabYandex.SelectTab("yand_edit1");
    tabYandex.DisableTab("yand_edit2");
    <?elseif ($STEP == 2):?>
    tabYandex.SelectTab("yand_edit2");
    tabYandex.DisableTab("yand_edit1");
    <?endif;?>
</script>