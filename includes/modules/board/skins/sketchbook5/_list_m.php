<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}
// var_dump($mi->zine_thumb_width,$mi->zine_thumb_height,$mi->zine_thumb_type);
?>

<ol class="<?php echo $mi->zine_info_icon ?> bd_lst bd_zine zine zine1 bd_m_lst img_load<?php echo $mi->tmb_effect ?>">
	<?php if(!$post_list && !$notice_list): ?><!-- cond="!$post_list && !$notice_list"  -->
		<li class="no_doc"><?php echo __('msg_no_posts', X2B_DOMAIN)?></li>
	<?php endif ?>
	<!--// 공지 목록 -->
	<?php if($notice_list): ?><!-- cond="$notice_list"  -->
		<?php foreach($notice_list as $no => $post): ?>	<!-- loop="$notice_list=>$no,$post" -->
			<li class="notice clear">
				<!--// 썸네일 영역 -->
				<div class="rt_area<?php if($mi->list_m_tmb != ' ' && $post->check_thumbnail()): ?> is_tmb<?php endif ?>">
					<?php if($mi->list_m_tmb != ' '): ?><!-- cond="$mi->list_m_tmb" -->
						<div class="tmb_wrp">
							<!--// 썸네일 -->
							<?php if($post->check_thumbnail()): ?><!-- cond="$post->check_thumbnail()" -->
								<img class="tmb" src="<?php $post->get_thumbnail($mi->zine_thumb_width,$mi->zine_thumb_height,$mi->zine_thumb_type) ?>" alt="" />
							<?php endif ?>
						</div>
					<?php endif ?>
					<!--// 제목 -->
					<h3>
						<?php echo $post->get_title($mi->subject_cut_size) ?>
						<?php if((int)($post->get_regdate('YmdHis') > date("YmdHis", time() - $mi->duration_new*3600))): // 60*60 ?>
							<i class="mrk new">NEW</i>
						<?php elseif((int)(x2b_zdate($post->get('last_update'),'YmdHis') > date("YmdHis", time() - $mi->duration_new*3600))): // 60*60 ?>
							<i class="mrk update">UP</i>
						<?php endif ?>
					</h3>
					<!--// 글 정보 -->
					<div class="info">
					<?php if($list_config['regdate_dt']): ?><!-- cond="$list_config['regdate']" -->
							<span><i class="fa fa-clock-o"></i><span>Date</span><b><?php echo $post->get_regdate("Y.m.d") ?></b></span>
						<?php endif ?>							
						<?php if($mi->use_category=='Y' && $post->get('category_id')): ?><!-- cond="$mi->use_category=='Y' && $post->get('category_srl')" -->
							<span><i class="fa fa-bars"></i><span>Category</span><b><?php echo $category_list[$post->get('category_id')]->title ?></b></span>
						<?php endif ?>
						<?php if($list_config['nick_name']): ?><!-- cond="$list_config['nick_name']" -->
							<span><i class="fa fa-user"></i><span>By</span><b><a href="#popup_menu_area" class="member_<?php echo $post->get('post_author') ?>" onclick="return false;"><?php echo $post->get_nick_name() ?></a></b></span>
						<?php endif ?>
						<?php if(in_array('list_m',$mi->cmt_count)): ?><!-- cond="@in_array('list_m',$mi->cmt_count)" -->
							<span><i class="fa fa-comment"></i><span>Reply</span><b><?php echo $post->get_comment_count() ?></b></span>
						<?php endif ?>
						<?php if($list_config['readed_count']): ?><!-- cond="$list_config['readed_count']" -->
							<span><i class="fa fa-eye"></i><span>Views</span><b><?php echo $post->get('readed_count') ?></b></span>
						<?php endif ?>
						<?php if(isset($list_config['voted_count'])): ?><!-- cond="$list_config['voted_count']" -->
							<span><i class="fa fa-heart"></i><span>Votes</span><b><?php echo $post->get('voted_count') ?></b></span>
						<?php endif ?>
						<!--// 확장변수 -->
						<?php if($mi->zine_extra != ' ' || $mi->link_board != ' '): ?><!-- <block cond="$mi->zine_extra || $mi->link_board"> -->
							<?php foreach($list_config as $key => $val): ?><!-- <block loop="$list_config=>$key,$val" cond="$val->idx!=-1"> -->
								<?php if($val->idx != -1): ?>
									<?php if($val->eid!='rating' && $post->get_user_define_value_HTML($val->eid)): ?><!-- cond="$val->eid!='rating' && $post->get_user_define_value_HTML($val->eid)"  -->
										<span class="itm br<?php if($val->eid=='link_url'): ?> link_url<?php endif ?>"><?php echo $val->name ?><b><?php echo $post->get_user_define_value_HTML($val->eid) ?></b></span>
									<?php endif ?>
									<?php if($val->eid=='rating'): ?><!-- cond="$val->eid=='rating'" -->
										<span><strong class="starRating"><span style="width:<?php echo $post->get_user_define_value_HTML($val->eid)*10 ?>%"><?php echo $post->get_user_define_value_HTML($val->eid) ?></span></strong></span>
									<?php endif ?>
								<?php endif ?>
							<?php endforeach ?><!-- </block> -->
						<?php endif ?><!-- </block> -->
						<?php if(in_array('list_m',$mi->ext_img)): ?><!-- <block cond="@in_array('list_m',$mi->ext_img)"> -->
							<?php echo $post->print_extra_images(60*60*$mi->duration_new) ?>
						<?php endif ?><!-- </block> -->
						<?php if($grant->manager): ?><!-- cond="$grant->manager"  -->
							<input type="checkbox" name="cart" value="<?php echo $post->post_id ?>" title="Check" onclick="doAddDocumentCart(this)" <?php if($post->is_carted()): ?> checked="checked" <?php endif ?> />  <!-- |cond="$post->isCarted()" -->
						<?php endif ?>
					</div>
				</div>
				<a href="<?php echo x2b_get_url('post_id',$post->post_id,'listStyle',$listStyle, 'cpage','') ?>"><span class="blind">read more</span></a>
			</li>
		<?php endforeach ?>
	<?php endif ?>
	<!--// 일반 목록 -->
	<?php foreach($post_list as $no => $post): ?><!-- loop="$post_list=>$no,$post"  -->
		<li class="<?php if($post_id==$post->post_id): ?>select <?php endif ?>clear">
			<!--// 썸네일 영역 -->
			<div class="rt_area<?php if($mi->list_m_tmb != ' ' && $post->check_thumbnail()):?> is_tmb<?php endif ?>">
				<?php if($mi->list_m_tmb != ' '): ?><!-- cond="$mi->list_m_tmb"  -->
					<div class="tmb_wrp">
						<!--// 썸네일 -->
						<?php if($post->check_thumbnail()): ?><!-- cond="$post->check_thumbnail()"  -->
							<img class="tmb" src="<?php echo $post->get_thumbnail($mi->zine_thumb_width,$mi->zine_thumb_height,$mi->zine_thumb_type) ?>" alt="" />
						<?php endif ?>
					</div>
				<?php endif ?>
				<!--// 제목 -->
				<h3>
				<?php echo $post->get_title($mi->subject_cut_size) ?>
					<?php if((int)($post->get_regdate('YmdHis')>date("YmdHis", time()-$mi->duration_new*60*60))): ?>
						<i class="mrk new">NEW</i>
					<?php elseif((int)(x2b_zdate($post->get('last_update'),'YmdHis')>date("YmdHis", time()-$mi->duration_new*60*60))): ?>
						<i class="mrk update">UP</i>
					<?php endif ?>
				</h3>
				<!--// 글 정보 -->
				<div class="info">
					<?php if($list_config['regdate_dt']): ?><!-- cond="$list_config['regdate']" -->
						<span><i class="fa fa-clock-o"></i><span>Date</span><b><?php echo x2b_get_time_gap($post->get('regdate_dt'), "Y.m.d") ?></b></span>
					<?php endif ?>
					<?php if($mi->use_category=='Y' && $post->get('category_id')): ?><!-- cond="$mi->use_category=='Y' && $post->get('category_srl')" -->
						<span><i class="fa fa-bars"></i><span>Category</span><b><?php echo $category_list[$post->get('category_id')]->title ?></b></span>
					<?php endif ?>
					<?php if($list_config['nick_name']): ?><!-- cond="$list_config['nick_name']" -->
						<span><i class="fa fa-user"></i><span>By</span><b><a href="#popup_menu_area" class="member_<?php echo $post->get('post_author') ?>" onclick="return false;"><?php echo $post->get_nick_name() ?></a></b></span>
					<?php endif ?>
					<?php if(in_array('list_m',$mi->cmt_count)): ?><!-- cond="@in_array('list_m',$mi->cmt_count)" -->
						<span><i class="fa fa-comment"></i><span>Reply</span><b><?php echo $post->get_comment_count() ?></b></span>
					<?php endif ?>
					<?php if($list_config['readed_count']): ?><!-- cond="$list_config['readed_count']" -->
						<span><i class="fa fa-eye"></i><span>Views</span><b><?php echo $post->get('readed_count') ?></b></span>
					<?php endif ?>
					<?php if(isset($list_config['voted_count'])): ?><!-- cond="$list_config['voted_count']" -->
						<span><i class="fa fa-heart"></i><span>Votes</span><b><?php echo $post->get('voted_count') ?></b></span>
					<?php endif ?>
					<!--// 확장변수 -->
					<?php if($mi->zine_extra != ' ' || $mi->link_board != ' '): ?><!-- <block cond="$mi->zine_extra || $mi->link_board"> -->
						<?php foreach($list_config as $key => $val): ?><!-- <block loop="$list_config=>$key,$val" cond="$val->idx!=-1"> -->
							<?php if($val->idx!=-1): ?>
								<?php if($val->eid!='rating' && $post->get_user_define_value_HTML($val->eid)): ?><!-- cond="$val->eid!='rating' && $post->get_user_define_value_HTML($val->eid)"  -->
									<span class="itm br<?php if($val->eid=='link_url'): ?> link_url<?php endif ?>"><?php echo $val->name ?><b><?php echo $post->get_user_define_value_HTML($val->eid) ?></b></span>
								<?php endif ?>
								<?php if($val->eid=='rating'): ?><!-- cond="$val->eid=='rating'" -->
									<span><strong class="starRating"><span style="width:<?php echo $post->get_user_define_value_HTML($val->eid)*10 ?>%"><?php echo $post->get_user_define_value_HTML($val->eid) ?></span></strong></span>
								<?php endif ?>
							<?php endif ?>
						<?php endforeach ?><!-- </block> -->
					<?php endif ?><!-- </block> -->
					<?php if(in_array('list_m',$mi->ext_img)): ?><!-- cond="@in_array('list_m',$mi->ext_img)"  -->
						<strong class="attached_image" title="Image"></strong>
					<?php endif ?>
					<?php if($grant->manager): ?><!-- cond="$grant->manager"  -->
						<input type="checkbox" name="cart" value="<?php echo $post->post_id ?>" title="Check" onclick="doAddDocumentCart(this)" <?php if($post->is_carted()): ?> checked="checked" <?php endif ?>/> <!-- |cond="$post->isCarted()" -->
					<?php endif ?>
					<?php if($mi->link_board != ' ' && $post->is_editable()): ?><!-- cond="$mi->link_board && $post->isEditable()"  -->
						<a class="link_modify" href="<?php echo x2b_get_url('act','dispBoardWrite','post_id',$post->post_id,'comment_id','') ?>"><?php echo __('cmd_modify', X2B_DOMAIN)?></a>
					<?php endif ?>
				</div>
			</div>
			<a class="hx" <?php if($mi->link_board == ' '): ?>href="<?php echo x2b_get_url('post_id',$post->post_id,'listStyle',$listStyle,'cpage','') ?>"<?php else: ?> href="<?php echo $post->get_user_define_eid_value('link_url') ?>" target="_blank"<?php endif ?> data-viewer="<?php echo x2b_get_url('post_id',$post->post_id,'listStyle','viewer','page','') ?>"><span class="blind">Read More</span></a>
		</li>
	<?php endforeach ?>
</ol>