<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

include $skin_path_abs.'__setting.php';

$rd_idx = 0;  // for _comment_write.php

wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-1', $skin_url . '/js/imagesloaded.pkgd.min.js', [], X2B_VERSION, true);
wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-2', $skin_url . '/js/jquery.cookie.js', ['jquery'], X2B_VERSION, true);
// <load target="../../../editor/skins/xpresseditor/js/xe_textarea.min.js" type="body" />
wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-xe_textarea', X2B_URL . '/includes/' . X2B_MODULES_NAME . '/editor/skins/xpresseditor/js/xe_textarea.min.js', ['jquery'], X2B_VERSION, true);
wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-3', $skin_url . '/js/jquery.autogrowtextarea.min.js', ['jquery'], X2B_VERSION, true);
wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-4', $skin_url . '/js/board.js', [], X2B_VERSION, true);
?>

<script>//<![CDATA[
var lang_type = "<?php echo $lang_type?>";
var bdLogin = "<?php if(!$is_logged): ?><?php echo __('desc_bd_login', X2B_DOMAIN) ?>@<?php echo wp_login_url() ?><?php endif ?>";
jQuery(function(jQuery){
	board('#bd_<?php echo $board_id?>_<?php echo intval($post->get('post_id'))?>');
<?php if($mi->default_style!='viewer'): ?>
	jQuery.cookie('bd_viewer_font',jQuery('body').css('font-family'));
<?php endif ?>
});
const s_plugin_base_url = "<?php echo X2B_URL ?>";
//]]></script>

<?php
// var_dump($mi);
// 뷰어로 보기
if($mi->default_style=='viewer' && $grant->view) {
	include $skin_path_abs.'_viewer.php';
}

if($mi->default_style !='viewer'): 
	wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-3', $skin_url . '/js/jquery.masonry.min.js', ['jquery'], X2B_VERSION, true);
?>

<!--// 상단내용 -->
<?php if($mi->hd_tx == ' '):?>
<div><?php echo $mi->header_text?></div>
<?php endif ?>
<?php if($mi->hd_tx=='2' && $post->is_exists()):?>
<div><?php echo $mi->header_text?></div>
<?php endif ?>
<?php if($mi->hd_tx=='3' && !$post->is_exists()):?>
<div><?php echo $mi->header_text?></div>
<?php endif ?>

<div id="bd_<?php echo $board_id?>_<?php echo intval($post->get('post_id'))?>" class="bd <?php //echo $_COOKIE['use_np']?> <?php echo $mi->fdb_count?>
<?php if($mi->hover != ' ' ):?> hover_effect<?php endif ?> 
<?php if($mi->select_lst != ' ' && ($mi->cnb || $mi->use_category!='Y')): ?> small_lst_btn<?php echo $post->is_exists()?><?php endif ?>
<?php if($mi->select_lst_more): ?> select_lst_cloud<?php endif ?>" data-default_style="<?php echo $mi->default_style?>" 
	<?php if($mi->link_board):?> data-link_board="<?php echo $mi->link_board ?>" <?php endif ?> 
	<?php if($mi->bubble=='N'):?> data-bdBubble="N" <?php endif ?> 
	<?php if($mi->lst_viewer=='Y'):?> data-lstViewer="<?php echo __('cmd_with_viewer', X2B_DOMAIN) ?>" <?php endif ?>
	data-bdFilesType="<?php echo $mi->files_type ?>" 
	<?php if($mi->img_opt):?> data-bdImgOpt="Y" <?php endif ?>
	<?php if($mi->img_link != ' ' && wp_is_mobile()):?> data-bdImgLink="Y" <?php endif ?>
	<?php if($mi->rd_nav_side || $mi->default_style=='blog' || $mi->default_style=='guest'):?> data-bdNavSide="N" <?php endif ?>>

	<!-- <div class="bd hover_effect" > -->
	<!--// Wizard -->
	<?php if($grant->manager && $mi->wizard!='N' && !wp_is_mobile()){  // <block cond="$grant->manager && $mi->wizard!='N' && !Mobile::isMobileCheckByAgent()">
	// <load target="css/wizard.css" />
	wp_enqueue_style(X2B_DOMAIN.'-sketchbook5-wizard', $skin_url."/css/wizard.css", array(), X2B_VERSION, 'all');
	// <include target="_wizard.php" />
	}?>  <!-- </block> -->

	
	<!--// 헤더 -->
	<div class="bd_hd <?php if($post->is_exists()):?> v2<?php endif ?> clear">
		<!--// 폰트 버튼 -->
<?php if($mi->font_btn!='N'):?>
		<div class="bd_font m_no fr" <?php if($post->is_exists() || $mi->font_btn=='2'):?> style="display:none" <?php endif ?>>
			<!-- <load target="js/font_ng.js" type="body" /> -->
			<?php 
			wp_enqueue_script(X2B_DOMAIN.'-sketchbook5-7', $skin_url . '/js/font_ng.js', [], X2B_VERSION, true);
			?>
			<a class="select tg_btn2" href="#" data-href=".bd_font_select"><b>T</b><strong>
				<?php if($mi->font=='ng'):?>나눔고딕
				<?php elseif($mi->font=='window_font'):?><?php echo __('lbl_window_font', X2B_DOMAIN) ?>
				<?php elseif($mi->font=='tahoma'):?><?php echo __('lbl_tahoma', X2B_DOMAIN) ?>
				<?php else:?><?php echo __('lbl_best_font', X2B_DOMAIN) ?><?php endif ?>
			</strong><span class="arrow down"></span></a>
			<div class="bd_font_select tg_cnt2"><button type="button" class="tg_blur2"></button>
				<ul>
					<li class="ui_font<?php if($mi->font==' '):?> on<?php endif ?>"><a href="#" title="<?php echo __('about_best_font_dsc', X2B_DOMAIN) ?>"><?php echo __('lbl_best_font', X2B_DOMAIN) ?></a><em>✔</em></li>
					<li class="ng<?php if($mi->font=='ng'):?> on<?php endif ?>"><a href="#">나눔고딕</a><em>✔</em></li>
					<li class="window_font<?php if($mi->font=='window_font'):?> on<?php endif ?>"><a href="#"><?php echo __('lbl_window_font', X2B_DOMAIN) ?></a><em>✔</em></li>
					<li class="tahoma<?php if($mi->font=='tahoma'):?> on<?php endif ?>"><a href="#"><?php echo __('lbl_tahoma', X2B_DOMAIN) ?></a><em>✔</em></li>
				</ul><button type="button" class="tg_blur2"></button>
			</div>
		</div>
<?php endif ?>

		<!--// 우측 상단 설정 메뉴 -->
		<div class="bd_set fr m_btn_wrp m_no">
<?php if($grant->view && $mi->default_style!='guest' && $mi->default_style!='blog'):?>	<!-- <block cond="$grant->view && $mi->default_style!='guest' && $mi->default_style!='blog'"> -->
	<?php if($post->is_exists() && $mi->viewer == ' '):?>  <!-- cond="$oDocument->isExists() && !$mi->viewer" -->
			<a class="bg_f_f9" href="#" onclick="window.open('<?php echo x2b_get_url('listStyle','viewer','page','')?>','viewer','width=9999,height=9999,scrollbars=yes,resizable=yes,toolbars=no');return false"><em>✔</em> <strong><?php echo __('cmd_with_viewer', X2B_DOMAIN) ?></strong></a>
	<?php endif ?>
	<?php if($mi->link_board):?> <!--@if($mi->link_board)-->
		<?php if($mi->viewer_with!='N'):?> <!-- cond="$mi->viewer_with!='N'" -->
			<a class="viewer_with bubble<?php if($mi->viewer_with=='2' || $_COOKIE['cookie_viewer_with']=='Y'):?> on<?php endif ?>" href="#" title="<?php echo __('about_with_viewer_info', X2B_DOMAIN) ?>."><em>✔</em> <strong><?php echo __('cmd_link_site_viewer', X2B_DOMAIN) ?></strong></a>
		<?php endif ?>
	<?php else:?> <!--@else-->
		<?php if($post->is_exists() && $mi->viewer_with!='N'):?>  	<!-- cond="!$oDocument->isExists() && $mi->viewer_with!='N'" -->
			<a class="viewer_with rd_viewer bubble<?php if($mi->viewer_with=='2' || $_COOKIE['cookie_viewer_with']=='Y'):?> on<?php endif ?>" href="#" title="<?php echo __('about_with_viewer_info', X2B_DOMAIN) ?>."><em>✔</em> <strong><?php echo __('cmd_with_viewer', X2B_DOMAIN) ?></strong></a>
		<?php endif ?>
	<?php endif ?> <!--@end-->
<?php endif ?>	<!-- </block> -->
<?php if(!$post->is_exists()):?>	<!-- <block cond="!$oDocument->isExists()"> -->
	<?php if($mi->srch_window == ' '):?>	<!-- cond="!$mi->srch_window"  -->
			<a class="show_srch bubble" href="#" title="<?php echo __('about_search_info', X2B_DOMAIN) ?>"><b class="ico_16px search"></b><?php echo __('cmd_search', X2B_DOMAIN) ?></a>
	<?php endif ?>
	<?php if($mi->write_btn == ' ' || ($mi->write_btn!='N' && $grant->write_post)):?>	<!-- cond="!$mi->write_btn || ($mi->write_btn!='N' && $grant->write_document)"  -->
			<a href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_WRITE_POST, 'post_id', '', 'page', '')?>"><b class="ico_16px write"></b><?php echo __('cmd_write', X2B_DOMAIN)?></a>
	<?php endif ?>
	<?php if($mi->font_btn=='2'):?>	<!-- cond="$mi->font_btn=='2'"  -->
			<span class="font_select"><a class="select tg_btn2" href="#" data-href=".bd_font_select"><b class="tx_ico_chk">T</b><?php echo __('lbl_font', X2B_DOMAIN) ?><i class="arrow down"></i></a></span>
	<?php endif ?>
	<?php if($grant->manager):?>	<!-- <block cond="$grant->manager"> -->
			<a href="<?php echo admin_url('admin.php?page=x2b_disp_board_update&board_id='.$board_id);?>"><b class="ico_16px setup"></b><?php echo __('cmd_setup', X2B_DOMAIN)?></a>
			<a class="m_no" id='btn_manage_post_header'><b class="tx_ico_chk">✔</b><?php echo __('cmd_manage_post', X2B_DOMAIN) ?></a>
		<?php if($mi->default_style!='list'):?>	<!-- cond="$mi->default_style!='list'" -->
			<input type="checkbox" onclick="XE.checkboxToggleAll({ doClick:true });" class="iCheck" title="Check All" />
		<?php endif ?>			
	<?php endif ?>	<!-- </block> -->
<?php endif ?>	<!-- </block> -->
		</div>
<!--// 본문 내비 -->
<!-- <include cond="$oDocument->isExists() && $mi->rd_nav=='2'" target="_read_nav.html" /> -->
<?php if($post->is_exists() && $mi->rd_nav=='2') {
	include $skin_path_abs.'_read_nav.php';
}?>

<!--// 본문 -->
<!-- <include cond="$oDocument->isExists()" target="_read.html" /> -->
<?php if($post->is_exists()) {
	include $skin_path_abs.'_read.php';
}?>

<!--// 본문에서 목록 감추기 -->
<?php if(!$post->is_exists() || ($post->is_exists() && !$mi->rd_lst)): ?> <!-- cond="!$oDocument->isExists() || ($oDocument->isExists() && !$mi->rd_lst)"  -->
		<div class="bd_lst_wrp">
			<div class="tl_srch clear">
				<!--// 제목 -->
				<?php if($mi->title): ?><!-- cond="$mi->title" -->
				<div class="bd_tl">
					<h1 class="ngeb clear"><i class="bg_color"></i><a href="<?php echo x2b_get_url()?>"><?php echo $mi->title?></a></h1>
					<?php if($mi->sub_title): ?><!-- cond="$mi->sub_title"  -->
					<h2 class="clear"><i class="bg_color"></i><?php echo $mi->sub_title?></h2>
					<?php endif ?>
				</div>
				<?php endif ?>
				<?php if(isset($mi->title_img)): ?><!-- cond="$mi->title_img"  -->
				<div class="fl"><img src="<?php echo $mi->title_img?>" alt="Title" /></div>
				<?php endif ?>
				<!--// FAQ 검색창 -->
				<?php if($mi->srch_window!='N'): ?><!-- cond="$mi->srch_window!='N'"  -->
				<div class="bd_faq_srch<?php if($mi->srch_window==3):?> clear<?php endif ?>" <?php if($mi->srch_window==2 || $search_keyword):?>style="display:block"<?php endif ?>>
					<?php include $skin_path_abs.'_search.php'; ?> <!-- <include target="_search.html" /> -->
				</div>
				<?php endif ?>
			</div>
		</div>
<?php endif ?>

		<!--// 카테고리 -->
		<div class="cnb_n_list">
<?php if($mi->use_category=='Y' && $mi->cnb!='N'): ?>   <!-- cond="$mi->use_category=='Y' && $mi->cnb!='N'" -->
			<div <?php if($mi->select_lst=='N'): ?> class="if_lst_btn"<?php endif ?> <?php if($mi->default_style=='blog'): ?>style="margin-bottom:30px"<?php endif ?>>
<?php
$cate_list = array(); 
$current_key = null;
foreach($category_list as $key=>$val) {
	if(!$val->depth) {
		$cate_list[$key] = $val;
		$cate_list[$key]->children = array();
		$current_key = $key;
	}
	elseif($current_key) {
		$cate_list[$current_key]->children[] = $val;
	}
}?>
	<?php if($mi->cnb == ' '): ?>   <!-- cond="!$mi->cnb"  -->
				<div class="bd_cnb clear css3pie<?php if($mi->cnb_open):?> open<?php endif ?>">
					<a class="home" href="<?php echo x2b_get_url('category','','page','','post_id','')?>" title="<?php echo __('lbl_post_count', X2B_DOMAIN)?> '<?php echo number_format($total_count)?>'"><i class="home ico_16px">Category</i></a>
					<div class="dummy_ie fr"></div>
					<ul class="bubble bg_f_f9 css3pie">
						<li class="cnbMore"><a href="#" class="bubble" title="<?php echo __('lbl_category', X2B_DOMAIN)?> <?php echo __('lbl_more', X2B_DOMAIN) ?>"><i class="fa<?php if($mi->cnb_open):?> fa-caret-up<?php else:?> fa-caret-down<?php endif?>"></i></a></li>
		<?php foreach($cate_list as $key => $val): ?><!-- loop="$cate_list=>$key,$val"  -->
						<li <?php if($category==$val->category_id): ?> class="on"<?php endif?>>
							<a class="a1<?php if($category==$val->category_id):?> on<?php endif?>" href="<?php echo x2b_get_url('category',$val->title,'post_id','','page','')?>" title="<?php echo __('lbl_post_count', X2B_DOMAIN)?> <?php if(!$mi->cnb_count):?> <?php echo $val->post_count?> <?php endif?>" <?php if($val->color!='transparent'): ?>style="color:<?php echo $val->color?>" <?php endif?>><?php echo $val->title?> <?php if($mi->cnb_count): ?> <small>(<?php echo $val->post_count?>)</small><?php endif?></a>
			<?php if( count($val->children) ): ?>  <!-- cond="count($val->children)" -->
							<ul class="wrp">
				<?php foreach($val->children as $idx => $item): ?>	<!-- loop="$val->children=>$idx,$item" -->
								<li class="li2<?php if($category==$item->category_id):?> on<?php endif?>"><a href="<?php echo x2b_get_url('category',$item->title,'post_id','','page','')?>" title="<?php echo __('lbl_post_count', X2B_DOMAIN)?> <?php if(!$mi->cnb_count):?> <?php echo $item->post_count?> <?php endif?>"  <?php if($val->color!='transparent'): ?>style="color:<?php echo $val->color?>" <?php endif?>><?php echo $item->title?> <?php if($mi->cnb_count): ?> <small>(<?php echo $val->post_count?>)</small><?php endif?></a></li>
				<?php endforeach ?>
							</ul>
			<?php endif ?>
						</li>
		<?php endforeach ?>
					</ul>
				</div>
	<?php endif ?>
	<?php if($mi->cnb=='cTab'): ?>   <!-- cond="$mi->cnb=='cTab'"  -->
				<ul class="cTab clear">
					<li class="home<?php if(!$category): ?> on<?php endif?>"><a href="<?php echo x2b_get_url('category','','page','','post_id','')?>" title="<?php echo __('lbl_post_count', X2B_DOMAIN)?> <?php echo number_format($total_count)?>"><?php echo __('lbl_total', X2B_DOMAIN) ?><?php if($mi->cnb_count):?> <small>(<?php echo number_format($total_count)?>)</small> <?php endif?></a></li>
		<?php foreach($cate_list as $key => $val): ?>	<!-- loop="$cate_list=>$key,$val"  -->
					<li <?php if($category==$val->category_id): ?> class="on"<?php endif?>>
						<a href="<?php echo x2b_get_url('category',$val->category_id,'post_id','','page','')?>" <?php if($val->color!='transparent'):?> style="color:<?php echo $val->color?>" <?php endif?>><?php echo $val->title?> <?php if($mi->cnb_count):?><small>(<?php echo $val->post_count?>)</small><?php endif?></a>
			<?php if(count($val->children)):?>	<!-- cond="count($val->children) -->
						<ul>
				<?php foreach($val->children as $idx => $item): ?>  <!-- loop="$val->children=>$idx,$item" 	 -->
							<li <?php if($category==$val->category_id): ?> class="on"<?php endif?>><a href="<?php echo x2b_get_url('category',$item->category_id,'post_id','','page','')?>" <?php if($val->color!='transparent'):?> style="color:<?php echo $val->color?>" <?php endif?>><?php echo $item->title?></a></li>
				<?php endforeach ?>
						</ul>
			<?php endif ?>
					</li>
		<?php endforeach ?>
				</ul>
	<?php endif ?>
	<?php if($mi->cnb=='cnb3' || $mi->cnb=='cnb4'): ?>   <!-- cond="$mi->cnb=='cnb3' || $mi->cnb=='cnb4'" -->
				<ul class="cnb3 <?php echo $mi->cnb?> <?php echo $mi->cnb3_align?> clear">
					<li class="home<?php if(!$category):?> on<?php endif?>">
						<a href="<?php echo x2b_get_url('category','','page','','post_id','')?>" title="<?php echo __('lbl_post_count', X2B_DOMAIN)?> <?php echo number_format($total_count)?>"><?php echo __('lbl_total', X2B_DOMAIN) ?> <?php if($mi->cnb_count):?> <small>(<?php echo number_format($total_count)?>)</small><?php endif?></a></li>
		<?php foreach($cate_list as $key => $val): ?> <!-- loop="$cate_list=>$key,$val"  -->
							<li <?php if($category==$val->category_id): ?> class="on"<?php endif?>><a href="<?php echo x2b_get_url('category',$val->category_id,'post_id','','page','')?>" <?php if($val->color!='transparent'):?> style="color:<?php echo $val->color?>" <?php endif?>><?php echo $val->title?><?php if($mi->cnb_count):?><small>(<?php echo $val->post_count?>)</small><?php endif?></a>
		<?php endforeach ?>
					</li>
				</ul>
	<?php endif ?>
			</div>

			<!--// 게시판 유형 선택 -->
	<?php if($mi->select_lst == ' '): ?>  <!-- cond="!$mi->select_lst" -->
			<div class="lst_btn fr">
				<ul>
					<li class="classic<?php if($mi->default_style=='list'):?> on<?php endif ?>"><a class="bubble" href="<?php echo x2b_get_url('listStyle','list','act','','post_id','')?>" title="Text Style"><b>List</b></a></li>
					<li class="zine<?php if($mi->default_style=='webzine'):?> on<?php endif ?>"><a class="bubble" href="<?php echo x2b_get_url('listStyle','webzine','act','','post_id','')?>" title="Text + Image Style"><b>Zine</b></a></li>
					<li class="gall<?php if($mi->default_style=='gallery'):?> on<?php endif ?>"><a class="bubble" href="<?php echo x2b_get_url('listStyle','gallery','act','','post_id','')?>" title="Gallery Style"><b>Gallery</b></a></li>
		<?php if($mi->select_lst_more): ?> <!-- cond="$mi->select_lst_more"  -->
					<li class="cloud<?php if($mi->default_style=='cloud_gall'):?> on<?php endif ?>"><a class="bubble" href="<?php echo x2b_get_url('listStyle','cloud_gall','act','','post_id','')?>" title="Photo Cloud"><b>Cloud</b></a></li>
		<?php endif ?>
				</ul>
			</div>
	<?php endif ?>
		</div>
<?php endif?>

<!--// 게시판 유형 -->
<?php if($mi->default_style=='list'){
	if(!wp_is_mobile() || $mi->list_m != ' '){
		include $skin_path_abs.'_list_normal.php';  // <include target="_list_normal.html" />
	}
	else {
		include $skin_path_abs.'_list_m.php';  // <include target="_list_m.html" />
	}
}	
// elseif($mi->default_style=='webzine') {
// 	include $skin_path_abs.'_list_webzine.php';  // <include target="_list_webzine.html" />
// }
// elseif($mi->default_style=='gallery') {
// 	include $skin_path_abs.'_list_gallery.php';  // <include target="_list_gallery.html" />
// }
// elseif($mi->default_style=='cloud_gall') {
// 	include $skin_path_abs.'_list_cloud_gall.php';  // <include target="_list_cloud_gall.html" />
// }
elseif($mi->default_style=='faq') {
	include $skin_path_abs.'_list_faq.php';  // <include target="_list_faq.html" />
}
// elseif($mi->default_style=='guest' && !$oDocument->isExists()) {
// 	include $skin_path_abs.'_list_guest.php';  // <include target="_list_guest.html" />
// }
// elseif($mi->default_style=='blog' && !$oDocument->isExists()) {
// 	if($mi->rd_nav!='N') {
// 		$mi->rd_nav='';
// 	}
// 	foreach($post_list as $no => $oPost) {  // <block loop="$document_list=>$no,$oDocument">
// 		include $skin_path_abs.'_read.php';  // <include target="_read.html" />
// 		$rd_idx=1;
// 	}
// }
else {
	include $skin_path_abs.'_list_normal.php';  // <include target="_list_normal.html" />
}?>

		<!--// 하단 메뉴 -->
		<?php if($mi->display_setup_button == ' '):?><!-- cond="!$mi->display_setup_button"  -->
			<div class="btm_mn clear">
				<!--// FAQ 검색창 -->
				<?php if($mi->srch_btm!='N' && wp_is_mobile()):?><!-- cond="$mi->srch_btm!='N' && Mobile::isMobileCheckByAgent()"  -->
					<div class="bd_faq_srch m_srch" style="display:block;float:none">
						<?php include $skin_path_abs.'_search.php'; ?><!-- <include target="_search.html" /> -->
					</div>
				<?php endif ?>
				<div class="fl">
					<?php if(in_array('home', $mi->btm_mn)):?><!-- cond="@in_array('home',$mi->btm_mn)"  -->
						<a class="btn_img fl" href="<?php echo x2b_get_url('','mid',$mid,'page',$page,'post_id','','listStyle',$listStyle)?>"><i class="fa fa-bars"></i> <?php echo __('cmd_list', X2B_DOMAIN)?></a>
					<?php endif ?>
					<!--// 하단 검색창 -->
					<?php if($mi->srch_btm!='N' && !wp_is_mobile()):?><!-- cond="$mi->srch_btm!='N' && !Mobile::isMobileCheckByAgent()"  -->
						<form action="<?php echo x2b_get_url()?>" method="get" onsubmit="return procFilter(this, search)" class="bd_srch_btm<?php if($mi->srch_btm==2 || $search_keyword):?> on<?php endif ?>" no-error-return-url="true">
							<input type="hidden" name="category" value="<?php echo htmlspecialchars((string)$category) ?>" />
							<span class="btn_img itx_wrp">
								<button type="submit" onclick="jQuery(this).parents('form.bd_srch_btm').submit();return false;" class="ico_16px search">Search</button>
								<label for="bd_srch_btm_itx_<?php echo $board_id ?>"><?php echo __('cmd_search', X2B_DOMAIN)?></label>
								<input type="text" name="search_keyword" id="bd_srch_btm_itx_<?php echo $board_id ?>" class="bd_srch_btm_itx srch_itx" value="<?php echo htmlspecialchars((string)$search_keyword)?>" style='background:none'/>
							</span>
							<span class="btn_img select">
								<select name="search_target" style='height:18px;'>
									<?php foreach($search_option as $key => $val):?><!-- loop="$search_option=>$key,$val"  -->
										<option value="<?php echo $key ?>" <?php if($search_target==$key):?> selected="selected" <?php endif?>><?php echo $val ?></option>
									<?php endforeach ?>
								</select>
							</span>
							<?php if(isset($last_division)):?><!-- cond="$last_division"  -->
								<a class="btn_img bg_f_f9" href="<?php echo x2b_get_url('page',1,'post_id','','division',$last_division,'last_division','')?>"><?php echo __('cmd_search_next', X2B_DOMAIN)?></a>
							<?php endif ?>
						</form>
					<?php endif ?>
				</div>
				<div class="fr">
					<?php if(in_array('lbl_tag',$mi->btm_mn)):?><!-- cond="@in_array('tag',$mi->btm_mn)"  -->
						<a class="btn_img m_no" href="<?php echo x2b_get_url('act','dispBoardTagList')?>"><i class="fa fa-tag"></i> <?php echo __('lbl_tag', X2B_DOMAIN)?></a>
					<?php endif?>
					<?php if( $mi->write_btm_btn == ' ' || ($mi->write_btm_btn!='N' && $grant->write_post)):?><!-- cond="!$mi->write_btm_btn || ($mi->write_btm_btn!='N' && $grant->write_document)"  -->
						<a class="btn_img" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_WRITE_POST, 'post_id', '', 'page', '')?>"><i class="ico_16px write"></i> <?php echo __('cmd_write', X2B_DOMAIN)?></a>
					<?php endif ?>
					<?php if($grant->manager):?><!-- <block cond="$grant->manager"> -->
						<a class="btn_img" href="<?php echo admin_url('admin.php?page=x2b_disp_board_update&board_id='.$board_id);?>"><i class="ico_16px setup"></i> <?php echo __('cmd_setup', X2B_DOMAIN)?></a>
						<a class="btn_img" id='btn_manage_post_bottom'><i class="tx_ico_chk">✔</i> <?php echo __('cmd_manage_post', X2B_DOMAIN)?></a>
					<?php endif?><!-- </block> -->
				</div>
			</div>
		<?php endif ?>
	</div>

	<?php
	$prev_page = max($page-1, 1);
	$next_page = min($page+1, $page_navigation->n_last_page);
	if(!isset($division)){
		$division = null;
	}
	if(!isset($last_division)){
		$last_division = null;
	}?>
	<!--// 페이지네이션 -->
	<form action="<?php echo x2b_get_url('cmd', '', 'post_id', '')?>" method="get" class="bd_pg clear">
		<fieldset>
		<legend class="blind">Board Pagination</legend>
		<input type="hidden" name="category" value="<?php echo htmlspecialchars((string)$category) ?>" />
		<input type="hidden" name="search_keyword" value="<?php echo htmlspecialchars((string)$search_keyword)?>" />
		<input type="hidden" name="search_target" value="<?php echo sanitize_key($search_target) ?>" />
		<input type="hidden" name="listStyle" value="<?php echo $mi->default_style ?>" />
		
		<?php if( $page!=$prev_page ):?><!-- cond="$page!=$prev_page"  -->
			<a href="<?php echo x2b_get_url('page',$prev_page,'post_id','','division',$division,'last_division',$last_division)?>" class="direction"><i class="fa fa-angle-left"></i> Prev</a>
		<?php endif?>
		<?php if( $page==$prev_page ):?><!-- cond="$page==$prev_page"  -->
			<strong class="direction"><i class="fa fa-angle-left"></i> Prev</strong>
		<?php endif?>
		<a class="frst_last bubble<?php if($page==1):?> this<?php endif?>" href="<?php echo x2b_get_url('page','','post_id','','division',$division,'last_division',$last_division)?>" title="<?php echo __('lbl_first_page', X2B_DOMAIN)?>">1</a>
		<?php if( $page>($mi->page_count)/2+2 ):?>	<!-- cond="$page>($mi->page_count)/2+2"  -->
			<span class="bubble"><a href="#" class="tg_btn2" data-href=".bd_go_page" title="<?php echo __('cmd_go_page', X2B_DOMAIN)?>">...</a></span>
		<?php endif?>

		<?php while($page_no = $page_navigation->getNextPage()) {  // <block loop="$page_no=$page_navigation->getNextPage()" cond="$page_no!=1 && $page_no!=$page_navigation->last_page">
			if( $page_no==1 || $page_no==$page_navigation->n_last_page ){
				continue;
			}
			if( $page==$page_no ):?>
				<!-- <strong class="this" cond="$page==$page_no">{$page_no}</strong>  -->
				<strong class="this"><?php echo $page_no?></strong> 
			<?php else:?>
				<!-- <a cond="$page!=$page_no" href="{getUrl('page',$page_no,'document_srl','')}">{$page_no}</a> -->
				<a href="<?php echo x2b_get_url('page',$page_no,'post_id','','division',$division,'last_division',$last_division)?>"><?php echo $page_no?></a>
			<?php endif?>
		<?php }?>  <!-- </block> -->
		
		<!-- cond="($page+($mi->page_count+1)/2<$page_navigation->last_page) && ($mi->page_count+1<$page_navigation->last_page)"  -->
		<?php if( ($page+($mi->page_count+1)/2<$page_navigation->n_last_page) && ($mi->page_count+1<$page_navigation->n_last_page) ):?>
			<span class="bubble"><a href="#" class="tg_btn2" data-href=".bd_go_page" title="<?php echo __('cmd_go_page', X2B_DOMAIN)?>">...</a></span>
		<?php endif?>
		
		<?php if( $page_navigation->n_last_page!=1 ):?><!-- cond="$page_navigation->last_page!=1"  -->
			<a class="frst_last bubble<?php if($page==$page_navigation->n_last_page):?> this<?php endif?>" href="<?php echo x2b_get_url('page',$page_navigation->n_last_page,'post_id','','division',$division,'last_division',$last_division)?>" title="<?php echo __('lbl_last_page', X2B_DOMAIN)?>"><?php echo $page_navigation->n_last_page?></a>
		<?php endif?>

		<?php if( $page!=$next_page ):?> <!-- cond="$page!=$next_page" -->
			<a href="<?php echo x2b_get_url('page',$next_page,'post_id','','division',$division,'last_division',$last_division)?>" class="direction">Next <i class="fa fa-angle-right"></i></a>
		<?php endif?>
		
		<?php if( $page==$next_page ):?> <!-- cond="$page==$next_page"  -->
			<strong class="direction">Next <i class="fa fa-angle-right"></i></strong>
		<?php endif?>
		
		<div class="bd_go_page tg_cnt2 wrp">
			<button type="button" class="tg_blur2"></button>
			<input type="text" name="page" class="itx" />/ <?php echo $page_navigation->n_last_page?> <button type="submit" class="bd_btn">GO</button>
			<span class="edge"></span>
			<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
			<button type="button" class="tg_blur2"></button>
		</div>
		</fieldset>
	</form>

	<?php include $skin_path_abs.'_footer.php';?>

	<?php if($grant->manager ): ?>
	<script>
	jQuery('#btn_manage_post_header').click(function() {
		show_admin_manage_post_popup();
		return false;
	});
	jQuery('#btn_manage_post_bottom').click(function() {
		show_admin_manage_post_popup();
		return false;
	});
	</script>
	<?php endif?>
<?php endif?>