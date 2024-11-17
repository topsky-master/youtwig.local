<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<p class="h3"><?php echo GetMessage('TMPL_FILE_LIST'); ?></p>
<?php if(!empty($arResult['aerrors'])): ?>
    <?php foreach($arResult['aerrors'] as $error): ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <div><?php echo $error; ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if(!empty($arResult['paths'])): ?>
    <ul class="paths">
        <?php foreach($arResult['paths'] as $path): ?>
            <li>
                <div><?php echo $path; ?><div class="delete btn btn-default" onclick="return deletefile(this)" data-href="?path=<?php echo urlencode($path); ?>&action=delete">×</div><div class="process btn btn-default" onclick="return settingsfile(this)" data-href="?path=<?php echo urlencode($path); ?>&action=settings"><?php echo GetMessage('TMPL_PROCESS'); ?></div></div>
                <div class="settings hidden"></div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
