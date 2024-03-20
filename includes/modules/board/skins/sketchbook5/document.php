<div class="bd hover_effect" >
	<div class="rd rd_nav_style2 clear" itemscope itemtype="http://schema.org/Article">
		<div class="rd_hd clear"> <!--  style="margin:0 -15px 20px"> -->
			<div class="board clear " style=";">
				<div class="top_area ngeb" style=";">
					<div class="detail-attr">
					<strong class="cate fl" title="Category"><?php echo esc_html($content->category_name)?></strong>
					</div>
					<div class="fr">
					<span class="date m_no"><?=date('Y-m-d H:i', strtotime($content->date))?></span>				
					</div>
					<h1 class="np_18px" itemprop="name"><a href="<?=esc_url($url->getDocumentURLWithUID($content->uid))?>"><?=$content->title?></a></h1>
				</div>
				<div class="btm_area clear">
					<div class="side" itemprop="author">
					<?=$content->getUserDisplay()?>
					</div>
					<div class="side fr">
					<?php if($board->isAdmin()):?><small class="m_no"><?=esc_html($content->ipaddress)?> </small><?php endif?>
					<span><?=__('Views', 'kboard')?> <b><?=intval($content->view)?></b></span>
					<span><?=__('Votes', 'kboard')?> <b><?=intval($content->like)?></b></span>
					<span><?=__('Comment', 'kboard')?> <b><?=intval($content->getCommentsCount('', ''))?></b></span>
					</div>
				</div>
			</div>
			<?php 
			$attachments = $content->getAttachmentList();
			if( count($attachments) ):?>
			<div id="files_290669" class="rd_fnt rd_file">
				<table class="bd_tb" style="margin-bottom: 0px;">
					<caption class="blind">Atachment</caption>
					<tbody>
						<tr>
							<th scope="row" class="ui_font"><strong>첨부</strong> <span class="fnt_count">'<b><?=count($attachments)?></b>'</span></th>
							<td>
								<ul>
									<?php foreach($attachments as $key=>$attach):?>
									<li><a class="bubble" href="<?=$url->getDownloadURLWithAttach($content->uid, $key)?>" title="<?=sprintf(__('Download %s', 'kboard'), $attach['file_name'])?> [File Size:<?=intval($attach['file_size']/1024)?>KB/Download:<?=number_format($attach['download_count'])?>]"><?=$attach['file_name']?><span class="wrp" style="margin-left: -73px; bottom: 100%; display: none;"><i class="edge"></i></span></a><span class="comma">,</span></li>
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
				<a class="print_doc bubble m_no" onclick="kboard_document_print('<?=$url->getDocumentPrint($content->uid)?>')" title="<?=__('Print', 'kboard')?>"><i class="fa fa-print"></i><b class="tx"><?=__('Print', 'kboard')?></b></a>
				<!-- <a class="document_289289 action bubble m_no" href="#popup_menu_area" onclick="return false;" title="이 게시물을"><i class="fa fa-ellipsis-h"></i><b class="tx">이 게시물을</b></a> -->
				<a class="edit" href="<?=esc_url($url->getContentEditor($content->uid))?>"><i class="ico_16px write"></i><?=__('Edit', 'kboard')?></a> 
				<a class="edit" href="<?=esc_url($url->getContentRemove($content->uid))?>" onclick="return confirm('<?=__('Are you sure you want to delete?', 'kboard')?>');"><i class="ico_16px delete"></i><?=__('Delete', 'kboard')?> </a>
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
				<?=$content->getDocumentOptionsHTML()?>
			</table>
			<article>
				<div class="document_289289_0 xe_content">
					<?=$content->content?>
				</div>
			</article>
		</div>
		<div class="rd_ft">
			<div class="bd_prev_next clear">
				<div>
					<?php
					$bottom_content_uid = $content->getPrevUID();
					if($bottom_content_uid):
					$bottom_content = new KBContent();
					$bottom_content->initWithUID($bottom_content_uid);
					?>
					<a class="bd_rd_prev bubble no_bubble fl" href="<?=esc_url($url->getDocumentURLWithUID($bottom_content_uid))?>">
					<span class="p"><em class="link"><i class="fa fa-angle-left"></i> Prev</em> <?=esc_attr(wp_strip_all_tags($bottom_content->title))?></span>
					<i class="fa fa-angle-left"></i>
					<span class="wrp prev_next" style="bottom: 100%; display: none;">
					<span class="speech">
					<img src="https://yuhanrox.co.kr/files/thumbnails/220/289/90x90.crop.jpg" alt="">
					<b><?=esc_attr(wp_strip_all_tags($bottom_content->title))?></b>
					<span><em><?=date('Y-m-d', strtotime($bottom_content->date))?></em><small>by </small><?=$bottom_content->getUserDisplay()?></span>
					</span><i class="edge"></i>
					<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</span>
					</a>
					<?php endif?>

					<?php
					$top_content_uid = $content->getNextUID();
					if($top_content_uid):
					$top_content = new KBContent();
					$top_content->initWithUID($top_content_uid);
					?>
					<a class="bd_rd_next bubble no_bubble fr" href="<?=esc_url($url->getDocumentURLWithUID($top_content_uid))?>">
					<span class="p"><?=esc_attr(wp_strip_all_tags($top_content->title))?> <em class="link">Next <i class="fa fa-angle-right"></i></em></span>
					<i class="fa fa-angle-right"></i>
					<span class="wrp prev_next">
					<span class="speech">
					<b><?=esc_attr(wp_strip_all_tags($top_content->title))?></b>
					<span><em><?=date('Y-m-d', strtotime($top_content->date))?></em><small>by </small><?=$top_content->getUserDisplay()?></span>
					</span><i class="edge"></i>
					<i class="ie8_only bl"></i><i class="ie8_only br"></i>
					</span>
					</a>
					<?php endif?>
				</div>
			</div>
			<?php if(!$board->meta->permission_vote_hide):?>
				<div class="rd_vote">
					<a class="bd_login" onclick="kboard_document_like(this)" data-uid="<?=$content->uid?>" title="<?=__('Like', 'kboard')?>" style="border:2px solid #333333;color:#333333;">
						<b><i class="fa fa-heart"></i> <?=intval($content->like)?></b>
						<p><?=__('Like', 'kboard')?></p>
					</a>
					<a class="blamed bd_login" onclick="kboard_document_unlike(this)" data-uid="<?=$content->uid?>" title="<?=__('Unlike', 'kboard')?>">
						<b><i class="fa fa-heart"></i> <?=intval($content->unlike)?></b>
						<p><?=__('Unlike', 'kboard')?></p>
					</a>
				</div>
			<?php endif?>
			<?php if($board->meta->use_related_post):
				if(isset($crp_posts) && count($crp_posts)):?>
				<div class="crp_related crp-rounded-thumbs">
					<h2>연관 포스트를 살펴보세요.</h2>
					<ul>
					<?php foreach($crp_posts as $_=>$single_post):
						$thumbnail_url = get_the_post_thumbnail_url($single_post->ID, 'small');
						$thumbnail_url = $thumbnail_url ? $thumbnail_url : $this->list['sketchbook5']->url.'/img/default.png';
					?>
						<li>
							<a href="<?=get_permalink($single_post->ID)?>" class="crp_link post-15">
								<figure><img width="150" height="150" src="<?=$thumbnail_url?>" class="crp_thumb crp_default_thumb" alt="<?=esc_attr(wp_strip_all_tags($single_post->post_title))?>" title="<?=esc_attr(wp_strip_all_tags($single_post->post_title))?>"></figure>
								<span class="crp_title"><?=esc_attr(wp_strip_all_tags($single_post->post_title))?></span>
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
					<a class="list" href="<?=esc_url($url->getBoardList())?>"> <?=__('List', 'kboard')?> </a>
					<?php if($board->isReply() && !$content->notice):?><a class="edit" href="<?=$url->set('parent_uid', $content->uid)->set('mod', 'editor')->toString()?>"> <?=__('Reply', 'kboard')?> </a><?php endif?>
				</div>
				<div class="rd_nav img_tx fr m_btn_wrp">
					<!-- <a class="back_to bubble m_no" href="#bd_136_289289" title="위로"><i class="fa fa-arrow-up"></i><b class="tx">위로</b></a>
					<a class="back_to bubble m_no" href="#rd_end_289289" title="(목록) 아래로"><i class="fa fa-arrow-down"></i><b class="tx">아래로</b></a>
					<a class="comment back_to bubble if_viewer m_no" href="#289289_comment" title="댓글로 가기"><i class="fa fa-comment"></i><b class="tx">댓글로 가기</b></a> -->
					<a class="print_doc bubble m_no" onclick="kboard_document_print('<?=$url->getDocumentPrint($content->uid)?>')" title="<?=__('Print', 'kboard')?>"><i class="fa fa-print"></i><b class="tx"><?=__('Print', 'kboard')?></b></a>
					<!-- <a class="document_289289 action bubble m_no" href="#popup_menu_area" onclick="return false;" title="이 게시물을"><i class="fa fa-ellipsis-h"></i><b class="tx">이 게시물을</b></a> -->
					<a class="edit" href="<?=esc_url($url->getContentEditor($content->uid))?>"><i class="ico_16px write"></i><?=__('Edit', 'kboard')?></a>
					<a class="edit" href="<?=esc_url($url->getContentRemove($content->uid))?>"><i class="ico_16px delete"></i><?=__('Delete', 'kboard')?> </a>
				</div>
			</div>
		</div>
		<div class="fdb_lst_wrp"><?php echo $board->buildComment($content->uid)?></div>
	</div>
</div>