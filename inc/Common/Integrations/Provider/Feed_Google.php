<?php
namespace GG_Woo_Feed\Common\Integrations\Provider;

use GG_Woo_Feed\Common\Model\Mapping;

class Feed_Google extends Feed_Abstract {
	/**
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
	 * @var     array $mapping
	 * @access  public
	 */
	public $mapping;

	/**
	 * @var     array $errorLog
	 * @access  public
	 */
	public $errorLog;

	/**
	 * @var     int $errorCounter
	 * @access  public
	 */
	public $errorCounter;

	/**
	 * @var     string $feedWrapper
	 * @access  public
	 */
	public $feedWrapper = 'item';

	/**
	 * @var     array $storeProducts
	 * @access  public
	 */
	private $storeProducts;

	/**
	 * Feed_Google constructor.
	 *
	 * @param $feed_query
	 */
	public function __construct( $feed_query ) {
		$feed_query['item_wrap'] = $this->feedWrapper;
		$this->products          = new Mapping( $feed_query );
		if ( ! isset( $feed_query['product_ids'] ) ) {
			$feed_query['product_ids'] = $this->products->query_products();
		}
		$this->products->get_products( $feed_query['product_ids'] );
		$this->rules = $feed_query;
	}


	/**
	 * Get frame.
	 *
	 * @return array
	 */
	public function get_frame() {
		if ( ! empty( $this->products ) ) {
			if ( 'xml' === $this->rules['feed_type'] ) {
				$feed = [
					"body"   => $this->products->feed_body,
					"header" => $this->get_xml_feed_header(),
					"footer" => $this->get_xml_feed_footer(),
				];

				return $feed;
			}

			if ( 'txt' === $this->rules['feed_type'] ) {
				$feed = [
					"body"   => $this->products->feed_body,
					"header" => $this->products->feed_header,
					"footer" => '',
				];

				return $feed;
			}

			if ( 'csv' === $this->rules['feed_type'] ) {
				$feed = [
					"body"   => $this->products->feed_body,
					"header" => $this->products->feed_header,
					"footer" => '',
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
	 * Map XML.
	 */
	public function map_xml() {
		$googleXMLAttribute = [
			"id"                        => [ "g:id", false ],
			"title"                     => [ "title", true ],
			"description"               => [ "description", true ],
			"link"                      => [ "link", true ],
			"mobile_link"               => [ "mobile_link", true ],
			"product_type"              => [ "g:product_type", true ],
			"google_taxonomy"           => [ "g:google_product_category", true ],
			"image"                     => [ "g:image_link", true ],
			"images"                    => [ "g:additional_image_link", false ],
			"images_1"                  => [ "g:additional_image_link_1", true ],
			"images_2"                  => [ "g:additional_image_link_2", true ],
			"images_3"                  => [ "g:additional_image_link_3", true ],
			"images_4"                  => [ "g:additional_image_link_4", true ],
			"images_5"                  => [ "g:additional_image_link_5", true ],
			"images_6"                  => [ "g:additional_image_link_6", true ],
			"images_7"                  => [ "g:additional_image_link_7", true ],
			"images_8"                  => [ "g:additional_image_link_8", true ],
			"images_9"                  => [ "g:additional_image_link_9", true ],
			"images_10"                 => [ "g:additional_image_link_10", true ],
			"condition"                 => [ "g:condition", false ],
			"availability"              => [ "g:availability", false ],
			"availability_date"         => [ "g:availability_date", false ],
			"inventory"                 => [ "g:inventory", false ],
			"price"                     => [ "g:price", true ],
			"sale_price"                => [ "g:sale_price", true ],
			"sale_price_effective_date" => [ "g:sale_price_effective_date", true ],
			"brand"                     => [ "g:brand", true ],
			"sku"                       => [ "g:mpn", true ],
			"upc"                       => [ "g:gtin", true ],
			"identifier_exists"         => [ "g:identifier_exists", true ],
			"item_group_id"             => [ "g:item_group_id", false ],
			"color"                     => [ "g:color", true ],
			"gender"                    => [ "g:gender", true ],
			"age_group"                 => [ "g:age_group", true ],
			"material"                  => [ "g:material", true ],
			"pattern"                   => [ "g:pattern", true ],
			"size"                      => [ "g:size", true ],
			"size_type"                 => [ "g:size_type", true ],
			"size_system"               => [ "g:size_system", true ],
			"tax"                       => [ "tax", true ],
			"tax_country"               => [ "g:tax_country", true ],
			"tax_region"                => [ "g:tax_region", true ],
			"tax_rate"                  => [ "g:tax_rate", true ],
			"tax_ship"                  => [ "g:tax_ship", true ],
			"tax_category"              => [ "g:tax_category", true ],
			"weight"                    => [ "g:shipping_weight", false ],
			"length"                    => [ "g:shipping_length", false ],
			"width"                     => [ "g:shipping_width", false ],
			"height"                    => [ "g:shipping_height", false ],
			"shipping_label"            => [ "g:shipping_label", false ],
			"shipping_country"          => [ "g:shipping_country", false ],
			"shipping_service"          => [ "g:shipping_service", false ],
			"shipping_price"            => [ "g:shipping_price", false ],
			"shipping_region"           => [ "g:shipping_region", false ],
			"multipack"                 => [ "g:multipack", true ],
			"is_bundle"                 => [ "g:is_bundle", true ],
			"adult"                     => [ "g:adult", true ],
			"adwords_redirect"          => [ "g:adwords_redirect", true ],
			"custom_label_0"            => [ "g:custom_label_0", true ],
			"custom_label_1"            => [ "g:custom_label_1", true ],
			"custom_label_2"            => [ "g:custom_label_2", true ],
			"custom_label_3"            => [ "g:custom_label_3", true ],
			"custom_label_4"            => [ "g:custom_label_4", true ],
			"excluded_destination"      => [ "g:excluded_destination", true ],
			"included_destination"      => [ "g:included_destination", true ],
			"expiration_date"           => [ "g:expiration_date", true ],
			"unit_pricing_measure"      => [ "g:unit_pricing_measure", true ],
			"unit_pricing_base_measure" => [ "g:unit_pricing_base_measure", true ],
			"energy_efficiency_class"   => [ "g:energy_efficiency_class", true ],
			"loyalty_points"            => [ "g:loyalty_points", true ],
			"installment"               => [ "g:installment", true ],
			"promotion_id"              => [ "g:promotion_id", true ],
			"cost_of_goods_sold"        => [ "g:cost_of_goods_sold", true ],
		];

		if ( ! empty( $this->products ) ) {
			foreach ( $this->products as $no => $product ) {
				$this->identifier_status_add( $no );
				foreach ( $product as $key => $value ) {
					$this->map_atts( $no,
						$key,
						$googleXMLAttribute[ $key ][0],
						$value,
						$googleXMLAttribute[ $key ][0] );
				}

				$this->process_google_shipping_attribute_for_xml( $no );
				$this->process_google_tax_attribute_for_xml( $no );
			}
		}
	}

	/**
	 * Map CSV TXT info.
	 */
	public function map_csv_txt() {
		//Basic product information
		$googleCSVTXTAttribute = [
			"id"                        => [ "id", false ],
			"title"                     => [ "title", true ],
			"description"               => [ "description", true ],
			"link"                      => [ "link", true ],
			"mobile_link"               => [ "mobile_link", true ],
			"product_type"              => [ "product type", true ],
			"google_taxonomy"           => [ "google product category", true ],
			"image"                     => [ "image link", true ],
			"images"                    => [ "additional image link", true ],
			"images_1"                  => [ "additional image link 1", true ],
			"images_2"                  => [ "additional image link 2", true ],
			"images_3"                  => [ "additional image link 3", true ],
			"images_4"                  => [ "additional image link 4", true ],
			"images_5"                  => [ "additional image link 5", true ],
			"images_6"                  => [ "additional image link 6", true ],
			"images_7"                  => [ "additional image link 7", true ],
			"images_8"                  => [ "additional image link 8", true ],
			"images_9"                  => [ "additional image link 9", true ],
			"images_10"                 => [ "additional image link 10", true ],
			"condition"                 => [ "condition", false ],
			"availability"              => [ "availability", false ],
			"availability_date"         => [ "availability date", false ],
			"inventory"                 => [ "inventory", false ],
			"price"                     => [ "price", true ],
			"sale_price"                => [ "sale price", true ],
			"sale_price_effective_date" => [ "sale price effective date", true ],
			"brand"                     => [ "brand", true ],
			"sku"                       => [ "mpn", true ],
			"upc"                       => [ "gtin", true ],
			"identifier_exists"         => [ "identifier exists", true ],
			"item_group_id"             => [ "item group id", false ],
			"color"                     => [ "color", true ],
			"gender"                    => [ "gender", true ],
			"age_group"                 => [ "age group", true ],
			"material"                  => [ "material", true ],
			"pattern"                   => [ "pattern", true ],
			"size"                      => [ "size", true ],
			"size_type"                 => [ "size type", true ],
			"size_system"               => [ "size system", true ],
			"tax"                       => [ "tax", true ],
			"tax_country"               => [ "tax country", true ],
			"tax_region"                => [ "tax region", true ],
			"tax_rate"                  => [ "tax rate", true ],
			"tax_ship"                  => [ "tax ship", true ],
			"tax_category"              => [ "tax category", true ],
			"weight"                    => [ "shipping weight", false ],
			"length"                    => [ "shipping length", false ],
			"width"                     => [ "shipping width", false ],
			"height"                    => [ "shipping height", false ],
			"shipping_label"            => [ "shipping label", false ],
			"shipping_country"          => [ "shipping country", false ],
			"shipping_service"          => [ "shipping service", false ],
			"shipping_price"            => [ "shipping price", false ],
			"shipping_region"           => [ "shipping region", false ],
			"multipack"                 => [ "multipack", true ],
			"is_bundle"                 => [ "is bundle", true ],
			"adult"                     => [ "adult", true ],
			"adwords_redirect"          => [ "adwords redirect", true ],
			"custom_label_0"            => [ "custom label 0", true ],
			"custom_label_1"            => [ "custom label 1", true ],
			"custom_label_2"            => [ "custom label 2", true ],
			"custom_label_3"            => [ "custom label 3", true ],
			"custom_label_4"            => [ "custom label 4", true ],
			"excluded_destination"      => [ "excluded destination", true ],
			"included_destination"      => [ "included destination", true ],
			"expiration_date"           => [ "expiration date", true ],
			"unit_pricing_measure"      => [ "unit pricing measure", true ],
			"unit_pricing_base_measure" => [ "unit pricing base measure", true ],
			"energy_efficiency_class"   => [ "energy efficiency class", true ],
			"loyalty_points"            => [ "loyalty points", true ],
			"installment"               => [ "installment", true ],
			"promotion_id"              => [ "promotion id", true ],
			"cost_of_goods_sold"        => [ "cost of goods sold", true ],
		];

		if ( ! empty( $this->products ) ) {
			foreach ( $this->products as $no => $product ) {
				foreach ( $product as $key => $value ) {
					$this->map_atts( $no,
						$key,
						$googleCSVTXTAttribute[ $key ][0],
						$value,
						$googleCSVTXTAttribute[ $key ][0] );
				}
				$this->process_google_shipping_attribute_for_CSVTXT( $no );
				$this->process_google_tax_attribute_for_CSVTXT( $no );
			}
		}
	}

	/**
	 * Map to google attribute
	 *
	 * @param      $no
	 * @param      $from
	 * @param      $to
	 * @param      $value
	 * @param bool $cdata
	 *
	 * @return array|string
	 */
	public function map_atts( $no, $from, $to, $value, $cdata = false ) {
		unset( $this->products[ $no ][ $from ] );
		if ( 'g:color' == $to ) {
			$value = str_replace( ',', '/', $value );
		}
		if ( 'xml' == $this->rules['feed_type'] ) {
			return $this->products[ $no ][ $to ] = $this->format_xml_line( $to, $value, $cdata );
		}

		return $this->products[ $no ][ $to ] = $value;
	}

	public function identifier_status_add( $no ) {
		$identifier = [ 'brand', 'upc', 'sku', 'mpn', 'gtin' ];
		$product    = $this->products[ $no ];

		if ( ! array_key_exists( 'g:identifier_exists', $product ) ) {
			if ( count( array_intersect_key( array_flip( $identifier ), $product ) ) >= 2 ) {
				# Any 2 required keys exist!
				$countIdentifier = 0;
				if ( array_key_exists( 'brand', $product ) && ! empty( $product['brand'] ) ) {
					$countIdentifier++;
				}
				if ( array_key_exists( 'upc', $product ) && ! empty( $product['upc'] ) ) {
					$countIdentifier++;
				}
				if ( array_key_exists( 'sku', $product ) && ! empty( $product['sku'] ) ) {
					$countIdentifier++;
				}
				if ( array_key_exists( 'mpn', $product ) && ! empty( $product['mpn'] ) ) {
					$countIdentifier++;
				}
				if ( array_key_exists( 'gtin', $product ) && ! empty( $product['gtin'] ) ) {
					$countIdentifier++;
				}
				if ( $countIdentifier >= 2 ) {
					$this->products[ $no ]["g:identifier_exists"] = $this->format_xml_line( "g:identifier_exists",
						"yes",
						$cdata = true );
				} else {
					$this->products[ $no ]["g:identifier_exists"] = $this->format_xml_line( "g:identifier_exists",
						'no',
						$cdata = true );
				}
			} else {
				$this->products[ $no ]["g:identifier_exists"] = $this->format_xml_line( "g:identifier_exists",
					'no',
					$cdata = true );
			}
		}
	}


	public function process_google_shipping_attribute_for_xml( $no ) {
		$shipping     = [ 'g:shipping_country', 'g:shipping_service', 'g:shipping_price', 'g:shipping_region' ];
		$shippingAttr = [];
		$products     = $this->products[ $no ];
		foreach ( $products as $keyAttr => $valueAttr ) {
			if ( in_array( $keyAttr, $shipping ) ) {
				$shippingAttr[] = [ $keyAttr => $valueAttr ];
				unset( $this->products[ $no ][ $keyAttr ] );
			}
		}
		if ( count( $shippingAttr ) ) {
			$str = '';
			foreach ( $shippingAttr as $key => $attributes ) {
				foreach ( $attributes as $keyAttr => $valueAttr ) {
					$str .= str_replace( 'shipping_', '', $valueAttr );
				}
			}

			return $this->products[ $no ]['g:shipping'] = $this->format_xml_line( "g:shipping", $str, false );
		}

		return false;
	}

	public function process_google_tax_attribute_for_xml( $no ) {
		$tax      = [ 'g:tax_country', 'g:tax_region', 'g:tax_rate', 'g:tax_ship' ];
		$taxAttr  = [];
		$products = $this->products[ $no ];
		foreach ( $products as $keyAttr => $valueAttr ) {
			if ( in_array( $keyAttr, $tax ) ) {
				$taxAttr[] = [ $keyAttr => $valueAttr ];
				unset( $this->products[ $no ][ $keyAttr ] );
			}
		}
		if ( count( $taxAttr ) ) {
			$str = '';
			foreach ( $taxAttr as $key => $attributes ) {
				foreach ( $attributes as $keyAttr => $valueAttr ) {
					$str = str_replace( [ 'tax_', 'ship' ], [ '', 'tax_ship' ], $valueAttr );
				}
			}

			return $this->products[ $no ]['g:tax'] = $this->format_xml_line( "g:tax", $str, false );
		}

		return false;
	}

	public function process_google_shipping_attribute_for_CSVTXT( $no ) {
		$shipping     = [ 'shipping country', 'shipping service', 'shipping price', 'shipping region' ];
		$shippingAttr = [];
		$products     = $this->products[ $no ];
		foreach ( $products as $keyAttr => $valueAttr ) {
			if ( in_array( $keyAttr, $shipping ) ) {
				$shippingAttr[] = [ $keyAttr => $valueAttr ];
				unset( $this->products[ $no ][ $keyAttr ] );
			}
		}
		if ( count( $shippingAttr ) ) {
			$str = '';
			foreach ( $shippingAttr as $key => $attributes ) {
				foreach ( $attributes as $keyAttr => $valueAttr ) {
					$country = ( 'shipping country' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
					$service = ( 'shipping service' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
					$price   = ( 'shipping price' == $keyAttr ) ? $str .= $valueAttr : '';
					$region  = ( 'shipping region' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
				}
			}

			return $this->products[ $no ]['shipping(country:region:service:price)'] = str_replace( ' : ', ':', $str );
		}

		return false;
	}

	public function process_google_tax_attribute_for_CSVTXT( $no ) {
		$tax      = [ 'tax country', 'tax region', 'tax rate', 'tax ship' ];
		$taxAttr  = [];
		$products = $this->products[ $no ];
		foreach ( $products as $keyAttr => $valueAttr ) {
			if ( in_array( $keyAttr, $tax ) ) {
				$taxAttr[] = [ $keyAttr => $valueAttr ];
				unset( $this->products[ $no ][ $keyAttr ] );
			}
		}
		if ( count( $taxAttr ) ) {
			$str = '';
			foreach ( $taxAttr as $key => $attributes ) {
				foreach ( $attributes as $keyAttr => $valueAttr ) {
					$country = ( 'tax country' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
					$region  = ( 'tax region' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
					$rate    = ( 'tax rate' == $keyAttr ) ? $str .= $valueAttr . ':' : '';
					$ship    = ( 'tax ship' == $keyAttr ) ? $str .= $valueAttr : '';
				}
			}

			return $this->products[ $no ]['tax(country:region:rate:tax_ship)'] = str_replace( ' : ', ':', $str );
		}

		return false;
	}

	public function format_xml_line( $attribute, $value, $cdata, $space = '' ) {
		if ( ! empty( $value ) ) {
			$value = trim( $value );
		}
		if ( is_array( $value ) ) {
			$value = wp_json_encode( $value );
		}
		if ( false === strpos( $value, "<![CDATA[" ) && 'http' == substr( trim( $value ), 0, 4 ) ) {
			$value = "<![CDATA[$value]]>";
		} elseif ( false === strpos( $value, "<![CDATA[" ) && true === $cdata && ! empty( $value ) ) {
			$value = "<![CDATA[$value]]>";
		} elseif ( $cdata ) {
			if ( ! empty( $value ) ) {
				$value = "<![CDATA[$value]]>";
			}
		}
		if ( substr( $attribute, 0, 23 ) === 'g:additional_image_link' ) {
			$attribute = "g:additional_image_link";
		}

		return "$space<$attribute>$value</$attribute>";
	}


	public function get_xml_feed_header() {
		$output = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
  <channel>
    <title><![CDATA[' . html_entity_decode( get_option( 'blogname' ) ) . ']]></title>
    <link><![CDATA[' . site_url() . ']]></link>
    <description><![CDATA[' . html_entity_decode( get_option( 'blogdescription' ) ) . ']]></description>';

		return $output;
	}

	public function get_xml_feed( $items ) {
		$feed = '';
		$feed .= "\n";
		if ( $items ) {
			foreach ( $items as $item => $products ) {
				$feed .= "      <" . $this->feedWrapper . ">";
				foreach ( $products as $key => $value ) {
					if ( ! empty( $value ) ) {
						$feed .= $value;
					}
				}
				$feed .= "\n      </" . $this->feedWrapper . ">\n";
			}

			return $feed;
		}

		return false;
	}

	public function get_xml_feed_footer() {
		$footer = "  </channel>
</rss>";

		return $footer;
	}

	public function short_products() {
		if ( $this->products ) {
			sleep( 1 );
			$array = [];
			$ij    = 0;
			foreach ( $this->products as $key => $item ) {
				$array[ $ij ] = $item;
				unset( $this->products[ $key ] );
				$ij++;
			}

			return $this->products = $array;
		}

		return $this->products;
	}

	/**
	 * Get CSV feed
	 *
	 * @return string
	 */
	public function get_csv_feed() {
		if ( $this->products ) {
			$headers = array_keys( $this->products[0] );
			$feed[]  = $headers;
			foreach ( $this->products as $no => $product ) {
				$row = [];
				foreach ( $headers as $key => $header ) {
					if ( strpos( $header, "additional image link" ) !== false ) {
						$header = "additional image link";
					}
					$row[] = isset( $product[ $header ] ) ? $product[ $header ] : '';
				}
				$feed[] = $row;
			}

			return $feed;
		}

		return false;
	}
}
