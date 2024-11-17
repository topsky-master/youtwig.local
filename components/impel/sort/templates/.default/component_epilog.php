<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

ob_start();
?>
    <form id="compare-sort">
        <div class="sort">
            <label for="sort-select">
                <?php echo GetMessage("SECTION_SORT"); ?>
            </label>
            <select id="sort-select" name="sort" class="selectpicker">
                <?php foreach ($arParams['howSort'] as $value=>$name): ?>
                    <option data-content="<?=htmlspecialcharsbx($name);?>" value="<?php echo urlencode($value); ?>"<?php if($value == $arParams['sort_code']): ?> selected="selected"<?php endif; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if(isset($_REQUEST['q'])): ?>
                <input name="q" type="hidden" value="<?=htmlspecialcharsbx($_REQUEST['q']); ?>" />
            <?php endif; ?>
        </div>
        <div class="grid-list">
            <a href="<?=$APPLICATION->GetCurDir();?>?LIST_TYPE=GRID<? if(isset($_REQUEST['q'])): ?>&q=<?=htmlspecialcharsbx($_REQUEST['q']); ?><? endif; ?>"<? if($arParams['LIST_TYPE'] == 'GRID'):?> class="active"<? endif; ?> id="grid-toogle"></a>
            <a href="<?=$APPLICATION->GetCurDir();?>?LIST_TYPE=LIST<? if(isset($_REQUEST['q'])): ?>&q=<?=htmlspecialcharsbx($_REQUEST['q']); ?><? endif; ?>"<? if($arParams['LIST_TYPE'] == 'LIST'):?> class="active"<? endif; ?> id="list-toogle"></a>
        </div>
    </form>
<?

$rightSideBreadcrumb = ob_get_clean();
$APPLICATION->SetPageProperty('RIGHT_SIDE_BREADCRUMB', $rightSideBreadcrumb);

