<div class="bd hover_effect" >
	<!-- 카테고리 시작 -->
	<?php
	if( $category_type != '' ){
		$category_type = 'tree-'.$category_type;
		$category_type = apply_filters('kboard_skin_category_type', $category_type, $board, $boardBuilder);
		echo $skin->render($board->skin, "list-category-{$category_type}.php", $vars);
	}
	?>
	<!-- 카테고리 끝 -->
	<table id="document-table" class="bd_lst bd_tb_lst bd_tb">
		<caption class="blind">List of Posts</caption>
		<thead class="bg_f_f9">
			<tr>
				<th scope="col" class="no"><span><a href="" title=""><?php echo __('Number', 'x2board')?></a></span></th>
				<th scope="col" class="m_no"><span><?php echo __('Category', 'x2board')?></span></th>
				<th scope="col" class="title"><span><a href=""><?php echo __('Title', 'x2board')?></a></span></th>
				<th scope="col"><span><?php echo __('Author', 'x2board')?></span></th>
				<th scope="col"><span><a href=""><?php echo __('Date', 'x2board')?></a></span></th>
				<th scope="col" class="m_no"><span><?php echo __('Votes', 'x2board')?></span></th>
				<th scope="col" class="m_no"><span><a href=""><?php echo __('Views', 'x2board')?></a></span></th>
				<?php if($grant->manager):?>
					<th scope="col" class="m_no"><span><input type="checkbox" id='toggle_all_doc' title="Toggle All" /></span></th>
				<?php endif?>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $notice_list as $no => $post ): //while($content = $list->hasNextNotice()): ?>
				<tr class="notice">
					<td class="no"><strong>공지</strong></td>
					<td class="cate" style="color:"></td>
					<td class="title">
						<a href="<?php echo esc_url($post->get_pretty_url())?>"><strong><?php echo $post->title?></strong></a>
						<a href="<?php echo esc_url($post->get_pretty_url())?>#289220_comment" class="replyNum" title="댓글"><?php echo $post->get_comment_count()?></a>
						<span class="extraimages">
							<?php if($post->is_new()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($post->is_secret == 'Y'):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><?php echo $post->get_nick_name()?></td>
					<td class="time"><?php echo $post->get_regdate('Y.m.d')?></td>
					<td class="m_no"><?php echo $post->vote_count?></td>
					<td class="m_no"><?php echo $post->readed_count?></td>
					<?php if($grant->manager):?>
						<td class="check m_no"><input type="checkbox" value="<?=$post->post_id?>" name='doc_chk' title="Check This Post"/></td>
					<?php endif?>
				</tr>
			<?php endforeach //endwhile?>
			<?php if(false): // while($content = $list->hasNextPopular()):?>
				<tr <?php if($content->uid == kboard_uid()):?>class='select'<?php endif?> >
					<td class="no"><?php echo $list->index()?></td>
					<td class="cate"><span style="color:"><?=esc_html($content->category_name)?></span></td>
					<td class="title">
						<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="hx">
						<?php echo $content->title?></a>
						<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#289220_comment" class="replyNum" title="댓글"><?php echo $content->getCommentsCount()?></a>
						<span class="extraimages">
							<?php if($content->isNew()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($content->secret):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><span><?php echo $content->getUserDisplay()?></span></td>
					<td class="time" title="2 시간 전"><?php echo $content->getDate()?></td>
					<td class="m_no"><?php echo $content->vote?></td>
					<td class="m_no"><?php echo $content->view?></td>
					<?php if($board->isAdmin()):?>
						<td class="check m_no"><input type="checkbox" value="<?=$content->uid?>" name='doc_chk' title="Check This Post"/></td>
					<?php endif?>
				</tr>
			<?php endif //endwhile?>
			<?php foreach( $post_list as $no => $post ): //while($content = $list->hasNext()):?>
				<tr <?php if($post->post_id == $post_id):?>class='select'<?php endif?> >
					<td class="no"><?php echo $no?></td>
					<td class="cate"><span style="color:"><?=esc_html($post->category_id)?></span></td>
					<td class="title">
						<a href="<?php echo esc_url($post->get_pretty_url())?>" class="hx">
						<?php echo $post->title?></a>
						<a href="<?php echo esc_url($post->get_pretty_url())?>#289220_comment" class="replyNum" title="댓글"><?php echo $post->get_comment_count()?></a>
						<span class="extraimages">
							<?php if($post->is_new()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($post->is_secret == 'Y'):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><span><?php echo $post->get_nick_name()?></span></td>
					<td class="time" title="2 시간 전"><?php echo $post->get_regdate('Y.m.d')?></td>
					<td class="m_no"><?php echo $post->vote_count?></td>
					<td class="m_no"><?php echo $post->readed_count?></td>
					<?php if($grant->manager):?>
						<td class="check m_no"><input type="checkbox" value="<?=$post->post_id?>" name='doc_chk' title="Check This Post"/></td>
					<?php endif?>
				</tr>
				<?php // $boardBuilder->builderReply($content->uid)?>
			<?php endforeach //endwhile ?>
		</tbody>
	</table>
	<div class="btm_mn clear">
		<div class="fl">
			<?php if($grant->manager):?>
				<!-- 게시판 관리 기능 시작 -->
				<a class="btn_img" href="<?=admin_url('admin.php?page=x2b_disp_board_update&board_id='.$board_id);?>" target='_blank'><i class="ico_16px setup"></i> 설정</a>
				<a class="btn_img" id='btn_control_panel'><i class="tx_ico_chk">✔</i> 게시글 관리</a>
				<!-- 게시판 관리 기능 끝 -->
			<?php endif?>
		</div>
		<div class="fr">
			<?php if($grant->write_post):?>
				<!-- 게스트 버튼 시작 -->
				<a class="btn_img" href="<?php echo esc_url($url_write_post)?>"><i class="ico_16px write"></i> <?php echo __('New', 'x2board')?></a>
				<!-- 게스트 버튼 끝 -->
			<?php endif?>
		</div>
	</div>
</div>

<div id="kboard-default-list">
	<!-- 페이징 시작 -->
	<div class="kboard-pagination">
		<ul class="kboard-pagination-pages">
			<?php // echo kboard_pagination($list->page, $list->total, $list->rpp)?>
		</ul>
	</div>
	<!-- 페이징 끝 -->
	
	<!-- 검색폼 시작 -->
	<div class="kboard-search">
		<form id="kboard-search-form-<?php echo $board_id?>" method="get" action="<?php echo esc_url($url->toString())?>">
			<?php echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
			
			<select name="target">
				<option value=""><?php echo __('All', 'x2board')?></option>
				<option value="title"<?php if(kboard_target() == 'title'):?> selected<?php endif?>><?php echo __('Title', 'x2board')?></option>
				<option value="content"<?php if(kboard_target() == 'content'):?> selected<?php endif?>><?php echo __('Content', 'x2board')?></option>
				<option value="member_display"<?php if(kboard_target() == 'member_display'):?> selected<?php endif?>><?php echo __('Author', 'x2board')?></option>
			</select>
			<input type="text" name="keyword" value="<?php echo esc_attr(kboard_keyword())?>">
			<button type="submit" class="kboard-default-button-small"><?php echo __('Search', 'x2board')?></button>
		</form>
	</div>
	<!-- 검색폼 끝 -->
</div>

<?php if($board->isAdmin() && $board->isTreeCategoryActive()):?>
<!-- 게시판 관리 기능 시작 -->
<div clas1s="kboard-control" id='panel_control' style="margin-top:12px; display:none;">
	<button type="button" id='btn_move_category' data-board-id='<?=$board->id?>' class="kboard-default-button-small"><?php echo __('Move Category to', 'x2board')?></button>
	<select name="target_category">
		<option value=""><?php echo __('Category select', 'x2board')?></option>
		<?php foreach($board->getCategoryList() as $cat_id=>$option_val):?>
			<option value="<?=$cat_id?>">
			<?=str_repeat("&nbsp;&nbsp;",$option_val->depth)?> <?=$option_val->category_name?> (<?=$option_val->document_count?>)
			</option>
		<?php endforeach?>
	</select>
</div>
<!-- 게시판 관리 기능 끝 -->
<script>
var bToggled = false;
jQuery('#btn_control_panel').click(function() {
	jQuery('#panel_control').slideToggle('slow', function() {
		if( !bToggled ) {
			bToggled = true;
		}
		else {
			bToggled = false;
		}
	});
});
</script>
<?php endif?>

<?php if($board->contribution()):?>
<div class="kboard-default-poweredby">
	<a href="#" title="">Powered by x2board</a>
</div>
<?php endif?>