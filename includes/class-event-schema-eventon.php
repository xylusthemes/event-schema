<?php
/**
 * Class for EventON
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Event_Schema
 * @subpackage Event_Schema/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Event_Schema_EventON {

	// The Events Calendar Event Posttype
	protected $event_posttype;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->taxonomy = 'event_type';
		$this->event_posttype = 'ajde_events';
		$this->location_taxonomy = 'event_location';
		$this->organizer_taxonomy = 'event_organizer';
		$schema_options = get_option( ES_OPTIONS , array() );
		if( !in_array( 'eventon', $schema_options ) ){
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

	/**
	 * Render ld+json schema for Event.
	 *
	 * @since 1.0.0
	 */
	public function render_event_structured_data(){
		if( is_singular( $this->event_posttype ) ){
			global $event_schema, $wpdb, $post;
			$event_id = get_the_ID();
			$name = get_the_title();
			$description = $post->post_excerpt;
			if( empty( trim( $description ) ) ){
				$description = strip_tags( $post->post_content );
			}
			$event_url   = get_permalink();
			$image_url = "";
			if( has_post_thumbnail( $event_id ) ){
				$image_url = get_the_post_thumbnail_url( $event_id , 'full' );
			}
			$is_all_day = false;
			if( get_post_meta( $event_id, 'evcal_allday', true ) == 'yes'){
				$is_all_day = true;
			}
			$start = get_post_meta( $event_id, 'evcal_srow', true );
			$end   = get_post_meta( $event_id, 'evcal_erow', true );

			$start_date = date( DATE_ATOM, $start );
			$end_date = date( DATE_ATOM, $end );
			if( $is_all_day ){
				$start_date = date( 'Y-m-d', $start ).' 00:00';
				$end_date = date( 'Y-m-d', $end ).' 23:59';
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

			$centralize_event['location'] = array(
				"name" => get_post_meta( $event_id, 'evcal_location_name', true ),
				"telephone" => '',
				"url" => get_post_meta( $event_id, 'evcal_location_link', true ),
			);
			$address = array();					
			$address['street_address'] 	 =  get_post_meta( $event_id, 'evcal_location', true );
			$address['address_locality'] =  '';
			$address['address_region']   =  '';
			$address['address_country']  =  '';
			$address['postal_code']      =  '';
			$centralize_event['location']['address'] = $address;
			$event_lat = get_post_meta( $event_id, 'evcal_lat', true );
			$event_lon = get_post_meta( $event_id, 'evcal_lon', true );
			if( $event_lat != '' && $event_lon != '' && $event_lat != '0.000000' && $event_lon != '0.000000' ){
				$centralize_event['location']['geo'] = array(
						"latitude"  => $event_lat,
						"longitude" => $event_lon,
					);
			}

			$centralize_event['organizer'] = array(
				"name" 		  => get_post_meta( $event_id, 'evcal_organizer', true ),
				"description" => '',
				"email" 	  => get_post_meta( $event_id, 'evcal_org_contact', true ),
				"url" 		  => get_post_meta( $event_id, 'evcal_org_exlink', true ),
			);

			// Render it.
			echo $event_schema->common->generate_ldjson( $centralize_event );
		}		
	}
}
