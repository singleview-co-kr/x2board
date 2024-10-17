<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>

<div class="cmt_editor" <?php if($mi->cmt_wrt_position=='cmt_wrt_btm' && $post->get_comment_count()):?>style="margin-top:30px"<?php endif ?>>

<label for="editor_<?php echo $post->post_id?>" class="cmt_editor_tl fl"><em>✔</em><strong><?php echo __('cmd_write_comment', X2B_DOMAIN)?></strong></label>
<!--// Editor Select -->
<?php if($mi->select_editor!='N'):?><!-- cond="$mi->select_editor!='N'"  -->
	<div class="editor_select bubble fr m_no" title="<?php echo __('desc_noti_rfsh', X2B_DOMAIN) ?>">
		<a class="tg_btn2" href="#" data-href="#editor_select"><em class="fa fa-info-circle bd_info_icon"></em> <?php echo __('cmd_select_editor', X2B_DOMAIN)?></a>
		<?php if($rd_idx==0):?> <!-- cond="$rd_idx==0"  -->
			<div id="editor_select" class="tg_cnt2 wrp m_no"><button type="button" class="tg_blur2"></button>
				<a <?php if($mi->cmt_wrt=='simple'):?> class="on" <?php endif ?> href="#" onclick="jQuery.cookie('bd_editor','simple');location.reload();return false"><em>✔ </em><?php echo __('lbl_textarea_editor_mode', X2B_DOMAIN)?></a>
				<a <?php if($mi->cmt_wrt=='editor'):?> class="on" <?php endif ?> href="#" onclick="jQuery.cookie('bd_editor','editor');location.reload();return false"><em>✔ </em><?php echo __('lbl_wysiwyg_editor_mode', X2B_DOMAIN)?></a>
				<?php if($mi->select_editor=='2' || $mi->cmt_wrt=='sns'):?><!-- cond="$mi->select_editor=='2' || $mi->cmt_wrt=='sns'"  -->
					<a <?php if($mi->cmt_wrt=='sns'):?> class="on" <?php endif ?> href="#" onclick="jQuery.cookie('bd_editor','sns');location.reload();return false"><em>✔ </em><?php echo __('lbl_sxc_editor_mode', X2B_DOMAIN)?></a>
				<?php endif ?>
				<span class="edge"></span><button type="button" class="tg_blur2"></button>
				<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
			</div>
		<?php endif ?>
	</div>
<?php endif ?>

<?php if($grant->write_comment && $post->is_enable_comment()):?><!--@if($grant->write_comment && $oDocument->isEnableComment())-->
	<!-- onsubmit="return procFilter(this, insert_comment)"  -->
	<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" id="x2board-comment-form" class="bd_wrt cmt_wrt clear">
		<?php x2b_write_comment_hidden_fields(); ?>
		<?php if($mi->cmt_wrt=='editor'):?><!-- cond="$mi->cmt_wrt=='editor'"  -->
			<div class="wysiwyg"><?php x2b_write_comment_editor(); ?></div>
		<?php endif ?>
		<?php if($mi->cmt_wrt=='simple'):
			wp_enqueue_script(X2B_DOMAIN.'-comment-validation', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/js/comment_validation.js', [X2B_JQUERY_VALIDATION], X2B_VERSION, true);	
		?><!-- cond="$mi->cmt_wrt=='simple'" -->
			<div class="simple_wrt">
				<!-- <img cond="$logged_info->profile_image->src" class="profile img" src="{$logged_info->profile_image->src}" alt="profile" /> -->
				<!-- cond="!$logged_info->profile_image->src"  -->
				<span class="profile img no_img">?</span>
				<div class="text">
					<input type="hidden" name="use_html" value="Y" />
					<!-- <input type="hidden" id="htm_<?php //echo $post->post_id?>" value="n" /> -->
					<textarea name="content" id="editor_<?php echo $post->post_id?>" cols="50" rows="4" required></textarea>
				</div>
				<input type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" class="bd_btn" style="color: #444; background: #F3F3F3 repeat-x;"/>
			</div>
		<?php endif ?>			
		<div class="edit_opt clear" <?php if($mi->cmt_wrt!='editor'):?> style="display:none" <?php endif ?> >	<!-- |cond="$mi->cmt_wrt!='editor'" -->
			<?php if(!$is_logged):?><!-- <block cond="!$is_logged"> -->
				<span class="itx_wrp">
					<input type="text" name="nick_name" id="nick_name_<?php echo $post->post_id?>" class="itx m_h" required="required" placeholder="<?php echo __('lbl_writer', X2B_DOMAIN)?>"/>
				</span>
				<span class="itx_wrp">
					<input type="password" name="password" id="password_<?php echo $post->post_id?>" class="itx m_h" required="required" placeholder="<?php echo __('lbl_password', X2B_DOMAIN)?>"/>
				</span>
			<?php endif ?>	<!-- </block> -->
			
			<div class="opt_chk">
				<!-- <block cond="$is_logged">
					<input type="checkbox" name="notify_message" value="Y" id="notify_message_<?php //echo $post->post_id?>"  checked="checked"|cond="@in_array('notify',$mi->wrt_opt)" />		
					<label for="notify_message_<?php //echo $post->post_id?>"><?php //echo __('notify', X2B_DOMAIN)?></label>
				</block> -->
				<?php if($mi->use_status!='PUBLIC'):?><!-- <block cond="$mi->use_status!='PUBLIC'"> -->
					<input type="checkbox" name="is_secret" value="Y" id="is_secret_<?php echo $post->post_id?>" /> <!-- checked="checked"|cond="@in_array('secret',$mi->wrt_opt)" -->
					<label for="is_secret_<?php echo $post->post_id?>"><?php echo __('lbl_secret', X2B_DOMAIN)?></label>
				<!-- </block> -->
				<?php endif ?>
			</div>
		</div>
	</form>

	<!--// 대댓글 -->
	<?php if($rd_idx==0):?><!-- cond="$rd_idx==0"  -->
		<div id="re_cmt">
			<label for="editor_2" class="cmt_editor_tl fl"><i class="fa fa-share fa-flip-vertical re"></i><strong><?php echo __('cmd_write_comment', X2B_DOMAIN)?></strong></label>
			<div class="editor_select fr">
				<a class="wysiwyg m_no" href="#"><em class="fa fa-info-circle bd_info_icon"></em> <?php echo __('cmd_use_wysiwyg', X2B_DOMAIN)?></a>
				<a class="close" href="#" onclick="jQuery('#re_cmt').fadeOut().parent().find('.re_comment').focus();return false"><i class="fa fa-times"></i> <?php echo __('cmd_close', X2B_DOMAIN)?></a>
			</div>
			<!-- onsubmit="return procFilter(this,insert_comment)" -->
			<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" class="bd_wrt clear" id="x2board-comment-form" >
				<?php x2b_write_comment_hidden_fields_embeded_editor(); ?>
				<input type="hidden" name="use_html" value="Y" /> 

				<div class="simple_wrt">
					<input type="hidden" id="htm_2" value="n" />
					<textarea name="content" id="editor_2" cols="50" rows="8"></textarea>
				</div>
				<div class="edit_opt">
					<?php if(!$is_logged):?><!-- <block cond="!$is_logged"> -->
						<span class="itx_wrp">
							<input type="text" name="nick_name" id="nick_name" class="itx n_p" required placeholder="<?php echo __('lbl_writer', X2B_DOMAIN)?>"/>
						</span>
						<span class="itx_wrp">
							<input type="password" name="password" id="password" class="itx n_p" required placeholder="<?php echo __('lbl_password', X2B_DOMAIN)?>"/>
						</span>	
					<?php endif ?><!-- </block> -->
					<input type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" class="bd_btn fr" style="color: #444; background: #F3F3F3 repeat-x;"/>
				</div>
				<span class="opt_chk">
					<!-- <block cond="$is_logged">
						<input type="checkbox" name="notify_message" value="Y" id="notify_message" checked="checked"|cond="@in_array('notify',$mi->wrt_opt)" />
						<label for="notify_message"><?php //echo __('notify', X2B_DOMAIN)?></label>
					</block> -->
					<?php if($mi->use_status!='PUBLIC'):?><!-- <block cond="$mi->use_status!='PUBLIC'"> -->
						<input type="checkbox" name="is_secret" value="Y" id="is_secret" /> <!-- checked="checked"|cond="@in_array('secret',$mi->wrt_opt)"  -->
						<label for="is_secret"><?php echo __('lbl_secret', X2B_DOMAIN)?></label>
					<?php endif ?><!-- </block> -->
				</span>
			</form>
		</div>
	<?php endif ?>
<?php else: ?><!--@else-->
	<div class="bd_wrt clear">
		<div class="simple_wrt">
			<span class="profile img no_img">?</span>
			<div class="text">
				<?php if(!$is_logged):?><!-- cond="!$is_logged"  -->
					<a class="cmt_disable bd_login" href="#"><?php echo __('cmd_write_comment', X2B_DOMAIN)?> <?php echo __('msg_not_permitted', X2B_DOMAIN)?> <?php echo __('desc_bd_login', X2B_DOMAIN) ?></a>
				<?php endif ?>
				<?php if($is_logged):?><!-- cond="$is_logged"  -->
					<div class="cmt_disable bd_login"><?php echo __('cmd_write_comment', X2B_DOMAIN)?> <?php echo __('msg_not_permitted', X2B_DOMAIN)?></div>
				<?php endif ?>					
			</div>
			<input type="button" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" disabled="disabled" class="bd_btn" />
		</div>
	</div>
<?php endif ?><!--@end-->

</div>