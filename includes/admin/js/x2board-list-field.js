/**
 * @author https://singleview.co.kr
 */

jQuery(document).ready(function(){
	jQuery('ul.x2board-list-config-fields-list, ul.x2board-list-config-fields-sortable').sortable({
		connectWith: '.connected-list-config-sortable',
		handle: '.x2board-list-config-field-handle',
		cancel: '',
		forcePlaceholderSize: true,
		placeholder: 'placeholder',
		remove: function(event, li){
			var eid = jQuery(li.item).find('.field_data.eid').val();

			jQuery(li.item).find('.eid').attr('name', 'board_list_fields['+eid+'][eid]');
			jQuery(li.item).find('.var_type').attr('name', 'board_list_fields['+eid+'][var_type]');
			jQuery(li.item).find('.var_name').attr('name', 'board_list_fields['+eid+'][var_name]');
			
			if(!eid){
				li.item.clone().insertAfter(li.item);
 				jQuery(this).sortable('cancel');
 				jQuery(li.item).find('.field_data').attr('name', '');
			}
			jQuery(li.item).removeClass(eid);

			return li.item.clone();
		},
	});

	jQuery('.x2board-fields').on('click', '.fields-list-config-remove', function(event){
		if(jQuery(this).closest('li').hasClass('default')){
			jQuery('.x2board-list-config-fields-default').addClass('list-active');
			jQuery(this).closest('li').removeClass('active');
			jQuery(this).closest('li').find('.field_data').attr('name', '');
			jQuery('.x2board-list-config-fields-default .x2board-list-config-fields-list').append(jQuery(this).closest('li'));
		}
		else{
			jQuery(this).closest('li').remove();
		}
	});
});