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

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_post` (
`post_id` bigint(20) unsigned NOT NULL,
`board_id` bigint(20) unsigned NOT NULL,
`parent_post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
`category_id` bigint(20) unsigned DEFAULT 0,
`post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
`nick_name` varchar(127) NOT NULL,
`title` varchar(127) NOT NULL,
`title_bold` char(1) NOT NULL DEFAULT 'N',
`title_color` varchar(7),
`content` longtext NOT NULL,
`email_address` varchar(25),
`password` varchar(60) NOT NULL,
`comment_count` int(10) unsigned NOT NULL,
`readed_count` int(10) unsigned NOT NULL,
`like` int(10) unsigned NOT NULL,
`dislike` int(10) unsigned NOT NULL,
`is_notice` char(1) NOT NULL DEFAULT 'N',
`is_secret` char(1) NOT NULL DEFAULT 'N',
`allow_search` char(1) NOT NULL DEFAULT '1',
`comment_status` varchar(10) NOT NULL DEFAULT 'ALLOW',
`post_status` varchar(10),
`vote_count` int(11) NOT NULL,
`uploaded_count` smallint(2) NOT NULL,
`ipaddress` varchar(128) NOT NULL,
`list_order` bigint(20) NOT NULL,
`update_order` bigint(20) NOT NULL,
`tags` varchar(256),
`ua` char(1) NOT NULL,
`regdate` datetime NOT NULL,
`last_update` datetime NOT NULL,
PRIMARY KEY (`post_id`),
KEY `idx_board_id` (`board_id`),
KEY `idx_parent_post_id` (`parent_post_id`),
KEY `idx_category_id` (`category_id`),
KEY `idx_is_notice` (`is_notice`),
KEY `idx_post_author` (`post_author`),
KEY `idx_readed_count` (`readed_count`),
KEY `idx_post_status` (`post_status`),
KEY `idx_vote_count` (`vote_count`),
KEY `idx_list_order` (`list_order`),
KEY `idx_update_order` (`update_order`),
KEY `idx_regdate` (`regdate`),
KEY `idx_last_update` (`last_update`)
) {$charset_collate};");

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_comments` (
`comment_id` bigint(11) NOT NULL,
`board_id` bigint(11) NOT NULL DEFAULT 0,
`parent_post_id` bigint(11) NOT NULL DEFAULT 0,
`parent_comment_id` bigint(11) NOT NULL DEFAULT 0,
`is_secret` char(1) NOT NULL DEFAULT 'N',
`content` longtext NOT NULL,
`password` varchar(60) DEFAULT NULL,
`nick_name` varchar(80) NOT NULL,
`comment_author` bigint(11) NOT NULL,
`email_address` varchar(250) NOT NULL,
`uploaded_count` bigint(11) NOT NULL DEFAULT 0,
`regdate` datetime DEFAULT NULL,
`last_update` datetime DEFAULT NULL,
`ipaddress` varchar(128) NOT NULL,
`list_order` bigint(11) NOT NULL,
`ua` char(1) NOT NULL,
`status` char(1) NOT NULL DEFAULT 1,  -- 없어도 되나?
PRIMARY KEY (`comment_id`),
UNIQUE KEY `idx_board_list_order` (`board_id`,`list_order`),
KEY `idx_board_id` (`board_id`),
KEY `idx_parent_post_id` (`parent_post_id`),
KEY `idx_parent_comment_id` (`parent_comment_id`),
KEY `idx_comment_author` (`comment_author`),
KEY `idx_uploaded_count` (`uploaded_count`),
KEY `idx_regdate` (`regdate`),
KEY `idx_last_update` (`last_update`),
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
`regdate` datetime DEFAULT NULL,
`depth` bigint(11) NOT NULL DEFAULT 0,
PRIMARY KEY (`comment_id`),
KEY `idx_list` (`parent_post_id`,`head`,`arrange`),
KEY `idx_date` (`board_id`,`regdate`)
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
`is_default` char(1) DEFAULT 'N',
`deleted` char(1) DEFAULT 'N',
`post_count` bigint(11) NOT NULL DEFAULT 0,
`regdate` datetime DEFAULT NULL,
`last_update` varchar(14) DEFAULT NULL,
`list_order` bigint(11) NOT NULL,
`group_ids` text DEFAULT NULL,
`color` varchar(11) DEFAULT NULL,
`description` varchar(200) DEFAULT NULL,
PRIMARY KEY (`category_id`),
KEY `idx_board_id` (`board_id`),
KEY `idx_regdate` (`regdate`)
) {$charset_collate};");