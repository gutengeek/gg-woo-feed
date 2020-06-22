<?php
namespace GG_Woo_Feed\Common;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       http://gg_woo_feed.com
 * @since      1.0.0
 **/
class Support {

	public static function is_activated_yith_brands() {
		return is_plugin_active( 'yith-woocommerce-brands-add-on/init.php' );
	}

	public static function is_activated_perfect_brands() {
		return is_plugin_active( 'perfect-woocommerce-brands/main.php' );
	}

	public static function is_activated_premmerce_brands() {
		return is_plugin_active( 'premmerce-woocommerce-brands/premmerce-brands.php' );
	}

	public static function is_activated_woo_multi_currency() {
		return is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' );
	}

	public static function is_activated_currency_switcher() {
		return is_plugin_active( 'currency-switcher-woocommerce/currency-switcher-woocommerce.php' );
	}

	public static function is_activated_currency_switcher_pro() {
		return is_plugin_active( 'currency-switcher-woocommerce-pro/currency-switcher-woocommerce-pro.php' );
	}

	public static function is_activated_woo_price_based_on_countries() {
		return is_plugin_active( 'woocommerce-product-price-based-on-countries/woocommerce-product-price-based-on-countries.php' );
	}

	public static function is_activated_WOOCS() {
		global $WOOCS;

		return is_object( $WOOCS ) && isset( $WOOCS->current_currency );
	}

	public static function get_multicurrency_supports() {
		$supports = [
			'woo_multi_currency' => [
				'status'     => static::is_activated_woo_multi_currency(),
				'link'       => 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/',
				'currencies' => [],
			],
			'currency_switcher'  => [
				'status'     => static::is_activated_woo_multi_currency(),
				'link'       => 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/',
				'currencies' => [],
			],
		];

		return $supports;
	}
}
