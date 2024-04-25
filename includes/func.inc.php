<?php
namespace X2board\Includes;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

/**
 * function library files for convenience
 *
 */

function plugin_loaded(){
	// if(!session_id() && !is_admin() ) { // && !wp_is_json_request()){
	if( isset($_POST['action']) ) {  // $_POST['action'] comes from AJAX only
		session_start();  // activate $_SESSION while AJAX execution
	}
	// 언어 파일 추가
	// load_plugin_textdomain('x2board', false, X2B_PATH . 'languages');
	register_post_type(X2B_DOMAIN, array(
		'labels' => array('name'=>X2B_DOMAIN),
		'show_ui'=> false,
		'show_in_menu'=> false,
		'rewrite' => false,
		'query_var' => X2B_DOMAIN.'_post_redirect',
		'public'=> true
	));
}

/**
 * register POST request handler
 */
function init_proc_cmd() {
	$s_cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

	// this cmd comes from wp-content\plugins\x2board\includes\user.php
	switch($s_cmd) {
		case X2B_CMD_PROC_WRITE_POST:
		case X2B_CMD_PROC_MODIFY_POST:
		case X2B_CMD_PROC_DELETE_POST:
		case X2B_CMD_PROC_WRITE_COMMENT:  // include X2B_CMD_PROC_REPLY_COMMENT
		// case X2B_CMD_PROC_MODIFY_COMMENT:
		case X2B_CMD_PROC_DELETE_COMMENT:
		case X2B_CMD_PROC_DOWNLOAD_FILE:
		case X2B_CMD_PROC_OUTPUT_FILE:
			_launch_x2b('proc');
			break;
	}

	// wp_ajax_nopriv_(action) executes for users that are not logged in
	// you should refresh admin page if you change this hook
	add_action('wp_ajax_nopriv_'.X2B_CMD_PROC_AJAX_FILE_UPLOAD, '\X2board\Includes\_launch_x2b');
	add_action('wp_ajax_'.X2B_CMD_PROC_AJAX_FILE_UPLOAD, '\X2board\Includes\_launch_x2b');
	add_action('wp_ajax_nopriv_'.X2B_CMD_PROC_AJAX_FILE_DELETE, '\X2board\Includes\_launch_x2b');
	add_action('wp_ajax_'.X2B_CMD_PROC_AJAX_FILE_DELETE, '\X2board\Includes\_launch_x2b');
}

/**
 * register custom URL router handler
 * refer to https://wordpress.stackexchange.com/questions/26388/how-can-i-create-custom-url-routes
 * refer to https://developer.wordpress.org/reference/functions/add_rewrite_rule/
 */
function init_custom_route() {
	$a_board_rewrite_settings = get_option( X2B_REWRITE_OPTION_TITLE );
	foreach( $a_board_rewrite_settings as $_ => $s_wp_page_post_name ) {
		// WP stores small-letter URL like wp-%ed%8e%98%ec%9d%b4%ec%a7%80-%ec%a0%9c%eb%aa%a9-2
		// router needs capitalized URL like wp-%ED%8E%98%EC%9D%B4%EC%A7%80-%EC%A0%9C%EB%AA%A9-2
		$s_wp_page_post_name = urlencode(urldecode($s_wp_page_post_name));
		add_rewrite_rule(
			$s_wp_page_post_name.'/([0-9]+)/?$',
			'index.php?pagename='.$s_wp_page_post_name.'&cmd=view_post&post_id=$matches[1]',
			'top' );
	}
	add_rewrite_tag( '%post_id%', '([^&]+)' );
}

// function init_custom_query_vars() {
// 	$query_vars[] = 'post_id';
//     return $query_vars;
// }
// add_filter( 'query_vars', '\X2board\Includes\init_custom_query_vars', 5 );

/**
 * 스크립트와 스타일 파일 등록
 */
function enqueue_user_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_script(X2B_JS_HANDLER_USER, X2B_URL . '/template/js/script.js', array(), X2B_VERSION, true);
// error_log(print_r(X2B_URL, true));
	// Tags Input 등록
	// wp_register_style('tagsinput', KBOARD_URL_PATH . '/assets/tagsinput/jquery.tagsinput.css', array(), '1.3.3');
	// wp_register_script('tagsinput', KBOARD_URL_PATH . '/assets/tagsinput/jquery.tagsinput.js', array('jquery'), '1.3.3');
	
	// Moment.js 등록
	// wp_register_script('moment', KBOARD_URL_PATH . '/assets/moment/moment.js', array('jquery'), '2.17.1');
	
	// jQuery Date Range Picker Plugin 등록
	// wp_register_style('daterangepicker', KBOARD_URL_PATH . '/assets/daterangepicker/daterangepicker.css', array(), '0.0.8');
	// wp_register_script('daterangepicker', KBOARD_URL_PATH . '/assets/daterangepicker/jquery.daterangepicker.js', array('jquery', 'moment'), '0.0.8');
	
	// jQuery lightSlider 등록
	// wp_register_style('lightslider', KBOARD_URL_PATH . '/assets/lightslider/css/lightslider.css', array(), '1.1.6');
	// wp_register_script('lightslider', KBOARD_URL_PATH . '/assets/lightslider/js/lightslider.js', array('jquery'), '1.1.6');
	
	// 아임포트 등록
	// wp_register_script('iamport-payment', 'https://cdn.iamport.kr/js/iamport.payment-1.1.7.js', array('jquery'), '1.1.7');
	
	// 구글 리캡차 등록
	// wp_register_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
	
	// jQuery Timepicker 등록
	// wp_register_style('jquery-timepicker', KBOARD_URL_PATH . '/template/css/jquery.timepicker.css', array(), '1.3.5');
	// wp_register_script('jquery-timepicker', KBOARD_URL_PATH . '/template/js/jquery.timepicker.js', array('jquery'), '1.3.5');
		
	// 우편번호 주소 검색
	// wp_enqueue_script('daum-postcode', '//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js', array(), NULL, true);
	
	// 필드 관련 스크립트 등록
	// wp_register_script('kboard-field-date', KBOARD_URL_PATH . '/template/js/field-date.js', array('jquery'), X2B_VERSION, true);
	// wp_register_script('kboard-field-time', KBOARD_URL_PATH . '/template/js/field-time.js', array('jquery'), X2B_VERSION, true);
	// wp_register_script('kboard-field-address', KBOARD_URL_PATH . '/template/js/field-address.js', array('jquery', 'daum-postcode'), X2B_VERSION, true);
	
	// 설정 등록
	$a_ajax_info= array(
		// 'version' => X2B_VERSION,
		// 'home_url' => home_url('/', 'relative'),
		// 'site_url' => site_url('/', 'relative'),
		// 'post_url' => admin_url('admin-post.php'),
		'url' => admin_url('admin-ajax.php'),
		// 'plugin_url' => KBOARD_URL_PATH,
		// 'view_iframe' => kboard_view_iframe(),
		// 'locale' => get_locale(),
		'cmd_file_upload' => X2B_CMD_PROC_AJAX_FILE_UPLOAD,
		'cmd_file_delete' => X2B_CMD_PROC_AJAX_FILE_DELETE,
		'nonce' => wp_create_nonce(X2B_AJAX_SECURITY),
	);
	wp_localize_script(X2B_JS_HANDLER_USER, 'x2board_ajax_info', apply_filters('x2board_settings', $a_ajax_info));
	
	// 번역 등록
	// $localize = array(
	// 	// 'kboard_add_media' => __('KBoard Add Media', 'kboard'),
	// 	'next' => __('Next', 'kboard'),
	// 	'prev' => __('Prev', 'kboard'),
	// 	'required' => __('%s is required.', 'kboard'),
	// 	'please_enter_the_title' => __('Please enter the title.', 'kboard'),
	// 	'please_enter_the_author' => __('Please enter the author.', 'kboard'),
	// 	'please_enter_the_password' => __('Please enter the password.', 'kboard'),
	// 	'please_enter_the_CAPTCHA' => __('Please enter the CAPTCHA.', 'kboard'),
	// 	'please_enter_the_name' => __('Please enter the name.', 'kboard'),
	// 	'please_enter_the_email' => __('Please enter the email.', 'kboard'),
	// 	'you_have_already_voted' => __('You have already voted.', 'kboard'),
	// 	'please_wait' => __('Please wait.', 'kboard'),
	// 	'newest' => __('Newest', 'kboard'),
	// 	'best' => __('Best', 'kboard'),
	// 	'updated' => __('Updated', 'kboard'),
	// 	'viewed' => __('Viewed', 'kboard'),
	// 	'yes' => __('Yes', 'kboard'),
	// 	'no' => __('No', 'kboard'),
	// 	'did_it_help' => __('Did it help?', 'kboard'),
	// 	'hashtag' => __('Hashtag', 'kboard'),
	// 	'tag' => __('Tag', 'kboard'),
	// 	'add_a_tag' => __('Add a Tag', 'kboard'),
	// 	'removing_tag' => __('Removing tag', 'kboard'),
	// 	'changes_you_made_may_not_be_saved' => __('Changes you made may not be saved.', 'kboard'),
	// 	'name' => __('Name', 'kboard'),
	// 	'email' => __('Email', 'kboard'),
	// 	'address' => __('Address', 'kboard'),
	// 	'address_2' => __('Address 2', 'kboard'),
	// 	'postcode' => __('Postcode', 'kboard'),
	// 	'phone_number' => __('Phone number', 'kboard'),
	// 	'mobile_phone' => __('Mobile phone', 'kboard'),
	// 	'phone' => __('Phone', 'kboard'),
	// 	'company_name' => __('Company name', 'kboard'),
	// 	'vat_number' => __('VAT number', 'kboard'),
	// 	'bank_account' => __('Bank account', 'kboard'),
	// 	'name_of_deposit' => __('Name of deposit', 'kboard'),
	// 	'find' => __('Find', 'kboard'),
	// 	'rate' => __('Rate', 'kboard'),
	// 	'ratings' => __('Ratings', 'kboard'),
	// 	'waiting' => __('Waiting', 'kboard'),
	// 	'complete' => __('Complete', 'kboard'),
	// 	'question' => __('Question', 'kboard'),
	// 	'answer' => __('Answer', 'kboard'),
	// 	'notify_me_of_new_comments_via_email' => __('Notify me of new comments via email', 'kboard'),
	// 	'ask_question' => __('Ask Question', 'kboard'),
	// 	'categories' => __('Categories', 'kboard'),
	// 	'pages' => __('Pages', 'kboard'),
	// 	'use_points' => __('Use points', 'kboard'),
	// 	'my_points' => __('My points', 'kboard'),
	// 	'available_points' => __('Available points', 'kboard'),
	// 	'apply_points' => __('Apply points', 'kboard'),
	// 	'privacy_policy' => __('Privacy policy', 'kboard'),
	// 	'i_agree_to_the_privacy_policy' => __('I agree to the privacy policy.', 'kboard'),
	// 	'i_confirm_the_terms_of_the_transaction_and_agree_to_the_payment_process' => __('I confirm the terms of the transaction and agree to the payment process.', 'kboard'),
	// 	'today' => __('Today', 'kboard'),
	// 	'yesterday' => __('Yesterday', 'kboard'),
	// 	'this_month' => __('This month', 'kboard'),
	// 	'last_month' => __('Last month', 'kboard'),
	// 	'last_30_days' => __('Last 30 days', 'kboard'),
	// 	'agree' => __('Agree', 'kboard'),
	// 	'disagree' => __('Disagree', 'kboard'),
	// 	'opinion' => __('Opinion', 'kboard'),
	// 	'comment' => __('Comment', 'kboard'),
	// 	'comments' => __('Comments', 'kboard'),
	// 	'point' => __('Point', 'kboard'),
	// 	'zipcode' => __('Zip Code', 'kboard'),
	// 	'this_year' => __('This year', 'kboard'),
	// 	'last_year' => __('Last year', 'kboard'),
	// 	'terms_of_service' => __('Terms of service', 'kboard'),
	// 	'i_agree_to_the_terms_of_service' => __('I agree to the terms of service.', 'kboard'),
	// 	'category' => __('Category', 'kboard'),
	// 	'select' => __('Select', 'kboard'),
	// 	'category_select' => __('Category select', 'kboard'),
	// 	'information' => __('Information', 'kboard'),
	// 	'telephone' => __('Telephone', 'kboard'),
	// 	'add' => __('Add', 'kboard'),
	// 	'close' => __('Close', 'kboard'),
	// );
	// wp_localize_script('kboard-script', 'kboard_localize_strings', apply_filters('kboard_localize_strings', $localize));
}

function _launch_x2b($s_cmd_type) {
	global $G_X2B_CACHE;
	$G_X2B_CACHE = array();

	if ( !defined( '__DEBUG__' ) ) {
		define('__DEBUG__', 0);
	}
	
	// load common classes
	require_once X2B_PATH . 'includes/classes/Context.class.php';
	require_once X2B_PATH . 'includes/classes/BaseObject.class.php';
	require_once X2B_PATH . 'includes/classes/ModuleObject.class.php';
	require_once X2B_PATH . 'includes/classes/ModuleHandler.class.php';
	require_once X2B_PATH . 'includes/classes/DB.class.php';
	require_once X2B_PATH . 'includes/classes/PageHandler.class.php';
	require_once X2B_PATH . 'includes/classes/FileHandler.class.php';
	require_once X2B_PATH . 'includes/classes/cache/CacheHandler.class.php';
	require_once X2B_PATH . 'includes/classes/UserDefineFields.class.php';
	require_once X2B_PATH . 'includes/classes/security/Password.class.php';
	require_once X2B_PATH . 'includes/classes/security/IpFilter.class.php';
	require_once X2B_PATH . 'includes/no_namespace.helper.php';  // shorten command for skin usage

	// load modules
	\X2board\Includes\Classes\ModuleHandler::auto_load_modules();

	$o_context = \X2board\Includes\Classes\Context::getInstance();
	
	// if( wp_is_json_request() ) { 
	if( $s_cmd_type == '' && isset($_POST['action']) ) {  // $_POST['action'] comes from AJAX only
		$s_cmd_type = 'proc';  // ajax call
		$_REQUEST['cmd'] = sanitize_text_field($_POST['action']);
	}
	// var_dump(wp_is_json_request());
	// var_dump($s_cmd_type);
	$o_context->init($s_cmd_type);
	$o_context->close();
	unset($o_context);
}

/**
 * Filter for 'the_content' to display the requested x2board.
 * regarding a 3rd-party plugin which hooks the_content, do not change $content 
 * just output HTML before the_content
 *
 * @since 1.0.1
 *
 * @param string $content Post content.
 * @return string After the filter has been processed
 */
function filter_the_content( $content ) {
	global $post; // , $wpdb, $wp_filters;

	// Track the number of times this function  is called.
	static $filter_calls = 0;
	++$filter_calls;
	if(isset($post->post_content) && is_page($post->ID) && !post_password_required()){
		if( $post->post_content === X2B_PAGE_IDENTIFIER ) {
			_launch_x2b('view');
			$content = str_replace(X2B_PAGE_IDENTIFIER,'', $content); //return $content . board_builder(array('id'=>$board_id));
		} 
	}
	return $content;

	// // Return if it's not in the loop or in the main query.
	// if ( ! ( in_the_loop() && is_main_query() && (int) get_queried_object_id() === (int) $post->ID ) ) {
	// 	return $content;
	// }

	// // Check if this is the last call of the_content.
	// if ( doing_filter( 'the_content' ) && isset( $wp_filters['the_content'] ) && (int) $wp_filters['the_content'] !== $filter_calls ) {
	// 	return $content;
	// }

	// // Return if this is a mobile device and disable on mobile option is enabled.
	// if ( wp_is_mobile() && x2b_get_option( 'disable_on_mobile' ) ) {
	// 	return $content;
	// }

	// // Return if this is an amp page and disable on amp option is enabled.
	// if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() && x2b_get_option( 'disable_on_amp' ) ) {
	// 	return $content;
	// }

	// // Check exclusions.
	// if ( x2b_exclude_on( $post, $x2b_settings ) ) {
	// 	return $content;    // Exit without adding related posts.
	// }

	// $add_to = x2b_get_option( 'add_to', false );

	// // Else add the content.
	// if ( ( ( is_single() ) && ! empty( $add_to['single'] ) ) ||
	// ( ( is_page() ) && ! empty( $add_to['page'] ) ) ||
	// ( ( is_home() ) && ! empty( $add_to['home'] ) ) ||
	// ( ( is_category() ) && ! empty( $add_to['category_archives'] ) ) ||
	// ( ( is_tag() ) && ! empty( $add_to['tag_archives'] ) ) ||
	// ( ( ( is_tax() ) || ( is_author() ) || ( is_date() ) ) && ! empty( $add_to['other_archives'] ) ) ) {

	// 	$x2b_code = get_crp( 'is_widget=0' );

	// 	return x2b_generate_content( $content, $x2b_code );

	// } else {
	// 	return $content;
	// }
}

/**
 * Content function with filter.
 *
 * @since 1.9
 */
function register_content_filter() {
	add_filter( 'the_content', '\X2board\Includes\filter_the_content' );
}

/**
 * Define a function to use {@see ModuleHandler::getModuleObject()} ($module_name, $type)
 *
 * @param string $module_name The module name to get a instance
 * @param string $type disp, proc, controller, class
 * @param string $kind admin, null
 * @return mixed Module instance
 */
function getModule($module_name, $type = 'view', $kind = '') {
	global $G_X2B_CACHE;
	if(!isset($G_X2B_CACHE['__MODULE_EXTEND__'])) {
		$G_X2B_CACHE['__MODULE_EXTEND__'] = array();
	}
	if(!isset($G_X2B_CACHE['_loaded_module'])) {
		$G_X2B_CACHE['_loaded_module'] = array();
	}
	if(!isset($G_X2B_CACHE['_called_constructor'])) {
		$G_X2B_CACHE['_called_constructor'] = array();
	}
	if(!isset($G_X2B_CACHE['__elapsed_class_load__'])) {
		$G_X2B_CACHE['__elapsed_class_load__'] = null;
	}

	return \X2board\Includes\Classes\ModuleHandler::getModuleInstance($module_name, $type, $kind);
}

/**
 * Create a controller instance of the module
 *
 * @param string $module_name The module name to get a controller instance
 * @return mixed Module controller instance
 */
function getController($module_name) {
	return getModule($module_name, 'controller');
}

/**
 * Create a admin controller instance of the module
 *
 * @param string $module_name The module name to get a admin controller instance
 * @return mixed Module admin controller instance
 */
function getAdminController($module_name) {
	return getModule($module_name, 'controller', 'admin');
}

/**
 * Create a view instance of the module
 *
 * @param string $module_name The module name to get a view instance
 * @return mixed Module view instance
 */
function getView($module_name) {
	return getModule($module_name, 'view');
}

/**
 * Create a admin view instance of the module
 *
 * @param string $module_name The module name to get a admin view instance
 * @return mixed Module admin view instance
 */
function getAdminView($module_name) {
	return getModule($module_name, 'view', 'admin');
}

/**
 * Create a model instance of the module
 *
 * @param string $module_name The module name to get a model instance
 * @return mixed Module model instance
 */
function getModel($module_name) {
	return getModule($module_name, 'model');
}

/**
 * Create an admin model instance of the module
 *
 * @param string $module_name The module name to get a admin model instance
 * @return mixed Module admin model instance
 */
function getAdminModel($module_name) {
	return getModule($module_name, 'model', 'admin');
}

/**
 * Create a class instance of the module
 *
 * @param string $module_name The module name to get a class instance
 * @return mixed Module class instance
 */
function getClass($module_name) {
	return getModule($module_name, 'class');
}

/**
 * Function to handle the result of DB::executeQuery() as an array
 *
 * @see DB::executeQuery()
 * @see executeQuery()
 * @param string $query_id (module name.query XML file)
 * @param object $args values of args object
 * @param string[] $arg_columns Column list
 * @return object Query result data
 */
function executeQueryArray($o_query, $arg_columns = NULL) {  // $query_id, $args = NULL, $arg_columns = NULL)
	// getPaginationSelect로 명칭 변경 예정
	$o_db = \X2board\Includes\Classes\DB::getInstance();
	$output = $o_db->executeQuery($o_query, $arg_columns);  // $args,
	// if(!is_array($output->data) && count((array)$output->data) > 0)
	// {
	// 	$output->data = array($output->data);
	// }
	return $output;  // $o_db->executeQuery() always outputs array 
}

/**
 * Alias of DB::getNextSequence()
 *
 * @see DB::getNextSequence()
 * @return int
 */
function getNextSequence() {
	// $o_db = \X2board\Includes\Classes\DB::getInstance();
	// $seq = $o_db->getNextSequence();
	// setUserSequence($seq);
	// return $seq;
	global $wpdb;
	$s_query = "INSERT INTO `{$wpdb->prefix}x2b_sequence` (seq) values ('0')";
	if ($wpdb->query($s_query) === FALSE) {
		wp_die($wpdb->last_error);
	} 		
	$seq = $wpdb->insert_id;
	if($seq % 10000 == 0)
	{
		$s_query = "delete from  `{$wpdb->prefix}x2b_sequence` where seq < ".$seq;
		if ($wpdb->query($s_query) === FALSE) {
			wp_die($wpdb->last_error);
		} 
	}
	setUserSequence($seq);
	return $seq;
}

/**
 * Set Sequence number to session
 *
 * @param int $seq sequence number
 * @return void
 */
function setUserSequence($seq) {
	$arr_seq = array();
	if(isset($_SESSION['seq']))	{
		if(!is_array($_SESSION['seq'])) {
			$_SESSION['seq'] = array($_SESSION['seq']);
		}
		$arr_seq = $_SESSION['seq'];
	}
	$arr_seq[] = $seq;
	$_SESSION['seq'] = $arr_seq;
}

/**
 * Check Sequence number grant
 *
 * @param int $seq sequence number
 * @return boolean
 */
function checkUserSequence($seq) {
	if(!isset($_SESSION['seq'])) {
		return false;
	}
	if(!in_array($seq, $_SESSION['seq'])) {
		return false;
	}
	return true;
}

/**
 * microtime() return
 *
 * @return float
 */
function getMicroTime() {
	list($time1, $time2) = explode(' ', microtime());
	return (float) $time1 + (float) $time2;
}

/**
 * This function is a shortcut to htmlspecialchars().
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @param bool $double_escape Set this to false to skip symbols that are already escaped (default: true)
 * @return string
 */
function escape($str, $double_escape = true, $escape_defined_lang_code = false) {
	if(!$escape_defined_lang_code && isDefinedLangCode($str)) return $str;

	$flags = ENT_QUOTES | ENT_SUBSTITUTE;
	return htmlspecialchars($str, $flags, 'UTF-8', $double_escape);
}

function isDefinedLangCode($str) {
	return preg_match('!^\$user_lang->([a-z0-9\_]+)$!is', trim($str));
}

/**
 * Change the time format YYYYMMDDHHIISS to the user defined format
 *
 * @param string|int $str YYYYMMDDHHIISS format time values
 * @param string $format Time format of php date() function
 * @param bool $conversion Means whether to convert automatically according to the language
 * @return string
 */
function zdate($str, $format = 'Y-m-d H:i:s', $conversion = TRUE) {
	if(!$str) {  // return null if no target time is specified
		return;
	}
	if($conversion == TRUE)	{  // convert the date format according to the language
		switch(substr(get_locale(), 0, 2)) {  // Context::getLangType()) {
			case 'en' :
			case 'es' :
				if($format == 'Y-m-d') {
					$format = 'M d, Y';
				}
				elseif($format == 'Y-m-d H:i:s') {
					$format = 'M d, Y H:i:s';
				}
				elseif($format == 'Y-m-d H:i') {
					$format = 'M d, Y H:i';
				}
				break;
			case 'vi' :
				if($format == 'Y-m-d') {
					$format = 'd-m-Y';
				}
				elseif($format == 'Y-m-d H:i:s') {
					$format = 'H:i:s d-m-Y';
				}
				elseif($format == 'Y-m-d H:i') {
					$format = 'H:i d-m-Y';
				}
				break;
		}
	}
	// If year value is less than 1970, handle it separately.
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
	else { // if year value is greater than 1970, get unixtime by using ztime() for date() function's argument. 
		$string = date($format, ztime($str));
	}
	// change day and am/pm for each language
	$a_unit_week = \X2board\Includes\Classes\Context::get( 'unit_week' ); //Context::getLang('unit_week');
	$a_unit_meridiem = \X2board\Includes\Classes\Context::get( 'unit_meridiem' ); //Context::getLang('unit_meridiem');
	$string = str_replace(array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), $a_unit_week, $string);
	$string = str_replace(array('am', 'pm', 'AM', 'PM'), $a_unit_meridiem, $string);
	return $string;
}

/**
 * Name of the month return
 *
 * @param int $month Month
 * @param boot $short If set, returns short string
 * @return string
 */
function getMonthName($month, $short = TRUE) {
	$short_month = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	$long_month = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	return !$short ? $long_month[$month] : $short_month[$month];
}

/**
 * YYYYMMDDHHIISS format changed to unix time value
 *
 * @param string $str Time value in format of YYYYMMDDHHIISS
 * @return int
 */
function ztime($str) {
	if(!$str) {
		return;
	}
	if (strlen($str) === 9 || (strlen($str) === 10 && $str <= 2147483647)) {
 		return intval($str);
 	}
	$hour = (int) substr($str, 8, 2);
	$min = (int) substr($str, 10, 2);
	$sec = (int) substr($str, 12, 2);
	$year = (int) substr($str, 0, 4);
	$month = (int) substr($str, 4, 2);
	$day = (int) substr($str, 6, 2);
	if(strlen($str) <= 8) {
		$gap = 0;
	}
	else {
		$gap = zgap();
	}
	return mktime($hour, $min, $sec, $month ? $month : 1, $day ? $day : 1, $year) + $gap;
}

/**
 * Get a time gap between server's timezone and XE's timezone
 *
 * @return int
 */
function zgap() {
	// $time_zone = $GLOBALS['_time_zone'];
	// if($time_zone < 0) {
	// 	$to = -1;
	// }
	// else {
	// 	$to = 1;
	// }
	$min       = 60 * get_option('gmt_offset');
	$sign      = $min < 0 ? "-" : "+";
	$absmin    = abs($min);
	$time_zone = sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60);
	$to = $time_zone < 0 ? -1 : 1;
	$t_hour = $absmin/60 * $to; // substr($time_zone, 1, 2) * $to;
	$t_min = $absmin%60 * $to; // substr($time_zone, 3, 2) * $to;
	$server_time_zone = date("O");
	// if($server_time_zone < 0) {
	// 	$so = -1;
	// }
	// else {
	// 	$so = 1;
	// }
	$so = $server_time_zone < 0 ? -1 : 1;
	$c_hour = substr($server_time_zone, 1, 2) * $so;
	$c_min = substr($server_time_zone, 3, 2) * $so;
	$g_min = $t_min - $c_min;
	$g_hour = $t_hour - $c_hour;
	$gap = $g_min * 60 + $g_hour * 60 * 60;
	return $gap;
}

/**
 * Pre-block the codes which may be hacking attempts
 *
 * @param string $content Taget content
 * @return string
 */
function removeHackTag($content)
{
	require_once X2B_PATH.'includes/classes/security/EmbedFilter.class.php';
	$oEmbedFilter = \X2board\Includes\Classes\Security\EmbedFilter::getInstance();
	$oEmbedFilter->check($content);
	// purifierHtml($content);  // too old purifier

	// change the specific tags to the common texts
	$content = preg_replace('@<(\/?(?:html|body|head|title|meta|base|link|script|style|applet)(/*).*?>)@i', '&lt;$1', $content);
	/**
	 * Remove codes to abuse the admin session in src by tags of images and video postings
	 * - Issue reported by Sangwon Kim
	 */
	$content = preg_replace_callback('@<(/?)([a-z]+[0-9]?)((?>"[^"]*"|\'[^\']*\'|[^>])*?\b(?:on[a-z]+|data|style|background|href|(?:dyn|low)?src)\s*=[\s\S]*?)(/?)($|>|<)@i',
									 '\X2board\Includes\removeSrcHack',
									  $content);
	$content = checkXmpTag($content);
	$content = blockWidgetCode($content);
	return $content;
}

/**
 * Remove src hack(preg_replace_callback)
 *
 * @param array $match
 * @return string
 */
function removeSrcHack($match) {
	$tag = strtolower($match[2]);

	// xmp tag ?뺣━
	if($tag == 'xmp') {
		return "<{$match[1]}xmp>";
	}
	if($match[1]) {
		return $match[0];
	}
	if($match[4]) {
		$match[4] = ' ' . $match[4];
	}

	$attrs = array();
	if(preg_match_all('/([\w:-]+)\s*=(?:\s*(["\']))?(?(2)(.*?)\2|([^ ]+))/s', $match[3], $m)) {
		foreach($m[1] as $idx => $name) {
			if(strlen($name) >= 2 && substr_compare($name, 'on', 0, 2) === 0) {
				continue;
			}

			$val = preg_replace_callback('/&#(?:x([a-fA-F0-9]+)|0*(\d+));/', function($n) {return chr($n[1] ? ('0x00' . $n[1]) : ($n[2] + 0)); }, $m[3][$idx] . $m[4][$idx]);
			$val = preg_replace('/^\s+|[\t\n\r]+/', '', $val);

			if(preg_match('/^[a-z]+script:/i', $val)) {
				continue;
			}
			$attrs[$name] = $val;
		}
	}

	$filter_arrts = array('style', 'src', 'href');

	if($tag === 'object') array_push($filter_arrts, 'data');
	if($tag === 'param') array_push($filter_arrts, 'value');

	foreach($filter_arrts as $attr) {
		if(!isset($attrs[$attr])) continue;

		$attr_value = rawurldecode($attrs[$attr]);
		$attr_value = htmlspecialchars_decode($attr_value, ENT_COMPAT);
		$attr_value = preg_replace('/\s+|[\t\n\r]+/', '', $attr_value);
		if(preg_match('@(\?|&|;)(act=(\w+))@i', $attr_value, $m) && $m[3] !== 'procFileDownload'){
			unset($attrs[$attr]);
		}
	}

	if(isset($attrs['style']) && preg_match('@(?:/\*|\*/|\n|:\s*expression\s*\()@i', $attrs['style'])) {
		unset($attrs['style']);
	}

	$attr = array();
	foreach($attrs as $name => $val) {
		if($tag == 'object' || $tag == 'embed' || $tag == 'a') {
			$attribute = strtolower(trim($name));
			if($attribute == 'data' || $attribute == 'src' || $attribute == 'href') {
				if(stripos($val, 'data:') === 0) {
					continue;
				}
			}
		}

		if($tag == 'img') {
			$attribute = strtolower(trim($name));
			if(stripos($val, 'data:') === 0) {
				continue;
			}
		}
		$val = str_replace('"', '&quot;', $val);
		$attr[] = $name . "=\"{$val}\"";
	}
	$attr = count($attr) ? ' ' . implode(' ', $attr) : '';
	return "<{$match[1]}{$tag}{$attr}{$match[4]}>";
}

// function purifierHtml(&$content) {
// 	require_once X2B_PATH.'includes/classes/security/Purifier.class.php';
// 	$oPurifier = \X2board\Includes\Classes\Security\Purifier::getInstance();
// 	// @see https://github.com/xpressengine/xe-core/issues/2278
// 	$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
// 	if($o_logged_info->is_admin !== 'Y') {
// 		$oPurifier->setConfig('HTML.Nofollow', true);
// 	}
// 	unset($o_logged_info);
// 	$oPurifier->purify($content);
// }

/**
 * blocking widget code
 *
 * @param string $content Taget content
 * @return string
 **/
function blockWidgetCode($s_content) {
	$s_content = preg_replace('/(<(?:img|div)(?:[^>]*))(widget)(?:(=([^>]*?)>))/is', '$1blocked-widget$3', $s_content);
	return $s_content;
}

/**
 * Check xmp tag, close it.
 *
 * @param string $content Target content
 * @return string
 */
function checkXmpTag($s_content) {
	$s_content = preg_replace('@<(/?)xmp.*?>@i', '<\1xmp>', $s_content);
	if(($start_xmp = strrpos($s_content, '<xmp>')) !== FALSE) {
		if(($close_xmp = strrpos($s_content, '</xmp>')) === FALSE) {
			$s_content .= '</xmp>';
		}
		else if($close_xmp < $start_xmp) {
			$s_content .= '</xmp>';
		}
	}
	return $s_content;
}

/**
 * 사용자 IP 주소를 반환한다.
 * @return string
 */
function get_remote_ip() {
	static $s_ip;
	if($s_ip === null){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$s_ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$s_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$s_ip = $_SERVER['REMOTE_ADDR'];
		}
	}
	return apply_filters('x2board_remote_ip', $s_ip);
}



/**
 * Get is current user crawler
 *
 * @param string $agent if set, use this value instead HTTP_USER_AGENT
 * @return bool
 */
function is_crawler($agent = NULL) {
	if(!$agent) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
	}

	$check_agent = array('bot', 'spider', 'spyder', 'crawl', 'http://', 'google', 'yahoo', 'slurp', 'yeti', 'daum', 'teoma', 'fish', 'hanrss', 'facebook', 'yandex', 'infoseek', 'askjeeves', 'stackrambler', 'python');
	$check_ip = array(
		/*'211.245.21.110-211.245.21.119' mixsh is closed */
	);

	foreach($check_agent as $str) {
		if(stristr($agent, $str) != FALSE) {
			return TRUE;
		}
	}
	return \X2board\Includes\Classes\IpFilter::filter($check_ip);
}

/**
 * Get a encoded url. Define a function to use Context::getUrl()
 *
 * getUrl() returns the URL transformed from given arguments of RequestURI
 * <ol>
 *  <li>argument format follows as (key, value).
 * ex) getUrl('key1', 'val1', 'key2',''): transform key1 and key2 to val1 and '' respectively</li>
 * <li>returns URL without the argument if no argument is given.</li>
 * <li>URL made of args_list added to RequestUri if the first argument value is ''.</li>
 * </ol>
 *
 * @return string
 */
// function getUrl() {
// function get_url() {  // this function is same with no_namespace.helper.php::x2b_get_url
// 	$n_num_args = func_num_args();
// 	$a_args_list = func_get_args();
// 	if($n_num_args) {
// 		$s_url = \X2board\Includes\Classes\Context::get_url($n_num_args, $a_args_list);
// 	}	
// 	else{ 
// 		$s_url = \X2board\Includes\Classes\Context::get_request_uri();
// 	}
// 	return preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $s_url);
// }

/**
 * get WP post ID that matches x2b post
 */
function get_wp_post_id_by_x2b_post_id($n_x2b_post_id) {
	global $wpdb;
	$n_x2b_post_id = esc_sql( $n_x2b_post_id );
	$n_wp_post_id = $wpdb->get_var("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_name`='$n_x2b_post_id' AND `post_type`='".X2B_DOMAIN."'");
	if(!$n_wp_post_id){
		$n_wp_post_id = $wpdb->get_var("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_name`='{$n_x2b_post_id}__trashed' AND `post_type`='".X2B_DOMAIN."'");
	}
	return intval($n_wp_post_id);
}

/**
 * Return the requested script path
 *
 * @return string
 */
// function getScriptPath() {
function get_script_path() {
	static $s_url = NULL;
	if($s_url == NULL) {
		$script_path = filter_var($_SERVER['SCRIPT_NAME'], FILTER_SANITIZE_STRING);
		$s_url = str_ireplace('/tools/', '/', preg_replace('/index.php.*/i', '', str_replace('\\', '/', $script_path)));
	}
	return $s_url;
}

/**
 * Remove embed media for admin
 *
 * @param string $content
 * @param int $writer_member_srl
 * @return void
 */
function stripEmbedTagForAdmin(&$s_content, $writer_member_id) {
	if(!\X2board\Includes\Classes\Context::get('is_logged')) {
		return;
	}
	// $oModuleModel = getModel('module');
	$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
	if($writer_member_id != $o_logged_info->ID && ($o_logged_info->is_admin == "Y" ) ) { //  || $oModuleModel->isSiteAdmin($logged_info)))
		if($writer_member_id) {
			// $oMemberModel = getModel('member');
			// $member_info = $oMemberModel->getMemberInfoByMemberSrl($writer_member_srl);
			$member_info = get_userdata($writer_member_id);
			if($member_info->roles[0] == "administrator") {
				return;
			}
		}
		$security_msg = "<div style='border: 1px solid #DDD; background: #FAFAFA; text-align:center; margin: 1em 0;'><p style='margin: 1em;'>" . __('security_warning_embed', 'x2board') . "</p></div>";
		$s_content = preg_replace('/<object[^>]+>(.*?<\/object>)?/is', $security_msg, $s_content);
		$s_content = preg_replace('/<embed[^>]+>(\s*<\/embed>)?/is', $security_msg, $s_content);
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
function getNumberingPath($no, $size = 3) {
	$mod = pow(10, $size);
	$output = sprintf('%0' . $size . 'd/', $no % $mod);
	if($no >= $mod)	{
		$output .= getNumberingPath((int) $no / $mod, $size);
	}
	return $output;
}

/**
 * check uploaded file which may be hacking attempts
 *
 * @param string $file Taget file path
 * @return bool
 */
function checkUploadedFile($file, $filename = null) {
	require_once X2B_PATH.'includes/classes/security/UploadFileFilter.class.php';
	return \X2board\Includes\Classes\Security\UploadFileFilter::check($file, $filename);
}

/**
 * Get a not encoded(html entity) url
 *
 * @see getUrl()
 * @return string
 */
function getNotEncodedUrl() {
	$num_args = func_num_args();
	$args_list = func_get_args();

	if($num_args) {
		$url = \X2board\Includes\Classes\Context::get_url($num_args, $args_list, NULL, FALSE);
	}
	else {
		$url = \X2board\Includes\Classes\Context::get_request_uri();
	}

	return preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $url);
}











///////////////////////////////
// define an empty function to avoid errors when iconv function doesn't exist
// if(!function_exists('iconv'))
// {
// 	eval('
// 		function iconv($in_charset, $out_charset, $str)
// 		{
// 			return $str;
// 		}
// 	');
// }

/**
 * Time zone
 * @var array
 */
// $time_zone = array(
// 	'-1200' => '[GMT -12:00] Baker Island Time',
// 	'-1100' => '[GMT -11:00] Niue Time, Samoa Standard Time',
// 	'-1000' => '[GMT -10:00] Hawaii-Aleutian Standard Time, Cook Island Time',
// 	'-0930' => '[GMT -09:30] Marquesas Islands Time',
// 	'-0900' => '[GMT -09:00] Alaska Standard Time, Gambier Island Time',
// 	'-0800' => '[GMT -08:00] Pacific Standard Time',
// 	'-0700' => '[GMT -07:00] Mountain Standard Time',
// 	'-0600' => '[GMT -06:00] Central Standard Time',
// 	'-0500' => '[GMT -05:00] Eastern Standard Time',
// 	'-0400' => '[GMT -04:00] Atlantic Standard Time',
// 	'-0330' => '[GMT -03:30] Newfoundland Standard Time',
// 	'-0300' => '[GMT -03:00] Amazon Standard Time, Central Greenland Time',
// 	'-0200' => '[GMT -02:00] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time',
// 	'-0100' => '[GMT -01:00] Azores Standard Time, Cape Verde Time, Eastern Greenland Time',
// 	'0000' => '[GMT  00:00] Western European Time, Greenwich Mean Time',
// 	'+0100' => '[GMT +01:00] Central European Time, West African Time',
// 	'+0200' => '[GMT +02:00] Eastern European Time, Central African Time',
// 	'+0300' => '[GMT +03:00] Moscow Standard Time, Eastern African Time',
// 	'+0330' => '[GMT +03:30] Iran Standard Time',
// 	'+0400' => '[GMT +04:00] Gulf Standard Time, Samara Standard Time',
// 	'+0430' => '[GMT +04:30] Afghanistan Time',
// 	'+0500' => '[GMT +05:00] Pakistan Standard Time, Yekaterinburg Standard Time',
// 	'+0530' => '[GMT +05:30] Indian Standard Time, Sri Lanka Time',
// 	'+0545' => '[GMT +05:45] Nepal Time',
// 	'+0600' => '[GMT +06:00] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time',
// 	'+0630' => '[GMT +06:30] Cocos Islands Time, Myanmar Time',
// 	'+0700' => '[GMT +07:00] Indochina Time, Krasnoyarsk Standard Time',
// 	'+0800' => '[GMT +08:00] China Standard Time, Australian Western Standard Time, Irkutsk Standard Time',
// 	'+0845' => '[GMT +08:45] Southeastern Western Australia Standard Time',
// 	'+0900' => '[GMT +09:00] Korea Standard Time, Japan Standard Time',
// 	'+0930' => '[GMT +09:30] Australian Central Standard Time',
// 	'+1000' => '[GMT +10:00] Australian Eastern Standard Time, Vladivostok Standard Time',
// 	'+1030' => '[GMT +10:30] Lord Howe Standard Time',
// 	'+1100' => '[GMT +11:00] Solomon Island Time, Magadan Standard Time',
// 	'+1130' => '[GMT +11:30] Norfolk Island Time',
// 	'+1200' => '[GMT +12:00] New Zealand Time, Fiji Time, Kamchatka Standard Time',
// 	'+1245' => '[GMT +12:45] Chatham Islands Time',
// 	'+1300' => '[GMT +13:00] Tonga Time, Phoenix Islands Time',
// 	'+1400' => '[GMT +14:00] Line Island Time'
// );

/**
 * Create a mobile instance of the module
 *
 * @param string $module_name The module name to get a mobile instance
 * @return mixed Module mobile instance
 */
// function &getMobile($module_name) {
// 	return getModule($module_name, 'mobile');
// }

/**
 * Create an api instance of the module
 *
 * @param string $module_name The module name to get a api instance
 * @return mixed Module api class instance
 */
// function getAPI($module_name)
// {
// 	return getModule($module_name, 'api');
// }

/**
 * Create a wap instance of the module
 *
 * @param string $module_name The module name to get a wap instance
 * @return mixed Module wap class instance
 */
// function getWAP($module_name)
// {
// 	return getModule($module_name, 'wap');
// }

/**
 * The alias of DB::executeQuery()
 *
 * @see DB::executeQuery()
 * @param string $query_id (module name.query XML file)
 * @param object $args values of args object
 * @param string[] $arg_columns Column list
 * @return object Query result data
 */
// function executeQuery($query_id, $args = NULL, $arg_columns = NULL)
// {
// 	$oDB = DB::getInstance();
// 	return $oDB->executeQuery($query_id, $args, $arg_columns);
// }

/**
 * Get a encoded url. If url is encoded, not encode. Otherwise html encode the url.
 *
 * @see getUrl()
 * @return string
 */
// function getAutoEncodedUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();

// 	if($num_args)
// 	{
// 		$url = Context::getUrl($num_args, $args_list, NULL, TRUE, TRUE);
// 	}
// 	else
// 	{
// 		$url = Context::getRequestUri();
// 	}

// 	return preg_replace('@\berror_return_url=[^&]*|\w+=(?:&|$)@', '', $url);
// }

/**
 * Return the value adding request uri to getUrl() to get the full url
 *
 * @return string
 */
// function getFullUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();
// 	$request_uri = Context::getRequestUri();
// 	if(!$num_args)
// 	{
// 		return $request_uri;
// 	}

// 	$url = Context::getUrl($num_args, $args_list);
// 	if(strncasecmp('http', $url, 4) !== 0)
// 	{
// 		preg_match('/^(http|https):\/\/([^\/]+)\//', $request_uri, $match);
// 		return substr($match[0], 0, -1) . $url;
// 	}
// 	return $url;
// }

/**
 * Return the value adding request uri to getUrl() to get the not encoded full url
 *
 * @return string
 */
// function getNotEncodedFullUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();
// 	$request_uri = Context::getRequestUri();
// 	if(!$num_args)
// 	{
// 		return $request_uri;
// 	}

// 	$url = Context::getUrl($num_args, $args_list, NULL, FALSE);
// 	if(strncasecmp('http', $url, 4) !== 0)
// 	{
// 		preg_match('/^(http|https):\/\/([^\/]+)\//', $request_uri, $match);
// 		$url = Context::getUrl($num_args, $args_list, NULL, FALSE);
// 		return substr($match[0], 0, -1) . $url;
// 	}
// 	return $url;
// }

/**
 * getSiteUrl() returns the URL by transforming the given argument value of domain
 * The first argument should consist of domain("http://" not included) and path
 * 
 * @return string
 */
// function getSiteUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();

// 	if(!$num_args)
// 	{
// 		return Context::getRequestUri();
// 	}

// 	$domain = array_shift($args_list);
// 	$num_args = count($args_list);

// 	return Context::getUrl($num_args, $args_list, $domain);
// }

/**
 * getSiteUrl() returns the not encoded URL by transforming the given argument value of domain
 * The first argument should consist of domain("http://" not included) and path
 * 
 * @return string
 */
// function getNotEncodedSiteUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();

// 	if(!$num_args)
// 	{
// 		return Context::getRequestUri();
// 	}

// 	$domain = array_shift($args_list);
// 	$num_args = count($args_list);

// 	return Context::getUrl($num_args, $args_list, $domain, FALSE);
// }

/**
 * Return the value adding request uri to the getSiteUrl() To get the full url
 *
 * @return string
 */
// function getFullSiteUrl()
// {
// 	$num_args = func_num_args();
// 	$args_list = func_get_args();

// 	$request_uri = Context::getRequestUri();
// 	if(!$num_args)
// 	{
// 		return $request_uri;
// 	}

// 	$domain = array_shift($args_list);
// 	$num_args = count($args_list);

// 	$url = Context::getUrl($num_args, $args_list, $domain);
// 	if(strncasecmp('http', $url, 4) !== 0)
// 	{
// 		preg_match('/^(http|https):\/\/([^\/]+)\//', $request_uri, $match);
// 		return substr($match[0], 0, -1) . $url;
// 	}
// 	return $url;
// }

/**
 * Return the exact url of the current page
 *
 * @return string
 */
// function getCurrentPageUrl()
// {
// 	$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
// 	$url = $protocol . $_SERVER['HTTP_HOST'] . preg_replace('/[<>"]/', '', $_SERVER['REQUEST_URI']);
// 	return htmlspecialchars($url, ENT_COMPAT, 'UTF-8', FALSE);
// }

/**
 * Return if domain of the virtual site is url type or id type
 *
 * @param string $domain
 * @return bool
 */
// function isSiteID($domain)
// {
// 	return preg_match('/^([a-zA-Z0-9\_]+)$/', $domain);
// }

/**
 * Put a given tail after trimming string to the specified size
 *
 * @param string $string The original string to trim
 * @param int $cut_size The size to be
 * @param string $tail Tail to put in the end of the string after trimming
 * @return string
 */
// function cut_str($string, $cut_size = 0, $tail = '...')
// {
// 	if($cut_size < 1 || !$string)
// 	{
// 		return $string;
// 	}

// 	if($GLOBALS['use_mb_strimwidth'] || function_exists('mb_strimwidth'))
// 	{
// 		$GLOBALS['use_mb_strimwidth'] = TRUE;
// 		return mb_strimwidth($string, 0, $cut_size + 4, $tail, 'utf-8');
// 	}

// 	$chars = array(12, 4, 3, 5, 7, 7, 11, 8, 4, 5, 5, 6, 6, 4, 6, 4, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 4, 4, 8, 6, 8, 6, 10, 8, 8, 9, 8, 8, 7, 9, 8, 3, 6, 7, 7, 11, 8, 9, 8, 9, 8, 8, 7, 8, 8, 10, 8, 8, 8, 6, 11, 6, 6, 6, 4, 7, 7, 7, 7, 7, 3, 7, 7, 3, 3, 6, 3, 9, 7, 7, 7, 7, 4, 7, 3, 7, 6, 10, 6, 6, 7, 6, 6, 6, 9);
// 	$max_width = $cut_size * $chars[0] / 2;
// 	$char_width = 0;

// 	$string_length = strlen($string);
// 	$char_count = 0;

// 	$idx = 0;
// 	while($idx < $string_length && $char_count < $cut_size && $char_width <= $max_width)
// 	{
// 		$c = ord(substr($string, $idx, 1));
// 		$char_count++;
// 		if($c < 128)
// 		{
// 			$char_width += (int) $chars[$c - 32];
// 			$idx++;
// 		}
// 		else if(191 < $c && $c < 224)
// 		{
// 			$char_width += $chars[4];
// 			$idx += 2;
// 		}
// 		else
// 		{
// 			$char_width += $chars[0];
// 			$idx += 3;
// 		}
// 	}

// 	$output = substr($string, 0, $idx);
// 	if(strlen($output) < $string_length)
// 	{
// 		$output .= $tail;
// 	}

// 	return $output;
// }

/**
 * If the recent post within a day, output format of YmdHis is "min/hours ago from now". If not within a day, it return format string.
 *
 * @param string $date Time value in format of YYYYMMDDHHIISS
 * @param string $format If gap is within a day, returns this format.
 * @return string
 */
// function getTimeGap($date, $format = 'Y.m.d')
// {
// 	$gap = $_SERVER['REQUEST_TIME'] + zgap() - ztime($date);

// 	$lang_time_gap = Context::getLang('time_gap');
// 	if($gap < 60)
// 	{
// 		$buff = sprintf($lang_time_gap['min'], (int) ($gap / 60) + 1);
// 	}
// 	elseif($gap < 60 * 60)
// 	{
// 		$buff = sprintf($lang_time_gap['mins'], (int) ($gap / 60) + 1);
// 	}
// 	elseif($gap < 60 * 60 * 2)
// 	{
// 		$buff = sprintf($lang_time_gap['hour'], (int) ($gap / 60 / 60) + 1);
// 	}
// 	elseif($gap < 60 * 60 * 24)
// 	{
// 		$buff = sprintf($lang_time_gap['hours'], (int) ($gap / 60 / 60) + 1);
// 	}
// 	else
// 	{
// 		$buff = zdate($date, $format);
// 	}

// 	return $buff;
// }

/**
 * Returns encoded value of given email address for email scraping
 *
 * @param string $email The email
 * @return string
 */
// function getEncodeEmailAddress($email)
// {
// 	$return = '';
// 	for($i = 0, $c = strlen($email); $i < $c; $i++)
// 	{
// 		$return .= '&#' . (rand(0, 1) == 0 ? ord($email[$i]) : 'X' . dechex(ord($email[$i]))) . ';';
// 	}
// 	return $return;
// }

/**
 * Prints debug messages 
 *
 * Display $buff contents into the file ./files/_debug_message.php.
 * You can see the file on your prompt by command: tail-f./files/_debug_message.php
 *
 * @param mixed $debug_output Target object to be printed
 * @param bool $display_option boolean Flag whether to print seperator (default:true)
 * @param string $file Target file name
 * @return void
 */
/*function debugPrint($debug_output = NULL, $display_option = TRUE, $file = '_debug_message.php')
{
	static $debug_file;

	if(!(__DEBUG__ & 1))
	{
		return;
	}

	static $firephp;
	$bt = debug_backtrace();
	if(is_array($bt))
	{
		$bt_debug_print = array_shift($bt);
		$bt_called_function = array_shift($bt);
	}
	$file_name = str_replace(_XE_PATH_, '', $bt_debug_print['file']);
	$line_num = $bt_debug_print['line'];
	$function = $bt_called_function['class'] . $bt_called_function['type'] . $bt_called_function['function'];

	if(__DEBUG_OUTPUT__ == 2 && version_compare(PHP_VERSION, '6.0.0') === -1)
	{
		if(!isset($firephp))
		{
			$firephp = FirePHP::getInstance(TRUE);
		}
		$type = FirePHP::INFO;

		$label = sprintf('[%s:%d] %s() (Memory usage: current=%s, peak=%s)', $file_name, $line_num, $function, FileHandler::filesize(memory_get_usage()), FileHandler::filesize(memory_get_peak_usage()));

		// Check a FirePHP option
		if($display_option === 'TABLE')
		{
			$label = $display_option;
		}
		if($display_option === 'ERROR')
		{
			$type = $display_option;
		}
		// Check if the IP specified by __DEBUG_PROTECT__ option is same as the access IP.
		if(__DEBUG_PROTECT__ === 1 && __DEBUG_PROTECT_IP__ != $_SERVER['REMOTE_ADDR'])
		{
			$debug_output = 'The IP address is not allowed. Change the value of __DEBUG_PROTECT_IP__ into your IP address in config/config.user.inc.php or config/config.inc.php';
			$label = NULL;
		}

		$firephp->fb($debug_output, $label, $type);
	}
	else
	{
		if(__DEBUG_PROTECT__ === 1 && __DEBUG_PROTECT_IP__ != $_SERVER['REMOTE_ADDR'])
		{
			return;
		}

		$print = array();
		if(!$debug_file)
		{
			$debug_file = _XE_PATH_ . 'files/' . $file;
		}
		if(!file_exists($debug_file)) $print[] = '<?php exit() ?>';

		if($display_option === TRUE || $display_option === 'ERROR')
		{
			$print[] = sprintf("[%s %s:%d] %s() - mem(%s)", date('Y-m-d H:i:s'), $file_name, $line_num, $function, FileHandler::filesize(memory_get_usage()));;
			$print[] = str_repeat('=', 80);
		}
		$type = gettype($debug_output);
		if(!in_array($type, array('array', 'object', 'resource')))
		{
			if($display_option === 'ERROR')
			{
				$print[] = 'ERROR : ' . var_export($debug_output, TRUE);
			}
			else
			{
				$print[] = 'DEBUG : ' . $type . '(' . var_export($debug_output, TRUE) . ')';
			}
		}
		else
		{
			$print[] = 'DEBUG : ' . trim(preg_replace('/\r?\n/', "\n" . '        ', print_r($debug_output, true)));
		}
		$backtrace_args = defined('\DEBUG_BACKTRACE_IGNORE_ARGS') ? \DEBUG_BACKTRACE_IGNORE_ARGS : 0;
		$backtrace = debug_backtrace($backtrace_args);

		if(count($backtrace) > 1 && $backtrace[1]['function'] === 'debugPrint' && !$backtrace[1]['class'])
		{
			array_shift($backtrace);
		}
		foreach($backtrace as $val)
		{
			$print[] = '        - ' . $val['file'] . ' : ' . $val['line'];
		}
		$print[] = PHP_EOL;
		@file_put_contents($debug_file, implode(PHP_EOL, $print), FILE_APPEND|LOCK_EX);
	}
}*/

/**
 * @param string $type query, trigger
 * @param float $elapsed_time
 * @param object $obj
 */
/*function writeSlowlog($type, $elapsed_time, $obj)
{
	if(!__LOG_SLOW_TRIGGER__ && !__LOG_SLOW_ADDON__ && !__LOG_SLOW_WIDGET__ && !__LOG_SLOW_QUERY__) return;

	static $log_filename = array(
		'query' => 'files/_slowlog_query.php',
		'trigger' => 'files/_slowlog_trigger.php',
		'addon' => 'files/_slowlog_addon.php',
		'widget' => 'files/_slowlog_widget.php'
	);
	$write_file = true;

	$log_file = _XE_PATH_ . $log_filename[$type];

	$buff = array();
	$buff[] = '<?php exit(); ?>';
	$buff[] = date('c');

	if($type == 'trigger' && __LOG_SLOW_TRIGGER__ > 0 && $elapsed_time > __LOG_SLOW_TRIGGER__)
	{
		$buff[] = "\tCaller : " . $obj->caller;
		$buff[] = "\tCalled : " . $obj->called;
	}
	else if($type == 'addon' && __LOG_SLOW_ADDON__ > 0 && $elapsed_time > __LOG_SLOW_ADDON__)
	{
		$buff[] = "\tAddon : " . $obj->called;
		$buff[] = "\tCalled position : " . $obj->caller;
	}
	else if($type == 'widget' && __LOG_SLOW_WIDGET__ > 0 && $elapsed_time > __LOG_SLOW_WIDGET__)
	{
		$buff[] = "\tWidget : " . $obj->called;
	}
	else if($type == 'query' && __LOG_SLOW_QUERY__ > 0 && $elapsed_time > __LOG_SLOW_QUERY__)
	{

		$buff[] = $obj->query;
		$buff[] = "\tQuery ID   : " . $obj->query_id;
		$buff[] = "\tCaller     : " . $obj->caller;
		$buff[] = "\tConnection : " . $obj->connection;
	}
	else
	{
		$write_file = false;
	}

	if($write_file)
	{
		$buff[] = sprintf("\t%0.6f sec", $elapsed_time);
		$buff[] = PHP_EOL . PHP_EOL;
		file_put_contents($log_file, implode(PHP_EOL, $buff), FILE_APPEND);
	}

	if($type != 'query')
	{
		$trigger_args = $obj;
		$trigger_args->_log_type = $type;
		$trigger_args->_elapsed_time = $elapsed_time;
		ModuleHandler::triggerCall('XE.writeSlowlog', 'after', $trigger_args);
	}
}*/

/**
 * @param void
 */
// function flushSlowlog()
// {
// 	$trigger_args = new stdClass();
// 	$trigger_args->_log_type = 'flush';
// 	$trigger_args->_elapsed_time = 0;
// 	ModuleHandler::triggerCall('XE.writeSlowlog', 'after', $trigger_args);
// }

/**
 * Delete the second object vars from the first argument
 *
 * @param object $target_obj An original object
 * @param object $del_obj BaseObject vars to delete from the original object
 * @return object
 */
// function delObjectVars($target_obj, $del_obj)
// {
// 	if(!is_object($target_obj))
// 	{
// 		return;
// 	}
// 	if(!is_object($del_obj))
// 	{
// 		return;
// 	}

// 	$target_vars = get_object_vars($target_obj);
// 	$del_vars = get_object_vars($del_obj);

// 	$target = array_keys($target_vars);
// 	$del = array_keys($del_vars);
// 	if(!count($target) || !count($del))
// 	{
// 		return $target_obj;
// 	}

// 	$return_obj = new stdClass();

// 	$target_count = count($target);
// 	for($i = 0; $i < $target_count; $i++)
// 	{
// 		$target_key = $target[$i];
// 		if(!in_array($target_key, $del))
// 		{
// 			$return_obj->{$target_key} = $target_obj->{$target_key};
// 		}
// 	}

// 	return $return_obj;
// }

// function getDestroyXeVars(&$vars)
// {
// 	$del_vars = array('error_return_url', 'success_return_url', 'ruleset', 'xe_validator_id');

// 	foreach($del_vars as $var)
// 	{
// 		if(is_array($vars)) unset($vars[$var]);
// 		else if(is_object($vars)) unset($vars->$var);
// 	}

// 	return $vars;
// }

/**
 * Change error_handing to debugPrint on php5 higher 
 *
 * @param int $errno
 * @param string $errstr
 * @param string $file
 * @param int $line
 * @return void
 */
// function handleError($errno, $errstr, $file, $line)
// {
// 	if(!__DEBUG__)
// 	{
// 		return;
// 	}
// 	$errors = array(E_USER_ERROR, E_ERROR, E_PARSE);
// 	if(!in_array($errno, $errors))
// 	{
// 		return;
// 	}

// 	$output = sprintf("Fatal error : %s - %d", $file, $line);
// 	$output .= sprintf("%d - %s", $errno, $errstr);

// 	debugPrint($output);
// }

/**
 * Decode the URL in Korean
 *
 * @param string $str The url
 * @return string
 */
// function url_decode($str)
// {
// 	return preg_replace('/%u([[:alnum:]]{4})/', '&#x\\1;', $str);
// }

// convert hexa value to RGB
// if(!function_exists('hexrgb'))
// {

// 	/**
// 	 * Convert hexa value to RGB
// 	 *
// 	 * @param string $hexstr
// 	 * @return array
// 	 */
// 	function hexrgb($hexstr)
// 	{
// 		$int = hexdec($hexstr);

// 		return array('red' => 0xFF & ($int >> 0x10),
// 			'green' => 0xFF & ($int >> 0x8),
// 			'blue' => 0xFF & $int);
// 	}

// }

/**
 * Php function for mysql old_password()
 * provides backward compatibility for zero board4 which uses old_password() of mysql 4.1 earlier versions. 
 * the function implemented by referring to the source codes of password.c file in mysql
 *
 * @param string $password
 * @return string
 */
// function mysql_pre4_hash_password($password)
// {
// 	$nr = 1345345333;
// 	$add = 7;
// 	$nr2 = 0x12345671;

// 	settype($password, "string");

// 	for($i = 0; $i < strlen($password); $i++)
// 	{
// 		if($password[$i] == ' ' || $password[$i] == '\t')
// 		{
// 			continue;
// 		}
// 		$tmp = ord($password[$i]);
// 		$nr ^= ((($nr & 63) + $add) * $tmp) + ($nr << 8);
// 		$nr2 += ($nr2 << 8) ^ $nr;
// 		$add += $tmp;
// 	}
// 	$result1 = sprintf("%08lx", $nr & ((1 << 31) - 1));
// 	$result2 = sprintf("%08lx", $nr2 & ((1 << 31) - 1));

// 	if($result1 == '80000000')
// 	{
// 		$nr += 0x80000000;
// 	}
// 	if($result2 == '80000000')
// 	{
// 		$nr2 += 0x80000000;
// 	}

// 	return sprintf("%08lx%08lx", $nr, $nr2);
// }

/**
 * Return the requested script path
 *
 * @return string
 */
// function getRequestUriByServerEnviroment()
// {
// 	return str_replace('<', '&lt;', preg_replace('/[<>"]/', '', $_SERVER['REQUEST_URI']));
// }

/**
 * PHP unescape function of javascript's escape
 * Function converts an Javascript escaped string back into a string with specified charset (default is UTF-8).
 * Modified function from http://pure-essence.net/stuff/code/utf8RawUrlDecode.phps
 *
 * @param string $source
 * @return string
 */
// function utf8RawUrlDecode($source)
// {
// 	$decodedStr = '';
// 	$pos = 0;
// 	$len = strlen($source);
// 	while($pos < $len)
// 	{
// 		$charAt = substr($source, $pos, 1);
// 		if($charAt == '%')
// 		{
// 			$pos++;
// 			$charAt = substr($source, $pos, 1);
// 			if($charAt == 'u')
// 			{
// 				// we got a unicode character
// 				$pos++;
// 				$unicodeHexVal = substr($source, $pos, 4);
// 				$unicode = hexdec($unicodeHexVal);
// 				$decodedStr .= _code2utf($unicode);
// 				$pos += 4;
// 			}
// 			else
// 			{
// 				// we have an escaped ascii character
// 				$hexVal = substr($source, $pos, 2);
// 				$decodedStr .= chr(hexdec($hexVal));
// 				$pos += 2;
// 			}
// 		}
// 		else
// 		{
// 			$decodedStr .= $charAt;
// 			$pos++;
// 		}
// 	}
// 	return $decodedStr;
// }

/**
 * Returns utf-8 string of given code
 *
 * @param int $num
 * @return string
 */
// function _code2utf($num)
// {
// 	if($num < 128)
// 	{
// 		return chr($num);
// 	}
// 	if($num < 2048)
// 	{
// 		return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
// 	}
// 	if($num < 65536)
// 	{
// 		return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
// 	}
// 	if($num < 2097152)
// 	{
// 		return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
// 	}
// 	return '';
// }

/**
 * Get whether utf8 or not given string
 *
 * @param string $string
 * @param bool $return_convert If set, returns converted string
 * @param bool $urldecode
 * @return bool|string
 */
// function detectUTF8($string, $return_convert = FALSE, $urldecode = TRUE)
// {
// 	if($urldecode)
// 	{
// 		$string = urldecode($string);
// 	}

// 	$sample = iconv('utf-8', 'utf-8', $string);
// 	$is_utf8 = (md5($sample) === md5($string));

// 	if(!$urldecode)
// 	{
// 		$string = urldecode($string);
// 	}

// 	if($return_convert)
// 	{
// 		return ($is_utf8) ? $string : iconv('euc-kr', 'utf-8', $string);
// 	}

// 	return $is_utf8;
// }

/**
 * get json encoded string of data
 *
 * @param mixed $data
 * @return string
 */
// function json_encode2($data)
// {
// 	switch(gettype($data))
// 	{
// 		case 'boolean':
// 			return $data ? 'true' : 'false';
// 		case 'integer':
// 		case 'double':
// 			return $data;
// 		case 'string':
// 			return '"' . strtr($data, array('\\' => '\\\\', '"' => '\\"')) . '"';
// 		case 'object':
// 			$data = get_object_vars($data);
// 		case 'array':
// 			$rel = FALSE; // relative array?
// 			$key = array_keys($data);
// 			foreach($key as $v)
// 			{
// 				if(!is_int($v))
// 				{
// 					$rel = TRUE;
// 					break;
// 				}
// 			}

// 			$arr = array();
// 			foreach($data as $k => $v)
// 			{
// 				$arr[] = ($rel ? '"' . strtr($k, array('\\' => '\\\\', '"' => '\\"')) . '":' : '') . json_encode2($v);
// 			}

// 			return $rel ? '{' . join(',', $arr) . '}' : '[' . join(',', $arr) . ']';
// 		default:
// 			return '""';
// 	}
// }

/**
 * Require pear
 *
 * @return void
 */
// function requirePear()
// {
// 	static $required = false;
// 	if($required)
// 	{
// 		return;
// 	}
// 	$sPhpVerLevel = 5;  // this is for /classes/file/FileHandler.class.php::getRemoteFile()

// 	if(version_compare(PHP_VERSION, "5.3.0") < 0)
// 	{
// 		set_include_path(_XE_PATH_ . "libs/PEAR" . PATH_SEPARATOR . get_include_path());
// 	}
// 	elseif(version_compare(PHP_VERSION, "7.5.0") < 0)
// 	{
// 		set_include_path(_XE_PATH_ . "libs/PEAR.1.9.5" . PATH_SEPARATOR . get_include_path());
// 		$sPhpVerLevel = 7;
// 	}
// 	else  // PHP8.0  XE1 can't updage over PHP8.0 as PHP8.1 deprecates $GLOBALS var
// 	{
// 		set_include_path(_XE_PATH_ . "libs/PEAR.1.10.13" . PATH_SEPARATOR . get_include_path());
// 		$sPhpVerLevel = 8;
// 	}

// 	$required = true;
// 	return $sPhpVerLevel;
// }

// function checkCSRF()
// {
// 	// Patch Begin (2018-04-26 22:56:23) singleview.co.kr
// 	//$sTargetAct = Context::get('act');
// 	//$sTargetMode = Context::get('mode');
// 	$aAllowModule = array('svpg', 'svorder', 'svauth');
// 	$sTargetModule = Context::get('module');
// 	if(in_array($sTargetModule, $aAllowModule))
// 		return TRUE;
// 	// Patch End (2018-04-26 22:56:23) singleview.co.kr 

// 	if($_SERVER['REQUEST_METHOD'] != 'POST')
// 	{
// 		return FALSE;
// 	}

// 	$default_url = Context::getDefaultUrl();
// 	$referer = $_SERVER["HTTP_REFERER"];

// 	if(strpos($default_url, 'xn--') !== FALSE && strpos($referer, 'xn--') === FALSE)
// 	{
// 		require_once(_XE_PATH_ . 'libs/idna_convert/idna_convert.class.php');
// 		$IDN = new idna_convert(array('idn_version' => 2008));
// 		$referer = $IDN->encode($referer);
// 	}

// 	$default_url = parse_url($default_url);
// 	$referer = parse_url($referer);

// 	$oModuleModel = getModel('module');
// 	$siteModuleInfo = $oModuleModel->getDefaultMid();

// 	if($siteModuleInfo->site_srl == 0)
// 	{
// 		if($default_url['host'] !== $referer['host'])
// 		{
// 			return FALSE;
// 		}
// 	}
// 	else
// 	{
// 		$virtualSiteInfo = $oModuleModel->getSiteInfo($siteModuleInfo->site_srl);
// 		if(strtolower($virtualSiteInfo->domain) != strtolower(Context::get('vid')) && !strstr(strtolower($virtualSiteInfo->domain), strtolower($referer['host'])))
// 		{
// 			return FALSE;
// 		}
// 	}

// 	return TRUE;
// }

/**
 * menu exposure check by isShow column
 * @param array $menu
 * @return void
 */
// function recurciveExposureCheck(&$menu)
// {
// 	if(is_array($menu))
// 	{
// 		foreach($menu AS $key=>$value)
// 		{
// 			if(!$value['isShow'])
// 			{
// 				unset($menu[$key]);
// 			}
// 			if(is_array($value['list']) && count($value['list']) > 0)
// 			{
// 				recurciveExposureCheck($menu[$key]['list']);
// 			}
// 		}
// 	}
// }

// function changeValueInUrl($key, $requestKey, $dbKey, $urlName = 'success_return_url')
// {
// 	if($requestKey != $dbKey)
// 	{
// 		$arrayUrl = parse_url(Context::get('success_return_url'));
// 		if($arrayUrl['query'])
// 		{
// 			parse_str($arrayUrl['query'], $parsedStr);

// 			if(isset($parsedStr[$key]))
// 			{
// 				$parsedStr[$key] = $requestKey;
// 				$successReturnUrl .= $arrayUrl['path'].'?'.http_build_query($parsedStr);
// 				Context::set($urlName, $successReturnUrl);
// 			}
// 		}
// 	}
// }

/**
 * Print raw html header
 *
 * @return void
 */
// function htmlHeader()
// {
// 	echo '<!DOCTYPE html>
// <html lang="ko">
// <head>
// <meta charset="utf-8" />
// </head>
// <body>';
// }

/**
 * Print raw html footer
 *
 * @return void
 */
// function htmlFooter()
// {
// 	echo '</body></html>';
// }

/**
 * Print raw alert message script
 *
 * @param string $msg
 * @return void
 */
// function alertScript($msg)
// {
// 	if(!$msg)
// 	{
// 		return;
// 	}

// 	echo '<script type="text/javascript">
// //<![CDATA[
// alert("' . $msg . '");
// //]]>
// </script>';
// }

/**
 * Print raw close window script
 *
 * @return void
 */
// function closePopupScript()
// {
// 	echo '<script type="text/javascript">
// //<![CDATA[
// window.close();
// //]]>
// </script>';
// }

/**
 * Print raw reload script
 *
 * @param bool $isOpener
 * @return void
 */
// function reload($isOpener = FALSE)
// {
// 	$reloadScript = $isOpener ? 'window.opener.location.reload()' : 'document.location.reload()';

// 	echo '<script type="text/javascript">
// //<![CDATA[
// ' . $reloadScript . '
// //]]>
// </script>';
// }

/**
 * This function escapes a string to be used in a CSS property.
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @return string
 */
// function escape_css($str)
// {
// 	return preg_replace('/[^a-zA-Z0-9_.#\/-]/', '', $str);
// }

/**
 * This function escapes a string to be used in a JavaScript string literal.
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @return string
 */
// function escape_js($str)
// {
// 	$flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
// 	$str = json_encode((string)$str, $flags);
// 	return substr($str, 1, strlen($str) - 2);
// }

/**
 * This function escapes a string to be used in a 'single-quoted' PHP string literal.
 * Null bytes are removed.
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @return string
 */
// function escape_sqstr($str)
// {
// 	return str_replace(array('\\0', '\\"'), array('', '"'), addslashes($str));
// }

/**
 * This function escapes a string to be used in a "double-quoted" PHP string literal.
 * Null bytes are removed.
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $str The string to escape
 * @return string
 */
// function escape_dqstr($str)
// {
// 	return str_replace(array('\\0', "\\'", '$'), array('', "'", '\\$'), addslashes($str));
// }

/**
 * This function splits a string into an array, but allows the delimter to be escaped.
 * For example, 'A|B\|C|D' will be split into 'A', 'B|C', and 'D'
 * because the bar between B and C is escaped.
 *
 * @copyright Rhymix Developers and Contributors
 * @link https://github.com/rhymix/rhymix
 *
 * @param string $delimiter The delimiter
 * @param string $str The string to split
 * @param int $limit The maximum number of items to return, 0 for unlimited (default: 0)
 * @param string $escape_char The escape character (default: backslash)
 * @return array
 */
// function explode_with_escape($delimiter, $str, $limit = 0, $escape_char = '\\')
// {
// 	if ($limit < 1) $limit = null;
// 	$result = array();
// 	$split = preg_split('/(?<!' . preg_quote($escape_char, '/') . ')' . preg_quote($delimiter, '/') . '/', $str, $limit);
// 	foreach ($split as $piece)
// 	{
// 		if (trim($piece) !== '')
// 		{
// 			$result[] = trim(str_replace($escape_char . $delimiter, $delimiter, $piece));
// 		}
// 	}
// 	return $result;
// }
/* End of file func.inc.php */