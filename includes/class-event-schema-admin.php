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
		add_filter( 'submenu_file', array( $this, 'get_selected_tab_submenu_wpec' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_notices', array( $this,'ec_display_all_notices' ), 1 );
		add_action( 'ec_display_all_notice', array( $this, 'ec_display_notices' ) );
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

		add_menu_page( __( 'Event Schema', 'event-schema' ), __( 'Event Schema', 'event-schema' ), 'manage_options', 'event_schema', array( $this, 'admin_page' ), 'dashicons-analytics', '30' );
		global $submenu;
		$submenu['event_schema'][] = array( __( 'Settings', 'event-schema' ), 'manage_options', admin_url( 'admin.php?page=event_schema&tab=settings' ) );
		$submenu['event_schema'][] = array( __( 'Support', 'event-schema' ), 'manage_options', admin_url( 'admin.php?page=event_schema&tab=support' ) );
		if( !wpec_is_pro() ){
        	$submenu['event_schema'][] = array( '<li class="wpec_upgrade_pro current">' . __( 'Upgrade to Pro', 'event-schema' ) . '</li>', 'manage_options', esc_url( "https://xylusthemes.com/plugins/event-schema/") );
		}
	}

	/**
	 * Tab Submenu got selected.
	 *
	 * @since 1.6.7
	 * @return void
	 */
	public function get_selected_tab_submenu_wpec( $submenu_file ){
		if( !empty( $_GET['page'] ) && esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) == 'event_schema' ){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$allowed_tabs = array( 'settings', 'support' );
			$tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'settings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if( in_array( $tab, $allowed_tabs ) ){
				$submenu_file = admin_url( 'admin.php?page=event_schema&tab='.$tab );
			}
		}
		return $submenu_file;
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

		$page    = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : '';
		$css_dir = ES_PLUGIN_URL . 'assets/css/';
		if( 'event_schema' == $page ){
	 		wp_enqueue_style( 'event-schema', $css_dir . 'event-schema-admin.css', array(), ES_VERSION );
		}
	}

	/**
	 * Load Admin page.
	 *
	 * @since 1.0
	 * @return void
	 */
	function admin_page() {
		global $event_schema;
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page_title = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( ucwords( $_GET['tab'] ) ) ) ) : 'Settings';
        $active_tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'settings';
        $gettab     = ucwords( str_replace( '_', ' ', $active_tab ) );
        if( $active_tab == 'settings' || $active_tab == 'support' ){
            $gettab     = ucwords( str_replace( '_', ' ', $gettab ) );
            $page_title = $gettab;
        }
        
        $posts_header_result = $event_schema->common->wpec_render_common_header( $page_title );
        ?>
        
        <div class="ec-container" >
            <div class="ec-wrap" >
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <?php
                            do_action( 'ec_display_all_notice' ); 
                        ?>
                        <div class="ajax_wpec_notice"></div>
                        <div id="postbox-container-2" class="postbox-container">
                            <div class="ec-app">
                                <div class="ec-tabs">
                                    <div class="tabs-scroller">
                                        <div class="var-tabs var-tabs--item-horizontal var-tabs--layout-horizontal-padding">
											<div class="var-tabs__tab-wrap var-tabs--layout-horizontal">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=event_schema&tab=settings' ) ); ?>" class="var-tab <?php echo $active_tab == 'settings' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
													<span class="tab-label"><?php esc_attr_e( 'Settings', 'xt-feed-for-linkedin' ); ?></span>
												</a>
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=event_schema&tab=support' ) ); ?>" class="var-tab <?php echo $active_tab == 'support' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
													<span class="tab-label"><?php esc_attr_e( 'Support & Help', 'xt-feed-for-linkedin' ); ?></span>
												</a>
											</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <?php
						
                            if( $active_tab == 'settings' ){
                                require_once ES_PLUGIN_DIR . '/templates/event-schema-settings.php';
                            }elseif( $active_tab == 'support' ){
                                require_once ES_PLUGIN_DIR . '/templates/event-schema-support.php';
                            }
                            ?>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
        </div>
        <?php
        $posts_footer_result = $event_schema->common->wpec_render_common_footer();
		?>
		<?php
	}

	/**
	 * Remove All Notices
	 */
	public function ec_display_all_notices() {
		// Remove default notices display.
		remove_action( 'admin_notices', 'wp_admin_notices' );
		remove_action( 'all_admin_notices', 'wp_admin_notices' );
	}

	/**
	 * Display notices in admin.
	 *
	 * @since    1.0.0
	 */
	public function ec_display_notices() {
		global $es_errors, $es_success_msg, $es_warnings, $es_info_msg;

		if ( ! empty( $es_errors ) ) {
			foreach ( $es_errors as $error ) :
			    ?>
			    <div class="notice notice-error is-dismissible ec-notice">
			        <p><?php echo esc_attr( $error ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_success_msg ) ) {
			foreach ( $es_success_msg as $success ) :
			    ?>
			    <div class="notice notice-success is-dismissible ec-notice">
			        <p><?php echo esc_attr( $success ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_warnings ) ) {
			foreach ( $es_warnings as $warning ) :
			    ?>
			    <div class="notice notice-warning is-dismissible ec-notice">
			        <p><?php echo esc_attr( $warning ); ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $es_info_msg ) ) {
			foreach ( $es_info_msg as $info ) :
			    ?>
			    <div class="notice notice-info is-dismissible ec-notice">
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
		if ( 'settings_page_event_schema' === $current_page ) {
			$rate_url = 'https://wordpress.org/support/plugin/event-schema/reviews/?rate=5#new-post';

			$footer_text .= sprintf(
				esc_html__( ' Rate %1$sEvent Schema%2$s %3$s', 'event-schema' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
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

			if ( ! is_wp_error( $plugin_data ) ) {
				
			} else {
				// If there was a bug on the Current Request just leave
				return false;
			}
			set_transient( $transient_name, $plugin_data, 24 * HOUR_IN_SECONDS );
		}
		return $plugin_data;
	}
}
