<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$menuid = $this->randString();
?>
<?if (!empty($arResult)):?>

    <nav id="header-top-links<?php echo $menuid; ?>" class="navbar navbar-default navbar-top header-top-links">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-<?php echo $menuid; ?>">
                <span class="sr-only"><?=GetMessage("TOP_CURRENT_MENU"); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse bs-example-navbar-collapse" id="bs-example-navbar-collapse-<?php echo $menuid; ?>">
            <ul class="nav navbar-nav header_links" id="header_links<?php echo $menuid; ?>">
                <?

                $previousLevel = 0;
                foreach($arResult as $arItem):

                $additional = "";

                if(isset($arItem['PARAMS']) && !empty($arItem['PARAMS'])){
                    foreach($arItem['PARAMS'] as $key => $value){
                        if(in_array(mb_strtolower($key),array('rel')))
                            $additional .=  (!empty($additional) ? ' ' : '').$key.'="'.htmlspecialcharsbx($value).'"';
                    }
                }

                ?>

                <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
                <?endif?>

                <?if ($arItem["IS_PARENT"]):?>

                <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                <li class="level-<?=$arItem["DEPTH_LEVEL"];?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-level-selected<?else:?>root-item<?endif?>" <?=$additional;?>><?=$arItem["TEXT"]?></a>
                    <ul class="dropdown-menu">
                        <?else:?>
                        <li class="<?if ($arItem["SELECTED"]):?>level-selected <?endif?>level-<?=$arItem["DEPTH_LEVEL"];?>"><?if ($arItem["DEPTH_LEVEL"] != 3):?><a href="<?=$arItem["LINK"]?>" class="parent" <?=$additional;?>><?=$arItem["TEXT"]?></a><? endif; ?>
                            <ul class="dropdown-m-menu <?if ($arItem["DEPTH_LEVEL"] == 2):?>dropdown-menu dropdown-sub-menu<?endif;?>">
                                <?endif?>

                                <?else:?>

                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                            <li class="level-<?=$arItem["DEPTH_LEVEL"];?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-level-selected<?else:?>root-item<?endif?>" <?=$additional;?>><?=$arItem["TEXT"]?></a>
                            <?else:?>
                                <li class="<?if ($arItem["SELECTED"]):?>level-selected <?endif?>level-<?=$arItem["DEPTH_LEVEL"];?>"><?if ($arItem["DEPTH_LEVEL"] != 3):?><a href="<?=$arItem["LINK"]?>" <?=$additional;?>><?=$arItem["TEXT"]?></a><? endif; ?>
                                    <?endif?>

                                    <?endif?>



                                    <?
                                    if (!$arItem["IS_PARENT"]):
                                    ?>
                                </li>
                            <?
                            endif;

                            ?>


                                <?$previousLevel = $arItem["DEPTH_LEVEL"];?>

                                <?endforeach?>

                                <?if ($previousLevel > 1)://close last item tags?>
                                    <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
                                <?endif?>
                            </ul>
        </div>
    </nav>
    <script type="text/javascript">
        //<!--

        $('#header_links<?php echo $menuid; ?>.nav.navbar-nav').each(function(){
            $('.dropdown-menu, .dropdown-m-menu',this).each(function(){

                if($(this).hasClass('dropdown-sub-menu')){
                    var colCount = $(this).children().length;
                    if(colCount){
                        $(this).addClass('column-' + colCount);
                    };
                };

                var dMenu = $(this).parent().get(0);
                if(!$(dMenu).parent().hasClass('nav')){

                    $(dMenu).addClass('dropdown-submenu');
                    var aLink = $(dMenu).children('a').first().get(0);
                    $(aLink).after('<button data-toggle="dropdown" class="hidden-lg dropdown-toggle navbar-toggle btn btn-default"><span class="sr-only"><?=GetMessage("TOP_CURRENT_MENU"); ?></span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>');

                } else {

                    $(dMenu).addClass('dropdown');
                    var aLink = $(dMenu).children('a').first().get(0);
                    $(aLink).append('<b class="caret"></b>');
                    $(aLink).addClass('dropdown-toggle');
                    //$(aLink).attr("data-toggle","dropdown");

                };

            });

        });

        $('#header_links<?php echo $menuid; ?> .dropdown-toggle').on('click', function(event) {

            event.preventDefault();
            event.stopPropagation();

            if($(this).parent().hasClass('open')){

                $(this).parent().removeClass('open');
                $(this).addClass('collapsed');
                $(this).attr("aria-expanded",false);

            } else {

                $(this).parent().addClass('open');
                $(this).removeClass('collapsed');
                $(this).attr("aria-expanded",true);

            };


        });


        //-->
    </script>
<?endif?>