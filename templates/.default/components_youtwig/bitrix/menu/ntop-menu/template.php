<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):

	$bhSearch = false;
	$dobj = new \Bitrix\Conversion\Internals\MobileDetect;
	if($dobj->isMobile()){
		$bhSearch = true;
	}	
?>
    <nav id="header-top-links" class="navbar navbar-default navbar-top header-top-links">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-">
                <i class="fa fa-bars" aria-hidden="true">
                </i>
            </button>
        </div>
        <div class="collapse navbar-collapse bs-example-navbar-collapse" id="bs-example-navbar-collapse-">
            <ul class="nav navbar-nav" id="hls">
                <?

                $previousLevel = 0;
                foreach($arResult as $arItem):

                $additional = "";

                if(isset($arItem['PARAMS']) && !empty($arItem['PARAMS'])){
                    foreach($arItem['PARAMS'] as $key => $value){
                        //if(!in_array(mb_strtolower($key),array('rel')))
                            //$additional .=  (!empty($additional) ? ' ' : '').$key.'="'.htmlspecialcharsbx($value).'"';
                    }
                }

                ?>

                <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
                <?endif?>

                <?if ($arItem["IS_PARENT"]):?>

                <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                <li class="lv-<?=$arItem["DEPTH_LEVEL"];?>"><label for="searchboxchk" class="hidden-sm hidden-md hidden-lg" id="search-open"><span class="glyphicon glyphicon-search"></span></label>
					<a data-title="<?=$arItem["TEXT"]?>" href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>rlvs<?else:?>rit<?endif?>" <?=$additional;?>>
						<?=$arItem["TEXT"]?>
					</a>
                    <ul class="dropdown-menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
                        <?else:?>
                        <li class="<?if ($arItem["SELECTED"]):?>lvs <?endif?>lv-<?=$arItem["DEPTH_LEVEL"];?>"><?if ($arItem["DEPTH_LEVEL"] != 3):?><a itemprop="url" href="<?=$arItem["LINK"]?>" class="parent" <?=$additional;?>><?=$arItem["TEXT"]?></a><? endif; ?>
                            <ul class="dropdown-m-menu <?if ($arItem["DEPTH_LEVEL"] == 2):?>dropdown-menu dropdown-sub-menu<?endif;?>">
                                <?endif?>

                                <?else:?>

                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                            <li class="lv-<?=$arItem["DEPTH_LEVEL"];?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>rlvs<?else:?>rit<?endif?>" <?=$additional;?>><?=$arItem["TEXT"]?></a>
                            <?else:?>
                                <li class="<?if ($arItem["SELECTED"]):?>lvs <?endif?>lv-<?=$arItem["DEPTH_LEVEL"];?>"><?if ($arItem["DEPTH_LEVEL"] != 3):?><a href="<?=$arItem["LINK"]?>" <?=$additional;?>><?=$arItem["TEXT"]?></a><? endif; ?>
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
<?endif?>