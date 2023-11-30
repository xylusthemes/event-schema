<?php
/**
 * Class for Events Manager
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

class Event_Schema_EM {

    // The Events Calendar Event Taxonomy
    protected $taxonomy;

    // The Events Calendar Event Posttype
    protected $event_posttype;

    // The Events Calendar Venue Posttype
    protected $venue_posttype;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {

        if ( defined( 'EM_POST_TYPE_EVENT' ) ) {
            $this->event_posttype = EM_POST_TYPE_EVENT;
        } else {
            $this->event_posttype = 'event';
        }
        if ( defined( 'EM_TAXONOMY_CATEGORY' ) ) {
            $this->taxonomy = EM_TAXONOMY_CATEGORY;
        } else {
            $this->taxonomy = 'event-categories';
        }
        if ( defined( 'EM_POST_TYPE_LOCATION' ) ) {
            $this->venue_posttype = EM_POST_TYPE_LOCATION;
        } else {
            $this->venue_posttype = 'location';
        }
        $schema_options = get_option( ES_OPTIONS , array() );
        if( !in_array( 'event_organizer', $schema_options ) ){
            add_action( 'wp_footer', array( $this, 'render_event_structured_data' ) );

            // add filter for event-list
            add_filter( 'em_events_output', array( $this, 'render_event_list_structured_data' ), 10, 2 );
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
    public function get_venue_posttype(){
        return $this->venue_posttype;
    }
    public function get_taxonomy(){
        return $this->taxonomy;
    }


    /**
     * Render ld+json schema for Event.
     *
     * @param null $ao_event event object
     * @since 1.0.0
     */
    public function render_event_structured_data($ao_event = null){

        $lb_event_direct = is_object($ao_event);

        if( is_singular( $this->event_posttype ) || $lb_event_direct){
            global $event_schema, $wpdb, $post;
            $event_id = $lb_event_direct ? $ao_event->post_id : get_the_ID();
            $start_date = get_post_meta( $event_id, '_event_start_date', true );
            if( $start_date == '' ){
                return;
            }
            $name = $lb_event_direct ? get_the_title($ao_event->post_id) : get_the_title();
            $description = $lb_event_direct ? '' : $post->post_excerpt;
            if( trim( $description ) == '' ){
                $description = addslashes( preg_replace('/((\w+\W*){54}(\w+))(.*)/', '${1}', $lb_event_direct ? $ao_event->post_content : $post->post_content) );
            }
            $event_url   = $lb_event_direct ? get_permalink($ao_event->post_id) : get_permalink();
            $image_url = "";
            if( has_post_thumbnail( $event_id ) ){
                $image_url = get_the_post_thumbnail_url( $event_id , 'full' );
            }
            $is_all_day = get_post_meta( $event_id, '_event_all_day', true );
            $start_date = get_post_meta( $event_id, '_event_start_date', true );
            $end_date = get_post_meta( $event_id, '_event_end_date', true );
            $start_time = get_post_meta( $event_id, '_event_start_time', true );
            $end_time = get_post_meta( $event_id, '_event_end_time', true );
            if( $is_all_day ){
                $start_time = '00:00';
                $end_time = '23:59';
            }
            $start_datetime = $start_date.'T'.$start_time;
            $start_datetime = $end_date.'T'.$end_time;
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

            $is_location_enabled = get_option( 'dbem_locations_enabled', false );
            if( $is_location_enabled ){
                $location_id = get_post_meta( $event_id, '_location_id', true );
                $event_location = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix. "em_locations WHERE location_id = %d", $location_id ) );
                if( ! empty( $event_location ) ){
                    $centralize_event['location'] = array(
                        "name" => isset( $event_location->location_name ) ? esc_attr( $event_location->location_name ) : '',
                        "telephone" => '',
                        "url" => '',
                    );
                    $address = array();
                    $address['street_address'] =  isset( $event_location->location_address ) ? esc_attr( $event_location->location_address ) : '';
                    $address['address_locality'] =  isset( $event_location->location_town ) ? esc_attr( $event_location->location_town ) : '';
                    $address['address_region'] =  isset( $event_location->location_state ) ? esc_attr( $event_location->location_state ) : '';
                    $address['address_country'] =  isset( $event_location->location_country ) ? esc_attr( $event_location->location_country ) : '';
                    $address['postal_code'] =  isset( $event_location->location_postcode ) ? esc_attr( $event_location->location_postcode ) : '';
                    $centralize_event['location']['address'] = $address;
                    $event_lat = isset( $event_location->location_latitude ) ? esc_attr( $event_location->location_latitude ) : '';
                    $event_lon = isset( $event_location->location_longitude ) ? esc_attr( $event_location->location_longitude ) : '';
                    if( $event_lat != '' && $event_lon != '' ){
                        $centralize_event['location']['geo'] = array(
                            "latitude"  => $event_lat,
                            "longitude" => $event_lon,
                        );
                    }
                }
            }

            if( get_post_meta( $event_id, '_event_rsvp', true ) && get_option( 'dbem_rsvp_enabled', false ) ){
                $emevent_id = get_post_meta( $event_id, '_event_id', true );
                $event_tikets = $wpdb->get_row( $wpdb->prepare( "SELECT MAX(ticket_price) AS max_ticket, MIN(ticket_price) AS min_ticket FROM " . $wpdb->prefix. "em_tickets WHERE event_id = %d", $emevent_id ) );
                if( !empty( $event_tikets ) ){
                    $offers = array();
                    $offers['url'] = $event_url;
                    if( $event_tikets->max_ticket == $event_tikets->min_ticket ){
                        $offers['high_price'] = $event_tikets->max_ticket;
                        $offers['price'] = $event_tikets->max_ticket;
                    }else{
                        $offers['price'] = $event_tikets->min_ticket;
                        $offers['low_price'] = $event_tikets->min_ticket;
                        $offers['high_price'] = $event_tikets->max_ticket;
                    }
                    $offers['price_currency'] = get_option( 'dbem_bookings_currency' );
                    $centralize_event['offers'] = $offers;
                }
            }
            // Render it.
            echo $event_schema->common->generate_ldjson( $centralize_event );
        }
    }

    /**
     * Render ld+json schema for Event-List
     *
     * @param $output
     * @param $events
     * @return mixed
     */
    public function render_event_list_structured_data($output, $events){
        foreach($events as $event){
            $this->render_event_structured_data($event);
        }

        return $output;
    }

}
