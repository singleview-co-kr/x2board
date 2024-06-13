<?php if(!defined('ABSPATH')) exit;?>
<div class="wrap">
	<div class="x2b-header-logo"></div>
	<h1 class="wp-heading-inline"><?php echo X2B_DOMAIN ?> : <?php echo __('cmd_board_list', X2B_DOMAIN)?></h1>
	
	<?php if ( isset($post_new_file )):?>
    <a href="<?php echo $post_new_file?>" class="page-title-action"> <?php echo __('cmd_create_board', X2B_DOMAIN) ?></a>
	<?php endif?>
	<!-- <a href="https://singleview.co.kr" class="page-title-action" onclick="window.open(this.href);return false;"><?php //echo __('Home', X2B_DOMAIN)?></a>
	<a href="https://singleview.co.kr/blog" class="page-title-action" onclick="window.open(this.href);return false;"><?php //echo __('Blog', X2B_DOMAIN)?></a> -->
	
	<hr class="wp-header-end">
	
	<form method="get">
		<input type="hidden" name="page" value="x2b_disp_admin_boards">
		<?php $this->search_box(__('lbl_search', X2B_DOMAIN), 'x2b_list_search')?>
	</form>
	<form method="post">
		<?php $this->display()?>
	</form>
</div>