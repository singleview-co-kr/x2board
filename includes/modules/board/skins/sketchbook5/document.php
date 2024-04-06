<div class="bd hover_effect" >
	<div class="rd rd_nav_style2 clear" itemscope itemtype="http://schema.org/Article">
		<div class="rd_hd clear"> <!--  style="margin:0 -15px 20px"> -->
			<div class="board clear " style=";">
				<div class="top_area ngeb" style=";">
					<div class="detail-attr">
					<strong class="cate fl" title="Category"><?php echo esc_html($post->category_name)?></strong>
					</div>
					<div class="fr">
					<span class="date m_no"><?php echo $post->get_regdate('Y.m.d H:i')?></span>				
					</div>
					<h1 class="np_18px" itemprop="name"><a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id, 'page', ''))?>"><?php echo $post->get_title()?></a></h1>
				</div>
				<div class="btm_area clear">
					<div class="side" itemprop="author">
					<?php echo $post->get_nick_name()?>
					</div>
					<div class="side fr">
					<?php if($grant->manager):?><small class="m_no"><?php echo esc_html($post->get_ip_addr())?> </small><?php endif?>
					<span><?php echo __('Views', 'x2board')?> <b><?php echo intval($post->readed_count)?></b></span>
					<span><?php echo __('Votes', 'x2board')?> <b><?php echo intval($post->voted_count)?></b></span>
					<span><?php echo __('Comment', 'x2board')?> <b><?php echo intval($post->get_comment_count())?></b></span>
					</div>
				</div>
			</div>
			<?php 
			$attachments = $post->get_uploaded_files();
			if( count($attachments) ):?>
			<div id="files_290669" class="rd_fnt rd_file">
				<table class="bd_tb" style="margin-bottom: 0px;">
					<caption class="blind">Atachment</caption>
					<tbody>
						<tr>
							<th scope="row" class="ui_font"><strong>첨부</strong> <span class="fnt_count">'<b><?php echo count($attachments)?></b>'</span></th>
							<td>
								<ul>
									<?php foreach($attachments as $key=>$attach):?>
									<li><a class="bubble" href="<?php echo esc_url($attach->download_url)?>" title="<?php echo sprintf(__('Download %s', 'x2board'), $attach->source_filename)?> [File Size:<?php echo intval($attach->file_size/1024)?>KB/Download:<?php echo number_format($attach->download_count)?>]"><?php echo $attach->source_filename?><span class="wrp" style="margin-left: -73px; bottom: 100%; display: none;"><i class="edge"></i></span></a><span class="comma">,</span></li>
									<?php endforeach?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php endif?>
			<div class="rd_nav img_tx fr m_btn_wrp">
				<!-- <div class="help bubble left m_no">
					<a class="text" href="#" onclick="jQuery(this).next().fadeToggle();return false;">?</a>
					<div class="wrp">
					<div class="speech">
						<h4>단축키</h4>
						<p><strong><i class="fa fa-long-arrow-left"></i><span class="blind">Prev</span></strong>이전 문서</p>
						<p><strong><i class="fa fa-long-arrow-right"></i><span class="blind">Next</span></strong>다음 문서</p>
					</div>
					<i class="edge"></i>
					<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</div>
				</div>
				<a class="tg_btn2 bubble m_no" href="#" data-href=".bd_font_select" title="글꼴 선택"><strong>가</strong><i class="arrow down"></i></a>	<a class="font_plus bubble" href="#" title="크게"><i class="fa fa-search-plus"></i><b class="tx">크게</b></a>
				<a class="font_minus bubble" href="#" title="작게"><i class="fa fa-search-minus"></i><b class="tx">작게</b></a>
				<a class="back_to bubble m_no" href="#bd_136_289289" title="위로"><i class="fa fa-arrow-up"></i><b class="tx">위로</b></a>
				<a class="back_to bubble m_no" href="#rd_end_289289" title="(목록) 아래로"><i class="fa fa-arrow-down"></i><b class="tx">아래로</b></a>
				<a class="comment back_to bubble if_viewer m_no" href="#289289_comment" title="댓글로 가기"><i class="fa fa-comment"></i><b class="tx">댓글로 가기</b></a> -->
				<a class="print_doc bubble m_no" onclick="#" title="<?php echo __('Print', 'x2board')?>"><i class="fa fa-print"></i><b class="tx"><?php echo __('Print', 'x2board')?></b></a>
				<!-- <a class="document_289289 action bubble m_no" href="#popup_menu_area" onclick="return false;" title="이 게시물을"><i class="fa fa-ellipsis-h"></i><b class="tx">이 게시물을</b></a> -->
				<a class="edit" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_POST))?>"><i class="ico_16px write"></i><?php echo __('Edit', 'x2board')?></a> 
				<a class="edit" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_POST))?>" onclick="return confirm('<?php echo __('Are you sure you want to delete?', 'x2board')?>');"><i class="ico_16px delete"></i><?php echo __('Delete', 'x2board')?> </a>
			</div>
			<!-- <div class="rd_nav_side">
				<div class="rd_nav img_tx fr m_btn_wrp" style="d11isplay: none;">
					<div class="help bubble left m_no">
					<a class="text" href="#" onclick="jQuery(this).next().fadeToggle();return false;">?</a>
					<div class="wrp">
						<div class="speech">
							<h4>단축키</h4>
							<p><strong><i class="fa fa-long-arrow-left"></i><span class="blind">Prev</span></strong>이전 문서</p>
							<p><strong><i class="fa fa-long-arrow-right"></i><span class="blind">Next</span></strong>다음 문서</p>
						</div>
						<i class="edge"></i>
						<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</div>
					</div>
					<a class="tg_btn2 bubble m_no" href="#" data-href=".bd_font_select" title="글꼴 선택"><strong>가</strong><i class="arrow down"></i></a>	<a class="font_plus bubble" href="#" title="크게"><i class="fa fa-search-plus"></i><b class="tx">크게</b></a>
					<a class="font_minus bubble" href="#" title="작게"><i class="fa fa-search-minus"></i><b class="tx">작게</b></a>
					<a class="back_to bubble m_no" href="#bd_136_289289" title="위로"><i class="fa fa-arrow-up"></i><b class="tx">위로</b></a>
					<a class="back_to bubble m_no" href="#rd_end_289289" title="(목록) 아래로"><i class="fa fa-arrow-down"></i><b class="tx">아래로</b></a>
					<a class="comment back_to bubble if_viewer m_no" href="#289289_comment" title="댓글로 가기"><i class="fa fa-comment"></i><b class="tx">댓글로 가기</b></a>
					<a class="print_doc bubble m_no" href="https://yuhanrox.co.kr/index.php?mid=CONSUMER_QNA&amp;document_srl=289289&amp;listStyle=viewer" title="인쇄"><i class="fa fa-print"></i><b class="tx">인쇄</b></a>
					<a class="document_289289 action bubble m_no" href="#popup_menu_area" onclick="return false;" title="이 게시물을"><i class="fa fa-ellipsis-h"></i><b class="tx">이 게시물을</b></a>	
					<a class="edit" href=""><i class="ico_16px write"></i>수정</a>  
					<a class="edit" href=""><i class="ico_16px delete"></i>삭제 </a>
				</div>
			</div> -->
		</div>
		<div class="rd_body clear" itemprop="description">
			<table class="et_vars bd_tb">
				<caption class="blind">Extra Form</caption>
				<?php echo $post->getDocumentOptionsHTML()?>
			</table>
			<article>
				<div class="document_289289_0 xe_content">
					<?php echo $post->get_content()?>
				</div>
			</article>
		</div>
<?php		
foreach( $post_list as $no => $o_post ) {
	if( $post_id == $o_post->post_id ) {
		$cur_post_pos_in_list = $no;
		break;
	}
}
?>
		<div class="rd_ft">
			<div class="bd_prev_next clear">
				<div>
					<?php if(isset($post_list[$cur_post_pos_in_list+1]) ):
						$o_prev_post = $post_list[$cur_post_pos_in_list+1];	?>
					<a class="bd_rd_prev bubble no_bubble fl" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $o_prev_post->post_id))?>">
					<span class="p"><em class="link"><i class="fa fa-angle-left"></i> Prev</em> <?php echo esc_attr(wp_strip_all_tags($o_prev_post->get_title()))?></span>
					<i class="fa fa-angle-left"></i>
					<span class="wrp prev_next" style="bottom: 100%; display: none;">
					<span class="speech">
					<img src="https://yuhanrox.co.kr/files/thumbnails/220/289/90x90.crop.jpg" alt="">
					<b><?php echo esc_attr(wp_strip_all_tags($o_prev_post->get_title()))?></b>
					<span><em><?php echo $o_prev_post->get_regdate('Y.m.d H:i')?></em><small>by </small><?php echo $o_prev_post->get_nick_name()?></span>
					</span><i class="edge"></i>
					<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</span>
					</a>
					<?php unset($o_prev_post); endif?>

					<?php if(isset($post_list[$cur_post_pos_in_list-1]) ):
						$o_next_post = $post_list[$cur_post_pos_in_list-1];	?>
					<a class="bd_rd_next bubble no_bubble fr" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $o_next_post->post_id))?>">
					<span class="p"><?php echo esc_attr(wp_strip_all_tags($o_next_post->get_title()))?> <em class="link">Next <i class="fa fa-angle-right"></i></em></span>
					<i class="fa fa-angle-right"></i>
					<span class="wrp prev_next">
					<span class="speech">
					<b><?php echo esc_attr(wp_strip_all_tags($o_next_post->get_title()))?></b>
					<span><em><?php echo $o_next_post->get_regdate('Y.m.d H:i')?></em><small>by </small><?php echo $o_next_post->get_nick_name()?></span>
					</span><i class="edge"></i>
					<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</span>
					</a>
					<?php unset($o_next_post); endif?>
				</div>
			</div>
			<?php if(true): //!$board->meta->permission_vote_hide):?>
				<div class="rd_vote">
					<a class="bd_login" onclick="kboard_document_like(this)" data-uid="<?php echo $post->post_id?>" title="<?php echo __('Like', 'x2board')?>" style="border:2px solid #333333;color:#333333;">
						<b><i class="fa fa-heart"></i> <?php echo intval($post->voted_count)?></b>
						<p><?php echo __('Like', 'x2board')?></p>
					</a>
					<a class="blamed bd_login" onclick="kboard_document_dislike(this)" data-uid="<?php echo $post->post_id?>" title="<?php echo __('Dislike', 'x2board')?>">
						<b><i class="fa fa-heart"></i> <?php echo intval($post->blamed_count)?></b>
						<p><?php echo __('Dislike', 'x2board')?></p>
					</a>
				</div>
			<?php endif?>
			<?php if(false): //$board->meta->use_related_post):
				if(isset($crp_posts) && count($crp_posts)):?>
				<div class="crp_related crp-rounded-thumbs">
					<h2>연관 포스트를 살펴보세요.</h2>
					<ul>
					<?php foreach($crp_posts as $_=>$single_post):
						$thumbnail_url = get_the_post_thumbnail_url($single_post->ID, 'small');
						$thumbnail_url = $thumbnail_url ? $thumbnail_url : $this->list['sketchbook5']->url.'/img/default.png';
					?>
						<li>
							<a href="<?php echo get_permalink($single_post->ID)?>" class="crp_link post-15">
								<figure><img width="150" height="150" src="<?php echo $thumbnail_url?>" class="crp_thumb crp_default_thumb" alt="<?php echo esc_attr(wp_strip_all_tags($single_post->post_title))?>" title="<?php echo esc_attr(wp_strip_all_tags($single_post->post_title))?>"></figure>
								<span class="crp_title"><?php echo esc_attr(wp_strip_all_tags($single_post->post_title))?></span>
							</a>
						</li>
					<?php endforeach?>
					</ul>
					<div class="crp_clear"></div>
				</div>
				<?php endif?>
			<?php endif?>

			<div class="rd_ft_nav clear">
				<!-- <div class="rd_nav img_tx to_sns fl" data-url="https://yuhanrox.co.kr/CONSUMER_QNA/289289?l=ko" data-title="%EC%83%88%EC%A0%9C%ED%92%88+%EB%B6%88%EB%9F%89">
					<a class="" href="#" data-type="facebook" title="To Facebook" onclick=""><i class="ico_sns16 facebook"></i><strong> Facebook</strong></a>
					<a class="" href="#" data-type="twitter" title="To Twitter"><i class="ico_sns16 twitter"></i><strong> Twitter</strong></a>
					<a class="" href="#" data-type="google" title="To Google"><i class="ico_sns16 google"></i><strong> Google</strong></a>
					<a class="" href="#" data-type="pinterest" title="To Pinterest"><i class="ico_sns16 pinterest"></i><strong> Pinterest</strong></a>
				</div> -->
				<div class="rd_nav img_tx fl m_btn_wrp">
					<a class="list" href="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>"> <?php echo __('List', 'x2board')?> </a>
					<?php if($post->is_allow_reply() && !$post->notice):?><a class="edit" href="<? // echo $url->set('parent_uid', $post->post_id)->set('mod', 'editor')->toString()?>"> <?php echo __('Reply', 'x2board')?> </a><?php endif?>
				</div>
				<div class="rd_nav img_tx fr m_btn_wrp">
					<!-- <a class="back_to bubble m_no" href="#bd_136_289289" title="위로"><i class="fa fa-arrow-up"></i><b class="tx">위로</b></a>
					<a class="back_to bubble m_no" href="#rd_end_289289" title="(목록) 아래로"><i class="fa fa-arrow-down"></i><b class="tx">아래로</b></a>
					<a class="comment back_to bubble if_viewer m_no" href="#289289_comment" title="댓글로 가기"><i class="fa fa-comment"></i><b class="tx">댓글로 가기</b></a> -->
					<a class="print_doc bubble m_no" onclick="kboard_document_print('<?php echo "url->getDocumentPrint(content->uid)"?>')" title="<?php echo __('Print', 'x2board')?>"><i class="fa fa-print"></i><b class="tx"><?php echo __('Print', 'x2board')?></b></a>
					<!-- <a class="document_289289 action bubble m_no" href="#popup_menu_area" onclick="return false;" title="이 게시물을"><i class="fa fa-ellipsis-h"></i><b class="tx">이 게시물을</b></a> -->
					<a class="edit" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_POST, 'post_id', $post->post_id))?>"><i class="ico_16px write"></i><?php echo __('Edit', 'x2board')?></a>
					<a class="edit" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_POST, 'post_id', $post->post_id))?>"><i class="ico_16px delete"></i><?php echo __('Delete', 'x2board')?> </a>
				</div>
			</div>
		</div>
		<!-- <div class="fdb_lst_wrp"><?php //echo $board->buildComment($content->uid)?></div> -->
		<div class="fdb_lst_wrp">
			<div id="kboard-comments-<?php echo $post->post_id?>" class="fdb_lst clear">
				
<!--------------------------------------------------->
				<?php x2b_include_skin('_comment_write');?>
<!--------------------------------------------------->
				
			</div>
			<?php if($post->get_comment_count() > 0):?>
				<!-- <div id="cmtPosition" aria-live="polite">
					<div class="fdb_tag">
						<a class="ui_font bubble" href="#" onclick="jQuery(this).parent().nextAll('ul,.bd_pg').slideToggle();return false">Comments <b>'<?=$post->get_comment_count()?>'</b>
						<span class="wrp" style="margin-left: -27.5px; bottom: 100%; display: none;"><span class="speech">댓글 보기</span><i class="edge"></i></span></a>
					</div> -->
					<!-- 댓글 리스트 시작 -->
					<?php x2b_include_skin('_comment_list');
					//$commentBuilder->buildTreeList('list-template.php')?>
					<!-- 댓글 리스트 끝 -->
				<!-- </div> -->
			<?php endif?>
		</div>
	</div>
</div>