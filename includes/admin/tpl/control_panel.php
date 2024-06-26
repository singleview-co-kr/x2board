<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;} ?>
<div class="wrap">
	<?php require 'header.php'; ?>
	
	<!-- <div id="welcome-panel" class="welcome-panel"> -->
		<?php // include 'welcome.php' ?>
	<!-- </div> -->
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __( 'cmd_setup_plugin', X2B_DOMAIN ); ?></a>
	</h2>
	<ul id="x2board-dashboard-options">
		<li id="x2board_iframe_whitelist">
			<form method="post" onsubmit="return x2board_system_option_update(this)">
				<input type="hidden" name="action" value="x2board_system_option_update">
				
				<h4>아이프레임 화이트리스트, 아래 등록된 iframe 주소를 허가합니다.</h4>
				<p>
					게시글 작성시 등록되지 않은 iframe 주소는 보안을 위해 차단됩니다.<br>
					형식에 맞춰서 한줄씩 도메인 주소를 입력해주세요.
				</p>
				<p>
					<textarea rows="10" name="option[x2board_iframe_whitelist]"><?php echo $this->_get_iframe_whitelist(); ?></textarea>
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
					<textarea name="option[x2board_name_filter]" style="width:100%"><?php echo $this->_get_forbidden_nickname(); ?></textarea>
					<button type="submit" class="button">금지단어 업데이트</button>
				</p>
				<p>
					<input type="text" name="option[x2board_name_filter_message]" value="<?php echo get_option( 'x2board_name_filter_message', '' ); ?>" style="width:100%" placeholder="<?php echo __( 'msg_not_available', X2B_DOMAIN ); ?>">
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
					<textarea name="option[x2board_content_filter]" style="width:100%"><?php echo $this->_get_forbidden_word_in_contents(); ?></textarea>
					<button type="submit" class="button">금지단어 업데이트</button>
				</p>
				<p>
					<input type="text" name="option[x2board_content_filter_message]" value="<?php echo get_option( 'x2board_content_filter_message', '' ); ?>" style="width:100%" placeholder="<?php echo __( 'msg_not_available', X2B_DOMAIN ); ?>">
					<button type="submit" class="button">금지단어 메시지 업데이트</button>
				</p>
			</form>
		</li>
	</ul>
</div>
