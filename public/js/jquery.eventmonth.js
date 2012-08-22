/* Default procedures to run*/
$(function(){
	$(".wkn").hover(
		function(){
			var row = $(this).find('.wkrow');
			$(row).addClass('wkhover').attr('border', '1px solid red');
		},
		function(){
			var row = $(this).find('.wkrow');
			$(row).removeClass('wkhover').attr('border', 'none');
		}
	);
});