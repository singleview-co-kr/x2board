<div id="kboard-default-editor" class="confirm">
	<form method="post" action="<?php echo esc_url($url->getConfirmExecuteComment($comment->uid))?>">
		<div class="kboard-attr-row kboard-confirm-row">
			<label class="attr-name"><?php echo __('Password', 'kboard')?></label>
			<div class="attr-value">
				<input type="password" name="password" placeholder="<?php echo __('Password', 'kboard')?>..." autofocus required>
				<?php if($board->isConfirmFailed()):?>
					<div class="description"><?php echo __('※ Your password is incorrect.', 'kboard')?></div>
				<?php endif?>
			</div>
		</div>
		<div class="kboard-control">
			<div class="left">
				<?php if($comment->uid && kboard_mod() != 'editor_comment'):?>
					<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-default-button-small"><?php echo __('List', 'kboard')?></a>
				<?php endif?>
				<a href="<?php echo esc_url($url->getDocumentURLWithUID($comment->content_uid))?>" class="kboard-default-button-small"><?php echo __('Document', 'kboard')?></a>
			</div>
			<div class="right">
				<button type="submit" class="kboard-default-button-small"><?php echo __('Password confirm', 'kboard')?></button>
			</div>
		</div>
	</form>
</div>