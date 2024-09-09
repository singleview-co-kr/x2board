<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * commentItem class
 * comment BaseObject
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/comment
 * @version 0.1
 */
namespace X2board\Includes\Modules\Comment;

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Comment\\commentItem' ) ) {

	class commentItem extends \X2board\Includes\Classes\BaseObject {
		/**
		 * comment number
		 *
		 * @var int
		 */
		var $comment_id = 0;

		/**
		 * Get the column list int the table
		 *
		 * @var array
		 */
		var $columnList = array();

		// ensure PHP 8.2 compatibility
		public $board_id = null;
		public $parent_post_id = null;
		public $parent_comment_id = null;
		public $wp_comment_id = null;
		public $is_secret = null;
		public $is_granted = null;
		public $content = null;
		public $voted_count = null;
		public $blamed_count = null;
		public $password = null;
		public $nick_name = null;
		public $comment_author = null;
		public $email_address = null;
		public $uploaded_count = null;
		public $ipaddress = null;
		public $list_order = null;
		public $status = null;
		public $ua = null;
		public $regdate_dt = null;
		public $last_update_dt = null;
		public $depth = null;

		/**
		 * Constructor
		 *
		 * @param int   $comment_id
		 * @param array $columnList
		 * @return void
		 */
		public function __construct( $comment_id = 0, $columnList = array() ) {
			$this->comment_id = $comment_id;
			$this->columnList = $columnList;
			$this->_load_from_db();
		}

		/**
		 * Load comment data from DB and set to commentItem object
		 * _loadFromDB()
		 *
		 * @return void
		 */
		private function _load_from_db() {
			if ( ! $this->comment_id ) {
				return;
			}
			global $wpdb;
			$s_query = 'SELECT * FROM ' . $wpdb->prefix . 'x2b_comments WHERE `comment_id`=' . $this->comment_id;
			if ( $wpdb->query( $s_query ) === false ) {
				return new \X2board\Includes\Classes\BaseObject( -1, $wpdb->last_error );
			} else {
				$a_result = $wpdb->get_results( $s_query );
				$wpdb->flush();
			}
			if ( count( (array) $a_result ) ) {
				$this->set_attr( $a_result[0] );
			} else {
				$this->comment_id = null;
			}
		}

		/**
		 * Comment attribute set to BaseObject object
		 * setAttribute($attribute)
		 *
		 * @return void
		 */
		public function set_attr( $attribute ) {
			if ( ! $attribute->comment_id ) {
				$this->comment_id = null;
				return;
			}

			$this->comment_id = $attribute->comment_id;
			$this->adds( $attribute );

			// define vars on the object for backward compatibility of skins
			// if ( count( (array) $attribute ) ) {
			// 	foreach ( $attribute as $key => $val ) {
			// 		$this->{$key} = $val;
			// 	}
			// }
		}

		/**
		 * Comment attribute set to BaseObject object
		 * isGranted()
		 *
		 * @return void
		 */
		public function is_granted() {
			if ( isset( $_SESSION['x2b_own_comment'][ $this->comment_id ] ) ) {
				return true;
			}

			if ( ! \X2board\Includes\Classes\Context::get( 'is_logged' ) ) {
				return false;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				return true;
			}

			$o_grant = \X2board\Includes\Classes\Context::get( 'grant' );
			if ( $o_grant->manager ) {
				return true;
			}

			if ( $this->get( 'comment_author' ) && ( $this->get( 'comment_author' ) == $o_logged_info->ID || $this->get( 'comment_author' ) * -1 == $o_logged_info->ID ) ) {
				return true;
			}
			return false;
		}

		/**
		 * setAccessible()
		 *
		 * @return
		 */
		public function set_accessible() {
			$_SESSION['x2b_accessibled_comment'][ $this->comment_id ] = true;
		}

		/**
		 * getNickName()
		 *
		 * @return
		 */
		public function get_nick_name() {
			$s_nick_name = strlen( (string)$this->get( 'nick_name' ) ) > 0 ? $this->get( 'nick_name' ) : __( 'lbl_no_name', X2B_DOMAIN );
			return \X2board\Includes\escape( $s_nick_name, false );
		}

		/**
		 * isAccessible()
		 *
		 * @return
		 */
		public function is_accessible() {
			if ( isset( $_SESSION['x2b_accessibled_comment'][ $this->comment_id ] ) ) {
				return true;
			}

			if ( $this->is_granted() || ! $this->is_secret() ) {
				$this->set_accessible();
				return true;
			}

			$o_post_model = \X2board\Includes\get_model( 'post' );
			$o_post       = $o_post_model->get_post( $this->get( 'post_id' ) );
			unset( $o_post_model );
			if ( $o_post->is_granted() ) {
				$this->set_accessible();
				unset( $o_post );
				return true;
			}
			unset( $o_post );
			return false;
		}

		/**
		 * isSecret()
		 *
		 * @return
		 */
		public function is_secret() {
			return $this->get( 'is_secret' ) == 'Y' ? true : false;
		}

		/**
		 * setGrant()
		 *
		 * @return
		 */
		public function set_grant() {
			$_SESSION['x2b_own_comment'][ $this->comment_id ] = true;
			$this->is_granted                                 = true;
		}

		/**
		 * isExists()
		 *
		 * @return
		 */
		public function is_exists() {
			return $this->comment_id ? true : false;
		}

		/**
		 * getRegdate($format = 'Y.m.d H:i:s')
		 *
		 * @return
		 */
		public function get_regdate( $format = 'Y.m.d H:i:s' ) {
			$dt_regdate = date_create( $this->get( 'regdate_dt' ) );
			$s_regdate  = date_format( $dt_regdate, $format );
			unset( $dt_regdate );
			return $s_regdate; // zdate($this->get('regdate'), $format);
		}

		/**
		 * getIpAddress()
		 *
		 * @return
		 */
		public function get_ip_addr() {
			if ( $this->is_granted() ) {
				return $this->get( 'ipaddress' );
			}
			return '*' . strstr( $this->get( 'ipaddress' ), '.' );
		}

		/**
		 * Return content after filter
		 * getContent($add_popup_menu = TRUE, $add_content_info = TRUE, $add_xe_content_class = TRUE)
		 *
		 * @return string
		 */
		public function get_content() {
			if ( $this->is_secret() && ! $this->is_accessible() ) {
				return __( 'msg_secret_post', X2B_DOMAIN );
			}
			$s_content = $this->get( 'content' );
			\X2board\Includes\strip_embed_tag_for_admin( $s_content, $this->get( 'comment_author' ) );

			// if additional information which can access contents is set
			$n_comment_author_id = $this->get( 'comment_author' );
			if ( $n_comment_author_id < 0 ) {
				$n_comment_author_id = 0;
			}
			$s_content = sprintf(
				'<!--BeforeComment(%d,%d)--><div class="comment_%d_%d x2b_content">%s</div><!--AfterComment(%d,%d)-->',
				$this->comment_id,
				$n_comment_author_id,
				$this->comment_id,
				$n_comment_author_id,
				$s_content,
				$this->comment_id,
				$n_comment_author_id
			);
			// x2b_content class name should be specified although content access is not necessary.
			return $s_content;
		}

		/**
		 * function hasUploadedFiles()
		 *
		 * @return
		 */
		public function has_uploaded_files() {
			if ( ( $this->is_secret() && ! $this->is_accessible() ) && ! $this->is_granted() ) {
				return false;
			}
			return $this->get( 'uploaded_count' ) ? true : false;
		}

		/**
		 * 댓글에 표시할 첨부파일을 반환한다.
		 * getUploadedFiles()
		 *
		 * @return object
		 */
		public function get_uploaded_files() {
			if ( ( $this->is_secret() && ! $this->is_accessible() ) && ! $this->is_granted() ) {
				return array();
			}

			if ( ! $this->get( 'uploaded_count' ) ) {
				return array();
			}

			$o_file_model = \X2board\Includes\get_model( 'file' );
			$file_list    = $o_file_model->get_files( $this->comment_id, 'file_id', true );
			unset( $o_file_model );
			return $file_list;
		}
	}
}
