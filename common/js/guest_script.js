/**
 * @author https://singleview.co.kr
 */

jQuery('#toggle_all_doc').click(function() {
	var checkedStatus = this.checked;
	jQuery('#document-table tbody tr').find('td :checkbox').each(function() {
		jQuery(this).prop('checked', checkedStatus);
	});
});

jQuery('#btn_move_category').click(function() {
	const chkbox_array = jQuery("input:checkbox[name=doc_chk]:checked").map(function (){
					return jQuery(this).val();
				}).toArray();
	if(!chkbox_array.length) {
		alert('문서를 선택하세요!');
		return;
	}
	const tgt_cat_id = jQuery("select[name=target_category]").val();
	if(!tgt_cat_id.length) {
		alert('이동할 카테고리를 선택하세요!');
		return;
	}
	if(!kboard_ajax_lock){
		kboard_ajax_lock = true;
		jQuery.post(kboard_settings.ajax_url, 
					{'action':'kboard_document_manage',
					 'board_id':jQuery(this).data('board-id'), 
					 'operation':'move_category',
					 'tgt_cat_id':tgt_cat_id,
					 'doc_uids':chkbox_array.join(','), 
					 'security':kboard_settings.ajax_security}, 
					 function(res) {
						kboard_ajax_lock = false;
						if(typeof callback === 'function'){
							callback(res);
						}
						else{
							if(res.result == 'error'){
								alert(res.message);
							}
							else{
								location.reload(true)
							}
						}
					 });
	}
	else{
		alert(kboard_localize_strings.please_wait);
	}
});