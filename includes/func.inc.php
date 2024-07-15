<?php
/**
 * The skin functionality of the plugin.
 *
 * @author  https://singleview.co.kr/
 * @version 0.0.1
 */
namespace X2board\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

/**
 * function library files for convenience
 */
function plugin_loaded() {
	// && !is_admin() && !wp_is_json_request()){
	if ( ! session_id() ) { // prevent duplicated seesion activation
		session_start();  // activate $_SESSION while AJAX execution
	}
	// third parameter should be relative path to WP_PLUGIN_DIR
	load_plugin_textdomain( X2B_DOMAIN, false, DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'languages' );

	register_post_type(
		X2B_DOMAIN,
		array(
			'labels'       => array( 'name' => X2B_DOMAIN ),
			'show_ui'      => false,
			'show_in_menu' => false,
			'rewrite'      => false,
			'query_var'    => X2B_DOMAIN . '_post_redirect',
			'public'       => true,
		)
	);
}

/**
 * register POST request handler
 */
function init_proc_cmd() {
	$s_cmd = isset( $_REQUEST['cmd'] ) ? $_REQUEST['cmd'] : '';

	// this cmd comes from wp-content\plugins\x2board\includes\user.php
	switch ( $s_cmd ) {
		case X2B_CMD_PROC_WRITE_POST:
		case X2B_CMD_PROC_VERIFY_PASSWORD:
		case X2B_CMD_PROC_MODIFY_POST:
		case X2B_CMD_PROC_DELETE_POST:
		case X2B_CMD_PROC_WRITE_COMMENT:  // include X2B_CMD_PROC_REPLY_COMMENT
			// case X2B_CMD_PROC_MODIFY_COMMENT:
		case X2B_CMD_PROC_DELETE_COMMENT:
		case X2B_CMD_PROC_DOWNLOAD_FILE:
		case X2B_CMD_PROC_OUTPUT_FILE:
			launch_x2b( 'proc' );
			break;
	}

	// wp_ajax_nopriv_(action) executes for users that are not logged in
	// you should refresh admin page if you change this hook
	add_action( 'wp_ajax_nopriv_' . X2B_CMD_PROC_AJAX_FILE_UPLOAD, '\X2board\Includes\launch_x2b' );
	add_action( 'wp_ajax_' . X2B_CMD_PROC_AJAX_FILE_UPLOAD, '\X2board\Includes\launch_x2b' );
	add_action( 'wp_ajax_nopriv_' . X2B_CMD_PROC_AJAX_FILE_DELETE, '\X2board\Includes\launch_x2b' );
	add_action( 'wp_ajax_' . X2B_CMD_PROC_AJAX_FILE_DELETE, '\X2board\Includes\launch_x2b' );
}

/**
 * register custom URL router handler
 * refer to https://wordpress.stackexchange.com/questions/26388/how-can-i-create-custom-url-routes
 * refer to https://developer.wordpress.org/reference/functions/add_rewrite_rule/
 */
function init_custom_route() {
	$a_board_rewrite_settings = get_option( X2B_REWRITE_OPTION_TITLE );
	if( $a_board_rewrite_settings ) {
		foreach ( $a_board_rewrite_settings as $_ => $s_wp_page_post_name ) {
			// WP stores small-letter URL like wp-%ed%8e%98%ec%9d%b4%ec%a7%80-%ec%a0%9c%eb%aa%a9-2
			// router needs capitalized URL like wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2
			$s_wp_page_post_name = urlencode( urldecode( $s_wp_page_post_name ) );
			add_rewrite_rule(
				$s_wp_page_post_name . '/([0-9]+)/?$',
				'index.php?pagename=' . $s_wp_page_post_name . '&cmd=view_post&post_id=$matches[1]',
				'top'
			);
		}
		add_rewrite_tag( '%post_id%', '([^&]+)' );
	}
}

/**
 * 스크립트와 스타일 파일 등록
 */
function enqueue_user_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( X2B_JQUERY_VALIDATION, X2B_URL . 'common/js/jquery.validate.min.js', array( 'jquery' ), '1.19.5', true );
	wp_enqueue_script( X2B_JS_HANDLER_USER, X2B_URL . 'common/js/guest_script.js', array( 'jquery' ), X2B_VERSION, true );

	$a_ajax_info = array(
		'url'             => admin_url( 'admin-ajax.php' ),
		'cmd_file_upload' => X2B_CMD_PROC_AJAX_FILE_UPLOAD,
		'cmd_file_delete' => X2B_CMD_PROC_AJAX_FILE_DELETE,
		'nonce'           => wp_create_nonce( X2B_AJAX_SECURITY ),
	);
	wp_localize_script( X2B_JS_HANDLER_USER, X2B_DOMAIN . '_ajax_info', $a_ajax_info );
	unset($a_ajax_info);

	// 번역 등록
	$a_localize = array(
		'lbl_required' => __( 'lbl_required', X2B_DOMAIN ),
		'lbl_content'  => __( 'lbl_content', X2B_DOMAIN ),
	);
	wp_localize_script( X2B_JS_HANDLER_USER, X2B_DOMAIN . '_locale', $a_localize );
	unset($a_localize);

	// 경로 등록
	$a_path = array(
		'modules_path_name' => X2B_MODULES_NAME,
	);
	wp_localize_script( X2B_JS_HANDLER_USER, X2B_DOMAIN . '_path', $a_path );
	unset($a_path);
}

/**
 * \includes\modules\import\import.admin.controller.php::_proc_xml_file()에서 호출
 */
function load_modules() {
	if ( ! defined( '__DEBUG__' ) ) {
		define( '__DEBUG__', 0 );
	}

	// load common classes
	require_once X2B_PATH . 'includes/classes/Context.class.php';
	require_once X2B_PATH . 'includes/classes/BaseObject.class.php';
	require_once X2B_PATH . 'includes/classes/ModuleObject.class.php';
	require_once X2B_PATH . 'includes/classes/ModuleHandler.class.php';
	require_once X2B_PATH . 'includes/classes/PaginateSelect.class.php';
	require_once X2B_PATH . 'includes/classes/PageHandler.class.php';
	require_once X2B_PATH . 'includes/classes/FileHandler.class.php';
	require_once X2B_PATH . 'includes/classes/cache/CacheHandler.class.php';
	require_once X2B_PATH . 'includes/classes/user_define_fields/GuestUserDefineFields.class.php';
	require_once X2B_PATH . 'includes/classes/user_define_fields/UserDefineListFields.class.php';
	require_once X2B_PATH . 'includes/classes/security/Password.class.php';
	require_once X2B_PATH . 'includes/classes/security/IpFilter.class.php';
	require_once X2B_PATH . 'includes/no_namespace.helper.php';  // shorten command for skin usage

	// load modules
	\X2board\Includes\Classes\ModuleHandler::auto_load_modules();
}

function launch_x2b( $s_cmd_type, $a_shortcode_args = null ) {
	global $G_X2B_CACHE;
	$G_X2B_CACHE = array();

	load_modules();

	$o_context = \X2board\Includes\Classes\Context::getInstance();

	if ( is_null( $a_shortcode_args ) ) {
		\X2board\Includes\Classes\Context::set( 'board_id', intval( get_the_ID() ) );
	} else {
		\X2board\Includes\Classes\Context::set( 'board_id', intval( $a_shortcode_args['board_id'] ) );
	}

	// if( wp_is_json_request() ) {
	// $s_cmd_type == '' is primarily for admin import
	// $_POST['action'] comes from AJAX only
	if ( $s_cmd_type == '' && isset( $_POST['action'] ) ) {
		$s_cmd_type      = 'proc';  // ajax call
		$_REQUEST['cmd'] = sanitize_text_field( $_POST['action'] );
	}
	$o_context->init( $s_cmd_type );
	$o_context->close();
	unset( $o_context );
}

/**
 * Filter for 'the_content' to display the requested x2board.
 * regarding a 3rd-party plugin which hooks the_content, do not change $content
 * just output HTML before the_content
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function filter_the_content( $content ) {
	global $post;

	// Track the number of times this function  is called.
	static $filter_calls = 0;
	++$filter_calls;
	if ( isset( $post->post_content ) && is_page( $post->ID ) && ! post_password_required() ) {
		if ( $post->post_content === X2B_PAGE_IDENTIFIER ) {
            $content = str_replace( X2B_PAGE_IDENTIFIER, '', $content );
            // enforce board position
            ob_start();
			launch_x2b( 'view' );
            $content .= ob_get_clean();
		}
	}
	if( get_option( X2B_ENDORSE_PLUGIN ) == 'Y' ) {
		return $content.'<div class="'.X2B_DOMAIN.'-default-poweredby"><a href="//singleview.co.kr">Powered by '.X2B_DOMAIN.'</a></div>';
	}
	return $content;
}

/**
 * Content function with filter.
 */
function register_content_filter() {
	add_filter( 'the_content', '\X2board\Includes\filter_the_content' );
}

/**
 * 게시판 생성 숏코드 [x2board]
 *
 * @param array $args
 * @return string
 */
function launch_shortcode( $a_args ) {
	if ( ! isset( $a_args['board_id'] ) || ! $a_args['board_id'] ) {
		return sprintf( __( 'msg_invalid_board_id', X2B_DOMAIN ), X2B_DOMAIN );
	}
	// validate requested board id
	global $wpdb;
	$n_board_id  = esc_sql( $a_args['board_id'] );
	$s_board_cnt = $wpdb->get_var( "SELECT count(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$n_board_id'" );
	if ( intval( $s_board_cnt ) !== 1 ) {
		return sprintf( __( 'msg_invalid_board_id', X2B_DOMAIN ), X2B_DOMAIN );
	}
	launch_x2b( 'view', $a_args );
}

/**
 * Change browser title to post title
 * https://wordpress.org/support/topic/change-title-tag-within-page/
 *
 * @param wp page title
 * @return new browser_title
 */
function change_browser_title( $data ) {
	if ( isset( $_GET['post_id'] ) ) {
		$s_post_name = esc_sql( $_GET['post_id'] );
	} else {
		global $post;
		if( is_null( $post ) ) {  // prevent PHP Warning:  Attempt to read property "post_name" on null
			return $data;
		}
		$s_wp_page_name = '/' . urlencode( urldecode( $post->post_name ) );
		$s_x2b_post_id  = str_replace( $s_wp_page_name, '', $_SERVER['REQUEST_URI'] );
		$a_query        = explode( '/', $s_x2b_post_id, 2 );
		$s_post_name    = '-0'; // sentinel
		if ( isset($a_query[1]) && is_numeric( $a_query[1] ) ) { // try best to find post_id from prettier post URL as possible, then give up
			$s_post_name = $a_query[1];
		}
		unset( $a_query );
	}
	global $wpdb;
	$s_post_title = $wpdb->get_var( $wpdb->prepare( "SELECT `post_title` FROM $wpdb->posts WHERE `post_name` = %s AND `post_type`='%s'", $s_post_name, X2B_DOMAIN ) );
	if ( is_null( $s_post_title ) ) {
		return $data;  // no change
	}
	// add your condition according to page
	return $s_post_title . ' &#8211; ' . $data;
}

/**
 * 관리자 실행을 위해 context 객체 생성
 *
 * @param none
 * @return $o_context
 */
function buildup_context_from_admin() {
	load_modules();
	$o_context = \X2board\Includes\Classes\Context::getInstance();
	return $o_context;
}

/**
 * Define a function to use {@see ModuleHandler::getModuleObject()} ($module_name, $type)
 *
 * @param string $module_name The module name to get a instance
 * @param string $type disp, proc, controller, class
 * @param string $kind admin, null
 * @return mixed Module instance
 */
function get_module( $module_name, $type = 'view', $kind = '' ) {
	global $G_X2B_CACHE;
	if ( ! isset( $G_X2B_CACHE['__MODULE_EXTEND__'] ) ) {
		$G_X2B_CACHE['__MODULE_EXTEND__'] = array();
	}
	if ( ! isset( $G_X2B_CACHE['_loaded_module'] ) ) {
		$G_X2B_CACHE['_loaded_module'] = array();
	}
	if ( ! isset( $G_X2B_CACHE['_called_constructor'] ) ) {
		$G_X2B_CACHE['_called_constructor'] = array();
	}
	if ( ! isset( $G_X2B_CACHE['__elapsed_class_load__'] ) ) {
		$G_X2B_CACHE['__elapsed_class_load__'] = null;
	}

	return \X2board\Includes\Classes\ModuleHandler::get_module_instance( $module_name, $type, $kind );
}

/**
 * Create a controller instance of the module
 *
 * @param string $module_name The module name to get a controller instance
 * @return mixed Module controller instance
 */
function get_controller( $module_name ) {
	return get_module( $module_name, 'controller' );
}

/**
 * Create a view instance of the module
 *
 * @param string $module_name The module name to get a view instance
 * @return mixed Module view instance
 */
function get_view( $module_name ) {
	return get_module( $module_name, 'view' );
}

/**
 * Create a model instance of the module
 *
 * @param string $module_name The module name to get a model instance
 * @return mixed Module model instance
 */
function get_model( $module_name ) {
	return get_module( $module_name, 'model' );
}

/**
 * Create a class instance of the module
 *
 * @param string $module_name The module name to get a class instance
 * @return mixed Module class instance
 */
function get_class( $module_name ) {
	return get_module( $module_name, 'class' );
}

/**
 * Function to handle the result of DB::executeQuery() as an array
 *
 * @see DB::executeQuery()
 * @param object $o_query query object
 * @return object Query result data
 */
function get_paginate_select( $o_query ) {
	$o_pagination = \X2board\Includes\Classes\PaginateSelect::getInstance();
	$output       = $o_pagination->execute_query( $o_query );
	unset( $o_pagination );
	return $output;
}

/**
 * Alias of DB::getNextSequence()
 *
 * @return int
 */
function get_next_sequence() {
	global $wpdb;
	$s_query = "INSERT INTO `{$wpdb->prefix}x2b_sequence` (seq) values ('0')";
	if ( $wpdb->query( $s_query ) === false ) {
		wp_die( $wpdb->last_error );
	}
	$seq = $wpdb->insert_id;
	if ( $seq % 10000 == 0 ) {
		$s_query = "delete from  `{$wpdb->prefix}x2b_sequence` where seq < " . $seq;
		if ( $wpdb->query( $s_query ) === false ) {
			wp_die( $wpdb->last_error );
		}
	}
	set_user_sequence( $seq );
	return $seq;
}

/**
 * Set Sequence number to session
 *
 * @param int $seq sequence number
 * @return void
 */
function set_user_sequence( $seq ) {
	$arr_seq = array();
	if ( isset( $_SESSION['x2b_seq'] ) ) {
		if ( ! is_array( $_SESSION['x2b_seq'] ) ) {
			$_SESSION['x2b_seq'] = array( $_SESSION['x2b_seq'] );
		}
		$arr_seq = $_SESSION['x2b_seq'];
	}
	$arr_seq[]           = $seq;
	$_SESSION['x2b_seq'] = $arr_seq;
}

/**
 * Check Sequence number grant
 *
 * @param int $seq sequence number
 * @return boolean
 */
function check_user_sequence( $seq ) {
	if ( ! isset( $_SESSION['x2b_seq'] ) ) {
		return false;
	}
	if ( ! in_array( $seq, $_SESSION['x2b_seq'] ) ) {
		return false;
	}
	return true;
}

/**
 * microtime() return
 *
 * @return float
 */
function get_micro_time() {
	list($time1, $time2) = explode( ' ', microtime() );
	return (float) $time1 + (float) $time2;
}

/**
 * This function is a shortcut to htmlspecialchars().
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @param bool   $double_escape Set this to false to skip symbols that are already escaped (default: true)
 * @return string
 */
function escape( $str, $double_escape = true, $escape_defined_lang_code = false ) {
	if ( ! $escape_defined_lang_code && is_defined_lang_code( $str ) ) {
		return $str;
	}

	$flags = ENT_QUOTES | ENT_SUBSTITUTE;
	return htmlspecialchars( $str, $flags, 'UTF-8', $double_escape );
}

function is_defined_lang_code( $str ) {
	return preg_match( '!^\$user_lang->([a-z0-9\_]+)$!is', trim( $str ) );
}

/**
 * Change the time format YYYYMMDDHHIISS to the user defined format
 *
 * @param string|int $str YYYYMMDDHHIISS format time values
 * @param string     $format Time format of php date() function
 * @param bool       $conversion Means whether to convert automatically according to the language
 * @return string
 */
function zdate( $str, $format = 'Y-m-d H:i:s', $conversion = true ) {
	if ( ! $str ) {  // return null if no target time is specified
		return;
	}
	if ( $conversion == true ) {  // convert the date format according to the language
		switch ( substr( get_locale(), 0, 2 ) ) {  // Context::get_lang_type()) {
			case 'en':
			case 'es':
				if ( $format == 'Y-m-d' ) {
					$format = 'M d, Y';
				} elseif ( $format == 'Y-m-d H:i:s' ) {
					$format = 'M d, Y H:i:s';
				} elseif ( $format == 'Y-m-d H:i' ) {
					$format = 'M d, Y H:i';
				}
				break;
			case 'vi':
				if ( $format == 'Y-m-d' ) {
					$format = 'd-m-Y';
				} elseif ( $format == 'Y-m-d H:i:s' ) {
					$format = 'H:i:s d-m-Y';
				} elseif ( $format == 'Y-m-d H:i' ) {
					$format = 'H:i d-m-Y';
				}
				break;
		}
	}
	// If year value is less than 1970, handle it separately.
	/*
	if((int) substr($str, 0, 4) < 1970) {
		$hour = (int) substr($str, 8, 2);
		$min = (int) substr($str, 10, 2);
		$sec = (int) substr($str, 12, 2);
		$year = (int) substr($str, 0, 4);
		$month = (int) substr($str, 4, 2);
		$day = (int) substr($str, 6, 2);
		$trans = array(
			'Y' => $year,
			'y' => sprintf('%02d', $year % 100),
			'm' => sprintf('%02d', $month),
			'n' => $month,
			'd' => sprintf('%02d', $day),
			'j' => $day,
			'G' => $hour,
			'H' => sprintf('%02d', $hour),
			'g' => $hour % 12,
			'h' => sprintf('%02d', $hour % 12),
			'i' => sprintf('%02d', $min),
			's' => sprintf('%02d', $sec),
			'M' => getMonthName($month),
			'F' => getMonthName($month, FALSE)
		);
		$string = strtr($format, $trans);
	}
	else {*/ // if year value is greater than 1970, get unixtime by using ztime() for date() function's argument.
		$string = date( $format, ztime( $str ) );
	// }
	// change day and am/pm for each language
	$a_unit_week     = \X2board\Includes\Classes\Context::get( 'unit_week' ); // Context::getLang('unit_week');
	$a_unit_meridiem = \X2board\Includes\Classes\Context::get( 'unit_meridiem' ); // Context::getLang('unit_meridiem');
	$string          = str_replace( array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ), $a_unit_week, $string );
	$string          = str_replace( array( 'am', 'pm', 'AM', 'PM' ), $a_unit_meridiem, $string );
	return $string;
}

/**
 * Name of the month return
 *
 * @param int  $month Month
 * @param boot $short If set, returns short string
 * @return string
 */
/*function getMonthName( $month, $short = true ) {
	$short_month = array( '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
	$long_month  = array( '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
	return ! $short ? $long_month[ $month ] : $short_month[ $month ];
}*/

/**
 * YYYYMMDDHHIISS format changed to unix time value
 *
 * @param string $str Time value in format of YYYYMMDDHHIISS
 * @return int
 */
function ztime( $str ) {
	if ( ! $str ) {
		return;
	}
	// if (strlen($str) === 9 || (strlen($str) === 10 && $str <= 2147483647)) {
	// return intval($str);
	// }
	$hour  = (int) substr( $str, 8, 2 );
	$min   = (int) substr( $str, 10, 2 );
	$sec   = (int) substr( $str, 12, 2 );
	$year  = (int) substr( $str, 0, 4 );
	$month = (int) substr( $str, 4, 2 );
	$day   = (int) substr( $str, 6, 2 );
	if ( strlen( $str ) <= 8 ) {
		$gap = 0;
	} else {
		$gap = zgap();
	}
	return mktime( $hour, $min, $sec, $month ? $month : 1, $day ? $day : 1, $year ) + $gap;
}

/**
 * Get a time gap between server's timezone and XE's timezone
 *
 * @return int
 */
function zgap() {
	// this option set by \includes\admin\admin.php::register_timezone_gap()
	return get_option( X2B_DOMAIN . '_timezone_gap' );
}

/**
 * Pre-block the codes which may be hacking attempts
 *
 * @param string $content Taget content
 * @return string
 */
function remove_hack_tag( $content ) {
	require_once X2B_PATH . 'includes/classes/security/EmbedFilter.class.php';
	$oEmbedFilter = \X2board\Includes\Classes\Security\EmbedFilter::getInstance();
	$oEmbedFilter->check( $content );
	// purifierHtml($content);  // too old purifier

	// change the specific tags to the common texts
	$content = preg_replace( '@<(\/?(?:html|body|head|title|meta|base|link|script|style|applet)(/*).*?>)@i', '&lt;$1', $content );
	/**
	 * Remove codes to abuse the admin session in src by tags of images and video postings
	 * - Issue reported by Sangwon Kim
	 */
	$content = preg_replace_callback(
		'@<(/?)([a-z]+[0-9]?)((?>"[^"]*"|\'[^\']*\'|[^>])*?\b(?:on[a-z]+|data|style|background|href|(?:dyn|low)?src)\s*=[\s\S]*?)(/?)($|>|<)@i',
		'\X2board\Includes\remove_src_hack',
		$content
	);
	$content = check_xmp_tag( $content );
	$content = block_widget_code( $content );
	return $content;
}

/**
 * Remove src hack(preg_replace_callback)
 *
 * @param array $match
 * @return string
 */
function remove_src_hack( $match ) {
	$tag = strtolower( $match[2] );

	// xmp tag ?뺣━
	if ( $tag == 'xmp' ) {
		return "<{$match[1]}xmp>";
	}
	if ( $match[1] ) {
		return $match[0];
	}
	if ( $match[4] ) {
		$match[4] = ' ' . $match[4];
	}

	$attrs = array();
	if ( preg_match_all( '/([\w:-]+)\s*=(?:\s*(["\']))?(?(2)(.*?)\2|([^ ]+))/s', $match[3], $m ) ) {
		foreach ( $m[1] as $idx => $name ) {
			if ( strlen( $name ) >= 2 && substr_compare( $name, 'on', 0, 2 ) === 0 ) {
				continue;
			}

			$val = preg_replace_callback(
				'/&#(?:x([a-fA-F0-9]+)|0*(\d+));/',
				function ( $n ) {
					return chr( $n[1] ? ( '0x00' . $n[1] ) : ( $n[2] + 0 ) );
				},
				$m[3][ $idx ] . $m[4][ $idx ]
			);
			$val = preg_replace( '/^\s+|[\t\n\r]+/', '', $val );

			if ( preg_match( '/^[a-z]+script:/i', $val ) ) {
				continue;
			}
			$attrs[ $name ] = $val;
		}
	}

	$filter_arrts = array( 'style', 'src', 'href' );

	if ( $tag === 'object' ) {
		array_push( $filter_arrts, 'data' );
	}
	if ( $tag === 'param' ) {
		array_push( $filter_arrts, 'value' );
	}

	foreach ( $filter_arrts as $attr ) {
		if ( ! isset( $attrs[ $attr ] ) ) {
			continue;
		}

		$attr_value = rawurldecode( $attrs[ $attr ] );
		$attr_value = htmlspecialchars_decode( $attr_value, ENT_COMPAT );
		$attr_value = preg_replace( '/\s+|[\t\n\r]+/', '', $attr_value );
		if ( preg_match( '@(\?|&|;)(act=(\w+))@i', $attr_value, $m ) && $m[3] !== 'procFileDownload' ) {
			unset( $attrs[ $attr ] );
		}
	}

	if ( isset( $attrs['style'] ) && preg_match( '@(?:/\*|\*/|\n|:\s*expression\s*\()@i', $attrs['style'] ) ) {
		unset( $attrs['style'] );
	}

	$attr = array();
	foreach ( $attrs as $name => $val ) {
		if ( $tag == 'object' || $tag == 'embed' || $tag == 'a' ) {
			$attribute = strtolower( trim( $name ) );
			if ( $attribute == 'data' || $attribute == 'src' || $attribute == 'href' ) {
				if ( stripos( $val, 'data:' ) === 0 ) {
					continue;
				}
			}
		}

		if ( $tag == 'img' ) {
			$attribute = strtolower( trim( $name ) );
			if ( stripos( $val, 'data:' ) === 0 ) {
				continue;
			}
		}
		$val    = str_replace( '"', '&quot;', $val );
		$attr[] = $name . "=\"{$val}\"";
	}
	$attr = count( $attr ) ? ' ' . implode( ' ', $attr ) : '';
	return "<{$match[1]}{$tag}{$attr}{$match[4]}>";
}

/**
 * blocking widget code
 *
 * @param string $content Taget content
 * @return string
 **/
function block_widget_code( $s_content ) {
	$s_content = preg_replace( '/(<(?:img|div)(?:[^>]*))(widget)(?:(=([^>]*?)>))/is', '$1blocked-widget$3', $s_content );
	return $s_content;
}

/**
 * Check xmp tag, close it.
 *
 * @param string $content Target content
 * @return string
 */
function check_xmp_tag( $s_content ) {
	$s_content = preg_replace( '@<(/?)xmp.*?>@i', '<\1xmp>', $s_content );
	if ( ( $start_xmp = strrpos( $s_content, '<xmp>' ) ) !== false ) {
		if ( ( $close_xmp = strrpos( $s_content, '</xmp>' ) ) === false ) {
			$s_content .= '</xmp>';
		} elseif ( $close_xmp < $start_xmp ) {
			$s_content .= '</xmp>';
		}
	}
	return $s_content;
}

/**
 * 사용자 IP 주소를 반환한다.
 *
 * @return string
 */
function get_remote_ip() {
	static $s_ip;
	if ( $s_ip === null ) {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$s_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$s_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$s_ip = $_SERVER['REMOTE_ADDR'];
		}
	}
	return $s_ip;
}

/**
 * Get is current user crawler
 *
 * @param string $agent if set, use this value instead HTTP_USER_AGENT
 * @return bool
 */
function is_crawler( $agent = null ) {
	if ( ! $agent ) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
	}

	$check_agent = array( 'bot', 'spider', 'spyder', 'crawl', 'http://', 'google', 'yahoo', 'slurp', 'yeti', 'daum', 'teoma', 'fish', 'hanrss', 'facebook', 'yandex', 'infoseek', 'askjeeves', 'stackrambler', 'python' );
	$check_ip    = array(
		/*'211.245.21.110-211.245.21.119' mixsh is closed */
	);

	foreach ( $check_agent as $str ) {
		if ( stristr( $agent, $str ) != false ) {
			return true;
		}
	}
	return \X2board\Includes\Classes\IpFilter::filter( $check_ip );
}

/**
 * get WP post ID that matches x2b post
 */
function get_wp_post_id_by_x2b_post_id( $n_x2b_post_id ) {
	global $wpdb;
	$n_x2b_post_id = esc_sql( $n_x2b_post_id );
	$n_wp_post_id  = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_name`='$n_x2b_post_id' AND `post_type`='" . X2B_DOMAIN . "'" );
	if ( ! $n_wp_post_id ) {
		$n_wp_post_id = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_name`='{$n_x2b_post_id}__trashed' AND `post_type`='" . X2B_DOMAIN . "'" );
	}
	return intval( $n_wp_post_id );
}

/**
 * Return the requested script path
 * getScriptPath()
 *
 * @return string
 */
function get_script_path() {
	static $s_url = null;
	if ( $s_url == null ) {
		$script_path = filter_var( $_SERVER['SCRIPT_NAME'], FILTER_SANITIZE_STRING );
		$s_url       = str_ireplace( '/tools/', '/', preg_replace( '/index.php.*/i', '', str_replace( '\\', '/', $script_path ) ) );
	}
	return $s_url;
}

/**
 * Remove embed media for admin
 *
 * @param string $content
 * @param int    $writer_member_srl
 * @return void
 */
function strip_embed_tag_for_admin( &$s_content, $writer_member_id ) {
	if ( ! \X2board\Includes\Classes\Context::get( 'is_logged' ) ) {
		return;
	}
	$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
	if ( $writer_member_id != $o_logged_info->ID && ( $o_logged_info->is_admin == 'Y' ) ) {
		if ( $writer_member_id ) {
			$member_info = get_userdata( $writer_member_id );
			if ( $member_info->roles[0] == 'administrator' ) {
				return;
			}
		}
		$security_msg = "<div style='border: 1px solid #DDD; background: #FAFAFA; text-align:center; margin: 1em 0;'><p style='margin: 1em;'>" . __( 'msg_security_warning_embed', X2B_DOMAIN ) . '</p></div>';
		$s_content    = preg_replace( '/<object[^>]+>(.*?<\/object>)?/is', $security_msg, $s_content );
		$s_content    = preg_replace( '/<embed[^>]+>(\s*<\/embed>)?/is', $security_msg, $s_content );
		// $content = preg_replace('/<img[^>]+editor_component="multimedia_link"[^>]*>(\s*<\/img>)?/is', $security_msg, $content);
	}
	return;
}

/**
 * Trim a given number to a fiven size recursively
 *
 * @param int $no A given number
 * @param int $size A given digits
 */
function get_numbering_path( $no, $size = 3 ) {
	$mod    = pow( 10, $size );
	$output = sprintf( '%0' . $size . 'd/', $no % $mod );
	if ( $no >= $mod ) {
		$output .= get_numbering_path( (int) $no / $mod, $size );
	}
	return $output;
}

/**
 * check uploaded file which may be hacking attempts
 *
 * @param string $file Taget file path
 * @return bool
 */
function check_uploaded_file( $file, $filename = null ) {
	require_once X2B_PATH . 'includes/classes/security/UploadFileFilter.class.php';
	return \X2board\Includes\Classes\Security\UploadFileFilter::check( $file, $filename );
}

/**
 * Get a not encoded(html entity) url
 *
 * @see getUrl()
 * @return string
 */
function get_not_encoded_url() {
	$num_args  = func_num_args();
	$args_list = func_get_args();

	if ( $num_args ) {
		$url = \X2board\Includes\Classes\Context::get_url( $num_args, $args_list, null, false );
	} else {
		$url = \X2board\Includes\Classes\Context::get_request_uri();
	}

	return preg_replace( '@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $url );
}

/**
 * Put a given tail after trimming string to the specified size
 *
 * @param string $string The original string to trim
 * @param int    $cut_size The size to be
 * @param string $tail Tail to put in the end of the string after trimming
 * @return string
 */
function cut_str( $string, $cut_size = 0, $tail = '...' ) {
	if ( $cut_size < 1 || ! $string ) {
		return $string;
	}

	global $G_X2B_CACHE;
	if ( isset( $G_X2B_CACHE['x2b_use_mb_strimwidth'] ) || function_exists( 'mb_strimwidth' ) ) {
		$GLOBALS['use_mb_strimwidth'] = true;
		return mb_strimwidth( $string, 0, $cut_size + 4, $tail, 'utf-8' );
	}

	$chars      = array( 12, 4, 3, 5, 7, 7, 11, 8, 4, 5, 5, 6, 6, 4, 6, 4, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 4, 4, 8, 6, 8, 6, 10, 8, 8, 9, 8, 8, 7, 9, 8, 3, 6, 7, 7, 11, 8, 9, 8, 9, 8, 8, 7, 8, 8, 10, 8, 8, 8, 6, 11, 6, 6, 6, 4, 7, 7, 7, 7, 7, 3, 7, 7, 3, 3, 6, 3, 9, 7, 7, 7, 7, 4, 7, 3, 7, 6, 10, 6, 6, 7, 6, 6, 6, 9 );
	$max_width  = $cut_size * $chars[0] / 2;
	$char_width = 0;

	$string_length = strlen( $string );
	$char_count    = 0;

	$idx = 0;
	while ( $idx < $string_length && $char_count < $cut_size && $char_width <= $max_width ) {
		$c = ord( substr( $string, $idx, 1 ) );
		++$char_count;
		if ( $c < 128 ) {
			$char_width += (int) $chars[ $c - 32 ];
			++$idx;
		} elseif ( 191 < $c && $c < 224 ) {
			$char_width += $chars[4];
			$idx        += 2;
		} else {
			$char_width += $chars[0];
			$idx        += 3;
		}
	}

	$output = substr( $string, 0, $idx );
	if ( strlen( $output ) < $string_length ) {
		$output .= $tail;
	}
	return $output;
}

/**
 * If the recent post within a day, output format of YmdHis is "min/hours ago from now". If not within a day, it return format string.
 *
 * @param string $date Time value in format of YYYYMMDDHHIISS
 * @param string $format If gap is within a day, returns this format.
 * @return string
 */
function get_time_gap( $date, $format = 'Y.m.d' ) {
	// traslate yyyy-mm-dd hh:ii:ss' into 'yyyymmddhhiiss'
	$date = preg_replace( '/[ \-\:]/i', '', $date );
	$gap  = $_SERVER['REQUEST_TIME'] + zgap() - ztime( $date );
	// $lang_time_gap = Context::getLang('time_gap');
	$lang_time_gap = array(
		'mins'  => __( 'lbl_mins_ago', X2B_DOMAIN ),
		'hours' => __( 'lbl_hrs_ago', X2B_DOMAIN ),
	);

	// if($gap < 60) {
	// $buff = sprintf($lang_time_gap['min'], (int) ($gap / 60) + 1);
	// }
	if ( $gap < 3600 ) { // 60 * 60
		$buff = sprintf( $lang_time_gap['mins'], (int) ( $gap / 60 ) + 1 );
	} elseif ( $gap < 7200 ) {  // 60 * 60 * 2
		$buff = sprintf( $lang_time_gap['hour'], (int) ( $gap / 60 / 60 ) + 1 );
	}
	// elseif($gap < 60 * 60 * 24) {
	// $buff = sprintf($lang_time_gap['hours'], (int) ($gap / 60 / 60) + 1);
	// }
	else {
		$buff = zdate( $date, $format );
	}
	return $buff;
}
