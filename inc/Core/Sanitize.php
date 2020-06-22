<?php
/**
 * Define
 * Note: only use for internal purpose.
 *
 * @package     GG_Woo_Feed
 */
namespace GG_Woo_Feed\Core;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sanitize data.
 *
 * @package    GG_Woo_Feed
 * @subpackage GG_Woo_Feed/views
 */
class Sanitize {

	/**
	 * The data to be sanitized
	 *
	 * @access    private
	 * @var    string
	 */
	private $data = '';

	/**
	 * The type of data
	 *
	 * @access    private
	 * @var    string
	 */
	private $type = '';

	/**
	 * Constructor
	 */
	public function __construct() {

		// Nothing to see here...

	} // __construct()

	/**
	 * Cleans the data
	 *
	 * @access    public
	 * @return  mixed         The sanitized data
	 * @uses      sanitize_email()
	 * @uses      sanitize_phone()
	 * @uses      esc_textarea()
	 * @uses      sanitize_text_field()
	 * @uses      esc_url()
	 */
	public function clean() {

		$sanitized = '';

		/**
		 * Add additional santization before the default sanitization
		 */
		do_action( 'gg_woo_feed_pre_sanitize', $sanitized );

		switch ( $this->type ) {

			case 'colorpicker' :
			case 'radio' :
			case 'select' :
				$sanitized = ! is_array( $this->data ) ? sanitize_text_field( $this->data ) : $this->data;
				break;
			case 'taxonomy_select' :
			case 'taxonomy_multicheck' :
				$sanitized = ! is_array( $this->data ) ? sanitize_text_field( $this->data ) : $this->data;
				break;
			case 'date' :
			case 'datetime' :
			case 'datetime-local' :
			case 'time' :
			case 'week' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'range' :
				$sanitized = intval( $this->data );
				break;
			case 'hidden' :
			case 'month' :
			case 'text' :
			case 'text_small' :
			case 'text_medium' :
			case 'password' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'text_url' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'text_number' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'text_email' :
				$sanitized = sanitize_email( $this->data );
				break;
			case 'text_tel' :
				$sanitized = $this->sanitize_phone( $this->data );
				break;
			case 'checkbox' :
			case 'switch' :
				$sanitized = in_array( $this->data, ['on', 'off'] ) ? sanitize_text_field( $this->data ) : 'off';
				break;
			case 'editor' :
				$sanitized = wp_kses_post( $this->data );
				break;
			case 'textarea' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'wysiwyg' :
				$sanitized = wp_kses_post( $this->data );
				break;
			case 'id' :
				$sanitized = absint( $this->data );
				break;
			case 'file' :
				$sanitized = sanitize_text_field( $this->data );
				break;
			case 'file_list' :
				$sanitized = gg_woo_feed_clean( $this->data );
				break;
			case 'map' :
				$sanitized = gg_woo_feed_clean( $this->data );
				break;

			default:
				$sanitized = gg_woo_feed_clean( $this->data );
		} // switch

		/**
		 * Add additional santization after the default .
		 */
		do_action( 'gg_woo_feed_post_sanitize', $sanitized );

		return $sanitized;

	} // clean()

	/**
	 * Checks a date against a format to ensure its validity
	 *
	 * @link    http://www.php.net/manual/en/function.checkdate.php
	 *
	 * @param string $date   The date as collected from the form field
	 * @param string $format The format to check the date against
	 * @return    string        A validated, formatted date
	 */
	private function validate_date( $date, $format = 'Y-m-d H:i:s' ) {

		$version = explode( '.', phpversion() );

		if ( ( (int) $version[0] >= 5 && (int) $version[1] >= 2 && (int) $version[2] > 17 ) ) {

			$d = \DateTime::createFromFormat( $format, $date );

		} else {

			$d = new \DateTime( date( $format, strtotime( $date ) ) );

		}

		return $d && $d->format( $format ) == $date;

	} // validate_date()

	/**
	 * Validates a phone number
	 *
	 * @access    private
	 * @param string $phone A phone number string
	 * @return    string|bool        $phone|FALSE        Returns the valid phone number, FALSE if not
	 * @since     0.1
	 * @link      http://jrtashjian.com/2009/03/code-snippet-validate-a-phone-number/
	 */
	private function sanitize_phone( $phone ) {

		if ( empty( $phone ) ) {
			return false;
		}

		if ( preg_match( '/^[+]?([0-9]?)[(|s|-|.]?([0-9]{3})[)|s|-|.]*([0-9]{3})[s|-|.]*([0-9]{4})$/', $phone ) ) {

			return trim( $phone );

		} // $phone validation

		return false;

	} // sanitize_phone()

	/**
	 * Performs general cleaning functions on data
	 *
	 * @param mixed $input Data to be cleaned
	 * @return    mixed    $return    The cleaned data
	 */
	private function sanitize_random( $input ) {

		$one    = trim( $input );
		$two    = stripslashes( $one );
		$return = htmlspecialchars( $two );

		return $return;

	} // sanitize_random()

	/**
	 * Sets the data class variable
	 *
	 * @param mixed $data The data to sanitize
	 */
	public function set_data( $data ) {

		$this->data = $data;

	} // set_data()

	/**
	 * Sets the type class variable
	 *
	 * @param string $type The field type for this data
	 */
	public function set_type( $type ) {

		$check = '';

		if ( empty( $type ) ) {

			$check = new \WP_Error( 'forgot_type', esc_html__( 'Specify the data type to sanitize.', 'now-hiring' ) );

		}

		if ( is_wp_error( $check ) ) {
			wp_die( $check->get_error_message(), esc_html__( 'Forgot data type', 'now-hiring' ) );
		}

		$this->type = $type;

	} // set_type()

}
