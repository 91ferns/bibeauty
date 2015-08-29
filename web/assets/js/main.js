jQuery(function($) {
	console.log('test');
});

//window scroll function for navbar opacity

jQuery(function($) {
	$(window).scroll(function() {
		if($(this).scrollTop() > 25){
			$('.navbar-default').addClass('opaque');
		} else {
			$('.navbar-default').removeClass('opaque');
		}
	});
});
