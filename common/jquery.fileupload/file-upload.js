jQuery(function() {  // jquery.fileupload on document write screen
	const n_board_id = jQuery("input[name=board_id]").val();
	const n_post_id = jQuery("input[name=post_id]").val();
	const n_editor_call_id = jQuery('.file-upload').data("editor_call_id");
	const n_comment_id = jQuery("input[name=comment_id]").val();
    jQuery(".file-upload").fileupload({
        dataType: "json",
        // maxChunkSize: 2500000, // split upload for large file
        sequentialUploads: false,
		url: x2board_ajax_info.url,
		formData: {'action': x2board_ajax_info.cmd_file_upload,
				   'security':x2board_ajax_info.nonce,
				   'board_id':n_board_id,
				   'post_id':n_post_id,
				   'comment_id':n_comment_id,
				   'editor_call_id':n_editor_call_id },
        add: function(e, data) {
            // check file total count
			var maxFileCount = parseInt(jQuery(this).data("maxfilecount"));
            if (isNaN(maxFileCount))
                maxFileCount = 1;
			var $t = jQuery(this);
			var $w = $t.closest("div");
			var $li = $w.find("ul.file-list li");
			if ($li.length >= maxFileCount) {
				// var txt = $w.find("label").text();
				alert("첨부 파일 (" + $li.length + " / " + maxFileCount + ")\n기존 파일을 삭제하신 후 업로드하세오.");
				return false;
			}
			// check each file size
			var maxEachFileSizeMb = parseFloat(jQuery(this).data("max_each_file_size_mb"));
            if (isNaN(maxEachFileSizeMb))
				maxEachFileSizeMb = 1;  // default 1 MB 
			var too_large_file_detected = false;
			maxEachFileSizeByte = maxEachFileSizeMb * 1000000;
			data.files.forEach(function(uploading_file_info) {
				if( maxEachFileSizeByte < uploading_file_info.size ) {  // bytes
					too_large_file_detected = true;
               		return false;  // exit forEach loop
				} 
			});
			if( too_large_file_detected ) {
                alert("개별 파일의 최대 용량은 " + maxEachFileSizeMb + "MB 입니다");
				return false;
			}
            // check file extension
			const accpet_file_types = jQuery(this).data("accpet_file_types");
			const re_accpet_file_types = new RegExp(accpet_file_types);  //  /(gif|jpe?g|png|pdf|doc|docx)$/i;
			const re_file_info = /(?:\.([^.]+))?$/;
			const file_ext = re_file_info.exec(data.files[0].name)[1];
			const re_rst = file_ext.match(re_accpet_file_types);
			if( re_rst === undefined || re_rst === null ) {
				alert("허용하지 않는 파일입니다");
                return false;
			}
			// append new file info
            data.context = jQuery('<li class="file my-1 row"></li>')
                .append(jQuery('<div class="file-name col-md-3 text-muted"></div>').text(data.files[0].name))
                .append('<div class="del-button col-md-1"></div>')
				.append('<div class="progress col-md-7 my-auto px-0"><div class="progress-bar progress-bar-striped bg-info" role="progressbar"></div></div>')
                .appendTo(jQuery(this).siblings(".file-list"));
			data.submit();
        },
        progress: function(e, data) {
            var progress = parseInt((data.loaded / data.total) * 100, 10);
            data.context.find(".progress-bar").css("width", progress + "%");
        },
        done: function(e, data) {
            var res = data.result.files[0];
			var file_id = res.file_id;
			var thumbnail_abs_url = res.thumbnail_abs_url;
			var file_type = res.file_type;
			let n_reserved_comment_id = res.reserved_comment_id;
            if (res.error !== "") {
                data.context.remove();
                alert(res.error);
                return false;
            }
			// memorize a reserved editor_sequence to find comment id if uploading a file
			if( n_reserved_comment_id ){
				jQuery("input[name=editor_sequence]").val(n_editor_call_id);
			}

            jQuery(this.form)
                .find("input[type=hidden]:last")
                .after('<input type="hidden" name="upload_file_id[]" value="' + file_id + '">');
			
			var append_btn_disabled = '';
			if( file_type !== 'image'){
				append_btn_disabled = 'disabled';	
			}
			var button_html = '<button type="button" class="btn btn-sm btn-danger file-embed" data-thumbnail_abs_url="' + thumbnail_abs_url + '" ' + append_btn_disabled + '><i class="fa fa-plus"></i></button> ' 
			button_html += '<button type="button" class="btn btn-sm btn-danger file-delete" data-file_id="' + file_id + '"><i class="far fa-trash-alt"></i></button>';
			data.context
                .find(".file-name")
                .removeClass("text-muted")
				.prepend(' <img src="' + thumbnail_abs_url + '" class="attach_thumbnail">')
                .append(' <span class="badge badge-success"><i class="fas fa-check"></i></span>')
                .end()
                .find(".del-button")
                .append(button_html);
        }
    });
    jQuery(document).on("click", ".file-embed", function(e) {
        var $t   = jQuery(this);
        var thumbnail_abs_url  = $t.data("thumbnail_abs_url");
		x2board_media_insert(thumbnail_abs_url);
    });
    jQuery(document).on("click", ".file-delete", function(e) {
		if(!confirm("파일을 삭제하시겠습니까?"))
            return false;

		var $t   = jQuery(this);
        var file_id  = $t.data("file_id");
		jQuery.post(x2board_ajax_info.url, {
			'action':x2board_ajax_info.cmd_file_delete,
			'board_id':n_board_id,
			'file_id':file_id,
			'post_id':n_post_id,
			'comment_id':n_comment_id,
			'editor_call_id':n_editor_call_id,
			'security':x2board_ajax_info.nonce},
			function(res) {
				if(typeof callback === 'function'){
					callback(res);
				}
				else {
					if(res.result == 'error') {
						console.log("message:"+res.message);
					}
					else{
						$t.closest("li").remove();
						jQuery("input[name='upload_file_id[]']").each(function(i) {
							var $el = jQuery(this);
							if ($el.val() == file_id)
								$el.remove();
						});
					}
				}
			}
		);
    });
});

function x2board_media_insert(media_src){
	if(media_src){
		parent.kboard_editor_insert_media(media_src);
	}
}