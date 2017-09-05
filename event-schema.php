<?php
/**
 * Plugin Name:       Event Schema
 * Plugin URI:        http://xylusthemes.com/plugins/event-schema/
 * Description:       Event Schema allows you to import Facebook ( facebook.com ) events into your WordPress site.
 * Version:           1.0.0
 * Author:            Xylus Themes
 * Author URI:        http://xylusthemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       event-schema
 * Domain Path:       /languages
 *
 * @package     Event_Schema
 * @author      Dharmesh Patel <dspatel44@gmail.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Event_Schema' ) ):

/**
* Main Event Schema class
*/
class Event_Schema{
	
	/** Singleton *************************************************************/
	/**
	 * Event_Schema The one true Event_Schema.
	 */
	private static $instance;

    /**
     * Main Event Schema Instance.
     * 
     * Insure that only one instance of Event_Schema exists in memory at any one time.
     * Also prevents needing to define globals all over the place.
     *
     * @since 1.0.0
     * @static object $instance
     * @uses Event_Schema::setup_constants() Setup the constants needed.
     * @uses Event_Schema::includes() Include the required files.
     * @uses Event_Schema::laod_textdomain() load the language files.
     * @see run_event_schema()
     * @return object| Event Schema the one true Event Schema.
     */
	public static function instance() {
		if( ! isset( self::$instance ) && ! (self::$instance instanceof Event_Schema ) ) {
			self::$instance = new Event_Schema;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			self::$instance->common = new Event_Schema_Common();
			self::$instance->admin = new Event_Schema_Admin();
			self::$instance->em = new Event_Schema_EM();
			self::$instance->event_organizer = new Event_Schema_Event_Organizer();
			self::$instance->aioec = new Event_Schema_Aioec();
			self::$instance->eventon = new Event_Schema_EventON();
			self::$instance->ife = new Event_Schema_IFE();
			self::$instance->iee = new Event_Schema_IEE();
			self::$instance->wpea = new Event_Schema_WPEA();	
		}
		return self::$instance;	
	}

	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent Event_Schema from being loaded more than once.
	 *
	 * @since 1.0.0
	 * @see Event_Schema::instance()
	 * @see run_event_schema()
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent Event_Schema from being cloned.
	 *
	 * @since 1.0.0
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'event-schema' ), '1.0.0' ); }

	/**
	 * A dummy magic method to prevent Event_Schema from being unserialized.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'event-schema' ), '1.0.0' ); }


	/**
	 * Setup plugins constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if( ! defined( 'ES_VERSION' ) ){
			define( 'ES_VERSION', '1.0.0' );
		}

		// Plugin folder Path.
		if( ! defined( 'ES_PLUGIN_DIR' ) ){
			define( 'ES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URL.
		if( ! defined( 'ES_PLUGIN_URL' ) ){
			define( 'ES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin root file.
		if( ! defined( 'ES_PLUGIN_FILE' ) ){
			define( 'ES_PLUGIN_FILE', __FILE__ );
		}

		// Options
		if( ! defined( 'ES_OPTIONS' ) ){
			define( 'ES_OPTIONS', 'event_schema_options' );
		}

		// Pro plugin Buy now Link.
		if( ! defined( 'ES_PLUGIN_BUY_NOW_URL' ) ){
			define( 'ES_PLUGIN_BUY_NOW_URL', 'http://xylusthemes.com/plugins/event-schema/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin' );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-common.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-admin.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-em.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-event_organizer.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-aioec.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-eventon.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-ife.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-iee.php';
		require_once ES_PLUGIN_DIR . 'includes/class-event-schema-wpea.php';
	}

	/**
	 * Loads the plugin language files.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain(){

		load_plugin_textdomain(
			'event-schema',
			false,
			ES_PLUGIN_DIR . '/languages/'
		);
	
	}
}

endif; // End If class exists check.

/**
 * The main function for that returns Event_Schema
 *
 * The main function responsible for returning the one true Event_Schema
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $event_schema = run_event_schema(); ?>
 *
 * @since 1.0.0
 * @return object|Event_Schema The one true Event_Schema Instance.
 */
function run_event_schema() {
	return Event_Schema::instance();
}

// Get Event_Schema Running.
global $event_schema, $es_errors, $es_success_msg, $es_warnings, $es_info_msg;
$event_schema = run_event_schema();
$es_errors = $es_warnings = $es_success_msg = $es_info_msg = array();
