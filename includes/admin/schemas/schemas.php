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
`allow_comment` char(1) NOT NULL DEFAULT 'Y',
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

dbDelta("CREATE TABLE `{$wpdb->prefix}x2b_category` (
`category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`board_id` bigint(20) unsigned NOT NULL,
`parent_id` bigint(20) NOT NULL DEFAULT 0,
`category_name` varchar(250) DEFAULT NULL,
`expand` char(1) DEFAULT 'N',
`post_count` mediumint(9) unsigned NOT NULL DEFAULT 0,
`list_order` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '표시 순서',
`group_srls` text DEFAULT NULL COMMENT '수정 권한 회원 그룹 번호',
`color` varchar(11) DEFAULT NULL,
`is_default` char(1) DEFAULT NULL COMMENT '새글 작성 시 기본 선택',
`deleted` char(1) NOT NULL DEFAULT 'N',
`regdate` varchar(14) DEFAULT NULL,
`last_update` varchar(14) DEFAULT NULL,
PRIMARY KEY (`category_id`),
KEY `board_id` (`board_id`),
KEY `deleted_by_board` (`board_id`,`deleted`)
) {$charset_collate};");