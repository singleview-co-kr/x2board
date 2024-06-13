<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>
<div class="rd_nav<?php if($mi->rd_nav_tx): ?> rd_nav_tx<?php endif ?> img_tx fr m_btn_wrp">
	<?php if($mi->rd_nav_tx): ?><!--@if($ft_read_nav)-->
		<?php if($mi->prev_next=='2' && !$post->is_notice() && $mi->default_style!='blog'): ?><!-- <block cond="$mi->prev_next=='2' && !$oDocument->isNotice() && $mi->default_style!='blog'"> -->
		<a class="rd_prev bubble no_bubble" href="#" title="<?php echo __('cmd_prev', X2B_DOMAIN)?>"><i class="fa fa-arrow-left"></i><b class="tx"><?php echo __('cmd_prev', X2B_DOMAIN)?></b></a>
		<a class="rd_next bubble no_bubble" href="#" title="<?php echo __('cmd_next', X2B_DOMAIN)?>"><i class="fa fa-arrow-right"></i><b class="tx"><?php echo __('cmd_next', X2B_DOMAIN)?></b></a>
		<?php endif ?><!-- </block> -->
	<?php else: ?><!--@else-->
		<?php if($mi->prev_next!='N' || $mi->default_style=='viewer'): ?><!-- cond="$mi->prev_next!='N' || $mi->default_style=='viewer'"  -->
			<div class="help bubble left m_no">
				<a class="text" href="#" onclick="jQuery(this).next().fadeToggle();return false;">?</a>
				<div class="wrp">
					<div class="speech">
						<h4><?php echo __('lbl_shortcut', X2B_DOMAIN) ?></h4>
						<p><strong><i class="fa fa-long-arrow-left"></i><span class="blind">Prev</span></strong><?php echo __('cmd_prev', X2B_DOMAIN)?><?php echo __('lbl_post', X2B_DOMAIN)?></p>
						<p><strong><i class="fa fa-long-arrow-right"></i><span class="blind">Next</span></strong><?php echo __('cmd_next', X2B_DOMAIN)?><?php echo __('lbl_post', X2B_DOMAIN)?></p>
						<?php if($mi->default_style=='viewer'): ?><!-- cond="$mi->default_style=='viewer'" -->
						<p><strong>ESC</strong><?php echo __('cmd_close', X2B_DOMAIN)?></p>
						<?php endif ?>
					</div>
					<i class="edge"></i>
					<!--// ie8; --><i class="ie8_only bl"></i><i class="ie8_only br"></i>
				</div>
			</div>
		<?php endif ?>
		<?php if($mi->font_btn!='N' && $lang_type=='ko_KR'): ?><!-- cond="$mi->font_btn!='N' && $lang_type=='ko_KR'"  -->
			<a class="tg_btn2 bubble m_no" href="#" data-href=".bd_font_select" title="<?php echo __('cmd_select_font', X2B_DOMAIN)?>"><strong>가</strong><i class="arrow down"></i></a>
		<?php endif ?>
		<a class="font_plus bubble" href="#" title="<?php echo __('lbl_larger', X2B_DOMAIN) ?>"><i class="fa fa-search-plus"></i><b class="tx"><?php echo __('lbl_larger', X2B_DOMAIN) ?></b></a>
		<a class="font_minus bubble" href="#" title="<?php echo __('lbl_smaller', X2B_DOMAIN) ?>"><i class="fa fa-search-minus"></i><b class="tx"><?php echo __('lbl_smaller', X2B_DOMAIN) ?></b></a>
		<?php if($mi->viewer=='2'): ?><!-- cond="$mi->viewer=='2'"  -->
			<a class="if_viewer bubble" href="#" onclick="window.open('<?php echo x2b_get_url('listStyle','viewer','page','')?>','viewer','width=9999,height=9999,scrollbars=yes,resizable=yes,toolbars=no');return false;" title="<?php echo __('cmd_with_viewer', X2B_DOMAIN) ?>"><i class="fa fa-picture-o"></i><b class="tx"><?php echo __('cmd_with_viewer', X2B_DOMAIN) ?></b></a>
		<?php endif ?>
	<?php endif ?><!--@end-->
	<a class="back_to bubble m_no" href="#bd_<?php echo $board_id?>_<?php echo $post->post_id?>" title="<?php echo __('cmd_move_up', X2B_DOMAIN)?>"><i class="fa fa-arrow-up"></i><b class="tx"><?php echo __('cmd_move_up', X2B_DOMAIN)?></b></a>
	<a class="back_to bubble m_no" href="#rd_end_<?php echo $post->post_id?>" title="(<?php echo __('cmd_list', X2B_DOMAIN)?>) <?php echo __('cmd_move_down', X2B_DOMAIN)?>"><i class="fa fa-arrow-down"></i><b class="tx"><?php echo __('cmd_move_down', X2B_DOMAIN)?></b></a>
	<a class="comment back_to bubble if_viewer m_no" href="#<?php echo $post->post_id?>_comment" title="<?php echo __('cmd_go_cmt', X2B_DOMAIN) ?>"><i class="fa fa-comment"></i><b class="tx"><?php echo __('cmd_go_cmt', X2B_DOMAIN) ?></b></a>
	<?php if($mi->rd_nav_item == ' '): ?><!-- cond="!$mi->rd_nav_item"  -->
		<a class="print_doc bubble m_no<?php if($mi->default_style=='viewer'):?> this<?php endif?>" href="<?php echo x2b_get_url('listStyle','viewer','act', '') ?>" title="<?php echo __('cmd_print', X2B_DOMAIN)?>"><i class="fa fa-print"></i><b class="tx"><?php echo __('cmd_print', X2B_DOMAIN)?></b></a>
	<?php endif ?>
	<?php if(($mi->show_files == ' ' || $mi->show_files=='2') && $post->has_uploaded_files()): ?><!-- cond="(!$mi->show_files || $mi->show_files=='2') && $oDocument->hasUploadedFiles()"  -->
		<a class="file back_to bubble m_no" href="#files_<?php echo $post->post_id?>" onclick="jQuery('#files_<?php echo $post->post_id?>').show();return false" title="<?php echo __('lbl_uploaded_file', X2B_DOMAIN)?>"><i class="fa fa-paperclip"></i><b class="tx"><?php echo __('lbl_uploaded_file', X2B_DOMAIN)?></b></a>
	<?php endif ?>
	<?php if($is_logged): ?><!-- cond="$is_logged"  -->
		<a class="post_<?php echo $post->post_id?> action bubble m_no" href="#popup_menu_area" onclick="return false;" title="<?php echo __('cmd_post_do', X2B_DOMAIN)?>"><i class="fa fa-ellipsis-h"></i><b class="tx"><?php echo __('cmd_post_do', X2B_DOMAIN)?></b></a>
	<?php endif ?>
	<?php if($post->is_editable()): ?><!-- <block cond="$oDocument->isEditable()"> -->
		<a class="edit" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_MODIFY_POST, 'post_id', $post->post_id)?>"><i class="ico_16px write"></i><?php echo __('cmd_modify', X2B_DOMAIN)?></a>
		<a class="edit" href="<?php echo x2b_get_url('cmd', X2B_CMD_VIEW_DELETE_POST, 'post_id', $post->post_id)?>"><i class="ico_16px delete"></i><?php echo __('cmd_delete', X2B_DOMAIN)?></a>
	<?php endif ?><!-- </block> -->
</div>