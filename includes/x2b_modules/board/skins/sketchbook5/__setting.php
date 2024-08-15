<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

$mi = $skin_vars;
// var_dump($current_module_info);
if(!isset($mi->content_cut_size) || strlen($mi->content_cut_size)==0) $mi->content_cut_size = 240;
if(!isset($mi->duration_new) || strlen($mi->duration_new)==0) $mi->duration_new = 24;
if(!isset($mi->colorset) || strlen($mi->colorset)==0) $mi->colorset = 'white';
if(!isset($mi->preview_tx) || strlen($mi->preview_tx)==0)  $mi->preview_tx = 150;
if(!isset($mi->extra_num) || strlen($mi->extra_num)==0) $mi->extra_num = 1;
if(!isset($mi->extra_num2) || strlen($mi->extra_num2)==0) $mi->extra_num2 = 1;
if(!isset($mi->thumbnail_width) || strlen($mi->thumbnail_width)==0)  $mi->thumbnail_width  = 90;
if(!isset($mi->thumbnail_height) || strlen($mi->thumbnail_height)==0) $mi->thumbnail_height = 90;
if(!isset($mi->zine_thumb_width) || strlen($mi->zine_thumb_width)==0) $mi->zine_thumb_width = 90;
if(!isset($mi->zine_thumb_height) || strlen($mi->zine_thumb_height)==0) $mi->zine_thumb_height = 90;
if(!isset($mi->zine_margin) || strlen($mi->zine_margin)==0) $mi->zine_margin = 10;
if(!isset($mi->cmt_wrt) || strlen($mi->cmt_wrt)==0) $mi->cmt_wrt = 'simple';
if(!isset($mi->img_insert_align) || strlen($mi->img_insert_align)==0) $mi->img_insert_align = 'center';

$mi->duration_new = 3600 * $mi->duration_new;  // 60*60 == 3600

// 체크박스 옵션 -->
// $mi->btm_mn = unserialize($mi->btm_mn);
// $mi->preview = unserialize($mi->preview);
// $mi->ext_img = unserialize($mi->ext_img);
// $mi->cmt_count = unserialize($mi->cmt_count);
// $mi->wrt_opt = unserialize($mi->wrt_opt);
// $mi->viewer_itm = unserialize($mi->viewer_itm);
// $mi->rd_blog_itm = unserialize($mi->rd_blog_itm);

if(isset($_COOKIE['bd_font']) && !$mi->font_btn) $mi->font = $_COOKIE['bd_font'];
if(isset($_COOKIE['bd_editor'])) $mi->cmt_wrt = $_COOKIE['bd_editor'];
if(isset($_COOKIE['cookie_viewer_with'])) $mi->viewer_with = '';

$mi->shadow = null;
if(isset($mi->custom_color)) $mi->color = $mi->custom_color;
if(isset($mi->custom_shadow)) $mi->shadow = $mi->custom_shadow;
if(!isset($mi->color)) $mi->color = 333333;
if(!isset($mi->color)) $mi->shadow = '#999';
// var_dump($mi->btm_mn);
if($mi->colorset=='black') $mi->color = EEEEEE;
if($mi->colorset=='black') $mi->shadow = '#000';
if($mi->color=='87cefa') $mi->shadow = '#5f9ea0'; // 라이트스카이블루 -->
if($mi->color=='6495ed') $mi->shadow = '#4682b4'; // 콘플라워블루 -->
if($mi->color=='4169e1' || $mi->color=='4682b4') $mi->shadow = '#646496'; // 로얄블루, 스틸블루(비표준) -->
if($mi->color=='adff2f') $mi->shadow = '#80E12A'; // 그린옐로우(비표준) -->
if($mi->color=='80E12A') $mi->shadow = '#4BAF4B'; // 초록(비표준) -->
if($mi->color=='ffb6c1') $mi->shadow = '#e9967a'; // 라이트핑크(비표준) -->
if($mi->color=='ff69b4') $mi->shadow = '#db7093'; // 핫핑크 -->
if($mi->color=='ff1493') $mi->shadow = '#C39'; // 딥핑크 -->
if($mi->color=='ffd700') $mi->shadow = '#daa520'; // 골드 -->
if($mi->color=='ffa500') $mi->shadow = '#f08080'; // 오렌지 -->
if($mi->color=='ff7f50') $mi->shadow = '#d2691e'; // 코랄 -->
if($mi->color=='ff6347') $mi->shadow = '#dc143c'; // 토마토 -->
if($mi->color=='bc8f8f') $mi->shadow = '#A36464'; // 장미빛(비표준) -->
if($mi->color=='ee82ee') $mi->shadow = '#da70d6'; // 바이올렛 -->
if($mi->color=='c71585') $mi->shadow = '#8b0000'; // 미디엄바이올렛 -->
if($mi->color=='db7093') $mi->shadow = '#cd5c5c'; // 팔레바이올렛 -->

if(wp_is_mobile()) {
	$mi->bubble = 'N';
	$mi->hover = 'N';
	$mi->font_btn = 'N';
	$mi->srch_window = 'N';
	$mi->zine_hover = 'N';
	$mi->rd_nav_side = 'N';
	if($mi->cmt_wrt=='editor') $mi->cmt_wrt = 'simple';
	if($mi->to_sns!='N') $mi->to_sns = 3;
}

// $mi->default_style = null;
if(isset($listStyle)) {
    if($listStyle=='list') { 
        $mi->default_style = 'list';
    }
    elseif($listStyle=='faq') {
        $mi->default_style = 'faq';
    }
    // elseif($listStyle=='gallery') {
    //     $mi->default_style = 'gallery';
    // }
    // elseif($listStyle=='guest') {
    //     $mi->default_style = 'guest';
    // }
    // elseif($listStyle=='webzine') {
    //     $mi->default_style = 'webzine';
    // }
    // elseif($listStyle=='cloud_gall') {
    //     $mi->default_style = 'cloud_gall';
    // }
    // elseif($listStyle=='blog') {
    //     $mi->default_style = 'blog';
    // }
    // elseif($listStyle=='viewer') {
    //     $mi->default_style = 'viewer';
    // }
}
elseif(!in_array($mi->default_style,array('list','gallery','guest','faq'))) {  // ,'webzine','cloud_gall','blog','viewer'
    $mi->default_style = 'list';
}

// if(wp_is_mobile()) {
//     <load target="../../../../common/js/jquery.min.js" index="-100006" />
//     <load target="../../../../common/js/xe.min.js" index="-100006" />
//     <load target="../../../../common/js/x.min.js" index="-100006" />
//     <load target="../../tpl/js/board.js" />
// }

// <load target="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
// wp_enqueue_style('x2board-sketchbook5-fontawsome', "https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css", array(), X2B_VERSION, 'all');
// <load target="css/board.css" />
// wp_enqueue_style('x2board-sketchbook5-board', $skin_url."/css/board.css", array(), X2B_VERSION, 'all');
// <load target="css/ie8.css" targetie="lt IE 9" />
// wp_enqueue_style('x2board-sketchbook5-ie8', $skin_url."/css/ie8.css", array(), X2B_VERSION, 'lt IE 9');
// <load cond="$mi->colorset=='black'" target="css/black.css" />
// if($mi->colorset=='black') {
//     wp_enqueue_style('x2board-sketchbook5-black-board', $skin_url."/css/black.css", array(), X2B_VERSION, 'all');
// }

// 커스텀 파일 넣기 -->
// <include cond="is_dir(_XE_PATH_.'/modules/board/skins/sketchbook5/custom')" target="custom/custom.php" />

$mi->ribbon_img = isset($mi->ribbon_img['full_url']) ? $mi->ribbon_img['full_url'] : null;
$mi->no_attached_img = isset($mi->no_attached_img['full_url']) ? $mi->no_attached_img['full_url'] : null;
$mi->use_category = $use_category;
$mi->normal_lst_tdw = isset($mi->normal_lst_tdw) ? $mi->normal_lst_tdw : null;
$mi->header_text = $current_module_info->header_text;

$mi->rd_ft_nav = !isset($mi->rd_ft_nav) || $mi->rd_ft_nav == '-1' ? null : $mi->rd_ft_nav;
$mi->btm_mn = !isset($mi->btm_mn) || $mi->btm_mn == '-1' ? array() : $mi->btm_mn;
$mi->preview = !isset($mi->preview) || $mi->preview == '-1' ? array() : $mi->preview;
$mi->cmt_count = !isset($mi->cmt_count) || $mi->cmt_count == '-1' ? array() : $mi->cmt_count;
$mi->ext_img = !isset($mi->ext_img) || $mi->ext_img == '-1' ? array() : $mi->ext_img;

$mi->to_sns_small = !isset($mi->to_sns_small) || $mi->to_sns_small == ' ' ? ' ' : $mi->to_sns_small;
$mi->fdb_style = !isset($mi->fdb_style) || $mi->fdb_style == ' ' ? ' ' : $mi->fdb_style;
$mi->profile_img = !isset($mi->profile_img) || $mi->profile_img == ' ' ? ' ' : $mi->profile_img;
$mi->fdb_nav = !isset($mi->fdb_nav) || $mi->fdb_nav == ' ' ? ' ' : $mi->fdb_nav;
$mi->cmt_wrt_position = !isset($mi->cmt_wrt_position) || $mi->cmt_wrt_position == ' ' ? ' ' : $mi->cmt_wrt_position;
$mi->page_count = $current_module_info->page_count;
$mi->use_status = $current_module_info->use_status;
$mi->cnb = !isset($mi->cnb) ? ' ' : $mi->cnb;
$mi->lst_viewer = !isset($mi->lst_viewer) || $mi->lst_viewer == ' ' ? ' ' : $mi->lst_viewer;
$mi->hd_tx = !isset($mi->hd_tx) || $mi->hd_tx == ' ' ? ' ' : $mi->hd_tx;
$mi->fdb_count = !isset($mi->fdb_count) || $mi->fdb_count == ' ' ? ' ' : $mi->fdb_count;
$mi->hover = !isset($mi->hover) || $mi->hover == ' ' ? ' ' : $mi->hover;
$mi->select_lst = !isset($mi->select_lst) || $mi->select_lst == '-1' ? null : $mi->select_lst;
$mi->select_lst_more = !isset($mi->select_lst_more) || $mi->select_lst_more == '-1' ? null : $mi->select_lst_more;
$mi->link_board = !isset($mi->link_board) || $mi->link_board == ' ' ? ' ' : $mi->link_board;
$mi->bubble = !isset($mi->bubble) || $mi->bubble == ' ' ? ' ' : $mi->bubble;
$mi->files_type = !isset($mi->files_type) || $mi->files_type == ' ' ? ' ' : $mi->files_type;
$mi->img_opt = !isset($mi->img_opt) || $mi->img_opt == '-1' ? null : $mi->img_opt;
$mi->img_link = !isset($mi->img_link) || $mi->img_link == ' ' ? ' ' : $mi->img_link;
$mi->rd_nav_side = !isset($mi->rd_nav_side) || $mi->rd_nav_side == ' ' ? ' ' : $mi->rd_nav_side;
$mi->wizard = !isset($mi->wizard) || $mi->wizard == ' ' ? ' ' : $mi->wizard;
$mi->font_btn = !isset($mi->font_btn) || $mi->font_btn == ' ' ? ' ' : $mi->font_btn;
$mi->font = !isset($mi->font) || $mi->font == ' ' ? ' ' : $mi->font;
$mi->viewer = !isset($mi->viewer) || $mi->viewer == ' ' ? ' ' : $mi->viewer;
$mi->viewer_with = !isset($mi->viewer_with) || $mi->viewer_with == ' ' ? ' ' : $mi->viewer_with;
$mi->rd_nav = !isset($mi->rd_nav) || $mi->rd_nav == ' ' ? ' ' : $mi->rd_nav;
$mi->rd_tl_font = !isset($mi->rd_tl_font) || $mi->rd_tl_font == ' ' ? ' ' : $mi->rd_tl_font;
$mi->rd_top_font = !isset($mi->rd_top_font) || $mi->rd_top_font == ' ' ? ' ' : $mi->rd_top_font;
$mi->rd_btm_font = !isset($mi->rd_btm_font) || $mi->rd_btm_font == ' ' ? ' ' : $mi->rd_btm_font;
$mi->prev_next_cut_size = !isset($mi->prev_next_cut_size) || $mi->prev_next_cut_size == '-1' ? null : $mi->prev_next_cut_size;
$mi->rd_nav_style = !isset($mi->rd_nav_style) || $mi->rd_nav_style == ' ' ? ' ' : $mi->rd_nav_style;
$mi->rd_padding = !isset($mi->rd_padding) || $mi->rd_padding == '-1' ? null : $mi->rd_padding;
$mi->rd_style = !isset($mi->rd_style) || $mi->rd_style == ' ' ? ' ' : $mi->rd_style;
$mi->et_var = !isset($mi->et_var) || $mi->et_var == ' ' ? ' ' : $mi->et_var;
$mi->to_sns = !isset($mi->to_sns) || $mi->to_sns == ' ' ? ' ' : $mi->to_sns;
$mi->img_insert = !isset($mi->img_insert) || $mi->img_insert == ' ' ? ' ' : $mi->img_insert;
$mi->display_sign = !isset($mi->display_sign) || $mi->display_sign == '-1' ? null : $mi->display_sign;
$mi->prev_next = !isset($mi->prev_next) || $mi->prev_next == ' ' ? ' ' : $mi->prev_next;
$mi->votes = !isset($mi->votes) || $mi->votes == ' ' ? ' ' : $mi->votes;
$mi->declare = !isset($mi->declare) || $mi->declare == ' ' ? ' ' : $mi->declare;
$mi->rd_nav_tx = !isset($mi->rd_nav_tx) || $mi->rd_nav_tx == ' ' ? ' ' : $mi->rd_nav_tx;
$mi->rd_nav_item = !isset($mi->rd_nav_item) || $mi->rd_nav_item == ' ' ? ' ' : $mi->rd_nav_item;
$mi->show_files = !isset($mi->show_files) ? '3' : $mi->show_files;
$mi->select_editor = !isset($mi->select_editor) || $mi->select_editor == ' ' ? ' ' : $mi->select_editor;
$mi->rd_lst = !isset($mi->rd_lst) || $mi->rd_lst == '-1' ? null : $mi->rd_lst;
$mi->title = !isset($mi->title) || $mi->title == '-1' ? null : $mi->title;
$mi->srch_window = !isset($mi->srch_window) || $mi->srch_window == ' ' ? ' ' : $mi->srch_window;
$mi->display_setup_button = !isset($mi->display_setup_button) ? ' ' : $mi->display_setup_button;
$mi->show_cate = !isset($mi->show_cate) ? ' ' : $mi->show_cate;

$mi->write_btn = !isset($mi->write_btn) ? ' ' : $mi->write_btn;
$mi->subject_cut_size = !isset($mi->subject_cut_size) || $mi->subject_cut_size == '-1' ? null : $mi->subject_cut_size;

$mi->rd_board_style = !isset($mi->rd_board_style) ? null : $mi->rd_board_style;
$mi->rd_css = !isset($mi->rd_css) ? null : $mi->rd_css;
$mi->rd_tl_css = !isset($mi->rd_tl_css) ? null : $mi->rd_tl_css;

$mi->rd_cate = !isset($mi->rd_cate) ? ' ' : $mi->rd_cate;
$mi->rd_nick = !isset($mi->rd_nick) ? ' ' : $mi->rd_nick;
$mi->rd_date = !isset($mi->rd_date) ? ' ' : $mi->rd_date;
$mi->rd_profile = !isset($mi->rd_profile) ? ' ' : $mi->rd_profile;
$mi->rd_link = !isset($mi->rd_link) ? ' ' : $mi->rd_link;
$mi->rd_info = !isset($mi->rd_info) ? ' ' : $mi->rd_info;
$mi->rd_view = !isset($mi->rd_view) ? ' ' : $mi->rd_view;
$mi->rd_vote = !isset($mi->rd_vote) ? ' ' : $mi->rd_vote;
$mi->rd_cmt = !isset($mi->rd_cmt) ? ' ' : $mi->rd_cmt;
$mi->srch_btm = !isset($mi->srch_btm) ? ' ' : $mi->srch_btm;
$mi->write_btm_btn = !isset($mi->write_btm_btn) ? ' ' : $mi->write_btm_btn;
$mi->cmt_this_btn = !isset($mi->cmt_this_btn) ? ' ' : $mi->cmt_this_btn;
$mi->cmt_vote = !isset($mi->cmt_vote) ? ' ' : $mi->cmt_vote;
$mi->thumbnail_type = !isset($mi->thumbnail_type) ? 'crop' : $mi->thumbnail_type;
$mi->cnb_count = !isset($mi->cnb_count) ? 'N' : $mi->cnb_count;
$mi->cnb_open = !isset($mi->cnb_open) ? 'Y' : $mi->cnb_open;
$mi->display_ip_address = !isset($mi->display_ip_address) ? 'N' : $mi->display_ip_address;

if(!isset($_COOKIE['cookie_viewer_with'])){
    $_COOKIE['cookie_viewer_with'] = null;
}

if(!isset($listStyle)){
    $listStyle = null;
}
?>

<!-- <load target="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" /> -->
<link rel='stylesheet' id='<?php echo X2B_DOMAIN ?>-sketchbook5-fontawsome-css' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css?ver=<?php echo X2B_VERSION ?>' type='text/css' media='all' />

<!-- <load target="css/board.css" /> -->
<link rel='stylesheet' id='<?php echo X2B_DOMAIN ?>-sketchbook5-board-css' href='<?php echo $skin_url ?>/css/board.css?ver=<?php echo X2B_VERSION ?>' type='text/css' media='all' />

<!-- <load cond="$mi->colorset=='black'" target="css/black.css" /> -->
<?php if($mi->colorset=='black'): ?>
    <link rel='stylesheet' id='<?php echo X2B_DOMAIN ?>-sketchbook5-black-css' href='<?php echo $skin_url ?>/css/black.css?ver=<?php echo X2B_VERSION ?>' type='text/css' media='all' />
<?php endif ?>

<!--%load_js_plugin("ui")-->
<script type="text/javascript" src="<?php echo X2B_URL ?>/common/js/plugins/ui/jquery-ui.min.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-jquery-ui-js"></script>
<link rel="stylesheet" href="<?php echo X2B_URL ?>/common/js/plugins/ui/jquery-ui.min.css?ver=<?php echo X2B_VERSION ?>" type='text/css' media='all' />

<style data-id="bdCss">
<?php if(isset($mi->font)): ?>
.bd,.bd input,.bd textarea,.bd select,.bd button,.bd table{font-family:<?php if($mi->font=='ng'):?>'Segoe UI',Meiryo,'나눔고딕',NanumGothic,ng,<?php elseif($mi->font=='window_font'): ?>'Segoe UI',Meiryo,'맑은 고딕','Malgun Gothic',<?php elseif($mi->font=='tahoma'): ?>Tahoma,'돋움',Dotum,<?php endif ?>'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',sans-serif}
    <?php if($mi->font=='tahoma'): ?>
    .bd .ngeb{font-family:'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',sans-serif}
    <?php else: ?>
    .bd .ngeb{font-weight:700;font-family:'Segoe UI',Meiryo,'나눔고딕 ExtraBold','NanumGothic ExtraBold',ngeb,'맑은 고딕','Malgun Gothic','나눔고딕',NanumGothic,ng,'Trebuchet MS','돋움',dotum,Helvetica,'Apple SD Gothic Neo',sans-serif}
    <?php endif ?>
<?php endif ?>
<?php if($mi->color!='333333'): ?>
.bd a:focus,.bd input:focus,.bd button:focus,.bd textarea:focus,.bd select:focus{outline-color:#<?php echo $mi->color?>;}
.bd .replyNum{color:#<?php echo $mi->color?> !important}
.bd .trackbackNum{color:<?php echo $mi->color?> !important}
.bd.fdb_count .replyNum{background:#<?php echo $mi->color?>;}
.bd.fdb_count .trackbackNum{background:<?php echo $mi->color?>;}
<?php endif ?>
.bd em,.bd .color{color:#<?php echo $mi->color?>;}
.bd .shadow{text-shadow:1px 1px 1px <?php echo $mi->shadow?>;}
.bd .bolder{color:#<?php echo $mi->color?>;text-shadow:2px 2px 4px <?php echo $mi->shadow?>;}
.bd .bg_color{background-color:#<?php echo $mi->color?>;}
/* <block cond="$mi->colorset=='white'"> */
<?php if($mi->colorset=='white'): ?>
.bd .bg_f_color{background-color:#<?php echo $mi->color?>;background:-webkit-linear-gradient(#FFF -50%,#<?php echo $mi->color?> 50%);background:linear-gradient(to bottom,#FFF -50%,#<?php echo $mi->color?> 50%);}
<?php endif ?>
.bd .border_color{border-color:#<?php echo $mi->color?>;}
.bd .bx_shadow{box-shadow:0 0 2px <?php echo $mi->shadow?>;}
.viewer_with.on:before{background-color:#<?php echo $mi->color?>;box-shadow:0 0 2px #<?php echo $mi->color?>;}
/* <block cond="$mi->ribbon_img"> */
<?php if($mi->ribbon_img): ?>
.ribbon_v .ribbon{width:<?php echo $mi->ribbon_size?>px;height:<?php echo $mi->ribbon_size?>px;background:url(<?php echo $mi->ribbon_img?>);border:0;-webkit-box-shadow:none;box-shadow:none}
<?php endif ?>
/* <block cond="$mi->ribbon_style"> */
<?php if(isset($mi->ribbon_style)): ?>
.ribbon_v2 .ribbon{<?php if($mi->ribbon_align):?>right:auto;left:-1px;<?php endif ?>background-color:<?php echo $mi->ribbon_color?>;border-color:<?php echo $mi->ribbon_color?>;}
<?php endif ?>
/* <block cond="$mi->no_img"> */
<?php if(false): //$mi->ribbon_no_imgstyle): ?>
.no_img{background:url(<?php echo $mi->no_img?>) 50% 50% no-repeat;background-size:cover;text-indent:-999px}
<?php endif ?>
/* <block cond="$mi->use_category!='Y' || $mi->cnb"> */
<?php if($mi->use_category!='Y' || $mi->cnb): ?>
.bd_zine.zine li:first-child,.bd_tb_lst.common_notice tr:first-child td{margin-top:2px;border-top:1px solid #DDD}
<?php endif ?>
/* <block cond="$mi->img_insert"> */
<?php if($mi->img_insert != ' '): ?>
.rd_gallery{text-align:<?php echo $mi->img_insert_align?>;<?php echo $mi->img_insert_css?>;}
.rd_gallery img{width:<?php echo $mi->img_insert_width?>;height:<?php echo $mi->img_insert_height?>;<?php echo $mi->img_insert_img_css?>;}
<?php endif ?>
/* <!--@if($mi->cnb3_color)--> */
<?php if(isset($mi->cnb3_color)): ?>
.cnb3 .on>a,.cnb3 a:hover,.cnb3 a:focus{border-color:<?php echo $mi->shadow?>;background:#<?php echo $mi->color?>;}
.cnb4 .on>a,.cnb4 a:hover,.cnb4 a:focus{border-color:#<?php echo $mi->color?>;color:#<?php echo $mi->color?>;}
<?php endif ?>

<!--// List Style -->
/* <block cond="!$mi->zine_tx_color"> */
<?php if(!isset($mi->zine_tx_color)): ?>
.bd_zine .info b,.bd_zine .info a{color:<?php echo $mi->shadow?>;}
.bd_zine.card h3{color:#<?php echo $mi->color?>;}
<?php endif ?>

/* <!--@if($mi->default_style=='list')--> */
<?php if($mi->default_style=='list'): ?>
    <!--// Normal -->
    /* <block cond="$mi->use_category!='Y'"> */
    <?php if($mi->use_category!='Y'): ?>
    .bd_tb_lst{margin-top:0}
    <?php endif ?>
    /* <block cond="$mi->lst_viewer=='Y'"> */
    <?php if($mi->lst_viewer=='Y'): ?>
    .bd_tb_lst .hx{position:relative;text-decoration:none}
    <?php endif ?>
    .bd_tb_lst .cate span,.bd_tb_lst .author span,.bd_tb_lst .last_post small{max-width:<?php echo $mi->normal_lst_tdw?>px}
/* <!--@elseif($mi->default_style=='webzine')--> */
<?php elseif($mi->default_style=='webzine'): ?>
    <!--// Webzine -->
    /* <block cond="!$mi->zine_hover"> */
    <?php if(!$mi->zine_hover): ?>
    .bd_zine.zine li:hover .tmb_wrp{ -ms-transform:rotate(5deg);transform:rotate(5deg)}
    .bd_zine.card li:hover{ -ms-transform:scale(1.05);transform:scale(1.05)}
    <?php endif ?>
    .bd_zine .tmb_wrp .no_img{width:<?php echo $mi->zine_thumb_width?>px;height:<?php echo $mi->zine_thumb_height?>px;line-height:<?php echo $mi->zine_thumb_height?>px}
    .bd_zine a:hover,.bd_zine a:focus,.bd_zine .select a{border-color:#<?php echo $mi->color?>;}
    .bd_zine.zine .tmb_wrp img,.bd_zine.card li{ <?php echo $mi->zine_css?> }
    /* <block cond="$list_config['thumbnail']"> */
    <?php if($list_config['thumbnail']): ?>
    .bd_zine.zine .rt_area{margin-left:<?php echo $mi->zine_thumb_width+20?>px}
    .bd_zine.zine .tmb_wrp{margin-left:-<?php echo $mi->zine_thumb_width+20?>px}
    <?php endif ?>
    /* <block cond="$mi->zine_style=='3' || $mi->zine_style=='4'"> */
    <?php if($mi->zine_style=='3' || $mi->zine_style=='4'): ?>
        .bd_zine{margin:0 auto;padding:<?php echo $mi->zine_margin?>px 0}
        .bd_zine li{width:<?php echo $mi->zine_thumb_width?>px;margin:<?php echo $mi->zine_margin?>px}
        .bd_zine .tmb_wrp .no_img{width:<?php echo $mi->zine_thumb_width-2?>px;<?php if($mi->thumbnail_type=='crop'):?>height:<?php echo $mi->zine_thumb_width-2?>px;line-height:<?php echo $mi->zine_thumb_width-2?>px<?php else:?>height:120px;line-height:120px<?php endif ?>;border:1px solid #DDD}
            /* <!--@if($mi->zine_thumb_width > 399)--> */
            <?php if($mi->zine_thumb_width > 399): ?>
            .bd_zine.card h3{font-size:15px}
            .bd_zine.card .cnt{margin-top:1.5em}
            .bd_zine.card .info{font-size:12px}
            /* <!--@elseif($mi->zine_thumb_width > 299)--> */
            <?php elseif($mi->zine_thumb_width > 299): ?>
            .bd_zine.card h3{font-size:14px}
            /* <!--@elseif($mi->zine_thumb_width > 209)--> */
            <?php elseif($mi->zine_thumb_width > 209): ?>
            .bd_zine.card h3{font-size:13px;letter-spacing:-1px}
            .bd_zine.card .info{font-size:11px}
            /* <!--@elseif($mi->zine_thumb_width < 139)--> */
            <?php elseif($mi->zine_thumb_width < 139): ?>
            <?php endif ?>
            @media screen and (max-width:640px){
            /* <!--@if($mi->zine_thumb_width > 279)--> */
            <?php if($mi->zine_thumb_width > 279): ?>
            .bd_zine.card li{width:240px}
            <?php else: ?>
            .bd_zine.card li{width:140px}
            <?php endif ?>
        }
        @media screen and (max-width:480px){
            /* <!--@if($mi->zine_thumb_width > 399)--> */
            <?php if($mi->zine_thumb_width > 399): ?>
            .bd_zine.card li{width:400px}
            /* <!--@elseif($mi->zine_thumb_width > 139)--> */
            <?php elseif($mi->zine_thumb_width > 139): ?>
            .bd_zine.card li{width:200px}
            <?php else: ?>
            .bd_zine.card li{width:116px}
            <?php endif ?>
        }
        @media screen and (max-width:360px){
            /* <!--@if($mi->zine_thumb_width > 279)--> */
            <?php if($mi->zine_thumb_width > 279): ?>
            .bd_zine.card li{width:300px}
            <?php else: ?>
            .bd_zine.card li{width:128px}
            <?php endif ?>
        }
    <?php endif ?>
/* <!--@elseif($mi->default_style=='gallery')--> */
<?php elseif($mi->default_style=='gallery'): ?>
    <!--// Gallery -->
    .bd_tmb_lst li{float:<?php echo $mi->gall_align?>;width:<?php echo $mi->thumbnail_width+40?>px;height:<?php echo $mi->thumbnail_height+80?>px;width:<?php echo $mi->li_width?>px;height:<?php echo $mi->li_height?>px}
    .bd_tmb_lst .no_img{width:<?php echo $mi->thumbnail_width?>px;<?php if($mi->thumbnail_type=='crop'):?>height:<?php echo $mi->thumbnail_height?>px;line-height:<?php echo $mi->thumbnail_height?>px<?php else: ?>height:<?php echo $mi->thumbnail_width?>px;line-height:<?php echo $mi->thumbnail_width?>px<?php endif ?>}
    .bd_tmb_lst .deco_img{background-image:url(<?php echo $mi->deco_img?>)}
    .bd_tmb_lst .tmb_wrp,.bd_tmb_lst.gall_style2 .tmb_wrp{max-width:<?php echo $mi->thumbnail_width?>px;<?php echo $mi->thumb_css?> }
    /* <block cond="$mi->tmb_hover_bg"> */
    <?php if($mi->tmb_hover_bg): ?>
    .tmb_wrp .info{background:#<?php echo $mi->color?>;color:#FFF;<?php if($mi->tmb_hover_bg=='3'): ?>filter:alpha(opacity=80);opacity:.8<?php endif ?>}
    <?php endif ?>
    /* <block cond="$mi->gall_deg"> */
    <?php if($mi->gall_deg): ?>
    .bd_tmb_lst .tmb_wrp{transition:transform .5s}
    .bd_tmb_lst .tmb_wrp:hover{z-index:1;-ms-transform:rotate(0) !important;transform:rotate(0) !important}
    <?php endif ?>
    /* <!--@if($mi->gall_hover_img)--> */
    <?php if($mi->gall_hover_img): ?>
    .tmb_wrp .info{background-image:url(<?php echo $mi->gall_hover_img?>)}
    <?php endif ?>
    @media screen and (max-width:640px){
    /* <!--@if($mi->thumbnail_width > 279)--> */
    <?php if($mi->thumbnail_width > 279): ?>
    .bd_tmb_lst li{width:50%}
    /* <!--@elseif($mi->thumbnail_width > 179)--> */
    <?php elseif($mi->thumbnail_width > 179): ?>
    .bd_tmb_lst li{width:33.333%}
    <?php else: ?>
    .bd_tmb_lst li{width:25%}
    <?php endif ?>
    }
    @media screen and (max-width:480px){
    /* <!--@if($mi->thumbnail_width > 399)--> */
    <?php if($mi->thumbnail_width > 399): ?>
    .bd_tmb_lst li{display:block}
    /* <!--@elseif($mi->thumbnail_width > 139)--> */
    <?php elseif($mi->thumbnail_width > 139): ?>
    .bd_tmb_lst li{width:50%}
    <?php else: ?>
    .bd_tmb_lst li{width:33.333%}
    <?php endif ?>
    }
    @media screen and (max-width:360px){
    /* <!--@if($mi->thumbnail_width > 279)--> */
    <?php if($mi->thumbnail_width > 279): ?>
    .bd_tmb_lst li{display:block}
    <?php else: ?>
    .bd_tmb_lst li{width:50%}
    <?php endif ?>
    }
<?php endif ?>
</style>