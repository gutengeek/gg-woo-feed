<?php
namespace GG_Woo_Feed\Common\Module\System_Info\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Model helper.
 */
final class Model_Helper {

	/**
	 * Model helper constructor.
	 *
	 * Initializing the model helper class.
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Filter possible properties.
	 *
	 * Retrieve possible properties filtered by property intersect key.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $possible_properties All the possible properties.
	 * @param array $properties          Properties to filter.
	 *
	 * @return array Possible properties filtered by property intersect key.
	 */
	public static function filter_possible_properties( $possible_properties, $properties ) {
		$properties_keys = array_flip( $possible_properties );

		return array_intersect_key( $properties, $properties_keys );
	}

	/**
	 * Prepare properties.
	 *
	 * Combine the possible properties with the user properties and filter them.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $possible_properties All the possible properties.
	 * @param array $user_properties     User properties.
	 *
	 * @return array Possible properties and user properties filtered by property intersect key.
	 */
	public static function prepare_properties( $possible_properties, $user_properties ) {
		$properties = array_fill_keys( $possible_properties, null );

		$properties = array_merge( $properties, $user_properties );

		return self::filter_possible_properties( $possible_properties, $properties );
	}
}
