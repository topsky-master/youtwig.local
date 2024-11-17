<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?php if($is_personal && $USER->IsAuthorized()){

            il::ob_start();
?>
    <a href="/?logout=yes" class="personal-logout">
        <?=GetMessage('log-out');?>
    </a>
<?

            $rightSideBreadcrumb = il::ob_get_clean();
            $APPLICATION->SetPageProperty('RIGHT_SIDE_BREADCRUMB', $rightSideBreadcrumb);

?>
    <div class="personal-account">
        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 personal-menu-area">
            <div class="personal-user">
                <a href="/personal/profile/" class="col-xs-2 col-sm-4 personal-icon">
                </a>
                <span class="col-xs-10 col-sm-8">
                    <?php if(!empty($userName)){?>
                    <a href="/personal/profile/" class="person-name">
                        <?=$userName;?>
                    </a>
                    <?php } ?>
                    <?php if(!empty($userEmail)){?>
                    <a href="mailto:<?=$userEmail;?>" class="person-email">
                        <?=$userEmail;?>
                    </a>
                    <?php } ?>
                </span>
            </div>
            <div class="personal-menu">
            <?
                $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "list",
                    array(
                        "ROOT_MENU_TYPE" => "personal",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
            );?>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-7 col-xs-12 personal-account-area">
            <div>
<?php } ?>