<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  importAdminController
 * @author singleview.co.kr
 * @brief  게시판 XML import
 **/
namespace X2board\Includes\Modules\Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Import\\importAdminController' ) ) {

	class importAdminController {

		private $_s_wp_upload_base_dir = null;
		private $_n_board_id           = null;
		private $_o_fileSystemDirect   = null;
		private $_a_image_ext          = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
		);

		public function __construct() {
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$this->_o_fileSystemDirect   = new \WP_Filesystem_Direct( false );
			$this->_s_wp_upload_base_dir = wp_get_upload_dir()['basedir'];
		}

		public function set_board_id( $n_board_id ) {
			$this->_n_board_id = $n_board_id;
		}

		public function set_x2b_sequence( $n_max_seq ) {
			global $wpdb;
			// sync x2b_sequence
			$n_max_seq = esc_sql( intval( $n_max_seq ) );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}x2b_sequence AUTO_INCREMENT={$n_max_seq}" );
		}

		/**
		 * XE2 XML 복원파일을 입력받아 기존 데이터를 비우고 DB에 입력한다.
		 *
		 * @param string $s_uploaded_file
		 */
		public function import_xe_xml( $s_uploaded_file, $s_uploaded_filename ) {
			$s_base_path = $this->_s_wp_upload_base_dir . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'dump' .
							DIRECTORY_SEPARATOR . $this->_n_board_id;
			if ( ! file_exists( $s_base_path ) ) {
				wp_mkdir_p( $s_base_path );
			}

			echo '기존 게시판에 대해서 301 Moved Permanently 수행하려면 ' . $s_base_path . DIRECTORY_SEPARATOR . $s_uploaded_filename . '.json 파일을 확인하세요.<BR>';

			$zip       = new \ZipArchive();
			$res       = $zip->open( $s_uploaded_file );
			$xml_files = array();
			if ( $res === true ) {  // echo 'unzip case!';
				$user_dirname = $s_base_path . DIRECTORY_SEPARATOR . 'unzipped';
				if ( ! file_exists( $user_dirname ) ) {
					wp_mkdir_p( $user_dirname );
				}
				$zip->extractTo( $user_dirname );
				$zip->close();
				unset( $zip );

				$files_in_folder = list_files( $user_dirname );
				foreach ( $files_in_folder as $s_single_file ) {
					if ( is_file( $s_single_file ) ) {
						$xml_files[] = $s_single_file;
					}
				}
				unset( $files_in_folder );

			} else {  // echo 'single xml case';
				$xml_files[] = $s_uploaded_file;
			}
			unset( $res );
			unset( $zip );

			$o_mapper          = new \stdClass(); // SEO translation mapper
			$o_mapper->old_mid = null;
			$o_mapper->map     = array();

			$post = get_post( intval( $this->_n_board_id ) );

			include 'XML2Array.class.php';
			foreach ( $xml_files as $s_single_file ) {
				$mapping_rst   = $this->_proc_xml_file( $s_single_file );
				$o_mapper->map = array_merge( $o_mapper->map, $mapping_rst->translation_mapper );
			}
			unset( $xml_files );
			$o_mapper->old_mid = $mapping_rst->old_mid;

			// remove unzip directory
			$this->_o_fileSystemDirect->rmdir( $s_base_path . DIRECTORY_SEPARATOR . 'unzipped', true );
			// write mapper file
			$this->_o_fileSystemDirect->put_contents( $s_base_path . DIRECTORY_SEPARATOR . $s_uploaded_filename . '.json', json_encode( $o_mapper ) );
		}

		private function _proc_xml_file( $file ) {
			global $wpdb;

			echo basename( $file ) . ' 파일을 처리합니다.<BR>';
			$xml               = file_get_contents( $file );
			$xml               = trim( $xml );
			$array             = \XML2Array::createArray( $xml );
			$a_linear_cat_info = array();

			if ( $array['posts']['@attributes']['startSeq'] == 0 ) {
				echo '연속된 XML 덤프의 첫 파일이므로 목표 게시판의 기존 정보를 제거합니다.<BR>';
				// 게시판 기존 문서 정보 삭제 - 시작
				$a_old_posts_from_board = $wpdb->get_results( "SELECT `post_id` FROM `{$wpdb->prefix}x2b_posts` WHERE `board_id`='{$this->_n_board_id}'" );

				// wp posts 초기화
				$a_wp_posts_to_delete_comment = array();
				if ( count( $a_old_posts_from_board ) ) {
					$a_old_post_list = array();
					foreach ( $a_old_posts_from_board as $_ => $row ) {
						$a_old_post_list[] = intval( $row->post_id );
					}
					$s_in_list = implode( ',', $a_old_post_list );
					unset( $a_old_post_list );

					$a_wp_posts_to_delete_comment = $wpdb->get_results( "SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_name` IN ({$s_in_list})" );
					$wpdb->query( "DELETE FROM `{$wpdb->prefix}posts` WHERE `post_name` IN ({$s_in_list})" );
				}
				unset( $a_old_posts_from_board );

				// wp comments 초기화
				if ( count( $a_wp_posts_to_delete_comment ) ) {
					$a_old_post_list = array();
					foreach ( $a_wp_posts_to_delete_comment as $_ => $row ) {
						$a_old_post_list[] = intval( $row->ID );
					}
					$s_in_list = implode( ',', $a_old_post_list );
					unset( $a_old_post_list );
					$wpdb->query( "DELETE FROM `{$wpdb->prefix}comments` WHERE `comment_post_ID` IN ({$s_in_list})" );
				}

				// x2board 초기화
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_categories` WHERE `board_id`='{$this->_n_board_id}'" );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_comments` WHERE `board_id`='{$this->_n_board_id}'" );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_comments_list` WHERE `board_id`='{$this->_n_board_id}'" );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_posts` WHERE `board_id`='{$this->_n_board_id}'" );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id`='{$this->_n_board_id}'" );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_user_define_vars` WHERE `board_id`='{$this->_n_board_id}'" );

				// 첨부 파일 초기화
				$s_attach_base_path         = $this->_s_wp_upload_base_dir . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR;
				$s_old_thumbnails_path      = $s_attach_base_path . 'thumbnails' . DIRECTORY_SEPARATOR . $this->_n_board_id;
				$s_old_attach_binaries_path = $s_attach_base_path . 'attach' . DIRECTORY_SEPARATOR . 'binaries' . DIRECTORY_SEPARATOR . $this->_n_board_id;
				$s_old_attach_images_path   = $s_attach_base_path . 'attach' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $this->_n_board_id;

				// 첨부 파일 저장 폴더 삭제
				$this->_o_fileSystemDirect->rmdir( $s_old_thumbnails_path, true );
				$this->_o_fileSystemDirect->rmdir( $s_old_attach_binaries_path, true );
				$this->_o_fileSystemDirect->rmdir( $s_old_attach_images_path, true );
				$wpdb->query( "DELETE FROM `{$wpdb->prefix}x2b_files` WHERE `board_id`='{$this->_n_board_id}'" );
				// 게시판 기존 문서 정보 삭제 - 종료

				// register category
				require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR . 'category.admin.controller.php';
				$o_cat_admin_controller = new \X2board\Includes\Modules\Category\categoryAdminController();

				echo '연속된 XML 덤프의 첫 파일이므로 원본 게시판의 카테고리 정보를 입력합니다.<BR>';
				$a_cat_info = array();
				if( isset($array['posts']['categories']) ) {
					foreach ( $array['posts']['categories']['category'] as $cat_idx => $cat_rec ) {
						$n_xe_cat_id      = $cat_rec['@attributes']['sequence'];
						$s_new_cat_name   = sanitize_text_field( $cat_rec['@value'] );
						$xe_cat_parent_id = intval( $cat_rec['@attributes']['parent'] );
						if ( $xe_cat_parent_id ) {
							$n_cat_parent_id = $a_cat_info[ $xe_cat_parent_id ]['n_cat_id'];
						} else {
							$n_cat_parent_id = 0;
						}
						$n_new_cat_id               = $o_cat_admin_controller->create_new_category( $this->_n_board_id, $s_new_cat_name );
						$a_cat_info[ $n_xe_cat_id ] = array(
							'xe_cat_name'     => $s_new_cat_name,
							'xe_parent_id'    => $cat_rec['@attributes']['parent'],
							'n_cat_id'        => $n_new_cat_id,
							'n_parent_cat_id' => $n_cat_parent_id,
						);
					}
				}

				// serialized category to update structure
				$a_serialized_cat = array();
				foreach ( $a_cat_info as $_ => $a_cat ) {
					$a_serialized_cat[] = urlencode( 'tree_category[' . $a_cat['n_cat_id'] . '][id]' ) . '=' . $a_cat['n_cat_id'];
					$a_serialized_cat[] = urlencode( 'tree_category[' . $a_cat['n_cat_id'] . '][is_default]' ) . '=';
					$a_serialized_cat[] = urlencode( 'tree_category[' . $a_cat['n_cat_id'] . '][title]' ) . '=' . $a_cat['xe_cat_name'];
					$s_parent_cat_id    = $a_cat['n_parent_cat_id'] ? $a_cat['n_parent_cat_id'] : '';
					$a_serialized_cat[] = urlencode( 'tree_category[' . $a_cat['n_cat_id'] . '][parent_id]' ) . '=' . $s_parent_cat_id;
				}
				$s_serialized_cat = implode( '&', $a_serialized_cat );
				unset( $a_serialized_cat );
				// update category structure
				$o_cat_admin_controller->update_category( $this->_n_board_id, $s_serialized_cat );
				unset( $o_cat_admin_controller );

				// arrange newly registered category info
				foreach ( $a_cat_info as $_ => $a_single_cat ) {
					$a_linear_cat_info[ $a_single_cat['xe_cat_name'] ] = $a_single_cat['n_cat_id'];
				}
				unset( $a_cat_info );
			} else {
				// retireve category
				echo '연속된 XML 덤프의 후속 파일이므로 목표 게시판의 카테고리 정보를 호출합니다.<BR>';
				// arrange already registered category info
				foreach ( $document_category as $category_id => $kb_tree_cat ) {
					$a_linear_cat_info[ $kb_tree_cat->category_name ] = $category_id;
				}
			}
			unset( $array['posts']['categories']['category'] );
			// $a_linear_cat_info = array('분류1' => 42, '분류1-1' => 43, '분류1-1-1' => 44,
			// '분류2' => 45, '분류2-1' => 46 );

			// begin - buildup proc Context for admin
			global $G_X2B_CACHE;
			require_once X2B_PATH . 'includes' . DIRECTORY_SEPARATOR . 'func.inc.php';
			$o_context = \X2board\Includes\buildup_context_from_admin();
			$o_context->init( 'admin_import' );
			unset( $o_context );
			// end - buildup proc Context for admin

			$o_post_controller    = \X2board\Includes\get_controller( 'post' );
			$o_comment_controller = \X2board\Includes\get_controller( 'comment' );

			$seo_translation_mapper  = array();
			$user_translation_mapper = array();  // $user_translation_mapper[<string>xe_user_id] = <int>wp_user_id

			// register document
			foreach ( $array['posts']['post'] as $idx => $rec ) {
				$o_translation_info = new \stdClass();
				echo basename( $file ) . ' 파일의 ' . $idx . '번째 데이터를 등록합니다.<BR>';

				$o_cur_post = new \stdClass(); // $a_new_post = [];

				$n_new_post_id        = $rec['document_srl'];
				$o_cur_post->board_id = $this->_n_board_id;
				$o_cur_post->post_id  = $n_new_post_id;

				// user_id
				$o_cur_post->post_author = isset( $rec['user_id'] ) ? $this->_get_wp_user_id( $rec['user_id'], $user_translation_mapper ) : 0;
				$o_cur_post->nick_name   = $rec['nick_name'];
				// html_entity_decode($a, ENT_XML1, "UTF-8");
				$o_cur_post->title          = htmlspecialchars_decode( $rec['title'] );
				$o_cur_post->content        = isset( $rec['content'] ) ? htmlspecialchars_decode( $rec['content'] ) : null;
				$o_cur_post->regdate_dt     = date( 'Y-m-d H:i:s', strtotime( $rec['regdate'] ) );
				$o_cur_post->last_update_dt = date( 'Y-m-d H:i:s', strtotime( $rec['last_update'] ) );
				$o_cur_post->readed_count   = isset( $rec['readed_count'] ) ? intval( $rec['readed_count'] ) : 0;
				$o_cur_post->voted_count = 0;

				if ( isset( $rec['comments'] ) ) {
					$o_cur_post->comment_count = intval( $rec['comments']['@attributes']['count'] );
					$proc_commment             = true;
				} else {
					$proc_commment             = false;
					$o_cur_post->comment_count = 0;
				}

				if ( isset( $rec['attaches'] ) && $rec['attaches']['@attributes']['count'] > 0 ) { // register attaches to post
					var_dump( 'attach of post' );
					if ( isset( $rec['attaches']['attach']['filename'] ) ) {
						$a_attaches[] = $rec['attaches']['attach'];
					} else {
						$a_attaches = $rec['attaches']['attach'];
					}
					$this->_register_attachments( $n_new_post_id, $a_attaches );
					unset( $a_attaches );
				}

				$o_cur_post->voted_count    = isset( $rec['voted_count'] ) ? intval( $rec['voted_count'] ) : 0;
				$o_cur_post->category_id    = isset( $rec['category'] ) ? $a_linear_cat_info[ $rec['category'] ] : 0;
				$o_cur_post->is_notice      = $rec['is_notice'] == 'Y' ? 'true' : null;
				$o_cur_post->comment_status = 'ALLOW';
				$o_cur_post->status         = $rec['status'];
				$o_cur_post->password       = isset( $rec['password'] ) ? $rec['password'] : null;
				$o_cur_post->ipaddress      = $rec['ipaddress'];
				// fixed columns
				$o_cur_post->ua = '';
				$output         = $o_post_controller->insert_post( $o_cur_post, true );
				unset( $o_cur_post );

				$o_translation_info->old_doc_srl = $n_new_post_id;
				$o_translation_info->new_doc_id  = $n_new_post_id;
				$seo_translation_mapper[]        = $o_translation_info;

				// register comment
				if ( $proc_commment ) {
					// single comment array structure is different with multiple comment
					if ( isset( $rec['comments']['comment']['sequence'] ) ) {
						$a_comments[] = $rec['comments']['comment'];
					} else {
						$a_comments = $rec['comments']['comment'];
					}
					foreach ( $a_comments as $_ => $single_comment_info ) {
						$n_comment_id = intval( $single_comment_info['sequence'] );

						if ( isset( $single_comment_info['attaches'] ) && $single_comment_info['attaches']['@attributes']['count'] > 0 ) {
							var_dump( 'attach on comment' );
							if ( isset( $single_comment_info['attaches']['attach']['filename'] ) ) {
								$a_attaches[] = $single_comment_info['attaches']['attach'];
							} else {
								$a_attaches = $single_comment_info['attaches']['attach'];
							}
							$this->_register_attachments( $n_comment_id, $a_attaches );
							unset( $a_attaches );
						}

						$o_comment_data                    = new \stdClass();
						$o_comment_data->comment_id        = $n_comment_id;
						$o_comment_data->board_id          = $this->_n_board_id;
						$o_comment_data->parent_post_id    = $n_new_post_id;
						$o_comment_data->parent_comment_id = isset( $single_comment_info['parent'] ) ? intval( $single_comment_info['parent'] ) : 0;
						$o_comment_data->comment_author    = isset( $single_comment_info['user_id'] ) ? $this->_get_wp_user_id( $single_comment_info['user_id'], $user_translation_mapper ) : 0;
						$o_comment_data->nick_name         = $single_comment_info['nick_name'];
						$o_comment_data->email_address     = null;
						$o_comment_data->content           = $single_comment_info['content'];
						$o_comment_data->voted_count       = 0;
						$o_comment_data->blamed_count      = 0;
						$o_comment_data->regdate_dt        = $single_comment_info['regdate'];
						$o_comment_data->status            = 1;
						$o_comment_data->password          = isset( $single_comment_info['password'] ) ? $single_comment_info['password'] : '';
						$o_comment_data->ipaddress         = $single_comment_info['ipaddress'];
						$o_comment_data->ua                = '';

						$o_comment_controller->insert_comment( $o_comment_data, true );
						unset( $o_comment_data );
					}
					unset( $a_comments );
				}
			}
			unset( $a_linear_cat_info );
			unset( $o_post_controller );
			unset( $o_comment_controller );

			$o_rst                     = new \stdClass();
			$o_rst->translation_mapper = $seo_translation_mapper;
			$o_rst->old_mid            = $array['posts']['@attributes']['mid'];
			return $o_rst;
		}

		private function _register_attachments( $n_upload_target_id, $a_attaches_from_xml ) {
			echo 'register attaches to ' . $n_upload_target_id . '<BR>';
			$o_file_controller = \X2board\Includes\get_controller( 'file' );
			$file_info         = array();

			foreach ( $a_attaches_from_xml as $_ => $single_attach_info ) {
				$file_info['tmp_name'] = $this->_s_wp_upload_base_dir . DIRECTORY_SEPARATOR . explode( '/files/', $single_attach_info['path'] )[1];
				$file_info['name']     = $single_attach_info['filename'];
				$o_file_controller->insert_file( $file_info, $this->_n_board_id, $n_upload_target_id, intval( $single_attach_info['download_count'] ), true );
			}
			unset( $import_data );

			unset( $o_file_controller );
		}

		private function _get_wp_user_id( $xe_user_id, &$user_translation_mapper ) {
			if ( isset( $user_translation_mapper[ $xe_user_id ] ) ) {
				return $user_translation_mapper[ $xe_user_id ];
			} else {
				$user = get_user_by( 'login', $xe_user_id );
				if ( $user ) {
					echo $xe_user_id . ' User is ' . $user->ID . '<BR>';
					$user_translation_mapper[ $xe_user_id ] = intval( $user->ID );
					return isset( $user->ID ) ? intval( $user->ID ) : 0;
				}
			}
		}
	}
}
