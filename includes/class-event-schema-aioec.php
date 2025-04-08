<?php
/**
 * Class for Import Events into All in One Event Calendar
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Event_Schema
 * @subpackage Event_Schema/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Event_Schema_Aioec {

	// The Events Calendar Event Taxonomy
	protected $taxonomy;

	// The Events Calendar Event Posttype
	protected $event_posttype;

	// The Events Calendar Event Custom Table
	protected $event_db_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		global $wpdb;
		$this->event_posttype = 'ai1ec_event';
		$this->taxonomy = 'events_categories';
		$this->event_db_table = "{$wpdb->prefix}ai1ec_events";
		$schema_options = get_option( ES_OPTIONS , array() );
		if( !in_array( 'aioec', $schema_options ) ){
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
			$name = get_the_title();

			$instance_id = 0;
			if( isset( $_GET['instance_id'] ) && $_GET['instance_id'] > 0 ){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$instance_id = absint( $_GET['instance_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			$description = $post->post_excerpt;
			if( empty( trim( $description ) ) ){
				$description = wp_strip_all_tags( $post->post_content );
			}
			$event_url   = get_permalink();
			$image_url = "";
			if( has_post_thumbnail( $event_id ) ){
				$image_url = get_the_post_thumbnail_url( $event_id , 'full' );
			}
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$db_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->event_db_table} WHERE `post_id` = %d", $event_id ) );
			if( !empty( $db_event ) ){
				$is_all_day = isset( $db_event->allday ) ? $db_event->allday : 0;
				$start_date = isset( $db_event->start ) ? gmdate( DATE_ATOM, $db_event->start ) : '';
				$end_date   = isset( $db_event->end ) ? gmdate( DATE_ATOM, $db_event->end ) : '';

				if( $instance_id > 0 ){
					$instance_table = $wpdb->prefix.'ai1ec_event_instances';
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$instance_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $instance_table WHERE `id` = %d AND `post_id` = %d", absint( $instance_id ), absint( $event_id ) ) );
					if( !empty( $instance_event ) ){
						$start_date = isset( $instance_event->start ) ? gmdate( DATE_ATOM, $instance_event->start ) : '';
						$end_date   = isset( $instance_event->end ) ? gmdate( DATE_ATOM, $instance_event->end ) : '';
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

				$centralize_event['location'] = array(
					"name" 		=> isset( $db_event->venue ) ? $db_event->venue : '',
					"telephone" => '',
					"url" 		=> '',
				);

				$address = array();					
				$address['street_address']   =  isset( $db_event->address ) ? $db_event->address : '';
				$address['address_locality'] =  isset( $db_event->city ) ? $db_event->city : '';
				$address['address_region']   =  isset( $db_event->province ) ? $db_event->province : '';
				$address['address_country']  =  isset( $db_event->country ) ? $db_event->country : '';
				$address['postal_code']      =  isset( $db_event->postal_code ) ? $db_event->postal_code : '';
				$centralize_event['location']['address'] = $address;
				$event_lat = isset( $db_event->latitude ) ? $db_event->latitude : '';
				$event_lon = isset( $db_event->longitude ) ? $db_event->longitude : '';
				if( $event_lat != '' && $event_lon != '' && $event_lat != '0.000000' && $event_lon != '0.000000' ){
					$centralize_event['location']['geo'] = array(
							"latitude"  => $event_lat,
							"longitude" => $event_lon,
						);
				}

				$centralize_event['organizer'] = array(
					"name" 		  => isset( $db_event->contact_name ) ? $db_event->contact_name : '',
					"description" => '',
					"telephone"   => isset( $db_event->contact_phone ) ? $db_event->contact_phone : '',
					"email" 	  => isset( $db_event->contact_email ) ? $db_event->contact_email : '',
					"url" 		  => isset( $db_event->contact_url ) ? $db_event->contact_url : ''
				);

				// Render it.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $event_schema->common->generate_ldjson( $centralize_event );

			}
		}		
	}
}
