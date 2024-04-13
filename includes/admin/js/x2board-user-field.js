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
				uniq_id = 'js_' + uniqid();
			}
// console.log(uniq_id);
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
					var option_id = 'js_' + uniqid();
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

function add_option(element){
	var label = '';
	var parent = jQuery(element).closest('.x2board-fields-sortable.connected-sortable').length;
	var parent_id = jQuery(element).closest('li').find('.parent_id').val();
	var uniq_id = uniqid();
	var field_type = 'radio';
	var name = jQuery(element).closest('li').find('.field_data.field_type').val();
	var value = uniq_id;

	if(parent){
		label = 'fields['+parent_id+'][row]['+uniq_id+'][label]';
		name = 'fields['+parent_id+'][default_value]';
	}
	
	if(jQuery(element).closest('li').find('.field_data.field_type').val() == 'checkbox'){
		field_type = 'checkbox';
		name = 'fields['+parent_id+'][row]['+uniq_id+'][default_value]';
		value = '1';
	}
	
	jQuery(element).closest('.attr-row').after('<div class="attr-row option-wrap">'+
		'<div class="attr-name option"><label for="'+uniq_id+'_label">라벨</label></div>'+
		'<div class="attr-value">'+
		'<input type="text" name="'+label+'" id="'+uniq_id+'_label" class="field_data option_label"> '+
		'<button type="button" class="'+field_type+'" onclick="add_option(this)">+</button> '+
		'<button type="button" class="'+field_type+'" onclick="remove_option(this)">-</button> '+
		'<label><input type="'+field_type+'" name="'+name+'" class="field_data default_value" value="'+value+'"> 기본값'+
		'</label></div></div>'
	);
}
function remove_option(element){
	if(jQuery(element).closest('li').find('.attr-row.option-wrap').length == 1) return false;
	jQuery(element).parents('.attr-row').remove();
}

function x2board_fields_toggle(element, active){
	if(jQuery(element).closest('li').hasClass(active)){
		jQuery(element).closest('li').removeClass(active);
	}
	else{
		jQuery(element).closest('li').addClass(active);
	}
}

function x2board_fields_permission_roles_view(element){
	if(jQuery(element).val() == 'roles'){
		jQuery(element).siblings('.x2board-permission-read-roles-view').removeClass('x2board-hide');
	}
	else{
		jQuery(element).siblings('.x2board-permission-read-roles-view').addClass('x2board-hide');
	}
}

/**
 * JavaScript alternative of PHP uniqid()
 * original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * revised by: Kankrelune (http://www.webfaktory.info/)
 * more: https://gist.github.com/ain/5638966
 */
function uniqid(prefix,more_entropy){if(typeof prefix==='undefined'){prefix=""}var retId;var formatSeed=function(seed,reqWidth){seed=parseInt(seed,10).toString(16);if(reqWidth<seed.length){return seed.slice(seed.length-reqWidth)}if(reqWidth>seed.length){return Array(1+(reqWidth-seed.length)).join('0')+seed}return seed};if(!this.php_js){this.php_js={}}if(!this.php_js.uniqidSeed){this.php_js.uniqidSeed=Math.floor(Math.random()*0x75bcd15)}this.php_js.uniqidSeed++;retId=prefix;retId+=formatSeed(parseInt(new Date().getTime()/1000,10),8);retId+=formatSeed(this.php_js.uniqidSeed,5);if(more_entropy){retId+=(Math.random()*10).toFixed(8).toString()}return retId}