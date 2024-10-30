/**
 * @brief 모바일 수정 UX에서만 작동
 */
jQuery(document).ready(function() {
	jQuery("#x2board-comment-form").submit(function(event) {
		var s_new_content= editorGetContentTextarea(1);
		jQuery("input[name=content]").val(s_new_content);
	});
}); // https://dreamjy.tistory.com/27

/**
 * @brief 기존 글 내용을 편집용 textarea에 복사함
 */
function editorStartTextarea(editor_sequence) {
    var obj = document.getElementById('editor_'+editor_sequence);
    var use_html = document.getElementById('htm_'+editor_sequence).value;
    obj.form.setAttribute('editor_sequence', editor_sequence);

    obj.style.width = '100%';
    var content = obj.form["content"].value;
    if(use_html) {
        content = content.replace(/<br([^>]*)>/ig,"\n");
        if(use_html!='br') {
            content = content.replace(/&lt;/g, "<");
            content = content.replace(/&gt;/g, ">");
            content = content.replace(/&quot;/g, '"');
            content = content.replace(/&amp;/g, "&");
        }
    }
    obj.value = content;
}

/**
 * @brief 새 글 내용을 hidden input에 복사함
 */
function editorGetContentTextarea(editor_sequence) {
    var obj = document.getElementById('editor_'+editor_sequence);
    var use_html = document.getElementById('htm_'+editor_sequence).value;
    var content = obj.value.trim();
    if(use_html) {
        if(use_html!='br') {
            content = content.replace(/&/g, "&amp;");
            content = content.replace(/</g, "&lt;");
            content = content.replace(/>/g, "&gt;");
            content = content.replace(/\"/g, "&quot;");
        }
        content = content.replace(/(\r\n|\n)/g, "<br />");
    }
    return content;
}

/*function frmSubmit(){
	var a = '';
	var b = jQuery('#nText');
	var c = b.val();
	c = c.replace(/(\r\n|\n)/g, "<br>");
	if(jQuery('#files .select').length){
		jQuery('#files .select').each(function(){
			var type = jQuery(this).find('button').attr('data-type');
			if(type=='img'){
				a = a+'<p><img src="'+jQuery(this).find('button').attr('data-file')+'" alt="'+jQuery(this).find('button').attr('title')+'" /></p>';
				a = a.replace(x2board_path.modules_path_name + '/board/skins/sketchbook5/','');
				a = a.replace(x2board_path.modules_path_name + '/board/m.skins/sketchbook5/','');
			} else if(type=='music'){
				a = a+'<div><audio src="'+jQuery(this).find('button').attr('data-file')+'" controls="controls">Your browser does not support this file type. You can download <a href="'+jQuery(this).find('button').attr('data-dnld')+'" style="text-decoration:underline">'+jQuery(this).find('small').text()+'</a> and play it!</audio></div>';
			} else if(type=='media'){
				a = a+'<div><video src="'+jQuery(this).find('button').attr('data-file')+'" controls="controls">Your browser does not support this file type. You can download <a href="'+jQuery(this).find('button').attr('data-dnld')+'" style="text-decoration:underline">'+jQuery(this).find('small').text()+'</a> and play it!</video></div>';
			} else {
				a = a+'<p><a href="'+jQuery(this).find('button').attr('data-dnld')+'" style="text-decoration:underline">'+jQuery(this).find('small').text()+'</a></p>';
			};
		});
		if(jQuery('#m_img_upoad_2:checked').length){
			c = c+a;
		} else {
			c = a+c;
		};
	};
	jQuery('#ff input[name=content]').val(c);
	var frm = document.getElementById('ff');
	procFilter(frm, insert);
	jQuery('#frmSubmit').attr('disabled','disabled');
}

jQuery(function($){
	$('#nText').html($('#ff input[name=content]').val().replace(/<br([^>]*)>/ig,"\n"));
});*/
