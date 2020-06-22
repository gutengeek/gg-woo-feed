<?php
namespace GG_Woo_Feed\Admin\Setting;

use GG_Woo_Feed\Core as Core;

class Google_Ads extends Core\Metabox {

	/**
	 * Get tab
	 */
	public function get_tab() {
		return [ 'id' => 'google_ads', 'heading' => esc_html__( 'Google Ads Tools' ) ];
	}

	/**
	 * Get settings.
	 */
	public function get_settings() {
		$settings = array_merge(
			$this->get_conversion_settings(),
			$this->get_remaketing_settings(),
			$this->get_advanced_settings()
		);

		return $settings;
	}

	/**
	 * @return array
	 */
	protected function get_conversion_settings() {
		$prefix = 'ct_';
		$fields = [
			[
				'id'          => $prefix . 'conversion_id',
				'name'        => esc_html__( 'Conversion ID', 'gg-woo-feed' ),
				'type'        => 'text',
				'description' => esc_html__( 'Enter a conversion ID.', 'gg-woo-feed' ),
			],
			[
				'id'          => $prefix . 'conversion_label',
				'name'        => esc_html__( 'Conversion Label', 'gg-woo-feed' ),
				'type'        => 'text',
				'description' => esc_html__( 'Enter a conversion label.', 'gg-woo-feed' ),
			],
			[
				'id'      => $prefix . 'order_total_logic',
				'name'    => esc_html__( 'Order Total Logic', 'gg-woo-feed' ),
				'type'    => 'radio',
				'options' => [
					'order_subtotal' => esc_html__( 'Use order_subtotal: Doesn\'t include tax and shipping (default)', 'gg-woo-feed' ),
					'order_total'    => esc_html__( 'Use order_total: Includes tax and shipping', 'gg-woo-feed' ),
				],
				'default' => 'order_subtotal',
			],
		];

		$settings['form_conversion_options'] = apply_filters( 'gg_woo_feed_form_conversion_options', [
			'id'        => 'form_conversion_options',
			'title'     => esc_html__( 'Conversion Tracking', 'gg-woo-feed' ),
			'icon-html' => '<span class="gg_woo_feed-icon gg_woo_feed-icon-heart"></span>',
			'fields'    => apply_filters( 'gg_woo_feed_form_conversion_metabox_fields', $fields ),
		] );

		return $settings;
	}

	/**
	 * @return array
	 */
	protected function get_remaketing_settings() {
		$prefix = 'dr_';
		$fields = [
			[
				'id'          => $prefix . 'conversion_id',
				'name'        => esc_html__( 'Conversion ID', 'gg-woo-feed' ),
				'type'        => 'text',
				'description' => __( '<a href="https://support.google.com/adwords/answer/2476688" target="_blank">Get your remarketing tag code</a>', 'gg-woo-feed' ),
			],
			[
				'id'          => $prefix . 'mc_prefix',
				'name'        => esc_html__( 'Google Merchant Center Prefix', 'gg-woo-feed' ),
				'type'        => 'text',
				'description' => __( 'If you use the WooCommerce Google Product Feed Plugin from WooThemes the value here should be "woocommerce_gpf_" (<a href="http://www.woothemes.com/products/google-product-feed/" target="_blank">WooCommerce Google Product Feed Plugin</a>). If you use any other plugin for the feed you can leave this field empty.',
					'gg-woo-feed' ),
			],
			[
				'id'      => $prefix . 'product_identifier',
				'name'    => esc_html__( 'Product Identifier', 'gg-woo-feed' ),
				'type'    => 'radio',
				'options' => [
					'post_id' => esc_html__( 'Post ID', 'gg-woo-feed' ),
					'sku'     => esc_html__( 'SKU', 'gg-woo-feed' ),
				],
				'default' => 'post_id',
			],
		];

		$settings['form_remaketing_options'] = apply_filters( 'gg_woo_feed_form_remaketing_options', [
				'id'        => 'form_remaketing_options',
				'title'     => esc_html__( 'Dynamic Remaketing', 'gg-woo-feed' ),
				'icon-html' => '<span class="gg_woo_feed-icon gg_woo_feed-icon-display"></span>',
				'fields'    => apply_filters( 'gg_woo_feed_form_remaketing_options_metabox_fields', $fields ),
			]
		);

		return $settings;
	}

	/**
	 * @return array
	 */
	protected function get_advanced_settings() {
		$prefix = 'gg_';
		$fields = [
			[
				'name'        => esc_html__( 'Ignore tracking for Admin or Shop manager', 'gg-woo-feed' ),
				'description' => esc_html__( 'Ignore tracking if an Admin or Shop manager is logged in.', 'gg-woo-feed' ),
				'id'          => $prefix . 'ignore_admin',
				'type'        => 'switch',
				'default'     => 'on',
			],
			[
				'name'        => esc_html__( 'Disable gtag.js', 'gg-woo-feed' ),
				'description' => esc_html__( 'Disable gtag.js insertion if another plugin is inserting it already.', 'gg-woo-feed' ),
				'id'          => $prefix . 'disable_gtag',
				'type'        => 'switch',
				'default'     => 'off',
			],
		];

		$settings['form_ads_advanced_options'] = apply_filters( 'gg_woo_feed_form_ads_advanced_options', [
				'id'        => 'form_ads_advanced_options',
				'title'     => esc_html__( 'Advanced', 'gg-woo-feed' ),
				'icon-html' => '<span class="gg_woo_feed-icon gg_woo_feed-icon-display"></span>',
				'fields'    => apply_filters( 'gg_woo_feed_form_ads_advanced_options_metabox_fields', $fields ),
			]
		);

		return $settings;
	}
}
