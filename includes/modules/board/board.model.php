<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  boardModel
 * @author singleview.co.kr
 * @brief  board module  Model class
 **/
namespace X2board\Includes\Modules\Board;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if (!class_exists('\\X2board\\Includes\\Modules\\Board\\boardModel')) {

	class boardModel extends board {
		/**
		 * @brief initialization
		 **/
		// function init()	{}
		
		/**
		 * @brief get the list configuration
		 * getListConfig($module_srl)
		 **/
		public function get_list_config() {
			$o_post_user_define_list_fields = new \X2board\Includes\Classes\UserDefineListFields();
			$o_current_module_info = \X2board\Includes\Classes\Context::get('current_module_info');
			$a_list_config = $o_post_user_define_list_fields->get_list_config($o_current_module_info->list_fields);
			unset($o_post_user_define_list_fields);
			unset($o_current_module_info);
			return $a_list_config;
		}

		/**
		 * @brief return latest posts for an admin dashboard
		 * should be aligned with \includes\classes\ModuleObject.class.php::_get_grant()
		 * should be aligned with \includes\classes\ModuleObject.class.php::set_module_info()
		 * $oModuleModel->getGrant()
		 */
		public static function get_grant( $n_board_id, $o_logged_info ) {
			$o_grant                    = new \stdClass();
			$o_grant->manager           = false;
			$o_grant->access            = false;
			$o_grant->is_admin          = false;
			$o_grant->list              = false;
			$o_grant->view              = false;
			$o_grant->write_post        = false;
			$o_grant->write_comment     = false;
			$o_grant->consultation_read = false;

			$o_rst = \X2board\Includes\Admin\Tpl\x2b_load_settings( $n_board_id );
			if ( $o_rst->b_ok === false ) {
				unset( $o_rst );
				wp_die( __( 'msg_invalid_configuration', X2B_DOMAIN ) );
			}

			// remove skin_vars. skin_vars is useless in this context
			unset($o_rst->a_skin_vars);
			$o_module_info = new \stdClass();
			$n_prefix_len  = strlen( X2B_SKIN_VAR_IDENTIFIER );
			foreach ( $o_rst->a_board_settings as $s_key => $o_val ) {
				if ( substr( $s_key, 0, $n_prefix_len ) == X2B_SKIN_VAR_IDENTIFIER ) {
					continue;
				}
				$s_key = str_replace( 'board_', '', $s_key );
				$o_module_info->$s_key = $o_val;
			}
			unset( $o_rst );

			// Set variables to grant group permission
			// $member_info = Context::get( 'logged_info' );
			if ( $o_logged_info->ID ) {
				if ( is_array( $o_logged_info->roles ) ) {
					$group_list = array_values( $o_logged_info->roles );
				} else {
					$group_list = array();
				}
			} else {
				$group_list = array();
			}

			$o_grant->access   = $o_grant->manager = $o_logged_info->is_admin == 'Y' ? true : false;
			$o_grant->is_admin = $o_logged_info->is_admin == 'Y' ? true : false;
			if ( ! $o_grant->manager ) {
				$grant_exists = $granted = array();
				// [0] { ["name"]=> string(6) "access" ["group_srl"]=> int(-3) }
				// [1] { ["name"]=> string(7) "manager" ["group_srl"]=> int(-3) }
				// [2] { ["name"]=> string(4) "list" ["group_srl"]=> int(0) }
				// [3] { ["name"]=> string(4) "view" ["group_srl"]=> int(0) }
				// [4] { ["name"]=> string(13) "write_comment" ["group_srl"]=> int(0) }
				// [5] { ["name"]=> string(14) "write_document" ["group_srl"]=> int(0) } }
				$a_current_grant_info = array(
					'access'            => $o_module_info->grant_access,
					'manager'           => $o_module_info->grant_manager,
					'list'              => $o_module_info->grant_list,
					'view'              => $o_module_info->grant_view,
					'write_post'        => $o_module_info->grant_write_post,
					'write_comment'     => $o_module_info->grant_write_comment,
					'consultation_read' => $o_module_info->grant_consultation_read,
				);

				// Arrange names and groups who has privileges
				foreach ( $a_current_grant_info as $s_grant_name => $group_srl ) {
					$grant_exists[ $s_grant_name ] = true;
					if ( isset( $granted[ $s_grant_name ] ) ) {
						continue;
					}

					if ( $group_srl == X2B_LOGGEDIN_USERS ) {  // Log-in member only  로그인 사용자
						$granted[ $s_grant_name ] = true;
						if ( $o_logged_info->ID ) {
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
						foreach ( $o_module_info->grant[ 'board_grant_' . $s_grant_name ] as $_ => $s_group_name ) {
							if ( isset( $o_logged_info->caps[ $s_group_name ] ) && $o_logged_info->caps[ $s_group_name ] ) {
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

				$a_default_grant = array(
					'list'              => 'guest',
					'view'              => 'guest',
					'write_post'        => 'guest',
					'write_comment'     => 'guest',
					'consultation_read' => 'manager',
				);

				foreach ( $a_default_grant as  $s_grant_name => $s_default_grp ) {
					if ( isset( $grant_exists[ $s_grant_name ] ) ) {
						continue;
					}
					switch ( $s_default_grp ) {
						case 'guest':
							$o_grant->{$s_grant_name} = true;
							break;
						case 'member':
							if ( $o_logged_info->ID ) {
								$o_grant->{$s_grant_name} = true;
							} else {
								$o_grant->{$s_grant_name} = false;
							}
							break;
						case 'manager':
						case 'root':
							if ( $o_logged_info->is_admin == 'Y' ) {
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
				foreach ( $a_default_grant as $s_grant_name => $s_default_grp ) {
					$o_grant->{$s_grant_name} = true;
				}
			}
			unset( $o_logged_info );
			return $o_grant;
		}
		/**
		 * @brief return board list
		 */
		public static function get_board_list() {
			global $wpdb;
			$a_board = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}x2b_mapper` ORDER BY `board_id` DESC");
			return $a_board;
		}
	}
}