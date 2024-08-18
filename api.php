<?php
/**
 * The API of the plugin.
 *
 * @author  https://singleview.co.kr/
 * @version 0.0.1
 */
namespace X2board\Api;

if (! defined('ABSPATH') ) {
    exit;  // Exit if accessed directly.
}

/**
 * post search API
 */
function get_quick_search( $n_board_id, $s_query = null, $o_param = null )
{
    global $wpdb;

    $n_subject_trim_length = isset($o_param->n_subject_trim_length) ? $o_param->n_subject_trim_length : 20;
    $n_content_trim_length = isset($o_param->n_content_trim_length) ? $o_param->n_content_trim_length : 100;
    $s_date_format = isset($o_param->s_date_format) ? $o_param->s_date_format : 'Y-m-d H:i:s';
    $n_posts_per_page = isset($o_param->n_posts_per_page) ? $o_param->n_posts_per_page : 9;
    $n_cur_page = isset($o_param->n_cur_page) ? $o_param->n_cur_page : 1;
    $n_offset = ( $n_cur_page - 1 ) * $n_posts_per_page;
    
    $a_retrieved_post = array();
    $s_board_cnt = $wpdb->get_var("SELECT count(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$n_board_id'");
    if (intval($s_board_cnt) !== 1 ) {
        return $a_retrieved_post;
    }

    $s_query = $s_query == '' ? null : $s_query;  // convert '' to null
    if($s_query ) {
        $a_query = explode(',', $s_query);
        
        $a_title_content_search = array();
        foreach( $a_query as $s_single_query ) {
            $a_title_content_search[] = "(`title` LIKE '%{$s_single_query}%' OR `content` LIKE '%{$s_single_query}%')";
        }
        unset($a_query);
        $s_title_content_search = implode(' OR ', $a_title_content_search);
        unset($a_title_content_search);
        $a_posts = $wpdb->get_results("SELECT `post_id`, `category_id`, `title`, `content`, `readed_count`, `regdate_dt` FROM `{$wpdb->prefix}x2b_posts` WHERE `board_id` = $n_board_id AND ( $s_title_content_search ) AND `status` = 'PUBLIC' ORDER BY `list_order` DESC LIMIT $n_offset, $n_posts_per_page");
    }
    else { // avoid like search as possible
        $a_posts = $wpdb->get_results("SELECT `post_id`, `category_id`, `title`, `content`, `readed_count`, `regdate_dt` FROM `{$wpdb->prefix}x2b_posts` WHERE `board_id` = $n_board_id AND `status` = 'PUBLIC' ORDER BY `list_order` DESC LIMIT $n_offset, $n_posts_per_page");
    }
    
    $a_board_permalink[$n_board_id] = esc_url(site_url() . '/' . urlencode(urldecode(get_post($n_board_id)->post_name)));
    $s_board_permalink = esc_url(site_url() . '/' . urlencode(urldecode(get_post($n_board_id)->post_name)));
    
    foreach( $a_posts as $o_rec ) {
        $o_new_rec = new \stdClass();
        $o_new_rec->title = wp_trim_words(strip_tags($o_rec->title), $n_subject_trim_length, '...');
        $o_new_rec->content = wp_trim_words(strip_tags($o_rec->content), $n_content_trim_length, '...');
        $o_new_rec->permalink = $s_board_permalink . '/' . $o_rec->post_id;
        if(intval($o_rec->category_id) != 0 ) {
            $s_category_title = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}x2b_categories` WHERE `category_id` = $o_rec->category_id AND `deleted`='N'");
        }
        else {
            $s_category_title = __('lbl_default_category', X2B_DOMAIN);;
        }
        $o_new_rec->category_title = $s_category_title;
        $o_new_rec->readed_count = $o_rec->readed_count;
        $o_new_rec->regdate  = date_format(date_create($o_rec->regdate_dt), $s_date_format);
        $a_retrieved_post[] = $o_new_rec;
    }
    unset($a_posts);
    unset($a_board_permalink);
    return $a_retrieved_post;
}

/**
 * notice API
 */
function get_notice( $n_board_id, $o_param = null )
{
    global $wpdb;

    $n_subject_trim_length = isset($o_param->n_subject_trim_length) ? $o_param->n_subject_trim_length : 50;
    $n_content_trim_length = isset($o_param->n_content_trim_length) ? $o_param->n_content_trim_length : 50;
    $s_date_format = isset($o_param->s_date_format) ? $o_param->s_date_format : 'Y-m-d H:i:s';
    $n_posts_per_page = isset($o_param->n_posts_per_page) ? $o_param->n_posts_per_page : 3;
    $n_offset = 0;
    
    $a_retrieved_notice = array();
    $s_board_cnt = $wpdb->get_var("SELECT count(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$n_board_id'");
    if (intval($s_board_cnt) !== 1 ) {
        return $a_retrieved_notice;
    }
    $a_posts = $wpdb->get_results("SELECT `post_id`, `board_id`, `title`, `content`, `readed_count`, `regdate_dt` FROM `{$wpdb->prefix}x2b_posts` WHERE `board_id` = $n_board_id AND `is_notice` = 'Y' AND `status` = 'PUBLIC' ORDER BY `list_order` DESC LIMIT $n_offset, $n_posts_per_page");
    $s_board_permalink = esc_url(site_url() . '/' . urlencode(urldecode(get_post($n_board_id)->post_name)));

    foreach( $a_posts as $o_rec ) {
        $o_new_rec = new \stdClass();
        $o_new_rec->title = wp_trim_words(strip_tags($o_rec->title), $n_subject_trim_length, '...');
        $o_new_rec->content = wp_trim_words(strip_tags($o_rec->content), $n_content_trim_length, '...');
        $o_new_rec->permalink = $s_board_permalink . '/' . $o_rec->post_id;
        $o_new_rec->readed_count = $o_rec->readed_count;
        $o_new_rec->regdate  = date_format(date_create($o_rec->regdate_dt), $s_date_format);
        $a_retrieved_notice[] = $o_new_rec;
    }
    unset($a_posts);
    unset($a_board_permalink);
    return $a_retrieved_notice;
}
