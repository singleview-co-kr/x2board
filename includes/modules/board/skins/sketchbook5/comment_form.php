<!-- <include target="_header.html" /> -->
<?php if($o_source_comment->is_exists()): ?>
    <!-- cond="$source_comment->isExists()"  -->
    <div class="context_data">
        <h3 class="author">
            <strong><?php echo esc_html($o_source_comment->get_nick_name())?></strong>
        </h3>
        <?php echo $o_source_comment->get_content()?>
    </div>
<?php endif?>
<div class="feedback">
    <!-- onsubmit="return procFilter(this, insert_comment)" -->
	<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" class="write_comment">
		<?php x2b_write_comment_hidden_fields(); ?>
        <?php x2b_write_comment_editor(); ?>
		<div class="write_author">

<?php if(!$is_logged): ?>
			<span class="item">
				<label for="userName" class="iLabel"><?php echo __('writer', 'x2board')?></label>
				<input type="text" name="nick_name" id="userName" class="iText userName" value="<?php echo $o_the_comment->get_nick_name()?>" />
			</span>
			<span class="item">
				<label for="userPw" class="iLabel"><?php echo __('password', 'x2board')?></label>
				<input type="password" name="password" id="userPw" class="iText userPw" />
			</span>
			<!-- <span class="item">
				<label for="homePage" class="iLabel"><?php echo __('homepage', 'x2board')?></label>
				<input type="text" name="homepage" id="homePage" class="iText homePage" value="<?php echo htmlspecialchars($o_the_comment->get('homepage'))?>" />
			</span> -->
<?php endif?>
<?php if($is_logged):
	$s_checked = $o_the_comment->get('notify_message') == 'Y' ? 'checked="checked"' : '';
?>
			<input type="checkbox" name="notify_message" value="Y" <?php echo $s_checked?> id="notify_message" class="iCheck" />
			<label for="notify_message"><?php echo __('notify', 'x2board')?></label>
<?php endif?>
<?php if($current_module_info->secret=='Y'):
	$s_checked = $o_the_comment->get('is_secret') == 'Y' ? 'checked="checked"' : '';
?>
			<input type="checkbox" name="is_secret" value="Y" id="is_secret" <?php echo $s_checked?> class="iCheck" />
			<label for="is_secret"><?php echo __('secret', 'x2board')?></label>
<?php endif?>			
		</div>
		<div class="btnArea">
			<button type="submit" class="btn"><?php echo __('cmd_comment_registration', 'x2board')?></button>
		</div>
	</form>
</div>
<!-- <include target="_footer.html" /> -->
