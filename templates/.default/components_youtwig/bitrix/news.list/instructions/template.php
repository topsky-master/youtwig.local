<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$eorder 		 =	(isset($_REQUEST['SORT_ORDER1']) && in_array($_REQUEST['SORT_ORDER1'],array('ASC','DESC'))) ? $_REQUEST['SORT_ORDER1'] : 'ASC';
$eproperties 	 =  array("model", "instruction", "products", "manufacturer", "type_of_product");
$sproperties 	 =  array("PROPERTY_model", "PROPERTY_instruction", "PROPERTY_products", "PROPERTY_manufacturer","PROPERTY_type_of_product");
$esort			 =  (isset($_REQUEST['SORT_BY1']) && in_array($_REQUEST['SORT_BY1'],$sproperties)) ? ($_REQUEST['SORT_BY1']) : 'ACTIVE_FROM';

$uri 			 = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
$uri			 = (mb_strpos($uri,'?') === false) ? ($uri.'?') : $uri;
$uri			 = preg_replace('~[&]*?SORT_ORDER1=[^&]+~i','',$uri);
$uri			 = preg_replace('~[&]*?SORT_BY1=[^&]+~i','',$uri);


?>
<h2>
    <?php echo GetMessage('CT_BNL_INSTRUCTIONS_TO_PRODUCTS'); ?>
</h2>
<?php if(is_array($arResult["ITEMS"]) && !empty($arResult["ITEMS"])){ ?>
    <?if($arParams["DISPLAY_TOP_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
    <div class="panel panel-default instructions">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>
                    <strong>
                        <a href="<?php echo $uri; ?>&SORT_BY1=PROPERTY_type_of_product&SORT_ORDER1=<?php if($eorder == 'ASC'):?>DESC<?php else: ?>ASC<?php endif;?>" rel="nofollow">
                            <?php if($eorder == 'ASC'):?>
                                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            <?php endif; ?>
                            <?php echo GetMessage('CT_BNL_PRODUCT_TYPE'); ?>
                        </a>
                    </strong>
                </th>
                <th>
                    <strong>
                        <a href="<?php echo $uri; ?>&SORT_BY1=PROPERTY_manufacturer&SORT_ORDER1=<?php if($eorder == 'ASC'):?>DESC<?php else: ?>ASC<?php endif;?>" rel="nofollow">
                            <?php if($eorder == 'ASC'):?>
                                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            <?php endif; ?>
                            <?php echo GetMessage('CT_BNL_MANUFACTURER'); ?>
                        </a>
                    </strong>
                </th>
                <th>
                    <strong>
                        <a href="<?php echo $uri; ?>&SORT_BY1=PROPERTY_model&SORT_ORDER1=<?php if($eorder == 'ASC'):?>DESC<?php else: ?>ASC<?php endif;?>" rel="nofollow">
                            <?php if($eorder == 'ASC'):?>
                                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            <?php endif; ?>
                            <?php echo GetMessage('CT_BNL_MODEL'); ?>
                        </a>
                    </strong>
                </th>
                <th>
                    <strong>
                        <?php echo GetMessage('CT_BNL_INSTRUCTIONS'); ?>
                    </strong>
                </th>
                <th>
                    <strong>
                        <?php echo GetMessage('CT_BNL_PRODUCTS'); ?>
                    </strong>
                </th>
            </tr>
            </thead>
            <tbody>
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <tr>
                    <td>
                        <?php
                        if(isset($arItem["PROPERTIES"]["type_of_product"]["VALUE"]) && !empty($arItem["PROPERTIES"]["type_of_product"]["VALUE"])){
                            echo $arItem["PROPERTIES"]["type_of_product"]["VALUE"];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if(isset($arItem["PROPERTIES"]["manufacturer"]["VALUE"]) && !empty($arItem["PROPERTIES"]["manufacturer"]["VALUE"])){
                            echo $arItem["PROPERTIES"]["manufacturer"]["VALUE"];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if(isset($arItem["PROPERTIES"]["model"]["VALUE"]) && !empty($arItem["PROPERTIES"]["model"]["VALUE"])){
                            echo $arItem["PROPERTIES"]["model"]["VALUE"];
                        }
                        ?>
                    </td>
                    <td>
                        <?php if(isset($arItem["PROPERTIES"])
                            && isset($arItem["PROPERTIES"]["instruction"])
                            && isset($arItem["PROPERTIES"]["instruction"]["VALUE"])
                        ){

                            $file										=  CFile::GetFileArray($arItem["PROPERTIES"]["instruction"]["VALUE"]);
                            if($file
                                && isset($file["SRC"])
                                && is_file($_SERVER["DOCUMENT_ROOT"].$file["SRC"])
                                && is_readable($_SERVER["DOCUMENT_ROOT"].$file["SRC"])
                            ){

                                $pathinfo								= pathinfo($_SERVER["DOCUMENT_ROOT"].$file["SRC"]);
                                $file_size								= filesize($_SERVER["DOCUMENT_ROOT"].$file["SRC"]);
                                $sz 									= 'BKMGTP';
                                $factor 								= floor((mb_strlen((string)($file_size)) - 1) / 3);
                                $file_size								= sprintf("%.2f", $file_size / pow(1024, $factor)) . (isset($sz[$factor]) ? $sz[$factor] : '') ;
                                $download_name							= $arItem['CODE'];

                                if(isset($pathinfo["extension"]) && !empty($pathinfo["extension"])){
                                    $download_name						.= '.'.$pathinfo["extension"];
                                };



                                ?>
                                <a href="<?php echo $file["SRC"]; ?>" download="<?php echo $download_name; ?>" class="download_link">
                                    <?php echo GetMessage('CT_BNL_DOWNLOAD'); ?> <?php echo mb_strtolower($pathinfo["extension"]);?> <?php echo $file_size;?>
                                </a>
                                <?php
                            };
                        }; ?>
                    </td>
                    <td>
                        <?php

                        if(isset($arItem["DISPLAY_PROPERTIES"])
                            && isset($arItem["DISPLAY_PROPERTIES"]["products"])
                            && isset($arItem["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])
                        )	{
                            ?>
                            <ul class="products">
                                <?php

                                if(!is_array($arItem["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"])){
                                    $arItem["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"] = array($arItem["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"]);
                                };

                                foreach($arItem["DISPLAY_PROPERTIES"]["products"]["DISPLAY_VALUE"] as $next){
                                    ?>
                                    <li>
                                        <?php echo preg_replace('~<a~is','<a target="_blank" ', $next); ?>
                                    </li>

                                    <?php
                                }?>
                            </ul>
                        <?php 	}; ?>
                    </td>
                </tr>
            <?endforeach;?>
            </tbody>
        </table>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
<?php } else { ?>
    <div class="alert alert-warning alert-dismissible fade in" role="alert"> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">
                ×
            </span>
        </button>
        <?php echo GetMessage('CT_BNL_NO_RESULTS'); ?>
    </div>
<?php } ?>
