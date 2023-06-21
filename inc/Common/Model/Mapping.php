<?php
namespace GG_Woo_Feed\Common\Model;

use GG_Woo_Feed\Common\Dropdown;
use Jetpack;
use Jetpack_Photon;
use GG_Woo_Feed\Core\Constant;

class Mapping {
	/**
	 * Feed file headers
	 *
	 * @var string|array
	 */
	public $feed_header;

	/**
	 * Feed File Body
	 *
	 * @var string|array
	 */
	public $feed_body;

	/**
	 * Feed file footer
	 *
	 * @var string|array
	 */
	public $feed_footer;

	/**
	 * CSV|TXT enclosure
	 *
	 * @var string
	 */
	protected $enclosure;

	/**
	 * CSV|TXT delimiter
	 *
	 * @var string
	 */
	protected $delimiter;

	/**
	 * Config.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Post status to query
	 *
	 * @var string
	 */
	protected $post_status = 'publish';

	/**
	 * Processed products
	 *
	 * @var array
	 */
	public $products = [];

	/**
	 * The count increase.
	 *
	 * @var int
	 */
	protected $pi = 0;

	/**
	 * Google shipping tax attributes
	 *
	 * @var array
	 */
	protected $google_shipping_tax = [
		'shipping_country',
		'shipping_region',
		'shipping_service',
		'shipping_price',
		'tax_country',
		'tax_region',
		'tax_rate',
		'tax_ship',
		'installment_months',
		'installment_amount',
		'subscription_period',
		'subscription_period_length',
		'subscription_amount',
	];
	/**
	 * Product types for query
	 *
	 * @var array
	 */
	protected $product_types = [
		'simple',
		'variable',
		'variation',
		'grouped',
		'external',
	];

	/**
	 * Mapping constructor.
	 *
	 * @param $config
	 */
	public function __construct( $config ) {
		$config       = $this->prepare_config( $config );
		$this->config = gg_woo_feed_parse_feed_queries( $config );

		if ( 'on' === gg_woo_feed_get_option( 'enable_jetpack_cdn_images', 'on' ) && class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) {
			add_filter( 'jetpack_photon_admin_allow_image_downsize', '__return_true' );
		}
	}


	/**
	 * Prepare config.
	 *
	 * @param $config
	 * @return array
	 */
	public function prepare_config( $config ) {
		return $config;
	}

	/**
	 * Get config.
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Get products
	 *
	 * @return array
	 */
	public function query_products() {
		$config = $this->get_config();

		if ( 'on' === $config['filter_by_attributes'] && $config['filter_by_attributes_atts'] && $config['conditions_attributes'] && $config['condition_values_attributes'] ) {
			return $this->query_by_attributes();
		}

		return $this->query_wc_products();
	}

	/**
	 * Get products
	 *
	 * @return array
	 */
	public function query_wc_products() {
		$config = $this->get_config();

		$args = apply_filters( 'gg_woo_feed_wc_product_query', [
			'limit'            => $config['product_limit'] ? $config['product_limit'] : -1,
			'status'           => $this->post_status,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'return'           => 'ids',
			'suppress_filters' => false,
		] );

		// Sale Status
		if ( $config['feed_filter_sale'] === 'sale' ) {
			$args['include'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
		} elseif ( $config['feed_filter_sale'] === 'notsale' ) {
			$args['exclude'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
		}

		// Stock Status
		if ( $config['feed_filter_stock'] && in_array( $config['feed_filter_stock'], [ 'instock', 'outofstock' ] ) ) {
			$args['stock_status'] = $config['feed_filter_stock'];
		}

		// Product Categories.
		if ( 'on' !== $config['feed_category_all'] && $config['feed_category'] ) {
			$args['category'] = (array) $config['feed_category'];
		}

		// Product Type.
		$args['type'] = $config['feed_filter_product_type'] ? $config['feed_filter_product_type'] : [ 'simple', 'variable', 'grouped', 'external' ];

		// Date Query
		if ( isset($config['filter_by_date']) && $config['filter_by_date'] == 'on' ) {
			if ( isset($config['filter_date_start']) && $config['filter_date_start']) {
				$args['date_query']['after'] = $config['filter_date_start'];
			}
			if ( isset($config['filter_date_end']) && $config['filter_date_end']) {
				$args['date_query']['before'] = $config['filter_date_end'];
			}
			$args['date_query']['inclusive'] = true;
		}

		$args  = apply_filters( 'gg_woo_feed_product_query_args', $args );
		
		$query = new \WC_Product_Query( $args );

		return $query->get_products();
	}

	/**
	 * Query by attributes.
	 *
	 * @return array
	 */
	public function query_by_attributes() {
		$config = $this->get_config();

		if ( 'on' !== $config['filter_by_attributes'] || ! $config['filter_by_attributes_atts'] || ! $config['conditions_attributes'] || ! $config['condition_values_attributes'] ) {
			return [];
		}

		$args = [
			'post_type'   => 'product_variation',
			'post_status' => 'publish',
			'numberposts' => $config['product_limit'] ? $config['product_limit'] : -1,
		];

		if ( 'on' !== $config['feed_category_all'] && $config['feed_category'] ) {
			$args['post_parent__in'] = $this->get_variation_parent_ids_from_term( (array) $config['feed_category'], 'product_cat', 'slug' );
		}

		// Sale Status
		if ( $config['feed_filter_sale'] === 'sale' ) {
			$args['post__in'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
		} elseif ( $config['feed_filter_sale'] === 'notsale' ) {
			$args['post__not_in'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
		}

		$args['meta_query']['relation'] = $this->get_filter_by_attributes_relationship();
		foreach ( $config['filter_by_attributes_atts'] as $key => $attribute ) {
			$attribute            = str_replace( [ Constant::PRODUCT_ATTR_PREFIX ], 'pa_', $attribute );
			$args['meta_query'][] = [
				'key'     => 'attribute_' . $attribute,
				'value'   => $this->parse_meta_query_value( $config['condition_values_attributes'][ $key ], $config['conditions_attributes'][ $key ] ),
				'compare' => $this->parse_meta_query_condition( $config['conditions_attributes'][ $key ] ),
			];
		}

		// Stock Status
		if ( $config['feed_filter_stock'] && in_array( $config['feed_filter_stock'], [ 'instock', 'outofstock' ] ) ) {
			$args['meta_query'][] = [
				'key'   => '_stock_status',
				'value' => $config['feed_filter_stock'],
			];
		}

		// Date Query
		if ( isset($config['filter_by_date']) && $config['filter_by_date'] == 'on' ) {
			if ( isset($config['filter_date_start']) && $config['filter_date_start']) {
				$args['date_query']['after'] = $config['filter_date_start'];
			}
			if ( isset($config['filter_date_end']) && $config['filter_date_end']) {
				$args['date_query']['before'] = $config['filter_date_end'];
			}
			$args['date_query']['inclusive'] = true;
		}

		$products = get_posts( $args );
		$product_ids = wp_list_pluck( $products, 'ID' );

		return $product_ids ? $product_ids : [];
	}

	/**
	 * Get Product Information according to feed config
	 *
	 * @param int[] $product_ids
	 *
	 * @return array
	 */
	public function get_products( $product_ids ) {
		if ( empty( $product_ids ) ) {
			return [];
		}

		foreach ( $product_ids as $key => $pid ) {
			$product = wc_get_product( $pid );

			// Skip for invalid products
			if ( ! is_object( $product ) ) {
				continue;
			}

			// Skip for invisible products
			if ( ! $product->is_visible() ) {
				continue;
			}

			// Apply variable and variation settings
			if ( $product->is_type( 'variable' ) && $product->has_child() ) {
				if ( 'on' !== $this->config['exclude_variations'] ) {
					$this->pi++;
					$variations = $product->get_visible_children();
					if ( is_array( $variations ) && ( count( $variations ) > 0 ) ) {
						$this->get_products( $variations );
					}
				}

				// Handle main product ID in the end.
				// Ignore main product ID if set off.
				if ( 'on' !== $this->config['show_main_variable_product'] ) {
					continue;
				}
			}

			if ( ! $this->is_allowed( $product ) ) {
				continue;
			}

			// Add Single item wrapper before product info loop start
			if ( 'xml' === $this->config['feed_type'] ) {
				$this->feed_body .= "\n";
				$this->feed_body .= '<' . $this->config['item_wrap'] . '>';
				$this->feed_body .= "\n";
			}

			// Unique Merchant Attributes
			$provider_atts = [];
			// Get Product Attribute values by type and assign to product array
			foreach ( $this->config['attributes'] as $attr_key => $attribute ) {
				$skipped_providers = [ 'google', 'facebook' ];
				if ( 'xml' === $this->config['feed_type'] &&
				     in_array( $this->config['mattributes'][ $attr_key ], $this->google_shipping_tax ) &&
				     in_array( $this->config['provider'], $skipped_providers )
				) {
					continue;
				}

				if ( ! isset( $this->config['mattributes'][ $attr_key ] ) || ! $this->config['mattributes'][ $attr_key ] ) {
					continue;
				}

				$plugin_attr = $this->config['mattributes'][ $attr_key ];

				if ( in_array( $plugin_attr, $provider_atts ) ) {
					continue;
				}

				if ( 'pattern' === $this->config['type'][ $attr_key ] ) {
					$attr_value = $this->config['default'][ $attr_key ];
				} else {
					$attr_value = $this->get_attr_value_by_type( $product, $attribute );
				}

				$provider  = $this->config['provider'];
				$feed_type = $this->config['feed_type'];

				$replaced_attribute = gg_woo_feed_get_provider_attributes( $plugin_attr, $provider, $feed_type );

				$attr_value = $this->custom_attr_value( $attr_value, $plugin_attr, $provider, $feed_type, $product );

				$attr_value = apply_filters( 'gg_woo_feed_mapping_attribute_value', $attr_value, $plugin_attr, $replaced_attribute, $provider, $feed_type, $this->config, $product, $this );

				// Escape by output_type.
				$output_type = $this->config['output_type'][ $attr_key ];
				if ( 'default' !== $output_type ) {
					$attr_value = $this->format_output( $attr_value, $output_type, $product, $attribute );
				}

				// Default Value.
				$default_value = $this->config['default_value'][ $attr_key ];
				if ( ! $attr_value ) {
					$attr_value = $default_value;
				}

				$prefix = $this->config['prefix'][ $attr_key ];
				$suffix = $this->config['suffix'][ $attr_key ];

				if ( '' !== $prefix || '' !== $suffix ) {
					$attr_value = $this->process_prefix_suffix( $attr_value, $prefix, $suffix, $attribute );
				}

				if ( 'xml' === $this->config['feed_type'] ) {
					$replaced_attribute = str_replace( '', '_', $replaced_attribute );

					if ( ! empty( $attr_value ) ) {
						$attr_value = trim( $attr_value );
					}

					if ( '' != $attr_value ) {
						$attr_value = gg_woo_feed_add_cdata( $plugin_attr, $attr_value, $provider );

						if ( 'g:color' === $replaced_attribute ) {
							$attr_value = str_replace( ', ', '/', $attr_value );
						}

						$attr_value = stripslashes( $attr_value );

						$this->feed_body .= '<' . $replaced_attribute . '>' . "$attr_value" . '</' . $replaced_attribute . '>';
						$this->feed_body .= "\n";
					} else {
						// $this->feed_body .= '<' . $replaced_attribute . '/>';
						// $this->feed_body .= "\n";
					}
				} elseif ( 'csv' === $this->config['feed_type'] ) {
					$plugin_attr = gg_woo_feed_get_provider_attributes( $plugin_attr, $provider, $feed_type );
					$plugin_attr = $this->process_string_for_csv( $plugin_attr );
					$attr_value  = $this->process_string_for_csv( $attr_value );
				} elseif ( 'txt' === $this->config['feed_type'] ) {
					$plugin_attr = gg_woo_feed_get_provider_attributes( $plugin_attr, $provider, $feed_type );
					$plugin_attr = $this->process_string_for_txt( $plugin_attr );
					$attr_value  = $this->process_string_for_txt( $attr_value );
				}

				$provider_atts[ $attr_key ]                  = $this->config['mattributes'][ $attr_key ];
				$this->products[ $this->pi ][ $plugin_attr ] = $attr_value;
			}

			$this->process_for_provider( $product, $this->pi );

			if ( 'xml' === $this->config['feed_type'] ) {
				if ( empty( $this->feed_header ) ) {
					$this->feed_header = $this->process_xml_feed_header();
					$this->feed_footer = $this->process_xml_feed_footer();
				}

				$this->feed_body .= '</' . $this->config['item_wrap'] . '>';
			} elseif ( 'txt' === $this->config['feed_type'] ) {
				if ( empty( $this->feed_header ) ) {
					$this->process_txt_feed_header();
				}
				$this->process_txt_feed_body();
			} else {
				if ( empty( $this->feed_header ) ) {
					$this->process_csv_feed_header();
				}
				$this->process_csv_feed_body();
			}
			$this->pi++;
		}

		do_action( 'gg_woo_feed_after_product_loop', $product_ids, $this->config );

		return $this->products;
	}

	/**
	 * Is allowed product?
	 *
	 * @param $product \WC_Product
	 *
	 * @return bool
	 */
	protected function is_allowed( $product ) {
		do_action( 'gg_woo_feed_before_allowed_product', $product );

		// Hard excluded by plugin meta.
		$excluded = $this->get_product_meta( $product, 'gg_woo_feed_meta_exclude_product' );

		if ( 'yes' === $excluded ) {
			return false;
		}

		// Check if missing some required fields.
		if ( ! in_array( 'image', $this->config['mattributes'] ) ) {
			return false;
		}

		foreach ( $this->config['mattributes'] as $attr_key => $mattribute ) {
			if ( 'image' === $mattribute ) {
				$p_attribute = $this->config['attributes'][ $attr_key ];
				$attr_value  = $this->get_attr_value_by_type( $product, $p_attribute );

				if ( ! $attr_value ) {
					return false;
				}
			}
		}

		// Check by advanced filters.
		$filter_relationship = $this->get_filter_relationship();
		$filter_atts         = $this->config['filter_atts'];
		$conditions          = $this->config['conditions'];
		$condition_values    = $this->config['condition_values'];
		$allowed             = true;

		if ( $filter_atts ) {
			foreach ( $filter_atts as $filter_key => $filter_attr_value ) {
				$condition          = isset( $conditions[ $filter_key ] ) && $conditions[ $filter_key ] ? $conditions[ $filter_key ] : '=';
				$condition_value    = isset( $condition_values[ $filter_key ] ) && $condition_values[ $filter_key ] ? $condition_values[ $filter_key ] : '';
				$condition_value    = strtolower( strip_tags( $condition_value ) );
				$product_attr_value = $this->get_attr_value_by_type( $product, $filter_attr_value );
				$product_attr_value = strtolower( strip_tags( $product_attr_value ) );

				if ( 'and' === $filter_relationship ) {
					switch ( $condition ) {
						case 'contains':
							if ( false !== strpos( $condition_value, ',' ) ) {
								$condition_array_value = explode( ',', $condition_value );
								$condition_array_value = array_map( 'trim', $condition_array_value );
								if ( ! in_array( $product_attr_value, $condition_array_value ) ) {
									return false;
								}
							} else {
								if ( ! preg_match( '/' . $condition_value . '/', $product_attr_value ) ) {
									return false;
								}
							}

							break;
						case 'containsnot':
							if ( false !== strpos( $condition_value, ',' ) ) {
								$condition_array_value = explode( ',', $condition_value );
								$condition_array_value = array_map( 'trim', $condition_array_value );

								if ( in_array( $product_attr_value, $condition_array_value ) ) {
									return false;
								}
							} else {
								if ( preg_match( '/' . $condition_value . '/', $product_attr_value ) ) {
									return false;
								}
							}

							break;
						case '=':
							if ( $product_attr_value != $condition_value ) {
								return false;
							}
							break;
						case '!=':
							if ( $product_attr_value == $condition_value ) {
								return false;
							}
							break;
						case '>':
							if ( $product_attr_value <= $condition_value ) {
								return false;
							}
							break;
						case '>=':
							if ( $product_attr_value < $condition_value ) {
								return false;
							}
							break;
						case '<':
							if ( $product_attr_value >= $condition_value ) {
								return false;
							}
							break;
						case '<=':
							if ( $product_attr_value > $condition_value ) {
								return false;
							}
							break;
					}
				}

				if ( 'or' === $filter_relationship ) {
					switch ( $condition ) {
						case 'contains':
							if ( false !== strpos( $condition_value, ',' ) ) {
								$condition_array_value = explode( ',', $condition_value );
								$condition_array_value = array_map( 'trim', $condition_array_value );
								if ( ! in_array( $product_attr_value, $condition_array_value ) ) {
									$allowed = false;
								} else {
									return true;
								}
							} else {
								if ( ! preg_match( '/' . $condition_value . '/', $product_attr_value ) ) {
									$allowed = false;
								} else {
									return true;
								}
							}

							break;
						case 'containsnot':
							if ( false !== strpos( $condition_value, ',' ) ) {
								$condition_array_value = explode( ',', $condition_value );
								$condition_array_value = array_map( 'trim', $condition_array_value );
								if ( in_array( $product_attr_value, $condition_array_value ) ) {
									$allowed = false;
								} else {
									return true;
								}
							} else {
								if ( preg_match( '/' . $condition_value . '/', $product_attr_value ) ) {
									$allowed = false;
								} else {
									return true;
								}
							}

							break;
						case '=':
							if ( $product_attr_value != $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
						case '!=':
							if ( $product_attr_value == $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
						case '>':
							if ( $product_attr_value <= $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
						case '>=':
							if ( $product_attr_value < $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
						case '<':
							if ( $product_attr_value >= $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
						case '<=':
							if ( $product_attr_value > $condition_value ) {
								$allowed = false;
							} else {
								return true;
							}
							break;
					}
				}
			}

			return $allowed;
		}

		do_action( 'gg_woo_feed_after_allowed_product', $product );

		return $allowed;
	}

	/**
	 * @param $attr_value
	 * @param $plugin_attr
	 * @param $provider
	 * @param $feed_type
	 * @param $product
	 * @return mixed
	 */
	public function custom_attr_value( $attr_value, $plugin_attr, $provider, $feed_type, $product ) {
		switch ( $plugin_attr ) {
			case 'title':
				$attr_value = $this->get_custom_value_title( $attr_value, $product );
				break;

			case 'description':
				$attr_value = $this->get_custom_value_description( $attr_value, $product );
				break;

			case 'brand':
				$attr_value = $this->get_custom_value_brand( $attr_value, $product );
				break;

			case 'condition':
				$attr_value = $this->get_custom_value_condition( $attr_value, $product );
				break;
			// GTIN
			case 'upc':
				$attr_value = $this->get_custom_value_gtin( $attr_value, $product );
				break;
			// MPN
			case 'sku':
				$attr_value = $this->get_custom_value_mpn( $attr_value, $product );
				break;

			case 'google_taxonomy':
				$attr_value = $this->get_custom_value_google_taxonomy( $attr_value, $product );
				break;

			case 'adult':
				$attr_value = $this->get_custom_value_adult( $attr_value, $product );
				break;

			case 'shipping_label':
				$attr_value = $this->get_custom_value_shipping_label( $attr_value, $product );
				break;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_title( $attr_value, $product ) {
		$meta         = Constant::PRODUCT_META_PREFIX . 'custom_title';
		$custom_title = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['title_use_custom'] ) && $custom_title ) {
			$attr_value = $custom_title;
		}

		if ( class_exists( 'WPSEO_Frontend' ) && ( 'on' === $this->config['title_use_yoast'] ) ) {
			$yoas_seo_title = $this->get_yoast_seo_title( $product );
			$attr_value     = $yoas_seo_title ? $yoas_seo_title : $attr_value;
		}

		if ( 'on' === $this->config['title_fix_uppercase'] ) {
			$attr_value = strtolower( $attr_value );
			$attr_value = ucwords( trim( $attr_value ), ' ' );
		}

		if ( 'on' === $this->config['title_remove_cap'] ) {
			$attr_value = strtolower( $attr_value );
		}

		return $attr_value;
	}

	/**
	 * Get Yoast SEO Product Title
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_yoast_seo_title( $product ) {
		$title = '';
		if ( class_exists( 'WPSEO_Frontend' ) ) {
			$title = \WPSEO_Frontend::get_instance()->get_seo_title( get_post( $product->get_id() ) );
		}

		return $title;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_description( $attr_value, $product ) {
		if ( class_exists( 'WPSEO_Frontend' ) && ( 'on' === $this->config['desc_use_yoast'] ) ) {
			$yoas_seo_meta_desc = $this->get_yoast_wpseo_meta_desc( $product );
			if ( $yoas_seo_meta_desc ) {
				return strip_tags( $yoas_seo_meta_desc );
			}
		}

		$meta        = Constant::PRODUCT_META_PREFIX . 'custom_description';
		$custom_desc = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['desc_use_custom'] ) && $custom_desc ) {
			return strip_tags( $custom_desc );
		}

		if ( ( 'on' === $this->config['desc_use_short_description'] ) ) {
			$short_desc = $product->get_short_description();
			if ( $short_desc ) {
				return strip_tags( $short_desc );
			}
		}

		if ( ( 'on' === $this->config['desc_use_description'] ) ) {
			$desc = $product->get_description();
			if ( $desc ) {
				return strip_tags( $desc );
			}
		}

		return strip_tags( $attr_value );
	}

	/**
	 * Get Yoast SEO Product Meta Description
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_yoast_wpseo_meta_desc( $product ) {
		$description = '';
		if ( class_exists( 'WPSEO_Frontend' ) ) {
			$description = wpseo_replace_vars( \WPSEO_Meta::get_value( 'metadesc', $product->get_id() ),
				get_post( $product->get_id() ) );
		}

		return $description;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_brand( $attr_value, $product ) {
		if ( ( 'on' === $this->config['brand_use_cat_custom'] ) ) {
			$custom_term_brand = $this->get_product_term_meta( $product, 'brand' );
			$attr_value        = $custom_term_brand ? $custom_term_brand : $attr_value;
		}

		$meta         = Constant::PRODUCT_META_PREFIX . 'brand';
		$custom_brand = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['brand_use_custom'] ) && $custom_brand ) {
			$attr_value = $custom_brand;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_adult( $attr_value, $product ) {
		if ( ( 'on' === $this->config['adult_use_cat_custom'] ) ) {
			$custom_term_adult = $this->get_product_term_meta( $product, 'adult' );
			$attr_value        = $custom_term_adult ? $custom_term_adult : $attr_value;
		}

		$meta         = Constant::PRODUCT_META_PREFIX . 'adult';
		$custom_adult = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['adult_use_custom'] ) && $custom_adult ) {
			$attr_value = $custom_adult;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_shipping_label( $attr_value, $product ) {
		if ( ( 'on' === $this->config['shipping_label_use_cat_custom'] ) ) {
			$custom_term_shipping_label = $this->get_product_term_meta( $product, 'shipping_label' );
			$attr_value                 = $custom_term_shipping_label ? $custom_term_shipping_label : $attr_value;
		}

		$meta                  = Constant::PRODUCT_META_PREFIX . 'shipping_label';
		$custom_shipping_label = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['shipping_label_use_custom'] ) && $custom_shipping_label ) {
			$attr_value = $custom_shipping_label;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_condition( $attr_value, $product ) {
		$meta             = Constant::PRODUCT_META_PREFIX . 'condition';
		$custom_condition = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['condition_use_custom'] ) && $custom_condition ) {
			$attr_value = $custom_condition;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_gtin( $attr_value, $product ) {
		$meta        = Constant::PRODUCT_META_PREFIX . 'gtin';
		$custom_gtin = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['gtin_use_custom'] ) && $custom_gtin ) {
			$attr_value = $custom_gtin;
		}

		return $attr_value;
	}

	/**
	 * @param string      $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_mpn( $attr_value, $product ) {
		if ( ( 'on' === $this->config['mpn_use_cat_custom'] ) ) {
			$custom_term_mpn = $this->get_product_term_meta( $product, 'mpn' );
			$attr_value      = $custom_term_mpn ? $custom_term_mpn : $attr_value;
		}

		$meta       = Constant::PRODUCT_META_PREFIX . 'mpn';
		$custom_mpn = $this->get_product_meta( $product, $meta );
		if ( ( 'on' === $this->config['mpn_use_custom'] ) && $custom_mpn ) {
			$attr_value = $custom_mpn;
		}

		return $attr_value;
	}

	/**
	 * @param             $attr_value
	 * @param \WC_Product $product
	 * @return mixed
	 */
	public function get_custom_value_google_taxonomy( $attr_value, $product ) {
		if ( ( 'on' === $this->config['google_taxonomy_use_cat_custom'] ) ) {
			$custom_term_gt = $this->get_product_term_google_taxonomy( $product );
			$attr_value     = $custom_term_gt ? $custom_term_gt : $attr_value;
		}

		if ( ( 'on' === $this->config['google_taxonomy_use_custom'] ) ) {
			$meta       = Constant::PRODUCT_META_PREFIX . 'google_taxonomy';
			$custom_gt  = $this->get_product_meta( $product, $meta );
			$attr_value = $custom_gt ? $custom_gt : $attr_value;
		}

		return $attr_value;
	}

	/**
	 * Process feed data.
	 *
	 * @param $product_obj \WC_Product
	 * @param $index       | Product Index
	 *
	 */
	protected function process_for_provider( $product_obj, $index ) {
		$product            = $this->products[ $index ];
		$merchantAttributes = $this->config['mattributes'];

		if ( 'xml' !== $this->config['feed_type'] && in_array( $this->config['provider'], [ 'google', 'facebook' ] ) ) {
			$shipping     = [];
			$tax          = [];
			$installment  = [];
			$s            = 0; // Shipping Index
			$t            = 0; // Tax Index
			$i            = 0; // Installment Index
			$shippingAttr = [
				'shipping_country',
				'shipping_service',
				'shipping_price',
				'shipping_region',
				'tax_country',
				'tax_region',
				'tax_rate',
				'tax_ship',
			];

			foreach ( $this->products[ $this->pi ] as $attribute => $value ) {
				if ( in_array( $attribute, $shippingAttr ) ) {

					if ( 'tax_country' === $attribute ) {
						$t++;
						$tax[ $t ] .= $value . ':';
					} elseif ( 'tax_region' === $attribute ) {
						$tax[ $t ] .= $value . ':';
					} elseif ( 'tax_rate' === $attribute ) {
						$tax[ $t ] .= $value . ':';
					} elseif ( 'tax_ship' === $attribute ) {
						$tax[ $t ] .= $value . ':';
					}

					if ( 'shipping_country' === $attribute ) {
						$s++;
						$shipping[ $s ] .= $value . ':';
					} elseif ( 'shipping_service' === $attribute ) {
						$shipping[ $s ] .= $value . ':';
					} elseif ( 'shipping_price' === $attribute ) {
						$shipping[ $s ] .= $value . ':';
					} elseif ( 'shipping_region' === $attribute ) {
						$shipping[ $s ] .= $value . ':';
					}

					unset( $this->products[ $this->pi ][ $attribute ] );
				}
			}

			foreach ( $shipping as $key => $val ) {
				$this->products[ $this->pi ]['shipping(country:region:service:price)'] = $val;
			}

			foreach ( $tax as $key => $val ) {
				$this->products[ $this->pi ]['tax(country:region:rate:tax_ship)'] = $val;
			}
		}

		if ( 'google' === $this->config['provider'] ) {
			$s        = 0;
			$t        = 0;
			$tax      = '';
			$shipping = '';
			if ( 'xml' === $this->config['feed_type'] ) {
				foreach ( $merchantAttributes as $key => $value ) {

					if ( ! in_array( $value, $this->google_shipping_tax ) ) {
						continue;
					}

					$prefix = $this->config['prefix'][ $key ];
					$suffix = $this->config['suffix'][ $key ];

					if ( 'pattern' === $this->config['type'][ $key ] ) {
						$output = $this->config['default'][ $key ];
					} else {
						$attribute = $this->config['attributes'][ $key ];
						$output    = $this->get_attr_value_by_type( $product_obj, $attribute );
					}

					if ( false !== strpos( $value, 'price' ) || false !== strpos( $value, 'rate' ) ) {
						$suffix = '' . $suffix;
					}

					$output = $prefix . $output . $suffix;

					if ( 'shipping_country' === $value ) {
						if ( 0 == $s ) {
							$shipping .= '<g:shipping>';
							$s        = 1;
						} else {
							$shipping .= '</g:shipping>' . "\n";
							$shipping .= '<g:shipping>';
						}
					} elseif ( ! in_array( 'shipping_country', $merchantAttributes ) && 'shipping_price' == $value ) {
						if ( 0 == $s ) {
							$shipping .= '<g:shipping>';
							$s        = 1;
						} else {
							$shipping .= '</g:shipping>' . "\n";
							$shipping .= '<g:shipping>';
						}
					}

					if ( 'shipping_country' == $value ) {
						$shipping .= '<g:country>' . $output . '</g:country>' . "\n";
					} elseif ( 'shipping_region' == $value ) {
						$shipping .= '<g:region>' . $output . '</g:region>' . "\n";
					} elseif ( 'shipping_service' == $value ) {
						$shipping .= '<g:service>' . $output . '</g:service>' . "\n";
					} elseif ( 'shipping_price' == $value ) {
						$shipping .= '<g:price>' . $output . '</g:price>' . "\n";
					} elseif ( 'tax_country' == $value ) {
						if ( 0 == $t ) {
							$tax .= '<g:tax>';
							$t   = 1;
						} else {
							$tax .= '</g:tax>' . "\n";
							$tax .= '<g:tax>';
						}
						$tax .= '<g:country>' . $output . '</g:country>' . "\n";
					} elseif ( 'tax_region' == $value ) {
						$tax .= '<g:region>' . $output . '</g:region>' . "\n";
					} elseif ( 'tax_rate' == $value ) {
						$tax .= '<g:rate>' . $output . '</g:rate>' . "\n";
					} elseif ( 'tax_ship' == $value ) {
						$tax .= '<g:tax_ship>' . $output . '</g:tax_ship>' . "\n";
					}
				}

				if ( 1 == $s ) {
					$shipping .= '</g:shipping>';
				}
				if ( 1 == $t ) {
					$tax .= '</g:tax>';
				}

				$additional_images = '';
				if ( 'on' === $this->config['include_additional_image_link'] ) {
					$array_images = $this->array_images( $product_obj );
					if ( $array_images ) {
						foreach ( $array_images as $image_key => $image_url ) {
							// Fixes: Limited 10 images.
							if ( $image_key <= 10 ) {
								$additional_images .= '<g:additional_image_link><![CDATA[ ' . $image_url . ' ]]></g:additional_image_link>';
							}
						}
					}
				}

				$this->feed_body .= $shipping;
				$this->feed_body .= $tax;
				$this->feed_body .= $additional_images;
			}

			$identifier      = [ 'brand', 'upc', 'sku', 'mpn', 'gtin' ];
			$countIdentifier = 0;
			if ( ! in_array( 'identifier_exists', $merchantAttributes ) ) {
				if ( count( array_intersect_key( array_flip( $identifier ), $product ) ) >= 2 ) {
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
				}

				if ( 'xml' === $this->config['feed_type'] ) {
					if ( $countIdentifier >= 2 ) {
						$this->feed_body .= "<g:identifier_exists>yes</g:identifier_exists>";
					} else {
						$this->feed_body .= "<g:identifier_exists>no</g:identifier_exists>";
					}
				} else {
					if ( $countIdentifier >= 2 ) {
						$this->products[ $this->pi ]['identifier exists'] = "yes";
					} else {
						$this->products[ $this->pi ]['identifier exists'] = "no";
					}
				}
			}
		}
	}

	/**
	 * Generate TXT Feed Header
	 *
	 * @return string
	 */
	protected function process_txt_feed_header() {
		// Set Delimiter
		if ( 'tab' === $this->config['delimiter'] ) {
			$this->delimiter = "\t";
		} else {
			$this->delimiter = $this->config['delimiter'];
		}

		// Set Enclosure
		if ( ! empty( $this->config['enclosure'] ) ) {
			$this->enclosure = $this->config['enclosure'];
			if ( 'double' == $this->enclosure ) {
				$this->enclosure = '"';
			} elseif ( 'single' == $this->enclosure ) {
				$this->enclosure = "'";
			} else {
				$this->enclosure = '';
			}
		} else {
			$this->enclosure = '';
		}

		$eol = PHP_EOL;

		$product           = $this->products[ $this->pi ];
		$headers           = array_keys( $product );
		$this->feed_header .= $this->enclosure . implode( "$this->enclosure$this->delimiter$this->enclosure",
				$headers ) . $this->enclosure . $eol;

		return $this->feed_header;
	}

	/**
	 * Generate TXT Feed Body
	 *
	 * @return string
	 */
	protected function process_txt_feed_body() {
		$productInfo = array_values( $this->products[ $this->pi ] );
		$eol         = PHP_EOL;

		$this->feed_body .= $this->enclosure . implode( "$this->enclosure$this->delimiter$this->enclosure",
				$productInfo ) . $this->enclosure . $eol;

		return $this->feed_body;
	}

	/**
	 * Generate CSV Feed Header
	 *
	 * @return array
	 */
	protected function process_csv_feed_header() {
		// Set Delimiter
		if ( 'tab' === $this->config['delimiter'] ) {
			$this->delimiter = "\t";
		} else {
			$this->delimiter = $this->config['delimiter'];
		}

		// Set Enclosure
		if ( ! empty( $this->config['enclosure'] ) ) {
			$this->enclosure = $this->config['enclosure'];
			if ( 'double' == $this->enclosure ) {
				$this->enclosure = '"';
			} elseif ( 'single' == $this->enclosure ) {
				$this->enclosure = "'";
			} else {
				$this->enclosure = '';
			}
		} else {
			$this->enclosure = '';
		}

		$product           = $this->products[ $this->pi ];
		$this->feed_header = array_keys( $product );

		return $this->feed_header;
	}

	/**
	 * Generate CSV Feed Body
	 *
	 * @return array
	 */
	protected function process_csv_feed_body() {
		$product           = $this->products[ $this->pi ];
		$this->feed_body[] = array_values( $product );

		return $this->feed_body;
	}

	/**
	 * Make XML feed header
	 *
	 * @return string
	 */
	protected function process_xml_feed_header() {
		$wrapper = $this->config['items_wrap'];

		$output = '<?xml version="1.0" encoding="UTF-8" ?>';
		$output .= "\n";
		$output .= '<' . $wrapper . '>';

		return $output;
	}

	/**
	 * Make XML feed header
	 *
	 * @return string
	 */
	protected function process_xml_feed_footer() {
		$wrapper = $this->config['items_wrap'];
		$footer  = "\n";
		$footer  .= "</$wrapper>";

		return $footer;
	}

	/**
	 * Process string for TXT CSV Feed
	 *
	 * @param $string
	 *
	 * @return mixed|string
	 */
	protected function process_string_for_txt( $string ) {
		if ( ! empty( $string ) ) {
			$string = html_entity_decode( $string, ENT_HTML401 | ENT_QUOTES );

			if ( stristr( $string, '"' ) ) {
				$string = str_replace( '"', '""', $string );
			}
			$string = str_replace( [ "\n", "\r", "\t" ], ' ', $string );
			$string = trim( $string );
			$string = stripslashes( $string );

			return $string;
		} elseif ( '0' == $string ) {
			return '0';
		} else {
			return '';
		}
	}

	/**
	 * Process string for CSV
	 *
	 * @param $string
	 *
	 * @return mixed|string
	 */
	protected function process_string_for_csv( $string ) {
		if ( ! empty( $string ) ) {
			$string = str_replace( [ "\n", "\r" ], ' ', $string );
			$string = trim( $string );
			$string = stripslashes( $string );

			return $string;
		} elseif ( '0' == $string ) {
			return '0';
		} else {
			return '';
		}
	}

	/**
	 * Get product attribute value by type
	 *
	 * @param $product  \WC_Product
	 * @param $attribute
	 *
	 * @return mixed|string
	 */
	public function get_attr_value_by_type( $product, $attribute ) {
		if ( method_exists( $this, $attribute ) ) {
			$output = $this->$attribute( $product );
		} elseif ( $this->is_attr_key( $attribute ) ) {
			$output = $this->get_product_attribute( $product, $attribute );
		} elseif ( $this->is_meta_key( $attribute ) ) {
			$output = $this->get_product_meta( $product, $attribute );
		} elseif ( $this->is_custom_field_key( $attribute ) ) {
			$output = $this->get_product_custom_field( $product, $attribute );
		} elseif ( 'image_' === substr( $attribute, 0, 6 ) ) {
			$image_key = explode( '_', $attribute );
			if ( ! isset( $image_key[1] ) || ( isset( $image_key[1] ) && ( empty( $image_key[1] ) || ! is_numeric( $image_key[1] ) ) ) ) {
				$image_key[1] = '';
			}

			$output = $this->images( $product, $image_key[1] );
		} else {
			$output = $attribute;
		}

		if ( is_array( $output ) ) {
			$output = wp_json_encode( $output );
		}

		$output = apply_filters( "gg_woo_feed_get_{$this->config['provider']}_{$attribute}_attribute", $output, $product, $this->config );

		return apply_filters( "gg_woo_feed_get_{$attribute}_attribute", $output, $product, $this->config );
	}

	/**
	 * Get Product Id
	 *
	 * @param \WC_Product $product
	 *
	 * @return int
	 */
	protected function id( $product ) {
		return $product->get_id();
	}

	/**
	 * Get Product name
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function title( $product ) {
		$title = $product->get_title();

		if ( 'on' === $this->config['title_add_variation'] ) {
			if ( $product->is_type( 'variable' ) && $product->has_child() ) {
				if ( 'price' === $this->config['feed_variable_title'] ) {
					// For default variation.
					if ( 'first' === $this->config['feed_variable_price'] ) {
						$default_attributes = $this->get_default_attributes( $product );
						$variation_id       = $this->find_matching_product_variation( $product, $default_attributes );

						if ( $variation_id ) {
							$product = wc_get_product( $variation_id );

							$title = $product->get_name();
						}
					} else {
						$visible_children = $product->get_visible_children();
						if ( is_array( $visible_children ) && ( count( $visible_children ) > 0 ) ) {
							$prices = [];
							foreach ( $visible_children as $key => $child ) {
								$price            = get_post_meta( $child, '_price', true );
								$prices[ $child ] = $price;
							}

							if ( $prices ) {
								if ( 'smallest' === $this->config['feed_variable_price'] ) {
									$product_id = array_keys( $prices, min( $prices ) );
									if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
										$product = wc_get_product( $product_id[0] );

										$title = $product->get_name();
									}
								}

								if ( 'biggest' === $this->config['feed_variable_price'] ) {
									$product_id = array_keys( $prices, max( $prices ) );
									if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
										$product = wc_get_product( $product_id[0] );

										$title = $product->get_name();
									}
								}
							}
						}
					}
				} elseif ( 'all' === $this->config['feed_variable_title'] ) {
					$attributes = $product->get_attributes();
					$pa         = [];
					if ( $attributes ) {
						foreach ( $attributes as $attribute => $product_attribute ) {
							$attribute = str_replace( [ Constant::PRODUCT_ATTR_PREFIX, 'pa_' ], '', $attribute );
							$pa[]      = ucfirst( $attribute ) . ': ' . $product->get_attribute( $attribute );
						}
					}

					if ( $pa ) {
						$extra = implode( ' - ', $pa );
						$title = $product->get_title() . ' - ' . $extra;
					}
				}
			} else {
				$title = $product->get_name();
			}
		}

		if ( 'on' === $this->config['title_fix_uppercase'] ) {
			$title = strtolower( $title );
			$title = ucwords( trim( $title ), ' ' );
		}

		if ( 'on' === $this->config['title_remove_cap'] ) {
			$title = strtolower( $title );
		}

		return $title;
	}

	/**
	 * Get Product description
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function description( $product ) {

		$description = $product->get_description();

		// Get Variation Description
		if ( $product->is_type( 'variation' ) && empty( $description ) ) {
			$parent      = wc_get_product( $product->get_parent_id() );
			$description = $parent->get_description();
		}
		$description = $this->escape_content( $description );

		// Add variations attributes after description to prevent Facebook error
		if ( 'facebook' === $this->config['provider'] ) {
			$variationInfo = explode( "-", $product->get_name() );
			if ( isset( $variationInfo[1] ) ) {
				$extension = $variationInfo[1];
			} else {
				$extension = $product->get_id();
			}
			$description .= ' ' . $extension;
		}

		return $description;
	}

	/**
	 * Get Product Short Description
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function short_description( $product ) {
		$short_description = $product->get_short_description();

		if ( $product->is_type( 'variation' ) && empty( $short_description ) ) {
			$parent            = wc_get_product( $product->get_parent_id() );
			$short_description = $parent->get_short_description();
		}

		return $this->escape_content( $short_description );
	}


	/**
	 * Remove shortcode.
	 *
	 * @param $content
	 *
	 * @return mixed|string
	 */
	protected function escape_content( $content ) {
		if ( empty( $content ) ) {
			return '';
		}

		if ( class_exists( 'ET_Builder_Module' ) || defined( 'ET_BUILDER_PLUGIN_VERSION' ) ) {
			$content = preg_replace( '/\[\/?et_pb.*?\]/', '', $content );
		}

		$content = do_shortcode( $content );
		$content = gg_woo_feed_strip_invalid_xml( $content );

		return strip_shortcodes( $content );
	}

	/**
	 * Get Product Categories
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function product_type( $product ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		$separator = apply_filters( 'gg_woo_feed_product_type_separator', '>', $this->config, $product );

		return wp_strip_all_tags( wc_get_product_category_list( $id, $separator, '' ) );
	}

	/**
	 * Get Product URL
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function link( $product ) {
		$utm = $this->config['campaign_parameters'];
		if ( ! empty( $utm['utm_source'] ) && ! empty( $utm['utm_medium'] ) && ! empty( $utm['utm_campaign'] ) ) {
			$utm = [
				'utm_source'   => $utm['utm_source'],
				'utm_medium'   => $utm['utm_medium'],
				'utm_campaign' => $utm['utm_campaign'],
				'utm_term'     => $utm['utm_term'],
				'utm_content'  => $utm['utm_content'],
			];

			return add_query_arg( array_filter( $utm ), $product->get_permalink() );
		}

		return $product->get_permalink();
	}

	/**
	 * Get External Product URL
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function ex_link( $product ) {
		if ( $product->is_type( 'external' ) ) {
			return $product->get_product_url();
		}

		return '';
	}

	/**
	 * Get Product Image
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function image( $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ),
				'full' );
			if ( has_post_thumbnail( $product->get_id() ) && ! empty( $get_image[0] ) ) :
				$image = $get_image[0];
			else :
				$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_parent_id() ),
					'full' );
				$image     = $get_image[0];
			endif;
		} else {
			if ( $product->is_type( 'variable' ) && $product->has_child() && ( 'price' === $this->config['feed_variable_image'] ) ) {

				// For default variation.
				if ( 'first' === $this->config['feed_variable_price'] ) {
					$default_attributes = $this->get_default_attributes( $product );
					$variation_id       = $this->find_matching_product_variation( $product, $default_attributes );

					if ( $variation_id ) {
						$product = wc_get_product( $variation_id );

						$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ),
							'full' );
						if ( has_post_thumbnail( $product->get_id() ) && ! empty( $get_image[0] ) ) :
							$image = $get_image[0];
						else :
							$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_parent_id() ),
								'full' );
							$image     = $get_image[0];
						endif;

						return $image;
					}
				}

				$visible_children = $product->get_visible_children();
				if ( is_array( $visible_children ) && ( count( $visible_children ) > 0 ) ) {
					$prices = [];
					foreach ( $visible_children as $key => $child ) {
						$price            = get_post_meta( $child, '_price', true );
						$prices[ $child ] = $price;
					}

					if ( $prices ) {
						if ( 'smallest' === $this->config['feed_variable_price'] ) {
							$product_id = array_keys( $prices, min( $prices ) );
							if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
								$product = wc_get_product( $product_id[0] );

								$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ),
									'full' );
								if ( has_post_thumbnail( $product->get_id() ) && ! empty( $get_image[0] ) ) :
									$image = $get_image[0];
								else :
									$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_parent_id() ),
										'full' );
									$image     = $get_image[0];
								endif;

								return $image;
							}
						}

						if ( 'biggest' === $this->config['feed_variable_price'] ) {
							$product_id = array_keys( $prices, max( $prices ) );
							if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
								$product = wc_get_product( $product_id[0] );

								$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ),
									'full' );
								if ( has_post_thumbnail( $product->get_id() ) && ! empty( $get_image[0] ) ) :
									$image = $get_image[0];
								else :
									$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_parent_id() ),
										'full' );
									$image     = $get_image[0];
								endif;

								return $image;
							}
						}
					}
				}
			} else {
				if ( has_post_thumbnail( $product->get_id() ) ) :
					$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ),
						'full' );
					$image     = $get_image[0];
				else :
					$image = ( wp_get_attachment_url( $product->get_id() ) );
				endif;
			}
		}

		return $image;
	}

	/**
	 * Get Product Featured Image
	 *
	 * @param \WC_Product $product Product Object.
	 *
	 * @return mixed
	 */
	protected function feature_image( $product ) {
		return $this->image( $product );
	}

	/**
	 * Get Comma Separated Product Images
	 *
	 * @param \WC_Product $product        Product Object.
	 * @param string      $additional_img Specific Additional Image.
	 *
	 * @return string
	 */
	protected function images( $product, $additional_img = '' ) {
		if ( $product->is_type( 'variation' ) ) {
			$img_urls = $this->get_product_gallery( wc_get_product( $product->get_parent_id() ) );
		} else {
			$img_urls = $this->get_product_gallery( $product );
		}

		// Return Specific Additional Image URL
		if ( '' != $additional_img ) {
			if ( array_key_exists( $additional_img, $img_urls ) ) {
				return $img_urls[ $additional_img ];
			}

			return '';
		}

		return implode( ',', array_filter( $img_urls ) );
	}

	/**
	 * Get Comma Separated Product Images
	 *
	 * @param \WC_Product $product        Product Object.
	 * @param string      $additional_img Specific Additional Image.
	 *
	 * @return mixed
	 */
	protected function array_images( $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$img_urls = $this->get_product_gallery( wc_get_product( $product->get_parent_id() ) );
		} else {
			$img_urls = $this->get_product_gallery( $product );
		}

		return $img_urls;
	}

	/**
	 * Get product gallery.
	 *
	 * @param \WC_Product $product
	 *
	 * @return array
	 */
	protected function get_product_gallery( $product ) {
		$attachment_ids = $product->get_gallery_image_ids();
		$img_urls       = [];
		if ( $attachment_ids && is_array( $attachment_ids ) ) {
			$array_key = 1;
			foreach ( $attachment_ids as $attachment_id ) {
				$attachment_src         = wp_get_attachment_image_src( $attachment_id, 'full' );
				$img_urls[ $array_key ] = $attachment_src[0];
				$array_key++;
			}
		}

		return $img_urls;
	}

	/**
	 * Get Product Condition
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function condition( $product ) {
		return apply_filters( 'gg_woo_feed_product_condition', 'new', $product );
	}

	/**
	 *  Get Product Type
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function type( $product ) {
		return $product->get_type();
	}

	/**
	 *  Get Product is a bundle product or not
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function is_bundle( $product ) {
		if ( $product->is_type( 'bundle' ) || $product->is_type( 'yith_bundle' ) ) {
			return 'yes';
		}

		return 'no';
	}

	/**
	 *  Get Product is a multi-pack product or not
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function multipack( $product ) {
		$multi_pack = '';
		if ( $product->is_type( 'grouped' ) ) {
			$multi_pack = ( ! empty( $product->get_children() ) ) ? count( $product->get_children() ) : '';
		}

		return $multi_pack;
	}

	/**
	 *  Get Product visibility status
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function visibility( $product ) {
		return $product->get_catalog_visibility();
	}

	/**
	 *  Get Product Total Rating
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function rating_total( $product ) {
		return $product->get_rating_count();
	}

	/**
	 * Get Product average rating
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function rating_average( $product ) {
		return $product->get_average_rating();
	}

	/**
	 * Get Product tags
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function tags( $product ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		$tags = get_the_term_list( $id, 'product_tag', '', ',', '' );

		if ( ! empty( $tags ) ) {
			return wp_strip_all_tags( $tags );
		}

		return '';
	}

	/**
	 * Get Product Parent Id
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function item_group_id( $product ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		return $id;
	}

	/**
	 * Get Product SKU
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function sku( $product ) {
		return $product->get_sku();
	}

	/**
	 * Get Product Parent SKU
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function parent_sku( $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$id     = $product->get_parent_id();
			$parent = wc_get_product( $id );

			return $parent->get_sku();
		}

		return $product->get_sku();
	}

	/**
	 * Get Product Availability Status
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function availability( $product ) {
		$status = $product->get_stock_status();
		if ( 'instock' === $status ) {
			return 'in stock';
		}

		if ( 'outofstock' === $status ) {
			return 'out of stock';
		}

		if ( 'onbackorder' === $status ) {
			return 'on backorder';
		}

		return 'in stock';
	}

	/**
	 * Get Product Quantity
	 *
	 * @param \WC_Product|\WC_Product_Variable $product
	 *
	 * @return mixed
	 */
	protected function quantity( $product ) {
		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			$visible_children = $product->get_visible_children();
			$qty              = [];
			foreach ( $visible_children as $key => $child ) {
				$child_qty = get_post_meta( $child, '_stock', true );
				$qty[]     = (int) $child_qty + 0;
			}

			if ( isset( $this->config['variable_quantity'] ) ) {
				$va_qty = $this->config['variable_quantity'];
				if ( 'max' === $va_qty ) {
					return max( $qty );
				}

				if ( 'min' === $va_qty ) {
					return min( $qty );
				}

				if ( 'sum' === $va_qty ) {
					return array_sum( $qty );
				}

				if ( 'first' === $va_qty ) {
					return ( (int) $qty[0] );
				}

				return array_sum( $qty );
			}
		}

		return $product->get_stock_quantity();
	}

	/**
	 * Get Product Sale Price Start Date
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function sale_price_sdate( $product ) {
		$start_date = $product->get_date_on_sale_from();
		if ( $start_date && is_object( $start_date ) ) {
			return $start_date->date_i18n();
		}

		return '';
	}

	/**
	 * Get Product Sale Price End Date
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function sale_price_edate( $product ) {
		$end_date = $product->get_date_on_sale_to();
		if ( $end_date && is_object( $end_date ) ) {
			return $end_date->date_i18n();
		}

		return '';
	}

	/**
	 * Get Product Price
	 *
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Grouped $product Product Object.
	 *
	 * @return mixed
	 */
	protected function price( $product ) {
		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			if ( 'smallest' === $this->config['feed_variable_price'] ) {
				return $product->get_variation_regular_price( 'min' );
			}

			if ( 'biggest' === $this->config['feed_variable_price'] ) {
				return $product->get_variation_regular_price( 'max' );
			}

			// For default variation.
			if ( 'first' === $this->config['feed_variable_price'] ) {
				$default_attributes = $this->get_default_attributes( $product );
				$variation_id       = $this->find_matching_product_variation( $product, $default_attributes );

				if ( $variation_id ) {
					$product = wc_get_product( $variation_id );

					return $product->get_regular_price();
				}
			}

			return $this->get_variable_product_price( $product, 'regular_price' );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'regular' );
		}

		return $product->get_regular_price();
	}

	/**
	 * Get current price
	 *
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Grouped $product
	 *
	 * @return int|float|double|mixed
	 */
	protected function current_price( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_price( $product, 'price' );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'current' );
		}

		return $product->get_price();
	}

	/**
	 * Get Product Sale Price
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function sale_price( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_price( $product, 'sale_price' );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'sale' );
		}

		$price = $product->get_sale_price();

		return $price > 0 ? $price : '';
	}

	/**
	 * Get Product Regular Price with Tax
	 *
	 * @param \WC_Product $product Product Object.
	 *
	 * @return mixed
	 */
	protected function price_with_tax( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_price( $product, 'regular_price', true );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'regular', true );
		}

		$price = $this->price( $product );

		return ( $product->is_taxable() && ! empty( $price ) ) ? $this->get_price_with_tax( $product,
			$price ) : $price;
	}

	/**
	 * Get Product Regular Price with Tax
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function current_price_with_tax( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_price( $product, 'current_price', true );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'current', true );
		}

		$price = $this->current_price( $product );

		return ( $product->is_taxable() && ! empty( $price ) ) ? $this->get_price_with_tax( $product,
			$price ) : $price;
	}

	/**
	 * Get Product Regular Price with Tax
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function sale_price_with_tax( $product ) {
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_price( $product, 'sale_price', true );
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $this->get_group_product_price( $product, 'sale', true );
		}

		$price = $this->sale_price( $product );
		if ( $product->is_taxable() && ! empty( $price ) ) {
			$price = $this->get_price_with_tax( $product, $price );
		}

		return $price > 0 ? $price : '';
	}

	/**
	 * Get total price of grouped product
	 *
	 * @param \WC_Product_Grouped $grouped
	 * @param string              $type
	 * @param bool                $tax
	 *
	 * @return int|string
	 */
	protected function get_group_product_price( $grouped, $type, $tax = false ) {
		$groupProductIds = $grouped->get_children();
		$sum             = 0;
		if ( ! empty( $groupProductIds ) ) {
			foreach ( $groupProductIds as $id ) {
				$product = wc_get_product( $id );

				if ( ! is_object( $product ) ) {
					continue;
				}

				if ( $tax ) {
					if ( 'regular' === $type ) {
						$regularPrice = $this->price_with_tax( $product );
						$sum          += (float) $regularPrice;
					} elseif ( 'current' === $type ) {
						$currentPrice = $this->current_price_with_tax( $product );
						$sum          += (float) $currentPrice;
					} else {
						$salePrice = $this->sale_price_with_tax( $product );
						$sum       += (float) $salePrice;
					}
				} else {
					if ( 'regular' === $type ) {
						$regularPrice = $this->price( $product );
						$sum          += (float) $regularPrice;
					} elseif ( 'current' === $type ) {
						$currentPrice = $this->current_price( $product );
						$sum          += (float) $currentPrice;
					} else {
						$salePrice = $this->sale_price( $product );
						$sum       += (float) $salePrice;
					}
				}
			}
		}

		if ( 'sale' === $type ) {
			$sum = $sum > 0 ? $sum : '';
		}

		return $sum;
	}

	/**
	 * Get total price of variable product
	 *
	 * @param \WC_Product_Variable $variable
	 * @param string               $type regular_price, sale_price & current_price
	 * @param bool                 $tax  calculate tax
	 *
	 * @return int|string
	 */
	protected function get_variable_product_price( $variable, $type, $tax = false ) {
		if ( 'regular_price' === $type ) {
			$price = $variable->get_variation_regular_price();
		} elseif ( 'sale_price' === $type ) {
			$price = $variable->get_variation_sale_price();
		} else {
			$price = $variable->get_variation_price();
		}

		if ( true === $tax && $variable->is_taxable() ) {
			$price = $this->get_price_with_tax( $variable, $price );
		}
		if ( 'sale_price' !== $type ) {
			$price = $price > 0 ? $price : '';
		}

		return $price;
	}

	/**
	 * Return product price with tax
	 *
	 * @param \WC_Product $product Product.
	 * @param float       $price   Price.
	 *
	 * @return float|string
	 */
	protected function get_price_with_tax( $product, $price ) {
		return wc_get_price_including_tax( $product, [ 'price' => $price ] );
	}

	/**
	 * Get Product Weight
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function weight( $product ) {
		return $product->get_weight();
	}

	/**
	 * Get Product Width
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function width( $product ) {
		return $product->get_width();
	}

	/**
	 * Get Product Height
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function height( $product ) {
		return $product->get_height();
	}

	/**
	 * Get Product Length
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function length( $product ) {
		return $product->get_length();
	}

	/**
	 * Get Product Shipping Class
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function shipping_class( $product ) {
		return $product->get_shipping_class();
	}

	/**
	 * Get Product Author Name
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function author_name( $product ) {
		$post = get_post( $product->get_id() );

		return get_the_author_meta( 'user_login', $post->post_author );
	}

	/**
	 * Get Product Author Email
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function author_email( $product ) {
		$post = get_post( $product->get_id() );

		return get_the_author_meta( 'user_email', $post->post_author );
	}

	/**
	 * Get Product Created Date
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function date_created( $product ) {
		return gmdate( 'Y-m-d', strtotime( $product->get_date_created() ) );
	}

	/**
	 * Get Product Last Updated Date
	 *
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	protected function date_updated( $product ) {
		return gmdate( 'Y-m-d', strtotime( $product->get_date_modified() ) );
	}

	/**
	 * Get Product Sale Price Effected Date for Google Shopping
	 *
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	protected function sale_price_effective_date( $product ) {
		$effective_date = '';
		$from           = $this->sale_price_sdate( $product );
		$to             = $this->sale_price_edate( $product );
		if ( ! empty( $from ) && ! empty( $to ) ) {
			$from = gmdate( 'c', strtotime( $from ) );
			$to   = gmdate( 'c', strtotime( $to ) );

			$effective_date = $from . '/' . $to;
		}

		return $effective_date;
	}

	/**
	 * Ger Product Attribute
	 *
	 * @param \WC_Product|\WC_Product_Variable $product
	 * @param string                           $attribute
	 * @return string
	 */
	protected function get_product_attribute( $product, $attribute ) {
		$attribute = str_replace( [ Constant::PRODUCT_ATTR_PREFIX, 'pa_' ], '', $attribute );

		if ( $product->is_type( 'variable' ) && $product->has_child() && ( 'price' === $this->config['feed_variable_attr'] ) ) {

			// For default variation.
			if ( 'first' === $this->config['feed_variable_price'] ) {
				$default_attributes = $this->get_default_attributes( $product );
				$variation_id       = $this->find_matching_product_variation( $product, $default_attributes );

				if ( $variation_id ) {
					$product = wc_get_product( $variation_id );

					return $product->get_attribute( $attribute );
				}
			}

			$visible_children = $product->get_visible_children();
			if ( is_array( $visible_children ) && ( count( $visible_children ) > 0 ) ) {
				$prices = [];
				foreach ( $visible_children as $key => $child ) {
					$price            = get_post_meta( $child, '_price', true );
					$prices[ $child ] = $price;
				}

				if ( $prices ) {
					if ( 'smallest' === $this->config['feed_variable_price'] ) {
						$product_id = array_keys( $prices, min( $prices ) );
						if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
							$product = wc_get_product( $product_id[0] );

							return $product->get_attribute( $attribute );
						}
					}

					if ( 'biggest' === $this->config['feed_variable_price'] ) {
						$product_id = array_keys( $prices, max( $prices ) );
						if ( $product_id && isset( $product_id[0] ) && $product_id[0] ) {
							$product = wc_get_product( $product_id[0] );

							return $product->get_attribute( $attribute );
						}
					}
				}
			}
		}

		return $product->get_attribute( $attribute );
	}

	/**
	 * Find matching product variation
	 *
	 * @param \WC_Product $product
	 * @param array       $attributes
	 * @return int Matching variation ID or 0.
	 */
	public function find_matching_product_variation( $product, $attributes ) {
		if ( ! $attributes ) {
			return 0;
		}

		foreach ( $attributes as $key => $value ) {
			if ( strpos( $key, 'attribute_' ) === 0 ) {
				continue;
			}

			unset( $attributes[ $key ] );
			$attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
		}

		if ( class_exists( 'WC_Data_Store' ) ) {
			$data_store = \WC_Data_Store::load( 'product' );

			return $data_store->find_matching_product_variation( $product, $attributes );
		}

		return $product->get_matching_variation( $attributes );
	}

	/**
	 * Get variation default attributes
	 *
	 * @param \WC_Product $product
	 * @return array
	 */
	public function get_default_attributes( $product ) {
		if ( method_exists( $product, 'get_default_attributes' ) ) {
			return $product->get_default_attributes();
		}

		return $product->get_variation_default_attributes();
	}

	/**
	 * Get product meta value.
	 *
	 * @param \WC_Product $product
	 * @param string      $meta post meta key
	 *
	 * @return mixed
	 */
	public function get_product_meta( $product, $meta ) {
		$value = get_post_meta( $product->get_id(), $meta, true );
		// if empty get meta value of parent post
		if ( '' == $value && $product->get_parent_id() ) {
			$value = get_post_meta( $product->get_parent_id(), $meta, true );
		}

		return $value;
	}

	/**
	 * Get product custom field meta value.
	 *
	 * @param \WC_Product $product
	 * @param string      $meta post meta key
	 *
	 * @return mixed
	 */
	public function get_product_custom_field( $product, $meta ) {
		$meta  = str_replace( Constant::PRODUCT_CUSTOM_FIELD_PREFIX, '', $meta );
		$value = get_post_meta( $product->get_id(), $meta, true );
		// if empty get meta value of parent post
		if ( '' == $value && $product->get_parent_id() ) {
			$value = get_post_meta( $product->get_parent_id(), $meta, true );
		}

		return $value;
	}

	/**
	 * Get Taxonomy
	 *
	 * @param \WC_Product $product
	 * @param string      $taxonomy
	 *
	 * @return string
	 */
	public function get_product_term_google_taxonomy( $product ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		try {
			$terms = get_the_terms( $id, 'product_cat' );

			if ( is_wp_error( $terms ) || ! $terms ) {
				return '';
			}

			foreach ( $terms as $term ) {
				$google_taxonomy = get_term_meta( $term->term_id, Constant::PRODUCT_TAX_PREFIX . 'google_taxonomy', true );
				if ( $google_taxonomy ) {
					return $google_taxonomy;
				}
			}
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Get Taxonomy
	 *
	 * @param \WC_Product $product
	 * @param string      $taxonomy
	 *
	 * @return string
	 */
	public function get_product_term_meta( $product, $meta_key, $taxonomy = 'product_cat' ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		try {
			$terms = get_the_terms( $id, $taxonomy );

			if ( is_wp_error( $terms ) || ! $terms ) {
				return '';
			}

			foreach ( $terms as $term ) {
				$term_value = get_term_meta( $term->term_id, Constant::PRODUCT_TAX_PREFIX . $meta_key, true );
				if ( $term_value ) {
					return $term_value;
				}
			}

			return '';
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Get Taxonomy
	 *
	 * @param \WC_Product $product
	 * @param string      $taxonomy
	 *
	 * @return string
	 */
	public function get_product_taxonomy( $product, $taxonomy ) {
		$id = $product->get_id();
		if ( $product->is_type( 'variation' ) ) {
			$id = $product->get_parent_id();
		}

		$separator = apply_filters( 'gg_woo_feed_product_taxonomy_term_list_separator', ',', $this->config, $product );

		return wp_strip_all_tags( get_the_term_list( $id, $taxonomy, '', $separator, '' ) );
	}

	/**
	 * Format price value
	 *
	 * @param string $name          Attribute Name
	 * @param int    $conditionName condition
	 * @param int    $result        price
	 *
	 * @return mixed
	 */
	protected function price_format( $name, $conditionName, $result ) {
		$plus    = "+";
		$minus   = "-";
		$percent = "%";

		if ( strpos( $name, 'price' ) !== false ) {
			if ( strpos( $result, $plus ) !== false && strpos( $result, $percent ) !== false ) {
				$result = str_replace( [ "+", "%" ], '', $result );
				if ( is_numeric( $result ) ) {
					$result = $conditionName + ( ( $conditionName * $result ) / 100 );
				}
			} elseif ( strpos( $result, $minus ) !== false && strpos( $result, $percent ) !== false ) {
				$result = str_replace( [ "-", "%" ], '', $result );
				if ( is_numeric( $result ) ) {
					$result = $conditionName - ( ( $conditionName * $result ) / 100 );
				}
			} elseif ( strpos( $result, $plus ) !== false ) {
				$result = str_replace( "+", "", $result );
				if ( is_numeric( $result ) ) {
					$result = ( $conditionName + $result );
				}
			} elseif ( strpos( $result, $minus ) !== false ) {
				$result = str_replace( "-", "", $result );
				if ( is_numeric( $result ) ) {
					$result = $conditionName - $result;
				}
			}
		}

		return $result;
	}

	/**
	 * Format output According to Output Type config
	 *
	 * @param string      $output
	 * @param array       $output_types
	 * @param \WC_Product $product
	 * @param string      $productAttribute
	 *
	 * @return float|int|string
	 */
	protected function format_output( $output, $output_types, $product, $productAttribute ) {
		if ( ! empty( $output_types ) && is_array( $output_types ) ) {
			if ( in_array( 2, $output_types ) ) {
				$output = wp_strip_all_tags( html_entity_decode( $output ) );
			}

			if ( in_array( 3, $output_types ) ) {
				$output = utf8_encode( $output );
			}

			if ( in_array( 4, $output_types ) ) { // htmlentities
				$output = htmlentities( $output, ENT_QUOTES, 'UTF-8' );
			}

			if ( in_array( 5, $output_types ) ) {
				$output = (int) $output;
			}

			if ( in_array( 6, $output_types ) ) {
				if ( ! empty( $output ) && $output > 0 ) {
					$output = (float) $output;
					$output = number_format( $output, 2, '.', '' );
				}
			}

			if ( in_array( 7, $output_types ) ) {
				$output = trim( $output );
				$output = preg_replace( '!\s+!', ' ', $output );
			}

			if ( in_array( 9, $output_types ) ) {
				$output = gg_woo_feed_strip_invalid_xml( $output );
			}

			if ( in_array( 10, $output_types ) ) {
				$output = $this->escape_content( $output );
			}

			if ( in_array( 8, $output_types ) ) { // Add CDATA
				$output = '<![CDATA[' . $output . ']]>';
			}
		}

		return $output;
	}

	/**
	 * Add Prefix and Suffix with attribute value
	 *
	 * @param $output
	 * @param $prefix
	 * @param $suffix
	 * @param $attribute
	 *
	 * @return string
	 */
	protected function process_prefix_suffix( $output, $prefix, $suffix, $attribute = '' ) {
		if ( '' == $output ) {
			return $output;
		}

		// Add Prefix before Output
		if ( '' != $prefix ) {
			$output = "$prefix" . $output;
		}

		// Add Suffix after Output
		if ( '' !== $suffix ) {
			if (
				'price' === $attribute
				|| 'sale_price' === $attribute
				|| 'current_price' === $attribute
				|| 'price_with_tax' === $attribute
				|| 'current_price_with_tax' === $attribute
				|| 'sale_price_with_tax' === $attribute
				|| 'shipping_price' === $attribute
				|| 'tax_rate' === $attribute
			) { // Add space before suffix if attribute contain price.
				$output = $output . ' ' . $suffix;
			} elseif ( substr( $output, 0, 4 ) === 'http' ) {
				$output = gg_woo_feed_make_url_with_parameter( $output, $suffix );
			} else {
				$output = $output . "$suffix";
			}
		}

		return "$output";
	}

	/**
	 * Is meta key.
	 *
	 * @param $attribute
	 * @return bool
	 */
	protected function is_meta_key( $attribute ) {
		return ( false !== strpos( $attribute, Constant::PRODUCT_META_PREFIX ) );
	}

	/**
	 * Is meta key.
	 *
	 * @param $attribute
	 * @return bool
	 */
	protected function is_custom_field_key( $attribute ) {
		return ( false !== strpos( $attribute, Constant::PRODUCT_CUSTOM_FIELD_PREFIX ) );
	}

	/**
	 * Is attribute key.
	 *
	 * @param $attribute
	 * @return bool
	 */
	protected function is_attr_key( $attribute ) {
		return ( false !== strpos( $attribute, Constant::PRODUCT_ATTR_PREFIX ) );
	}

	/**
	 * Return filter_relationship.
	 *
	 * @return string
	 */
	protected function get_filter_relationship() {
		$filter_relationship = $this->config['filter_relationship'];
		if ( ! isset( $filter_relationship ) ) {
			return 'and';
		}

		if ( ! in_array( $filter_relationship, [ 'and', 'or' ] ) ) {
			return 'and';
		}

		return $filter_relationship;
	}

	/**
	 * Return filter_relationship.
	 *
	 * @return string
	 */
	protected function get_filter_by_attributes_relationship() {
		$filter_relationship = $this->config['filter_attribute_relationship'];
		if ( ! isset( $filter_relationship ) ) {
			return 'AND';
		}

		if ( ! in_array( $filter_relationship, [ 'and', 'or' ] ) ) {
			return 'AND';
		}

		return strtoupper( $filter_relationship );
	}

	/**
	 * @param array  $term
	 * @param string $taxonomy
	 * @param string $type
	 * @return array
	 */
	protected function get_variation_parent_ids_from_term( $term, $taxonomy, $type ) {
		global $wpdb;

		return $wpdb->get_col( "
        SELECT DISTINCT p.ID
        FROM {$wpdb->prefix}posts as p
        INNER JOIN {$wpdb->prefix}posts as p2 ON p2.post_parent = p.ID
        INNER JOIN {$wpdb->prefix}term_relationships as tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->prefix}term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->prefix}terms as t ON tt.term_id = t.term_id
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
        AND p2.post_status = 'publish'
        AND tt.taxonomy = '$taxonomy'
        AND t.$type IN ( '" . implode( "','", $term ) . "' )
    " );
	}

	/**
	 * Parse meta query value.
	 *
	 * @param $value
	 * @param $condition
	 * @return array|string
	 */
	protected function parse_meta_query_value( $value, $condition ) {
		$condition = $this->parse_meta_query_condition( $condition );

		if ( in_array( $condition, [ 'IN', 'NOT IN' ] ) ) {
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		return $value;
	}

	/**
	 * Parse meta query condition.
	 *
	 * @param $condition
	 * @return string
	 */
	protected function parse_meta_query_condition( $condition ) {
		if ( ! in_array( $condition, array_keys( Dropdown::get_meta_query_conditions() ) ) ) {
			return '=';
		}

		return strtoupper( sanitize_text_field( $condition ) );
	}
}
