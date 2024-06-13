<?php
/**
 * Renders the settings page.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link https://singleview.co.kr
 * @since 2.6.0
 *
 * @package x2board
 * @subpackage 
 */

namespace X2board\Includes\Admin\Tpl;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Render the settings page.
 *
 * @since 2.6.0
 *
 * @return void
 */
function x2b_options_page() {
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( sanitize_key( wp_unslash( $_GET['tab'] ) ), x2b_get_settings_sections() ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<div class="wrap">
		<?php include 'header.php' ?>

		<!-- <p>
			<a class="x2b_button" href="<?php //echo admin_url( 'tools.php?page=x2b_tools_page' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<?php // esc_html_e( 'Visit the Tools page', 'autoclose' ); ?>
			</a>
		<p> -->

		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">

			<ul class="nav-tab-wrapper" style="padding:0">
				<?php
				foreach ( x2b_get_settings_sections() as $tab_id => $tab_name ) {

					$active = $active_tab === $tab_id ? ' ' : '';

					echo '<li style="margin:0;"><a href="#' . esc_attr( $tab_id ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab ' . sanitize_html_class( $active ) . '">';
						echo esc_html( $tab_name );
					echo '</a></li>';

				}
				?>
			</ul>

			<!-- <form method="post" action="options.php"> -->
			<form id="x2b-setting-form" action="<?php echo admin_url('admin-post.php')?>" method="post" enctype="multipart/form-data">
<?php 
if($_GET['page'] == X2B_CMD_ADMIN_VIEW_BOARD_INSERT ){
	$s_action = X2B_CMD_ADMIN_PROC_INSERT_BOARD;
}
if($_GET['page'] == X2B_CMD_ADMIN_VIEW_BOARD_UPDATE ){
	$s_action = X2B_CMD_ADMIN_PROC_UPDATE_BOARD;
}
// var_dump($s_action);
wp_nonce_field($s_action);
?>				
				<input type="hidden" name="action" value="<?php echo $s_action?>">
				<input type="hidden" name="board_id" value="<?php echo $_GET['board_id']?>">

				<?php foreach ( x2b_get_settings_sections() as $tab_id => $tab_name ) : ?>

				<div id="<?php echo esc_attr( $tab_id ); ?>">
					<table class="form-table">
					<?php
// echo $tab_id.'<BR>';
						do_settings_fields( 'x2board_settings_' . $tab_id, 'x2board_settings_' . $tab_id );
					?>
					</table>
					<p>
					<?php
						// Default submit button.
						submit_button(
							__( 'cmd_save_change', X2B_DOMAIN ),
							'primary',
							'submit',
							false
						);

						echo '&nbsp;&nbsp;';
						// Reset button.
						$confirm = esc_js( __( 'msg_delete_board', X2B_DOMAIN ) );
						submit_button(
							__( 'cmd_delete_board', X2B_DOMAIN ),
							'secondary',
							'delete_board',
							false,
							array(
								'onclick' => "return confirm('{$confirm}');",
							)
						);
					?>
					</p>
				</div><!-- /#tab_id-->

				<?php endforeach; ?>

			</form>

		</div><!-- /#post-body-content -->

		<div id="postbox-container-1" class="postbox-container">

			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php include_once X2B_PATH . 'includes\admin\tpl\sidebar.php'; ?>
			</div><!-- /#side-sortables -->

		</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
		<br class="clear" />
		</div><!-- /#poststuff -->

	</div><!-- /.wrap -->

	<?php
	echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Array containing the settings' sections.
 *
 * @since 2.6.0
 *
 * @return array Settings array
 */
function x2b_get_settings_sections() {
	$x2b_settings_sections = array(
		'general'           => __( 'lbl_board_info', X2B_DOMAIN ),
		'category'          => __( 'lbl_category_info', X2B_DOMAIN ),
		'user_define_field' => __( 'lbl_user_define_field', X2B_DOMAIN ),
		'permission'        => __( 'lbl_permission_info', X2B_DOMAIN ),
		'extra'             => __( 'lbl_extra_info', X2B_DOMAIN ),
		'skin_vars'         => __( 'lbl_skin_info', X2B_DOMAIN ),
	);

	/**
	 * Filter the array containing the settings' sections.
	 *
	 * @since 2.6.0
	 *
	 * @param array $x2b_settings_sections Settings array
	 */
	return apply_filters( 'x2b_settings_sections', $x2b_settings_sections );
}


/**
 * Miscellaneous callback funcion
 *
 * @since 2.6.0
 *
 * @param array $args Arguments passed by the setting.
 * @return void
 */
function x2b_missing_callback( $args ) {
	/* translators: %s: Setting ID. */
	printf( 'The callback function used for the <strong>%s</strong> setting is missing.', esc_html( $args['id'] ) );
}


/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 2.6.0
 *
 * @param array $args Arguments passed by the setting.
 * @return void
 */
function x2b_header_callback( $args ) {

	$html = '<hr><FONT SIZE="5"><B>' . wp_kses_post( $args['desc'] ) . '</FONT></B></hr>';

	/**
	 * After Settings Output filter
	 *
	 * @since 2.6.0
	 * @param string $html HTML string.
	 * @param array  $args Arguments array.
	 */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display text fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_text_callback( $args ) {

	// First, we read the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;

// var_dump($args['id']);
	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
		$value = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$size = sanitize_html_class( isset( $args['size'] ) ? $args['size'] : 'regular' );

	$class = sanitize_html_class( $args['field_class'] );

	$disabled = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
	$readonly = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';

	$attributes = $disabled . $readonly;

	foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
		$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
	}

	$html  = sprintf( '<input type="text" id="%1$s" name="%1$s" class="%2$s" value="%3$s" %4$s />', sanitize_key( $args['id'] ), $class . ' ' . $size . '-text', esc_attr( stripslashes( $value ) ), $attributes );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display textarea.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_textarea_callback( $args ) {

	// First, we read the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
		$value = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$class = sanitize_html_class( $args['field_class'] );

	$html  = sprintf( '<textarea class="%3$s" cols="50" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', sanitize_key( $args['id'] ), esc_textarea( stripslashes( $value ) ), 'large-text ' . $class );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display checboxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_checkbox_callback( $args ) {

	// First, we read the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;
	$default = isset( $args['options'] ) ? $args['options'] : '';
	$set     = isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ? $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] : x2b_get_default_option( $args['id'] );
	
	if( isset( $args['checked_value'] ) ) {
		$s_checked_value = isset( $args['checked_value']['checked'] ) ? $args['checked_value']['checked'] : '1';
		$s_unchecked_value = isset( $args['checked_value']['unchecked'] ) ? $args['checked_value']['unchecked'] : '-1';
	}
	else {
		$s_checked_value = '1';
		$s_unchecked_value = '-1';
	}

	if( $set !== '-1'){
		if( $s_checked_value == '1' ){
			$checked = ! empty( $set ) ? checked( 1, intval($set), false ) : '';
		}
		else {
			$checked = ! empty( $set ) ? checked( $s_checked_value, $set, false ) : '';
		}
	}

	$html  = sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', sanitize_key( $args['id'] ), $s_unchecked_value );
	$html .= sprintf( '<input type="checkbox" id="%1$s" name="%1$s" value="%2$s" %3$s />', sanitize_key( $args['id'] ), $s_checked_value, $checked );
	$html .= ( (bool) $set !== (bool) $default ) ? '<em style="color:orange"> ' . __( 'msg_modified_from_default_setting', X2B_DOMAIN ) . '</em>' : ''; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_multicheck_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;
	$html = '';
	if ( ! empty( $args['options'] ) ) {
		$html .= sprintf( '<input type="hidden" name="%1$s" value="-1" />', sanitize_key( $args['id'] ) );

		foreach ( $args['options'] as $key => $option ) {
			if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ][ $key ] ) ) {
				$enabled = $key;
			} else {
				$enabled = null;
			}

			if( isset( $args['mandatory'] ) ) { // mandatory field
				$s_disabled = isset( $args['mandatory'][ $key ] ) && $args['mandatory'][ $key ] == 'mandatory' ? ' checked="checked" onclick="alert(\''.$key.' is mandatory\'); return false;"' : '';
			}
			else {
				$s_disabled = null;
			}

			$html .= sprintf( '<input name="%1$s[%2$s]" id="%1$s[%2$s]" type="checkbox" value="%3$s" %4$s %5$s /> ', sanitize_key( $args['id'] ), esc_attr( $key ), esc_attr( $key ), checked( $key, $enabled, false ), $s_disabled );
			$html .= sprintf( '<label for="%1$s[%2$s]">%3$s</label> <br />', sanitize_key( $args['id'] ), esc_attr( $key ), $option );
		}

		$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Grantselect Callback
 *
 * Renders grant checkboxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_grantselect_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
		$value = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
	} else {
		$value = isset( $args['default'] ) ? $args['default'] : '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="crp-chosen"';
	} else {
		$chosen = '';
	}
	
	$html = sprintf( '<select id="%1$s" name="%1$s" %2$s class="x2board-grant-select"/>', esc_attr( $args['id'] ), $chosen );
	foreach ( $args['options'] as $option => $name ) {
		$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option ), selected( $option, $value, false ), $name );
	}
	$html .= '</select>';

	if( $value == X2B_CUSTOMIZE ) {
		$s_hide_class = '';
		$a_allowed_grant = $A_X2B_ADMIN_BOARD_SETTINGS['board_grant'][ $args['id'] ];
	}
	else {
		$s_hide_class = 'x2board-hide';
		$a_allowed_grant = array();
	}

	$html .= '<div class="x2board-permission-read-roles-view '.$s_hide_class.'">';
	foreach(get_editable_roles() as $roles_key=>$roles_value) {
		$s_mandatory = $roles_key=='administrator' ? 'onclick="return false" '.checked( true, true, false ) : '';
		$s_checked = ( $roles_key != 'administrator' && in_array($roles_key, $a_allowed_grant)) ? checked( true, true, false ) : '';
		$html .= '<label><input type="checkbox" name="grant['.esc_attr($args['id']).']['.X2B_CUSTOMIZE.'][]" class="field_data roles_checkbox" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
	}
	
	$html .='</div>';
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_radio_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;
	$html = '';

	foreach ( $args['options'] as $key => $option ) {
		$checked = false;
		if(is_numeric($key)) {
			$key = strval($key);
		}

		if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) && $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] === $key ) {
			$checked = true;
		} elseif ( isset( $args['default'] ) && $args['default'] === $key && ! isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
			$checked = true;
		}

		$html .= sprintf( '<input name="%1$s" id="%1$s[%2$s]" type="radio" value="%2$s" %3$s />', sanitize_key( $args['id'] ), $key, checked( true, $checked, false ) );
		$html .= sprintf( '<label for="%1$s[%2$s]">%3$s</label> <br />', sanitize_key( $args['id'] ), $key, $option );
	}

	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_number_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
		$value = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = isset( $args['size'] ) ? $args['size'] : 'regular';

	$html  = sprintf( '<input type="number" step="%1$s" max="%2$s" min="%3$s" class="%4$s" id="%5$s" name="%5$s" value="%6$s"/>', esc_attr( $step ), esc_attr( $max ), esc_attr( $min ), sanitize_html_class( $size ) . '-text', sanitize_key( $args['id'] ), esc_attr( stripslashes( $value ) ) );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_select_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
		$value = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
	} else {
		$value = isset( $args['default'] ) ? $args['default'] : '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="crp-chosen"';
	} else {
		$chosen = '';
	}

	$html = sprintf( '<select id="%1$s" name="%1$s" %2$s />', esc_attr( $args['id'] ), $chosen );

	foreach ( $args['options'] as $option => $name ) {
		$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option ), selected( $option, $value, false ), $name );
	}

	$html .= '</select>';
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * WP image UI Callback
 *
 * Renders image file upload UI fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_image_callback( $args ) {
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	$s_var_id = esc_attr( $args['id'] );

	$html = sprintf( '<input type="file" id="%1$s" name="%1$s" accept="image/gif, image/png, image/jpeg" />', $s_var_id );
	if(isset($A_X2B_ADMIN_BOARD_SETTINGS[$s_var_id]['full_url'])) {
		$html .= '<br />';
		$html .= sprintf( '<img src="%1$s" style="max-width:200px" />', esc_attr( $A_X2B_ADMIN_BOARD_SETTINGS[$s_var_id]['full_url'] ) );
		$html .= '<label><input type="checkbox" name="delete_old_file['.$s_var_id.']" value="'.esc_attr( $A_X2B_ADMIN_BOARD_SETTINGS[$s_var_id]['abs_path'] ).'">'.__( 'cmd_delete_file', X2B_DOMAIN ).'</label>';
	}
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * WP sortable UI Callback
 *
 * Renders WP sortable UI fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
function x2b_wpsortableui_callback( $args ) {

	$html = '<div class="col-left x2board-category-setting-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<div class="x2board-update-category">
					<h2>'.__( 'lbl_update_category', X2B_DOMAIN ).'</h2>
					<div class="form-field form-required term-name-wrap">
						<label for="update-category-name">'.__( 'lbl_update_category', X2B_DOMAIN ).'</label>
						<input type="text" id="update-category-name" class="update_category_name" name="update_category_name">
						<input type="hidden" id="current-category-name" class="update_category_name" name="current_category_name">
						<input type="hidden" id="category-id" name="category_id" value="">
						<input type="hidden" id="parent-id" name="parent_id" value="">
					</div>
				</div>
				
				<div class="x2board-update-category btn">
					<label><input type="checkbox" id="default-category" name="default-category" value="Y">'.__( 'lbl_default_category', X2B_DOMAIN ).'</label>
					<button type="button" class="button" onclick="x2board_category_handler(\'update\')">'.__( 'cmd_update', X2B_DOMAIN ).'</button>
					<button type="button" class="button" onclick="x2board_category_handler(\'remove\')">'.__( 'cmd_remove', X2B_DOMAIN ).'</button>
				</div>
				
				<div class="x2board-new-category">
					<h2>'.__( 'cmd_add_new_category', X2B_DOMAIN ).'</h2>
					<div class="form-field form-required term-name-wrap">
						<label for="new-category-name">'.__( 'lbl_new_category_name', X2B_DOMAIN ).'</label>
						<input type="text" id="new-category-name" name="new_category">
						<input type="hidden" id="new-parent-id">
					</div>
				</div>
				
				<div class="x2board-new-category-btn">
					<button type="button" class="button-primary" onclick="x2board_category_handler(\'insert\')">'.__( 'cmd_add_new_category', X2B_DOMAIN ).'</button>
				</div>
			</div>
		</div>
	</div>
	<div class="x2board-category-setting-right">
		<div class="x2board-category-setting-sortable">
		<h2>'.__( 'lbl_category_status', X2B_DOMAIN ).'</h2>
		<ul class="sortable">';
	
	$o_cat_admin_model = new \X2board\Includes\Modules\Category\categoryAdminModel();
	$html .= $o_cat_admin_model->build_category_sortable_html();
	unset($o_cat_admin_model);

	$html .= '</ul>
		</div>
	</div>';
	echo apply_filters( 'x2b_after_setting_output', $html, $args );
}

function x2b_wpuserfieldui_callback( $args ) {
	$o_post_admin_model = new \X2board\Includes\Modules\Post\postAdminModel();
	echo $o_post_admin_model->render_user_field_ui();
	unset($o_cat_admin_model);
}

function x2b_wplistfieldui_callback( $args ) {
	$o_board_admin_model = new \X2board\Includes\Modules\Board\boardAdminModel();
	echo $o_board_admin_model->render_user_field_ui();
	unset($o_board_admin_model);
}

/**
 * Display the default thumbnail below the setting.
 *
 * @since 2.6.0
 *
 * @param  string $html Current HTML.
 * @param  array  $args Argument array of the setting.
 * @return string
 */
// function x2b_admin_thumbnail( $html, $args ) {

// 	$thumb_default = crp_get_option( 'thumb_default' );

// 	if ( 'thumb_default' === $args['id'] && '' !== $thumb_default ) {
// 		$html .= '<br />';
// 		$html .= sprintf( '<img src="%1$s" style="max-width:200px" title="%2$s" alt="%2$s" />', esc_attr( $thumb_default ), esc_html__( 'Default thumbnail', 'contextual-related-posts' ) );
// 	}

// 	return $html;
// }
// add_filter( 'x2b_after_setting_output', 'x2b_admin_thumbnail', 10, 2 );

/**
 * Display csv fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_csv_callback( $args ) {

// 	x2b_text_callback( $args );
// }


/**
 * Display CSV fields of numbers.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_numbercsv_callback( $args ) {

// 	x2b_csv_callback( $args );
// }


/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_descriptive_text_callback( $args ) {
// 	$html = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

// 	/** This filter has been defined in settings-page.php */
// 	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// }


/**
 * Display CSS fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_css_callback( $args ) {

// 	x2b_textarea_callback( $args );
// }


/**
 * Radio callback with description.
 *
 * Renders radio boxes with each item having it separate description.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_radiodesc_callback( $args ) {
// 	global $A_X2B_ADMIN_BOARD_SETTINGS;
// 	$html = '';

// 	foreach ( $args['options'] as $option ) {
// 		$checked = false;

// 		if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) && $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] === $option['id'] ) {
// 			$checked = true;
// 		} elseif ( isset( $args['default'] ) && $args['default'] === $option['id'] && ! isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
// 			$checked = true;
// 		}

// 		$html .= sprintf( '<input name="%1$s" id="%1$s[%2$s]" type="radio" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), $option['id'], checked( true, $checked, false ) );
// 		$html .= sprintf( '<label for="%1$s[%2$s]">%3$s</label>', sanitize_key( $args['id'] ), $option['id'], $option['name'] );
// 		$html .= ': <em>' . wp_kses_post( $option['description'] ) . '</em> <br />';
// 	}

// 	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

// 	/** This filter has been defined in settings-page.php */
// 	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// }


/**
 * Callback for thumbnail sizes
 *
 * Renders list of radio boxes with various thumbnail sizes.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_thumbsizes_callback( $args ) {
// 	global $A_X2B_ADMIN_BOARD_SETTINGS;
// 	$html = '';

// 	if ( ! isset( $args['options']['crp_thumbnail'] ) ) {
// 		$args['options']['crp_thumbnail'] = array(
// 			'name'   => 'crp_thumbnail',
// 			'width'  => crp_get_option( 'thumb_width', 150 ),
// 			'height' => crp_get_option( 'thumb_height', 150 ),
// 			'crop'   => crp_get_option( 'thumb_crop', true ),
// 		);
// 	}

// 	foreach ( $args['options'] as $name => $option ) {
// 		$checked = false;

// 		if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) && $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] === $name ) {
// 			$checked = true;
// 		} elseif ( isset( $args['default'] ) && $args['default'] === $name && ! isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
// 			$checked = true;
// 		}
// 		$cropped = $option['crop'] ? __( ' cropped', X2B_DOMAIN ) : '';

// 		$html .= sprintf(
// 			'<input name="%1$s" id="%1$s[%2$s]" type="radio" value="%2$s" %3$s /> ',
// 			sanitize_key( $args['id'] ),
// 			$name,
// 			checked( true, $checked, false )
// 		);
// 		$html .= sprintf(
// 			'<label for="%1$s[%2$s]">%3$s</label> <br />',
// 			sanitize_key( $args['id'] ),
// 			$name,
// 			$name . ' (' . $option['width'] . 'x' . $option['height'] . $cropped . ')'
// 		);
// 	}

// 	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

// 	/** This filter has been defined in settings-page.php */
// 	echo apply_filters( 'x2b_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// }


/**
 * Display post types fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_posttypes_callback( $args ) {

// 	global $A_X2B_ADMIN_BOARD_SETTINGS;
// 	$html = '';

// 	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
// 		$options = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
// 	} else {
// 		$options = isset( $args['options'] ) ? $args['options'] : '';
// 	}

// 	// If post_types contains a query string then parse it with wp_parse_args.
// 	if ( is_string( $options ) && strpos( $options, '=' ) ) {
// 		$post_types = wp_parse_args( $options );
// 	} else {
// 		$post_types = wp_parse_list( $options );
// 	}

// 	$wp_post_types   = get_post_types(
// 		array(
// 			'public' => true,
// 		)
// 	);
// 	$posts_types_inc = array_intersect( $wp_post_types, $post_types );

// 	$html .= sprintf( '<input type="hidden" name="%1$s" value="-1" />', sanitize_key( $args['id'] ) );

// 	foreach ( $wp_post_types as $wp_post_type ) {

// 		$html .= sprintf( '<input name="%1$s[%2$s]" id="%1$s[%2$s]" type="checkbox" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), esc_attr( $wp_post_type ), checked( true, in_array( $wp_post_type, $posts_types_inc, true ), false ) );
// 		$html .= sprintf( '<label for="%1$s[%2$s]">%2$s</label> <br />', sanitize_key( $args['id'] ), $wp_post_type );

// 	}

// 	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

// 	/** This filter has been defined in settings-page.php */
// 	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// }


/**
 * Display taxonomies fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
// function x2b_taxonomies_callback( $args ) {

// 	global $A_X2B_ADMIN_BOARD_SETTINGS;
// 	$html = '';

// 	if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
// 		$options = $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ];
// 	} else {
// 		$options = isset( $args['options'] ) ? $args['options'] : '';
// 	}

// 	// If taxonomies contains a query string then parse it with wp_parse_args.
// 	if ( is_string( $options ) && strpos( $options, '=' ) ) {
// 		$taxonomies = wp_parse_args( $options );
// 	} else {
// 		$taxonomies = wp_parse_list( $options );
// 	}

// 	/* Fetch taxonomies */
// 	$argsc         = array(
// 		'public' => true,
// 	);
// 	$output        = 'objects';
// 	$operator      = 'and';
// 	$wp_taxonomies = get_taxonomies( $argsc, $output, $operator );

// 	$taxonomies_inc = array_intersect( wp_list_pluck( (array) $wp_taxonomies, 'name' ), $taxonomies );

// 	$html .= sprintf( '<input type="hidden" name="%1$s" value="-1" />', sanitize_key( $args['id'] ) );

// 	foreach ( $wp_taxonomies as $wp_taxonomy ) {

// 		$html .= sprintf( '<input name="%1$s[%2$s]" id="%1$s[%2$s]" type="checkbox" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), esc_attr( $wp_taxonomy->name ), checked( true, in_array( $wp_taxonomy->name, $taxonomies_inc, true ), false ) );
// 		$html .= sprintf( '<label for="%1$s[%2$s]">%3$s (%4$s)</label> <br />', sanitize_key( $args['id'] ), esc_attr( $wp_taxonomy->name ), $wp_taxonomy->labels->name, $wp_taxonomy->name );

// 	}

// 	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

// 	/** This filter has been defined in settings-page.php */
// 	echo apply_filters( 'crp_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
// }


/**
 * Function to add an action to search for tags using Ajax.
 *
 * @since 2.6.0
 *
 * @return void
 */
// function x2b_tags_search() {

// 	if ( ! isset( $_REQUEST['tax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
// 		wp_die();
// 	}

// 	$tax      = '';
// 	$taxonomy = sanitize_key( $_REQUEST['tax'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
// 	if ( ! empty( $taxonomy ) ) {
// 		$tax = get_taxonomy( $taxonomy );
// 		if ( ! $tax ) {
// 			wp_die();
// 		}

// 		if ( ! current_user_can( $tax->cap->assign_terms ) ) {
// 			wp_die();
// 		}
// 	}
// 	$s = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// 	$comma = _x( ',', 'tag delimiter' );
// 	if ( ',' !== $comma ) {
// 		$s = str_replace( $comma, ',', $s );
// 	}
// 	if ( false !== strpos( $s, ',' ) ) {
// 		$s = explode( ',', $s );
// 		$s = $s[ count( $s ) - 1 ];
// 	}
// 	$s = trim( $s );

// 	/** This filter has been defined in /wp-admin/includes/ajax-actions.php */
// 	$term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $tax, $s );

// 	/*
// 	 * Require $term_search_min_chars chars for matching (default: 2)
// 	 * ensure it's a non-negative, non-zero integer.
// 	 */
// 	if ( ( 0 === $term_search_min_chars ) || ( strlen( $s ) < $term_search_min_chars ) ) {
// 		wp_die();
// 	}

// 	$terms = get_terms(
// 		array(
// 			'taxonomy'   => ! empty( $taxonomy ) ? $taxonomy : null,
// 			'name__like' => $s,
// 			'hide_empty' => false,
// 		)
// 	);

// 	$results = array();
// 	foreach ( (array) $terms as $term ) {
// 		$results[] = "{$term->name} ({$term->taxonomy}:{$term->term_taxonomy_id})";
// 	}

// 	echo wp_json_encode( $results );
// 	wp_die();
// }
// add_action( 'wp_ajax_nopriv_x2b_tag_search', 'x2b_tags_search' );
// add_action( 'wp_ajax_x2b_tag_search', 'x2b_tags_search' );


/**
 * Display the default thumbnail below the setting.
 *
 * @since 2.6.0
 *
 * @param  string $html Current HTML.
 * @param  array  $args Argument array of the setting.
 * @return string
 */
// function x2b_admin_thumbnail( $html, $args ) {

// 	$thumb_default = x2b_get_option( 'thumb_default' );

// 	if ( 'thumb_default' === $args['id'] && '' !== $thumb_default ) {
// 		$html .= '<br />';
// 		$html .= sprintf( '<img src="%1$s" style="max-width:200px" title="%2$s" alt="%2$s" />', esc_attr( $thumb_default ), esc_html__( 'Default thumbnail', X2B_DOMAIN ) );
// 	}

// 	return $html;
// }
// add_filter( 'x2b_after_setting_output', 'x2b_admin_thumbnail', 10, 2 );


/**
 * Output messages when a specific style is selected.
 *
 * @since 2.8.0
 *
 * @param  string $html Current HTML.
 * @param  array  $args Argument array of the setting.
 * @return string
 */
// function x2b_styles_messages( $html, $args ) {

// 	$crp_styles = crp_get_option( 'crp_styles' );

// 	if ( in_array( $crp_styles, array( 'rounded_thumbs', 'thumbs_grid' ), true ) && ( 'show_excerpt' === $args['id'] || 'show_author' === $args['id'] || 'show_date' === $args['id'] ) ) {
// 		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles or Text only in the Styles tab', X2B_DOMAIN ) . '</span>';
// 	}

// 	if ( in_array( $crp_styles, array( 'rounded_thumbs', 'thumbs_grid', 'text_only' ), true ) && 'post_thumb_op' === $args['id'] ) {
// 		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles in the Styles tab', X2B_DOMAIN ) . '</span>';
// 	}

// 	return $html;
// }
// add_filter( 'x2b_after_setting_output', 'x2b_styles_messages', 10, 2 );