<div id="kboard-default-editor" class="bd">
	<form class="kboard-form" method="post" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', '', 'page', ''))?>" enctype="multipart/form-data">
		<?php x2b_write_comment_hidden_fields();
$board_meta_max_attached_count = 1;
$accept_file_types = null;
$board_meta_max_each_file_size_mb = 0;	
$o_comment->attach = array();
?>
		
		<div class="kboard-attr-row">
            <div class="attr-value">
				<?php echo x2b_write_comment_content_editor() ?>
            </div>
        </div>
		<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount='<?php echo $board_meta_max_attached_count?>' data-accpet_file_types="<?php echo $accept_file_types?>" data-max_each_file_size_mb="<?php echo $board_meta_max_each_file_size_mb?>">
		<ul class="file-list list-unstyled mb-0">
			<?php foreach($o_comment->attach as $file_key=>$file_value):?>
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
				<?php if($o_comment->comment_id):?>
				<button type="button" class="kboard-default-button-medium white" onClick="history.back()"><?=__('Back to post', 'kboard')?></button>
				<!-- <button type="button" class="kboard-default-button-medium white" onClick="location.href='<?php //echo esc_url($url->getBoardList())?>'"><?=__('List', 'kboard')?></button> -->
				<?php else:?>
				<!-- <button type="button" class="kboard-default-button-medium white" onClick="location.href='<?php //echo esc_url(x2b_get_url())?>'"><?=__('Back to list', 'kboard')?></button> -->
				<?php endif?>
			<!-- </div>
			<div class="right"> -->
				<?php if($this->grant->write_comment): //if($board->isWriter()):?>
				<button type="submit" class="kboard-default-button-medium blue"><?=__('Save', 'kboard')?></button>
				<?php endif?>
			</div>
		</div>
	</form>
</div>

<?php wp_enqueue_script('kboard-default-script', "{$skin_url}/js/ed1itor.js", array(), KBOARD_VERSION, true)?>