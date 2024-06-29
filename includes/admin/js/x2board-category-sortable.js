/**
 * @author https://singleview.co.kr
 */

jQuery(document).ready(function(){
	jQuery('ul.sortable').nestedSortable({
		update: function(){
			x2board_category_sortable(jQuery('ul.sortable').nestedSortable('toArray'));
		},
		listType: 'ul',
		forcePlaceholderSize: true,
		items: 'li',
		opacity: 0.5,
		placeholder: 'placeholder',
		revert: 0,
		tabSize: 25,
		toleranceElement: '> div',
		maxLevels: 100,
		isTree: true,
		expandOnHover: 700,
		startCollapsed: false
	});
});

function x2board_category_sortable(a_tree_category){
	var board_id = jQuery('input[name=board_id]').val();
	s_tree_category_serialize = JSON.stringify(a_tree_category);
	jQuery.post(ajaxurl, {action: x2board_admin_ajax_info.cmd_ajax_reorder_category, 
						  board_id: board_id, 
						  tree_category_serialize: s_tree_category_serialize}, 
						  function(data){
		jQuery('.sortable li').remove();
		jQuery('.sortable').append(data.table_body);
	});
}

function x2board_category_edit(category_id, category_name, parent_id, is_default){
	jQuery('#parent-id').val(parent_id);
	
	jQuery('li .parent-id'+category_id).val(parent_id);
	jQuery('.x2board-update-category').css('display', 'block');
		
	jQuery('#category-id').val(category_id);
	if( is_default === 'Y') {
		jQuery('#default-category').prop('checked',true);
	}
	else {
		jQuery('#default-category').prop('checked',false);
	}
	jQuery('.update_category_name').val(category_name);
	jQuery('#update-category-name').focus();
}

function x2board_category_handler(sub_action) {
	var board_id = jQuery('input[name=board_id]').val();
	var category_name = '';
	var category_id = jQuery('#category-id').val();
	var current_parent_id = jQuery('#parent-id').val();
	if(sub_action == 'insert'){
		category_name = jQuery('#new-category-name').val();
		if(!category_name){
			return false;
		}
		jQuery.post(ajaxurl, {action: x2board_admin_ajax_info.cmd_ajax_insert_category, 
							  board_id:board_id, new_cat_name: category_name}, function(data){
			var new_category_id = data.new_cat_id;
			jQuery('.sortable').append('<input type="hidden" name="tree_category['+new_category_id+'][id]" value="'+new_category_id+'">');
			jQuery('.sortable').append('<input type="hidden" name="tree_category['+new_category_id+'][is_default]" value="">');
			jQuery('.sortable').append('<input type="hidden" name="tree_category['+new_category_id+'][title]" value="'+category_name+'">');
			jQuery('.sortable').append('<input type="hidden" name="tree_category['+new_category_id+'][parent_id]" value="">');
			_x2board_category_handler_ajax();
		});
		return false;
	}

	if(sub_action == 'update'){
		category_name = jQuery('#update-category-name').val();
		if(!category_name){
			return false;
		}
		jQuery('#tree-category-name-'+category_id).val(category_name);
		// 기본 지정 카테고리 값 탐색 시작
		var mode = null;
		if(jQuery('#default-category').is(':checked')) {
			mode = 'set_current_default_cat';
		}
		else {
			mode = 'unset_current_default_cat';
		}

		if( mode == 'set_current_default_cat' ) {  // 선택한 카테고리를 기본 지정함
			jQuery('.sortable').find('div > div').each(function(index, element){
				temp_category_id = jQuery(element).attr('data-id');
				if( category_id == temp_category_id ) {
					jQuery('input[name="tree_category['+ temp_category_id +'][is_default]"]').val('Y');
				}
				else {
					jQuery('input[name="tree_category['+ temp_category_id +'][is_default]"]').val('');
				}
			});
		}
		else if( mode == 'unset_current_default_cat' ) {  // 선택한 카테고리를 기본 지정 해제함
			jQuery('input[name="tree_category['+ category_id +'][is_default]"]').val('');
		}
	}

	if(sub_action == 'remove'){
		if(!category_id){
			return false;
		}
		jQuery('.x2board-tree-category-parents').each(function(index, element){
			if(category_id == jQuery(element).val()){
				jQuery(element).val(current_parent_id);
			}
		});
		jQuery('input[name="tree_category['+category_id+'][id]"]').remove();
		jQuery('input[name="tree_category['+category_id+'][is_default]"]').remove();
		jQuery('input[name="tree_category['+category_id+'][title]"]').remove();
		jQuery('input[name="tree_category['+category_id+'][parent_id]"]').remove();
	}
	_x2board_category_handler_ajax();
	return false;
}

function _x2board_category_handler_ajax() {
	var board_id = jQuery('input[name=board_id]').val();
	var tree_category = jQuery('.sortable').find('input[name^="tree_category"]').serialize();
	jQuery.post(ajaxurl, {action:x2board_admin_ajax_info.cmd_ajax_manage_category, 
						  tree_category:tree_category, 
						  board_id:board_id}, function(data){
		jQuery('#new-category-name').val('');
		jQuery('#update-category-name').val('');
		jQuery('#default-category').prop('checked',false);
		jQuery('#new-default-category').prop('checked',false);
		jQuery('#new-category-name').focus();
		jQuery('.sortable li').remove();
		jQuery('.sortable input').remove();
		jQuery('.sortable').prepend(data.table_body);
	});
}