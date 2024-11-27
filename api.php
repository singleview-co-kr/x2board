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

require_once X2B_PATH . 'includes/classes/cache/CacheFileDisk.class.php';

/**
 * post search API
 * a_board_id
 * s_query -> blank or comma separated string
 * s_notice_mode -> notice_only, include_notice, exclude_notice(default)
 * a_select_column
 * n_posts_per_page
 * n_cur_page
 * n_subject_trim_length
 * n_content_trim_length
 * s_date_format
 */
function get_quick_search( $o_param = null )
{
    global $wpdb;

    // begin - validate requested board_id
    $a_board_id = isset($o_param->a_board_id) ? $o_param->a_board_id : false; // sentinel
    if( ! $a_board_id ) {
        return array();
    }
    $a_valid_board_id = array();
    foreach( $a_board_id as $n_board_id ) {
        $s_board_cnt = $wpdb->get_var("SELECT count(*) FROM `{$wpdb->prefix}x2b_mapper` WHERE `board_id`='$n_board_id'");
        if (intval($s_board_cnt) == 1 ) {
            $a_valid_board_id[] = $n_board_id;
        }
    }
    unset( $a_board_id );

    $n_valid_board_cnd = count( $a_valid_board_id );
    if( $n_valid_board_cnd == 0 ) {
        return array();
    }
    // end - validate requested board_id
    $s_query = isset($o_param->s_query) ? $o_param->s_query : null;
    $s_query = $s_query == '' ? null : $s_query;  // convert '' to null

    $a_select_column = isset($o_param->a_select_column) ? $o_param->a_select_column : false;
    $n_posts_per_page = isset($o_param->n_posts_per_page) ? $o_param->n_posts_per_page : 9;
    $n_cur_page = isset($o_param->n_cur_page) ? $o_param->n_cur_page : 1;
    $n_offset = ( $n_cur_page - 1 ) * $n_posts_per_page;
    // begin - build select column
    if( ! $a_select_column ) {
        $s_select_column = '`post_id`, `category_id`, `title`, `content`, `readed_count`, `regdate_dt`';
    }
    else {
        $a_tmp_column = array();  // array( 'post_id', 'category_id', 'title', 'content', 'readed_count', 'regdate_dt' );
        foreach( $a_select_column as $s_column ) {
            $a_tmp_column[] = '`'.$s_column.'`';
        }
        unset( $a_select_column );
        $s_select_column = implode( ',', $a_tmp_column );
        unset( $a_tmp_column );
    }
    // end - build select column

    // begin - build where clause
    if( $n_valid_board_cnd == 1 ) {
        $s_where_board = '`board_id` = ' . $n_board_id;
    }
    elseif( $n_valid_board_cnd > 1 ) {
        $s_where_board = '`board_id` IN (' . implode( ',', $a_valid_board_id ) . ')';
    }
    unset( $a_valid_board_id );

    $a_where_clause = array( $s_where_board );

    $s_title_content_search = null;
    if( $s_query ) {  // avoid like search as possible
        $a_query = explode(' ', $s_query);  // default is blank separated
        if( count( $a_query ) == 1 ) { // if comma separated
            $a_query = explode( ',', $s_query );
        }
        $a_title_content_search = array();
        foreach( $a_query as $s_single_query ) {
            if( strlen($s_single_query) > 0 ) {
                $a_title_content_search[] = "(`title` LIKE '%{$s_single_query}%' OR `content` LIKE '%{$s_single_query}%')";
            }
        }
        unset($a_query);
        $n_query_cnt = count( $a_title_content_search );
        if( $n_query_cnt == 1 ) {
            $s_title_content_search = 'AND ' . implode(' OR ', $a_title_content_search);
        }
        elseif( $n_query_cnt > 1 ) {
            $s_title_content_search = 'AND (' . implode(' OR ', $a_title_content_search) . ')';
        }
        unset($a_title_content_search);
        $a_where_clause[] = $s_title_content_search;
    }
    $s_notice_mode = isset($o_param->s_notice_mode) ? $o_param->s_notice_mode : 'exclude_notice';
    if( ! in_array( $s_notice_mode, array( 'notice_only', 'include_notice', 'exclude_notice' ) ) ) {
        $s_notice_mode = 'exclude_notice';  // set default
    }
    if( $s_notice_mode == 'notice_only' ) {
        $a_where_clause[] = "AND `is_notice` = 'Y'";
    }
    elseif( $s_notice_mode == 'exclude_notice' ) {
        $a_where_clause[] = "AND `is_notice` = 'N'";
    }
    elseif( $s_notice_mode == 'include_notice' ) {
        $a_where_clause[] = "AND `is_notice` IN ('Y', 'N')";
    }
    $a_where_clause[] = "AND `status` = 'PUBLIC'";
    $s_where_clause = implode(' ', $a_where_clause);
    unset( $a_where_clause );
    // end - build where clause
    
    $s_select_query = "SELECT {$s_select_column} FROM `{$wpdb->prefix}x2b_posts` WHERE {$s_where_clause} ORDER BY `list_order` ASC LIMIT $n_offset, $n_posts_per_page";

    $o_cache_handler = new \X2board\Includes\Classes\CacheFileDisk();
    $o_cache_handler->set_storage_label( 'search' );
    $o_cache_handler->set_cache_key( $s_select_query );
    $a_retrieved_post = $o_cache_handler->get();
    if( ! $a_retrieved_post ) {  // load db
// error_log(print_r('load x2b db', true));
        $a_posts = $wpdb->get_results( $s_select_query );
        // manipulate
        $a_board_permalink[$n_board_id] = esc_url(site_url() . '/' . urlencode(urldecode(get_post($n_board_id)->post_name)));
        $s_board_permalink = esc_url(site_url() . '/' . urlencode(urldecode(get_post($n_board_id)->post_name)));
        
        $n_subject_trim_length = isset($o_param->n_subject_trim_length) ? $o_param->n_subject_trim_length : 20;
        $n_content_trim_length = isset($o_param->n_content_trim_length) ? $o_param->n_content_trim_length : 100;
        $s_date_format = isset($o_param->s_date_format) ? $o_param->s_date_format : 'Y-m-d H:i:s';
        $a_retrieved_post = array();
        $a_category_info = array();
        foreach( $a_posts as $o_rec ) {
            $o_new_rec = new \stdClass();
            $o_new_rec->title = wp_trim_words(strip_tags($o_rec->title), $n_subject_trim_length, '...');
            $o_new_rec->content = wp_trim_words(strip_tags($o_rec->content), $n_content_trim_length, '...');
            $o_new_rec->permalink = $s_board_permalink . '/' . $o_rec->post_id;
            if(intval($o_rec->category_id) != 0 ) {
                if( isset( $a_category_info[$o_rec->category_id] )) {
                    $s_category_title = $a_category_info[$o_rec->category_id];
                }
                else {
                    $s_category_title = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}x2b_categories` WHERE `category_id` = $o_rec->category_id AND `deleted`='N'");
                    $a_category_info[$o_rec->category_id] = $s_category_title;
                }
            }
            else {
                $s_category_title = __('lbl_default_category', X2B_DOMAIN);
            }
            $o_new_rec->category_title = $s_category_title;
            $o_new_rec->readed_count = $o_rec->readed_count;
            $o_new_rec->regdate  = date_format(date_create($o_rec->regdate_dt), $s_date_format);
            $a_retrieved_post[] = $o_new_rec;
        }
        unset($a_posts);
        unset($a_board_permalink);
        $o_cache_handler->put( $a_retrieved_post );
    }
    unset( $o_cache_handler );
    return $a_retrieved_post;
}
