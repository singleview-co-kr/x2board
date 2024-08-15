<table class="bd_lst bd_tb_lst bd_tb common_notice">
	<?php foreach( $notice_list as $no => $post ): ?>  <!-- loop="$notice_list=>$no,$document"  -->
		<tr class="notice">
			<?php if( isset( $list_config['no'] ) ): ?> 	<!-- cond="$list_config['no']" -->
				<td><?php echo __('lbl_notice', X2B_DOMAIN)?></td>
			<?php endif ?>
			<td class="title"><a href="<?php echo x2b_get_url( 'post_id', $post->post_id, 'listStyle', $listStyle, 'cpage', '' ) ?>"><strong><?php echo $post->get_title($mi->subject_cut_size)?></strong></a></td>
			<?php if( isset( $list_config['nick_name'] ) ): ?>	<!-- cond="$list_config['nick_name']" -->
				<td><a href="#popup_menu_area" class="member_<?php echo $post->get('post_author') ?>" onclick="return false"><?php echo $post->get_nick_name() ?></a></td>
			<?php endif ?>
			<?php if( isset( $list_config['regdate_dt'] ) ): ?> <!-- cond="$list_config['regdate']" -->
				<td><?php echo $post->get_regdate( 'Y.m.d' ) ?></td>
			<?php endif ?>
			<?php if( isset( $list_config['last_update_dt'] ) ): ?>	<!-- cond="$list_config['last_update']" -->
				<td><?php echo x2b_get_time_gap( $post->get('last_update_dt'), "y.m.d" ) ?></td>
			<?php endif ?>
		</tr>
	<?php endforeach ?>
</table>