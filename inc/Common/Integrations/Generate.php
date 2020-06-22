<?php
namespace GG_Woo_Feed\Common\Integrations;

class Generate {
	/**
	 * Feed Provider class.
	 *
	 * @var object
	 */
	public $feed_provider;

	/**
	 * Constructor.
	 *
	 * @param string $provider
	 * @param array  $source
	 */
	public function __construct( $provider, $source ) {
		$provider_class      = $this->get_provider_class( $provider );
		$this->feed_provider = new $provider_class( $source );
	}

	/**
	 * Get Product data
	 *
	 * @return array|bool|string
	 */
	public function get_frame() {
		return $this->feed_provider->get_frame();
	}

	/**
	 * Get Feed Provider class.
	 *
	 * @param $provider
	 * @return string
	 */
	protected function get_provider_class( $provider ) {
		if ( 'google' === $provider ) {
			$class = 'GG_Woo_Feed\Common\Integrations\Provider\Feed_Google';
		} elseif ( 'pinterest' === $provider ) {
			$class = 'GG_Woo_Feed\Common\Integrations\Provider\Feed_Pinterest';
		} elseif ( 'facebook' === $provider ) {
			$class = 'GG_Woo_Feed\Common\Integrations\Provider\Feed_Facebook';
		} else {
			$class = 'GG_Woo_Feed\Common\Integrations\Provider\Feed_Custom';
		}

		return apply_filters( 'gg_woo_feed_parse_provider_class', $class, $provider );
	}
}
