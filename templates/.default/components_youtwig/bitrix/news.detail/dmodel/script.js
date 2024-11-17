$(function(){

$('.indcode-list li:not(:first-child) a').each(function(){

var linkHref = this.href.replace(/[^\/]+?\/\/[^\/]+?\//,'/');
linkHref = linkHref.replace(/[^\/]*?\?.*$/,'/');

var cPathname = location.pathname;

if(cPathname == linkHref){

$(this).addClass('active');
$(this).on('click',function(event){

event.preventDefault();
location.href = $('#bpage').val();
return false;

});

}

});

})