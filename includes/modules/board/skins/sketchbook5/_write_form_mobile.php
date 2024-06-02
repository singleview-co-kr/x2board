<!--// Mobile Upload
	Source : http://www.phpletter.com/
	For XE : http://www.userpin.org/
	Modify & UI : http://sketchbooks.co.kr/
	문제
	1. 파일이름에 작은따옴표가 들어가는 경우 에러
-->
<?php
// include_once('./modules/file/file.class.php');
// include_once('./modules/file/file.controller.php');

/*
$o_file_controller = \X2board\Includes\getController('file');
$editor_sequence = '1';
$upload_target_id = $post_id;
$o_file_controller->set_upload_info($editor_sequence, $upload_target_id);
unset($o_file_controller);
$_SESSION['x2b_upload_info'][$editor_sequence]->enabled = true;
$_SESSION['x2b_upload_info'][$editor_sequence]->upload_target_id = $upload_target_id;
*/

// Context::loadLang('./modules/editor/lang');
// File config
// $o_file_model = \X2board\Includes\getModel('file');
// $file_config = $o_file_model->getUploadConfig();
// unset($o_file_model);

// Editor Config
// $oDocument->getEditor()->option;
?>

<!-- <load target="css/m_editor.css" /> -->
<link rel='stylesheet' id='<?php echo X2B_DOMAIN ?>-sketchbook5-m_editor-css' href='<?php echo $skin_url ?>/css/m_editor.css?ver=<?php echo X2B_VERSION ?>' type='text/css' media='all' />
<!-- <load target="js/editor_m.js" /> -->
<script type="text/javascript" src="<?php echo $skin_url ?>/js/editor_m.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-editor_m-js"></script>
<!-- <load target="js/ajaxfileupload.js" type="body" /> -->
<script type="text/javascript" src="<?php echo $skin_url ?>/js/ajaxfileupload.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-ajaxfileupload-js"></script>

<script>//<![CDATA[
var lang_confirm_delete ='<?php echo __('confirm_delete', 'x2board')?>';
var allowedFileTypes = '<?php if($grant->manager): ?>*.*<?php else: ?><?php echo $current_module_info->file_allowed_filetypes ?><?php endif ?>';
//]]></script>

<?php if($post_id): ?><!-- <block cond="$document_srl"> -->
	<div class="context_data">
		<h3>※ <?php echo $lang->m_editor_notice ?></h3>
	</div>
	<div class="context_message"><br /></div>
<?php endif ?><!-- </block> -->

<div class="bd_wrt bd_wrt_main clear">

<form action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" id="ff" class="m_editor_v<?php echo $mi->m_editor ?>">
	<!-- <input type="hidden" name="mid" value="{$mid}" />
	<input type="hidden" name="document_srl" value="{$document_srl}" /> 
	<input type="hidden" name="content" value="" /> -->
	<!--@if(!$post->getContentText() && $mi->content_default)-->
	<!-- {htmlspecialchars($mi->content_default)}@else{$post->getContentText()} -->
	<!--@end-->
	<?php x2b_write_post_input_fields(); ?>
	<input type="hidden" name="use_html" value="Y" />
	
	<?php if(false): ?>
	<!--// 상단 : 카테고리, 제목 -->
		<table class="bd_wrt_hd bd_tb">
			<tr>
				<td>
					<?php if($mi->use_category=='Y' && $category_list): ?><!-- cond="$mi->use_category=='Y' && $category_list"  -->
						<select name="category_id" class="category">
							<option value=""><?php echo __('category', 'x2board')?></option>
							<?php foreach($category_list as $val): ?><!-- loop="$category_list => $val"  -->
								<option <?php if(!$val->grant): ?> disabled="disabled" <?php endif ?> value="<?php echo $val->category_id ?>" <?php if($val->grant && $val->selected||$val->category_id==$post->get('category_id')): ?> selected="selected" <?php endif ?> >
									<?php echo str_repeat("&nbsp;&nbsp;",$val->depth)?> <?php echo $val->title?> (<?php echo $val->post_count?>)
								</option>
							<?php endforeach ?>
						</select>
					<?php endif ?>
				</td>
				<td width="100%">
					<span class="itx_wrp">
						<label for="postTitle"><?php echo __('title', 'x2board')?></label>
						<?php if($post->get_title_text()){
							$s_title = htmlspecialchars($post->get_title_text());
						}
						else {
							$s_title = null;
						}?> 
						<input type="text" name="title" class="itx" id="postTitle" title="<?php echo __('title', 'x2board')?>" value="<?php echo $s_title ?>" />
					</span>
				</td>
			</tr>
		</table>

		<?php if(count($extra_keys)): ?><!-- cond="count($extra_keys)"  -->
			<table class="et_vars exForm bd_tb">
				<caption><strong><em>*</em></strong> <small>: <?php echo __('is_required', 'x2board')?></small></caption>
				<?php foreach($extra_keys as $key => $val): ?><!-- loop="$extra_keys=>$key,$val" -->
					<tr>
						<th scope="row"><?php if($val->is_required=='Y'): ?><em>*</em><?php endif ?> <?php echo $val->name ?></th>
						<td><?php echo $val->getFormHTML() ?></td>
					</tr>
				<?php endforeach ?>
			</table>
		<?php endif ?>

		<!-- Editor -->
		<!--// wysiwyg -->
		<?php if($mi->m_editor != ' '): ?><!-- cond="!$mi->m_editor"  -->
			<div class="m_editor">
			<!-- <load target="js/editor_wysiwyg.js" /> -->
			<script type="text/javascript" src="<?php echo $skin_url ?>/js/editor_wysiwyg.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-editor_wysiwyg-js"></script>
			<!-- <load target="js/bootstrap-wysiwyg.js" /> -->
			<script type="text/javascript" src="<?php echo $skin_url ?>/js/bootstrap-wysiwyg.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-bootstrap-wysiwyg-js"></script>
			<!-- <load target="js/jquery.hotkeys.js" /> -->
			<script type="text/javascript" src="<?php echo $skin_url ?>/js/jquery.hotkeys.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-jquery-hotkeys-js"></script>
				<div id="alerts"></div>
				<div class="btn-toolbar clear" data-role="editor-toolbar" data-target="#editor">
					<div class="btn-group">
						<a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
						<a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
						<a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
					</div>
					<div class="btn-group">
						<a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
						<a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
						<!--//
						<a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
						<a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
						-->
					</div>
					<div class="btn-group hide_w320">
						<a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
						<!--//<a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>-->
					</div>
					<div class="btn-group fr">
						<a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
						<a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
					</div>
					<div class="blind"><input type="text" data-edit="inserthtml" id="inserthtml" /></div>
				</div>
				<div id="editor"><p>&nbsp;</p></div>
			</div>
		<?php endif ?>

		<!--// textarea -->
		<?php if($mi->m_editor==2): ?><!-- cond="$mi->m_editor==2"  -->
			<div class="m_editor">
				<!-- <load target="js/editor_textarea.js" /> -->
				<script type="text/javascript" src="<?php echo $skin_url ?>/js/editor_textarea.js?ver=<?php echo X2B_VERSION ?>" id="<?php echo X2B_DOMAIN ?>-sketchbook5-editor_textarea-js"></script>
				<textarea id="nText" col="50" rows="8"></textarea>
			</div>
		<?php endif ?>

	
		<!--// 이미지 업로드 -->
		<?php if($allow_fileupload): ?><!-- cond="$allow_fileupload" -->
			<div id="mUpload">
				<div class="bg_f_f9 clear">
					<strong class="fl"><?php echo __('edit_upload_file', 'x2board')?></strong> <button type="button" class="bd_btn fr" onclick="jQuery('#Filedata').click()"><?php echo __('upload_file', 'x2board')?></button>
				</div>
				<ul id="files" class="clear">
					<block cond="$post->hasUploadedFiles()" loop="$post->getUploadedFiles()=>$key,$file">
	<?php
		$ext = substr($file->source_filename, -4);
		$ext = strtolower($ext);
		$type = 'etc';
		if(in_array($ext,array('.jpg','jpeg','.gif','.png'))) $type = 'img';
		if(in_array($ext,array('.mp3','.wav','.ogg','.aac'))) $type = 'music';
		if(in_array($ext,array('webm','.mp4','.ogv','.avi','.mov','.mkv'))) $type = 'media';
	?>
					<li cond="$type=='img'" id="file_{$file->file_srl}" class="success"><button type="button" data-file="{$file->uploaded_filename}" data-type="img" title="{$file->source_filename}" style="background-image:url({$file->uploaded_filename})" onclick="jQuery(this).parent().toggleClass('select')"><b>✔</b></button><a class="delete_file" href="#" onclick="delete_file({$file->file_srl});return false;"><b>X</b></a><a class="insert_file" href="#" onclick="insert_file({$file->file_srl});return false;"><i class="fa fa-arrow-up"></i></a></li>
					<li cond="$type!='img'" id="file_{$file->file_srl}" class="success type2 {$type}"><small>{$file->source_filename}</small><button type="button" data-file="{$file->uploaded_filename}" data-type="{$type}" data-dnld="{$file->download_url}" onclick="jQuery(this).parent().toggleClass('select')"><b>✔</b></button><a class="delete_file" href="#" onclick="delete_file({$file->file_srl});return false;"><b>X</b></a><a class="insert_file" href="#" onclick="insert_file({$file->file_srl});return false;"><i class="fa fa-arrow-up"></i></a></li>
					</block>
					
					<li id="loading"></li>
					<li class="info clear<!--@if($post->hasUploadedFiles())--> is_img<!--@end-->">
						<span><?php echo __('no_files', 'x2board')?></span>
						<div cond="!$mi->m_editor">
							<button type="button" class="all bd_btn" id="mEditorSelect"><i class="fa fa-check"></i> <span><?php echo __('cmd_select_all', 'x2board')?></span><span><?php echo $lang->cmd_deselect_all ?></span></button>
							<button type="button" class="insert bd_btn" id="mEditorInsert"><i class="fa fa-arrow-up"></i> <?php echo __('edit_link_file', 'x2board')?></button>
							<button type="button" class="delete bd_btn" id="mEditorDelete"><i class="fa fa-trash-o"></i> <?php echo __('edit_delete_selected', 'x2board')?></button>
						</div>
						<div cond="$mi->m_editor==2">
							<p><i class="tx_ico_chk">✔</i><?php echo __('select_files_to_insert', 'x2board')?></p>
							<input type="radio" name="m_img_upoad" id="m_img_upoad_1" checked="checked" /><label for="m_img_upoad_1"><?php echo $lang->m_img_upoad_1 ?></label>
							<input type="radio" name="m_img_upoad" id="m_img_upoad_2" /><label for="m_img_upoad_2"><?php echo $lang->m_img_upoad_2 ?></label>
						</div>
					</li>
				</ul>
			</div>
		<?php endif ?>
		<!--// 태그 -->
		<div class="tag itx_wrp">
			<span class="itx_wrp">
				<label for="tags"><?php echo __('tag', 'x2board')?> : <?php echo __('about_tag', 'x2board')?></label>
				<input type="text" name="tags" id="tags" value="<?php echo htmlspecialchars($post->get('tags')) ?>" class="itx" />
			</span>
		</div>

		<!--// 비로그인 입력 -->
		<div class="edit_opt">
			<?php if(!$is_logged): ?><!-- <block cond="!$is_logged"> -->
				<span class="itx_wrp">
					<label for="nick_name"><?php echo __('writer', 'x2board')?></label>
					<input type="text" name="nick_name" id="nick_name" value="<?php echo $post->get_nick_name() ?>" class="itx n_p" />
				</span>
				<span class="itx_wrp">
					<label for="password"><?php echo __('password', 'x2board')?></label>
					<input type="password" name="password" id="password" class="itx n_p" />
				</span>
				<span class="itx_wrp">
					<label for="email_address"><?php echo __('email_address', 'x2board')?></label>
					<input type="text" name="email_address" id="email_address" value="<?php echo htmlspecialchars($post->get('email_address')) ?>" class="itx m_h" />
				</span>
				<!-- <span class="itx_wrp">
					<label for="homepage">{$lang->homepage}</label>
					<input type="text" name="homepage" id="homepage" value="{htmlspecialchars($oDocument->get('homepage'))}" class="itx m_h" />
				</span> -->
			<?php endif ?><!-- </block> -->
		</div>

		<!--// 글쓰기 옵션 체크 -->
		<div class="opt_chk clear">
			<?php if($grant->manager): ?><!-- cond="$grant->manager"  -->
				<div class="section">
					<input type="checkbox" name="is_notice" value="Y" <?php if($post->is_notice()): ?> checked="checked" <?php endif ?> id="is_notice" />
					<label for="is_notice"><?php echo __('notice', 'x2board')?></label>
				</div>
			<?php endif ?>
			<div class="section">
				<input type="checkbox" name="comment_status" value="ALLOW" <?php if($post->allow_comment()): ?> checked="checked" <?php endif ?> id="comment_status" />
				<label for="comment_status"<?php echo __('allow_comment', 'x2board')?>></label>
				<!-- <input type="checkbox" name="allow_trackback" value="Y" checked="checked"|cond="$oDocument->allowTrackback()" id="allow_trackback" />
				<label for="allow_trackback">{$lang->allow_trackback}</label> -->
			</div>
			<!-- <div cond="$is_logged" class="section">
				<input type="checkbox" name="notify_message" value="Y" checked="checked"|cond="$oDocument->useNotify() || (!$oDocument->useNotify() && @in_array('notify',$mi->wrt_opt))" id="notify_message" />
				<label for="notify_message">{$lang->notify}</label>
			</div> -->
			<?php if(is_array($status_list)): ?><!-- cond="is_array($status_list)"  -->
				<div class="section">
					<?php foreach($category_list as $val): ?><!--@foreach($status_list AS $key=>$value)-->
						<?php if(!in_array('secret',$mi->wrt_opt)): ?><!-- cond="@!in_array('secret',$mi->wrt_opt)"  -->
							<input type="radio" name="status" value="<?php echo $key ?>" id="<?php echo $key ?>" <?php if($post->get('status')==$key || ($key=='PUBLIC' && !$post_id)):?> checked="checked" <?php endif ?> />
						<?php endif ?>
						<?php if(in_array('secret',$mi->wrt_opt)): ?><!-- cond="@in_array('secret',$mi->wrt_opt)"  -->
							<input type="radio" name="status" value="<?php echo $key ?>" id="<?php echo $key ?>" <?php if($post->get('status')==$key || ($key=='SECRET' && !$post_id)): ?> checked="checked" <?php endif ?> />
						<?php endif ?>
						<label for="<?php echo $key ?>"><?php echo $value ?></label>
					<?php endforeach ?><!--@end-->
				</div>
			<?php endif ?>
		</div>
	<?php endif ?>
</form>

<?php if(false): ?>
	<form id="FiledataWrp" name="form" action="./" method="POST" enctype="multipart/form-data" class="blind">
		<input id="Filedata" type="file" name="Filedata" />
	</form>
	<!--// SocialXE -->
	<!-- <div cond="$mi->cmt_wrt=='sns'" class="sns_wrt">
		<p>※ <?php //echo __('allow_comment', 'x2board')?></p>
		<img class="zbxe_widget_output" widget="socialxe_info" colorset="{$mi->colorset}" skin="default"  />
	</div> -->
<?php endif ?>
	<!--// Buttons -->
	<div class="regist">
		<!-- <button cond="$is_logged && !$oDocument->isExists() || $oDocument->get('status')=='TEMP'" type="button" onclick="doDocumentSave(this);" class="bd_btn temp">{$lang->cmd_temp_save}</button> -->
        <input type="submit" id="frmSubmit" value="<?php echo __('cmd_submit', 'x2board')?>" class="bd_btn blue" onclick="frmSubmit();return false;" />
		<button type="button" onclick="history.back();" class="bd_btn cancle"><?php echo __('cmd_back', 'x2board')?></button>
	</div>
</div>