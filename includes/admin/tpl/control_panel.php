<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;} ?>
<div class="wrap">
	<?php require 'header.php'; ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
	
				<!-- <div id="welcome-panel" class="welcome-panel"> -->
					<?php // include 'welcome.php' ?>
				<!-- </div> -->
				<h2 class="nav-tab-wrapper">
					<a href="#" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __( 'cmd_setup_plugin', X2B_DOMAIN ); ?></a>
				</h2>
				<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data">
					<?php wp_nonce_field( X2B_CMD_ADMIN_PROC_UPDATE_GLOBAL_VARS ); ?>
					<input type="hidden" name="action" value="<?php echo X2B_CMD_ADMIN_PROC_UPDATE_GLOBAL_VARS; ?>">
					<ul id="x2board-dashboard-options">
						<li id="x2boardsupport_plugin">
							<h4><?php echo __( 'lbl_endorse_plugin', X2B_DOMAIN )?></h4>
							<p><?php echo __( 'desc_endorse_plugin', X2B_DOMAIN )?></p>
							<p>
							<?php
							$s_checked = $b_agree_endorse_plugin ? 'checked' : '';
							?>
							<input type="checkbox" name="x2board_endorse_plugin" value="Y" <?php echo $s_checked ?> ><?php echo __( 'lbl_agree_endorse_plugin', X2B_DOMAIN ) ?>
							</p>
						</li>
						<li id="x2board_nickname_filter">
							<h4><?php echo __( 'lbl_forbidden_nickname', X2B_DOMAIN )?></h4>
							<p><?php echo __( 'desc_forbidden_nickname', X2B_DOMAIN )?></p>
							<p>
								<textarea name="x2board_forbidden_nickname" style="width:100%"><?php echo $this->_get_forbidden_nickname(); ?></textarea>
							</p>
						</li>
						<li id="x2board_content_filter">
							<h4><?php echo __( 'lbl_forbidden_word', X2B_DOMAIN )?></h4>
							<p><?php echo __( 'desc_forbidden_word', X2B_DOMAIN )?></p>
							<p>
								<textarea name="x2board_forbidden_word" style="width:100%"><?php echo $this->_get_forbidden_word(); ?></textarea>
							</p>
						</li>
					</ul>
					<button type="submit" class="button"><?php echo __( 'cmd_update', X2B_DOMAIN )?></button>
				</form>
			</div><!-- /#post-body-content -->
			<div id="postbox-container-1" class="postbox-container">

				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php include_once X2B_PATH . 'includes'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'sidebar.php'; ?>
				</div><!-- /#side-sortables -->

			</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
	</div><!-- /#poststuff -->
</div>
