<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>

<?php
// <include target="_header.html" />
include $skin_path_abs.'_header.php';
// <load target="js/editor.js" type="body" />
wp_enqueue_script('x2board-sketchbook5-editor', $skin_url . '/js/editor.js', [], X2B_VERSION, true);

if(wp_is_mobile() && $mi->m_editor!=3) {  	// cond="wp_is_mobile() && $mi->m_editor!=3"
	include $skin_path_abs.'_write_form_mobile.php'; 	// <include target="_write_form_mobile.html" />
}?>

<?php if(!wp_is_mobile() || $mi->m_editor==3): ?><!-- cond="!Mobile::isMobileCheckByAgent() || $mi->m_editor==3" -->
	
    <style>		
a.x2board-default-button-small,
input.x2board-default-button-small,
button.x2board-default-button-small { position: relative; display: inline-block; zoom: 1; margin: 0; padding: 0 10px; width: auto; height: 28px; line-height: 28px; font-size: 14px; font-weight: normal; letter-spacing: normal; color: #545861 !important; background: #eaeaea !important; border: none; border-radius: 0; text-decoration: none !important; cursor: pointer; vertical-align: middle; text-shadow: none; box-shadow: none; transition-duration: 0.3s; box-sizing: content-box; }
a.x2board-default-button-small:hover,
a.x2board-default-button-small:focus,
a.x2board-default-button-small:active,
input.x2board-default-button-small:hover,
input.x2board-default-button-small:focus,
input.x2board-default-button-small:active,
button.x2board-default-button-small:hover,
button.x2board-default-button-small:focus,
button.x2board-default-button-small:active { opacity: 0.7; }

a.x2board-default-button-medium,
input.x2board-default-button-medium,
button.x2board-default-button-medium {
	word-wrap: break-word;
    -webkit-text-size-adjust: none;
    font-family: 'Lato', 'Nanum Gothic', 'Apple SD Gothic Neo', 'Malgun Gothic', '돋움', Dotum, 'Lucida Sans', 'Trebuchet MS', Arial, Tahoma, sans-serif;
    outline: none;
    box-sizing: border-box;
    transition: border .4s,background .4s;
    /* display: inline-block; */
    position: relative;
    margin: 0;
    /* padding: 4px 20px; */
    border: 1px solid;
    border-radius: 3px;
    white-space: nowrap;
    cursor: pointer;
    text-decoration: none !important;
    text-align: center;
    font-size: 12px;
    line-height: 1.5;
    font-weight: 700;
    /* text-shadow: 0 1px 0 #000; */
    /* border-color: #669 !important;
    box-shadow: 0 1px 1px rgba(0,0,0,.1); */
    /* background: linear-gradient(to bottom,#77C 0%,#55B 100%); */
    /* height: 34px; */
    min-width: 92px;
    margin-left: 20px;
}

.white {
	color: #000 !important;
	border-color: #669 !important;
	box-shadow: 0 1px 1px rgba(0,0,0,.1);
	background: linear-gradient(to bottom,#FFF 0%,#F3F3F3 100%);
}

.blue {
	color: #FFF !important;
	border-color: #669 !important;
    box-shadow: 0 1px 1px rgba(0,0,0,.1);
	background: linear-gradient(to bottom,#77C 0%,#55B 100%);
}
/* { position: relative; display: inline-block; zoom: 1; margin: 0; padding: 0 10px; width: auto; height: 28px; line-height: 28px; font-size: 14px; font-weight: normal; letter-spacing: normal; color: #545861 !important; background: #eaeaea !important; border: none; border-radius: 0; text-decoration: none !important; cursor: pointer; vertical-align: middle; text-shadow: none; box-shadow: none; transition-duration: 0.3s; box-sizing: content-box; } */
a.x2board-default-button-medium:hover,
a.x2board-default-button-medium:focus,
a.x2board-default-button-medium:active,
input.x2board-default-button-medium:hover,
input.x2board-default-button-medium:focus,
input.x2board-default-button-medium:active,
button.x2board-default-button-medium:hover,
button.x2board-default-button-medium:focus,
button.x2board-default-button-medium:active { opacity: 0.7; }

#x2board-default-list .x2board-control { float: left; padding-bottom: 15px; width: 100%; text-align: right; }

#x2board-default-editor { overflow: hidden; word-wrap: break-word;
	-webkit-text-size-adjust: none;
	font-family: Lato, "Nanum Gothic", "Apple SD Gothic Neo", "Malgun Gothic", 돋움, Dotum, "Lucida Sans", "Trebuchet MS", Arial, Tahoma, sans-serif;
	font-size: 12px;
	line-height: 1.5;
	color: #444;
	box-sizing: border-box;
	margin: 0;
	padding: 15px;
	background: #F3F3F3;
	border: 1px solid #DDD;
	border-radius: 10px;
	display: block;
	position: relative;
	clear: both;}
#x2board-default-editor form { margin: 0; padding: 0; }
#x2board-default-editor select {    
    border-collapse: collapse;
    word-break: break-all;
    word-wrap: break-word;
    font-family: Tahoma,Geneva,sans-serif;
    font-size: 12px;
    line-height: 1.5;
    box-sizing: border-box;
    transition: border .4s,background .4s;
    display: inline-block;
    margin: 0;
    padding: 4px 6px;
    background: #FAFAFA;
	width: 30%;
    border-radius: 3px;
    border: 1px solid;
    border-color: #BBB #DDD #DDD #BBB;
	-webkit-text-size-adjust: none;
	-webkit-appearance: menulist; 
	-moz-appearance: menulist; 
	appearance: menulist;
}
/* { display: inline; margin: 0; padding: 0 5px; font-size: 14px; width: 30%; height: 28px; line-height: 28px; color: #666666; border-radius: 0; border: 0; border-bottom: 1px solid #dcdcdc; box-shadow: none; background: none; background-color: transparent; box-sizing: content-box; vertical-align: middle; text-indent: 0; -webkit-appearance: menulist; -moz-appearance: menulist; appearance: menulist; } */
#x2board-default-editor input[type=text],
#x2board-default-editor input[type=email],
#x2board-default-editor input[type=number],
#x2board-default-editor input[type=date],
#x2board-default-editor input[type=password] {
	word-wrap: break-word;
    -webkit-text-size-adjust: none;
    border-collapse: collapse;
    font-family: Lato, "Nanum Gothic", "Apple SD Gothic Neo", "Malgun Gothic", 돋움, Dotum, "Lucida Sans", "Trebuchet MS", Arial, Tahoma, sans-serif;
    outline: none;
    font-size: 12px;
    line-height: 1.5;
    box-sizing: border-box;
    transition: border .4s,background .4s;
    display: inline-block;
    margin: 0;
    padding: 4px 6px;
    background: #FAFAFA;
    border-radius: 3px;
    border: 1px solid;
    border-color: #BBB #DDD #DDD #BBB;
    height: 28px;
    width: 100%;
}
/* { display: inline; margin: 0; padding: 0 5px; width: 30%; height: 28px; line-height: 28px; font-size: 14px; color: #666666; border-radius: 0; border: 0; border-bottom: 1px solid #dcdcdc; box-shadow: none; background: none; background-color: transparent; box-sizing: content-box; vertical-align: middle; } */
#x2board-default-editor input[type=checkbox] { width: auto; -webkit-appearance: checkbox; -moz-appearance: checkbox; appearance: checkbox; }
#x2board-default-editor input[type=radio] { width: auto; -webkit-appearance: radio; -moz-appearance: radio; appearance: radio; }
#x2board-default-editor select:hover,
#x2board-default-editor input[type=text]:hover,
#x2board-default-editor input[type=email]:hover,
#x2board-default-editor input[type=number]:hover,
#x2board-default-editor input[type=date]:hover,
#x2board-default-editor input[type=password]:hover { border-bottom: 1px solid #9e9e9e; }
/* #x2board-default-editor input[type=file] { display: inline; margin: 0; padding: 0; width: 30%; font-size: 12px; color: #666666; border-radius: 0; border: 0; box-shadow: none; background-color: transparent; } */
#x2board-default-editor .x2board-attr-title input { width: 70%; }
#x2board-default-editor .x2board-attr-row { float: left; padding: 5px 0; width: 100%; background-color: #F3F3F3; }
#x2board-default-editor .x2board-attr-row .attr-name { display: inline; float: left; margin: 0; padding: 0 10px 0 10px; width: 120px; line-height: 30px; color: #545861; font-size: 12px; font-weight: bold; }
#x2board-default-editor .x2board-attr-row .attr-name img { display: inline; margin: 0; padding: 0; width: auto; max-width: 100%; vertical-align: middle; }
#x2board-default-editor .x2board-attr-row .attr-name .attr-required-text { color: red; font-weight: normal; font-size: 12px; }
#x2board-default-editor .x2board-attr-row.x2board-attr-content .attr-name { display: none; }
#x2board-default-editor .x2board-attr-row .attr-value { margin: 0 0 0 140px; padding: 0; line-height: 30px; font-size: 12px; }
#x2board-default-editor .x2board-attr-row .attr-value .attr-value-option { display: inline; padding-right: 20px; width: auto; font-size: 12px; cursor: pointer; }
#x2board-default-editor .x2board-attr-row .attr-value .attr-value-option input { cursor: pointer; }
#x2board-default-editor .x2board-attr-row .attr-value .attr-value-label { display: inline; padding-right: 5px; width: auto; cursor: pointer; }
#x2board-default-editor .x2board-attr-row .attr-value .attr-value-label input { cursor: pointer; }
#x2board-default-editor .x2board-attr-row .attr-value .description { margin: 0; color: #666666; }
/* #x2board-default-editor .x2board-attr-row .attr-value .x2board-tree-category-wrap { float: left; width: 100%; }
#x2board-default-editor .x2board-attr-row .attr-value .x2board-tree-category-wrap select { clear: both; float: left; margin-bottom: 5px; width: 30%; } */
#x2board-default-editor .x2board-content { float: left; margin: 0; padding: 0; width: 100%; background-color: white; }
#x2board-default-editor .x2board-content .editor-textarea { display: inline; margin: 0; padding: 4px 6px; width: 100%; min-width: 100%; max-width: 100%; height: 250px; min-height: 0; font-size: 12px; border: 1px solid #dcdcdc; box-shadow: none; background: none; background-color: transparent; box-sizing: border-box; text-indent: 0; }
#x2board-default-editor .x2board-control { border-top: 1px solid #CCC; float: left; padding: 15px 0; width: 100%; }
#x2board-default-editor .x2board-control .left { position: static; float: left; }
#x2board-default-editor .x2board-control .center { position: static; float: center; width: 100%; text-align: center; }
#x2board-default-editor .x2board-control .right { position: static; float: right; width: 50%; text-align: right; }
/* #x2board-default-editor.confirm { margin: 100px auto; max-width: 590px; }
#x2board-default-editor.confirm input[type=password] { width: 100%; box-sizing: border-box; } */
	</style>

	<div id="x2board-default-editor" class="bd">
        <!-- onsubmit="return procFilter(this, window.insert)" -->
		<form class="x2board-form" id="x2board-post-form" action="<?php echo esc_url(x2b_get_url('cmd', '', 'post_id', ''))?>" method="post" class="bd_wrt bd_wrt_main clear">
			<?php x2b_write_post_input_fields(); ?>

			<!--// Buttons -->
			<div class="x2board-control">
				<div class="center">
					<button type="submit" class="x2board-default-button-medium blue"><?php echo __('cmd_submit', X2B_DOMAIN)?></button>
					<button type="button" class="x2board-default-button-medium white" onClick="history.back()"><?php echo __('cmd_back', X2B_DOMAIN)?></button>
				</div>
			</div>
		</form>
	</div>
<?php endif ?>
</div>