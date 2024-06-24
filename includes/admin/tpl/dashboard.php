<?php if(!defined('ABSPATH')) exit;?>
<div class="wrap">
	<?php include 'header.php' ?>
	
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container">
				<div id="dashboard_activity" class="postbox ">
					<div class="postbox-header">
						<h2 class="hndle ui-sortable-handle"><?php echo __('lbl_latest_post', X2B_DOMAIN)?></h2> <A HREF='<?php echo admin_url( 'admin.php?page='.X2B_CMD_ADMIN_VIEW_LATEST_POST )?>'><?php echo __('cmd_view_more', X2B_DOMAIN)?></A>
					</div>
					<div class="inside">
						<div id="activity-widget">
							<div id="published-posts" class="activity-block">
								<ul>
									<?php foreach( $a_latest_posts as $_ => $o_latest_post ):?>
										<li><span><?php echo $o_latest_post->s_regdate?></span> <a href="<?php echo $o_latest_post->s_post_permlink?>" aria-label="<?php echo $o_latest_post->title?>" target='_new'><?php echo $o_latest_post->title?></a></li>
									<?php endforeach?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<div id="dashboard_activity" class="postbox ">
					<div class="postbox-header">
						<h2 class="hndle ui-sortable-handle"><?php echo __('cmd_latest_comment', X2B_DOMAIN)?></h2> <A HREF='<?php echo admin_url( 'admin.php?page='.X2B_CMD_ADMIN_VIEW_LATEST_COMMENT )?>'><?php echo __('cmd_view_more', X2B_DOMAIN)?></A>
					</div>
					<div class="inside">
						<div id="activity-widget">
							<div id="published-posts" class="activity-block">
								<ul>
									<?php foreach( $a_latest_comments as $_ => $o_latest_comment ):?>
										<li><span><?php echo $o_latest_comment->s_regdate?></span> <a href="<?php echo $o_latest_comment->s_comment_permlink?>" aria-label="<?php echo $o_latest_comment->content?>" target='_new'><?php echo $o_latest_comment->content?></a></li>
									<?php endforeach?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-3" class="postbox-container">
				<div id="dashboard_activity" class="postbox ">
					<div class="postbox-header">
						<h2 class="hndle ui-sortable-handle"><?php echo __('cmd_latest_file', X2B_DOMAIN)?></h2> <A HREF='<?php echo admin_url( 'admin.php?page='.X2B_CMD_ADMIN_VIEW_LATEST_FILE )?>'><?php echo __('cmd_view_more', X2B_DOMAIN)?></A>
					</div>
					<div class="inside">
						<div id="activity-widget">
							<div id="published-posts" class="activity-block">
								<ul>
									<?php foreach( $a_latest_files as $_ => $o_latest_file ):?>
										<li><span><?php echo $o_latest_file->s_regdate?></span> <a href="<?php echo $o_latest_file->source_filename?>" aria-label="<?php echo $o_latest_file->source_filename?>" target='_new'><?php echo $o_latest_file->source_filename?></a></li>
									<?php endforeach?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function kboard_system_option_update(form){
	jQuery.post(ajaxurl, jQuery(form).serialize(), function(res){
		window.location.reload();
	});
	return false;
}
</script>