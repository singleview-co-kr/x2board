<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * @class ModuleObject
 * @author XEHub (developers@xpressengine.com)
 * base class of ModuleHandler
 */
namespace X2board\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\X2board\\Includes\\Classes\\ModuleObject' ) ) {

	class ModuleObject extends BaseObject {
		var $module               = null; // < Class name of Xe Module that is identified by mid
		public $module_info       = null; // < an object containing the module information
		var $module_path          = null; // < a path to directory where module source code resides
		var $skin_path            = null; // < a path of directory where skin files reside
		var $skin_file            = null; // < name of skin file
		public $skin_vars         = null;
		private $_a_default_grant = array(
			'list'              => 'guest',
			'view'              => 'guest',
			'write_post'        => 'guest',
			'write_comment'     => 'guest',
			'consultation_read' => 'manager',
		);

		/**
		 * Cunstructor
		 *
		 * @return void
		 */
		public function __construct() { }

		/**
		 * sett to set module information
		 * this is called by board.class.php::__construct();
		 *
		 * @param object $module_info object containing module information
		 * @param object $xml_info object containing module description
		 * @return void
		 * {["list"]=> object(stdClass)#173 (2) { ["title"]=> string(6) "목록" ["default"]=> string(5) "guest" }
		 *  ["view"]=> object(stdClass)#182 (2) { ["title"]=> string(6) "열람" ["default"]=> string(5) "guest" }
		 *  ["write_document"]=> object(stdClass)#180 (2) { ["title"]=> string(10) "글 작성" ["default"]=> string(5) "guest" }
		 *  ["write_comment"]=> object(stdClass)#175 (2) { ["title"]=> string(13) "댓글 작성" ["default"]=> string(5) "guest" }
		 *  ["consultation_read"]=> object(stdClass)#176 (2) { ["title"]=> string(16) "상담글 조회" ["default"]=> string(7) "manager" } }
		 */
		public function set_module_info( $n_board_id ) {
			require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'default-settings.php';
			require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'register-settings.php';
			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings( $n_board_id );
			if ( $o_rst->b_ok === false ) {
				unset( $o_rst );
				wp_die( __( 'msg_invalid_configuration', X2B_DOMAIN ) );
			}

			// unset unnecessary variables;
			unset( $o_rst->a_board_settings['board_title'] );
			unset( $o_rst->a_board_settings['wp_page_title'] );

			// set module_info
			$this->module_info = new \stdClass();
			$n_prefix_len      = strlen( X2B_SKIN_VAR_IDENTIFIER );
			foreach ( $o_rst->a_board_settings as $s_key => $o_val ) {
				if ( substr( $s_key, 0, $n_prefix_len ) == X2B_SKIN_VAR_IDENTIFIER ) {
					continue;
				}
				$s_key                     = str_replace( 'board_', '', $s_key );
				$this->module_info->$s_key = $o_val;
			}

			if( ! $this->module_info->use_status ) {
				$o_post_model = \X2board\Includes\get_model( 'post' );
				$s_default_status = $o_post_model->get_default_status();
				$this->module_info->use_status = array( $s_default_status => $s_default_status );
			}

			$this->module_info->use_anonymous = !$this->module_info->use_anonymous ? 'N' : $this->module_info->use_anonymous;

			// set skin_vars
			$this->skin_vars = (object) $o_rst->a_skin_vars;
			unset( $o_rst );

			// for \includes\modules\file\file.model.php::get_upload_config() usage
			$this->module_info->board_id = $n_board_id;
			Context::set( 'current_module_info', $this->module_info );
			Context::set( 'lang_type', get_locale() );

			$o_grant = $this->_get_grant();
			// display no permission if the current module doesn't have an access privilege
			if ( ! isset( $o_grant->access ) ) {
				wp_die( __( 'msg_not_permitted', X2B_DOMAIN ) );
			}

			// checks permission and action if you don't have an admin privilege
			if ( ! isset( $o_grant->manager ) ) {
				// get permission types(guest, member, manager, root) of the currently requested action
				$permission_target = null;
				// Check permissions
				switch ( $permission_target ) {
					case 'root':
					case 'manager':
						$this->stop( 'msg_is_not_administrator' );
						return;
					case 'member':
						$is_logged = Context::get( 'is_logged' );
						if ( ! $is_logged ) {
							$this->stop( 'msg_not_permitted_act' );
							return;
						}
						break;
				}
			}
			// permission variable settings
			$this->grant = $o_grant;
			Context::set( 'grant', $o_grant );
			if ( method_exists( $this, 'init' ) ) {
				$this->init();
			}
		}

		/**
		 * @brief Return permission by using module info, xml info and member info
		 */
		private function _get_grant() {
			$o_grant                    = new \stdClass();
			$o_grant->manager           = false;
			$o_grant->access            = false;
			$o_grant->is_admin          = false;
			$o_grant->list              = false;
			$o_grant->view              = false;
			$o_grant->write_post        = false;
			$o_grant->write_comment     = false;
			$o_grant->consultation_read = false;

			// Set variables to grant group permission
			$member_info = Context::get( 'logged_info' );
			// $member_info->group_list  -> $member_info->roles array(1) { [1]=> string(12) "관리그룹" }
			if ( $member_info->ID ) {
				if ( is_array( $member_info->roles ) ) {
					$group_list = array_values( $member_info->roles );
				} else {
					$group_list = array();
				}
			} else {
				$group_list = array();
			}

			$o_grant->access   = $o_grant->manager = $member_info->is_admin == 'Y' ? true : false;
			$o_grant->is_admin = $member_info->is_admin == 'Y' ? true : false;
			if ( ! $o_grant->manager ) {
				$grant_exists = $granted = array();
				// [0] { ["name"]=> string(6) "access" ["group_srl"]=> int(-3) }
				// [1] { ["name"]=> string(7) "manager" ["group_srl"]=> int(-3) }
				// [2] { ["name"]=> string(4) "list" ["group_srl"]=> int(0) }
				// [3] { ["name"]=> string(4) "view" ["group_srl"]=> int(0) }
				// [4] { ["name"]=> string(13) "write_comment" ["group_srl"]=> int(0) }
				// [5] { ["name"]=> string(14) "write_document" ["group_srl"]=> int(0) } }
				$a_current_grant_info = array(
					'access'            => $this->module_info->grant_access,
					'manager'           => $this->module_info->grant_manager,
					'list'              => $this->module_info->grant_list,
					'view'              => $this->module_info->grant_view,
					'write_post'        => $this->module_info->grant_write_post,
					'write_comment'     => $this->module_info->grant_write_comment,
					'consultation_read' => $this->module_info->grant_consultation_read,
				);

				// Arrange names and groups who has privileges
				foreach ( $a_current_grant_info as $s_grant_name => $group_srl ) {
					$grant_exists[ $s_grant_name ] = true;
					if ( isset( $granted[ $s_grant_name ] ) ) {
						continue;
					}

					if ( $group_srl == X2B_LOGGEDIN_USERS ) {  // Log-in member only  로그인 사용자
						$granted[ $s_grant_name ] = true;
						if ( $member_info->ID ) {
							$o_grant->{$s_grant_name} = true;
						}
					} elseif ( $group_srl == X2B_ADMINISTRATOR ) {   // 관리자만
						$granted[ $s_grant_name ] = true;
						$o_grant->{$s_grant_name} = $o_grant->is_admin;
					} elseif ( $group_srl == X2B_ALL_USERS ) {  // 모든 사용자
						$granted[ $s_grant_name ] = true;
						$o_grant->{$s_grant_name} = true;
					} elseif ( $group_srl == X2B_CUSTOMIZE ) {  // If a target is a 선택 그룹 사용자
						$o_grant->{$s_grant_name} = false;
						$granted[ $s_grant_name ] = false;
						foreach ( $this->module_info->grant[ 'board_grant_' . $s_grant_name ] as $_ => $s_group_name ) {
							if ( isset( $member_info->caps[ $s_group_name ] ) && $member_info->caps[ $s_group_name ] ) {
								$o_grant->{$s_grant_name} = true;
								$granted[ $s_grant_name ] = true;
								break;
							}
						}
					} else {
						wp_die( __( 'msg_invalid_grant_code', X2B_DOMAIN ) . ': board_grant_' . $s_grant_name );
					}
				}

				// Separate processing for the virtual group access
				if ( ! $grant_exists['access'] ) {
					$o_grant->access = true;
				}

				foreach ( $this->_a_default_grant as  $s_grant_name => $s_default_grp ) {
					if ( isset( $grant_exists[ $s_grant_name ] ) ) {
						continue;
					}
					switch ( $s_default_grp ) {
						case 'guest':
							$o_grant->{$s_grant_name} = true;
							break;
						case 'member':
							if ( $member_info->ID ) {
								$o_grant->{$s_grant_name} = true;
							} else {
								$o_grant->{$s_grant_name} = false;
							}
							break;
						case 'manager':
						case 'root':
							if ( $member_info->is_admin == 'Y' ) {
								$o_grant->{$s_grant_name} = true;
							} else {
								$o_grant->{$s_grant_name} = false;
							}
							break;
					}
				}
				unset( $grant_exists );
				unset( $a_current_grant_info );
			}

			if ( $o_grant->manager ) {  // allow all privileges for an administrator
				$o_grant->access = true;
				foreach ( $this->_a_default_grant as $s_grant_name => $s_default_grp ) {
					$o_grant->{$s_grant_name} = true;
				}
			}
			unset( $member_info );
			return $o_grant;
		}

		/**
		 * set the directory path of the skin directory
		 * setTemplatePath($path)
		 *
		 * @param string path of skin directory.
		 * @return void
		 * */
		public function set_skin_path( $path ) {
			if ( ! $path ) {
				return;
			}

			if ( substr_compare( $path, '/', -1 ) !== 0 ) {
				$path .= '/';
			}
			$this->skin_path = $path;
			if ( ! is_dir( $this->skin_path ) ) {
				wp_die( $this->skin_path . ' does not exist' );
			}
		}

		/**
		 * render the named skin file
		 *
		 * @param string name of file
		 * @return void
		 * */
		public function render_skin_file( $filename ) {
			if ( isset( $filename ) && substr_compare( $filename, '.php', -4 ) !== 0 ) {
				$filename .= '.php';
			}
			$this->skin_file = $filename;

			$s_skin_file_abs_path = $this->skin_path . $this->skin_file;
			if ( ! file_exists( $s_skin_file_abs_path ) ) {
				printf( __( 'msg_file_not_exists', X2B_DOMAIN ), $s_skin_file_abs_path );
			}
			ob_start();

			// convert module_info into a skin-callable variable
			extract( Context::get_all_4_skin(), EXTR_SKIP );

			// convert skin_vars into a skin-callable variable via $skin_vars->var_id
			$a_skin_vars = array( 'skin_vars' => $this->skin_vars );
			extract( $a_skin_vars, EXTR_SKIP );
			unset( $a_skin_vars );

			include $s_skin_file_abs_path;
			return ob_get_clean();
		}

		/**
		 * setter to set the name of module
		 *
		 * @param string $module name of module
		 * @return void
		 * */
		public function set_module( $s_module ) {
			$this->module = $s_module;
		}

		/**
		 * setter to set the name of module path
		 *
		 * @param string $path the directory path to a module directory
		 * @return void
		 * */
		public function set_module_path( $path ) {
			if ( substr_compare( $path, '/', -1 ) !== 0 ) {
				$path .= '/';
			}
			$this->module_path = $path;
		}
	}
}
/* End of file ModuleObject.class.php */
