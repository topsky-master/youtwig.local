$(function(){
        if($(".social-icons a.titles").length){
            $(".social-icons a.titles").each(function(){
                $(this).hover(function(){
                    $(this).tooltip({placement: "bottom", title: $(this).attr("data-original-title")});
                    $(this).tooltip("show");
                });
            });
        }
    }
)