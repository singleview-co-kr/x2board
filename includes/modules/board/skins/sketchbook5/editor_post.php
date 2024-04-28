<div id="x2board-default-editor" class="bd">
<!-- onsubmit="return x2board_editor_execute(this);" -->
	<form class="x2board-form" method="post" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" enctype="multipart/form-data">  
		<?php x2b_write_post_input_fields(); ?>
		
		<div class="x2board-control">
			<div class="center">
				<button type="button" class="x2board-default-button-medium white" onClick="history.back()"><?php echo __('Back', 'x2board')?></button>
				<button type="submit" class="x2board-default-button-medium blue"><?php echo __('Save', 'x2board')?></button>
			</div>
		</div>
	</form>
</div>

<?php //wp_enqueue_script('x2board-default-script', "{$skin_url}/js/editor.js", array(), X2B_VERSION, true)?>
<?php wp_enqueue_style('x2board-sketchbook5-style', "{$skin_url}/style.css", array(), X2B_VERSION, 'all')?>