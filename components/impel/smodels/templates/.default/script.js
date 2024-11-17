function whatToChooseCreates(){

    var rURI = location.protocol + "//" + location.hostname + "/include/smodelsajax.php";

    $("#model-snew").autocomplete({
        source: function(request ,response){

            var acUrl = rURI + "?&bxajaxid=choose_model_snew";
            var mValue = $.trim(request.term);

            if(mValue != ""){

                acUrl += "&model_snew="+encodeURIComponent(mValue);

                $.getJSON(acUrl , function (data){

                    if(!data.length){

                        $("#model-snew").tooltip({placement: "bottom", trigger: "click", title: $("#model-snew").attr('data-no-results')});
                        $("#model-snew").tooltip("show");

                    } else {

                        $("#model-snew").tooltip("destroy");

                    }

                    response($.map(data,function(opt){
                        return {
                            label : opt.name,
                            value : opt.value
                        }
                    }));

                });

            } else {

                $("#sredirect").attr("disabled",true);
                $('#sredirectwhere').val("");

            }

        },
        select: function( event, ui ) {

            if(ui.item.value != ""){

                $('#sredirectwhere').val($.trim(ui.item.value));
                $("#sredirect").attr("disabled",false);

            } else {

                $("#sredirect").attr("disabled",true);

            }

            $('#model-snew').val($.trim(ui.item.label));
            return false;

        },
        minLength: 2

    });

    $("#sredirect").click(function(){
        if(!this.disabled){
            var mdIndex =  $("#sredirectwhere").val();
            if(mdIndex != ""){
                location.href = mdIndex;
            }
        }
    });

}

$(function(){
    whatToChooseCreates();
});