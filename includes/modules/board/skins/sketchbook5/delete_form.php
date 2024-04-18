<!-- <include target="_header.html" /> -->

<?php if($oPost->is_exists()):?>
<div class="context_data">
	<h3 class="title"><?php echo esc_html($oPost->get_title())?></h3>
	<p class="author">
		<strong><?php echo esc_html($oPost->get_nick_name())?></strong>
	</p>
</div>
<?php endif?>
<form action="./" method="get" class="context_message">   <!-- onsubmit="return procFilter(this, delete_document)"  -->
	<input type="hidden" name="cmd" value="<?php echo X2B_CMD_PROC_DELETE_POST?>" />	
	<input type="hidden" name="board_id" value="<?php echo intval($board_id)?>" />
	<input type="hidden" name="page" value="<?php echo intval($page)?>" />
	<input type="hidden" name="post_id" value="<?php echo intval($post_id)?>" />
	<h1><?php echo __('Do you want to delete?', 'x2board')?></h1>
	<div class="btnArea">
		<input type="submit" class="btn" value="<?php echo __('Delete', 'x2board')?>" />
		<button type="button" class="btn" onclick="history.back()"><?php echo __('Cancel', 'x2board')?></button>
	</div>
</form>
<!-- <include target="_footer.html" /> -->
