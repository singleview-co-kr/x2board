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
// if(!isset($mi->cmt_wrt) || strlen($mi->cmt_wrt)==0) $mi->cmt_wrt = 'simple';
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
if($mi->custom_color) $mi->color = $mi->custom_color;
if($mi->custom_shadow) $mi->shadow = $mi->custom_shadow;
if(!$mi->color) $mi->color = 333333;
if(!$mi->color) $mi->shadow = '#999';
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

$lang = new \stdClass();
if($lang_type=='ko_KR') {
	$lang->search_info = '검색창을 열고 닫습니다';
	$lang->viewer = '뷰어로 보기';
	$lang->with_viewer = '게시물을 뷰어로 보기';
	$lang->with_viewer_info = '이 버튼을 활성화시키면, 목록에서 게시물 링크를 클릭 시 \'뷰어로 보기\'로 보게 됩니다';
	$lang->go_cmt = '댓글로 가기';
	$lang->more = '더보기';
	$lang->use_wysiwyg = '에디터 사용하기';
	$lang->sns_wrt = '설정된 SNS로 작성된 글을 동시에 발송합니다. 발송하려 하는 해당 SNS의 아이콘을 클릭하세요';
	$lang->shortcut = '단축키';
	$lang->larger = '크게';
	$lang->smaller = '작게';
	$lang->font = '글꼴';
	$lang->best_font_dsc = '사이트 기본 글꼴을 유지합니다';
	$lang->best_font = '기본글꼴';
	$lang->window_font = '맑은고딕';
	$lang->tahoma = '돋움';
	$lang->select_editor = '에디터 선택하기';
	$lang->textarea = '텍스트 모드';
	$lang->wysiwyg = '에디터 모드';
	$lang->sxc_editor = 'SNS 보내기';
	$lang->noti_rfsh = '※ 주의 : 페이지가 새로고침됩니다';
	$lang->bd_login = '로그인 하시겠습니까?';
	$lang->link_site = '사이트';
	$lang->link_site_viewer = '사이트를 뷰어로 보기';
	$lang->score = '점';
	$lang->cmd_deselect_all = '선택 해제';
	$lang->m_editor_notice = 'HTML로 작성된 문서를 모바일 기기에서 수정하는 것을 권장하지 않습니다';
	$lang->select_files_to_insert = '본문에 넣을 파일을 선택하세요';
	$lang->m_img_upoad_1 = '본문 위에 넣기';
	$lang->m_img_upoad_2 = '본문 아래에 넣기';
}
else {
	$lang->search_info = 'Show or Hide search window';
	$lang->viewer = 'Viewer';
	$lang->with_viewer = 'Read with Viewer';
	$lang->with_viewer_info = 'If this button is activated, when you click links in list, read with Viewer';
	$lang->go_cmt = 'Go comment';
	$lang->more = ', More';
	$lang->use_wysiwyg = 'Write with WYSIWYG';
	$lang->sns_wrt = 'If you send this post to your SNS, Click the SNS icon';
	$lang->shortcut = 'Shortcut';
	$lang->larger = 'Larger Font';
	$lang->smaller = 'Smaller Font';
	$lang->font = 'Font';
	$lang->best_font_dsc = 'Default';
	$lang->best_font = 'Default';
	$lang->window_font = 'Segoe UI';
	if($lang_type=='ja-JP') {
        $lang->window_font = 'メイリオ';
    }
	$lang->tahoma = 'Tahoma';
	$lang->select_editor = 'Select Editor';
	$lang->textarea = 'Textarea';
	$lang->wysiwyg = 'WYSIWYG';
	$lang->sxc_editor = 'To SNS';
	$lang->noti_rfsh = '※ Be careful of Refresh';
	$lang->bd_login = 'Sign In?';
	$lang->link_site = 'Website';
	$lang->link_site_viewer = 'Go Website with Viewer';
	$lang->score = ' Score';
	$lang->cmd_deselect_all = 'Deselect';
	$lang->m_editor_notice = 'It is not recommended to update an article on mobile devices';
	$lang->select_files_to_insert = 'Select files to insert to content';
	$lang->m_img_upoad_1 = 'Files + Content';
	$lang->m_img_upoad_2 = 'Content + Files';
	// $lang->cmd_vote = 'Like';
	// $lang->cmd_vote_down = 'Dislike';
}

if(isset($listStyle)) {
    if($listStyle=='list') { 
        $mi->default_style = 'list';
    }
    elseif($listStyle=='webzine') {
        $mi->default_style = 'webzine';
    }
    elseif($listStyle=='gallery') {
        $mi->default_style = 'gallery';
    }
    elseif($listStyle=='cloud_gall') {
        $mi->default_style = 'cloud_gall';
    }
    elseif($listStyle=='guest') {
        $mi->default_style = 'guest';
    }
    elseif($listStyle=='blog') {
        $mi->default_style = 'blog';
    }
    elseif($listStyle=='faq') {
        $mi->default_style = 'faq';
    }
    elseif($listStyle=='viewer') {
        $mi->default_style = 'viewer';
    }
}
elseif(!in_array($mi->default_style,array('list','webzine','gallery','cloud_gall','guest','blog','faq','viewer'))) {
    $mi->default_style = 'list';
}

if(wp_is_mobile()) {
    // <load target="../../../../common/js/jquery.min.js" index="-100006" />
    // <load target="../../../../common/js/xe.min.js" index="-100006" />
    // <load target="../../../../common/js/x.min.js" index="-100006" />
    // <load target="../../tpl/js/board.js" />    
}

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
$mi->normal_lst_tdw = $mi->normal_lst_tdw;
$mi->header_text = $current_module_info->header_text;

$mi->rd_ft_nav = $mi->rd_ft_nav == '-1' ? null : $mi->rd_ft_nav;
$mi->btm_mn = $mi->btm_mn == '-1' ? array() : $mi->btm_mn;
$mi->preview = $mi->preview == '-1' ? array() : $mi->preview;
$mi->cmt_count = $mi->cmt_count == '-1' ? array() : $mi->cmt_count;
$mi->ext_img = $mi->ext_img == '-1' ? array() : $mi->ext_img;
$mi->to_sns_small = $mi->to_sns_small == ' ' ? null : $mi->to_sns_small;
$mi->fdb_style = $mi->fdb_style == ' ' ? null : $mi->fdb_style;
$mi->profile_img = $mi->profile_img == ' ' ? null : $mi->profile_img;
$mi->fdb_nav = $mi->fdb_nav == ' ' ? null : $mi->fdb_nav;
$mi->cmt_wrt_position = $mi->cmt_wrt_position == ' ' ? null : $mi->cmt_wrt_position;
$mi->page_count = $current_module_info->page_count;
$mi->use_status = $current_module_info->use_status;

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

<style data-id="bdCss">
<?php if($mi->font): ?>
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
<?php if($mi->ribbon_style): ?>
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
<?php if($mi->img_insert): ?>
.rd_gallery{text-align:<?php echo $mi->img_insert_align?>;<?php echo $mi->img_insert_css?>;}
.rd_gallery img{width:<?php echo $mi->img_insert_width?>;height:<?php echo $mi->img_insert_height?>;<?php echo $mi->img_insert_img_css?>;}
<?php endif ?>
/* <!--@if($mi->cnb3_color)--> */
<?php if($mi->cnb3_color): ?>
.cnb3 .on>a,.cnb3 a:hover,.cnb3 a:focus{border-color:<?php echo $mi->shadow?>;background:#<?php echo $mi->color?>;}
.cnb4 .on>a,.cnb4 a:hover,.cnb4 a:focus{border-color:#<?php echo $mi->color?>;color:#<?php echo $mi->color?>;}
<?php endif ?>

<!--// List Style -->
/* <block cond="!$mi->zine_tx_color"> */
<?php if(!$mi->zine_tx_color): ?>
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