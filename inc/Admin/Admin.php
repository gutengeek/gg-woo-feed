<?php
namespace GG_Woo_Feed\Admin;

use GG_Woo_Feed\Admin\Page\Google_Status;
use GG_Woo_Feed\Admin\Page\Google_Sync;
use GG_Woo_Feed\Admin\Setting as Setting;
use GG_Woo_Feed\Libraries as Libraries;
use GG_Woo_Feed\Admin\Page\System_Info;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name        The name of this plugin.
	 * @param string $version            The version of this plugin.
	 * @param string $plugin_text_domain The text domain of this plugin.
	 * @since       1.0.0
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->plugin_text_domain = $plugin_text_domain;
	}

	public $settings_objs = [];

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name . '_admin', GGWOOFEED_URL . 'assets/css/admin/admin.css', [], $this->version, 'all' );

		wp_enqueue_style( 'select2', GGWOOFEED_URL . 'assets/3rd/select2/css/select2.min.css', null, '4.0.7', false );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_screen = get_current_screen();

		wp_enqueue_script( 'select2', GGWOOFEED_URL . 'assets/3rd/select2/js/select2.min.js', null, '4.0.7', false );
		wp_enqueue_script( 'gg_woo_feed-admin', GGWOOFEED_URL . 'assets/js/admin.js', [], $this->version, false );
		wp_enqueue_script( 'clipboard', GGWOOFEED_URL . 'assets/3rd/clipboard.min.js', [], '2.0.4', false );

		if ( null !== $current_screen && ( ( false !== strpos( $current_screen->id, 'gg-woo-feed-add-feed' ) ) || 'toplevel_page_gg-woo-feed-feeds' === $current_screen->id ) ) {
			wp_enqueue_script( 'gg_woo_feed-feed', GGWOOFEED_URL . 'assets/js/feed.js', [ 'jquery-ui-sortable' ], $this->version, false );

			wp_localize_script(
				'gg_woo_feed-feed',
				'ggWooFeed',
				[
					'manage_feeds_link' => esc_url( admin_url( 'admin.php?page=gg-woo-feed-feeds' ) ),
					'nonce'             => wp_create_nonce( 'gg_woo_feed_nonce' ),
				]
			);
		}

		wp_localize_script(
			'gg_woo_feed-admin',
			'ggWooFeed',
			[
				'manage_feeds_link' => esc_url( admin_url( 'admin.php?page=gg-woo-feed-feeds' ) ),
				'nonce'             => wp_create_nonce( 'gg_woo_feed_nonce' ),
				'ajax'              => [
					'nonce' => wp_create_nonce( 'gg_woo_feed_nonce' ),
				],
			]
		);

		do_action( 'gg_woo_feed_admin_enqueue_scripts', $this );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(
			apply_filters( $this->plugin_name . '-dashboard-page-title', esc_html__( 'GG Woo Feed', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-dashboard-menu-title', esc_html__( 'GG Woo Feed', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-feeds',
			[ $this, 'render_manage_feeds' ],
			'dashicons-rss'
		);

		add_submenu_page(
			$this->plugin_name . '-feeds',
			apply_filters( $this->plugin_name . '-feeds-page-title', esc_html__( 'Manage Feeds', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-feeds-menu-title', esc_html__( 'Manage Feeds', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-feeds',
			[ $this, 'render_manage_feeds' ]
		);

		add_submenu_page(
			$this->plugin_name . '-feeds',
			apply_filters( $this->plugin_name . '-add-feed-page-title', esc_html__( 'Add New Feed', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-add-feed-menu-title', esc_html__( 'Add New Feed', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-add-feed',
			[ $this, 'render_add_new_feed' ]
		);

		add_submenu_page(
			$this->plugin_name . '-feeds',
			apply_filters( $this->plugin_name . '-settings-page-title', esc_html__( 'Settings', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-settings-menu-title', esc_html__( 'Settings', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-settings',
			[ $this, 'page_options' ]
		);

		new Google_Sync( $this->plugin_name, $this->version, $this->plugin_text_domain );
		new Google_Status( $this->plugin_name, $this->version, $this->plugin_text_domain );
		new System_Info( $this->plugin_name, $this->version, $this->plugin_text_domain );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	private function load_object_settings() {
		if ( empty( $this->settings_objs ) ) {
			$matching = apply_filters( 'gg_woo_feed_load_settings', [
				'general'    => Setting\General::class,
				'google_ads' => Setting\Google_Ads::class,
			] );

			foreach ( $matching as $match => $class ) {
				$object                        = new $class();
				$this->settings_objs[ $match ] = $object;
			}
		}

		return $this->settings_objs;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function update_settings() {
		if ( isset( $_POST['save_page_options'] ) ) {
			// Verify nonce field
			gg_woo_feed_verify_nonce('gg_woo_feed_save_form_meta', 'gg_woo_feed_meta_nonce');

			$tab     = $this->get_tab_active();
			$objects = $this->load_object_settings();
			if ( isset( $objects[ $tab ] ) ) {

				$settings = $objects[ $tab ]->get_settings();
				$objects[ $tab ]->save_settings_options( $settings, 'gg_woo_feed_settings' );
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	private function get_tab_active() {
		$tab = 'general';
		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( $_GET['tab'] );
		}

		return $tab;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function page_options() {
		$matching   = $this->load_object_settings();
		$tab_active = $this->get_tab_active();

		echo '<div class="gg_woo_feed-settings-page">';
		echo '<div class="setting-tab-head"><ul class="inline-list">';
		foreach ( $matching as $match => $object ) {
			$tab = $object->get_tab();

			$tab_url = esc_url( add_query_arg( [
				'settings-updated' => false,
				'tab'              => $tab['id'],
				'subtab'           => false,
			] ) );

			$class = $tab['id'] == $tab_active ? ' class="active"' : "";

			echo '<li' . $class . '><a href="' . $tab_url . '" >' . $tab['heading'] . '</a></li>';
		}
		echo '</ul></div>';


		$form = Libraries\Form\Form::get_instance();

		$form->setup( 'page_options', 'gg_woo_feed_settings' );

		$args = [];

		echo '<form action="" method="post">';
		if ( isset( $matching[ $tab_active ] ) && isset( $this->settings_objs[ $tab_active ] ) ) {
			$object   = $this->settings_objs[ $tab_active ];
			$settings = $object->get_settings();
			echo $form->render( $args, $settings );
		}

		echo '<button class="gg_woo_feed-btn gg_woo_feed-btn-submit" name="save_page_options" value="savedata" type="submit">' . esc_html__( 'Save' ) . '</button>';
		echo '</form>';

		echo '</div>';
	}

	/**
	 * Render manage feeds and edit feed page.
	 */
	public function render_manage_feeds() {
		$is_edit   = isset( $_REQUEST['action'] ) && 'edit' === sanitize_text_field( $_REQUEST['action'] );
		$feed_name = isset( $_REQUEST['feed'] ) ? sanitize_text_field( $_REQUEST['feed'] ) : false;
		$nonce     = isset( $_REQUEST['nonce'] ) && ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		if ( $is_edit && $feed_name && $nonce ) {
			if ( ! wp_verify_nonce( $nonce, 'gg_woo_feed-nonce-edit-feed' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'gg-woo-feed' ), 403 );
				die();
			}

			$feed_info = maybe_unserialize( get_option( $feed_name ) );

			if ( false !== $feed_info ) {
				global $wpdb;
				$query  = $wpdb->prepare( "SELECT option_id FROM $wpdb->options WHERE option_name = %s LIMIT 1", $feed_name );
				$result = $wpdb->get_row( $query );
				if ( $result ) {
					$feed_id = $result->option_id;
				}

				$provider     = strtolower( $feed_info['feedqueries']['provider'] );
				$feed_queries = $feed_info['feedqueries'];

				require_once( GGWOOFEED_DIR . 'inc/Admin/view/edit-feed.php' );
			} else {
				wp_safe_redirect( admin_url( 'admin.php?page=gg-woo-feed-feeds' ) );
				die();
			}
		} else {
			require_once( GGWOOFEED_DIR . 'inc/Admin/view/manage-feeds.php' );
		}
	}

	/**
	 * Render Add new feed.
	 */
	public function render_add_new_feed() {
		require_once( GGWOOFEED_DIR . 'inc/Admin/view/add-feed.php' );
	}
}
