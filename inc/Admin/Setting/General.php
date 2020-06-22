<?php
namespace GG_Woo_Feed\Admin\Setting;

use Jetpack;
use Jetpack_Photon;
use GG_Woo_Feed\Common\Support;
use GG_Woo_Feed\Core as Core;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class General extends Core\Metabox {

	/**
	 * Register User Shortcodes
	 *
	 * Define and register list of user shortcodes such as register form, login form, dashboard shortcode
	 */
	public function get_tab() {
		return [ 'id' => 'general', 'heading' => esc_html__( 'General' ) ];
	}

	/**
	 * Register User Shortcodes
	 *
	 * Define and register list of user shortcodes such as register form, login form, dashboard shortcode
	 *
	 * @since    1.0.0
	 */
	public function get_settings() {
		$intervals = gg_woo_feed_get_schedule_interval_options();

		$fields = [
			[
				'id'          => 'schedule',
				'name'        => esc_html__( 'Refresh interval', 'gg-woo-feed' ),
				'type'        => 'select',
				'options'     => $intervals,
				'default'     => '0',
				'description' => esc_html__( 'Set a schedule to automatically refresh the feeds.', 'gg-woo-feed' ),
			],
			[
				'id'          => 'product_per_batch',
				'name'        => esc_html__( 'Product per batch', 'gg-woo-feed' ),
				'type'        => 'text_number',
				'default'     => '300',
				'description' => esc_html__( 'Split all products to batchs. This will reduce errors due to server overload.', 'gg-woo-feed' ),
			],
			// [
			// 	'name'        => esc_html__( 'Enable debug log', 'gg-woo-feed' ),
			// 	'description' => esc_html__( 'Enable debug log.', 'gg-woo-feed' ),
			// 	'id'          => 'enable_debug_log',
			// 	'type'        => 'switch',
			// 	'default'     => 'off',
			// ],
		];

		if ( class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) {
			$fields[] = [
				'name'        => esc_html__( 'Enable Jetpack CDN images', 'gg-woo-feed' ),
				'description' => esc_html__( 'Enable Jetpack CDN images url in feeds.', 'gg-woo-feed' ),
				'id'          => 'enable_jetpack_cdn_images',
				'type'        => 'switch',
				'default'     => 'on',
			];
		}

		$fields[] = [
			'id'          => 'google_site_verification',
			'name'        => esc_html__( 'Google Site Verification', 'gg-woo-feed' ),
			'type'        => 'text',
			'description' => esc_html__( 'Enter your Google Site Verification', 'gg-woo-feed' ),
		];

		return apply_filters( 'gg_woo_feed_settings_global', $fields );
	}
}
