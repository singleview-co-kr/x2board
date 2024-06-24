<?php 
if(!defined('ABSPATH')) exit;

$a_mapper = array(X2B_CMD_ADMIN_VIEW_LATEST_POST => 'cmd_latest_post',
				  X2B_CMD_ADMIN_VIEW_LATEST_COMMENT => 'cmd_latest_comment',
				  X2B_CMD_ADMIN_VIEW_LATEST_FILE => 'cmd_latest_file'
				);
?>

<style type="text/css">
.wp-list-table .column-title { width: 40%; }
.wp-list-table .column-content { width: 40%; }
.wp-list-table .column-source_filename { width: 30%; }
.wp-list-table .column-wp_page_id { width: 15%; }
.wp-list-table .column-regdate_dt { width: 10%; }
.wp-list-table .column-status { width: 5%; }
</style>
<div class="wrap">
	<div class="x2b-header-logo"></div>
	<h1 class="wp-heading-inline"><?php echo X2B_DOMAIN ?> : <?php echo __($a_mapper[$_REQUEST['page']], X2B_DOMAIN)?></h1>
	
	<hr class="wp-header-end">
	
	<form method="get">
		<input type="hidden" name="page" value="x2b_disp_admin_boards">
		<?php $o_latest->search_box(__('lbl_search', X2B_DOMAIN), 'x2b_posts')?>
	</form>
	<form method="post">
		<?php $o_latest->display()?>
	</form>
</div>