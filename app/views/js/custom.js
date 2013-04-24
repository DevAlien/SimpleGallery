var $j = jQuery.noConflict();

$j(document).ready(function() {

	$j(".image h5.capt").hide();
	
	$j(".image").hover(function () {										  
		$j("h5.capt", this).slideToggle("fast");
	});
	
});