<?php
/**
 * Default settings.
 *
 * Functions to register the default settings of the plugin.
 *
 * @link https://singleview.co.kr 
 * @since 2.6.0
 *
 * @package x2board
 * @subpackage 
 */

namespace X2board\Includes\Admin\Tpl;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


/**
 * Retrieve the array of plugin settings
 *
 * @since 2.6.0
 *
 * @return array Settings array
 */
function x2b_get_registered_settings() {

	$crp_settings = array(
		'general'   => x2b_settings_general(),
		'list'      => x2b_settings_list(),
		'output'    => x2b_settings_output(),
		'thumbnail' => x2b_settings_thumbnail(),
		'styles'    => x2b_settings_styles(),
		'feed'      => x2b_settings_feed(),
	);

	/**
	 * Filters the settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array   $crp_settings Settings array
	 */
	return apply_filters( 'x2b_registered_settings', $crp_settings );
}


/**
 * Retrieve the array of General settings
 *
 * @since 2.6.0
 *
 * @return array General settings array
 */
function x2b_settings_general() {
// error_log(print_r('x2b_settings_general', true));
	$settings = array(
		'x2board_title'					=> array(
			'id'      => 'x2board_title',
			'name'    => esc_html__( '게시판 제목', 'x2board' ),
			'desc'    => esc_html__( '관리용 게시판 제목을 입력하세요. 방문자에게는 표시되지 않아요.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'wp_page_title'					=> array(
			'id'      => 'wp_page_title',
			'name'    => esc_html__( 'WP 페이지 제목', 'x2board' ),
			'desc'    => esc_html__( 'WP 페이지 제목은 브라우저 제목으로 표시되요.', 'x2board' ),
			'type'    => 'text',
			'options' => false,
		),
		'x2board_skin'					=> array(
			'id'      => 'x2board_skin',
			'name'    => esc_html__( '게시판 스킨', 'x2board' ),
			'desc'    => esc_html__( '게시판 스킨을 선택하세요.', 'x2board' ),
			'type'    => 'select',
			'options' => array(
				'default'            => esc_html__( 'default', 'x2board' ),
				'sktechbook5'        => esc_html__( 'sktechbook5', 'x2board' ),
			),
		),
		'x2board_list_per_page'		      => array(
			'id'      => 'x2board_list_per_page',
			'name'    => esc_html__( '페이지 당 목록 수', 'x2board' ),
			'desc'    => esc_html__( '한 페이지에 표시될 글 수를 지정할 수 있습니다. (기본 20개)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'x2board_search_list_count'		      => array(
			'id'      => 'x2board_search_list_count',
			'name'    => esc_html__( '검색 목록 수', 'x2board' ),
			'desc'    => esc_html__( '검색, 카테고리 선택 등을 할 경우 표시될 글 수를 지정할 수 있습니다. (기본 20개)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'x2board_page_count'		      => array(
			'id'      => 'x2board_page_count',
			'name'    => esc_html__( '페이지 수', 'x2board' ),
			'desc'    => esc_html__( '목록 하단, 페이지를 이동하는 링크 수를 지정할 수 있습니다. (기본 10개)', 'x2board' ),
			'type'    => 'number',
			'options' => '10',
		),
		'x2board_auto_subject_length'		      => array(
			'id'      => 'x2board_auto_subject_length',
			'name'    => esc_html__( '자동 제목 문자수', 'x2board' ),
			'desc'    => esc_html__( '빈 제목 입력되면 본문 첫줄에서 추출하는 문자열 수를 지정합니다. (기본 20자)', 'x2board' ),
			'type'    => 'number',
			'options' => '20',
		),
		'x2board_header_text'     => array(
			'id'      => 'x2board_header_text',
			'name'    => esc_html__( '상단 내용', 'x2board' ),
			'desc'    => esc_html__( '콘텐츠 상단에 표시되는 내용입니다. (HTML 태그 사용 가능)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false, // esc_html__( 'No related posts found', 'x2board' ),
		),
		'x2board_footer_text'     => array(
			'id'      => 'x2board_footer_text',
			'name'    => esc_html__( '하단 내용', 'x2board' ),
			'desc'    => esc_html__( '콘텐츠 하단에 표시되는 내용입니다. (HTML 태그 사용 가능)', 'x2board' ),
			'type'    => 'textarea',
			'options' => false,
		),

		// 'cache'                        => array(
		// 	'id'      => 'cache',
		// 	'name'    => esc_html__( 'Cache HTML output', 'x2board' ),
		// 	'desc'    => esc_html__( 'Only works if the previous option is disabled. Enabling this will cache the entire HTML generated when the post is visited the first time. The cache is cleaned when you save this page. Highly recommended particularly on busy sites. Default is true.', 'x2board' ),
		// 	'type'    => 'checkbox',
		// 	'options' => true,
		// ),
		// 'add_to'                       => array(
		// 	'id'      => 'add_to',
		// 	'name'    => esc_html__( 'Automatically add related posts to', 'x2board' ),
		// 	/* translators: 1: Code. */
		// 	'desc'    => sprintf( esc_html__( 'If you choose to disable this, please add %1$s to your template file where you want it displayed', 'x2board' ), "<code>&lt;?php if ( function_exists( 'echo_crp' ) ) { echo_crp(); } ?&gt;</code>" ),
		// 	'type'    => 'multicheck',
		// 	'default' => array(
		// 		'single' => 'single',
		// 		'page'   => 'page',
		// 	),
		// 	'options' => array(
		// 		'single'            => esc_html__( 'Posts', 'x2board' ),
		// 		'page'              => esc_html__( 'Pages', 'x2board' ),
		// 		'home'              => esc_html__( 'Home page', 'x2board' ),
		// 		'feed'              => esc_html__( 'Feeds', 'x2board' ),
		// 		'category_archives' => esc_html__( 'Category archives', 'x2board' ),
		// 		'tag_archives'      => esc_html__( 'Tag archives', 'x2board' ),
		// 		'other_archives'    => esc_html__( 'Other archives', 'x2board' ),
		// 	),
		// ),
	);

	/**
	 * Filters the General settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings General settings array
	 */
	return apply_filters( 'x2b_settings_general', $settings );
}


/**
 * Retrieve the array of Output settings
 *
 * @since 2.6.0
 *
 * @return array Output settings array
 */
function x2b_settings_output() {

	$settings = array(
		'title'                 => array(
			'id'      => 'title',
			'name'    => esc_html__( 'Heading of posts', 'x2board' ),
			'desc'    => esc_html__( 'Displayed before the list of the posts as a master heading', 'x2board' ),
			'type'    => 'text',
			'options' => '<h2>' . esc_html__( 'Related Posts', 'x2board' ) . ':</h2>',
			'size'    => 'large',
		),
		'blank_output'          => array(
			'id'      => 'blank_output',
			'name'    => esc_html__( 'Show when no posts are found', 'x2board' ),
			/* translators: 1: Code. */
			'desc'    => '',
			'type'    => 'radio',
			'default' => 'blank',
			'options' => array(
				'blank'       => esc_html__( 'Blank output', 'x2board' ),
				'custom_text' => esc_html__( 'Display custom text', 'x2board' ),
			),
		),
		'blank_output_text'     => array(
			'id'      => 'blank_output_text',
			'name'    => esc_html__( 'Custom text', 'x2board' ),
			'desc'    => esc_html__( 'Enter the custom text that will be displayed if the second option is selected above.', 'x2board' ),
			'type'    => 'textarea',
			'options' => esc_html__( 'No related posts found', 'x2board' ),
		),
		'show_excerpt'          => array(
			'id'      => 'show_excerpt',
			'name'    => esc_html__( 'Show post excerpt', 'x2board' ),
			'desc'    => esc_html__( 'If the post does not have an excerpt, the plugin will automatically create one containing the number of words specified in the next option.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'excerpt_length'        => array(
			'id'      => 'excerpt_length',
			'name'    => esc_html__( 'Length of excerpt (in words)', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '10',
			'min'     => '0',
			'size'    => 'small',
		),
		'show_date'             => array(
			'id'      => 'show_date',
			'name'    => esc_html__( 'Show date', 'x2board' ),
			'desc'    => esc_html__( 'Displays the date of the post. Uses the same date format set in General Options.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'show_author'           => array(
			'id'      => 'show_author',
			'name'    => esc_html__( 'Show author', 'x2board' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'options' => false,
		),
		'show_primary_term'     => array(
			'id'      => 'show_primary_term',
			'name'    => esc_html__( 'Show primary category/term', 'x2board' ),
			'desc'    => esc_html__( 'This will display the primary category/term. This is usually set via your SEO plugin and will default to the first category/term returned by WordPress', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'title_length'          => array(
			'id'      => 'title_length',
			'name'    => esc_html__( 'Limit post title length (in characters)', 'x2board' ),
			'desc'    => esc_html__( 'Any title longer than the number of characters set above will be cut and appended with an ellipsis (&hellip;)', 'x2board' ),
			'type'    => 'number',
			'options' => '60',
			'min'     => '0',
			'size'    => 'small',
		),
		'link_new_window'       => array(
			'id'      => 'link_new_window',
			'name'    => esc_html__( 'Open links in new window', 'x2board' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'options' => false,
		),
		'link_nofollow'         => array(
			'id'      => 'link_nofollow',
			'name'    => esc_html__( 'Add nofollow to links', 'x2board' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'options' => false,
		),
		'exclude_output_header' => array(
			'id'   => 'exclude_output_header',
			'name' => '<h3>' . esc_html__( 'Exclusion settings', 'x2board' ) . '</h3>',
			'desc' => '',
			'type' => 'header',
		),
		'exclude_on_post_ids'   => array(
			'id'      => 'exclude_on_post_ids',
			'name'    => esc_html__( 'Exclude display on these posts', 'x2board' ),
			'desc'    => esc_html__( 'Comma separated list of post, page or custom post type IDs. e.g. 188,320,500', 'x2board' ),
			'type'    => 'numbercsv',
			'options' => '',
		),
		'exclude_on_post_types' => array(
			'id'      => 'exclude_on_post_types',
			'name'    => esc_html__( 'Exclude display on these post types', 'x2board' ),
			'desc'    => esc_html__( 'The related posts will not display on any of the above selected post types.', 'x2board' ),
			'type'    => 'posttypes',
			'options' => '',
		),
		'exclude_on_cat_slugs'  => array(
			'id'          => 'exclude_on_cat_slugs',
			'name'        => esc_html__( 'Exclude on Terms', 'x2board' ),
			'desc'        => esc_html__( 'The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. This field requires a specific format as displayed by the autocomplete.', 'x2board' ),
			'type'        => 'csv',
			'options'     => '',
			'size'        => 'large',
			'field_class' => 'category_autocomplete',
		),
		'html_wrapper_header'   => array(
			'id'   => 'html_wrapper_header',
			'name' => '<h3>' . esc_html__( 'HTML to display', 'x2board' ) . '</h3>',
			'desc' => '',
			'type' => 'header',
		),
		'before_list'           => array(
			'id'      => 'before_list',
			'name'    => esc_html__( 'Before the list of posts', 'x2board' ),
			'desc'    => '',
			'type'    => 'text',
			'options' => '<ul>',
		),
		'after_list'            => array(
			'id'      => 'after_list',
			'name'    => esc_html__( 'After the list of posts', 'x2board' ),
			'desc'    => '',
			'type'    => 'text',
			'options' => '</ul>',
		),
		'before_list_item'      => array(
			'id'      => 'before_list_item',
			'name'    => esc_html__( 'Before each list item', 'x2board' ),
			'desc'    => '',
			'type'    => 'text',
			'options' => '<li>',
		),
		'after_list_item'       => array(
			'id'      => 'after_list_item',
			'name'    => esc_html__( 'After each list item', 'x2board' ),
			'desc'    => '',
			'type'    => 'text',
			'options' => '</li>',
		),
	);

	/**
	 * Filters the Output settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Output settings array
	 */
	return apply_filters( 'x2b_settings_output', $settings );
}


/**
 * Retrieve the array of List settings
 *
 * @since 2.6.0
 *
 * @return array List settings array
 */
function x2b_settings_list() {

	$settings = array(
		'limit'                  => array(
			'id'      => 'limit',
			'name'    => esc_html__( 'Number of posts to display', 'x2board' ),
			'desc'    => esc_html__( 'Maximum number of posts that will be displayed in the list. This option is used if you do not specify the number of posts in the widget or shortcodes', 'x2board' ),
			'type'    => 'number',
			'options' => '6',
			'min'     => '0',
			'size'    => 'small',
		),
		'daily_range'            => array(
			'id'      => 'daily_range',
			'name'    => esc_html__( 'Related posts should be newer than', 'x2board' ),
			'desc'    => esc_html__( 'This sets the cut-off period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.', 'x2board' ),
			'type'    => 'number',
			'options' => '0',
			'min'     => '0',
		),
		'ordering'               => array(
			'id'      => 'ordering',
			'name'    => esc_html__( 'Order posts', 'x2board' ),
			'desc'    => '',
			'type'    => 'radio',
			'default' => 'relevance',
			'options' => crp_get_orderings(),
		),
		'random_order'           => array(
			'id'      => 'random_order',
			'name'    => esc_html__( 'Randomize posts', 'x2board' ),
			'desc'    => esc_html__( 'This shuffles the selected related posts. If you select to order by date in the previous option, then the related posts will first be sorted by date and the selected ones are shuffled. Does not work if Cache HTML output is enabled.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'match_content'          => array(
			'id'      => 'match_content',
			'name'    => esc_html__( 'Related posts based on title and content', 'x2board' ),
			'desc'    => esc_html__( 'If unchecked, only posts titles are used. Enable the cache if enabling this option for better performance. Each site is different, so toggle this option to see which setting gives you better quality related posts.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
		'match_content_words'    => array(
			'id'      => 'match_content_words',
			'name'    => esc_html__( 'Limit content to be compared', 'x2board' ),
			/* translators: 1: Number. */
			'desc'    => sprintf( esc_html__( 'This sets the maximum words of the content that will be matched. Set to 0 for no limit. Max value: %1$s. Only applies if you activate the above option.', 'x2board' ), CRP_MAX_WORDS ),
			'type'    => 'number',
			'options' => '0',
			'min'     => '0',
			'max'     => CRP_MAX_WORDS,
		),
		'post_types'             => array(
			'id'      => 'post_types',
			'name'    => esc_html__( 'Post types to include', 'x2board' ),
			'desc'    => esc_html__( 'At least one option should be selected above. Select which post types you want to include in the list of posts. This field can be overridden using a comma separated list of post types when using the manual display.', 'x2board' ),
			'type'    => 'posttypes',
			'options' => 'post,page',
		),
		'same_post_type'         => array(
			'id'      => 'same_post_type',
			'name'    => esc_html__( 'Limit to same post type', 'x2board' ),
			'desc'    => esc_html__( 'If checked, the related posts will only be selected from the same post type of the current post.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'same_author'            => array(
			'id'      => 'same_author',
			'name'    => esc_html__( 'Limit to same author', 'x2board' ),
			'desc'    => esc_html__( 'If checked, the related posts will only be selected from the same author of the current post.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'primary_term'           => array(
			'id'      => 'primary_term',
			'name'    => esc_html__( 'Limit to same primary term', 'x2board' ),
			'desc'    => esc_html__( 'If enabled, then it will only select posts from the primary category/term. This is usually set via your SEO plugin and will default to the first category/term returned by WordPress', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'same_taxes'             => array(
			'id'      => 'same_taxes',
			'name'    => esc_html__( 'Only from same', 'x2board' ),
			'desc'    => esc_html__( 'Limit the related posts only to the categories, tags, and/or taxonomies of the current post.', 'x2board' ),
			'type'    => 'taxonomies',
			'options' => '',
		),
		'match_all'              => array(
			'id'      => 'match_all',
			'name'    => esc_html__( 'Match all taxonomies', 'x2board' ),
			'desc'    => esc_html__( 'If enabled, then it will only select posts that match all the above selected taxonomies. This can result in no related posts being found.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'no_of_common_terms'     => array(
			'id'      => 'no_of_common_terms',
			'name'    => esc_html__( 'Number of common terms', 'x2board' ),
			'desc'    => esc_html__( 'Enter the minimum number of common terms that have to be matched before a post is considered related.', 'x2board' ),
			'type'    => 'number',
			'options' => '1',
			'min'     => '1',
		),
		'related_meta_keys'      => array(
			'id'      => 'related_meta_keys',
			'name'    => esc_html__( 'Related Meta Keys', 'x2board' ),
			'desc'    => esc_html__( 'Enter a comma-separated list of meta keys. Posts that match the same value of the meta key are displayed before the other related posts', 'x2board' ),
			'type'    => 'csv',
			'options' => '',
			'size'    => 'large',
		),
		'exclude_post_ids'       => array(
			'id'      => 'exclude_post_ids',
			'name'    => esc_html__( 'Post/page IDs to exclude', 'x2board' ),
			'desc'    => esc_html__( 'Comma-separated list of post or page IDs to exclude from the list. e.g. 188,320,500', 'x2board' ),
			'type'    => 'numbercsv',
			'options' => '',
		),
		'exclude_cat_slugs'      => array(
			'id'          => 'exclude_cat_slugs',
			'name'        => esc_html__( 'Exclude Terms', 'x2board' ),
			'desc'        => esc_html__( 'The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. This field requires a specific format as displayed by the autocomplete.', 'x2board' ),
			'type'        => 'csv',
			'options'     => '',
			'size'        => 'large',
			'field_class' => 'category_autocomplete',
		),
		'exclude_categories'     => array(
			'id'       => 'exclude_categories',
			'name'     => esc_html__( 'Exclude Term Taxonomy IDs', 'x2board' ),
			'desc'     => esc_html__( 'This is a readonly field that is automatically populated based on the above input when the settings are saved. These might differ from the IDs visible in the Categories page which use the term_id. Contextual Related Posts uses the term_taxonomy_id which is unique to this taxonomy.', 'x2board' ),
			'type'     => 'text',
			'options'  => '',
			'readonly' => true,
		),
		'disable_contextual'     => array(
			'id'      => 'disable_contextual',
			'name'    => esc_html__( 'Disable contextual matching', 'x2board' ),
			'desc'    => esc_html__( 'Selecting this option will turn off contextual matching. This is only useful if you activate the option: "Only from same" from the General tab. Otherwise, you will end up with the same set of related posts on all pages with no relevance.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'disable_contextual_cpt' => array(
			'id'      => 'disable_contextual_cpt',
			'name'    => esc_html__( 'Disable contextual matching ONLY on attachments and custom post types', 'x2board' ),
			'desc'    => esc_html__( 'Applies only if the previous option is checked. Selecting this option will retain contextual matching for posts and pages but disable this on any custom post types.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
	);

	/**
	 * Filters the List settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings List settings array
	 */
	return apply_filters( 'x2b_settings_list', $settings );
}


/**
 * Retrieve the array of Thumbnail settings
 *
 * @since 2.6.0
 *
 * @return array Thumbnail settings array
 */
function x2b_settings_thumbnail() {

	$settings = array(
		'post_thumb_op'      => array(
			'id'      => 'post_thumb_op',
			'name'    => esc_html__( 'Location of the post thumbnail', 'x2board' ),
			'desc'    => '',
			'type'    => 'radio',
			'default' => 'text_only',
			'options' => array(
				'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'x2board' ),
				'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'x2board' ),
				'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'x2board' ),
				'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'x2board' ),
			),
		),
		'thumb_size'         => array(
			'id'      => 'thumb_size',
			'name'    => esc_html__( 'Thumbnail size', 'x2board' ),
			'desc'    => esc_html__( 'You can choose from existing image sizes above or create a custom size. If you have chosen Custom size above, then enter the width, height and crop settings below. For best results, use a cropped image. If you change the width and/or height below, existing images will not be automatically resized.' ) . '<br />' . sprintf(
				/* translators: 1: OTF Regenerate plugin link, 2: Force regenerate plugin link. */
				esc_html__( 'I recommend using %1$s or %2$s to regenerate all image sizes.', 'x2board' ),
				'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=otf-regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">OTF Regenerate Thumbnails</a>',
				'<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=regenerate-thumbnails&amp;TB_iframe=true&amp;width=600&amp;height=550' ) ) . '" class="thickbox">Regenerate Thumbnails</a>'
			),
			'type'    => 'thumbsizes',
			'default' => 'crp_thumbnail',
			'options' => crp_get_all_image_sizes(),
		),
		'thumb_width'        => array(
			'id'      => 'thumb_width',
			'name'    => esc_html__( 'Thumbnail width', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '150',
			'min'     => '0',
			'size'    => 'small',
		),
		'thumb_height'       => array(
			'id'      => 'thumb_height',
			'name'    => esc_html__( 'Thumbnail height', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '150',
			'min'     => '0',
			'size'    => 'small',
		),
		'thumb_crop'         => array(
			'id'      => 'thumb_crop',
			'name'    => esc_html__( 'Hard crop thumbnails', 'x2board' ),
			'desc'    => esc_html__( 'Check this box to hard crop the thumbnails. i.e. force the width and height above vs. maintaining proportions.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
		'thumb_create_sizes' => array(
			'id'      => 'thumb_create_sizes',
			'name'    => esc_html__( 'Generate thumbnail sizes', 'x2board' ),
			'desc'    => esc_html__( 'If you select this option and Custom size is selected above, the plugin will register the image size with WordPress to create new thumbnails. Does not update old images as explained above.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
		'thumb_html'         => array(
			'id'      => 'thumb_html',
			'name'    => esc_html__( 'Thumbnail size attributes', 'x2board' ),
			'desc'    => '',
			'type'    => 'radio',
			'default' => 'html',
			'options' => array(
				/* translators: %s: Code. */
				'css'  => sprintf( esc_html__( 'Use CSS to set the width and height: e.g. %s', 'x2board' ), '<code>style="max-width:250px;max-height:250px"</code>' ),
				/* translators: %s: Code. */
				'html' => sprintf( esc_html__( 'Use HTML attributes to set the width and height: e.g. %s', 'x2board' ), '<code>width="250" height="250"</code>' ),
				'none' => esc_html__( 'No width or height set. You will need to use external styles to force any width or height of your choice.', 'x2board' ),
			),
		),
		'thumb_meta'         => array(
			'id'      => 'thumb_meta',
			'name'    => esc_html__( 'Thumbnail meta field name', 'x2board' ),
			'desc'    => esc_html__( 'The value of this field should contain the URL of the image and can be set in the metabox in the Edit Post screen', 'x2board' ),
			'type'    => 'text',
			'options' => 'post-image',
		),
		'scan_images'        => array(
			'id'      => 'scan_images',
			'name'    => esc_html__( 'Get first image', 'x2board' ),
			'desc'    => esc_html__( 'The plugin will fetch the first image in the post content if this is enabled. This can slow down the loading of your page if the first image in the followed posts is large in file-size.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
		'thumb_default_show' => array(
			'id'      => 'thumb_default_show',
			'name'    => esc_html__( 'Use default thumbnail?', 'x2board' ),
			'desc'    => esc_html__( 'If checked, when no thumbnail is found, show a default one from the URL below. If not checked and no thumbnail is found, no image will be shown.', 'x2board' ),
			'type'    => 'checkbox',
			'options' => true,
		),
		'thumb_default'      => array(
			'id'      => 'thumb_default',
			'name'    => esc_html__( 'Default thumbnail', 'x2board' ),
			'desc'    => esc_html__( 'Enter the full URL of the image that you wish to display if no thumbnail is found. This image will be displayed below.', 'x2board' ),
			'type'    => 'text',
			'options' => CRP_PLUGIN_URL . 'default.png',
			'size'    => 'large',
		),
	);

	/**
	 * Filters the Thumbnail settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Thumbnail settings array
	 */
	return apply_filters( 'x2b_settings_thumbnail', $settings );
}


/**
 * Retrieve the array of Styles settings
 *
 * @since 2.6.0
 *
 * @return array Styles settings array
 */
function x2b_settings_styles() {

	$settings = array(
		'crp_styles' => array(
			'id'      => 'crp_styles',
			'name'    => esc_html__( 'Related Posts style', 'x2board' ),
			'desc'    => '',
			'type'    => 'radiodesc',
			'default' => 'rounded_thumbs',
			'options' => crp_get_styles(),
		),
		'custom_css' => array(
			'id'          => 'custom_css',
			'name'        => esc_html__( 'Custom CSS', 'x2board' ),
			/* translators: 1: Opening a tag, 2: Closing a tag, 3: Opening code tage, 4. Closing code tag. */
			'desc'        => sprintf( esc_html__( 'Do not include %3$sstyle%4$s tags. Check out the %1$sFAQ%2$s for available CSS classes to style.', 'x2board' ), '<a href="' . esc_url( 'http://wordpress.org/plugins/x2board/faq/' ) . '" target="_blank">', '</a>', '<code>', '</code>' ),
			'type'        => 'css',
			'options'     => '',
			'field_class' => 'codemirror_css',
		),
	);

	/**
	 * Filters the Styles settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Styles settings array
	 */
	return apply_filters( 'x2b_settings_styles', $settings );
}


/**
 * Retrieve the array of Feed settings
 *
 * @since 2.6.0
 *
 * @return array Feed settings array
 */
function x2b_settings_feed() {

	$settings = array(
		'feed_options_desc'  => array(
			'id'   => 'feed_options_desc',
			'name' => '<strong>' . esc_html__( 'About this tab', 'x2board' ) . '</strong>',
			'desc' => esc_html__( 'Below options override the related posts settings for your blog feed. These only apply if you have selected to add related posts to Feeds in the General Options tab. It is recommended to not display thumbnails as there is no easy way to style the related posts in the feed.', 'x2board' ),
			'type' => 'descriptive_text',
		),
		'limit_feed'         => array(
			'id'      => 'limit_feed',
			'name'    => esc_html__( 'Number of posts to display', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '5',
			'min'     => '0',
			'size'    => 'small',
		),
		'show_excerpt_feed'  => array(
			'id'      => 'show_excerpt_feed',
			'name'    => esc_html__( 'Show post excerpt', 'x2board' ),
			'desc'    => '',
			'type'    => 'checkbox',
			'options' => false,
		),
		'post_thumb_op_feed' => array(
			'id'      => 'post_thumb_op_feed',
			'name'    => esc_html__( 'Location of the post thumbnail', 'x2board' ),
			'desc'    => '',
			'type'    => 'radio',
			'default' => 'text_only',
			'options' => array(
				'inline'      => esc_html__( 'Display thumbnails inline with posts, before title', 'x2board' ),
				'after'       => esc_html__( 'Display thumbnails inline with posts, after title', 'x2board' ),
				'thumbs_only' => esc_html__( 'Display only thumbnails, no text', 'x2board' ),
				'text_only'   => esc_html__( 'Do not display thumbnails, only text', 'x2board' ),
			),
		),
		'thumb_width_feed'   => array(
			'id'      => 'thumb_width_feed',
			'name'    => esc_html__( 'Thumbnail width', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '250',
			'min'     => '0',
			'size'    => 'small',
		),
		'thumb_height_feed'  => array(
			'id'      => 'thumb_height_feed',
			'name'    => esc_html__( 'Thumbnail height', 'x2board' ),
			'desc'    => '',
			'type'    => 'number',
			'options' => '250',
			'min'     => '0',
			'size'    => 'small',
		),
	);

	/**
	 * Filters the Feed settings array
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings Feed settings array
	 */
	return apply_filters( 'x2b_settings_feed', $settings );
}


/**
 * Upgrade pre v2.5.0 settings.
 *
 * @since 2.6.0
 * @return array Settings array
 */
function x2b_upgrade_settings() {
	return array();
}


/**
 * Get the various styles.
 *
 * @since 2.6.0
 * @return array Style options.
 */
function x2b_get_styles() {

	$styles = array(
		array(
			'id'          => 'no_style',
			'name'        => esc_html__( 'No styles', 'x2board' ),
			'description' => esc_html__( 'Select this option if you plan to add your own styles', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'text_only',
			'name'        => esc_html__( 'Text only', 'x2board' ),
			'description' => esc_html__( 'Disable thumbnails and no longer include the default style sheet', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'rounded_thumbs',
			'name'        => esc_html__( 'Rounded thumbnails', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/rounded-thumbs.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Enabling this option will turn on the thumbnails. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'masonry',
			'name'        => esc_html__( 'Masonry', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/masonry.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Enables a masonry style layout similar to one made famous by Pinterest.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'grid',
			'name'        => esc_html__( 'Grid', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/grid.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Uses CSS Grid for display. Might not work on older browsers.', 'x2board' ) . '<br />',
		),
		array(
			'id'          => 'thumbs_grid',
			'name'        => esc_html__( 'Rounded thumbnails with CSS grid', 'x2board' ),
			'description' => '<br /><img src="' . esc_url( plugins_url( 'includes/admin/images/thumbs-grid.png', CRP_PLUGIN_FILE ) ) . '" width="500" /> <br />' . esc_html__( 'Uses CSS grid. It will also turn off the display of the author, excerpt and date if already enabled. Disabling this option will not revert any settings.', 'x2board' ) . '<br />',
		),
	);

	/**
	 * Filter the array containing the styles to add your own.
	 *
	 * @since 2.6.0
	 *
	 * @param array $styles Different styles.
	 */
	return apply_filters( 'x2b_get_styles', $styles );
}

/**
 * Get the various order settings.
 *
 * @since 2.8.0
 * @return array Order settings.
 */
function x2b_get_orderings() {

	$orderings = array(
		'relevance' => esc_html__( 'By relevance', 'x2board' ),
		'random'    => esc_html__( 'Randomly', 'x2board' ),
		'date'      => esc_html__( 'By date', 'x2board' ),
	);

	/**
	 * Filter the array containing the order settings.
	 *
	 * @since 2.8.0
	 *
	 * @param array $orderings Order settings.
	 */
	return apply_filters( 'x2b_get_orderings', $orderings );
}
