<div id="cmtPosition" aria-live="polite">
	<div class="fdb_tag">
		<a class="ui_font bubble" href="#" onclick="jQuery(this).parent().nextAll('ul,.bd_pg').slideToggle();return false">Comments <b>'<?=$post->get_comment_count()?>'</b>
		<span class="wrp" style="margin-left: -27.5px; bottom: 100%; display: none;"><span class="speech">댓글 보기</span><i class="edge"></i></span></a>
	</div>

	<ul class="fdb_lst_ul ">
		<?php foreach($post->get_comments() as $key => $comment): // while($comment = $commentList->hasNext()): $commentURL->setCommentUID($comment->uid);?>
			<?php if($comment->get('depth')):?>
				<li id="comment_{$comment->comment_id}" class="fdb_itm clear re bg{($comment->get('depth'))%2}" style="margin-left:{(($comment->get('depth')-1)%10+1)*2}%">
					<i class="fa fa-share fa-flip-vertical re"></i><i cond="$comment->get('depth')>10" class="fa fa-share fa-flip-vertical re rere"></i>
			<?php else:?>
			<li id="comment-uid-<?=$comment->uid?>" itemscope itemtype="http://schema.org/Comment" class="fdb_itm clear" data-username="<?=$comment->user_display?>" data-created="<?=$comment->created?>">
			<?php endif?>
				<span class="profile img no_img">?</span>
				<div class="meta">
					<a href="#popup_menu_area" class="member_4" onclick="return false"><?=$comment->get_nick_name()?></a>
					<span class="date" itemprop="dateCreated"><?php echo $comment->get_regdate()?></span>
					<?php if($grant->manager):?><small class="m_no">(<?=$comment->get_ip_addr()?>)</small><?php endif?>
					<?php
					$n_attachment = count((array)$comment->attach);
					if($n_attachment):?>
					<a class="tg_btn2" href="#files_<?php echo $comment->uid?>"> 첨부 <small>(<?php echo $n_attachment?>)</small></a>
					<div id="files_<?php echo $comment->uid?>" class="cmt_files tg_cnt2" style="display: none;">
						<button type="button" class="tg_blur2" style="position:absolute;top:0;left:0;border:0;background:none;width:1px;height:1px;overflow:hidden;"></button>
						<button type="button" class="tg_close2" title="닫기" style="position:absolute;top:0;right:0;border:0;background:transparent;font:20px Tahoma,AppleGothic,sans-serif;color:#999;"><b class="ui-icon ui-icon-closethick">X</b></button>
						<ul class="wrp">
						<?php foreach($comment->attach as $_=>$attach_info):?>
							<li>
								<em>•</em>
								<a class="bubble" href="<?php echo esc_url($attach_info['download_url'])?>"><?php echo esc_attr($attach_info['file_name'])?>
									<span class="wrp" style="margin-left: -73px; bottom: 100%; display: none;">
										<span class="speech">[File Size:<?php echo esc_attr(number_format($attach_info['file_size']/1000))?>KB/Download:<?php echo esc_attr(number_format($attach_info['download_count']))?>]</span><i class="edge"></i>
									</span>
								</a>
							</li>
						<?php endforeach?>
						</ul>
						<button type="button" class="tg_blur2" style="position:absolute;top:0;left:0;border:0;background:none;width:1px;height:1px;overflow:hidden;"></button>
					</div>
					<?php endif?>
				</div>
				<div class="comment_<?=$comment->comment_id?>_4 xe_content" itemprop="description">
				<?php if($comment->is_accessible()):?>
					<?php echo $comment->get_content()?>
				<?php else:?>
					<?php if($comment->remaining_time_for_reading):?>
						<div class="remaining_time_for_reading"><?=sprintf(__('You can read comments after %d minutes. <a href="%s">Login</a> and you can read it right away.', 'x2board'), round($comment->remaining_time_for_reading/60), wp_login_url($_SERVER['REQUEST_URI']))?></div>
					<?php elseif($comment->login_is_required_for_reading):?>
						<div class="login_is_required_for_reading"><?=sprintf(__('You do not have permission to read this comment. Please <a href="%s">login</a>.', 'x2board'), wp_login_url($_SERVER['REQUEST_URI']))?></div>
					<?php else:?>
						<div class="you_do_not_have_permission"><?=__('You do not have permission to read this comment.', 'x2board')?></div>
					<?php endif?>
				<?php endif?>
				</div>
				<div class="fdb_nav img_tx">
					<!-- <a class="comment_<?=$comment->comment_id?> m_no" href="#popup_menu_area" onclick="return false"><i class="fa fa-ellipsis-h"></i>이 댓글을</a> -->
					<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_COMMENT, 'comment_id',$comment->comment_id)?>"><i class="fa fa-pencil"></i><?=__('Edit', 'x2board')?></a>
					<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_COMMENT, 'comment_id',$comment->comment_id)?>" title="<?=__('Delete', 'x2board')?>"><i class="fa fa-eraser"></i><?=__('Delete', 'x2board')?></a>
					
					<?php if(!$grant->write_comment){
						$s_onclick = "alert('".__('msg_not_permitted', 'x2board')."')";
					}
					else {
						$s_onclick = "reComment({$comment->get('parent_post_id')},{$comment->get('comment_id')},'');";
					}
					?>
					<?php if($post->allow_comment()):?>
						<a class="re_comment kboard-reply" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_REPLY_COMMENT,'comment_id',$comment->comment_id) ?>" onclick="<?php echo $s_onclick?> return false;" title="<?=__('Reply', 'x2board')?>"><i class="fa fa-comment"></i> <?=__('Reply', 'x2board')?></a>
					<?php endif?>

					<span class="vote ui_font">
						<a class="bd_login" href="#" onclick="kboard_comment_like(this);return false;" data-uid="<?=$comment->uid?>" title="cmd_vote"><em><i class="fa fa-thumbs-up"></i> <?=intval($comment->like)?></em></a>
						<a class="bd_login" href="#" onclick="kboard_comment_unlike(this);return false;" data-uid="<?=$comment->uid?>" title="cmd_vote_down"><i class="fa fa-thumbs-down"></i> <?=intval($comment->unlike)?></a>
					</span>		
				</div>
			</li>
			<!-- 답글 리스트 시작 -->
			<?php //$commentBuilder->buildTreeList('list-template.php', $comment->uid, $depth+1)?>
			<!-- 답글 리스트 끝 --> 

			<!-- 댓글 입력 폼 시작 -->
			<form id="kboard-comment-reply-form-<?=$comment->comment_id?>" method="post" action="<?php //echo $commentURL->getInsertURL()?>" class="comments-reply-form" enctype="multipart/form-data" onsubmit="return kboard_comments_execute(this);">
				<input type="hidden" name="content_uid" value="<?php //echo $comment->content_uid?>">
				<input type="hidden" name="parent_uid" value="<?php //echo $comment->uid?>">
				<input type="hidden" name="member_uid" value="<?php //echo $member_uid?>">
			</form>
			<!-- 댓글 입력 폼 끝 -->
		<?php endforeach //endwhile?>
	</ul>

	<!--// 댓글 페이지네이션 -->
	<!-- <block cond="$post->comment_page_navigation"> -->
	<?php 
	$cpage = \X2board\Includes\Classes\Context::get('cpage');  // cpage is set wafter \includes\classes\ModuleObject.class.php::render_skin_file() has been executed
	if($post->comment_page_navigation):?>
	<div class="bd_pg clear {$mi->fdb_hide}">
		<a href="<?php echo x2b_get_url('cpage', 1)?>#<?php echo $post->get('post_id') ?>_comment" class="direction" title="<?=__('first_page', 'x2board')?>"><i class="fa fa-angle-double-left"></i> <span><?=__('First', 'x2board')?></span></a>
		<!-- <block loop="$page_no=$oDocument->comment_page_navigation->getNextPage()"> -->
		<?php while($page_no = $post->comment_page_navigation->getNextPage()): ?>
			<?php if( $cpage == $page_no ):?>	<!-- <strong cond="$cpage==$page_no" class="this"><?php //echo $page_no?></strong>  -->
				<strong class="this"><?php echo $page_no?></strong> 
			<?php endif?>
			<?php if( $cpage != $page_no ):?>	 <!-- <a cond="$cpage!=$page_no" href="{getUrl('cpage',$page_no)}#{$oDocument->get('document_srl')}_comment"><?php //echo $page_no?></a> -->
				<a href="<?php echo x2b_get_url('cpage', $page_no) ?>#<?php echo $post->get('post_id')?>_comment"><?php echo $page_no?></a>
			<?php endif?>
			
		<?php endwhile?>
		<!-- </block> -->
		<a href="<?php echo x2b_get_url('cpage', $post->comment_page_navigation->n_last_page)?>#<?php echo $post->get('post_id') ?>_comment" class="direction" title="<?=__('last_page', 'x2board')?>"><span><?=__('Last', 'x2board')?></span> <i class="fa fa-angle-double-right"></i></a>
	</div>
	<?php endif?>
	<!-- </block> -->
</div>
<?php //wp_enqueue_script('x2board-comments-script', "{$skin_url}/script.js", array(), X2B_VERSION, true)?>