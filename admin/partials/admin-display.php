<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://stehle-internet.de/
 * @since      1.0.0
 *
 * @package    Quick_New_Site_Defaults
 * @subpackage Quick_New_Site_Defaults/admin/partials
 */

 
if ( ! empty( $_POST ) ) {
	// For backwards compat with plugins that don't use the Settings API and just set updated=1 in the redirect
	$text = 'Settings saved.';
	add_settings_error( 'general', 'settings_updated', __( $text ), 'updated' );
}
?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php settings_errors(); ?>
	<?php $this->check_permissions(); ?>
	<p><?php esc_html_e( 'Set the default settings of the freshly installed site with one single click.', 'quick-new-site-defaults' ); ?>.</p>
	<div class="qnsd_wrapper">
		<div id="qnsd_main">
			<div class="qnsd_content">
				<form method="post" action="">
					<table class="form-table">
						<tbody>
<?php
foreach( $msgs as $key => $element ) {
?>
							<tr>
								<th scope="row"><?php echo $element[ 'label' ]; ?></th>
								<td><?php echo $element[ 'text' ]; ?></td>
							</tr>
<?php
} // foreach( msgs )
?>
						</tbody>
					</table>
<?php
wp_nonce_field( 'quick_set_options', 'quick_set_options_nonce' );
submit_button(); 
?>
				</form>
				<?php // <p><a href="https://codex.wordpress.org/Function_Reference/deactivate_plugins">Deactivate</a> and delete plugin</p> ?>
			</div><!-- .qnsd_content -->
		</div><!-- #qnsd_main -->
		<div id="qnsd_footer">
			<div class="qnsd_content">
				<h2><?php esc_html_e( 'Helpful Links', 'quick-new-site-defaults' ); ?></h2>
				<dl>
					<dt><?php esc_html_e( 'Do you like the plugin?', 'quick-new-site-defaults' ); ?></dt>
					<dd><a href="http://wordpress.org/support/view/plugin-reviews/quick-new-site-defaults"><?php esc_html_e( 'Rate it at wordpress.org!', 'quick-new-site-defaults' ); ?></a></dd>
					<dt><?php esc_html_e( 'Do you need support or have an idea for the plugin?', 'quick-new-site-defaults' ); ?></dt>
					<dd><a href="http://wordpress.org/support/plugin/quick-new-site-defaults"><?php esc_html_e( 'Post your questions and ideas about Quick New Site Defaults in the forum at wordpress.org!', 'quick-new-site-defaults' ); ?></a></dd>
					<dt><?php esc_html_e( 'The plugin is for free. But the plugin author would be delighted to your small contribution.', 'quick-new-site-defaults' ); ?></dt>
					<dd><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=ECK28LWNT44EU"><img src="https://www.paypalobjects.com/<?php echo $this->get_paypal_locale(); ?>/i/btn/btn_donateCC_LG.gif" alt="(<?php esc_html_e( 'Donation Button', 'quick-new-site-defaults' ); ?>)" id="paypal_button" /><br /><?php esc_html_e( 'Donate with PayPal', 'quick-new-site-defaults' ); ?></a><img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1" /></dd>
				</dl>
			</div><!-- .qnsd_content -->
		</div><!-- #qnsd_footer -->
	</div><!-- .qnsd_wrapper -->
</div><!-- .wrap -->
