<?php
/**
 * Register settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @package x2board
 */
namespace X2board\Includes\Admin\Tpl;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * load settings function
 *
 * @return $a_board_settings
 * return setting title together
 */
function x2b_load_settings( $board_id ) {
	// $o_board_info ) {
	$o_rst                                = new \stdClass();
	$o_rst->b_ok                          = false;
	$o_rst->a_board_settings              = null;
	$o_rst->s_x2b_setting_board_title     = X2B_DOMAIN . '_settings_board_' . $board_id;
	$o_rst->s_x2b_setting_skin_vars_title = X2B_DOMAIN . '_settings_skin_vars_' . $board_id;

	$n_board_id = intval( sanitize_text_field( $board_id ) );
	if ( intval( $n_board_id ) == 0 ) {
		return $o_rst;
	}

	// check if the requested WP page is x2board tagged
	$o_wp_page = get_post( $n_board_id );
	if ( $o_wp_page->post_content != X2B_PAGE_IDENTIFIER ) {
		unset( $o_wp_page );
		return $o_rst;
	}

	$a_board_settings = get_option( $o_rst->s_x2b_setting_board_title );
	if ( $a_board_settings === false ) {
		$o_rst->a_board_settings = array();
	}

	// insert custom router setting for a pretty post URL
	$a_board_rewrite_settings              = get_option( X2B_REWRITE_OPTION_TITLE );
	$a_board_settings['board_use_rewrite'] = isset( $a_board_rewrite_settings[ $board_id ] ) ? 'Y' : 'N';
	unset( $a_board_rewrite_settings );

	// load skin vars into $a_board_settings for an admin configuration UX
	$a_skin_vars = get_option( $o_rst->s_x2b_setting_skin_vars_title );
	if ( $a_skin_vars !== false ) {
		foreach ( $a_skin_vars as $s_var_id => $o_val ) {
			$a_board_settings[ X2B_SKIN_VAR_IDENTIFIER . $s_var_id ] = $o_val;
		}
	}
	// load skin vars into $o_rst for a guest skin
	$o_rst->a_skin_vars = $a_skin_vars;
	unset( $a_skin_vars );

	// insert ['board_title'] and ['wp_page_title'] into $a_board_settings
	global $wpdb;
	$board_id                          = esc_sql( $n_board_id );
	$board_title                       = $wpdb->get_var( "SELECT `board_title` FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$board_id'" );
	$a_board_settings['board_title']   = $board_title;
	$a_board_settings['wp_page_title'] = $o_wp_page->post_title;
	unset( $o_wp_page );

	// build up board settings into $a_board_settings
	foreach ( \X2board\Includes\Admin\Tpl\x2b_get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			// ignore header type field
			if ( 'header' === $option['type'] ) {
				continue;
			}
			// no change if a value comes from get_option()
			if ( isset( $a_board_settings[ $option['id'] ] ) ) {
				continue;
			}
			// When checkbox is set to true, set this to 1.
			if ( 'checkbox' === $option['type'] && ! empty( $option['options'] ) ) {
				$a_board_settings[ $option['id'] ] = 1;
			} else {
				$a_board_settings[ $option['id'] ] = 0;
			}
			// If an option is set.   'csv', 'numbercsv', 'posttypes', 'css'
			if ( in_array( $option['type'], array( 'textarea', 'text', 'number' ), true ) && isset( $option['options'] ) ) {
				$a_board_settings[ $option['id'] ] = $option['options'];
			}
			// , 'radiodesc', 'thumbsizes'
			if ( in_array( $option['type'], array( 'multicheck', 'grantselect', 'radio', 'select' ), true ) && isset( $option['default'] ) ) {
				$a_board_settings[ $option['id'] ] = $option['default'];
			}
		}
	}

	// load textdomain for skin_vars
	$s_board_skin = isset( $a_board_settings['board_skin'] ) ? $a_board_settings['board_skin'] : 'default';
	// third parameter should be relative path to WP_PLUGIN_DIR
	load_plugin_textdomain( X2B_DOMAIN, false, X2B_DOMAIN . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . X2B_MODULES_NAME . DIRECTORY_SEPARATOR . 'board' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . $s_board_skin . DIRECTORY_SEPARATOR . 'lang' );

	$o_rst->b_ok             = true;
	$o_rst->a_board_settings = $a_board_settings;
	return $o_rst;
}

/**
 * Register settings function
 *
 * @return void
 */
function x2b_register_settings() {

	// First, we write the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;
	if ( isset( $_GET['board_id'] ) ) {  // update board configuration
		$o_rst = x2b_load_settings( $_GET['board_id'] );
		if ( false === $o_rst->b_ok ) { // for creating a new board
			$A_X2B_ADMIN_BOARD_SETTINGS = x2b_settings_defaults();
		} else {  // for updating a old board
			$A_X2B_ADMIN_BOARD_SETTINGS = $o_rst->a_board_settings;
		}
	} else {  // create new board
		$_GET['board_id']           = null;  // prevent PHP Notice:  Undefined index: board_id
		$A_X2B_ADMIN_BOARD_SETTINGS = x2b_settings_defaults();
	}
	unset( $o_rst );

	// will be executed in \includes\admin\tpl\settings-page.php::x2b_options_page()
	foreach ( x2b_get_registered_settings() as $section => $settings ) {
		add_settings_section(
			X2B_DOMAIN . '_settings_' . $section, // ID used to identify this section and with which to register options, e.g. x2b_settings_general.
			__return_empty_string(), // No title, we will handle this via a separate function.
			'__return_false', // No callback function needed. We'll process this separately.
			X2B_DOMAIN . '_settings_' . $section  // Page on which these options will be added.
		);

		foreach ( $settings as $setting ) {
			$args = wp_parse_args(
				$setting,
				array(
					'section'          => $section,
					'id'               => null,
					'name'             => '',
					'desc'             => '',
					'type'             => null,
					'options'          => '',
					'max'              => null,
					'min'              => null,
					'step'             => null,
					'size'             => null,
					'field_class'      => '',
					'field_attributes' => '',
					'placeholder'      => '',
				)
			);
			add_settings_field(
				X2B_DOMAIN . '_settings[' . $args['id'] . ']', // ID of the settings field. We save it within the x2b_settings array.
				$args['name'],     // Label of the setting.
				function_exists( '\X2board\Includes\Admin\Tpl\x2b_' . $args['type'] . '_callback' ) ?
								'\X2board\Includes\Admin\Tpl\x2b_' . $args['type'] . '_callback' :
								'X2board\Includes\Admin\Tpl\x2b_missing_callback', // Function to handle the setting.
				X2B_DOMAIN . '_settings_' . $section,    // Page to display the setting. In our case it is the section as defined above.
				X2B_DOMAIN . '_settings_' . $section,    // Name of the section.
				$args
			);
		}
	}
	// Register the settings into the options table.
	register_setting(
		X2B_DOMAIN . '_settings',
		X2B_DOMAIN . '_settings',
		array(
			'sanitize_callback' => X2B_DOMAIN . '_settings_sanitize',
		)
	);
}


/**
 * Default settings.
 *
 * @return array Default settings
 */
function x2b_settings_defaults() {

	$options = array();

	// Populate some default values.
	foreach ( x2b_get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			// When checkbox is set to true, set this to 1.
			if ( 'checkbox' === $option['type'] && ! empty( $option['options'] ) ) {
				$options[ $option['id'] ] = 1;
			} else {
				$options[ $option['id'] ] = 0;
			}
			// If an option is set.
			// 'csv', 'numbercsv', 'posttypes', 'css',
			if ( in_array( $option['type'], array( 'textarea', 'text', 'number' ), true ) && isset( $option['options'] ) ) {
				$options[ $option['id'] ] = $option['options'];
			}
			// , 'radiodesc', 'thumbsizes'
			if ( in_array( $option['type'], array( 'multicheck', 'grantselect', 'radio', 'select' ), true ) && isset( $option['default'] ) ) {
				$options[ $option['id'] ] = $option['default'];
			}
		}
	}

	/**
	 * Filters the default settings array.
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( 'x2b_settings_defaults', $options );
}


/**
 * Get the default option for a specific key
 *
 * @param string $key Key of the option to fetch.
 * @return mixed
 */
function x2b_get_default_option( $key = '' ) {
	$default_settings = x2b_settings_defaults();
	if ( array_key_exists( $key, $default_settings ) ) {
		return $default_settings[ $key ];
	} else {
		return false;
	}
}
