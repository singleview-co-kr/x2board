<?php
/*
Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * postItem class
 * post object
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/post
 */
namespace X2board\Includes\Modules\Post;

if ( ! class_exists( '\\X2board\\Includes\\Modules\\Post\\postItem' ) ) {

	require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
	require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';

	class postItem extends \X2board\Includes\Classes\BaseObject {
		/**
		 * post number
		 *
		 * @var int
		 */
		private $_n_wp_post_id = 0;

		/**
		 * column list
		 *
		 * @var array
		 */
		private $_a_columnList = array();

		/**
		 * upload file list
		 *
		 * @var array
		 */
		private $_a_uploaded_file = array();

		/**
		 * memory for WP_Filesystem_Direct
		 *
		 * @var array
		 */
		private $_o_fileSystemDirect = null;

		/**
		 * Constructor
		 *
		 * @param int              $post_id
		 * @param bool             $load_extra_vars
		 * @param array columnList
		 * @return void
		 */
		function __construct( $post_id = 0, $load_extra_vars = true, $columnList = array() ) {
			$this->_n_wp_post_id = $post_id;
			$this->_a_columnList = $columnList;
			$this->_load_from_db( $load_extra_vars );

			if ( ! isset( $_SESSION['x2b_post_management'] ) ) {
				$_SESSION['x2b_post_management'] = array();
			}
		}

		/**
		 *
		 * setAttribute($attribute, $load_extra_vars=true)
		 *
		 * @param
		 * @return void
		 */
		public function set_attr( $attribute, $load_extra_vars = true ) {
			global $G_X2B_CACHE;
			if ( ! isset( $attribute->post_id ) ) {
				$this->_n_wp_post_id = null;
				return;
			}
			$this->_n_wp_post_id = $attribute->post_id;
			$this->adds( $attribute );

			$o_post_model = \X2board\Includes\get_model( 'post' );
			$s_secret_tag = $o_post_model->get_config_status( 'secret' );
			unset( $o_post_model );

			// set is_secret as boolean
			if ( $this->get( 'status' ) == $s_secret_tag ) {
				$this->add( 'is_secret', true );
			} else {
				$this->add( 'is_secret', false );
			}

			// convert is_notice to boolean
			if ( $this->get( 'is_notice' ) == 'Y' ) {
				$this->add( 'is_notice', true );
			} else {
				$this->add( 'is_notice', false );
			}

			// set allow_comment as boolean
			if ( $this->get( 'comment_status' ) == 'ALLOW' ) {
				$this->add( 'allow_comment', true );
			} else {
				$this->add( 'allow_comment', false );
			}

			// Tags
			if ( $this->get( 'tags' ) ) {
				$tag_list = explode( ',', $this->get( 'tags' ) );
				$tag_list = array_map( 'trim', $tag_list );
				$this->add( 'tag_list', $tag_list );
			}

			// append if any extended user field exists
			$o_post_model          = \X2board\Includes\get_model( 'post' );
			$a_extended_user_field = $o_post_model->get_post_user_define_vars_from_DB( array( $this->_n_wp_post_id ) );
			unset( $o_post_model );
			foreach ( $a_extended_user_field as $_ => $o_user_field ) {
				$this->add( $o_user_field->eid, $o_user_field->value );
			}

			if ( $this->get( 'category_id' ) ) {
				$n_category_id = intval( $this->get( 'category_id' ) );
				$n_board_id    = intval( $this->get( 'board_id' ) );

				$o_category_model = \X2board\Includes\get_model( 'category' );
				$s_title          = $o_category_model->get_category_name( $n_board_id, $n_category_id );
				unset( $o_category_model );
			} else {
				$s_title = null;
			}
			$this->add( 'category_title', $s_title );
			if ( $load_extra_vars ) {
				$G_X2B_CACHE['POST_LIST'][ $attribute->post_id ] = $this;
			}
			$G_X2B_CACHE['POST_LIST'][ $this->_n_wp_post_id ] = $this;
		}

		/**
		 *
		 * getCommentCount()
		 *
		 * @param
		 * @return void
		 */
		public function get_comment_count() {
			return $this->get( 'comment_count' );
		}

		/**
		 *
		 * getComments()
		 *
		 * @param
		 * @return void
		 */
		public function get_comments() {
			if ( ! $this->get_comment_count() ) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			if ( ! $this->is_granted() && $this->is_secret() ) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			// cpage is a number of comment pages
			// caution URI key name is [cpage], internally cloned into [%%post_id%%_cpage]
			$cpageStr = sprintf( '%d_cpage', $this->_n_wp_post_id );  // 17_cpage
			$cpage    = \X2board\Includes\Classes\Context::get( $cpageStr );

			if ( ! $cpage ) {
				$cpage = \X2board\Includes\Classes\Context::get( 'cpage' );
			}

			// Get a list of comments
			$o_comment_model = \X2board\Includes\get_model( 'comment' );
			$output          = $o_comment_model->get_comment_list( $this->_n_wp_post_id, $cpage ); // , $is_admin);
			if ( ! $output->to_bool() || ! count( $output->data ) ) {
				// return array to avoid Warning:  Invalid argument supplied for foreach() under any case
				return array();
			}
			// Create commentItem object from a comment list
			// If admin priviledge is granted on parent posts, you can read its child posts.
			$accessible   = array();
			$comment_list = array();
			foreach ( $output->data as $key => $val ) {
				$oCommentItem = new \X2board\Includes\Modules\Comment\commentItem();
				$oCommentItem->set_attr( $val );
				// If permission is granted to the post, you can access it temporarily
				if ( $oCommentItem->is_granted() ) {
					$accessible[ $val->comment_id ] = true;
				}
				// If the comment is set to private and it belongs child post, it is allowable to read the comment for who has a admin privilege on its parent post
				if ( $val->parent_comment_id > 0 && $val->is_secret == 'Y' && ! $oCommentItem->isAccessible() && $accessible[ $val->parent_comment_id ] === true ) {
					$oCommentItem->setAccessible();
				}
				$comment_list[ $val->comment_id ] = $oCommentItem;
			}
			// Variable setting to be displayed on the skin
			//
			// caution URI key name is [cpage], internally cloned into [%%post_id%%_cpage]
			//
			\X2board\Includes\Classes\Context::set( $cpageStr, $output->page_navigation->n_cur_page );
			\X2board\Includes\Classes\Context::set( 'cpage', $output->page_navigation->n_cur_page );
			if ( $output->total_page > 1 ) {
				$this->comment_page_navigation = $output->page_navigation;
			}
			return $comment_list;
		}

		/**
		 *
		 * @param
		 * @return
		 */
		public function is_new() {
			$b_new = false;
			if ( $this->post_id ) {
				$n_expiration_sec = 86400; // 60*60*24
				// pass true in a second parameter to tell it to use the GMT offset.
				if ( $n_expiration_sec > 1 && ( current_time( 'timestamp', true ) - strtotime( $this->regdate_dt ) ) <= $n_expiration_sec ) {
					$b_new = true;
				}
			}
			return $b_new;
		}

		/**
		 * getNickName()
		 *
		 * @param
		 * @return
		 */
		public function get_nick_name() {
			return htmlspecialchars( $this->get( 'nick_name' ), ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
		}

		/**
		 * getRegdate()
		 *
		 * @param
		 * @return
		 */
		public function get_regdate( $format = 'Y.m.d H:i:s' ) {
			$dt_regdate = date_create( $this->get( 'regdate_dt' ) );
			$s_regdate  = date_format( $dt_regdate, $format );
			unset( $dt_regdate );
			return $s_regdate;
		}

		/**
		 * setDocument($post_id, $load_extra_vars = true)
		 *
		 * @param
		 * @return
		 */
		public function set_post( $post_id, $load_extra_vars = true ) {
			$this->_n_wp_post_id = $post_id;
			$this->_load_from_db( $load_extra_vars );
		}

		/**
		 * _loadFromDB($load_extra_vars = true)
		 * Get data from database, and set the value to postItem object
		 *
		 * @param bool $load_extra_vars should be false not to reset $this->_n_wp_post_id for writing a new post case
		 * @return void
		 */
		private function _load_from_db( $load_extra_vars = true ) {
			if ( ! $this->_n_wp_post_id || ! $load_extra_vars ) {
				return;
			}
			global $wpdb;
			$o_post = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}x2b_posts` WHERE `post_id`={$this->_n_wp_post_id}" );
			$this->set_attr( $o_post, $load_extra_vars );
		}

		/**
		 * isExists()
		 *
		 * @param
		 * @return
		 */
		public function is_exists() {
			return $this->_n_wp_post_id ? true : false;
		}

		/**
		 * isGranted()
		 *
		 * @param
		 * @return
		 */
		public function is_granted() {
			if ( isset( $_SESSION['x2b_own_post'][ $this->_n_wp_post_id ] ) ) {
				return $this->grant_cache = true;
			}
			if ( $this->grant_cache !== null ) {
				return $this->grant_cache;
			}

			if ( ! \X2board\Includes\Classes\Context::get( 'is_logged' ) ) {
				return $this->grant_cache = false;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				unset( $o_logged_info );
				return $this->grant_cache = true;
			}
			unset( $o_logged_info );
			return $this->grant_cache = false;
		}

		/**
		 * setGrant()
		 *
		 * @param
		 * @return
		 */
		public function set_grant() {
			$_SESSION['x2b_own_post'][ $this->_n_wp_post_id ] = true;
			$this->grant_cache                                = true;
		}

		/**
		 * getStatus()
		 *
		 * @param
		 * @return
		 */
		public function get_status() {
			$s_cur_post_status = $this->get( 'status' );
			if ( ! $s_cur_post_status ) {
				$o_post_class     = \X2board\Includes\get_class( 'post' );
				$s_default_status = $o_post_class->get_default_status();
				unset( $o_post_class );
				return $s_default_status;
			}
			return $s_cur_post_status;
		}

		/**
		 * isNotice()
		 *
		 * @param
		 * @return
		 */
		public function is_notice() {
			return $this->get( 'is_notice' ) == 'Y' ? true : false;
		}

		/**
		 * isSecret()
		 *
		 * @param
		 * @return
		 */
		public function is_secret() {
			$o_post_model = \X2board\Includes\get_model( 'post' );
			$s_secret_tag = $o_post_model->get_config_status( 'secret' );
			unset( $o_post_model );
			return $this->get( 'status' ) == $s_secret_tag ? true : false;
		}

		/**
		 * Update readed count
		 *
		 * @return void
		 */
		public function update_readed_count() {
			$o_post_controller = \X2board\Includes\get_controller( 'post' );
			if ( $o_post_controller->update_readed_count( $this ) ) {
				$readed_count = $this->get( 'readed_count' );
				$this->add( 'readed_count', $readed_count + 1 );
			}
		}

		/**
		 * @param
		 * @return
		 */
		public function get_title( $cut_size = 0, $tail = '...' ) {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}

			$title = $this->get_title_text( $cut_size, $tail );

			$attrs = array();
			$this->add( 'title_color', trim( $this->get( 'title_color' ) ) );
			if ( $this->get( 'title_bold' ) == 'Y' ) {
				$attrs[] = 'font-weight:bold;';
			}
			if ( $this->get( 'title_color' ) && $this->get( 'title_color' ) != 'N' ) {
				$attrs[] = 'color:#' . $this->get( 'title_color' );
			}

			if ( count( $attrs ) ) {
				$s_title = sprintf( '<span style="%s">%s</span>', implode( ';', $attrs ), htmlspecialchars( $title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false ) );
			} else {
				$s_title = htmlspecialchars( $title, ENT_COMPAT | ENT_HTML401, 'UTF-8', false );
			}
			unset( $attrs );
			return esc_attr( wp_strip_all_tags( $s_title ) );
		}

		/**
		 * getTitleText()
		 *
		 * @param
		 * @return
		 */
		public function get_title_text( $cut_size = 0, $tail = '...' ) {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}
			if ( $cut_size ) {
				$title = \X2board\Includes\cut_str( $this->get( 'title' ), $cut_size, $tail );
			} else {
				$title = $this->get( 'title' );
			}
			return $title;
		}

		/**
		 * getIpaddress()
		 *
		 * @param
		 * @return
		 */
		public function get_ip_addr() {
			if ( $this->is_granted() ) {
				return $this->get( 'ipaddress' );
			}
			return '*' . strstr( $this->get( 'ipaddress' ), '.' );
		}

		/**
		 * getContent()
		 *
		 * @param
		 * @return
		 */
		public function get_content( $add_popup_menu = false, $add_content_info = false, $resource_realpath = false, $add_xe_content_class = false, $stripEmbedTagException = false ) {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}

			if ( $this->is_secret() && ! $this->is_granted() && ! $this->is_accessible() ) {
				return __( 'msg_secret_post', X2B_DOMAIN );  // Context::getLang('msg_is_secret');
			}

			$result = $this->_check_accessible_from_status();
			if ( $result ) {
				$_SESSION['accessible'][ $this->_n_wp_post_id ] = true;
			}

			$s_content = $this->get( 'content' );
			if ( ! $stripEmbedTagException ) {
				\X2board\Includes\strip_embed_tag_for_admin( $s_content, $this->get( 'post_author' ) );
			}

			$n_post_author_id = $this->get( 'post_author' );
			if ( $n_post_author_id < 0 ) {
				$n_post_author_id = 0;
			}
			$s_content = sprintf(
				'<!--BeforePost(%d,%d)--><div class="post_%d_%d x2b_content">%s</div><!--AfterPost(%d,%d)-->',
				$this->_n_wp_post_id,
				$n_post_author_id,
				$this->_n_wp_post_id,
				$n_post_author_id,
				$s_content,
				$this->_n_wp_post_id,
				$n_post_author_id,
				$this->_n_wp_post_id,
				$n_post_author_id
			);
			// Add x2b_content class although accessing content is not required

			// Change the image path to a valid absolute path if resource_realpath is true
			if ( $resource_realpath ) {
				$s_content = preg_replace_callback( '/<img([^>]+)>/i', array( $this, 'replaceResourceRealPath' ), $s_content );
			}
			return $s_content;
		}

		/**
		 * isAccessible()
		 *
		 * @param
		 * @return
		 */
		public function is_accessible() {
			return $_SESSION['accessible'][ $this->_n_wp_post_id ] == true ? true : false;
		}

		/**
		 * Check accessible by document status
		 * _checkAccessibleFromStatus()
		 *
		 * @param array $matches
		 * @return mixed
		 */
		private function _check_accessible_from_status() {
			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' ) {
				return true;
			}

			$status = $this->get( 'status' );
			if ( empty( $status ) ) {
				return false;
			}

			$o_post_model     = \X2board\Includes\get_model( 'post' );
			$configStatusList = $o_post_model->get_status_list();
			if ( $status == $configStatusList['public'] ) {  // || $status == $configStatusList['publish']) {
				return true;
			} elseif ( $status == $configStatusList['secret'] ) {  // $status == $configStatusList['private'] ||
				if ( $this->get( 'post_author' ) == $o_logged_info->ID ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * allowComment()
		 *
		 * @param
		 * @return
		 */
		public function allow_comment() {
			// if post is not exists. so allow comment status is true ??? 뭔소리?
			if ( ! $this->is_exists() ) {
				return true;
			}
			return $this->get( 'comment_status' ) == 'ALLOW' ? true : false;
		}

		/**
		 * Check whether to have a permission to write comment
		 * isEnableComment()
		 * Authority to write a comment and to write a post is separated
		 *
		 * @return bool
		 */
		public function is_enable_comment() {
			$o_module_info                          = \X2board\Includes\Classes\Context::get( 'current_module_info' );
			$n_forbid_comment_old_post_days         = intval( $o_module_info->comment_forbid_to_leave_comment_old_post_days );
			$b_allow_comment_for_admin_for_old_post = $o_module_info->allow_comment_for_admin_for_old_post == 'Y' ? true : false;
			unset( $o_module_info );

			$o_logged_info = \X2board\Includes\Classes\Context::get( 'logged_info' );
			if ( $o_logged_info->is_admin == 'Y' && $b_allow_comment_for_admin_for_old_post ) { // allow admin to write comment
				$b_check_comment_privilege = false;
			} else {
				$b_check_comment_privilege = true;
			}
			unset( $o_logged_info );

			if ( $b_check_comment_privilege && $n_forbid_comment_old_post_days > 0 ) {
				$dt_target   = date_create( $this->get( 'regdate_dt' ) );
				$dt_start    = new \DateTime( date( 'Y-m-d' ) );
				$dt_interval = date_diff( $dt_start, $dt_target );
				unset( $dt_start );
				unset( $dt_target );
				if ( $dt_interval->days > $n_forbid_comment_old_post_days ) {
					return false;
				}
			}

			// Return false if not authorized, if a secret post, if the post is set not to allow any comment
			if ( ! $this->allow_comment() ) {
				return false;
			}
			if ( ! $this->is_granted() && $this->is_secret() ) {
				return false;
			}
			return true;
		}

		/**
		 * 게시글에 표시할 첨부파일을 반환한다.
		 * getUploadedFiles($sortIndex = 'file_srl')
		 *
		 * @return object
		 */
		public function get_uploaded_files( $sortIndex = 'file_id' ) {
			if ( ! $this->_n_wp_post_id ) { // if write new post
				return array();
			}
			if ( $this->is_secret() && ! $this->is_granted() ) {
				return array();
			}
			if ( ! $this->get( 'uploaded_count' ) ) {
				return array();
			}
			if ( ! isset( $this->_a_uploaded_file[ $sortIndex ] ) ) {
				$o_file_model                         = \X2board\Includes\get_model( 'file' );
				$this->_a_uploaded_file[ $sortIndex ] = $o_file_model->get_files( $this->_n_wp_post_id, $sortIndex, true );  // array(),
				unset( $o_file_model );
			}
			return $this->_a_uploaded_file[ $sortIndex ];
		}

		/**
		 * for post.php skin usage
		 * isExtraVarsExists()
		 *
		 * @return object
		 */
		public function is_user_define_extended_vars_exists() {
			if ( ! $this->get( 'board_id' ) ) {
				return false;
			}
			$a_user_define_extended_field = $this->get_user_define_extended_fields();
			return count( $a_user_define_extended_field ) ? true : false;
		}

		/**
		 * for post.php skin usage
		 * differ with \includes\modules\post\post.model.php::get_user_define_extended_fields()
		 * this method returns list of the designated post
		 * getExtraVars()
		 *
		 * @return object
		 */
		public function get_user_define_extended_fields() {
			if ( ! $this->get( 'board_id' ) || ! $this->_n_wp_post_id ) {
				return null;
			}
			$o_post_model        = \X2board\Includes\get_model( 'post' );
			$inserted_extra_vars = $o_post_model->get_user_define_vars( $this->_n_wp_post_id );
			unset( $o_post_model );

			$o_post_user_define_fields = new \X2board\Includes\Classes\GuestUserDefineFields();
			$a_default_fields          = $o_post_user_define_fields->get_default_fields();
			unset( $o_post_user_define_fields );

			$a_ignore_field_type = array_keys( $a_default_fields );
			unset( $a_default_fields );
			$a_user_define_extended_fields = array();
			foreach ( $inserted_extra_vars as $s_eid => $o_field ) {				
				$field_type = ( isset( $o_field->type ) && $o_field->type ) ? $o_field->type : '';
				if ( in_array( $field_type, $a_ignore_field_type ) ) { // ignore default fields
					continue;
				}
				if ( is_null( $o_field->value ) ) {
					continue;
				}
				$a_user_define_extended_fields[ $o_field->idx ] = $o_field;
			}
			unset( $inserted_extra_vars );
			unset( $a_ignore_field_type );
			return $a_user_define_extended_fields;
		}

		/**
		 * Return the value obtained from getExtraImages with image tag
		 * printExtraImages($time_check = 43200)
		 *
		 * @param int $time_check
		 * @return string
		 */
		public function print_extra_images( $time_check = 43200 ) {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}
			$buffs = $this->_get_extra_images( $time_check );
			if ( ! count( $buffs ) ) {
				return;
			}

			$s_path = sprintf( '%s%s', X2B_URL, 'includes/' . X2B_MODULES_NAME . '/post/tpl/icons/' );
			$buff   = array();
			foreach ( $buffs as $key => $val ) {
				$buff[] = sprintf( '<img src="%s%s.gif" alt="%s" title="%s" style="margin-right:2px;" />', $s_path, $val, $val, $val );
			}
			return implode( '', $buff );
		}

		/**
		 * Functions to display icons for new post, latest update, secret(private) post, image/video/attachment
		 * Determine new post and latest update by $time_interval
		 * getExtraImages($time_interval = 43200)
		 *
		 * @param int $time_interval
		 * @return array
		 */
		private function _get_extra_images( $time_interval = 43200 ) {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}
			// variables for icon list
			$buffs = array();

			$check_files = false;

			// Check if secret post is
			if ( $this->is_secret() ) {
				$buffs[] = 'secret';
			}

			// Set the latest time
			$time_check = date( 'YmdHis', $_SERVER['REQUEST_TIME'] - $time_interval );

			// Check new post
			if ( $this->get( 'regdate_dt' ) > $time_check ) {
				$buffs[] = 'new';
			} elseif ( $this->get( 'last_update_dt' ) > $time_check ) {
				$buffs[] = 'update';
			}

			// Check the attachment
			if ( $this->has_uploaded_files() ) {
				$buffs[] = 'file';
			}

			return $buffs;
		}

		/**
		 * hasUploadedFiles()
		 *
		 * @param
		 * @return
		 */
		public function has_uploaded_files() {
			if ( ! $this->_n_wp_post_id ) {
				return;
			}
			if ( $this->is_secret() && ! $this->is_granted() ) {
				return false;
			}
			return $this->get( 'uploaded_count' ) ? true : false;
		}

		/**
		 * isCarted()
		 *
		 * @param
		 * @return
		 */
		public function is_carted() {
			if ( isset( $_SESSION['x2b_post_management'][ $this->_n_wp_post_id ] ) ) {
				return $_SESSION['x2b_post_management'][ $this->_n_wp_post_id ];
			}
			return false;
		}

		/**
		 * addCart()
		 *
		 * @param
		 * @return
		 */
		public function add_cart() {
			$_SESSION['x2b_post_management'][ $this->_n_wp_post_id ] = true;
		}

		/**
		 * removeCart()
		 *
		 * @param
		 * @return
		 */
		public function remove_cart() {
			unset( $_SESSION['x2b_post_management'][ $this->_n_wp_post_id ] );
		}

		/**
		 * isEditable()
		 *
		 * @param
		 * @return
		 */
		public function is_editable() {
			if ( $this->is_granted() || ! $this->get( 'post_author' ) ) {
				return true;
			}
			return false;
		}

		/**
		 * getSummary($str_size = 50, $tail = '...')
		 *
		 * @param
		 * @return
		 */
		public function get_summary( $str_size = 50, $tail = '...' ) {
			$content = $this->get_content( false, false );

			$content = nl2br( $content );

			// For a newlink, inert a whitespace
			$content = preg_replace( '!(<br[\s]*/{0,1}>[\s]*)+!is', ' ', $content );

			// Replace tags such as </p> , </div> , </li> and others to a whitespace
			$content = str_replace( array( '</p>', '</div>', '</li>', '-->' ), ' ', $content );

			// Remove Tags
			$content = preg_replace( '!<([^>]*?)>!is', '', $content );

			// Replace < , >, "
			$content = str_replace( array( '&lt;', '&gt;', '&quot;', '&nbsp;' ), array( '<', '>', '"', ' ' ), $content );

			// Delete  a series of whitespaces
			$content = preg_replace( '/ ( +)/is', ' ', $content );

			// Truncate string
			$content = trim( \X2board\Includes\cut_str( $content, $str_size, $tail ) );

			// Replace back < , <, "
			$content = str_replace( array( '<', '>', '"' ), array( '&lt;', '&gt;', '&quot;' ), $content );

			return $content;
		}

		/**
		 * getPermanentUrl()
		 *
		 * @param
		 * @return
		 */
		public function get_permanent_url() {
			return x2b_get_url( 'cmd', X2B_CMD_VIEW_POST, 'post_id', $this->_n_wp_post_id );
		}

		/**
		 * Return author's profile image
		 * getProfileImage()
		 *
		 * @return string
		 */
		public function get_profile_image() {
			if ( ! $this->is_exists() || ! $this->get( 'post_author' ) ) {
				return;
			}
			return get_avatar( $this->get( 'post_author' ), 32 );
		}

		/**
		 * getExtraEidValue()
		 *
		 * @param
		 * @return
		 */
		public function get_user_define_eid_value( $eid ) {
			$extra_vars = $this->get_user_define_extended_fields();
			if ( $extra_vars ) {
				// Handle extra variable(eid)
				foreach ( $extra_vars as $idx => $key ) {
					$extra_eid[ $key->eid ] = $key;
				}
			}
			if ( isset( $extra_eid ) ) {
				if ( is_array( $extra_eid ) && array_key_exists( $eid, $extra_eid ) ) {
					return $extra_eid[ $eid ]->getValue();
				}
			}
			return '';
		}

		/**
		 * getExtraValueHTML($idx)
		 *
		 * @param
		 * @return
		 */
		public function get_user_define_value_HTML( $s_eid ) {
			$extra_vars = $this->get_user_define_extended_fields();
			if ( is_array( $extra_vars ) && array_key_exists( $s_eid, $extra_vars ) ) {
				return $extra_vars[ $s_eid ]->getValueHTML();
			} else {
				return '';
			}
		}

		/**
		 * thumbnailExists($width = 80, $height = 0, $type = '')
		 *
		 * @param
		 * @return
		 */
		public function check_thumbnail( $width = 80, $height = 0, $type = '' ) {
			if ( ! $this->_n_wp_post_id ) {
				return false;
			}
			if ( ! $this->get_thumbnail( $width, $height, $type ) ) {
				return false;
			}
			return true;
		}

		/**
		 * getThumbnail($width = 80, $height = 0, $thumbnail_type = '')
		 *
		 * @param
		 * @return
		 */
		public function get_thumbnail( $width = 80, $height = 0, $thumbnail_type = '' ) {
			// Return false if the post doesn't exist
			if ( ! $this->_n_wp_post_id ) {
				return false;
			}

			if ( $this->is_secret() && ! $this->is_granted() ) {
				return false;
			}

			// If not specify its height, create a square
			if ( ! $height ) {
				$height = $width;
			}

			// Return false if neither attachement nor image files in the post
			if ( ! $this->get( 'uploaded_count' ) ) {
				$content = $this->get( 'content' );
				if ( ! $content ) {
					global $wpdb;
					$o_row = $wpdb->get_row( "SELECT `content` FROM `{$wpdb->prefix}x2b_posts` WHERE `post_id`={$this->_n_wp_post_id}" );
					if ( $o_row->content ) {
						$content = $o_row->content;
						$this->add( 'content', $o_row->content );
					}
					unset( $o_row );
				}
				if ( ! preg_match( '!<img!is', $content ) ) {
					return false;
				}
			}

			// Get thumbnai_type information from post module's configuration
			if ( ! in_array( $thumbnail_type, array( 'crop', 'ratio' ) ) ) {
				$o_module_info  = \X2board\Includes\Classes\Context::get( 'current_module_info' );
				$thumbnail_type = $o_module_info->thumbnail_type;
				unset( $o_module_info );
			}

			// Define thumbnail information
			$n_board_id         = \X2board\Includes\Classes\Context::get( 'board_id' );
			$s_rand_dir         = \X2board\Includes\get_numbering_path( $this->_n_wp_post_id, 3 );
			$thumbnail_path     = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . X2B_DOMAIN . DIRECTORY_SEPARATOR . 'thumbnails' .
								DIRECTORY_SEPARATOR . $n_board_id . DIRECTORY_SEPARATOR . $s_rand_dir;
			$thumbnail_file     = sprintf( '%s%dx%d.%s.jpg', $thumbnail_path, $width, $height, $thumbnail_type );
			$thumbnail_lockfile = sprintf( '%s%dx%d.%s.lock', $thumbnail_path, $width, $height, $thumbnail_type );

			$thumbnail_url = wp_get_upload_dir()['baseurl'] . '/' . X2B_DOMAIN . '/thumbnails/' . $n_board_id . '/' . $s_rand_dir;
			$thumbnail_url = sprintf( '%s%dx%d.%s.jpg', $thumbnail_url, $width, $height, $thumbnail_type );

			// Return false if thumbnail file exists and its size is 0. Otherwise, return its path
			if ( file_exists( $thumbnail_file ) || file_exists( $thumbnail_lockfile ) ) {
				if ( filesize( $thumbnail_file ) < 1 ) {
					return false;
				} else {
					return $thumbnail_url . '?' . date( 'YmdHis', filemtime( $thumbnail_file ) );
				}
			}

			if ( ! file_exists( $thumbnail_path ) ) {
				if ( ! wp_mkdir_p( $thumbnail_path ) ) {
					return false;
				}
			}

			// Create lockfile to prevent race condition
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wp-filesystem-direct.php';
			$wp_filesystem = new \WP_Filesystem_Direct( false );

			$wp_filesystem->put_contents(
				$thumbnail_lockfile,
				'',
				FS_CHMOD_FILE // predefined mode settings for WP files
			);

			// Target File
			$source_file = null;
			$is_tmp_file = false;

			// Find an iamge file among attached files if exists
			if ( $this->has_uploaded_files() ) {
				$file_list   = $this->get_uploaded_files();
				$first_image = null;
				foreach ( $file_list as $file ) {
					if ( $file->direct_download !== 'Y' ) {
						continue;
					}

					if ( $file->cover_image === 'Y' && file_exists( $file->uploaded_filename ) ) {
						$source_file = $file->uploaded_filename;
						break;
					}

					if ( $first_image ) {
						continue;
					}

					if ( preg_match( '/\.(jpe?g|png|gif|bmp)$/i', $file->source_filename ) ) {
						if ( file_exists( $file->uploaded_filename ) ) {
							$first_image = $file->uploaded_filename;
						}
					}
				}

				if ( ! $source_file && $first_image ) {
					$source_file = $first_image;
				}
			}
			// If not exists, file an image file from the content
			$is_tmp_file = false;
			if ( ! $source_file ) {
				$random = new \X2board\Includes\Classes\Security\Password();
				$content = $this->get( 'content' );
				preg_match_all( "!<img[^>]*src=(?:\"|\')([^\"\']*?)(?:\"|\')!is", $content, $matches, PREG_SET_ORDER );

				foreach ( $matches as $target_image ) {
					$target_src = trim( $target_image[1] );
					// if(preg_match('/\/(common|modules|widgets|addons|layouts|m\.layouts)\//i', $target_src)) continue;

					if ( ! preg_match( '/^(http|https):\/\//i', $target_src ) ) {
						$target_src = Context::getRequestUri() . $target_src;
					}

					$target_src = htmlspecialchars_decode( $target_src );

					$tmp_file = _XE_PATH_ . 'files/cache/tmp/' . $random->createSecureSalt( 32, 'hex' );
					FileHandler::getRemoteFile( $target_src, $tmp_file );
					if ( ! file_exists( $tmp_file ) ) {
						continue;
					}

					$imageinfo     = getimagesize( $tmp_file );
					list($_w, $_h) = $imageinfo;
					if ( $imageinfo === false || ( $_w < ( $width * 0.3 ) && $_h < ( $height * 0.3 ) ) ) {
						FileHandler::removeFile( $tmp_file );
						continue;
					}

					$source_file = $tmp_file;
					$is_tmp_file = true;
					break;
				}
				unset($random);
			}

			$output_file = null;
			if ( $source_file ) {
				$output_file = \X2board\Includes\Classes\FileHandler::create_image_file( $source_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type );
			}

			// Remove source file if it was temporary
			if ( $is_tmp_file ) {
				FileHandler::removeFile( $source_file );
			}

			if ( is_null( $this->_o_fileSystemDirect ) ) {
				$this->_o_fileSystemDirect = new \WP_Filesystem_Direct( false );
			}
			$this->_o_fileSystemDirect->delete( $thumbnail_lockfile );

			// Create an empty file if thumbnail generation failed
			if ( ! $output_file ) {
				$wp_filesystem->put_contents(
					$thumbnail_file,
					'',
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
			unset($wp_filesystem);
			return $thumbnail_url . '?' . date( 'YmdHis', filemtime( $thumbnail_file ) );
		}
	}
}
