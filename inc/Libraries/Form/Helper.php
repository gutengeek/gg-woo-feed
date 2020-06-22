<?php
/**
 * Define
 * Note: only use for internal purpose.
 *
 * @package     GG_Woo_Feed
 * @since       1.0
 */
namespace GG_Woo_Feed\Libraries\Form;

use GG_Woo_Feed\Libraries\Form\Field\File;
use GG_Woo_Feed\Libraries\Form\Field\Map;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the dependencies and enqueueing of the Opaljob JS scripts
 *
 * @package   Opaljob
 * @author    Opal team
 */
class Helper {

	/**
	 * The Opaljob JS handle
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	protected static $handle = 'gg_woo_feed-form';

	/**
	 * The Opaljob JS variable name
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	protected static $js_variable = 'opalJob_l10';

	/**
	 * Array of Opaljob JS dependencies
	 *
	 * @var   array
	 * @since 1.0.0
	 */
	protected static $dependencies = [
		'jquery' => 'jquery',
	];

	/**
	 * Array of Opaljob fields model data for JS.
	 *
	 * @var   array
	 * @since 1.0.0
	 */
	protected static $fields = [];

	/**
	 * Add a dependency to the array of Opaljob JS dependencies
	 *
	 * @param array|string $dependencies Array (or string) of dependencies to add.
	 * @since 1.0.0
	 */
	public static function add_dependencies( $dependencies ) {
		foreach ( (array) $dependencies as $dependency ) {
			static::$dependencies[ $dependency ] = $dependency;
		}
	}

	/**
	 * Enqueue the form CSS
	 *
	 * @since  1.0.0
	 */
	public static function enqueue_styles() {
		// Iconpicker.
		wp_register_style( 'fonticonpicker', plugin_dir_url( __FILE__ ) . '/assets/3rd/font-iconpicker/css/jquery.fonticonpicker.min.css' );
		wp_register_style( 'fonticonpicker-grey-theme', plugin_dir_url( __FILE__ ) . '/assets/3rd/font-iconpicker/themes/grey-theme/jquery.fonticonpicker.grey.min.css' );

		wp_enqueue_style( 'fonticonpicker' );
		wp_enqueue_style( 'fonticonpicker-grey-theme' );
		wp_enqueue_style( 'font-awesome' );

		// Enqueue CSS.
		wp_enqueue_style( static::$handle, plugin_dir_url( __FILE__ ) . 'assets/css/form.css', [], GGWOOFEED_VERSION );
	}

	/**
	 * Enqueue the form JS
	 *
	 * @since  1.0.0
	 */
	public static function enqueue_scripts( $dependencies ) {
		// Only use minified files if SCRIPT_DEBUG is off.
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		// Iconpicker.
		wp_register_script( 'fonticonpicker', plugin_dir_url( __FILE__ ) . 'assets/3rd/font-iconpicker/jquery.fonticonpicker.min.js', [ 'jquery' ], '2.0.0', false );

		// Map.
		wp_register_script( 'gg_woo_feed-google-maps', Map::get_map_api_uri(), null, GGWOOFEED_VERSION, false );
		wp_register_script( 'gg_woo_feed-google-maps-js', plugin_dir_url( __FILE__ ) . 'assets/js/google.js', [], GGWOOFEED_VERSION );

		// Uploader.
		wp_register_script( 'gg_woo_feed-uploader-js', plugin_dir_url( __FILE__ ) . 'assets/js/uploader.js', [], GGWOOFEED_VERSION );

		// if colorpicker.
		if ( isset( $dependencies['wp-color-picker'] ) ) {
			if ( ! is_admin() ) {
				static::colorpicker_frontend();
			}
		}

		// if file/file_list.
		if ( isset( $dependencies['media-editor'] ) ) {
			wp_enqueue_script( 'media-editor' );
			wp_enqueue_media();
			static::load_template_script();
		}

		if ( isset( $dependencies['opal-map'] ) ) {
			wp_enqueue_script( 'gg_woo_feed-google-maps' );
			wp_enqueue_script( 'gg_woo_feed-google-maps-js' );
			unset( $dependencies['opal-map'] );
		}

		if ( isset( $dependencies['fonticonpicker'] ) ) {
			wp_enqueue_script( 'fonticonpicker' );
			unset( $dependencies['fonticonpicker'] );
		}

		if ( isset( $dependencies['gg_woo_feed-uploader-js'] ) ) {
			wp_enqueue_script( 'gg_woo_feed-uploader-js' );
			unset( $dependencies['gg_woo_feed-uploader-js'] );
		}

		// Enqueue JS.
		wp_enqueue_script( static::$handle, plugin_dir_url( __FILE__ ) . 'assets/js/form.js', $dependencies, GGWOOFEED_VERSION, true );

		static::localize( $debug );

		do_action( 'gg_woo_feed_footer_enqueue' );
	}

	/**
	 *  Load template script.
	 */
	public static function load_template_script () {
		File::output_js_underscore_templates();
	}

	/**
	 * Localize the php variables for Opaljob JS
	 *
	 * @param mixed $debug Whether or not we are debugging.
	 * @since  1.0.0
	 *
	 */
	protected static function localize( $debug ) {
		static $localized = false;
		if ( $localized ) {
			return;
		}

		$localized = true;
		$l10n      = [
			'script_debug'      => $debug,
			'up_arrow_class'    => 'dashicons dashicons-arrow-up-alt2',
			'down_arrow_class'  => 'dashicons dashicons-arrow-down-alt2',
			'user_can_richedit' => user_can_richedit(),
			'defaults'          => [
				'code_editor'  => false,
				'color_picker' => false,
				'date_picker'  => [
					'changeMonth'     => true,
					'changeYear'      => true,
					'dateFormat'      => _x( 'mm/dd/yy', 'Valid formatDate string for jquery-ui datepicker', 'gg-woo-feed' ),
					'dayNames'        => explode( ',', esc_html__( 'Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday', 'gg-woo-feed' ) ),
					'dayNamesMin'     => explode( ',', esc_html__( 'Su, Mo, Tu, We, Th, Fr, Sa', 'gg-woo-feed' ) ),
					'dayNamesShort'   => explode( ',', esc_html__( 'Sun, Mon, Tue, Wed, Thu, Fri, Sat', 'gg-woo-feed' ) ),
					'monthNames'      => explode( ',', esc_html__( 'January, February, March, April, May, June, July, August, September, October, November, December', 'gg-woo-feed' ) ),
					'monthNamesShort' => explode( ',', esc_html__( 'Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec', 'gg-woo-feed' ) ),
					'nextText'        => esc_html__( 'Next', 'gg-woo-feed' ),
					'prevText'        => esc_html__( 'Prev', 'gg-woo-feed' ),
					'currentText'     => esc_html__( 'Today', 'gg-woo-feed' ),
					'closeText'       => esc_html__( 'Done', 'gg-woo-feed' ),
					'clearText'       => esc_html__( 'Clear', 'gg-woo-feed' ),
				],
			],
			'strings'           => [
				'upload_file'  => esc_html__( 'Use this file', 'gg-woo-feed' ),
				'upload_files' => esc_html__( 'Use these files', 'gg-woo-feed' ),
				'remove_image' => esc_html__( 'Remove Image', 'gg-woo-feed' ),
				'remove_file'  => esc_html__( 'Remove', 'gg-woo-feed' ),
				'file'         => esc_html__( 'File:', 'gg-woo-feed' ),
				'download'     => esc_html__( 'Download', 'gg-woo-feed' ),
				'check_toggle' => esc_html__( 'Select / Deselect All', 'gg-woo-feed' ),
			],
		];

		if ( isset( static::$dependencies['code-editor'] ) && function_exists( 'wp_enqueue_code_editor' ) ) {
			$l10n['defaults']['code_editor'] = wp_enqueue_code_editor( [
				'type' => 'text/html',
			] );
		}

		wp_localize_script( static::$handle, static::$js_variable, apply_filters( 'gg_woo_feed_localized_data', $l10n ) );
	}

	/**
	 * We need to register colorpicker on the front-end
	 *
	 * @since  1.0.0
	 */
	public static function colorpicker_frontend() {
		wp_register_script( 'iris', admin_url( 'js/iris.min.js' ), [ 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ], GGWOOFEED_VERSION );
		wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), [ 'iris' ], GGWOOFEED_VERSION );
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', [
			'clear'         => esc_html__( 'Clear', 'gg-woo-feed' ),
			'defaultString' => esc_html__( 'Default', 'gg-woo-feed' ),
			'pick'          => esc_html__( 'Select Color', 'gg-woo-feed' ),
			'current'       => esc_html__( 'Current Color', 'gg-woo-feed' ),
		] );
	}


	public static function ajax_search_users() {
		$search_query = sanitize_text_field( $_GET['q'] );

		$get_users_args = [
			'number' => 9999,
			'search' => $search_query . '*',
		];

		$get_users_args = apply_filters( 'gg_woo_feed_search_users_args', $get_users_args );

		$found_users = apply_filters( 'gg_woo_feed_ajax_found_property_users', get_users( $get_users_args ), $search_query );

		$users = [];
		if ( ! empty( $found_users ) ) {
			foreach ( $found_users as $user ) {
				$object = gg_woo_feed_new_user_object( $user->ID );
				$users[] = [
					'id'          => $user->ID,
					'name'        => $object->get_name() . ' ('.$user->user_login.')',
					'avatar_url'  => $object->avatar,
					'full_name'   => $object->get_name() . ' ('.$user->user_login.')',
					'description' => 'okokok',
				];
			}
		}

		$output = [
			'total_count'        => count( $users ),
			'items'              => $users,
			'incomplete_results' => false,
		];
		echo json_encode( $output );

		die();
	}
}
