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

		$supported_plugins = array();
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		// check Events Manager.
		if( defined( 'EM_VERSION' ) ){
			$supported_plugins['em'] = __( 'Events Manager', 'event-schema' );
		}
		
		// Check event_organizer.
		if( defined( 'EVENT_ORGANISER_VER' ) &&  defined( 'EVENT_ORGANISER_DIR' ) ){
			$supported_plugins['event_organizer'] = __( 'Event Organiser', 'event-schema' );
		}

		// check EventON.
		if( class_exists( 'EventON' ) ){
			$supported_plugins['eventon'] = __( 'EventON', 'event-schema' );
		}

		// check All in one Event Calendar
		if( class_exists( 'Ai1ec_Event' ) ){
			$supported_plugins['aioec'] = __( 'All in one Event Calendar', 'event-schema' );
		}

		// check Import Facebook Events
		if( class_exists( 'Import_Facebook_Events' ) ){
			$supported_plugins['import_facebook_events'] = __( 'Import Facebook Events', 'event-schema' );
		}

		// check Import Eventbrite Events
		if( class_exists( 'Import_Eventbrite_Events' ) ){
			$supported_plugins['import_eventbrite_events'] = __( 'Import Eventbrite Events', 'event-schema' );
		}

		// check Import Meetup Events
		if( class_exists( 'Import_Meetup_Events' ) ){
			$supported_plugins['import_meetup_events'] = __( 'Import Meetup Events', 'event-schema' );
		}

		// check WP Event Aggregator
		if( class_exists( 'WP_Event_Aggregator' ) ){
			$supported_plugins['wp_event_aggregator'] = __( 'WP Event Aggregator', 'event-schema' );
		}

		// check My Calendar
		/*if ( is_plugin_active( 'my-calendar/my-calendar.php' ) ) {
			$supported_plugins['my_calendar'] = __( 'My Calendar', 'event-schema' );
		}*/		
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
				$description = strip_tags( $post_xt->post_content );
			}
			
			$event_url   = get_permalink( $event_id );
			$image_url = "";
			if( has_post_thumbnail( $event_id ) ){
				$image_url = get_the_post_thumbnail_url( $event_id , 'full' );
			}
			$is_all_day = get_post_meta( $event_id, '_event_all_day', true );
			$start_date = get_post_meta( $event_id, 'start_ts', true );
			$end_date = get_post_meta( $event_id, 'end_ts', true );
			
			if( $start_date != ''){
				$start_date = date( DATE_ATOM, $start_date );
				if( $is_all_day ){
					$start_date = date( 'Y-m-d', $start_date ).' 00:00';
				}
			}
			if( $end_date != ''){
				$end_date = date( DATE_ATOM, $end_date );
				if( $is_all_day ){
					$end_date = date( 'Y-m-d', $end_date ).' 23:59';
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

}
