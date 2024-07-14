<script>
var request_uri = "<?php echo content_url()?>/";

jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

// var current_mid = "secret";
// var waiting_message = "서버에 요청 중입니다. 잠시만 기다려주세요.";
// var ssl_actions = new Array();
// var default_url = "https://singleview.co.kr/";
// var enforce_ssl = true;
</script>

<!-- css -->
<?php wp_enqueue_style("x2board-ckeditor-default-style", X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/skins/ckeditor/css/default.css', [], X2B_VERSION) ?>
<?php wp_enqueue_style("x2board-xeicon-style", X2B_URL . 'common/xeicon/xeicon.min.css', [], X2B_VERSION) ?>
<!-- <load target="css/default.css" /> -->
<!-- <load target="../../../../common/xeicon/xeicon.min.css" /> -->

<!-- JS -->
<!--%load_js_plugin("ckeditor")-->

<?php //wp_enqueue_script('x-js', X2B_URL . 'common/js/x.js', array(), X2B_VERSION, true)?>
<?php wp_enqueue_script('xe-js', X2B_URL . 'common/js/xe.js', array(), X2B_VERSION, true)?>
<?php wp_enqueue_script('ckeditor-js', X2B_URL . 'common/js/plugins/ckeditor/ckeditor/ckeditor.js', array(), X2B_VERSION, true)?>

<?php wp_enqueue_script('ckeditor-editor-common', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/tpl/js/editor_common.js', array(), X2B_VERSION, true)?>
<?php wp_enqueue_script('ckeditor-editor-app', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/tpl/js/editor.app.js', array(), X2B_VERSION, true)?>
<?php wp_enqueue_script('ckeditor-xe-interface', X2B_URL . 'includes/' . X2B_MODULES_NAME . '/editor/skins/ckeditor/js/xe_interface.js', array(), X2B_VERSION, true)?>
<!-- <load target="../../tpl/js/editor_common.js" />
<load target="../../tpl/js/editor.app.js" />
<load target="js/xe_interface.js" /> -->

<!-- {@ $css_content = null } -->
<?php $css_content = null;
if($content_font || $content_font_size) {
	if($content_style === 'ckeditor_light') {
		$css_content = '.x2b_content.editable p { margin: 0;'. chr(125);
	}

	$css_content .= ' .x2b_content.editable { ';
	if(isset( $content_font )) {
		$css_content .= 'font-family:' . $content_font . ';';
	}
	if(isset( $content_font_size )) {
		$css_content .= 'font-size:' . $content_font_size . ';';
	}
	$css_content .= chr(125);
}
?>
<div class="get_editor">
<div id="ckeditor_instance_<?php echo $editor_sequence?>" data-editor-sequence="<?php echo $editor_sequence ?>" data-editor-primary-key-name="<?php echo $editor_primary_key_name ?>" data-editor-content-key-name="<?php echo $editor_content_key_name ?>" style="min-height:<?php echo $editor_height ?>px;"></div>
</div>
<!-- <block cond="$allow_fileupload"> set in \includes\modules\editor\editor.model.php::_get_editor()
	<include target="file_upload.html" />
</block> -->

<?php
$editorContentCssFilemtime = filemtime($content_style_path . '/editor.css');
$lang_type = 'ko';
$lang_type = str_replace('jp','ja',$lang_type);
?>

<script>
(function($){
	"use strict";
	// editor
	$(function(){
<?php if(!\X2board\Includes\Classes\FileHandler::exists('common/js/plugins/ckeditor/ckeditor/config.js')): ?>
			CKEDITOR.config.customConfig = '';
<?php endif ?>

		var settings = {
			ckeconfig: {
				height: '<?php echo $editor_height?>',
				skin: '<?php echo $colorset ?>',
				contentsCss: '<?php echo $content_style_url?>/editor.css?<?php echo $editorContentCssFilemtime?>',
				x2b_editor_sequence: <?php echo $editor_sequence ?>,
				toolbarCanCollapse: true,
				language: "<?php echo $lang_type?>"
			},
			loadXeComponent: true,
			enableToolbar: true,
			content_field: jQuery('[name=<?php echo $editor_content_key_name?>]')
		};

		CKEDITOR.dtd.$removeEmpty.ins = 0;
		CKEDITOR.dtd.$removeEmpty.i = 0;

<?php if($enable_component): 
	$xe_component = array();
	foreach($component_list as $component_name => $component) {
		$xe_component[] = $component_name . ":'" . htmlentities($component->title, ENT_QUOTES, 'UTF-8') . "'";
	}
	$xe_component = implode(',', $xe_component);
?>
			settings.ckeconfig.xe_component_arrays = {<?php echo $xe_component?>};
<?php endif ?>
		

<?php if(!$enable_default_component): ?>
		
			settings.enableToolbar = false;
			settings.ckeconfig.toolbarCanCollapse = false;
		
<?php endif ?>

<?php if(!$enable_component): ?>
		
			settings.loadXeComponent = false;
		
<?php endif ?>

<?php if($module_type === 'comment' || wp_is_mobile() ): ?>
		
			settings.ckeconfig.toolbarStartupExpanded = false;
		
<?php endif ?>
		
<?php if(!$html_mode): ?>
		
			settings.ckeconfig.removeButtons = 'Save,Preview,Print,Cut,Copy,Paste,Source';
		
<?php endif ?>

<?php if($css_content): ?>
		
		CKEDITOR.addCss('<?php echo $css_content ?>');
<?php endif ?>
		var ckeApp = $('#ckeditor_instance_<?php echo $editor_sequence ?>').XeCkEditor(settings);
	});
})(jQuery);
</script>