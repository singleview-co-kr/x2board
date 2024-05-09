<?php
/**
 * Register settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link  
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
 * load settings function
 *
 * @since 2.6.0
 *
 * @return $a_board_settings
 * return setting title together
 */
function x2b_load_settings( $board_id ) { // $o_board_info ) {
	$o_rst = new \stdClass();
	$o_rst->b_ok = false;
	$o_rst->a_board_settings = null;
	$o_rst->s_x2b_setting_title = X2B_DOMAIN.'_settings_'.$board_id;

	$n_board_id = intval( sanitize_text_field($board_id));
// var_dump($n_board_id)	;
	if( intval( $n_board_id ) == 0 ) {
		return $o_rst;
	}

	// check if the requested WP page is x2board tagged
	$o_wp_page = get_post( $n_board_id );
	if( $o_wp_page->post_content != X2B_PAGE_IDENTIFIER ) {
		unset($o_wp_page);
		return $o_rst;
	}
	
	$a_board_settings = get_option( $o_rst->s_x2b_setting_title );
	if( $a_board_settings === false ) {
		$o_rst->a_board_settings = array();
	}

	// insert custom router setting for a pretty post URL
	$a_board_rewrite_settings = get_option( X2B_REWRITE_OPTION_TITLE );
	$a_board_settings['board_use_rewrite'] = isset($a_board_rewrite_settings[$board_id]) ? 'Y' : 'N' ;
	unset($a_board_rewrite_settings);
	
	// insert ['board_title'] and ['wp_page_title'] into $a_board_settings
	global $wpdb;
	$board_id = esc_sql( $n_board_id );
	$board_title = $wpdb->get_var("SELECT `board_title` FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$board_id'");
	$a_board_settings['board_title'] = $board_title;
	$a_board_settings['wp_page_title'] = $o_wp_page->post_title;
	unset($o_wp_page);

	foreach ( \X2board\Includes\Admin\Tpl\x2b_get_registered_settings() as $tab => $settings ) {
// var_Dump($tab);
		foreach ( $settings as $option ) {
// var_Dump($option);
			// ignore header type field
			if ( 'header' === $option['type'] ) {
				continue;
			}
			// no change if a value comes from get_option()
			if( isset( $a_board_settings[$option['id']] )) {
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
			if ( in_array( $option['type'], array( 'multicheck', 'grantselect','radio', 'select' ), true ) && isset( $option['default'] ) ) {
				$a_board_settings[ $option['id'] ] = $option['default'];
			}
		}
	}
	$o_rst->b_ok = true;
	$o_rst->a_board_settings = $a_board_settings;
	// $a_board_settings['s_x2b_setting_title'] = $s_x2b_setting_title; // return setting title together, for admin usage only
// var_Dump($o_rst);	
	return $o_rst;
}

/**
 * Register settings function
 *
 * @since 2.6.0
 *
 * @return void
 */
function x2b_register_settings() {
	
	// First, we write the options collection.
	global $A_X2B_ADMIN_BOARD_SETTINGS;

	$o_rst = x2b_load_settings( $_GET['board_id'] ); //$o_board_info );
	if ( false === $o_rst->b_ok ) { // for creating a new board
		// add_option( X2B_DOMAIN.'_settings', x2b_settings_defaults() );
		$A_X2B_ADMIN_BOARD_SETTINGS = x2b_settings_defaults();
	}
	else {  // for updating a old board
		$A_X2B_ADMIN_BOARD_SETTINGS = $o_rst->a_board_settings;
	}
// var_dump($o_rst->a_board_settings);
	unset($o_rst);

	foreach ( x2b_get_registered_settings() as $section => $settings ) {
		add_settings_section(
			X2B_DOMAIN.'_settings_' . $section, // ID used to identify this section and with which to register options, e.g. x2b_settings_general.
			__return_empty_string(), // No title, we will handle this via a separate function.
			'__return_false', // No callback function needed. We'll process this separately.
			X2B_DOMAIN.'_settings_' . $section  // Page on which these options will be added.
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
// var_Dump($args);
			add_settings_field(
				X2B_DOMAIN.'_settings[' . $args['id'] . ']', // ID of the settings field. We save it within the x2b_settings array.
				$args['name'],     // Label of the setting.
				function_exists( '\X2board\Includes\Admin\Tpl\x2b_' . $args['type'] . '_callback' ) ?
								 '\X2board\Includes\Admin\Tpl\x2b_' . $args['type'] . '_callback' : 
								 'X2board\Includes\Admin\Tpl\x2b_missing_callback', // Function to handle the setting.
								 X2B_DOMAIN.'_settings_' . $section,    // Page to display the setting. In our case it is the section as defined above.
								 X2B_DOMAIN.'_settings_' . $section,    // Name of the section.
				$args
			);
		}
	}

	// Register the settings into the options table.
	register_setting(
		X2B_DOMAIN.'_settings',
		X2B_DOMAIN.'_settings',
		array(
			'sanitize_callback' => X2B_DOMAIN.'_settings_sanitize',
		)
	);
}


/**
 * Default settings.
 *
 * @since 2.6.0
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

	// $upgraded_settings = x2b_upgrade_settings();
	// if ( false !== $upgraded_settings ) {
	// 	$options = array_merge( $options, $upgraded_settings );
	// }

	/**
	 * Filters the default settings array.
	 *
	 * @since 2.6.0
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( 'x2b_settings_defaults', $options );
}


/**
 * Get the default option for a specific key
 *
 * @since 2.6.0
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


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 2.6.0
 *
 * @param string $key           Key of the option to fetch.
 * @param mixed  $default_value Default value to fetch if option is missing.
 * @return mixed
 */
// function x2b_get_option( $key = '', $default_value = null ) {

// 	global $x2b_settings;

// 	if ( empty( $x2b_settings ) ) {
// 		$x2b_settings = x2b_get_settings();
// 	}

// 	if ( is_null( $default_value ) ) {
// 		$default_value = x2b_get_default_option( $key );
// 	}

// 	$value = isset( $x2b_settings[ $key ] ) ? $x2b_settings[ $key ] : $default_value;

// 	/**
// 	 * Filter the value for the option being fetched.
// 	 *
// 	 * @since 2.6.0
// 	 *
// 	 * @param mixed   $value   Value of the option
// 	 * @param mixed   $key     Name of the option
// 	 * @param mixed   $default_value Default value
// 	 */
// 	$value = apply_filters( 'x2b_get_option', $value, $key, $default_value );

// 	/**
// 	 * Key specific filter for the value of the option being fetched.
// 	 *
// 	 * @since 2.6.0
// 	 *
// 	 * @param mixed   $value   Value of the option
// 	 * @param mixed   $key     Name of the option
// 	 * @param mixed   $default_value Default value
// 	 */
// 	return apply_filters( 'x2b_get_option_' . $key, $value, $key, $default_value );
// }


/**
 * Update an option
 *
 * Updates an x2b setting value in both the db and the global variable.
 * Warning: Passing in a null value will remove
 *          the key from the x2b_options array.
 *
 * @since 2.6.0
 *
 * @param string          $key   The Key to update.
 * @param string|bool|int $value The value to set the key to.
 * @return boolean   True if updated, false if not.
 */
// function x2b_update_option( $key = '', $value = null ) {

// 	// If no key, exit.
// 	if ( empty( $key ) ) {
// 		return false;
// 	}

// 	// If null value, delete.
// 	if ( is_null( $value ) ) {
// 		$remove_option = x2b_delete_option( $key );
// 		return $remove_option;
// 	}

// 	// First let's grab the current settings.
// 	$options = get_option( X2B_DOMAIN.'_settings' );

// 	/**
// 	 * Filters the value before it is updated
// 	 *
// 	 * @since 2.6.0
// 	 *
// 	 * @param string|bool|int $value The value to set the key to
// 	 * @param string  $key   The Key to update
// 	 */
// 	$value = apply_filters( 'x2b_update_option', $value, $key );

// 	// Next let's try to update the value.
// 	$options[ $key ] = $value;
// 	$did_update      = update_option( X2B_DOMAIN.'_settings', $options );

// 	// If it updated, let's update the global variable.
// 	if ( $did_update ) {
// 		global $x2b_settings;
// 		$x2b_settings[ $key ] = $value;
// 	}
// 	return $did_update;
// }


/**
 * Remove an option
 *
 * Removes an x2b setting value in both the db and the global variable.
 *
 * @since 2.6.0
 *
 * @param string $key The Key to update.
 * @return boolean   True if updated, false if not.
 */
// function x2b_delete_option( $key = '' ) {

// 	// If no key, exit.
// 	if ( empty( $key ) ) {
// 		return false;
// 	}

// 	// First let's grab the current settings.
// 	$options = get_option( X2B_DOMAIN.'_settings' );

// 	// Next let's try to update the value.
// 	if ( isset( $options[ $key ] ) ) {
// 		unset( $options[ $key ] );
// 	}

// 	$did_update = update_option( X2B_DOMAIN.'_settings', $options );

// 	// If it updated, let's update the global variable.
// 	if ( $did_update ) {
// 		global $x2b_settings;
// 		$x2b_settings = $options;
// 	}
// 	return $did_update;
// }


/**
 * Flattens x2b_get_registered_settings() into $setting[id] => $setting[type] format.
 *
 * @since 2.6.0
 *
 * @return array Default settings
 */
// function x2b_get_registered_settings_types() {

// 	$options = array();

// 	// Populate some default values.
// 	foreach ( x2b_get_registered_settings() as $tab => $settings ) {
// 		foreach ( $settings as $option ) {
// 			$options[ $option['id'] ] = $option['type'];
// 		}
// 	}

// 	/**
// 	 * Filters the settings array.
// 	 *
// 	 * @since 2.6.0
// 	 *
// 	 * @param array   $options Default settings.
// 	 */
// 	return apply_filters( 'x2b_get_settings_types', $options );
// }


/**
 * Reset settings.
 *
 * @since 2.6.0
 *
 * @return void
 */
// function x2b_settings_reset() {
// 	delete_option( X2B_DOMAIN.'_settings' );
// }
