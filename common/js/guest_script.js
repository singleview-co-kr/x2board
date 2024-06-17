/**
 * @author https://www.cosmosfarm.com
 */

/**
 * inViewport jQuery plugin by Roko C.B.
 * http://stackoverflow.com/a/26831113/383904 Returns a callback function with
 * an argument holding the current amount of px an element is visible in
 * viewport (The min returned value is 0 (element outside of viewport)
 */
// (function($, win){
// 	$.fn.kboardViewport = function(cb){
// 		return this.each(function(i, el){
// 			function visPx(){
// 				var elH = $(el).outerHeight(), H = $(win).height(), r = el.getBoundingClientRect(), t = r.top, b = r.bottom;
// 				return cb.call(el, Math.max(0, t > 0 ? Math.min(elH, H - t) : (b < H ? b : H)));
// 			}
// 			visPx();
// 			$(win).on("resize scroll", visPx);
// 		});
// 	};
// }(jQuery, window));

// var kboard_ajax_lock = false;

// jQuery(document).ready(function(){
// 	var kboard_mod = jQuery('input[name=mod]', '.kboard-form').val();
// 	if(kboard_mod == 'editor'){
// 		if(kboard_current.use_editor == 'snote'){ // summernote
// 			jQuery('.summernote').each(function(){
// 				var height = parseInt(jQuery(this).height());
// 				var placeholder = jQuery(this).attr('placeholder');
// 				var lang = 'en-US';
				
// 				if(kboard_settings.locale == 'ko_KR'){
// 					lang = 'ko-KR';
// 				}
// 				else if(kboard_settings.locale == 'ja'){
// 					lang = 'ja-JP';
// 				}
				
// 				jQuery(this).summernote({
// 					toolbar: [
// 						['style', ['style']],
// 						['fontsize', ['fontsize']],
// 						['font', ['bold', 'italic', 'underline', 'clear']],
// 						['fontname', ['fontname']],
// 						['color', ['color']],
// 						['para', ['ul', 'ol', 'paragraph']],
// 						['height', ['height']],
// 						['table', ['table']],
// 						['insert', ['link', 'video', 'hr']],
// 						['view', ['fullscreen', 'codeview']],
// 						['help', ['help']]
// 					],
// 					fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Nanum Gothic', 'Malgun Gothic', 'Noto Sans KR', 'Apple SD Gothic Neo'],
// 					fontNamesIgnoreCheck: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Nanum Gothic', 'Malgun Gothic', 'Noto Sans KR', 'Apple SD Gothic Neo'],
// 					fontSizes: ['8','9','10','11','12','13','14','15','16','17','18','19','20','24','30','36','48','64','82','150'],
// 					lang: lang,
// 					height: height,
// 					placeholder: placeholder
// 				});
// 			});
// 		}
// 	}
// });

// function kboard_tree_category_search(default_url){
// 	var tree_category_index = jQuery("#kboard_search_option option:selected").val();
// 	var redirect_url = default_url;
// 	if(tree_category_index) {
// 		redirect_url += "?category/" + tree_category_index; 
// 	}
// 	window.location.href = redirect_url;
// 	return false;
// }

// function kboard_editor_insert_media(url){
// 	if(kboard_current.use_editor == 'snote'){ // summernote
// 		jQuery('#kboard_content').summernote('editor.saveRange');
// 		jQuery('#kboard_content').summernote('editor.restoreRange');
// 		jQuery('#kboard_content').summernote('editor.focus');
// 		jQuery('#kboard_content').summernote('editor.pasteHTML', "<img src=\""+url+"\" alt=\"\">");
// 	}
// 	else if(typeof tinyMCE != 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()){
// 		tinyMCE.activeEditor.execCommand('mceInsertContent', false, "<img id=\"last_kboard_media_content\" src=\""+url+"\" alt=\"\">");
// 		tinyMCE.activeEditor.focus();
// 		tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.dom.select('#last_kboard_media_content')[0], true);
// 		tinyMCE.activeEditor.selection.collapse(false);
// 		tinyMCE.activeEditor.dom.setAttrib('last_kboard_media_content', 'id', '');
// 	}
// 	else if(jQuery('#kboard_content').length){
// 		jQuery('#kboard_content').val(function(index, value){
// 			return value + (!value?'':' ') + "<img src=\""+url+"\" alt=\"\">";
// 		});
// 	}
// }

// function kboard_document_print(url){
// 	window.open(url, 'kboard_document_print');
// 	return false;
// }

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

// function kboard_document_like(button, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		jQuery.post(kboard_settings.ajax_url, {'action':'kboard_document_like', 'document_uid':jQuery(button).data('uid'), 'security':kboard_settings.ajax_security}, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback === 'function'){
// 				callback(res);
// 			}
// 			else{
// 				if(res.result == 'error'){
// 					alert(res.message);
// 				}
// 				else{
// 					jQuery('.kboard-document-like-count', button).text(res.data.like);
// 				}
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_document_dislike(button, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		jQuery.post(kboard_settings.ajax_url, {'action':'kboard_document_dislike', 'document_uid':jQuery(button).data('uid'), 'security':kboard_settings.ajax_security}, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback === 'function'){
// 				callback(res);
// 			}
// 			else{
// 				if(res.result == 'error'){
// 					alert(res.message);
// 				}
// 				else{
// 					jQuery('.kboard-document-dislike-count', button).text(res.data.dislike);
// 				}
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_comment_like(button, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		jQuery.post(kboard_settings.ajax_url, {'action':'kboard_comment_like', 'comment_uid':jQuery(button).data('uid'), 'security':kboard_settings.ajax_security}, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback === 'function'){
// 				callback(res);
// 			}
// 			else{
// 				if(res.result == 'error'){
// 					alert(res.message);
// 				}
// 				else{
// 					jQuery('.kboard-comment-like-count', button).text(res.data.like);
// 				}
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_comment_dislike(button, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		jQuery.post(kboard_settings.ajax_url, {'action':'kboard_comment_dislike', 'comment_uid':jQuery(button).data('uid'), 'security':kboard_settings.ajax_security}, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback === 'function'){
// 				callback(res);
// 			}
// 			else{
// 				if(res.result == 'error'){
// 					alert(res.message);
// 				}
// 				else{
// 					jQuery('.kboard-comment-dislike-count', button).text(res.data.dislike);
// 				}
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_fields_validation(form, callback){
// 	jQuery('.kboard-attr-row.required', form).each(function(index, element){
// 		var required;
		
// 		if(jQuery(element).hasClass('kboard-attr-content')){
// 			if(kboard_current.use_editor == 'yes'){
// 				if(jQuery('#wp-kboard_content-wrap').hasClass('tmce-active')){
// 					jQuery('#kboard_content').val(tinymce.get('kboard_content').getContent());
// 				}
// 			}
// 			required = jQuery('#kboard_content');
// 		}
// 		else{
// 			required = jQuery(element).find('.required');
// 		}
		
// 		if(jQuery(required).is(':checkbox') || jQuery(required).is(':radio')){
// 			if(jQuery(element).find('.required:checked').length == 0){
// 				alert(kboard_localize_strings.required.replace('%s', jQuery(element).find('.field-name').text()));
// 				callback(jQuery(required).eq(0));
// 				return false;
// 			}
// 		}
// 		else if(jQuery(required).val() == 'default' || !jQuery(required).val()){
// 			if(jQuery(element).hasClass('kboard-attr-address')){
// 				if(!jQuery('.kboard-row-postcode input').val() || !jQuery('.kboard-row-address-1 input').val()){
// 					alert(kboard_localize_strings.required.replace('%s', jQuery(element).find('.field-name').text()));
// 					callback(required);
// 					return false;
// 				}
// 				else{
// 					return true;
// 				}
// 			}
// 			else if(jQuery(element).hasClass('kboard-attr-file')){
// 				if(jQuery('input[name="'+jQuery(element).children('.attr-value').children().attr('name')+'"]').val() == ''){
// 					alert(kboard_localize_strings.required.replace('%s', jQuery(element).find('.field-name').text()));
// 					callback(required);
// 					return false;
// 				}
// 				else{
// 					return true;
// 				}
// 			}
// 			else{
// 				alert(kboard_localize_strings.required.replace('%s', jQuery(element).find('.field-name').text()));
// 				callback(required);
// 				return false;
// 			}
// 		}
// 	});
// }

// function kboard_content_update(content_uid, data, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		jQuery.post(kboard_settings.ajax_url, {'action':'kboard_content_update', 'content_uid':content_uid, 'data':data, 'security':kboard_settings.ajax_security}, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback === 'function'){
// 				callback(res);
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_ajax_builder(args, callback){
// 	if(!kboard_ajax_lock){
// 		kboard_ajax_lock = true;
// 		var callback2 = (typeof callback === 'function') ? callback : args['callback'];
// 		args['action'] = 'kboard_ajax_builder';
// 		args['callback'] = '';
// 		args['security'] = kboard_settings.ajax_security;
// 		jQuery.get(kboard_settings.ajax_url, args, function(res){
// 			kboard_ajax_lock = false;
// 			if(typeof callback2 === 'function'){
// 				callback2(res);
// 			}
// 		});
// 	}
// 	else{
// 		alert(kboard_localize_strings.please_wait);
// 	}
// 	return false;
// }

// function kboard_editor_open_media(){
// 	var w = 900;
// 	var h = 500;
// 	var media_popup_url = kboard_current.add_media_url;
// 	console.log(media_popup_url);
// 	if(kboard_current.board_id){
// 		if(jQuery('#kboard_media_wrapper').length){
// 			jQuery('#kboard_media_wrapper').show();
// 			jQuery('#kboard_media_wrapper').html(jQuery('<iframe frameborder="0"></iframe>').attr('src', media_popup_url));
// 			jQuery('#kboard_media_background').show();
// 		}
// 		else{
// 			var wrapper = jQuery('<div id="kboard_media_wrapper"></div>');
// 			var background = jQuery('<div id="kboard_media_background"></div>').css({opacity:'0.5'}).click(function(){
// 				kboard_media_close();
// 			});
			
// 			function init_window_size(){
// 				if(window.innerWidth <= 900){
// 					wrapper.css({left:0, top:0, margin:'10px', width:(window.innerWidth-20), height:(window.innerHeight-20)});
// 				}
// 				else{
// 					wrapper.css({left:'50%', top:'50%', margin:0, 'margin-left':(w/2)*-1, 'margin-top':(h/2)*-1, width:w, height:h});
// 				}
// 			}
// 			init_window_size();
// 			jQuery(window).resize(init_window_size);
			
// 			wrapper.html(jQuery('<iframe frameborder="0"></iframe>').attr('src', media_popup_url));
// 			jQuery('body').append(background);
// 			jQuery('body').append(wrapper);
			
// 			if(!jQuery('input[name="media_group"]').filter(function(){return this.value==kboard_settings.media_group}).length){
// 				jQuery('[name="board_id"]').parents('form').append(jQuery('<input type="hidden" name="media_group">').val(kboard_settings.media_group));
// 			}
// 		}
// 	}
// }

// function kboard_media_close(){
// 	jQuery('#kboard_media_wrapper').hide();
// 	jQuery('#kboard_media_background').hide();
// }