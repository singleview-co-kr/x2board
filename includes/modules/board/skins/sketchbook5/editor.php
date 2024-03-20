<div id="kboard-default-editor" class="bd">
	<form class="kboard-form" method="post" action="<?=esc_url($url->getContentEditorExecute())?>" enctype="multipart/form-data" onsubmit="return kboard_editor_execute(this);">
		<?php $skin->editorHeader($content, $board)?>
		
		<?php foreach($board->fields()->getSkinFields() as $key=>$field):?>
			<?=$board->fields()->getTemplate($field, $content, $boardBuilder)?>
		<?php endforeach?>
		
		<div class="kboard-control">
			<div class="center">
				<?php if($content->uid):?>
				<button type="button" class="kboard-default-button-medium white" onClick="location.href='<?=esc_url($url->getDocumentURLWithUID($content->uid))?>'"><?=__('Back', 'kboard')?></button>
				<button type="button" class="kboard-default-button-medium white" onClick="location.href='<?=esc_url($url->getBoardList())?>'"><?=__('List', 'kboard')?></button>
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

<?php wp_enqueue_script('kboard-default-script', "{$skin_path}/js/editor.js", array(), KBOARD_VERSION, true)?>