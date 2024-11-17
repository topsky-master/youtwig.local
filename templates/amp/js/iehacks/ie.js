$(function() {
    if (window.PIE) {
        $('.carousel-indicators li').each(function() {
            PIE.attach(this);
        });
    }
}); 

jQuery(window).one("unload", function() {
	var global = jQuery.timer.global;
	for ( var label in global ) {
	var els = global[label], i = els.length;
	while ( --i )
	jQuery.timer.remove(els[i], label);
	}
});