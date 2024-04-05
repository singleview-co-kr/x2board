<div id="kboard-default-editor" class="bd">
	<form class="kboard-form" method="post" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" enctype="multipart/form-data" onsubmit="return kboard_editor_execute(this);">
		<?php x2b_write_post_input_fields(); ?>
		
		<div class="kboard-control">
			<div class="center">
				<button type="button" class="kboard-default-button-medium white" onClick="history.back()"><?php echo __('Back', 'x2board')?></button>
				<button type="submit" class="kboard-default-button-medium blue"><?php echo __('Save', 'kboard')?></button>
			</div>
		</div>
	</form>
</div>

<?php //wp_enqueue_script('kboard-default-script', "{$skin_path}/js/editor.js", array(), X2B_VERSION, true)?>