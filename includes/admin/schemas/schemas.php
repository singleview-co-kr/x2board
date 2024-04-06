<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://singleview.co.kr
 * @since 2.6.0
 *
 * @package    x2board
 * @subpackage Admin
 */

namespace X2board\Includes\Admin;

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

global $wpdb;
	
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
$charset_collate = $wpdb->get_charset_collate();

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_sequence` (
`seq` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
PRIMARY KEY (`seq`)
) {$charset_collate};");

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_mapper` (
`board_id` bigint(20) unsigned NOT NULL,
`wp_page_id` bigint(20) unsigned NOT NULL,
`board_title` varchar(127) NOT NULL,
`create_date` datetime NOT NULL,
PRIMARY KEY (`board_id`)
) {$charset_collate};");

// `module_srl` bigint(11) NOT NULL DEFAULT 0,  --- for XE document
// `module_srl` bigint(11) NOT NULL DEFAULT 0,  --- for XE document
// `category_id` bigint(20) unsigned DEFAULT 0,   --- it was category_srl
// `lang_code` varchar(10) NOT NULL DEFAULT '',  --- for XE document
// `trackback_count` bigint(11) NOT NULL DEFAULT 0,  --- for XE document
// `user_id` varchar(80) DEFAULT NULL,   --- for XE document
// `user_name` varchar(80) NOT NULL,   --- for XE document
// `member_srl` bigint(11) NOT NULL,  --- for XE document
// `post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
// `homepage` varchar(250) NOT NULL,  --- for XE document
// `extra_vars` text DEFAULT NULL,  --- for XE document
// `regdate` varchar(14) DEFAULT NULL,   --- for XE document
// `last_update` varchar(14) DEFAULT NULL,   --- for XE document
// `allow_trackback` char(1) NOT NULL DEFAULT 'Y',   --- for XE document
// `notify_message` char(1) NOT NULL DEFAULT 'N',   --- for XE document

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_posts` (
`post_id` bigint(20) unsigned NOT NULL,
`module_srl` bigint(11) NOT NULL DEFAULT 0,
`board_id` bigint(20) unsigned NOT NULL,
`parent_post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
`category_id` bigint(20) unsigned DEFAULT 0,
`lang_code` varchar(10) NOT NULL DEFAULT '',
`is_notice` char(1) NOT NULL DEFAULT 'N',
`title` varchar(127) NOT NULL,
`title_bold` char(1) NOT NULL DEFAULT 'N',
`title_color` varchar(7),
`content` longtext NOT NULL,
`readed_count` int(10) unsigned NOT NULL,
`voted_count` int(11) NOT NULL,
`blamed_count` bigint(11) NOT NULL DEFAULT 0,
`comment_count` int(10) unsigned NOT NULL,
`trackback_count` bigint(11) NOT NULL DEFAULT 0,
`uploaded_count` smallint(2) NOT NULL,
`password` varchar(60) NOT NULL,
`user_id` varchar(80) DEFAULT NULL,
`user_name` varchar(80) NOT NULL,
`nick_name` varchar(127) NOT NULL,
`member_srl` bigint(11) NOT NULL,
`post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
`email_address` varchar(25),
`homepage` varchar(250) NOT NULL,
`tags` varchar(256),
`extra_vars` text DEFAULT NULL,
`regdate` varchar(14) DEFAULT NULL,
`last_update` varchar(14) DEFAULT NULL,
`last_updater` varchar(80) DEFAULT NULL,
`ipaddress` varchar(128) NOT NULL,
`list_order` bigint(20) NOT NULL,
`update_order` bigint(20) NOT NULL,
`allow_trackback` char(1) NOT NULL DEFAULT 'Y',
`notify_message` char(1) NOT NULL DEFAULT 'N',
`status` varchar(20),
`comment_status` varchar(10) NOT NULL DEFAULT 'ALLOW',
`allow_search` char(1) NOT NULL DEFAULT '1',
`ua` char(1) NOT NULL,
`regdate_dt` datetime NOT NULL,
`last_update_dt` datetime NOT NULL,
PRIMARY KEY (`post_id`),
KEY `idx_board_id` (`board_id`),
KEY `idx_parent_post_id` (`parent_post_id`),
KEY `idx_category_id` (`category_id`),
KEY `idx_is_notice` (`is_notice`),
KEY `idx_post_author` (`post_author`),
KEY `idx_readed_count` (`readed_count`),
KEY `idx_voted_count` (`voted_count`),
KEY `idx_blamed_count` (`blamed_count`),
KEY `idx_comment_count` (`comment_count`),
KEY `idx_uploaded_count` (`uploaded_count`),
KEY `idx_status` (`status`),
KEY `idx_list_order` (`list_order`),
KEY `idx_update_order` (`update_order`),
KEY `idx_regdate_dt` (`regdate_dt`),
KEY `idx_last_update_dt` (`last_update_dt`),
KEY `idx_ipaddress` (`ipaddress`),
KEY `idx_board_list_order` (`board_id`,`list_order`),
KEY `idx_board_update_order` (`board_id`,`update_order`),
KEY `idx_board_readed_count` (`board_id`,`readed_count`),
KEY `idx_board_voted_count` (`board_id`,`voted_count`),
KEY `idx_board_notice` (`board_id`,`is_notice`),
KEY `idx_board_document_srl` (`board_id`,`post_id`),
KEY `idx_board_blamed_count` (`board_id`,`blamed_count`),
KEY `idx_board_status` (`board_id`,`status`)
) {$charset_collate};");

// `module_srl` bigint(11) NOT NULL DEFAULT 0,  -- for XE
// `voted_count` bigint(11) NOT NULL DEFAULT 0,  -- for XE
// `blamed_count` bigint(11) NOT NULL DEFAULT 0,  -- for XE
// `notify_message` char(1) NOT NULL DEFAULT 'N',  -- for XE
// `user_id` varchar(80) DEFAULT NULL,  -- for XE
// `user_name` varchar(80) NOT NULL,  -- for XE
// `homepage` varchar(250) NOT NULL,  -- for XE
// `regdate` varchar(14) DEFAULT NULL,  -- for XE
// `last_update` varchar(14) DEFAULT NULL,  -- for XE

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_comments` (
`comment_id` bigint(11) NOT NULL,
`module_srl` bigint(11) NOT NULL DEFAULT 0,
`board_id` bigint(11) NOT NULL DEFAULT 0,
`parent_post_id` bigint(11) NOT NULL DEFAULT 0,
`parent_comment_id` bigint(11) NOT NULL DEFAULT 0,
`is_secret` char(1) NOT NULL DEFAULT 'N',
`content` longtext NOT NULL,
`voted_count` bigint(11) NOT NULL DEFAULT 0,
`blamed_count` bigint(11) NOT NULL DEFAULT 0,
`notify_message` char(1) NOT NULL DEFAULT 'N',
`password` varchar(60) DEFAULT NULL,
`user_id` varchar(80) DEFAULT NULL,
`user_name` varchar(80) NOT NULL,
`nick_name` varchar(80) NOT NULL,
`member_srl` bigint(11) NOT NULL,
`comment_author` bigint(11) NOT NULL,
`email_address` varchar(250) NOT NULL,
`homepage` varchar(250) NOT NULL,
`uploaded_count` bigint(11) NOT NULL DEFAULT 0,
`regdate` varchar(14) DEFAULT NULL,
`last_update` varchar(14) DEFAULT NULL,
`ipaddress` varchar(128) NOT NULL,
`list_order` bigint(11) NOT NULL,
`status` char(1) NOT NULL DEFAULT 1,
`ua` char(1) NOT NULL,
`regdate_dt` datetime DEFAULT NULL,
`last_update_dt` datetime DEFAULT NULL,
PRIMARY KEY (`comment_id`),
UNIQUE KEY `idx_board_list_order` (`board_id`,`list_order`),
KEY `idx_board_id` (`board_id`),
KEY `idx_parent_post_id` (`parent_post_id`),
KEY `idx_parent_comment_id` (`parent_comment_id`),
KEY `idx_comment_author` (`comment_author`),
KEY `idx_uploaded_count` (`uploaded_count`),
KEY `idx_regdate_dt` (`regdate_dt`),
KEY `idx_last_update_dt` (`last_update_dt`),
KEY `idx_ipaddress` (`ipaddress`),
KEY `idx_list_order` (`list_order`),
KEY `idx_status` (`status`)
) {$charset_collate};");

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_comments_list` (
`comment_id` bigint(11) NOT NULL,
`parent_post_id` bigint(11) NOT NULL DEFAULT 0,
`head` bigint(11) NOT NULL DEFAULT 0,
`arrange` bigint(11) NOT NULL DEFAULT 0,
`board_id` bigint(11) NOT NULL DEFAULT 0,
`regdate` varchar(14) DEFAULT NULL,
`regdate_dt` datetime DEFAULT NULL,
`depth` bigint(11) NOT NULL DEFAULT 0,
PRIMARY KEY (`comment_id`),
KEY `idx_list` (`parent_post_id`,`head`,`arrange`),
KEY `idx_date` (`board_id`,`regdate_dt`)
) {$charset_collate};");		

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_files` (
`file_id` bigint(11) NOT NULL,
`upload_target_id` bigint(11) NOT NULL DEFAULT 0,
`upload_target_type` char(3) DEFAULT NULL,
`sid` varchar(60) DEFAULT NULL,
`board_id` bigint(11) NOT NULL DEFAULT 0,
`author` bigint(11) NOT NULL,
`download_count` bigint(11) NOT NULL DEFAULT 0,
`direct_download` char(1) NOT NULL DEFAULT 'N',
`source_filename` varchar(250) DEFAULT NULL,
`uploaded_filename` varchar(250) DEFAULT NULL,
`file_size` bigint(11) NOT NULL DEFAULT 0,
`comment` varchar(250) DEFAULT NULL,
`isvalid` char(1) DEFAULT 'N',
`cover_image` char(1) NOT NULL DEFAULT 'N',
`regdate` datetime DEFAULT NULL,
`ipaddress` varchar(128) NOT NULL,
PRIMARY KEY (`file_id`),
KEY `idx_upload_target_id` (`upload_target_id`),
KEY `idx_upload_target_type` (`upload_target_type`),
KEY `idx_board_id` (`board_id`),
KEY `idx_author` (`author`),
KEY `idx_download_count` (`download_count`),
KEY `idx_file_size` (`file_size`),
KEY `idx_is_valid` (`isvalid`),
KEY `idx_list_order` (`cover_image`),
KEY `idx_regdate` (`regdate`),
KEY `idx_ipaddress` (`ipaddress`)
) {$charset_collate};");

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_categories` (
`category_id` bigint(11) NOT NULL DEFAULT 0,
`board_id` bigint(11) NOT NULL DEFAULT 0,
`parent_id` bigint(12) NOT NULL DEFAULT 0,
`title` varchar(250) DEFAULT NULL,
`expand` char(1) DEFAULT 'N',
`post_count` bigint(11) NOT NULL DEFAULT 0,
`regdate` varchar(14) DEFAULT NULL,
`last_update` varchar(14) DEFAULT NULL,
`list_order` bigint(11) NOT NULL,
`group_ids` text DEFAULT NULL,
`color` varchar(11) DEFAULT NULL,
`description` varchar(200) DEFAULT NULL,
`is_default` char(1) DEFAULT 'N',
`deleted` char(1) DEFAULT 'N',
`regdate_dt` datetime DEFAULT NULL,
`last_update_dt` datetime DEFAULT NULL,
PRIMARY KEY (`category_id`),
KEY `idx_board_id` (`board_id`),
KEY `idx_regdate_dt` (`regdate_dt`)
) {$charset_collate};");