<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?if (count($arResult) > 0):?>
    <p class="h4 viewed-title">
        <?=GetMessage('TMPL_VIEWED_LIST');?>
    </p>
    <div class="view-list">
        <?foreach($arResult as $arItem):?>
            <div class="view-item col-md-12 col-sm-6">
                <div class="product-image col-xs-4 col-sm-2 col-md-3 col-lg-4">
                    <?if(is_array($arItem["PICTURE"])
                        && isset($arItem["PICTURE"]["src"])):?>
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="product-image">
                            <img src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=htmlspecialchars($arItem["NAME"], ENT_HTML401, LANG_CHARSET);?>" class="img-responsive" />
                        </a>
                    <?endif;?>
                </div>
                <div class="col-xs-8 col-sm-10 col-md-9 col-lg-8">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="product-name">
                        <?=$arItem["NAME"]?>
                    </a>
                </div>
            </div>
        <?endforeach;?>
    </div>
<?endif;?>