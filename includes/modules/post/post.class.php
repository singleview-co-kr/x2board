<?php
/**
 * @class  post
 * @author singleview.co.kr
 * @brief  post module high class
 **/
namespace X2board\Includes\Modules\Post;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

require_once X2B_PATH . 'includes/modules/post/post.item.php';

if (!class_exists('\\X2board\\Includes\\Modules\\Post\\post')) {

	class post extends \X2board\Includes\Classes\ModuleObject {
		/**
		 * Search option to use in admin page
		 * @var array
		 */
		var $search_option = array('title','content','title_content','user_name',); // /< Search options
		/**
		 * Status list
		 * @var array
		 */
		var $statusList = array('private'=>'PRIVATE', 'public'=>'PUBLIC', 'secret'=>'SECRET', 'temp'=>'TEMP');

		function __construct() {
// var_dump('post claas');
			global $G_X2B_CACHE;
			if(!isset($G_X2B_CACHE['POST_LIST'])) {
				$G_X2B_CACHE['POST_LIST'] = array();
			}
			
			if(!isset($_SESSION['x2b_own_post'])) {
				$_SESSION['x2b_own_post'] = array();
			}
		}

		/**
		 * Return default status
		 * @return string
		 */
		// function getDefaultStatus() {
		public function get_default_status() {
			return $this->statusList['public'];
		}

		/**
		 * Document Status List
		 * @return array
		 */
		// function getStatusList()
		public function get_status_list() {
			return $this->statusList;
		}

		/**
		 * Return status by key
		 * @return string
		 */
		// function getConfigStatus($key)
		public function get_config_status($key) {
			if(array_key_exists(strtolower($key), $this->statusList)) return $this->statusList[$key];
			else $this->get_default_status();
		}

		/**
		 * Re-generate the cache file
		 * @return void
		 */
		// function recompileCache()
		// {
		// 	if(!is_dir('./files/cache/tmp'))
		// 	{
		// 		FileHandler::makeDir('./files/cache/tmp');
		// 	}
		// }

		}
}
/* End of file post.class.php */
