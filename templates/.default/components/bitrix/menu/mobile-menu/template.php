<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <div class="panel-group accordion-menu hidden" id="accordion0" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab">
                <p class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion0" href="#self" data-target="#collapse0" aria-expanded="true" aria-controls="collapse0" class="parent sublink sublinkfirst collapsed">
                        <?=$APPLICATION->GetTitle();?>
                    </a>
                </p>
            </div>
            <div id="collapse0" class="panel-collapse collapse" role="tabpanel">
                <div class="panel-body">
            <?

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

            foreach($arResult as $number => $arItem):

            $additional = "";

            if(isset($arItem['PARAMS']) && !empty($arItem['PARAMS'])){

                foreach($arItem['PARAMS'] as $key => $value){
                    if(in_array(mb_strtolower($key),array('rel')))
                        $additional .=  (!empty($additional) ? ' ' : '').$key.'="'.htmlspecialcharsbx($value).'"';
                }
            }

            if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                <?=str_repeat("</div></div></div></div>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
            <?  endif; ?>
            <?  if ($arItem["IS_PARENT"]):?>
                    <div class="panel-group accordion-menu" id="accordion<?=($arItem["DEPTH_LEVEL"] + 1);?>" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab">
                                <p class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion<?=($arItem["DEPTH_LEVEL"] + 1);?>" href="#self" data-target="#collapse<?=$number;?>" aria-expanded="true" aria-controls="collapse<?=$number;?>" class="parent sublink collapsed" <?=$additional;?>>
                                        <?=$arItem["TEXT"]?>
                                    </a>
                                </p>
                            </div>
                            <div id="collapse<?=$number;?>" class="panel-collapse collapse" role="tabpanel">
                                <div class="panel-body">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab">
                                            <p class="panel-title">
                                                <a role="button" data-title="<?=$arItem["TEXT"]?>" href="<?=$arItem["LINK"]?>" class="collapsed" <?=$additional;?>>
                                                    <?=GetMessage('TO_CURRENT_SECTION')?>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                    <?  else:?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab">
                                            <p class="panel-title">
                                                <a role="button" data-title="<?=$arItem["TEXT"]?>" href="<?=$arItem["LINK"]?>" class="collapsed" <?=$additional;?>>
                                                    <?=$arItem["TEXT"]?>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                    <? endif; ?>
                    <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>
                    <? endforeach; ?>
                    <? if ($previousLevel > 1)://close last item tags?>
                        <?=str_repeat("</div></div></div></div>", ($previousLevel-1) );?>
                    <? endif?>
                </div>
            </div>
        </div>
    </div>
    <script>
        //<!--

        function recreateAccordionMenu(){

            $(".accordion-menu").each(function(){

                var curAcc = this;
                var cInd = false;

                var cPath = location.pathname;
                var cPathWithHost = location.protocol + '//' + location.hostname + cPath;

                var pLen = location.href.length;

                $(curAcc).find('a').each(function(count){

                    var aLink = $(this).get(0);

                    if(aLink
                        && !$(aLink).hasClass('sublink')){

                            var chunk = false;
                            aLink = aLink.href.replace(/\?.*$/i,'');

                            if(aLink == cPathWithHost){
                                cInd = count;
                            } else if(aLink  == cPath){
                                cInd = count;
                            }

                        }



                });

                if(cInd !== false){
                    $(curAcc).find('a').eq(cInd).addClass('subopen subopenfirst fbold').removeClass('collapsed');
                    $(curAcc).find('a').eq(cInd).parents('.panel-collapse:not(#collapse0)').addClass('in');

                };

            });

            $(".sublinkfirst").addClass("fbold");

            if($(".subopenfirst",".accordion-menu").get(0)){
                var fLink = $(".subopenfirst",".accordion-menu").eq(0);
                var fTitle = $.trim($(fLink).attr("data-title") ? $(fLink).attr("data-title") : $(fLink).text());
                $(".sublinkfirst").prop({"href": fLink.href}).html(fTitle);
            };

            $(".accordion-menu").removeClass("hidden");

        };

        if (typeof window.frameCacheVars != 'undefined') {

            BX.addCustomEvent("onFrameDataReceived", function(){
                setTimeout(function(){
                    recreateAccordionMenu();
                },200);
            });

        } else {

            $(function(){
                setTimeout(function(){
                    recreateAccordionMenu();
                },200);
            });

        };

        //-->
    </script>
<? endif?>