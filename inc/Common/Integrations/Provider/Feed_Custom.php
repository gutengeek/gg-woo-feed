<?php
namespace GG_Woo_Feed\Common\Integrations\Provider;

use GG_Woo_Feed\Common\Integrations\Generate;
use GG_Woo_Feed\Common\Model\Mapping;

class Feed_Custom {
	/**
	 *
	 * @var     Mapping $products
	 * @access  public
	 */
	public $products;

	/**
	 * @var     array $rules
	 * @access  public
	 */
	public $rules;


	/**
	 *
	 * @var     array $storeProducts
	 * @access  public
	 */
	private $storeProducts;

	/**
	 * Feed_Custom constructor.
	 *
	 * @param Generate $feed_query
	 */
	public function __construct( $feed_query ) {
		$this->products = new Mapping( $feed_query );
		if ( ! isset( $feed_query['product_ids'] ) ) {
			$feed_query['product_ids'] = $this->products->query_products();
		}
		$this->products->get_products( $feed_query['product_ids'] );
		$this->rules = $feed_query;
	}

	/**
	 * Map XML
	 */
	public function map_xml() {
		if ( $this->products ) {
			foreach ( $this->products as $no => $product ) {
				foreach ( $product as $key => $value ) {
					$this->products[ $no ][ $key ] = $this->format_xml_line( $key, $value );
				}
			}
		}
	}

	public function map_csv_txt() {

	}

	/**
	 * Make the XML node.
	 *
	 * @param        $attribute
	 * @param        $value
	 * @param string $space
	 * @return string
	 */
	public function format_xml_line( $attribute, $value, $space = '' ) {
		$attribute = str_replace( ' ', '_', $attribute );
		if ( ! empty( $value ) ) {
			$value = trim( $value );
		}
		if ( false === strpos( $value, '<![CDATA[' ) && 'http' == substr( trim( $value ), 0, 4 ) ) {
			$value = "<![CDATA[$value]]>";
		} elseif ( false === strpos( $value, '<![CDATA[' ) && ! is_numeric( trim( $value ) ) && ! empty( $value ) ) {
			$value = "<![CDATA[$value]]>";
		}

		return "
        $space<$attribute>$value</$attribute>";
	}

	/**
	 * Get frame
	 *
	 * @return array|bool|string
	 */
	public function get_frame() {
		if ( ! empty( $this->products ) ) {
			if ( 'xml' === $this->rules['feed_type'] ) {
				$feed = [
					"body"   => $this->products->feed_body,
					"header" => $this->products->feed_header,
					"footer" => $this->products->feed_footer,
				];

				return $feed;
			}

			if ( 'txt' === $this->rules['feed_type'] ) {
				$feed = [
					'body'   => $this->products->feed_body,
					'header' => $this->products->feed_header,
					'footer' => '',
				];

				return $feed;
			}

			if ( 'csv' === $this->rules['feed_type'] ) {
				$feed = [
					'body'   => $this->products->feed_body,
					'header' => $this->products->feed_header,
					'footer' => '',
				];

				return $feed;
			}
		}

		$feed = [
			"body"   => '',
			"header" => '',
			"footer" => '',
		];

		return $feed;
	}

	/**
	 * Get header.
	 *
	 * @param $engine
	 * @return mixed
	 */
	public function get_header( $engine ) {
		return $engine->get_xml_feed_header();
	}

	/**
	 * Get footer.
	 *
	 * @param $engine
	 * @return mixed
	 */
	public function get_footer( $engine ) {
		return $engine->get_xml_feed_footer();
	}
}
