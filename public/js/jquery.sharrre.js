function initSharrre(id)
{
	
	$('#'+id).sharrre({
		share: {
			googlePlus: true,
			facebook: true,
			twitter: true,
			linkedin: false
		},
		buttons: {
			googlePlus: {size: 'tall'},
			facebook: {layout: 'box_count'},
			twitter: {count: 'vertical', via: ''},
			linkedin: {counter:  'top'}
		},
		hover: function(api, options){
			$(api.element).find('.buttons').show();
		},
		hide: function(api, options){
			$(api.element).find('.buttons').hide();
		},
		enableTracking: true
	});
}