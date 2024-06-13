<include target="_header.html" />
<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" onsubmit="return procFilter(this, input_password)" class="context_message">
<input type="hidden" name="cmd" value="<?php echo X2B_CMD_PROC_VERIFY_PASSWORD?>" />		
<input type="hidden" name="board_id" value="<?php echo intval($board_id)?>" />
	<input type="hidden" name="page" value="<?php echo intval($page)?>" />
	<input type="hidden" name="post_id" value="<?php echo intval($post_id)?>" />
	<input type="hidden" name="comment_id" value="<?php echo intval($comment_id)?>" />
	<h1><?php echo __('msg_input_password', X2B_DOMAIN)?></h1>
	<input type="password" name="password" title="<?php echo __('lbl_password', X2B_DOMAIN)?>" class="itx" />
	<input class="bd_btn" type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" />
</form>
<include target="_footer.html" />