<div id="kboard-default-editor" class="confirm">
	<div class="kboard-attr-row kboard-confirm-row">
		<label class="attr-name"><?php echo __('Message', 'x2board')?></label>
		<div class="attr-value">
			<?php echo __('please pay attention', 'x2board')?>
			<div class="description"><?php echo $message?></div>
		</div>
	</div>
	<div class="kboard-control">
		<div class="left">
			<?php if($content->uid && kboard_mod() != 'document'):?>
			<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="kboard-default-button-small"><?php echo __('Document', 'kboard')?></a>
			<?php endif?>
			<a href="<?php echo esc_url($url->getBoardList())?>" class="kboard-default-button-small"><?php echo __('List', 'kboard')?></a>
		</div>
		<div class="right">
			<button type="submit" class="kboard-default-button-small"><?php echo __('Password confirm', 'kboard')?></button>
		</div>
	</div>
</div>