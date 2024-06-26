<?php
/**
 * Sidebar
 *
 * @link  https://singleview.co.kr
 *
 * @package    x2board
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div class="postbox-container">
	<div id="donatediv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php echo __( 'cmd_support_x2board', X2B_DOMAIN ); ?></span></h3>
			<div class="inside" style="text-align: center">
				<div id="donate-form">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="business" value="donate@singleview.co.kr">
						<input type="hidden" name="lc" value="IN">
						<input type="hidden" name="item_name" value="<?php echo __( 'lbl_donate_for_x2board', X2B_DOMAIN ); ?>">
						<input type="hidden" name="item_number" value="x2board_plugin_settings">
						<strong><?php echo __( 'cmd_enter_amount_in_usd', X2B_DOMAIN ); ?></strong>: <input name="amount" value="15.00" size="6" type="text"><br />
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="button_subtype" value="services">
						<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
						<input type="image" src="<?php echo esc_url( X2B_URL . 'includes/admin/images/paypal_donate_button.gif' ); ?>" border="0" name="submit" alt="<?php echo __( 'cmd_support_x2board', X2B_DOMAIN ); ?>">
						<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
				<!-- /#donate-form -->
			</div>
			<!-- /.inside -->
	</div>
	<!-- /.postbox -->

	<div id="qlinksdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php echo __( 'lbl_quick_links', X2B_DOMAIN ); ?></span></h3>
			<div class="inside">
				<div id="quick-links">
					<ul>
						<li>
							<a href="https://singleview.co.kr/plugins/x2board/">
								<?php echo __( 'lbl_x2board_plugin_homepage', X2B_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://wordpress.org/plugins/x2board/faq/">
								<?php echo __( 'lbl_wp_faq', X2B_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="http://wordpress.org/support/plugin/x2board">
								<?php echo __( 'lbl_wp_support', X2B_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://wordpress.org/support/view/plugin-reviews/x2board">
								<?php echo __( 'lbl_wp_reviews', X2B_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://github.com/">
								<?php echo __( 'lbl_github', X2B_DOMAIN ); ?>
							</a>
						</li>
						<!-- <li>
							<a href="https://singleview.co.kr/plugins/">
								<?php // echo __( 'Other plugins', X2B_DOMAIN ); ?>
							</a>
						</li> -->
					</ul>
				</div>
			</div>
			<!-- /.inside -->
	</div>
	<!-- /.postbox -->
</div>
