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

if(is_array($arResult["ITEMS"])
    && !empty(is_array($arResult["ITEMS"]))):

    ?>
    <nav id="mobile-menu" class="hidden-lg hidden-md hidden-sm">
        <div>
            <?=$arResult['MENU_DESCRIPTION'];?>
            <ul>
                <?foreach($arResult["ITEMS"] as $arItem):?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                    $smLink = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'])
                        : '';

                    $smAtribute = isset($arItem['DISPLAY_PROPERTIES'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE'])
                    &&isset($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        ? trim($arItem['DISPLAY_PROPERTIES']['LINK_ATRIBUTE']['VALUE'])
                        : '';

                    ?>
                    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="menu-item-<?=$arItem['ID'];?><?php if(isset($arItem['CODE']) && !empty($arItem['CODE'])): ?> menu-item-<?=mb_strtolower($arItem['CODE']);?><?php endif; ?>">
                        <?if(!empty($smLink)):?>
                        <a href="<?=$smLink;?>" <?=$smAtribute;?>>
                            <?endif;?>
                            <span>
                            <?if($arParams["DISPLAY_PICTURE"]!="N"
                                && is_array($arItem["PREVIEW_PICTURE"])):?>
                                <img class="img-responsive" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" />
                            <?endif?>
                                <?if($arParams["DISPLAY_NAME"]!="N"
                                    && $arItem["NAME"]):?>
                                    <?=$arItem["NAME"];?>
                                <?endif;?>
                                <?php if(isset($arItem['CODE'])
                                    && !empty($arItem['CODE'])): ?>

                                    <?php switch ($arItem['CODE']){
                                        case 'cart':
                                            ?>
                                            <span id="mobilemenu<?=mb_strtolower($arItem['CODE']);?>">
                                                0
                                            </span>
                                            <?php

                                            break;
                                    }; ?>
                                <?php endif; ?>
                            </span>
                            <?if(!empty($smLink)):?>
                        </a>
                    <?endif;?>

                    </li>
                <?endforeach;?>
            </ul>
        </div>
    </nav>
    <script type="text/javascript">
        //<!--
        $(window).load(function() {

            var ajaxCountCartURI = location.protocol + '//' + location.hostname + '/?mobile_cart_count=1'

            $menu = $("#mobile-menu").mmenu({
                navbar: {
                    title: ""
                }
            }, {
                offCanvas: {
                    pageNodetype: "div",
                    pageSelector: "#wrap-page"
                }
            });

            mmAPI = $menu.data( "mmenu" );

            $("#hamburger").bind("click",function(){

                if($("#mobilemenucart").length){
                    $.getJSON( ajaxCountCartURI, function( data ) {

                        $("#mobilemenucart").html(data.mobile_cart_count);

                        if(data.mobile_cart_count){
                            if(!$("#mobilemenucart").parent().parent().hasClass("green-cart")){
                                $("#mobilemenucart").parent().parent().addClass("green-cart");
                            }
                        } else {
                            if($("#mobilemenucart").parent().parent().hasClass("green-cart")){
                                $("#mobilemenucart").parent().parent().removeClass("green-cart");
                            }
                        };
                    });
                };

                if($("#hamburger").hasClass("is-active")){
                    mmAPI.close();
                } else {
                    mmAPI.open();
                };

            });

            mmAPI.bind( "open:finish", function() {
                setTimeout(function() {
                    $("#hamburger").addClass( "is-active" );
                }, 100);
            });

            mmAPI.bind( "close:finish", function() {
                setTimeout(function() {
                    $("#hamburger").removeClass( "is-active" );
                }, 100);
            });

            $(window).on("resize orientationchange", function(){
                try{
                    if($(window).width() > 767
                        && $("#hamburger").hasClass("is-active")){
                        mmAPI.close();
                    };
                } catch(e){

                }
            });

            $("#hamburger").css("opacity","1.0");
			$("#mobile-menu").css({opacity: "1.0", height: "auto", overflow: "visible"});

        });
        //-->
    </script>
<?php endif; ?>