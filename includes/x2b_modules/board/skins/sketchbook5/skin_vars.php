<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

$settings = array(
    'sketchbook5_setup_header'					=> array(
        'id'      => 'sketchbook5_setup_header',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header', X2B_DOMAIN ),  // 전체 설정
        'type'    => 'header',
        'options' => false,
    ),
    'wizard'               => array(
        'id'      => 'wizard',
        'name'    => __( 'skin_name_wizard', X2B_DOMAIN ),  // 위저드 사용
        'desc'    => __( 'skin_desc_wizard', X2B_DOMAIN ),  // 스킨 설정을 할 수 있는 내비게이션을 게시판 브라우저 좌측에 출력합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_admin_only', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ),  // 관리자만(기본)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ),  // 사용 안함
        ),
    ),
    'colorset'               => array(
        'id'      => 'colorset',
        'name'    => __( 'skin_name_colorset', X2B_DOMAIN ),   // 컬러셋
        'type'    => 'radio',
        'default' => '',
        'options' => array(
            'white'      => __( 'skin_opt_color_white', X2B_DOMAIN ),  // 하얀색
            'black'       => __( 'skin_opt_color_black', X2B_DOMAIN ),  // 검은색
        ),
    ),
    'default_style'               => array(
        'id'      => 'default_style',
        'name'    => __( 'skin_name_default_style', X2B_DOMAIN ),  // 목록 유형
        'type'    => 'radio',
        'default' => 'list',
        'options' => array(
            'list'      => __( 'skin_opt_type_list', X2B_DOMAIN ),   // 목록형
            // 'webzine'       => __( '웹진형', X2B_DOMAIN ),
            // 'gallery'      => __( '갤러리형', X2B_DOMAIN ),
            // 'cloud_gall'       => __( '클라우드 갤러리', X2B_DOMAIN ),
            // 'guest'       => __( '방명록형', X2B_DOMAIN ),
            // 'blog'      => __( '블로그형', X2B_DOMAIN ),
            'faq'       => __( 'skin_opt_type_faq', X2B_DOMAIN ),   // FAQ형
        ),
    ),
    'link_board'               => array(
        'id'      => 'link_board',
        'name'    => __( 'skin_name_link_board', X2B_DOMAIN ),  // 링크 게시판으로 사용
        'desc'    => __( 'skin_desc_link_board', X2B_DOMAIN ), // 게시판의 목록의 링크를 누르면 확장변수에서 입력한 사이트 주소로 이동합니다. 확장변수에서 'link_url' 항목을 등록하신 후 사용하세요. 'prettyphoto 애드온 사용'은 prettyphoto 애드온을 설치하고 활성화시키셔야 합니다. 자세한 사용법은 매뉴얼 또는 제작자의 홈페이지를 참조하세요.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ),  // 사용 안함  (기본)
            '2'       => __( 'skin_opt_use_yes', X2B_DOMAIN ),  // 사용
            '3'      => __( 'skin_opt_pretty_photo', X2B_DOMAIN ),  // prettyPhoto 애드온 사용
        ),
    ),
    'color'					=> array(
        'id'      => 'color',
        'name'    => __( 'skin_name_color', X2B_DOMAIN ),  // 게시판 색상
        'desc'    => __( 'skin_desc_color', X2B_DOMAIN ),  // 컬러셋과는 별도로 게시판의 전체 '색상' 분위기를 결정하는 부분입니다. 제목, 데코레이션, 테두리 등의 색상을 결정합니다.
        'type'    => 'select',
        'default' => '333333',
        'options' => array(
            '333333'            => __( 'skin_opt_color_black', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ),  // 검은색(기본)
            '87cefa'        => __( 'skin_opt_color_light_sky_blue', X2B_DOMAIN ),  // 라이트스카이블루(파스텔-하늘)
            '6495ed'            => __( 'skin_opt_color_cone_flower_blue', X2B_DOMAIN ),  // 콘플라워블루(연한하늘)
            '4169e1'            => __( 'skin_opt_color_royal_blue', X2B_DOMAIN ),  // 로얄블루(밝은파랑)
            '4682b4'            => __( 'skin_opt_color_steel_blue', X2B_DOMAIN ),  // 스틸블루(짙은파랑)
            'adff2f'            => __( 'skin_opt_color_green_yellow', X2B_DOMAIN ),  // 그린옐로우(파스텔-연두)
            '80E12A'            => __( 'skin_opt_color_green', X2B_DOMAIN ),  // 초록색
            'ffb6c1'            => __( 'skin_opt_color_light_pink', X2B_DOMAIN ),  // 라이트핑크(파스텔-분홍)
            'ff69b4'            => __( 'skin_opt_color_hot_pink', X2B_DOMAIN ),   // 핫핑크(분홍)
            'ff1493'            => __( 'skin_opt_color_deep_pink', X2B_DOMAIN ),  // 딥핑크(선홍)
            'ffd700'            => __( 'skin_opt_color_gold', X2B_DOMAIN ),  // 골드(노랑)
            'ffa500'            => __( 'skin_opt_color_orange', X2B_DOMAIN ), // 오렌지(주황)
            'ff7f50'            => __( 'skin_opt_color_coral', X2B_DOMAIN ), // 코랄(진한주황)
            'ff6347'            => __( 'skin_opt_color_tomato', X2B_DOMAIN ), // 토마토(연한빨강)
            'bc8f8f'            => __( 'skin_opt_color_rose_brown', X2B_DOMAIN ),  // 장미빛갈색(흑백-보라)
            'ee82ee'            => __( 'skin_opt_color_violet', X2B_DOMAIN ), // 바이올렛(파스텔-보라)
            'db7093'            => __( 'skin_opt_color_violet_red', X2B_DOMAIN ), // 팔레바이올렛레드(연한자주)
            'c71585'            => __( 'skin_opt_color_medium_violet_red', X2B_DOMAIN ), // 미디엄바이올렛레드(진한자주)
        ),
    ),
    'custom_color'					=> array(
        'id'      => 'custom_color',
        'name'    => __( 'skin_name_custom_color', X2B_DOMAIN ), // 게시판 색상 입력
        'desc'    => __( 'skin_desc_custom_color', X2B_DOMAIN ), // 위의 색상 외에 직접 색상을 입력할 수 있습니다. 입력방법은 색태그(예 : #FFFFFF)에서 '#'을 제외한 'FFFFFF' 부분만 입력하세요. 아래의 '그림자' 색상도 반드시(!) 입력하셔야 합니다.
        'type'    => 'text',
        'options' => false,
    ),
    'custom_shadow'					=> array(
        'id'      => 'custom_shadow',
        'name'    => __( 'skin_name_custom_shadow', X2B_DOMAIN ), // 그림자 색상 입력
        'desc'    => __( 'skin_desc_custom_shadow', X2B_DOMAIN ), // 입력방법은 색태그(예 : #FFFFFF)에서 '#'을 포함한(!) '#FFFFFF' 부분까지 입력하세요. 위의 '색상'보다 조금 더 어두운 색상을 입력하는 것을 추천합니다.
        'type'    => 'text',
        'options' => false,
    ),
    'hover'					=> array(
        'id'      => 'hover',
        'name'    => __( 'skin_name_hover', X2B_DOMAIN ),  // 아이콘에 마우스 오버시 효과
        'desc'    => __( 'skin_desc_hover', X2B_DOMAIN ), // 아이콘에 마우스 오버했을 때 효과를 설정합니다. 모바일에서는 작동하지 않습니다. *css3 animation 을 지원하는 브라우저에서만 작동합니다.
        'type'    => 'select',
        'default' => ' ',
        'options' => array(
            ' '            => __( 'skin_opt_spin_effect', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 회전 효과(기본)
            'N'        => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'font'					=> array(
        'id'      => 'font',
        'name'    => __( 'skin_name_font', X2B_DOMAIN ), // 기본 글꼴 설정
        'desc'    => __( 'skin_desc_font', X2B_DOMAIN ), // 게시판의 기본글꼴을 설정합니다. 단, 아래의 '글꼴 선택 버튼'을 표시하는 경우 사용자가 선택한 글꼴이 우선합니다.
        'type'    => 'select',
        'default' => ' ',
        'options' => array(
            ' '            => __( 'skin_opt_depend_on_layout', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 레이아웃에 따라(기본)
            'ng'        => __( 'skin_opt_font_nanum_gothic', X2B_DOMAIN ), // 나눔고딕
            'window_font'        => __( 'skin_opt_font_clear_gothic', X2B_DOMAIN ), // 맑은 고딕
            'tahoma'        => __( 'skin_opt_font_tahoma', X2B_DOMAIN ), // 돋움
        ),
    ),
    'title'					=> array(
        'id'      => 'title',
        'name'    => __( 'skin_name_title', X2B_DOMAIN ), // 게시판 제목
        'type'    => 'text',
        'options' => false,
    ),
    'sub_title'					=> array(
        'id'      => 'sub_title',
        'name'    => __( 'skin_name_sub_title', X2B_DOMAIN ), // 게시판 부제목
        'type'    => 'text',
        'options' => false,
    ),
    'title_img'					=> array(
        'id'      => 'title_img',
        'name'    => __( 'skin_name_title_img', X2B_DOMAIN ), // 게시판 제목에 이미지 사용
        'desc'    => __( 'skin_desc_title_img', X2B_DOMAIN ), // 게시판 제목을 이 항목에 등록한 이미지로 사용할 수 있습니다.
        'type'    => 'image',
        'options' => false,
    ),
    'sketchbook5_setup_header1'					=> array(
        'id'      => 'sketchbook5_setup_header1',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header1', X2B_DOMAIN ), // 게시판 상단 설정
        'type'    => 'header',
        'options' => false,
    ),
    // 'breadcrumb'               => array(
    //     'id'      => 'breadcrumb',
    //     'name'    => __( '좌측 상단 빵조각 메뉴', X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => 'Y',
    //     'options' => array(
    //         'Y'      => __( '표시(기본)', X2B_DOMAIN ),
    //         'N'       => __( '표시 안함', X2B_DOMAIN ),
    //     ),
    // ),
    'srch_window'               => array(
        'id'      => 'srch_window',
        'name'    => __( 'skin_name_srch_window', X2B_DOMAIN ), // 상단 검색창
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_btn_click_search', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 버튼 표시 + 클릭 시 검색창(기본)
            '2'       => __( 'skin_opt_always_search', X2B_DOMAIN ), // 검색창 항상 표시
            '3'       => __( 'skin_opt_block_search', X2B_DOMAIN ), // 블럭형 큰 검색창(FAQ용)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'write_btn'               => array(
        'id'      => 'write_btn',
        'name'    => __( 'skin_name_write_btn', X2B_DOMAIN ), // 쓰기 버튼
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ),  // 표시(기본)
            '2'       => __( 'skin_opt_admin_only', X2B_DOMAIN ), // 권한자에게만
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'font_btn'               => array(
        'id'      => 'font_btn',
        'name'    => __( 'skin_name_font_btn', X2B_DOMAIN ), // 글꼴 선택 버튼
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_font_select', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ),  // 선택된 글꼴 표시하는 버튼(기본)
            '2'       => __( 'skin_opt_simple_btn', X2B_DOMAIN ), // 간단한 버튼
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'select_lst'               => array(
        'id'      => 'select_lst',
        'name'    => __( 'skin_name_select_lst', X2B_DOMAIN ), // 목록 유형 버튼
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'select_lst_more'                       => array(
        'id'      => 'select_lst_more',
        'name'    => __( 'skin_name_select_lst_more', X2B_DOMAIN ), // 목록 유형 버튼 추가
        'type'    => 'multicheck',
        'options' => array(
            'cloud'            => __( 'skin_opt_type_cloud', X2B_DOMAIN ), // 클라우드형
        ),
    ),
    'cnb'               => array(
        'id'      => 'cnb',
        'name'    => __( 'skin_name_cnb', X2B_DOMAIN ), // 카테고리 메뉴 스타일
        'desc'    => __( 'skin_desc_cnb', X2B_DOMAIN ), // '표시 안함' 게시판 설정의 '게시판 정보' 메뉴의 '분류 사용' 항목과 관계없이 표시하지 않습니다. '박스 스타일'은 2차 카테고리를 지원하지 않습니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_style_gradation', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 그라데이션 스타일(기본)
            'cTab'      => __( 'skin_opt_style_tab', X2B_DOMAIN ), // 탭 스타일
            'cnb3'      => __( 'skin_opt_style_box', X2B_DOMAIN ), // 박스 스타일
            'cnb4'      => __( 'skin_opt_style_box_border', X2B_DOMAIN ), // 박스+테두리 스타일
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'cnb_count'               => array(
        'id'      => 'cnb_count',
        'name'    => __( 'skin_name_cnb_count', X2B_DOMAIN ), // 카테고리 게시물 수 표시
        'desc'    => __( 'skin_desc_cnb_count', X2B_DOMAIN ), // 카테고리의 각 메뉴 이름 옆에 게시물 수를 표시합니다.
        'type'    => 'radio',
        'default' => 'N',
        'options' => array(
            'Y'      => __( 'skin_opt_display_yes', X2B_DOMAIN ), // 표시
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
        ),
    ),
    'cnb_open'  => array(
        'id'      => 'cnb_open',
        'name'    => __( 'skin_name_cnb_open', X2B_DOMAIN ), // 카테고리 처음에 펼치기
        'desc'    => __( 'skin_desc_cnb_open', X2B_DOMAIN ), // '그라데이션+테두리' 스타일에서 처음부터 목록이 펼쳐져 있게 합니다.
        'type'    => 'checkbox',
        'options' => false,
        'default' => array(	'Y' => 'Y',),
        'checked_value' => array(
            'checked' => 'Y',
            'unchecked'   => 'N',
        ),
    ),
    'cnb3_color'               => array(
        'id'      => 'cnb3_color',
        'name'    => __( 'skin_name_cnb3_color', X2B_DOMAIN ), // 카테고리 박스 스타일 색상
        'desc'    => __( 'skin_desc_cnb3_color', X2B_DOMAIN ), // 선택된 메뉴의 색상을 '게시판 색상'에서 선택한 색상으로 사용합니다. 기본 색상은 그레이입니다.
        'type'    => 'radio',
        'default' => 'N',
        'options' => array(
            'Y'      => __( 'skin_opt_use_yes', X2B_DOMAIN ), // 사용
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용 안함(기본)
        ),
    ),
    // 'cnb3_align'               => array(
    //     'id'      => 'cnb3_align',
    //     'name'    => __( '카테고리 박스 스타일 정렬', X2B_DOMAIN ),
    //     'desc'    => __( "기본은 좌측 정렬입니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => 'left',
    //     'options' => array(
    //         'left'      => __( '좌측(기본)', X2B_DOMAIN ),
    //         'center'       => __( '가운데', X2B_DOMAIN ),
    //     ),
    // ),
    'sketchbook5_setup_header2'					=> array(
        'id'      => 'sketchbook5_setup_header2',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header2', X2B_DOMAIN ), // 게시판 하단 메뉴 설정
        'type'    => 'header',
        'options' => false,
    ),
    'display_setup_button'               => array(
        'id'      => 'display_setup_button',
        'name'    => __( 'skin_name_display_setup_button', X2B_DOMAIN ), // 표시 여부
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'srch_btm'               => array(
        'id'      => 'srch_btm',
        'name'    => __( 'skin_name_srch_btm', X2B_DOMAIN ), // 하단 검색창
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_click', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 클릭 시 표시(기본)
            '2'      => __( 'skin_opt_display_init', X2B_DOMAIN ), // 처음부터 표시
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'write_btm_btn'               => array(
        'id'      => 'write_btm_btn',
        'name'    => __( 'skin_name_write_btm_btn', X2B_DOMAIN ), // 쓰기 버튼
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            '2'      => __( 'skin_opt_admin_only', X2B_DOMAIN ), // 권한자에게만
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'btm_mn'                       => array(
        'id'      => 'btm_mn',
        'name'    => __( 'skin_name_btm_mn', X2B_DOMAIN ), // 버튼 추가
        'type'    => 'multicheck',
        'options' => array(
            'home'            => __( 'skin_opt_display_list_btn', X2B_DOMAIN ), // 목록 버튼 표시
            'tag'            => __( 'skin_opt_display_tag_btn', X2B_DOMAIN ), // TAG 버튼 표시
        ),
    ),
    'sketchbook5_setup_header3'					=> array(
        'id'      => 'sketchbook5_setup_header3',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header3', X2B_DOMAIN ), // 목록 공통 설정
        'type'    => 'header',
        'options' => false,
    ),
    'rd_lst'               => array(
        'id'      => 'rd_lst',
        'name'    => __( 'skin_name_rd_lst', X2B_DOMAIN ), // 본문에서 목록 출력
        'desc'    => __( 'skin_desc_rd_lst', X2B_DOMAIN ), // 본문을 볼 때 본문 하단에 목록을 출력할 것인지를 결정합니다.
        'type'    => 'radio',
        'default' => 'Y',
        'options' => array(
            'Y'      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'notice_style'               => array(
        'id'      => 'notice_style',
        'name'    => __( '공지 출력 형식', X2B_DOMAIN ),
        'desc'    => __( "공지의 출력형식을 결정합니다. 모든 목록형태에서 동일한 공지의 출력이 필요한 경우 사용됩니다.", X2B_DOMAIN ),
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( '선택된 목록 유형과 같은 방식(기본)', X2B_DOMAIN ),
            '2'       => __( '모든 목록 유형에서 동일한 한 줄 공지 형식', X2B_DOMAIN ),
        ),
    ),
    'subject_cut_size'					=> array(
        'id'      => 'subject_cut_size',
        'name'    => __( 'skin_name_subject_cut_size', X2B_DOMAIN ), // 제목 글자 수
        'desc'    => __( 'skin_desc_subject_cut_size', X2B_DOMAIN ), // 입력하지 않으면 기본 설정은 설정은 자르지 않습니다. *갤러리형은 별도 항목이 존재합니다.
        'type'    => 'number',
        'options' => false,
    ),
    'content_cut_size'					=> array(
        'id'      => 'content_cut_size',
        'name'    => __( 'skin_name_content_cut_size', X2B_DOMAIN ), // 내용 글자 수
        'desc'    => __( 'skin_desc_content_cut_size', X2B_DOMAIN ), // 입력하지 않으면 기본 설정은 240 입니다.
        'type'    => 'number',
        'options' => false,
    ),
    'duration_new'					=> array(
        'id'      => 'duration_new',
        'name'    => __( 'skin_name_duration_new', X2B_DOMAIN ), // 새 글 표시 시간
        'desc'    => __( 'skin_desc_duration_new', X2B_DOMAIN ), // 입력하지 않으면 기본 설정은 24 입니다.
        'type'    => 'number',
        'options' => false,
    ),
    'display_ip_address'  => array(
        'id'      => 'display_ip_address',
        'name'    => __( 'skin_name_display_ip_address', X2B_DOMAIN ), // 아이피 주소 표시
        'type'    => 'checkbox',
        'options' => false,
        'default' => array(	'N' => 'N',),
        'checked_value' => array(
            'checked' => 'Y',
            'unchecked'   => 'N',
        ),
    ),
    'no_img'					=> array(
        'id'      => 'no_img',
        'name'    => __( 'skin_name_no_img', X2B_DOMAIN ), // 대체 이미지
        'desc'    => __( 'skin_desc_no_img', X2B_DOMAIN ), // 목록과 본문에서 섬네일 또는 프로필 이미지가 없는 경우 '?' 혹은 'No Image' 표시 대신 사용자가 직접 등록한 이미지를 사용할 수 있습니다. 여기에 이미지를 등록하면 활성화됩니다.
        'type'    => 'image',
        'options' => false,
    ),
    'hd_tx'               => array(
        'id'      => 'hd_tx',
        'name'    => __( 'skin_name_hd_tx', X2B_DOMAIN ), // '상단 내용' 출력 방식
        'desc'    => __( 'skin_desc_hd_tx', X2B_DOMAIN ), // 게시판 제목이나 공지 등에 활용되는 게시판 설정의 '게시판 정보'  항목의 '상단 내용'을 본문 출력 방식을 설정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_always', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 항상 출력(기본)
            '2'       => __( 'skin_opt_display_body', X2B_DOMAIN ), // 본문에서만 출력
            '3'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 본문에서 출력 안함
        ),
    ),
    'bubble'               => array(
        'id'      => 'bubble',
        'name'    => __( 'skin_name_bubble', X2B_DOMAIN ), // 말풍선 기능
        'desc'    => __( 'skin_desc_bubble', X2B_DOMAIN ), // 해당 요소의 'title'을 가지고 말풍선(툴팁)을 만들어 시인성을 높입니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용(기본)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'fdb_count'               => array(
        'id'      => 'fdb_count',
        'name'    => __( 'skin_name_fdb_count', X2B_DOMAIN ), // 댓글수 스타일
        'desc'    => __( 'skin_desc_fdb_count', X2B_DOMAIN ), // '목록형', '갤러리형'에서 댓글수의 디자인을 결정합니다. 박스형(강조형)은 댓글 수를 강조할 때 유용합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_type_text', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 텍스트형(기본)
            'fdb_count'       => __( 'skin_opt_type_box_left_align', X2B_DOMAIN ), // 박스형(강조형) - 좌측 정렬
            'fdb_count2'       => __( 'skin_opt_type_box_right_align', X2B_DOMAIN ), // 박스형(강조형) - 우측 정렬
        ),
    ),
    'sketchbook5_setup_header4'					=> array(
        'id'      => 'sketchbook5_setup_header4',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header4', X2B_DOMAIN ), // 목록형 설정
        'type'    => 'header',
        'options' => false,
    ),
    'show_cate'               => array(
        'id'      => 'show_cate',
        'name'    => __( 'skin_name_show_cate', X2B_DOMAIN ), // 카테고리 표시
        'desc'    => __( 'skin_desc_show_cate', X2B_DOMAIN ), // 목록에서 제목 앞에 카테고리를 표시합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ),  // 표시 안함
        ),
    ),
    'normal_lst_tdW'					=> array(
        'id'      => 'normal_lst_tdW',
        'name'    => __( 'skin_name_normal_lst_tdW', X2B_DOMAIN ), // 분류, 닉네임 영역 넓이 제한
        'desc'    => __( 'skin_desc_normal_lst_tdW', X2B_DOMAIN ), // 분류, 닉네임 영역이 길어지는 경우 제목 영역이 너무 좁아지는 것을 막기 위해 최대 넓이를 제한합니다. 입력하지 않으면 '90'(px)입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'preview'  => array(
        'id'      => 'preview',
        'name'    => __( 'skin_name_preview', X2B_DOMAIN ), // 요약(내용), 섬네일 미리보기
        'desc'    => __( 'skin_desc_preview', X2B_DOMAIN ), // *미리보기의 이미지의 크기는 '갤러리형' 섬네일 설정에 따릅니다.
        'type'    => 'multicheck',
        'options' => array(
            'tx'            => __( 'skin_opt_summary', X2B_DOMAIN ), // 요약
            'img'            => __( 'skin_opt_thumbnail', X2B_DOMAIN ), // 섬네일
        ),
    ),
    'preview_tx'					=> array(
        'id'      => 'preview_tx',
        'name'    => __( 'skin_name_preview_tx', X2B_DOMAIN ), // 미리보기 요약 글자 수
        'desc'    => __( 'skin_desc_preview_tx', X2B_DOMAIN ), // 입력하지 않으면 '150'입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'list_m'               => array(
        'id'      => 'list_m',
        'name'    => __( 'skin_name_list_m', X2B_DOMAIN ), // *모바일형 설정
        'desc'    => __( 'skin_desc_list_m', X2B_DOMAIN ), // 모바일에서 웹용 테이블 목록을 사용할 것인지 모바일에 최적화된 목록을 사용할 것인지를 결정합니다. 몇가지 옵션항목은 웹진형과 공유합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_type_mobile', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 모바일형 사용(기본)
            '2'       => __( 'skin_opt_type_table', X2B_DOMAIN ), // 테이블형 사용
        ),
    ),
    'list_m_tmb'               => array(
        'id'      => 'list_m_tmb',
        'name'    => __( 'skin_name_list_m_tmb', X2B_DOMAIN ), // *모바일형 썸네일
        'desc'    => __( 'skin_desc_list_m_tmb', X2B_DOMAIN ), // 모바일형에서 썸네일의 표시 여부를 결정합니다. 썸네일 크기는 웹진형의 설정을 사용합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
            'Y'       => __( 'skin_opt_display_yes', X2B_DOMAIN ),  // 표시
        ),
    ),
    'sketchbook5_setup_header5'					=> array(
        'id'      => 'sketchbook5_setup_header5',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header5', X2B_DOMAIN ), // 웹진형, 갤러리형, 모바일(목록)형 공통 설정
        'type'    => 'header',
        'options' => false,
    ),
    'tmb_effect'               => array(
        'id'      => 'tmb_effect',
        'name'    => __( 'skin_name_tmb_effect', X2B_DOMAIN ), // 섬네일 인트로 효과
        'desc'    => __( 'skin_desc_tmb_effect', X2B_DOMAIN ), // 페이지 로딩 시에 섬네일 효과를 설정합니다. (단지 디자인 요소만이 아닌, 이미지가 로딩되기 전에 이미지 영역을 감싸는 요소들이 흐트러지는 것을 방지하는 역할을 합니다.)
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_fade_in', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // Fade-In(기본)
            '2'       => __( 'skin_opt_loading_icon_fade_in', X2B_DOMAIN ), // 'loading' 아이콘 + Fade-In
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 없음
        ),
    ),
    'ext_img'  => array(
        'id'      => 'ext_img',
        'name'    => __( 'skin_name_ext_img', X2B_DOMAIN ), // 새 글 등 아이콘 표시
        'type'    => 'multicheck',
        'options' => array(
            // 'zine'            => __( '웹진형', X2B_DOMAIN ),
            // 'gall'            => __( '갤러리형', X2B_DOMAIN ),
            'list_m'            => __( 'skin_opt_type_mobile_list', X2B_DOMAIN ), // 모바일(목록)형
        ),
    ),
    'cmt_count'  => array(
        'id'      => 'cmt_count',
        'name'    => __( 'skin_name_cmt_count', X2B_DOMAIN ), // 댓글수 표시
        'type'    => 'multicheck',
        'options' => array(
            // 'zine'            => __( '웹진형', X2B_DOMAIN ),
            // 'gall'            => __( '갤러리형', X2B_DOMAIN ),
            'list_m'            => __( 'skin_opt_type_mobile_list', X2B_DOMAIN ), // 모바일(목록)형
        ),
    ),
    'ribbon_style'               => array(
        'id'      => 'ribbon_style',
        'name'    => __( 'skin_name_ribbon_style', X2B_DOMAIN ), // 리본 스타일
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_triangle_ribbon', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 삼각 리본(기본)
            '2'       => __( 'skin_opt_square_tile_ribbon', X2B_DOMAIN ), // 타일형(사각) 리본
        ),
    ),
    'ribbon_img'					=> array(
        'id'      => 'ribbon_img',
        'name'    => __( 'skin_name_ribbon_img', X2B_DOMAIN ), // 리본 이미지
        'desc'    => __( 'skin_desc_ribbon_img', X2B_DOMAIN ), // 위의 '리본 스타일' 항목에서 '삼각 리본'을 선택하시는 경우 리본 이미지를 등록하여 사용할 수 있습니다(*공지, 새 글, 업데이트 제외). 입력하지 않으면 60 X 60(px)이 기본사이즈입니다. 직접 등록하시는 경우 등록하시는 이미지의 사이즈(가로, 세로 동일)를 입력해주세요.
        'type'    => 'image',
        'options' => false,
    ),
    'ribbon_size'					=> array(
        'id'      => 'ribbon_size',
        'name'    => __( 'skin_name_ribbon_size', X2B_DOMAIN ), // 리본 이미지 사이즈
        'desc'    => __( 'skin_desc_ribbon_size', X2B_DOMAIN ), // '숫자'만(px 단위입니다) 입력해주세요.
        'type'    => 'text',
        'options' => false,
    ),
    'ribbon_align'               => array(
        'id'      => 'ribbon_align',
        'name'    => __( 'skin_name_ribbon_align', X2B_DOMAIN ), // 타일형 리본 정렬
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_upper_right', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 우측 상단(기본)
            'left'       => __( 'skin_opt_upper_left', X2B_DOMAIN ), // 좌측 상단
        ),
    ),
    'ribbon_color'					=> array(
        'id'      => 'ribbon_color',
        'name'    => __( 'skin_name_ribbon_color', X2B_DOMAIN ), // 타일형 리본 색상
        'desc'    => __( 'skin_desc_ribbon_color', X2B_DOMAIN ), // 타일형 리본을 선택한 경우 색상을 직접 지정할 수 있습니다(*공지, 새 글, 업데이트 제외). 입력방법은 풀코드를 입력해야합니다. 예) #FF0, rgb(0,25,55), rgba(55,15,5,1) 등
        'type'    => 'text',
        'options' => false,
    ),
    'extra_num'					=> array(
        'id'      => 'extra_num',
        'name'    => __( 'skin_name_extra_num', X2B_DOMAIN ), // 리본 확장변수 번호
        'desc'    => __( 'skin_desc_extra_num', X2B_DOMAIN ), // '좌측 상단 리본'에서 확장변수 사용 시 선택할 확장변수의 번호를 설정합니다. 설정이 없으면 1번이 지정됩니다.
        'type'    => 'text',
        'options' => false,
    ),
    'sketchbook5_setup_header6'					=> array(
        'id'      => 'sketchbook5_setup_header6',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header6', X2B_DOMAIN ), // 웹진형 설정 (*표시 : '모바일(목록)형'과 공통)
        'type'    => 'header',
        'options' => false,
    ),
    // 'zine_style'               => array(
    //     'id'      => 'zine_style',
    //     'name'    => __( "웹진 스타일", X2B_DOMAIN ),
    //     // 'desc'    => __( "페이지 로딩 시에 섬네일 효과를 설정합니다. (단지 디자인 요소만이 아닌, 이미지가 로딩되기 전에 이미지 영역을 감싸는 요소들이 흐트러지는 것을 방지하는 역할을 합니다.)", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '목록 스타일(기본)', X2B_DOMAIN ),
    //         '2'       => __( "목록 스타일2(큰 날짜)", X2B_DOMAIN ),
    //         '3'       => __( "카드 스타일(테두리 있음)", X2B_DOMAIN ),
    //         '4'       => __( "카드 스타일2(테두리 없음)", X2B_DOMAIN ),
    //     ),
    // ),
    // 'card_effect'               => array(
    //     'id'      => 'card_effect',
    //     'name'    => __( "카드 스타일 효과", X2B_DOMAIN ),
    //     // 'desc'    => __( "페이지 로딩 시에 섬네일 효과를 설정합니다. (단지 디자인 요소만이 아닌, 이미지가 로딩되기 전에 이미지 영역을 감싸는 요소들이 흐트러지는 것을 방지하는 역할을 합니다.)", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '부드러운 이동(기본)', X2B_DOMAIN ),
    //         'N'       => __( "효과 없음", X2B_DOMAIN ),
    //     ),
    // ),
    'zine_margin'					=> array(
        'id'      => 'zine_margin',
        'name'    => __( 'skin_name_zine_margin', X2B_DOMAIN ), // 카드 스타일 여백
        'desc'    => __( 'skin_desc_zine_margin', X2B_DOMAIN ), // '카드 스타일'에서 바깥 여백(margin)을 설정합니다. 입력하지 않으면 기본 설정은 '10' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'zine_hover'               => array(
        'id'      => 'zine_hover',
        'name'    => __( 'skin_name_zine_hover', X2B_DOMAIN ), // 마우스 오버 전환 효과
        'desc'    => __( 'skin_desc_zine_hover', X2B_DOMAIN ), // 마우스 오버시에 섬네일이 기울어지거나, 확대되는 효과를 사용하지 않습니다. *테두리 효과에는 영향을 미치지 않습니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ), // 사용(기본값)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'zine_thumb_type'               => array(
        'id'      => 'zine_thumb_type',
        'name'    => __( 'skin_name_zine_thumb_type', X2B_DOMAIN ), // 섬네일 비율(*)
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_crop', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 크롭(기본)
            'ratio'       => __( 'skin_opt_ratio', X2B_DOMAIN ), // 원본 비율
        ),
    ),
    'zine_thumb_width'					=> array(
        'id'      => 'zine_thumb_width',
        'name'    => __( 'skin_name_zine_thumb_width', X2B_DOMAIN ), // 섬네일 넓이
        'desc'    => __( 'skin_desc_zine_thumb_width', X2B_DOMAIN ), // 섬네일의 넓이에 따라 '카드스타일'에서 카드 블록의 넓이가 결정됩니다. 입력하지 않으면 기본 설정은 '90' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'zine_thumb_height'					=> array(
        'id'      => 'zine_thumb_height',
        'name'    => __( 'skin_name_zine_thumb_height', X2B_DOMAIN ), // 섬네일 높이
        'desc'    => __( 'skin_desc_zine_thumb_height', X2B_DOMAIN ), // 입력하지 않으면 기본 설정은 '90' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    // 'zine_css'     => array(
    //     'id'      => 'zine_css',
    //     'name'    => __( '섬네일 css', X2B_DOMAIN ),
    //     'desc'    => __( '섬네일의 css를 직접 입력할 수 있습니다. 커스텀 사용자를 위한 항목입니다.', X2B_DOMAIN ),
    //     'type'    => 'textarea',
    //     'options' => false,
    // ),
    // 'zine_ribbon'					=> array(
    //     'id'      => 'zine_ribbon',
    //     'name'    => __( '섬네일 리본', X2B_DOMAIN ),
    //     'desc'    => __( "섬네일 좌측 상단의 리본에 들어갈 변수를 출력합니다. 확장변수의 경우 위의 '웹진, 갤러리 공통메뉴'에서 번호를 설정하세요. 설정이 없으면 1번을 출력합니다.", X2B_DOMAIN ),
    //     'type'    => 'select',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '            => __( '새 글 > 업데이트(기본)', X2B_DOMAIN ),
    //         'N'        => __( '출력 안함', X2B_DOMAIN ),
    //         'new_date'            => __( '새 글 > 업데이트 > 날짜', X2B_DOMAIN ),
    //         'cate'        => __( '카테고리', X2B_DOMAIN ),
    //         'date'            => __( '날짜', X2B_DOMAIN ),
    //         'read'        => __( '읽은 수', X2B_DOMAIN ),
    //         'vote'            => __( '추천 수', X2B_DOMAIN ),
    //         'cmt'        => __( '댓글 수', X2B_DOMAIN ),
    //         'extra'            => __( '확장변수', X2B_DOMAIN ),
    //     ),
    // ),
    'zine_extra'               => array(
        'id'      => 'zine_extra',
        'name'    => __( 'skin_name_zine_extra', X2B_DOMAIN ), // 확장변수 출력(*)
        'desc'    => __( 'skin_desc_zine_extra', X2B_DOMAIN ), // 하단에 메타 정보 영역에 확장변수의 목록을 출력합니다. *모바일형과 설정을 공유합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            'Y'      => __( 'skin_opt_display_yes', X2B_DOMAIN ), // 출력
            ' '       => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력 안함(기본)
        ),
    ),
    'zine_tx_color'               => array(
        'id'      => 'zine_tx_color',
        'name'    => __( 'skin_name_zine_tx_color', X2B_DOMAIN ), // 메타 정보 색상(*)
        'desc'    => __( 'skin_desc_zine_tx_color', X2B_DOMAIN ), // 하단의 글쓴이, 조회수 등의 메타 정보의 글자 색상을 설정합니다. *모바일형과 설정을 공유합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_depend_on_board_color', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 게시판 색상에 따라(기본)
            'N'       => __( 'skin_opt_bw_tone', X2B_DOMAIN ), // 흑백톤
        ),
    ),
    'zine_info_icon'               => array(
        'id'      => 'zine_info_icon',
        'name'    => __( 'skin_name_zine_info_icon', X2B_DOMAIN ), // 메타 정보 아이콘(*)
        'desc'    => __( 'skin_desc_zine_info_icon', X2B_DOMAIN ), // 메타 정보에 아이콘을 사용합니다. *모바일형과 설정을 공유합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
            'info_icon'       => __( 'skin_opt_display_icon_only', X2B_DOMAIN ), // 아이콘만 표시
            'info_icon2'       => __( 'skin_opt_display_icon_text', X2B_DOMAIN ), // 아이콘+텍스트 표시
        ),
    ),
    'sketchbook5_setup_header7'					=> array(
        'id'      => 'sketchbook5_setup_header7',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header7', X2B_DOMAIN ), // 갤러리 형 (*표시 : '클라우드 갤러리'와 공통)
        'type'    => 'header',
        'options' => false,
    ),
    // 'gall_style'               => array(
    //     'id'      => 'gall_style',
    //     'name'    => __( "갤러리 스타일", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '이미지 아래 제목 표시(기본)', X2B_DOMAIN ),
    //         '2'       => __( "액자 스타일(전체 테두리)", X2B_DOMAIN ),
    //     ),
    // ),
    // 'gall_align'               => array(
    //     'id'      => 'gall_align',
    //     'name'    => __( "섬네일 정렬", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '가운데 정렬(기본값)', X2B_DOMAIN ),
    //         'left'       => __( "좌측 정렬", X2B_DOMAIN ),
    //     ),
    // ),
    'thumbnail_type'               => array(
        'id'      => 'thumbnail_type',
        'name'    => __( 'skin_name_thumbnail_type', X2B_DOMAIN ), // 섬네일 비율(*)
        'type'    => 'radio',
        'default' => 'crop',
        'options' => array(
            'crop'      => __( 'skin_opt_crop', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 크롭(기본)
            'ratio'       => __( 'skin_opt_ratio', X2B_DOMAIN ), // 원본 비율
        ),
    ),
    'thumbnail_width'					=> array(
        'id'      => 'thumbnail_width',
        'name'    => __( 'skin_name_thumbnail_width', X2B_DOMAIN ), // 섬네일 넓이(*)
        'desc'    => __( 'skin_desc_thumbnail_width', X2B_DOMAIN ), // '갤러리형' 및 '클라우드 갤러리형'의 섬네일에 적용됩니다. 입력하지 않으면 기본 설정은 '90' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'thumbnail_height'					=> array(
        'id'      => 'thumbnail_height',
        'name'    => __( 'skin_name_thumbnail_height', X2B_DOMAIN ), // 섬네일 높이(*)
        'desc'    => __( 'skin_desc_thumbnail_height', X2B_DOMAIN ), // '갤러리형' 및 '클라우드 갤러리형'의 섬네일에 적용됩니다. 입력하지 않으면 기본 설정은 '90' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    // 'gallery_tl'               => array(
    //     'id'      => 'gallery_tl',
    //     'name'    => __( "제목 표시(*)", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '표시(기본)', X2B_DOMAIN ),
    //         'N'       => __( "표시 안함", X2B_DOMAIN ),
    //     ),
    // ),
    // 'gall_tl_cut'					=> array(
    //     'id'      => 'gall_tl_cut',
    //     'name'    => __( '갤러리형 제목 글자 수(*)', X2B_DOMAIN ),
    //     'desc'    => __( "입력하지 않으면 기본 설정은 설정은 자르지 않습니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'gall_tl_font'               => array(
    //     'id'      => 'gall_tl_font',
    //     'name'    => __( "액자스타일 제목 글꼴", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '필기체(기본)', X2B_DOMAIN ),
    //         'ng'       => __( "글꼴 설정에 따라", X2B_DOMAIN ),
    //     ),
    // ),
    // 'li_width'					=> array(
    //     'id'      => 'li_width',
    //     'name'    => __( '섬네일 영역 넓이', X2B_DOMAIN ),
    //     'desc'    => __( "이 항목을 전체 영역에 맞게 설정하여 '가로 정렬할 이미지의 수'를 설정합니다. 예) 게시판 전체영역의 넓이가 '800px'이고 가로 갯수를 '4'개로 하고 싶은 경우 설정 넓이는 '200'으로 설정하시면 됩니다. 입력하지 않으면 섬네일 넓이에 +40 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'li_height'					=> array(
    //     'id'      => 'li_height',
    //     'name'    => __( '섬네일 영역 높이', X2B_DOMAIN ),
    //     'desc'    => __( "입력하지 않으면 섬네일 높이에 +80 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'gall_shadow'               => array(
    //     'id'      => 'gall_shadow',
    //     'name'    => __( "섬네일 그림자", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '표시(기본)', X2B_DOMAIN ),
    //         'N'       => __( "표시 안함", X2B_DOMAIN ),
    //     ),
    // ),
    // 'thumb_css'     => array(
    //     'id'      => 'thumb_css',
    //     'name'    => __( '섬네일 css', X2B_DOMAIN ),
    //     'desc'    => __( '섬네일의 css를 직접 입력할 수 있습니다. 커스텀 사용자를 위한 항목입니다.', X2B_DOMAIN ),
    //     'type'    => 'textarea',
    //     'options' => false,
    // ),
    // 'tmb_hover'               => array(
    //     'id'      => 'tmb_hover',
    //     'name'    => __( "섬네일 마우스 오버 효과", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( 'Fade-In(기본)', X2B_DOMAIN ),
    //         '2'       => __( "위에서 닦아내기", X2B_DOMAIN ),
    //         '3'       => __( "옆에서 닦아내기", X2B_DOMAIN ),
    //         '4'       => __( "우측 하단으로 닦아내기", X2B_DOMAIN ),
    //         'R'       => __( "랜덤효과", X2B_DOMAIN ),
    //         'N'       => __( "없음", X2B_DOMAIN ),
    //     ),
    // ),
    // 'tmb_hover_bg'               => array(
    //     'id'      => 'tmb_hover_bg',
    //     'name'    => __( "섬네일 마우스 오버 배경색", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '검은색+투명(기본)', X2B_DOMAIN ),
    //         '2'       => __( "'게시판 효과 색상'", X2B_DOMAIN ),
    //         '3'       => __( "'게시판 효과 색상'+투명", X2B_DOMAIN ),
    //     ),
    // ),
    // 'gall_ribbon'					=> array(
    //     'id'      => 'gall_ribbon',
    //     'name'    => __( '좌측 상단 리본', X2B_DOMAIN ),
    //     'desc'    => __( "섬네일 좌측 상단의 리본에 들어갈 변수를 출력합니다. 확장변수의 경우 위의 '웹진, 갤러리 공통메뉴'에서 번호를 설정하세요. 설정이 없으면 1번을 출력합니다.", X2B_DOMAIN ),
    //     'type'    => 'select',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '            => __( '새 글 > 업데이트 > 날짜(기본)', X2B_DOMAIN ),
    //         'N'        => __( '표시 안함', X2B_DOMAIN ),
    //         'date'            => __( '날짜', X2B_DOMAIN ),
    //         'new_update'        => __( '새 글 > 업데이트', X2B_DOMAIN ),
    //         'cate'            => __( '카테고리', X2B_DOMAIN ),
    //         'read'        => __( '읽은 수', X2B_DOMAIN ),
    //         'vote'            => __( '추천 수', X2B_DOMAIN ),
    //         'cmt'        => __( '댓글 수', X2B_DOMAIN ),
    //         'extra'            => __( '확장변수1', X2B_DOMAIN ),
    //     ),
    // ),
    // 'trans_window'					=> array(
    //     'id'      => 'trans_window',
    //     'name'    => __( '우측 하단 투명창', X2B_DOMAIN ),
    //     'desc'    => __( "섬네일 우측 하단에 들어갈 변수를 출력합니다. 확장변수의 경우 아래에서 출력할 확장변수 번호를 설정하세요. 설정이 없으면 1번을 출력합니다.", X2B_DOMAIN ),
    //     'type'    => 'select',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '            => __( '글쓴이(기본)', X2B_DOMAIN ),
    //         'N'        => __( '표시 안함', X2B_DOMAIN ),
    //         'date'            => __( '날짜', X2B_DOMAIN ),
    //         'cate'            => __( '카테고리', X2B_DOMAIN ),
    //         'read'        => __( '읽은 수', X2B_DOMAIN ),
    //         'vote'            => __( '추천 수', X2B_DOMAIN ),
    //         'cmt'        => __( '댓글 수', X2B_DOMAIN ),
    //         'extra'            => __( '확장변수2', X2B_DOMAIN ),
    //     ),
    // ),
    'extra_num2'					=> array(
        'id'      => 'extra_num2',
        'name'    => __( 'skin_name_extra_num2', X2B_DOMAIN ), // 투명창 확장변수 번호
        'desc'    => __( 'skin_desc_extra_num2', X2B_DOMAIN ), // '우측 하단 투명창'에서 확장변수 사용 시 선택할 확장변수의 번호를 설정합니다. 설정이 없으면 1번이 지정됩니다,
        'type'    => 'text',
        'options' => false,
    ),
    // 'deco'               => array(
    //     'id'      => 'deco',
    //     'name'    => __( "데코레이션", X2B_DOMAIN ),
    //     'desc'    => __( "섬네일을 꾸밉니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '책갈피 스타일(기본)', X2B_DOMAIN ),
    //         '2'       => __( "테이프 스타일", X2B_DOMAIN ),
    //         'N'       => __( "표시 안함", X2B_DOMAIN ),
    //     ),
    // ),
    'deco_img'					=> array(
        'id'      => 'deco_img',
        'name'    => __( 'skin_name_deco_img', X2B_DOMAIN ), // 데코레이션 이미지
        'desc'    => __( 'skin_desc_deco_img', X2B_DOMAIN ), // 기본 제공되는 데코레이션 이미지 외에 사용자가 직접 이미지를 등록할 수 있습니다. 상단 가운데에 위치하게 됩니다.
        'type'    => 'image',
        'options' => false,
    ),
    // 'gall_deg'					=> array(
    //     'id'      => 'gall_deg',
    //     'name'    => __( '섬네일의 기울기', X2B_DOMAIN ),
    //     'desc'    => __( "섬네일의 기울기를 사용합니다. 숫자(양수)를 입력하여 범위를 설정합니다. 단위는 각도(0~180)로 입니다. 입력하지 않으면 사용하지 않습니다. *css3를 지원하는 브라우저에서만 적용됩니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // // <var name="gall_hover_img" type="image">
    // //     <title xml:lang="ko">마우스 오버 이미지</title>
    // //     <description xml:lang="ko">마우스 오버했을 때의 이미지를 사용자가 직접 이미지를 등록할 수 있습니다. 가운데에 위치하게 됩니다.</description>
    // // </var>
    // 'sketchbook5_setup_header8'					=> array(
    //     'id'      => 'sketchbook5_setup_header8',
    //     'desc'    => __( 'skin_desc_sketchbook5_setup_header8', X2B_DOMAIN ), // 클라우드 갤러리
    //     'type'    => 'header',
    //     'options' => false,
    // ),
    // 'cloud_rand'               => array(
    //     'id'      => 'cloud_rand',
    //     'name'    => __( "섬네일의 무작위 위치", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '처음부터(기본)', X2B_DOMAIN ),
    //         'N'       => __( "적용 안함", X2B_DOMAIN ),
    //     ),
    // ),
    // 'cloud_y'					=> array(
    //     'id'      => 'cloud_y',
    //     'name'    => __( '갤러리 높이', X2B_DOMAIN ),
    //     'desc'    => __( "갤러리의 높이(=섬네일의 무작위 세로 위치의 범위)를 설정합니다. 설정하지 않으면 기본 '600' 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'cloud_rotate'               => array(
    //     'id'      => 'cloud_rotate',
    //     'name'    => __( "섬네일 기울기", X2B_DOMAIN ),
    //     'desc'    => __( "*css3를 지원하는 브라우저에서만 적용됩니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '처음부터(기본)', X2B_DOMAIN ),
    //         'N'       => __( "적용 안함", X2B_DOMAIN ),
    //     ),
    // ),
    // 'cloud_deg'					=> array(
    //     'id'      => 'cloud_deg',
    //     'name'    => __( '섬네일 기울기 범위', X2B_DOMAIN ),
    //     'desc'    => __( "섬네일의 기울기 좌우 범위를 설정합니다. 단위는 각도(0~180)로 '숫자(양수)'만 입력하세요. 설정하지 않으면 기본 '25' 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'cloud_btn'               => array(
    //     'id'      => 'cloud_btn',
    //     'name'    => __( "무작위, 기울기, 재위치 버튼", X2B_DOMAIN ),
    //     'desc'    => __( "이 버튼들을 표시하면 관리자가 설정한 무작위 위치, 기울기의 최초 설정보다 사용자의 해당 버튼 활성화 여부가 우선합니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '표시(기본)', X2B_DOMAIN ),
    //         'N'       => __( "표시 안함", X2B_DOMAIN ),
    //     ),
    // ),
    // 'cloud_margin'					=> array(
    //     'id'      => 'cloud_margin',
    //     'name'    => __( '섬네일의 여백', X2B_DOMAIN ),
    //     'desc'    => __( "'무작위 위치'를 사용하지 않을 경우의 정렬된 이미지의 여백을 설정합니다. 설정하지 않으면 기본 '12' 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'sketchbook5_setup_header9'					=> array(
    //     'id'      => 'sketchbook5_setup_header9',
    //     'desc'    => __( 'skin_desc_sketchbook5_setup_header9', X2B_DOMAIN ), // FAQ 게시판 형
    //     'type'    => 'header',
    //     'options' => false,
    // ),
    'faq_style'               => array(
        'id'      => 'faq_style',
        'name'    => __( "스타일 프리셋", X2B_DOMAIN ),
        'desc'    => __( "FAQ 게시판의 스타일을 설정합니다. '공식 FAQ모듈 스타일'은 공식 FAQ모듈과 유사한 스타일로 변형시킵니다.", X2B_DOMAIN ),
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( '기본값(기본)', X2B_DOMAIN ),
            'faq_official'       => __( "공식 FAQ모듈 스타일", X2B_DOMAIN ),
        ),
    ),
    // 'sketchbook5_setup_header10'					=> array(
    //     'id'      => 'sketchbook5_setup_header10',
    //     'desc'    => __( 'skin_desc_sketchbook5_setup_header10', X2B_DOMAIN ), // 블로그 형
    //     'type'    => 'header',
    //     'options' => false,
    // ),
    // 'blog_style'               => array(
    //     'id'      => 'blog_style',
    //     'name'    => __( "스타일 프리셋", X2B_DOMAIN ),
    //     'desc'    => __( "블로그형 스타일을 설정합니다. 외곽 영역에서 테두리와 그림자 여부를 설정합니다. *익스8 이하의 경우 'css3pie 애드온'을 활성화하여야 적용이 됩니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '테두리+그림자(기본)', X2B_DOMAIN ),
    //         'no_style'       => __( "없음", X2B_DOMAIN ),
    //     ),
    // ),
    'sketchbook5_setup_header11'					=> array(
        'id'      => 'sketchbook5_setup_header11',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header11', X2B_DOMAIN ), // 글쓰기 및 댓글쓰기 설정
        'type'    => 'header',
        'options' => false,
    ),
    'tl_color'               => array(
        'id'      => 'tl_color',
        'name'    => __( 'skin_name_tl_color', X2B_DOMAIN ), // '제목 색깔' 및 '제목 굵기' 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_admin_only', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 관리자만(기본)
            '2'       => __( 'skin_opt_allow_all', X2B_DOMAIN ), // 모든 사용자
        ),
    ),
    /*'wrt_opt'                       => array(
        'id'      => 'wrt_opt',
        'name'    => __( '쓰기 옵션 기본값 설정', X2B_DOMAIN ),
        'type'    => 'multicheck',
        'options' => array(
            'notify'            => __( '알림 기본 체크', X2B_DOMAIN ),
            'secret'            => __( '비밀글 기본 체크', X2B_DOMAIN ),
        ),
        // 'mandatory' => array(
        //     'PUBLIC'            => 'mandatory',
        // ),
    ),*/
    // 'content_default'     => array(
    //     'id'      => 'content_default',
    //     'name'    => __( '글 작성시 미리 입력된 글 출력', X2B_DOMAIN ),
    //     'desc'    => __( '글쓰기 화면에서 글쓰기 창 안에 표시될 글을 출력하는 기능입니다. 미리 준비된 양식이나 글쓰기 안내글 등에 사용할 수 있습니다. HTML을 사용 할 수 있습니다.', X2B_DOMAIN ),
    //     'type'    => 'textarea',
    //     'options' => false,
    // ),
    'cmt_wrt'               => array(
        'id'      => 'cmt_wrt',
        'name'    => __( 'skin_name_cmt_wrt', X2B_DOMAIN ), // 댓글 에디터 설정
        'desc'    => __( 'skin_desc_cmt_wrt', X2B_DOMAIN ), // (1) Textarea : 에디터를 불러오지 않고 글을 쓸 수 있습니다. (2) 에디터
        'type'    => 'radio',
        'default' => 'simple',
        'options' => array(
            'simple'      => __( 'skin_opt_editor_textarea', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // Textarea(기본)
            'editor'       => __( 'skin_opt_editor_editor', X2B_DOMAIN ), // 에디터
            // 'sns'       => __( "SocialXE", X2B_DOMAIN ),
        ),
    ),
    'select_editor'               => array(
        'id'      => 'select_editor',
        'name'    => __( 'skin_name_select_editor', X2B_DOMAIN ), // 에디터 선택 버튼 표시
        'desc'    => __( 'skin_desc_select_editor', X2B_DOMAIN ), // 본문의 댓글 영역에 사용자가 에디터를 선택할 수 있는 버튼을 표시합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_editor_textarea', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // Textarea, 에디터(기본)
            // '2'       => __( "+ SocialXE 표시", X2B_DOMAIN ),
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    // 'auto_view_sub'               => array(
    //     'id'      => 'auto_view_sub',
    //     'name'    => __( "*SocialXE 대댓글 자동 펼치기", X2B_DOMAIN ),
    //     'desc'    => __( "위의 '댓글창 방식 옵션'에서 'SocialXE'를 선택한 경우, '대댓글 자동 펼치기' 옵션 사용 여부를 결정합니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '사용(기본)', X2B_DOMAIN ),
    //         'Y'       => __( "사용 안함", X2B_DOMAIN ),
    //     ),
    // ),
    'sketchbook5_setup_header12'					=> array(
        'id'      => 'sketchbook5_setup_header12',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header12', X2B_DOMAIN ), // 첨부파일
        'type'    => 'header',
        'options' => false,
    ),
    'show_files'               => array(
        'id'      => 'show_files',
        'name'    => __( 'skin_name_show_files', X2B_DOMAIN ), // 첨부파일 표시 방법
        'type'    => 'radio',
        'default' => '3',
        'options' => array(
            ' '      => __( 'skin_opt_hide_disply_btn', X2B_DOMAIN ), // 숨기고 버튼 표시
            '2'       => __( 'skin_opt_display_on_bottom', X2B_DOMAIN ), // 하단 표시
            '3'       => __( 'skin_opt_display_below_subject', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 상단(제목 밑에) 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'files_type'               => array(
        'id'      => 'files_type',
        'name'    => __( 'skin_name_files_type', X2B_DOMAIN ), // 확장자 필터 기능
        'desc'    => __( 'skin_desc_files_type', X2B_DOMAIN ), // 첨부파일에서 이미지, 동영상, 음악 파일을 제외하는 기능을 사용합니다. 아래의 옵션에서 출력할 파일 타입을 선택하세요.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            'Y'       => __( 'skin_opt_use_yes', X2B_DOMAIN ), // 사용
            ' '      => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용 안함(기본)
        ),
    ),
    'files_img'               => array(
        'id'      => 'files_img',
        'name'    => __( 'skin_name_files_img', X2B_DOMAIN ), // 이미지 파일
        'desc'    => __( 'skin_desc_files_img', X2B_DOMAIN ), // 첨부파일에서 이미지 파일(jpg, jpeg, gif, png)의 출력 여부를 선택합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 출력 안함
        ),
    ),
    'files_video'               => array(
        'id'      => 'files_video',
        'name'    => __( 'skin_name_files_video', X2B_DOMAIN ), // 동영상 파일
        'desc'    => __( 'skin_desc_files_video', X2B_DOMAIN ), //첨부파일에서 동영상 파일(mpg, mpeg, avi, wmv, mp4, mov, mkv, swf, flv, ogv, webm)의 출력 여부를 선택합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 출력 안함
        ),
    ),
    'files_audio'               => array(
        'id'      => 'files_audio',
        'name'    => __( 'skin_name_files_audio', X2B_DOMAIN ), // 음악 파일
        'desc'    => __( 'skin_desc_files_audio', X2B_DOMAIN ), // 첨부파일에서 음악 파일(mp3, ogg, wma, wav, ape, flac, mid)의 출력 여부를 선택합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 출력 안함
        ),
    ),
    'files_etc'               => array(
        'id'      => 'files_etc',
        'name'    => __( 'skin_name_files_etc', X2B_DOMAIN ), // 기타 파일
        'desc'    => __( 'skin_desc_files_etc', X2B_DOMAIN ), // 위의 3가지 파일 타입(이미지, 비디오, 오디오)을 제외한 나머지 파일의 출력 여부를 선택합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 출력(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 출력 안함
        ),
    ),
    'sketchbook5_setup_header13'					=> array(
        'id'      => 'sketchbook5_setup_header13',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header13', X2B_DOMAIN ), // 본문 이미지 설정
        'type'    => 'header',
        'options' => false,
    ),
    'img_opt'  => array(
        'id'      => 'img_opt',
        'name'    => __( 'skin_name_img_opt', X2B_DOMAIN ), // 이미지 부가 기능
        'desc'    => __( 'skin_desc_img_opt', X2B_DOMAIN ), // 1. 이미지 드래그 : 본문에 삽입된 이미지를 마우스로 드래그할 수 있습니다.
        'type'    => 'multicheck',
        'options' => array(
            'drag'            => __( 'skin_opt_image_drag', X2B_DOMAIN ), // 이미지 드래그
        ),
    ),
    'img_insert'               => array(
        'id'      => 'img_insert',
        'name'    => __( 'skin_name_img_insert', X2B_DOMAIN ),  // 이미지 자동 출력
        'desc'    => __( 'skin_desc_img_insert', X2B_DOMAIN ), // 첨부된 이미지를 본문에 자동 출력하는 기능입니다. *주의 : 본문에 에디터로 '삽입'하는 형식과는 달리 단순히 첨부된 이미지 파일을 보여주는 방법입니다. 추후 게시판 스킨을 다른 것으로 사용하거나 마이그레이션 하는 경우에 본문에 이미지가 표시되지 않으니 주의하세요.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            '2'      => __( 'skin_opt_upper_body', X2B_DOMAIN ),  // 본문 상단
            '3'       => __( 'skin_opt_bottom_body', X2B_DOMAIN ),  // 본문 하단
            ' '       => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용 안함(기본)
        ),
    ),
    // 'img_insert2'               => array(
    //     'id'      => 'img_insert2',
    //     'name'    => __( "특정 이미지만 자동 출력", X2B_DOMAIN ),
    //     'desc'    => __( "첨부된 이미지 중에 파일 이름이 '_rd_gallery'로 끝나는 특정 이미지 파일만 자동 출력하는 기능입니다. 이 기능으로 특정 이미지만 자동 출력할 수 있습니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         'Y'      => __( '사용', X2B_DOMAIN ),
    //         ' '       => __( "사용 안함(기본)", X2B_DOMAIN ),
    //     ),
    // ),
    'img_insert_align'               => array(
        'id'      => 'img_insert_align',
        'name'    => __( 'skin_name_img_insert_align', X2B_DOMAIN ), // 자동 출력된 이미지 정렬
        'desc'    => __( 'skin_desc_img_insert_align', X2B_DOMAIN ), // 자동 출력되는 이미지를 정렬합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            'Y'      => __( 'skin_opt_middle', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 가운데(기본)
            'left'       => __( 'skin_opt_side_left', X2B_DOMAIN ), // 좌측
            'right'       => __( 'skin_opt_side_right', X2B_DOMAIN ), // 우측
        ),
    ),
    'img_insert_width'     => array(
        'id'      => 'img_insert_width',
        'name'    => __( 'skin_name_img_insert_width', X2B_DOMAIN ), // 자동 출력된 이미지 넓이
        'desc'    => __( 'skin_desc_img_insert_width', X2B_DOMAIN ), // 자동 출력되는 이미지의 넓이를 설정합니다. '단위(px 또는 %)'까지 입력하세요. 입력하지 않으면 제한하지 않습니다.
        'type'    => 'text',
        'options' => false,
    ),
    'img_insert_height'     => array(
        'id'      => 'img_insert_height',
        'name'    => __( 'skin_name_img_insert_height', X2B_DOMAIN ), // 자동 출력된 이미지 높이
        'desc'    => __( 'skin_desc_img_insert_height', X2B_DOMAIN ), // 자동 출력되는 이미지의 높이를 설정합니다. '단위(px 또는 %)'까지 입력하세요. 입력하지 않으면 제한하지 않습니다.
        'type'    => 'text',
        'options' => false,
    ),
    'img_insert_css'     => array(
        'id'      => 'img_insert_css',
        'name'    => __( 'skin_name_img_insert_css', X2B_DOMAIN ), // 자동 출력된 갤러리 영역 CSS
        'desc'    => __( 'skin_desc_img_insert_css', X2B_DOMAIN ), // 자동 출력되는 이미지를 감싸는 갤러리 영역의 스타일(CSS)을 직접 입력합니다.
        'type'    => 'textarea',
        'options' => false,
    ),
    'img_insert_img_css'     => array(
        'id'      => 'img_insert_img_css',
        'name'    => __( 'skin_name_img_insert_img_css', X2B_DOMAIN ), // 자동 출력된 이미지 CSS
        'desc'    => __( 'skin_desc_img_insert_img_css', X2B_DOMAIN ), // 자동 출력되는 이미지의 스타일(CSS)을 직접 입력합니다.
        'type'    => 'textarea',
        'options' => false,
    ),
    'sketchbook5_setup_header14'					=> array(
        'id'      => 'sketchbook5_setup_header14',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header14', X2B_DOMAIN ), // 본문 일반 설정
        'type'    => 'header',
        'options' => false,
    ),
    'rd_padding'     => array(
        'id'      => 'rd_padding',
        'name'    => __( 'skin_name_rd_padding', X2B_DOMAIN ), // 본문 전체 여백 설정
        'desc'    => __( 'skin_desc_rd_padding', X2B_DOMAIN ), // 본문의 전체 여백(padding)을 설정합니다. 사용자의 레이아웃에 따라 본문 폭이 달라 필요한 영역 확보를 하지 못하는 경우를 위해 옵션을 두었습니다. 일반 css 입력과 동일하므로 'px', 'em', '%' 등 '단위'까지 반드시(!) 입력해야 합니다(※ '0'으로 사용할 경우 '0'은 저장되지 않으니 '0px'을 입력하세요). 입력하지 않으면 기본설정은 '0 15px' 입니다. 예) '0px', '10px', '10px 20px 30px 10px', '3% 0' 등
        'type'    => 'text',
        'options' => false,
    ),
    'rd_nav'               => array(
        'id'      => 'rd_nav',
        'name'    => __( 'skin_name_rd_nav', X2B_DOMAIN ), // 본문 상단 내비
        'desc'    => __( 'skin_desc_rd_nav', X2B_DOMAIN ), // 본문을 읽는 데 도움을 주는 기능('글자의 확대-축소', '상단-댓글-목록으로 이동', '프린트', '첨부파일', '이 게시물을', '수정', '삭제' 등)을 모은 내비게이션입니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_below_subject', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 제목 영역 아래(기본)
            '2'       => __( 'skin_opt_display_above_subject', X2B_DOMAIN ), // 제목 영역 위
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_nav_style'               => array(
        'id'      => 'rd_nav_style',
        'name'    => __( 'skin_name_rd_nav_style', X2B_DOMAIN ), // 본문 내비 스타일
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_border_box', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 테두리+박스(기본)
            '2'       => __( 'skin_opt_border_no', X2B_DOMAIN ), // 테두리 없음
        ),
    ),
    'rd_nav_tx'               => array(
        'id'      => 'rd_nav_tx',
        'name'    => __( 'skin_name_rd_nav_tx', X2B_DOMAIN ), // 본문 내비 텍스트 표시
        'desc'    => __( 'skin_desc_rd_nav_tx', X2B_DOMAIN ), // 아이콘 옆에 텍스트를 표시합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            'Y'      => __( 'skin_opt_display_text', X2B_DOMAIN ), // 텍스트 표시
            ' '       => __( 'skin_opt_hide_text', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 텍스트 표시 안함(기본)
        ),
    ),
    'rd_nav_side'               => array(
        'id'      => 'rd_nav_side',
        'name'    => __( 'skin_name_rd_nav_side', X2B_DOMAIN ), // 본문 사이드 내비
        'desc'    => __( 'skin_desc_rd_nav_side', X2B_DOMAIN ), // 처음에는 보이지 않다가 스크롤 하면 상단 내비가 윈도우 영역에서 사라지면 우측 하단에 나타나는 내비의 사용 여부를 결정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용(기본)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'rd_nav_item'               => array(
        'id'      => 'rd_nav_item',
        'name'    => __( 'skin_name_rd_nav_item', X2B_DOMAIN ), // 본문 내비 프린트 버튼
        'desc'    => __( 'skin_desc_rd_nav_item', X2B_DOMAIN ), // 본문 내비의 프린트 버튼의 표시 여부를 설정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'prev_next'               => array(
        'id'      => 'prev_next',
        'name'    => __( 'skin_name_prev_next', X2B_DOMAIN ), // 이전글-다음글
        'desc'    => __( 'skin_desc_prev_next', X2B_DOMAIN ), // 본문 하단의 링크, 키보드의 방향키 '←', '→' 을 사용하여 이전글, 다음글로 이동할 수 있습니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_below_body_text_link_hotkey', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 본문 하단 텍스트 링크+단축키 사용(기본)
            '2'       => __( 'skin_opt_body_navi_icon_btn_hotkey', X2B_DOMAIN ), // 본문 내비 아이콘 버튼+단축키 사용
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'prev_next_cut_size'     => array(
        'id'      => 'prev_next_cut_size',
        'name'    => __( 'skin_name_prev_next_cut_size', X2B_DOMAIN ), // 이전글-다음글 제목 글자수 제한
        'desc'    => __( 'skin_desc_prev_next_cut_size', X2B_DOMAIN ), // 이전글-다음글의 글자수를 제한합니다. 기본값은 '60' 입니다.
        'type'    => 'text',
        'options' => false,
    ),
    'no_attached_img'					=> array(
        'id'      => 'no_attached_img',
        'name'    => __( 'skin_name_no_attached_img', X2B_DOMAIN ), // 본문에 이미지가 없는 경우
        'desc'    => __( 'skin_desc_no_attached_img', X2B_DOMAIN ), // 본문에 이미지가 없는 경우, 본문 상단에 미리 등록된 이미지를 표시합니다. 이 항목에서 이미지를 등록하시면 활성화됩니다.
        'type'    => 'image',
        'options' => false,
    ),
    'et_var'               => array(
        'id'      => 'et_var',
        'name'    => __( 'skin_name_et_var', X2B_DOMAIN ), // 확장변수 위치
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_in_the_body', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 본문 안에(기본)
            '2'       => __( 'skin_opt_below_subject', X2B_DOMAIN ), // 제목 아래
        ),
    ),
    'display_sign'               => array(
        'id'      => 'display_sign',
        'name'    => __( 'skin_name_display_sign', X2B_DOMAIN ), // 글쓴이 서명 표시
        'type'    => 'radio',
        'default' => 'N',
        'options' => array(
            'Y'      => __( 'skin_opt_display_yes', X2B_DOMAIN ), // 표시
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
        ),
    ),
    'votes'               => array(
        'id'      => 'votes',
        'name'    => __( 'skin_name_votes', X2B_DOMAIN ), // 추천/비추천 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            '2'       => __( 'skin_opt_display_recommend_only', X2B_DOMAIN ), // 추천만 표시
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'declare'               => array(
        'id'      => 'declare',
        'name'    => __( 'skin_name_declare', X2B_DOMAIN ), // 신고 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
            'Y'       => __( 'skin_opt_display_yes', X2B_DOMAIN ), // 표시
        ),
    ),
    'to_sns'               => array(
        'id'      => 'to_sns',
        'name'    => __( 'skin_name_to_sns', X2B_DOMAIN ), // SNS로 보내기 버튼
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_small_icon_bottom_editor', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 작은 아이콘 + 하단 편집 영역(기본)
            '2'       => __( 'skin_opt_small_icon_upper', X2B_DOMAIN ), // 작은 아이콘 + 상단
            '3'       => __( 'skin_opt_big_icon_below_body', X2B_DOMAIN ), // 큰 아이콘(본문 하단)
            '4'       => __( 'skin_opt_custom_input_below_body', X2B_DOMAIN ), // 커스텀 입력(본문 하단)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'to_sns_small'               => array(
        'id'      => 'to_sns_small',
        'name'    => __( 'skin_name_to_sns_small', X2B_DOMAIN ), // SNS 작은 버튼 텍스트 표시
        'desc'    => __( 'skin_desc_to_sns_small', X2B_DOMAIN ), // 위의 항목에서 '작은 아이콘' 사용 시에 아이콘 옆에 텍스트의 사용 여부를 설정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'bubble'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    // 'to_sns_big'               => array(
    //     'id'      => 'to_sns_big',
    //     'name'    => __( "SNS 큰 버튼 정렬", X2B_DOMAIN ),
    //     'desc'    => __( "위의 항목에서 '큰 아이콘' 사용 시에 정렬을 설정합니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '가운데(기본)', X2B_DOMAIN ),
    //         'left'       => __( "왼쪽", X2B_DOMAIN ),
    //         'right'       => __( "오른쪽", X2B_DOMAIN ),
    //     ),
    // ),
    'to_sns_content'     => array(
        'id'      => 'to_sns_content',
        'name'    => __( 'skin_name_to_sns_content', X2B_DOMAIN ), // SNS 커스텀 버튼
        'desc'    => __( 'skin_desc_to_sns_content', X2B_DOMAIN ), // 위의 항목에서 '커스텀 입력' 사용 시에 직접 코드를 입력할 수 있습니다. 예제 버튼은 매뉴얼 혹은 피드백 사이트를 참조하세요.
        'type'    => 'textarea',
        'options' => false,
    ),
    'rd_ft_nav'  => array(
        'id'      => 'rd_ft_nav',
        'name'    => __( 'skin_name_rd_ft_nav', X2B_DOMAIN ), // 본문 하단 메뉴 영역
        'type'    => 'multicheck',
        'options' => array(
            'lst_btn'            => __( 'skin_opt_display_list_btn', X2B_DOMAIN ), // 목록 버튼 표시
        ),
    ),
    'sketchbook5_setup_header15'					=> array(
        'id'      => 'sketchbook5_setup_header15',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header15', X2B_DOMAIN ), // 피드백(트랙백, 댓글) 목록 설정
        'type'    => 'header',
        'options' => false,
    ),
    'fdb_style'               => array(
        'id'      => 'fdb_style',
        'name'    => __( 'skin_name_fdb_style', X2B_DOMAIN ), // 댓글 스타일
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_borderless', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 테두리 없는(기본)
            'fdb_v2'       => __( 'skin_opt_border_nametag', X2B_DOMAIN ), // 테두리+네임택
        ),
    ),
    'profile_img'               => array(
        'id'      => 'profile_img',
        'name'    => __( 'skin_name_profile_img', X2B_DOMAIN ), // 프로필 이미지 표시
        'desc'    => __( 'skin_desc_profile_img', X2B_DOMAIN ), // 댓글의 좌측, 방명록의 프로필 이미지를 보이게 할 것인지를 결정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'no_profile'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    // 'fdb_hide'               => array(
    //     'id'      => 'fdb_hide',
    //     'name'    => __( "피드백 목록 최초 감추기", X2B_DOMAIN ),
    //     'desc'    => __( "처음에 댓글, 트랙백 목록이 숨겨져 있다가 'Comment' or 'Trackback' 버튼 클릭 시 펼쳐집니다.", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         'fdb_hide'      => __( '사용', X2B_DOMAIN ),
    //         ' '       => __( "사용 안함(기본)", X2B_DOMAIN ),
    //     ),
    // ),
    'cmt_wrt_position'               => array(
        'id'      => 'cmt_wrt_position',
        'name'    => __( 'skin_name_cmt_wrt_position', X2B_DOMAIN ), // 댓글 에디터 위치
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_above_comment_list', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 댓글 목록 위(기본)
            'cmt_wrt_btm'       => __( 'skin_opt_below_comment_list', X2B_DOMAIN ), // 댓글 목록 아래
        ),
    ),
    'fdb_nav'               => array(
        'id'      => 'fdb_nav',
        'name'    => __( 'skin_name_fdb_nav', X2B_DOMAIN ), // 댓글 편집창 위치
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_above_list_display_mouse_over', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 댓글 상단 + 숨기고 마우스 오버시 표시(기본)
            'fdb_nav_btm'       => __( 'skin_opt_below_comment_display_reply_reply', X2B_DOMAIN ), // 댓글 하단 + 대댓글 버튼 표시
        ),
    ),
    'cmt_this_btn'               => array(
        'id'      => 'cmt_this_btn',
        'name'    => __( 'skin_name_cmt_this_btn', X2B_DOMAIN ), // '이 댓글을' 표시
        'desc'    => __( 'skin_desc_cmt_this_btn', X2B_DOMAIN ), // 기본으로 표시되는 '이 댓글을' 표시할 것인 지, 해당 목록에 포함되는 '추천, 비추천, 신고'를 직접 외부로 표시할 것인지를 결정합니다. 관리자에게는 '이 댓글을' 버튼이 항상 보입니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            '2'       => __( 'skin_opt_display_no_display_like_dislike_declare', X2B_DOMAIN ), // 표시안함 + '추천, 비추천, 신고' 표시
        ),
    ),
    'cmt_vote'               => array(
        'id'      => 'cmt_vote',
        'name'    => __( 'skin_name_cmt_vote', X2B_DOMAIN ), // 댓글 추천-비추천 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_if_like_dislike', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 추천-비추천이 있는 경우 표시(기본)
            '2'       => __( 'skin_opt_display_always', X2B_DOMAIN ), // 항상 표시
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'cmt_vote_down'               => array(
        'id'      => 'cmt_vote_down',
        'name'    => __( 'skin_name_cmt_vote_down', X2B_DOMAIN ), // 댓글 비추천 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'sketchbook5_setup_header16'					=> array(
        'id'      => 'sketchbook5_setup_header16',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header16', X2B_DOMAIN ), // 본문 제목 공통 설정
        'type'    => 'header',
        'options' => false,
    ),
    'rd_style'               => array(
        'id'      => 'rd_style',
        'name'    => __( 'skin_name_rd_style', X2B_DOMAIN ), // 프리셋
        'desc'    => __( 'skin_desc_rd_style', X2B_DOMAIN ), // '뷰어로 보기' 시에는 '블로그 스타일'로 표시됩니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_style_board', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 게시판 스타일(기본)
            'blog'       => __( 'skin_opt_style_blog', X2B_DOMAIN ), // 블로그 스타일
        ),
    ),
    'rd_css'     => array(
        'id'      => 'rd_css',
        'name'    => __( 'skin_name_rd_css', X2B_DOMAIN ), // 전체 제목 영역 CSS
        'desc'    => __( 'skin_desc_rd_css', X2B_DOMAIN ), // 제목를 둘러싸는 전체 영역의 스타일(CSS)을 직접 입력합니다.
        'type'    => 'textarea',
        'options' => false,
    ),
    'rd_tl_font'               => array(
        'id'      => 'rd_tl_font',
        'name'    => __( 'skin_name_rd_tl_font', X2B_DOMAIN ), // 제목 글꼴
        'desc'    => __( 'skin_desc_rd_tl_font', X2B_DOMAIN ), // '게시판 스타일'의 경우 '상단영역' 전체에 적용됩니다. *'나눔글꼴'을 사용하시는 경우에 가장 미려하게 보입니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_font_gothic', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 고딕체(기본)
            'nmeb'       => __( 'skin_opt_font_nmeb', X2B_DOMAIN ), // 명조체
            'np'       => __( 'skin_opt_font_np', X2B_DOMAIN ), // 필기체
        ),
    ),
    'rd_tl_css'     => array(
        'id'      => 'rd_tl_css',
        'name'    => __( 'skin_name_rd_tl_css', X2B_DOMAIN ), // 제목 CSS 입력
        'desc'    => __( 'skin_desc_rd_tl_css', X2B_DOMAIN ), // 제목의 스타일(CSS)을 직접 입력합니다.
        'type'    => 'textarea',
        'options' => false,
    ),
    'sketchbook5_setup_header17'					=> array(
        'id'      => 'sketchbook5_setup_header17',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header17', X2B_DOMAIN ), // 본문 '게시판 스타일' 제목 설정
        'type'    => 'header',
        'options' => false,
    ),
    'rd_board_style'               => array(
        'id'      => 'rd_board_style',
        'name'    => __( 'skin_name_rd_board_style', X2B_DOMAIN ), // 게시판 스타일 프리셋
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_basic_board', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 기본 게시판(기본)
            'xe_v3'       => __( 'skin_opt_style_xe_v3', X2B_DOMAIN ), // XE v3 스타일
        ),
    ),
    'rd_cate'               => array(
        'id'      => 'rd_cate',
        'name'    => __( 'skin_name_rd_cate', X2B_DOMAIN ), // 카테고리
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_upper_left', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 좌측 상단(기본)
            '2'       => __( 'skin_opt_bottom_left', X2B_DOMAIN ), // 좌측 하단
            '3'       => __( 'skin_opt_bottom_right', X2B_DOMAIN ), // 우측 하단
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_date'               => array(
        'id'      => 'rd_date',
        'name'    => __( 'skin_name_rd_date', X2B_DOMAIN ), // 날짜
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_upper_right', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 우측 상단(기본)
            '2'       => __( 'skin_opt_bottom_left', X2B_DOMAIN ), //좌측 하단
            '3'       => __( 'skin_opt_bottom_right', X2B_DOMAIN ), // 우측 하단
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), //표시 안함
        ),
    ),
    'rd_nick'               => array(
        'id'      => 'rd_nick',
        'name'    => __( 'skin_name_rd_nick', X2B_DOMAIN ), // 글쓴이
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_bottom_left', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 좌측 하단(기본)
            '2'       => __( 'skin_opt_upper_right', X2B_DOMAIN ), // 우측 상단
            '3'       => __( 'skin_opt_bottom_right', X2B_DOMAIN ), // 우측 하단
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_info'               => array(
        'id'      => 'rd_info',
        'name'    => __( 'skin_name_rd_info', X2B_DOMAIN ), // 조회/추천/댓글 위치
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_bottom_right', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 우측 하단(기본)
            '2'       => __( 'skin_opt_bottom_left', X2B_DOMAIN ), // 좌측 하단
        ),
    ),
    'rd_view'               => array(
        'id'      => 'rd_view',
        'name'    => __( 'skin_name_rd_view', X2B_DOMAIN ), // 조회수
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_vote'               => array(
        'id'      => 'rd_vote',
        'name'    => __( 'skin_name_rd_vote', X2B_DOMAIN ), // 추천수
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_cmt'               => array(
        'id'      => 'rd_cmt',
        'name'    => __( 'skin_name_rd_cmt', X2B_DOMAIN ), // 댓글수
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_link'               => array(
        'id'      => 'rd_link',
        'name'    => __( 'skin_name_rd_link', X2B_DOMAIN ), // 게시물 주소
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            '2'      => __( 'skin_opt_bottom_left', X2B_DOMAIN ), // 좌측 하단
            '3'       => __( 'skin_opt_bottom_right', X2B_DOMAIN ), // 우측 하단
            ' '       => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
        ),
    ),
    'rd_profile'               => array(
        'id'      => 'rd_profile',
        'name'    => __( 'skin_name_rd_profile', X2B_DOMAIN ), // 프로필 이미지 표시
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            'Y'      => __( 'skin_opt_display_yes', X2B_DOMAIN ), // 표시
            ' '       => __( 'skin_opt_display_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시 안함(기본)
        ),
    ),
    // 'rd_hd_widget'     => array(
    //     'id'      => 'rd_hd_widget',
    //     'name'    => __( '제목 영역 위젯 등 출력', X2B_DOMAIN ),
    //     'desc'    => __( "제목 하단 영역에 나타나는 커스텀 공간입니다. 'html 입력' 또는 '회원정보위젯', '전광판위젯', '광고' 등을 넣어 사용할 수 있습니다.", X2B_DOMAIN ),
    //     'type'    => 'textarea',
    //     'options' => false,
    // ),
    'sketchbook5_setup_header18'					=> array(
        'id'      => 'sketchbook5_setup_header18',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header18', X2B_DOMAIN ), // 본문 '블로그 스타일' 및 '뷰어로 보기' 제목 설정
        'type'    => 'header',
        'options' => false,
    ),
    'rd_blog_style'               => array(
        'id'      => 'rd_blog_style',
        'name'    => __( 'skin_name_rd_blog_style', X2B_DOMAIN ), // 블로그 스타일 프리셋
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_style_thick_list', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 굵은 라인 스타일(기본)
            '2'       => __( 'skin_opt_style_simple_line', X2B_DOMAIN ), // 심플 라인 스타일
            '3'       => __( 'skin_opt_style_gray_line', X2B_DOMAIN ), // 전체 회색 테두리 스타일
            '4'       => __( 'skin_opt_style_borderless', X2B_DOMAIN ), // 테두리 없는 스타일
        ),
    ),
    'rd_h1_ani'               => array(
        'id'      => 'rd_h1_ani',
        'name'    => __( 'skin_name_rd_h1_ani', X2B_DOMAIN ), // 제목 애니메이션
        'desc'    => __( 'skin_desc_rd_h1_ani', X2B_DOMAIN ), // *css3 animation을 지원하는 브라우저에서만 동작합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_fade_in', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 페이드인(기본)
            '2'       => __( 'skin_opt_vertical_turn_around', X2B_DOMAIN ), // 수직 한바퀴
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 없음
        ),
    ),
    'rd_tl'               => array(
        'id'      => 'rd_tl',
        'name'    => __( 'skin_name_rd_tl', X2B_DOMAIN ), // 제목 표시 및 정렬
        'desc'    => __( 'skin_desc_rd_tl', X2B_DOMAIN ), // *css3 animation을 지원하는 브라우저에서만 동작합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_align_left', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 좌측 정렬(기본)
            'center'       => __( 'skin_opt_align_middle', X2B_DOMAIN ), // 가운데 정렬
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_top'               => array(
        'id'      => 'rd_top',
        'name'    => __( 'skin_name_rd_top', X2B_DOMAIN ), // 상단 영역 정렬
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_depend_on_subject_area', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 제목 영역에 따라(기본)
            'left'       => __( 'skin_opt_align_left', X2B_DOMAIN ), // 좌측 정렬
            'center'       => __( 'skin_opt_align_middle', X2B_DOMAIN ), // 가운데 정렬
            'right'       => __( 'skin_opt_align_right', X2B_DOMAIN ), // 우측 정렬
        ),
    ),
    'rd_top_font'               => array(
        'id'      => 'rd_top_font',
        'name'    => __( 'skin_name_rd_top_font', X2B_DOMAIN ), // 상단 영역 글꼴
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_font_gothic', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 고딕체(기본)
            'nmeb'       => __( 'skin_opt_font_nmeb', X2B_DOMAIN ), // 명조체
            'np'       => __( 'skin_opt_font_np', X2B_DOMAIN ), // 필기체
        ),
    ),
    // 'rd_btm'               => array(
    //     'id'      => 'rd_btm',
    //     'name'    => __( "하단 영역 정렬", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '제목 영역에 따라(기본)', X2B_DOMAIN ),
    //         'left'       => __( "좌측 정렬", X2B_DOMAIN ),
    //         'center'       => __( "가운데 정렬", X2B_DOMAIN ),
    //         'right'       => __( "우측 정렬", X2B_DOMAIN ),
    //     ),
    // ),
    'rd_btm_font'               => array(
        'id'      => 'rd_btm_font',
        'name'    => __( 'skin_name_rd_btm_font', X2B_DOMAIN ), // 하단 영역 글꼴
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_font_gothic', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 고딕체(기본)
            'nmeb'       => __( 'skin_opt_font_nmeb', X2B_DOMAIN ), // 명조체
            'np'       => __( 'skin_opt_font_np', X2B_DOMAIN ), // 필기체
        ),
    ),
    'rd_blog_cate'               => array(
        'id'      => 'rd_blog_cate',
        'name'    => __( 'skin_name_rd_blog_cate', X2B_DOMAIN ), // 카테고리
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_upper', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 상단(기본)
            '2'       => __( 'skin_opt_bottom', X2B_DOMAIN ), // 하단
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_blog_nick'               => array(
        'id'      => 'rd_blog_nick',
        'name'    => __( 'skin_name_rd_blog_nick', X2B_DOMAIN ), // 글쓴이
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            '2'      => __( 'skin_opt_upper', X2B_DOMAIN ), // 상단
            ' '       => __( 'skin_opt_bottom', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 하단(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_blog_date'               => array(
        'id'      => 'rd_blog_date',
        'name'    => __( 'skin_name_rd_blog_date', X2B_DOMAIN ), // 날짜
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            '2'      => __( 'skin_opt_upper', X2B_DOMAIN ), // 상단
            ' '       => __( 'skin_opt_bottom', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 하단(기본)
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'rd_blog_itm'                       => array(
        'id'      => 'rd_blog_itm',
        'name'    => __( 'skin_name_rd_blog_itm', X2B_DOMAIN ), // 조회/추천/댓글 표시
        'type'    => 'multicheck',
        'options' => array(
            'view'            => __( 'skin_opt_view_count', X2B_DOMAIN ), // 조회
            'like'            => __( 'skin_opt_like_count', X2B_DOMAIN ), // 추천
            'cmt'            => __( 'skin_opt_comment_count', X2B_DOMAIN ), // 댓글
        ),
    ),
    'rd_preview'               => array(
        'id'      => 'rd_preview',
        'name'    => __( 'skin_name_rd_preview', X2B_DOMAIN ), // 본문 프리뷰
        'desc'    => __( 'skin_desc_rd_preview', X2B_DOMAIN ), // '블로그 스타일'에서 제목 밑에 본문의 프리뷰 출력합니다. 확장변수에서 \"확장변수 이름 : 'rd_preview', 형식 : '여러 줄 입력칸(textarea)'\" 을 지정해주시면 사용할 수 있습니다. 자세한 설명은 XE자료실 또는 제작자의 홈페이지를 참조해주세요.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용(기본)
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'sketchbook5_setup_header19'					=> array(
        'id'      => 'sketchbook5_setup_header19',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header19', X2B_DOMAIN ), // '뷰어로 보기' 설정
        'type'    => 'header',
        'options' => false,
    ),
    'viewer_with'               => array(
        'id'      => 'viewer_with',
        'name'    => __( 'skin_name_viewer_with', X2B_DOMAIN ), // 게시판 상단 버튼
        'desc'    => __( 'skin_desc_viewer_with', X2B_DOMAIN ), // 게시판의 상단에 게시물을 클릭 시 바로 뷰어로 보게 하는 '게시물을 뷰어로 보기' 버튼의 표시 여부를 결정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_display_yes', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 표시(기본)
            '2'       => __( 'skin_opt_activate_on_init', X2B_DOMAIN ), // 시작시 활성화
            'N'       => __( 'skin_opt_display_no', X2B_DOMAIN ), // 표시 안함
        ),
    ),
    'viewer'               => array(
        'id'      => 'viewer',
        'name'    => __( 'skin_name_viewer', X2B_DOMAIN ), // '뷰어로 보기' 버튼
        'desc'    => __( 'skin_desc_viewer', X2B_DOMAIN ), // 본문에서 '뷰어로 보기' 버튼을 설정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_body_upper_right', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 본문 우측 상단(기본)
            '2'       => __( 'skin_opt_body_icon', X2B_DOMAIN ), // 본문 내비 영역에 아이콘 버튼
            'N'       => __( 'skin_opt_use_no', X2B_DOMAIN ), // 사용 안함
        ),
    ),
    'lst_viewer'               => array(
        'id'      => 'lst_viewer',
        'name'    => __( 'skin_name_lst_viewer', X2B_DOMAIN ), // 목록 게시물에 'viewer' 버튼
        'desc'    => __( 'skin_desc_lst_viewer', X2B_DOMAIN ), // 목록의 게시물 링크에 마우스를 올릴 때 'viewer' 버튼을 나타낼 것인지를 결정합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용 안함(기본)
            'Y'       => __( 'skin_opt_use_yes', X2B_DOMAIN ), // 사용
        ),
    ),
    // 'viewer_style'               => array(
    //     'id'      => 'viewer_style',
    //     'name'    => __( "뷰어 스타일", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '검은색 배경(기본)', X2B_DOMAIN ),
    //         '2'       => __( "옅은 회색 배경", X2B_DOMAIN ),
    //     ),
    // ),
    // 'viewer_width'     => array(
    //     'id'      => 'viewer_width',
    //     'name'    => __( '뷰어 본문 넓이', X2B_DOMAIN ),
    //     'desc'    => __( "본문의 최대 넓이를 설정합니다. '숫자'만 입력하세요.(단위는 px입니다) 입력하지 않으면 '720' 입니다.", X2B_DOMAIN ),
    //     'type'    => 'text',
    //     'options' => false,
    // ),
    // 'viewer_lst'               => array(
    //     'id'      => 'viewer_lst',
    //     'name'    => __( "우측 목록 표시", X2B_DOMAIN ),
    //     'type'    => 'radio',
    //     'default' => ' ',
    //     'options' => array(
    //         ' '      => __( '표시(기본)', X2B_DOMAIN ),
    //         'N'       => __( "표시 안함", X2B_DOMAIN ),
    //     ),
    // ),
    'viewer_itm'                       => array(
        'id'      => 'viewer_itm',
        'name'    => __( 'skin_name_viewer_itm', X2B_DOMAIN ), // 각 부분 표시 설정
        'type'    => 'multicheck',
        'options' => array(
            'vote'            => __( 'skin_opt_recommendation', X2B_DOMAIN ), // 추천
            'sns'            => __( 'skin_opt_sns', X2B_DOMAIN ), // SNS
            'fnt'            => __( 'skin_opt_file_trackback', X2B_DOMAIN ), // 파일, 트랙백
            'cmt'            => __( 'skin_opt_comment', X2B_DOMAIN ), // 댓글
        ),
    ),
    'sketchbook5_setup_header20'					=> array(
        'id'      => 'sketchbook5_setup_header20',
        'desc'    => __( 'skin_desc_sketchbook5_setup_header20', X2B_DOMAIN ), // *모바일 설정
        'type'    => 'header',
        'options' => false,
    ),
    'img_link'               => array(
        'id'      => 'img_link',
        'name'    => __( 'skin_name_img_link', X2B_DOMAIN ), // 이미지 원본 링크
        'desc'    => __( 'skin_desc_img_link', X2B_DOMAIN ), // 모바일에서 이미지를 클릭하면 원본 링크로 이동합니다. *다른 리사이즈 애드온과 충돌이 될 수 있으니 해당 애드온 설정에서 모바일 설정을 확인해주세요.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_use_yes', X2B_DOMAIN ), // 사용
            '2'       => __( 'skin_opt_use_no', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // 사용 안함(기본)
        ),
    ),
    'm_editor'               => array(
        'id'      => 'm_editor',
        'name'    => __( 'skin_name_m_editor', X2B_DOMAIN ), // 모바일 에디터
        'desc'    => __( 'skin_desc_m_editor', X2B_DOMAIN ), // 모바일 에디터를 선택합니다.
        'type'    => 'radio',
        'default' => ' ',
        'options' => array(
            ' '      => __( 'skin_opt_editor_html5_wysiwyg', X2B_DOMAIN ).__( 'lbl_default', X2B_DOMAIN ), // HTML5 WYSIWYG(기본)
            '2'       => __( 'skin_opt_editor_textarea', X2B_DOMAIN ), // Textarea
            '3'       => __( 'skin_opt_editor_web_editor', X2B_DOMAIN ), // 웹 에디터
        ),
    ),
);
?>