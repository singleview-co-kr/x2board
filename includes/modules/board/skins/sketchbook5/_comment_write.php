<?php 
$mi_cmt_wrt_position ='cmt_wrt_btm';
$mi_cmt_wrt ='editor';
$mi_use_status='PUBLIC';
if( $mi_cmt_wrt_position=='cmt_wrt_btm' && $post->get_comment_count() ){
	$s_style = 'margin-top:30px';
}
else {
	$s_style = null;
}
?>
<div class="cmt_editor" style="<?php echo $s_style?>">

	<label for="editor_{$post->post_id}" class="cmt_editor_tl fl"><em>✔</em><strong><?php echo __('write_comment', 'x2board')?></strong></label>
	<!--// Editor Select -->
	<!-- <div cond="$mi->select_editor!='N'" class="editor_select bubble fr m_no" title="{$lang->noti_rfsh}">
		<a class="tg_btn2" href="#" data-href="#editor_select"><em class="fa fa-info-circle bd_info_icon"></em> {$lang->select_editor}</a>
		<div cond="$rd_idx==0" id="editor_select" class="tg_cnt2 wrp m_no"><button type="button" class="tg_blur2"></button>
			<a class="on"|cond="$mi->cmt_wrt=='simple'" href="#" onclick="jQuery.cookie('bd_editor','simple');location.reload();return false"><em>✔ </em>{$lang->textarea}</a>
			<a class="on"|cond="$mi->cmt_wrt=='editor'" href="#" onclick="jQuery.cookie('bd_editor','editor');location.reload();return false"><em>✔ </em>{$lang->wysiwyg}</a>
			<a cond="$mi->select_editor=='2' || $mi->cmt_wrt=='sns'" class="on"|cond="$mi->cmt_wrt=='sns'" href="#" onclick="jQuery.cookie('bd_editor','sns');location.reload();return false"><em>✔ </em>{$lang->sxc_editor}</a>
			<span class="edge"></span><button type="button" class="tg_blur2"></button> -->
			<!--// ie8; --><!---i class="ie8_only bl"></i><i class="ie8_only br"></i>
		</div>
	</div--->

	<!--@if($grant->write_comment && $post->isEnableComment())-->
	<?php if($grant->write_comment && $post->is_enable_comment()):?>
		<form id="kboard-comments-form-<?php echo $post->post_id?>" class="bd_wrt cmt_wrt clear" method="post" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" enctype="multipart/form-data" onsubmit="return kboard_comments_execute(this);">
			<?php echo $comment_hidden_field_html ?>

			<div class="kboard-comments-form">
				<!-- <div cond="$mi->cmt_wrt=='editor'" class="wysiwyg">{$oDocument->getCommentEditor()}</div>
				<div cond="$mi->cmt_wrt=='simple'" class="simple_wrt">
					<img cond="$logged_info->profile_image->src" class="profile img" src="{$logged_info->profile_image->src}" alt="profile" />
					<span cond="!$logged_info->profile_image->src" class="profile img no_img">?</span>
					<div class="text">
						<input type="hidden" name="use_html" value="Y" />
						<input type="hidden" id="htm_{$oDocument->document_srl}" value="n" />
						<textarea id="editor_{$oDocument->document_srl}" cols="50" rows="4"></textarea>
					</div>
					<input type="submit" value="{$lang->cmd_submit}" class="bd_btn" />
				</div> -->
				<div class="simple_wrt">
					<!-- <span class="profile img no_img">?</span>		 -->
					<div class="text">
						<?php echo $comment_editor_html ?>
					</div>
					<div class="text">
						<?php
						// 댓글 입력 필드 시작
						ob_start();
						?>
						<?php if(is_user_logged_in()):?>
							<input type="hidden" name="member_display" value="<?php echo 'member_display'?>">
						<?php else:?>
							<input type="text" id="comment_member_display_<?php echo $post->post_id?>" name="member_display" placeholder="<?php echo __('Author', 'x2board')?>" value="<?php //echo $temporary->member_display?>" class="itx n_p" required>
							<input type="password" name="password" placeholder="<?php echo __('Password', 'x2board')?>" id="comment_password_<?php echo $post->post_id?>" class="itx n_p" required>
						<?php endif?>
						<?php if(false): //$board->useCAPTCHA()):?>
							<?php if(kboard_use_recaptcha()):?>
								<div class="comments-field field-recaptcha">
									<div class="g-recaptcha" data-sitekey="<?php echo kboard_recaptcha_site_key()?>"></div>
								</div>
							<?php else:?>
								<label class="comments-field-label" for="comment_captcha"><img src="<?php echo kboard_captcha()?>" alt=""></label>
								<input type="text" name="captcha" placeholder='CAPTCHA' id="comment_captcha" class="itx n_p" required>
							<?php endif?>
						<?php endif?>

						<?php if(false): //$board->isCommentAttach()):
						// wp_enqueue_style("kboard-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload.css', [], X2B_VERSION);
						// wp_enqueue_style("kboard-jquery-fileupload-css", X2B_URL . '/assets/jquery.fileupload/css/jquery.fileupload-ui.css', [], X2B_VERSION);
						// wp_enqueue_script('kboard-jquery-ui-widget', X2B_URL . '/assets/jquery.fileupload/js/vendor/jquery.ui.widget.js', [], X2B_VERSION, true);
						// wp_enqueue_script('kboard-jquery-iframe-transport', X2B_URL . '/assets/jquery.fileupload/js/jquery.iframe-transport.js', [], X2B_VERSION, true);
						// wp_enqueue_script('kboard-fileupload', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload.js', [], X2B_VERSION, true);
						// wp_enqueue_script('kboard-fileupload-process', X2B_URL . '/assets/jquery.fileupload/js/jquery.fileupload-process.js', [], X2B_VERSION, true);
						// wp_enqueue_script('kboard-fileupload-caller', X2B_URL . '/template/js/file-upload.js', [], X2B_VERSION, true);
						// $accept_file_types = str_replace(" ", "", kboard1_allow_file_extensions());
						// $accept_file_types = str_replace(",", "|", $accept_file_types);
						?>
						<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount='<?php echo $board->meta->max_attached_count?>' data-accpet_file_types="<?php echo $accept_file_types?>" data-max_each_file_size_mb="<?php echo $board->meta->max_each_file_size_mb?>">
						<ul class="file-list list-unstyled mb-0"></ul>
						<?php endif?>
						
						<div class="comments-field field-comment-hide">
							<?php if(false): //$board->meta->comments_username_masking == '2'):?>
								<label class="comments-field-label" for="comment_hide_<?php echo $post->post_id?>"><?php echo __('작성자 숨기기', 'x2board')?></label>
								<input type="checkbox" id="comment_hide_<?php echo $post->post_id?>" name="comment_option_hide" value="1"<?php if($temporary->comment_hide):?> checked<?php endif?>><label class="comments-field-label" for="comment_hide_<?php echo $post->post_id?>">작성자 숨기기</label>
							<?php endif?>
						</div>
						
						<div class="comments-field field-comment-anonymous">
							<?php if(false): // $board->meta->comments_anonymous == '2'):?>
								<label class="comments-field-label" for="comment_anonymous_<?php echo $post->post_id?>"><?php echo __('익명댓글', 'x2board')?></label>
								<input type="checkbox" id="comment_anonymous_<?php echo $post->post_id?>" name="comment_option_anonymous" value="1"<?php if($temporary->comment_anonymous):?> checked<?php endif?>><label class="comments-field-label" for="comment_anonymous_<?php echo $post->post_id?>">익명</label>
							<?php endif?>
						</div>

						<?php
						if($mi_cmt_wrt != 'editor' ){
							$s_style = 'display:none';
						}
						?>
						<div class="edit_opt clear" style="<?php echo $s_style?>">
							<?php if(!is_user_logged_in()):?>
							<span class="itx_wrp">
								<label for="nick_name_{$oDocument->document_srl}">{$lang->writer}</label>
								<input type="text" name="nick_name" id="nick_name_{$oDocument->document_srl}" class="itx n_p" />
							</span>
							<span class="itx_wrp">
								<label for="password_{$oDocument->document_srl}">{$lang->password}</label>
								<input type="password" name="password" id="password_{$oDocument->document_srl}" class="itx n_p" />
							</span>
							<span class="itx_wrp">
								<label for="email_address_{$oDocument->document_srl}">{$lang->email_address}</label>
								<input type="text" name="email_address" id="email_address_{$oDocument->document_srl}" class="itx m_h" />
							</span>
							<?php endif?>
							
						<?php if($mi_cmt_wrt == '____editor' ):?>
							<!-- <input type="submit" value="11<?php echo __('Submit', 'x2board')?>" class="bd_btn fr" /> -->
							<input type="submit" value="<?php echo __('Submit', 'x2board')?>" class="bd_btn fr" style="border-radius: 3px; margin: 4px 0; padding: 4px 20px;">
						<?php endif	?>
							<div class="opt_chk">
								<?php if(is_user_logged_in()):?>
									<!-- <input type="checkbox" name="notify_message" value="Y" id="notify_message_{$oDocument->document_srl}" checked="checked"|cond="@in_array('notify',$mi->wrt_opt)" />
									<label for="notify_message_{$oDocument->document_srl}">{$lang->notify}</label>
									</block> -->
									<?php if($mi_use_status != 'PUBLIC' ):?>
									<input type="checkbox" name="is_secret" value="Y" id="is_secret_{$post->post_id}" checked="checked"|cond="@in_array('secret',$mi->wrt_opt)" />
									<label for="is_secret_{$post->post_id}"><?php echo __('secret', 'x2board')?></label>
									<?php endif?>
								<?php endif?>
							</div>
						</div>

						<?php
						// 댓글 입력 필드 출력
						$field_html = ob_get_clean();
						// do_action('kboard_comments_field', $field_html, $board, $post->post_id, $commentBuilder);
						// do_action('kboard_comments_field', $field_html, $post->post_id);
						?>
					</div>
				</div>
				<div class="wp-editor-tabs" style='float: right;'>
					<input type="submit" value="<?php echo __('Submit', 'x2board')?>" class="bd_btn" style="border-radius: 3px; margin: 4px 0; padding: 4px 20px;">
				</div>
			</div>
		</form>

		<!--// 대댓글 -->
		<!-- <div cond="$rd_idx==0" id="re_cmt"> -->
		<div id="re_cmt">
			<label for="editor_2" class="cmt_editor_tl fl"><i class="fa fa-share fa-flip-vertical re"></i><strong><?php echo __('write_comment', 'x2board')?></strong></label>
			<div class="editor_select fr">
				<!-- <a class="wysiwyg m_no" href="#"><em class="fa fa-info-circle bd_info_icon"></em> 에디터 사용하기</a> -->
				<a class="close" href="#" onclick="jQuery('#re_cmt').fadeOut().parent().find('.re_comment').focus();return false"><i class="fa fa-times"></i> <?php echo __('Close', 'x2board')?></a>
			</div>
			<form id="kboard-comments-form-<?php echo $post->post_id?>" class="bd_wrt clear" method="post" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" enctype="multipart/form-data" onsubmit="return kboard_comments_execute(this);">
				<?php echo $comment_hidden_field_html ?>
			<!-- <form method="post" action="<?php //echo $commentURL->getInsertURL()?>" class="bd_wrt clear" enctype="multipart/form-data" onsubmit="return kboard_comments_execute(this);">	
				<input type="hidden" name="content_uid" value="<?php //echo $content_uid?>">
				<input type="hidden" name="member_uid" value="<?php //echo $member_uid?>">
				<?php //wp_nonce_field('kboard-comments-execute-'.$content_uid, 'kboard-comments-execute-nonce', !wp_doing_ajax())?>
				<input type="hidden" name="parent_uid" value=""> -->
				<div class="simple_wrt">
					<div class="text">
						<?php echo $comment_editor_html ?>
					</div>
					<div class="text">
						<?php
						// 댓글 입력 필드 시작
						ob_start();
						?>
						<?php if($grant->manager):?>
						<!-- <div class="opt_chk">
						<input type="checkbox" name="notify_message" value="Y" id="notify_message_xe">
							<label for="notify_message_xe">알림</label>
						</div> -->
						<?php endif?>
						
						<?php if(is_user_logged_in()):?>
						<!-- <input type="hidden" name="member_display" value="<?php //echo $member_display?>"> -->
						<?php else:?>
						<input type="text" id="comment_member_display_<?=$post->post_id?>" name="member_display" placeholder="<?=__('Author', 'x2board')?>" value="<?php //echo $temporary->member_display?>" class="itx n_p" required>
						<input type="password" name="password" placeholder="<?=__('Password', 'x2board')?>" id="comment_password_<?=$post->post_id?>" class="itx n_p" required>
						<?php endif?>
						<?php if(false): //$board->useCAPTCHA()):?>
							<?php if(kboard_use_recaptcha()):?>
								<div class="comments-field field-recaptcha">
									<div class="g-recaptcha" data-sitekey="<?=kboard_recaptcha_site_key()?>"></div>
								</div>
							<?php else:?>
								<label class="comments-field-label" for="comment_captcha"><img src="<?=kboard_captcha()?>" alt=""></label>
								<input type="text" name="captcha" placeholder='CAPTCHA' id="comment_captcha" class="itx n_p" required>
							<?php endif?>
						<?php endif?>

						<?php if(false): //$board->isCommentAttach()):?>
							<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount='<?php echo $board->meta->max_attached_count?>' data-accpet_file_types="<?php echo $accept_file_types?>" data-max_each_file_size_mb="<?php echo $board->meta->max_each_file_size_mb?>">
							<ul class="file-list list-unstyled mb-0"></ul>
						<?php endif?>
						
						<div class="comments-field field-comment-hide">
							<?php if(false): //$board->meta->comments_username_masking == '2'):?>
								<label class="comments-field-label" for="comment_hide_<?php echo $content_uid?>"><?php echo __('작성자 숨기기', 'x2board')?></label>
								<input type="checkbox" id="comment_hide_<?php echo $content_uid?>" name="comment_option_hide" value="1"<?php if($temporary->comment_hide):?> checked<?php endif?>><label class="comments-field-label" for="comment_hide_<?php echo $content_uid?>">작성자 숨기기</label>
							<?php endif?>
						</div>
						
						<div class="comments-field field-comment-anonymous">
							<?php if(false): //$board->meta->comments_anonymous == '2'):?>
								<label class="comments-field-label" for="comment_anonymous_<?php echo $content_uid?>"><?php echo __('익명댓글', 'x2board')?></label>
								<input type="checkbox" id="comment_anonymous_<?php echo $content_uid?>" name="comment_option_anonymous" value="1"<?php if($temporary->comment_anonymous):?> checked<?php endif?>><label class="comments-field-label" for="comment_anonymous_<?php echo $content_uid?>">익명</label>
							<?php endif?>
						</div>
						<?php
						// 댓글 입력 필드 출력
						$field_html = ob_get_clean();
						// do_action('kboard_comments_field', $field_html); //, $board, $content_uid, $commentBuilder);
						?>
					</div>
				</div>
				<div class="edit_opt">
					<input type="submit" value="<?php echo __('Submit', 'x2board')?>" class="bd_btn fr" style="border-radius: 3px; margin: 4px 0; padding: 4px 20px;">
				</div>
			</form>
		</div>

	<!--@else-->
	<?php else:?>
	<div class="bd_wrt clear">
		<div class="simple_wrt">
			<span class="profile img no_img">?</span>
			<div class="text">
				<?php if(is_user_logged_in()):?>
					<div class="cmt_disable bd_login"><?php echo __('write_comment', 'x2board')?><?php echo __('msg_not_permitted', 'x2board')?></div>
				<?php else:?>
					<a class="cmt_disable bd_login" href="#"><?php echo __('write_comment', 'x2board')?><?php echo __('msg_not_permitted', 'x2board')?><?php echo __('bd_login', 'x2board')?></a>
				<?php endif?>
			</div>
			<input type="button" value="{$lang->cmd_submit}" disabled="disabled" class="bd_btn" />
		</div>
	</div>
	<?php endif?>
	<!--@end-->

</div>