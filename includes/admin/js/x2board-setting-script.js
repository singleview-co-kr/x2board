jQuery(document).ready(function(){
	x2board_setting_tab_init();
});

function x2board_setting_tab_init(){
	var index = location.hash.slice(1).replace('tab-x2board-setting-', '');
	console.log(index);
	x2board_setting_tab_change(index);
}

function x2board_setting_tab_change(index){
	jQuery('.tab-x2board').removeClass('nav-tab-active').eq(index).addClass('nav-tab-active');
	jQuery('.tab-x2board-setting').removeClass('tab-x2board-setting-active').eq(index).addClass('tab-x2board-setting-active');
	jQuery('input[name=tab_x2board_setting]').val(index);

	if(index == 3){
		jQuery('#x2board-setting-form .submit').hide();
	}
	else{
		jQuery('#x2board-setting-form .submit').show();
	}
}