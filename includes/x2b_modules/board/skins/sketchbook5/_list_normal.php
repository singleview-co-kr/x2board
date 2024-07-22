<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if(isset($order_type) && $order_type=="desc"){
	$order_icon="down";
	$order_type="asc";
}
else{
	$order_icon="up";
	$order_type="desc";	
}?>
<?php if(!$post_list && !$notice_list): ?><!-- cond="!$document_list && !$notice_list"  -->
<p class="no_doc">
	<?php echo __('msg_no_posts', X2B_DOMAIN)?>
</p>
<?php endif ?>

<?php if($post_list || $notice_list): ?>  <!-- cond="$document_list || $notice_list"  -->
<table class="bd_lst bd_tb_lst bd_tb">
	<caption class="blind">List of Articles</caption>
	<thead class="bg_f_f9">
		<!--// 테이블 헤더 -->
		<tr>
	<?php foreach( $list_config as $_ => $val ):?><!-- <block loop="$list_config=>$key,$val"> -->
		<?php if($val->var_type=='no' && $val->idx==-1): ?><!-- cond="$val->type=='no'" -->
			<th scope="col" class="no"><span><a href="<?php echo x2b_get_url('order_type',$order_type)?>" title="<?php if($order_type=="desc"):?><?php echo __('lbl_order_desc', X2B_DOMAIN)?><?php else:?><?php echo __('lbl_order_asc', X2B_DOMAIN)?><?php endif ?>"><?php echo __('lbl_number', X2B_DOMAIN)?></a></span></th>
		<?php elseif($val->var_type=='title' && $val->idx==-1): ?><!-- <block cond="$val->type=='title'"> -->
			<?php if($mi->show_cate == ' ' && $mi->use_category=='Y'): ?><!-- cond="!$mi->show_cate && $mi->use_category=='Y'"  -->
			<th scope="col" class="m_no"><span><?php echo __('lbl_category', X2B_DOMAIN)?></span></th>
			<?php endif ?>
			<th scope="col" class="title"><span><a href="<?php echo x2b_get_url('sort_index','title','order_type',$order_type)?>"><?php if($mi->link_board == ' '):?><?php echo __('lbl_title', X2B_DOMAIN)?><?php else:?><?php echo __('lbl_link_site', X2B_DOMAIN) ?><?php endif ?><?php if($sort_index=='title'):?><i class="arrow <?php echo $order_icon?>"></i><?php endif ?></a></span></th>
		<?php elseif($val->var_type=='nick_name' && $val->idx==-1): ?><!-- cond="$val->type=='nick_name'" -->
			<th scope="col"><span><?php echo __('lbl_writer', X2B_DOMAIN)?></span></th>
		<?php elseif($val->var_type=='regdate_dt' && $val->idx==-1): ?><!-- cond="$val->type=='regdate'" -->
			<th scope="col" ><span><a href="<?php echo x2b_get_url('sort_index','regdate','order_type',$order_type)?>"><?php echo __('lbl_date', X2B_DOMAIN)?><?php if($sort_index=='regdate'):?><i class="arrow <?php echo $order_icon?>"></i><?php endif ?></a></span></th>
		<?php elseif($val->var_type=='last_update_dt' && $val->idx==-1): ?><!-- cond="$val->type=='last_update'"  -->
			<th scope="col" class="m_no"><span><a href="<?php echo x2b_get_url('sort_index','last_update','order_type',$order_type)?>"><?php echo __('lbl_last_update', X2B_DOMAIN)?><?php if($sort_index=='last_update'):?><i class="arrow <?php echo $order_icon?>"></i><?php endif ?></a></span></th>
		<!-- <th scope="col" cond="$val->type=='last_post'" class="m_no"><span>{$lang->last_post}</span></th> -->
		<?php elseif($val->var_type=='readed_count' && $val->idx==-1): ?><!-- cond="$val->type=='readed_count'"  -->
			<th scope="col" class="m_no"><span><a href="<?php echo x2b_get_url('sort_index','readed_count','order_type',$order_type)?>"><?php echo __('lbl_readed_count', X2B_DOMAIN)?><?php if($sort_index=='readed_count'):?><i class="arrow <?php echo $order_icon?>"></i><?php endif ?></a></span></th>
		<?php elseif($val->var_type=='voted_count' && $val->idx==-1): ?><!-- cond="$val->type=='voted_count'"  -->
			<th scope="col" class="m_no"><span><a href="<?php echo x2b_get_url('sort_index','voted_count','order_type',$order_type)?>"><?php echo __('lbl_voted_count', X2B_DOMAIN)?><?php if($sort_index=='voted_count'):?><i class="arrow <?php echo $order_icon?>"></i><?php endif ?></a></span></th>
		<?php else: ?>	<!-- cond="$val->idx!=-1" -->
			<?php if($val->eid!='link_url' && $val->idx!=-1): ?><!-- cond="$val->eid!='link_url'" -->
			<th scope="col" class="m_no"><span><a href="<?php echo x2b_get_url('sort_index',$val->eid,'order_type',$order_type)?>"><?php echo $val->var_name ?></a></span></th>
			<?php endif ?>
		<?php endif ?>		
	<?php endforeach ?> <!-- </block> -->
	<?php if($grant->manager): ?>	<!-- cond="$grant->manager"  -->
			<th scope="col" class="m_no"><span><input type="checkbox" onclick="checkboxToggleAll({ doClick:true });" class="iCheck" id="to1ggle_all_post" title="Check All" /></span></th>
	<?php endif ?>					
		</tr>
	</thead>
	<tbody>
		<!--// Notice -->
	<?php foreach( $notice_list as $no => $post ):  //  loop="$notice_list=>$no,$document" 
		$s_category_color = null;
		$s_category_title = null;
		if(isset($category_list[$post->get('category_id')])) {
			if($category_list[$post->get('category_id')]->color!='transparent'){
				$s_category_color = 'style=color:'.$category_list[$post->get('category_id')]->color;
			}
			$s_category_title = $category_list[$post->get('category_id')]->title;
		}?>
		<tr class="notice">
		<?php foreach( $list_config as $_ => $val ):?><!-- <block loop="$list_config=>$key,$val"> -->
			<?php if($val->var_type=='no'): ?><!-- cond="$val->type=='no'"  -->
				<td class="no">
					<?php if($post_id==$post->post_id): ?>&raquo;<?php endif ?>	<?php if($post_id!=$post->post_id): ?><strong ><?php echo __('lbl_notice', X2B_DOMAIN)?></strong><?php endif ?>	
				</td>
			<?php endif ?>
			<?php if($val->var_type=='title'): ?><!-- <block cond="$val->type=='title'"> -->
				<?php if($mi->show_cate == ' ' && $mi->use_category=='Y'): ?> <!-- cond="!$mi->show_cate && $mi->use_category=='Y'"  -->
				<td class="cate" <?php echo $s_category_color?> ><strong><?php echo $s_category_title ?></strong></td>
				<?php endif ?>
				<td class="title">
					<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id, 'listStyle', $listStyle, 'cpage','')?>"><strong><?php echo $post->get_title($mi->subject_cut_size)?></strong></a>
					<?php if($mi->link_board == ' '): ?><!-- <block cond="!$mi->link_board"> -->
					<a cond="$post->get_comment_count()" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id)?>#<?php echo $post->post_id?>_comment" class="replyNum" title="<?php echo __('lbl_comment', X2B_DOMAIN)?>"><?php echo $post->get_comment_count()?></a>
					<!-- <a cond="$post->getTrackbackCount()" href="<?php //echo esc_url('post_id', $post->post_id)?>#{$post->post_id}_trackback" class="trackbackNum" title="{$lang->trackback}">{$post->getTrackbackCount()}</a> -->
					<?php endif ?><!-- </block> -->
					<span class="extraimages"><?php echo $post->print_extra_images($mi->duration_new)?></span>
				</td>
			<?php endif ?>	<!-- </block> -->
			<?php if($val->var_type=='nick_name'): ?><!-- cond="$val->type=='nick_name'" -->
				<td class="author"><span><a href="#popup_menu_area" class="member_<?php echo $post->get('post_author')?>" onclick="return false"><?php echo $post->get_nick_name() ?></a></span></td>
			<?php endif ?>
				<!-- <td class="author" cond="$val->type=='user_id'"><span>{$post->getUserID()}</span></td>
				<td class="author" cond="$val->type=='user_name'"><span>{$post->getUserName()}</span></td> -->
			<?php if($val->var_type=='regdate_dt'): ?><!-- cond="$val->type=='regdate_dt'" -->
				<td class="time"><?php echo $post->get_regdate('Y.m.d')?></td>
			<?php endif ?>
			<?php if($val->var_type=='last_update_dt'): ?><!-- cond="$val->type=='last_update_dt'" -->
				<td class="time m_no"><?php echo x2b_zdate($post->get('last_update'),'Y.m.d')?></td>
			<?php endif ?>				
				<!-- <td class="time last_post m_no" cond="$val->type=='last_post'">
					<block cond="(int)($post->get('comment_count'))>0">
						<a href="<?php //echo x2b_get_url('post_id',$post->post_id)?>#{$post->post_id}_comment" title="{getTimeGap($post->get('last_update'), "H:i")}">{zdate($post->get('last_update'),'Y.m.d')}<small cond="$post->getLastUpdater()">(by {$post->getLastUpdater()})</small></a>
					</block>
					<block cond="(int)($post->get('comment_count'))==0">&nbsp;</block>
				</td> -->
			<?php if($val->var_type=='readed_count'): ?><!-- cond="$val->type=='readed_count'"  -->
				<td class="m_no"><?php echo $post->get('readed_count')>0?$post->get('readed_count'):'0'?></td> 
			<?php endif ?>
			<?php if($val->var_type=='voted_count'): ?><!-- cond="$val->type=='voted_count'" -->
				<td class="m_no"><?php echo $post->get('voted_count')!=0?$post->get('voted_count'):'0'?></td>
			<?php endif ?>
			<?php if($val->idx!=-1): ?><!-- cond="$val->idx!=-1"  -->
				<td <?php if($val->eid!='link_url'): ?> class="m_no" <?php endif ?>>
				<?php if($val->eid=='rating'): ?>
						<span class="starRating" title="<?php echo $post->get_user_define_value_HTML($val->idx)?> <?php echo __('lbl_score', X2B_DOMAIN) ?>"><span style="width:<?php echo $post->get_user_define_value_HTML($val->idx)*10 ?>%"><?php echo $post->get_user_define_value_HTML($val->idx)?></span></span>
				<?php else: ?><!--@else-->
					<?php echo $post->get_user_define_value_HTML($val->idx)?>
				<?php endif ?><!--@end--></td>
			<?php endif ?>
		<?php endforeach ?>	<!-- </block> -->
		<?php if($grant->manager): ?><!-- cond="$grant->manager"  -->
			<td class="check m_no"><input type="checkbox" name="cart" value="<?php echo $post->post_id?>" class="iCheck" title="Check This Article" onclick="doAddPostCart(this)" <?php if($post->is_carted()): ?>checked="checked" <?php endif?> /></td>
		<?php endif ?>	
		</tr>
	<?php endforeach ?>
		<!--// Normal -->
	<?php 
// $post_list = array();
	foreach( $post_list as $no => $post ):  // loop="$post_list=>$no,$post" 
		$s_category_color = null;
		$s_category_title = null;
		if(isset($category_list[$post->get('category_id')])) {
			// $cur_category_info = $category_list[$post->get('category_id')];
			if($category_list[$post->get('category_id')]->color!='transparent'){
				$s_category_color = 'style=color:'.$category_list[$post->get('category_id')]->color;
			}
			$s_category_title = $category_list[$post->get('category_id')]->title;
		}?>
		<tr <?php if($post_id==$post->post_id): ?> class="select" <?php endif ?> >
		<?php foreach( $list_config as $_ => $val ):?><!-- <block loop="$list_config=>$key,$val"> -->
			<?php if($val->var_type=='no'): ?><!-- cond="$val->type=='no'"  -->
			<td class="no">
				<?php if($post_id==$post->post_id): ?>&raquo;<?php endif ?>	
				<?php if($post_id!=$post->post_id): ?><?php echo $no?> <?php endif ?>	
			</td>
			<?php endif ?>
			<?php if($val->var_type=='title'): ?><!-- <block cond="$val->type=='title'"> -->
				<?php if($mi->show_cate == ' ' && $mi->use_category=='Y'): ?> <!-- cond="!$mi->show_cate && $mi->use_category=='Y'"  -->
					<td class="cate" <?php echo $s_category_color?> ><?php echo $s_category_title ?></td>
					<!-- <td cond="$mi->show_cate== ' ' && $mi->use_category=='Y'" class="cate"><span style="color:{$category_list[$post->get('category_srl')]->color}"|cond="$category_list[$post->get('category_srl')]->color!='transparent'">{$category_list[$post->get('category_srl')]->title}</span></td> -->
				<?php endif ?>
					<td class="title">
						<!--// 제목 -->
				<?php if(!$mi->preview || (!in_array('tx',$mi->preview) && !$post->check_thumbnail())): ?> <!--@if(!$mi->preview || (@!in_array('tx',$mi->preview) && !$post->check_thumbnail()))-->
						<a <?php if($mi->link_board == ' '):?> href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id,'listStyle',$listStyle,'cpage','')?>"<?php else: ?> href="<?php echo $post->get_user_define_eid_value('link_url')?>" target="_blank" <?php endif ?> class="hx" data-viewer="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id,'listStyle','viewer','page','')?>">
							<?php echo $post->get_title($mi->subject_cut_size)?>
						</a>
				<?php else: ?><!--@else-->
						<a <?php if($mi->link_board == ' '):?> href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id,'listStyle',$listStyle,'cpage','')?>"<?php else: ?> href="<?php echo $post->get_user_define_eid_value('link_url')?>" target="_blank"<?php endif ?> class="hx bubble no_bubble <?php if(!in_array('tx',$mi->preview)):?> only_img <?php endif ?>" data-viewer="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id,'listStyle','viewer','page','')?>">
							<?php echo $post->get_title($mi->subject_cut_size)?>
							<?php if(!$post->is_secret()):?><!-- cond="!$post->isSecret()"  -->
							<span class="wrp">
								<?php if($post->check_thumbnail() && in_array('img',$mi->preview)):?><!-- cond="$post->check_thumbnail() && @in_array('img',$mi->preview)"  -->
								<img src="<?php echo $post->get_thumbnail($mi->thumbnail_width,$mi->thumbnail_height,$mi->thumbnail_type)?>" alt="" />
								<?php endif ?>
								<span class="speech"><?php echo htmlspecialchars($post->get_summary($mi->preview_tx))?></span><i class="edge"></i>
								<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
							</span>
							<?php endif ?>
						</a>
				<?php endif ?><!--@end-->
				<?php if($mi->link_board == ' '): ?><!-- <block cond="$mi->link_board == ' '"> -->
					<?php if($post->get_comment_count()): ?><!-- cond="$post->get_comment_count()"  -->
						<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id', $post->post_id)?>#<?php echo $post->post_id?>_comment" class="replyNum" title="<?php echo __('lbl_comment', X2B_DOMAIN)?>"><?php echo $post->get_comment_count()?></a>
					<?php endif ?>
				<?php endif ?><!-- </block> -->
						<span class="extraimages"><?php echo $post->print_extra_images($mi->duration_new)?>
						<?php if($post->check_thumbnail()): ?><!-- cond="$post->check_thumbnail()" -->
						<i class="attached_image" title="Image"></i>
						<?php endif ?>
						</span>
						<?php if($mi->link_board != ' ' && $post->is_editable()): ?><!-- cond="$mi->link_board && $post->isEditable()"  -->
						<a class="link_modify" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_POST, 'post_id',$post->post_id)?>"><?php echo __('cmd_modify', X2B_DOMAIN)?></a>
						<?php endif ?>
					</td>
			<?php endif ?><!-- </block> -->
			<?php if($val->var_type=='nick_name'): ?> <!-- cond="$val->type=='nick_name'" -->
				<td class="author" ><span><a href="#popup_menu_area" class="member_<?php echo $post->get('post_author')?>" onclick="return false"><?php echo $post->get_nick_name()?></a></span></td>
			<?php endif ?>
			<?php if($val->var_type=='regdate_dt'): ?> <!-- cond="$val->type=='regdate'"  -->
				<td class="time" title="<?php echo x2b_get_time_gap($post->get('regdate'), "H:i")?>"><?php echo $post->get_regdate('Y.m.d')?></td>
			<?php endif ?>
			<?php if($val->var_type=='last_update_dt'): ?> <!-- cond="$val->type=='last_update'" -->
				<td class="time last_update m_no"><?php echo x2b_zdate($post->get('last_update_dt'),'Y.m.d')?></td>
			<?php endif ?>
			<!-- <td class="time last_post m_no" cond="$val->type=='last_post'">
				<block cond="(int)($post->get('comment_count'))>0">
					<a href="<?php //echo x2b_get_url('post_id',$post->post_id)?>#{$post->post_id}_comment" title="{getTimeGap($post->get('last_update'), "H:i")}">{zdate($post->get('last_update'),'Y.m.d')}<small cond="$post->getLastUpdater()">(by {$post->getLastUpdater()})</small></a>
				</block>
				<block cond="(int)($post->get('comment_count'))==0">&nbsp;</block>
			</td> -->
			<?php if($val->var_type=='readed_count'): ?><!-- cond="$val->type=='readed_count'"  -->
				<td class="m_no"><?php echo $post->get('readed_count')>0?$post->get('readed_count'):'0'?></td>
			<?php endif ?>
			<?php if($val->var_type=='voted_count'): ?><!-- cond="$val->type=='voted_count'"  -->
				<td class="m_no"><?php echo $post->get('voted_count')!=0?$post->get('voted_count'):'0'?></td>
			<?php endif ?>
			<?php if($val->idx!=-1): ?><!-- cond="$val->idx!=-1"  -->
			<td class="<?php if($val->eid=='link_url'):?>link_url<?php else: ?>m_no<?php endif?>">
				<?php if($val->eid=='rating'):?>
					<span class="starRating" title="<?php echo $post->get_user_define_value_HTML($val->idx)?><?php echo __('lbl_score', X2B_DOMAIN) ?>"><span style="width:<?php echo $post->get_user_define_value_HTML($val->idx)*10?>%"><?php echo $post->get_user_define_value_HTML($val->idx)?></span></span>
				<?php else:?><!--@else-->
					<?php echo $post->get_user_define_value_HTML($val->idx)?>
				<?php endif?><!--@end-->
			</td>
			<?php endif ?>
		<?php endforeach ?>	<!-- </block> -->
		<?php if($grant->manager): ?>	<!-- cond="$grant->manager"  -->
			<td class="check m_no"><input type="checkbox" name="cart" value="<?php echo $post->post_id?>" class="iCheck" title="Check This Article" onclick="doAddPostCart(this)" <?php if($post->is_carted()): ?>checked="checked" <?php endif?> /></td>
		<?php endif ?>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>