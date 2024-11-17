<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"]);
$strNavQueryStringFull = ($arResult["NavQueryString"]);

$removeParams = 'PAGEN,CLEAR_CACHE,SECTION_CODE,ELEMENT_CODE,SECTION_CODE_PATH,CODE,BACKURL,BXAJAXID,SET_FILTER,ACTION,BXRAND,SECTION_ID,ARRFILTER_,AJAX,RESETFILTER,LID';

if(mb_stripos($removeParams,',') !== false){
    $removeParams = explode(',',$removeParams);
} else {
    $removeParams = array($removeParams);
}

$removeParams = array_map('trim',$removeParams);
$removeParams = array_unique($removeParams);

$arQuery = array();

if(!empty($strNavQueryString)){
    mb_parse_str(htmlspecialcharsback($strNavQueryString), $arQuery);
    foreach ($arQuery as $k => $v) {
        foreach($removeParams as $deleteParam){
            if(mb_stripos($k,$deleteParam) !== false){
                unset($arQuery[$k]);
            }
        }

        if((mb_stripos(htmlspecialcharsback($k),'/') !== false)){
            unset($arQuery[$k]);
        }
    }
}

if(!empty($arQuery)){
    $strNavQueryString = http_build_query($arQuery, '', '&');
} else {
    $strNavQueryString = "";
}

$strNavQueryString = ($strNavQueryString != "" ? ('?&'.$strNavQueryString) : "");

$arQuery = array();

if(!empty($strNavQueryStringFull)){
    mb_parse_str(htmlspecialcharsback($strNavQueryStringFull), $arQuery);
    foreach ($arQuery as $k => $v) {
        foreach($removeParams as $deleteParam){
            if(mb_stripos($k,$deleteParam) !== false){
                unset($arQuery[$k]);
            }
        }

        if((mb_stripos(htmlspecialcharsback($k),'/') !== false)){
            unset($arQuery[$k]);
        }
    }
}

if(!empty($arQuery)){
    $strNavQueryStringFull = http_build_query($arQuery, '', '&');
} else {
    $strNavQueryStringFull = "";
}

$strNavQueryStringFull = ($strNavQueryStringFull != "" ? ('?&'.$strNavQueryStringFull) : "");

$prev_found = false;
$next_found = false;

$arResult["sUrlPath"] = preg_replace('#(/pages([\d]*?)-([\d]+))#is', '', $arResult["sUrlPath"]);
$arResult["sUrlPath"] = '/'.trim(preg_replace('~[^/]*?$~','',$arResult["sUrlPath"]),'/').'/';
$arResult["sUrlPath"] .= (isset($_GET['BRAND_SMART_FILTER_PATH']) ? trim(trim($_GET['BRAND_SMART_FILTER_PATH'],'/').'/') : '');

$pageURL = 'pages'.(!in_array($arResult["NavNum"],array(1)) ? $arResult["NavNum"] : '').'-';

if($arResult["NavPageCount"] > 1)
{

    ?>
    <div class="blog-page-navigation">
        <ul class="pagination"><?
            if($arResult["bDescPageNumbering"] === true):
                $bFirst = true;
                if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
                    if($arResult["bSavePage"]):
                        ?><li>
                        <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]+1)?>/<?=$strNavQueryString?>">
                            <?=GetMessage("nav_prev")?>
                        </a>
                        </li><?
                    else:
                        if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):
                            ?><li>
                            <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">
                                <?=GetMessage("nav_prev")?>
                            </a>
                            </li><?
                        else:
                            ?><li>
                            <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]+1)?>/<?=$strNavQueryString?>">
                                <?=GetMessage("nav_prev")?>
                            </a>
                            </li><?
                        endif;
                    endif;
                    ?><?

                    if ($arResult["nStartPage"] < $arResult["NavPageCount"]):
                        $bFirst = false;
                        if($arResult["bSavePage"]):
                            ?><li class="hidden-xs">
                            <a class="blog-page-first" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=$arResult["NavPageCount"]?>/<?=$strNavQueryString?>">
                                1
                            </a>
                            </li><?
                        else:
                            ?><li class="hidden-xs">
                            <a class="blog-page-first" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">
                                1
                            </a>
                            </li><?
                        endif;
                        ?><?
                        if ($arResult["nStartPage"] < ($arResult["NavPageCount"] - 1)):
                            ?><li class="disabled hidden-xs">
	<span>
		...
	</span>
                        </li><?
                        endif;
                    endif;
                endif;
                do
                {
                    $NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;

                    if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
                        ?><li class="disabled hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
				<span>
					<?=$NavRecordGroupPrint?>
				</span>
                        </a>
                        </li><?
                    elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
                            <?=$NavRecordGroupPrint?>
                        </a>
                        </li><?
                    else:
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=$arResult["nStartPage"]?>/<?=$strNavQueryString?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
                            <?=$NavRecordGroupPrint?>
                        </a>
                        </li><?
                    endif;
                    ?><?

                    $arResult["nStartPage"]--;
                    $bFirst 						= false;


                } while($arResult["nStartPage"] >= $arResult["nEndPage"]);

                if ($arResult["NavPageNomer"] > 1):
                    if ($arResult["nEndPage"] > 1):
                        if ($arResult["nEndPage"] > 2):
                            ?><li class="disabled hidden-xs">
	<span>
		...
	</span>
                        </li><?
                        endif;
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$pageURL;?>1/<?=$strNavQueryString?>">
                            <?=$arResult["NavPageCount"]?>
                        </a>
                        </li><?
                    endif;

                    ?><li>
                    <a class="blog-page-next"href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]-1)?>/<?=$strNavQueryString?>">
                        <?=GetMessage("nav_next")?>
                    </a>
                    </li><?
                endif;

            else:
                $bFirst = true;

                if ($arResult["NavPageNomer"] > 1):
                    if($arResult["bSavePage"]):
                        ?><li>
                        <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]-1)?>/<?=$strNavQueryString?>">
                            <?=GetMessage("nav_prev")?>
                        </a>
                        </li><?
                    else:
                        if ($arResult["NavPageNomer"] > 2):
                            ?><li>
                            <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]-1)?>/<?=$strNavQueryString?>">
                                <?=GetMessage("nav_prev")?>
                            </a>
                            </li><?
                        else:
                            ?><li>
                            <a class="blog-page-previous" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">
                                <?=GetMessage("nav_prev")?>
                            </a>
                            </li><?
                        endif;

                    endif;
                    ?><?

                    if ($arResult["nStartPage"] > 1):
                        $bFirst = false;
                        if($arResult["bSavePage"]):
                            ?><li class="hidden-xs">
                            <a class="blog-page-first" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?>1/<?=$strNavQueryString?>">
                                1
                            </a>
                            </li><?
                        else:
                            ?><li class="hidden-xs">
                            <a class="blog-page-first" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">
                                1
                            </a>
                            </li><?
                        endif;
                        ?><?
                        if ($arResult["nStartPage"] > 2):
                            ?><li class="disabled hidden-xs">
	<span>
		...
	</span>
                        </li><?
                        endif;
                    endif;
                endif;

                do
                {
                    if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
                        ?><li class="active hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
                        <span>
                            <?=$arResult["nStartPage"]?>
                        </span>
                        </a>
                        </li><?
                    elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
                            <?=$arResult["nStartPage"]?>
                        </a>
                        </li><?
                    else:
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=$arResult["nStartPage"]?>/<?=$strNavQueryString?>" class="<?=($bFirst ? "blog-page-first" : "")?>">
                            <?=$arResult["nStartPage"]?>
                        </a>
                        </li><?
                    endif;
                    ?><?

                    $arResult["nStartPage"]++;
                    $bFirst = false;



                } while($arResult["nStartPage"] <= $arResult["nEndPage"]);

                if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
                    if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
                        if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
                            ?><li class="disabled hidden-xs">
	<span>
		...
	</span>
                        </li><?
                        endif;
                        ?><li class="hidden-xs">
                        <a href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=$arResult["NavPageCount"]?>/<?=$strNavQueryString?>">
                            <?=$arResult["NavPageCount"]?>
                        </a>
                        </li><?
                    endif;
                    ?><li>
                    <a class="blog-page-next" href="<?=$arResult["sUrlPath"]?><?=$pageURL;?><?=($arResult["NavPageNomer"]+1)?>/<?=$strNavQueryString?>">
                        <?=GetMessage("nav_next")?>
                    </a>
                    </li><?
                endif;
            endif;

            if ($arResult["bShowAll"]):
                if ($arResult["NavShowAll"]):
                    ?><li class="hidden-xs">
                    <a class="blog-page-pagen" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryString?>&SHOWALL_<?=$arResult["NavNum"]?>=0">
                        <?=GetMessage("nav_paged")?>
                    </a>
                    </li><?
                else:
                    ?><li class="hidden-xs">
                    <a class="blog-page-all" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryString?>&SHOWALL_<?=$arResult["NavNum"]?>=1">
                        <?=GetMessage("nav_all")?>
                    </a>
                    </li><?
                endif;
            endif
            ?></ul>
    </div>
    <?
}
?>