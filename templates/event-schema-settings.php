<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $event_schema;
$schema_options = get_option( ES_OPTIONS , array() );
$active_plugins = $event_schema->common->get_active_supported_event_plugins();
?>
<div class="ec-card" style="margin-top: 20px;">
    <form method="post" id="es_setting_form">
        <div class="xtei-settings-wrapper">
            <h2 class="xtei-section-title"><?php esc_html_e( 'Event Schema Controls', 'event-schema' ); ?></h2>
            <div class="xtei-grid">
                <?php
                $pro_plugins = array( 'eventprime', 'eventin', 'wp_events_manager', 'foo_event', 'eec' );

                if ( ! empty( $active_plugins ) ) {
                    foreach ( $active_plugins as $key => $value ) {

                        $is_pro = in_array( $key, $pro_plugins, true );
                        $is_checked = in_array( $key, $schema_options ) ? 'checked="checked"' : '';
                        ?>
                        <div class="xtei-item <?php echo $is_pro ? 'xtei-pro' : ''; ?>">
                            <?php if ( $is_pro ) : ?>
                                <span class="xtei-pro-badge">PRO</span>
                            <?php endif; ?>

                            <div class="xtei-label">
                                <?php printf( esc_html__( 'Disable Event Schema for %s', 'event-schema' ), esc_html( $value ) ); ?>
                            </div>

                            <label class="xtei-switch" <?php echo $is_pro ? 'style="opacity: 0.5"' : ''; ?> >
                                <input type="checkbox" 
                                    name="<?php echo $is_pro ? '' : 'event_schema[' . esc_attr( $key ) . ']'; ?>" 
                                    value="<?php echo esc_attr( $key ); ?>" 
                                    <?php echo $is_checked; ?> 
                                    <?php echo $is_pro ? 'disabled' : ''; ?> 
                                >
                                <span class="xtei-slider"></span>
                            </label>

                            <p class="xtei-help">
                                <?php printf( esc_html__( 'Disable structured data (schema.org) output for %s.', 'event-schema' ), esc_html( $value ) ); ?>
                            </p>

                            <?php if ( $is_pro ) : ?>
                                <a href="https://xylusthemes.com/plugins/event-schema/" target="_blank" class="xtei-pro-link">
                                    <?php esc_html_e( 'Get PRO version', 'event-schema' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="xtei-save-wrap">
                <input type="hidden" name="es_action" value="es_save_settings">
                <?php wp_nonce_field( 'es_setting_form_nonce_action', 'es_setting_form_nonce' ); ?>
                <input type="submit" class="wpec_button xtei_submit_button" value="<?php esc_attr_e( 'Save Settings', 'event-schema' ); ?>">
            </div>
        </div>
    </form>
</div>
