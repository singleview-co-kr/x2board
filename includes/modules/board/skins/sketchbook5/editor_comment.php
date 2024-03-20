<div id="kboard-default-editor" class="bd">
	<form class="kboard-form" method="post" enctype="multipart/form-data">
		<?php $skin->editorHeaderComment($comment, $board)?>
		
		<div class="kbo11ard-attr-row">
            <div class="attr-value">
				<?php wp_editor($comment->content, 'comment_content_'.$comment->uid, array('media_buttons'=>$board->isAdmin(), 'textarea_name'=>'comment_content', 'editor_height'=>200))?>
            </div>
        </div>
		<?php 
		wp_enqueue_style("kboard-jquery-fileupload-css", KBOARD_URL_PATH . '/assets/jquery.fileupload/css/jquery.fileupload.css', [], KBOARD_VERSION);
		wp_enqueue_style("kboard-jquery-fileupload-css", KBOARD_URL_PATH . '/assets/jquery.fileupload/css/jquery.fileupload-ui.css', [], KBOARD_VERSION);
		wp_enqueue_script('kboard-jquery-ui-widget', KBOARD_URL_PATH . '/assets/jquery.fileupload/js/vendor/jquery.ui.widget.js', [], KBOARD_VERSION, true);
		wp_enqueue_script('kboard-jquery-iframe-transport', KBOARD_URL_PATH . '/assets/jquery.fileupload/js/jquery.iframe-transport.js', [], KBOARD_VERSION, true);
		wp_enqueue_script('kboard-fileupload', KBOARD_URL_PATH . '/assets/jquery.fileupload/js/jquery.fileupload.js', [], KBOARD_VERSION, true);
		wp_enqueue_script('kboard-fileupload-process', KBOARD_URL_PATH . '/assets/jquery.fileupload/js/jquery.fileupload-process.js', [], KBOARD_VERSION, true);
		wp_enqueue_script('kboard-fileupload-caller', KBOARD_URL_PATH . '/template/js/file-upload.js', [], KBOARD_VERSION, true);
		$accept_file_types = str_replace(" ", "", kboard_allow_file_extensions());
		$accept_file_types = str_replace(",", "|", $accept_file_types);
		?>
		<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount='<?php echo $board->meta->max_attached_count?>' data-accpet_file_types="<?php echo $accept_file_types?>" data-max_each_file_size_mb="<?php echo $board->meta->max_each_file_size_mb?>">
		<ul class="file-list list-unstyled mb-0">
			<?php foreach($comment->attach as $file_key=>$file_value):?>
				<li class="file my-1 row">
					<div class="file-name col-md-3">
						<img src='<?=$file_value['thumbnail_abs_url']?>' class='attach_thumbnail'>
						<?=$file_value['file_name']?> 
					</div>
					<div class="del-button col-md-1">
						<button type="button" class="btn btn-sm btn-danger file-embed" data-thumbnail_abs_url="<?=$file_value['thumbnail_abs_url']?>" <?php if( $file_value['file_type'] !== 'image'):?>disabled<?php endif?>><i class="fa fa-plus"></i></button>
						<button type="button" class="btn btn-sm btn-danger file-delete" data-file_uid="<?=$file_value['file_uid']?>"><i class="far fa-trash-alt"></i></button>
					</div>
					<div class="progress col-md-7 my-auto px-0">
						<!-- <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 100%;"></div> -->
					</div>
				</li>
			<?php endforeach?>
		</ul>
		
		<div class="kboard-control">
			<div class="center">
				<?php if($comment->uid):?>
				<button type="button" class="kboard-default-button-medium white" onClick="location.href='<?=esc_url($url->getDocumentURLWithUID($comment->content_uid))?>'"><?=__('Back', 'kboard')?></button>
				<!-- <button type="button" class="kboard-default-button-medium white" onClick="location.href='<?=esc_url($url->getBoardList())?>'"><?=__('List', 'kboard')?></button> -->
				<?php else:?>
				<button type="button" class="kboard-default-button-medium white" onClick="location.href='<?=esc_url($url->getBoardList())?>'"><?=__('Back', 'kboard')?></button>
				<?php endif?>
			<!-- </div>
			<div class="right"> -->
				<?php if($board->isWriter()):?>
				<button type="submit" class="kboard-default-button-medium blue"><?=__('Save', 'kboard')?></button>
				<?php endif?>
			</div>
		</div>
	</form>
</div>

<?php wp_enqueue_script('kboard-default-script', "{$skin_path}/js/ed1itor.js", array(), KBOARD_VERSION, true)?>