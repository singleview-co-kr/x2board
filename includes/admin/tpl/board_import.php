<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;} ?>
<div class="wrap">
	<?php require 'header.php'; ?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<h2 class="nav-tab-wrapper">
					<a href="#tab-x2board-setting-0" class="tab-x2board nav-tab nav-tab-active" onclick="x2board_setting_tab_change(0);">XE2</a>
					<!-- <a href="#tab-x2board-setting-1" class="tab-x2board nav-tab" onclick="x2board_setting_tab_change(1);">other Board</a> -->
				</h2>
				<div class="tab-x2board-setting tab-x2board-setting-active">
					<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
						<?php wp_nonce_field( X2B_CMD_ADMIN_PROC_UPDATE_SEQ ); ?>
						<input type="hidden" name="action" value="<?php echo X2B_CMD_ADMIN_PROC_UPDATE_SEQ; ?>">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label for="x2b_sequence_max_id"><?php echo __( 'lbl_max_seq_id', X2B_DOMAIN ); ?></label></th>
									<td><?php echo $n_cur_auto_increment; ?></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="x2b_sequence_max_id"><?php echo __( 'name_req_seq_id', X2B_DOMAIN ); ?></label></th>
									<td>
										<input type="text" name="x2b_sequence_max_id">
										<p class="description"><?php echo __( 'desc_req_seq_id', X2B_DOMAIN ); ?></p>
										<br>
										<input type="submit" class="button-primary" value="<?php echo __( 'cmd_update', X2B_DOMAIN ); ?>">
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
						<?php wp_nonce_field( X2B_CMD_ADMIN_PROC_IMPORT_BOARD ); ?>
						<input type="hidden" name="action" value="<?php echo X2B_CMD_ADMIN_PROC_IMPORT_BOARD; ?>">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label for="xe_export_xml_file"><?php echo __( 'name_select_xe2_backup_file', X2B_DOMAIN ); ?></label></th>
									<td>
										<input type="file" name="xe_export_xml_file" accept=".xml,.zip">
										<p class="description"><?php echo __( 'desc_select_xe2_backup_file', X2B_DOMAIN ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for=""><?php echo X2B_DOMAIN; ?> <?php echo __( 'name_select_target_board', X2B_DOMAIN ); ?></label></th>
									<td>
										<select name="board_id">
											<option value=""><?php echo __( 'name_select_target_board', X2B_DOMAIN ); ?></option>
										<?php
										foreach ( $o_board_list->items as $o_board ) :
											?>
											<option value="<?php echo $o_board->board_id; ?>"><?php echo $o_board->board_title; ?></option>
										<?php endforeach ?>
										</select>
										<br>
										<input type="submit" class="button-primary" value="<?php echo __( 'cmd_import', X2B_DOMAIN ); ?>">
										<p class="description"><?php echo __( 'desc_select_target_board', X2B_DOMAIN ); ?></p>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div><!-- /#post-body-content -->
			<div id="postbox-container-1" class="postbox-container">

				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php include_once X2B_PATH . 'includes'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'sidebar.php'; ?>
				</div><!-- /#side-sortables -->

			</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
	</div><!-- /#poststuff -->
</div>
