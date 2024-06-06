<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  importAdminModel
 * @author singleview.co.kr
 * @brief  게시판 XML import
 **/
namespace X2board\Includes\Modules\Import;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Modules\\Import\\importAdminModel')) {

	class importAdminModel {
		public function __construct() {	}

        /**
		 * XE2 XML 복원파일을 입력받아 기존 데이터를 비우고 DB에 입력한다.
		 * @param string $s_uploaded_file
		 */
		public function get_x2b_sequence(){
			global $wpdb;
			// retrieve x2b_sequence
			$o_rst = $wpdb->get_row("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '{$wpdb->prefix}x2b_sequence'");
			$n_cur_auto_increment = $o_rst->AUTO_INCREMENT;
			unset($o_rst);
            return $n_cur_auto_increment;
		}
	}
}
?>