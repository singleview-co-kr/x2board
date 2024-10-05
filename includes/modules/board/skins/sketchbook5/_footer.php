<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>
	<?php if($current_module_info->footer_text):?>  	<!-- cond="$module_info->footer_text" -->
		<div class="footer_text"><?php echo $current_module_info->footer_text?></div>
	<?php endif?>
</div>