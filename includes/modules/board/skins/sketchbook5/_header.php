<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>

<?php include $skin_path_abs.'__setting.php'; ?>   <!-- <include target="__setting.html" /> -->

<!--// 상단 html 출력 -->
<div><?php echo $current_module_info->header_text?></div>

<div id="bd" class="bd">