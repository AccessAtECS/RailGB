$(document).bind( 'mobileinit', function(){
	$.mobile.loader.prototype.options.text = "loading";
	$.mobile.loader.prototype.options.textVisible = false;
	$.mobile.loader.prototype.options.theme = "d";
	$.mobile.loader.prototype.options.html = "";
	$.mobile.page.prototype.options.backBtnTheme  = "d";
});

