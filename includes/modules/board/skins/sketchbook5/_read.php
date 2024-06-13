<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

$mi->tmb_effect='N';
if(!$mi->rd_tl_font) $mi->rd_tl_font='ngeb';
if(!$mi->rd_top_font) $mi->rd_top_font='ngeb';
if(!$mi->rd_btm_font) $mi->rd_btm_font='ngeb';
if(!$mi->prev_next_cut_size) $mi->prev_next_cut_size=60;
$sns_link=$post->get_permanent_url().'?l='.$lang_type;
$sns_title=urlencode($post->get_title());

$s_category_color = null;
$s_category_title = null;
if(isset($category_list[$post->get('category_id')])) {
	if($category_list[$post->get('category_id')]->color!='transparent'){
		$s_category_color = 'style=color:'.$category_list[$post->get('category_id')]->color;
	}
	$s_category_title = $category_list[$post->get('category_id')]->title;
}
?>

<div class="rd<?php if($mi->rd_nav_style == ' '): ?> rd_nav_style2<?php endif?><?php if($mi->default_style=='blog'): ?> rd_blog <?php echo $mi->blog_style ?><?php endif ?> clear" style="padding:<?php echo $mi->rd_padding?>;" data-docSrl="<?php echo $post->post_id?>">
	<!--// Header -->
	<div class="rd_hd clear" style="<?php if( $mi->rd_style == ' '):?>margin:0 -15px 20px<?php endif?><?php if($mi->rd_padding):?>;margin-left:0;margin-right:0<?php endif?>">
		<!--// 제목 : 게시판 스타일 -->
		<?php if($mi->rd_style == ' '): ?><!-- cond="!$mi->rd_style"  -->
		<div class="board clear <?php echo $mi->rd_board_style?>" style="<?php echo $mi->rd_css?>;">
			<div class="top_area <?php echo $mi->rd_tl_font?>" style="<?php echo $mi->rd_tl_css?>;">
				<?php if( $mi->rd_cate == ' ' && $mi->use_category=='Y' && $post->get('category_id')): ?><!-- cond="!$mi->rd_cate && $mi->use_category=='Y' && $oDocument->get('category_id')" -->
				<strong class="cate fl" title="Category"><?php echo $category_list[$post->get('category_id')]->title?></strong>
				<?php endif ?>
				<div class="fr">
					<?php if($mi->rd_nick=='2'): ?><!-- cond="$mi->rd_nick=='2'"  -->
					<a href="#popup_menu_area" class="nick member_<?php echo $post->get('post_author')?>" onclick="return false"><?php echo $post->get_nick_name()?></a>
					<?php endif ?>
					<?php if( $mi->rd_date == ' '): ?><!-- cond="!$mi->rd_date"  -->
					<span class="date m_no"><?php echo $post->get_regdate('Y.m.d H:i')?></span>
					<?php endif ?>
				</div>
				<h1 class="np_18px"><a href="<?php echo $post->get_permanent_url()?>"><?php echo $post->get_title()?></a></h1>
			</div>
			<div class="btm_area clear">
				<?php if($mi->rd_profile=='Y') {
					echo $post->get_profile_image();
				}?>
				<!-- <img cond="$mi->rd_profile=='Y' && $post->get_profile_image()" class="img fl" src="<?php // echo $post->get_profile_image()?>" alt="profile" title="<?php // echo $post->get_nick_name()?>" /> -->
				<div class="side">
					<?php if($mi->rd_nick == ' '): ?><!-- cond="!$mi->rd_nick"  -->
						<a href="#popup_menu_area" class="nick member_<?php echo $post->get('post_author')?>" onclick="return false"><?php echo $post->get_nick_name()?></a>
					<?php endif ?>
					<?php if($mi->rd_link=='2'): ?><!-- cond="$mi->rd_link=='2'"  -->
						<a class="link m_no" href="<?php echo $post->get_permanent_url()?>"><?php echo urldecode($post->get_permanent_url())?></a>
					<?php endif ?>
					<?php if($mi->rd_date=='2'): ?><!-- cond="$mi->rd_date=='2'" -->
						<span class="date"><?php echo $post->get_regdate('Y.m.d H:i')?></span>
					<?php endif ?>						
					<?php if($mi->rd_cate=='2' && $mi->use_category=='Y' && $s_category_title): ?><!-- cond="$mi->rd_cate=='2' && $mi->use_category=='Y' && $oDocument->get('category_id')"  -->
						<strong class="cate" title="Category"><?php echo $s_category_title ?></strong>
					<?php endif ?>
					<?php if($mi->rd_info=='2'): ?><!-- <block cond="$mi->rd_info=='2'"> -->
						<?php if($mi->rd_view==' '): ?><!-- cond="!$mi->rd_view" -->
							<span><?php echo __('lbl_readed_count', X2B_DOMAIN)?> <b><?php echo $post->get('readed_count')?></b></span>
						<?php endif ?>
						<?php if($mi->rd_vote==' '): ?>  <!-- cond="!$mi->rd_vote" -->
							<span><?php echo __('lbl_voted_count', X2B_DOMAIN)?> <b><?php echo $post->get('voted_count')?></b></span>
						<?php endif ?>
						<?php if($mi->rd_cmt==' '): ?>	<!-- cond="!$mi->rd_cmt" -->
							<span><?php echo __('lbl_comment', X2B_DOMAIN)?> <b><?php echo $post->get_comment_count()?></b></span>
						<?php endif ?>
					<?php endif ?><!-- </block> -->
				</div>
				<div class="side fr">
					<?php if($grant->manager || $mi->display_ip_address): ?><!-- cond="$grant->manager || $mi->display_ip_address"  -->
						<small class="m_no">(<?php echo $post->get_ip_addr()?>) </small>
					<?php endif ?>
					<?php if($mi->rd_link=='3'): ?><!-- cond="$mi->rd_link=='3'"  -->
						<a class="link m_no" href="<?php echo $post->get_permanent_url()?>"><?php echo urldecode($post->get_permanent_url())?></a>
					<?php endif ?>
					<?php if($mi->rd_nick=='3'): ?>  <!-- cond="$mi->rd_nick=='3'"  -->
						<a href="#popup_menu_area" class="nick member_<?php echo $post->get('post_author')?>" onclick="return false"><?php echo $post->get_nick_name()?></a>
					<?php endif ?>
					<?php if($mi->rd_date=='3'): ?> <!-- cond="$mi->rd_date=='3'"  -->
						<span class="date"><?php echo $post->get_regdate('Y.m.d H:i')?></span>
					<?php endif ?>
					<?php if($mi->rd_cate=='3' && $mi->use_category=='Y' && $post->get('category_id')): ?> <!-- cond="$mi->rd_cate=='3' && $mi->use_category=='Y' && $oDocument->get('category_id')" -->
						<strong  class="cate" title="Category"><?php echo $s_category_title?></strong>
					<?php endif ?>
					<?php if($mi->rd_info==' '): ?><!-- <block cond="!$mi->rd_info"> -->
						<?php if($mi->rd_view==' '): ?><!-- cond="!$mi->rd_view" -->
							<span><?php echo __('lbl_readed_count', X2B_DOMAIN)?> <b><?php echo $post->get('readed_count')?></b></span>
						<?php endif ?>
						<?php if($mi->rd_vote==' '): ?>  <!-- cond="!$mi->rd_vote" -->
							<span><?php echo __('lbl_voted_count', X2B_DOMAIN)?> <b><?php echo $post->get('voted_count')?></b></span>
						<?php endif ?>
						<?php if($mi->rd_cmt==' '): ?>	<!-- cond="!$mi->rd_cmt" -->
							<span><?php echo __('lbl_comment', X2B_DOMAIN)?> <b><?php echo $post->get_comment_count()?></b></span>
						<?php endif ?>
					<?php endif ?><!-- </block> -->
				</div>
				<!--// 제목 밑 커스텀 위젯 등 영역 -->
				<!-- {$mi->rd_hd_widget} -->
			</div>
		</div>
		<?php endif ?>
		<!--// 제목 : 블로그 스타일 -->
		<?php if($mi->rd_style=='blog'): ?><!-- cond="$mi->rd_style=='blog'"  -->
			<div class="blog v<?php echo $mi->rd_blog_style?>" style="text-align:<?php echo $mi->rd_tl ?>;<?php echo $mi->rd_css ?>;">
				<div class="top_area <?php echo $mi->rd_top_font?> np_18px" style="text-align:<?php echo $mi->rd_top?>">
					<?php if(!$mi->rd_blog_cate && $mi->use_category=='Y' && $post->get('category_id')): ?><!-- cond="!$mi->rd_blog_cate && $mi->use_category=='Y' && $post->get('category_id')"  -->
						<span title="Category">
							<b class="cate"><strong <?php echo $s_category_color?>><?php echo $s_category_title?></strong></b>
						</span>
					<?php endif ?>
					<?php if($mi->rd_style=='blog'): ?><!-- cond="$mi->rd_blog_nick=='2'" -->
						<span><small>by</small><b><?php echo $post->get_nick_name()?></b></span>
					<?php endif ?>
					<?php if($mi->rd_style=='blog'): ?><!-- cond="$mi->rd_blog_date=='2'" -->
						<span title="<?php echo $post->get_regdate('Y.m.d H:i')?>"><small>posted</small><b class="date"><?php echo $post->get_regdate('M d, Y')?></b></span>
					<?php endif ?>
				</div>
				<?php if($mi->rd_tl!='N'): ?><!-- cond="$mi->rd_tl!='N'"  -->
					<h1 class="font <?php echo $mi->rd_tl_font ?>" style="<?php echo $mi->rd_tl_css ?>;-webkit-animation-name:rd_h1_v<?php echo $mi->rd_h1_ani?>;-moz-animation-name:rd_h1_v<?php echo $mi->rd_h1_ani?>;animation-name:rd_h1_v<?php echo $mi->rd_h1_ani?>;"><?php echo $post->get_title()?></h1>
				<?php endif ?>
				<?php if( $mi->rd_preview == ' ' && $post->get_user_define_eid_value('rd_preview')): ?><!-- cond="!$mi->rd_preview && $oDocument->getExtraEidValue('rd_preview')" -->
					<div class="rd_preview"><?php echo $post->get_user_define_eid_value('rd_preview')?></div>
					<div class="btm_area <?php echo $mi->rd_btm_font ?> np_18px" style="text-align:<?php echo $mi->rd_btm?>"> 
						<?php if($mi->rd_blog_cate=='2' && $mi->use_category=='Y' && $post->get('category_id')): ?><!-- cond="$mi->rd_blog_cate=='2' && $mi->use_category=='Y' && $oDocument->get('category_id')" -->
							<span><small>In </small><b title="Category"><?php echo $s_category_title?></b></span>
						<?php endif ?>
						<?php if($mi->rd_blog_nick == ' '): ?><!-- cond="!$mi->rd_blog_nick" -->
							<span><small>by </small><b><?php echo $post->get_nick_name()?></b></span>
						<?php endif ?>
						<?php if($mi->rd_blog_date == ' '): ?><!-- cond="!$mi->rd_blog_date"  -->
							<span title="<?php echo $post->get_regdate('Y.m.d H:i')?>"><small>posted </small><b class="date"><?php echo $post->get_regdate('M d, Y')?></b></span>
						<?php endif ?>
						<?php if(in_array('view',$mi->rd_blog_itm)): ?><!-- cond="@in_array('view',$mi->rd_blog_itm)" -->
							<span><small>Views</small> <b><?php echo $post->get('readed_count')?></b></span>
						<?php endif ?>
						<?php if(in_array('like',$mi->rd_blog_itm)): ?><!-- cond="@in_array('like',$mi->rd_blog_itm)" -->
							<span><small>Likes</small> <b><?php echo $post->get('voted_count')?></b></span>
						<?php endif ?>
						<?php if(in_array('cmt',$mi->rd_blog_itm)): ?><!-- cond="@in_array('cmt',$mi->rd_blog_itm)" -->
							<span><small>Replies</small> <b><?php echo $post->get_comment_count()?></b></span>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
		<?php endif ?>
		<!--// Secret -->
		<?php if(!$post->is_secret() || $post->is_granted()): ?>
		<!--// Files : Header -->
			<?php if($post->has_uploaded_files() && $mi->show_files=='3'){  // cond="$post->hasUploadedFiles() && $mi->show_files=='3'"
				include $skin_path_abs.'_read_files.php';  // <include target="_read_files.html" />
			}?>
			<!--// Extra Var : Header -->
			<?php if($mi->et_var=='2' && $post->is_user_define_extended_vars_exists() && (!$post->is_secret() || $post->is_granted())): ?><!-- cond="$mi->et_var=='2' && $oDocument->isExtraVarsExists() && (!$oDocument->isSecret() || $oDocument->isGranted())" -->
				<table class="et_vars bd_tb">
					<caption class="blind">Extra Form</caption>
					<?php foreach( $post->get_user_define_extended_fields() as $_ => $val ):  // loop="$oDocument->getExtraVars() => $key,$val"
						if($val->getValueHTML() && $val->eid!='rd_preview'): ?><!-- cond="$val->getValueHTML() && $val->eid!='rd_preview'" -->
							<tr>						
								<th scope="row"><?php echo esc_html($val->name) ?></th>
								<?php if($val->eid!='rating'): ?><!-- cond="$val->eid!='rating'" -->
									<td><?php echo esc_html($val->getValueHTML())?></td>
								<?php endif ?>
								<?php if($val->eid=='rating'): ?><!-- cond="$val->eid=='rating'"  -->
									<td class="rating"><span class="starRating" title="<?php echo esc_html($val->getValueHTML())?><?php echo __('lbl_score', X2B_DOMAIN) ?>"><span style="width:<?php echo esc_html($val->getValueHTML())*10?>%"><?php echo esc_html($val->getValueHTML())?></span></span></td>
								<?php endif ?>
							</tr>
						<?php endif ?>
						

					<?php endforeach ?>
				</table>
			<?php endif ?>
			<!--// SNS small -->
			<?php if($mi->to_sns=='2'){  // cond="$mi->to_sns=='2'"
				include $skin_path_abs.'_read_sns.php'; // <include  target="_read_sns.html" />
			}
			// Read Navi 
			if($mi->rd_nav == ' '){  // cond="!$mi->rd_nav" 
				include $skin_path_abs.'_read_nav.php'; // <include target="_read_nav.html" />
			}?>
			<?php if($mi->rd_nav_side == ' '): ?> <!-- cond="!$mi->rd_nav_side"  -->
				<div class="rd_nav_side">
				<?php include $skin_path_abs.'_read_nav.php'; ?> <!-- <include target="_read_nav.html" /> -->
				</div>
			<?php endif ?>
		<?php endif ?>
	</div>

	<!--// Secret -->
	<?php if($post->is_secret() && !$post->is_granted()): ?> <!--@if($oDocument->isSecret() && !$oDocument->isGranted())-->
	<div class="rd_body">
		<form action="./" method="get" onsubmit="return procFilter(this, input_password)" class="secretMessage">
			<input type="hidden" name="board_id" value="<?php echo $board_id?>" />
			<input type="hidden" name="page" value="<?php echo $page?>" />
			<input type="hidden" name="post_id" value="<?php echo $post->post_id?>" />
			<h3>&quot;<?php echo __('msg_secret_post', X2B_DOMAIN)?>&quot;</h3>
			<span class="itx_wrp">
				<label for="cpw_<?php echo $post->post_id?>"><?php echo __('lbl_password', X2B_DOMAIN)?></label>
				<input type="password" name="password" id="cpw_<?php echo $post->post_id?>" class="itx" />
				<input class="bd_btn" type="submit" value="<?php echo __('cmd_submit', X2B_DOMAIN)?>" />
			</span>
		</form>
	</div>
	<?php else: ?> <!--@else-->
			<!--// Body -->
			<div class="rd_body clear">
				<!--// Extra Var -->
				<?php if($mi->et_var == ' ' && $post->is_user_define_extended_vars_exists() && (!$post->is_secret() || $post->is_granted())): ?><!-- cond="!$mi->et_var && $oDocument->is_user_define_extended_vars_exists() && (!$oDocument->isSecret() || $oDocument->isGranted())" -->
					<table class="et_vars bd_tb">
						<caption class="blind">Extra Form</caption>
						<?php $etIdx=1;
						foreach( $post->get_user_define_extended_fields() as $_ => $val ):?>	<!-- loop="$oDocument->getExtraVars() => $key,$val" -->
							<?php if($val->getValueHTML() && $val->eid!='rd_preview'): ?><!-- cond="$val->getValueHTML() && $val->eid!='rd_preview'" -->
								<tr class="bg<?php echo $etIdx%2 ?>">
									<th scope="row"><?php echo esc_html($val->name) ?></th>
									<?php if($val->eid!='rating'): ?><!-- cond="$val->eid!='rating'" -->
										<td><?php echo esc_html($val->getValueHTML()) ?></td>
									<?php endif ?>
									<?php if($val->eid=='rating'): ?><!-- cond="$val->eid=='rating'"  -->
										<td class="rating"><span class="starRating" title="<?php echo esc_html($val->getValueHTML()) ?><?php echo __('lbl_score', X2B_DOMAIN) ?>"><span style="width:<?php echo esc_html($val->getValueHTML())*10 ?>%"><?php echo esc_html($val->getValueHTML()) ?></span></span></td>
									<?php endif ?>
								</tr>
							<?php endif;
							$etIdx++;
						endforeach ?>
					</table>
				<?php endif ?>
				<!--// 본문에 이미지 없을 때 -->
				<?php if($mi->no_attached_img && !$post->check_thumbnail()): ?><!-- cond="$mi->no_attached_img && !$oDocument->thumbnailExists()"  -->
					<p style="margin-bottom:30px;text-align:center"><img src="<?php echo $mi->no_attached_img?>" alt="No Attached Image" /></p>
				<?php endif ?>
				<!--// 본문 -->
				<?php if($mi->img_insert=='2'): ?><!-- cond="$mi->img_insert=='2'"  -->
					<div class="x2b_content rd_gallery">
						<?php foreach( $post->getExtraVars() as $key => $val ): // <block loop="$post->get_uploaded_files()=>$key,$file"> 
							if(!$mi->img_insert2 ) {  // <block cond="!$mi->img_insert2">
								$ext=substr($file->source_filename, -4);
								$ext=strtolower($ext);
								$extImg=in_array($ext,array('.jpg','jpeg','.gif','.png'));
							} // </block>
							if($mi->img_insert2) {  // <block cond="$mi->img_insert2">
								$ext=substr($file->source_filename, -15);
								$ext=strtolower($ext);
								$extImg=in_array($ext,array('_rd_gallery.jpg','rd_gallery.jpeg','_rd_gallery.gif','_rd_gallery.png'));
							} // </block>
							?>
							<img cond="$extImg" src="<?php echo $file->uploaded_filename?>" alt="" />
						<?php endforeach ?><!-- </block> -->
					</div>
				<?php endif ?>

				<article><?php echo $post->get_content(false) ?></article>

				<?php if($mi->img_insert=='3'): ?><!-- cond="$mi->img_insert=='3'" -->
					<div class="x2b_content rd_gallery">
						<?php foreach( $post->get_uploaded_files() as $key => $file ): // <block loop="$post->get_uploaded_files()=>$key,$file">
							if(!$mi->img_insert2 ) {  // <block cond="!$mi->img_insert2">
								$ext=substr($file->source_filename, -4);
								$ext=strtolower($ext);
								$extImg=in_array($ext,array('.jpg','jpeg','.gif','.png'));
							} // </block>
							if($mi->img_insert2) {  // <block cond="$mi->img_insert2">
								$ext=substr($file->source_filename, -15);
								$ext=strtolower($ext);
								$extImg=in_array($ext,array('_rd_gallery.jpg','rd_gallery.jpeg','_rd_gallery.gif','_rd_gallery.png'));
							} // </block>
							?>
							<img cond="$extImg" src="<?php echo $file->uploaded_filename ?>" alt="" />
						<?php endforeach ?>  <!-- </block> -->
					</div>
				<?php endif ?>
				<!--// Tag -->
				<?php 
				$tag_list=(array)$post->get('tag_list'); 
				if(count($tag_list) ): ?>  <!-- cond="count($tag_list)"  -->
					<div class="rd_t_f rd_tag css3pie clear">
						<div class="bg_f_color border_color">TAG &bull;</div>
						<ul>
							<?php for($i=0;$i<count($tag_list);$i++):?>
								<?php $tag=$tag_list[$i]; ?>
								<li><a href="<?php echo x2b_get_url('search_target','tag','search_keyword',$tag,'post_id','')?>"><?php echo htmlspecialchars($tag)?></a><span class="comma">,</span></li>
							<?php endfor ?><!--@end-->
						</ul>
					</div>
				<?php endif ?>
			</div>

			<!--// Footer -->
			<div class="rd_ft">
				<!--// Sign -->
				<?php if($mi->display_sign=='Y'): ?><!-- cond="$mi->display_sign!='N' && ($post->get_profile_image())"  -->
				<div class="rd_sign clear">
					<h4><em class="fa fa-info-circle bd_info_icon"></em> Who's <em><?php echo $post->get_nick_name()?></em></h4>
					<!-- <img cond="$post->get_profile_image()" class="img fl" src="{$post->get_profile_image()}" alt="profile" /> -->
					<?php echo $post->get_profile_image(); ?>
					<!-- <div cond="$post->getSignature()" class="get_sign">{$post->getSignature()}</div> -->
				</div>
				<?php endif ?>
				<!--// Prev-Next -->
		<!--// 현재목록 외 이전글-다음글 구하기 
		Source form : http://www.xpressengine.com/21617245 by 시니시즘. Thanks!
		1. search : X
		2. get image : X
		3. except_notice : /
		-->
				<?php if($mi->prev_next!='N' && !$post->is_notice() && $mi->default_style!='blog'): ?><!-- cond="$mi->prev_next!='N' && !$post->isNotice() && $mi->default_style!='blog'"  -->
					<div class="bd_prev_next clear" <?php if($mi->prev_next=='2'):?>style="display:none"<?php endif?> >
<?php
// prev-next page 
$cur_post_pos_in_list = -1000;  // sentinel
foreach( $post_list as $no => $o_post ) {
	if( $post_id == $o_post->post_id ) {
		$cur_post_pos_in_list = $no;
		break;
	}
}
?>
						<div <?php if($mi->default_style=='viewer'):?> style="max-width:<?php echo $mi->viewer_width?>px" <?php endif ?>>
							<?php if(isset($post_list[$cur_post_pos_in_list+1])):?><!-- cond="$post_list[$no+1]->post_id"  -->
								<a class="bd_rd_prev bubble no_bubble fl<?php if($mi->default_style=='viewer'):?> right<?php endif ?>" href="<?php echo x2b_get_url('post_id',$post_list[$cur_post_pos_in_list+1]->post_id)?>">
									<?php if($mi->prev_next == ' '): ?> <!-- cond="!$mi->prev_next"  -->
										<span class="p"><em class="link"><i class="fa fa-angle-left"></i> Prev</em> <?php echo $post_list[$cur_post_pos_in_list+1]->get_title($mi->prev_next_cut_size)?></span>
									<?php endif ?>
									<i class="fa fa-angle-left"></i>
									<span class="wrp prev_next">
										<span class="speech">
											<?php if($mi->prev_next == ' '): ?><!-- cond="$post_list[$no+1]->thumbnailExists()"  -->
											<img src="<?php echo $post_list[$cur_post_pos_in_list+1]->get_thumbnail($mi->thumbnail_width,$mi->thumbnail_height,$mi->thumbnail_type)?>" alt="" />
											<?php endif ?>
											<b><?php echo $post_list[$cur_post_pos_in_list+1]->get_title($mi->prev_next_cut_size)?></b>
											<span><em><?php echo $post_list[$cur_post_pos_in_list+1]->get_regdate('Y.m.d')?></em><small>by </small><?php echo $post_list[$cur_post_pos_in_list+1]->get_nick_name()?></span>
										</span><i class="edge"></i>
										<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
									</span>
								</a>
							<?php endif ?>
							<?php if(isset($post_list[$cur_post_pos_in_list-1])):?><!-- cond="$post_list[$no-1]->post_id"  -->
								<a class="bd_rd_next bubble no_bubble fr<?php if($mi->default_style=='viewer'):?> left<?php endif ?>" href="{getUrl('post_id',$post_list[$cur_post_pos_in_list-1]->post_id)}">
									<?php if($mi->prev_next == ' '):?><!-- cond="!$mi->prev_next"  -->
										<span class="p"><?php echo $post_list[$cur_post_pos_in_list-1]->get_title($mi->prev_next_cut_size)?> <em class="link">Next <i class="fa fa-angle-right"></i></em></span>
									<?php endif ?>
									<i class="fa fa-angle-right"></i>
									<span class="wrp prev_next">
										<span class="speech">
											<?php if($post_list[$cur_post_pos_in_list-1]->check_thumbnail()):?><!-- cond="$post_list[$no-1]->thumbnailExists()"  -->
												<img src="<?php echo $post_list[$cur_post_pos_in_list-1]->get_thumbnail($mi->thumbnail_width,$mi->thumbnail_height,$mi->thumbnail_type)?>" alt="" />
											<?php endif ?>
											<b><?php echo $post_list[$cur_post_pos_in_list-1]->get_title($mi->prev_next_cut_size) ?></b>
											<span><em><?php echo $post_list[$cur_post_pos_in_list-1]->get_regdate('Y.m.d') ?></em><small>by </small><?php echo $post_list[$cur_post_pos_in_list-1]->get_nick_name() ?></span>
										</span><i class="edge"></i>
										<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
									</span>
								</a>
							<?php endif ?>
						</div>
					</div>
				<?php endif ?>

		<!--// Vote -->
		<?php if($mi->votes!='N') :?><!-- cond="$mi->votes!='N'"  -->
			<div class="rd_vote">
				<a class="bd_login" href="#" onclick="doCallModuleAction('post','procDocumentVoteUp','{$post->post_id}');return false;"|cond="$is_logged" style="border:2px solid #<?php echo $mi->color ?>;color:#<?php echo $mi->color?>;">
					<b><i class="fa fa-heart"></i> <?php echo $post->get('voted_count')?></b>
					<p><?php echo __('cmd_vote', X2B_DOMAIN)?></p>
				</a>
				<?php if( $mi->votes == ' '): ?><!-- cond="!$mi->votes"  -->
					<a class="blamed bd_login" href="#" onclick="doCallModuleAction('post','procDocumentVoteDown','{$post->post_id}');return false;"|cond="$is_logged">
						<b><i class="fa fa-heart"></i> <?php echo $post->get('blamed_count')?></b>
						<p><?php echo __('cmd_vote_down', X2B_DOMAIN)?></p>
					</a>
				<?php endif ?>
				<?php if( $mi->declare == 'Y'): ?><!-- cond="$mi->declare"  -->
					<a class="blamed declare bd_login" href="#" onclick="doCallModuleAction('document','procDocumentDeclare','{$post->post_id}');return false;"|cond="$is_logged">
						<b><i class="fa fa-phone"></i></b>
						<p><?php echo __('cmd_declare', X2B_DOMAIN)?></p>
					</a>
				<?php endif ?>
			</div>
		<?php endif ?>
		<!--// SNS -->
		<?php if($mi->to_sns=='3') :?><!-- cond="$mi->to_sns=='3'"  -->
			<div class="to_sns big" style="text-align:<?php echo $mi->to_sns_big?>" data-url="<?php echo $sns_link?>" data-title="<?php echo $sns_title?>">
				<a class="facebook bubble" href="#" data-type="facebook" title="To Facebook"><b class="ico_sns facebook">Facebook</b></a>
				<a class="twitter bubble" href="#" data-type="twitter" title="To Twitter"><b class="ico_sns twitter">Twitter</b></a>
				<a class="google bubble" href="#" data-type="google" title="To Google"><b class="ico_sns google">Google</b></a>
				<a class="pinterest bubble" href="#" data-type="pinterest" title="To Pinterest"><b class="ico_sns pinterest">Pinterest</b></a>
				<?php if(wp_is_mobile()):?><!-- <block cond="Mobile::isMobileCheckByAgent()"> -->
					<a class="line bubble" href="line://msg/text/?<?php echo $sns_title ?>%0D%0A<?php echo $sns_link?>" title="To Line"><b class="ico_sns line">Line</b></a>
					<a class="kakao bubble" href="kakaolink://sendurl?msg=<?php echo $sns_title ?>&url=<?php echo $sns_link?>&appid=m.kakao.com&appver=2.0&appname=카카오" title="To Kakao Talk"><b class="ico_sns kakao">Kakao</b></a>
				<?php endif?><!-- </block> -->
			</div>
		<?php endif ?>
		<?php if($mi->to_sns=='4' && $mi->to_sns_content) :?><!-- cond="$mi->to_sns=='4' && $mi->to_sns_content"  -->
			<div class="to_sns small clear"><?php echo $mi->to_sns_content ?></div>
		<?php endif ?>
		<!--// Files -->
		<?php if($post->has_uploaded_files() && ( $mi->show_files == ' ' || $mi->show_files==2)) {	// cond="$post->hasUploadedFiles() && (!$mi->show_files || $mi->show_files==2)" 
			include $skin_path_abs.'_read_files.php';   // <include target="_read_files.html" />
		}?>
		<!--// Read Footer Navi -->
		<div class="rd_ft_nav clear">
			<?php if($mi->default_style!='viewer' && $mi->rd_ft_nav) :?><!-- cond="$mi->default_style!='viewer' && $mi->rd_ft_nav"  -->
				<a class="btn_img fl" href="{getUrl('post_id','')}"><i class="fa fa-bars"></i> <?php echo __('cmd_list', X2B_DOMAIN)?></a>
			<?php endif ?>
			<!--// SNS small -->
			<?php if($mi->to_sns == ' ') {	//  cond="!$mi->to_sns" 
				include $skin_path_abs.'_read_sns.php';  // <include target="_read_sns.html" />
			}?>
			<!--// Read Nav -->
			<?php 
			$ft_read_nav=1;
			include $skin_path_abs.'_read_nav.php';  // <include target="_read_nav.html" /> 
			$ft_read_nav=''; ?>
		</div>
	</div>

	<!--// Comment -->
		<?php if($mi->cmt_wrt=='sns') { // <block cond="$mi->cmt_wrt=='sns'">
			$mi->cmt_wrt_position='';
			$mi->profile_img='';
		}?>	<!-- </block> -->
		<!-- cond="!$mi->viewer_cmt"  always true -->
		<div class="fdb_lst_wrp <?php echo $mi->fdb_style?> <?php echo $mi->profile_img?>">
			<div id="<?php echo $post->post_id?>_comment" class="fdb_lst clear <?php echo $mi->fdb_nav?> <?php echo $mi->cmt_wrt_position?>">
				<!--// Editor -->
				<?php if(false): //$mi->cmt_wrt=='sns'):?> <!--@if($mi->cmt_wrt=='sns')-->
					<!--// SocialXE -->
					<?php if($post->allow_comment() && $mi->select_editor!='N'):?> <!-- cond="$post->allowComment() && $mi->select_editor!='N'"  -->
						<div class="editor_select bubble fr m_no" title="<?php echo __('desc_noti_rfsh', X2B_DOMAIN) ?>">
							<a class="tg_btn2" href="#" data-href="#editor_select"><em class="fa fa-info-circle bd_info_icon"></em> <?php echo __('cmd_select_editor', X2B_DOMAIN) ?></a>
							<?php if($rd_idx == 0):?> <!-- cond="$rd_idx==0"  -->
								<div id="editor_select" class="editor_select_cnt tg_cnt2 wrp"><button type="button" class="tg_blur2"></button>
									<!-- |cond="$mi->cmt_wrt=='simple'" -->
									<a <?php if($mi->cmt_wrt=='simple'):?> class="on" <?php endif?> href="#" onclick="jQuery.cookie('bd_editor','simple');location.reload();return false"><em>✔ </em><?php echo __('lbl_textarea_editor_mode', X2B_DOMAIN) ?></a>
									<!-- |cond="$mi->cmt_wrt=='editor'"  -->
									<a <?php if($mi->cmt_wrt=='editor'):?> class="on" <?php endif?> href="#" onclick="jQuery.cookie('bd_editor','editor');location.reload();return false"><em>✔ </em><?php echo __('lbl_wysiwyg_editor_mode', X2B_DOMAIN) ?></a>
									<!-- |cond="$mi->cmt_wrt=='sns'" -->
									<a <?php if($mi->cmt_wrt=='sns'):?> class="on" <?php endif?> href="#" onclick="jQuery.cookie('bd_editor','sns');location.reload();return false"><em>✔ </em><?php echo __('lbl_sxc_editor_mode', X2B_DOMAIN) ?></a>
									<i class="edge"></i><button type="button" class="tg_blur2"></button>
									<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
								</div>
							<?php endif?>
						</div>
					<?php endif?>
					<img class="zbxe_widget_output" widget="socialxe_comment" skin="sketchbook5" colorset="<?php echo $mi->colorset ?>" post_id="<?php echo $post->post_id ?>" content_link="<?php echo x2b_get_url('','post_id',$post->post_id,'dummy','1')?>" content_title="<?php echo htmlspecialchars($post->get_title_text()) ?>" enter_send="N" <?php if($mi->auto_view_sub == ' '):?> auto_view_sub="Y" <?php endif ?> style="overflow:visible" />
				<?php else:?><!--@else-->
					<!--// Comment Write : Top -->
					<?php if($post->allow_comment() && !$mi->cmt_wrt_position){ //  cond="$post->allowComment() && !$mi->cmt_wrt_position"
						include $skin_path_abs.'_comment_write.php'; //  <include target="_comment_write.html" />
					} ?>
					<!--// Comment List -->
					<div id="cmtPosition" aria-live="polite">
						<?php include $skin_path_abs.'_comment.php'; ?>	 <!-- <include target="_comment.html" /> -->
					</div>
					<!--// Comment Write : Bottom -->
					<?php if($post->allow_comment() && $mi->cmt_wrt_position=='cmt_wrt_btm'){ //  cond="$post->allowComment() && $mi->cmt_wrt_position=='cmt_wrt_btm'"
						include $skin_path_abs.'_comment_write.php'; //  <include  target="_comment_write.html" />
					} ?>
				<?php endif?><!--@end-->
			</div>
		</div>
		<!--//End - Secret -->
	<?php endif ?><!--@end-->
</div>

<!--// 목록 보이지 않을 때 보이는 하단 메뉴 -->
<?php if($mi->rd_lst == 'N' && $mi->default_style!='blog'):?> <!-- cond="$mi->rd_lst && $mi->default_style!='blog'"  -->
	<div class="btm_mn clear" style="border-top:1px solid #CCC">
		<div class="fl">
			<a class="btn_img" href="<?php echo x2b_get_url('post_id','')?>"><i class="fa fa-bars"></i> <?php echo __('cmd_list', X2B_DOMAIN)?></a>
			<a class="btn_img back_to" href="#bd_<?php echo $board_id?>"><i class="fa fa-arrow-up"></i> <?php echo __('cmd_move_up', X2B_DOMAIN)?></a>
		</div>
		<div class="fr">
			<?php if($mi->prev_next=='2' && !$post->is_notice()):?> <!-- <block cond="$mi->prev_next=='2' && !$post->isNotice()"> -->
				<a class="btn_img no rd_prev bubble no_bubble" href="#"><?php echo __('cmd_prev', X2B_DOMAIN)?></a>
				<a class="btn_img no rd_next bubble no_bubble" href="#"><?php echo __('cmd_next', X2B_DOMAIN)?></a>
			<?php endif ?> <!-- </block> -->
			<?php if($mi->prev_next=='2' && !$post->is_notice()):?> <!-- cond="!$mi->write_btm_btn || ($mi->write_btm_btn!='N' && $grant->write_document)" -->
				<a class="btn_img" href="<?php echo x2b_get_url('act','dispBoardWrite','post_id','')?>"><b class="ico_16px write"></b> <?php echo __('cmd_write', X2B_DOMAIN)?></a>
			<?php endif ?>
		</div>
	</div>
<?php endif ?>
<hr id="rd_end_<?php echo $post->post_id ?>" class="rd_end clear" />