/**
 * @author https://singleview.co.kr
 */

jQuery(document).ready(function(){
	jQuery('ul.x2board-fields-list, ul.x2board-fields-sortable').sortable({
		connectWith: '.connected-sortable',
		handle: '.x2board-field-handle',
		cancel: '',
		forcePlaceholderSize: true,
		placeholder: 'placeholder',
		remove: function(event, li){
			var type = jQuery(li.item).hasClass('default');
			var field = jQuery(li.item).find('.field_data.field_type').val();
			var uniq_id;

			if(type){
				uniq_id = jQuery(li.item).find('.field_data.field_type').val();
			}
			else{
				uniq_id = 'uniqid from x2board-user-field.js';
			}

			jQuery(li.item).find('.field_type').attr('name', 'fields['+uniq_id+'][field_type]');
			jQuery(li.item).find('.field_name').attr('name', 'fields['+uniq_id+'][field_name]');
			jQuery(li.item).find('.meta_key').attr('name', 'fields['+uniq_id+'][meta_key]');
			jQuery(li.item).find('.role').attr('name', 'fields['+uniq_id+'][role]');
			jQuery(li.item).find('.placeholder').attr('name', 'fields['+uniq_id+'][placeholder]');
			jQuery(li.item).find('.required').attr('name', 'fields['+uniq_id+'][required]');
			jQuery(li.item).find('.show_document').attr('name', 'fields['+uniq_id+'][show_document]');
			jQuery(li.item).find('.field_label').attr('name', 'fields['+uniq_id+'][field_label]');
			jQuery(li.item).find('.class').attr('name', 'fields['+uniq_id+'][class]');
			jQuery(li.item).find('.default_value').attr('name', 'fields['+uniq_id+'][default_value]');
			jQuery(li.item).find('.html').attr('name', 'fields['+uniq_id+'][html]');
			jQuery(li.item).find('.shortcode').attr('name', 'fields['+uniq_id+'][shortcode]');
			jQuery(li.item).find('.hidden').attr('name', 'fields['+uniq_id+'][hidden]');
			jQuery(li.item).find('.option_field').attr('name', 'fields['+uniq_id+'][option_field]');
			jQuery(li.item).find('.field_description').attr('name', 'fields['+uniq_id+'][description]');
			jQuery(li.item).find('.custom_class').attr('name', 'fields['+uniq_id+'][custom_class]');
			jQuery(li.item).find('.close_button').attr('name', 'fields['+uniq_id+'][close_button]');
			
			if(jQuery(li.item).find('.option-wrap').length){
				jQuery(li.item).find('.option-wrap').each(function(index, element){
					var option_id = uniqid();
					jQuery(element).find('.option_label').attr('name', 'fields['+uniq_id+'][row]['+option_id+'][label]');
					jQuery(element).find('.default_value').attr('name', 'fields['+uniq_id+'][default_value]');
					if(field == 'checkbox'){
						jQuery(element).find('.default_value').attr('name', 'fields['+uniq_id+'][row]['+option_id+'][default_value]');
					}
					if(field == 'radio' || field == 'select'){
						jQuery(element).find('.default_value').val(option_id);
					}
				});
			}
			
			jQuery(li.item).find('.field_data.roles').attr('name', 'fields['+uniq_id+'][permission]');
			jQuery(li.item).find('.field_data.secret-roles').attr('name', 'fields['+uniq_id+'][secret_permission]');
			jQuery(li.item).find('.field_data.notice-roles').attr('name', 'fields['+uniq_id+'][notice_permission]');
			jQuery(li.item).find('.roles_checkbox').each(function(index, element){
				jQuery(element).attr('name', 'fields['+uniq_id+'][roles][]');
			});
			jQuery(li.item).find('.secret_checkbox').each(function(index, element){
				jQuery(element).attr('name', 'fields['+uniq_id+'][secret][]');
			});
			jQuery(li.item).find('.notice_checkbox').each(function(index, element){
				jQuery(element).attr('name', 'fields['+uniq_id+'][notice][]');
			});
			
			jQuery(li.item).addClass(uniq_id);
			jQuery(li.item).prepend('<input type="hidden" class="parent_id" value="'+uniq_id+'">');

			if(!type){
				li.item.clone().insertAfter(li.item);
 				jQuery(this).sortable('cancel');
 				jQuery(li.item).find('.field_data').attr('name', '');
			}
			jQuery(li.item).removeClass(uniq_id);

			return li.item.clone();
		},
	});
	
	jQuery('.x2board-fields-header').click(function(){
		x2board_fields_toggle(this, 'list-active');
	});
	
	jQuery('.x2board-fields').on('click', '.toggle', function(){
		x2board_fields_toggle(this, 'active');
	});
	
	jQuery('.x2board-fields').on('click', '.fields-remove', function(event){
		if(jQuery(this).closest('li').hasClass('default')){
			jQuery('.x2board-fields-default').addClass('list-active');
			jQuery(this).closest('li').removeClass('active');
			jQuery(this).closest('li').find('.field_data').attr('name', '');
			jQuery('.x2board-fields-default .x2board-fields-list').append(jQuery(this).closest('li'));
		}
		else{
			jQuery(this).closest('li').remove();
		}
	});
});

function x2board_fields_toggle(element, active){
	if(jQuery(element).closest('li').hasClass(active)){
		jQuery(element).closest('li').removeClass(active);
	}
	else{
		jQuery(element).closest('li').addClass(active);
	}
}