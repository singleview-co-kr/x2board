<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

// <include target="_header.html" />
include $skin_path_abs.'_header.php';
// <load target="js/editor.js" type="body" />
wp_enqueue_script('x2board-sketchbook5-editor', $skin_url . '/js/editor.js', [], X2B_VERSION, true);
?>

<?php if($o_source_comment->is_exists()): ?><!--@if($oSourceComment->isExists())-->
<div class="context_data">
	<div class="fdb_itm" style="margin:0;padding:0;border:0">
		<div class="meta">
			<b><?php echo esc_html($o_source_comment->get_nick_name())?></b><span class="date"><?php echo esc_html($o_source_comment->get_regdate("Y.m.d H:i"))?></span>
		</div>
		<?php echo $o_source_comment->get_content()?>
	</div>
</div>
<div class="context_message"></div>

<div class="cmt_line">▼</div>

<?php else: ?><!--@else-->
<div class="context_data">
	<h3 class="title">&quot;<?php echo __('cmd_reply', X2B_DOMAIN)?> <?php echo __('cmd_modify', X2B_DOMAIN)?>&quot;</h3>
	<?php if(wp_is_mobile()): ?><!-- cond="wp_is_mobile()" -->
		<p >※ <?php echo __('about_m_editor_notice', X2B_DOMAIN) ?>.</p>
	<?php endif ?>
</div>
<div class="context_message" style="margin-bottom:40px"></div>
<?php endif ?><!--@end-->

<!--// 댓글 수정화면 -->
<!-- onsubmit="<?php if(wp_is_mobile() && $mi->m_editor == ' '): ?>jQuery(this).find('input[name=content]').val(jQuery('#editor').html());<?php endif ?>return procFilter(this, insert_comment)"  -->
<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', '')) ?>" method="post"  class="bd_wrt bd_wrt_main clear" id="x2board-comment-form" >
	<?php x2b_write_comment_hidden_fields(); ?>
	<?php if(!wp_is_mobile() || $mi->m_editor==3): ?><!-- cond="!wp_is_mobile() || $mi->m_editor==3"  -->
		<div class="get_editor">
			<?php x2b_write_comment_editor(); 
			if($use_comment_attach) { // set by user define field
				x2b_write_comment_filebox();
			}?>
		</div>
	<?php endif;
	if(wp_is_mobile() && $mi->m_editor!=3): ?><!-- cond="wp_is_mobile() && $mi->m_editor!=3" -->
		<div class="m_editor">
			<!-- Textarea -->
			<?php if($mi->m_editor==2): ?><!-- <block cond="$mi->m_editor==2"> -->
				<!-- <load target="../../../editor/tpl/js/editor_common.min.js" /> -->
				<!-- <load target="../../../editor/skins/xpresseditor/js/xe_textarea.min.js" /> -->
				<script type="text/javascript" src="<?php echo $skin_url ?>/js/editor_textarea.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-editor_textarea-js"></script>
				<input type="hidden" name="use_html" value="Y" />
				<input type="hidden" id="htm_1" value="n" />
				<textarea id="editor_1" col="50" rows="8"></textarea>
				<script>editorStartTextarea(1);</script>
			<?php endif ?><!-- </block> -->
			<!-- WYSIWYG -->
			<?php if($mi->m_editor == ' '): ?><!-- <block cond="!$mi->m_editor"> -->
				<!-- <load target="css/m_editor.css" />
				<load target="js/editor_wysiwyg.js" />
				<load target="js/bootstrap-wysiwyg.js" />
				<load target="js/jquery.hotkeys.js" /> -->
					<div id="alerts"></div>
					<div class="btn-toolbar clear" data-role="editor-toolbar" data-target="#editor">
						<div class="btn-group">
							<a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
							<a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
							<a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
						</div>
						<div class="btn-group">
							<a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
							<a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
							<!--//
							<a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
							<a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
							-->
						</div>
						<div class="btn-group hide_w320">
							<a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
							<!--//<a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>-->
						</div>
						<div class="btn-group fr">
							<a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
							<a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
						</div>
						<div class="blind"><input type="text" data-edit="inserthtml" id="inserthtml" /></div>
					</div>
					<div id="editor"><p>&nbsp;</p></div>
			<?php endif ?><!-- </block> -->
		</div>
	<?php endif ?>
	<div class="edit_opt">
		<?php if(!$is_logged): ?><!-- <block cond="!$is_logged"> -->
			<span class="itx_wrp">
				<!-- <label for="nick_name"><?php // echo __('lbl_writer', X2B_DOMAIN)?></label> -->
				<input type="text" name="nick_name" id="nick_name" class="itx n_p" value="<?php echo esc_html($o_the_comment->get_nick_name())?>" placeholder="<?php echo __('lbl_writer', X2B_DOMAIN)?>"/>
			</span>
			<span class="itx_wrp">
				<!-- <label for="password"><?php // echo __('lbl_password', X2B_DOMAIN)?></label> -->
				<input type="password" name="password" id="password" class="itx n_p" placeholder="<?php echo __('lbl_password', X2B_DOMAIN)?>"/>
			</span>	
			<!-- <span class="itx_wrp">
				<label for="email_address">{$lang->email_address}</label>
				<input type="text" name="email_address" id="email_address" class="itx m_h" />
			</span>	 -->
		<?php endif ?><!-- </block> -->
	</div>
	<div class="opt_chk clear">
		<?php if($mi->use_status!='PUBLIC'): ?><!-- <block cond="$mi->use_status!='PUBLIC'"> -->
			<input type="checkbox" name="is_secret" value="Y" id="is_secret" <?php if($o_the_comment->get('is_secret')=='Y'): ?>checked="checked" <?php endif ?>/>
			<label for="is_secret"><?php echo __('lbl_secret', X2B_DOMAIN)?></label>
		<?php endif ?><!-- </block> -->
	</div>
	<div class="regist">
		<button type="button" onclick="history.back()" class="bd_btn"><?php echo __('cmd_back', X2B_DOMAIN)?></button>
		<input type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" class="bd_btn blue" id="submit_comment"/>
	</div>
</form>

<?php
// <include target="_footer.html" />
include $skin_path_abs.'_footer.php';
?>