$(function(){

if($("#back_to_menu").get(0)){
    $("#back_to_menu").bind("click",function(){
        var topParent 	= $("#search_block_top");
        $(topParent).css({borderRadius: "0 5px 5px 0"});
        $(topParent).animate({width: mDefaultWidth},400);

        if(mDefaultWidth != "34px")
            $(".search-title",topParent).css("display","inline");

        $(".glyphicon-search",topParent).css("width",mDefaultWidth);
    });
};

$( ".autocomplete" ).each(function(){

    var tForm  = $(this).parents("form").eq(0);

    $(this).autocomplete({

        source: function(request,response){

            $.ajax({
                url: (location.protocol+"//"+location.hostname+"/search/ajax.php"),
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function( data ) {
                    if(data.result.length){
                        response( $.map( data.result, function(item) {
                            return {
                                label: item.name,
                                value: item.value
                            }
                        }));
                    };
                }

            });
        },
        minLength: 3,
        select: function(event, ui){
            if(ui.item.value){
                location.href = ui.item.value;
            };

            event.preventDefault();
            return false;
        }

    }).data("ui-autocomplete")._renderItem = function (ul, item) {
         return $("<li></li>")
             .data("item.autocomplete", item)
             .append("<a>" + item.label + "</a>")
             .appendTo(ul);
     };

});


});