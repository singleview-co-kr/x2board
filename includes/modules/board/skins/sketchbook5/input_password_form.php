<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}
// <include target="_header.html" />
include $skin_path_abs.'_header.php';
?>
<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" onsubmit="return procFilter(this, input_password)" class="context_message">
	<?php wp_nonce_field('x2b_'.X2B_CMD_PROC_VERIFY_PASSWORD, 'x2b_'.X2B_CMD_PROC_VERIFY_PASSWORD.'_nonce'); ?>
	<input type="hidden" name="cmd" value="<?php echo X2B_CMD_PROC_VERIFY_PASSWORD?>" />
	<input type="hidden" name="verified_cmd" value="<?php echo $cmd ?>" />
	<input type="hidden" name="board_id" value="<?php echo intval($board_id)?>" />
	<input type="hidden" name="page" value="<?php echo intval($page)?>" />
	<input type="hidden" name="post_id" value="<?php echo intval($post_id)?>" />
	<input type="hidden" name="comment_id" value="<?php echo intval($comment_id)?>" />
	<h1><?php echo __('msg_input_password', X2B_DOMAIN)?></h1>
	<input type="password" name="password" title="<?php echo __('lbl_password', X2B_DOMAIN)?>" class="itx" />
	<input class="bd_btn" type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" />
</form>
<?php
// <include target="_footer.html" />
include $skin_path_abs.'_footer.php';
?>