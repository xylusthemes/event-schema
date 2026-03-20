<?php
/**
 * Common functions class for Event Schema.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Event_Schema
 * @subpackage Event_Schema/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The common functionality of the plugin.
 *
 * @package     Event_Schema
 * @subpackage  Event_Schema/includes
 */
class Event_Schema_Common {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'handle_schema_settings_submit' ), 99 );
	}
	/**
	 * Save Setting for Event schema.
	 *
	 * @since    1.0.0
	 */
	public function handle_schema_settings_submit() {
		global $es_errors, $es_success_msg;
		if ( isset( $_POST['es_action'] ) && $_POST['es_action'] == 'es_save_settings' &&  check_admin_referer( 'es_setting_form_nonce_action', 'es_setting_form_nonce' ) ) {
				
			$es_options = array();
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$es_options = isset( $_POST['event_schema'] ) ? $_POST['event_schema'] : array();
			$is_update = update_option( ES_OPTIONS, $es_options );
			if( $is_update ){
				$es_success_msg[] = __( 'Import settings has been saved successfully.', 'event-schema' );
			}else{
				$es_errors[] = __( 'Something went wrong! please try again.', 'event-schema' );
			}
		}
	}

	/**
	 * Get Active supported active plugins.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_active_supported_event_plugins() {

		$supported_plugins                             = array();
		$supported_plugins['em']                       = esc_attr__( 'Events Manager', 'event-schema' );
		$supported_plugins['event_organizer']          = esc_attr__( 'Event Organiser', 'event-schema' );
		$supported_plugins['eventon']                  = esc_attr__( 'EventON', 'event-schema' );
		$supported_plugins['aioec']                    = esc_attr__( 'All in One Event Calendar', 'event-schema' );
		$supported_plugins['import_facebook_events']   = esc_attr__( 'Import Facebook Events', 'event-schema' );
		$supported_plugins['import_eventbrite_events'] = esc_attr__( 'Import Eventbrite Events', 'event-schema' );
		$supported_plugins['import_meetup_events']     = esc_attr__( 'Import Meetup Events', 'event-schema' );
		$supported_plugins['wp_event_aggregator']      = esc_attr__( 'WP Event Aggregator', 'event-schema' );
		$supported_plugins['eventprime']               = esc_attr__( 'Event Prime', 'event-schema' );
		$supported_plugins['eventin']                  = esc_attr__( 'EventIn', 'event-schema' );
		$supported_plugins['wp_events_manager']        = esc_attr__( 'WP Events Manager', 'event-schema' );
		$supported_plugins['foo_event']                = esc_attr__( 'Foo Event', 'event-schema' );
		return $supported_plugins;
	}

	/**
	 * Generate ld+json for event.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function generate_ldjson( $event = array(), $wrapper = true ){

		if( empty($event ) ){
			return;
		}
		$xt_ldjson = '';
		if( $wrapper ){
			$xt_ldjson .= '<script type="application/ld+json">';
		}		
		$xt_ldjson .= '{';
	
		if( isset( $event['name'] ) && $event['name'] != '' ){
			$xt_ldjson .= '"name":"' . esc_attr( $event["name"] ) . '",';
		}
		if( isset( $event['description'] ) && $event['description'] != '' ){
			$xt_ldjson .= '"description":' . json_encode( $event["description"] ). ',';
		}
		if( isset( $event['url'] ) && $event['url'] != '' ){
			$xt_ldjson .= '"url":"' . esc_url( $event["url"] ) . '",';
		}
		if( isset( $event['start_date'] ) && $event['start_date'] != '' ){
			$xt_ldjson .= '"startDate":"' . $event["start_date"] . '",';
		}
		if( isset( $event['end_date'] ) && $event['end_date'] != '' ){
			$xt_ldjson .= '"endDate":"' . $event["end_date"] . '",';
		}
		if( isset( $event['image'] ) && $event['image'] != '' ){
			$xt_ldjson .= '"image":"' . esc_url( $event["image"] ) . '",';
		}

		if( isset( $event['location'] ) && !empty( $event['location'] ) ){
			$location = $event['location'];
			$xt_ldjson .= '"location":{';
			if( isset( $location['name'] ) && $location['name'] != '' ){
				$xt_ldjson .= '"name":"' . esc_attr( $location["name"] ) . '",';
			}
			if( isset( $location['telephone'] ) && $location['telephone'] != '' ){
				$xt_ldjson .= '"telephone":"' . esc_attr( $location["telephone"] ) . '",';
			}
			if( isset( $location['url'] ) && $location['url'] != '' ){
				$xt_ldjson .= '"url":"' . esc_attr( $location["url"] ) . '",';
			}

			if( isset( $location['address'] ) && !empty( $location['address'] ) ){
				$address = $location['address'];
				$xt_ldjson .= '"address":{';
				if( isset( $address['street_address'] ) && $address['street_address'] != '' ){
					$xt_ldjson .= '"streetAddress":"' . esc_attr( $address["street_address"] ) . '",';
				}
				if( isset( $address['address_locality'] ) && $address['address_locality'] != '' ){
					$xt_ldjson .= '"addressLocality":"' . esc_attr( $address["address_locality"] ) . '",';
				}
				if( isset( $address['address_region'] ) && $address['address_region'] != '' ){
					$xt_ldjson .= '"addressRegion":"' . esc_attr( $address["address_region"] ) . '",';
				}
				if( isset( $address['address_country'] ) && $address['address_country'] != '' ){
					$xt_ldjson .= '"addressCountry":"' . esc_attr( $address["address_country"] ) . '",';
				}
				if( isset( $address['postal_code'] ) && $address['postal_code'] != '' ){
					$xt_ldjson .= '"postalCode":"' . esc_attr( $address["postal_code"] ) . '",';
				}
				$xt_ldjson .= '"@type":"PostalAddress"
				},';
			}
			if( isset( $location['geo'] ) && !empty( $location['geo'] ) ){
				$geo = $location['geo'];
				if( !empty( $geo['latitude'] ) && ! empty( $geo['longitude'] ) ){
					$xt_ldjson .= '"geo": {';
					$xt_ldjson .= '"latitude": "' . $geo["latitude"] . '",';
					$xt_ldjson .= '"longitude": "' . $geo["longitude"] . '",';
					$xt_ldjson .= '"@type":"GeoCoordinates"
					},';
				}
			}
			$xt_ldjson .= '"@type":"Place"	
			},';
		}

		if( isset( $event['organizer'] ) && !empty( $event['organizer'] ) ){
			$organizer = $event['organizer'];
			if( isset( $organizer['name'] ) && $organizer['name'] != '' ){
				$xt_ldjson .= '"organizer": {';
				$xt_ldjson .= '"name":"' . $organizer["name"] . '",';
				if( isset( $organizer['description'] ) && $organizer['description'] != '' ){
					$xt_ldjson .= '"description": "' . addslashes( $organizer["description"] ) . '",';	
				}
				if( isset( $organizer['email'] ) && $organizer['email'] != '' ){
					$xt_ldjson .= '"email": "' . esc_url( $organizer["email"] ) . '",';	
				}
				if( isset( $organizer['telephone'] ) && $organizer['telephone'] != '' ){
					$xt_ldjson .= '"telephone":"' . esc_attr( $organizer["telephone"] ) . '",';
				}
				if( isset( $organizer['url'] ) && $organizer['url'] != '' ){
					$xt_ldjson .= '"url": "' . esc_url( $organizer["url"] ) . '",';	
				}
				$xt_ldjson .= '"@type":"Organization"
				},';
			}
		}

		if( isset( $event['offers'] ) && !empty( $event['offers'] ) ){
			$offers = $event['offers'];
			if( !empty( $offers['low_price'] ) || ! empty( $offers['high_price'] ) ){
				$xt_ldjson .= '"offers": {';
				if ( !empty( $offers['low_price'] ) && ! empty( $offers['high_price'] ) ) {
					$xt_ldjson .= '"price": "' .  $offers["low_price"] . '",';
					$xt_ldjson .= '"lowPrice": "' .  $offers["low_price"] . '",';
					$xt_ldjson .= '"highPrice": "' .  $offers["high_price"] . '",';	
				} else {
					$xt_ldjson .= '"price": "' . $offers["high_price"] . '",';	
				}
				if( isset( $offers['url'] ) && $offers['url'] != '' ){
					$xt_ldjson .= '"url": "' . esc_url( $offers["url"] ) . '",';	
				}
				if( isset( $offers['price_currency'] ) && $offers['price_currency'] != '' ){
					$xt_ldjson .= '"priceCurrency": "' . $offers["price_currency"] . '",';	
				}
				$xt_ldjson .= '"@type":"AggregateOffer"
				},';
			}
		}

		$xt_ldjson .= '"@context":"http://schema.org",
			"@type":"Event"
		}';
		if( $wrapper ){
			$xt_ldjson .= '</script>';
		}
		
		return $xt_ldjson;
	}

	/**
	 * Generate centralize Event array by Event ID for xylus theme's plugin
	 *
	 * @since 1.0.1
	 */
	public function es_centralize_array_by_event_id( $event_id ){
		if( $event_id != '' && is_numeric( $event_id ) ){
			global $event_schema, $wpdb;
			$post_xt = get_post( $event_id );
			$start_date = get_post_meta( $event_id, 'start_ts', true );
			if( $start_date == '' ){
				return false;
			}
			$name = get_the_title( $event_id );
			$description = $post_xt->post_excerpt;
			if( empty( trim( $description ) ) ){
				$description = wp_strip_all_tags( $post_xt->post_content );
			}
			
			$event_url   = get_permalink( $event_id );
			$image_url = "";
			if( has_post_thumbnail( $event_id ) ){
				$image_url = get_the_post_thumbnail_url( $event_id , 'full' );
			}
			$is_all_day = get_post_meta( $event_id, '_event_all_day', true );
			$start_date = get_post_meta( $event_id, 'start_ts', true );
			$end_date = get_post_meta( $event_id, 'end_ts', true );
			
			if ( $start_date != '' ) {
				$start_date = gmdate( DATE_ATOM, $start_date );
				if ( $is_all_day ) {
					$start_date = gmdate( 'Y-m-d', $start_date ) . ' 00:00';
				}
			}
			
			if ( $end_date != '' ) {
				$end_date = gmdate( DATE_ATOM, $end_date );
				if ( $is_all_day ) {
					$end_date = gmdate( 'Y-m-d', $end_date ) . ' 23:59';
				}
			}

			$centralize_event = array(
				"ID"         => $event_id,
				"name"       => $name,
				"description"=> $description,
				"url"        => $event_url,
				"start_date" => $start_date,
				"end_date"   => $end_date,
				"is_all_day" => $is_all_day,
				"image"      => $image_url,
			);

			$venue_name    = get_post_meta( $event_id, 'venue_name', true );
			$venue_address = get_post_meta( $event_id, 'venue_address', true );
			$venue_city    = get_post_meta( $event_id, 'venue_city', true );
			$venue_state   = get_post_meta( $event_id, 'venue_state', true );
			$venue_country = get_post_meta( $event_id, 'venue_country', true );
			$venue_zipcode = get_post_meta( $event_id, 'venue_zipcode', true );
			$venue_lat     = get_post_meta( $event_id, 'venue_lat', true );
			$venue_lon     = get_post_meta( $event_id, 'venue_lon', true );
			$venue_url     = get_post_meta( $event_id, 'venue_url', true );

			if( $venue_name != '' ){
				$centralize_event['location'] = array(
					"name" => $venue_name,
					"telephone" => '',
					"url" => $venue_url,
				);

				// Add address.
				$address = array();					
				$address['street_address']   = $venue_address;
				$address['address_locality'] = $venue_city;
				$address['address_region']   = $venue_state;
				$address['address_country']  = $venue_country;
				$address['postal_code']      = $venue_zipcode;
				$centralize_event['location']['address'] = $address;

				if( $venue_lat != '' && $venue_lon != '' ){
					$centralize_event['location']['geo'] = array(
							"latitude"  => $venue_lat,
							"longitude" => $venue_lon,
						);
				}	
			}			
			
			// Add Organizer.
			$organizer_name  = get_post_meta( $event_id, 'organizer_name', true );
			$organizer_email = get_post_meta( $event_id, 'organizer_email', true );
			$organizer_phone = get_post_meta( $event_id, 'organizer_phone', true );
			$organizer_url   = get_post_meta( $event_id, 'organizer_url', true );

			$organizer = array();
			$organizer['name'] = $organizer_name;
			$organizer['email'] = $organizer_email;
			$organizer['telephone'] = $organizer_phone;
			$organizer['url'] = $organizer_url;
			$centralize_event['organizer'] = $organizer;

			return $centralize_event;
		}
		return false;
	}

	/**
     * Get Plugin array
     *
     * @since 1.1.0
     * @return array
     */
    public function es_get_xyuls_themes_plugins(){
        return array(
            'wp-bulk-delete' => array( 'plugin_name' => esc_html__( 'WP Bulk Delete', 'event-schema' ), 'description' => 'Delete posts, pages, comments, users, taxonomy terms and meta fields in bulk with different powerful filters and conditions.' ),
            'wp-event-aggregator' => array( 'plugin_name' => esc_html__( 'WP Event Aggregator', 'event-schema' ), 'description' => 'WP Event Aggregator: Easy way to import Facebook Events, Eventbrite events, MeetUp events into your WordPress Event Calendar.' ),
            'import-facebook-events' => array( 'plugin_name' => esc_html__( 'Import Social Events', 'event-schema' ), 'description' => 'Import Facebook events into your WordPress website and/or Event Calendar. Nice Display with shortcode & Event widget.' ),
            'import-eventbrite-events' => array( 'plugin_name' => esc_html__( 'Import Eventbrite Events', 'event-schema' ), 'description' => 'Import Eventbrite Events into WordPress website and/or Event Calendar. Nice Display with shortcode & Event widget.' ),
            'import-meetup-events' => array( 'plugin_name' => esc_html__( 'Import Meetup Events', 'event-schema' ), 'description' => 'Import Meetup Events allows you to import Meetup (meetup.com) events into your WordPress site effortlessly.' ),
            'wp-smart-import' => array( 'plugin_name' => esc_html__( 'WP Smart Import : Import any XML File to WordPress', 'event-schema' ), 'description' => 'The most powerful solution for importing any CSV files to WordPress. Create Posts and Pages any Custom Posttype with content from any CSV file.' ),
        );
    }

	/**
     * Render Page header Section
     *
     * @since 1.1
     * @return void
     */
    public function wpec_render_common_header( $page_title  ){
        ?>
        <div class="ec-header" >
            <div class="ec-container" >
                <div class="ec-header-content" >
                    <span style="font-size:18px;"><?php esc_html_e('Dashboard','xt-feed-for-linkedin'); ?></span>
                    <span class="spacer"></span>
                    <span class="page-name"><?php echo esc_attr( $page_title ); ?></span></span>
                    <div class="header-actions" >
                        <span class="round" title="Documentation" >
                            <a href="<?php echo esc_url( 'https://docs.xylusthemes.com/docs/event-schema/' ); ?>" target="_blank">
                                <svg viewBox="0 0 20 20" fill="#000000" height="20px" xmlns="http://www.w3.org/2000/svg" class="ec-circle-question-mark">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path>
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    }

    /**
     * Render Page Footer Section
     *
     * @since 1.1
     * @return void
     */
    public function wpec_render_common_footer(){
        ?>
            <div id="ec-footer-links" >
                <div class="ec-footer">
                    <div><?php esc_attr_e( 'Made with ♥ by the Xylus Themes','xt-feed-for-linkedin'); ?></div>
                    <div class="ec-links" >
                        <a href="<?php echo esc_url( 'https://xylusthemes.com/support/' ); ?>" target="_blank" ><?php esc_attr_e( 'Support','xt-feed-for-linkedin'); ?></a>
                        <span>/</span>
                        <a href="<?php echo esc_url( 'https://docs.xylusthemes.com/docs/event-schema/' ); ?>" target="_blank" ><?php esc_attr_e( 'Docs','xt-feed-for-linkedin'); ?></a>
                        <span>/</span>
                        <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=xylus&tab=search&type=term' ) ); ?>" ><?php esc_attr_e( 'Free Plugins','xt-feed-for-linkedin'); ?></a>
                    </div>
                    <div class="ec-social-links">
                        <a href="<?php echo esc_url( 'https://www.facebook.com/xylusinfo/' ); ?>" target="_blank" >
                            <svg class="ec-facebook">
                                <path fill="currentColor" d="M16 8.05A8.02 8.02 0 0 0 8 0C3.58 0 0 3.6 0 8.05A8 8 0 0 0 6.74 16v-5.61H4.71V8.05h2.03V6.3c0-2.02 1.2-3.15 3-3.15.9 0 1.8.16 1.8.16v1.98h-1c-1 0-1.31.62-1.31 1.27v1.49h2.22l-.35 2.34H9.23V16A8.02 8.02 0 0 0 16 8.05Z"></path>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://www.linkedin.com/company/xylus-consultancy-service-xcs-/' ); ?>" target="_blank" >
                            <svg class="ec-linkedin">
                                <path fill="currentColor" d="M14 1H1.97C1.44 1 1 1.47 1 2.03V14c0 .56.44 1 .97 1H14a1 1 0 0 0 1-1V2.03C15 1.47 14.53 1 14 1ZM5.22 13H3.16V6.34h2.06V13ZM4.19 5.4a1.2 1.2 0 0 1-1.22-1.18C2.97 3.56 3.5 3 4.19 3c.65 0 1.18.56 1.18 1.22 0 .66-.53 1.19-1.18 1.19ZM13 13h-2.1V9.75C10.9 9 10.9 8 9.85 8c-1.1 0-1.25.84-1.25 1.72V13H6.53V6.34H8.5v.91h.03a2.2 2.2 0 0 1 1.97-1.1c2.1 0 2.5 1.41 2.5 3.2V13Z"></path>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://x.com/XylusThemes" target="_blank' ); ?>" target="_blank" >
                            <svg class="ec-twitter" width="24" height="24" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="12" fill="currentColor"></circle>
                                <g>
                                    <path d="M13.129 11.076L17.588 6H16.5315L12.658 10.4065L9.5665 6H6L10.676 12.664L6 17.9865H7.0565L11.1445 13.332L14.41 17.9865H17.9765L13.129 11.076ZM11.6815 12.7225L11.207 12.0585L7.4375 6.78H9.0605L12.1035 11.0415L12.576 11.7055L16.531 17.2445H14.908L11.6815 12.7225Z" fill="white"></path>
                                </g>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://www.youtube.com/@xylussupport7784' ); ?>" target="_blank" >
                            <svg class="ec-youtube">
                                <path fill="currentColor" d="M16.63 3.9a2.12 2.12 0 0 0-1.5-1.52C13.8 2 8.53 2 8.53 2s-5.32 0-6.66.38c-.71.18-1.3.78-1.49 1.53C0 5.2 0 8.03 0 8.03s0 2.78.37 4.13c.19.75.78 1.3 1.5 1.5C3.2 14 8.51 14 8.51 14s5.28 0 6.62-.34c.71-.2 1.3-.75 1.49-1.5.37-1.35.37-4.13.37-4.13s0-2.81-.37-4.12Zm-9.85 6.66V5.5l4.4 2.53-4.4 2.53Z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php   
    }

}

/**
 * Check is pro active or not.
 *
 * @since  1.5.0
 * @return boolean
 */
function wpec_is_pro() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( 'event-schema-pro/event-schema-pro.php' ) ) {
		return true;
	}
	return false;
}