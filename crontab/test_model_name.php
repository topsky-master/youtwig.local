<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");



?>
    <form class="form-inline">
        <div class="form-group">
            <input type="text" class="form-control" placelhoder="Введите модель" <?php if(isset($_REQUEST['model'])): ?> value="<?=htmlspecialcharsbx($_REQUEST['model']);?>" <?php endif; ?> name="model" />
        </div>
        <input type="submit" class="btn btn-default" value="Проверить" />

        <?php

        if(isset($_REQUEST['model'])){
            $model = trim(preg_replace('~\s*?\([\d\s]+\s*?$~isu','',$_REQUEST['model']));
            $model = trim(preg_replace('~\s*?\([\d\s]+\)\s*?$~isu','',$model));
            $model = trim(str_ireplace('(','',$model));
            $model = trim(str_ireplace(')','',$model));
            $model = trim(preg_replace('~\s+~isu','',$model));
            $model = trim($model, '/\\-()');

            echo '<br /><br /><textarea class="form-control" readonly="true">'.htmlspecialcharsbx($model).'</textarea>';
        }
        ?>

    </form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>