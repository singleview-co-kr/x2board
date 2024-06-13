<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
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

if (!class_exists('\\X2board\\Includes\\Modules\\Comment\\commentItem')) {

	class commentItem extends \X2board\Includes\Classes\BaseObject
	{

		/**
		 * comment number
		 * @var int
		 */
		var $comment_id = 0;

		/**
		 * Get the column list int the table
		 * @var array
		 */
		var $columnList = array();

		/**
		 * Constructor
		 * @param int $comment_id
		 * @param array $columnList
		 * @return void
		 */
		public function __construct($comment_id = 0, $columnList = array())
		{
			$this->comment_id = $comment_id;
			$this->columnList = $columnList;
			$this->_load_from_db();
		}

		/**
		 * Load comment data from DB and set to commentItem object
		 * @return void
		 */
		// function _loadFromDB() {
		private function _load_from_db() {
			if(!$this->comment_id) {
				return;
			}
			// $args = new \stdClass();
			// $args->comment_id = $this->comment_id;
			// $output = executeQuery('comment.getComment', $args, $this->columnList);
			global $wpdb;
			$s_query = "SELECT * FROM ".$wpdb->prefix."x2b_comments WHERE `comment_id`=".$this->comment_id;
			if ($wpdb->query($s_query) === FALSE) {
				return new \X2board\Includes\Classes\BaseObject(-1, $wpdb->last_error);
			} 
			else {
				$a_result = $wpdb->get_results($s_query);
				$wpdb->flush();
			}
			if( count((array)$a_result) ){
				$this->set_attr($a_result[0]);
			}
		}

		/**
		 * Comment attribute set to BaseObject object
		 * @return void
		 */
		// function setAttribute($attribute)
		public function set_attr($attribute) {
			if(!$attribute->comment_id) {
				$this->comment_id = NULL;
				return;
			}

			$this->comment_id = $attribute->comment_id;
			$this->adds($attribute);

			// define vars on the object for backward compatibility of skins
			if(count((array)$attribute)) {
				foreach($attribute as $key => $val) {
					$this->{$key} = $val;
				}
			}
		}

		// function isGranted()
		public function is_granted() {
			if(isset($_SESSION['x2b_own_comment'][$this->comment_id])) {
				return TRUE;
			}

			if(!\X2board\Includes\Classes\Context::get('is_logged'))	{
				return FALSE;
			}

			$o_logged_info = \X2board\Includes\Classes\Context::get('logged_info');
			if($o_logged_info->is_admin == 'Y') {
				return TRUE;
			}

			$o_grant = \X2board\Includes\Classes\Context::get('grant');
			if($o_grant->manager) {
				return TRUE;
			}

			if($this->get('comment_author') && ($this->get('comment_author') == $o_logged_info->ID || $this->get('comment_author') * -1 == $o_logged_info->ID)) {
				return TRUE;
			}
			return FALSE;
		}

		// function setAccessible()
		public function set_accessible() {
			$_SESSION['x2b_accessibled_comment'][$this->comment_id] = TRUE;
		}

		// function getNickName()
		public function get_nick_name()	{
			$s_nick_name = strlen($this->get('nick_name')) > 0 ? $this->get('nick_name') : __('lbl_no_name', X2B_DOMAIN);
			return \X2board\Includes\escape($s_nick_name, false);
		}

		// function isAccessible()
		public function is_accessible()	{
			if(isset($_SESSION['x2b_accessibled_comment'][$this->comment_id])) {
				return TRUE;
			}

			if($this->is_granted() || !$this->is_secret()) {
				$this->set_accessible();
				return TRUE;
			}

			$o_post_model = \X2board\Includes\getModel('post');
			$o_post = $o_post_model->get_post($this->get('post_id'));
			unset($o_post_model);
			if($o_post->is_granted()) {
				$this->set_accessible();
				unset($o_post);
				return TRUE;
			}
			unset($o_post);
			return FALSE;
		}

		// function isSecret()
		public function is_secret() {
			return $this->get('is_secret') == 'Y' ? TRUE : FALSE;
		}

		// function setGrant()
		public function set_grant() {
			$_SESSION['x2b_own_comment'][$this->comment_id] = TRUE;
			$this->is_granted = TRUE;
		}

		// function isExists()
		public function is_exists() {
			return $this->comment_id ? TRUE : FALSE;
		}

		// function getRegdate($format = 'Y.m.d H:i:s')
		public function get_regdate($format = 'Y.m.d H:i:s') {
			$dt_regdate = date_create($this->get('regdate_dt'));
			$s_regdate = date_format($dt_regdate, $format);
			unset($dt_regdate);
			return $s_regdate;
			// return zdate($this->get('regdate'), $format);
		}

		// function getIpAddress()
		public function get_ip_addr() {
			if($this->is_granted()) {
				return $this->get('ipaddress');
			}
			return '*' . strstr($this->get('ipaddress'), '.');
		}

		/**
		 * Return content after filter
		 * @return string
		 */
		// function getContent($add_popup_menu = TRUE, $add_content_info = TRUE, $add_xe_content_class = TRUE)
		public function get_content() {
			if($this->is_secret() && !$this->is_accessible()) {
				return __('msg_secret_post', X2B_DOMAIN);
			}
			$s_content = $this->get('content');
			\X2board\Includes\stripEmbedTagForAdmin($s_content, $this->get('comment_author'));

			// when displaying the comment on the pop-up menu
			// if($add_popup_menu && Context::get('is_logged')) {
			// 	$content = sprintf(
			// 			'%s<div class="comment_popup_menu"><a href="#popup_menu_area" class="comment_%d" onclick="return false">%s</a></div>', $content, $this->comment_srl, Context::getLang('cmd_comment_do')
			// 	);
			// }

			// if additional information which can access contents is set
			// if($add_content_info) {
				$n_comment_author_id = $this->get('comment_author');
				if($n_comment_author_id < 0) {
					$n_comment_author_id = 0;
				}
				$s_content = sprintf(
					'<!--BeforeComment(%d,%d)--><div class="comment_%d_%d x2b_content">%s</div><!--AfterComment(%d,%d)-->', 
					$this->comment_id, $n_comment_author_id, 
					$this->comment_id, $n_comment_author_id, 
					$s_content, 
					$this->comment_id, $n_comment_author_id
				);
				// x2b_content class name should be specified although content access is not necessary.
			// }
			// else {
			// 	if($add_xe_content_class) {
			// 		$content = sprintf('<div class="xe_content">%s</div>', $content);
			// 	}
			// }
			return wpautop($s_content);
		}

		// function hasUploadedFiles()
		public function has_uploaded_files() {
			if(($this->is_secret() && !$this->is_accessible()) && !$this->is_granted()) {
				return FALSE;
			}
			return $this->get('uploaded_count') ? TRUE : FALSE;
		}

		/**
		 * 댓글에 표시할 첨부파일을 반환한다.
		 * @return object
		 */
		// function getUploadedFiles()
		public function get_uploaded_files() {
			if(($this->is_secret() && !$this->is_accessible()) && !$this->is_granted()) {
				return array();
			}

			if(!$this->get('uploaded_count')) {
				return array();
			}

			$o_file_model = \X2board\Includes\getModel('file');
			$file_list = $o_file_model->get_files($this->comment_id, array(), 'file_id', true);
			unset($o_file_model);
			return $file_list;
		}


		/**
		 * Return the editor html
		 * used in skins/comment_form.php
		 * @return string
		 */
		// function getEditor()
		/*public function get_editor() {
			// $n_board_id = $this->get('board_id');
			// if(!$n_board_id) {
			// 	$n_board_id = \X2board\Includes\Classes\Context::get('board_id');
			// }
			$o_editor_model = \X2board\Includes\getModel('editor');
			return $o_editor_model->get_board_editor('comment', $this->comment_id, 'comment_id', 'content');  // $n_board_id, 
		}*/









///////////////////////////		

		// function setComment($comment_srl)
		// {
		// 	$this->comment_srl = $comment_srl;
		// 	$this->_loadFromDB();
		// }

		// function isEditable()
		// {
		// 	if($this->isGranted() || !$this->get('member_srl'))
		// 	{
		// 		return TRUE;
		// 	}
		// 	return FALSE;
		// }

		// function getUserID()
		// {
		// 	return escape($this->get('user_id'), false);
		// }

		/**
		 * Return content with htmlspecialchars
		 * @return string
		 */
		// function getContentText($strlen = 0)
		// {
		// 	if($this->isSecret() && !$this->isAccessible())
		// 	{
		// 		return Context::getLang('msg_is_secret');
		// 	}

		// 	$content = $this->get('content');

		// 	if($strlen)
		// 	{
		// 		return cut_str(strip_tags($content), $strlen, '...');
		// 	}

		// 	return escape($content, false);
		// }

		/**
		 * Return summary content
		 * @return string
		 */
		function getSummary($str_size = 50, $tail = '...')
		{
			$content = $this->getContent(FALSE, FALSE);

			// for newline, insert a blank.
			$content = preg_replace('!(<br[\s]*/{0,1}>[\s]*)+!is', ' ', $content);

			// replace tags such as </p> , </div> , </li> by blanks.
			$content = str_replace(array('</p>', '</div>', '</li>', '-->'), ' ', $content);

			// Remove tags
			$content = preg_replace('!<([^>]*?)>!is', '', $content);

			// replace < , >, " 
			$content = str_replace(array('&lt;', '&gt;', '&quot;', '&nbsp;'), array('<', '>', '"', ' '), $content);

			// delete a series of blanks
			$content = preg_replace('/ ( +)/is', ' ', $content);

			// truncate strings
			$content = trim(cut_str($content, $str_size, $tail));

			// restore >, <, , "\
			$content = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $content);

			return $content;
		}

		function getRegdateTime()
		{
			$regdate = $this->get('regdate');
			$year = substr($regdate, 0, 4);
			$month = substr($regdate, 4, 2);
			$day = substr($regdate, 6, 2);
			$hour = substr($regdate, 8, 2);
			$min = substr($regdate, 10, 2);
			$sec = substr($regdate, 12, 2);
			return mktime($hour, $min, $sec, $month, $day, $year);
		}

		function getRegdateGM()
		{
			return $this->getRegdate('D, d M Y H:i:s') . ' ' . $GLOBALS['_time_zone'];
		}

		function getUpdate($format = 'Y.m.d H:i:s')
		{
			return zdate($this->get('last_update'), $format);
		}

		function getPermanentUrl()
		{
			return getFullUrl('', 'mid', $this->getCommentMid(), 'document_srl', $this->get('document_srl')) . '#comment_' . $this->get('comment_srl');
		}

		function getUpdateTime()
		{
			$year = substr($this->get('last_update'), 0, 4);
			$month = substr($this->get('last_update'), 4, 2);
			$day = substr($this->get('last_update'), 6, 2);
			$hour = substr($this->get('last_update'), 8, 2);
			$min = substr($this->get('last_update'), 10, 2);
			$sec = substr($this->get('last_update'), 12, 2);
			return mktime($hour, $min, $sec, $month, $day, $year);
		}

		function getUpdateGM()
		{
			return gmdate("D, d M Y H:i:s", $this->getUpdateTime());
		}

		/**
		 * Return author's profile image
		 * @return object
		 */
		function getProfileImage()
		{
			if(!$this->isExists() || !$this->get('member_srl'))
			{
				return;
			}
			$oMemberModel = getModel('member');
			$profile_info = $oMemberModel->getProfileImage($this->get('member_srl'));
			if(!$profile_info)
			{
				return;
			}

			return $profile_info->src;
		}

		function thumbnailExists($width = 80, $height = 0, $type = '')
		{
			if(!$this->comment_srl)
			{
				return FALSE;
			}

			if(!$this->getThumbnail($width, $height, $type))
			{
				return FALSE;
			}

			return TRUE;
		}

		function getThumbnail($width = 80, $height = 0, $thumbnail_type = '')
		{
			// return false if no doc exists
			if(!$this->comment_srl)
			{
				return;
			}

			if($this->isSecret() && !$this->isGranted())
			{
				return;
			}

			// If signiture height setting is omitted, create a square
			if(!$height)
			{
				$height = $width;
			}

			$content = $this->get('content');
			if(!$this->hasUploadedFiles())
			{
				if(!$content)
				{
					$args = new stdClass();
					$args->comment_srl = $this->comment_srl;
					$output = executeQuery('document.getComment', $args, array('content'));
					if($output->toBool() && $output->data)
					{
						$content = $output->data->content;
						$this->add('content', $content);
					}
				}

				if(!preg_match("!<img!is", $content)) return;
			}

			// get thumbail generation info on the doc module configuration.
			if(!in_array($thumbnail_type, array('crop', 'ratio')))
			{
				$thumbnail_type = 'crop';
			}

			// Define thumbnail information
			$thumbnail_path = sprintf('files/thumbnails/%s', getNumberingPath($this->comment_srl, 3));
			$thumbnail_file = sprintf('%s%dx%d.%s.jpg', $thumbnail_path, $width, $height, $thumbnail_type);
			$thumbnail_lockfile = sprintf('%s%dx%d.%s.lock', $thumbnail_path, $width, $height, $thumbnail_type);
			$thumbnail_url = Context::getRequestUri() . $thumbnail_file;

			// return false if a size of existing thumbnail file is 0. otherwise return the file path
			if(file_exists($thumbnail_file) || file_exists($thumbnail_lockfile))
			{
				if(filesize($thumbnail_file) < 1)
				{
					return FALSE;
				}
				else
				{
					return $thumbnail_url . '?' . date('YmdHis', filemtime($thumbnail_file));
				}
			}

			// Create lockfile to prevent race condition
			FileHandler::writeFile($thumbnail_lockfile, '', 'w');

			// Target file
			$source_file = NULL;
			$is_tmp_file = FALSE;

			// find an image file among attached files
			if($this->hasUploadedFiles())
			{
				$file_list = $this->getUploadedFiles();

				$first_image = null;
				foreach($file_list as $file)
				{
					if($file->direct_download !== 'Y') continue;

					if($file->cover_image === 'Y' && file_exists($file->uploaded_filename))
					{
						$source_file = $file->uploaded_filename;
						break;
					}

					if($first_image) continue;

					if(preg_match("/\.(jpe?g|png|gif|bmp)$/i", $file->source_filename))
					{
						if(file_exists($file->uploaded_filename))
						{
							$first_image = $file->uploaded_filename;
						}
					}
				}

				if(!$source_file && $first_image)
				{
					$source_file = $first_image;
				}
			}

			// get an image file from the doc content if no file attached. 
			$is_tmp_file = false;
			if(!$source_file)
			{
				$random = new Password();

				preg_match_all("!<img[^>]*src=(?:\"|\')([^\"\']*?)(?:\"|\')!is", $content, $matches, PREG_SET_ORDER);

				foreach($matches as $target_image)
				{
					$target_src = trim($target_image[1]);
					if(preg_match('/\/(common|modules|widgets|addons|layouts|m\.layouts)\//i', $target_src)) continue;

					if(!preg_match('/^(http|https):\/\//i',$target_src))
					{
						$target_src = Context::getRequestUri().$target_src;
					}

					$target_src = htmlspecialchars_decode($target_src);

					$tmp_file = _XE_PATH_ . 'files/cache/tmp/' . $random->createSecureSalt(32, 'hex');
					FileHandler::getRemoteFile($target_src, $tmp_file);
					if(!file_exists($tmp_file)) continue;

					$imageinfo = getimagesize($tmp_file);
					list($_w, $_h) = $imageinfo;
					if($imageinfo === false || ($_w < ($width * 0.3) && $_h < ($height * 0.3))) {
						FileHandler::removeFile($tmp_file);
						continue;
					}

					$source_file = $tmp_file;
					$is_tmp_file = true;
					break;
				}
			}

			$output = FileHandler::createImageFile($source_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type);

			// Remove source file if it was temporary
			if($is_tmp_file)
			{
				FileHandler::removeFile($source_file);
			}

			// Remove lockfile
			FileHandler::removeFile($thumbnail_lockfile);

			// Create an empty file if thumbnail generation failed
			if(!$output)
			{
				FileHandler::writeFile($thumbnail_file, '','w');
			}

			return $thumbnail_url . '?' . date('YmdHis', filemtime($thumbnail_file));
		}

		function isCarted()
		{
			return $_SESSION['comment_management'][$this->comment_srl];
		}

		/**
		 * Return author's signiture
		 * @return string
		 */
		// function getSignature()
		// {
		// 	// pass if the posting not exists.
		// 	if(!$this->isExists() || !$this->get('member_srl'))
		// 	{
		// 		return;
		// 	}

		// 	// get the signiture information
		// 	$oMemberModel = getModel('member');
		// 	$signature = $oMemberModel->getSignature($this->get('member_srl'));

		// 	// check if max height of the signiture is specified on the member module
		// 	if(!isset($GLOBALS['__member_signature_max_height']))
		// 	{
		// 		$oModuleModel = getModel('module');
		// 		$member_config = $oModuleModel->getModuleConfig('member');
		// 		$GLOBALS['__member_signature_max_height'] = $member_config->signature_max_height;
		// 	}

		// 	$max_signature_height = $GLOBALS['__member_signature_max_height'];

		// 	if($max_signature_height)
		// 	{
		// 		$signature = sprintf('<div style="max-height:%dpx;overflow:auto;overflow-x:hidden;height:expression(this.scrollHeight > %d ? \'%dpx\': \'auto\')">%s</div>', $max_signature_height, $max_signature_height, $max_signature_height, $signature);
		// 	}

		// 	return $signature;
		// }
		
		/**
		 * Returns the comment's mid in order to construct SEO friendly URLs
		 * @return string
		 */
		// function getCommentMid()
		// {
		// 	$model = getModel('module');
		// 	$module = $model->getModuleInfoByModuleSrl($this->get('module_srl'));
		// 	return $module->mid;
		// }

		// function useNotify()
		// {
		// 	return $this->get('notify_message') == 'Y' ? TRUE : FALSE;
		// }

		/**
		 * Notify to comment owner
		 * @return void
		 */
		// function notify($type, $content)
		// {
		// 	// return if not useNotify
		// 	if(!$this->useNotify())
		// 	{
		// 		return;
		// 	}

		// 	// pass if the author is not logged-in user 
		// 	if(!$this->get('member_srl'))
		// 	{
		// 		return;
		// 	}

		// 	// return if the currently logged-in user is an author of the comment.
		// 	$logged_info = Context::get('logged_info');
		// 	if($logged_info->member_srl == $this->get('member_srl'))
		// 	{
		// 		return;
		// 	}

		// 	// get where the comment belongs to 
		// 	$oDocumentModel = getModel('document');
		// 	$oDocument = $oDocumentModel->getDocument($this->get('document_srl'));

		// 	// Variables
		// 	if($type)
		// 	{
		// 		$title = "[" . $type . "] ";
		// 	}

		// 	$title .= cut_str(strip_tags($content), 30, '...');
		// 	$content = sprintf('%s<br /><br />from : <a href="%s#comment_%s" target="_blank">%s</a>', $content, getFullUrl('', 'document_srl', $this->get('document_srl')), $this->get('comment_srl'), getFullUrl('', 'document_srl', $this->get('document_srl')));
		// 	$receiver_srl = $this->get('member_srl');
		// 	$sender_member_srl = $logged_info->member_srl;

		// 	// send a message
		// 	$oCommunicationController = getController('communication');
		// 	$oCommunicationController->sendMessage($sender_member_srl, $receiver_srl, $title, $content, FALSE);
		// }

		// function isExistsHomepage()
		// {
		// 	if(trim($this->get('homepage')))
		// 	{
		// 		return TRUE;
		// 	}

		// 	return FALSE;
		// }

		// function getHomepageUrl()
		// {
		// 	$url = trim($this->get('homepage'));
		// 	if(!$url)
		// 	{
		// 		return;
		// 	}

		// 	if(strncasecmp('http://', $url, 7) !== 0)
		// 	{
		// 		$url = "http://" . $url;
		// 	}

		// 	return escape($url, false);
		// }

		// function getMemberSrl()
		// {
		// 	return $this->get('member_srl');
		// }

		// function getUserName()
		// {
		// 	return escape($this->get('user_name'), false);
		// }
	}
}
/* End of file comment.item.php */