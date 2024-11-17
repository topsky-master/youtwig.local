<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
        IncludeTemplateLangFile(__FILE__);
        ?>
        <?php
        if(defined('ERROR_404')):
            require_once $_SERVER['DOCUMENT_ROOT'].'/404_catalog.php';
        endif;
        ?>
    </div>
</div>
</body>
</html>