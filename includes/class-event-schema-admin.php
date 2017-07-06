<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Event_Schema
 * @subpackage  Event_Schema/admin
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Event_Schema
 * @subpackage  Event_Schema/admin
 * @author      Dharmesh Patel <dspatel44@gmail.com>
 */
class Event_Schema_Admin {

	/**
	 * Adminpage url.
	 *
	 * @var string $adminpage_url adminpage url for Event schema settings.
	 */
	public $adminpage_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->adminpage_url = admin_url( 'options-general.php?page=event_schema' );

		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_filter( 'admin_footer_text', array( $this, 'add_event_schema_credit' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget') );
	}

	/**
	 * Create the Admin menu and submenu and assign their links to global varibles.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_menu_pages() {
		add_options_page( __( 'Event Schema', 'event-schema' ), __( 'Event Schema', 'event-schema' ), 'manage_options', 'event_schema', array( $this, 'admin_page' ) );
	}

	/**
	 * Load Admin Styles.
	 *
	 * Enqueues the required admin styles.
	 *
	 * @since 1.0
	 * @param string $hook Page hook.
	 * @return void
	 */
	function enqueue_admin_styles( $hook ) {

	  	$css_dir = ES_PLUGIN_URL . 'assets/css/';
	 	wp_enqueue_style( 'event-schema', $css_dir . 'event-schema-admin.css', false, '' );
	}

	/**
	 * Load Admin page.
	 *
	 * @since 1.0
	 * @return void
	 */
	function admin_page() {
		?>
		<div class="wrap">
		    <h2><?php esc_html_e( 'Event Schema', 'event-schema' ); ?></h2>
		    <?php
		    // Set Default Tab to Import.
		    $tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';
		    ?>
		    <div id="poststuff">
		        <div id="post-body" class="metabox-holder columns-2">

		            <div id="postbox-container-1" class="postbox-container">
		            	<?php require_once ES_PLUGIN_DIR . '/templates/admin-sidebar.php'; ?>
		            </div>
		            <div id="postbox-container-2" class="postbox-container">

		                <h1 class="nav-tab-wrapper">

		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'settings', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'settings' === $tab ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Settings', 'event-schema' ); ?>
		                    </a>
		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'support' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Support & Help', 'event-schema' ); ?>
		                    </a>
		                </h1>

		                <div class="event-schema-page">

		                	<?php
		                	if ( 'settings' === $tab ) {

		                		require_once ES_PLUGIN_DIR . '/templates/event-schema-settings.php';

		                	}elseif ( $tab == 'support' ) {
		                		
		                		require_once ES_PLUGIN_DIR . '/templates/event-schema-support.php';

		                	}
			                ?>
		                	<div style="clear: both"></div>
		                </div>
		        </div>		        
		    </div>
		</div>
		<?php
	}

	/**
	 * Display notices in admin.
	 *
	 * @since    1.0.0
	 */
	public function display_notices() {
		global $es_errors, $es_success_msg, $es_warnings, $es_info_msg;

		if ( ! empty( $es_errors ) ) {
			foreach ( $es_errors as $error ) :
			    ?>
			    <div class="notice notice-error is-dismissible">
			        <p><?php echo esc_attr( $error ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_success_msg ) ) {
			foreach ( $es_success_msg as $success ) :
			    ?>
			    <div class="notice notice-success is-dismissible">
			        <p><?php echo esc_attr( $success ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_warnings ) ) {
			foreach ( $es_warnings as $warning ) :
			    ?>
			    <div class="notice notice-warning is-dismissible">
			        <p><?php echo esc_attr( $warning ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_info_msg ) ) {
			foreach ( $es_info_msg as $info ) :
			    ?>
			    <div class="notice notice-info is-dismissible">
			        <p><?php echo esc_attr( $info ); ?></p>
			    </div>
			    <?php
			endforeach;
		}
	}

	/**
	 * Register the dashboard widget.
	 *
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'wpea_dashboard_widget',
			esc_html__( 'News from Xylus Themes', 'event-schema' ),
			array($this, 'render_dashboard_widget' )
		);
	}

	/**
	 * Render the dashboard widget.
	 *
	 */
	function render_dashboard_widget( $posts = 10 ) {
		echo '<div class="wpea-dashboard-widget">';
		wp_widget_rss_output( 'https://xylusthemes.com/feed/', array( 'items' => $posts ) );
		echo '</div>';
	}

	/**
	 * Add Event Schema ratting text
	 *
	 * @since 1.0
	 * @param string $footer_text Footer Credit text in wordpress admin area.
	 * @return string
	 */
	public function add_event_schema_credit( $footer_text ) {
		$screen = get_current_screen();
		$current_page = isset( $screen->id ) ? $screen->id : '';
		if ( ' settings_page_event_schema' === $current_page ) {
			$rate_url = 'https://wordpress.org/support/plugin/event-schema/reviews/?rate=5#new-post';

			$footer_text .= sprintf(
				esc_html__( ' Rate %1$sEvent Schema%2$s %3$s', 'event-schema' ),
				'<strong>',
				'</strong>',
				'<a href="' . $rate_url . '" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}
		return $footer_text;
	}

	/**
	 * Get Plugin array
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_xylus_themes_plugins(){
		return array(
			'wp-bulk-delete' => esc_html__( 'WP Bulk Delete', 'event-schema' ),
			'wp-event-aggregator' => esc_html__( 'WP Event Aggregator', 'event-schema' ),
			'import-facebook-events' => esc_html__( 'Import Facebook Events', 'event-schema' ),
			'import-eventbrite-events' => esc_html__( 'Import Eventbrite Events', 'event-schema' ),
			'import-meetup-events' => esc_html__( 'Import Meetup Events', 'event-schema' ),
		);
	}

	/**
	 * Get Plugin Details.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_wporg_plugin( $slug ){

		if( $slug == '' ){
			return false;
		}

		$transient_name = 'support_plugin_box'.$slug;
		$plugin_data = get_transient( $transient_name );
		if( false === $plugin_data ){
			if ( ! function_exists( 'plugins_api' ) ) {
				include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
			}

			$plugin_data = plugins_api( 'plugin_information', array(
				'slug' => $slug,
				'is_ssl' => is_ssl(),
				'fields' => array(
					'banners' => true,
					'active_installs' => true,
				),
			) );

			if ( ! is_wp_error( $data ) ) {
				
			} else {
				// If there was a bug on the Current Request just leave
				return false;
			}
			set_transient( $transient_name, $plugin_data, 24 * HOUR_IN_SECONDS );
		}
		return $plugin_data;
	}
}
