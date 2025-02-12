<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postModel class
 * model class of the module post
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 */
namespace X2board\Includes\Modules\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Post\\postModel' ) ) {

	class postModel extends post {
		private $_a_default_fields     = array();
		private $_a_extends_fields     = array();
		private $_a_user_define_fields = array();

		/**
		 * constructor
		 *
		 * @return void
		 */
		public function __construct() {
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['EXTRA_VARS'] ) ) {
				$G_X2B_CACHE['EXTRA_VARS'] = array();
			}

			if ( ! isset( $G_X2B_CACHE['X2B_USER_DEFINE_KEYS'] ) ) {
				$G_X2B_CACHE['X2B_USER_DEFINE_KEYS'] = array();
			}

			$o_post_user_define_fields = \X2board\Includes\Classes\GuestUserDefineFields::getInstance();
			$this->_a_default_fields   = $o_post_user_define_fields->get_default_fields();
			$this->_a_extends_fields   = $o_post_user_define_fields->get_extended_fields();
			unset( $o_post_user_define_fields );
			$this->_set_user_define_fields();
		}

		/**
		 * Initialization
		 *
		 * @return void
		 */
		// function init() {}

		/**
		 * bringing the list of posts
		 * getDocumentList($obj, $except_notice = false, $load_extra_vars=true, $columnList = array())
		 *
		 * @param object $obj
		 * @param bool   $except_notice
		 * @param bool   $load_extra_vars
		 * @param array  $columnList
		 * @return BaseObject
		 */
		public function get_post_list( $obj, $except_notice = false, $load_extra_vars = true, $columnList = array() ) {
			global $G_X2B_CACHE;
			$o_sort_check = $this->_set_sort_index( $obj, $load_extra_vars );

			$obj->sort_index           = $o_sort_check->sort_index;
			$obj->is_user_define_field = $o_sort_check->is_user_define_field;

			global $wpdb;
			$o_search_check = $this->_set_search_option( $obj ); // , $args);// , $query_id, $use_division);
			$query_id       = $o_search_check->s_query_id; // 'post.getPostList';   // basic document list query
			if ( $o_sort_check->is_user_define_field && substr_count( $obj->search_target, 'extra_vars' ) ) {
			} elseif ( $o_sort_check->is_user_define_field ) {
				$output = executeQueryArray( $query_id, $args );
			} else {
				// document.getDocumentList query execution
				// Query_id if you have a group by clause getDocumentListWithinTag getDocumentListWithinComment or used again to perform the query because
				$groupByQuery = array(
					'post.getPostListWithinComment'   => 1,
					'post.getPostListWithinExtraVars' => 1,
					// 'post.getPostListWithinTag'       => 1,
				);
				if ( isset( $groupByQuery[ $query_id ] ) ) {
					$group_args             = clone($args);
					$group_args->sort_index = 'documents.' . $args->sort_index;
					$output                 = executeQueryArray( $query_id, $group_args );
					if ( ! $output->to_bool() || ! count( $output->data ) ) {
						return $output;
					}

					foreach ( $output->data as $key => $val ) {
						if ( $val->document_srl ) {
							$target_srls[] = $val->document_srl;
						}
					}

					$page_navigation = $output->page_navigation;
					$keys            = array_keys( $output->data );
					$virtual_number  = $keys[0];

					$target_args                = new stdClass();
					$target_args->document_srls = implode( ',', $target_srls );
					$target_args->list_order    = $args->sort_index;
					$target_args->order_type    = $args->order_type;
					$target_args->list_count    = $args->list_count;
					$target_args->page          = 1;
					$output                     = executeQueryArray( 'post.getPosts', $target_args );
					$output->page_navigation    = $page_navigation;
					$output->total_count        = $page_navigation->total_count;
					$output->total_page         = $page_navigation->total_page;
					$output->page               = $page_navigation->cur_page;
				} elseif ( $query_id == 'post.getPostList' ) { // basic post list query
					$o_query             = new \stdClass();
					$o_query->s_tables   = '`' . $wpdb->prefix . 'x2b_posts` as `posts`';
					$o_query->s_columns  = '*';
					$o_query->s_where    = $o_search_check->s_where . " AND `posts`.`is_notice`='N'";
					$o_query->s_orderby  = $o_search_check->s_orderby;
					$o_query->page       = $obj->page;
					$o_query->list_count = $obj->list_count;
					$o_query->page_count = $obj->page_count;
					$output              = \X2board\Includes\get_paginate_select( $o_query ); // $query_id, $args, $columnList);
					unset( $o_query );
				} elseif ( $query_id == 'post.getPostListWithExtraVars' ) { // extended user define field list query
					$o_query             = new \stdClass();
					$o_query->s_tables   = '`' . $wpdb->prefix . 'x2b_posts` as `posts`, `' . $wpdb->prefix . 'x2b_user_define_vars` as `user_vars` ';
					$o_query->s_columns  = '*';
					$o_query->s_where    = $o_search_check->s_where . " AND `posts`.`is_notice`='N'";
					$o_query->s_groupby  = $o_search_check->s_groupby;
					$o_query->s_orderby  = $o_search_check->s_orderby;
					$o_query->page       = $obj->page;
					$o_query->list_count = $obj->list_count;
					$o_query->page_count = $obj->page_count;
					$output              = \X2board\Includes\get_paginate_select( $o_query );
					unset( $o_query );
				}
			}
			unset( $o_sort_check );
			unset( $o_search_check );

			// Return if no result or an error occurs
			if ( ! $output->to_bool() || ! count( $output->data ) ) {
				return $output;
			}

			$idx  = 0;
			$data = $output->data;
			unset( $output->data );

			if ( ! isset( $virtual_number ) ) {
				$keys           = array_keys( $data );
				$virtual_number = $keys[0];
			}

			if ( $except_notice ) {
				foreach ( $data as $key => $attribute ) {
					if ( $attribute->is_notice == 'Y' ) {
						--$virtual_number;
					}
				}
			}

			$output->data = array();
			foreach ( $data as $key => $attribute ) {
				if ( $except_notice && $attribute->is_notice == 'Y' ) {
					continue;
				}
				$post_id = $attribute->post_id;
				if ( ! isset( $G_X2B_CACHE['POST_LIST'][ $post_id ] ) ) {
					$o_post = null;
					$o_post = new \X2board\Includes\Modules\Post\postItem();
					$o_post->set_attr( $attribute, false );
					$G_X2B_CACHE['POST_LIST'][ $post_id ] = $o_post;
				}

				$output->data[ $virtual_number ] = $G_X2B_CACHE['POST_LIST'][ $post_id ];
				--$virtual_number;
			}

			if ( $load_extra_vars ) {
				$this->_set_to_all_post_extra_vars();
			}

			if ( count( $output->data ) ) {
				foreach ( $output->data as $number => $post ) {
					$output->data[ $number ] = $G_X2B_CACHE['POST_LIST'][ $post->post_id ];
				}
			}
			return $output;
		}

		/**
		 *
		 * getNoticeList($obj, $columnList = array())
		 *
		 * @param object $obj
		 * @param array  $columnList
		 * @return object|void
		 */
		public function get_notice_list( $obj, $columnList = array() ) {
			global $wpdb;
			$o_query               = new \stdClass();
			$o_query->s_query_type = 'select';
			$o_query->s_tables     = '`' . $wpdb->prefix . 'x2b_posts`';
			$o_query->s_columns    = '*';
			$o_query->s_where      = 'WHERE `board_id`=' . $obj->wp_page_id . " AND `is_notice`='Y'";
			$o_query->s_orderby    = 'ORDER BY `list_order` desc';
			$output                = \X2board\Includes\get_paginate_select( $o_query );
			unset( $o_query );
			if ( ! $output->to_bool() || ! $output->data ) {
				return $output;
			}

			global $G_X2B_CACHE;
			$result = new \stdClass();
			foreach ( $output->data as $key => $val ) {
				$post_id = $val->post_id;
				if ( ! $post_id ) {
					continue;
				}

				if ( ! isset( $G_X2B_CACHE['POST_LIST'][ $post_id ] ) ) {
					$o_post = null;
					$o_post = new postItem();
					$o_post->set_attr( $val, false );
					$G_X2B_CACHE['POST_LIST'][ $post_id ] = $o_post;
				}
				$result->data[ $post_id ] = $G_X2B_CACHE['POST_LIST'][ $post_id ];
			}
			$this->_set_to_all_post_extra_vars();
			foreach ( $result->data as $post_id => $val ) {
				$result->data[ $post_id ] = $G_X2B_CACHE['POST_LIST'][ $post_id ];
			}
			return $result;
		}

		/**
		 * getDocument($document_srl=0, $is_admin = false, $load_extra_vars=true, $columnList = array())
		 *
		 * @param int   $post_id
		 * @param bool  $is_admin
		 * @param bool  $load_extra_vars
		 * @param array $columnList
		 * @return postItem
		 */
		public function get_post( $n_post_id = 0, $is_admin = false, $load_extra_vars = true, $columnList = array() ) {
			if ( ! $n_post_id ) {
				return new postItem();
			}
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['POST_LIST'][ $n_post_id ] ) ) {
				$o_post = new postItem( $n_post_id, $load_extra_vars, $columnList );

				if ( ! $o_post->is_exists() ) {
					return $o_post;
				}
				$G_X2B_CACHE['POST_LIST'][ $n_post_id ] = $o_post;
				if ( $load_extra_vars ) {
					$this->_set_to_all_post_extra_vars();
				}
			}
			if ( $is_admin ) {
				$G_X2B_CACHE['POST_LIST'][ $n_post_id ]->set_grant();
			}
			return $G_X2B_CACHE['POST_LIST'][ $n_post_id ];
		}

		/**
		 * Bringing multiple posts (or paging)
		 *
		 * @param array|string $a_post_id
		 * @param bool         $is_admin
		 * @param bool         $load_extra_vars
		 * @param array        $columnList
		 * @return array value type is documentItem
		 * function getDocuments($a_post_id, $is_admin = false, $load_extra_vars=true, $columnList = array())
		 */
		public function get_posts( $a_post_id, $is_admin = false ) {
			// Get board_id of posts
			global $wpdb;
			$s_query = 'SELECT * FROM ' . $wpdb->prefix . 'x2b_posts WHERE `post_id` in (' . implode( ',', $a_post_id ) . ')';
			if ( $wpdb->query( $s_query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			} else {
				$a_post_list = $wpdb->get_results( $s_query );
				$wpdb->flush();
			}

			global $G_X2B_CACHE;
			$a_result = array();
			foreach ( $a_post_list as $_ => $o_post ) {
				$n_post_id = $o_post->post_id;
				if ( ! $n_post_id ) {
					continue;
				}

				if ( ! isset( $G_X2B_CACHE['POST_LIST'][ $n_post_id ] ) ) {
					$o_post_item = null;
					$o_post_item = new \X2board\Includes\Modules\Post\postItem();
					$o_post_item->set_attr( $o_post, false );
					if ( $is_admin ) {
						$o_post_item->set_grant();
					}
					$G_X2B_CACHE['POST_LIST'][ $n_post_id ] = $o_post_item;
				}
				$a_result[ $o_post->post_id ] = $G_X2B_CACHE['POST_LIST'][ $n_post_id ];
			}
			// if($load_extra_vars) $this->setToAllDocumentExtraVars();
			$a_post = array();
			if ( count( $a_result ) ) {
				foreach ( $a_result as $n_post_id => $val ) {
					$a_post[ $n_post_id ] = $G_X2B_CACHE['POST_LIST'][ $n_post_id ];
				}
			}
			return $a_post;
		}

		/**
		 * 게시판 사용자 포스트 작성 화면용 필드 정뵤 반환
		 *
		 * @return array
		 */
		public function get_default_fields() {
			$a_default_fields = $this->_a_default_fields;
			foreach ( $a_default_fields as $key => $value ) {
				if ( $this->_a_user_define_fields ) {
					if ( isset( $this->_a_user_define_fields[ $key ] ) ) {
						unset( $a_default_fields[ $key ] );
					}
				}
			}
			return $a_default_fields;
		}

		/**
		 * 확장 필드를 반환한다.
		 *
		 * @return array
		 */
		public function get_extended_fields() {
			return $this->_a_extends_fields;
		}

		/**
		 * Returns a list of user-defined fields, excluding default fields.
		 * differ with \includes\modules\post\post.item.php::get_user_define_extended_fields()
		 * this method returns list of the designated board
		 *
		 * @return array
		 */
		public function get_user_define_extended_fields( $n_board_id ) {
			$a_user_define_keys = $this->get_user_define_keys( $n_board_id );

			$o_post_user_define_fields = \X2board\Includes\Classes\GuestUserDefineFields::getInstance();
			$a_default_fields          = $o_post_user_define_fields->get_default_fields();
			unset( $o_post_user_define_fields );
			$a_ignore_field_type           = array_keys( $a_default_fields );
			$a_user_define_extended_fields = array();
			foreach ( $a_user_define_keys as $n_seq => $o_field ) {
				$field_type = ( isset( $o_field->type ) && $o_field->type ) ? $o_field->type : '';
				if ( in_array( $field_type, $a_ignore_field_type ) ) { // ignore default fields
					continue;
				}
				$a_user_define_extended_fields[ $n_seq ] = $o_field;
			}
			return $a_user_define_extended_fields;
		}

		/**
		 * retrieve user define fields from DB
		 * admin: 'field_name' => db: var_name  관리자 화면에서 [필드 레이블] 입력란은 field_name에 저장함
		 * admin: 'field_type' => db: var_type
		 * admin: 'meta_key' => db: eid
		 * admin: 'default_value' => db: var_default
		 * admin: 'description' => db: var_desc
		 * admin: 'required' => db: var_is_required
		 * admin: 'field_label' => db: ??  관리자 화면에서 용도 불명, 사용자 화면에서 기본 필드명 표시위한 용도
		 */
		private function _set_user_define_fields() {
			if ( ! empty( $this->_a_user_define_fields ) ) {
				return;
			}
			$n_board_id = \X2board\Includes\Classes\Context::get( 'board_id' );
			$s_columns  = '`var_name`, `var_type`, `var_is_required`, `var_search`, `var_default`, `var_desc`, `eid`, `json_param`';
			global $wpdb;
			$a_temp = $wpdb->get_results( "SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC" );

			foreach ( $a_temp as $_ => $o_field ) {
				$a_other_field = unserialize( $o_field->json_param );

				$a_single_field['field_type']    = $o_field->var_type;
				$a_single_field['field_name']    = $o_field->var_name;
				$a_single_field['meta_key']      = $o_field->eid;
				$a_single_field['default_value'] = $o_field->var_default;
				$a_single_field['description']   = $o_field->var_desc;
				$a_single_field['required']      = $o_field->var_is_required;
				if ( isset( $this->_a_default_fields[ $o_field->var_type ] ) ) {
					$a_single_field['class'] = $this->_a_default_fields[ $o_field->var_type ]['class'];
				} elseif ( isset( $this->_a_extends_fields[ $o_field->var_type ] ) ) {
					$a_single_field['class'] = $this->_a_extends_fields[ $o_field->var_type ]['class'];
				}

				$a_single_field                               = array_merge( $a_single_field, $a_other_field );
				$this->_a_user_define_fields[ $o_field->eid ] = $a_single_field;

				unset( $a_single_field );
				unset( $a_other_field );
			}
			unset( $a_temp );
		}

		/**
		 * 관리자가 설정한 입력 필드를 반환한다.
		 * getSkinFields()
		 *
		 * @return array
		 */
		public function get_user_define_fields() {
			$a_fields = array();
			if ( $this->_a_user_define_fields ) {
				$a_fields = $this->_a_user_define_fields;
			} else {
				$a_fields = $this->_a_default_fields;
			}
			$n_board_id          = \X2board\Includes\Classes\Context::get( 'board_id' );
			$o_user_define_field = \X2board\Includes\Classes\GuestUserDefineFields::getInstance();
			$o_user_define_field->set_board_id( $n_board_id );
			$o_user_define_field->set_user_define_keys_2_submit( $a_fields );
			return $o_user_define_field->get_user_define_vars();
		}

		/**
		 * A particular post to get the value of the extra variable function
		 * getExtraVars($module_srl, $document_srl)
		 *
		 * @param int $n_post_id
		 * @return array
		 */
		public function get_user_define_vars( $n_post_id ) {
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['EXTRA_VARS'][ $n_post_id ] ) ) {
				// Extended to extract the values of variables set
				$G_X2B_CACHE['POST_LIST'][ $n_post_id ] = $this->get_post( $n_post_id, false );
				$this->_set_to_all_post_extra_vars();
			}
			if ( isset( $G_X2B_CACHE['EXTRA_VARS'][ $n_post_id ] ) ) {
				return $G_X2B_CACHE['EXTRA_VARS'][ $n_post_id ];
			}
			return null;
		}

		/**
		 * Extra variables for each article will not be processed bulk select and apply the macro city
		 * setToAllDocumentExtraVars()
		 *
		 * @return void
		 */
		private function _set_to_all_post_extra_vars() {
			global $G_X2B_CACHE;
			$checked_posts = array();
			$_post_list    = &$G_X2B_CACHE['POST_LIST'];

			// X2B POST_LIST all posts that the object referred to the global variable settings
			if ( count( $_post_list ) <= 0 ) {
				return;
			}

			// Find all called the document object variable has been set extension
			$post_ids = array();
			foreach ( $_post_list as $key => $val ) {
				if ( ! $val->post_id || isset( $checked_posts[ $val->post_id ] ) ) {
					continue;
				}
				$checked_posts[ $val->post_id ] = true;
				$post_ids[]                     = $val->post_id;
			}
			// If the post number, return detected
			if ( ! count( $post_ids ) ) {
				return;
			}

			// Expand unspecified variables article about a current visitor to the extension of the language code, the search variable
			$a_rst = $this->get_post_user_define_vars_from_DB( $post_ids );
			$extra_vars = array();
			if ( $a_rst !== false && $a_rst ) {
				foreach ( $a_rst as $_ => $o_val ) {
					if ( ! isset( $o_val->value ) ) {
						continue;
					}
					if ( ! isset( $extra_vars[ $o_val->board_id ][ $o_val->post_id ][ $o_val->eid ][0] ) ) {
						$extra_vars[ $o_val->board_id ][ $o_val->post_id ][ $o_val->eid ][0] = trim( $o_val->value );
					}
					$extra_vars[ $o_val->post_id ][ $o_val->eid ][ 'ko' ] = trim( $o_val->value );
				}
			}

			$user_lang_code = $post_lang_code = 'ko';
			for ( $i = 0, $c = count( $post_ids );$i < $c;$i++ ) {
				$n_post_id = $post_ids[ $i ];
				unset( $vars );
				if ( ! $_post_list[ $n_post_id ] || ! is_object( $_post_list[ $n_post_id ] ) || ! $_post_list[ $n_post_id ]->is_exists() ) {
					continue;
				}
				$n_board_id = $_post_list[ $n_post_id ]->get( 'board_id' );
				$extra_keys = $this->get_user_define_keys( $n_board_id );

				if ( isset( $extra_vars[ $n_post_id ] ) ) {
					$vars           = $extra_vars[ $n_post_id ];  // user define field의 실제 입력값 추출
					// Expand the variable processing
					if ( count( $extra_keys ) ) {
						foreach ( $extra_keys as $_ => $key ) {
							$extra_keys[ $key->eid ] = clone($key);
							if ( isset( $vars[ $key->eid ] ) ) {
								$val = $vars[ $key->eid ];
								if ( isset( $val[ $user_lang_code ] ) ) {
									$v = $val[ $user_lang_code ];
								} elseif ( isset( $val[ $post_lang_code ] ) ) {
									$v = $val[ $post_lang_code ];
								} elseif ( isset( $val[0] ) ) {
									$v = $val[0];
								}
							} else {
								$v = null;
							}
							$extra_keys[ $key->eid ]->value = $v;
						}
					}
				}
				$G_X2B_CACHE['EXTRA_VARS'][ $n_post_id ] = $extra_keys;
			}
		}

		/**
		 * Return post extra information from database
		 * getDocumentExtraVarsFromDB($documentSrls)
		 *
		 * @param array $documentSrls
		 * @return object
		 */
		public function get_post_user_define_vars_from_DB( $a_post_id ) {
			if ( ! is_array( $a_post_id ) || count( $a_post_id ) == 0 ) {
				return new \X2board\Includes\Classes\BaseObject( -1, __( 'msg_invalid_request', X2B_DOMAIN ) );
			}
			global $wpdb;
			$s_tables = '`' . $wpdb->prefix . 'x2b_user_define_vars`';
			$s_where  = '`post_id` in (' . implode( ',', $a_post_id ) . ')';
			$a_temp   = $wpdb->get_results( "SELECT * FROM {$s_tables} WHERE {$s_where}" );
			if ( $a_temp === null ) {
				wp_die( $wpdb->last_error );
			} else {
				$wpdb->flush();
			}
			return $a_temp;
		}

		/**
		 * Import page of the post
		 * getDocumentPage($o_post, $opt)
		 *
		 * @param posttItem $o_post
		 * @param object    $opt
		 * @return int
		 */
		public function get_post_page( $o_post, $o_in_args ) {
			$o_sort_check                    = $this->_set_sort_index( $o_in_args, true );
			$o_in_args->sort_index           = $o_sort_check->sort_index;
			$o_in_args->is_user_define_field = $o_sort_check->is_user_define_field;

			$o_search_check = $this->_set_search_option( $o_in_args );
			if ( $o_sort_check->is_user_define_field ) {
				return 1;
			} elseif ( $o_sort_check->sort_index === 'list_order' || $o_sort_check->sort_index === 'update_order' ) {
				if ( $o_in_args->order_type === 'desc' ) {
					$o_in_args->{'rev_' . $o_sort_check->sort_index} = $o_post->get( $o_sort_check->sort_index );
				} else {
					$o_in_args->{$o_sort_check->sort_index} = $o_post->get( $o_sort_check->sort_index );
				}
			} elseif ( $o_sort_check->sort_index === 'regdate_dt' ) {

				if ( $o_in_args->order_type === 'asc' ) {
					$o_in_args->{'rev_' . $o_sort_check->sort_index} = $o_post->get( $o_sort_check->sort_index );
				} else {
					$o_in_args->{$o_sort_check->sort_index} = $o_post->get( $o_sort_check->sort_index );
				}
			} else {
				return 1;
			}
			// total number of the article search page
			$query_id = $o_search_check->s_query_id . 'Page';
			global $wpdb;
			$s_tables = '`' . $wpdb->prefix . 'x2b_posts` as `posts`';
			$s_query  = "SELECT COUNT(*) as `rec_cnt` FROM {$s_tables}";

			if ( $query_id == 'post.getPostListPage' ) {
				// SELECT count(`document_srl`) as `count` FROM `xe_documents` as `documents` WHERE `module_srl` in (?) and `status` in (?,?) and ( `list_order` <= ? )
				if ( isset( $o_in_args->list_order ) ) {
					$o_search_check->s_where .= ' AND `posts`.`list_order` <= ' . $o_in_args->list_order;
				}
			}

			$s_query  .= " {$o_search_check->s_where} {$o_search_check->s_orderby}";
			$o_rec_cnt = $wpdb->get_row( $s_query );
			if ( $o_rec_cnt === null ) {
				wp_die( $wpdb->last_error );
			} else {
				$wpdb->flush();
			}
			$count  = intval( $o_rec_cnt->rec_cnt );
			$n_page = (int) ( ( $count - 1 ) / $o_in_args->list_count ) + 1;
			return $n_page;
		}

		/**
		 * Setting sort index
		 * _setSortIndex($obj, $load_extra_vars)
		 *
		 * @param object $obj
		 * @param bool   $load_extra_vars
		 * @return object
		 */
		private function _set_sort_index( $obj, $load_extra_vars ) {
			$sortIndex            = $obj->sort_index;
			$is_user_define_field = false;
			$a_sortable_field     = array(
				'list_order',
				'regdate_dt',
				'last_update_dt',
				'update_order',
				'readed_count',
				'voted_count',
				'comment_count',
				'uploaded_count',
				'title',
				'category_id',
			);
			if ( ! in_array( $sortIndex, $a_sortable_field ) ) {
				// get module_srl extra_vars list
				if ( $load_extra_vars ) {
					$extra_output = executeQueryArray( 'post.getGroupsExtraVars', $extra_args );
					if ( ! $extra_output->data || ! $extra_output->to_bool() ) {
						$sortIndex = 'list_order';
					} else {
						$check_array = array();
						foreach ( $extra_output->data as $val ) {
							$check_array[] = $val->eid;
						}
						if ( ! in_array( $sortIndex, $check_array ) ) {
							$sortIndex = 'list_order';
						} else {
							$is_user_define_field = true;
						}
					}
				} else {
					$sortIndex = 'list_order';
				}
			}
			unset( $a_sortable_field );
			$o_rst                       = new \stdClass();
			$o_rst->sort_index           = $sortIndex;
			$o_rst->is_user_define_field = $is_user_define_field;
			return $o_rst;
		}

		/**
		 * 게시물 목록의 검색 옵션을 Setting함(2011.03.08 - cherryfilter)
		 * page변수가 없는 상태에서 page 값을 알아오는 method(getDocumentPage)는 검색하지 않은 값을 return해서 검색한 값을 가져오도록 검색옵션이 추가 됨.
		 * 검색옵션의 중복으로 인해 private method로 별도 분리
		 *
		 * @param object $searchOpt
		 * @param object $args
		 * @param string $query_id
		 * @param bool   $use_division
		 * @return void
		 */
		private function _set_search_option( $searchOpt ) {
			// Variable check
			$args              = new \stdClass();
			$args->category_id = $searchOpt->category_id ? $searchOpt->category_id : null;
			$args->order_type  = $searchOpt->order_type;
			$args->page        = $searchOpt->page ? $searchOpt->page : 1;
			$args->list_count  = $searchOpt->list_count ? $searchOpt->list_count : 20;
			$args->page_count  = $searchOpt->page_count ? $searchOpt->page_count : 10;
			$args->start_date  = isset( $searchOpt->start_date ) ? $searchOpt->start_date : null;
			$args->end_date    = isset( $searchOpt->end_date ) ? $searchOpt->end_date : null;
			$args->sort_index  = $searchOpt->sort_index;

			// Check the target and sequence alignment
			$orderType = array(
				'desc' => 1,
				'asc'  => 1,
			);
			if ( ! isset( $orderType[ $args->order_type ] ) ) {
				$args->order_type = 'asc';
			}
			unset( $orderType );

			if ( is_array( $searchOpt->wp_page_id ) ) {
				$args->board_id = implode( ',', $searchOpt->wp_page_id );
			} else {
				$args->board_id = $searchOpt->wp_page_id;
			}

			if ( isset( $searchOpt->exclude_board_id ) ) {
				if ( is_array( $searchOpt->exclude_board_id ) ) {
					$args->exclude_board_id = implode( ',', $searchOpt->exclude_board_id );
				} else {
					$args->exclude_board_id = $searchOpt->exclude_board_id;
				}
			} else {
				$args->exclude_board_id = null;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( isset( $searchOpt->statusList ) ) {
				$args->statusList = $searchOpt->statusList;
			} else {
				$args->statusList = array( $this->get_config_status( 'secret' ), $this->get_config_status( 'public' ) );
			}
			// Category is selected, further sub-categories until all conditions
			//
			// 여기서  category_id가 라벨에서 ID로 변경
			//
			if ( $args->category_id ) {
				$args->category_id = implode( ',', $this->_get_category_list( $args->category_id ) );
			}

			// Used to specify the default query id (based on several search options to query id modified)
			$query_id = 'post.getPostList';

			// If the search by specifying the post division naeyonggeomsaekil processed for
			$use_division = false;

			// Search options
			$search_target  = $searchOpt->search_target;
			$search_keyword = $searchOpt->search_keyword;

			global $wpdb;
			$s_field_search_clause = null;
			if ( $search_target && $search_keyword ) {
				switch ( $search_target ) {
					case 'title':
					case 'content':
						if ( $search_keyword ) {
							$search_keyword = str_replace( ' ', '%', $search_keyword );
						}
						// $args->{"s_".$search_target} = $search_keyword;
						$s_field_search_clause = '`' . $search_target . "` like '%" . $search_keyword . "%'";
						$use_division          = true;
						break;
					case 'title_content':
						if ( $search_keyword ) {
							$search_keyword = str_replace( ' ', '%', $search_keyword );
						}
						// $args->s_title = $search_keyword;
						// $args->s_content = $search_keyword;
						$s_field_search_clause = "( `title` like '%" . $search_keyword . "%' or `content` like '%" . $search_keyword . "%' )";
						$use_division          = true;
						break;
					// case 'user_name' :
					case 'nick_name':
					case 'email_address':
						// case 'homepage' :
						if ( $search_keyword ) {
							$search_keyword = str_replace( ' ', '%', $search_keyword );
						}
						// $args->{"s_".$search_target} = $search_keyword;
						$s_field_search_clause = '`' . $search_target . "` like '%" . $search_keyword . "%'";
						break;
					case 'comment':
						$args->s_comment = $search_keyword;
						$query_id        = 'post.getPostListWithinComment';
						$use_division    = true;
						break;
					case 'tag':
						if ( $search_keyword ) {
							$search_keyword = str_replace( ' ', '%', $search_keyword );
						}
						$s_field_search_clause = "`tags` like '%" . $search_keyword . "%'";
						$use_division          = true;
						// $query_id   		   = 'post.getPostListWithinTag';
						break;
					/*
					case 'user_id' :
						if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
						$args->s_user_id = $search_keyword;
						$args->sort_index = 'documents.'.$args->sort_index;
						break;
					case 'is_notice' :
						if($search_keyword=='N') {
							$args->{"s_".$search_target} = 'N';
						}
						elseif($search_keyword=='Y') {
							$args->{"s_".$search_target} = 'Y';
						}
						else {
							$args->{"s_".$search_target} = '';
						}
						break;
					case 'is_secret' :
						if($search_keyword=='N') {
							$args->statusList = array($this->getConfigStatus('public'));
						}
						elseif($search_keyword=='Y') {
							$args->statusList = array($this->getConfigStatus('secret'));
						}
						elseif($search_keyword=='temp') {
							$args->statusList = array($this->getConfigStatus('temp'));
						}
						break;
					case 'post_authors' :// case 'member_srl' :
					case 'readed_count' :
					case 'voted_count' :
					case 'comment_count' :
					// case 'trackback_count' :
					case 'uploaded_count' :
						$args->{"s_".$search_target} = (int)$search_keyword;
						break;
					case 'post_authors' :// case 'member_srls' :
						$args->{"s_".$search_target} = (int)$search_keyword;

						if($o_logged_info->ID) {
							$srls = explode(',', $search_keyword);
							foreach($srls as $srl) {
								if(abs($srl) != $o_logged_info->ID) {
									break; // foreach
								}
								$args->{"s_".$search_target} = $search_keyword;
								break; // foreach
							}
						}
						break;
					case 'blamed_count' :
						$args->{"s_".$search_target} = (int)$search_keyword * -1;
						break;
					case 'regdate_dt' : // case 'regdate' :
					case 'last_update_dt' :
					case 'ipaddress' :
						$args->{"s_".$search_target} = $search_keyword;
						break;*/
					// case 'extra_vars':
					// $args->var_value = str_replace(' ', '%', $search_keyword);
					// $query_id = 'document.getDocumentListWithinExtraVars';
					// break;
					default:  // extended user define fields case
						// check if extended user define field exists in the tbl::user_Define_keys
						$s_tables = '`' . $wpdb->prefix . 'x2b_user_define_keys`';
						$s_query  = "SELECT var_search FROM {$s_tables} ";
						$s_query .= 'WHERE `board_id` = ' . $args->board_id . " AND `eid` = '" . $search_target . "'";
						$data     = $wpdb->get_row( $s_query );
						if ( $data === null ) {  // weird situation as $search_target has been already validated at \includes\modules\board\board.view.php::_disp_post_list()
							wp_die( $wpdb->last_error );
						} else {
							$wpdb->flush();
						}
						if ( $data->var_search == 'Y' ) {
							$query_id               = 'post.getPostListWithExtraVars';
							$s_field_search_clause  = "`user_vars`.`board_id` = '" . $args->board_id . "' ";
							$s_field_search_clause  = '`user_vars`.`post_id` = `posts`.`post_id` ';
							$s_field_search_clause .= "AND `user_vars`.`eid` = '" . $search_target . "' ";
							$s_field_search_clause .= "AND `user_vars`.`value` like '%" . $search_keyword . "%'";
						}
						// if(strpos($search_target,'extra_vars')!==false) {
						// $args->var_idx = substr($search_target, strlen('extra_vars'));
						// $args->var_value = str_replace(' ','%',$search_keyword);
						// $args->sort_index = 'posts.'.$args->sort_index;
						// $query_id = 'post.getPostListWithExtraVars';
						// }
						break;
				}
			}
			unset( $o_logged_info );

			if ( $searchOpt->is_user_define_field ) {
				$query_id = 'post.getPostListExtraSort';
			} else {  // basic list
				/**
				 * list_order asc sort of division that can be used only when
				 */
				if ( $args->sort_index != 'list_order' || $args->order_type != 'asc' ) {
					$use_division = false;
				}

				/**
				 * If it is true, use_division changed to use the post division
				 */
				if ( $use_division ) {
					// Division begins
					$n_division = (int) \X2board\Includes\Classes\Context::get( 'division' );

					// order by list_order and (module_srl===0 or module_srl may count), therefore case table full scan
					if ( $args->sort_index == 'list_order' && ( $args->exclude_board_id === '0' || count( explode( ',', $args->board_id ) ) > 5 ) ) {
						// $listSqlID = 'document.getDocumentListUseIndex';
						$divisionSqlID = 'post.getDocumentDivisionUseIndex';
					} else {
						// $listSqlID = 'post.getPostList';
						$divisionSqlID = 'post.getPostDivision';
						// SELECT `list_order`  FROM `_posts` WHERE ( `board_id` in (?) and `board_id` not in (?,?) and `list_order` >= ? )
					}

					$o_query            = new \stdClass();
					$o_query->s_tables  = '`' . $wpdb->prefix . 'x2b_posts`';
					$o_query->s_columns = '`list_order`';

					$o_query->s_where = 'WHERE `board_id` in (' . $args->board_id . ') ';
					if ( $args->exclude_board_id ) {
						$o_query->s_where .= 'AND `board_id` not in (' . $args->exclude_board_id . ') ';
					}
					if ( isset( $args->list_order ) ) {
						$o_query->s_where .= 'AND `list_order` >= ' . $args->list_order;
					}
					$o_query->s_orderby  = 'ORDER BY ' . $args->sort_index . ' ' . $args->order_type;
					$o_query->list_count = 1;
					// If you do not value the best division top
					if ( ! $n_division ) {
						$output = \X2board\Includes\get_paginate_select( $o_query );
						if ( $output->data ) {
							$item       = array_pop( $output->data );
							$n_division = $item->list_order;
						}
						unset( $output );
					}
					// The last division
					$n_last_division = (int) \X2board\Includes\Classes\Context::get( 'last_division' );
					// Division after division from the 5000 value of the specified Wanted
					if ( ! $n_last_division ) {
						$o_query->page = 5001;
						$output        = \X2board\Includes\get_paginate_select( $o_query );
						if ( $output->data ) {
							$item            = array_pop( $output->data );
							$n_last_division = $item->list_order;
						}
					}
					unset( $o_query );

					// Make sure that after n_last_division article
					if ( $n_last_division ) {
						// "SELECT count(*) as `count` FROM `xe_documents` as `documents`  WHERE `module_srl` in (?) and `list_order` > ? "
						$s_tables = '`' . $wpdb->prefix . 'x2b_posts`';
						$s_query  = "SELECT COUNT(*) as `count` FROM {$s_tables} ";
						$s_query .= 'WHERE `board_id` in (' . $args->board_id . ') ';
						if ( $args->exclude_board_id ) {
							$s_query .= "AND `board_id` not in ('.$args->exclude_board_id.') ";
						}
						$s_query .= ' AND `list_order` > ' . $n_last_division;
						$data     = $wpdb->get_row( $s_query );
						if ( $data === null ) {
							wp_die( $wpdb->last_error );
						} else {
							$wpdb->flush();
						}
						if ( intval( $data->count ) < 1 ) {
							$n_last_division = null;
						}
					}
					// $args->division = $n_division;
					// $args->last_division = $n_last_division;
					\X2board\Includes\Classes\Context::set( 'division', $n_division );
					\X2board\Includes\Classes\Context::set( 'last_division', $n_last_division );
				}
			}

			$o_query_rst             = new \stdClass();
			$o_query_rst->s_query_id = $query_id;
			$o_query_rst->s_where    = 'WHERE (';
			$o_query_rst->s_where   .= '`posts`.`board_id` in (' . $args->board_id . ') ';

			if ( $args->category_id ) {
				$o_query_rst->s_where .= 'AND `category_id` in (' . $args->category_id . ') ';
			}
			if ( isset( $args->statusList ) && is_array( $args->statusList ) ) {
				$o_query_rst->s_where .= "AND `posts`.`status` in ('" . implode( "', '", $args->statusList ) . "') "; // and `list_order` <= 2100000000";
			}
			if ( $use_division ) {
				$o_query_rst->s_where .= 'AND ( `posts`.`list_order` >= ' . $n_division . ' AND `posts`.`list_order` < ' . $n_last_division . ' ) ';
			}
			if ( $s_field_search_clause ) {
				if ( $query_id == 'post.getPostList' ) {
					$o_query_rst->s_where .= 'AND ' . $s_field_search_clause . ' ';
				} elseif ( $query_id == 'post.getPostListWithExtraVars' ) {
					$o_query_rst->s_where .= 'AND ' . $s_field_search_clause . ' ';
				}
			}
			$o_query_rst->s_where .= ') ';

			if ( isset( $args->list_order ) ) {
				$o_query_rst->s_where .= 'AND `posts`.`list_order` <=' . $args->list_order . ' ';
			}

			if ( $query_id == 'post.getPostListWithExtraVars' ) {
				$o_query_rst->s_groupby = 'GROUP BY `user_vars`.`post_id` ';
			}

			if ( isset( $args->sort_index ) ) {
				$o_query_rst->s_orderby = 'ORDER BY `posts`.`' . $args->sort_index . '` ' . $args->order_type;
			}
			return $o_query_rst;
		}

		/**
		 * Return status name list
		 * getStatusNameList()
		 *
		 * @return array
		 */
		public function get_status_name_list() {
			global $a_translated_status_name;
			if ( ! isset( $a_translated_status_name ) ) {
				$a_translated_status_name = array();
				foreach ( $this->get_status_list() as $s_key => $s_val ) {
					$a_translated_status_name[ $s_val ] = __( 'opt_' . $s_key, X2B_DOMAIN );
				}
				return $a_translated_status_name;
			}
			return $a_translated_status_name;
		}

		/**
		 * Function to retrieve the key values of the extended variable document
		 * $Form_include: writing articles whether to add the necessary extensions of the variable input form
		 * getExtraKeys($module_srl)
		 *
		 * @param int $board_id
		 * @return array
		 */
		public function get_user_define_keys( $n_board_id ) {
			global $G_X2B_CACHE;
			if ( ! isset( $G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][ $n_board_id ] ) ) {
				$a_keys          = false;
				$o_cache_handler = \X2board\Includes\Classes\CacheHandler::getInstance( 'object', null, true );
				if ( $o_cache_handler->isSupport() ) {
					$object_key = 'module_post_user_define_keys:' . $n_board_id;
					$cache_key  = $o_cache_handler->getGroupKey( 'site_and_module', $object_key );
					$a_keys     = $o_cache_handler->get( $cache_key );
				}

				if ( $a_keys === false ) {  // _set_user_define_fields()과 동일한 DB 호출  -> 캐쉬화해야 함
					$s_columns = '`board_id` as `board_id`, `var_idx` as `idx`, `var_name` as `name`, `var_type` as `type`, `var_is_required` as `is_required`, `var_search` as `search`, `var_default` as `default`, `var_desc` as `desc`, `eid` as `eid`  ';
					global $wpdb;
					$a_temp = $wpdb->get_results( "SELECT {$s_columns} FROM `{$wpdb->prefix}x2b_user_define_keys` WHERE `board_id` = '{$n_board_id}' ORDER BY `var_idx` ASC" );

					// correcting index order
					// DB에서 가져온 첫번째 var_idx가 1보다 클 때 idx를 수정함
					// XE는 변수 순서를 바꿀 때마다 ajax 갱신해서 문제가 될 수 있지만
					// WP는 변경된 순서를 일괄 컴파일해서 저장하므로 문제가 될 가능성이 낮음
					/*
					$isFixed = FALSE;
					if(is_array($a_temp)) {
						$prevIdx = 0;
						foreach($a_temp as $no => $value) {
							// case first
							if($prevIdx == 0 && $value->idx != 1) {
								$prevIdx = 1;
								$isFixed = TRUE;
								continue;
							}

							// case others
							if($prevIdx > 0 && $prevIdx + 1 != $value->idx) {
								$prevIdx += 1;
								$isFixed = TRUE;
								continue;
							}
							$prevIdx = $value->idx;
						}
					}
					if($isFixed) {
						$output = executeQueryArray('document.getDocumentExtraKeys', $obj);
					}*/

					$o_user_define_fields = \X2board\Includes\Classes\GuestUserDefineFields::getInstance(); // $n_board_id);
					$o_user_define_fields->set_user_define_keys_2_display( $a_temp );
					$a_keys = $o_user_define_fields->get_user_define_vars();
					unset( $o_user_define_fields );
					if ( ! $a_keys ) {
						$a_keys = array();
					}

					if ( $o_cache_handler->isSupport() ) {
						$o_cache_handler->put( $cache_key, $a_keys );
					}
				}
				unset( $o_cache_handler );
				$G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][ $n_board_id ] = $a_keys;
			}
			return $G_X2B_CACHE['X2B_USER_DEFINE_KEYS'][ $n_board_id ];
		}

		/**
		 * Bringing the Categories list the specific module
		 * Speed and variety of categories, considering the situation created by the php script to include a list of the must, in principle, to use
		 * getCategoryList()
		 *
		 * @param array $columnList
		 * @return array
		 */
		private function _get_category_list( $s_category_label ) {
			$o_category = \X2board\Includes\get_model( 'category' );
			$n_board_id = \X2board\Includes\Classes\Context::get( 'board_id' );
			$o_category->set_board_id( $n_board_id );
			$a_tree_category = $o_category->build_linear_category();
			unset( $o_category );

			$a_category_id = array();
			foreach ( $a_tree_category as $n_cat_id => $o_cat_info ) {
				if ( $o_cat_info->title == $s_category_label ) {
					$a_category_id[] = $n_cat_id;
					$a_category_id   = array_merge( $a_category_id, $o_cat_info->children );
					return $a_category_id;
				}
			}
			return $a_category_id;
		}
	}
}
