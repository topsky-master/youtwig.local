<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$areaId = isset($arParams["AREA_ID"]) && !empty($arParams["AREA_ID"]) ? trim($arParams["AREA_ID"]) : '';
$modelId = "bx_model_upload".$areaId;
?>
<div id="<?=$modelId;?>" class="upload-models">
    <div class="results<?php if(empty($arResult['filelist'])): ?> hidden<?php endif; ?>">
        <div>
            <?php echo $arResult['filelist']; ?>
        </div>
    </div>
    <?

    $type_of_product = $arResult['TYPE_OF_PRODUCT'];

    $block_title = isset($arParams['BLOCK_TITLE'])
    && !empty($arParams['BLOCK_TITLE'])
        ? trim($arParams['BLOCK_TITLE'])
        : '';

    $utime = microtime(true);

    ?>
    <p class="h3"><?php echo GetMessage("TMPL_UPLOAD_FILE"); ?></p>
    <div id="upload-results" class="alert alert-info hidden"></div>
    <form enctype="multipart/form-data" action="<?php $_SERVER['REQUEST_URI']; ?>" method="POST" data-id="<?=$utime;?>">
        <input type="hidden" name="<?=$utime;?>" value="<?=$utime;?>" />
        <div class="models"><i class="fa fa-spinner fa-spin fa-3x fa-fw hidden"></i><input name="models[]" class="file form-control" type="file" /><button class="addbutton btn btn-info" onclick="return clonelast(event);">+</button></div>
        <button type="submit" name="file_upload" value="true" class="btn btn-info" id="file_upload"><?php echo GetMessage('TMPL_UPLOAD_FILE'); ?></button>
    </form>
    <?php echo $arResult['replaces']; ?>
</div>
<script type="text/javascript">
    //<!--

    AUMESSAGES = {
        'TMPL_SELECT_FILE': '<?php echo GetMessage('TMPL_SELECT_FILE');?>'
    };

    function saveReplaces(event,elt){

        var aFields = $(elt).parents('form').serialize();
        console.log(aFields);

        $.ajax({
            url: location.href,
            cache: false,
            type: 'POST',
            data: aFields,
            success: function(response){

                $(elt).parents('form').html(response);

            }
        });

        event.preventDefault();
        return false;
    };

    function cloneReplaces(event){
        $('.replaces li').eq($('.replaces li').length - 3).after($('.replaces li').eq($('.replaces li').length - 3).clone());
        event.preventDefault();
        return false;
    };

    function loadprofile(elt,event){

        $("#preview-action").val('profile_load');
        var aFields = $(elt).parents('form').serialize();

        $.ajax({
            url: location.href,
            cache: false,
            type: 'POST',
            data: aFields,
            success: function(response){

                $(elt).parents('form').html(response);

            }
        });

        event.preventDefault();
        return false;
    }

    var tmr = 1;
    var step = 1;
    var intVal = false;

    function getprocess(elt,event){

        if (!intVal) {
            intVal = setInterval(function(){
                $(elt).html('Обрабатываю (' + tmr + ' сек.)');
                tmr += 1;
            },1000);
        }

        $("#preview-action").val('process_get');
        var aFields = $(elt).parents('form').serialize();
        var cLink = location.href + (location.href.indexOf('?') === -1 ? '?' : '') + '&step=' + step;
        aFields['step'] = step;

        $.ajax({
            url: cLink,
            cache: false,
            type: 'POST',
            data: aFields,
            error: function () {
                setTimeout(function(){
                    getprocess(elt,event);
                },2000);
            },
            success: function(response){

                if (response == 'next') {
                    ++step;
                    setTimeout(function(){
                        getprocess(elt,event);
                    },30000);
                } else {

                    $(elt).parents('form')[0].outerHTML = response;

                    if($('#json_order_columns').val()){
                        $('#profile_preview').trigger('click');
                    }

                    if (intVal) {
                        clearInterval(intVal);
                        intVal = false;
                        tmr = 0;
                    }

                    step = 1;
                }

            }
        });

        event.preventDefault();
        return false;
    }

    function disableSelect(){

        $('form').each(function(){

            var __form = this;
            var sSel = [];
            $('select.colums',__form).each(function(){
                if(this.selectedIndex)
                    sSel[sSel.length] = this.selectedIndex;
            });

            $('select.colums',__form).each(function(){

                var cSelect = this;

                $('option',cSelect).each(
                    function(itemCount){
                        this.disabled = false;
                        if(!this.selected
                            && $.inArray(itemCount,sSel) != -1){
                            this.disabled = true;
                        }
                    }
                );
            });

        });

    }

    function getpreview(elt,event) {

        var aFields = $(elt).parents('form').serialize();
        $(elt).parents('form').find('.preview-result').html('');

        $(elt).parents('form').find('.preview-result').addClass('hidden');

        $.ajax({
            url: location.href,
            cache: false,
            type: 'POST',
            data: aFields,
            success: function(response){

                console.log('here');
                $(elt).parents('form').find('.preview-result').html(response);
                $(elt).parents('form').find('.preview-result').removeClass('hidden');

            }
        });

        event.preventDefault();
        return false;
    }

    function clonelast(event){

        $('.models').last().clone().insertAfter($('.models').last());
        event.preventDefault();

        return false;
    }

    function settingsfile(oLnk){

        var dHref = $(oLnk).attr('data-href');

        $.ajax({
            url: location.protocol + '//' + location.hostname + location.pathname + dHref,
            cache: false,
            type: 'GET',
            success: function(response){

                $(oLnk).parents('div').next('div').html(response);
                $(oLnk).parents('div').next('div').removeClass('hidden');

            }
        });

        return false;
    }

    function saveprofile(elt,event){

        $("#preview-action").val('profile_save');
        var aFields = $(elt).parents('form').serialize();

        $.ajax({
            url: location.href,
            cache: false,
            type: 'POST',
            data: aFields,
            success: function(response){

                $(elt).parents('form').html(response);

            }
        });

        event.preventDefault();
        return false;
    }

    function deletefile(oLnk){

        var dHref = $(oLnk).attr('data-href');

        $.ajax({
            url: location.protocol + '//' + location.hostname + location.pathname + dHref,
            cache: false,
            type: 'GET',
            success: function(response){

                $(oLnk).parents('li').remove();

            }
        });

        return false;
    }

    $(function(){

        $("#file_upload").bind("click",function(event){

            event.preventDefault();

            var fd = new FormData();

            var filesFound = false;

            $('.file').each(function(){
                if(this.files
                    && this.files.length){

                    filesFound = true;
                    fd.append('models[]',this.files[0]);
                    $(this).parent().find('.fa').removeClass('hidden');
                }
            });

            $('#upload-results').addClass("hidden");
            $('#upload-results').html("");

            if(filesFound){
                fd.append('action','file_upload');
                $('.upload-models .results > div').html("");
                $('.upload-models .results').addClass('hidden');
            } else {

                $('#upload-results').removeClass("hidden");
                $('#upload-results').html(AUMESSAGES.TMPL_SELECT_FILE);

                return false;
            }

            $.ajax({
                url: location.href,
                cache: false,
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response){

                    $('.file').each(function(){
                        $(this).parent().find('.fa').addClass('hidden');
                    });

                    $('.upload-models .results').removeClass('hidden');
                    $('.upload-models .results > div').html(response);

                }
            });

            return false;

        });

    });
    //-->
</script>