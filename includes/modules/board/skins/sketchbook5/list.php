<?php if($post->is_exists()) {
	x2b_include_skin('post');
}?>

<div class="bd hover_effect" >
	<!-- 카테고리 시작 -->
	<?php
	if( $this->module_info->use_category == 'Y') {
		$category_type = 'tree-tab';
		x2b_include_skin("list-category-{$category_type}");
	}
	?>
	<!-- 카테고리 끝 -->
	<table id="document-table" class="bd_lst bd_tb_lst bd_tb">
		<caption class="blind"><?php echo __('List of Posts', 'x2board')?></caption>
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
					<td class="no"><strong><?php echo __('Notice', 'x2board')?></strong></td>
					<td class="cate" style="color:"></td>
					<td class="title">
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>"><strong><?php echo $post->title?></strong></a>
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>#289220_comment" class="replyNum" title="댓글"><?php echo $post->get_comment_count()?></a>
						<span class="extraimages">
							<?php if($post->is_new()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($post->is_secret == 'Y'):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><?php echo $post->get_nick_name()?></td>
					<td class="time"><?php echo $post->get_regdate('Y.m.d')?></td>
					<td class="m_no"><?php echo $post->voted_count?></td>
					<td class="m_no"><?php echo $post->readed_count?></td>
					<?php if($grant->manager):?>
						<td class="check m_no"><input type="checkbox" value="<?php echo $post->post_id?>" name='doc_chk' title="Check This Post"/></td>
					<?php endif?>
				</tr>
			<?php endforeach //endwhile?>
			<?php if(false): // while($content = $list->hasNextPopular()):?>
				<tr <?php if($content->uid == kboard_uid()):?>class='select'<?php endif?> >
					<td class="no"><?php echo $list->index()?></td>
					<td class="cate"><span style="color:"><?php echo esc_html($content->category_name)?></span></td>
					<td class="title">
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>" class="hx">
						<?php echo $content->title?></a>
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>#289220_comment" class="replyNum" title="댓글"><?php echo $content->getCommentsCount()?></a>
						<span class="extraimages">
							<?php if($content->isNew()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($content->secret):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><span><3?php echo $content->getUserDisplay()?></span></td>
					<td class="time" title="2 시간 전"><?php echo $content->getDate()?></td>
					<td class="m_no"><?php echo $content->vote?></td>
					<td class="m_no"><?php echo $content->view?></td>
					<?php if($board->isAdmin()):?>
						<td class="check m_no"><input type="checkbox" value="<?php echo $content->uid?>" name='doc_chk' title="Check This Post"/></td>
					<?php endif?>
				</tr>
			<?php endif //endwhile?>
			<?php 
			foreach( $post_list as $no => $post ): //while($content = $list->hasNext()):?>
				<tr <?php if($post->post_id == $post_id):?>class='select'<?php endif?> >
					<td class="no"><?php echo $no?></td>
					<td class="cate"><span style="color:"><?php echo esc_html($post->category_title)?></span></td>
					<td class="title">
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>" class="hx">
						<?php echo $post->title?></a>
						<a href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id))?>#289220_comment" class="replyNum" title="댓글"><?php echo $post->get_comment_count()?></a>
						<span class="extraimages">
							<?php if($post->is_new()):?><span class="kboard-default-new-notify">N</span><?php endif?>
							<?php if($post->is_secret == 'Y'):?><img src="<?php echo $skin_path?>/img/icon-lock.png" alt="<?php echo __('Secret', 'x2board')?>"><?php endif?>
						</span>
					</td>
					<td class="author"><span><?php echo $post->get_nick_name()?></span></td>
					<td class="time" title="2 시간 전"><?php echo $post->get_regdate('Y.m.d')?></td>
					<td class="m_no"><?php echo $post->voted_count?></td>
					<td class="m_no"><?php echo $post->readed_count?></td>
					<?php if($grant->manager):?>
						<td class="check m_no"><input type="checkbox" value="<?php echo $post->post_id?>" name='doc_chk' title="Check This Post"/></td>
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
				<a class="btn_img" href="<?php echo admin_url('admin.php?page=x2b_disp_board_update&board_id='.$board_id);?>" target='_blank'><i class="ico_16px setup"></i> <?php echo __('Configure board', 'x2board')?></a>
				<a class="btn_img" id='btn_control_panel'><i class="tx_ico_chk">✔</i><?php echo __('Manage posts', 'x2board')?></a>
				<!-- 게시판 관리 기능 끝 -->
			<?php endif?>
		</div>
		<div class="fr">
			<?php if($grant->write_post):?>
				<!-- 게스트 버튼 시작 -->
				<a class="btn_img" href="<?php echo esc_url(x2b_get_url('cmd', X2B_CMD_VIEW_WRITE_POST, 'post_id', '', 'page', ''))?>"><i class="ico_16px write"></i> <?php echo __('New', 'x2board')?></a>
				<!-- 게스트 버튼 끝 -->
			<?php endif?>
		</div>
	</div>
</div>

<?php
$prev_page = max($page-1, 1);
$next_page = min($page+1, $page_navigation->n_last_page);
$mi_page_count = $this->n_page_count;
// var_dump($prev_page);
// var_dump($next_page);
// var_dump($this->n_page_count);
// var_dump($page_navigation->n_last_page);
// var_dump($page);
?>

<div id="kboard-default-list">
	<!-- <div class="kboard-pagination">
		<ul class="kboard-pagination-pages"> -->
		<?php // echo kboard_pagination($list->page, $list->total, $list->rpp)?>
		<!-- </ul>
	</div> -->
	<!-- 검색폼 시작 -->
	<div class="kboard-search">
		<!-- 페이징 시작 -->
		<!--// 페이지네이션 -->
		<form action="./" method="get" class="bd_pg clear">
			<fieldset>
			<legend class="blind"><?php echo __('Board Pagination', 'x2board')?></legend>
			<input type="hidden" name="vid" value="{$vid}" />
			<input type="hidden" name="mid" value="{$mid}" />
			<input type="hidden" name="category" value="{$category}" />
			<input type="hidden" name="search_keyword" value="{htmlspecialchars($search_keyword)}" />
			<input type="hidden" name="search_target" value="{$search_target}" />
			<input type="hidden" name="listStyle" value="{$mi->default_style}" />
			
			<?php if( $page!=$prev_page ):?>
				<a href="<?php echo x2b_get_url('page',$prev_page,'post_id','')?>" class="direction"><i class="fa fa-angle-left"></i> <?php echo __('Prev', 'x2board')?></a>
			<?php endif?>
			<?php if( $page==$prev_page ):?>
				<strong class="direction"><i class="fa fa-angle-left"></i> <?php echo __('Prev', 'x2board')?></strong>
			<?php endif?>
			<a class="frst_last bubble <?php if( $page==1 ):?> this<?php endif?>" href="<?php echo x2b_get_url('page','','post_id','')?>" title="<?php echo __('first_page', 'x2board')?>">1</a>
			<?php if( $page>($mi_page_count)/2+2 ):?>
				<span class="bubble"><a href="#" class="tg_btn2" data-href=".bd_go_page" title="<?php echo __('go_page', 'x2board')?>">...</a></span>
			<?php endif?>

			<!-- <block loop="$page_no=$page_navigation->getNextPage()" cond="$page_no!=1 && $page_no!=$page_navigation->last_page"> -->
			<?php while($page_no = $page_navigation->getNextPage()) {
				if( $page_no==1 || $page_no==$page_navigation->n_last_page ){
					continue;
				}
			?>
				<?php if( $page==$page_no ):?>
					<!-- <strong class="this" cond="$page==$page_no">{$page_no}</strong>  -->
					<strong class="this"><?php echo $page_no?></strong> 
				<?php else:?>
					<!-- <a cond="$page!=$page_no" href="{getUrl('page',$page_no,'document_srl','')}">{$page_no}</a> -->
					<a href="<?php echo x2b_get_url('page',$page_no,'post_id','')?>"><?php echo $page_no?></a>
				<?php endif?>	
			<?php }?>
			<!-- </block> -->

			<?php if( ($page+($mi_page_count+1)/2<$page_navigation->n_last_page) && ($mi_page_count+1<$page_navigation->n_last_page) ):?>
				<!-- <span cond="($page+($mi->page_count+1)/2<$page_navigation->last_page) && ($mi->page_count+1<$page_navigation->last_page)" class="bubble"><a href="#" class="tg_btn2" data-href=".bd_go_page" title="{$lang->cmd_go_to_page}">...</a></span> -->
				<span class="bubble"><a href="#" class="tg_btn2" data-href=".bd_go_page" title="<?php echo __('cmd_go_to_page', 'x2board')?>">...</a></span>
			<?php endif?>
			<?php if( $page_navigation->n_last_page!=1 ):?>
				<a class="frst_last bubble  <?php if( $page==$page_navigation->n_last_page ):?> this <?php endif?>" href="<?php echo x2b_get_url('page', $page_navigation->n_last_page, 'post_id','')?>" title="<?php echo __('last_page', 'x2board')?>"><?php echo $page_navigation->n_last_page?></a>
			<?php endif?>
			<?php if( $page!=$next_page ):?>
				<!-- <a cond="$page!=$next_page" href="{getUrl('page',$next_page,'document_srl','')}" class="direction">Next <i class="fa fa-angle-right"></i></a> -->
				<a href="<?php echo x2b_get_url('page',$next_page,'post_id','')?>" class="direction"><?php echo __('Next', 'x2board')?> <i class="fa fa-angle-right"></i></a>
			<?php endif?>
			<?php if( $page==$next_page ):?>
				<!-- <strong cond="$page==$next_page" class="direction">Next <i class="fa fa-angle-right"></i></strong> -->
				<strong class="direction"><?php echo __('Next', 'x2board')?> <i class="fa fa-angle-right"></i></strong>
			<?php endif?>
			<div class="bd_go_page tg_cnt2 wrp">
				<button type="button" class="tg_blur2"></button>
				<input type="text" name="page" class="itx" />/ <?php echo $page_navigation->n_last_page?> <button type="submit" class="bd_btn"><?php echo __('GO', 'x2board')?></button>
				<span class="edge"></span>
				<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
				<button type="button" class="tg_blur2"></button>
			</div>
			</fieldset>
		<!-- </form> -->
		<!-- 페이징 끝 -->
		
		<!-- <form id="kboard-search-form-<?php echo $board_id?>" method="get" action="<?php echo x2b_get_url('cmd', '')?>"> -->
			<?php //echo $url->set('pageid', '1')->set('target', '')->set('keyword', '')->set('mod', 'list')->toInput()?>
			
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

<?php if($grant->manager ):
	//&& $board->isTreeCategoryActive()):?>
<!-- 게시판 관리 기능 시작 -->
<div clas1s="kboard-control" id='panel_control' style="margin-top:12px; display:none;">
	<button type="button" id='btn_move_category' data-board-id='<?php echo $board_id?>' class="kboard-default-button-small"><?php echo __('Move Category to', 'x2board')?></button>
	<select name="target_category">
		<option value=""><?php echo __('Category select', 'x2board')?></option>
		<?//php foreach($board->getCategoryList() as $cat_id=>$option_val):?>
			<option value="<? // echo $cat_id?>">
			<?// echo str_repeat("&nbsp;&nbsp;",$option_val->depth)?> <?// echo $option_val->category_name?> (<? // echo $option_val->document_count?>)
			</option>
		<?//php endforeach?>
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

<?php //if($board->contribution()):?>
<div class="kboard-default-poweredby">
	<a href="#" title="">Powered by x2board</a>
</div>
<?php //endif?>