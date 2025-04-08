<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $event_schema;
$schema_options = get_option( ES_OPTIONS , array() );
$active_plugins = $event_schema->common->get_active_supported_event_plugins();
?>
<div class="es_container">
    <div class="es_row">
    	
    	<form method="post" id="es_setting_form">                

            <table class="form-table">
                <tbody>
                    <?php
                        if ( !empty( $active_plugins ) ) {
                            foreach ($active_plugins as $key => $value) {
                                ?>
                                <tr>  
                                    <th scope="row">
                                        <?php printf( esc_attr__( 'Disable Event Schema for %s', 'event-schema' ), esc_attr( $value ) ) ; // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?> : 
                                    </th>
                                    <td>
                                        <?php 
                                        $update_facebook_events = isset( $facebook_options['update_events'] ) ? $facebook_options['update_events'] : 'no';
                                        ?>
                                        <input type="checkbox" name="event_schema[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $key ); ?>" <?php if( in_array( $key, $schema_options ) ) { echo 'checked="checked"'; } ?> />
                                        <span class="xtei_small">
                                            <?php printf( esc_attr__( 'Check to disable event schema(structured data) for %s .', 'event-schema' ), esc_attr( $value ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php                                
                            }
                        } else {

                        }
                    ?>
                </tbody>
            </table>
            <br/>

            <div class="es_element">
                <input type="hidden" name="es_action" value="es_save_settings" />
                <?php wp_nonce_field( 'es_setting_form_nonce_action', 'es_setting_form_nonce' ); ?>
                <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'event-schema' ); ?>" />
            </div>
            </form>
    </div>
</div>
