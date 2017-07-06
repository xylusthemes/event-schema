<?php
/**
 * Template file for Support page.
 *
 * @package     Event_Schema
 * @subpackage  Event_Schema/admin
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $event_schema;
$open_source_support_url = 'https://wordpress.org/support/plugin/event-schema/';
$support_url = 'https://xylusthemes.com/support/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin';

$review_url = 'https://wordpress.org/support/plugin/event-schema/reviews/?rate=5#new-post';
$facebook_url = 'https://www.facebook.com/xylusinfo/';
$twitter_url = 'https://twitter.com/XylusThemes/';

?>
<div class="wpea_container">
    <div class="wpea_row">
        <div class="wpea-column support_well">
        	<h3><?php esc_attr_e( 'Getting Support', 'event-schema' ); ?></h3>
            <p><?php _e( 'Thanks you for using Event Schema, We are sincerely appreciate your support and we’re excited to see you using our plugins.','event-schema' ); ?> </p>
            <p><?php _e( 'Our support team is always around to help you.','event-schema' ); ?></p>
                
            <p><strong><?php _e( 'Looking for free support?','event-schema' ); ?></strong></p>
            <a class="button button-secondary" href="<?php echo $open_source_support_url; ?>" target="_blank" >
                <?php _e( 'Open-source forum on WordPress.org','event-schema' ); ?>
            </a>

            <p><strong><?php _e( 'Looking for more immediate support?','event-schema' ); ?></strong></p>
            <p><?php _e( 'We offer premium support on our website with the purchase of our premium plugins.','event-schema' ); ?>
            </p>
            
            <a class="button button-primary" href="<?php echo $support_url; ?>" target="_blank" >
                <?php _e( 'Contact us directly (Premium Support)','event-schema' ); ?>
            </a>

            <p><strong><?php _e( 'Enjoying Event Schema or have feedback?','event-schema' ); ?></strong></p>
            <a class="button button-secondary" href="<?php echo $review_url; ?>" target="_blank" >Leave us a review</a> 
            <a class="button button-secondary" href="<?php echo $twitter_url; ?>" target="_blank" >Follow us on Twitter</a> 
            <a class="button button-secondary" href="<?php echo $facebook_url; ?>" target="_blank" >Like us on Facebook</a>
        </div>

        <?php 
        $plugins = array();
        $plugin_list = $event_schema->admin->get_xylus_themes_plugins();
        if ( ! empty( $plugin_list ) ) {
            foreach ( $plugin_list as $key => $value ) {
                $plugins[] = $event_schema->admin->get_wporg_plugin( $key );
            }
        }
        ?>
        <div class="" style="margin-top: 20px;">
            <h3 class="setting_bar"><?php _e( 'Plugins you should try','event-schema' ); ?></h3>
            <?php 
            if( !empty( $plugins ) ){
                foreach ($plugins as $plugin ) {
                    ?>
                    <div class="plugin_box">
                        <?php if( $plugin->banners['low'] != '' ){ ?>
                            <img src="<?php echo $plugin->banners['low']; ?>" class="plugin_img" title="<?php echo $plugin->name; ?>">
                        <?php } ?>                    
                        <div class="plugin_content">
                            <h3><?php echo $plugin->name; ?></h3>

                            <?php wp_star_rating( array(
                            'rating' => $plugin->rating,
                            'type'   => 'percent',
                            'number' => $plugin->num_ratings,
                            ) );?>

                            <?php if ( '' != $plugin->version ) { ?>
                                <p><strong><?php esc_html_e( 'Version:','event-schema' ); ?> </strong><?php echo $plugin->version; ?></p>
                            <?php } ?>

                            <?php if ( '' != $plugin->requires ) { ?>
                                <p><strong><?php esc_html_e( 'Requires:','event-schema' ); ?> </strong> <?php _e( 'WordPress ','event-schema' ); echo $plugin->requires; ?>+</p>
                            <?php } ?>

                            <?php if( $plugin->active_installs != '' ){ ?>
                                <p><strong><?php esc_html_e( 'Active Installs:','event-schema' ); ?> </strong><?php echo $plugin->active_installs; ?>+</p>
                            <?php } ?>

                            <a class="button button-secondary" href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin->slug. '&TB_iframe=1&width=772&height=600'); ?>" target="_blank">
                                <?php esc_html_e( 'Install Now','event-schema' ); ?>
                            </a>
                            <a class="button button-primary" href="<?php echo $plugin->homepage . '?utm_source=crosssell&utm_medium=web&utm_content=supportpage&utm_campaign=freeplugin'; ?>" target="_blank">
                                <?php esc_html_e( 'Buy Now','event-schema' ); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div style="clear: both;">
        </div>
    </div>

</div>