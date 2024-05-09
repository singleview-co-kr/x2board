<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class ModuleObject
 * @author XEHub (developers@xpressengine.com)
 * base class of ModuleHandler
 * */
namespace X2board\Includes\Classes;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Classes\\ModuleObject')) {

	class ModuleObject extends BaseObject {

		// var $mid = NULL; ///< string to represent run-time instance of Module (XE Module)
		var $module = NULL; ///< Class name of Xe Module that is identified by mid
		// var $module_srl = NULL; ///< integer value to represent a run-time instance of Module (XE Module)
		public $module_info = NULL; ///< an object containing the module information
		// var $origin_module_info = NULL;
		// var $xml_info = NULL; ///< an object containing the module description extracted from XML file
		var $module_path = NULL; ///< a path to directory where module source code resides
		// var $act = NULL; ///< a string value to contain the action name
		var $skin_path = NULL; ///< a path of directory where skin files reside
		var $skin_file = NULL; ///< name of skin file
		// var $layout_path = ''; ///< a path of directory where layout files reside
		// var $layout_file = ''; ///< name of layout file
		// var $edited_layout_file = ''; ///< name of temporary layout files that is modified in an admin mode
		// var $stop_proc = FALSE; ///< a flag to indicating whether to stop the execution of code.
		var $module_config = NULL;
		// var $ajaxRequestMethod = array('XMLRPC', 'JSON');
		// var $gzhandler_enable = TRUE;
		private $_a_default_grant = array('list' => "guest", 
										  'view' => "guest", 
										  'write_post' => "guest", 
										  'write_comment' => "guest",	
										  'consultation_read' => "manager");

		/**
		 * Cunstructor
		 *
		 * @return void
		 */
		public function __construct() { }

		/**
		 * sett to set module information
		 * this is called by board.class.php::__construct();
		 * @param object $module_info object containing module information
		 * @param object $xml_info object containing module description
		 * @return void
		 * */
		public function setModuleInfo($n_board_id) { // , $o_module_info, $xml_info)
			// The default variable settings
			// $this->mid = $module_info->mid;
			// $this->module_srl = $module_info->module_srl;
			
			// {["list"]=> object(stdClass)#173 (2) { ["title"]=> string(6) "목록" ["default"]=> string(5) "guest" } 
			// ["view"]=> object(stdClass)#182 (2) { ["title"]=> string(6) "열람" ["default"]=> string(5) "guest" } 
			// ["write_document"]=> object(stdClass)#180 (2) { ["title"]=> string(10) "글 작성" ["default"]=> string(5) "guest" } 
			// ["write_comment"]=> object(stdClass)#175 (2) { ["title"]=> string(13) "댓글 작성" ["default"]=> string(5) "guest" } 
			// ["consultation_read"]=> object(stdClass)#176 (2) { ["title"]=> string(16) "상담글 조회" ["default"]=> string(7) "manager" } }

			require_once X2B_PATH . 'includes\admin\tpl\default-settings.php';
			require_once X2B_PATH . 'includes\admin\tpl\register-settings.php';
			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings($n_board_id);
			if( $o_rst->b_ok === false ) {
				unset($o_rst);
				wp_die(__('Invalid module configuration.', 'x2board'));
			}

			// unset unnecessary variables;
			unset($o_rst->a_board_settings['board_title']);
			unset($o_rst->a_board_settings['wp_page_title']);
			$this->module_info = new \stdClass();
			foreach( $o_rst->a_board_settings as $s_key => $o_val ) {
				$s_key = str_replace('board_','',$s_key);
				$this->module_info->$s_key = $o_val;
			}
			unset($o_rst);

			// for \includes\modules\file\file.model.php::get_upload_config() usage
			$this->module_info->board_id = $n_board_id; 
			Context::set('current_module_info', $this->module_info);
			// $this->module_info = (object)$o_rst->a_board_settings;
// var_dump($this->module_info);
// exit;
			// $this->skin_vars = $o_module_info->skin_vars;

			// $this->origin_module_info = $module_info;
			// $this->xml_info = $xml_info;
			
			// validate certificate info and permission settings necessary in Web-services
			
			// $logged_info = Context::get('logged_info');
			// module model create an object
			// $oModuleModel = getModel('module');
			// permission settings. access, manager(== is_admin) are fixed and privilege name in XE
			// $module_srl = Context::get('module_srl');
			// if(!$module_info->mid && !is_array($module_srl) && preg_match('/^([0-9]+)$/', $module_srl)) {
			// 	$request_module = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			// 	if($request_module->module_srl == $module_srl) {
			// 		$grant = $oModuleModel->getGrant($request_module, $logged_info);
			// 	}
			// }
			// else {
				$o_grant = $this->_get_grant(); //$module_info, $logged_info, $xml_info);
				// have at least access grant
				// if(substr_count($this->act, 'Member') || substr_count($this->act, 'Communication')) {
				// 	$grant->access = 1;
				// }
			// }

			// display no permission if the current module doesn't have an access privilege
			if(!isset($o_grant->access)) {
				wp_die(__('msg_not_permitted', 'x2board'));
			}

			// checks permission and action if you don't have an admin privilege
			if(!isset($o_grant->manager)) {
				// get permission types(guest, member, manager, root) of the currently requested action
				$permission_target = null;//$xml_info->permission->{$this->act};
				// check manager if a permission in module.xml otherwise action if no permission
				// if(!$permission_target && substr_count($this->act, 'Admin'))
				// {
				// 	$permission_target = 'manager';
				// }
				// Check permissions
				switch($permission_target) {
					case 'root' :
					case 'manager' :
						$this->stop('msg_is_not_administrator');
						return;
					case 'member' :
						$is_logged = Context::get('is_logged');
						if(!$is_logged) {
							$this->stop('msg_not_permitted_act');
							return;
						}
						break;
				}
			}
			// permission variable settings
			$this->grant = $o_grant;
			Context::set('grant', $o_grant);

			// $this->module_config = null;// $oModuleModel->getModuleConfig($this->module, $module_info->site_srl);
			if(method_exists($this, 'init')) {
				$this->init();
			}
		}

		/**
		 * @brief Return permission by using module info, xml info and member info
		 */
		private function _get_grant() {  //$module_info, $member_info, $xml_info = '') {
			$o_grant = new \stdClass();
			$o_grant->manager = false; 
			$o_grant->access = false;
			$o_grant->is_admin = false;
			$o_grant->list = false;
			$o_grant->view = false; 
			$o_grant->write_post = false;
			$o_grant->write_comment = false;
			$o_grant->consultation_read = false;
			// $o_grant->is_site_admin = true;

			// if(!$xml_info) {
			// 	$module = $module_info->module;
			// 	$xml_info = $this->getModuleActionXml($module);
			// }

			// Set variables to grant group permission
			$member_info = Context::get('logged_info');
			// $n_module_srl = $this->module_info->board_id; // $module_srl = $module_info->module_srl;
			if($member_info->ID) {
				if(is_array($member_info->roles)) {  // $member_info->group_list  ->   $member_info->roles   array(1) { [1]=> string(12) "관리그룹" }
					$group_list = array_values($member_info->roles);
				}
				else {
					$group_list = array();
				}
			}
			else {
				$group_list = array();
			}
// var_dump($group_list);
			// If board_id doesn't exist(if unable to set permissions)
			// if(!$module_srl) {
			// 	$grant->access = true;
			// 	if($this->isSiteAdmin($member_info, $module_info->site_srl)) {
			// 		$grant->access = $grant->manager = $grant->is_site_admin = true;
			// 	}
			// 	$grant->is_admin = $grant->manager = ($member_info->is_admin == 'Y') ? true : false;
			// }
			// else {
				// If module_srl exists
				// Get a type of granted permission
				// $o_grant->access = $o_grant->manager = $o_grant->is_site_admin = ($member_info->is_admin=='Y'||$this->isSiteAdmin($member_info, $module_info->site_srl))?true:false;
				$o_grant->access = $o_grant->manager = $member_info->is_admin == 'Y' ? true : false;
				$o_grant->is_admin = $member_info->is_admin == 'Y' ? true : false;
				// If a just logged-in member is, check if the member is a module administrator
				// if(!$o_grant->manager && $member_info->ID) {
				// 	$args = new stdClass();
				// 	$args->module_srl = $n_module_srl;
				// 	$args->member_srl = $member_info->member_srl;
				// 	$output = executeQuery('module.getModuleAdmin',$args);
				// 	if($output->data && $output->data->member_srl == $member_info->ID) {
				// 		$o_grant->manager = true;
				// 	}
				// }
				// If not an administrator, get information from the DB and grant manager privilege.

				if(!$o_grant->manager) {
					// $args = new \stdClass();
					// If planet, get permission settings from the planet home
					// if($module_info->module == 'planet') {
					// 	$output = executeQueryArray('module.getPlanetGrants', $args);
					// }
					// else {
						// $args = new stdClass;
						// $args->module_srl = $n_module_srl;
						// $output = executeQueryArray('module.getModuleGrants', $args);
					// }
					$grant_exists = $granted = array();
// [0] { ["name"]=> string(6) "access" ["group_srl"]=> int(-3) } 
// [1] { ["name"]=> string(7) "manager" ["group_srl"]=> int(-3) } 
// [2] { ["name"]=> string(4) "list" ["group_srl"]=> int(0) } 
// [3] { ["name"]=> string(4) "view" ["group_srl"]=> int(0) } 
// [4] { ["name"]=> string(13) "write_comment" ["group_srl"]=> int(0) } 
// [5] { ["name"]=> string(14) "write_document" ["group_srl"]=> int(0) } }
					$a_current_grant_info = array('access' => $this->module_info->grant_access,
												  'manager' => $this->module_info->grant_manager,
												  'list' => $this->module_info->grant_list, 
												  'view' => $this->module_info->grant_view, 
												  'write_post' => $this->module_info->grant_write_post, 
												  'write_comment' => $this->module_info->grant_write_comment, 
												  'consultation_read' => $this->module_info->grant_consultation_read );
					
					// Arrange names and groups who has privileges
					foreach($a_current_grant_info as $s_grant_name => $group_srl) {
						$grant_exists[$s_grant_name] = true;
						if(isset($granted[$s_grant_name])) {
							continue;
						}

						if($group_srl == X2B_LOGGEDIN_USERS) {  // Log-in member only  로그인 사용자
							$granted[$s_grant_name] = true;
							if($member_info->ID) {
								$o_grant->{$s_grant_name} = true;
							}
						}
						elseif($group_srl == X2B_ADMINISTRATOR) {   //  관리자만
							$granted[$s_grant_name] = true;
							$o_grant->{$s_grant_name} = $o_grant->is_admin;// || $o_grant->is_site_admin);
						}
						elseif($group_srl == X2B_ALL_USERS) {  //  모든 사용자
							$granted[$s_grant_name] = true;
							$o_grant->{$s_grant_name} = true;
						}
						elseif($group_srl == X2B_CUSTOMIZE) {  // If a target is a 선택 그룹 사용자
							$o_grant->{$s_grant_name} = false;
							$granted[$s_grant_name] = false;
							// if($group_list && count($group_list) && in_array($group_srl, $group_list)) {
							foreach($this->module_info->grant['board_grant_'.$s_grant_name] as $_ => $s_group_name) {
								if( isset( $member_info->caps[$s_group_name] ) && $member_info->caps[$s_group_name] ) {
									$o_grant->{$s_grant_name} = true;
									$granted[$s_grant_name] = true;
									break;
								}
							};
						}
						else {
							wp_die(__('Invalid grant code: board_grant_'.$s_grant_name, 'x2board'));
						}
						// elseif($group_srl == -2) {  // Site-joined member only  가입한 사용자
						// 	$granted[$s_grant_name] = true;
						// 	// Do not grant any permission for non-logged member
						// 	if(!$member_info->ID) {  // Log-in member
						// 		$o_grant->{$s_grant_name} = false;
						// 	}
						// 	else {  // All of non-logged members
						// 		$site_module_info = Context::get('site_module_info');
						// 		// Permission granted if no information of the currently connected site exists
						// 		if(!$site_module_info->site_srl) {
						// 			$o_grant->{$s_grant_name} = true;
						// 		}
						// 		// Permission is not granted if information of the currently connected site exists
						// 		elseif(count($group_list)) {
						// 			$o_grant->{$s_grant_name} = true;
						// 		}
						// 	}
						// }
					}
var_dump($o_grant);
					// Separate processing for the virtual group access
					if(!$grant_exists['access']) {
						$o_grant->access = true;
					}

					foreach($this->_a_default_grant as  $s_grant_name => $s_default_grp) {
						if(isset($grant_exists[$s_grant_name])) {
							continue;
						}
						switch($s_default_grp) {
							case 'guest' :
								$o_grant->{$s_grant_name} = true;
								break;
							case 'member' :
								if($member_info->ID) {
									$o_grant->{$s_grant_name} = true;
								}
								else {
									$o_grant->{$s_grant_name} = false;
								}
								break;
							case 'manager' :
							case 'root' :
								if($member_info->is_admin == 'Y') {
									$o_grant->{$s_grant_name} = true;
								}
								else {
									$o_grant->{$s_grant_name} = false;
								}
								break;
						}
					}
					unset($grant_exists);
					unset($a_current_grant_info);
				}

				if($o_grant->manager) {  // allow all privileges for an administrator
					$o_grant->access = true;
					foreach($this->_a_default_grant as $s_grant_name => $s_default_grp) {
						$o_grant->{$s_grant_name} = true;
					}
				}
			// }
			unset($member_info);
// var_dump($o_grant);
			return $o_grant;
		}

		/**
		 * set message
		 * @param string $message a message string
		 * @param string $type type of message (error, info, update)
		 * @return void
		 * */
		// public function setMessage($message = 'success', $type = NULL) {
		// 	parent::setMessage($message);
		// 	$this->setMessageType($type);
		// }

		/**
		 * set type of message
		 * @param string $type type of message (error, info, update)
		 * @return void
		 * */
		// public function setMessageType($type) {
		// 	$this->add('message_type', $type);
		// }

		/**
		 * get type of message
		 * @return string $type
		 * */
		// function getMessageType()
		// {
		// 	$type = $this->get('message_type');
		// 	$typeList = array('error' => 1, 'info' => 1, 'update' => 1);
		// 	if(!isset($typeList[$type]))
		// 	{
		// 		$type = $this->getError() ? 'error' : 'info';
		// 	}
		// 	return $type;
		// }

		/**
		 * set the directory path of the skin directory
		 * @param string path of skin directory.
		 * @return void
		 * */
		// function setTemplatePath($path)
		public function set_skin_path($path) {
			if(!$path) return;

			// if((strlen($path) >= 1 && substr_compare($path, '/', 0, 1) !== 0) && (strlen($path) >= 2 && substr_compare($path, './', 0, 2) !== 0)) {
			// 	$path = './' . $path;
			// }

			if(substr_compare($path, '/', -1) !== 0) {
				$path .= '/';
			}
			$this->skin_path = $path;
			if( !is_dir($this->skin_path) ) {
				wp_die( $this->skin_path . ' does not exist' );
			}

			// if( $h_dir = @opendir($this->skin_path) ) {
				// $a_required_skinfile = $this->_a_required_skinfile;
				// while(($filename = readdir($h_dir)) !== false){
				// 	if(isset($filename) && substr_compare($filename, '.php', -4) == 0) {  // if php file found
				// 		unset($a_required_skinfile[$filename]);
				// 	}
			// 		$skin = new \stdClass();
			// 		$skin->name = $name;
			// 		$skin->dir = $this->skin_path . $name;
			// 		$skin->url = $s_url . $name;
			// 		$this->list[$name] = $skin;   /////////////////////
			// 	}
			// 	if( count($a_required_skinfile) > 0 ) {
			// 		wp_die( implode(', ', array_keys($a_required_skinfile) ) . ' are required but not exist in ' . $path );
			// 	}
			// }
			// if( is_resource($h_dir) ) {
			// 	closedir($h_dir);
			// }
			// $this->list = apply_filters('x2board_skin_list', $this->list);
			// $this->latestview_list = apply_filters('x2board_skin_latestview_list', $this->list);
			// $this->merged_list = array_merge($this->list, $this->latestview_list);
			// ksort($this->list);
			// ksort($this->latestview_list);
			// ksort($this->merged_list);
		}

		/**
		 * render the named skin file
		 * @param string name of file
		 * @return void
		 * */
		public function render_skin_file($filename) {
			if(isset($filename) && substr_compare($filename, '.php', -4) !== 0) {
				$filename .= '.php';
			}
			$this->skin_file = $filename;

			$s_skin_file_abs_path = $this->skin_path . $this->skin_file;
			if( !file_exists( $s_skin_file_abs_path ) ) {
				echo sprintf(__('%s file does not exist.', 'x2board'), $s_skin_file_abs_path);
			}
			ob_start();

			extract(Context::getAll4Skin(), EXTR_SKIP);
			include $s_skin_file_abs_path;
			return ob_get_clean();
		}

		/**
		 * setter to set the name of module
		 * @param string $module name of module
		 * @return void
		 * */
		public function setModule($s_module) {
			$this->module = $s_module;
// var_dump($this->module);
		}

		/**
		 * setter to set the name of module path
		 * @param string $path the directory path to a module directory
		 * @return void
		 * */
		public function setModulePath($path) {
			if(substr_compare($path, '/', -1) !== 0) {
				$path.='/';
			}
			$this->module_path = $path;
		}
////////////////////////////////////////

		/**
		 * set the stop_proc and approprate message for msg_code
		 * @param string $msg_code an error code
		 * @return ModuleObject $this
		 * */
		// function stop($msg_code)
		// {
		// 	wp_die('ModuleObject::stop()');
			// flag setting to stop the proc processing
			// $this->stop_proc = TRUE;
			// Error handling
			// $this->setError(-1);
			// $this->setMessage($msg_code);
			// Error message display by message module
			// $type = Mobile::isFromMobilePhone() ? 'mobile' : 'view';
			// $oMessageObject = ModuleHandler::getModuleInstance('message', $type);
			// $oMessageObject->setError(-1);
			// $oMessageObject->setMessage($msg_code);
			// $oMessageObject->dispMessage();

			// $this->set_skin_path($oMessageObject->getTemplatePath());
			// $this->render_skin_file($oMessageObject->getTemplateFile());

			// return $this;
		// }

		/**
		 * retrieve the directory path of the template directory
		 * @return string
		 * */
		// function getTemplateFile()
		// {
		// 	return $this->skin_file;
		// }

		/**
		 * retrieve the directory path of the template directory
		 * @return string
		 * */
		// function getTemplatePath()
		// {
		// 	return $this->skin_path;
		// }

		/**
		 * sett to set the template path for refresh.html
		 * refresh.html is executed as a result of method execution
		 * Tpl as the common run of the refresh.html ..
		 * @return void
		 * */
		// function setRefreshPage()
		// {
		// 	$this->set_skin_path('./common/tpl');
		// 	$this->render_skin_file('refresh');
		// }

		/**
		 * sett to set the action name
		 * @param string $act
		 * @return void
		 * */
		// function setAct($act)
		// {
		// 	$this->act = $act;
		// }

		/**
		 * setter to set an url for redirection
		 * @param string $url url for redirection
		 * @remark redirect_url is used only for ajax requests
		 * @return void
		 * */
		// function setRedirectUrl($url = './', $output = NULL)
		// {
		// 	$ajaxRequestMethod = array_flip($this->ajaxRequestMethod);
		// 	if(!isset($ajaxRequestMethod[Context::getRequestMethod()]))
		// 	{
		// 		$this->add('redirect_url', $url);
		// 	}

		// 	if($output !== NULL && is_object($output))
		// 	{
		// 		return $output;
		// 	}
		// }

		/**
		 * get url for redirection
		 * @return string redirect_url
		 * */
		// function getRedirectUrl()
		// {
		// 	return $this->get('redirect_url');
		// }

		/**
		 * set the file name of the temporarily modified by admin
		 * @param string name of file
		 * @return void
		 * */
		// function setEditedLayoutFile($filename)
		// {
		// 	if(!$filename) return;

		// 	if(substr_compare($filename, '.html', -5) !== 0)
		// 	{
		// 		$filename .= '.html';
		// 	}
		// 	$this->edited_layout_file = $filename;
		// }

		/**
		 * retreived the file name of edited_layout_file
		 * @return string
		 * */
		// function getEditedLayoutFile()
		// {
		// 	return $this->edited_layout_file;
		// }

		/**
		 * set the file name of the layout file
		 * @param string name of file
		 * @return void
		 * */
		// function setLayoutFile($filename)
		// {
		// 	if(!$filename) return;

		// 	if(substr_compare($filename, '.html', -5) !== 0)
		// 	{
		// 		$filename .= '.html';
		// 	}
		// 	$this->layout_file = $filename;
		// }

		/**
		 * get the file name of the layout file
		 * @return string
		 * */
		// function getLayoutFile()
		// {
		// 	return $this->layout_file;
		// }

		/**
		 * set the directory path of the layout directory
		 * @param string path of layout directory.
		 * */
		// function setLayoutPath($path)
		// {
		// 	if(!$path) return;

		// 	if((strlen($path) >= 1 && substr_compare($path, '/', 0, 1) !== 0) && (strlen($path) >= 2 && substr_compare($path, './', 0, 2) !== 0))
		// 	{
		// 		$path = './' . $path;
		// 	}
		// 	if(substr_compare($path, '/', -1) !== 0)
		// 	{
		// 		$path .= '/';
		// 	}
		// 	$this->layout_path = $path;
		// }

		/**
		 * set the directory path of the layout directory
		 * @return string
		 * */
		// function getLayoutPath($layout_name = "", $layout_type = "P")
		// {
		// 	return $this->layout_path;
		// }

		/**
		 * excute the member method specified by $act variable
		 * @return boolean true : success false : fail
		 * */
	// 	function proc()
	// 	{
	// 		// pass if stop_proc is true
	// 		if($this->stop_proc)
	// 		{
	// 			debugPrint($this->message, 'ERROR');
	// 			return FALSE;
	// 		}

	// 		// trigger call
	// 		$triggerOutput = ModuleHandler::triggerCall('moduleObject.proc', 'before', $this);
	// 		if(!$triggerOutput->toBool())
	// 		{
	// 			$this->setError($triggerOutput->getError());
	// 			$this->setMessage($triggerOutput->getMessage());
	// 			return FALSE;
	// 		}

	// 		// execute an addon(call called_position as before_module_proc)
	// 		$called_position = 'before_module_proc';
	// 		$oAddonController = getController('addon');
	// 		$addon_file = $oAddonController->getCacheFilePath(Mobile::isFromMobilePhone() ? "mobile" : "pc");
	// 		if(FileHandler::exists($addon_file)) include($addon_file);

	// 		if(isset($this->xml_info->action->{$this->act}) && method_exists($this, $this->act))
	// 		{
	// 			// Check permissions
	// 			if($this->module_srl && !$this->grant->access)
	// 			{
	// 				$this->stop("msg_not_permitted_act");
	// 				return FALSE;
	// 			}

	// 			// integrate skin information of the module(change to sync skin info with the target module only by seperating its table)
	// 			$is_default_skin = ((!Mobile::isFromMobilePhone() && $this->module_info->is_skin_fix == 'N') || (Mobile::isFromMobilePhone() && $this->module_info->is_mskin_fix == 'N'));
	// 			$usedSkinModule = !($this->module == 'page' && ($this->module_info->page_type == 'OUTSIDE' || $this->module_info->page_type == 'WIDGET'));
	// 			if($usedSkinModule && $is_default_skin && $this->module != 'admin' && strpos($this->act, 'Admin') === false && $this->module == $this->module_info->module)
	// 			{
	// 				$dir = (Mobile::isFromMobilePhone()) ? 'm.skins' : 'skins';
	// 				$valueName = (Mobile::isFromMobilePhone()) ? 'mskin' : 'skin';
	// 				$oModuleModel = getModel('module');
	// 				$skinType = (Mobile::isFromMobilePhone()) ? 'M' : 'P';
	// 				$skinName = $oModuleModel->getModuleDefaultSkin($this->module, $skinType);
	// 				if($this->module == 'page')
	// 				{
	// 					$this->module_info->{$valueName} = $skinName;
	// 				}
	// 				else
	// 				{
	// 					$isTemplatPath = (strpos($this->getTemplatePath(), '/tpl/') !== FALSE);
	// 					if(!$isTemplatPath)
	// 					{
	// 						$this->setTemplatePath(sprintf('%s%s/%s/', $this->module_path, $dir, $skinName));
	// 					}
	// 				}
	// 			}

	// 			$oModuleModel = getModel('module');
	// 			$oModuleModel->syncSkinInfoToModuleInfo($this->module_info);
	// 			Context::set('module_info', $this->module_info);
	// 			// Run
	// 			$output = $this->{$this->act}();
	// 		}
	// 		else
	// 		{
	// 			return FALSE;
	// 		}

	// 		// trigger call
	// 		$triggerOutput = ModuleHandler::triggerCall('moduleObject.proc', 'after', $this);
	// 		if(!$triggerOutput->toBool())
	// 		{
	// 			$this->setError($triggerOutput->getError());
	// 			$this->setMessage($triggerOutput->getMessage());
	// 			return FALSE;
	// 		}

	// 		// execute an addon(call called_position as after_module_proc)
	// 		$called_position = 'after_module_proc';
	// 		$oAddonController = getController('addon');
	// 		$addon_file = $oAddonController->getCacheFilePath(Mobile::isFromMobilePhone() ? "mobile" : "pc");
	// 		if(FileHandler::exists($addon_file)) include($addon_file);

	// 		if(is_a($output, 'BaseObject') || is_subclass_of($output, 'BaseObject'))
	// 		{
	// 			$this->setError($output->getError());
	// 			$this->setMessage($output->getMessage());

	// 			if(!$output->toBool())
	// 			{
	// 				return FALSE;
	// 			}
	// 		}
	// 		// execute api methods of the module if view action is and result is XMLRPC or JSON
	// 		if($this->module_info->module_type == 'view' || $this->module_info->module_type == 'mobile')
	// 		{
	// 			if(Context::getResponseMethod() == 'XMLRPC' || Context::getResponseMethod() == 'JSON')
	// 			{
	// 				$oAPI = getAPI($this->module_info->module, 'api');
	// 				if(method_exists($oAPI, $this->act))
	// 				{
	// 					$oAPI->{$this->act}($this);
	// 				}
	// 			}
	// 		}
	// 		return TRUE;
	// 	}
	}
}
/* End of file ModuleObject.class.php */