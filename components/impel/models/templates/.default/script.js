function whatToChooseCreate(){

    var rURI = location.protocol + '//' + location.hostname + '/include/models.php';

    $('#manufacturer').html('<option value="" selected="true">'+$('#manufacturer').attr('data-title')+'</option>');

    $('#type_of_product').change(function(){
        var tIndex = $('#type_of_product').val();
        tIndex = tIndex.replace('_','');

        if(tIndex == ""){

            $('#manufacturer').html('<option value="" selected="true">'+$('#manufacturer').attr('data-title')+'</option>');
            $('#manufacturer').selectpicker('render');
            $('#manufacturer').selectpicker('refresh');
            $('#redirectwhere').val("");
            $('#model_new').val("");
            $("#redirect").attr("disabled",true);
            $('#model_new').attr("disabled",true);
            $("#model_new").tooltip("destroy");

        } else {

            $.getJSON( rURI, "&type_of_product=" + tIndex + "&bxajaxid=choose_type", function(dataJson){

                $('#manufacturer').html('<option value="" selected="true">'+$('#manufacturer').attr('data-title')+'</option>');

                if(dataJson){

                    for(var manufacturer in dataJson){
                        $('#manufacturer').append('<option value="'+manufacturer+'">'+dataJson[manufacturer]+'</option>');
                    }
                }

                $('#manufacturer').selectpicker('render');
                $('#manufacturer').selectpicker('refresh');
                $('#model_new').val("");
                $('#redirectwhere').val("");
                $("#redirect").attr("disabled",true);
                $('#model_new').attr("disabled",true);
                $("#model_new").tooltip("destroy");

            });

        }


    });

    $('#manufacturer').change(function(){

        var tIndex = $('#type_of_product').val();
        tIndex = tIndex.replace('_','');

        var mIndex = $('#manufacturer').val();
        mIndex = mIndex.replace('_','');


        if(tIndex == "" || mIndex == ""){

            if(tIndex == ""){
                $('#manufacturer').html('<option value="" selected="true">'+$('#manufacturer').attr('data-title')+'</option>');
                $('#model_new').val("");
            }

            if(mIndex == ""){
                $('#model_new').val("");
            }

            $('#manufacturer').selectpicker('render');
            $('#manufacturer').selectpicker('refresh');
            $('#model_new').val("");
            $('#redirectwhere').val("");
            $("#redirect").attr("disabled",true);
            $('#model_new').attr("disabled",true);
            $("#model_new").tooltip("destroy");

        } else {

            $("#redirect").attr("disabled",true);
            $('#model_new').attr("disabled",false);
            $("#model_new").tooltip("destroy");

            $("#model_new").bind("change",function(){

                if($("#model_new").attr("stop") == true){

                    $("#model_new").attr("stop",false);

                } else {

                    var acUrl = rURI + "?&bxajaxid=choose_model_new";
                    var tIndex = $('#type_of_product').val();
                    tIndex = tIndex.replace('_','');

                    var mIndex = $('#manufacturer').val();
                    mIndex = mIndex.replace('_','');

                    var mValue = $.trim(this.value);

                    if(tIndex != "" && mIndex != "" && mValue != ""){

                        acUrl += "&type_of_product="+tIndex+"&manufacturer="+mIndex+"&model_new="+encodeURIComponent(mValue);

                        $.getJSON(acUrl , function (data){

                            if(!data.length){

                                $("#model_new").tooltip({placement: "bottom", trigger: "click", title: $("#model_new").attr('data-no-results')});
                                $("#model_new").tooltip("show");
                                $("#redirect").attr("disabled",false);
                                $('#redirectwhere').val("");

                            } else {

                                $("#model_new").tooltip("destroy");

                                if(data && typeof data[0] != "undefined"){

                                    var firstResult = data[0];

                                    if(typeof firstResult.value != "undefined" && firstResult.value){

                                        if(firstResult.value != ""){

                                            if(typeof firstResult.name != "undefined" && firstResult.name){

                                                $("#model_new").attr("stop",true);
                                                $("#model_new").val($.trim(firstResult.name));

                                            }


                                            $("#redirect").attr("disabled",false);
                                            $('#redirectwhere').val($.trim(firstResult.value));
                                        } else {
                                            $("#redirect").attr("disabled",true);
                                        }

                                    }

                                }

                            }

                        });

                    } else {

                        $("#redirect").attr("disabled",true);
                        $('#redirectwhere').val("");

                    }

                }

            });

            $("#model_new").autocomplete({
                source: function(request ,response){

                    var acUrl = rURI + "?&bxajaxid=choose_model_new";
                    var tIndex = $('#type_of_product').val();
                    tIndex = tIndex.replace('_','');

                    var mIndex = $('#manufacturer').val();
                    mIndex = mIndex.replace('_','');

                    var mValue = $.trim(request.term);

                    if(tIndex != "" && mIndex != "" && mValue != ""){

                        acUrl += "&type_of_product="+tIndex+"&manufacturer="+mIndex+"&model_new="+encodeURIComponent(mValue);

                        $.getJSON(acUrl , function (data){

                            if(!data.length){

                                $("#model_new").tooltip({placement: "bottom", trigger: "click", title: $("#model_new").attr('data-no-results')});
                                $("#model_new").tooltip("show");

                            } else {

                                $("#model_new").tooltip("destroy");

                            }

                            response($.map(data,function(opt){
                                return {
                                    label : opt.name,
                                    value : opt.value
                                }
                            }));

                        });

                    } else {

                        $("#redirect").attr("disabled",true);
                        $('#redirectwhere').val("");

                    }

                },
                select: function( event, ui ) {

                    if(ui.item.value != ""){

                        $('#redirectwhere').val($.trim(ui.item.value));
                        $("#redirect").attr("disabled",false);

                    } else {

                        $("#redirect").attr("disabled",true);

                    }

                    $('#model_new').val($.trim(ui.item.label));
                    return false;

                },
                minLength: 2

            });

        }

    });

    $("#redirect").click(function(){
        if(!this.disabled){
            var mdIndex =  $("#redirectwhere").val();
            if(mdIndex != ""){
                location.href = mdIndex;
            }
        }
    });

}

$(function(){
    whatToChooseCreate();
});