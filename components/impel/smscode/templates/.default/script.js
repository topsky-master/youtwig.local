$(remapPasscode);

function remapPasscode() {

    if($('#passcode').get(0)){

        $('#passcode').unbind("submit");
        $('#passcode').bind("submit",function(event){

            var dataString = $(this).serialize();

            $.post(
                location.protocol + "//" + location.hostname + "/include/onetimepassword.php",
                dataString,
                function(data) {

                    var passcodeHTML = $.parseHTML(data);

                    $('#passcode').html($(passcodeHTML).html());

                    remapPasscode();

                    if($('#need_reload').get(0)){
						
						var cData = $('#ORDER_FORM').serialize();
						cData += '&recovery=true';
						$.post(location.protocol + '//' + location.hostname + location.pathname, cData, function(cData) {
							var oLink = location.href;
							oLink = oLink.indexOf('?') === -1 ? oLink + '?' : oLink;
							oLink += '&sessid=' + $('[name=sessid]').val();
							location.href = oLink;
						});
						
					}

                }
            );

            event.preventDefault();
            return false;

        });

    }

}