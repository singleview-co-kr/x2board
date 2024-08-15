<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}
// <include cond="$notice_list && $mi->notice_style=='2'" target="_notice.html" />
if( $notice_list && $mi->notice_style == '2' ) {
	include $skin_path_abs.'_notice.php';
}
?>
<ul class="bd_lst bd_faq <?php echo $mi->faq_style ?>">
	<!--// Notice -->
	<!-- <li cond="$notice_list && $mi->notice_style != ' '" loop="$notice_list=>$no,$document" class="notice clear"> -->
	<?php if( $notice_list && $mi->notice_style == ' ' ): ?>
		<?php foreach( $notice_list as $no => $post ): ?>
			<li class="notice clear">
				<a class="q clear" href="<?php echo x2b_get_url( 'post_id', $post->post_id, 'cpage', '' ) ?>">
					<b class="mrk"><?php echo __('lbl_notice', X2B_DOMAIN)?></b>
					<?php if( $mi->use_category == 'Y' && $post->get('category_id') ): ?>
						<strong class="cate">&#91;<?php echo $category_list[$post->get('category_id')]->title ?>&#93;</strong>
					<?php endif ?>
					<span class="tl"><?php echo $post->get_title($mi->subject_cut_size)?></span>
					<span class="fr">
						<?php if( $mi->faq_style != ' ' ): ?> <!--@if(!$mi->faq_style)-->
							<?php if( isset( $list_config['nick_name'] ) ): ?>
								<span class="nick"><?php echo $post->get_nick_name() ?></span>  <!-- cond="$list_config['nick_name']"  -->
							<?php endif ?>
							<?php if( isset( $list_config['regdate_dt'] ) ): ?>	
								<span class="date"><?php echo $post->get_regdate('Y.m.d') ?></span> <!-- cond="$list_config['regdate']"  -->
							<?php endif ?>
						<?php endif ?> <!--@end-->
					</span>
				</a>
				<?php if( $grant->manager ): ?> <!-- cond="$grant->manager"  -->
					<input type="checkbox" name="cart" value="<?php echo $post->post_id ?>" class="iCheck" title="Check This Article" onclick="doAddPostCart(this)" <?php if($post->is_carted()): ?>checked="checked" <?php endif?> />
				<?php endif ?>
			</li>
		<?php endforeach ?>
	<?php endif ?>
	<!--// Normal -->
	<!-- <li loop="$document_list=>$no,$document" id="bdFaq_{$document->document_srl}" class="article clear"> -->
	<?php foreach( $post_list as $no => $post ):?>
		<li id="bdFaq_<?php echo $post->post_id ?>" class="article clear">
			<?php if( $grant->manager ): ?> <!-- cond="$grant->manager"  -->
				<input type="checkbox" name="cart" value="<?php echo $post->post_id ?>" class="iCheck" title="Check This Article" onclick="doAddPostCart(this)" <?php if($post->is_carted()): ?>checked="checked" <?php endif?> />
			<?php endif ?>
			<a class="q clear" href="<?php echo x2b_get_url('post_id', $post->post_id, 'cpage', '') ?>" onClick="bdFaq(<?php echo $post->post_id?>);return false;">
				<b class="mrk mrkQ">Q<span>:</span></b>
				<?php if( $mi->use_category == 'Y' && $post->get('category_id') ): ?><!-- cond="$mi->use_category=='Y' && $document->get('category_srl')" -->
					<strong class="cate">&#91;<?php echo $category_list[$post->get('category_id')]->title ?>&#93;</strong>
				<?php endif ?>
				<span class="tl"><?php echo $post->get_title($mi->subject_cut_size)?></span>
				<span class="fr">
					<?php if( $post->get_user_define_eid_value('rating') ): ?> <!-- cond="$document->getExtraEidValue('rating')"  -->
						<span class="starRating" title="<?php echo $post->get_user_define_eid_value('rating') ?>점"><span style="width:<?php echo $post->get_user_define_eid_value('rating')*10 ?>%"><?php echo $post->get_user_define_eid_value('rating') ?>점</span></span>
					<?php endif ?>
					<?php if( $list_config['nick_name'] ): ?> <!-- cond="$list_config['nick_name']"  -->
						<span class="nick"><?php echo $post->get_nick_name() ?></span>
					<?php endif ?>
					<?php if( $list_config['regdate_dt'] ): ?> <!-- cond="$list_config['regdate']" -->
						<span class="date"><?php echo $post->get_regdate('Y.m.d') ?></span>
					<?php endif ?>
				</span>
				<i class="fa fa-chevron-up"></i>
				<i class="fa fa-chevron-down"></i>
			</a>
			<div class="a clear">
				<b class="mrk mrkA">A<span>:</span></b>
				<div class="editArea">
					<a class="url" href="<?php echo $post->get_permanent_url() ?>"><?php echo $post->get_permanent_url() ?></a>
					<?php if( $post->is_editable() ): ?>  <!-- <block cond="$document->isEditable()"> -->
						<a class="edit" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_POST, 'post_id', $post->post_id)?>"><span class="ico_16px write"></span><?php echo __('cmd_modify', X2B_DOMAIN)?></a>
						<a class="edit" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_POST, 'post_id', $post->post_id)?>"><span class="ico_16px delete"></span><?php echo __('cmd_delete', X2B_DOMAIN)?></a>
					<?php endif ?><!-- </block> -->
				</div>
				<?php echo $post->get_content(false) ?>
			</div>
		</li>
	<?php endforeach ?>
</ul>