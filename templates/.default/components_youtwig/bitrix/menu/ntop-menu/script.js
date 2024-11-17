$(function(){

    if($('#hls').get(0)){
        $('#hls').each(function(){
            $('.dropdown-menu, .dropdown-m-menu',this).each(function(){

                if($(this).hasClass('dropdown-sub-menu')){
                    var colCount = $(this).children().length;
                    if(colCount){
                        $(this).addClass('clm-' + colCount);
                    }
                }

                var dMenu = $(this).parent().get(0);
                if(!$(dMenu).parent().hasClass('nav')){

                    $(dMenu).addClass('dropdown-submenu');
                    var aLink = $(dMenu).children('a').first().get(0);
                    $(aLink).after('<button data-toggle="dropdown" class="hidden-lg dropdown-toggle navbar-toggle btn btn-default"><span class="sr-only"><?=GetMessage("TOP_CURRENT_MENU"); ?></span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>');

                } else {

                    $(dMenu).addClass('dropdown');
                    var aLink = $(dMenu).children('a').first().get(0);
                    $(aLink).append('<b class="caret"></b>');
                    $(aLink).addClass('dropdown-toggle');
                    //$(aLink).attr("data-toggle","dropdown");

                }

            });

        });

        $('#hls .dropdown-toggle').on('click', function(event) {

            event.preventDefault();
            event.stopPropagation();

            if($(this).parent().hasClass('open')){

                $(this).parent().removeClass('open');
                $(this).addClass('collapsed');
                $(this).attr("aria-expanded",false);

            } else {

                $(this).parent().addClass('open');
                $(this).removeClass('collapsed');
                $(this).attr("aria-expanded",true);

            }


        });

    }

});