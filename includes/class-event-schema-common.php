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
	public function generate_ldjson( $event = array() ){

		if( empty($event ) ){
			return;
		}
		$xt_ldjson = '<script type="application/ld+json">
		{';
	
		if( isset( $event['name'] ) && $event['name'] != '' ){
			$xt_ldjson .= '"name":"' . esc_attr( $event["name"] ) . '",';
		}
		if( isset( $event['description'] ) && $event['description'] != '' ){
			$xt_ldjson .= '"description":"' . $event["description"] . '",';
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
		}
		</script>';
		return $xt_ldjson;
	}
}
