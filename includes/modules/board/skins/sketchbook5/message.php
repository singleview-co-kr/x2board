<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}
// <include target="_header.html" />
include $skin_path_abs.'_header.php';
?>
<div class="context_message">
    <h1><?php echo $message?></h1>
    <div class="btnArea">
<?php if( $is_member_registration && !$is_logged):?>
        <a class="bd_btn_login blue" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php echo __( 'cmd_login', X2B_DOMAIN ); ?></a>
<?php endif ?>		
        <button class="bd_btn" type="button" onclick="history.back();"><?php echo __('cmd_back', X2B_DOMAIN)?></button>
    </div>
</div>
<?php
// <include target="_footer.html" />
include $skin_path_abs.'_footer.php';
?>