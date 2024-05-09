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
		<h1><?php esc_html_e( 'X2Board Settings', 'x2board' ); ?></h1>

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
							__( 'Save Changes', 'x2board' ),
							'primary',
							'submit',
							false
						);

						echo '&nbsp;&nbsp;';
						// Reset button.
						$confirm = esc_js( __( 'Do you really want to delete this board?', 'x2board' ) );
						submit_button(
							__( 'Delete Board', 'x2board' ),
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
		'general'           => __( 'Board info', 'x2board' ),
		'category'          => __( 'Category info', 'x2board' ),
		'user_define_field' => __( 'User define field', 'x2board' ),
		'permission'        => __( 'Permission info', 'x2board' ),
		'extra'             => __( 'Extra info', 'x2board' ),
		'skin_vars'         => __( 'Skin info', 'x2board' ),
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
	printf( esc_html__( 'The callback function used for the <strong>%s</strong> setting is missing.', 'x2board' ), esc_html( $args['id'] ) );
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
	$html .= ( (bool) $set !== (bool) $default ) ? '<em style="color:orange"> ' . esc_html__( 'Modified from default setting', 'x2board' ) . '</em>' : ''; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
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

		if ( isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) && $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] === $key ) {
			$checked = true;
		} elseif ( isset( $args['default'] ) && $args['default'] === $key && ! isset( $A_X2B_ADMIN_BOARD_SETTINGS[ $args['id'] ] ) ) {
			$checked = true;
		}

		$html .= sprintf( '<input name="%1$s" id="%1$s[%2$s]" type="radio" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), $key, checked( true, $checked, false ) );
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
					<h2>'.esc_html__( 'Update category', 'x2board' ).'</h2>
					<div class="form-field form-required term-name-wrap">
						<label for="update-category-name">'.esc_html__( 'Category to update', 'x2board' ).'</label>
						<input type="text" id="update-category-name" class="update_category_name" name="update_category_name">
						<input type="hidden" id="current-category-name" class="update_category_name" name="current_category_name">
						<input type="hidden" id="category-id" name="category_id" value="">
						<input type="hidden" id="parent-id" name="parent_id" value="">
					</div>
				</div>
				
				<div class="x2board-update-category btn">
					<label><input type="checkbox" id="default-category" name="default-category" value="Y">'.esc_html__( 'Default Category', 'x2board' ).'</label>
					<button type="button" class="button" onclick="x2board_category_handler(\'update\')">'.esc_html__( 'Update', 'x2board' ).'</button>
					<button type="button" class="button" onclick="x2board_category_handler(\'remove\')">'.esc_html__( 'Remove', 'x2board' ).'</button>
				</div>
				
				<div class="x2board-new-category">
					<h2>'.esc_html__( 'Add new category', 'x2board' ).'</h2>
					<div class="form-field form-required term-name-wrap">
						<label for="new-category-name">'.esc_html__( 'New category name', 'x2board' ).'</label>
						<input type="text" id="new-category-name" name="new_category">
						<input type="hidden" id="new-parent-id">
					</div>
				</div>
				
				<div class="x2board-new-category-btn">
					<button type="button" class="button-primary" onclick="x2board_category_handler(\'insert\')">'.esc_html__( 'Add new category', 'x2board' ).'</button>
				</div>
			</div>
		</div>
	</div>
	<div class="x2board-category-setting-right">
		<div class="x2board-category-setting-sortable">
		<h2>'.esc_html__( 'Category status', 'x2board' ).'</h2>
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

	// $o_user_define_fields_ui = new X2bUserDefineFieldsUI();
	// echo $o_user_define_fields_ui->render_user_field_ui();
	// unset($o_user_define_fields_ui);
}

if (!class_exists('\\X2board\\Includes\\Admin\\Tpl\\X2bUserDefineFieldsUI')) {

	class X2bUserDefineFieldsUI {
		
		private $_a_unchosen_user_default_fields = array();
		private $_a_user_define_fields = array();
		private $_a_extended_fields = array();
		
		/**
		 * Constructor
		 *
		 * @param int $board_id Sequence of board
		 * @return void
		 */
		public function __construct() {
			$n_board_id = intval(sanitize_text_field($_GET['board_id'] ));
			$s_columns = '`var_name`, `var_type`, `var_is_required`, `var_search`, `var_default`, `var_desc`, `eid`, `json_param`';  // , `meta_key`
			global $wpdb;
			$a_temp = $wpdb->get_results("SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC");
// var_dump($a_temp);
			
			foreach( $a_temp as $_ => $o_field ) {
				$a_other_field = unserialize($o_field->json_param);

				$a_single_field['field_type'] = $o_field->var_type;
				// $a_single_field['field_label'] = $o_field->var_name;
				$a_single_field['field_name'] = $o_field->var_name;
				$a_single_field['meta_key'] = $o_field->eid;
				$a_single_field['default_value'] = $o_field->var_default;
				$a_single_field['description'] = $o_field->var_desc;
				$a_single_field['required'] = $o_field->var_is_required;

				$a_single_field = array_merge($a_single_field, $a_other_field);
				$this->_a_user_define_fields[$o_field->eid] = $a_single_field;

				unset($a_single_field);
				unset($a_other_field);
			}
			unset($a_temp);

			$o_post_user_define_fields = \X2board\Includes\Classes\AdminUserDefineFields::getInstance();
			$o_post_user_define_fields->set_user_define_fields_from_db($this->_a_user_define_fields);
			$this->a_user_define_fields = $o_post_user_define_fields->get_user_define_fields();
			$this->_a_unchosen_user_default_fields = $o_post_user_define_fields->get_unchosen_default_fields();
			$this->_a_extended_fields = $o_post_user_define_fields->get_extended_fields();
			
// var_dump($this->_a_unchosen_user_default_fields);

			unset($o_post_user_define_fields);
		}

		/**
		 * WP user field UI Callback
		 *
		 * Renders WP user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @return void
		 */
		/*public function render_user_field_ui() {
			$s_html = '<div class="x2board-fields-wrap">
						<!---div class="x2board-fields-message">
							일부 스킨에서는 입력필드 설정이 적용되지 않습니다.
						</div --->
						<div class="x2board-fields-left">
							<h3 class="x2board-fields-h3">'.__('Available field', 'x2board').'</h3>
							<ul class="x2board-fields">
								<li class="x2board-fields-default left">
									<button type="button" class="x2board-fields-header">'.
										__('Basic field', 'x2board').
										'<span class="fields-up">▲</span>
										<span class="fields-down">▼</span>
									</button>
									<ul class="x2board-fields-list x2board-fields-content">';
			$s_html .= $this->_render_unchosen_default_fields();
			$s_html .=				'</ul>
								</li>
								<li class="x2board-fields-extension left">
								<button type="button" class="x2board-fields-header">'.
								__('Extended fields', 'x2board').
									'<span class="fields-up">▲</span>
									<span class="fields-down">▼</span>
								</button>
								<ul class="x2board-fields-list x2board-fields-content">';
			
			if($this->_a_extended_fields) {
				$s_html .= $this->_render_user_extended_fields();
			}

			$s_html .= 		'</ul>
						</li>
					</ul>
				</div>
				<div class="x2board-fields-right">
					<div class="x2board-fields x2board-sortable-fields">
						<h3 class="x2board-fields-h3">'.__('User define fields presentation', 'x2board').'</h3>
						<div class="description">'.__('Drag from the left section to activate', 'x2board').'</div>
						<ul class="x2board-skin-fields x2board-fields-sortable connected-sortable">';
			$s_html .= $this->_render_user_define_fields();
			$s_html .= 	'</ul>
						<div class="description"><button type="button" class="button button-small" onclick="x2board_skin_fields_reset()">'.__('Reset configuration', 'x2board').'</button></div>
					</div>
				</div>
			</div>';
			echo apply_filters( 'x2b_after_setting_output', $s_html );
		}*/

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		/*private function _render_unchosen_default_fields() {
			$s_html = null;
			foreach($this->_a_unchosen_user_default_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}*/

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		/*private function _render_user_extended_fields() {
			$s_html =	null;
			foreach($this->_a_extended_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}*/

		/**
		 * unchosen user default field UI render
		 *
		 * Renders unchosen user field UI fields.
		 *
		 * @since 2.6.0
		 *
		 * @param array $array of unchosen user default field 
		 * @return void
		 */
		/*private function _render_user_define_fields() {
			$s_html = null;
			foreach($this->a_user_define_fields as $key=>$o_item) {
				$s_html .= 	$o_item->get_widget_html();
			}
			return $s_html;
		}*/
	}
}
//////////////////////////////////////
// static $a_user_default_fields = array();
// static $a_extended_fields = array();
// static $a_all_fields = array();

/**
 * WP user field UI Callback
 *
 * Renders WP user field UI fields.
 *
 * @since 2.6.0
 *
 * @param array $args Array of arguments.
 * @return void
 */
/*function x2b_wpuserfieldui_callback( $args ) {
	
	$html = '<div class="x2board-fields-wrap">
				<!---div class="x2board-fields-message">
					일부 스킨에서는 입력필드 설정이 적용되지 않습니다.
				</div --->
				<div class="x2board-fields-left">
					<h3 class="x2board-fields-h3">'.__('Available field', 'x2board').'</h3>
					<ul class="x2board-fields">
						<li class="x2board-fields-default left">
							<button type="button" class="x2board-fields-header">';
	$html .=					__('Basic field', 'x2board');
	$html .= 					'<span class="fields-up">▲</span>
								<span class="fields-down">▼</span>
							</button>
							<ul class="x2board-fields-list x2board-fields-content">';
	
	// $n_board_id = isset($_GET['board_id'])?esc_attr($_GET['board_id']):'';
	$o_post_admin_model = new \X2board\Includes\Modules\Post\postAdminModel();
	
	$a_user_default_fields = $o_post_admin_model->get_default_fields();
	$a_extended_fields = $o_post_admin_model->get_extended_fields();

	$a_all_fields = array_merge($a_user_default_fields, $a_extended_fields);

	$a_user_default_fields_unchosen = $o_post_admin_model->get_default_fields_unchosen();
	foreach($a_user_default_fields_unchosen as $key=>$item)	{
////////////////////////
		$html .= 				'<li class="default '.$key.'">';
		// $html .= 					'<input type="hidden" class="field_data class" value="'.$item['class'].'">';
		// $s_value = isset($item['close_button'])?$item['close_button']:'';
		// $html .= 					'<input type="hidden" class="field_data close_button" value="'.$s_value.'">';
		$html .= 					'<div class="x2board-extends-fields">
										<div class="x2board-fields-title toggle x2board-field-handle">
											<button type="button">';
		$html .=					 		esc_html($item['field_label']);
		$html .= 							'<span class="fields-up">▲</span>
												<span class="fields-down">▼</span>
											</button>
										</div>
										<div class="x2board-fields-toggle">';
		$html .= 							'<button type="button" class="fields-remove" title="'.__('Remove', 'x2board').'">X</button>
										</div>
									</div>
									<div class="x2board-fields-content">';
		$html .=						'<input type="hidden" class="field_data field_type" value="'.esc_attr($item['field_type']).'">';
		$html .= 						'<input type="hidden" class="field_data field_label" value="'.esc_attr($item['field_label']).'">';
		if(isset($item['option_field'])){
			$html .= 					'<input type="hidden" class="field_data option_field" value="'.esc_attr($item['option_field']).'">';
		}
		$html .=					 	'<div class="attr-row">';
		$html .= 							'<label class="attr-name" for="'.$key.'_field_label">'.__('Field Label', 'x2board').'</label>
											<div class="attr-value">';
		$html .= 								'<input type="text" id="'.$key.'_field_label" class="field_data field_name" placeholder="'.esc_attr($item['field_label']).'">';
		$html .= 							'</div>
										</div>';
		if(isset($item['roles'])){
			$html .= 					'<div class="attr-row">';
			$html .= 						'<label class="attr-name" for="'.$key.'_roles">'.__('Whom to display', 'x2board').'</label>
											<div class="attr-value">';
			$html .= 							'<select id="'.$key.'_roles" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">
													<option value="all" selected>'.__('All', 'x2board').'</option>
													<option value="author">'.__('Loggedin user', 'x2board').'</option>
													<option value="roles">'.__('Choose below', 'x2board').'</option>
												</select>
												<div class="x2board-permission-read-roles-view x2board-hide">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false" checked' : '';
				$html .=							'<label><input type="checkbox" class="field_data roles_checkbox" value="'.$roles_key.'" '.$s_mandatory.'> '. _x($roles_value['name'], 'User role').'</label>';
			}
			$html .=							'</div>
											</div>
										</div>';
		}
		if(isset($item['secret_permission'])) {
			$html .=					'<div class="attr-row">';
			$html .=						'<label class="attr-name" for="'.$key.'_secret">'.__('Secret post', 'x2board').'</label>';
			$html .=						'<div class="attr-value">';
			$html .=							'<select id="'.$key.'_secret" class="field_data secret-roles" onchange="x2board_fields_permission_roles_view(this)">
													<option value="all">'.__('All', 'x2board').'</option>';
			$s_selected = $item['secret_permission'] == 'author' ? 'selected' : '';
			$html .=								'<option value="author" '.$s_selected.'  >'.__('Loggedin user', 'x2board').'</option>';
			$s_selected = $item['secret_permission'] == 'roles' ? 'selected' : '';
			$html .=								'<option value="roles" '.$s_selected.'>'.__('Choose below', 'x2board').'</option>
												</select>';
			$s_hide = $item['secret_permission'] != 'roles' ? 'x2board-hide' : '';
			$html .=							'<div class="x2board-permission-read-roles-view '.$s_hide.'">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false" checked' : '';
				$html .=							'<label><input type="checkbox" class="field_data secret_checkbox" value="'.$roles_key.'" '.$s_mandatory.'> '._x($roles_value['name'], 'User role').'</label>';
			}
			$html .=							'</div>
											</div>
										</div>';
		}
		if(isset($item['notice_permission'])) {
			$html .=					'<div class="attr-row">
											<label class="attr-name" for="'.$key.'-notice">'.__('Notice', 'x2board').'</label>';
			$html .=						'<div class="attr-value">';
			$html .=							'<select id="'.$key.'-notice" class="field_data notice-roles" onchange="x2board_fields_permission_roles_view(this)">
													<option value="all">'.__('All', 'x2board').'</option>';
			$s_selected = $item['notice_permission'] == 'author' ? 'selected' : '';
			$html .=								'<option value="author" '.$s_selected.'>'.__('Loggedin user', 'x2board').'</option>';
			$s_selected = $item['notice_permission'] == 'roles' ? 'selected' : '';
			$html .=								'<option value="roles" '.$s_selected.'>'.__('Choose below', 'x2board').'</option>
												</select>';
			$s_hide = $item['notice_permission'] != 'roles' ? 'x2board-hide' : '';
			$html .=							'<div class="x2board-permission-read-roles-view '.$s_hide.'">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
				$s_checked = ( $roles_key=='administrator' || in_array($roles_key, $item['notice'])) ? 'checked' : '';
				$html .=							'<label><input type="checkbox" class="field_data notice_checkbox" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
			}
			$html .=							'</div>
											</div>
										</div>';
		}
		if(isset($item['default_value'])) {
			$html .=					'<div class="attr-row">';
			$html .=						'<label class="attr-name" for="'.$key.'_default_value">'.__('Defaul value', 'x2board').'</label>
											<div class="attr-value">';
			if($item['field_type'] == 'search') {
				$html .=						'<select id="'.$key.'_default_value" class="field_data default_value">
													<option value="1">'.__('Title and content', 'x2board').'</option>
													<option value="2">'.__('Title (secret post)', 'x2board').'</option>
													<option value="3">'.__('Hide from search', 'x2board').'</option>
												</select>';
			}
			else {
				$html .=						'<input type="text" class="field_data default_value">';
			}
			$html .=						'</div>
										</div>';
		}
		if(isset($item['placeholder'])) {
			$html .=					'<div class="attr-row">
											<label class="attr-name" for="'.$key.'_placeholder">Placeholder</label>';
			$s_placeholder = (isset($item['placeholder']) && $item['placeholder']) ? $item['placeholder'] : '';
			$html .=						'<div class="attr-value"><input type="text" id="'.$key.'_placeholder" class="field_data placeholder" value="'.$s_placeholder.'"></div>
										</div>';
		}
		if(isset($item['description'])) {
			$html .=					'<div class="attr-row">
											<label class="attr-name" for="'.$key.'_description">설명</label>
											<div class="attr-value">
												<input type="text" id="'.$key.'_description" class="field_data field_description" value="'.$item['description'].'">
											</div>
										</div>';
		}
		if(isset($item['required']) || isset($item['show_document']) || isset($item['hidden'])) {
			$html .=					'<div class="attr-row">';
			if(isset($item['required'])) {
				$s_checked = $item['required'] ? 'checked' : '';
				$html .=					'<label>
												<input type="hidden" class="field_data required" value="">
												<input type="checkbox" class="field_data required" value="1" '.$s_checked.'>'.__('Required', 'x2board').'
											</label>';
			}
			if(isset($item['show_document'])) {
				$s_checked = $item['show_document'] ?  'checked' : '';
				$html .=					'<label>
												<input type="hidden" class="field_data show_document" value="">
												<input type="checkbox" class="field_data show_document" value="1" '.$s_checked.'>'.__('Display on post content', 'x2board').'
											</label>';
			}
			if(isset($item['hidden'])) {
				$s_checked = isset($item['show_document']) ?  'checked' : '';
				$html .=					'<label>
												<input type="hidden" class="field_data hidden" value="">
												<input type="checkbox" class="field_data hidden" value="1" '.$s_checked.'>'.__('Hiding', 'x2board').'
											</label>';
			}
			$html .=					'</div>';
							}
			$html .=					'</div>
									</li>';
//////////////////////////
	}
	unset($a_user_default_fields_unchosen);
	unset($a_user_default_fields);
	$html .= 					'</ul>
							</li>
							<li class="x2board-fields-extension left">';
	$html .=	 			'<button type="button" class="x2board-fields-header">';
	$html .=	 			__('Extended fields', 'x2board');
	$html .= 					'<span class="fields-up">▲</span>
								<span class="fields-down">▼</span>
							</button>
							<ul class="x2board-fields-list x2board-fields-content">';
	
	if($a_extended_fields) {
		foreach($a_extended_fields as $key=>$item) {
////////////////////////////////
			$html .=			'<li class="extends '.$key.'">';
			// $html .= 				'<input type="hidden" value="'.$item['class'].'" class="field_data class">';
			// $s_value = isset($item['close_button']) ? $item['close_button'] : '';
			// $html .= 				'<input type="hidden" class="field_data close_button" value="'.$s_value.'">';
			$html .= 				'<div class="x2board-extends-fields">
										<div class="x2board-fields-title toggle x2board-field-handle">
											<button type="button">';
			$html .=					 	esc_html($item['field_label']);
			$html .=				 		'<span class="fields-up">▲</span>
											<span class="fields-down">▼</span>
											</button>
										</div>
										<div class="x2board-fields-toggle">
											<button type="button" class="fields-remove" title="'. __('Remove', 'x2board').'">X</button>
										</div>
									</div>
									<div class="x2board-fields-content">';
			$html .= 					'<input type="hidden" class="field_data field_type" value="'.esc_attr($item['field_type']).'">';
			$html .= 					'<input type="hidden" class="field_data field_label" value="'.esc_attr($item['field_label']).'">';
			if( $o_post_admin_model->is_multiline_fields($item['field_type']) ) {
				$html .=				 '<div class="attr-row">
											<label class="attr-name">'.__('Field label', 'x2board').'</label>
											<div class="attr-value"><input type="text" class="field_data field_name" placeholder="'.esc_attr($item['field_label']).'"></div>
										</div>';
				if(isset($item['meta_key'])){
					$html .= 			'<div class="attr-row">
											<label class="attr-name">'.__('Meta key', 'x2board').'</label>
											<div class="attr-value"><input type="text" class="field_data meta_key" placeholder="meta_key"></div>
											<div class="description">※ 입력하지 않으면 자동으로 설정되며 저장 이후에는 값을 변경할 수 없습니다.</div>
										</div>';
				}
				$html .= '<div class="attr-row">
							<label class="attr-name">'.$item['field_label'].'</label>
							<div class="attr-value">';
				if($item['field_type'] == 'html'){
					$html .= '<textarea class="field_data html" rows="5"></textarea>';
				}
				elseif($item['field_type'] == 'shortcode'){
					$html .= '<textarea class="field_data shortcode" rows="5"></textarea>';
				}
				$html .= '</div>
						</div>';
				if(isset($item['show_document'])) {
					$html .= '<input type="hidden" class="field_data show_document" value="">
							<label><input type="checkbox" class="field_data show_document" value="1">'.__('Display on post content', 'x2board').'</label>';
				}
			}
			else {
				$html .= '<div class="attr-row">
							<label class="attr-name">'.__('Field label', 'x2board').'</label>
							<div class="attr-value"><input type="text" class="field_data field_name" placeholder="'.esc_attr($item['field_label']).'"></div>
						</div>';
				if(isset($item['meta_key'])) {
					$html .= '<div class="attr-row">
								<label class="attr-name">'.__('Meta key', 'x2board').'</label>
								<div class="attr-value"><input type="text" class="field_data meta_key" placeholder="meta_key"></div>
								<div class="description">※ 입력하지 않으면 자동으로 설정되며 저장 이후에는 값을 변경할 수 없습니다.</div>
							</div>';
				}
				if(isset($item['row'])) {
					$uniq_id = 'php_'.uniqid();
					$html .= '<div class="x2board-radio-reset">
								<div class="attr-row option-wrap">
									<div class="attr-name option">
										<label for="'.$uniq_id.'">'.__('Label', 'x2board').'</label>
									</div>
									<div class="attr-value">
										<input type="text" id="'.$uniq_id.'" class="field_data option_label">
										<button type="button" class="'.$item['field_type'].'" onclick="add_option(this)">+</button>
										<button type="button" class="'.$item['field_type'].'" onclick="remove_option(this)">-</button>
										<label>';
					if($item['field_type'] == 'checkbox') {
						$html .= 			'<input type="checkbox" name="'.$item['field_type'].'" class="field_data default_value" value="1">';
					}
					else {
						$html .= 			'<input type="radio" name="'.$item['field_type'].'" class="field_data default_value" value="1">';
					}
					$html .= 			__('Default value', 'x2board');
					$html .= 			'</label>';
					if($item['field_type'] == 'radio' || $item['field_type'] == 'select') {
						$html .= 		'<span style="vertical-align:middle;cursor:pointer;" onclick="x2board_radio_reset(this)">· '.__('Reset', 'x2board').'</span>';
					}
					$html .= 		'</div>
									</div>
								</div>';
				}
				if(isset($item['roles'])) {
					$html .=	 '<div class="attr-row">
									<label class="attr-name">'.__('Whom to diplay', 'x2board').'</label>
									<div class="attr-value">
										<select class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">
											<option value="all" selected>'.__('All', 'x2board').'</option>
											<option value="author">'.__('Loggedin user', 'x2board').'</option>
											<option value="roles">'.__('Choose below', 'x2board').'</option>
										</select>
										<div class="x2board-permission-read-roles-view x2board-hide">';
					foreach(get_editable_roles() as $roles_key=>$roles_value) {
						$s_mandatory = $roles_key=='administrator' ? 'onclick="return false" checked' : '';
						$html .= '	<label><input type="checkbox" class="field_data roles_checkbox" value="'.$roles_key.'" '.$s_mandatory.'  > '. _x($roles_value['name'], 'User role').'</label>';
					}
					$html .=	'</div>
								</div>
							</div>';
				}
				if(isset($item['default_value']) && !isset($item['row'])) {
					$html .= '<div class="attr-row">
								<label class="attr-name">'.__('Default value', 'x2board').'</label>
								<div class="attr-value"><input type="text" class="field_data default_value"></div>
							</div>';
				}
				if(isset($item['placeholder'])) {
					$html .= '<div class="attr-row">
								<label class="attr-name">Placeholder</label>
								<div class="attr-value"><input type="text" class="field_data placeholder"></div>
							</div>';
				}
				if(isset($item['description'])) {
					$html .= '<div class="attr-row">
								<label class="attr-name">'.__('Description', 'x2board').'</label>
								<div class="attr-value">
									<input type="text" class="field_data field_description" value="'.$item['description'].'">';
					$html .= 	'</div>
							</div>';
				}
				if(isset($item['custom_class'])) {
					$html .= '<div class="attr-row">
								<label class="attr-name">'.__('CSS class', 'x2board').'</label>
								<div class="attr-value"><input type="text" class="field_data custom_class"></div>
							</div>';
				}
				$html .= '<div class="attr-row">';
				if(isset($item['required'])) {
					$html .= '<input type="hidden" class="field_data required" value="">
							<label><input type="checkbox" class="field_data required" value="1">'.__('Required', 'x2board').'</label>';
				}
				if(isset($item['show_document'])) {
					$html .= '<input type="hidden" class="field_data show_document" value="">
							<label><input type="checkbox" class="field_data show_document" value="1">'.__('Display on post content', 'x2board').'</label>';
				}
				if(isset($item['hidden'])) {
					$s_hidden_filed_notifier = $item['field_type'] == 'text' ? '(hidden)' : '';
					$html .= '<input type="hidden" class="field_data hidden" value="">
							<label><input type="checkbox" class="field_data hidden" value="1">'.__('Hiding', 'x2board').''.$s_hidden_filed_notifier.'</label>';
				}
				$html .= '</div>';
			}
			$html .= '</div>
					</li>';
////////////////////////////////
		}
	}
	
	$html .= 			'</ul>
					</li>
				</ul>
			</div>
			<div class="x2board-fields-right">
				<div class="x2board-fields x2board-sortable-fields">
					<h3 class="x2board-fields-h3">입력 필드 구조</h3>
					<div class="description">왼쪽 열에서 필드를 드래그 앤 드롭으로 추가하세요.</div>
					<ul class="x2board-skin-fields x2board-fields-sortable connected-sortable">';
	$a_user_define_fields = $o_post_admin_model->get_user_define_fields();

	foreach($a_user_define_fields as $key=>$item) {
		$s_field_type = $item['field_type'];
		$meta_key = isset($item['meta_key']) && $item['meta_key'] ? $item['meta_key'] : $key;
		$field_label = $o_post_admin_model->get_field_label($item);
		$html .= 		'<li class="'.$o_post_admin_model->is_default_field($item['field_type']).' '.esc_attr($meta_key).' '.esc_attr($item['field_type']).'">';
		$html .= 			'<input type="hidden" class="parent_id" value="'.esc_attr($meta_key).'">';
		// $html .= 			'<input type="hidden" name="fields['.esc_attr($meta_key).'][class]" class="field_data class" value="'.$item['class'].'">';
		// $s_clos_btn = isset($item['close_button']) ? $item['close_button'] : '';
		// $html .= 			'<input type="hidden" name="fields['.esc_attr($meta_key).'][close_button]" class="field_data close_button" value="'.$s_clos_btn.'">';
		$html .= 				'<div class="x2board-saved-fields-header">';
// var_dump($a_all_fields[$s_field_type]['close_button']);		
		// $s_clos_btn = !(isset($a_all_fields[$s_field_type]['close_button']) && $a_all_fields[$s_field_type]['close_button'] == 'yes') ? 'only-toggle' : '';
		$html .= 					'<div class="x2board-fields-title toggle x2board-field-handle">
										<button type="button">';
		$html .= 							esc_html($field_label);
		if(isset($item['field_name']) && $item['field_name']) {
			$html .= ' : ' . esc_html($item['field_name']);
		}
		$html .= 							'<span class="fields-up">▲</span>
											<span class="fields-down">▼</span>
										</button>
									</div>';
		if(isset($a_all_fields[$s_field_type]['close_button']) && $a_all_fields[$s_field_type]['close_button'] == 'yes') {
			$html .= 				'<div class="x2board-fields-toggle">
										<button type="button" class="fields-remove" title="'.__('Remove', 'x2board').'">X</button>
									</div>';
		}
		$html .= 				'</div>
								<div class="x2board-fields-content">';
		$html .= 					'<input type="hidden" name="fields['.esc_attr($meta_key).'][field_type]" class="field_data field_type" value="'.esc_attr($item['field_type']).'">';
		// 입력란의 [필드 레이블]에 해당하는 변수가 field_name 변수, field_name이 공란일 때 [필드 레이블]의 기본값
		$html .= 					'<input type="hidden" name="fields['.esc_attr($meta_key).'][field_label]" class="field_data field_label" value="'.esc_attr($field_label).'">';
		if(isset($item['option_field'])) {
			$html .= 				'<input type="hidden" name="fields['.esc_attr($meta_key).'][option_field]" class="field_data option_field" value="1">';
		}
		if(isset($item['hidden'])) {
			$html .= 				'<input type="text" name="fields['.esc_attr($meta_key).'][hidden]" class="field_data hidden" value="1">';
		}
		if($item['field_type'] == 'title') {
			$html .= 				'<div class="attr-row">
										<div class="description">※ 제목은 항상 필수로 입력해야 합니다.</div>
										<input type="hidden" name="fields[title][permission]" value="all">
									</div>';
		}							
		elseif($item['field_type'] == 'author') {
			$html .= 				'<div class="attr-row">
										<div class="description">※ 비회원은 비밀번호를 항상 필수로 입력해야 합니다.</div>
									</div>';
		}
		elseif($item['field_type'] == 'attach') {
			$html .= 				'<div class="attr-row">
										<label class="attr-name" for="max_each_file_size_mb">첨부파일 당 최대 용량(Mb)</label>
										<div class="attr-value">';
			$html .= 						'<input type="text" id="max_each_file_size_mb" name="max_each_file_size_mb" value="'.esc_attr($meta->max_each_file_size_mb ).'" placeholder="Mb 단위 입력">
										</div>
									</div>';
			$html .= 				'<div class="attr-row">
										<label class="attr-name" for="max_attached_count">게시글당 최대 첨부파일 개수</label>
										<div class="attr-value">
											<select name="max_attached_count" id="max_attached_count">
												<option value="">없음</option>
												<option value="1" selected>1개</option>
											</select>
											<!-- <p class="description">일부 스킨에서는 적용되지 않습니다.</p> -->
										</div>
									</div>';
		}
		elseif($item['field_type'] == 'address') {
			$html .= 				'<div class="attr-row">
										<div class="description">※ 개인 정보이므로 게시글 본문에 표시를 선택하면 관리자에게 계속, 글 작성자에게 세션 시간 동안만 표시됩니다.</div>
									</div>';
		}
									
		if(isset($item['field_name'])) {
			$html .= 				'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'-field-label">필드 레이블</label>
										<div class="attr-value">
											<input type="text" id="'.esc_attr($meta_key).'-field-label" name="fields['.esc_attr($meta_key).'][field_name]" class="field_data field_name" value="'.esc_attr($item['field_name']).'" placeholder="'.esc_attr($field_label).'">
										</div>
									</div>';
		}
		$s_readonly = isset($meta_key) ? 'readonly' : '';
		$html .= 					'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'">메타키</label>
										<div class="attr-value">
											<input type="text" name="fields['.esc_attr($meta_key).'][meta_key]" id="'.esc_attr($meta_key).'" class="field_data meta_key" value="'.$meta_key.'" '.$s_readonly.' placeholder="meta_key">
										</div>
										<div class="description">※ 입력하지 않으면 자동으로 설정되며 저장 이후에는 값을 변경할 수 없습니다.</div>
									</div>';
		if(isset($item['row'])) {
			if($o_post_admin_model->is_value_exists($item['row'])) {
				$already_echo = false;
				$html .= 			'<div class="x2board-radio-reset">';
				foreach($item['row'] as $option_key=>$option_value) {
					if(isset($option_value['label']) && $option_value['label']) {
						$html .= 		'<div class="attr-row option-wrap">
											<div class="attr-name option">
												<label for="'.esc_attr($option_key).'_label">라벨</label>
											</div>
											<div class="attr-value">
												<input type="text" id="'.esc_attr($option_key).'_label" name="fields['.esc_attr($meta_key).'][row]['.esc_attr($option_key).'][label]" id="'.esc_attr($meta_key).'" class="field_data option_label" value="'.esc_attr($option_value['label']).'">
												<button type="button" class="'.esc_attr($item['field_type']).'" onclick="add_option(this)">+</button>
												<button type="button" class="'.esc_attr($item['field_type']).'" onclick="remove_option(this)">-</button>
												<label>';
						if($item['field_type'] == 'checkbox') {
							$s_default_value = (isset($option_value['default_value']) && $option_value['default_value'] == '1') ? 'checked' : '';
							$html .= 			'<input type="checkbox" name="fields['.esc_attr($meta_key).'][row]['.esc_attr($option_key).'][default_value]" class="field_data default_value" '.$s_default_value.' value="1">';
						}
						else {
							$s_checked = (isset($item['default_value']) && $item['default_value']==$option_key) ? 'checked' : '';
							$html .= 			'<input type="radio" name="fields['.esc_attr($meta_key).'][default_value]" class="field_data default_value"        value="'.esc_attr($option_key).'">';
						}
						$html .= 				'기본값
												</label>';
						if($item['field_type'] == 'radio' || $item['field_type'] == 'select') {
							if(!$already_echo) {
								$html .= 		'<span style="vertical-align:middle;cursor:pointer;" onclick="x2board_radio_reset(this)">· '.__('Reset', 'x2board').'</span>';
								$already_echo=true;
							}
						}
						$html .= 			'</div>
										</div>';
					}
				}
				$html .= '</div>';
			}
			else {
				$uniq_id = 'php_'.uniqid();
				$html .= '<div class="attr-row option-wrap">
							<div class="attr-name option">
								<label for="'.esc_attr($meta_key).'_label">라벨</label>
							</div>
							<div class="attr-value">
								<input type="text" id="'.esc_attr($meta_key).'_label" name="fields['.esc_attr($meta_key).'][row]['.$uniq_id.'][label]" class="field_data option_label" value="">
								<button type="button" class="'.esc_attr($item['field_type']).'" onclick="add_option(this)">+</button>
								<button type="button" class="'.esc_attr($item['field_type']).'" onclick="remove_option(this)">-</button>
								<label>';
				if($item['field_type'] == 'checkbox') {
					$html .= '<input type="checkbox" name="fields['.esc_attr($meta_key).'][row]['.$uniq_id.'][default_value]" class="field_data default_value" value="">';
				}
				else {
					$html .= '<input type="radio" name="fields['.esc_attr($meta_key).'][default_value]" class="field_data default_value" value="">';
				}
				$html .= 	'기본값
								</label>
							</div>
						</div>';
			}
		}
		if(isset($item['permission']) && $item['field_type'] != 'title') {
			$html .= 	'<div class="attr-row">
							<label class="attr-name" for="'.esc_attr($meta_key).'_permission">표시할 권한</label>
							<div class="attr-value">';
			if($item['field_type'] == 'author') {
				$html .= 		'<select id="'.esc_attr($meta_key).'_permission" name="fields['.esc_attr($meta_key).'][permission]" class="field_data roles">
									<option value="">비회원일때만 표시</option>';
				$s_selected = $item['permission'] == 'always_visible' ? 'selected' : '';
				$html .= 			'<option value="always_visible" '.$s_selected.'  >항상 표시</option>';
				$s_selected = $item['permission'] == 'always_hide' ? 'selected' : '';
				$html .= 			'<option value="always_hide" '.$s_selected.' >항상 숨김</option>
								</select>';
			}
			else {
				$html .= 		'<select id="'.esc_attr($meta_key).'_permission" name="fields['.esc_attr($meta_key).'][permission]" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">';
				$s_selected = $item['permission'] == 'all' ? 'selected' : '';
				$html .= 			'<option value="all" '.$s_selected.' >제한없음</option>';
				$s_selected = $item['permission'] == 'author' ? 'selected' : '';
				$html .= 			'<option value="author" '.$s_selected.'>로그인 사용자</option>';
				$s_selected = $item['permission'] == 'roles' ? 'selected' : '';
				$html .=	 		'<option value="roles" '.$s_selected.'>직접선택</option>
								</select>';
				$s_hide = $item['permission'] != 'roles' ? 'x2board-hide' : '';
				$html .= 		'<div class="x2board-permission-read-roles-view '.$s_hide.'">';
				foreach(get_editable_roles() as $roles_key=>$roles_value) {
					$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
					if( isset($item['roles'])) {
						$s_checked = ($roles_key=='administrator' || in_array($roles_key, $item['roles'])) ? 'checked' : '';
					}
					else {
						$s_checked = '';
					}
					$html .= 		'<label><input type="checkbox" name="fields['.esc_attr($meta_key).'][roles][]" class="field_data" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '._x($roles_value['name'], 'User role').'</label>';
				}
				$html .= 		'</div>';
			}
			$html .= 		'</div>
						</div>';
		}
		if(isset($item['secret_permission'])) {
			$html .= 	'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_secret">비밀글</label>
										<div class="attr-value">
											<select id="'.esc_attr($meta_key).'_secret" name="fields[option][secret_permission]" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">';
			$s_selected = $item['secret_permission'] == 'all' ? 'selected' : '';
			$html .= 							'<option value="all" '.$s_selected.'>제한없음</option>';
			$s_selected = $item['secret_permission'] == 'author' ? 'selected' : '';
			$html .= 							'<option value="author" '.$s_selected.'>로그인 사용자</option>';
			$s_selected = $item['secret_permission'] == 'roles' ? 'selected' : '';
			$html .= 							'<option value="roles" '.$s_selected.'>직접선택</option>';
			$html .= 						'</select>';
			$s_hile = $item['secret_permission'] != 'roles' ? 'x2board-hide' : '';
			$html .= 						'<div class="x2board-permission-read-roles-view '.$s_hile.'">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
				$s_checked = ($roles_key=='administrator' || in_array($roles_key, $item['secret'])) ? 'checked' : '';
				$html .= 						'<label><input type="checkbox" name="fields[option][secret][]" class="field_data" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
			}
			$html .=	 					'</div>
										</div>
									</div>';
		}
		if(isset($item['notice_permission'])) {
			$html .=	 			'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_notice">공지사항</label>
										<div class="attr-value">
											<select id="'.esc_attr($meta_key).'_notice" name="fields[option][notice_permission]" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">';
			$s_selected = $item['notice_permission'] == 'all' ? 'selected' : '';
			$html .=	 						'<option value="all" '.$s_selected.'>제한없음</option>';
			$s_selected = $item['notice_permission'] == 'author' ? 'selected' : '';
			$html .=	 						'<option value="author" '.$s_selected.'>로그인 사용자</option>';
			$s_selected = $item['notice_permission'] == 'roles' ? 'selected' : '';
			$html .=	 						'<option value="roles" '.$s_selected.'>직접선택</option>
											</select>';
			$s_hide = $item['notice_permission'] != 'roles' ? 'x2board-hide' : '';
			$html .=	 					'<div class="x2board-permission-read-roles-view '.$s_hide.' ">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
				$s_checked = ($roles_key=='administrator' || in_array($roles_key, $item['notice'])) ? 'checked' : '';
				$html .=	 					'<label><input type="checkbox" name="fields[option][notice][]" class="field_data" value="'.$roles_key.'" '.$s_mandatory.' '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
			}
			$html .=	 					'</div>
										</div>
									</div>';
		}
		if(isset($item['allow_comment_permission'])) {
			$html .=	 			'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_allow_comment">개별글 댓글 허용</label>
										<div class="attr-value">
											<select id="'.esc_attr($meta_key).'_allow_comment" name="fields[option][allow_comment_permission]" class="field_data roles" onchange="x2board_fields_permission_roles_view(this)">';
			$s_selected = $item['allow_comment_permission'] == 'all' ? 'selected' : '';
			$html .=	 						'<option value="all" '.$s_selected.' >제한없음</option>';
			$s_selected = $item['allow_comment_permission'] == 'author' ? 'selected' : '';
			$html .=	 						'<option value="author" '.$s_selected.'>로그인 사용자</option>';
			$s_selected = $item['allow_comment_permission'] == 'roles' ? 'selected' : '';
			$html .=	 						'<option value="roles" '.$s_selected.'>직접선택</option>';
			$html .=	 					'</select>';
			$s_hide = $item['allow_comment_permission'] != 'roles' ? 'x2board-hide' : '';
			$html .=	 					'<div class="x2board-permission-read-roles-view '.$s_hide.' ">';
			foreach(get_editable_roles() as $roles_key=>$roles_value) {
				$s_mandatory = $roles_key=='administrator' ? 'onclick="return false"' : '';
				$s_checked = ($roles_key=='administrator' || in_array($roles_key, $item['allow_comment'])) ? 'checked' : '';
				$html .=	 					'<label><input type="checkbox" name="fields[option][allow_comment][]" class="field_data" value="'.$roles_key.'" '.$s_mandatory.'  '.$s_checked.'> '. _x($roles_value['name'], 'User role').'</label>';
			}
			$html .=	 					'</div>
										</div>
									</div>';
		}
		if(isset($item['default_value']) && $item['field_type'] != 'checkbox' && $item['field_type'] != 'radio' && $item['field_type'] != 'select') {
			$html .=	 			'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_default_value">기본값</label>
										<div class="attr-value">';
			if($item['field_type'] == 'search') {
				$html .=	 				'<select id="'.esc_attr($meta_key).'_default_value" name="fields[search][default_value]" class="field_data default_value">';
				$s_selected = $item['default_value'] == '1' ? 'selected' : '';
				$html .=			 			'<option value="1" '.$s_selected.'  >제목과 내용 검색허용</option>';
				$s_selected = $item['default_value'] == '2' ? 'selected' : '';
				$html .=			 			'<option value="2" '.$s_selected.'>제목만 검색허용 (비밀글)</option>';
				$s_selected = $item['default_value'] == '3' ? 'selected' : '';
				$html .=			 			'<option value="3" '.$s_selected.'>통합검색 제외</option>
											</select>';
			}
			else {
				$html .=			 		'<input type="text" id="'.esc_attr($meta_key).'_default_value" name="fields['.esc_attr($meta_key).'][default_value]" class="field_data default_value" value="'.$item['default_value'].'">';
			}
			$html .=			 		'</div>
									</div>';
		}
		if($o_post_admin_model->is_multiline_fields($item['field_type'])) {
			$html .=			 	'<div class="attr-row">';
			if($item['field_type'] == 'html') {
				$html .=			 	'<label class="attr-name" for="'.esc_attr($meta_key).'_html">'.$item['field_name'].'</label>
										<div class="attr-value">
											<textarea id="'.esc_attr($meta_key).'_html" name="fields['.esc_attr($meta_key).'][html]" class="field_data html" rows="5">'.$item['html'].'</textarea>
										</div>';
			}
			elseif($item['field_type'] == 'shortcode') {
				$html .=			 	'<label class="attr-name" for="'.esc_attr($meta_key).'_shortcode">'.$item['field_name'].'</label>
										<div class="attr-value">
											<textarea id="'.esc_attr($meta_key).'_shortcode" name="fields['.esc_attr($meta_key).'][shortcode]" class="field_data shortcode" rows="5">'.$item['shortcode'].'</textarea>
										</div>';
			}
			$html .=			 	'</div>';
		}
		if(isset($item['placeholder'])) {
			$html .=			 	'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_placeholder">Placeholder</label>
										<div class="attr-value"><input type="text" id="'.esc_attr($meta_key).'_placeholder" name="fields['.esc_attr($meta_key).'][placeholder]" class="field_data placeholder" value="'.esc_attr($item['placeholder']).'"></div>
									</div>';
		}
		if(isset($item['description'])) {
			$html .=			 	'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_description">설명</label>
										<div class="attr-value">
											<input type="text" id="'.esc_attr($meta_key).'_description" name="fields['.esc_attr($meta_key).'][description]" class="field_data field_description" value="'.esc_attr($item['description']).'">
										</div>
									</div>';
		}
		if(isset($item['custom_class'])) {
			$html .=			 	'<div class="attr-row">
										<label class="attr-name" for="'.esc_attr($meta_key).'_custom_class">CSS 클래스</label>
										<div class="attr-value"><input type="text" id="'.esc_attr($meta_key).'_custom_class" name="fields['.esc_attr($meta_key).'][custom_class]" class="field_data custom_class" value="'.esc_attr($item['custom_class']).'"></div>
									</div>';
		}
		if(isset($item['show_document']) && !$o_post_admin_model->is_multiline_fields($item['field_type'])) {
			$html .=			 	'<div class="attr-row">
										<label class="attr-name">스킨 출력 예제</label>
										<div class="attr-value">
											<div class="example">';
			if($o_post_admin_model->is_default_field($item['field_type']) == 'extends' || (isset($item['option_field']) && $item['option_field'])) {
				if($item['field_type'] == 'file') {
					$print_code = '<?php echo $content->attach->{\'' . $meta_key . '\'}[1]?>';
				}
				else if($item['field_type'] == 'checkbox') {
					$print_code = '<?php echo implode(\', \', $content->option->{\'' . $meta_key . '\'})?>';
				}
				else {
					$print_code = '<?php echo $content->option->{\'' . $meta_key . '\'}?>';
				}
			}
			$html .= esc_html($print_code);
			$html .=			 		'</div>
										</div>
									</div>';
		}
		if(isset($item['required']) || isset($item['show_document']) || isset($item['hidden'])) {
			$html .=			 	'<div class="attr-row">';
			if(isset($item['required'])){
				$s_checked = $item['required'] ? 'checked' : '';
				$html .=			 	'<label>
											<input type="hidden" name="fields['.esc_attr($meta_key).'][required]" class="field_data required" value="">
											<input type="checkbox" name="fields['.esc_attr($meta_key).'][required]" class="field_data required" value="1" '.$s_checked.' >필수
										</label>';
			}
			if(isset($item['show_document'])) {
				$s_checked = $item['show_document'] ? 'checked' : '';
				$html .=			 	'<label>
											<input type="hidden" name="fields['.esc_attr($meta_key).'][show_document]" class="field_data show_document" value="">
											<input type="checkbox" name="fields['.esc_attr($meta_key).'][show_document]" class="field_data show_document" value="1" '.$s_checked.' >게시글 본문에 표시
										</label>';
			}
			if(isset($item['hidden'])) {
				$s_checked = $item['hidden'] ? 'checked' : '';
				$s_hidden = $item['field_type'] == 'text' ? '(hidden)' : '';
				$html .=			 	'<label>
											<input type="hidden" name="fields['.esc_attr($meta_key).'][hidden]" class="field_data hidden" value="">
											<input type="checkbox" name="fields['.esc_attr($meta_key).'][hidden]" class="field_data hidden" value="1" '.$s_checked.' >숨김'.$s_hidden.'
										</label>';
			}
			$html .=			 	'</div>';
		}
		$html .=			 	'</div>
							</li>';
	}
	unset($a_extended_fields);
	unset($a_user_define_fields);
	$html .= 		'</ul>
					<div class="description"><button type="button" class="button button-small" onclick="x2board_skin_fields_reset()">설정 초기화</button></div>
				</div>
			</div>
		</div>';
	
	unset($o_post_admin_model);
	echo apply_filters( 'x2b_after_setting_output', $html, $args );
}
*/
//////////////////////////////////////

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
// 		$cropped = $option['crop'] ? __( ' cropped', 'x2board' ) : '';

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
// 		$html .= sprintf( '<img src="%1$s" style="max-width:200px" title="%2$s" alt="%2$s" />', esc_attr( $thumb_default ), esc_html__( 'Default thumbnail', 'x2board' ) );
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
// 		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles or Text only in the Styles tab', 'x2board' ) . '</span>';
// 	}

// 	if ( in_array( $crp_styles, array( 'rounded_thumbs', 'thumbs_grid', 'text_only' ), true ) && 'post_thumb_op' === $args['id'] ) {
// 		$html .= '<span style="color:red">' . esc_html__( 'This option cannot be changed because of the selected related posts style. To modify this option, you will need to select No styles in the Styles tab', 'x2board' ) . '</span>';
// 	}

// 	return $html;
// }
// add_filter( 'x2b_after_setting_output', 'x2b_styles_messages', 10, 2 );