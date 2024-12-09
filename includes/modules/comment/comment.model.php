<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * commentModel class
 * model class of the comment module
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/comment
 */
namespace X2board\Includes\Modules\Comment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Comment\\commentModel' ) ) {

	class commentModel extends comment {

		/**
		 * Initialization
		 *
		 * @return void
		 */
		public function init() { }

		/**
		 * Get the comment
		 * getComment($comment_srl = 0, $is_admin = FALSE, $columnList = array())
		 *
		 * @param int   $comment_srl
		 * @param bool  $is_admin
		 * @param array $columnList
		 * @return commentItem
		 */
		public function get_comment( $comment_id = 0, $is_admin = false, $columnList = array() ) {
			$o_comment = new commentItem( $comment_id, $columnList );
			if ( $is_admin ) {
				$o_comment->set_grant();
			}
			return $o_comment;
		}

		/**
		 * Get the total number of comments in corresponding with document_srl.
		 * getCommentCount($document_srl)
		 *
		 * @param int $document_srl
		 * @return int
		 */
		public function get_comment_count( $n_parent_post_id ) {
			// get the number of comments on the post module
			$o_post_model = \X2board\Includes\get_model( 'post' );
			$columnList   = null;
			$o_post       = $o_post_model->get_post( $n_parent_post_id, false, true, $columnList );
			unset( $o_post_model );

			// return if no post exists.
			if ( ! $o_post->is_exists() ) {
				return;
			}

			// get a list of comments
			$n_post_id = $o_post->get( 'post_id' );
			unset( $o_post );

			// check if module is using validation system
			$o_comment_controller = \X2board\Includes\get_controller( 'comment' );
			$is_using_validation  = $o_comment_controller->is_using_comment_validation();
			unset( $o_comment_controller );
			$s_where = "`parent_post_id`='$n_parent_post_id'";
			if ( $is_using_validation ) {
				$s_where .= 'AND status=1';
			}
			global $wpdb;
			$count = $wpdb->get_var( "SELECT count(*) as `count` FROM `{$wpdb->prefix}x2b_comments` WHERE {$s_where}" );
			return intval( $count );
		}

		/**
		 * Get a comment list of the post in corresponding post_id
		 * getCommentList($document_srl, $page = 0, $is_admin = FALSE, $count = 0)
		 *
		 * @param int  $document_srl
		 * @param int  $page
		 * @param bool $is_admin
		 * @param int  $count
		 * @return object
		 */
		public function get_comment_list( $n_parent_post_id, $page = 0, $is_admin = false, $count = 0 ) {
			if ( ! isset( $n_parent_post_id ) ) {
				return;
			}

			// get the number of comments on the document module
			$o_post_model = \X2board\Includes\get_model( 'post' );
			$columnList   = array( 'n_post_id', 'board_id', 'comment_count' );
			$o_post       = $o_post_model->get_post( $n_parent_post_id, false, true, $columnList );
			unset( $o_post_model );

			// return if no post exists.
			if ( ! $o_post->is_exists() ) {
				return;
			}

			// return if no comment exists
			if ( $o_post->get_comment_count() < 1 ) {
				return;
			}
			$board_id = $o_post->get( 'board_id' );
			if ( ! $count ) {
				$o_comment_config = $this->_get_comment_config();
				$comment_count    = $o_comment_config->comment_count;
			} else {
				$comment_count = $count;
			}

			// get a very last page if no page exists
			if ( ! $page ) {
				$page = (int) ( ( $o_post->get_comment_count() - 1 ) / $comment_count ) + 1;
			}

			// check if module is using validation system
			$o_comment_controller = \X2board\Includes\get_controller( 'comment' );
			$is_using_validation  = $o_comment_controller->is_using_comment_validation();
			unset( $o_comment_controller );
			$s_where_comment_status = null;
			if ( $is_using_validation ) {
				$s_where_comment_status = '`comments`.`status`=1 AND ';
			}

			global $wpdb;
			$o_query             = new \stdClass();
			$o_query->s_tables   = '`' . $wpdb->prefix . 'x2b_comments` as `comments`, `' . $wpdb->prefix . 'x2b_comments_list` as `comments_list`';
			$o_query->s_columns  = '`comments`.*, `comments_list`.`depth` as `depth` ';
			$o_query->s_where    = "WHERE {$s_where_comment_status} `comments_list`.`parent_post_id` = " . $n_parent_post_id . ' and `comments_list`.`comment_id` = `comments`.`comment_id` and `comments_list`.`head` >= 0 and `comments_list`.`arrange` >= 0 ';
			$o_query->s_orderby  = 'ORDER BY `comments`.`status` desc, `comments_list`.`head` asc, `comments_list`.`arrange` asc';
			$o_query->page       = $page;
			$o_query->list_count = $comment_count;
			$o_query->page_count = $comment_count;
			$output              = \X2board\Includes\get_paginate_select( $o_query );
			// return if an error occurs in the query results
			if ( ! $output->to_bool() ) {
				return $output;
			}

			// insert data into CommentPageList table if the number of results is different from stored comments
			if ( ! $output->data ) {
				// try query without comments.status condition again then decide to fix comment list
				$o_query->s_where = 'WHERE `comments_list`.`parent_post_id` = ' . $n_parent_post_id . ' and `comments_list`.`comment_id` = `comments`.`comment_id` and `comments_list`.`head` >= 0 and `comments_list`.`arrange` >= 0 ';
				$o_rst_check      = \X2board\Includes\get_paginate_select( $o_query );
				if ( ! $o_rst_check->data ) {
					$this->_fix_comment_list( $o_post->get( 'board_id' ), $n_parent_post_id );
					$output = \X2board\Includes\get_paginate_select( $o_query );
					if ( ! $output->to_bool() ) {
						return $output;
					}
				}
				unset( $o_rst_check );
			}
			unset( $o_query );
			return $output;
		}

		/**
		 * Return a configuration of comments for each module
		 * getCommentConfig($module_srl)
		 *
		 * @param int $module_srl
		 * @return object
		 */
		private function _get_comment_config() {
			$o_board_config   = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$o_comment_config = new \stdClass();
			if ( is_object( $o_board_config ) ) {
				$o_comment_config->comment_count          = $o_board_config->comment_count;
				$o_comment_config->comment_use_vote_up    = $o_board_config->comment_use_vote_up;
				$o_comment_config->comment_use_vote_down  = $o_board_config->comment_use_vote_down;
				$o_comment_config->comment_use_validation = $o_board_config->comment_use_validation;
			}
			unset( $o_board_config );

			if ( ! isset( $o_comment_config->comment_count ) ) {
				$o_comment_config->comment_count = 50;
			}
			return $o_comment_config;
		}

		/**
		 * Returns the number of child comments
		 * getChildComments($comment_srl)
		 *
		 * @param int $comment_id
		 * @return int
		 */
		public function get_child_comments( $n_comment_id ) {
			global $wpdb;
			$s_query = 'SELECT `comment_id`, `comment_author` FROM ' . $wpdb->prefix . 'x2b_comments WHERE `parent_comment_id`=' . $n_comment_id;
			if ( $wpdb->query( $s_query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			} else {
				$a_result = $wpdb->get_results( $s_query );
				$wpdb->flush();
			}
			return $a_result;
		}

		/**
		 * Update a list of comments in corresponding with post_id
		 *
		 * @param int $n_board_id
		 * @param int $n_post_id
		 * @return void
		 */
		private function _fix_comment_list( $n_board_id, $n_parent_post_id ) {
			// create a lock file to prevent repeated work when performing a batch job
			$s_lock_folder = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'lock';
			if ( ! file_exists( $s_lock_folder ) ) {
				wp_mkdir_p( $s_lock_folder );
			}

			$s_lock_file = $s_lock_folder . DIRECTORY_SEPARATOR . $n_parent_post_id;
			if ( file_exists( $s_lock_file ) && filemtime( $s_lock_file ) + 36000 < $_SERVER['REQUEST_TIME'] ) {  // 36000 == 60 * 60 * 10
				return;
			}

			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$o_wp_filesystem = new \WP_Filesystem_Direct( false );
			$o_wp_filesystem->put_contents( 
				$s_lock_file, 
				'', 
				(0664 & ~ umask()) // avoid PHP warning - Use of undefined constant FS_CHMOD_FILE
			); // FileHandler::writeFile($s_lock_file, '');

			// get a list
			global $wpdb;
			// SELECT `comment_srl`, `parent_srl`, `regdate` FROM `xe_comments` as `comments` WHERE ( `document_srl` = ? ) and `list_order` <= 2100000000 ORDER BY `list_order` asc
			$s_query = 'SELECT `comment_id`, `parent_comment_id`, `regdate_dt` FROM ' . $wpdb->prefix . 'x2b_comments WHERE `parent_post_id`=' . $n_parent_post_id . ' and `list_order` <= 2100000000 ORDER BY `list_order` asc';
			if ( $wpdb->query( $s_query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			} else {
				$a_source_list = $wpdb->get_results( $s_query );
				$wpdb->flush();
			}

			// Sort comments by the hierarchical structure
			$n_comment_count = count( $a_source_list );

			$o_root         = new \stdClass();
			$a_list         = array();
			$a_comment_list = array();

			// generate a hierarchical structure of comments for loop
			for ( $i = $n_comment_count - 1; $i >= 0; $i-- ) {
				$n_comment_id        = $a_source_list[ $i ]->comment_id;
				$n_parent_comment_id = $a_source_list[ $i ]->parent_comment_id;
				if ( ! $n_comment_id ) {
					continue;
				}

				// generate a list
				$a_list[ $n_comment_id ] = $a_source_list[ $i ];

				if ( $n_parent_comment_id ) {
					$a_list[ $n_parent_comment_id ]->child[] = &$a_list[ $n_comment_id ];
				} else {
					$o_root->child[] = &$a_list[ $n_comment_id ];
				}
			}
			$this->_arrange_comment( $a_comment_list, $o_root->child, 0, null );

			// insert values to the database
			if ( count( $a_comment_list ) ) {
				$o_comment_controller = \X2board\Includes\get_controller( 'comment' );
				foreach ( $a_comment_list as $n_comment_id => $item ) {
					$o_comment_args                 = new \stdClass();
					$o_comment_args->comment_id     = $n_comment_id;
					$o_comment_args->parent_post_id = $n_parent_post_id;
					$o_comment_args->board_id       = $n_board_id;
					$o_comment_args->regdate_dt     = $item->regdate_dt;
					$o_comment_args->arrange        = $item->arrange;
					$o_comment_args->head           = $item->head;
					$o_comment_args->depth          = $item->depth;
					$o_comment_controller->insert_comment_list( $o_comment_args );
					unset( $o_comment_args );
				}
				unset( $o_comment_controller );
			}
			unset( $a_list );
			unset( $a_comment_list );
			unset( $o_root );

			// remove the lock file if successful.
			$o_wp_filesystem->delete( $s_lock_file );
			unset( $o_wp_filesystem );
		}

		/**
		 * Relocate comments in the hierarchical structure
		 *
		 * @param array  $comment_list
		 * @param array  $list
		 * @param int    $depth
		 * @param object $parent
		 * @return void
		 */
		private function _arrange_comment( &$comment_list, $list, $depth, $parent = null ) {
			if ( ! count( $list ) ) {
				return;
			}

			foreach ( $list as $key => $val ) {
				if ( $parent ) {
					$val->head = $parent->head;
				} else {
					$val->head = $val->comment_id;
				}

				$val->arrange = count( $comment_list ) + 1;

				if ( isset( $val->child ) ) {
					$val->depth                       = $depth;
					$comment_list[ $val->comment_id ] = $val;
					$this->_arrange_comment( $comment_list, $val->child, $depth + 1, $val );
					unset( $val->child );
				} else {
					$val->depth                       = $depth;
					$comment_list[ $val->comment_id ] = $val;
				}
			}
		}
	}
}
