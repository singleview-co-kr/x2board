<include target="_header.html" />
<div class="context_message">
    <h1><?php echo $message?></h1>
    <div class="btnArea">
<?php if(!$is_logged):?>
        <a class="bd_btn blue" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'cmd_login', 'x2board' ); ?></a>
<?php endif ?>		
        <button class="bd_btn" type="button" onclick="history.back();"><?php echo __('cmd_back', 'x2board')?></button>
    </div>
</div>
<include target="_footer.html" />