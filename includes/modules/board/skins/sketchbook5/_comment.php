<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>
<!--// Comment v2 -->
<?php if($mi->fdb_style=='fdb_v2' && $mi->default_style!='guest'):?><!-- cond="$mi->fdb_style=='fdb_v2' && $mi->default_style!='guest'"  -->
	<a class="nametag ui_font bubble" href="#" onclick="jQuery(this).nextAll('ul,.bd_pg').slideToggle();return false" title="Click! <?php echo __('lbl_comment', X2B_DOMAIN)?> <?php echo __('cmd_view', X2B_DOMAIN)?>~"><strong class="bg_color bx_shadow">Comment <b>'<?php echo $post->get_comment_count()?>'</b></strong></a>
<?php endif?>
<!--// Comment v1 -->
<?php if($post->get_comment_count()):?><!-- <block cond="$oDocument->getCommentcount()"> -->
	<?php if($mi->fdb_style == ' '):?><!-- cond="!$mi->fdb_style"  -->
		<div class="fdb_tag<?php if($mi->cmt_wrt_position=='cmt_wrt_btm'):?> bg_f_f9 css3pie<?php endif?>">
			<a class="ui_font bubble" href="#" onclick="jQuery(this).parent().nextAll('ul,.bd_pg').slideToggle();return false" title="<?php echo __('lbl_comment', X2B_DOMAIN)?> <?php echo __('cmd_view', X2B_DOMAIN)?>">Comments <b>'<?php echo $post->get_comment_count()?>'</b></a>
		</div>
	<?php endif?>

	<ul class="fdb_lst_ul {$mi->fdb_hide}">
		<?php foreach($post->get_comments() as $key => $comment): ?><!-- <block loop="$oDocument->getComments()=>$key,$comment"> -->
			<?php if($comment->get('depth')): ?><!--@if($comment->get('depth'))-->
				<li id="comment_<?php echo $comment->comment_id?>" class="fdb_itm clear re bg<?php echo $comment->get('depth')%2?>" style="margin-left:<?php echo (($comment->get('depth')-1)%10+1)*2?>%">
					<i class="fa fa-share fa-flip-vertical re"></i>
					<?php if($comment->get('depth')>10): ?>  <!-- cond="$comment->get('depth')>10"  -->
						<i class="fa fa-share fa-flip-vertical re rere"></i>
					<?php endif?>
			<?php else:?><!--@else-->
				<li id="comment_<?php echo $comment->comment_id?>" class="fdb_itm clear">
			<?php endif?><!--@end-->
					<!--// 프로필 -->
					<?php if(!$mi->profile_img):?> <!-- <block cond="!$mi->profile_img"> -->
					<!-- <img cond="$comment->getProfileImage()" class="profile img" src="{$comment->getProfileImage()}" alt="profile" /> -->
					<span class="profile img no_img">?</span>
					<?php endif?> <!-- </block> -->
					
					<!--// 댓글 정보 -->
					<div class="meta">
						<?php if($comment->comment_author):?> <!-- cond="$comment->member_srl"  -->
							<a href="#popup_menu_area" class="member_<?php echo $comment->comment_author?>" onclick="return false"><?php echo $comment->get_nick_name()?></a>
						<?php endif?>
						<?php if(false)://!$comment->comment_author && $comment->homepage):?> <!-- cond="!$comment->member_srl && $comment->homepage"  -->
							<!-- <a href="{$comment->getHomepageUrl()}" target="_blank"><?php //echo $comment->get_nick_name()?></a> -->
						<?php endif?>							
						<?php if(!$comment->comment_author):?> <!-- cond="!$comment->member_srl && !$comment->homepage" -->
							<b><?php echo $comment->get_nick_name()?></b>
						<?php endif?>
						<span class="date"><?php echo x2b_get_time_gap($comment->get('regdate_dt'), "Y.m.d H:i")?></span>
						<?php if($grant->manager || $mi->display_ip_address == 'Y'):?> <!-- cond="$grant->manager || $mi->display_ip_address"  -->
							<small class="m_no">(<?php echo $comment->get_ip_addr()?>)</small>
						<?php endif?>
						<?php if($comment->is_secret()):?> <!-- cond="$comment->isSecret()"  -->
							<span class="ico_secret">SECRET</span>
						<?php endif?>
						<!--// 첨부파일 -->
						<?php if($comment->has_uploaded_files()):?><!-- <block cond="$comment->has_uploaded_files()"> -->
							<a class="tg_btn2" href="#files_<?php echo $comment->comment_id?>"><b class="ui-icon ui-icon-disk">Files</b><?php echo __('lbl_uploaded_file', X2B_DOMAIN)?> <small>(<?php echo$comment->get('uploaded_count')?>)</small></a>
							<div id="files_<?php echo $comment->comment_id?>" class="cmt_files tg_cnt2">
								<button type="button" class="tg_blur2"></button><button type="button" class="tg_close2" title="<?php echo __('cmd_close', X2B_DOMAIN)?>"><b class="ui-icon ui-icon-closethick">X</b></button>
								<ul class="wrp">
									<?php foreach($comment->get_uploaded_files() as $_=>$file):?> <!-- loop="$comment->getUploadedFiles()=>$key,$file" -->
										<li><em>&bull;</em> <a class="bubble" href="<?php echo esc_url($file->download_url)?>" title="[File Size:<?php echo esc_attr(number_format($file->file_size/1000))?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename?></a></li>
									<?php endforeach?>
								</ul>
								<button type="button" class="tg_blur2"></button>
							</div>
						<?php endif?><!-- </block> -->
					</div>
					<!--// 댓글 본문 -->
					<?php if(!$comment->is_accessible()): ?>
						<!-- onsubmit="return procFilter(this, input_password)" -->
						<form action="./" method="get" id="x2board-comment-form">
							<input type="hidden" name="board_id" value="<?php echo $board_id?>" />
							<input type="hidden" name="page" value="<?php echo $page?>" />
							<input type="hidden" name="parent_post_id" value="<?php echo $comment->get('parent_post_id')?>" />
							<input type="hidden" name="comment_id" value="<?php echo $comment->get('comment_id')?>" />
							<p>&quot;<?php echo __('msg_secret_post', X2B_DOMAIN)?>&quot;</p>
							<span class="itx_wrp">
								<label for="cpw_<?php echo $comment->comment_id?>"><?php echo __('lbl_password', X2B_DOMAIN)?></label>
								<input type="password" id="cpw_<?php echo $comment->comment_id?>" name="password" class="itx" />
								<input type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" class="bd_btn" />
							</span>
						</form>
					<?php else: ?>
						<?php echo $comment->get_content() ?>
					<?php endif ?>
					<!--// 편집 등 -->
					<div class="fdb_nav img_tx">
						<?php if($mi->cmt_this_btn=='2' && $is_logged): ?><!-- <block cond="$mi->cmt_this_btn=='2' && $is_logged"> -->
							<a href="#" onclick="doCallModuleAction('comment','procCommentDeclare','<?php echo $comment->comment_id?>');return false"><i class="fa fa-phone"></i><?php echo __('cmd_declare', X2B_DOMAIN)?></a>
							<a href="#" onclick="doCallModuleAction('comment','procCommentVoteUp','<?php echo $comment->comment_id?>');return false"><i class="fa fa-heart color"></i><?php echo __('cmd_vote', X2B_DOMAIN)?></a>
							<a cond="$mi->cmt_vote_down == ' '" href="#" onclick="doCallModuleAction('comment','procCommentVoteDown','<?php echo $comment->comment_id?>');return false"><i class="fa fa-heart"></i><?php echo __('cmd_vote_down', X2B_DOMAIN)?></a>
						<?php endif ?><!-- </block> -->
						<?php if(($mi->cmt_this_btn == ' ' && $is_logged) || $grant->manager): ?><!-- cond="(!$mi->cmt_this_btn && $is_logged) || $grant->manager" -->
							<a class="comment_<?php echo $comment->comment_id?> m_no" href="#popup_menu_area" onclick="return false"><i class="fa fa-ellipsis-h"></i><?php echo __('cmd_comment_do', X2B_DOMAIN)?></a>
						<?php endif ?>
						<?php if($comment->is_granted() || !$comment->get('comment_author')): ?><!-- <block cond="$comment->isGranted() || !$comment->get('comment_author')"> -->
							<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_COMMENT, 'comment_id',$comment->comment_id)?>"><i class="fa fa-pencil"></i><?php echo __('cmd_modify', X2B_DOMAIN)?></a>
							<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_COMMENT, 'comment_id',$comment->comment_id)?>"><i class="fa fa-eraser"></i><?php echo __('cmd_delete', X2B_DOMAIN)?></a>
						<?php endif ?><!-- </block> -->
						<?php if($post->allow_comment() ): ?><!-- cond="$oDocument->allowComment()"  -->
							<a class="re_comment" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_REPLY_COMMENT,'comment_id',$comment->comment_id)?>" onclick="<?php if(!$grant->write_comment || !$post->is_enable_comment()):?>alert('<?php echo __('msg_not_permitted', X2B_DOMAIN)?>')<?php else: ?>reComment(<?php echo $comment->get('parent_post_id')?>,<?php echo $comment->get('comment_id')?>,'<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_REPLY_COMMENT,'comment_id',$comment->comment_id)?>')<?php endif ?>;return false;"><i class="fa fa-comment"></i> <?php echo __('cmd_reply', X2B_DOMAIN)?></a>
						<?php endif ?>
						<!--// 추천-비추천 -->
						<?php if($mi->cmt_vote!='N' && ($mi->cmt_vote=='2' || $comment->get('voted_count')!=0 || $comment->get('blamed_count')!=0) ): ?><!-- cond="$mi->cmt_vote!='N' && ($mi->cmt_vote=='2' || $comment->get('voted_count')!=0 || $comment->get('blamed_count')!=0)" -->
							<span class="vote ui_font">
								<a class="bd_login" href="#" onclick="doCallModuleAction('comment','procCommentVoteUp','<?php echo $comment->comment_id?>');return false"|cond="$is_logged" title="<?php echo __('cmd_vote', X2B_DOMAIN)?>"><em><i class="fa fa-heart color"></i> <?php echo $comment->get('voted_count') ? $comment->get('voted_count') : 0 ?></em></a>
								<a cond="$mi->cmt_vote_down == ' " class="bd_login" href="#" <?php if($is_logged ): ?> onclick="doCallModuleAction('comment','procCommentVoteDown','<?php echo $comment->comment_id?>');return false" <?php endif ?> title="<?php echo __('cmd_vote_down', X2B_DOMAIN)?>"><i class="fa fa-heart"></i> <?php echo abs($comment->get('blamed_count') ? $comment->get('blamed_count') : 0)?></a>
							</span>
						<?php endif ?>
					</div>
				</li>
		<?php endforeach ?> <!-- </block> -->
	</ul>

	<!--// 댓글 페이지네이션 -->
	<?php if($post->comment_page_navigation):?><!-- <block cond="$oDocument->comment_page_navigation"> -->
		<div class="bd_pg clear {$mi->fdb_hide}">
			<a href="<?php echo x2b_get_url('cpage',1)?>#<?php echo $post->get('parent_post_id')?>_comment" class="direction" title="<?php echo __('lbl_first_page', X2B_DOMAIN)?>"><i class="fa fa-angle-double-left"></i> <span>First</span></a>
			<?php while($page_no = $post->comment_page_navigation->getNextPage()): ?><!-- <block loop="$page_no=$oDocument->comment_page_navigation->getNextPage()"> -->
				<?php if( $cpage == $page_no ):?>	<!-- <strong cond="$cpage==$page_no" class="this"><?php //echo $page_no?></strong>  -->
					<strong class="this"><?php echo $page_no?></strong> 
				<?php endif?>
				<?php if( $cpage != $page_no ):?>	 <!-- <a cond="$cpage!=$page_no" href="{getUrl('cpage',$page_no)}#{$oDocument->get('document_srl')}_comment"><?php //echo $page_no?></a> -->
					<a href="<?php echo x2b_get_url('cpage', $page_no) ?>#<?php echo $post->get('parent_post_id')?>_comment"><?php echo $page_no?></a>
				<?php endif?>
			<?php endwhile?><!-- </block> -->
			<!-- <a href="{getUrl('cpage',$oDocument->comment_page_navigation->last_page)}#{$oDocument->get('document_srl')}_comment" class="direction" title="<?php echo __('lbl_last_page', X2B_DOMAIN)?>"><span>Last</span> <i class="fa fa-angle-double-right"></i></a> -->
			<a href="<?php echo x2b_get_url('cpage', $post->comment_page_navigation->n_last_page)?>#<?php echo $post->get('parent_post_id') ?>_comment" class="direction" title="<?php echo __('lbl_last_page', X2B_DOMAIN)?>"><span>Last</span> <i class="fa fa-angle-double-right"></i></a>
		</div>
	<?php endif ?><!-- </block> -->
<?php endif?><!-- </block> -->