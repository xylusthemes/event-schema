<?php
/**
 * Class for Event Organizer
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Event_Schema
 * @subpackage Event_Schema/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Event_Schema_Event_Organizer {

	// The Events Calendar Event Taxonomy
	protected $taxonomy;

	// The Events Calendar Event Posttype
	protected $event_posttype;

	// The Events Calendar Venue Posttype
	protected $venue_taxonomy;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		global $wpdb;
		$this->event_posttype = 'event';
		$this->taxonomy = 'event-category';
		$this->venue_taxonomy = 'event-venue';
		$schema_options = get_option( ES_OPTIONS , array() );
		if( !in_array( 'event_organizer', $schema_options ) ){
			add_action( 'wp_footer', array( $this, 'render_event_structured_data' ) );
		}		
	}


	/**
	 * Get Posttype and Taxonomy Functions
	 *
	 * @return string
	 */
	public function get_event_posttype(){
		return $this->event_posttype;
	}	
	public function get_venue_taxonomy(){
		return $this->venue_taxonomy;
	}
	public function get_taxonomy(){
		return $this->taxonomy;
	}

	/**
	 * Render ld+json schema for Event.
	 *
	 * @since 1.0.0
	 */
	public function render_event_structured_data(){
		if( is_singular( $this->event_posttype ) ){
			global $event_schema, $wpdb, $post;
			$event_id = get_the_ID();
			$start_date = get_post_meta( $event_id, '_eventorganiser_schedule_start_start', true );
			if( $start_date == '' ){
				return;
			}
			$name = get_the_title();
			$description = $post->post_excerpt;
			if( trim( $description ) == '' ){
				$description = addslashes( preg_replace('/((\w+\W*){54}(\w+))(.*)/', '${1}', $post->post_content) );
			}
			$event_url   = get_permalink();
			$image_url = "";
			if( has_post_thumbnail( $event_id ) ){
				$image_url = get_the_post_thumbnail_url( $event_id , 'full' );
			}
			$is_all_day = false;
			$start_date = get_post_meta( $event_id, '_eventorganiser_schedule_start_start', true );
			$end_date = get_post_meta( $event_id, '_eventorganiser_schedule_start_finish', true );
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

			$venue = get_the_terms( $event_id, $this->venue_taxonomy );
			if( !empty( $venue ) ){
				$venue_id = $venue[0]->term_id;
				$venue_data = array();
				$venue_data['name'] = $venue[0]->name;
				$venue_info = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."eo_venuemeta WHERE eo_venue_id = %d" , $venue_id ) );
				if( !empty( $venue_info ) ){
					foreach ($venue_info as $venue_value ) {
						$venue_data[$venue_value->meta_key] = $venue_value->meta_value;
					}
				}

				$centralize_event['location'] = array(
					"name" => $venue_data['name'],
					"telephone" => '',
					"url" => '',
				);
				$address = array();					
				$address['street_address'] =  isset( $venue_data['_address'] ) ? esc_attr( $venue_data['_address'] ) : '';
				$address['address_locality'] =  isset( $venue_data['_city'] ) ? esc_attr( $venue_data['_city'] ) : '';
				$address['address_region'] =  isset( $venue_data['_state'] ) ? esc_attr( $venue_data['_state'] ) : '';
				$address['address_country'] =  isset( $venue_data['_country'] ) ? esc_attr( $venue_data['_country'] ) : '';
				$address['postal_code'] =  isset( $venue_data['_postcode'] ) ? esc_attr( $venue_data['_postcode'] ) : '';
				$centralize_event['location']['address'] = $address;
				$event_lat = isset( $venue_data['_lat'] ) ? esc_attr( $venue_data['_lat'] ) : '';
				$event_lon = isset( $venue_data['_lng'] ) ? esc_attr( $venue_data['_lng'] ) : '';
				if( $event_lat != '' && $event_lon != '' && $event_lat != '0.000000' && $event_lon != '0.000000' ){
					$centralize_event['location']['geo'] = array(
							"latitude"  => $event_lat,
							"longitude" => $event_lon,
						);
				}
			}
			// Render it.
			echo $event_schema->common->generate_ldjson( $centralize_event );
		}		
	}
}
