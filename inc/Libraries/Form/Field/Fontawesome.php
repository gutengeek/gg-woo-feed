<?php
/**
 * Define
 * Note: only use for internal purpose.
 *
 * @package     GG_Woo_Feed
 * @since       1.0
 */
namespace GG_Woo_Feed\Libraries\Form\Field;

use GG_Woo_Feed\Libraries\Form\Form;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML Form
 *
 * A helper class for outputting common HTML elements, such as product drop downs
 *
 * @package     GG_Woo_Feed
 * @subpackage  GG_Woo_Feed\Libraries
 */
class Fontawesome {

	const BRANDS_URL = GGWOOFEED_URL . 'assets/3rd/font-awesome/webfonts/fa-brands-400.svg';
	const SOLID_URL = GGWOOFEED_URL . 'assets/3rd/font-awesome/webfonts/fa-solid-900.svg';
	const REGULAR_URL = GGWOOFEED_URL . 'assets/3rd/font-awesome/webfonts/fa-regular-400.svg';

	/**
	 * @var array
	 */
	private $icons = [];

	/**
	 * Fontawesome constructor.
	 */
	public function __construct() {
		$this->get_solid_icons();
		$this->get_regular_icons();
		$this->get_brands_icons();
	}

	/**
	 * Gets all icons.
	 *
	 * @return array
	 */
	public function get_icons() {
		return $this->icons;
	}

	/**
	 * Gets data.
	 *
	 * @param $path
	 * @return mixed
	 */
	public function get_data( $path ) {
		$svg = wp_remote_get( $path );
		$svg = wp_remote_retrieve_body( $svg );

		preg_match_all( '/glyph-name="(.*?)"/', $svg, $data, PREG_SET_ORDER );

		return $data;
	}

	/**
	 * Gets solid icons.
	 */
	public function get_solid_icons() {
		$data = $this->get_data( static::SOLID_URL );
		if ( $data ) {
			foreach ( $data as $match ) {
				$item           = [];
				$item['class']  = 'fa-' . $match[1];
				$item['prefix'] = 'fas';
				$this->icons[]  = $item;
			}
		}
	}

	/**
	 * Gets regular icons.
	 */
	public function get_regular_icons() {
		$data = $this->get_data( static::REGULAR_URL );
		if ( $data ) {
			foreach ( $data as $match ) {
				$item           = [];
				$item['class']  = 'fa-' . $match[1];
				$item['prefix'] = 'far';
				$this->icons[]  = $item;
			}
		}
	}

	/**
	 * Gets brands icons.
	 */
	public function get_brands_icons() {
		$data = $this->get_data( static::BRANDS_URL );

		if ( $data ) {
			foreach ( $data as $match ) {
				$item           = [];
				$item['class']  = 'fa-' . $match[1];
				$item['prefix'] = 'fab';
				$this->icons[]  = $item;
			}
		}
	}
}
