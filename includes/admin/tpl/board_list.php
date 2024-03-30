<?php if(!defined('ABSPATH')) exit;?>
<div class="wrap">
	<div class="x2b-header-logo"></div>
	<h1 class="wp-heading-inline">X2Board : <?php echo __('Board List', 'x2board')?></h1>
	
	<?php if ( isset($post_new_file )):?>
    <a href="<?php echo $post_new_file?>" class="page-title-action"> <?php echo __('Create board', 'x2board') ?></a>
	<?php endif?>
	<!-- <a href="https://singleview.co.kr" class="page-title-action" onclick="window.open(this.href);return false;"><?php echo __('Home', 'x2board')?></a>
	<a href="https://blog.singleview.co.kr" class="page-title-action" onclick="window.open(this.href);return false;"><?php echo __('Blog', 'x2board')?></a> -->
	
	<hr class="wp-header-end">
	
	<form method="get">
		<input type="hidden" name="page" value="x2b_disp_admin_boards">
		<?php $this->search_box(__('Search', 'x2board'), 'x2b_list_search')?>
	</form>
	<form method="post">
		<?php $this->display()?>
	</form>
</div>