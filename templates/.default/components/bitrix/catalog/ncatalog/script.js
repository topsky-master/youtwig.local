function setSortCount(){

    $('#PAGE_ELEMENT_COUNT,#sort-select').change(function(){
        $(this).parents('form').submit();
    });

};

function bindFilterLeft(){
	$('.filter-title').bind("click",function(){
        var oConainer = $(this).next('div');
        switch(oConainer.css('display')){

            case 'none':

                    $(this).removeClass('filter-collapsed');
                    oConainer.css('display','block');

                break;
            default:


                    $(this).addClass('filter-collapsed');
                    oConainer.css('display','none');

                break;

        };

    });

    $('.filter-parameters-content > p').bind("click",function(){

        if($(this).parent().hasClass('filter-expanded')){
            $(this).parent().removeClass('filter-expanded');
        } else {
            $(this).parent().addClass('filter-expanded');
        };

    });
};


$(function(){

	bindFilterLeft();
	setSortCount();

});