<?php
namespace X2board\Includes\Classes;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Skin {
	const SKIN_ABS_PATH = 'includes/modules/board/skins/';
	private static $instance;
	private $active = array();
	private $list;
	private $latestview_list;
	private $merged_list;
	
	private function __construct(){
		$s_dir = X2B_PATH . self::SKIN_ABS_PATH;
		$s_url = X2B_URL . self::SKIN_ABS_PATH;
		if( $h_dir = @opendir($s_dir) ) {
			while(($name = readdir($h_dir)) !== false){
				if($name == '.' || $name == '..' || $name == 'readme.txt' || $name == '__MACOSX' || $name == '.git') continue;
				$skin = new \stdClass();
				$skin->name = $name;
				$skin->dir = $s_dir . $name;
				$skin->url = $s_url . $name;
				$this->list[$name] = $skin;
			}
		}
		if( is_resource($h_dir) ) {
			closedir($h_dir);
		}
		
		$this->list = apply_filters('x2board_skin_list', $this->list);
		$this->latestview_list = apply_filters('x2board_skin_latestview_list', $this->list);
		$this->merged_list = array_merge($this->list, $this->latestview_list);
		
		ksort($this->list);
		// ksort($this->latestview_list);
		ksort($this->merged_list);
	}
	
	/**
	 * singleton
	 * @return \X2board\Includes\Classes\Skin
	 */
	public static function getInstance(){
		if(!self::$instance) self::$instance = new Skin();
		return self::$instance;
	}

	/**
	 * 스킨을 불러온다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	public function render($skin_name, $file){  //, $vars=array()){
		ob_start();
		
		$current_file_path = '';

		if(isset($this->merged_list[$skin_name])){
			extract(\X2board\Includes\Classes\Context::getAll4Skin(), EXTR_SKIP);
// var_dump(\X2board\Includes\Classes\Context::getAll4Skin());
// var_dump($page_navigation);
			
// var_dump($this->merged_list[$skin_name]);
			// $is_ajax = false;
			// if(defined('DOING_AJAX') && DOING_AJAX){
			// 	if(file_exists("{$this->merged_list[$skin_name]->dir}/ajax-{$file}")){
			// 		$is_ajax = true;
			// 	}
			// }
			
			// $is_admin = false;
			// if(is_admin()){
			// 	if(file_exists("{$this->merged_list[$skin_name]->dir}/admin-{$file}")){
			// 		$is_admin = true;
			// 	}
			// }
			
			// if($is_ajax){
			// 	$current_file_path = "{$this->merged_list[$skin_name]->dir}/ajax-{$file}";
			// }
			// else if($is_admin){
			// 	$current_file_path = "{$this->merged_list[$skin_name]->dir}/admin-{$file}";
			// }
			// else{
				$current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
			// }
			
			$current_file_path = apply_filters('kboard_skin_file_path', $current_file_path, $skin_name, $file);  //, $vars, $this);
		}

		if($current_file_path && file_exists($current_file_path)){
			include $current_file_path;
		}
		else{
			echo sprintf(__('%s file does not exist.', 'kboard'), $file);
		}
		return ob_get_clean();
	}
	
	/**
	 * 스킨의 editor 폼에 필수 정보를 출력한다.
	 * @param KBContent $content
	 * @param KBoard $board
	 */
	public function editorHeader($content, $board){
		wp_nonce_field('kboard-editor-execute', 'kboard-editor-execute-nonce');
		
		if($content->uid){
			wp_nonce_field("kboard-editor-content-{$content->uid}", 'kboard-editor-content-nonce');
		}
		
		do_action('kboard_skin_editor_header_before', $content, $board);
		
		$header = array();
		$header['action'] = '<input type="hidden" name="action" value="kboard_editor_execute">';
		$header['mod'] = '<input type="hidden" name="mod" value="editor">';
		$header['uid'] = sprintf('<input type="hidden" name="uid" value="%d">', $content->uid);
		$header['board_id'] = sprintf('<input type="hidden" name="board_id" value="%d">', $content->board_id);
		$header['parent_uid'] = sprintf('<input type="hidden" name="parent_uid" value="%d">', $content->parent_uid);
		$header['member_uid'] = sprintf('<input type="hidden" name="member_uid" value="%d">', $content->member_uid);
		$header['member_display'] = sprintf('<input type="hidden" name="member_display" value="%s">', $content->member_display);
		$header['date'] = sprintf('<input type="hidden" name="date" value="%s">', $content->date);
		$header['user_id'] = sprintf('<input type="hidden" name="user_id" value="%d">', get_current_user_id());
		
		$product_id = isset($_GET['woocommerce_product_tabs_inside']) ? intval($_GET['woocommerce_product_tabs_inside']) : '';
		if($product_id){
			$header['kboard_option_woocommerce_product_id'] = sprintf('<input type="hidden" name="kboard_option_woocommerce_product_id" value="%d">', $product_id);
		}
		
		$header = apply_filters('kboard_skin_editor_header', $header, $content, $board);
		
		foreach($header as $input){
			echo $input . "\n";
		}
		
		do_action('kboard_skin_editor_header_after', $content, $board);
	}
	
	/**
	 * 스킨의 editor_comment 폼에 필수 정보를 출력한다.
	 * @param KBContent $content
	 * @param KBoard $board
	 */
	public function editorHeaderComment($content, $board){
		if($content->uid){
			wp_nonce_field("kboard-comments-update-{$content->uid}", 'kboard-comments-update-nonce');
		}
		
		do_action('kboard_skin_editor_header_before', $content, $board);
		
		// action="http://127.0.0.1/?action=kboard_comment_update&uid=1312&kboard-comments-update-nonce=936d897058"
		$header = array();
		$header[] = '<input type="hidden" name="action" value="kboard_comment_update">';
		$header[] = sprintf('<input type="hidden" name="uid" value="%d">', $content->uid);
		$header = apply_filters('kboard_skin_editor_header', $header, $content, $board);
		
		foreach($header as $input){
			echo $input . "\n";
		}
		do_action('kboard_skin_editor_header_after', $content, $board);
	}
	
	/**
	 * 모든 스킨 리스트를 반환한다.
	 * @return array
	 */
	// public function getList(){
	// 	return $this->list ? $this->list : array();
	// }
	
	/**
	 * 최신글 모아보기 스킨 리스트를 반환한다.
	 */
	// public function getLatestviewList(){
	// 	return $this->latestview_list ? $this->latestview_list : array();
	// }
	
	/**
	 * 모든 스킨과 최신글 모아보기 스킨의 합쳐진 리스트를 반환한다.
	 * @return array
	 */
	// public function getMergedList(){
	// 	return $this->merged_list ? $this->merged_list : array();
	// }
	
	/**
	 * 스킨 파일이 있는지 확인한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return boolean
	 */
	// public function fileExists($skin_name, $file){
	// 	$file_exists = false;
	// 	$current_file_path = '';
		
	// 	if(isset($this->merged_list[$skin_name])){
	// 		$is_ajax = false;
	// 		if(defined('DOING_AJAX') && DOING_AJAX){
	// 			if(file_exists("{$this->merged_list[$skin_name]->dir}/ajax-{$file}")){
	// 				$is_ajax = true;
	// 			}
	// 		}
			
	// 		$is_admin = false;
	// 		if(is_admin()){
	// 			if(file_exists("{$this->merged_list[$skin_name]->dir}/admin-{$file}")){
	// 				$is_admin = true;
	// 			}
	// 		}
			
	// 		if($is_ajax){
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/ajax-{$file}";
	// 		}
	// 		else if($is_admin){
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/admin-{$file}";
	// 		}
	// 		else{
	// 			$current_file_path = "{$this->merged_list[$skin_name]->dir}/{$file}";
	// 		}
	// 	}
		
	// 	if($current_file_path && file_exists($current_file_path)){
	// 		$file_exists = true;
	// 	}
	// 	return $file_exists;
	// }
	
	/**
	 * 스킨의 functions.php 파일을 불러온다.
	 * @param string $skin_name
	 */
	// public function loadFunctions($skin_name){
	// 	if(isset($this->merged_list[$skin_name]) && file_exists("{$this->merged_list[$skin_name]->dir}/functions.php")){
	// 		include_once "{$this->merged_list[$skin_name]->dir}/functions.php";
	// 	}
	// }
	
	/**
	 * 템플릿 파일을 불러온다.
	 * @param string $file
	 * @param array $vars
	 * @return string
	 */
	// public function loadTemplate($file, $vars=array()){
	// 	ob_start();
		
	// 	$template_file_path = X2B_PATH . '/template/skin/' . $file;
		
	// 	if(file_exists($template_file_path)){
	// 		extract($vars, EXTR_SKIP);
			
	// 		include $template_file_path;
	// 	}
	// 	else{
	// 		echo sprintf(__('%s file does not exist.', 'kboard'), $file);
	// 	}
		
	// 	return ob_get_clean();
	// }
	
	/**
	 * 스킨 URL 주소를 반환한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	// public function url($skin_name, $file=''){
	// 	if(isset($this->merged_list[$skin_name])){
	// 		return "{$this->merged_list[$skin_name]->url}" . ($file ? "/{$file}" : '');
	// 	}
	// 	return '';
	// }
	
	/**
	 * 스킨 DIR 경로를 반환한다.
	 * @param string $skin_name
	 * @param string $file
	 * @return string
	 */
	// public function dir($skin_name, $file=''){
	// 	if(isset($this->merged_list[$skin_name])){
	// 		return "{$this->merged_list[$skin_name]->dir}" . ($file ? "/{$file}" : '');
	// 	}
	// 	return '';
	// }
	
	/**
	 * 사용 중인 스킨 리스트를 반환한다.
	 * @return array
	 */
	// public function getActiveList(){
	// 	global $wpdb;
		
	// 	$blog_id = get_current_blog_id();
	// 	if(!isset($this->active[$blog_id]) || !$this->active[$blog_id]){
	// 		$this->active[$blog_id] = array();
	// 		$results = $wpdb->get_results("SELECT `skin` FROM `{$wpdb->prefix}kboard_board_setting` UNION SELECT `skin` FROM `{$wpdb->prefix}kboard_board_latestview`");
			
	// 		foreach($results as $row){
	// 			$this->active[$blog_id][] = $row->skin;
	// 		}
	// 	}
		
	// 	return apply_filters('kboard_skin_active_list', $this->active[$blog_id]);
	// }
	
	// public function getOptionSearchFieldKey($key, $compare){ }
	// public function getOptionSearchFieldValue($key, $value){ }
}