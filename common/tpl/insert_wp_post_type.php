<?php if(!defined('ABSPATH')) exit;?>

<style>
.grid-container {
	display: grid;
	grid-template-columns: 1fr 1fr; /* Adjust the fraction units as needed */
	gap: 10px; /* Optional: adds space between items */
}
.grid-item {
	/* Your item styles here */
}
</style>

<h2><?php echo __( 'lbl_enter_wp_post_type', X2B_DOMAIN )?></h2>

<div class="x_modal-body x_form-horizontal" style="max-height:none">
<div><input type="text" id="txt_insert_wp_post_type" name="keyword" value="" placeholder="<?php echo __( 'lbl_search', X2B_DOMAIN )?>"></div>
	<div class="grid-container">
		<div class="grid-item"><label  for="select_wp_post_type"><?php echo __( 'lbl_wp_post_types', X2B_DOMAIN )?></label></div>
		<div class="grid-item"><select name="select_wp_post_type" id="select_wp_post_type" style='padding-top:0px;padding-bottom:0px'>
				<?php foreach( $a_post_type as $_ => $s_wp_post_type ): ?>
					<option value="<?php echo $s_wp_post_type ?>"><?php echo $s_wp_post_type ?></option>
				<?php endforeach ?>
			</select>
		</div>
	</div>
	<div><input type="button" id="btn_search_wp_post_type" value="<?php echo __( 'lbl_wp_post_search', X2B_DOMAIN )?>"></div>
	<div><span id='div_caller_rst' style='color:red;'></span></div>
	<div>
		<ul id="ul_post_list" style="margin-top:5px">
			<?php foreach( $post_list as $_ => $o_post ): ?>
				<li class="post_list" onClick='copy_post_type_caller( "<?php echo $o_post->ID ?>" );'>
					<?php echo $o_post->post_title ?> <i class="vr">|</i> <?php echo $o_post->post_status ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<script>
jQuery( '#txt_insert_wp_post_type' ).keypress(function(event) {
	if (event.which == 13) {
		event.preventDefault();
		jQuery('#btn_search_wp_post_type').click();
	}
});

jQuery( '#btn_search_wp_post_type' ).on('click', function() {
	var keyword = jQuery( '#txt_insert_wp_post_type' ).val();
	var s_wp_post_type = jQuery('#select_wp_post_type').find(":selected").val();
	if (keyword != '') {
		jQuery.post(x2board_ajax_info.url, {
			'security':x2board_ajax_info.nonce,
			'action': x2board_ajax_info.cmd_render_insert_wp_post_type,
			'board_id': x2board_ajax_info.board_id,
			'mode': 'get_search_rst',
			'keyword': keyword,
			'wp_post_type': s_wp_post_type}, function(res) {
			if(typeof callback === 'function') {
				callback(res);
			}
			else {
				if(res.result == 'error') {
					alert(res.message);
				}
				if( typeof res.wp_post_type == 'object' && res.wp_post_type.length ) {
					jQuery("#ul_post_list").empty();
					res.wp_post_type.forEach (function (el, index) {
						jQuery("#ul_post_list").append(el);  // add li
					});
				} else {
					jQuery("#ul_post_list").empty();
				}
			}
		});
	}
});

function copy_post_type_caller( n_wp_post_id ) {
	let s_wp_posttype_caller = 'sv_' + n_wp_post_id + '_sv';  // refer to \includes\modules\editor\editor.model.php
	// https://www.w3schools.com/howto/howto_js_copy_clipboard.asp
	navigator.clipboard.writeText( s_wp_posttype_caller );
	jQuery("#div_caller_rst").html( "Copied the code: " + s_wp_posttype_caller );
}
</script>