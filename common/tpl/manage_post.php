<?php if(!defined('ABSPATH')) exit;?>

<?php if( count($post_list) == 0 ):?>
<script type="text/javascript">
	alert( "<?php echo __( 'msg_no_post', X2B_DOMAIN )?>" );
	window.open('', '_self', '');
	window.close();
</script>
<?php endif ?>

<div class="x popup">
	<div class="x_modal-header">
		<h1><?php echo __( 'lbl_manage_post', X2B_DOMAIN )?></h1>
	</div>
	<div class="x_modal-body x_form-horizontal" style="max-height:none">
				<div class="x_control-group">
			<div class="x_control-label"><?php echo __( 'lbl_selected_post_count', X2B_DOMAIN )?> (<?php echo count($post_list)?>)</div>
			<div class="x_controls">
				<ul style="margin-top:5px">
					<?php foreach( $post_list as $_ => $o_post ): ?>
						<li class="post_list">
							<input type="hidden" name="cart" value="<?php echo $o_post->post_id ?>" /><?php echo $o_post->title ?> <i class="vr">|</i> <?php echo $o_post->nick_name ?>
						</li>
					<?php endforeach ?>				
				</ul>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label" for="_target_board"><?php echo __( 'lbl_target_board', X2B_DOMAIN )?></label>
			<div class="x_controls">
				<span class="x_input-append">
				<select id="target_board_id" name="target_board_id">
					<option value=""><?php echo __( 'msg_select_board', X2B_DOMAIN )?></option>
					<?php foreach( $board_list as $_ => $o_board ): ?>
						<option value="<?php echo $o_board->board_id ?>"><?php echo $o_board->board_title ?></option>
					<?php endforeach ?>	
				</select>
				</span>
			</div>
		</div>
		<div class="x_control-group">
			<label class="x_control-label" for="target_category"><?php echo __( 'lbl_category', X2B_DOMAIN )?></label>
			<div class="x_controls">
				<select id="target_category_id" name="target_category_id"></select>
			</div>
		</div>
		<!-- <div class="x_control-group">
			<label class="x_control-label" for="message_content">쪽지 보내기</label>
			<div class="x_controls" style="margin-right:14px">
				<textarea name="message_content" id="message_content" rows="4" cols="42" style="width:100%"></textarea>
			</div>
		</div> -->
	</div>
	<div class="x_modal-footer">
		<span class="x_btn-group x_pull-left">
			<!-- <button type="button" class="x_btn" onclick="doManageDocument('trash');">휴지통</button> -->
			<button type="button" class="x_btn" id='btn_delete_post'><?php echo __( 'cmd_delete', X2B_DOMAIN )?></button> <!-- onclick="doManageDocument('delete');" -->
		</span>
		<span class="x_btn-group x_pull-right">
			<button type="button" class="x_btn x_btn-inverse" id='btn_move_post'><?php echo __( 'cmd_move', X2B_DOMAIN )?></button> <!-- onclick="doManageDocument('move');" -->
			<!-- <button type="button" class="x_btn x_btn-inverse" onclick="doManageDocument('copy');">복사</button> -->
		</span>
	</div>
</div>

<script>
jQuery("#target_board_id").change(function(){
	jQuery.post(x2board_ajax_info.url, {
		'action': x2board_ajax_info.cmd_manage_post,
		'board_id': jQuery(this).val(),
		'mode': 'get_category_by_board_id',
		'security':x2board_ajax_info.nonce}, function(res) {
			// x2board_ajax_lock = false;
			if(typeof callback === 'function') {
				callback(res);
			}
			else {
				if(res.result == 'error') {
					alert(res.message);
				}
				else{
					jQuery("select#target_category_id option").remove();  // clear option
					res.category.forEach (function (el, index) {
						jQuery("#target_category_id").append(el);  // add option
					});
				}
			}
		});
});

jQuery('#btn_move_post').click(function() {
	const n_tgt_board_id = jQuery("select[name=target_board_id]").val();
	if(!n_tgt_board_id.length) {
		alert('<?php echo __( 'msg_select_board', X2B_DOMAIN )?>');
		return;
	}
	const n_tgt_cat_id = jQuery("select[name=target_category_id]").val();
	let a_carted_post = new Array();
	jQuery("input:hidden[name=cart]").each(function(){
		a_carted_post.push(jQuery(this).val());
	});
	if(!a_carted_post.length) {
		alert('<?php echo __( 'msg_select_post', X2B_DOMAIN )?>');
		return;
	}

	jQuery.post(x2board_ajax_info.url, {
		'action': x2board_ajax_info.cmd_manage_post,
		'board_id': n_tgt_board_id,
		'target_category_id': n_tgt_cat_id,
		'mode': 'move',
		'carted_post_id': a_carted_post.join('|@|'),
		'security':x2board_ajax_info.nonce}, function(res) {
			// x2board_ajax_lock = false;
			if(typeof callback === 'function') {
				callback(res);
			}
			else {
				if(res.result == 'error') {
					alert(res.message);
				}
				else{
					if(opener) { 
						opener.window.location.href = '<?php echo get_the_permalink();?>';
					}
    			    window.close();
				}
			}
		});
});

jQuery('#btn_delete_post').click(function() {
	let a_carted_post = new Array();
	jQuery("input:hidden[name=cart]").each(function(){
		a_carted_post.push(jQuery(this).val());
	});
	if(!a_carted_post.length) {
		alert('<?php echo __( 'msg_select_post', X2B_DOMAIN )?>');
		return;
	}

	jQuery.post(x2board_ajax_info.url, {
		'action': x2board_ajax_info.cmd_manage_post,
		'board_id': x2board_ajax_info.board_id,
		'mode': 'delete',
		'carted_post_id': a_carted_post.join('|@|'),
		'security':x2board_ajax_info.nonce}, function(res) {
			// x2board_ajax_lock = false;
			if(typeof callback === 'function') {
				callback(res);
			}
			else {
				if(res.result == 'error') {
					alert(res.message);
				}
				else{
					if(opener) { 
						opener.window.location.href = '<?php echo get_the_permalink();?>';
					}
    			    window.close();
				}
			}
		});
});
</script>