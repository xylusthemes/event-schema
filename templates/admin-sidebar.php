<?php
/**
 * Sidebar for Admin Pages
 *
 * @package     Event_Schema
 * @subpackage  Event_Schema/templates
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="upgrade_to_pro">
	<h2><?php esc_html_e( 'Custom WordPress Development Services','event-schema'); ?></h2>
	<p><?php esc_html_e( "From small blog to complex web apps, we push the limits of what's possible with WordPress.","event-schema" ); ?></p>
	<a class="button button-primary upgrade_button" href="<?php echo esc_url('https://xylusthemes.com/contact/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin'); ?>" target="_blank">
		<?php esc_html_e( 'Hire Us','event-schema'); ?>
	</a>
</div>

<div>
	<p style="text-align:center">
		<strong><?php esc_html_e( 'Would you like to remove these ads?','event-schema'); ?></strong><br>
		<a href="<?php echo esc_url( ES_PLUGIN_BUY_NOW_URL ); ?>" target="_blank">
			<?php esc_html_e( 'Get Premium','event-schema'); ?>
		</a>
	</p>
</div>