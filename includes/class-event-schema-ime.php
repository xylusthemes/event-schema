<?php
/**
 * Class for Import Meetup Events
 *
 * @link       http://xylusthemes.com/
 * @since      1.1.0
 *
 * @package    Event_Schema
 * @subpackage Event_Schema/includes
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Event_Schema_IME {

	// "Import Meetup Events" Event Taxonomy
	protected $taxonomy;

	// "Import Meetup Events" Event Posttype
	protected $event_posttype;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->event_posttype = 'meetup_events';
		$this->taxonomy = 'meetup_category';
		
		$schema_options = get_option( ES_OPTIONS , array() );
		if( !in_array( 'import_meetup_events', $schema_options ) ){
			add_action( 'wp_footer', array( $this, 'render_event_structured_data' ) );
			add_action( 'ime_after_event_list', array( $this, 'render_event_list_structured_data' ) );
			add_action( 'ime_after_widget_event_list', array( $this, 'render_event_list_structured_data' ) );			
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
			
			$centralize_event = $event_schema->common->es_centralize_array_by_event_id ( $event_id ); 

			// Render it.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $event_schema->common->generate_ldjson( $centralize_event );
		}		
	}

	/**
	 * Render ld+json schema for Event.
	 *
	 * @since 1.0.0
	 */
	public function render_event_list_structured_data( $meetup_events ){
		global $event_schema;
		$event_schemas = array();
		if( !empty( $meetup_events->posts ) ){
			foreach ($meetup_events->posts as $meetup_event ) {
				$centralize_event = $event_schema->common->es_centralize_array_by_event_id ( $meetup_event->ID );
				if( !empty( $centralize_event ) ){
					$event_schemas[] = $event_schema->common->generate_ldjson( $centralize_event, false );
				}
			}
		}

		if( !empty($event_schemas) ){
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $event_schemas_str = '<script type="application/ld+json">['.implode(',', $event_schemas ).']</script>';
		}
	}

}
