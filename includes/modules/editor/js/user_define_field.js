function x2board_radio_reset(obj){
	jQuery(obj).parents('.kboard-radio-reset').find('input[type=radio]').each(function(){
		jQuery(this).prop('checked', false);
	});
}