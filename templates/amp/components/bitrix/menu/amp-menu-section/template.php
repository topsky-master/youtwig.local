<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <?

    global $APPLICATION;

    $aTitle = GetMessage('TO_CURRENT_SECTION');

    $arResetMenu = array();

    foreach($arResult as $arItem):

        if($arItem["DEPTH_LEVEL"] == 1 || $arItem["DEPTH_LEVEL"] == 3) continue;

        if($arItem["DEPTH_LEVEL"] > 3){
            $arItem["DEPTH_LEVEL"] -= 1;
        }

        $arItem["DEPTH_LEVEL"] -= 1;

        $arResetMenu[] = $arItem;

    endforeach;

    $previousLevel = 0;
    $arResult = $arResetMenu;

    unset($arResetMenu);
    ?>
    <amp-accordion class="amp-category-list" disable-session-states>
    <section>
    <h4>
        <?=$aTitle?>
    </h4>
    <amp-accordion disable-session-states>
        <?
        foreach($arResult as $number => $arItem):

        $additional = "";

        if(isset($arItem['PARAMS']) && !empty($arItem['PARAMS'])){

            foreach($arItem['PARAMS'] as $key => $value){
                if(in_array(mb_strtolower($key),array('rel')))
                    $additional .=  (!empty($additional) ? ' ' : '').$key.'="'.htmlspecialcharsbx($value).'"';
            }
        }

        if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
            <?=str_repeat("</amp-accordion></section>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
        <?  endif; ?>
        <?  if ($arItem["IS_PARENT"]):?>
        <section>
            <h4>
                <?=$arItem["TEXT"]?>
            </h4>
            <amp-accordion disable-session-states>
                <section class="currentsection" expanded>
                    <h4>
                        <?=$arItem["TEXT"]?>
                    </h4>
                    <div>
                        <a href="<?=$arItem["LINK"];?>" class="fbold">
                            <?=GetMessage('TO_CURRENT_SECTION')?>
                        </a>
                    </div>
                </section>
                <?  else:?>
                    <section>
                        <h4>
                            <?=$arItem["TEXT"]?>
                        </h4>
                        <div>
                            <a href="<?=$arItem["LINK"];?>" class="fbold">
                                <?=GetMessage('TO_CURRENT_SECTION')?>
                            </a>
                        </div>
                    </section>
                <? endif; ?>
                <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>
                <? endforeach; ?>
                <? if ($previousLevel > 1)://close last item tags?>
                    <?=str_repeat("</amp-accordion></section>", ($previousLevel-1) );?>
                <? endif?>
        </section>
    </amp-accordion>
<? endif?>