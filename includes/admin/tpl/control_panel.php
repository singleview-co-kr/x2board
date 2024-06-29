<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;} ?>
<div class="wrap">
	<?php require 'header.php'; ?>
	
	<!-- <div id="welcome-panel" class="welcome-panel"> -->
		<?php // include 'welcome.php' ?>
	<!-- </div> -->
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __( 'cmd_setup_plugin', X2B_DOMAIN ); ?></a>
	</h2>
	<ul id="x2board-dashboard-options">
		<li id="x2board_name_filter">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4><?php echo __( 'lbl_forbidden_nickname', X2B_DOMAIN )?></h4>
				<p><?php echo __( 'desc_forbidden_nickname', X2B_DOMAIN )?></p>
				<p>
					<textarea name="option[x2board_name_filter]" style="width:100%"><?php echo $this->_get_forbidden_nickname(); ?></textarea>
					<button type="submit" class="button"><?php echo __( 'cmd_update', X2B_DOMAIN )?></button>
				</p>
			</form>
		</li>
		<li id="x2board_content_filter">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4><?php echo __( 'lbl_forbidden_word', X2B_DOMAIN )?></h4>
				<p><?php echo __( 'desc_forbidden_word', X2B_DOMAIN )?></p>
				<p>
					<textarea name="option[x2board_content_filter]" style="width:100%"><?php echo $this->_get_forbidden_word(); ?></textarea>
					<button type="submit" class="button"><?php echo __( 'cmd_update', X2B_DOMAIN )?></button>
				</p>
			</form>
		</li>
	</ul>
</div>
