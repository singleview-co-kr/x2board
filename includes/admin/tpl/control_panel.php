<?php if(!defined('ABSPATH')) exit;?>
<div class="wrap">
	<?php include 'header.php' ?>
	
	<!-- <div id="welcome-panel" class="welcome-panel"> -->
		<?php //include 'welcome.php'?>
	<!-- </div> -->
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __('cmd_setup_plugin', X2B_DOMAIN)?></a>
	</h2>
	<ul id="x2board-dashboard-options">
		<li id="x2board_xssfilter">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_xssfilter]" value="<?php echo get_option('x2board_xssfilter')?'':'1'?>">
				
				<h4><?php echo get_option('x2board_xssfilter')?'해킹 차단 옵션이 비활성화 되어 있습니다.':'해킹으로 부터 보호되고 있습니다.'?></h4>
				<p>
				서버에 ModSecurity등의 방화벽이 설치되어 있으면 이 옵션을 비활성화 가능합니다.<br>
				이 옵션을 100% 신뢰하지 마세요, 서버와 네트워크에 방화벽 설치를 권장합니다.<br>
				이 옵션을 비활성화 하면 시스템 속도가 빨라집니다.
				</p>
				<p><button type="submit" class="button">XSS공격 차단 <?php echo get_option('x2board_xssfilter')?'활성화':'비활성화'?></button></p>
			</form>
		</li>
		<li id="x2board_fontawesome">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_fontawesome]" value="<?php echo get_option('x2board_fontawesome')?'':'1'?>">
				
				<h4><?php echo get_option('x2board_fontawesome')?'Font Awesome 사용 중지되었습니다.':'Font Awesome 사용 가능합니다.'?></h4>
				<p>
					Font Awesome은 오픈소스 아이콘 폰트 입니다.<br>
					X2Board의 게시판 스킨에 사용되고 있습니다.<br>
					테마의 레이아웃 또는 버튼이 깨지거나 다른 플러그인과 충돌이 발생되면 이 옵션을 비활성화해보세요.
				</p>
				<p><button type="submit" class="button">Font Awesome <?php echo get_option('x2board_fontawesome')?'활성화':'비활성화'?></button></p>
			</form>
		</li>
		<li id="x2board_attached_copy_download">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_attached_copy_download]" value="<?php echo get_option('x2board_attached_copy_download')?'':'1'?>">
				
				<h4><?php echo get_option('x2board_attached_copy_download')?'첨부파일 다운로드 깨짐 방지가 활성화 되어 있습니다.':'기본적인 방법으로 첨부파일이 다운로드 되고 있습니다.'?></h4>
				<p>
					다운로드 받은 첨부파일이 깨져 사용자가 읽을 수 없다면 이 옵션을 활성화 하세요.<br>
					이 옵션을 활성화 하면 새로운 방법으로 첨부파일을 다운로드 받습니다.<br>
					시스템 성능이 저하될 수 있으니 서버에 첨부파일에 대한 MIME Type 설정을 추가할 것을 권장합니다.
				</p>
				<p><button type="submit" class="button">첨부파일 다운로드 깨짐 방지 <?php echo get_option('x2board_attached_copy_download')?'비활성화':'활성화'?></button></p>
			</form>
		</li>
		<li id="x2board_attached_open_browser">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_attached_open_browser]" value="<?php echo get_option('x2board_attached_open_browser')?'':'1'?>">
				
				<h4>다운로드 방식 : <?php echo get_option('x2board_attached_open_browser')?'가능한 경우 브라우저에서 읽기':'PC에 저장하기'?></h4>
				<p>
					첨부파일을 다운로드 방식을 변경할 수 있습니다.<br>
					기본적으로는 파일을 PC에 저장하도록 다운로드합니다.<br>
					또는 가능한 경우 브라우저에서 즉시 내용을 읽을 수 있습니다.
				</p>
				<p><button type="submit" class="button">다운로드 방식 변경</button></p>
			</form>
		</li>
		
		<li id="x2board_new_document_notify_time">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>새글 알림 아이콘을 리스트에서 보여줍니다.</h4>
				<p>
					리스트에서 정해진 시간 이내로 등록된 글에 NEW 표시가 나타나도록 설정합니다.<br>
					일부 스킨에서는 적용되지 않습니다.
				</p>
				<p>
					<select name="option[x2board_new_document_notify_time]">
						<option value="1">비활성화</option>
						<option value="3600"<?php if($this->_new_post_notify_time() == '3600'):?> selected<?php endif?>>1시간</option>
						<option value="10800"<?php if($this->_new_post_notify_time() == '10800'):?> selected<?php endif?>>3시간</option>
						<option value="21600"<?php if($this->_new_post_notify_time() == '21600'):?> selected<?php endif?>>6시간</option>
						<option value="43200"<?php if($this->_new_post_notify_time() == '43200'):?> selected<?php endif?>>12시간</option>
						<option value="86400"<?php if($this->_new_post_notify_time() == '86400'):?> selected<?php endif?>>하루</option>
						<option value="172800"<?php if($this->_new_post_notify_time() == '172800'):?> selected<?php endif?>>2일</option>
						<option value="259200"<?php if($this->_new_post_notify_time() == '259200'):?> selected<?php endif?>>3일</option>
						<option value="345600"<?php if($this->_new_post_notify_time() == '345600'):?> selected<?php endif?>>4일</option>
						<option value="432000"<?php if($this->_new_post_notify_time() == '432000'):?> selected<?php endif?>>5일</option>
						<option value="518400"<?php if($this->_new_post_notify_time() == '518400'):?> selected<?php endif?>>6일</option>
						<option value="604800"<?php if($this->_new_post_notify_time() == '604800'):?> selected<?php endif?>>1주일</option>
					</select>
					<button type="submit" class="button">변경</button>
				</p>
			</form>
		</li>
		<li id="x2board_captcha_stop">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_captcha_stop]" value="<?php echo get_option('x2board_captcha_stop')?'':'1'?>">
				
				<h4>모든 게시판에서 <?php echo get_option('x2board_captcha_stop')?'비로그인 사용자 CAPTCHA 기능이 중지되었습니다.':'비로그인 사용자 CAPTCHA 기능을 사용중입니다.'?></h4>		
				<p>
					CAPTCHA(캡챠)란 기계는 인식 할 수없는 임의의 문자를 생성하여 입력 받아, 스팸을 차단하는 기능입니다.<br>
					게시판과 댓글 작성시 비로그인 사용자는 CAPTCHA 보안코드를 입력하도록 합니다.<br>
					비활성화 하게되면 스팸이 등록될 확률이 높아집니다.
				</p>
				<p><button type="submit" class="button">모든 게시판에서 비로그인 사용자 CAPTCHA 기능 <?php echo get_option('x2board_captcha_stop')?'사용하기':'중지하기'?></button></p>
			</form>
		</li>
		<li id="x2board_recaptcha">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>구글 reCAPTCHA</h4>
				<p>
					구글 reCAPTCHA는 게시판에서 스팸을 막기 위한 효과적인 솔루션입니다.<br>
					구글 reCAPTCHA를 활성화하면 x2board에 내장된 CAPTCHA 보안코드 대신 구글 reCAPTCHA를 사용하게 됩니다.<br>
					<a href="https://www.google.com/recaptcha/admin" onclick="window.open(this.href);return false;">https://www.google.com/recaptcha/admin</a> 에서 발급받은 Site key와 Secret key를 입력하면 자동으로 활성화됩니다.<br>
					구글 reCAPTCHA 기능이 없는 일부 스킨에서는 동작하지 않습니다.<br>
					<br>
					reCAPTCHA v2 -> Checkbox 타입을 선택해주세요.<br>
					<a href="https://blog.naver.com/PostView.nhn?blogId=chan2rrj&logNo=221282560693" onclick="window.open(this.href);return false;">리캡차(reCAPTCHA) 설정 자세히 보기</a>
				</p>
				<p>
					Site key <input type="text" name="option[x2board_recaptcha_site_key]" value="<?php echo get_option('x2board_recaptcha_site_key')?>" placeholder="Site key"><br>
					Secret key <input type="text" name="option[x2board_recaptcha_secret_key]" value="<?php echo get_option('x2board_recaptcha_secret_key')?>" placeholder="Secret key"><br>
					<button type="submit" class="button">구글 reCAPTCHA 정보 업데이트</button>
				</p>
			</form>
		</li>
		<li id="x2board_custom_css">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>커스텀 CSS</h4>
				<p>
					스킨파일 수정없이 새로운 디자인 속성을 추가할 수 있습니다.<br>
					잘못된 CSS를 입력하게 되면 사이트 레이아웃이 깨질 수 있습니다.<br>
					CSS 수정 관련 질문은 커뮤니티를 이용해 주세요. <a href="https://www.cosmosfarm.com/threads" onclick="window.open(this.href);return false;"><?php echo __('cmd_goto_community', X2B_DOMAIN)?></a>
				</p>
				<p>
					<textarea rows="10" name="option[x2board_custom_css]"><?php echo get_option('x2board_custom_css')?></textarea>
					<button type="submit" class="button">커스텀 CSS 업데이트</button>
				</p>
			</form>
		</li>
		<li id="x2board_iframe_whitelist">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>아이프레임 화이트리스트, 아래 등록된 iframe 주소를 허가합니다.</h4>
				<p>
					게시글 작성시 등록되지 않은 iframe 주소는 보안을 위해 차단됩니다.<br>
					형식에 맞춰서 한줄씩 도메인 주소를 입력해주세요.
				</p>
				<p>
					<textarea rows="10" name="option[x2board_iframe_whitelist]"><?php echo $this->_get_iframe_whitelist()?></textarea>
					<button type="submit" class="button">아이프레임 화이트리스트 업데이트</button>
				</p>
			</form>
		</li>
		<li id="x2board_name_filter">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>작성자 금지단어</h4>
				<p>
					작성자 이름으로 사용할 수 없는 단어를 입력해주세요.<br>
					관리자가 아닌 경우에 포함된 단어가 존재하면 게시판 글 작성을 중단합니다.<br>
					단어를 콤마(,)로 구분해서 추가해주세요.
				</p>
				<p>
					<textarea name="option[x2board_name_filter]" style="width:100%"><?php echo $this->_get_forbidden_nickname()?></textarea>
					<button type="submit" class="button">금지단어 업데이트</button>
				</p>
				<p>
					<input type="text" name="option[x2board_name_filter_message]" value="<?php echo get_option('x2board_name_filter_message', '')?>" style="width:100%" placeholder="<?php echo __('msg_not_available', X2B_DOMAIN)?>">
					<button type="submit" class="button">금지단어 메시지 업데이트</button>
				</p>
			</form>
		</li>
		<li id="x2board_content_filter">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>본문/제목/댓글 금지단어</h4>
				<p>
					게시글 본문과 제목 그리고 댓글에 사용할 수 없는 단어를 입력해주세요.<br>
					관리자가 아닌 경우에 포함된 단어가 존재하면 게시판 글 작성을 중단합니다.<br>
					단어를 콤마(,)로 구분해서 추가해주세요.
				</p>
				<p>
					<textarea name="option[x2board_content_filter]" style="width:100%"><?php echo $this->_get_forbidden_word_in_contents()?></textarea>
					<button type="submit" class="button">금지단어 업데이트</button>
				</p>
				<p>
					<input type="text" name="option[x2board_content_filter_message]" value="<?php echo get_option('x2board_content_filter_message', '')?>" style="width:100%" placeholder="<?php echo __('msg_not_available', X2B_DOMAIN)?>">
					<button type="submit" class="button">금지단어 메시지 업데이트</button>
				</p>
			</form>
		</li>
		<li id="x2board_content_delete_immediately">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				<input type="hidden" name="option[x2board_content_delete_immediately]" value="<?php echo get_option('x2board_content_delete_immediately')?'':'1'?>">
				
				<h4>게시글 바로 삭제 : <?php echo get_option('x2board_content_delete_immediately')?'바로 삭제':'휴지통으로 이동'?></h4>
				<p>
					기본적으로 게시글을 지우면 해당 게시글은 휴지통으로 이동합니다.<br>
					경우에 따라서 이 휴지통 기능이 필요 없을 수 있으며 휴지통 기능이 필요 없다면 이 기능을 활성화해주세요.<br>
					휴지통으로 이동시에는 게시글 포인트 미적용 및 첨부파일이 삭제되지 않으며 영구적으로 삭제시에만 실행됩니다.
				</p>
				<p><button type="submit" class="button">게시글 바로 삭제 <?php echo get_option('x2board_content_delete_immediately')?'비활성화':'활성화'?></button></p>
			</form>
		</li>
		<li id="x2board_image_optimize">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>이미지 최적화</h4>
				<p>
					X2Board에서 업로드되는 이미지를 최적화해 서버의 저장공간을 절약할 수 있습니다.<br>
					x2board 미디어 추가 기능과 게시판 첨부파일로 업로드되는 이미지에 적용됩니다.<br>
					jpg, png 계열의 이미지 파일에 적용되며 gif 파일에는 적용되지 않습니다.<br>
					필드가 빈 값일 경우 동작하지 않고 업로드 원본 그대로 저장합니다.<br>
					사진의 메타데이터가 삭제될 수 있습니다.<br>
					일부 서버 환경에서는 동작하지 않을 수 있습니다.
				</p>
				<p>
					최대 이미지 사이즈 <input type="text" name="option[x2board_image_optimize_width]" value="<?php echo get_option('x2board_image_optimize_width')?>" placeholder="width">x<input type="text" name="option[x2board_image_optimize_height]" value="<?php echo get_option('x2board_image_optimize_height')?>" placeholder="height">px<br>
					이미지 퀄러티 변경 <input type="text" name="option[x2board_image_optimize_quality]" value="<?php echo get_option('x2board_image_optimize_quality')?>" placeholder="1-100">% (1-100 사이의 숫자만 입력하세요)<br>
					<button type="submit" class="button">이미지 최적화 업데이트</button>
				</p>
			</form>
		</li>
		<li>
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>복사 방지 스크립트 실행</h4>
				<p>
					X2Board가 있는 페이지에서 복사 방지 스크립트를 실행합니다.<br>
					드래그 + 우클릭, 복사 방지 단계별로 설정할 수 있습니다.<br>
					복사 방지는 클립보드에 복사된 값을 삭제하는 방식으로 복사가 실행되어도 값이 저장되지 않습니다.<br>
					X2Board 동작하는 페이지 전체에 적용됩나다.<br>
					관리자를 제외한 나머지 모두에게 적용됩니다.<br>
					일부 서버 환경에서는 동작하지 않을 수 있습니다.
				</p>
				<p>
					<select name="option[x2board_prevent_copy]">
						<option value="">비활성화</option>
						<option value="1"<?php if(get_option('x2board_prevent_copy') == '1'):?> selected<?php endif?>>복사 방지</option>
						<option value="2"<?php if(get_option('x2board_prevent_copy') == '2'):?> selected<?php endif?>>드래그, 우클릭 방지</option>
						<option value="2"<?php if(get_option('x2board_prevent_copy') == '3'):?> selected<?php endif?>>드래그, 우클릭, 복사 방지</option>
					</select>
					<button type="submit" class="button">적용</button>
				</p>
			</form>
		</li>
		<li>
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>전체 검색시 작성자 포함</h4>
				<p>
					활성화 상태 일 때 게시글 전체 검색시<br>
					"제목 or 내용 or 작성자"로 검색할 수 있습니다.<br>
					비활성화 상태 일 때 게시글 전체 검색시<br>
					"제목 or 내용"으로 검색할 수 있습니다.
				</p>
				<p>
					<select name="option[x2board_search_include_member_display]">
						<option value="">비활성화</option>
						<option value="1"<?php if(get_option('x2board_search_include_member_display') == '1'):?> selected<?php endif?>>활성화</option>
					</select>
					<button type="submit" class="button">적용</button>
				</p>
			</form>
		</li>
		<li>
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>더 많은 게시글 검색하기</h4>
				<p>
					게시판에서 검색 시 키워드의 공백 기준으로 OR 검색을 시도합니다.<br>
					게시글이 많고 키워드가 복잡해질수록 홈페이지 속도가 느려질 수 있습니다.
				</p>
				<p>
					<select name="option[x2board_search_auto_operator_or]">
						<option value="">비활성화</option>
						<option value="1"<?php if(get_option('x2board_search_auto_operator_or') == '1'):?> selected<?php endif?>>활성화</option>
					</select>
					<button type="submit" class="button">적용</button>
				</p>
			</form>
		</li>
	</ul>
</div>